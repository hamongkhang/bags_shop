<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_WCFM_Vendors extends APPMAKER_WC_REST_Posts_Abstract_Controller {



	protected $type;
	protected $namespace = 'appmaker-wc/v1';
	//protected $isRoot        = true;
	//protected $inAppPagesKey = '_inAppPages';

	public function __construct() {
		parent::__construct();
		$this->type      = 'inAppPages';
		$this->rest_base = "$this->type";
		/**
		 * Register the routes to get categories and sub-categories
		 */

		register_rest_route(
			$this->namespace,
			'/products/inquiry/(?P<id>[\d]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_product_inquiry' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/inAppPages/dynamic/vendors',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'generate_vendor_listing' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		$this->options = get_option( 'appmaker_wc_settings' );

		add_filter( 'appmaker_wc_product_tabs', array( $this, 'new_product_tab' ), 2, 1 );
		add_filter( 'appmaker_wc_product_widgets', array( $this, 'get_vendor_tab' ), 10, 3 );
		add_filter( 'appmaker_wc_wcfm_vendor', array( $this, 'get_vendor_info' ), 10, 1 );
		add_filter( 'appmaker_wc_cart_items', array( $this, 'product_vendor_name_on_cart' ), 1, 1 );
		add_filter( 'appmaker_wc_order_review', array( $this, 'product_vendor_order_review' ), 1, 1 );
		add_filter( 'woocommerce_rest_prepare_shop_order', array( $this, 'product_vendor_order_detail' ), 1, 3 );
		add_filter( 'appmaker_wc_account_page_response', array( $this, 'my_account_support' ), 10, 1 );
	}

	public function new_product_tab( $tabs ) {
		/* Adds the new tab */
		if ( ! isset( $tabs['wcfm_product_store_tab'] ) ) {
			$tabs['wcfm_product_store_tab'] = array(
				'title'    => __( 'Store', 'woocommerce' ),
				'priority' => 30,
				'callback' => 'woocommerce_product_description_tab',
			);
		}
		if ( ! isset( $tabs['inquiry'] ) ) {
			$tabs['inquiry'] = array(
				'title'    => __( 'Ask a Question', 'wc-frontend-manager' ),
				'priority' => 32,
				'callback' => 'woocommerce_product_description_tab',
			);
		}
		return $tabs;  /* Return all  tabs including the new New Custom Product Tab  to display */
	}

	public function get_vendor_info( $product_id ) {
		global $WCFM, $WCFMmp;
		$vendor_info              = array();
		$vendor_id                = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $product_id );
		$store_logo               = false;
		if( $vendor_id ){

			$vendor_info['id']        = $vendor_id;
		    $shop_name                = html_entity_decode( strip_tags( $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_name_by_vendor( absint( $vendor_id ) ) ) );
		    $vendor_info['shop_name'] = $shop_name;
		    $store_logo               = $WCFM->wcfm_vendor_support->wcfm_get_vendor_logo_by_vendor( $vendor_id );

		}		
		if ( ! $store_logo ) {
			$store_logo = apply_filters( 'wcfmmp_store_default_logo', $WCFM->plugin_url . 'assets/images/wcfmmp.png' );
		}		
		$vendor_info['shop_image'] = $store_logo;
		return $vendor_info;
	}

	public function get_vendor_tab( $return, $product_local, $data ) {

		global $product_obj,$product;
        $product_obj = $product_local;
        $product     = $product_local;        

		$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );
        $product_tabs = apply_filters( 'appmaker_wc_product_tabs', $product_tabs );
        $widgets_enabled_in_app = APPMAKER_WC::$api->get_settings( 'product_widgets_enabled', array() ); 
        if ( ! empty( $widgets_enabled_in_app ) && is_array( $widgets_enabled_in_app ) ) {
            foreach($widgets_enabled_in_app as $id){
                if(array_key_exists($id,$product_tabs)){
                    $tabs[$id] = $product_tabs[$id];
                }
            }
        }else{
            $tabs = $product_tabs;
		}

		$user_id     = get_current_user_id();
		$product_id  = $product->get_id();
		$vendor_info = $this->get_vendor_info( $product_id );
		if($vendor_info){

			$vendor_id   = isset($vendor_info['id']) ? $vendor_info['id'] : '';
		    $shop_name   = isset($vendor_info['shop_name']) ?$vendor_info['shop_name'] : '' ;
		    $shop_image  = isset($vendor_info['shop_image']) ? $vendor_info['shop_image'] : '';
		}		
			
		if( $vendor_id) {
			foreach ( $tabs as $id => $tab ) {
				if ( 'wcfm_product_store_tab' == $id ) {
					$return['wcfm_product_store_tab'] = array(
						'type'   => 'vendor_card',
						'image'  => $shop_image,
						'title'  => __( 'Vendor', 'wc-frontend-manager' ) . ' : ' . $shop_name,
	
						'action' => array(
							'type'   => 'LIST_PRODUCT',
							'params' => array(
								'author' => $vendor_id,
								'title'  => $shop_name,
							),
						),
					);
				}
			
				$url          = site_url();
				$api_key      = $this->options['api_key'];
				$access_token = apply_filters( 'appmaker_wc_set_user_access_token', $user_id );
				if ( 'inquiry' == $id ) {
				
					$return['inquiry'] =  array(
						'type'       => 'menu',
						'expandable' => true,
						'expanded'   => false,
						//'title'      => __( 'Inquiry', 'appmaker' ),
						'title'      => __( 'Ask a Question', 'wc-frontend-manager' ),
						'content'    => '',
						'action'     => array(
							'type'   => 'OPEN_IN_WEB_VIEW',
							'params' => array(
								'url'   => (is_user_logged_in() == true ) ? $url . '/?rest_route=/appmaker-wc/v1/products/inquiry/' . $product_id . '&api_key=' . $api_key . '&access_token=' . $access_token . '&user_id=' . $user_id : $url . '/?rest_route=/appmaker-wc/v1/products/inquiry/' . $product_id . '&api_key=' . $api_key ,
								//'title' => __( 'Inquiry', 'appmaker' ),
								'title' => __( 'Ask a Question', 'wc-frontend-manager' ),
							),
						),
					);
				}				
				
			}
		} else {
			unset( $return['wcfm_product_store_tab'] );
			unset( $return['inquiry'] );
		}			
		

		return $return;
	}

	public function product_vendor_name_on_cart( $return ) {

		foreach ( $return['products'] as $key => $product ) {
			$variation_string = '';
			if ( $product['variation_id'] != 0 ) {
				$variation_id = $product['variation_id'];
				$variation    = wc_get_product( $variation_id );
				$product_id   = $variation->get_parent_id();
			} else {
				$product_id = $product['product_id'];
			}
			$vendor_info = $this->get_vendor_info( $product_id );
			if ( ! empty( $vendor_info['id'] ) ) {
				$variation_string                               .= __( 'Vendor', 'wc-frontend-manager' ) . ' : ' . $vendor_info['shop_name'] . "\n";
				$return['products'][ $key ]['variation_string'] .= $variation_string;
			}
		}
		return $return;
	}

	public function product_vendor_order_review( $return ) {
		// print_r($return['products']);exit;
		foreach ( $return['products'] as $key => $product ) {
			$variation_string = '';
			$vendor_info      = $this->get_vendor_info( $product['product_id'] );
			if ( ! empty( $vendor_info['id'] ) ) {
				$variation_string                       .= "\n" . __( 'Vendor', 'wc-frontend-manager' ) . ' : ' . $vendor_info['shop_name'] . "\n";
				$return['products'][ $key ]['quantity'] .= $variation_string;
			}
		}
		return $return;
	}


	public function product_vendor_order_detail( $response, $post, $request ) {
		$shop_name = '';
		$order     = wc_get_order( ( $response->data['id'] ) );
		foreach ( $order->get_items() as $item ) {
			$product = $item->get_data();
			if ( $product['variation_id'] != 0 ) {
				$variation_id = $product['variation_id'];
				$variation    = wc_get_product( $variation_id );
				$product_id   = $variation->get_parent_id();
			} else {
				$product_id = $product['product_id'];
			}
			$vendor_info = $this->get_vendor_info( $product_id );
			if ( ! empty( $vendor_info['id'] ) ) {
				$shop_name = 'Vendor : ' . $vendor_info['shop_name'] . "\n";
			}
			foreach ( $response->data['line_items'] as $key => $item ) {

				if ( $product_id == $item['product_id'] ) {
					$response->data['line_items'][ $key ]['quantity'] .= "\n" . $shop_name;
				}
			}
		}
		return $response;
	}

	public function get_product_inquiry( $request ) {
		global $wp, $WCFM;
		$user_id     = get_current_user_id();
		$product_id  = $request['id'];
		$vendor_info = $this->get_vendor_info( $product_id );
		$vendor_id   = $vendor_info['id'];
		ob_start();
		$content = $WCFM->template->get_template(
			'enquiry/wcfm-view-enquiry-form.php',
			array(
				'product_id' => $product_id,
				'vendor_id'  => $vendor_id,
			)
		);

		echo $content;

		$output = ob_get_contents();
		$output = <<<HTML
	<html>
	<head>
	$output
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
	<style>
	#wcfm_enquiry_form{direction: rtl !important;}
	div.wcfm_popup_wrapper h2, form.wcfm_popup_wrapper h2 {
	direction: rtl;
    font-size: 20px;
    font-style: italic;
    line-height: 20px;
    /*display: table-cell;*/
    float: left;
    font-weight: 600;
    color: #17a2b8;
    margin-top: 6px;
    margin-bottom: 15px;
    width: auto;
    padding: 0;
    padding-bottom: 15px;
    clear: none;
}.wcfm_popup_wrapper .wcfm_popup_label {
    width: 38%;
    font-weight: 600;
    font-size: 15px;
    font-style: italic;
    display: inline-block;
    vertical-align: top;
}
.wcfm_popup_wrapper .wcfm_popup_input {
    width: 59%!important;
}.wcfm_popup_wrapper .wcfm_popup_input {
    line-height: 18px;
    padding: 8px 10px;
    font-size: 15px;
    display: inline-block!important;
    box-shadow: none;
    background-color: #fff!important;
    border: 1px solid #ccc!important;
    border-radius: 3px;
    margin-bottom: 15px;
}
.wcfm_popup_wrapper .wcfm_popup_button {
    float: right;
    margin-top: 10px;
    margin-left: 10px;
    background: #1a1a1a none repeat scroll 0 0;
    border: 0 none;
    border-radius: 4px;
    color: #fff;
    font-family: Montserrat,"Helvetica Neue",sans-serif;
    font-weight: 500;
    letter-spacing: .046875em;
    line-height: 1;
    padding: .84375em .875em .78125em;
    text-transform: uppercase;
}
.wcfm-error {
    color: #f86c6b;
}
.wcfm-success {
    color: #4dbd74;
}
.wcfm-success, .wcfm-error{
    border: 1px solid;
    border-radius: 2px;
    margin: 10px 0;
    padding: 15px 10px 15px 50px;
    background-repeat: no-repeat;
    background-position: 10px center;
    
}</style>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"type="text/javascript"></script>
	<script>
	jQuery(document).ready(function($) {
		$("#wcfm_enquiry_form").submit(function(e) {
		e.preventDefault(); // avoid to execute the actual submit of the form.
		var form = $(this);
		var url = "/wp-admin/admin-ajax.php";
		$.ajax({
			type: "POST",
			url: url,
			data: {
			action: "wcfm_ajax_controller",
			controller: "wcfm-enquiry-tab",
			wcfm_enquiry_tab_form: form.serialize(),
			status: "submit"
			}, 
			success: function(data)
			{
				data = JSON.parse(data)
				if(data.status == false){
				$(".wcfm-message").html("<div class='wcfm-error'>"+data.message+"</div>")
				}else{
					$(".wcfm-message").html("<div class='wcfm-success'>"+data.message+"</div>")
				}
			}
			});
		});	
	});
	</script>
	</head>
	</html>
HTML;

		ob_end_clean();
		header( 'Content-Type:text/html' );
		echo $output;
		exit;
	}

	public function generate_vendor_listing() {
		$request     = '';
		$all_vendors = $this->get_vendor_listing( $request );
		$this->createVendorPage( $all_vendors );
	}

	public function get_vendor_listing( $request ) {
		global $WCFM;
		$_POST['controller'] = 'wcfm-vendors';
		define( 'WCFM_REST_API_CALL', true );
		$WCFM->init();
		$wcfm_vendors_array    = array();
		$wcfm_vendors_json_arr = array();
		$response              = array();
		$wcfm_vendors_array    = $WCFM->ajax->wcfm_ajax_controller();

		if ( ! empty( $wcfm_vendors_array ) ) {
			$index = 0;
			foreach ( $wcfm_vendors_array as $wcfm_vendors_id => $wcfm_vendors_name ) {
				$response[ $index ] = $this->get_formatted_item_data( $wcfm_vendors_id, $wcfm_vendors_json_arr, $wcfm_vendors_name );
				$index++;
			}
			usort( $response, array( $this, 'sortbyname' ) );			
			return apply_filters( 'wcfmapi_rest_prepare_store_vendors_objects', $response, $request );
			//return rest_ensure_response( $response );
		} else {
			return rest_ensure_response( $response );
		}
	}
	
	public function sortbyname($left, $right) {
		return strcmp($left['vendor_shop_name'] , $right['vendor_shop_name']);
	}

	protected function get_formatted_item_data( $wcfm_vendors_id, $wcfm_vendors_json_arr, $wcfm_vendors_name ) {
		global $WCFM;

		$wcfm_vendors_json_arr['vendor_id'] = $wcfm_vendors_id;
		//$wcfm_vendors_json_arr['vendor_display_name'] =  $wcfm_vendors_name;
		$wcfm_vendors_json_arr['vendor_shop_name'] = html_entity_decode( $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_name_by_vendor( $wcfm_vendors_id ), ENT_QUOTES, 'UTF-8');
		$store_logo                                = $WCFM->wcfm_vendor_support->wcfm_get_vendor_logo_by_vendor( $wcfm_vendors_id );
		if ( ! $store_logo ) {
			$store_logo = apply_filters( 'wcfmmp_store_default_logo', $WCFM->plugin_url . 'assets/images/wcfmmp.png' );
		}
		$wcfm_vendors_json_arr['vendor_shop_image']   = $store_logo;
		$vendor_rating                                = get_user_meta( $wcfm_vendors_id, '_wcfmmp_avg_review_rating', true );
		$wcfm_vendors_json_arr['vendor_ratings']      = $vendor_rating;
		$wcfm_vendors_json_arr['vendor_total_orders'] = '';

		return $wcfm_vendors_json_arr;
	}

	function createVendorPage( $vendors ) {
			$grid_items = array();
		foreach ( $vendors  as $vendor ) {
			$grid_items[] = $this->grid_item( $vendor );
		}
			$page_id     = 'vendor_home';
			$in_app_page = array(
				'id'      => $page_id,
				'title'   => __( 'Vendor List', 'wc-frontend-manager' ),
				'style'   => array( 'backgroundColor' => '#F2F5F8' ),
				'widgets' => $grid_items,
			);

			$in_app_page_json = json_encode( $in_app_page );

			echo $in_app_page_json;
			exit;
	}

	function grid_item( $vendor ) {
		//print_r($vendor);
		$data = array(
			'type'       => 'vendor',
			'attributes' => array(
				'title'        => $vendor['vendor_shop_name'],
				'image'        => ! empty( $vendor['vendor_shop_image'] ) ? $vendor['vendor_shop_image'] : '',
				'rating'       => ! empty( $vendor['vendor_ratings'] ) ? $vendor['vendor_ratings'] : '0',
				'orders'       => '',
				'store_action' => array(
					'type'   => 'LIST_PRODUCT',
					'params' => array(
						'author' => $vendor['vendor_id'],
						'title'  => $vendor['vendor_shop_name'],
					),
				),
			),
		);
			 return $data;
	}


	public function my_account_support( $return ) {

		if( ! APPMAKER_WC::$api->get_settings( 'hide_wcfm_support_ticket_tab', false ) ) {
			$base_url     = site_url();
			$my_account_page_id = get_option( 'woocommerce_myaccount_page_id' );
			if ( $my_account_page_id ) {
				$url = get_permalink( $my_account_page_id );
			}
			if( empty($url) ){
				$url = $base_url . '/my-account';
			}
			
			$url          = $url. 'support-tickets/';
			$api_key      = $this->options['api_key'];
			$user_id      = $user_id = get_current_user_id();
			$access_token = apply_filters( 'appmaker_wc_set_user_access_token', $user_id );
			$url          = add_query_arg( array( 'from_app_support' => true ), $url );
			$url          = base64_encode( $url );
			$url          = $base_url . '/?rest_route=/appmaker-wc/v1/user/redirect/&url=' . $url . '&api_key=' . $api_key . '&access_token=' . $access_token . '&user_id=' . $user_id;
			$wallet       = array(
				'wallet' => array(
					'title'  => __( 'Support Tickets', 'wc-frontend-manager' ),
					'icon'   => array(
						'android' => 'send',
						'ios'     => 'send',
					),
					'action' => array(
						'type'   => 'OPEN_IN_WEB_VIEW',
						'params' => array( 'url' => $url ),
					),
				),
			);
			$return       = array_slice( $return, 0, 5, true ) +
			$wallet +
			array_slice( $return, 5, count( $return ) - 3, true );
		}
		return $return;
	}

}
 new APPMAKER_WC_WCFM_Vendors();
