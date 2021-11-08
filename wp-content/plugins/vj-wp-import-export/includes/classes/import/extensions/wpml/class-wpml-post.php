<?php


namespace wpie\import\wpml;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/extensions/wpml/class-wpml-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/extensions/wpml/class-wpml-base.php');
}

class WPML_Post extends WPML_Base {

        protected function preProcessData() {

                $this->elementType = 'post_' . wpie_sanitize_field( $this->get_field_value( 'wpie_import_type' ) );

                add_filter( 'wpie_is_term_exists', [ $this, 'term_exists' ], 10, 4 );

                return true;
        }

        protected function searchExisting() {

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

        protected function searchTranslation( $is_multiple = true ) {
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
         * Filter term_exists() according to the current language.
         * This is needed because Plugin may find a wrong term in case several existing
         * terms are sharing the same name.
         *
         * @since 3.5.0
         *
         * @param mixed  $term_exists Array of found term_id and term_taxonmy_id, null if no term found.
         * @param string $term        Searched term (name or slug).
         * @param string $taxonomy    Taxonomy.
         * @param int    $parent      Term id of the term parent.
         * @return mixed
         */
        public function term_exists( $term_exists, $term, $taxonomy, $parent ) {

                global $sitepress;

                if ( $sitepress->is_translated_taxonomy( $taxonomy ) && !empty( $this->importLang ) && isset( $term_exists[ 'term_id' ] ) ) {

                        $this->remove_wpml_term_filters();

                        $term_id = apply_filters( 'wpml_object_id', $term_exists[ 'term_id' ], $taxonomy, false, $this->importLang );

                        $newTerm = \get_term_by( 'id', $term_id, $taxonomy, ARRAY_A );

                        if ( isset( $newTerm[ 'term_id' ] ) && absint( $newTerm[ 'term_id' ] ) > 0 ) {
                                $term_exists = $newTerm;
                        } elseif ( $term_exists && isset( $term_exists[ 'term_id' ] ) ) {

                                $term_exists = $this->duplicate_term( $term_exists[ 'term_id' ], $taxonomy );
                        }

                        $this->add_wpml_term_filters();
                }
                return $term_exists;
        }

        public function duplicate_term( $term_id, $taxonomy ) {

                if ( absint( $term_id ) < 1 ) {
                        return;
                }

                $termData = \get_term_by( 'id', $term_id, $taxonomy, ARRAY_A );

                if ( !isset( $termData[ 'name' ] ) ) {
                        return;
                }

                global $sitepress;

                $term_name = $termData[ 'name' ];

                $new_term = wp_insert_term( $term_name, $taxonomy, array( 'slug' => \WPML_Terms_Translations::term_unique_slug( sanitize_title( $term_name ), $taxonomy, $this->importLang ) ) );

                if ( is_array( $new_term ) && isset( $new_term[ 'term_taxonomy_id' ] ) ) {
                        $trid = $sitepress->get_element_trid( $term_id, 'tax_' . $taxonomy );
                        $sitepress->set_element_language_details( $new_term[ 'term_taxonomy_id' ], 'tax_' . $taxonomy, $trid, $this->importLang );
                }

                return $new_term;
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
