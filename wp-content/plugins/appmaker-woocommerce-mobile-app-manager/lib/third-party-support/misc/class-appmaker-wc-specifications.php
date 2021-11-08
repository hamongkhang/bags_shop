<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
/*
class APPMAKER_WC_Product_specifications {

    public function __construct() {
        add_filter( 'appmaker_wc_product_widgets', array( $this, 'product_specification' ), 2, 3 );
    }


    public function product_specification( $return, $product ,$data)
    {

        $post_id= $data['id'];

        $table = dw_get_table_result( $post_id );
        if( !$table ) return;

        foreach( $table as $table_group ) {
            $result['name']=$table_group['group_name'];
            if ($table_group['attributes']) {
                $attributes = $table_group['attributes'];
                foreach ($attributes as $attr){
                 $result['attributes']=array(
                     'name'=> $attr['attr_name'],
                     'value' =>$attr['value']
                 );
                }
            }
        }

        return $return;


    }


  }
  new APPMAKER_WC_Product_specifications();
*/