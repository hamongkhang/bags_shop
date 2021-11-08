<?php


namespace wpie\import\polylang;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php');
}

class WPIE_Polylang_Import extends \wpie\import\base\WPIE_Import_Base {

        private $default_language;
        private $active_language;
        private $importLang;

        public function __construct( $wpie_import_option = [], $import_type = "" ) {

                $this->default_language = isset( \PLL()->options[ 'default_lang' ] ) ? \PLL()->model->get_language( \PLL()->options[ 'default_lang' ] ) : \PLL()->curlang;

                $this->active_language = \PLL()->curlang;

                $this->wpie_import_option = $wpie_import_option;

                $this->import_type = $import_type;

                add_filter( 'wpie_import_images_ids', [ $this, 'import_images' ], 10, 2 );

                add_filter( 'wpie_is_term_exists', [ $this, 'term_exists' ], 10, 4 );

                add_filter( 'wpie_before_term_import', [ $this, 'maybe_share_term_slug' ], 10, 1 );

                add_action( 'wpie_insert_post', [ $this, 'set_post_language' ], 10, 3 );

                add_action( 'wpie_add_new_post_term', [ $this, 'new_post_term' ] );

                add_filter( 'wpie_get_term_by', [ $this, 'get_term_by' ], 10, 2 );
        }

        /**
         * Allows to use shared term slugs when importing taxonomies
         *
         * @sine 0.2
         *
         * @param array $data Article data.
         * @return array Unmodified article data
         */
        public function maybe_share_term_slug( $data ) {
                if ( \pll_is_translated_taxonomy( $data[ 'taxonomy_type' ] ) && !empty( $data[ 'slug' ] ) ) {
                        $data[ 'slug' ] .= '___' . $this->importLang->slug;
                }
                return $data;
        }

        public function import_images( $images = [], $item_id = 0 ) {

                if ( empty( $images ) ) {
                        return [];
                }

                $newImages = [];

                foreach ( $images as $image ) {

                        $imageID = $image;

                        if ( !\pll_get_post_language( $image ) ) {
                                \pll_set_post_language( $image, $this->importLang );
                        } else {
                                $image_id = \PLL()->model->post->get_translation( $image, $this->importLang );

                                if ( !$image_id ) {
                                        $imageID = \PLL()->posts->create_media_translation( $image, $this->importLang );
                                        $this->updateParent( $imageID, $item_id );
                                } else {
                                        $imageID = $image_id;
                                }
                        }

                        $newImages[] = $imageID;
                }

                return $newImages;
        }

        private function updateParent( $id, $parent ) {

                if ( empty( $id ) || empty( $parent ) || $this->import_type !== "post" ) {
                        return;
                }

                \wp_update_post(
                        array(
                                'ID' => $id,
                                'post_parent' => $parent
                        )
                );
        }

        private function setImportLang() {

                $this->importLang = false;

                $item_lang = wpie_sanitize_field( $this->get_field_value( 'wpie_polylang_lang_code' ) );

                if ( $item_lang === "as_specified" ) {
                        $item_lang = wpie_sanitize_field( $this->get_field_value( 'wpie_item_polylang_lang' ) );
                }

                if ( !empty( $item_lang ) ) {
                        $this->importLang = \PLL()->model->get_language( $item_lang );
                }

                if ( $this->importLang === false ) {
                        $this->importLang = $this->active_language;
                }
        }

        public function before_item_import( $wpie_import_record = array(), &$existing_item_id = 0, &$is_new_item = true, &$is_search_duplicates ) {

                $this->wpie_import_record = $wpie_import_record;

                \PLL()->curlang = $this->importLang;

                $this->setImportLang();

                $source_item = false;

                $this->resetLanguage();

                if ( $this->import_type == "post" ) {
                        $source_item = $this->search_post_duplicate_item();
                } elseif ( $this->import_type == "taxonomy" ) {
                        $source_item = $this->search_taxonomy_duplicate_item();
                }

                $this->restoreLanguage();

                if ( !empty( $source_item ) && $source_item !== 0 && is_numeric( $source_item ) ) {

                        if ( $this->import_type == "post" ) {
                                $element_id = \PLL()->model->post->get_translation( $source_item, $this->importLang );
                        } else {
                                $element_id = \PLL()->model->term->get_translation( $source_item, $this->importLang );
                        }

                        if ( $element_id !== false ) {
                                $is_new_item = false;
                                $existing_item_id = $element_id;
                        }
                }

                $is_search_duplicates = false;

                unset( $source_item );
        }

        public function after_item_import( $item_id = 0, $item = null, $is_new_item = true ) {

                $this->item_id = $item_id;

                $this->item = $item;

                $this->is_new_item = $is_new_item;

                $this->update_tanslation();

                \wp_update_term_count( \pll_languages_list( [ 'fields' => 'term_taxonomy_id' ] ), 'language' );
        }

        private function update_tanslation() {

                if ( $this->importLang === false ) {
                        return;
                }

                if ( $this->import_type == "post" ) {

                        \pll_set_post_language( $this->item_id, $this->importLang );

                        $this->resetLanguage();

                        $source_id = $this->search_source_post_item();

                        $this->restoreLanguage();

                        if ( $source_id === false ) {
                                return;
                        }

                        $translations = \pll_get_post_translations( $source_id );

                        \PLL()->model->post->save_translations( $this->item_id, $translations );
                } else {

                        \pll_set_term_language( $this->item_id, $this->importLang );

                        $this->resetLanguage();

                        $source_id = $this->search_source_taxonomy_item();

                        $this->restoreLanguage();

                        if ( $source_id === false ) {
                                return;
                        }

                        $translations = \pll_get_term_translations( $source_id );

                        \PLL()->model->term->save_translations( $this->item_id, $translations );
                }
        }

        public function set_post_language( $itemID = 0 ) {

                if ( absint( $itemID ) < 1 ) {
                        return;
                }

                \pll_set_post_language( $itemID, $this->importLang );
        }

        private function resetLanguage() {

                \PLL()->curlang = "";
        }

        private function restoreLanguage() {

                \PLL()->curlang = $this->active_language;
        }

        private function search_source_taxonomy_item( $is_multiple = false ) {

                global $wp_version;

                $logic = wpie_sanitize_field( $this->get_field_value( 'wpie_item_polylang_default_item', true ) );

                $taxonomy_type = wpie_sanitize_field( $this->get_field_value( 'wpie_taxonomy_type' ) );

                $taxonomy_items = false;

                if ( $logic == "id" ) {

                        $item_id = absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_polylang_trid' ) ) );

                        if ( $item_id > 0 ) {

                                $term = get_term_by( 'id', $item_id, $taxonomy_type );

                                if ( !empty( $term ) && !is_wp_error( $term ) ) {
                                        $taxonomy_items = $item_id;
                                }
                                unset( $term );
                        }

                        unset( $item_id );
                } elseif ( $logic == "slug" ) {

                        $slug = wpie_sanitize_field( $this->get_field_value( 'wpie_item_polylang_translation_slug_data' ) );

                        if ( !empty( $slug ) ) {

                                $term = false;

                                $args = [
                                        'get' => 'all',
                                        'number' => 1,
                                        'taxonomy' => $taxonomy_type,
                                        'update_term_meta_cache' => false,
                                        'orderby' => 'none',
                                        'fields' => 'ids',
                                        'suppress_filter' => true,
                                ];

                                if ( strpos( "lang=>", $slug ) === false ) {

                                        $translations = explode( "||", $slug );

                                        foreach ( $translations as $data ) {

                                                $langData = explode( "=>", $data );

                                                if ( isset( $langData[ 2 ] ) && !empty( $langData[ 2 ] ) ) {

                                                        $args[ 'slug' ] = $langData[ 2 ];

                                                        $term = $this->get_term_data( $args, $taxonomy_type );
                                                }

                                                if ( is_array( $term ) && isset( $term[ 0 ] ) ) {
                                                        $taxonomy_items = $term[ 0 ];
                                                        break;
                                                }
                                        }

                                        unset( $translations );
                                } else {
                                        $args[ 'slug' ] = $slug;

                                        $term = $this->get_term_data( $args, $taxonomy_type );

                                        if ( is_array( $term ) && isset( $term[ 0 ] ) ) {
                                                $taxonomy_items = $term[ 0 ];
                                        }
                                }
                        }
                        unset( $slug );
                } elseif ( $logic == "name" ) {

                        $name = wpie_sanitize_field( $this->get_field_value( 'wpie_item_polylang_default_item_name_data' ) );

                        if ( !empty( $name ) ) {

                                $args = array(
                                        'get' => 'all',
                                        'number' => 0,
                                        'taxonomy' => $taxonomy_type,
                                        'update_term_meta_cache' => false,
                                        'orderby' => 'none',
                                        'fields' => 'ids',
                                        'suppress_filter' => true,
                                        'name' => $name
                                );

                                if ( version_compare( $wp_version, '4.5.0', '<' ) ) {

                                        $term = get_terms( $taxonomy_type, $args );
                                } else {
                                        $term = get_terms( $args );
                                }

                                if ( is_array( $term ) && isset( $term[ 0 ] ) ) {
                                        $taxonomy_items = $term[ 0 ];
                                }
                        }
                        unset( $name );
                }
                unset( $logic, $taxonomy_type );

                return $taxonomy_items;
        }

        private function get_term_data( $args, $taxonomy ) {

                global $wp_version;

                if ( version_compare( $wp_version, '4.5.0', '<' ) ) {

                        return \get_terms( $taxonomy, $args );
                }

                return \get_terms( $args );
        }

        private function search_taxonomy_duplicate_item() {

                global $wp_version;

                $taxonomy_item = false;

                $wpie_duplicate_indicator = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic', true ) );

                $taxonomy_type = wpie_sanitize_field( $this->get_field_value( 'wpie_taxonomy_type' ) );

                if ( $wpie_duplicate_indicator == "id" ) {

                        $term_id = absint( wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_id' ) ) );

                        if ( $term_id > 0 ) {

                                $term = get_term_by( 'id', $term_id, $taxonomy_type );

                                if ( !empty( $term ) && !is_wp_error( $term ) ) {
                                        $taxonomy_item = $term_id;
                                }
                                unset( $term );
                        }
                        unset( $term_id );
                } elseif ( $wpie_duplicate_indicator == "slug" ) {

                        $slug = wpie_sanitize_field( $this->get_field_value( 'wpie_item_term_slug', false, true ) );

                        if ( !empty( $slug ) ) {

                                $args = array(
                                        'get' => 'all',
                                        'number' => 1,
                                        'taxonomy' => "category",
                                        'update_term_meta_cache' => false,
                                        'orderby' => 'id',
                                        'fields' => 'ids',
                                        'suppress_filter' => true,
                                        'slug' => $slug
                                );

                                if ( version_compare( $wp_version, '4.5.0', '<' ) ) {

                                        $term = get_terms( $taxonomy_type, $args );
                                } else {
                                        $term = get_terms( $args );
                                }

                                if ( is_array( $term ) && isset( $term[ 0 ] ) ) {
                                        $taxonomy_item = $term[ 0 ];
                                }
                        }
                        unset( $slug );
                } elseif ( $wpie_duplicate_indicator == "name" ) {

                        $name = wpie_sanitize_field( $this->get_field_value( 'wpie_item_term_name' ) );

                        if ( !empty( $name ) ) {

                                $args = array(
                                        'get' => 'all',
                                        'number' => 1,
                                        'taxonomy' => $taxonomy_type,
                                        'update_term_meta_cache' => false,
                                        'orderby' => 'none',
                                        'fields' => 'ids',
                                        'suppress_filter' => true,
                                        'name' => $name
                                );

                                if ( version_compare( $wp_version, '4.5.0', '<' ) ) {

                                        $term = get_terms( $taxonomy_type, $args );
                                } else {
                                        $term = get_terms( $args );
                                }

                                if ( is_array( $term ) && isset( $term[ 0 ] ) ) {
                                        $taxonomy_item = $term[ 0 ];
                                }
                        }

                        unset( $name );
                } elseif ( $wpie_duplicate_indicator == "cf" ) {

                        $meta_key = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_cf_key' ) );

                        $meta_val = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_cf_value' ) );

                        if ( !empty( $meta_key ) ) {

                                $args = array(
                                        'taxonomy' => $taxonomy_type,
                                        'number' => 1,
                                        'fields' => 'ids',
                                        'hide_empty' => false,
                                        'meta_query' => array(
                                                array(
                                                        'key' => $meta_key,
                                                        'value' => $meta_val,
                                                        'compare' => '='
                                                )
                                        )
                                );

                                if ( version_compare( $wp_version, '4.5.0', '<' ) ) {

                                        $term = get_terms( $taxonomy_type, $args );
                                } else {
                                        $term = get_terms( $args );
                                }

                                if ( is_array( $term ) && isset( $term[ 0 ] ) ) {
                                        $taxonomy_item = $term[ 0 ];
                                }
                                unset( $term, $args );
                        }

                        unset( $meta_key, $meta_val );
                }

                unset( $taxonomy_type, $wpie_duplicate_indicator );

                if ( is_wp_error( $taxonomy_item ) ) {
                        return false;
                }
                return $taxonomy_item;
        }

        private function search_post_duplicate_item() {

                global $wpdb;

                $post_id = 0;

                $wpie_duplicate_indicator = empty( $this->get_field_value( 'wpie_existing_item_search_logic', true ) ) ? 'title' : wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic', true ) );

                if ( $wpie_duplicate_indicator == "id" ) {

                        $duplicate_id = absint( wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_id' ) ) );

                        if ( $duplicate_id > 0 ) {
                                $_post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE ID = %d LIMIT 1", $duplicate_id ) );

                                if ( $_post && absint( $_post ) > 0 ) {
                                        $post_id = absint( $duplicate_id );
                                }
                                unset( $_post );
                        }
                        unset( $duplicate_id );
                } elseif ( $wpie_duplicate_indicator == "title" || $wpie_duplicate_indicator == "content" ) {

                        $wpie_field = 'post_' . $wpie_duplicate_indicator;

                        $temp_field = 'wpie_item_' . $wpie_duplicate_indicator;

                        $data = $this->get_field_value( $temp_field );

                        $postType = wpie_sanitize_field( $this->get_field_value( 'wpie_import_type', true ) );

                        if ( !empty( $data ) ) {

                                $_post = $wpdb->get_var(
                                        $wpdb->prepare(
                                                "SELECT ID FROM " . $wpdb->posts . "
                                                WHERE
                                                    post_type = %s
                                                    AND ID != 0
                                                    AND `" . $wpie_field . "` IN ( %s,%s,%s )
                                                ", $postType, html_entity_decode( $data ), htmlentities( $data ), $data
                                        )
                                );

                                if ( $_post && !empty( $_post ) ) {
                                        $post_id = $_post;
                                }

                                unset( $_post );
                        }
                        unset( $wpie_field, $temp_field, $data, $postType );
                } elseif ( $wpie_duplicate_indicator == "cf" || $wpie_duplicate_indicator == "sku" ) {


                        if ( $wpie_duplicate_indicator == "cf" ) {

                                $meta_key = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_cf_key' ) );

                                $meta_val = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_cf_value' ) );
                        } else {
                                $meta_key = "_sku";
                                $meta_val = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_sku' ) );
                        }


                        $post_types = wpie_sanitize_field( $this->get_field_value( 'wpie_import_type', true ) );

                        if ( $post_types == "product" ) {

                                if ( strpos( trim( strtolower( $meta_key ) ), "sku" ) !== false ) {
                                        $meta_key = "_sku";
                                }

                                $post_types = [ "product", "product_variation" ];
                        } else {
                                $post_types = [ $post_types ];
                        }

                        $sql_post_type = implode( "','", $post_types );

                        $id = $wpdb->get_var(
                                $wpdb->prepare(
                                        "
                                                SELECT posts.ID
                                                FROM {$wpdb->posts} as posts
                                                INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
                                                WHERE posts.post_type IN ( '{$sql_post_type}' )
                                                AND posts.post_status NOT IN ('trash','auto-draft' )
                                                AND postmeta.meta_key = %s                                               
                                                AND postmeta.meta_value = %s
                                                ORDER BY posts.ID ASC
                                                LIMIT 0, 1
                                        ",
                                        $meta_key,
                                        $meta_val
                                )
                        );

                        if ( absint( $id ) > 0 ) {
                                $post_id = $id;
                        }

                        if ( $post_id === 0 ) {

                                $id = $wpdb->get_var(
                                        $wpdb->prepare(
                                                "
                                                        SELECT posts.ID
                                                        FROM {$wpdb->posts} as posts
                                                        INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
                                                        WHERE posts.post_type IN ( '{$sql_post_type}' )
                                                        AND postmeta.meta_key = %s                                               
                                                        AND postmeta.meta_value = %s
                                                        ORDER BY posts.ID ASC
                                                        LIMIT 0, 1
                                                ",
                                                $meta_key,
                                                $meta_val
                                        )
                                );

                                if ( absint( $id ) > 0 ) {
                                        $post_id = $id;
                                }
                        }
                        if ( $post_id === 0 ) {

                                $id = $wpdb->get_var(
                                        $wpdb->prepare(
                                                "
                                                        SELECT posts.ID
                                                        FROM {$wpdb->posts} as posts
                                                        INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
                                                        WHERE posts.post_type IN ( '{$sql_post_type}' )
                                                        AND postmeta.meta_key IN ( %s,%s,%s)                                               
                                                        AND postmeta.meta_value IN( %s,%s,%s,%s )
                                                        ORDER BY posts.ID ASC
                                                        LIMIT 0, 1
                                                ",
                                                $meta_key,
                                                trim( $meta_key ),
                                                wpie_sanitize_field( $meta_key ),
                                                $meta_val,
                                                trim( $meta_val ),
                                                wpie_sanitize_field( $meta_val ),
                                                preg_replace( '%[ \\t\\n]%', '', $meta_val )
                                        )
                                );

                                if ( absint( $id ) > 0 ) {
                                        $post_id = $id;
                                }
                        }

                        unset( $meta_key, $meta_val, $post_types, $sql_post_type, $id );
                }

                unset( $wpie_duplicate_indicator );

                return $post_id;
        }

        private function search_source_post_item() {

                global $wpdb;

                $searchBy = wpie_sanitize_field( $this->get_field_value( 'wpie_item_polylang_default_item', true ) );

                $post_id = 0;

                if ( $searchBy === "id" ) {

                        $item_id = absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_polylang_trid' ) ) );

                        if ( $item_id > 0 ) {

                                $id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE ID = %d LIMIT 0,1", $item_id ) );

                                if ( $id && absint( $id ) > 0 ) {
                                        $post_id = absint( $item_id );
                                }
                                unset( $id );
                        }
                        unset( $item_id );
                } elseif ( $searchBy === "title" ) {

                        $title = $this->get_field_value( "wpie_item_polylang_translation_title_data" );

                        $post_type = wpie_sanitize_field( $this->get_field_value( 'wpie_import_type', true ) );

                        if ( !empty( $title ) ) {

                                if ( strpos( "lang=>", $title ) === false ) {

                                        $translations = explode( "||", $title );

                                        foreach ( $translations as $data ) {

                                                $langData = explode( "=>", $data );

                                                if ( isset( $langData[ 2 ] ) && !empty( $langData[ 2 ] ) ) {
                                                        $post_id = $this->get_post_by_title( $langData[ 2 ], $post_type );
                                                }

                                                if ( $post_id > 0 ) {
                                                        break;
                                                }
                                        }

                                        unset( $id );
                                } else {
                                        $post_id = $this->get_post_by_title( $title, $post_type );
                                }
                        }
                        unset( $title );
                }

                unset( $searchBy );

                if ( $post_id == 0 ) {
                        return false;
                }
                return $post_id;
        }

        private function get_post_by_title( $title = "", $post_type = "post" ) {

                global $wpdb;

                $id = $wpdb->get_var(
                        $wpdb->prepare(
                                "SELECT ID FROM " . $wpdb->posts . "
                                                        WHERE
                                                            post_type = %s
                                                            AND ID != 0
                                                            AND `post_title` IN ( %s,%s,%s )
                                                        LIMIT 1
                                                ", $post_type, html_entity_decode( $title ), htmlentities( $title ), $title
                        )
                );

                if ( $id && absint( $id ) > 0 ) {
                        return absint( $id );
                }
                return 0;
        }

        /**
         * Filter term_exists() according to the current language.
         * This is needed because Plugin may find a wrong term in case several existing
         * terms are sharing the same name.
         *
         * @since 3.5.0
         *
         * @param mixed  $term_exists Array of found term_id and term_taxonmy_id, null if no term found.
         * @param string $taxonomy    Taxonomy.
         * @param string $term        Searched term (name or slug).
         * @param int    $parent      Term id of the term parent.
         * @return mixed
         */
        public function term_exists( $term_exists, $term, $taxonomy, $parent ) {

                if ( \pll_is_translated_taxonomy( $taxonomy ) && !empty( $this->importLang ) ) {

                        $new_term_exists = \PLL()->model->term_exists( $term, $taxonomy, $parent, $this->importLang );

                        if ( absint( $new_term_exists ) > 0 ) {
                                $term_exists = [ 'term_id' => $new_term_exists ];
                        } elseif ( $term_exists && isset( $term_exists[ 'term_id' ] ) ) {

                                $newTerm = $this->getTermTranslation( $term_exists[ 'term_id' ], $taxonomy );

                                $term_exists = intval( $newTerm ) > 0 ? [ 'term_id' => $newTerm ] : null;
                        }
                }
                return $term_exists;
        }

        private function getTermTranslation( $termId = 0, $taxonomy = "" ) {

                if ( (!is_numeric( $termId )) || absint( $termId ) < 1 ) {
                        return $termId;
                }

                $term = get_term( $termId, $taxonomy );

                if ( !($term && isset( $term->term_id ) ) ) {
                        return $termId;
                }

                $language = \PLL()->model->term->get_language( $termId );

                if ( $language && $language->slug !== $this->importLang->slug ) {

                        $translationId = \PLL()->model->term->get_translation( $termId, $this->importLang );

                        if ( $translationId !== false ) {
                                return $translationId;
                        }

                        $translation = $this->translateTerm( $termId, $taxonomy );

                        if ( $translation !== false ) {
                                return $translation;
                        }
                }

                return $termId;
        }

        private function translateTerm( $termId, $taxonomy ) {

                if ( isset( \PLL()->sync_content ) ) {
                        return \PLL()->sync_content->duplicate_term( "", $termId, $this->importLang->slug );
                }

                return $this->duplicateTerm( $termId, $taxonomy );
        }

        private function duplicateTerm( $termId, $taxonomy ) {

                $term = get_term( $termId, $taxonomy );

                if ( !($term && isset( $term->term_id ) ) ) {
                        return false;
                }

                $tr_parent = empty( $term->parent ) ? 0 : \PLL()->model->term->get_translation( $term->parent, $this->importLang->slug );

                // Duplicate the parent if the parent translation doesn't exist yet.
                if ( empty( $tr_parent ) && !empty( $term->parent ) ) {
                        $tr_parent = $this->duplicateTerm( $tr_parent, $term->parent );
                }

                $args = array(
                        'description' => wp_slash( $term->description ),
                        'parent' => $tr_parent,
                );

                if ( isset( \PLL()->options[ 'force_lang' ] ) ) {
                        // Share slugs
                        $args[ 'slug' ] = $term->slug . '___' . $this->importLang->slug;
                } else {
                        // Language set from the content: assign a different slug
                        // otherwise we would change the current term language instead of creating a new term
                        $args[ 'slug' ] = sanitize_title( $term->name ) . '-' . $this->importLang->slug;
                }

                $t = wp_insert_term( wp_slash( $term->name ), $term->taxonomy, $args );

                $tr_term = 0;

                if ( is_array( $t ) && isset( $t[ 'term_id' ] ) ) {
                        $tr_term = $t[ 'term_id' ];
                        \PLL()->model->term->set_language( $tr_term, $this->importLang->slug );
                        $translations = \PLL()->model->term->get_translations( $term->term_id );
                        $translations[ $this->importLang->slug ] = $tr_term;
                        \PLL()->model->term->save_translations( $term->term_id, $translations );

                        do_action( 'pll_duplicate_term', $term->term_id, $tr_term, $this->importLang->slug );
                }
                return $tr_term;
        }

        public function get_term_by( $term_id, $taxonomy ) {

                return $this->getTermTranslation( $term_id, $taxonomy );
        }

        public function new_post_term( $term_id ) {

                if ( absint( $term_id ) < 1 ) {
                        return;
                }

                \pll_set_term_language( $term_id, $this->importLang );
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
