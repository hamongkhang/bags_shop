<?php


namespace wpie\import\wpml;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/extensions/wpml/class-wpml-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/extensions/wpml/class-wpml-base.php');
}

class WPML_Attribute extends WPML_Base {

        private $taxonomy = false;

        protected function preProcessData() {

                $this->taxonomy = false;

                $this->setTaxonomy();

                if ( $this->taxonomy === false || !$this->is_translatable_taxonomy( $this->taxonomy ) ) {
                        return false;
                }

                $this->elementType = 'tax_' . $this->taxonomy;

                return true;
        }

        private function setTaxonomy() {

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

                        $label = \wpie_sanitize_field( $this->get_field_value( 'wpie_item_name' ) );

                        if ( $searchBy === "name" && !empty( $label ) ) {

                                $tax = $wpdb->get_var( $wpdb->prepare( "SELECT attribute_name FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_label = %s", $label ) );

                                if ( !empty( $tax ) ) {
                                        $this->taxonomy = $prefix . $tax;
                                }
                        } else {

                                $slug_method = \wpie_sanitize_field( $this->get_field_value( 'wpie_item_slug' ) );

                                $slug = "";

                                if ( ( strtolower( trim( $slug_method ) )) === "auto" && !empty( $label ) ) {
                                        $slug = $label;
                                } elseif ( strtolower( trim( $slug_method ) ) === "as_specified" ) {
                                        $slug = \wpie_sanitize_field( $this->get_field_value( 'wpie_item_slug_as_specified_data' ) );
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

        protected function searchExisting() {

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

        protected function searchTranslation() {

                if ( $this->taxonomy === false ) {

                        global $wpieAttrTaxonomy;

                        $this->taxonomy = $wpieAttrTaxonomy;

                        $this->elementType = 'tax_' . $this->taxonomy;
                }

                global $wp_version;

                $this->remove_wpml_term_filters();

                $logic = wpie_sanitize_field( $this->get_field_value( 'wpie_item_wpml_default_item', true ) );

                $taxonomy_items = false;

                if ( $logic == "id" ) {

                        $item_id = absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_wpml_trid' ) ) );

                        if ( $item_id > 0 ) {

                                $term = get_term_by( 'id', $item_id, $this->taxonomy );

                                if ( !empty( $term ) && !is_wp_error( $term ) ) {
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
                } elseif ( $logic == "name" || $logic == "title" ) {

                        $name = wpie_sanitize_field( $this->get_field_value( 'wpie_item_wpml_translation_title' ) );

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
