<?php


namespace wpie\import\upload\ftp;

use WP_Error;
use WP_Filesystem_FTPext;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-upload.php' ) ) {
        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-upload.php');
}

class WPIE_FTP_SFTP extends \wpie\import\upload\WPIE_Upload {

        private $options = [];
        private $path = "";

        public function __construct() {
                
        }

        public function download( $options = [], $import_id = 0 ) {
                if ( !is_dir( WPIE_UPLOAD_IMPORT_DIR ) || !wp_is_writable( WPIE_UPLOAD_IMPORT_DIR ) ) {

                        return new \WP_Error( 'wpie_import_error', __( 'Uploads folder is not writable', 'vj-wp-import-export' ) );
                }

                if ( (!is_array( $options ) ) || empty( $options ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'Invalid FTP details', 'vj-wp-import-export' ) );
                }

                $this->path = isset( $options[ "path" ] ) ? wpie_sanitize_field( $options[ "path" ] ) : '';

                $fileName = basename( $this->path );

                if ( !preg_match( '%\W(xml|zip|csv|xls|xlsx|xml|ods|txt|json|gz|tar)$%i', trim( $fileName ) ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'Uploaded file must be XML, CSV, ZIP, XLS, XLSX, ODS, TXT, JSON, GZ, TAR', 'vj-wp-import-export' ) );
                }

                $hostName = isset( $options[ "host" ] ) ? wpie_sanitize_field( $options[ "host" ] ) : '';

                $hostPort = isset( $options[ "port" ] ) ? absint( wpie_sanitize_field( $options[ "port" ] ) ) : 0;

                $hostUser = isset( $options[ "username" ] ) ? wpie_sanitize_field( $options[ "username" ] ) : '';

                $hostPass = isset( $options[ "password" ] ) ? wpie_sanitize_field( $options[ "password" ] ) : '';

                $connection_type = isset( $options[ "connection_type" ] ) && !empty( $options[ "connection_type" ] ) ? strtolower( trim( wpie_sanitize_field( $options[ "connection_type" ] ) ) ) : 'ftp';

                $this->options = [
                        'port' => $hostPort,
                        'hostname' => $hostName,
                        'username' => $hostUser,
                        'password' => $hostPass,
                ];

                if ( file_exists( ABSPATH . 'wp-admin/includes/file.php' ) ) {
                        require_once( ABSPATH . 'wp-admin/includes/file.php');
                }

                if ( !class_exists( 'WP_Filesystem_Base' ) ) {
                        require_once( ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php' );
                }

                if ( $connection_type === "sftp" ) {
                        $file = $this->sftpDownload();
                } else {
                        if ( $connection_type === "ftps" ) {
                                $this->options[ 'connection_type' ] = "ftps";
                        }

                        $file = $this->ftpDownload();
                }

                if ( is_wp_error( $file ) ) {
                        return $file;
                }

                $fileDir = $this->createImportDir( $file, $fileName );

                unlink( $file );

                if ( is_wp_error( $fileDir ) ) {
                        return $fileDir;
                }

                return parent::wpie_manage_import_file( $fileName, $fileDir, $import_id );
        }

        private function sftpDownload() {
                if ( !class_exists( 'WP_Filesystem_SSH2' ) ) {
                        require_once( ABSPATH . 'wp-admin/includes/class-wp-filesystem-ssh2.php' );
                }

                if ( absint( $this->options[ 'port' ] ) < 1 ) {
                        $this->options[ 'port' ] = 22;
                }

                $connection = new \WP_Filesystem_SSH2( $this->options );

                if ( $connection->errors->has_errors() ) {
                        return $connection->errors;
                }

                $connected = $connection->connect();

                if ( $connection->errors->has_errors() ) {
                        return $connection->errors;
                }

                if ( !$connected ) {
                        unset( $connected, $connection );

                        return new \WP_Error( 'wpie_import_error', __( 'SFTP Connection Error', 'vj-wp-import-export' ) );
                }

                unset( $connected );

                if ( !$connection->is_file( $this->path ) ) {
                        unset( $connection );

                        return new \WP_Error( 'wpie_import_error', __( 'File Not Found', 'vj-wp-import-export' ) );
                }

                $contents = $connection->get_contents( $this->path );

                unset( $connection );

                if ( empty( $contents ) ) {
                        unset( $contents );

                        return new \WP_Error( 'wpie_import_error', __( 'File is Empty', 'vj-wp-import-export' ) );
                }

                $tempFile = time() . rand() . ".tmp";

                $tempFilePath = get_temp_dir() . $tempFile;

                if ( false === file_put_contents( $tempFilePath, $contents ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'Uploads folder is not writable', 'vj-wp-import-export' ) );
                }

                return $tempFilePath;
        }

        private function ftpDownload() {
                if ( !class_exists( 'WP_Filesystem_FTPext' ) ) {
                        require_once( ABSPATH . 'wp-admin/includes/class-wp-filesystem-ftpext.php' );
                }

                if ( !defined( 'FS_CONNECT_TIMEOUT' ) ) {
                        define( 'FS_CONNECT_TIMEOUT', 300 );
                }

                if ( absint( $this->options[ 'port' ] ) < 1 ) {
                        $this->options[ 'port' ] = 21;
                }

                $connection = new \WP_Filesystem_FTPext( $this->options );

                if ( $connection->errors->has_errors() ) {
                        return $connection->errors;
                }

                $connected = $connection->connect();

                if ( $connection->errors->has_errors() ) {
                        return $connection->errors;
                }

                if ( !$connected ) {
                        unset( $connected, $connection );

                        return new \WP_Error( 'wpie_import_error', __( 'FTP Connection Error', 'vj-wp-import-export' ) );
                }

                unset( $connected );

                if ( !$connection->is_file( $this->path ) ) {
                        unset( $connection );

                        return new \WP_Error( 'wpie_import_error', __( 'File Not Found', 'vj-wp-import-export' ) );
                }

                $contents = $connection->get_contents( $this->path );

                unset( $connection );

                if ( empty( $contents ) ) {
                        unset( $contents );

                        return new \WP_Error( 'wpie_import_error', __( 'File is Empty', 'vj-wp-import-export' ) );
                }

                $tempFile = time() . rand() . ".tmp";

                $tempFilePath = get_temp_dir() . $tempFile;

                if ( false === file_put_contents( $tempFilePath, $contents ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'Uploads folder is not writable', 'vj-wp-import-export' ) );
                }

                return $tempFilePath;
        }

        private function createImportDir( $file = "", $fileName = "" ) {
                $newfiledir = parent::wpie_create_safe_dir_name( $fileName );

                wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir );

                wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original" );

                wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/parse" );

                wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/parse/chunks" );

                $filePath = WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original/" . $fileName;

                chmod( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original/", 0755 );

                if ( !copy( $file, $filePath ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'Uploads folder is not writable', 'vj-wp-import-export' ) );
                }

                return $newfiledir;
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
