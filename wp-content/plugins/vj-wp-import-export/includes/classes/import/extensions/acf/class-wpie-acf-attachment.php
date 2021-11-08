<?php

namespace wpie\import\acf\attachment;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php');
}

if ( file_exists( ABSPATH . 'wp-admin/includes/image.php' ) ) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
}

class WPIE_ACF_Attachment extends \wpie\import\base\WPIE_Import_Base {

        private $target_dir;

        public function __construct( $item_id = 0, $is_new_item = true, $wpie_import_option = array(), $wpie_import_record = array() ) {


                $this->item_id = $item_id;

                $this->is_new_item = $is_new_item;

                $this->wpie_import_option = $wpie_import_option;

                $this->wpie_import_record = $wpie_import_record;

                $wp_uploads = wp_upload_dir();

                $this->target_dir = isset( $wp_uploads[ 'path' ] ) ? $wp_uploads[ 'path' ] : "";

                unset( $wp_uploads );
        }

        public function wpie_get_file_from_url( $file_url = "", $type = "image", $is_search_through_media = false, $upload_dir_data = false ) {

                if ( empty( $file_url ) ) {
                        return false;
                }

                if ( ! is_array( $file_url ) ) {
                        $file_url = array( $file_url );
                }

                $data = array();

                foreach ( $file_url as $file ) {

                        $filename = basename( $file );

                        $attach_id = false;

                        if ( $is_search_through_media ) {

                                $temp_id = $this->wpie_get_file_from_gallery( $filename );

                                if ( $temp_id !== false ) {
                                        $attach_id = absint( $temp_id );
                                }
                                unset( $attch_image );
                        }
                        if ( $attach_id === false && $upload_dir_data ) {

                                $attch_data = $this->wpie_get_file_from_local( WPIE_UPLOAD_TEMP_DIR . "/" . $filename );

                                if ( ! ($attch_data === false || is_wp_error( $attch_data )) ) {
                                        $attach_id = $attch_data;
                                }
                                unset( $attch_data );
                        }

                        if ( $attach_id === false || empty( $attach_id ) ) {

                                $attch_data = false;

                                if ( file_exists( ABSPATH . 'wp-admin/includes/media.php' ) ) {
                                        require_once(ABSPATH . 'wp-admin/includes/media.php');
                                }
                                if ( file_exists( ABSPATH . 'wp-admin/includes/file.php' ) ) {
                                        require_once(ABSPATH . 'wp-admin/includes/file.php');
                                }
                                if ( $type === "file" ) {
                                        $attch_data = $this->download_atatchment( $file );
                                } else {

                                        $attch_data = media_sideload_image( $file, $this->item_id, '', 'id' );
                                }

                                if ( ! ($attch_data === false || empty( $attch_data ) || is_wp_error( $attch_data )) ) {
                                        $attach_id = $attch_data;
                                }
                                unset( $attch_data );
                        }
                        if ( $attach_id !== false ) {
                                $data[] = absint( $attach_id );
                        }

                        unset( $attach_id, $filename );
                }

                return $data;
        }

        private function wpie_get_file_from_local( $file = "" ) {

                if ( ( ! wp_is_writable( $this->target_dir )) || empty( $file ) || ( ! file_exists( $file )) ) {
                        return false;
                }

                return $this->download_atatchment( $file );
        }

        private function download_file_from_url( $file = "" ) {

                if ( empty( $file ) ) {
                        return false;
                }

                if ( file_exists( ABSPATH . 'wp-admin/includes/media.php' ) ) {
                        require_once(ABSPATH . 'wp-admin/includes/media.php');
                }
                if ( file_exists( ABSPATH . 'wp-admin/includes/file.php' ) ) {
                        require_once(ABSPATH . 'wp-admin/includes/file.php');
                }

                $attch = $this->download_file( $file );

                if ( is_wp_error( $attch ) ) {
                        return false;
                }
                $author_id = absint( $this->get_post_user() );

                if ( $author_id > 0 ) {
                        global $wpdb;
                        $wpdb->update( $wpdb->posts, [ "post_author" => $author_id ], [ 'ID' => $attch ] );
                }
                return $attch;
        }

        private function download_file( $file_url = "" ) {

                if ( empty( $file_url ) ) {
                        return new \WP_Error( 'http_404', __( 'Empty Image URL', 'vj-wp-import-export' ) );
                }

                $fileName = time() . rand() . ".tmp";

                $filePath = WPIE_UPLOAD_TEMP_DIR . "/" . $fileName;

                $response = wp_safe_remote_get( $file_url, array( 'timeout' => 3000, 'stream' => true, 'filename' => $filePath ) );

                if ( is_wp_error( $response ) ) {

                        if ( file_exists( $filePath ) ) {
                                unlink( $filePath );
                        }

                        return $response;
                }

                if ( 200 != wp_remote_retrieve_response_code( $response ) ) {

                        if ( file_exists( $filePath ) ) {
                                unlink( $filePath );
                        }

                        return new \WP_Error( 'http_404', trim( wp_remote_retrieve_response_message( $response ) ) );
                }

                $content_md5 = wp_remote_retrieve_header( $response, 'content-md5' );

                if ( $content_md5 ) {

                        $md5_check = verify_file_md5( $filePath, $content_md5 );

                        if ( is_wp_error( $md5_check ) ) {

                                if ( file_exists( $filePath ) ) {
                                        unlink( $filePath );
                                }

                                return $md5_check;
                        }

                        unset( $md5_check );
                }

                $original_name = $this->get_filename_from_headers( $response, $file_url );

                if ( is_wp_error( $original_name ) ) {

                        if ( file_exists( $filePath ) ) {
                                unlink( $filePath );
                        }

                        return $original_name;
                }

                $file_details = wp_check_filetype();

                if ( isset( $file_details[ 'type' ] ) && $file_details[ 'type' ] !== false ) {
                        if ( file_exists( $filePath ) ) {
                                unlink( $filePath );
                        }
                        return new \WP_Error( 'invalid_image', __( 'Invalid File Extension', 'vj-wp-import-export' ) );
                }
               

                $file_array = [ "name" => $original_name, "tmp_name" => $filePath ];

                $id = media_handle_sideload( $file_array, $this->item_id );

                // If error storing permanently, unlink.
                if ( is_wp_error( $id ) ) {
                        @unlink( $file_array[ 'tmp_name' ] );
                        return $id;
                }

                // Store the original attachment source in meta.
                add_post_meta( $id, '_source_url', $file_url );

                return $id;
        }

        private function get_filename_from_headers( $response = "", $file_url = "" ) {

                $header_content_disposition = wp_remote_retrieve_header( $response, 'content-disposition' );

                $default_filename = basename( parse_url( $file_url, PHP_URL_PATH ) );

                if ( empty( $header_content_disposition ) ) {
                        return $default_filename;
                }

                $regex = '/.*?filename=(?<fn>[^\s]+|\x22[^\x22]+\x22)\x3B?.*$/m';

                $new_file_data = null;

                $original_name = "";

                if ( preg_match( $regex, $header_content_disposition, $new_file_data ) ) {

                        if ( isset( $new_file_data[ 'fn' ] ) && ! empty( $new_file_data[ 'fn' ] ) ) {
                                $wp_filetype = wp_check_filetype( $new_file_data[ 'fn' ] );
                                if ( isset( $wp_filetype[ 'ext' ] ) && ( ! empty( $wp_filetype[ 'ext' ] )) && isset( $wp_filetype[ 'type' ] ) && ( ! empty( $wp_filetype[ 'type' ] )) ) {
                                        $original_name = $new_file_data[ 'fn' ];
                                }
                        }
                }

                if ( empty( $original_name ) ) {

                        $regex = '/.*filename=([\'\"]?)([^\"]+)\1/';

                        if ( preg_match( $regex, $header_content_disposition, $new_file_data ) ) {

                                if ( isset( $new_file_data[ '2' ] ) && ! empty( $new_file_data[ '2' ] ) ) {
                                        $wp_filetype = wp_check_filetype( $new_file_data[ '2' ] );
                                        if ( isset( $wp_filetype[ 'ext' ] ) && ( ! empty( $wp_filetype[ 'ext' ] )) && isset( $wp_filetype[ 'type' ] ) && ( ! empty( $wp_filetype[ 'type' ] )) ) {
                                                $original_name = $new_file_data[ '2' ];
                                        }
                                }
                        }
                }
                if ( empty( $original_name ) ) {
                        $original_name = $default_filename;
                }

                return preg_replace( "/[^a-z0-9\_\-\.]/i", '', preg_replace( '#[ -]+#', '-', $original_name ) );
        }

        private function download_atatchment( $file = "" ) {

                if ( empty( $file ) ) {
                        return false;
                }
                $filename = basename( $file );

                $upload_file = wp_upload_bits( $filename, null, file_get_contents( $file ) );

                if ( ! $upload_file[ 'error' ] ) {

                        $wp_filetype = wp_check_filetype( $filename, null );

                        $attachment = array(
                                'post_mime_type' => $wp_filetype[ 'type' ],
                                'post_parent'    => $this->item_id,
                                'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
                                'post_content'   => '',
                                'post_status'    => 'inherit',
                                'post_author'    => $this->get_post_user()
                        );

                        $attachment_id = wp_insert_attachment( $attachment, $upload_file[ 'file' ], $this->item_id );

                        if ( ! is_wp_error( $attachment_id ) ) {

                                $attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file[ 'file' ] );

                                wp_update_attachment_metadata( $attachment_id, $attachment_data );

                                unset( $attachment_data );
                        }

                        unset( $attachment, $wp_filetype, $upload_file, $filename );

                        return $attachment_id;
                }

                unset( $upload_file, $filename );

                return false;
        }

        private function get_post_user() {

                $user_id = 0;
                if ( ! empty( $this->item_id ) ) {
                        $post = get_post( $this->item_id );
                        if ( $post && isset( $post->post_author ) && ! empty( $post->post_author ) ) {
                                $user_id = $post->post_author;
                        }
                        unset( $post );
                }
                if ( $user_id === 0 ) {
                        if ( ! empty( $this->import_username ) ) {

                                $user = get_user_by( "login", $this->import_username );

                                if ( $user && isset( $user->ID ) ) {
                                        $user_id = $user->ID;
                                }
                                unset( $user );
                        }
                }
                if ( $user_id === 0 ) {

                        $current_user = wp_get_current_user();

                        if ( $current_user && isset( $current_user->ID ) ) {
                                $user_id = $current_user->ID;
                        }
                        unset( $current_user );
                }
                return $user_id;
        }

        private function wpie_get_file_from_gallery( $file = "" ) {

                if ( empty( $file ) ) {
                        return false;
                }
                global $wpdb;


                $attachment = $wpdb->get_var( $wpdb->prepare( "SELECT post.ID FROM {$wpdb->posts} post INNER JOIN {$wpdb->postmeta} meta ON post.ID = meta.post_id WHERE post.post_type = 'attachment' AND meta.meta_key = %s AND (meta.meta_value = %s OR meta.meta_value LIKE %s) LIMIT 0,1;", '_wp_attached_file', basename( $file ), "%/" . basename( $file ) ) );

                if ( $attachment && absint( $attachment ) > 0 ) {
                        return $attachment;
                }

                $attachment = $wpdb->get_var( $wpdb->prepare( "SELECT post.ID FROM {$wpdb->posts} post INNER JOIN {$wpdb->postmeta} meta ON post.ID = meta.post_id WHERE post.post_type = 'attachment' AND meta.meta_key = %s AND (meta.meta_value = %s OR meta.meta_value LIKE %s) LIMIT 0,1;", '_wp_attached_file', sanitize_file_name( basename( $file ) ), "%/" . sanitize_file_name( basename( $file ) ) ) );

                if ( $attachment && absint( $attachment ) > 0 ) {
                        return $attachment;
                }

                $wp_filetype = wp_check_filetype( basename( $file ) );

                if ( isset( $wp_filetype[ 'type' ] ) && ! empty( $wp_filetype[ 'type' ] ) ) {
                        $name = pathinfo( $file, PATHINFO_FILENAME );
                        $attch = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . $wpdb->posts . " WHERE (post_title = %s OR post_title = %s OR post_name = %s) AND post_type = 'attachment' AND post_mime_type = %s;", $name, $name, $name, $wp_filetype[ 'type' ] ) );
                        if ( $attch && absint( $attch ) > 0 ) {
                                return $attch;
                        }
                }
                return false;
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
