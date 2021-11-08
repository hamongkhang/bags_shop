<?php

namespace wpie\import\upload\onedrive;

use WP_Error;

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'vj-wp-import-export'));
}

if (file_exists(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-upload.php')) {
    require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-upload.php');
}

class WPIE_Onedrive_Upload extends \wpie\import\upload\WPIE_Upload {

    public function __construct() {
        
    }

    public function download_onedrive_file() {

        if (!is_dir(WPIE_UPLOAD_IMPORT_DIR) || !wp_is_writable(WPIE_UPLOAD_IMPORT_DIR)) {

            return new \WP_Error('wpie_import_error', __('Uploads folder is not writable', 'vj-wp-import-export'));
        }

        $file_url = isset($_POST["file_url"]) ? wpie_sanitize_field($_POST["file_url"]) : '';

        $fileName = isset($_POST["fileName"]) ? wpie_sanitize_field($_POST["fileName"]) : '';

        $newfiledir = parent::wpie_create_safe_dir_name($fileName);

        wp_mkdir_p(WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir);


        wp_mkdir_p(WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original");

        wp_mkdir_p(WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/parse");

        wp_mkdir_p(WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/parse/chunks");

        $filePath = WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original/" . $fileName;

        chmod(WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original/", 0755);

        $response = wp_safe_remote_get($file_url, array('timeout' => 3000, 'stream' => true, 'filename' => $filePath));

        unset($file_url);

        if (is_wp_error($response)) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            unset($fileName, $newfiledir, $filePath);

            return $response;
        } elseif (200 != wp_remote_retrieve_response_code($response)) {

            if (file_exists($filePath)) {
                unlink($filePath);
            }
            unset($fileName, $newfiledir, $filePath);

            return new \WP_Error('http_404', trim(wp_remote_retrieve_response_message($response)));
        }

        $content_md5 = wp_remote_retrieve_header($response, 'content-md5');

        unset($response);

        if ($content_md5) {

            $md5_check = verify_file_md5($filePath, $content_md5);

            if (is_wp_error($md5_check)) {

                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                unset($fileName, $newfiledir, $filePath, $content_md5);

                return $md5_check;
            }
        }
        unset($filePath, $content_md5);

        $wpie_import_id = isset($_POST['wpie_import_id']) ? intval(wpie_sanitize_field($_POST['wpie_import_id'])) : 0;

        return parent::wpie_manage_import_file($fileName, $newfiledir, $wpie_import_id);
    }

}
