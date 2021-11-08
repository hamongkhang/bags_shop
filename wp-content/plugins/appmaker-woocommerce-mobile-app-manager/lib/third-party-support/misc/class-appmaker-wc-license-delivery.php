<?php
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly
class APPMAKER_WC_License_Delivery
{

    public function __construct()
    {
        add_filter('woocommerce_rest_prepare_shop_order', array($this, 'product_order_license_delivery_details'), 1, 3);
    }

    public function product_order_license_delivery_details($response, $post, $request)
	{
        $order     = wc_get_order(($response->data['id']));

    if('completed' == $order->get_status() ){
		foreach ($order->get_items() as $item) {
            $license_code_ids = is_array( $item['license_code_ids']) ?  $item['license_code_ids']:unserialize( $item['license_code_ids'] );
            $rows             = WC_LD_Model::get_codes_by_id( implode( ",", (array)$license_code_ids ) );
           // print_r($rows);
            $product_id = (isset($item['variation_id']) && !empty($item['variation_id'])) ? $item['variation_id'] : $item['product_id'];
            $code_1_title = WC_LD_Model::get_code_title( 1, $product_id );
            $code_2_title = WC_LD_Model::get_code_title( 2, $product_id );
            $code_3_title = WC_LD_Model::get_code_title( 3, $product_id );
            $code_4_title = WC_LD_Model::get_code_title( 4, $product_id );
            $code_5_title = WC_LD_Model::get_code_title( 5, $product_id );
            $license_delivery = array();
            foreach ( $rows as $row ) {
              //  $license_delivery .= '';
             
                if ( ! empty( $row['license_code1'] ) ) {
                    $license_delivery[] = array('title' =>  $code_1_title , 'value' => $row['license_code1']  );
                    //$license_delivery .= esc_html( $code_1_title ) . ' : ' . esc_html( $row['license_code1'] ) . "\n";
                }
    
                if ( ! empty( $row['license_code2'] ) ) {
                    $license_delivery[] = array('title' =>  $code_2_title , 'value' => $row['license_code2']  );
                    //$license_delivery .= esc_html( $code_2_title ) . ' : ' . esc_html( $row['license_code2'] ) . "\n";
                }
    
                if ( ! empty( $row['license_code3'] ) ) {
                    $license_delivery[] = array('title' =>  $code_3_title , 'value' => $row['license_code3']  );
                   // $license_delivery .= esc_html( $code_3_title ) . ' : ' . esc_html( $row['license_code3'] ) . "\n";
                }
    
                if ( ! empty( $row['license_code4'] ) ) {
                    $license_delivery[] = array('title' =>  $code_4_title , 'value' => $row['license_code4']  );
                  //  $license_delivery .= esc_html( $code_4_title ) . ' : ' . esc_html( $row['license_code4'] ) . "\n";
                }
                if ( ! empty( $row['license_code5'] ) ) {
                    $license_delivery[] = array('title' =>  $code_5_title , 'value' => $row['license_code5']  );
                    //$license_delivery .= esc_html( $code_5_title ) . ' : <a href="' . esc_url( wp_get_attachment_url($row['license_code5']) ) . '" target="_blank">'.wp_get_attachment_image($row['license_code5']).'</a>';
                }
    
            }
    
			foreach ($response->data['line_items'] as $key => $item) {

				if($product_id == $item['product_id']) {
                    //$response->data['line_items'][$key]['quantity'] .= "\n". $license_delivery;
                    $response->data['line_items'][$key]['purchase_code'] = $license_delivery;
				}
			}
        }
    }
        return $response;
        
    }

}

new APPMAKER_WC_License_Delivery();
