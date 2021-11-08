<?php

class APPMAKER_WC_REST_FRONTEND_Controller extends APPMAKER_WP_WC_REST_FRONTEND_Controller
{
    /**
     * Plugin name (appmaker_wc or appmaker) Get settings according to plugin
     *
     * @var string
     */
    public $plugin = 'appmaker_wc';
    /**
     * Endpoint namespace.
     *
     * @var string
     */
    protected $namespace = 'appmaker-wc/v1';

    /**
     * Get a single meta
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_item( $request )
    {      
        $this->send_cache_header();
        $cache_key = 'appmaker_wc_' . $this->type;
        if (! empty($request['key']) ) {
            $cache_key .= '-' . $request['key'];
        }

        if (! empty($request['language']) ) {
            $cache_key .= '-' . $request['language'];
        }
        if (! empty($request['currency']) ) {
            $cache_key .= '-' . $request['currency'];
        }

        if (APPMAKER_WC::$api->get_settings('cache_enabled', false) ) {
            $response = get_transient($cache_key);
            // Valid cache found.
            if (false !== $response ) {
                return $response;
            }
        }

        if (! empty($request['language']) ) {
            $option_original = get_option($this->getSafeKey($request['key'] . '-' . $request['language']));
            $option = get_option($this->getSafeKey($request['key'] . '-' . $request['language'] . '_app'));
        } else {
            $option_original = false;
            $option = false;
        }

        if (! $option || empty($option) || isset($option['data']) && empty($option['data']) ||  isset($option['widgets']) && empty($option['widgets']) ) {
            $option_original = get_option($this->getSafeKey($request['key']));
            $option = get_option($this->getSafeKey($request['key'] . '_app'));
        }

        if (( ! $option || empty($option) || empty($option['data']) )  && 'navigationMenu' === $this->type ) {
            $option = APPMAKER_WP_WC_Converter::convert_navMenu_data(APPMAKER_WC::$api->APPMAKER_WC_REST_BACKEND_NAV_Controller->get_default_menu());
        } elseif (false !== $option_original  && 'inAppPages' === $this->type ) {
            $option = APPMAKER_WP_WC_Converter::convert_inAppPage_data($option_original, $request['key']);
        } elseif (! $option && 'inAppPages' === $this->type && 'home' === $request['key'] ) {
            $option = APPMAKER_WP_WC_Converter::convert_inAppPage_data(APPMAKER_WC::$api->APPMAKER_WC_REST_BACKEND_INAPPPAGE_Controller->get_default_home(),  $request['key']);
        } elseif (! $option ) {
            return new WP_Error('rest_invalid_key', __('Key is not invalid.'), array( 'status' => 404 ));
        }

        $item = array( 'key' => $request['key'], 'data' => $option );

        $data     = $this->prepare_item_for_response($item, $request);
        $response = rest_ensure_response($data);
        set_transient($cache_key, $response, 60 * 10);
        return $response;
    }
}
