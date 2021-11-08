<?php


namespace wpie\export\acf;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

class WPIE_ACF {

        private $cf_list = [];

        public function __construct() {

                add_filter( 'wpie_pre_item_meta', array( $this, "wpie_pre_item_meta" ), 10, 2 );
        }

        public function wpie_pre_item_meta( $meta = [], $key = "" ) {

                if ( !empty( $this->cf_list ) && in_array( $key, $this->cf_list ) ) {

                        $meta[ "is_acf" ] = 1;
                }
                return $meta;
        }

        public function pre_process_fields( &$export_fields = [], $export_type = [] ) {

                $this->cf_list = [];

                $saved_acfs = get_posts( array( 'posts_per_page' => -1, 'post_type' => 'acf-field-group' ) );

                if ( function_exists( '\acf_local' ) ) {
                        $acfs = \acf_local()->groups;
                } elseif ( function_exists( '\acf_get_local_field_groups' ) ) {
                        $acfs = \acf_get_local_field_groups();
                }

                if ( !empty( $saved_acfs ) ) {
                        foreach ( $saved_acfs as $key => $obj ) {
                                if ( isset( $obj->post_name ) && is_array( $acfs ) && !isset( $acfs[ $obj->post_name ] ) ) {
                                        $acfs[ $obj->post_name ] = $obj;
                                }
                        }
                }

                unset( $saved_acfs );

                $duplicate_fields = [];

                if ( !empty( $acfs ) ) {

                        $count = 0;

                        foreach ( $acfs as $key => $acf ) {

                                $title = isset( $acf->post_title ) ? $acf->post_title : (isset( $acf->post_excerpt ) ? $acf->post_excerpt : "");

                                $group_id = isset( $acf->ID ) ? $acf->ID : 0;

                                $field_key = 'wpie_acf_field_' . $count;

                                $export_fields[ $field_key ][ "title" ] = "ACF - " . $title;

                                if ( !empty( $group_id ) && is_numeric( $group_id ) ) {

                                        $acf_fields = get_posts( array( 'posts_per_page' => -1,
                                                'post_type' => 'acf-field', 'post_parent' => $group_id,
                                                'post_status' => 'publish', 'orderby' => 'menu_order',
                                                'order' => 'ASC' ) );

                                        if ( !empty( $acf_fields ) ) {

                                                foreach ( $acf_fields as $field ) {

                                                        $field_name = isset( $field->post_title ) ? $field->post_title : (isset( $field->post_excerpt ) ? $field->post_excerpt : "");

                                                        if ( empty( $field_name ) ) {
                                                                continue;
                                                        }

                                                        $_key = isset( $field->post_name ) ? $field->post_name : "";

                                                        $options = isset( $field->post_content ) ? maybe_unserialize( $field->post_content ) : [];

                                                        $field_id = isset( $field->ID ) ? $field->ID : 0;

                                                        $export_fields[ $field_key ][ 'data' ][] = array(
                                                                'name' => $field_name,
                                                                'options' => $options,
                                                                'type' => 'wpie-acf',
                                                                'id' => $field_id,
                                                                'acfKey' => $_key
                                                        );

                                                        $field_type = isset( $options[ 'type' ] ) ? $options[ 'type' ] : "";

                                                        $meta_key = isset( $field->post_excerpt ) ? $field->post_excerpt : "";

                                                        $duplicate_fields[] = $meta_key;

                                                        $duplicate_fields[] = "_" . $meta_key;

                                                        if ( !empty( $field_type ) && in_array( $field_type, [
                                                                        "group",
                                                                        "clone",
                                                                        "flexible_content",
                                                                        "repeater" ] ) ) {

                                                                $_extra_remove_fields = $this->add_cf_options( $field_id );

                                                                if ( is_array( $_extra_remove_fields ) && !empty( $_extra_remove_fields ) ) {

                                                                        $duplicate_fields = array_merge( $duplicate_fields, $_extra_remove_fields );
                                                                }
                                                        }

                                                        unset( $meta_key, $field_name, $options, $type, $_key );
                                                }
                                        }
                                        unset( $acf_fields );
                                }

                                $count++;

                                unset( $title, $group_id, $field_key );
                        }
                        unset( $count );
                }
                unset( $acfs );

                if ( !empty( $duplicate_fields ) ) {

                        $this->cf_list = [];

                        $found_data = [];

                        foreach ( $duplicate_fields as $cf_field ) {

                                if ( empty( $cf_field ) ) {
                                        continue;
                                }
                                if ( is_array( $cf_field ) ) {

                                        $_key = isset( $cf_field[ 'key' ] ) ? $cf_field[ 'key' ] : "";

                                        $regexp = isset( $cf_field[ 'regexp' ] ) ? $cf_field[ 'regexp' ] : "";

                                        if ( !empty( $regexp ) ) {

                                                $matches = preg_grep( $regexp, $export_fields[ 'meta' ] );

                                                if ( !empty( $matches ) ) {
                                                        foreach ( $matches as $match ) {
                                                                $found_data[] = "_" . $match;
                                                                $this->cf_list[] = $match;
                                                        }
                                                }
                                        } else if ( !empty( $_key ) ) {
                                                $found_data[] = "_" . $_key;
                                                $this->cf_list[] = $_key;
                                        }
                                } else {
                                        $found_data[] = $cf_field;
                                }
                        }

                        if ( !empty( $found_data ) ) {

                                $this->cf_list = array_unique( $this->cf_list );

                                $export_fields[ 'meta' ] = array_diff( $export_fields[ 'meta' ], array_unique( $found_data ) );
                        }
                        unset( $found_data );
                }
        }

        private function add_cf_options( $field_id ) {

                $field_list = [];

                if ( !empty( $field_id ) ) {

                        $field = acf_get_field( $field_id );

                        if ( $field ) {

                                $type = isset( $field[ 'type' ] ) ? trim( strtolower( sanitize_text_field( $field[ 'type' ] ) ) ) : "";

                                if ( !empty( $type ) ) {

                                        switch ( $type ) {
                                                case 'clone':
                                                case 'group':

                                                        $name = isset( $field[ 'name' ] ) ? $field[ 'name' ] : "";

                                                        $sub_fields = isset( $field[ 'sub_fields' ] ) ? $field[ 'sub_fields' ] : [];

                                                        if ( !empty( $sub_fields ) ) {

                                                                foreach ( $sub_fields as $s_field ) {

                                                                        $s_field_id = isset( $s_field[ 'ID' ] ) ? $s_field[ 'ID' ] : "";

                                                                        $new_field = $this->add_cf_options( $s_field_id );

                                                                        if ( !empty( $new_field ) ) {
                                                                                $field_list[] = [ "key" => $name . "_" . $new_field ];
                                                                        }
                                                                }
                                                        }

                                                        break;
                                                case 'repeater':

                                                        $name = isset( $field[ 'name' ] ) ? $field[ 'name' ] : "";

                                                        $sub_fields = isset( $field[ 'sub_fields' ] ) ? $field[ 'sub_fields' ] : [
                                                        ];

                                                        if ( !empty( $sub_fields ) ) {

                                                                foreach ( $sub_fields as $s_key => $s_field ) {

                                                                        $s_field_id = isset( $s_field[ 'ID' ] ) ? $s_field[ 'ID' ] : "";

                                                                        $new_field = $this->add_cf_options( $s_field_id );

                                                                        if ( !empty( $new_field ) ) {
                                                                                $field_list[] = [
                                                                                        "key" => $name . "_" . $s_key . "_" . $new_field,
                                                                                        "regexp" => "/^" . $name . "_.*_" . $new_field . "$/" ];
                                                                        }
                                                                }
                                                        }

                                                        break;
                                                case 'flexible_content':

                                                        $name = isset( $field[ 'name' ] ) ? $field[ 'name' ] : "";

                                                        $layouts = isset( $field[ 'layouts' ] ) ? $field[ 'layouts' ] : [];

                                                        if ( !empty( $layouts ) ) {
                                                                foreach ( $layouts as $_layout ) {

                                                                        $sub_fields = isset( $_layout[ 'sub_fields' ] ) ? $_layout[ 'sub_fields' ] : [];

                                                                        if ( !empty( $sub_fields ) ) {

                                                                                foreach ( $sub_fields as $s_key => $s_field ) {

                                                                                        $s_field_id = isset( $s_field[ 'ID' ] ) ? $s_field[ 'ID' ] : "";

                                                                                        $new_field = $this->add_cf_options( $s_field_id );

                                                                                        if ( !empty( $new_field ) ) {
                                                                                                $field_list[] = [
                                                                                                        "key" => $name . "_" . $s_key . "_" . $new_field,
                                                                                                        "regexp" => "/^" . $name . "_.*_" . $new_field . "$/" ];
                                                                                        }
                                                                                }
                                                                        }
                                                                }
                                                        }


                                                        break;

                                                default :
                                                        $name = isset( $field[ 'name' ] ) ? $field[ 'name' ] : "";

                                                        return $name;

                                                        break;
                                        }
                                }
                        }
                }
                return $field_list;
        }

        public function change_export_labels( &$export_labels = array(), $field_type = "", $field_name = "", $field_label = "", $field_option = array() ) {

                if ( $field_type == "wpie-acf" ) {

                        $options = isset( $field_option[ 'options' ] ) ? $field_option[ 'options' ] : "";

                        $acf_field = isset( $options[ 'type' ] ) ? $options[ 'type' ] : "";

                        if ( !empty( $acf_field ) ) {

                                switch ( $acf_field ) {
                                        case 'link':
                                                unset( $export_labels[ $field_name ] );
                                                $export_labels[ $field_name . "_url" ] = $field_label . " URL";
                                                $export_labels[ $field_name . "_title" ] = $field_label . " Title";
                                                $export_labels[ $field_name . "_target" ] = $field_label . " Target";
                                                break;
                                        case 'google_map':
                                                unset( $export_labels[ $field_name ] );
                                                $export_labels[ $field_name . "_address" ] = $field_label . " Address";
                                                $export_labels[ $field_name . "_lat" ] = $field_label . " Lat";
                                                $export_labels[ $field_name . "_lng" ] = $field_label . " Lng";
                                                break;
                                }
                        }
                }
        }

        public function process_addon_data( &$export_data = array(), $field_type = "", $field_name = "", $field_option = array(), $item = null, $site_date_format = "" ) {

                if ( $field_type == "wpie-acf" ) {

                        $is_php = isset( $field_option[ 'isPhp' ] ) ? wpie_sanitize_field( $field_option[ 'isPhp' ] ) == 1 : false;

                        $php_func = isset( $field_option[ 'phpFun' ] ) ? wpie_sanitize_field( $field_option[ 'phpFun' ] ) : "";

                        $date_type = isset( $field_option[ 'dateType' ] ) ? wpie_sanitize_field( $field_option[ 'dateType' ] ) : "";

                        $date_format = isset( $field_option[ 'dateFormat' ] ) ? wpie_sanitize_field( $field_option[ 'dateFormat' ] ) : "";

                        $options = isset( $field_option[ 'options' ] ) ? $field_option[ 'options' ] : "";

                        $acf_field = isset( $options[ 'type' ] ) ? $options[ 'type' ] : "";

                        $acf_field_key = isset( $field_option[ 'acfKey' ] ) ? $field_option[ 'acfKey' ] : "";

                        $item_id = 0;

                        if ( isset( $item->term_id ) ) {
                                $item_id = $item->taxonomy . "_" . $item->term_id;
                        } elseif ( isset( $item->comment_ID ) ) {
                                $item_id = 'comment_' . $item->comment_ID;
                        } elseif ( isset( $item->user_login ) && isset( $item->ID ) ) {
                                $item_id = "user_" . $item->ID;
                        } elseif ( isset( $item->ID ) ) {
                                $item_id = $item->ID;
                        }

                        if ( !empty( $acf_field ) ) {

                                $value = "";

                                $is_skip = false;

                                switch ( $acf_field ) {

                                        case 'repeater':
                                        case 'flexible_content':
                                        case 'clone':
                                        case 'group':
                                                $is_skip = true;

                                                $repeater = get_field( $acf_field_key, $item_id, false );

                                                if ( !empty( $repeater ) ) {
                                                        $value = json_encode( $repeater );
                                                }
                                                $export_data[ $field_name ] = $value;

                                                break;
                                        case 'google_map':

                                                $is_skip = true;

                                                $gmap_data = get_field( $acf_field_key, $item_id );

                                                if ( is_array( $gmap_data ) && !empty( $gmap_data ) ) {

                                                        $export_data[ $field_name . "_address" ] = isset( $gmap_data[ 'address' ] ) ? $gmap_data[ 'address' ] : "";
                                                        $export_data[ $field_name . "_lat" ] = isset( $gmap_data[ 'lat' ] ) ? $gmap_data[ 'lat' ] : "";
                                                        $export_data[ $field_name . "_lng" ] = isset( $gmap_data[ 'lng' ] ) ? $gmap_data[ 'lng' ] : "";
                                                        unset( $export_data[ $field_name ] );
                                                }
                                                unset( $gmap_data );
                                                break;
                                        case 'link':

                                                $is_skip = true;

                                                $link_data = get_field( $acf_field_key, $item_id );

                                                if ( is_array( $link_data ) ) {

                                                        $_url = isset( $link_data[ 'url' ] ) ? $link_data[ 'url' ] : "";
                                                        $_title = isset( $link_data[ 'title' ] ) ? $link_data[ 'title' ] : "";
                                                        $_target = isset( $link_data[ 'target' ] ) ? $link_data[ 'target' ] : "";
                                                } else {
                                                        $_url = $link_data;
                                                        $_title = "";
                                                        $_target = "";
                                                }

                                                $export_data[ $field_name . "_url" ] = $_url;
                                                $export_data[ $field_name . "_title" ] = $_title;
                                                $export_data[ $field_name . "_target" ] = $_target;
                                                unset( $export_data[ $field_name ], $link_data );

                                                break;
                                        case 'page_link':

                                                $page_link = get_field( $acf_field_key, $item_id, false );

                                                if ( !empty( $page_link ) ) {

                                                        if ( is_array( $page_link ) ) {

                                                                $page_link_data = array();

                                                                foreach ( $page_link as $link_ids ) {
                                                                        $page_link_data[] = get_the_title( $link_ids );
                                                                }
                                                                if ( !empty( $page_link_data ) ) {
                                                                        $value = implode( ",", $page_link_data );
                                                                }
                                                                unset( $page_link_data );
                                                        } else {
                                                                $value = $page_link;
                                                        }
                                                }

                                                break;
                                        case 'textarea':
                                        case 'oembed':
                                        case 'wysiwyg':
                                        case 'wp_wysiwyg':
                                        case 'date_time_picker':
                                        case 'date_picker':

                                                $value = get_field( $acf_field_key, $item_id, false );

                                                break;
                                        case 'message':

                                                $value = isset( $options[ 'message' ] ) && !empty( $options[ 'message' ] ) ? $options[ 'message' ] : "";

                                                break;

                                        default:

                                                $value = get_field( $acf_field_key, $item_id );

                                                break;
                                }

                                if ( $is_skip === false ) {

                                        $value = stripslashes_deep( $value );

                                        $export_data[ $field_name ] = $this->format_acf_field( $value, $field_option, $item_id );
                                }

                                unset( $value );
                        }

                        unset( $is_php, $php_func, $date_type, $date_format, $options, $acf_field, $acf_field_key );
                }
        }

        private function format_acf_field( $value = "", $field_option = array(), $post_id = false, $is_return = false ) {

                $options = isset( $field_option[ 'options' ] ) ? $field_option[ 'options' ] : array();

                $field_type = isset( $options[ 'type' ] ) ? $options[ 'type' ] : "";

                $field_key = isset( $field_option[ 'acfKey' ] ) ? $field_option[ 'acfKey' ] : "";

                if ( !empty( $field_type ) && !empty( $field_key ) ) {

                        if ( $field_type === "true_false" ) {
                                $value = ($value) ? "yes" : "no";
                        } elseif ( empty( $value ) ) {
                                $value = "";
                        } elseif ( !empty( $value ) ) {

                                $delimiter = ",";

                                switch ( $field_type ) {

                                        case 'select':
                                        case 'checkbox':
                                        case 'page_link':
                                        case 'radio':
                                        case 'button_group':

                                                if ( is_array( $value ) ) {

                                                        $data = array_values( $value );

                                                        if ( isset( $data[ 0 ] ) && is_array( $data[ 0 ] ) ) {

                                                                $new_data = array();

                                                                foreach ( $data as $_key => $_value ) {
                                                                        $new_data[] = isset( $_value[ 'value' ] ) ? $_value[ 'value' ] : "";
                                                                }

                                                                $value = implode( $delimiter, $new_data );
                                                        } elseif ( isset( $data[ 'value' ] ) ) {
                                                                $value = $data[ 'value' ];
                                                        } else {

                                                                $value = implode( $delimiter, $data );
                                                        }
                                                        unset( $data );
                                                }

                                                break;

                                        case 'date_picker':
                                                $value = date( 'Y-m-d', strtotime( $value ) );

                                                break;

                                        case 'gallery':

                                                if ( is_array( $value ) ) {

                                                        $gallery = array();

                                                        foreach ( $value as $_key => $_value ) {
                                                                if ( is_numeric( $_value ) ) {

                                                                        $gallery[] = stripslashes_deep( wp_get_attachment_url( $_value ) );
                                                                } elseif ( is_array( $_value ) ) {

                                                                        $gallery[] = isset( $_value[ 'url' ] ) && !empty( $_value[ 'url' ] ) ? stripslashes_deep( $_value[ 'url' ] ) : "";
                                                                }
                                                        }
                                                        $value = implode( $delimiter, $gallery );

                                                        unset( $gallery );
                                                }

                                                break;

                                        case 'file':
                                        case 'image':

                                                if ( is_numeric( $value ) ) {

                                                        $value = wp_get_attachment_url( $value );
                                                } elseif ( is_array( $value ) ) {

                                                        $value = isset( $value[ 'url' ] ) && !empty( $value[ 'url' ] ) ? $value[ 'url' ] : "";
                                                }
                                                break;

                                        case 'post_object':
                                        case 'relationship':

                                                if ( isset( $options[ 'multiple' ] ) && !empty( $options[ 'multiple' ] ) || ($field_type == "relationship" && is_array( $value )) ) {

                                                        $post_data = [];

                                                        if ( is_array( $value ) ) {

                                                                foreach ( $value as $key => $pid ) {

                                                                        if ( is_numeric( $pid ) ) {

                                                                                $entry = get_post( $pid );

                                                                                if ( $entry ) {
                                                                                        $post_data[] = $entry->post_title;
                                                                                }
                                                                                unset( $entry );
                                                                        } elseif ( isset( $pid->post_title ) ) {
                                                                                $post_data[] = $pid->post_title;
                                                                        }
                                                                }
                                                        }
                                                        $value = implode( $delimiter, $post_data );

                                                        unset( $post_data );
                                                } else {
                                                        if ( is_numeric( $value ) ) {

                                                                $entry = get_post( $value );

                                                                if ( $entry ) {
                                                                        $value = $entry->post_title;
                                                                }
                                                                unset( $entry );
                                                        } elseif ( is_array( $value ) ) {
                                                                
                                                        } elseif ( isset( $value->post_title ) ) {
                                                                $value = $value->post_title;
                                                        }
                                                }

                                        case 'taxonomy':

                                                if ( is_array( $value ) ) {

                                                        $term_data = array();

                                                        foreach ( $value as $key => $tid ) {

                                                                if ( is_object( $tid ) && isset( $tid->name ) ) {
                                                                        $term_data[] = $tid->name;
                                                                } else {
                                                                        $entry = get_term( $tid, $options[ 'taxonomy' ] );

                                                                        if ( $entry && !is_wp_error( $entry ) ) {

                                                                                $term_data[] = $entry->name;
                                                                        }
                                                                        unset( $entry );
                                                                }
                                                        }

                                                        $value = implode( $delimiter, $term_data );

                                                        unset( $term_data );
                                                }

                                                break;

                                        case 'user':

                                                if ( is_array( $value ) ) {

                                                        $user_data = array();

                                                        foreach ( $value as $key => $user ) {

                                                                if ( is_array( $user ) && isset( $user[ 'user_email' ] ) ) {
                                                                        $user_data[] = $user[ 'user_email' ];
                                                                } else {
                                                                        $user_entry = get_user_by( 'ID', $user );
                                                                        if ( $user_entry ) {
                                                                                $user_data[] = $user_entry->user_email;
                                                                        }
                                                                        unset( $user_entry );
                                                                }
                                                        }

                                                        $value = implode( $delimiter, $user_data );

                                                        unset( $user_data );
                                                }

                                                break;

                                        default :

                                                if ( is_array( $value ) && !$is_return ) {
                                                        $value = json_encode( $value );
                                                }
                                }

                                unset( $delimiter );
                        }
                }
                unset( $options, $field_type, $field_key );

                return $value;
        }

}
