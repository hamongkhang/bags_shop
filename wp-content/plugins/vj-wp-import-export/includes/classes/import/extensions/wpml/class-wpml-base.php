<?php


namespace wpie\import\wpml;

use \wpie\import\base\WPIE_Import_Base;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php');
}

abstract class WPML_Base extends WPIE_Import_Base {

        protected $default_language;
        protected $active_languages;
        protected $current_language;
        protected $importLang;
        protected $elementType;

        abstract protected function searchExisting();

        abstract protected function searchTranslation();

        abstract protected function preProcessData();

        public function __construct( $wpie_import_option = [], $import_type = "" ) {

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

                $this->importLang = is_array( $this->importLang ) && isset( $this->importLang[ 'code' ] ) ? $this->importLang[ 'code' ] : $this->importLang;
        }

        public function before_item_import( &$wpie_import_record = [], &$existing_item_id = 0, &$is_new_item = true, &$is_search_duplicates ) {

                $this->wpie_import_record = $wpie_import_record;

                $this->setImportLang();

                if ( !$this->preProcessData() ) {
                        return;
                }

                $translations = $this->searchExisting();

                if ( !empty( $translations ) && $translations !== 0 && is_numeric( $translations ) ) {
                        $translations = [ $translations ];
                }

                if ( is_array( $translations ) && !empty( $translations ) && $this->importLang !== false ) {

                        global $wpdb;

                        if ( $this->elementType === "post_product" ) {
                                $elementType = $wpdb->prepare("%s", "post_product" ) . "," . $wpdb->prepare("%s", "post_product_variation" );
                        } else {
                                $elementType = $wpdb->prepare("%s", $this->elementType );
                        }

                        $element_id = $wpdb->get_var(
                                $wpdb->prepare(
                                        "SELECT element_id FROM {$wpdb->prefix}icl_translations 
                                                WHERE element_id IN ('" . implode( "','", $translations ) . "') AND 
                                                language_code = %s AND
                                                element_type IN (" . $elementType . ")",
                                        $this->importLang
                                )
                        );

                        if ( !empty( $element_id ) && $element_id > 0 ) {
                                $is_new_item      = false;
                                $existing_item_id = $element_id;
                        }
                }

                $is_search_duplicates = false;
        }

        public function after_item_import( $item_id = 0, $item = null, $is_new_item = true ) {

                $this->item_id = $item_id;

                $this->item = $item;

                $this->is_new_item = $is_new_item;

                $this->updateTranslations();
        }

        protected function updateTranslations() {

                global $sitepress, $wpdb;

                if ( $this->elementType === "post_product" ) {
                        $productData = \wc_get_product( $this->item_id );

                        if ( $productData->get_type() === "variation" ) {
                                $this->elementType = "post_product_variation";
                        }
                }

                $translations = $this->searchTranslation();

                $old_trid = 0;

                if ( !$this->is_new_item ) {

                        $old_trid = $wpdb->get_var(
                                $wpdb->prepare( "SELECT trid FROM {$wpdb->prefix}icl_translations 
                                                WHERE element_id = %d AND 
                                                language_code = %s AND
                                                element_type = %s",
                                        $this->item_id,
                                        $this->importLang,
                                        $this->elementType
                                ) );
                }

                $trid = 0;

                if ( !empty( $translations ) ) {

                        $default_lang = $this->default_language;

                        if ( is_array( $translations ) ) {

                                if ( ($item_key = array_search( $this->item_id, $translations )) !== false ) {
                                        unset( $translations[ $item_key ] );
                                }

                                $trid = $wpdb->get_var(
                                        $wpdb->prepare( "SELECT trid FROM {$wpdb->prefix}icl_translations 
                                                        WHERE element_id IN ('" . implode( "','", $translations ) . "') AND 
                                                        language_code = %s AND
                                                        element_type = %s Limit 0,1",
                                                $this->default_language,
                                                $this->elementType
                                        ) );

                                if ( empty( $trid ) ) {
                                        $translation = $wpdb->get_row(
                                                $wpdb->prepare( "SELECT trid,language_code,source_language_code FROM {$wpdb->prefix}icl_translations 
                                                                WHERE element_id IN ('" . implode( "','", $translations ) . "') AND 
                                                                element_type = %s Limit 0,1",
                                                        $this->elementType
                                                ) );

                                        if ( $translation && isset( $translation->trid ) ) {
                                                $trid         = $translation->trid;
                                                $default_lang = empty( $translation->source_language_code ) ? $translation->language_code : $translation->source_language_code;
                                        }
                                }
                        } elseif ( is_numeric( $translations ) && absint( $translations ) > 0 ) {
                                $trid = $wpdb->get_var(
                                        $wpdb->prepare(
                                                "SELECT trid FROM {$wpdb->prefix}icl_translations
                                                WHERE element_id = %d AND element_type = %s Limit 0,1",
                                                absint( $translations ),
                                                $this->elementType
                                        ) );
                        }

                        if ( !empty( $trid ) && absint( $trid ) > 0 && $old_trid !== $trid ) {
                                $sitepress->set_element_language_details( $this->item_id, $this->elementType, $trid, $this->importLang, $default_lang );
                        }
                }

                if ( empty( $trid ) || absint( $trid ) === 0 ) {
                        $sitepress->set_element_language_details( $this->item_id, $this->elementType, false, $this->importLang );
                }

                unset( $trid, $translations );
        }

        protected function remove_wpml_term_filters() {

                global $sitepress;

                remove_filter( 'get_terms_args', array( $sitepress, 'get_terms_args_filter' ) );

                remove_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1 );

                remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ) );
        }

        protected function add_wpml_term_filters() {

                global $sitepress;

                add_filter( 'get_terms_args', array( $sitepress, 'get_terms_args_filter' ), 10, 2 );

                add_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1, 1 );

                add_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ), 10, 3 );
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
