<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
class APPMAKER_WP_SUPER_CACHE
{
    public function __construct()
    {
       add_filter('appmaker_wc_product_image_url', array($this, 'appmaker_product_thumbnail'),10, 2 );
       add_filter('appmaker_wc_in_app_page_media', array($this, 'appmaker_in_app_page_media'), 10, 1 );
       add_filter('appmaker_wc_product_images', array($this, 'appmaker_product_images'), 10, 1);
       add_filter('appmaker_wc_rest_prepare_product', array($this, 'appmaker_wc_varitaion_images'), 10, 3 );
    }

    public function appmaker_wc_varitaion_images( $data, $post, $request ){
        if( isset($data['images'])) {
            foreach( $data['images'] as $id => $image ) {
                $data['images'][$id] = $this->appmaker_cdn_url($image );
            }
        }
        return $data;

    }
    public function appmaker_product_images( $images ) {
      
        if( !empty( $images) ) {
            foreach($images as $id => $image ) {
               $images[$id] =  $this->appmaker_cdn_url($image );
            }
        }
      return $images;
    }

    public function appmaker_in_app_page_media ( $image ) {
        if(!empty($image)) {
            $image = $this->appmaker_cdn_url($image );
        }
        return $image;
    }

    public function appmaker_product_thumbnail ( $image, $size ) {
        if(!empty($image) && isset($image['url'])) {
            $image['url'] = $this->appmaker_cdn_url($image['url']);
        }
        return $image;
    }

    public function appmaker_cdn_url( $content ) {        
        if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'photon' ) ) {
            return $content;
        }
        scossdl_off_get_options();

        $content = $this->appmaker_scossdl_off_rewriter( $content );
       return $content;
    }

    function appmaker_scossdl_off_rewriter( $match ) {
        global $ossdl_off_blog_url, $ossdl_off_excludes, $ossdl_off_cdn_url;	   

        if ( scossdl_off_exclude_match( $match, $ossdl_off_excludes ) ) {
            return $match;
        }
        $include_dirs = scossdl_off_additional_directories();
        if ( preg_match( '`(' . $include_dirs . ')`', $match ) ) {
            //$offset = scossdl_string_mod( $match[1], $count_cnames );
            return str_replace( $ossdl_off_blog_url, $ossdl_off_cdn_url, $match );
        }

        return $match;
    }

   
}
new APPMAKER_WP_SUPER_CACHE();