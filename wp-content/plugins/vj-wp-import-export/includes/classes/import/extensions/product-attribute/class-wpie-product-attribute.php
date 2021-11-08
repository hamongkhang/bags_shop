<?php


namespace wpie\import;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}
if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-engine.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-engine.php');
}

class WPIE_Product_Attribute_Import extends \wpie\import\engine\WPIE_Import_Engine {

        protected $import_type = "product_attribute";
        private $taxonomy = "";

        public function process_import_data() {

                global $wpdb, $wpieAttrTaxonomy;

                $taxonomy = $this->get_taxonomy();

                if ( $taxonomy === false || is_wp_error( $taxonomy ) ) {

                        $this->set_log( '<strong>' . __( 'ERROR', 'vj-wp-import-export' ) . '</strong> : ' . __( 'something wrong, Taxonomy not found', 'vj-wp-import-export' ) );

                        $this->process_log[ 'skipped' ]++;

                        $this->process_log[ 'imported' ]++;

                        return true;
                }

                $this->taxonomy = $taxonomy;

                $wpieAttrTaxonomy = $this->taxonomy;

                $this->wpie_final_data[ 'taxonomy_type' ] = $this->taxonomy;

                if ( $this->is_update_field( "term_name" ) ) {

                        $this->wpie_final_data[ 'name' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_term_name' ) );
                }
                if ( $this->is_update_field( "term_description" ) ) {

                        $this->wpie_final_data[ 'description' ] = $this->get_field_value( 'wpie_item_term_description' );
                }
                if ( $this->is_update_field( "term_slug" ) ) {

                        $term_slug = wpie_sanitize_field( $this->get_field_value( 'wpie_item_term_slug', false, true ) );

                        if ( $term_slug != "auto" ) {
                                $this->wpie_final_data[ 'slug' ] = $term_slug;
                        }

                        unset( $term_slug );
                }

                $this->wpie_final_data = apply_filters( 'wpie_before_term_import', $this->wpie_final_data, $this->wpie_import_option );

                if ( $this->is_new_item ) {

                        $term = wp_insert_term( $this->wpie_final_data[ 'name' ], $this->wpie_final_data[ 'taxonomy_type' ], $this->wpie_final_data );
                } else {

                        $term = wp_update_term( $this->existing_item_id, $this->wpie_final_data[ 'taxonomy_type' ], $this->wpie_final_data );
                }

                $this->process_log[ 'imported' ]++;

                if ( is_wp_error( $term ) ) {

                        $this->set_log( '<strong>' . __( 'ERROR', 'vj-wp-import-export' ) . '</strong> : ' . $term->get_error_message() );

                        $this->process_log[ 'skipped' ]++;

                        return true;
                } elseif ( $term == 0 ) {

                        $this->set_log( '<strong>' . __( 'ERROR', 'vj-wp-import-export' ) . '</strong> : ' . __( 'something wrong, ID = 0 was generated.', 'vj-wp-import-export' ) );

                        $this->process_log[ 'skipped' ]++;

                        return true;
                }

                $this->item_id = isset( $term[ 'term_id' ] ) ? $term[ 'term_id' ] : 0;

                unset( $term );

                if ( $this->is_new_item ) {
                        $this->process_log[ 'created' ]++;
                } else {
                        $this->process_log[ 'updated' ]++;
                }

                if ( $this->backup_service !== false && $this->is_new_item ) {
                        $this->backup_service->create_backup( $this->item_id, true );
                }
                $this->item = get_term_by( 'id', $this->item_id, $this->wpie_final_data[ 'taxonomy_type' ] );

                $this->process_log[ 'last_records_id' ] = $this->item_id;

                $this->process_log[ 'last_records_status' ] = 'pending';

                $this->process_log[ 'last_activity' ] = date( 'Y-m-d H:i:s' );

                $wpdb->update( $wpdb->prefix . "wpie_template", array( 'last_update_date' => current_time( 'mysql' ), 'process_log' => maybe_serialize( $this->process_log ) ), array( 'id' => $this->wpie_import_id ) );

                do_action( 'wpie_after_attribute_term_import', $this->item_id, $this->wpie_final_data, $this->wpie_import_option );

                if ( $this->is_update_field( "cf" ) ) {

                        $this->wpie_import_cf();
                }

                return $this->item_id;
        }

        private function get_taxonomy() {

                global $wpdb;

                $taxonomy = null;

                $source = wpie_sanitize_field( $this->get_field_value( 'wpie_item_attribute_source', true ) );

                if ( $source === "single" ) {
                        $slug = wpie_sanitize_field( $this->get_field_value( 'wpie_item_attribute_list', true ) );
                        $slug = preg_replace( '/^pa\_/', '', wc_sanitize_taxonomy_name( $slug ) );
                        if ( empty( $slug ) ) {
                                return false;
                        }
                        $taxonomy = $wpdb->get_var( $wpdb->prepare( "SELECT attribute_name FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = %s", $slug ) );
                } else {

                        $label = wpie_sanitize_field( $this->get_field_value( 'wpie_item_name' ) );
                        $slug = wpie_sanitize_field( $this->get_field_value( 'wpie_item_slug', false, true ) );

                        if ( ( strtolower( trim( $slug ) ) === "auto" || empty( $slug )) && !empty( $label ) ) {
                                $slug = $label;
                        }
                        if ( empty( $label ) && !empty( $slug ) ) {
                                $label = $slug;
                        }

                        $order_by = wpie_sanitize_field( $this->get_field_value( 'wpie_item_attribute_orderby', false, true ) );
                        $order_by = !empty( $order_by ) ? trim( strtolower( $order_by ) ) : 'menu_order';
                        if ( !in_array( $order_by, [ 'menu_order', 'name', 'name_num', 'id' ], true ) ) {

                                $order_by = 'menu_order';
                        }

                        $has_archives = wpie_sanitize_field( $this->get_field_value( 'wpie_item_attribute_public', false, true ) );
                        $has_archives = ((!empty( $has_archives )) && intval( $has_archives ) === 1) ? 1 : 0;

                        $attr = [
                                'name' => $label,
                                'slug' => $slug,
                                'order_by' => $order_by,
                                'has_archives' => $has_archives
                        ];

                        $attribute_id = $this->search_attribute( $attr );

                        if ( absint( $attribute_id ) > 0 ) {

                                $existing_attr = $this->get_attribute_by_id( $attribute_id );

                                if ( $existing_attr !== false ) {

                                        $attr[ 'id' ] = $attribute_id;

                                        if ( !$this->is_update_field( "name" ) ) {
                                                $attr[ "name" ] = $existing_attr->attribute_label;
                                        }
                                        if ( !$this->is_update_field( "slug" ) ) {
                                                $attr[ "slug" ] = $existing_attr->attribute_name;
                                        }
                                        if ( !$this->is_update_field( "orderby" ) ) {
                                                $attr[ "has_archives" ] = $existing_attr->attribute_public;
                                        }
                                        if ( !$this->is_update_field( "public" ) ) {
                                                $attr[ "order_by" ] = $existing_attr->attribute_orderby;
                                        }

                                        if (
                                                $attr[ 'name' ] !== $existing_attr->attribute_label ||
                                                $attr[ 'slug' ] !== $existing_attr->attribute_name ||
                                                $attr[ 'order_by' ] !== $existing_attr->attribute_orderby ||
                                                $attr[ 'has_archives' ] !== $existing_attr->attribute_public ) {

                                                $attribute_id = $this->create_attribute( $attr );
                                        }
                                }
                        } else {
                                $attribute_id = $this->create_attribute( $attr );
                        }

                        if ( is_wp_error( $attribute_id ) ) {
                                return $attribute_id;
                        }
                        $taxonomy = $wpdb->get_var( $wpdb->prepare( "SELECT attribute_name FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_id = %d", $attribute_id ) );
                }

                if ( (!empty( $taxonomy )) && is_string( $taxonomy ) ) {
                        $taxonomy = wc_attribute_taxonomy_name( preg_replace( '/^pa\_/', '', $taxonomy ) );
                }


                return $taxonomy;
        }

        private function create_attribute( $attribute = [], $prefix = 1 ) {

                $name = isset( $attribute[ 'name' ] ) ? $attribute[ 'name' ] : "";

                if ( strlen( $name ) >= 28 ) {
                        $name = substr( $name, 0, 28 );
                }

                $slug = isset( $attribute[ 'slug' ] ) ? $attribute[ 'slug' ] : $name;

                $slug = preg_replace( '/^pa\_/', '', wc_sanitize_taxonomy_name( $slug ) );

                $slug = $prefix > 1 ? $slug . "-" . $prefix : $slug;

                if ( wc_check_if_attribute_name_is_reserved( $slug ) || (taxonomy_exists( $slug ) && $this->is_new_item) ) {

                        $prefix++;
                        return $this->create_attribute( $attribute, $prefix );
                }

                $attribute_data = [
                        'id' => isset( $attribute[ 'id' ] ) ? absint( $attribute[ 'id' ] ) : 0,
                        'name' => $name,
                        'slug' => $slug,
                        'type' => isset( $attribute[ 'type' ] ) ? $attribute[ 'type' ] : "select",
                        'order_by' => isset( $attribute[ 'order_by' ] ) ? $attribute[ 'order_by' ] : "menu_order",
                        'has_archives' => isset( $attribute[ 'has_archives' ] ) && absint( $attribute[ 'has_archives' ] ) === 1 ? 1 : 0
                ];

                $id = wc_create_attribute( $attribute_data );

                if ( is_wp_error( $id ) ) {
                        return $id;
                }

                global $wpdb;

                $slug = $wpdb->get_var( $wpdb->prepare( "SELECT attribute_name FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_id = %d", $id ) );

                $slug = wc_attribute_taxonomy_name( $slug );

                if ( taxonomy_exists( $slug ) ) {
                        return $id;
                }
                // Register the taxonomy now so that the import works!
                $new_taxonomy = register_taxonomy(
                        $slug,
                        apply_filters( 'woocommerce_taxonomy_objects_' . $slug, [ 'product' ] ),
                        apply_filters(
                                'woocommerce_taxonomy_args_' . $slug,
                                [
                                        'hierarchical' => true,
                                        'show_ui' => false,
                                        'query_var' => true,
                                        'rewrite' => false,
                                ]
                        )
                );
                if ( is_wp_error( $new_taxonomy ) ) {
                        return $new_taxonomy;
                }

                return $id;
        }

        private function get_attribute_by_id( $attribute_id = 0 ) {

                if ( absint( $attribute_id ) < 1 ) {
                        return false;
                }

                global $wpdb;
                $attribute = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_id = %d LIMIT 1", absint( $attribute_id ) ) );

                if ( isset( $attribute->attribute_id ) ) {
                        return $attribute;
                }
                return false;
        }

        protected function search_attribute( $attribute = [] ) {

                global $wpdb;

                $attr_id = 0;

                $logic = empty( $this->get_field_value( 'wpie_existing_item_search_logic', true ) ) ? 'title' : wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic', true ) );

                if ( $logic == "slug" ) {

                        $slug = isset( $attribute[ 'slug' ] ) ? $attribute[ 'slug' ] : "";

                        if ( !empty( $slug ) ) {

                                $slug = preg_replace( '/^pa\_/', '', wc_sanitize_taxonomy_name( $slug ) );

                                $_attribute_id = $wpdb->get_var( $wpdb->prepare( "SELECT attribute_id FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = %s LIMIT 1", $slug ) );

                                if ( $_attribute_id && absint( $_attribute_id ) > 0 ) {
                                        $attr_id = absint( $_attribute_id );
                                }
                                unset( $_attribute_id );
                        }
                        unset( $slug );
                } elseif ( $logic == "name" ) {

                        $name = isset( $attribute[ 'name' ] ) ? $attribute[ 'name' ] : "";

                        if ( !empty( $name ) ) {
                                $_attribute_id = $wpdb->get_var( $wpdb->prepare( "SELECT attribute_id FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_label = %s LIMIT 1", $name ) );

                                if ( $_attribute_id && absint( $_attribute_id ) > 0 ) {
                                        $attr_id = absint( $_attribute_id );
                                }
                                unset( $_attribute_id );
                        }
                        unset( $name );
                }

                unset( $logic );

                return $attr_id;
        }

        protected function search_duplicate_item() {

                $taxonomy = $this->get_taxonomy();

                if ( $taxonomy === false || is_wp_error( $taxonomy ) ) {

                        return;
                }

                global $wpdb;

                $logic = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_term_search_logic', true ) );

                if ( $logic === "id" ) {

                        $duplicate_id = absint( wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_term_search_logic_id' ) ) );

                        if ( $duplicate_id > 0 ) {

                                $term = get_term_by( 'id', $duplicate_id, $taxonomy );

                                if ( !empty( $term ) && !is_wp_error( $term ) ) {
                                        $this->existing_item_id = $duplicate_id;
                                }
                                unset( $term );
                        }
                        unset( $duplicate_id );
                } elseif ( $logic === "slug" ) {

                        $label = wpie_sanitize_field( $this->get_field_value( 'wpie_item_term_name' ) );

                        $slug_method = wpie_sanitize_field( $this->get_field_value( 'wpie_item_term_slug' ) );

                        $slug = "";

                        if ( ( strtolower( trim( $slug_method ) )) === "auto" && !empty( $label ) ) {
                                $slug = $label;
                        } elseif ( strtolower( trim( $slug_method ) ) === "as_specified" ) {
                                $slug = wpie_sanitize_field( $this->get_field_value( 'wpie_item_term_slug_as_specified_data' ) );
                                if ( empty( $slug ) ) {
                                        $slug = $label;
                                }
                        }

                        if ( !empty( $slug ) ) {

                                $term = get_term_by( 'slug', $slug, $taxonomy );

                                if ( !empty( $term ) && !is_wp_error( $term ) ) {
                                        $this->existing_item_id = $term->term_id;
                                }
                                unset( $term );
                        }
                        unset( $slug );
                } elseif ( $logic === "name" ) {

                        $name = wpie_sanitize_field( $this->get_field_value( 'wpie_item_term_name' ) );

                        if ( !empty( $name ) ) {

                                $term = get_term_by( 'name', $name, $taxonomy );
                                if ( !empty( $term ) && !is_wp_error( $term ) ) {
                                        $this->existing_item_id = $term->term_id;
                                }
                                unset( $term );
                        }

                        unset( $name );
                } elseif ( $logic === "cf" ) {

                        $meta_key = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_term_search_logic_cf_key' ) );

                        $meta_val = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_term_search_logic_cf_value' ) );

                        if ( !empty( $meta_key ) ) {

                                $args = array(
                                        'taxonomy' => $taxonomy,
                                        'number' => 1,
                                        'hide_empty' => false,
                                        'meta_query' => array(
                                                array(
                                                        'key' => $meta_key,
                                                        'value' => $meta_val,
                                                        'compare' => '='
                                                )
                                        )
                                );

                                global $wp_version;

                                if ( version_compare( $wp_version, '4.5.0', '<' ) ) {

                                        $terms = get_terms( $taxonomy, $args );
                                } else {
                                        $terms = get_terms( $args );
                                }

                                if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
                                        foreach ( $terms as $term ) {
                                                $this->existing_item_id = $term->term_id;
                                                break;
                                        }
                                }
                                unset( $terms, $args );
                        }

                        unset( $meta_key, $meta_val );
                }

                unset( $taxonomy, $logic );
        }

}
