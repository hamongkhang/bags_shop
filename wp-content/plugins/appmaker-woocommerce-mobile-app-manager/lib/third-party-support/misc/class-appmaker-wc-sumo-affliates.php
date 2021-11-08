<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_Sumo_Affliates
{
    private $options;
    public $plugin = 'appmaker_wc';

    public function __construct()
    {      
        $this->options = get_option( $this->plugin . '_settings' );
       add_filter( 'appmaker_wc_account_page_response', array($this,'affliate_dashboard'),10,1 );

    }    

    public function affliate_dashboard($return){
        
        $base_url = site_url();
        $aff_dashboard_url = $base_url.'/dashboard';
        $aff_tree_url = $base_url.'/affiliate-tree';

        $api_key = $this->options['api_key'];
        $user_id = get_current_user_id();
        $access_token = apply_filters( 'appmaker_wc_set_user_access_token', $user_id );  

        $aff_dashboard_url = base64_encode($aff_dashboard_url);
        $aff_tree_url = base64_encode($aff_tree_url);

        $aff_dashboard_url = $base_url.'/?rest_route=/appmaker-wc/v1/user/redirect/&url='.$aff_dashboard_url.'&api_key='.$api_key.'&access_token='.$access_token.'&user_id='.$user_id;
        $aff_tree_url = $base_url.'/?rest_route=/appmaker-wc/v1/user/redirect/&url='.$aff_tree_url.'&api_key='.$api_key.'&access_token='.$access_token.'&user_id='.$user_id;
        
       // $fs_affiliate = get_user_meta ( $user_id , 'fs_affiliates_enabled' , true ) == 'yes' ;
       // $aff_dashboard_title = ($fs_affiliate) ? __ ( 'Affiliate Dashboard' , FS_AFFILIATES_LOCALE ) :  __ ( 'Register as an Affiliate' , FS_AFFILIATES_LOCALE );

        $affliate = array('aff_dashboard'=>array(
            'title'  => __ ( 'Affiliate Dashboard' , FS_AFFILIATES_LOCALE ) ,
            'icon'   => array(
                'android' => 'layers',
                'ios'     => 'layers',
            ),
            'action' => array(
                'type' => 'OPEN_IN_WEB_VIEW',
                'params' => array( 'url' => $aff_dashboard_url),
            ),
          ),
          'aff_tree'=>array(
            'title'  => 'Affiliate Tree',
            'icon'   => array(
                'android' => 'align-center',
                'ios'     => 'align-center',
            ),
            'action' => array(
                'type' => 'OPEN_IN_WEB_VIEW',
                'params' => array( 'url' => $aff_tree_url),
            ),
          ),
        );       
        $return = array_slice($return, 0, 5, true) +
            $affliate +
            array_slice($return, 5, count($return)-3, true);
       return $return;
    }


}
new APPMAKER_WC_Sumo_Affliates();
