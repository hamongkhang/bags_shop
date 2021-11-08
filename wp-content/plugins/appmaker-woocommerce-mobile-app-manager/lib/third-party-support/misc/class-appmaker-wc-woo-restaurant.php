<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_Woo_Restaurant {


	public function __construct() {
        add_filter( 'appmaker_wc_product_fields', array( $this, 'restaurant_product_fields' ), 2, 2 );
        add_action( 'appmaker_wc_before_add_to_cart',array($this,'add_checkbox_value'),1,1 );
        add_filter('appmaker_wc_cart_items',array($this,'product_addon_cart'),1,1);
        add_filter('appmaker_wc_order_review',array($this,'product_addon_order_review'),1,1);
        add_filter('woocommerce_rest_prepare_shop_order', array($this, 'product_addon_order_detail'), 1, 3);

    }

    public function product_addon_order_detail($response,$post,$request){
        // print_r($response->data['line_items']);exit;
 
         $order = wc_get_order(($response->data['id']));
         foreach ( $order->get_items() as $item) {
             $product = $item->get_data();
             if($product['variation_id'] != 0){
                 $product_id = $product['variation_id'];
             }else
                $product_id = $product['product_id'];
            foreach($item->get_formatted_meta_data() as $id => $meta_data){
                $product_name ="\n".$meta_data->display_key." : ".strip_tags($meta_data->display_value);
                foreach($response->data['line_items'] as $key => $item){
                    //echo $product_id;echo $item['product_id']."\n";
                    if($product_id == $item['product_id']) {
                        $response->data['line_items'][$key]['quantity'] .= html_entity_decode($product_name);
                    }
 
                }
            }
 
         }
 
 
         return $response;
     }

    public function product_addon_order_review($return){
        //  print_r($return['products']);exit;
         foreach($return['products'] as $key =>$product){
             if( isset( $product['xc_woo_restaurant'] ) && !empty( $product['xc_woo_restaurant'] ) ){
                 foreach($product['xc_woo_restaurant'] as $addon => $addon_value){
                     $addon_string = "\n".$addon_value['name'].'('.APPMAKER_WC_Helper::get_display_price($addon_value['price']).')';
                     $return['products'][$key]['quantity'] .= $addon_string;
                 }
             }	
         }
         return $return;
     }

     
    public function product_addon_cart($return){
        // print_r($return['products']);exit;
         foreach($return['products'] as $key =>$product){
             if( isset( $product['xc_woo_restaurant'] ) && !empty( $product['xc_woo_restaurant'] ) ){
 
                 foreach($product['xc_woo_restaurant'] as $addon => $addon_value){
                     $addon_string = $addon_value['name'].' : '.$addon_value['value']."\n";
                     $return['products'][$key]['variation_string'] .= $addon_string;
                 }
             }            
         }
         return $return;
     }
    
    public function add_checkbox_value($params){

		$product_addons = get_xc_woo_restaurant_addons( $_POST['product_id'] );
		
        if( !empty($product_addons) ) {
            if( !empty($product_addons) ){
                foreach ($product_addons as $addon => $value) {
                    $key = 'restaurant-addon-' . sanitize_title( $value['field-name'] );
                    if(is_array($_POST[$key]) && $value['type'] == 'checkbox') {
                        foreach ($_POST[$key] as $id => $checked) {
                            if ($checked == true) {
                                $_POST[$key][$id] = sanitize_title($value['options'][$id]['label']);
                                $_REQUEST[$key][$id] = $_POST[$key][$id];
                            }
                        }
                    }
                }
            }	
		}

	}


    public function restaurant_product_fields( $fields,$product ) {

        $prefix = false;
        if( $product ) {

            $product_id = $product->get_id();
            $product_addons = get_xc_woo_restaurant_addons( $product_id, $prefix );
            if( $product_addons ) {
                foreach ( $product_addons as $addon => $value ) {
                    // if( 'radiobutton' == $value['type'] ) { 
                    //     $product_addons[$addon]['type']='radiobutton';
                    // }
                    //  if( 'checkbox' ==  $value['type'] && !empty( $value['options'] ) ) {                        
                    //     $value['type'] = 'select';
                    //     $product_addons[$addon]['type']='select';
                    //  }
                        
                     
                    if( ! in_array( $value['type'],
                         array(
                             'select',
                             'radiobutton', 
                             'custom_textarea',
                             'checkbox',
                        ), true )
                    ) {
                         continue;
                    }
         
                     $required = ( $value['required'] === 1 || $value['required'] === '1' );
                     if ( $value['type'] === 'select' || $value['type'] === 'radiobutton'  ) {
                         if( $value['type'] == 'radiobutton' ){
                            $key = 'restaurant-addon-' . sanitize_title( $value['field-name'] ).'[]';  
                         }else {
                            $key = 'restaurant-addon-' . sanitize_title( $value['field-name'] );  
                         }                      
                                                                      
                         $field[ $key ]['required'] = $required;                        
                         $field[ $key ]['label'] = $value['name'];                         
                         $field[ $key ]['type'] = 'select';
                         $field[ $key ]['options'] = array();
                         $loop = 0;
                         foreach ( $value['options'] as $option ) {
                             $loop ++;
                             if ( $value['type'] === 'radiobutton' ) {
                                 $option_value = sanitize_title( $option['label'] );
                             } else {
                                 $option_value = sanitize_title( $option['label'] ) . '-' . $loop;
                             }
                             $field[ $key ]['options'][ $option_value ] = $option['label'];
                             if ( ! empty( $option['price'] ) && $option['price'] > 0 ) {
                                 $field[ $key ]['options'][ $option_value ] .= ' (' . APPMAKER_WC_Helper::get_display_price( $option['price'] ) . ') ';
                             }
                         }
                     } else {
                         if ( $value['type'] === 'checkbox' ) {
                             $id = 0;
                             $key = 'restaurant-addon-' . sanitize_title( $value['field-name'] ) . "[$id]";                             
                         } else if( $value['type'] !== 'custom_textarea' ) {
                             $key = 'restaurant-addon-' . sanitize_title( $value['field-name'] );
                         }
                         if( $key ){
                            $field[ $key ]['required'] = $required;
                         }
                         //$field[ $key ]['label']    = $value['name'];
                         if( isset( $value['options'] ) ) {
                             $loop = 0;
                             foreach ( $value['options'] as $id => $option ) {
                                 if ( $value['type'] !== 'checkbox' ) {						
                                     $option_key = empty( $option['label'] ) ? $id : sanitize_title( $option['label'] );							
                                 }else if ($value['type'] === 'checkbox' ) {
                                    $key = 'restaurant-addon-' . sanitize_title( $value['field-name'] ) . "[$loop]";
                                    $field[ $key ]['type'] = 'checkbox';
                                    $loop++;
                                 }
                                 if( $value['type'] == 'custom_textarea' ) {                                   
                                    $key = 'restaurant-addon-' . sanitize_title( $value['field-name'] ) . '['.$option['label'].']';
                                    $field[ $key ]['type'] = 'textarea';
                                    $field[ $key ]['minLength'] = !empty($option['min']) ? (int) $option['min'] : null;
                                    $field[ $key ]['maxLength'] = !empty($option['max']) ? (int) $option['max'] : null;                                    
                                 } 				
                                 $field[$key]['value'] = sanitize_title($option['label']);
                                 $field[ $key ]['label']    = ! empty( $option['label'] ) ? $option['label'] : $value['name'];
                                 if ( ! empty( $option['price'] ) && $option['price'] > 0 ) {
                                     $field[ $key ]['label'] .= ' (' . APPMAKER_WC_Helper::get_display_price( $option['price'] ) . ') ';
                                 }						
                             }
                         }
                         switch ( $value['type'] ) {
                            
                             case 'custom_textarea' :
                                 $field[ $key ]['type'] = 'textarea';
                                 break;                            
                             case 'checkbox' :
                                 $field[ $key ]['type'] = 'checkbox';
                                 break;
                         }				
                     }
                }
            }

        }
        if(!empty($fields) && !empty($field)) {
            $field = APPMAKER_WC_Dynamic_form::get_fields($field, 'product');
            $fields['items'] = array_merge($fields['items'], $field['items']);
            $fields['order'] = array_merge($fields['order'], $field['order']);
            $fields['dependencies'] = array_merge($fields['dependencies'], $field['dependencies']);
            return $fields;
        }else if(!empty($field)){
            $fields = APPMAKER_WC_Dynamic_form::get_fields($field, 'product');
            return $fields;
        }
        else{

            return $fields;
        }
    }
}
new APPMAKER_WC_Woo_Restaurant();