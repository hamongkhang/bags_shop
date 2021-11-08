<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_YITH_WISHLIST
{

    public function __construct()
    {
      add_filter('appmaker_wc_wishlist_items',array($this,'appmaker_yith_products_wishlist'), 2, 2 );
      add_filter('appmaker_wc_wishlist_add_item_response', array( $this, 'appmaker_add_item_yith_wishlist') , 2 ,2 );
      add_filter('appmaker_wc_wishlist_remove_item_response', array( $this, 'appmaker_remove_item_yith_wishlist') , 2 ,2 );
      add_filter('appmaker_wc_product_data', array($this, 'appmaker_product_check_in_wishlist'), 2, 3);
    }

    public function appmaker_yith_products_wishlist( $response , $request ){
        
        $data = array();
        //sets order by arguments
		$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'dateadded';
		$order = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'desc';
        $user_id = get_current_user_id();

        //$wishlists = YITH_WCWL_Wishlist_Factory::get_wishlists(); //print_r($wishlists);exit;
        if( $user_id ) {
            $args = array(          
                'user_id' => $user_id? $user_id: false,
                'session_id' => ( ! is_user_logged_in() ) ? YITH_WCWL_Session()->get_session_id() : false,
                'wishlist_id' => is_user_logged_in() ? $user_id : 0, // wishlist id is same as user id , 'all' if 
                'order' => $order,
                'orderby'=> $orderby,
            );
            //if( $user_id ){
                $_GET['user_id'] = $user_id;
           // }           
           // if( ! is_user_logged_in() ){
                $wishlist = YITH_WCWL_Wishlist_Factory::get_current_wishlist();
                if( ! empty($wishlist ) ) {
                    $args['wishlist_id'] = $wishlist->get_id();
                }                
           // }           

            $items = YITH_WCWL_Wishlist_Factory::get_wishlist_items( $args );
            $remove_after_add_to_cart = 'yes' == get_option( 'yith_wcwl_remove_after_add_to_cart' );
            if( $items && is_array( $items ) ) {
                foreach( $items as $item ){
                    $data[]      = $item->get_product_id();          
                }
                if( ! empty( $data ) ) {
                    $response = $data;
                }
            } else {
                $response = array( 'status'=> 1 , 'data' => $data );
            }
        } 
        
        return $response;
    }

    public function appmaker_add_item_yith_wishlist( $response , $request ) {
        $user_id = get_current_user_id();       
        if( $user_id ) {
            try {
                $atts = array(
                    'add_to_wishlist'     => $request['id'],
                    'wishlist_id'         =>is_user_logged_in() ? $user_id : 0,
                    'quantity'            => 1,
                    'user_id'             => $user_id ? $user_id : false,
                    'dateadded'           => '',
                    'wishlist_name'       => '',
                    'wishlist_visibility' => 0,
                    'session_id'          => ( ! is_user_logged_in() ) ? YITH_WCWL_Session()->get_session_id() : false,
                );
                
                //$user_id = get_current_user_id();
                //if( $user_id ){
                    $_GET['user_id'] = $user_id;
                //} 
                // YITH_WCWL()->add();
                // $_REQUEST['add_to_wishlist'] = $request['id'];
                $wishlist = YITH_WCWL_Wishlist_Factory::get_current_wishlist();
                if( ! empty($wishlist ) ) {
                    $atts['wishlist_id'] = $wishlist->get_id();
                } else {
                    $wishlist = WC_Data_Store::load( 'wishlist' )->generate_default_wishlist($user_id);
                    $atts['wishlist_id'] = $wishlist->get_id();
                }
                
                YITH_WCWL()->add( $atts );
    
                $message = apply_filters( 'yith_wcwl_product_added_to_wishlist_message', get_option( 'yith_wcwl_product_added_text' ) );
            } catch ( Exception $e ) {
                $return  = $e->getTextualCode();
                $message = apply_filters( 'yith_wcwl_error_adding_to_wishlist_message', $e->getMessage() );
            } catch ( Exception $e ) {
                $return  = 'error';
                $message = apply_filters( 'yith_wcwl_error_adding_to_wishlist_message', $e->getMessage() );
            }
            $response['message'] = $message;//print_r(YITH_WCWL_Session()->get_session_id());
        }       
        return $response;
    }

    public function appmaker_remove_item_yith_wishlist( $response, $request ) {
        $user_id = get_current_user_id();       
        if( $user_id ) {
            $atts = array(
                'remove_from_wishlist' => $request['id'],
                'wishlist_id' => is_user_logged_in() ? get_current_user_id() : 0,
                'user_id' => get_current_user_id()? get_current_user_id(): false,
                'session_id' => ( ! is_user_logged_in() ) ? YITH_WCWL_Session()->get_session_id() : false,
            );
            $user_id = get_current_user_id();
            if( $user_id ){
                $_GET['user_id'] = $user_id;
            } 
            $wishlist = YITH_WCWL_Wishlist_Factory::get_current_wishlist();
            if( ! empty($wishlist ) ) {
                $atts['wishlist_id'] = $wishlist->get_id();
            } 
            try {
                YITH_WCWL()->remove($atts);
                $message = apply_filters( 'yith_wcwl_product_removed_text', __( 'Product successfully removed.', 'yith-woocommerce-wishlist' ) );
            } catch ( Exception $e ) {
                $message = $e->getMessage();
            }
    
            $response['message'] = $message;
        }
       
        return $response;

    }

    public function appmaker_product_check_in_wishlist($data, $product, $expanded){
        if(! empty( $product ) && get_current_user_id() ) {
            $product_id = $product->get_id();
            $user_id = get_current_user_id();
            $wishlist_id = false;
            if($user_id){
                $wishlist_id = $user_id;
                $_GET['user_id'] = $user_id;
            }
            $wishlist = YITH_WCWL_Wishlist_Factory::get_current_wishlist();
            if( ! empty($wishlist ) ) {
                $wishlist_id = $wishlist->get_id();
            } 
		    $exists = YITH_WCWL()->is_product_in_wishlist( $product_id,$wishlist_id );
            $data['product_in_wishlist'] = $exists;
        }
		return $data;
	}
}
new APPMAKER_WC_YITH_WISHLIST();
