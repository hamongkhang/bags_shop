<?php

namespace wpie\import\schedule;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

class Template_Copy {

        public $options = [];

        public function __construct( $options = [] ) {

                $this->options = $options;

                return $this->reset_file_path();
        }

        private function reset_file_path() {

                $active_file = isset( $this->options[ 'activeFile' ] ) ? $this->options[ 'activeFile' ] : "";

                if ( ! empty( $active_file ) ) {

                        $importFile = isset( $this->options[ 'importFile' ] ) ? $this->options[ 'importFile' ] : "";

                        if ( ! empty( $importFile ) && isset( $importFile[ $active_file ] ) ) {

                                $new_file = $this->safe_dir( $active_file );

                                $is_dir_exist = wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $new_file );

                                if ( $is_dir_exist ) {

                                        $existing_files = $importFile[ $active_file ];

                                        $base_dir = isset( $importFile[ $active_file ][ 'baseDir' ] ) ? $importFile[ $active_file ][ 'baseDir' ] : "";

                                        if ( ! empty( $base_dir ) ) {

                                                $new_files = [];

                                                if ( ! empty( $existing_files ) ) {

                                                        foreach ( $existing_files as $_k => $_v ) {
                                                                $new_files[ $_k ] = str_replace( $base_dir, $new_file, $_v );
                                                        }
                                                }

                                                $this->options[ 'importFile' ][ $active_file ] = $new_files;

                                                $this->copy_dir( WPIE_UPLOAD_IMPORT_DIR . "/" . $base_dir, WPIE_UPLOAD_IMPORT_DIR . "/" . $new_file );
                                        }
                                }
                        }
                }
        }

        private function copy_dir( $source = "", $dest = "", $permissions = 0755 ) {

                if ( empty( $source ) || empty( $source ) ) {
                        return false;
                }

                // Check for symlinks
                if ( is_link( $source ) ) {
                        return symlink( readlink( $source ), $dest );
                }

                // Simple copy for a file
                if ( is_file( $source ) ) {
                        return copy( $source, $dest );
                }

                // Make destination directory
                if ( ! is_dir( $dest ) ) {
                        mkdir( $dest, $permissions );
                }

                // Loop through the folder
                $dir = dir( $source );

                while ( false !== $entry = $dir->read() ) {
                        // Skip pointers
                        if ( $entry == '.' || $entry == '..' ) {
                                continue;
                        }

                        // Deep copy directories
                        $this->copy_dir( "$source/$entry", "$dest/$entry", $permissions );
                }

                // Clean up
                $dir->close();

                return true;
        }

        private function safe_dir( $str = "", $separator = 'dash' ) {

                if ( empty( $str ) ) {
                        $str = time();
                }

                if ( $separator == 'dash' ) {
                        $search = '_';
                        $replace = '-';
                } else {
                        $search = '-';
                        $replace = '_';
                }

                $trans = [
                        '&\#\d+?;'       => '',
                        '&\S+?;'         => '',
                        '\s+'            => $replace,
                        '[^a-z0-9\-\._]' => '',
                        $search . '+'    => $replace,
                        $search . '$'    => $replace,
                        '^' . $search    => $replace,
                        '\.+$'           => ''
                ];

                $str = strip_tags( $str );

                foreach ( $trans as $key => $val ) {
                        $str = preg_replace( "#" . $key . "#i", $val, $str );
                }

                unset( $search, $replace, $trans );

                return md5( trim( wp_unslash( strtolower( $str ) ) ) . time() );
        }

}
