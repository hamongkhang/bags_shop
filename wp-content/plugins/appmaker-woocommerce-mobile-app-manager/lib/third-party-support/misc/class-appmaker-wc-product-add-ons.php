<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_Product_Addons {


	public function __construct() {
        add_filter( 'appmaker_wc_product_fields', array( $this, 'product_fields' ), 2, 2 );
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
                       $response->data['line_items'][$key]['quantity'] .= $product_name;
                   }

               }
           }

        }


        return $response;
    }

    public function product_addon_order_review($return){
       //  print_r($return['products']);exit;
        foreach($return['products'] as $key =>$product){
			if( isset( $product['addons'] ) && !empty( $product['addons'] ) ){
				foreach($product['addons'] as $addon => $addon_value){
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
			if( isset( $product['addons'] ) && !empty( $product['addons'] ) ){

				foreach($product['addons'] as $addon => $addon_value){
					$addon_string = $addon_value['name'].' : '.$addon_value['value']."\n";
					$return['products'][$key]['variation_string'] .= $addon_string;
				}
			}            
        }
        return $return;
    }

	public function add_checkbox_value($params){

		$product_addons = WC_Product_Addons_Helper::get_product_addons( $_POST['product_id'] );
		
        if( !empty($product_addons) ){
            foreach ($product_addons as $addon => $value) {
                $key = 'addon-' . sanitize_title( $value['field_name'] );
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


	/**
	 * @param array $fields
	 * @param WC_Product $product
	 *
	 * @return array|mixed|void
	 */
	public function product_fields($fields,$product ) {
		$product_addons = WC_Product_Addons_Helper::get_product_addons( $product->get_id() );
		$field         = array();
		if( !empty($product_addons) ){

			foreach ( $product_addons as $addon => $value ) {
				if($value['type']=='multiple_choice'){
					 if($value['display'] == 'radiobutton'){
						 $value['type']= 'radiobutton';
						 $product_addons[$addon]['type']='radiobutton';
					 }else{
						 $value['type']= 'select';
						 $product_addons[$addon]['type']='select';
					 }
					
				 }
				 if( ! in_array( $value['type'],
					 array(
						 'select',
						 'radiobutton',
						 'custom',
						 'custom_letters_or_digits',
						 'custom_letters_only',
						 'custom_textarea',
						 'custom_text',
						 'custom_digits_only',
						 'custom_email',
						 'custom_price',
						 'input_multiplier',
						 'checkbox',
	 
	 
				 ), true )
				 ) {
					 continue;
				 }
	 
				 $required = ( $value['required'] === 1 || $value['required'] === '1' );
				 if ( $value['type'] === 'select' || $value['type'] === 'radiobutton' ) {
					 $key = 'addon-' . sanitize_title( $value['field_name'] );
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
						 $num = 0;
						 $key = 'addon-' . sanitize_title( $value['field_name'] ) . "[$num]";
					 }else{
						 $key = 'addon-' . sanitize_title( $value['field_name'] );
					 }
					 $field[ $key ]['required'] = $required;
					 $field[ $key ]['label']    = $value['name'];
					 
					 if(isset($value['options'])){
	 
						 foreach ( $value['options'] as $id => $option ) {
							if ( $value['type'] === 'checkbox' ) {								
								$key = 'addon-' . sanitize_title( $value['field_name'] ) . "[$id]";
								$field[ $key ]['type'] = 'checkbox';
								$field[ $key ]['required'] = $required;								
							}elseif ( $value['type'] !== 'checkbox' ) {						
								 $option_key = empty( $option['label'] ) ? $id : sanitize_title( $option['label'] );							
							 }						
							 $field[$key]['value'] = sanitize_title($option['label']);
							 $field[ $key ]['label']    = ! empty( $option['label'] ) ? $option['label'] : $value['name'];
							 if ( ! empty( $option['price'] ) && $option['price'] > 0 ) {
								 $field[ $key ]['label'] .= ' (' . APPMAKER_WC_Helper::get_display_price( $option['price'] ) . ') ';
							 }
							 						
						 }
					 }
					 switch ( $value['type'] ) {
						 case 'custom' :
						 case 'custom_letters_or_digits' :
						 case 'custom_text'	:
						 case 'custom_letters_only' :
							 $field[ $key ]['type'] = 'text';
							 break;
						 case 'custom_textarea' :
							 $field[ $key ]['type'] = 'textarea';
							 break;
						 case 'custom_digits_only' :
							 $field[ $key ]['type'] = 'number';
							 break;
						 case 'custom_email' :
							 $field[ $key ]['type'] = 'email';
							 break;
						 case 'custom_price' :
						 case 'input_multiplier' :
							 $field[ $key ]['type'] = 'number';
							 break;
						 case 'checkbox' :
							 $field[ $key ]['type'] = 'checkbox';
							 break;
					 }				
				 }
				 if( $value['description_enable'] && ! empty( $value['description'] ) ) {
					$field[ $key ]['label']    .= ' :'."\n".$value['description'];
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

new APPMAKER_WC_Product_Addons();
