<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_Visualizer extends APPMAKER_WC_REST_Posts_Abstract_Controller {

	protected $namespace = 'appmaker-wc/v1';
	protected $rest_base = 'products';
	private $options;
	public function __construct() {

		parent::__construct();
        register_rest_route($this->namespace, '/' . $this->rest_base . '/visualizer/(?P<id>[\d]+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_chart_script' ),
                'permission_callback' => array( $this, 'api_permissions_check' ),
                'args'                => $this->get_collection_params(),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
		) );
		
		add_filter( 'appmaker_wc_product_widgets', array( $this, 'visualizer_chart' ), 2, 2 );
		add_filter('appmaker_wc_product_tab_settings', array($this,'visualizer_tab'),1,3);
		$this->options = get_option('appmaker_wc_settings');
	}

	public function visualizer_tab($fields,$product_widget_ids,$product_widgets){

		$settings_controller = APPMAKER_WC::$api->APPMAKER_WC_REST_Settings_Controller;
		
		$fields[] = $settings_controller->get_field(
			array(
							'type'        => 'select',
							'id'          => 'product_widget_visualize',
							'label'       => 'Product Visualize Widget',
							'default'     => $product_widget_ids,
							'multi'       => false,
							'data_source' => array(
								'data' => $product_widgets,
			),
			)
		);

		return $fields;
	}

	public function visualizer_chart( $return, $product_obj ) {

		global $product;
		$product = $product_obj;
		
		$tabs = apply_filters( 'woocommerce_product_tabs', array() );
		$tabs = apply_filters( 'appmaker_wc_product_tabs', $tabs );
		$url = site_url();		
		$api_key = $this->options['api_key'];
		foreach ( $tabs as $key => $tab ) {

			$visualizer_tab = APPMAKER_WC::$api->get_settings('product_widget_visualize', '');			
			if ( $visualizer_tab == $key ) {				
            // $shortcode  = strip_tags($tab['content']);
			    $content = APPMAKER_WC::$api->APPMAKER_WC_REST_Products_Controller->return_data( $tab['callback'], array( $key, $tab ) );
				
				preg_match( '/visualizer-([0-9]+)/', $content, $visualizer );
				$visualizer_id = $visualizer[1];
				$return[ $key ] = array(
					'type'       => 'menu',
					'expandable' => isset( $tab['expandable'] ) ? $tab['expandable'] && true : true,
					'expanded'   => isset( $tab['expanded'] ) ? $tab['expanded'] && true : false,
					'title'      => APPMAKER_WC::$api->APPMAKER_WC_REST_Products_Controller->decode_html( apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ) ),
					'content'    => '',
					'action'     => array(
						'type'   => 'OPEN_IN_WEB_VIEW',
						'params' => array(
							'url'  =>  $url.'/?rest_route=/appmaker-wc/v1/products/visualizer/'.$visualizer_id. '&api_key=' . $api_key,
							'title' => APPMAKER_WC::$api->APPMAKER_WC_REST_Products_Controller->decode_html( apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ) ),
						),
					),
				);
			}
		}
			return $return;
	}

	public function get_chart_script($request){	
		
		$id = $request['id'];
        ob_start();
		wp_head();
		echo do_shortcode(' [visualizer id='.$id.'] ');
		wp_footer();
		$output = ob_get_contents();
        $output = <<<HTML
<html>
<head>
    $output
</head>

</html>
HTML;

        ob_end_clean();
        header('Content-Type:text/html');
        echo $output;exit;
    }
}
new APPMAKER_WC_Visualizer();
