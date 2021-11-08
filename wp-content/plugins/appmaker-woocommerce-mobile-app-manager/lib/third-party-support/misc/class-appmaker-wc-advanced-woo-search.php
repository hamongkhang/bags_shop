<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_woo_search
{

    public function __construct()
    {
         add_filter('appmaker_wc_rest_product_query',array($this,'add_search'),2,2);
         add_filter('appmaker_wc_product_query_result',array($this,'product_query_result'),2,2);

    }

    public function add_search( $args, $request ) {
        if(isset($request['keyword'])) {
            $request['search'] = $_REQUEST['keyword'];
            $args['s'] = $_REQUEST['s'] = $request['search'];
        }
        return $args;
    }

    public function product_query_result( $query_result, $query_args )
    {       
        if(isset($_REQUEST['search']))
              $_REQUEST['keyword'] = $_REQUEST['search'];
        $posts = array();
        //$posts_array = (array) aws_search( $_REQUEST['keyword'] );
        if ( isset( $_REQUEST['search'] ) ) {
            $search_result = AWS_Search::factory()->search( $_REQUEST['keyword'] );
            $data = $search_result['products'];
            foreach ($data as $post) {
                $posts[] = $post['post_data'];
            }   

            $posts_per_page = $query_args['posts_per_page'];
            $paged  = $query_args['paged'];
            $offset = ( $paged > 1 ) ? $paged * $posts_per_page - $posts_per_page : 0;

            $products = array_slice( $posts, $offset, $posts_per_page );

            $query_result = $products;
            return $query_result;

        }else

             return $query_result;

    }
}
new  APPMAKER_WC_woo_search();