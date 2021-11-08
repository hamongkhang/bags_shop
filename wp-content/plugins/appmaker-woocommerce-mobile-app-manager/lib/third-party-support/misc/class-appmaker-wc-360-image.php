<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_image360
{

    public function __construct()
    {
        add_filter('appmaker_wc_product_data', array($this, 'image360'), 2, 3);
    }

    public function image360($data, $product, $expanded){

        $data['replace_with_360_image'] = false;
        if(get_post_meta( $product->get_id(), 'wc360_enable', true )){
            $data['replace_with_360_image'] = true;
        }
        return $data;
    }
}
new APPMAKER_WC_image360();