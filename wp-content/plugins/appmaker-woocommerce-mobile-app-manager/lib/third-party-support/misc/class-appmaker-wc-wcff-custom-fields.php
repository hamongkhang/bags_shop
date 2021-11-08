<?php
/**
 * Created by IntelliJ IDEA.
 * User: muneef
 * Date: 11/08/17
 * Time: 4:41 PM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_wcff_custom_fields {


	public function __construct() {
		
		add_filter( 'appmaker_wc_product_fields', array( $this, 'product_fields' ), 2, 2 );	
	}	
	
	public function product_fields( $fields, $product ) {
		$all_fields = apply_filters( 'wcff/load/all_fields', $product->get_id(), 'wccpf' );
		if(!is_array($all_fields)){
			$all_fields = wcff()->dao->load_fields_for_product($product->get_id(), 'wccpf');
		}
			
		$fields         = array();
		$previous_value = null;
		if( $all_fields ){

			foreach ( $all_fields as $title => $field_props ) {
				if ( count( $field_props ) > 0 ) {
					foreach ( $field_props as $x  => $field_prop ) {
	
						if ( ! in_array( $field_prop['type'] ,
							array(
							'text',
							'number',
							'email',
							'textarea',
							'checkbox',
							'radio',
							'select',
							'datepicker',
							'label',
						),true)
						) {
							continue;
						}
	
						$key = $field_prop['name'];
						$fields[ $key ]['label'] = $field_prop['label'];
						if( isset($field_prop['required']) ){
							$fields[ $key ]['required'] = $field_prop['required'];
						}					
	
						if ( $previous_value && $field_prop['type'] != 'label' ) {
							$fields[ $key ]['label'] = $previous_value . ".\n \n" . $field_prop['label'];
							$previous_value = null;
						}
						$min_date = date('d-m-Y');	
						if( $field_prop['type'] == 'datepicker' ) {
                           if( isset( $field_prop['disable_next_x_day'] ) && $field_prop['disable_next_x_day'] >= 1 ) {
							  $min_date = date('d-m-Y', strtotime( $min_date.'+ ' . $field_prop['disable_next_x_day'] . ' days'));
						   }
						}					
						switch ( $field_prop['type'] ) {
							case 'datepicker':								
								$fields[ $key ]['type'] = 'datepicker';
								$fields[ $key ]['minDate'] = $min_date;
								$fields[ $key ]['default'] = $min_date;
								break;
							case 'radio':
								$fields[ $key ]['type'] = 'select';
								break;
							case 'label':
								$fields[ $key ]['type'] = 'hidden';
								$previous_value = $previous_value . "\n \n" . $field_prop['message'];
								break;
							default:
								$fields[ $key ]['type'] = $field_prop['type'];
								break;
						}
						 if ( $field_prop['type'] === 'select' || $field_prop['type'] === 'radio' ) {
						 	$options_array = explode( ';',$field_prop['choices'] );
	
							foreach ( $options_array as $option ) {
								$key_val = explode("|", $option);
								if( count($key_val) == 2 ){
									$fields[ $key ]['options'][ $key_val[0] ] = $key_val[1];
							    }else
						 		   $fields[ $key ]['options'][ $option ] = $option;
						 	}
						 }
						// if( ( $field_prop['type'] == 'select' || $field_prop['type'] == 'radio' ) && isset( $field_prop['choices'] ) ){
                        //     $options = array();
						// 	$choices = explode(";", ( ! empty( $field_prop["choices"] ) ? $field_prop["choices"] : "" ) );
						// 	$choices = apply_filters( "wcff_select_option_before_rendering", $choices, $field_prop["name"] );
						// 	if( is_array( $choices ) ) {
						// 		foreach ( $choices as $choice ) {
						// 			$key_val = explode("|", $choice);
						// 			if( isset($key_val[0]) && isset($key_val[1]) ){
						// 					$options[ $key_val[0] ] = $key_val[1];
						// 			}
						// 		}
						// 	}
						// }
					}
				}
			}
	
			$fields = APPMAKER_WC_Dynamic_form::get_fields( $fields, 'product' );
		}
		
		return $fields;
	}


}

new APPMAKER_WC_wcff_custom_fields();
