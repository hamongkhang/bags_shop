<?php


namespace wpie\import\wpml;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php');
}

class WPIE_WPML_Import extends \wpie\import\base\WPIE_Import_Base {

        private $default_language;
        private $active_languages;
        private $current_language;
        private $importLang;
        private $taxonomy;

        public function __construct( $wpie_import_option = array(), $import_type = "" ) {

                global $sitepress;

                $this->default_language = $sitepress->get_default_language();

                $this->active_languages = $sitepress->get_active_languages();

                $this->current_language = $sitepress->get_current_language();

                $this->wpie_import_option = $wpie_import_option;

                $this->import_type = $import_type;
        }

        private function setImportLang() {

                $this->importLang = false;

                $lang = wpie_sanitize_field( $this->get_field_value( 'wpie_wpml_lang_code' ) );

                if ( $lang === "as_specified" ) {
                        $lang = wpie_sanitize_field( $this->get_field_value( 'wpie_item_wpml_lang' ) );
                }

                if ( !empty( $lang ) && isset( $this->active_languages[ $lang ] ) ) {
                        $this->importLang = $this->active_languages[ $lang ];
                }

                if ( $this->importLang === false ) {
                        $this->importLang = $this->current_language;
                }
        }

        private function setTaxonomy() {

                $this->taxonomy = false;

                if ( $this->import_type === "taxonomy" ) {

                        $this->taxonomy = wpie_sanitize_field( $this->get_field_value( 'wpie_taxonomy_type' ) );
                } elseif ( $this->import_type === "product_attribute" ) {

                        global $wpdb;
                        $prefix = "pa_";

                        $source = wpie_sanitize_field( $this->get_field_value( 'wpie_item_attribute_source', true ) );

                        if ( $source === "single" ) {

                                $slug = wpie_sanitize_field( $this->get_field_value( 'wpie_item_attribute_list', true ) );

                                if ( empty( $slug ) ) {
                                        return false;
                                }

                                $slug = preg_replace( '/^pa\_/', '', \wc_sanitize_taxonomy_name( $slug ) );

                                if ( empty( $slug ) ) {
                                        return false;
                                }
                                $tax = $wpdb->get_var( $wpdb->prepare( "SELECT attribute_name FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = %s", $slug ) );

                                if ( !empty( $tax ) ) {
                                        $this->taxonomy = $prefix . $tax;
                                }
                        } else {
                                $searchBy = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_name' ) );

                                $label = wpie_sanitize_field( $this->get_field_value( 'wpie_item_name' ) );

                                if ( $searchBy === "name" && !empty( $label ) ) {

                                        $tax = $wpdb->get_var( $wpdb->prepare( "SELECT attribute_name FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_label = %s", $label ) );

                                        if ( !empty( $tax ) ) {
                                                $this->taxonomy = $prefix . $tax;
                                        }
                                } else {

                                        $slug_method = wpie_sanitize_field( $this->get_field_value( 'wpie_item_slug', false, true ) );

                                        $slug = "";

                                        if ( ( strtolower( trim( $slug_method ) )) === "auto" && !empty( $label ) ) {
                                                $slug = $label;
                                        } elseif ( strtolower( trim( $slug_method ) ) === "as_specified" ) {
                                                $slug = wpie_sanitize_field( $this->get_field_value( 'wpie_item_slug_as_specified_data' ) );
                                                if ( empty( $slug ) ) {
                                                        $slug = $label;
                                                }
                                        }

                                        if ( empty( $slug ) ) {
                                                return false;
                                        }

                                        $slug = preg_replace( '/^pa\_/', '', \wc_sanitize_taxonomy_name( $slug ) );

                                        if ( empty( $slug ) ) {
                                                return false;
                                        }
                                        $tax = $wpdb->get_var( $wpdb->prepare( "SELECT attribute_name FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = %s", $slug ) );

                                        if ( !empty( $tax ) ) {
                                                $this->taxonomy = $prefix . $tax;
                                        }
                                }
                        }
                }
        }

        public function before_item_import( &$wpie_import_record = array(), &$existing_item_id = 0, &$is_new_item = true, &$is_search_duplicates ) {

                $this->wpie_import_record = $wpie_import_record;

                $source_item = false;

                $this->setImportLang();

                $this->setTaxonomy();

                if ( $this->import_type === "product_attribute" && !empty( $this->taxonomy ) ) {
                        $wpie_import_record[ '__wpieTaxonomy' ] = $this->taxonomy;
                }

                if ( $this->import_type === "post" ) {
                        $source_item = $this->search_post_duplicate_item();
                } elseif ( $this->import_type === "taxonomy" || $this->import_type === "product_attribute" ) {

                        if ( !$this->is_translatable_taxonomy( $this->taxonomy ) ) {
                                return;
                        }
                        $source_item = $this->search_taxonomy_duplicate_item();
                }

                if ( !empty( $source_item ) && $source_item !== 0 && is_numeric( $source_item ) ) {
                        $source_item = array( $source_item );
                }

                if ( is_array( $source_item ) && !empty( $source_item ) && $this->importLang !== false ) {

                        $element_type = "post_post";

                        if ( $this->import_type === "post" ) {
                                $element_type = 'post_' . wpie_sanitize_field( $this->get_field_value( 'wpie_import_type' ) );
                        } elseif ( $this->import_type === "taxonomy" || $this->import_type === "product_attribute" ) {
                                $element_type = 'tax_' . $this->taxonomy;
                        }

                        if ( $this->import_type === "post" || (($this->import_type === "taxonomy" || $this->import_type === "product_attribute") && !empty( $this->taxonomy )) ) {

                                global $wpdb;

                                $element_id = $wpdb->get_var(
                                        $wpdb->prepare(
                                                "SELECT element_id FROM {$wpdb->prefix}icl_translations 
                                                WHERE element_id IN ('" . implode( "','", $source_item ) . "') AND 
                                                language_code = %s AND
                                                element_type = %s",
                                                $this->importLang,
                                                $element_type
                                        )
                                );

                                if ( !empty( $element_id ) && $element_id > 0 ) {
                                        $is_new_item = false;
                                        $existing_item_id = $element_id;
                                }
                        }
                }

                $is_search_duplicates = false;

                if ( $this->import_type === "taxonomy" || $this->import_type === "product_attribute" ) {
                        $this->remove_wpml_term_filters();
                }

                unset( $source_item );
        }

        public function after_item_import( $item_id = 0, $item = null, $is_new_item = true ) {

                $this->item_id = $item_id;

                $this->item = $item;

                $this->is_new_item = $is_new_item;

                $this->update_tanslation();
        }

        private function update_tanslation() {

                global $sitepress, $wpdb;

                $element_type = "post_post";

                if ( $this->import_type === "post" ) {
                        $element_type = 'post_' . $this->item->post_type;
                } elseif ( $this->import_type === "taxonomy" || $this->import_type === "product_attribute" ) {

                        $element_type = 'tax_' . $this->item->taxonomy;
                }

                if ( $this->import_type === "post" ) {
                        $source_item = $this->search_source_post_item( true );
                } elseif ( $this->import_type === "taxonomy" || $this->import_type === "product_attribute" ) {
                        $source_item = $this->search_source_taxonomy_item( true );
                }

                $old_trid = 0;
                if ( !$this->is_new_item ) {

                        $old_trid = $wpdb->get_var(
                                $wpdb->prepare( "SELECT trid FROM {$wpdb->prefix}icl_translations 
                                                WHERE element_id = %d AND 
                                                language_code = %s AND
                                                element_type = %s",
                                        $this->item_id,
                                        $this->importLang,
                                        $element_type
                                ) );
                }

                $trid = 0;

                if ( !empty( $source_item ) ) {

                        $default_lang = $this->default_language;

                        if ( is_array( $source_item ) ) {

                                if ( ($item_key = array_search( $this->item_id, $source_item )) !== false ) {
                                        unset( $source_item[ $item_key ] );
                                }

                                $trid = $wpdb->get_var(
                                        $wpdb->prepare( "SELECT trid FROM {$wpdb->prefix}icl_translations 
                                                        WHERE element_id IN ('" . implode( "','", $source_item ) . "') AND 
                                                        language_code = %s AND
                                                        element_type = %s Limit 0,1",
                                                $this->default_language,
                                                $element_type
                                        ) );

                                if ( empty( $trid ) ) {
                                        $translation = $wpdb->get_row(
                                                $wpdb->prepare( "SELECT trid,language_code,source_language_code FROM {$wpdb->prefix}icl_translations 
                                                                WHERE element_id IN ('" . implode( "','", $source_item ) . "') AND 
                                                                element_type = %s Limit 0,1",
                                                        $element_type
                                                ) );

                                        if ( $translation && isset( $translation->trid ) ) {
                                                $trid = $translation->trid;
                                                $default_lang = empty( $translation->source_language_code ) ? $translation->language_code : $translation->source_language_code;
                                        }
                                }
                        } elseif ( is_numeric( $source_item ) && absint( $source_item ) > 0 ) {
                                $trid = $wpdb->get_var(
                                        $wpdb->prepare(
                                                "SELECT trid FROM {$wpdb->prefix}icl_translations
                                                WHERE element_id = %d AND element_type = %s Limit 0,1",
                                                absint( $source_item ),
                                                $element_type
                                        ) );
                        }

                        if ( !empty( $trid ) && absint( $trid ) > 0 && $old_trid !== $trid ) {
                                $sitepress->set_element_language_details( $this->item_id, $element_type, $trid, $this->importLang, $default_lang );
                        }
                }

                if ( empty( $trid ) || absint( $trid ) === 0 ) {
                        $sitepress->set_element_language_details( $this->item_id, $element_type, false, $this->importLang );
                }

                unset( $element_type, $trid, $source_item );
        }

        private function remove_wpml_term_filters() {

                global $sitepress;

                remove_filter( 'get_terms_args', array( $sitepress, 'get_terms_args_filter' ) );

                remove_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1 );

                remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ) );
        }

        private function search_source_taxonomy_item( $is_multiple = false ) {

                global $wp_version;

                $this->remove_wpml_term_filters();

                $logic = wpie_sanitize_field( $this->get_field_value( 'wpie_item_wpml_default_item', true ) );

                $taxonomy_items = false;

                if ( $logic == "id" ) {

                        $item_id = absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_wpml_trid' ) ) );

                        if ( $item_id > 0 ) {

                                $term = get_term_by( 'id', $item_id, $this->taxonomy );

                                if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
                                        $taxonomy_items = $item_id;
                                }
                                unset( $term );
                        }

                        unset( $item_id );
                } elseif ( $logic == "slug" ) {

                        $slug = wpie_sanitize_field( $this->get_field_value( 'wpie_item_wpml_translation_slug', false, true ) );

                        if ( !empty( $slug ) ) {

                                $args = array(
                                        'get' => 'all',
                                        'number' => 0,
                                        'taxonomy' => $this->taxonomy,
                                        'update_term_meta_cache' => false,
                                        'orderby' => 'none',
                                        'fields' => 'ids',
                                        'suppress_filter' => true,
                                        'slug' => $slug
                                );

                                if ( version_compare( $wp_version, '4.5.0', '<' ) ) {

                                        $taxonomy_items = get_terms( $this->taxonomy, $args );
                                } else {
                                        $taxonomy_items = get_terms( $args );
                                }
                        }
                        unset( $slug );
                } elseif ( $logic == "name" ) {

                        $name = wpie_sanitize_field( $this->get_field_value( 'wpie_item_wpml_default_item_name', false, true ) );

                        if ( !empty( $name ) ) {

                                $args = array(
                                        'get' => 'all',
                                        'number' => 0,
                                        'taxonomy' => $this->taxonomy,
                                        'update_term_meta_cache' => false,
                                        'orderby' => 'none',
                                        'fields' => 'ids',
                                        'suppress_filter' => true,
                                        'name' => $name
                                );

                                if ( version_compare( $wp_version, '4.5.0', '<' ) ) {

                                        $taxonomy_items = get_terms( $this->taxonomy, $args );
                                } else {
                                        $taxonomy_items = get_terms( $args );
                                }
                        }
                        unset( $name );
                }
                unset( $logic );

                return $taxonomy_items;
        }

        private function search_taxonomy_duplicate_item() {

                global $wp_version;

                $this->remove_wpml_term_filters();

                $taxonomy_item = false;

                $wpie_duplicate_indicator = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic', true ) );

                if ( $wpie_duplicate_indicator == "id" ) {

                        $duplicate_id = absint( wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_id' ) ) );

                        if ( $duplicate_id > 0 ) {

                                $term = get_term_by( 'id', $duplicate_id, $this->taxonomy );

                                if ( !empty( $term ) && !is_wp_error( $term ) ) {
                                        $taxonomy_item = $duplicate_id;
                                }
                                unset( $term );
                        }
                        unset( $duplicate_id );
                } elseif ( $wpie_duplicate_indicator == "slug" ) {

                        $label = wpie_sanitize_field( $this->get_field_value( 'wpie_item_term_name' ) );

                        $slug_method = wpie_sanitize_field( $this->get_field_value( 'wpie_item_term_slug', false, true ) );

                        $slug = "";

                        if ( ( strtolower( trim( $slug_method ) )) === "auto" && !empty( $label ) ) {
                                $slug = $label;
                        } elseif ( strtolower( trim( $slug_method ) ) === "as_specified" ) {
                                $slug = wpie_sanitize_field( $this->get_field_value( 'wpie_item_term_slug_as_specified_data' ) );
                                if ( empty( $slug ) ) {
                                        $slug = $label;
                                }
                        }

                        if ( empty( $slug ) ) {
                                return false;
                        }

                        if ( !empty( $slug ) ) {

                                $args = array(
                                        'get' => 'all',
                                        'number' => 0,
                                        'taxonomy' => $this->taxonomy,
                                        'update_term_meta_cache' => false,
                                        'orderby' => 'id',
                                        'fields' => 'ids',
                                        'suppress_filter' => true,
                                        'slug' => $slug
                                );

                                if ( version_compare( $wp_version, '4.5.0', '<' ) ) {

                                        $taxonomy_item = get_terms( $this->taxonomy, $args );
                                } else {
                                        $taxonomy_item = get_terms( $args );
                                }
                        }
                        unset( $slug );
                } elseif ( $wpie_duplicate_indicator == "name" ) {

                        $name = wpie_sanitize_field( $this->get_field_value( 'wpie_item_term_name' ) );

                        if ( !empty( $name ) ) {

                                $args = array(
                                        'get' => 'all',
                                        'number' => 0,
                                        'taxonomy' => $this->taxonomy,
                                        'update_term_meta_cache' => false,
                                        'orderby' => 'none',
                                        'fields' => 'ids',
                                        'suppress_filter' => true,
                                        'name' => $name
                                );

                                if ( version_compare( $wp_version, '4.5.0', '<' ) ) {

                                        $taxonomy_item = get_terms( $this->taxonomy, $args );
                                } else {
                                        $taxonomy_item = get_terms( $args );
                                }
                        }

                        unset( $name );
                } elseif ( $wpie_duplicate_indicator == "cf" ) {

                        $meta_key = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_cf_key' ) );

                        $meta_val = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_cf_value' ) );

                        if ( !empty( $meta_key ) ) {

                                $args = array(
                                        'taxonomy' => $this->taxonomy,
                                        'number' => 0,
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

                                        $taxonomy_item = get_terms( $this->taxonomy, $args );
                                } else {
                                        $taxonomy_item = get_terms( $args );
                                }

                                unset( $args );
                        }

                        unset( $meta_key, $meta_val );
                }

                unset( $wpie_duplicate_indicator );

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

                        $wpie_field_data = $this->get_field_value( $temp_field );

                        if ( !empty( $wpie_field_data ) ) {

                                $_post = $wpdb->get_col(
                                        $wpdb->prepare(
                                                "SELECT ID FROM " . $wpdb->posts . "
                                                WHERE
                                                    post_type = %s
                                                    AND ID != 0
                                                    AND `" . $wpie_field . "` IN ( %s,%s,%s )
                                                ", wpie_sanitize_field( $this->get_field_value( 'wpie_import_type', true ) ), html_entity_decode( $wpie_field_data ), htmlentities( $wpie_field_data ), $wpie_field_data
                                        )
                                );

                                if ( $_post && !empty( $_post ) ) {
                                        $post_id = $_post;
                                }

                                unset( $_post );
                        }
                        unset( $wpie_field, $wpie_field_data, $temp_field );
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

        private function search_source_post_item( $is_multiple = false ) {

                global $wpdb;

                $logic = wpie_sanitize_field( $this->get_field_value( 'wpie_item_wpml_default_item', true ) );

                $post_id = 0;

                if ( $logic == "id" ) {

                        $item_id = absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_wpml_trid' ) ) );

                        if ( $item_id > 0 ) {

                                $_post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE ID = %d LIMIT 0,1", $item_id ) );

                                if ( $_post && absint( $_post ) > 0 ) {
                                        $post_id = absint( $item_id );
                                }
                                unset( $_post );
                        }
                        unset( $item_id );
                } elseif ( $logic == "title" ) {

                        $wpie_field_data = $this->get_field_value( "wpie_item_wpml_translation_title" );

                        if ( !empty( $wpie_field_data ) ) {

                                if ( $is_multiple ) {

                                        $_post = $wpdb->get_col(
                                                $wpdb->prepare(
                                                        "SELECT ID FROM " . $wpdb->posts . "
                                                        WHERE
                                                            post_type = %s
                                                            AND ID != 0
                                                            AND `post_title` IN ( %s,%s,%s )
                                                ", wpie_sanitize_field( $this->get_field_value( 'wpie_import_type', true ) ), html_entity_decode( $wpie_field_data ), htmlentities( $wpie_field_data ), $wpie_field_data
                                                )
                                        );

                                        if ( $_post && !empty( $_post ) ) {
                                                return $_post;
                                        }

                                        unset( $_post );
                                } else {

                                        $_post = $wpdb->get_var(
                                                $wpdb->prepare(
                                                        "SELECT ID FROM " . $wpdb->posts . "
                                                        WHERE
                                                            post_type = %s
                                                            AND ID != 0
                                                            AND post_title = %s
                                                        LIMIT 1
                                                ", wpie_sanitize_field( $this->get_field_value( 'wpie_import_type', true ) ), wpie_sanitize_field( $wpie_field_data )
                                                )
                                        );

                                        if ( $_post && absint( $_post ) > 0 ) {
                                                $post_id = absint( $_post );
                                        }

                                        unset( $_post );
                                }
                        }
                        unset( $wpie_field_data );
                }

                unset( $logic );

                if ( $post_id == 0 ) {
                        return false;
                }
                return $post_id;
        }

        /**
         * @param string $taxonomy
         *
         * @return bool
         */
        private function is_translatable_taxonomy( $taxonomy ) {

                if ( empty( $taxonomy ) ) {
                        return false;
                }
                global $sitepress;

                $is_translatable = false;

                $taxonomy_object = \get_taxonomy( $taxonomy );

                if ( $taxonomy_object ) {
                        $post_type = isset( $taxonomy_object->object_type[ 0 ] ) ? $taxonomy_object->object_type[ 0 ] : 'post';
                        $translatable_taxonomies = $sitepress->get_translatable_taxonomies( true, $post_type );
                        $is_translatable = in_array( $taxonomy, $translatable_taxonomies );
                }

                return $is_translatable;
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
