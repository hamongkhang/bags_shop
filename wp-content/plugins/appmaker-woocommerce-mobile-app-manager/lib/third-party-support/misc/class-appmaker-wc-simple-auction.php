<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_simple_auction {

	public function __construct() {

		add_filter( 'appmaker_wc_product_tabs', array( $this, 'auction_tabs' ), 2, 1 );
        add_filter( 'appmaker_wc_product_widgets', array( $this, 'auction_widgets' ), 2, 2 );
        add_filter('appmaker_wc_input_stepper', array( $this,'submit_bid'),2,1);
        add_filter('appmaker_wc_product_data', array( $this,'buy_now_text'),2,2);
		
	}

	public function auction_tabs( $tabs ) {

          global $product;
          if(!empty($product) && (method_exists( $product, 'get_type') && $product->get_type() == 'auction')){                      
   
                $tabs['auction_details'] = array(
                    'title'    => __( 'Auction details', 'appmaker-woocommerce-mobile-app-manager' ),
                    'priority' => 2,
                    'callback' => 'woocommerce_product_description_tab',
                );
                if($product->is_closed() === FALSE){   
                  
                $tabs['auction_timer'] = array(
                    'title'    =>__( 'Time left:', 'wc_simple_auctions' ),
                    'priority' => 3,
                    'callback' => 'woocommerce_product_description_tab',
                );
                    if($product->is_started() === TRUE ){
                        $tabs['bid_value'] = array(
                            'title'    =>__( 'Start Bidding', 'appmaker-woocommerce-mobile-app-manager' ),
                            'priority' => 4,
                            'callback' => 'woocommerce_product_description_tab',
                        );
                    }
                }            
    
            }
        
    	return $tabs; 
	}


    public function buy_now_text($data,$product){
    
        if( ( method_exists( $product, 'get_type') && $product->get_type() == 'auction' ) ) {

            if(!empty($data)){
            
                $data['buy_now_button_text'] =  __( 'Buy now for ', 'appmaker-woocommerce-mobile-app-manager' ).APPMAKER_WC_Helper::get_display_price($product->get_regular_price());

                if ( $product->is_closed() === TRUE ){
                    $data['buy_now_button_text'] = __('Pay Now', 'wc_simple_auctions' );
                    $data['buy_now_action']      = array(
                                                    'type'   => 'OPEN_CHECKOUT',
                                                    'params' => array(),
                                                );
                }
                $price = $product->get_curent_bid();
                if( $price && 0 == $data['price'] ) {
                    $data['price'] = $price;
                    $data['price_display'] = APPMAKER_WC_Helper::get_display_price( $price );
                    
                }

            }
        }else if( $data['display_add_to_cart'] == false && !$product->is_type( 'external' ) ){
            $data['display_add_to_cart'] = true;
        }            

       return $data;
    }

	public function auction_widgets( $return, $product_local ) {
     
        global $product_obj,$product;
        $product_obj = $product_local;
        $product     = $product_local;
		$tabs    = apply_filters( 'woocommerce_product_tabs', array() );
		$tabs    = apply_filters( 'appmaker_wc_product_tabs', $tabs );
        $user_id = get_current_user_id();
        $_POST['post_id'] = $product_obj->get_id();

        /*if((method_exists( $product, 'get_type') && $product->get_type() == 'auction')){           
   
            $tabs['auction_details']  = '';
            if($product->is_closed() === FALSE){

                $tabs['auction_timer']    = '';  
                if($product->is_started() === TRUE ){
                      $tabs['bid_value']        = '';
                }
            }            

        }  */  
       if( ( method_exists( $product, 'get_type' ) && 'auction' ==  $product->get_type() ) ) { 

            foreach ( $tabs as $key => $tab ) {
                $title   = APPMAKER_WC::$api->get_settings( 'product_tab_field_title_'.$key );
                if ( 'auction_details' === $key ) {     
                    
                /* $item_condition = __( 'Item condition:', 'wc_simple_auctions' ).$product->get_condition();                
                    $auction_ends  = __( 'Auction ends:', 'wc_simple_auctions' ).date_i18n( get_option( 'date_format' ),  strtotime( $product->get_auction_end_time() ));
                    $gmt_offset    = get_option('gmt_offset') > 0 ? '+'.get_option('gmt_offset') : get_option('gmt_offset');
                    $time          = get_option('timezone_string') ? get_option('timezone_string') : __('UTC ','wc_simple_auctions').$gmt_offset;
                    $timezone      = __('Timezone: ','appmaker-woocommerce-mobile-app-manager') .$time;

                    if ($product->get_auction_sealed() != 'yes'){
                        $starting_bid   = wpautop( do_shortcode($product->get_price_html()));*/

                    ob_start();
                    include_once('class-appmaker-wc-auction-output.php');
                    $content = ob_get_clean();
                /* if($product->is_closed() === FALSE){
                    
                        $content = 
                    }*/

                    $start_date = date('c', strtotime($product_obj->get_auction_start_time()));
                    $end_date   = date('c', strtotime($product_obj->get_auction_end_time()));                
                    
                    $return['auction_details'] = array(
                        'type'          => 'text',
                        'title'         => empty( $title ) ? __( 'Auction details', 'appmaker-woocommerce-mobile-app-manager' ) : $title,
                        'expandable'    => false,
                        'expanded'      => true,
                        'content'       => $content,				
                        'default_value' => '',

                    );
                }else if ( 'auction_timer' === $key ) {
                    $return['auction_timer'] = array(
                        'type'                  => 'countdowntimer',
                        'title'                 => empty( $title ) ?  __( 'Time left:', 'wc_simple_auctions' ) : $title,
                        'expandable'            => false,
                        'expanded'              => true,
                        'bid_start_date'        => $start_date,
                        'bid_end_date'     	    => $product_obj->get_auction_end_time(),                    
                        'default_value'         => '',

                    );
                }else if ( 'bid_value' === $key ) {

                    $min = $max = 0;
                    if($product->get_auction_type() == 'reverse'){
                        if ($product->get_auction_sealed() != 'yes'){       
                            $max = $product->bid_value() ;   
                            $default = $max;          
                        }
                    
                    }else{
                        if ($product->get_auction_sealed() != 'yes'){       
                            $min = $product->bid_value() ;      
                            $default = $min;       
                        }
                    } 

                    $return['bid_value'] = array(
                        'type'              => 'input_stepper',
                        'title'             => empty( $title ) ?   __( 'Start Bidding', 'appmaker-woocommerce-mobile-app-manager' ): $title,
                        'expandable'        => false,
                        'expanded'          => true,
                        'data-auction-id'   => $product_obj->get_id(),
                        'current_bid_price' => $product_obj->get_curent_bid(),
                        'min_bid_price'     => $min,
                        'max_bid_price'	    => $max,
                        'step'              => 1,
                        'button_name'       => __( 'Bid', 'wc_simple_auctions' ),
                        'default_value'     => $default,

                    );
                }
            }
        }
		return $return;
	}
    
    public function submit_bid($request){
       
        $bid = $request['bid'];
        $_POST['post_id'] = $product_id = $request['id'];
        global $product_data ;
        $product_data = wc_get_Product($product_id);
        //$woocommerce_auctions = new WooCommerce_simple_auction();
       // print_r($woocommerce_auctions->woocommerce_simple_auctions_place_bid());exit;
       
    
       $bid_obj = new WC_Bid(); 
       $bid_obj->placebid($product_id, $bid);
       
       $this->bid_placed_message($product_id);
       
       $notices =  WC()->session->get('wc_notices', array());
       $notice = array('current_bid'=> APPMAKER_WC_Helper::get_display_price($product_data->get_curent_bid()),
                       'message' =>array(),
                      );
       if (!empty($notices)) {

        if(!empty($notices['error'])){
            foreach ( $notices['error'] as $key => $error ) {
                if ('EMPTY_ERROR' !== $error ) {
                    $notice['message'][] = html_entity_decode(strip_tags($error));
                }
            }
        }else if (! empty($notices['success'] ) ) {
         
            foreach($notices['success'] as $key => $message ){
                  $notice['message'][] = html_entity_decode(strip_tags($message));
            }
        }
        wc_clear_notices();     
       
        }    
        return $notice;

    }

    public function bid_placed_message($product_id){

        global $woocommerce; 
        $product_data = wc_get_product($product_id);
        $current_user = wp_get_current_user();
    
        if($current_user->ID == $product_data->get_auction_current_bider()){
            if(!$product_data->is_reserve_met() && ('yes' !== $product_data->get_auction_sealed() ) ){
                $message = sprintf( __( 'Successfully placed bid for &quot;%s&quot; but it does not meet the reserve price!', 'wc_simple_auctions' ),$product_data -> get_title()  );
            } else{
    
                if($product_data->get_auction_proxy() && $product_data->get_auction_max_bid()){
                    $message = sprintf( __( 'Successfully placed bid for &quot;%s&quot;! Your max bid is %s.', 'wc_simple_auctions' ),$product_data -> get_title(), wc_price($product_data->get_auction_max_bid())  );
                }else{
                    $message = sprintf( __( 'Successfully placed bid for &quot;%s&quot;!', 'wc_simple_auctions' ),$product_data -> get_title()  );
                }
            }	
            
        } else {
            $message = sprintf( __( "Your bid was successful but you've been outbid for &quot;%s&quot;!", 'wc_simple_auctions' ),$product_data -> get_title()  );	
        }	
    
        wc_add_notice ( apply_filters('woocommerce_simple_auctions_placed_bid_message', $message,$product_id ) );
    }

}
new APPMAKER_WC_simple_auction();
