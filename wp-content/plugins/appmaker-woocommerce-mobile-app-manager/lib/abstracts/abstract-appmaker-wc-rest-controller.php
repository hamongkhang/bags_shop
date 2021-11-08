<?php

if (! defined('ABSPATH') ) {
    exit;
}

/**
 * Abstract Rest Controler Class
 */
abstract class APPMAKER_WC_REST_Controller extends APPMAKER_WP_WC_REST_Controller
{

    /**
     * Plugin Name (WooCommerce or Wordpress) in order to get settings
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
     * Route base.
     *
     * @var string
     */
    protected $rest_base = '';

    protected $isRoot = false;

    /**
     *  Return woocommerce wordpress notice errors
     *
     * @return bool|WP_Error
     */
    public function get_wc_notices_errors()
    {
        $notices = WC()->session->get('wc_notices', array());
        if (! empty($notices['error']) ) {
            $return = false;
            foreach ( $notices['error'] as $key => $error ) {
                if ('EMPTY_ERROR' !== $error ) {
                    if(isset($error['notice']) ){                        
                        $error_message = $error['notice'];
                    }else
                        $error_message = $error;
                    $return = $this->add_error($return, html_entity_decode($error_message), 'appmaker_wc_error', array( 'status' => 405 ));
                }
            }
            wc_clear_notices();

            return $return;
        } else {
            return false;
        }
    }

    /**
     * @param mixed|WP_Error $return
     *
     * @param string         $message
     * @param string         $code
     * @param string         $data
     *
     * @return WP_Error
     */
    public function add_error( $return, $message = '', $code = 'appmaker_wc_error', $data = '' )
    {
        $message = strip_tags($message);
        $message = html_entity_decode($message);
        if (is_wp_error($return) ) {
            $return->add($code, $message, $data);
        } else {
            $return = new WP_Error($code, $message, $data);
        }
        return $return;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function ensure_absolute_link( $url )
    {
        if (! preg_match('~^(?:f|ht)tps?://~i', $url) ) {
            $url = get_site_url(null, $url);
        }
        if (substr($url, 0, 2) === '//' ) {
            $url = 'https:' . $url;
        }
        return $url;
    }

    /**
     * @param $function
     * @param array    $params
     *
     * @return string
     */
    public function return_data( $function, $params = array() )
    {
        $return = '';
        ob_start();
        call_user_func_array($function, $params);
        $return = ob_get_clean();
        return $return;
    }

}
