<?php


namespace wpie\export\polylang;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export-base.php' ) ) {

        require_once(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export-base.php');
}

class WPIE_Polylang_Export extends \wpie\export\base\WPIE_Export_Base {

        private $default_language;
        private $active_language;

        public function __construct() {
                
        }

        public function pre_process_fields( &$export_fields = array(), $export_type = array() ) {

                if ( !isset( $export_fields[ 'standard' ] ) ) {
                        $export_fields[ 'standard' ] = array();
                }
                if ( !isset( $export_fields[ 'standard' ][ 'data' ] ) ) {
                        $export_fields[ 'standard' ][ 'data' ] = array();
                }
                $export_fields[ 'standard' ][ 'data' ][] = array(
                        'name' => 'Polylang Translation ID',
                        'type' => 'polylang_trid',
                );
                if ( in_array( "taxonomies", $export_type ) ) {
                        $export_fields[ 'standard' ][ 'data' ][] = array(
                                'name' => 'Polylang Translation Slug',
                                'type' => 'polylang_translation_slug',
                        );
                } else {
                        $export_fields[ 'standard' ][ 'data' ][] = array(
                                'name' => 'Polylang Translation Title',
                                'type' => 'polylang_translation_title',
                        );
                }
                $export_fields[ 'standard' ][ 'data' ][] = array(
                        'name' => 'Polylang Language',
                        'type' => 'polylang_lang',
                );
        }

        public function init_process( $template_options = [] ) {

                $this->active_language = \PLL()->curlang;

                $this->default_language = \pll_default_language();

                if ( isset( $template_options[ 'wpie_polylang_lang' ] ) && !empty( $template_options[ 'wpie_polylang_lang' ] ) ) {
                        \PLL()->curlang = \PLL()->model->get_language( wpie_sanitize_field( $template_options[ 'wpie_polylang_lang' ] ) );
                }
        }

        public function process_addon_data( &$export_data = array(), $field_type = "", $field_name = "", $field_option = array(), $item = null, $site_date_format = "" ) {

                global $wp_taxonomies;

                if ( $field_type && in_array( $field_type, array( "polylang_trid", "polylang_lang", "polylang_translation_title", "polylang_translation_slug" ) ) ) {

                        $is_php = isset( $field_option[ 'isPhp' ] ) ? wpie_sanitize_field( $field_option[ 'isPhp' ] ) == 1 : false;

                        $php_func = isset( $field_option[ 'phpFun' ] ) ? wpie_sanitize_field( $field_option[ 'phpFun' ] ) : "";

                        $item_id = 0;

                        $item_type = "";

                        $element_type = "";

                        if ( isset( $item->term_taxonomy_id ) ) {
                                $item_id = $item->term_taxonomy_id;
                                $item_type = "taxonomy";
                                $element_type = isset( $item->taxonomy ) ? $item->taxonomy : "category";
                        } elseif ( isset( $item->ID ) && isset( $item->post_type ) ) {
                                $item_id = $item->ID;
                                $item_type = "post";
                                $element_type = $item->post_type;
                        } else {
                                return;
                        }

                        switch ( $field_type ) {

                                case 'polylang_trid':

                                        if ( $item_type === "post" ) {
                                                $polylang_original_id = \PLL()->model->post->get_translation( $item_id, $this->default_language );
                                        } else {
                                                $polylang_original_id = \PLL()->model->term->get_translation( $item_id, $this->default_language );
                                        }

                                        $polylang_original_id = ($polylang_original_id === false || absint( $polylang_original_id ) === 0 ) ? $item_id : $polylang_original_id;

                                        $export_data[ $field_name ] = apply_filters( 'wpie_export_polylang_trid_field', $this->apply_user_function( ($polylang_original_id ), $is_php, $php_func ), $item );

                                        break;

                                case 'polylang_lang':

                                        if ( $item_type === "post" ) {

                                                $language = \pll_get_post_language( $item_id );
                                        } else {
                                                $language = \pll_get_term_language( $item_id );
                                        }

                                        $export_data[ $field_name ] = apply_filters( 'wpie_export_polylang_language_code', $this->apply_user_function( $language, $is_php, $php_func ), $item );

                                        unset( $language );

                                        break;
                                case 'polylang_translation_title':

                                        $translations = \pll_get_post_translations( $item_id );

                                        $title = "||";

                                        if ( !empty( $translations ) ) {
                                                foreach ( $translations as $lang => $postId ) {
                                                        if ( $item_id === $postId ) {
                                                                continue;
                                                        }
                                                        $title .= "||lang=>" . $lang . "=>" . \get_the_title( $postId );
                                                }
                                        }

                                        $title = trim( $title, "|" );

                                        $export_data[ $field_name ] = apply_filters( 'wpie_export_polylang_language_title', $this->apply_user_function( $title, $is_php, $php_func ), $item );

                                        unset( $translations, $title );

                                        break;
                                case 'polylang_translation_slug':

                                        $translations = \pll_get_term_translations( $item_id );

                                        $title = "||";

                                        if ( !empty( $translations ) ) {

                                                foreach ( $translations as $lang => $ttId ) {

                                                        if ( $item_id === $ttId ) {
                                                                continue;
                                                        }
                                                        $taxonomy = get_term_by( "term_taxonomy_id", $ttId, $item->taxonomy );

                                                        $title .= "||lang=>" . $lang . "=>" . (isset( $taxonomy->slug ) ? $taxonomy->slug : "");
                                                }
                                        }

                                        $title = trim( $title, "|" );

                                        $export_data[ $field_name ] = apply_filters( 'wpie_export_polylang_language_slug', $this->apply_user_function( $title, $is_php, $php_func ), $item );

                                        unset( $translations, $title );

                                        break;
                        }

                        unset( $is_php, $php_func );
                }
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
