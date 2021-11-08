<?php
/**
 * REST API Products controller
 *
 * Handles requests to the /products endpoint.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * REST API Products controller class.
 *
 */
class APPMAKER_WC_REST_Products_Controller extends APPMAKER_WC_REST_Posts_Abstract_Controller {

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
    protected $rest_base = 'products';

    /**
     * Post type.
     *
     * @var string
     */
    protected $post_type = 'product';

    protected $product_images = array();

    protected $product_image_ids = array();

    protected $sanitize_attribute_ids = true;
    public $head   = false;
	public $footer = false;

    /**
     * Initialize product actions.
     */
    public function __construct() {
        parent::__construct();
        add_filter( "appmaker_wc_rest_{$this->post_type}_query", array( $this, 'query_args' ), 10, 2 );
        add_action( "appmaker_wc_rest_insert_{$this->post_type}", array( $this, 'clear_transients' ) );
        $this->sanitize_attribute_ids = APPMAKER_WC::$api->get_settings( 'sanitize_attribute_ids', 1 );
    }

    /**
     * Register the routes for products.
     */
    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/filters', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_filters' ),
                'permission_callback' => array( $this, 'api_permissions_check' ),
                'args'                => $this->get_collection_params(),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->rest_base, array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_items' ),
                'permission_callback' => array( $this, 'api_permissions_check' ),
                'args'                => $this->get_collection_params(),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_item' ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),
                'args'                => array(
                    'context' => $this->get_context_param( array( 'default' => 'view' ) ),
                ),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        //add review to product
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)/comments', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'add_comment' ),
                'permission_callback' => array( $this, 'api_permissions_check' ),
                'args'                => $this->add_comment_params(),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        //get all reviews of a product
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/comments', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_reviews' ),
                'permission_callback' => array( $this, 'api_permissions_check' ),
                'args'                => array(
                    'context' => $this->get_context_param( array( 'default' => 'view' ) ),
                ),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        /*register_rest_route( $this->namespace, '/' . $this->rest_base .'/(?P<slug>[a-zA-Z0-9-_]+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_products_from_slug' ),
                'permission_callback' => array( $this, 'api_permissions_check' ),
                'args'                => $this->get_collection_params(),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );*/

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/deeplinking', array(
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'get_item_from_url' ),
                'permission_callback' => array( $this, 'api_permissions_check' ),
                'args'                => $this->get_collection_params(),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        //add submit form        
         register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)/submit', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'submit_form' ),
                'permission_callback' => array( $this, 'api_permissions_check' ),
                //'args'                => $this->add_comment_params(),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        //add input stepper       
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)/stepper', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'input_stepper' ),
                'permission_callback' => array( $this, 'api_permissions_check' ),
                //'args'                => $this->add_comment_params(),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        //get pre-build products list
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/lists/(?P<key>[a-zA-Z0-9\-\_]+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_products_list' ),
                'permission_callback' => array( $this, 'api_permissions_check' ),
                'args'                => $this->get_collection_params(),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        //get product tab content
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/content/(?P<key>[a-zA-Z0-9\-\_]+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_content' ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),
                'args'                => $this->get_collection_params(),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        //post product tab content
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/content/(?P<key>[a-zA-Z0-9\-\_]+)', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'get_content' ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),
                'args'                => $this->get_collection_params(),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        //get tabbed categories
        register_rest_route( $this->namespace, '/' . $this->rest_base .'/tabbed/categories', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_tabbed_categories' ),
                'permission_callback' => array( $this, 'api_permissions_check' ),
                'args'                => $this->get_collection_params(),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

    }

    public function add_comment_params() {
        $params = array();

        $params['rating']   = array(
            'description'       => __( 'Comment Author name.', 'woocommerce' ),
            'type'              => 'string',
            'validate_callback' => 'rest_validate_request_arg',
        );
        $params['content']      = array(
            'description'       => __( 'Comment Author name.', 'woocommerce' ),
            'type'              => 'string',
            'validate_callback' => 'rest_validate_request_arg',
            'required'          => true,
        );
        return $params;
    }

    /**
     * Get the query params for collections of attachments.
     *
     * @return array
     */
    public function get_collection_params() {
        $params = parent::get_collection_params();

        $params['orderby'] = array(
            'enum'    => array(
                'date',
                'default',
                'popularity',
                'price',
                'rating',
            ),
            'default' => false,
        );

        $params['order'] = array(
            'enum'    => array(
                'ASC',
                'DESC',
                'asc',
                'desc',
            ),
            'default' => false,
        );
     
        $params['slug']           = array(
            'description'       => __( 'Limit result set to products with a specific slug.', 'woocommerce' ),
            'type'              => 'string',
            'validate_callback' => 'rest_validate_request_arg',
        );
        $params['status']         = array(
            'default'           => 'any',
            'description'       => __( 'Limit result set to products assigned a specific status.', 'woocommerce' ),
            'type'              => 'string',
            'enum'              => array_merge( array( 'any' ), array_keys( get_post_statuses() ) ),
            'sanitize_callback' => 'sanitize_key',
            'validate_callback' => 'rest_validate_request_arg',
        );
        $params['type']           = array(
            'description'       => __( 'Limit result set to products assigned a specific type.', 'woocommerce' ),
            'type'              => 'string',
            'enum'              => array_keys( wc_get_product_types() ),
            'sanitize_callback' => 'sanitize_key',
            'validate_callback' => 'rest_validate_request_arg',
        );
        $params['category']       = array(
            'description'       => __( 'Limit result set to products assigned a specific category.', 'woocommerce' ),
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => 'rest_validate_request_arg',
        );
        $params['tag']            = array(
            'description'       => __( 'Limit result set to products assigned a specific tag.', 'woocommerce' ),
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => 'rest_validate_request_arg',
        );
        $params['shipping_class'] = array(
            'description'       => __( 'Limit result set to products assigned a specific shipping class.', 'woocommerce' ),
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => 'rest_validate_request_arg',
        );
        $params['attribute']      = array(
            'description'       => __( 'Limit result set to products with a specific attribute.', 'woocommerce' ),
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => 'rest_validate_request_arg',
        );        
        $params['attribute_term'] = array(
            'description'       => __( 'Limit result set to products with a specific attribute term (required an assigned attribute).', 'woocommerce' ),
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => 'rest_validate_request_arg',
        );
        $params['sku']            = array(
            'description'       => __( 'Limit result set to products with a specific SKU.', 'woocommerce' ),
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => 'rest_validate_request_arg',
        );

        return $params;
    }

    /**
     * Query args.
     *
     * @param array $args
     * @param WP_REST_Reques
    register_rest_route( $this->namespace, '/' . $this->rest_base . '/batch', array(
     * array(
     * 'methods'             => WP_REST_Server::EDITABLE,
     * 'callback'            => array( $this, 'batch_items' ),
     * 'permission_callback' => array( $this, 'batch_items_permissions_check' ),
     * 'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
     * ),
     * 'schema' => array( $this, 'get_public_batch_schema' ),
     * ) );t $request
     *
     * @return array
     */
    public function query_args( $args, $request ) {
        // Set post_status.
        $args['post_status'] = $request['status'];

        // Taxonomy query to filter products by type, category,
        // tag, shipping class, and attribute.
        $tax_query = array();

        // Map between taxonomy name and arg's key.
        $taxonomies = array(
            'product_cat'            => 'category',
            'product_tag'            => 'tag',
            'product_shipping_class' => 'shipping_class',
        );

        // Set tax_query for each passed arg.
        foreach ( $taxonomies as $taxonomy => $key ) {
            if ( ! empty( $request[ $key ] ) ) {
                $terms = explode( ',', $request[ $key ] );

                $tax_query[] = array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $terms,
                );
            }
        }

        // Filter product type by slug.
        if ( ! empty( $request['type'] ) ) {
            $terms = explode( ',', $request['type'] );
            $tax_query[] = array(
                'taxonomy' => 'product_type',
                'field'    => 'slug',
                'terms'    => $terms,
            );
        }

        //Exclude specific product types
        // $tax_query[] = array(
        //     'relation' => 'AND',
        //     array(
        //     'taxonomy' => 'product_type',
        //     'field'    => 'slug',
        //     'terms'    => array('woosb'),
        //     'operator' =>'NOT IN'
        //     )
        // );

        // Filter by attribute and term.
        if ( ! empty( $request['attribute'] ) && ! empty( $request['attribute_term'] ) ) {
            if ( in_array( $request['attribute'], wc_get_attribute_taxonomy_names() ) ) {
                $terms = explode( ',', $request['attribute_term'] );

                $tax_query[] = array(
                    'taxonomy' => $request['attribute'],
                    'field'    => 'term_id',
                    'terms'    => $terms,
                );
            }
        }

        // Filter products 

        if( ! empty( $request['prebuild'] ) ) {
            $type = $request['prebuild'];
            if ( ! empty( $args['meta_query'] ) ) {
                $args['meta_query'] = array();
            }
            $args['post_status'] = 'publish';
            switch( $type ) {
                case 'recent' : $args['order'] = 'date';
                    $args['orderby'] = 'DESC';
                    break;

                case 'best_selling' : $args['meta_key'] = 'total_sales';
                    $args['orderby']  = 'meta_value_num';
                    $args['order']     = 'DESC';
                    $args['fields']  = 'id=>parent';                                 
                    break;
                case 'top_rated': $args['meta_key']  = '_wc_average_rating';
                    $args['orderby' ]  = 'meta_value_num';
                    $args['order']     = 'DESC';
                    $args['fields' ]   = 'id=>parent';
                    break;
                case 'featured' : $args['fields' ]  = 'id=>parent';                                    
                    $args['order']     = 'DESC';                  
                    $args['post__in' ]  = array_merge( array( 0 ), wc_get_featured_product_ids() );
                    break;
                case 'sale':  $args['posts_per_page'] = 12;
                    $args['fields' ]   = 'id=>parent';                         
                    $args['order']     = 'DESC';                  
                    $args['post__in' ]  = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
                    break;                

            }        

        }

      //brand filter
        if ( ! empty( $request['product_brand'] ) ) {
            $term = $request['product_brand'];
            $taxonomy_id = class_exists( 'YITH_WCBR' ) ? 'yith_product_brand' : 'product_brand';
            $taxonomy_id = apply_filters('appmaker_wc_backend_taxonomy', $taxonomy_id );          
            $tax_query[] = array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => $taxonomy_id,
                        'terms'    => $term,
                        'field'    => 'id'
                    )
            );
        }
        
        // Filter by sku.
        if ( ! empty( $request['sku'] ) ) {
            if ( ! empty( $args['meta_query'] ) ) {
                $args['meta_query'] = array();
            }

            $args['meta_query'][] = array(
                'key'     => '_sku',
                'value'   => $request['sku'],
                'compare' => '=',
            );

            $args['post_type'] = array( 'product', 'product_variation' );
        }
        // App Filter by attribute and term and price .
        if ( ! empty( $request['app_filter'] ) ) {
            $app_filter = $request['app_filter'];
            if ( isset( $app_filter['price_filter'] ) ) {
                if ( isset( $app_filter['price_filter']['min'] ) && isset( $app_filter['price_filter']['max'] ) ) {
                    $args['meta_query'][] = array(
                        'key'     => '_price',
                        'value'   => array( $app_filter['price_filter']['min'], $app_filter['price_filter']['max'] ),
                        'compare' => 'BETWEEN',
                        'type'    => 'numeric',
                    );
                } elseif ( isset( $app_filter['price_filter']['min'] ) ) {
                    $args['meta_query'][] = array(
                        'key'     => '_price',
                        'value'   => $app_filter['price_filter']['min'],
                        'compare' => '>=',
                        'type'    => 'numeric',
                    );
                } elseif ( isset( $app_filter['price_filter']['max'] ) ) {
                    $args['meta_query'][] = array(
                        'key'     => '_price',
                        'value'   => $app_filter['price_filter']['max'],
                        'compare' => '<=',
                        'type'    => 'numeric',
                    );
                }

                unset( $app_filter['price_filter'] );
            }
            foreach ( $app_filter as $filter => $values ) {
                $tax_query[] = array(
                    'taxonomy' => $filter,
                    'field'    => 'slug',
                    'terms'    => $values,
                );
            }
        }
        if ( ! empty( $tax_query ) ) {
            $args['tax_query'] = $tax_query;
        } else {
            $args['tax_query'] = array();
        }


        if ( method_exists( WC()->query, 'get_tax_query' ) ) {
            $args['tax_query'] = WC()->query->get_tax_query( $args['tax_query'] );
        }
        $args['meta_query'][] = WC()->query->visibility_meta_query();
        if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
                $args['meta_query'][] = array(
                    'key'     => '_stock_status',
                    'value'   => 'outofstock',
                    'compare' => '!=',
                );
        }


        if ( isset( $request['author'] ) ) {
            $args['author'] = $request['author'];
        }

        return apply_filters( 'appmaker_wc_product_query_args', $args, $request );
    }

    public function get_filters($request) {
        global $wpdb;
        $this->send_cache_header();
        $cacheEnabled = APPMAKER_WC::$api->get_settings( 'cache_enabled', false );
        if ( $cacheEnabled ) {        
            $hash = $this->get_request_hash( $request );
            $response = get_transient( 'appmaker_wc_products_filters_response_' . $hash );
            if ( !empty( $response ) ) {                                
                return $response;
            }
        }

        $return = array( 'items' => array() );
        $prices = $this->get_filtered_price();

        if ( ! ( $prices->min_price == $prices->max_price || $prices->min_price > $prices->max_price || APPMAKER_WC::$api->get_settings( 'hide_price_from_filter', false )) ) {
            $return['items']['price_filter'] = array(
                'type'  => 'multi_slider',
                'id'    => 'price_filter',
                'label' => __( 'Price', 'woocommerce' ),
                'min'   => (double) $prices->min_price,
                'max'   => (double) $prices->max_price,
                'step'  => ( ( $prices->max_price - $prices->min_price ) > 100 ) ? ( $prices->max_price - $prices->min_price ) / 100 : 1,
            );
        }

        $attrs_raw              = wc_get_attribute_taxonomy_names();
        $i                      = - 1;       
        $filters_enabled_in_app = APPMAKER_WC::$api->get_settings( 'product_filter_attributes', array() );
        //To get product filter based on attribute
        $attribute_filter = apply_filters('appmaker_wc_enable_auto_filters_by_cat',false);
        if($attribute_filter == false){ 
        foreach ( $attrs_raw as $filter ) {
            $cat_filter_attributes = array();
            if ( isset( $request['category'] ) && ! empty( $request['category'] ) ) {
                $cat_filter_attributes = get_term_meta( $request['category'], 'appmaker-wc-category-filter-attributes', true );
                if ( empty ( $cat_filter_attributes ) ) {
                   $parent_categories = get_ancestors( $request['category'], 'product_cat' );
                   foreach ( $parent_categories as $parent_category ) {
                        $cat_filter_attributes = get_term_meta( $request['category'], 'appmaker-wc-category-filter-attributes', true );
                        if ( ! empty( $cat_filter_attributes ) ) {
                            break;
                        }
                   }
                }
                if ( ! empty( $cat_filter_attributes ) && ! in_array( $filter, $cat_filter_attributes ) ) {
                    continue;
                }
            }
            if ( empty( $cat_filter_attributes ) && ! empty( $filters_enabled_in_app ) && ! in_array( $filter, $filters_enabled_in_app ) ) {
                continue;
            }

            $terms = get_terms( $filter );
            if ( ! empty( $terms ) ) {
                $return['items'][ $filter ] = array(
                    'id'    => $filter,
                    'type'  => 'checkbox',
                    'label' => $this->decode_html( wc_attribute_label( $filter ) ),
                );
                foreach ( $terms as $term ) {
                    $return['items'][ $filter ]['values'][] = array(
                        'label' => $this->decode_html( $term->name ),
                        'value' => $term->slug,
                    );
                }
            }
        }
        }
        else{
            $filter_raw = array();
            $cat_name = get_term($request['category'], 'product_cat', ARRAY_A );
            if(!empty($cat_name ) && !is_wp_error($cat_name )){

                $args = array(
                    'category'  => array($cat_name['slug'] ),
                    'limit' => 200 
                );
                
                foreach( wc_get_products($args) as $product ){
                    foreach( $product->get_attributes() as $attr_name => $attr ){
                    $filter_raw[] = $attr_name;
                    if(is_array($attr->get_terms())){    
                        foreach( $attr->get_terms() as $term ){
                            $terms_raw[] = $term->name;
                        }
                    }
                    }
                }
                $filters = array_unique(array_intersect((array)$filter_raw,(array)$attrs_raw));
                if(is_array($filters)){    
                foreach ( $filters as $filter ){
                    $terms = get_terms( $filter );
                    if ( ! empty( $terms ) ) {
                        
                        $return['items'][ $filter ] = array(
                            'id'    => $filter,
                            'type'  => 'checkbox',
                            'label' => $this->decode_html( wc_attribute_label( $filter ) ),
                        );
                        foreach ( $terms as $term ) {
                            if(in_array($term->name,$terms_raw)){
                            $return['items'][ $filter ]['values'][] = array(
                                'label' => $this->decode_html( $term->name ),
                                'value' => $term->slug,
                            );
                            }
                        }
                    }
                }
                }
            }
           
        }
        
        if ( $cacheEnabled ) {
            $cache_time = APPMAKER_WC::$api->get_settings( 'cache_time', 60 );
            if ( ! isset( $hash ) ) {
                $hash = $this->get_request_hash( $request );
            }
            set_transient( 'appmaker_wc_products_filters_response_' . $hash, $return, $cache_time * 60 );
        }

        return apply_filters( 'appmaker_wc_product_filters', $return );
    }

    protected function get_filtered_price() {
        global $wpdb, $wp_the_query;

        $args       = $wp_the_query->query_vars;
        $tax_query  = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
        $meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();

        if ( ! empty( $args['taxonomy'] ) && ! empty( $args['term'] ) ) {
            $tax_query[] = array(
                'taxonomy' => $args['taxonomy'],
                'terms'    => array( $args['term'] ),
                'field'    => 'slug',
            );
        }

        foreach ( $meta_query as $key => $query ) {
            if ( ! empty( $query['price_filter'] ) || ! empty( $query['rating_filter'] ) ) {
                unset( $meta_query[ $key ] );
            }
        }

        $meta_query = new WP_Meta_Query( $meta_query );
        $tax_query  = new WP_Tax_Query( $tax_query );

        $meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
        $tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

        $sql = "SELECT min( CAST( price_meta.meta_value AS UNSIGNED ) ) as min_price, max( CAST( price_meta.meta_value AS UNSIGNED ) ) as max_price FROM {$wpdb->posts} ";
        $sql .= " LEFT JOIN {$wpdb->postmeta} as price_meta ON {$wpdb->posts}.ID = price_meta.post_id " . $tax_query_sql['join'] . $meta_query_sql['join'];
        $sql .= " 	WHERE {$wpdb->posts}.post_type = 'product'
					AND {$wpdb->posts}.post_status = 'publish'
					AND price_meta.meta_key IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price' ) ) ) ) . "')
					AND price_meta.meta_value > '' ";
        $sql .= $tax_query_sql['where'] . $meta_query_sql['where'];

        return $wpdb->get_row( $sql );
    }

    /**
     * Prepare a single product output for response.
     *
     * @param WP_Post $post Post object.
     * @param WP_REST_Request $request Request object.
     *
     * @return WP_REST_Response $data
     */
    public function prepare_item_for_response( $post, $request ) {
        $expanded = isset( $request['id'] ) ? true : false;       

        $product  = wc_get_product( $post->ID );

        if(empty($product))
        {
            return false;
        }
        $data     = $this->get_product_data( $product, $expanded );

        // Add variations to variable products.
        if ( $product->is_type( 'variable' ) && $product->has_child() && $expanded ) {
            $data['variations'] = $this->get_variation_data( $product );
            if ( isset( $this->product_images[ $post->ID ] ) ) {
                $this->product_images[ $post->ID ] = array_values( array_unique( $this->product_images[ $post->ID ] ) );
                $data['images']                    = $this->product_images[ $post->ID ];
            }
            $data['images_meta'] = array();
            if ( isset( $this->product_image_ids[ $post->ID ] ) ) {
                $this->product_images[ $post->ID ] = array_values( array_unique( $this->product_images[ $post->ID ] ) );
                $data['images_meta']               = $this->get_images_meta( $this->product_images[ $post->ID ] );
            }
        }
        if ( empty( $data['images'] ) ) {
            // Set a placeholder image if the product has no images set.
            $image                 = $this->ensure_absolute_link( wc_placeholder_img_src() );
            $data['images'][]      = $image;
            $data['images_meta'][] = array( 'caption' => false, 'size' => wc_get_image_size('large') );
        }

        // Add grouped products data.
        if ( $product->is_type( 'grouped' ) && $product->has_child() ) {
            $data['grouped_products'] = $product->get_children();
        }

        $context = ! empty( $request['context'] ) ? $request['context'] : 'view';
        $data    = $this->add_additional_fields_to_object( $data, $request );
        $data    = $this->filter_response_by_context( $data, $context );

        // Wrap the data in a response object.
        //	$response = rest_ensure_response( $data );
        //	$response->add_links( $this->prepare_links( $product, $request ) );

        /**
         * Filter the data for a response.
         *
         * The dynamic portion of the hook name, $this->post_type, refers to post_type of the post being
         * prepared for the response.
         *
         * @param WP_REST_Response $response The response object.
         * @param WP_Post $post Post object.
         * @param WP_REST_Request $request Request object.
         */
        return apply_filters( "appmaker_wc_rest_prepare_{$this->post_type}", $data, $post, $request );
    }


    /**
     * Get product data.
     *
     * @param WC_Product|WC_Product_Simple|WC_Product_Variation|WC_Product_External|WC_Product_Variable $product_obj
     *
     * @param bool $expanded
     *
     * @return array
     */
    public function get_product_data( $product_obj, $expanded = false ) {
        if ( isset( APPMAKER_WC::$api->APPMAKER_WC_REST_Cart_Controller ) ) {
            $cart_controller = APPMAKER_WC::$api->APPMAKER_WC_REST_Cart_Controller;
        } else {
            $cart_controller = new APPMAKER_WC_REST_Cart_Controller();
        }

        global $product;
        global $post;

        $product = $product_obj;
        $post    = get_post( APPMAKER_WC_Helper::get_id( $product ) );
        $product_id                  = (int) $product->is_type( 'variation' ) ? $product->get_variation_id() : APPMAKER_WC_Helper::get_id( $product );
        //product in webview
        $product_in_webview_array = $this->get_product_in_webview($product);
        $post_data = get_post( APPMAKER_WC_Helper::get_id( $product ) );
        $thumbnail = $this->get_thumbnail( $product );

        if ( isset( $_GET['auto_search'] ) && $_GET['auto_search'] == true ) {
            $data      = array(
                'id'                      => (int) $product_obj->is_type( 'variation' ) ? $product_obj->get_variation_id() : APPMAKER_WC_Helper::get_id( $product ),
                'name'                    => $this->decode_html( $product_obj->get_title() ),
                'product_in_webview'      => false,   
                'thumbnail'               => $thumbnail['url'],
                'thumbnail_meta'          => $thumbnail['size']
            );
            if( is_array($product_in_webview_array) && $product_in_webview_array['product_in_webview'] ) {

                $data['product_in_webview_action'] = $product_in_webview_array['product_in_webview_action'];
                $data['product_in_webview']        = $product_in_webview_array['product_in_webview'];
            }
            return $data;
        }       

        $display_add_to_cart = APPMAKER_WC::$api->get_settings( 'display_add_to_cart_block', true );
        if($display_add_to_cart){
            $display_add_to_cart = $product_obj->is_type( 'external' ) ? false : true;
        }
           
        $price = ((get_option( 'woocommerce_prices_include_tax', 'no' )=='no') && (get_option( 'woocommerce_tax_display_shop','inc' )=='incl' ))?$product_obj->get_price_including_tax():$product_obj->get_price();
        $regular_price = ((get_option( 'woocommerce_prices_include_tax', 'no' )=='no') && (get_option( 'woocommerce_tax_display_shop','inc' )=='incl' ))?wc_get_price_including_tax( $product, array('price' => $product_obj->get_regular_price()  ) ):$product_obj->get_regular_price();
        $sale_price = $product_obj->get_sale_price() ? $product_obj->get_sale_price() : ( ( $product_obj->get_price() < $product_obj->get_regular_price() ) ? $product_obj->get_price() : '') ;
        $sale_percentage = $this->get_sale_percentage( $product );
        if( empty( $price ) || 0 == $price ) {
            $price = $regular_price;
        }
        
        if ( $product_obj->is_type( 'external' ) ) {
            $buy_now_text = empty( $product_obj->get_button_text() ) ? __( 'Buy product', 'woocommerce' ) : $product_obj->get_button_text();
        } else {
            $buy_now_text = __( 'Buy now', 'woocommerce' );
        }

        $hide_short_desc_product_list = APPMAKER_WC::$api->get_settings( 'hide_short_description_product_list', true );
        $data      = array(
            'id'                      => (int) $product_obj->is_type( 'variation' ) ? $product_obj->get_variation_id() : APPMAKER_WC_Helper::get_id( $product ),
            'name'                    => $this->decode_html( $product_obj->get_title() ),
            'slug'                    => $post_data->post_name,
            'permalink'               => $this->ensure_absolute_link( $product_obj->get_permalink() ),
            'type'                    => $product_obj->get_type(),
            'featured'                => $product_obj->is_featured(),
            //'description'             => wpautop( do_shortcode( $post_data->post_content ) ),
            'short_description'       => ($hide_short_desc_product_list) ? '' : apply_filters( 'woocommerce_short_description', $post_data->post_excerpt ),
            'sku'                     => $product_obj->get_sku(),
            'currency'                => get_woocommerce_currency(),
            'currency_symbol'         => html_entity_decode(get_woocommerce_currency_symbol(),ENT_QUOTES, 'UTF-8'),
            'price'                   => $price,
            'regular_price'           => $regular_price,
            //'regular_price'           =>$product_obj->get_regular_price() ,
            'sale_price'              => $sale_price,
            'price_display'           => APPMAKER_WC_Helper::get_display_price($price),
            'regular_price_display'   => APPMAKER_WC_Helper::get_display_price( $regular_price ),
            'sale_price_display'      => APPMAKER_WC_Helper::get_display_price($sale_price),
            'price_html'              => $product_obj->get_price_html(),
            'on_sale'                 => ( $product_obj->get_price() < $product_obj->get_regular_price() || $product_obj->is_on_sale() ),
            'sale_percentage'         => ($sale_percentage != 0)? $sale_percentage.'%': false,
            'purchasable'             => $product_obj->is_purchasable(),
            'downloadable'            => $product_obj->is_downloadable(),
            'display_add_to_cart'     => $display_add_to_cart,
            'change_thumbnail_image_size'=>(bool) APPMAKER_WC::$api->get_settings( 'change_thumbnail_image_size', false ),
            'hide_buy_now_block'      => (bool) APPMAKER_WC::$api->get_settings( 'hide_buy_now_block', false ),
            'buy_now_action'          => $this->get_buy_now_action( $product ),
            'buy_now_button_text'     => $buy_now_text,
            'add_to_cart_button_text' => __( 'Add to cart', 'woocommerce' ),
            'qty_config'              => $cart_controller->get_qty_args( $product ),
            'stock_quantity'          => $product_obj->get_stock_quantity(),
            'in_stock'                => $product_obj->is_in_stock(),
            'weight'                  => $product_obj->get_weight(),
            'dimensions'              => array(
                'length' => $product_obj->get_length(),
                'width'  => $product_obj->get_width(),
                'height' => $product_obj->get_height(),
            ),
            'reviews_allowed'         => ( 'open' === $post_data->comment_status ),
            'display_rating'          => ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) ? false : true,
            'average_rating'          => APPMAKER_WC::$api->get_settings( 'hide_product_rating', false ) ? false : wc_format_decimal( $product_obj->get_average_rating(), 2 ),
            'rating_count'            => (int) $product_obj->get_rating_count(),
            //'view_more_reviews'       => ( (int) $product_obj->get_review_count() ) > 10 ? true : false ,
            //'categories'              => $this->get_taxonomy_terms( $product ),
            //'tags'                    => $this->get_taxonomy_terms( $product, 'tag' ),
            'images'                  => array(),
            'thumbnail'               => $thumbnail['url'],
            'thumbnail_meta'          => $thumbnail['size']
        );

        $sync_cart_qty = APPMAKER_WC::$api->get_settings( 'cart_sync_qty', false ); 
        if( is_array( $data['qty_config'] ) && ! WC()->cart->is_empty() && ( $sync_cart_qty || isset( $_GET['show_in_cart'] ) ) ) {         
            $master_product_id = $product_id;
            $in_cart = false;
            foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                if ( $master_product_id === $cart_item['product_id'] ) {
                    $qty = $cart_item['quantity'];
                    $data['qty_config']['in_cart'] = $qty ;
                    $in_cart = true;
                    break;
                }
            }              
        }

        $images              = $this->get_images( $product, $expanded, APPMAKER_WC_Helper::get_id( $product ) );            
        $data['images']      = $images['images'];
        $data['images_meta'] = $this->get_images_meta( $images['attachment_ids'] );

        if(empty($data['images'])){
            $data['images'] = array($thumbnail['url']);
            $size = $thumbnail['size'];  
            $data['images_meta'][] = array('caption'=>'','size'=>$size);         
        }   
        //product backorder      
        if( $product->managing_stock() && $product->is_on_backorder( 1 ) && $product->backorders_require_notification() && $product->get_type() === 'simple'  ){
            $data['notify_backorder'] = true ;
        } elseif ( ! $product->managing_stock() && $product->is_on_backorder( 1 ) && $product->get_type() === 'simple' ) {
			$data['notify_backorder'] = true ;
        } else {
            $data['notify_backorder'] = false ;
        }
        $data['notify_backorder_label'] =  __( 'On backorder', 'woocommerce' );
        
        $attributes          = $this->get_attributes( $product, true, false );
        if ( isset( $attributes['pa_color'] ) ) {
            $data['color'] = apply_filters( 'appmaker_wc_color_response', $attributes['pa_color'] );
        } else {
            $data['color'] = false;
        }
        $data['attributes']         = array_values( $attributes );
        $data['default_attributes'] = $this->get_default_attributes( $product );
        if ( $product->is_type( 'variable' ) && $product->has_child() &&  APPMAKER_WC::$api->get_settings( 'product_variations_data', false )  ) {	
            $data['variations'] = $this->get_variation_data( $product );	
        } 
        $data['product_in_webview'] = false;
        if( is_array($product_in_webview_array) && $product_in_webview_array['product_in_webview'] ) {
            $data['product_in_webview'] = $product_in_webview_array['product_in_webview'];
            $data['product_in_webview_action'] = $product_in_webview_array['product_in_webview_action'];
        }           

        $attributes_enabled_in_app = APPMAKER_WC::$api->get_settings( 'product_display_attributes', array() );  
        $data['labels'] = array();      
        if( $attributes_enabled_in_app ) {
            $labels = array();
            foreach ( $attributes_enabled_in_app as $attribute_id ) {
                $product_attributes = $product->get_attributes();
                foreach( $product_attributes as $id => $attribute ) {
                    if( $id == $attribute_id){
                        $terms = wc_get_product_terms( $product_id, $attribute_id ) ;
                        if($terms){
                            foreach($terms as $item => $term){
                                 $labels[]  = array('label' => $term->name );
                            }
                        }
                    }
                }
            }
            $data['labels'] = $labels ;
        }

        if( isset( $_GET['show_product_fields'] ) || APPMAKER_WC::$api->get_settings( 'add_product_fields_list', false )  ) {
            $fields                     = apply_filters( 'appmaker_wc_product_fields', array(), $product );
            if ( ! empty( $fields ) ) {
                $data['fields'] = $fields;
            }
        }

        if ( $expanded ) {
            //$data['attributes']         = array_values( $attributes );
           // $data['default_attributes'] = $this->get_default_attributes( $product );
            $data['variations']         = array();
            $data['product_widgets']    = array();
            $fields                     = apply_filters( 'appmaker_wc_product_fields', array(), $product );
            if ( ! empty( $fields ) ) {
                $data['fields'] = $fields;
            }

            $data['product_widgets'] = array_values( $this->product_widgets( $product, $data ) );
            $product = $product_obj;
            $post    = get_post( APPMAKER_WC_Helper::get_id( $product_obj ) );

            if ( class_exists( 'WC_Bulk_Variations_Compatibility' ) && ! empty( $GLOBALS['wc_bulk_variations'] ) ) {

                if ( get_post_meta( APPMAKER_WC_Helper::get_id( $product ), '_bv_type', true ) ) {
                    if ( WC_Bulk_Variations_Compatibility::is_wc_version_gte_2_4() ) {
                        $matrix_data = woocommerce_bulk_variations_create_matrix_v24( APPMAKER_WC_Helper::get_id( $product ) );
                    } else {
                        $matrix_data = woocommerce_bulk_variations_create_matrix( APPMAKER_WC_Helper::get_id( $product ) );
                    }
                    //print_r($matrix_data);
                    unset( $matrix_data['variations'] );
                    $matrix_data['row_title']    = WC_Bulk_Variations_Compatibility::wc_attribute_label( $matrix_data['row_attribute'] );
                    $matrix_data['column_title'] = WC_Bulk_Variations_Compatibility::wc_attribute_label( $matrix_data['column_attribute'] );

                    foreach ( $matrix_data['matrix_rows'] as $row ) {
                        foreach ( $matrix_data['matrix_columns'] as $column ) {
                            if ( isset( $matrix_data['matrix'][ $row ][ $column ] ) ) {
                                $matrix_data['variation_details'][ $row ][ $column ] = array(
                                    'regular_price_display' => APPMAKER_WC_Helper::get_display_price( $matrix_data['matrix'][ $row ][ $column ]['regular_price'] ),
                                    'manage_stock'          => isset( $matrix_data['matrix'][ $row ][ $column ]['manage_stock'] ) ? $matrix_data['matrix'][ $row ][ $column ]['manage_stock'] : true,
                                    'stock_quantity'        => isset( $matrix_data['matrix'][ $row ][ $column ]['stock_quantity'] ) ? $matrix_data['matrix'][ $row ][ $column ]['stock_quantity'] : $matrix_data['matrix'][ $row ][ $column ]['max_qty'],
                                    'in_stock'              => isset( $matrix_data['matrix'][ $row ][ $column ]['is_in_stock'] ) ? $matrix_data['matrix'][ $row ][ $column ]['is_in_stock'] : ( ! $matrix_data['matrix'][ $row ][ $column ]['manage_stock'] || $matrix_data['matrix'][ $row ][ $column ]['stock_status'] == 'instock' ),
                                );
                            } else {
                                $matrix_data['variation_details'][ $row ][ $column ] = false;
                            }
                        }
                    }
                    unset( $matrix_data['matrix'] );
                    $data['bulk_variations'] = $matrix_data;

                }
            }
        }

        if( APPMAKER_WC::$api->get_settings( 'disable_flash_sale_badge', false ) == '1'){
            $data['sale_percentage'] = '';
        }

        if ( $product_obj->get_type() === 'variable' ) {           

            $variation_prices_array = $this->variable_product_price( $product );        
            

            if ( $product_obj->is_on_sale() ) {

                $variation_regular_price_array = $this->variable_sale_product_regular_price_display( $product ); 

                $data['regular_price']         = $variation_regular_price_array['value'];
                $data['regular_price_display'] = $variation_regular_price_array['display'];
                $data['price']                 =  $variation_prices_array['value'];
                $data['price_display']         =  $variation_prices_array['display'];
                $data['sale_price']            = $variation_prices_array['value'];
                $data['sale_price_display']    = $variation_prices_array['display'];
            } else {
                $data['price']                 = $variation_prices_array['value'];
                $data['price_display']         = $variation_prices_array['display'];
                $data['regular_price_display'] = $variation_prices_array['display'];
            }
        }
        return apply_filters( 'appmaker_wc_product_data', $data, $product, $expanded );

    }

    public function get_product_in_webview( $product ) {

        $data = array();
        $products_in_webview         = apply_filters( 'appmaker_wc_products_in_webview', array( 'bundle','yith_bundle','lottery','grouped','gift-card','composite','woosb','pw-gift-card' ) );
        $category_in_webview         = apply_filters('appmaker_wc_category_in_webview', array());
        $product_category            = wp_get_post_terms( $product->get_id() ,'product_cat' );
        foreach($product_category as $term){
            $category = $term->term_id;
        }
        $enable_all_products_webview = apply_filters( 'appmaker_wc_all_products_in_webview', APPMAKER_WC::$api->get_settings('enable_products_in_webview', false ) );
        $data['product_in_webview']   = false; 
        $product_id                  = (int) $product->is_type( 'variation' ) ? $product->get_variation_id() : APPMAKER_WC_Helper::get_id( $product );
        if( in_array($product->get_type() , $products_in_webview ) || in_array($product_id, $products_in_webview ) || $enable_all_products_webview || (! empty($category_in_webview) && in_array($category, $category_in_webview ) )) { 
            $title = $this->decode_html( $product->get_title() );
            $data['product_in_webview'] = true;
            $product_url =  add_query_arg( array( 'from_app' => true , 'key' => true ), $this->ensure_absolute_link( $product->get_permalink() ) );                      
            $data['product_in_webview_action'] = array(                
                    'type' => 'OPEN_IN_WEB_VIEW',
                    'params' => array( 'url' => $product_url, 'title'  => ''),         
                );
        }
        return $data;      
    }

    protected function get_images_meta( $attachment_ids ) {
        $data = array();
        foreach ( $attachment_ids as $id ) {
            $attachment = get_post( $id );
            $data[]     = array(
                'caption' => function_exists( 'wp_get_attachment_caption' ) && wp_get_attachment_caption( $id ) ? $attachment->post_excerpt : '',
                'size'  => APPMAKER_WC_Helper::get_image_dimensions($attachment)
            );
        }

        return $data;
    }

    /**
     * @param WC_Product_Variable $product
     *
     * @return string
     */
    protected function variable_product_price( $product ) {
        $prices = $product->get_variation_prices( true );
        $price  = array();
        // No variations, or no active variation prices.
        if ( $product->get_price() === '' || empty( $prices['price'] ) ) {
            $price['value']   =  $product->get_variation_price();
            $price['display'] = APPMAKER_WC_Helper::get_display_price( $price['value'] );

        } else {
            $min_price = $product->get_variation_price('min', TRUE);
            $max_price = $product->get_variation_price('max', TRUE);
            if( ! $min_price && ! $max_price ) {
                $min_price = current( $prices['price'] );
                $max_price = end( $prices['price'] );
            }            
            $price['display']    = $min_price !== $max_price ? sprintf( _x( '%1$s-%2$s', '', 'woocommerce' ), APPMAKER_WC_Helper::get_display_price( $min_price ), APPMAKER_WC_Helper::get_display_price( $max_price ) ) : APPMAKER_WC_Helper::get_display_price( $min_price );
            $price['value']      = $min_price !== $max_price ? sprintf( _x( '%1$s-%2$s', '', 'woocommerce' ), $min_price ,  $max_price ) : $min_price ;
        }

        return $price;        
    }

    /**
     * @param WC_Product_Variable $product
     */
    protected function variable_sale_product_regular_price_display( $product ) {
        $prices = $product->get_variation_prices( true );

        if ( $product->get_price() === '' || empty( $prices['price'] ) ) {
            $price['value'] = $product->get_variation_regular_price();
            $price['display'] = APPMAKER_WC_Helper::get_display_price( $price['value'] );

        } else {
            $min_regular_price = $product->get_variation_regular_price('min', TRUE);
            $max_regular_price = $product->get_variation_regular_price('max', TRUE);
            if( ! $min_regular_price && ! $max_regular_price ) {
                $min_regular_price = current( $prices['regular_price'] );
                $max_regular_price = end( $prices['regular_price'] );
            }            
            $price['display']    = $min_regular_price !== $max_regular_price ? sprintf( _x( '%1$s-%2$s', '', 'woocommerce' ), APPMAKER_WC_Helper::get_display_price( $min_regular_price ), APPMAKER_WC_Helper::get_display_price( $max_regular_price ) ) : APPMAKER_WC_Helper::get_display_price( $min_regular_price );
            $price['value']      = $min_regular_price !== $max_regular_price ? sprintf( _x( '%1$s-%2$s', '', 'woocommerce' ), $min_regular_price , $max_regular_price  ) :  $min_regular_price ;
        }

        return $price;
    }

    protected function get_buy_now_action( $product ) {
        if ( $product->is_type( 'external' ) ) {
            return array(
                'type'   => 'OPEN_URL',
                'params' => array( 'url' => $product->get_product_url() ),
            );
        } else {
            return array(
                'type'   => 'normal',
                'params' => array(),
            );
        }
    }

    /**
     * Get taxonomy terms.
     *
     * @param WC_Product $product
     * @param string $taxonomy
     *
     * @return array
     */
    protected function get_taxonomy_terms( $product, $taxonomy = 'cat' ) {
        $terms = array();

        foreach ( wp_get_post_terms( APPMAKER_WC_Helper::get_id( $product ), 'product_' . $taxonomy ) as $term ) {
            $terms[] = array(
                'id'   => $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
            );
        }

        return $terms;
    }

    /**
     * Get the images for a product or product variation.
     *
     * @param WC_Product|WC_Product_Variation $product
     *
     * @param bool $merge_images
     *
     * @param int $merge_id
     *
     * @return array
     *
     */
    protected function get_images( $product, $merge_images = false, $merge_id = 0 ) {
        $images         = array();
        $attachment_ids = array();

        if($merge_images){

            if ( $product->is_type( 'variation' ) ) {
                if ( has_post_thumbnail( $product->get_variation_id() ) ) {
    
                    // Add variation image if set.
                    $attachment_ids[] = get_post_thumbnail_id( $product->get_variation_id() );
                } elseif ( has_post_thumbnail( APPMAKER_WC_Helper::get_id( $product ) ) ) {
                    // Otherwise use the parent product featured image if set.
                    $attachment_ids[] = get_post_thumbnail_id( APPMAKER_WC_Helper::get_id( $product ) );
                }
            } else {
                // Add featured image.
                if ( has_post_thumbnail( APPMAKER_WC_Helper::get_id( $product ) ) ) {
                    $attachment_ids[] = get_post_thumbnail_id( APPMAKER_WC_Helper::get_id( $product ) );
                }
                // Add gallery images.
                if ( method_exists( $product, 'get_gallery_image_ids' ) ) {
                    $attachment_ids = array_merge( $attachment_ids, $product->get_gallery_image_ids() );
                } else {
                    $attachment_ids = array_merge( $attachment_ids, $product->get_gallery_attachment_ids() );
                }
            }
    
            // Build image data.
            foreach ( $attachment_ids as $position => $attachment_id ) {
                $attachment_post = get_posts( $attachment_id );
                if ( is_null( $attachment_post ) ) {
                    continue;
                }
    
                $attachment = wp_get_attachment_image_src( $attachment_id, 'full' );
                if ( ! is_array( $attachment ) ) {
                    continue;
                }
               $image    = $this->ensure_absolute_link( current( $attachment ) );
                $images[] = $image;
    
            }
    
            if ( true === $merge_images ) {
                if ( ! isset( $this->product_images[ $merge_id ] ) ) {
                    $this->product_images[ $merge_id ]    = array();
                    $this->product_image_ids[ $merge_id ] = array();
                }
                $this->product_images[ $merge_id ]    = array_merge( $this->product_images[ $merge_id ], $images );
                $this->product_image_ids[ $merge_id ] = array_merge( $this->product_image_ids[ $merge_id ], $attachment_ids );
    
            } elseif ( empty( $images ) ) {
                // Set a placeholder image if the product has no images set.
                $image    = $this->ensure_absolute_link( wc_placeholder_img_src() );
                $images[] = $image;
            }

        }        
        $images = apply_filters( 'appmaker_wc_product_images', $images );
        return array( 'images' => $images, 'attachment_ids' => $attachment_ids );
    }

    /**
     * @param $product
     *
     * @return array|false
     */
    protected function get_thumbnail( $product ) {

        if ( APPMAKER_WC::$api->get_settings( 'change_thumbnail_image_size', false ) ) {
            $size='full';
        }else
            $size='medium';
        $size = apply_filters( 'appmaker_wc_product_image_size', $size );
        $thumbnail_id = get_post_thumbnail_id( $product->is_type( 'variation' ) ? $product->variation_id : APPMAKER_WC_Helper::get_id( $product ) );
        $image = wp_get_attachment_image_src( $thumbnail_id,  $size  );
        if ( empty( $image ) ) {
            $image =  array(
                "url" => $this->ensure_absolute_link( wc_placeholder_img_src() ),
                "size" => wc_get_image_size( $size )
            );
        } else {
            $thumb_post = get_post($thumbnail_id);
            $image = array(
                "url" => $this->ensure_absolute_link( $image[0] ),
                "size" => APPMAKER_WC_Helper::get_image_dimensions($thumb_post ,$size ,true ),
            );
        }
        $image = apply_filters('appmaker_wc_product_image_url',$image,$size);
        return $image;
    }

    private function sanitize_id( $title ) {
        $title = trim( $title );
        //$title = strtolower( $title );
        return ( $this->sanitize_attribute_ids ) ? sanitize_title( $title ) : $title;
    }

    /**
     * Get the attributes for a product or product variation.
     *
     * @param WC_Product|WC_Product_Variation|WC_Product_Variable $product
     *
     * @param bool $variations
     * @param bool $visible
     *
     * @return array
     */
    protected function get_attributes( $product, $variations = true, $visible = true ) {
        $attributes = array();

        if ( $product->is_type( 'variation' ) ) {

            // Variation attributes.
            foreach ( $product->get_variation_attributes() as $attribute_name => $attribute ) {
                $name = str_replace( 'attribute_', '', $attribute_name );
                //$name = strtolower($name);
                // Taxonomy-based attributes are prefixed with `pa_`, otherwise simply `attribute_`.
                if ( 0 === strpos( $attribute_name, 'attribute_pa_' ) ) {
                    $attributes[ $name ] = array(
                        'id'     => $name,
                        'name'   => $this->get_attribute_taxonomy_label( $name ),
                        'option' => $attribute,
                    );
                } else {
                    $id                = $this->sanitize_id( $name );
                    $attributes[ $id ] = array(
                        'id'     => $id,
                        'name'   => html_entity_decode( str_replace( 'pa_', '', $name ), ENT_QUOTES, 'UTF-8' ),
                        'option' => $attribute,
                    );
                }
            }
        } else {
            if ( ! $product->is_type( 'variable' ) ) {
                $variations = false;
            }
            foreach ( $product->get_attributes() as $attribute ) {
                if ( $variations && ! $visible ) {
                    $display = $attribute['is_variation'];
                } elseif ( ! $variations && $visible ) {
                    $display = $attribute['is_visible'];
                } elseif ( ! $variations && ! $visible ) {
                    $display = false;
                } else {
                    $display = true;
                }
                $only_variations = $variations && ! $visible;
                $id              = $this->sanitize_id( $attribute['name'] );
                if ( $attribute['is_taxonomy'] && $display ) {
                    $attributes[ $id ] = array(
                        'id'       => $id,
                        'name'     => $this->get_attribute_taxonomy_label( $attribute['name'] ),
                        'position' => (int) $attribute['position'],
                        'visible'  => (bool) $attribute['is_visible'],
                        'options'  => $this->get_attribute_options( $product, $attribute, $only_variations ),
                    );
                } elseif ( $display ) {
                    $attributes[ $id ] = array(
                        'id'       => $id,
                        'name'     => str_replace( 'pa_', '', $attribute['name'] ),
                        'position' => (int) $attribute['position'],
                        'visible'  => (bool) $attribute['is_visible'],
                        'options'  => $this->get_attribute_options( $product, $attribute, $only_variations ),
                    );
                }

                if ( $id == 'pa_color' && $display ) {
                    foreach ( $attributes[ $id ]['options'] as $color_key => $color ) {
                        $attributes[ $id ]['options'][ $color_key ]['color_code'] = apply_filters( 'appmaker_wc_color_code', $color['slug'] );
                    }
                }
            }
        }

        return apply_filters( 'appmaker_wc_product_attributes', $attributes, $product, $variations, $visible );
    }

    /**
     * Get attribute taxonomy label.
     *
     * @param  string $name
     *
     * @return string
     */
    protected function get_attribute_taxonomy_label( $name ) {
        $tax = get_taxonomy( $name );
        if ( empty( $tax ) ) {
            $tax = get_taxonomy( urldecode( $name ) );
        }
        if ( ! empty( $tax ) ) {
            $labels = get_taxonomy_labels( $tax );
            $tax_label = $labels->singular_name;
            // WPML attribute label transaltion
            if ( class_exists( 'SitePress' ) ) {                
                $tax_label =  apply_filters( 'wpml_translate_single_string', $tax_label, 'WordPress', 'taxonomy singular name: '.$tax_label );                
            }
            return html_entity_decode( $tax_label, ENT_QUOTES, 'UTF-8' );
        } else {
            return '';
        }
    }

    /**
     * Get attribute options.
     *
     * @param WC_Product_Variation $product
     * @param array $attribute
     *
     * @return array
     */
    protected function get_attribute_options( $product, $attribute, $only_variations = false ) {
        if ( $product->is_type( 'variable' ) ) {
            $variation_attrs = $product->get_variation_attributes();
            if ( isset( $attribute['name'] ) && isset( $variation_attrs[ $attribute['name'] ] ) ) {
                $variation_attrs = $variation_attrs[ $attribute['name'] ];
            } else {
                $variation_attrs = array();
            }
        } else {
            $variation_attrs = array();
        }

        if ( isset( $attribute['is_taxonomy'] ) && $attribute['is_taxonomy'] ) {
            $terms  = wc_get_product_terms( APPMAKER_WC_Helper::get_id( $product ), $attribute['name'], array( 'fields' => 'all' ) );
            $return = array();
            foreach ( $terms as $term ) {
                $in_variation = $this->check_option_in_variation( $variation_attrs, $term->slug );
                if ( ! $only_variations || $in_variation ) {
                    $return[] = array(
                        'name' => htmlspecialchars_decode ($term->name),
                        'slug' => $term->slug,
                    );
                }
            }

            return $return;
        } elseif ( isset( $attribute['value'] ) ) {
            $terms  = explode( '|', $attribute['value'] );
            $return = array();
            foreach ( $terms as $term ) {
                $in_variation = $this->check_option_in_variation( $variation_attrs, $term );
                if ( ! $only_variations || $in_variation ) {
                    $return[] = array(
                        'name' => trim( $term ),
                        //'slug' => $this->sanitize_id( $term ),
                        'slug' => trim($term),
                    );
                }
            }

            return $return;
        }

        return array();
    }

    /**
     * Check option in variation.
     *
     * @param array $variation_attrs $variation_attrs Array.
     * @param string $option option to check.
     *
     * @return bool
     */
    protected function check_option_in_variation( $variation_attrs, $option ) {
        if ( ! empty( $variation_attrs ) ) {
            $regex = preg_replace( '/\//', '\/', trim( preg_quote( $option ) ) );

            return empty( $variation_attrs ) ? false : ( preg_grep( '/(' . $regex . ')/i', $variation_attrs ) ? true : false );
        } else {
            return false;
        }
    }

    /**
     * Get default attributes.
     *
     * @param WC_Product $product
     *
     * @return array
     */
    protected function get_default_attributes( $product ) {
        $default = array();

        if ( $product->is_type( 'variable' ) ) {
            foreach ( array_filter( (array) get_post_meta( APPMAKER_WC_Helper::get_id( $product ), '_default_attributes', true ), 'strlen' ) as $key => $value ) {
                if ( 0 === strpos( $key, 'pa_' ) ) {
                    $default[] = array(
                        'id'     => $key,
                        'name'   => $this->get_attribute_taxonomy_label( $key ),
                        'option' => $value,
                    );
                } else {
                    $default[] = array(
                        'id'     => $this->sanitize_id( $key ),
                        'name'   => str_replace( 'pa_', '', $key ),
                        'option' => $value,
                    );
                }
            }
        }

        return $default;
    }

    /**
     * @param WC_Product $product_obj
     *
     * @return mixed
     */

    protected function product_widgets( $product_obj, $data ) {
        global $product;
        global $post,$wp_query;        
        $product = $product_obj;
        $post    = get_post( APPMAKER_WC_Helper::get_id($product_obj));
        $wp_query->is_singular = true;
        $wp_query->post  = $post;
        $return  = array();
        remove_filter( 'woocommerce_product_tabs', 'woocommerce_sort_product_tabs', 99 );
        add_filter( 'appmaker_wc_product_tabs', 'woocommerce_sort_product_tabs', 99);
        $product_tabs    = apply_filters( 'woocommerce_product_tabs', array() );
        $product_tabs    = apply_filters( 'appmaker_wc_product_tabs', $product_tabs);
        $force_hide_description = APPMAKER_WC::$api->get_settings('force_hide_description', false);
        if ( empty( $product_tabs ) ) {
            $product_tabs = array( 'description' => '' );
        }elseif(!isset( $product_tabs['description']) && $force_hide_description === false){
            $product_tabs['description'] = '';
        }
        $tabs = array();
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

        $short_description_shown = false;
        foreach ( $tabs as $key => $tab ) {
            $product = $product_obj;
            $tab_type = APPMAKER_WC::$api->get_settings('product_tab_display_type_' . $key, 'DEFAULT');
            if ($tab_type === 'HIDDEN') {
                continue;
            }

            if ( $key == 'reviews' && ( $tab_type == 'DEFAULT' ) ) {
                $reviews = $this->get_reviews( $product, 10);
                ob_start();
                $this->decode_html( apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ) );
                $review_title = ob_get_clean();
                if( empty($review_title) ) {
                    $review_title = $this->decode_html( apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ) );
                }
              //  if ( ! empty( $reviews ) && ! empty( $reviews->data ) ) {
                    $return[ $key ] = array(
                        'type'       => 'reviews',
                        'expandable' => true,
                        'expanded'   => false,
                        'allow_product_review'=>( 'open' === $post->comment_status ),
                        'review_button_title'=>__('Add review', 'appmaker-woocommerce-mobile-app-manager'),
                        'view_more_reviews'       => ( (int) $product->get_review_count() ) > 10 ? true : false ,
                        'title'      =>  $review_title ,
                        'items'      => $reviews,
                    );
              //  }
            }
            elseif($key=='short_description' && $tab_type=='DEFAULT' && $short_description_shown === false){
                $short_description_shown = true;
                if ( method_exists( 'WPBMap', 'addAllMappedShortcodes' ) ) {
                    WPBMap::addAllMappedShortcodes();
                }
                $post_data   = get_post( APPMAKER_WC_Helper::get_id( $product ) );
                $short_description= apply_filters( 'woocommerce_short_description', $post_data->post_excerpt );
                $title=APPMAKER_WC::$api->get_settings( 'product_tab_field_title_short_description');
                if ( ! empty( $short_description ) ) {
                    $return['short_description'] = array(
                        'type'       => 'text',
                        'title'      =>empty($title)?__( 'Short description', 'woocommerce' ):$title,
                        'expandable' => true,
                        'expanded'   => true,
                        'content'    => get_locale()=='ar'?'<span style="textAlign: left;">'.apply_filters( 'appmaker_wc_product_description', wpautop( do_shortcode( $short_description ) ) ).'</span>' : apply_filters( 'appmaker_wc_product_description', wpautop( do_shortcode( $short_description ) ) ),
                    );
                }

            }elseif ( $key == 'description' && $tab_type == 'DEFAULT') {


                if ( method_exists( 'WPBMap', 'addAllMappedShortcodes' ) ) {
                    WPBMap::addAllMappedShortcodes();
                }
                $post_data   = get_post( APPMAKER_WC_Helper::get_id( $product) );
                $description = wpautop( do_shortcode( $post_data->post_content ) );
                $title=APPMAKER_WC::$api->get_settings( 'product_tab_field_title_description');

               if ( empty( $description ) && $short_description_shown === false ) {
                    $description = apply_filters( 'woocommerce_short_description', $post_data->post_excerpt );
                    $short_description_shown = true;
               }

                if ( ! empty( $description ) ) {
                    $return['description'] = array(
                        'type'       => 'text',
                        'title'      =>empty($title)? __( 'Description', 'woocommerce' ):$title,
                        'expandable' => true,
                        'expanded'   => true,
                        'content'    => get_locale()=='ar'?'<span  style="textAlign: left;">'.apply_filters( 'appmaker_wc_product_description', wpautop( do_shortcode( $description ) ) ).'</span>' : apply_filters( 'appmaker_wc_product_description', wpautop( do_shortcode( $description ) ) ),
                    );
                }
            }
            elseif($key=='accessories'&& $tab_type == 'DEFAULT'){
                $accessories = Electro_WC_Helper::get_accessories( $product );

              /*  array_unshift( $accessories, $current_product_id );*/

                if ( sizeof( $accessories ) === 0 && !array_filter( $accessories ) ) {
                    return;
                }
                $accessory_products = $this->get_products_data( $accessories);
                if ( ! empty( $accessory_products) ) {
                    $return[$key] = array(
                        'type'       => 'product_scroller',
                        'expandable' => false,
                        'expanded'   => true,
                        'title'      => APPMAKER_WC::$api->get_settings( 'product_tab_field_title_accessories'),
                        'products'   => $accessory_products,
                        'default'    => 0,
                        'grocery_mode'=>true,
                    );
                }
            }
            elseif ( $key == 'additional_information'|| $key=='specifications' ||  $key=='specification'&& $tab_type == 'DEFAULT' ) {

                $display_attributes = $this->get_display_attributes( $product );
                $title=APPMAKER_WC::$api->get_settings( 'product_tab_field_title_additional_information');
                if ( ! empty( $display_attributes ) ) {
                    $return['specification'] = array(
                        'type'       => 'key_value',
                        'expandable' => true,
                        'expanded'   => false,
                        'title'      =>empty($title)?  __( 'Specification', 'appmaker-woocommerce-mobile-app-manager' ):$title,
                        'items'      => $display_attributes,
                    );
                }
            }elseif ($key=='related_products' && $tab_type=='DEFAULT'){
                /**
                 * Limit Related Products to 7 at any case.
                 */

                if ( method_exists( $product, 'get_upsell_ids' ) ) {
                    $related_products = $product->get_upsell_ids();
                } else {
                    $related_products = $product->get_upsells();
                }

                if ( empty( $related_products ) ) {
                    if ( function_exists( 'wc_get_related_products' ) ) {
                        $related_products = wc_get_related_products( APPMAKER_WC_Helper::get_id( $product ), 7 );
                    } else {
                        $related_products = $product->get_related( 7 );
                    }
                }

                $related_products = array_slice( $related_products, 0, 7 );
                $related_products = $this->get_products_data( $related_products );
                $title = APPMAKER_WC::$api->get_settings( 'product_tab_field_title_related_products');
                if ( ! empty( $related_products ) ) {
                    $return[$key] = array(
                        'type'       => 'product_scroller',
                        'expandable' => false,
                        'expanded'   => true,
                        'title'      => empty($title)? __( 'Related products', 'woocommerce' ) : $title,
                        'products'   => $related_products,
                    );
                }
            }
            else {
               if ( $key == 'description' ) {                
                    $post_data = get_post( APPMAKER_WC_Helper::get_id( $product ) );
                    $content   = wpautop( do_shortcode( $post_data->post_content ) );
                    if ( empty( $content ) ) {
                        $content = apply_filters( 'woocommerce_short_description', $post_data->post_excerpt );
                    }
                    $content = apply_filters( 'appmaker_wc_product_description', wpautop( do_shortcode( $content ) ) );
                } 
                else if($key == 'short_description'){                   
                    $post_data = get_post( APPMAKER_WC_Helper::get_id( $product ) );
                    $content = apply_filters( 'woocommerce_short_description', $post_data->post_excerpt );
                    $content = apply_filters( 'appmaker_wc_product_description', wpautop( do_shortcode( $content ) ) );
                }
                else {                
                    $content = get_locale()=='ar'?'<span style="textAlign: left;">'. $this->return_data( $tab['callback'], array( $key, $tab ) ).'</span>' :  $this->return_data( $tab['callback'], array( $key, $tab ) );
                }
                $title = APPMAKER_WC::$api->get_settings( 'product_tab_field_title_'.$key);
                $add_header_in_webview = APPMAKER_WC::$api->get_settings( 'add_headers_in_webview', false );
                if ( $tab_type == 'OPEN_IN_WEB_VIEW'  && !$add_header_in_webview )  {

                    $app_only_head = APPMAKER_WC_General_Helper::get_custom_html();
                    $custom_style = base64_decode(APPMAKER_WC::$api->get_settings( 'custom_webview_header', $app_only_head ));
                    $content = "<html><head>".$custom_style."</head><body>".$content."</body></html>";
                    
                    $content = preg_replace ('/=\\"\/\/(.*?)/i', '="https://', $content);                
                    $return[ $key ] = array(
                        'type'       => 'menu',
                        'expandable' => isset( $tab['expandable'] ) ? $tab['expandable'] && true : true,
                        'expanded'   => isset( $tab['expanded'] ) ? $tab['expanded'] && true : false,
                        'title'      => !empty($title)? $title: $this->decode_html( apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ) ),
                        'content'    => $content,
                        'action'     => array(
                            'type'   => 'OPEN_IN_WEB_VIEW',
                            'params' => array(
                                'html'  => $content,
                                'title' => $this->decode_html( apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ) ),
                            ),
                        ),
                    );
                }elseif ( $tab_type == 'OPEN_IN_WEB_VIEW'  && $add_header_in_webview ) {
                    $options            = get_option( 'appmaker_wc_settings' );
                    $api_key            = $options['api_key'];
                    $base_url           = site_url();   
                    $user_id            = get_current_user_id();
                    $access_token       = apply_filters( 'appmaker_wc_set_user_access_token', $user_id );                   
                    $product_id         = APPMAKER_WC_Helper::get_id($product_obj);
                    $url                = $base_url . '/?rest_route=/' . $this->namespace . '/' . $this->rest_base . '/content/' . $key. '&id='. $product_id . '&api_key=' . $api_key .'&access_token='.$access_token.'&user_id='.$user_id ;                   
                   $return[ $key ] = array(
                        'type'       => 'menu',
                        'expandable' => isset( $tab['expandable'] ) ? $tab['expandable'] && true : true,
                        'expanded'   => isset( $tab['expanded'] ) ? $tab['expanded'] && true : false,
                        'title'      => !empty($title)? $title: $this->decode_html( apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ) ),
                        'content'    => $content,
                        'action'     => array(
                            'type'   => 'OPEN_IN_WEB_VIEW',
                            'params' => array(
                                'url'  => $url,
                                'title' => $this->decode_html( apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ) ),
                            ),
                        ),
                    );

                }else {
                    $content  = preg_replace('/<h2[^>]*>([\s\S]*?)<\/h2[^>]*>/'," ",$content);
                    $return[ $key ] = array(
                        'type'       => 'text',
                        'expandable' => isset( $tab['expandable'] ) ? $tab['expandable'] && true : true,
                        'expanded'   => isset( $tab['expanded'] ) ? $tab['expanded'] && true : false,
                        'title'      => !empty($title)? $title: $this->decode_html( apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ) ),
                        'content'    => $content,
                    );
                }
            }
        }


        return apply_filters( 'appmaker_wc_product_widgets', $return, $product_obj, $data );
    }

    /**
     * Add headers to product description
     */

    public function get_content( $request ) {

        global $product,$post,$wp_query;     
        if ( method_exists( 'WPBMap', 'addAllMappedShortcodes' ) ) {
            WPBMap::addAllMappedShortcodes();
        }         
        $product = wc_get_product($request['id']);
        $post    = get_post($request['id']);       
        $GLOBALS['post'] = $post; 
        $wp_query->is_singular = true;
		$wp_query->post  = $post;
        $tab_id = $request['key'];
        $product_tabs    = apply_filters( 'woocommerce_product_tabs', array() );
        $product_tabs    = apply_filters( 'appmaker_wc_product_tabs', $product_tabs);
        $content = '';
        foreach ( $product_tabs as $key => $tab ) {          

            if ( $key == $tab_id) {
                if ( $key == 'description' ) {              
                    
                    $content   = wpautop( do_shortcode( $post->post_content ) );
                    if ( empty( $content ) ) {
                        $content = apply_filters( 'woocommerce_short_description', $post->post_excerpt );
                    }
                    $content = apply_filters( 'appmaker_wc_product_description', wpautop( do_shortcode( $content ) ) );
                } 
                else if($key == 'short_description'){                  
                    
                    $content = apply_filters( 'woocommerce_short_description', $post->post_excerpt );
                    $content = apply_filters( 'appmaker_wc_product_description', wpautop( do_shortcode( $content ) ) );
                }
                else {                
                    $content = get_locale()=='ar'?'<span style="textAlign: left;">'. $this->return_data( $tab['callback'], array( $key, $tab ) ).'</span>' :  $this->return_data( $tab['callback'], array( $key, $tab ) );
                }
            }
        }
        $content = apply_filters('appmaker_product_widget_webview_content', $content );
        $custom_style = APPMAKER_WC_General_Helper::get_custom_html();
        $custom_head = base64_decode(APPMAKER_WC::$api->get_settings( 'custom_webview_header', $custom_style )); 
		if ( empty( $this->head ) ) {
			ob_start();
			wp_head();
			$this->head = apply_filters( 'appmaker_wc_post_head', ob_get_clean() );
		}
        if ( empty( $this->footer ) ) {
			ob_start();
			wp_footer();
			$this->footer = ob_get_clean();
			
        }        
		$output = '<!DOCTYPE html>
            <html>
            <head>
            ' . $this->head . '
            ' . $custom_head . '
            <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
            <style>
           
            </style>        
            </head>         
            <body>            
            <div style="padding: 15px">           
            ' . $content . '           
			</div>
            </body>
            ' . $this->footer . '
            </html>
            ';
            header( 'Content-Type:text/html' );
            echo $output;            
		    exit;
    }
    
    /**
     * @param WC_Product|WP_REST_Request $request
     *
     * @param int $limit
     *
     * @return mixed
     */
    public function get_reviews( $request, $limit = 0 ) {
        $page = 1;
        if ( is_a( $request, 'WP_REST_Request' ) ) {
            $product = wc_get_product( $request['product_id'] );
            $limit   = isset( $request['per_page'] ) ? $request['per_page'] : 10 ;
            if( isset( $request['page'] ) ) {
                $page = $request['page'];
            }
        } else {
            $product = $request;
        }

        if ( ! is_a( $product, 'WC_Product' ) ) {
            return new WP_Error( 'invalid_product', 'Invalid Product' );
        }      

        $args = array(
            'post_id' => APPMAKER_WC_Helper::get_id( $product ),
            'number'  => ! empty ( $limit ) ? $limit : 10 ,
            'orderby' => 'comment_date_gmt ',
            'order'   => 'DESC',
            'status'  => 'approve',
            'paged'   => $page
        );

        $reviews = get_comments( $args );
        if ( $limit != 0 ) {
            $reviews = array_slice( $reviews, 0, $limit );
        }
        $data = array();
        foreach ( $reviews as $review ) {
            $username = $review->comment_author;
            $user = get_user_by('login', $username);    
            $name = isset($user->data->display_name)? $user->data->display_name:$review->comment_author;
            if(is_numeric($name)){
                //$name = str_repeat("*", strlen($name)-4) . substr($name, -4);// to show last 4 digits only
                $name = substr($name, 0, -5) . "*****";
            }
            $data[] = array(
                'id'     => (int) $review->comment_ID,
                //	'date_created' => wc_rest_prepare_date_response( $review->comment_date_gmt ),
                'review' => $review->comment_content,
                'rating' => wc_format_decimal( get_comment_meta( $review->comment_ID, 'rating', true ), 1 ),
                'name'   => $name,
                'avatar' => get_avatar_url( $review->comment_author_email ),
                //	'verified'     => wc_review_is_from_verified_owner( $review->comment_ID )
            );
        }

        return rest_ensure_response( $data );
    }

    /**
     * @param WC_Product|WC_Product_Simple $product
     *
     * @return array
     */
    protected function get_display_attributes( $product ) {
        $attributes = $this->get_attributes( $product, false, true );
        $return     = array();

        if ( $product->enable_dimensions_display() ) {
            $addional_attributes = array();
            if ( $product->has_weight() ) {
                $addional_attributes[] = array(
                    'name'    => __( 'Weight', 'woocommerce' ),
                    'options' => array( array( 'name' => wc_format_localized_decimal( $product->get_weight() ) . ' ' . esc_attr( get_option( 'woocommerce_weight_unit' ) ) ) ),
                );
            }
            if ( $product->has_dimensions() ) {
                $addional_attributes[] = array(
                    'name'    => __( 'Dimensions', 'woocommerce' ),
                    'options' => array( array( 'name' => $product->get_dimensions() ) ),
                );
            }
            $attributes = array_merge( $addional_attributes, $attributes );
        }

        foreach ( $attributes as $attribute ) {
            if ( isset( $attribute['options'] ) && is_array( $attribute['options'] ) ) {
                $value = '';
                foreach ( $attribute['options'] as $option ) {
                    $value .= $option['name'] . ', ';
                }

                $return[] = array(
                    'label' => wc_attribute_label( $attribute['name'] ),
                    'value' => html_entity_decode(trim( $value, " \t\n\r \v," )),
                );
            }
        }

        return $return;
    }


    /**
     * @param $products
     *
     * @return array
     */
    public function get_products_data( $products ) {
        $return = array();
        foreach ( $products as $product_id ) {
            $singleProductData = array();

            if ( APPMAKER_WC::$api->get_settings( 'cache_enabled', false ) ) {            
                $singleProductData = get_transient( 'appmaker_wc_product_list_data_' . $product_id );                
            }

            if ( empty( $singleProductData ) ) {
                $product  = APPMAKER_WC_Helper::get_product( $product_id );
                if ( ! empty( $product ) ) {
                    $singleProductData = $this->get_product_data( $product );
                    if ( APPMAKER_WC::$api->get_settings( 'cache_enabled', false ) ) {
                        $cache_time = APPMAKER_WC::$api->get_settings( 'cache_time', 60 );
                        set_transient( 'appmaker_wc_product_list_data_' . $product_id, $singleProductData, $cache_time * 60 );
                    }   
                }               
            }

            if ( ! empty( $singleProductData ) ) {
                $return[] = $singleProductData;
            }
        }

        return $return;
    }

    /**s
     * Get an individual variation's data.
     *
     * @param WC_Product_Variable $product
     *
     * @return array
     */
    protected function get_variation_data( $product ) {
        $variations = array();
        
        foreach ( $product->get_children() as $child_id ) {
            $variation = $product->get_child( $child_id );
            if ( ! $variation->exists() ) {
                continue;
            }
            $variation_image =$this->get_images( $variation, true, APPMAKER_WC_Helper::get_id( $product ) );
            $sale_percentage = $this->get_sale_percentage( $variation );
            $sale_price = $variation->get_sale_price();
            $price = ((get_option( 'woocommerce_prices_include_tax', 'no' )=='no')&& (get_option( 'woocommerce_tax_display_shop','inc' )=='incl' ))?$variation->get_price_including_tax():$variation->get_price();
            $regular_price = ((get_option( 'woocommerce_prices_include_tax', 'no' )=='no') && (get_option( 'woocommerce_tax_display_shop','inc' )=='incl' ))?wc_get_price_including_tax( $variation, array('price' => $variation->get_regular_price()  ) ):$variation->get_regular_price() ;
            $variation_status = $variation->get_status();
            if($variation_status != 'private') {
                $variations[] = array(
                    'id'                    => $variation->get_variation_id(),
                    'permalink'             => $variation->get_permalink(),
                    'sku'                   => $variation->get_sku(),
                    //  'price'                 => $variation->get_price(),
                'price'                   => $price,
                    // 'regular_price'         => $variation->get_regular_price(),
                'regular_price'           => $regular_price,
                    'sale_price'            => $sale_price,
                    // 'price_display'         => APPMAKER_WC_Helper::get_display_price( $variation->get_price() ),
                    // 'regular_price_display' => APPMAKER_WC_Helper::get_display_price( $variation->get_regular_price() ),
                    'price_display'           => APPMAKER_WC_Helper::get_display_price( $price ),
                'regular_price_display'   => APPMAKER_WC_Helper::get_display_price( $regular_price ),
                    'sale_price_display'    => APPMAKER_WC_Helper::get_display_price( $sale_price ),
                    //	'date_on_sale_from'     => $variation->get_date_on_sale_from() ? date( 'Y-m-d', $variation->get_date_on_sale_from() ) : '',
                    //	'date_on_sale_to'       => $variation->get_date_on_sale_to() ? date( 'Y-m-d', $variation->get_date_on_sale_to() ) : '',
                    'on_sale'               => $variation->is_on_sale(),
                    'downloadable'          => $variation->is_downloadable(),
                    'in_stock'              => $variation->is_in_stock(),
                    'status'                => $variation_status,
                    'sale_percentage'       =>  ($sale_percentage != 0)? $sale_percentage.'%': false,
                    'purchasable'           => $product->is_purchasable(),
                    'dimensions'            => array(
                        'length' => $variation->get_length(),
                        'width'  => $variation->get_width(),
                        'height' => $variation->get_height(),
                    ),
                    'image'                 => $variation_image['images'],
                    'attributes'            => array_values( $this->get_attributes( $variation ) ),
                );
            }
            $images            = $this->get_images( $variation, true, APPMAKER_WC_Helper::get_id( $product ) );
            $data['images']      = $images['images'];

            $data['images_meta'] = $this->get_images_meta( $images['attachment_ids'] );

        }
   /*     $images            = $this->get_images( $variation, true, APPMAKER_WC_Helper::get_id( $product ) );
        $data['images']      = $images['images'];
        $data['images_meta'] = $this->get_images_meta( $images['attachment_ids'] );*/

        return apply_filters( 'appmaker_wc_product_variations', $variations, $product );
    }

    /**
     * @param WC_Product|WC_Product_Variable $product
     *
     * @return float|int
     */
    public function get_sale_percentage( $product ) {
    	$maximumper = 0;
        if ( ! is_a( $product, 'WC_Product_Variable' ) ) {
            $sale_price = $product->get_sale_price() ? $product->get_sale_price() : (( $product->get_price() < $product->get_regular_price() ) ? $product->get_price() : '');
            if( $sale_price ) {
                $maximumper = ( $product->is_on_sale() && 0 != $product->get_regular_price() && ( $product->get_regular_price() > $sale_price ) ) ? round( ( ( $product->get_regular_price() - $sale_price ) / $product->get_regular_price() ) * 100 ) : 0;
            }
        } else {
            $maximumper = apply_filters( 'appmaker_wc_sale_percentage',$maximumper, $product );
            if( $maximumper != 0 && !empty($maximumper)){
               return $maximumper;
            }
            // $available_variations = method_exists( $product, 'get_available_variations' ) ? $product->get_available_variations() : array();
            // if ( is_array( $available_variations ) ) {
            //     for ( $i = 0; $i < count( $available_variations ); ++ $i ) {
            //         $variation_id      = $available_variations[ $i ]['variation_id'];
            //         $variable_product1 = new WC_Product_Variation( $variation_id );
            //         $regular_price     = (float) $variable_product1->get_regular_price();
            //         $sales_price       = (float) $variable_product1->get_sale_price();
            $regular_price = $product->get_variation_regular_price();
            $sales_price = $product->get_variation_price();
                    $percentage        = ( 0 != $regular_price ) ? round( ( ( ( $regular_price - $sales_price ) / $regular_price ) * 100 ), 1 ) : 0;
                    if ( $percentage > $maximumper && ( $percentage > 0 && $percentage < 100 ) ) {
                        $maximumper = $percentage;
                    }
                //}
            //}
        }

        return ( $maximumper <= 0 || $maximumper >= 100 ) ? 0 : $maximumper;
    }

    /**
     * Prepare links for the request.
     *
     * @param WC_Product $product Product object.
     *
     * @return array Links for the given product.
     */
    protected function prepare_links( $product, $request = array() ) {
        $links = array(
            'self'       => array(
                'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, APPMAKER_WC_Helper::get_id( $product ) ) ),
            ),
            'collection' => array(
                'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ),
            ),
        );

        if ( $product->is_type( 'variation' ) && $product->parent ) {
            $links['up'] = array(
                'href' => rest_url( sprintf( '/%s/products/%d', $this->namespace, $product->parent->get_id() ) ),
            );
        } elseif ( $product->is_type( 'simple' ) && ( ( method_exists( $product, 'get_parent_id' ) && $product->get_parent_id() != null ) || ( ! method_exists( $product, 'get_parent_id' ) && $product->get_parent() != false ) ) ) {
            $parent      = method_exists( $product, 'get_parent_id' ) ? $product->get_parent_id() : $product->get_parent();
            $links['up'] = array(
                'href' => rest_url( sprintf( '/%s/products/%d', $this->namespace, $parent ) ),
            );
        }

        return $links;
    }

    /**
     * Clear cache/transients.
     *
     * @param WP_Post $post Post data.
     */
    public function clear_transients( $post ) {
        wc_delete_product_transients( $post->ID );
    }

    /**
     * Get the Product's schema, conforming to JSON Schema.
     *
     * @return array
     */
    public function get_item_schema() {
        $weight_unit    = get_option( 'woocommerce_weight_unit' );
        $dimension_unit = get_option( 'woocommerce_dimension_unit' );
        $schema         = array(
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => $this->post_type,
            'type'       => 'object',
            'properties' => array(
                'id'                 => array(
                    'description' => __( 'Unique identifier for the resource.', 'woocommerce' ),
                    'type'        => 'integer',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'name'               => array(
                    'description' => __( 'Product name.', 'woocommerce' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
                'slug'               => array(
                    'description' => __( 'Product slug.', 'woocommerce' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
                'permalink'          => array(
                    'description' => __( 'Product URL.', 'woocommerce' ),
                    'type'        => 'string',
                    'format'      => 'uri',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'type'               => array(
                    'description' => __( 'Product type.', 'woocommerce' ),
                    'type'        => 'string',
                    'default'     => 'simple',
                    'enum'        => array_keys( wc_get_product_types() ),
                    'context'     => array( 'view', 'edit' ),
                ),
                'featured'           => array(
                    'description' => __( 'Featured product.', 'woocommerce' ),
                    'type'        => 'boolean',
                    'default'     => false,
                    'context'     => array( 'view', 'edit' ),
                ),
                'description'        => array(
                    'description' => __( 'Product description.', 'woocommerce' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
                'short_description'  => array(
                    'description' => __( 'Product short description.', 'woocommerce' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
                'sku'                => array(
                    'description' => __( 'Unique identifier.', 'woocommerce' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
                'price'              => array(
                    'description' => __( 'Current product price.', 'woocommerce' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'regular_price'      => array(
                    'description' => __( 'Product regular price.', 'woocommerce' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
                'sale_price'         => array(
                    'description' => __( 'Product sale price.', 'woocommerce' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
                'price_html'         => array(
                    'description' => __( 'Price formatted in HTML.', 'woocommerce' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'on_sale'            => array(
                    'description' => __( 'Shows if the product is on sale.', 'woocommerce' ),
                    'type'        => 'boolean',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'purchasable'        => array(
                    'description' => __( 'Shows if the product can be bought.', 'woocommerce' ),
                    'type'        => 'boolean',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'downloadable'       => array(
                    'description' => __( 'If the product is downloadable.', 'woocommerce' ),
                    'type'        => 'boolean',
                    'default'     => false,
                    'context'     => array( 'view', 'edit' ),
                ),
                'external_url'       => array(
                    'description' => __( 'Product external URL. Only for external products.', 'woocommerce' ),
                    'type'        => 'string',
                    'format'      => 'uri',
                    'context'     => array( 'view', 'edit' ),
                ),
                'button_text'        => array(
                    'description' => __( 'Product external button text. Only for external products.', 'woocommerce' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
                'tax_status'         => array(
                    'description' => __( 'Tax status.', 'woocommerce' ),
                    'type'        => 'string',
                    'default'     => 'taxable',
                    'enum'        => array( 'taxable', 'shipping', 'none' ),
                    'context'     => array( 'view', 'edit' ),
                ),
                'tax_class'          => array(
                    'description' => __( 'Tax class.', 'woocommerce' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
                'manage_stock'       => array(
                    'description' => __( 'Stock management at product level.', 'woocommerce' ),
                    'type'        => 'boolean',
                    'default'     => false,
                    'context'     => array( 'view', 'edit' ),
                ),
                'stock_quantity'     => array(
                    'description' => __( 'Stock quantity.', 'woocommerce' ),
                    'type'        => 'integer',
                    'context'     => array( 'view', 'edit' ),
                ),
                'in_stock'           => array(
                    'description' => __( 'Controls whether or not the product is listed as "in stock" or "out of stock" on the frontend.', 'woocommerce' ),
                    'type'        => 'boolean',
                    'default'     => true,
                    'context'     => array( 'view', 'edit' ),
                ),
                'backorders'         => array(
                    'description' => __( 'If managing stock, this controls if backorders are allowed.', 'woocommerce' ),
                    'type'        => 'string',
                    'default'     => 'no',
                    'enum'        => array( 'no', 'notify', 'yes' ),
                    'context'     => array( 'view', 'edit' ),
                ),
                'backorders_allowed' => array(
                    'description' => __( 'Shows if backorders are allowed.', 'woocommerce' ),
                    'type'        => 'boolean',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'backordered'        => array(
                    'description' => __( 'Shows if the product is on backordered.', 'woocommerce' ),
                    'type'        => 'boolean',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'sold_individually'  => array(
                    'description' => __( 'Allow one item to be bought in a single order.', 'woocommerce' ),
                    'type'        => 'boolean',
                    'default'     => false,
                    'context'     => array( 'view', 'edit' ),
                ),
                'weight'             => array(
                    'description' => sprintf( __( 'Product weight (%s).', 'woocommerce' ), $weight_unit ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
                'dimensions'         => array(
                    'description' => __( 'Product dimensions.', 'woocommerce' ),
                    'type'        => 'array',
                    'context'     => array( 'view', 'edit' ),
                    'properties'  => array(
                        'length' => array(
                            'description' => sprintf( __( 'Product length (%s).', 'woocommerce' ), $dimension_unit ),
                            'type'        => 'string',
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'width'  => array(
                            'description' => sprintf( __( 'Product width (%s).', 'woocommerce' ), $dimension_unit ),
                            'type'        => 'string',
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'height' => array(
                            'description' => sprintf( __( 'Product height (%s).', 'woocommerce' ), $dimension_unit ),
                            'type'        => 'string',
                            'context'     => array( 'view', 'edit' ),
                        ),
                    ),
                ),
                'shipping_required'  => array(
                    'description' => __( 'Shows if the product need to be shipped.', 'woocommerce' ),
                    'type'        => 'boolean',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'shipping_taxable'   => array(
                    'description' => __( 'Shows whether or not the product shipping is taxable.', 'woocommerce' ),
                    'type'        => 'boolean',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'shipping_class'     => array(
                    'description' => __( 'Shipping class slug.', 'woocommerce' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
                'shipping_class_id'  => array(
                    'description' => __( 'Shipping class ID.', 'woocommerce' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'reviews_allowed'    => array(
                    'description' => __( 'Allow reviews.', 'woocommerce' ),
                    'type'        => 'boolean',
                    'default'     => true,
                    'context'     => array( 'view', 'edit' ),
                ),
                'average_rating'     => array(
                    'description' => __( 'Reviews average rating.', 'woocommerce' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'rating_count'       => array(
                    'description' => __( 'Amount of reviews that the product have.', 'woocommerce' ),
                    'type'        => 'integer',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'parent_id'          => array(
                    'description' => __( 'Product parent ID.', 'woocommerce' ),
                    'type'        => 'integer',
                    'context'     => array( 'view', 'edit' ),
                ),
                'purchase_note'      => array(
                    'description' => __( 'Optional note to send the customer after purchase.', 'woocommerce' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
                'categories'         => array(
                    'description' => __( 'List of categories.', 'woocommerce' ),
                    'type'        => 'array',
                    'context'     => array( 'view', 'edit' ),
                    'properties'  => array(
                        'id'   => array(
                            'description' => __( 'Category ID.', 'woocommerce' ),
                            'type'        => 'integer',
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'name' => array(
                            'description' => __( 'Category name.', 'woocommerce' ),
                            'type'        => 'string',
                            'context'     => array( 'view', 'edit' ),
                            'readonly'    => true,
                        ),
                        'slug' => array(
                            'description' => __( 'Category slug.', 'woocommerce' ),
                            'type'        => 'string',
                            'context'     => array( 'view', 'edit' ),
                            'readonly'    => true,
                        ),
                    ),
                ),
                'tags'               => array(
                    'description' => __( 'List of tags.', 'woocommerce' ),
                    'type'        => 'array',
                    'context'     => array( 'view', 'edit' ),
                    'properties'  => array(
                        'id'   => array(
                            'description' => __( 'Tag ID.', 'woocommerce' ),
                            'type'        => 'integer',
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'name' => array(
                            'description' => __( 'Tag name.', 'woocommerce' ),
                            'type'        => 'string',
                            'context'     => array( 'view', 'edit' ),
                            'readonly'    => true,
                        ),
                        'slug' => array(
                            'description' => __( 'Tag slug.', 'woocommerce' ),
                            'type'        => 'string',
                            'context'     => array( 'view', 'edit' ),
                            'readonly'    => true,
                        ),
                    ),
                ),
                'images'             => array(
                    'description' => __( 'List of images.', 'woocommerce' ),
                    'type'        => 'array',
                    'context'     => array( 'view', 'edit' ),
                    'properties'  => array(
                        'id'            => array(
                            'description' => __( 'Image ID.', 'woocommerce' ),
                            'type'        => 'integer',
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'date_created'  => array(
                            'description' => __( "The date the image was created, in the site's timezone.", 'woocommerce' ),
                            'type'        => 'date-time',
                            'context'     => array( 'view', 'edit' ),
                            'readonly'    => true,
                        ),
                        'date_modified' => array(
                            'description' => __( "The date the image was last modified, in the site's timezone.", 'woocommerce' ),
                            'type'        => 'date-time',
                            'context'     => array( 'view', 'edit' ),
                            'readonly'    => true,
                        ),
                        'src'           => array(
                            'description' => __( 'Image URL.', 'woocommerce' ),
                            'type'        => 'string',
                            'format'      => 'uri',
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'name'          => array(
                            'description' => __( 'Image name.', 'woocommerce' ),
                            'type'        => 'string',
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'alt'           => array(
                            'description' => __( 'Image alternative text.', 'woocommerce' ),
                            'type'        => 'string',
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'position'      => array(
                            'description' => __( 'Image position. 0 means that the image is featured.', 'woocommerce' ),
                            'type'        => 'integer',
                            'context'     => array( 'view', 'edit' ),
                        ),
                    ),
                ),
                'attributes'         => array(
                    'description' => __( 'List of attributes.', 'woocommerce' ),
                    'type'        => 'array',
                    'context'     => array( 'view', 'edit' ),
                    'properties'  => array(
                        'id'        => array(
                            'description' => __( 'Attribute ID.', 'woocommerce' ),
                            'type'        => 'integer',
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'name'      => array(
                            'description' => __( 'Attribute name.', 'woocommerce' ),
                            'type'        => 'string',
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'position'  => array(
                            'description' => __( 'Attribute position.', 'woocommerce' ),
                            'type'        => 'integer',
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'visible'   => array(
                            'description' => __( "Define if the attribute is visible on the \"Additional Information\" tab in the product's page.", 'woocommerce' ),
                            'type'        => 'boolean',
                            'default'     => false,
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'variation' => array(
                            'description' => __( 'Define if the attribute can be used as variation.', 'woocommerce' ),
                            'type'        => 'boolean',
                            'default'     => false,
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'options'   => array(
                            'description' => __( 'List of available term names of the attribute.', 'woocommerce' ),
                            'type'        => 'array',
                            'context'     => array( 'view', 'edit' ),
                        ),
                    ),
                ),
                'default_attributes' => array(
                    'description' => __( 'Defaults variation attributes.', 'woocommerce' ),
                    'type'        => 'array',
                    'context'     => array( 'view', 'edit' ),
                    'properties'  => array(
                        'id'     => array(
                            'description' => __( 'Attribute ID.', 'woocommerce' ),
                            'type'        => 'integer',
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'name'   => array(
                            'description' => __( 'Attribute name.', 'woocommerce' ),
                            'type'        => 'string',
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'option' => array(
                            'description' => __( 'Selected attribute term name.', 'woocommerce' ),
                            'type'        => 'string',
                            'context'     => array( 'view', 'edit' ),
                        ),
                    ),
                ),
                'variations'         => array(
                    'description' => __( 'List of variations.', 'woocommerce' ),
                    'type'        => 'array',
                    'context'     => array( 'view', 'edit' ),
                    'properties'  => array(
                        'id'                 => array(
                            'description' => __( 'Variation ID.', 'woocommerce' ),
                            'type'        => 'integer',
                            'context'     => array( 'view', 'edit' ),
                            'readonly'    => true,
                        ),
                        'date_created'       => array(
                            'description' => __( "The date the variation was created, in the site's timezone.", 'woocommerce' ),
                            'type'        => 'date-time',
                            'context'     => array( 'view', 'edit' ),
                            'readonly'    => true,
                        ),
                        'date_modified'      => array(
                            'description' => __( "The date the variation was last modified, in the site's timezone.", 'woocommerce' ),
                            'type'        => 'date-time',
                            'context'     => array( 'view', 'edit' ),
                            'readonly'    => true,
                        ),
                        'permalink'          => array(
                            'description' => __( 'Variation URL.', 'woocommerce' ),
                            'type'        => 'string',
                            'format'      => 'uri',
                            'context'     => array( 'view', 'edit' ),
                            'readonly'    => true,
                        ),
                        'sku'                => array(
                            'description' => __( 'Unique identifier.', 'woocommerce' ),
                            'type'        => 'string',
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'price'              => array(
                            'description' => __( 'Current variation price.', 'woocommerce' ),
                            'type'        => 'string',
                            'context'     => array( 'view', 'edit' ),
                            'readonly'    => true,
                        ),
                        'regular_price'      => array(
                            'description' => __( 'Variation regular price.', 'woocommerce' ),
                            'type'        => 'string',
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'sale_price'         => array(
                            'description' => __( 'Variation sale price.', 'woocommerce' ),
                            'type'        => 'string',
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'date_on_sale_from'  => array(
                            'description' => __( 'Start date of sale price.', 'woocommerce' ),
                            'type'        => 'string',
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'date_on_sale_to'    => array(
                            'description' => __( 'End data of sale price.', 'woocommerce' ),
                            'type'        => 'string',
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'on_sale'            => array(
                            'description' => __( 'Shows if the variation is on sale.', 'woocommerce' ),
                            'type'        => 'boolean',
                            'context'     => array( 'view', 'edit' ),
                            'readonly'    => true,
                        ),
                        'purchasable'        => array(
                            'description' => __( 'Shows if the variation can be bought.', 'woocommerce' ),
                            'type'        => 'boolean',
                            'context'     => array( 'view', 'edit' ),
                            'readonly'    => true,
                        ),
                        'visible'            => array(
                            'description' => __( 'If the variation is visible.', 'woocommerce' ),
                            'type'        => 'boolean',
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'virtual'            => array(
                            'description' => __( 'If the variation is virtual.', 'woocommerce' ),
                            'type'        => 'boolean',
                            'default'     => false,
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'downloadable'       => array(
                            'description' => __( 'If the variation is downloadable.', 'woocommerce' ),
                            'type'        => 'boolean',
                            'default'     => false,
                            'context'     => array( 'view', 'edit' ),
                        ),
                        'downloads'          => array(
                            'description' => __( 'List of downloadable files.', 'woocommerce' ),
                            'type'        => 'array',
                            'context'     => array( 'view', 'edit' ),
                            'properties'  => array(
                                'id'   => array(
                                    'description' => __( 'File MD5 hash.', 'woocommerce' ),
                                    'type'        => 'string',
                                    'context'     => array( 'view', 'edit' ),
                                    'readonly'    => true,
                                ),
                                'name' => array(
                                    'description' => __( 'File name.', 'woocommerce' ),
                                    'type'        => 'string',
                                    'context'     => array( 'view', 'edit' ),
                                ),
                                'file' => array(
                                    'description' => __( 'File URL.', 'woocommerce' ),
                                    'type'        => 'string',
                                    'context'     => array( 'view', 'edit' ),
                                ),
                            ),
                            'download_limit'     => array(
                                'description' => __( 'Amount of times the variation can be downloaded.', 'woocommerce' ),
                                'type'        => 'integer',
                                'default'     => null,
                                'context'     => array( 'view', 'edit' ),
                            ),
                            'download_expiry'    => array(
                                'description' => __( 'Number of days that the customer has up to be able to download the variation.', 'woocommerce' ),
                                'type'        => 'integer',
                                'default'     => null,
                                'context'     => array( 'view', 'edit' ),
                            ),
                            'tax_status'         => array(
                                'description' => __( 'Tax status.', 'woocommerce' ),
                                'type'        => 'string',
                                'default'     => 'taxable',
                                'enum'        => array( 'taxable', 'shipping', 'none' ),
                                'context'     => array( 'view', 'edit' ),
                            ),
                            'tax_class'          => array(
                                'description' => __( 'Tax class.', 'woocommerce' ),
                                'type'        => 'string',
                                'context'     => array( 'view', 'edit' ),
                            ),
                            'manage_stock'       => array(
                                'description' => __( 'Stock management at variation level.', 'woocommerce' ),
                                'type'        => 'boolean',
                                'default'     => false,
                                'context'     => array( 'view', 'edit' ),
                            ),
                            'stock_quantity'     => array(
                                'description' => __( 'Stock quantity.', 'woocommerce' ),
                                'type'        => 'integer',
                                'context'     => array( 'view', 'edit' ),
                            ),
                            'in_stock'           => array(
                                'description' => __( 'Controls whether or not the variation is listed as "in stock" or "out of stock" on the frontend.', 'woocommerce' ),
                                'type'        => 'boolean',
                                'default'     => true,
                                'context'     => array( 'view', 'edit' ),
                            ),
                            'backorders'         => array(
                                'description' => __( 'If managing stock, this controls if backorders are allowed.', 'woocommerce' ),
                                'type'        => 'string',
                                'default'     => 'no',
                                'enum'        => array( 'no', 'notify', 'yes' ),
                                'context'     => array( 'view', 'edit' ),
                            ),
                            'backorders_allowed' => array(
                                'description' => __( 'Shows if backorders are allowed.', 'woocommerce' ),
                                'type'        => 'boolean',
                                'context'     => array( 'view', 'edit' ),
                                'readonly'    => true,
                            ),
                            'backordered'        => array(
                                'description' => __( 'Shows if the variation is on backordered.', 'woocommerce' ),
                                'type'        => 'boolean',
                                'context'     => array( 'view', 'edit' ),
                                'readonly'    => true,
                            ),
                            'weight'             => array(
                                'description' => sprintf( __( 'Variation weight (%s).', 'woocommerce' ), $weight_unit ),
                                'type'        => 'string',
                                'context'     => array( 'view', 'edit' ),
                            ),
                            'dimensions'         => array(
                                'description' => __( 'Variation dimensions.', 'woocommerce' ),
                                'type'        => 'array',
                                'context'     => array( 'view', 'edit' ),
                                'properties'  => array(
                                    'length' => array(
                                        'description' => sprintf( __( 'Variation length (%s).', 'woocommerce' ), $dimension_unit ),
                                        'type'        => 'string',
                                        'context'     => array( 'view', 'edit' ),
                                    ),
                                    'width'  => array(
                                        'description' => sprintf( __( 'Variation width (%s).', 'woocommerce' ), $dimension_unit ),
                                        'type'        => 'string',
                                        'context'     => array( 'view', 'edit' ),
                                    ),
                                    'height' => array(
                                        'description' => sprintf( __( 'Variation height (%s).', 'woocommerce' ), $dimension_unit ),
                                        'type'        => 'string',
                                        'context'     => array( 'view', 'edit' ),
                                    ),
                                ),
                            ),
                            'shipping_class'     => array(
                                'description' => __( 'Shipping class slug.', 'woocommerce' ),
                                'type'        => 'string',
                                'context'     => array( 'view', 'edit' ),
                            ),
                            'shipping_class_id'  => array(
                                'description' => __( 'Shipping class ID.', 'woocommerce' ),
                                'type'        => 'string',
                                'context'     => array( 'view', 'edit' ),
                                'readonly'    => true,
                            ),
                            'image'              => array(
                                'description' => __( 'Variation image data.', 'woocommerce' ),
                                'type'        => 'array',
                                'context'     => array( 'view', 'edit' ),
                                'properties'  => array(
                                    'id'            => array(
                                        'description' => __( 'Image ID.', 'woocommerce' ),
                                        'type'        => 'integer',
                                        'context'     => array( 'view', 'edit' ),
                                    ),
                                    'date_created'  => array(
                                        'description' => __( "The date the image was created, in the site's timezone.", 'woocommerce' ),
                                        'type'        => 'date-time',
                                        'context'     => array( 'view', 'edit' ),
                                        'readonly'    => true,
                                    ),
                                    'date_modified' => array(
                                        'description' => __( "The date the image was last modified, in the site's timezone.", 'woocommerce' ),
                                        'type'        => 'date-time',
                                        'context'     => array( 'view', 'edit' ),
                                        'readonly'    => true,
                                    ),
                                    'src'           => array(
                                        'description' => __( 'Image URL.', 'woocommerce' ),
                                        'type'        => 'string',
                                        'format'      => 'uri',
                                        'context'     => array( 'view', 'edit' ),
                                    ),
                                    'name'          => array(
                                        'description' => __( 'Image name.', 'woocommerce' ),
                                        'type'        => 'string',
                                        'context'     => array( 'view', 'edit' ),
                                    ),
                                    'alt'           => array(
                                        'description' => __( 'Image alternative text.', 'woocommerce' ),
                                        'type'        => 'string',
                                        'context'     => array( 'view', 'edit' ),
                                    ),
                                    'position'      => array(
                                        'description' => __( 'Image position. 0 means that the image is featured.', 'woocommerce' ),
                                        'type'        => 'integer',
                                        'context'     => array( 'view', 'edit' ),
                                    ),
                                ),
                            ),
                            'attributes'         => array(
                                'description' => __( 'List of attributes.', 'woocommerce' ),
                                'type'        => 'array',
                                'context'     => array( 'view', 'edit' ),
                                'properties'  => array(
                                    'id'     => array(
                                        'description' => __( 'Attribute ID.', 'woocommerce' ),
                                        'type'        => 'integer',
                                        'context'     => array( 'view', 'edit' ),
                                    ),
                                    'name'   => array(
                                        'description' => __( 'Attribute name.', 'woocommerce' ),
                                        'type'        => 'string',
                                        'context'     => array( 'view', 'edit' ),
                                    ),
                                    'option' => array(
                                        'description' => __( 'Selected attribute term name.', 'woocommerce' ),
                                        'type'        => 'string',
                                        'context'     => array( 'view', 'edit' ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'grouped_products'   => array(
                        'description' => __( 'List of grouped products ID.', 'woocommerce' ),
                        'type'        => 'array',
                        'context'     => array( 'view', 'edit' ),
                        'readonly'    => true,
                    ),
                    'menu_order'         => array(
                        'description' => __( 'Menu order, used to custom sort products.', 'woocommerce' ),
                        'type'        => 'integer',
                        'context'     => array( 'view', 'edit' ),
                    ),
                ),
            ),
        );

        return $schema; //$this->add_additional_fields_schema( $schema );
    }

    /**
     * Get post types.
     *
     * @return array
     */
    protected function get_post_types() {
        return array( 'product', 'product_variation' );
    }

    /**
     * Get the downloads for a product or product variation.
     *
     * @param WC_Product|WC_Product_Variation $product
     *
     * @return array
     */
    protected function get_downloads( $product ) {
        $downloads = array();

        if ( $product->is_downloadable() ) {
            foreach ( $product->get_files() as $file_id => $file ) {
                $downloads[] = array(
                    'id'   => $file_id, // MD5 hash.
                    'name' => $file['name'],
                    'file' => $file['file'],
                );
            }
        }

        return $downloads;
    }

    /**
     * Get product menu order.
     *
     * @param WC_Product $product
     *
     * @return int
     */
    protected function get_product_menu_order( $product ) {
        $menu_order = get_post( APPMAKER_WC_Helper::get_id( $product ) )->menu_order;

        if ( $product->is_type( 'variation' ) ) {
            $variation  = get_post( APPMAKER_WC_Helper::get_id( $product ) );
            $menu_order = $variation->menu_order;
        }

        return $menu_order;
    }
       
    /**
     * Remove user id and access token from request params to check cache
     **/            
    protected function get_request_hash( $request ){
        $cache_params = $request->get_params();
        if ( isset( $cache_params['access_token'] ) ) {
            unset( $cache_params['access_token'] );
        }
        if ( isset( $cache_params['user_id'] ) ) {
            unset( $cache_params['user_id'] );
        }
        if ( isset( $cache_params['cache_rand'] ) ) {
            unset( $cache_params['cache_rand'] );
        }
        return md5(json_encode($cache_params));
    }

    /**
     * Get a collection of posts.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_items( $request ) {

        global $wp, $wp_query;
        $this->send_cache_header();
        $cacheEnabled = APPMAKER_WC::$api->get_settings( 'cache_enabled', false ) &&  ( ! isset( $request['search'] ) || empty( $request['search'] ) );
        if ( $cacheEnabled ) {            
            $hash = $this->get_request_hash( $request );
            $response = get_transient( 'appmaker_wc_products_list_response_' . $hash );
            if ( !empty( $response ) ) {                                
                return $response;
            }
        }
        
        $args                        = array();
        $args['offset']              = $request['offset'];
        $args['paged']               = $request['page'];
        $args['post__in']            = $request['include'];
        $args['post__not_in']        = $request['exclude'];
        $args['posts_per_page']      = isset( $request['per_page'] ) ? $request['per_page'] : 10;
        $args['name']                = $request['slug'];
        $args['post_parent__in']     = $request['parent'];
        $args['post_parent__not_in'] = $request['parent_exclude'];
        $args['s']                   = $_REQUEST['s'] = $request['search'];

        if ( isset( $request['search'] ) && ! empty( $request['search'] ) ) {
            $wp->query_vars['s'] = $request['search'];
            $wp->query_vars['post_type'] = 'product';
            $wp_query->is_search = true;
        }
       
        if( empty( $request['orderby'] ) ){
            $request['orderby'] =  apply_filters('appmaker_wc_set_sort', $request['orderby'] );
        }
        $sort_args = WC()->query->get_catalog_ordering_args( $request['orderby'], $request['order'] );
        if ( is_array( $sort_args ) ) {
            $args = array_merge( $args, $sort_args );
        }
        $args['date_query'] = array();
        // Set before into date query. Date query must be specified as an array of an array.
        if ( isset( $request['before'] ) ) {
            $args['date_query'][0]['before'] = $request['before'];
        }

        // Set after into date query. Date query must be specified as an array of an array.
        if ( isset( $request['after'] ) ) {
            $args['date_query'][0]['after'] = $request['after'];
        }

        if ( is_array( $request['filter'] ) ) {
            $args = array_merge( $args, $request['filter'] );
            unset( $args['filter'] );
        }

        // Force the post_type argument, since it's not a user input variable.
        $args['post_type'] = $this->post_type;



        $args['post_status'] = 'publish';

        $args['suppress_filters'] = true;

        /**
         * Filter the query arguments for a request.
         *
         * Enables adding extra arguments or setting defaults for a post
         * collection request.
         *
         * @param array $args Key value array of query var to query value.
         * @param WP_REST_Request $request The request used.
         */

        // relevanssi plugin fix
        remove_filter( 'the_posts', 'relevanssi_query' );
        remove_filter( 'posts_request', 'relevanssi_prevent_default_request', 10 );
        remove_filter( 'query_vars', 'relevanssi_query_vars' );

        $args         = apply_filters( "appmaker_wc_rest_{$this->post_type}_query", $args, $request );
        $query_args   = $this->prepare_items_query( $args, $request );

        //change post_status to publish if it has value any
        if( isset($query_args['post_status']) && 'any' == $query_args['post_status'] ){
            $query_args['post_status'] = 'publish';
        }
        
        // $posts_query  = new WP_Query();
        if(class_exists('WC_Geolocation_Based_Products_Frontend')) {
            $geo_location = WC_Geolocation_Based_Products_Frontend::get_instance();
            global $wp_the_query;
            $posts_query  = $wp_the_query;
            $posts_query->parse_query( $query_args );           
            if( empty(  $posts_query->parse_query( $query_args ) ) ){
                $posts_query  =  new WP_Query( $query_args );
            }
            $geo_location->filter_query($posts_query);         
            $query_result = $posts_query->get_posts();            
        }else {
            $query_result = query_posts($query_args);
        }
        $query_result = apply_filters('appmaker_wc_product_query_result',$query_result,$query_args);
        if(empty($query_result)){
            $query_result=get_posts($query_args);
        }

        //wpml search filter by language
        if ( class_exists( 'SitePress' ) ) {
            foreach ($query_result as $id => $post) {

                global $wpml_post_translations, $sitepress;
                if ($wpml_post_translations->get_element_lang_code($post->ID) != $sitepress->get_current_language()) {
                    unset($query_result[$id]);
                }
            }
        }

        $posts        = array();
        foreach ( $query_result as $post ) {
            // if ( ! current_user_can( 'read_post', $post->ID ) ) {
            // 	continue;
            // }

            $data    = $this->prepare_item_for_response( $post, $request );
            if ( ! empty( $data ) ) {
                $posts[] = $this->prepare_response_for_collection( $data );
            }
        }

        $page        = (int) $query_args['paged'];
        $total_posts = $GLOBALS['wp_query']->found_posts;

        if ( $total_posts < 1 ) {
            // Out-of-bounds, run the query again without LIMIT for total count
            unset( $query_args['paged'] );
            $count_query = new WP_Query();
            $count_query->query( $query_args );
            $total_posts = $count_query->found_posts;
        }

        $max_pages = ceil( $total_posts / (int) $query_args['posts_per_page'] );

        $response = rest_ensure_response( $posts );
        $response->header( 'X-WP-Total', (int) $total_posts );
        $response->header( 'X-WP-TotalPages', (int) $max_pages );

        $request_params = $request->get_query_params();
        if ( ! empty( $request_params['filter'] ) ) {
            // Normalize the pagination params.
            unset( $request_params['filter']['posts_per_page'] );
            unset( $request_params['filter']['paged'] );
        }
        $base = add_query_arg( $request_params, rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ) );

        if ( $page > 1 ) {
            $prev_page = $page - 1;
            if ( $prev_page > $max_pages ) {
                $prev_page = $max_pages;
            }
            $prev_link = add_query_arg( 'page', $prev_page, $base );
            $response->link_header( 'prev', $prev_link );
        }
        if ( $max_pages > $page ) {
            $next_page = $page + 1;
            $next_link = add_query_arg( 'page', $next_page, $base );
            $response->link_header( 'next', $next_link );
        }

        if ( $cacheEnabled ) {
            $cache_time = APPMAKER_WC::$api->get_settings( 'cache_time', 60 );
            if ( ! isset( $hash ) ) {
                $hash = $this->get_request_hash( $request );
            }
            set_transient( 'appmaker_wc_products_list_response_' . $hash, $response, $cache_time * 60 );
        }

        return $response;
    }

    /**
     * Get a single item.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_item( $request ) {
        $this->send_cache_header();
        if ( APPMAKER_WC::$api->get_settings( 'cache_enabled', false ) ) {
            $hash = $this->get_request_hash( $request );
            $response = get_transient( 'appmaker_wc_products_response_' . $hash );
            if ( false !== $response ) {
                return $response;
            }
        }

        $response = parent::get_item( $request );

        if ( APPMAKER_WC::$api->get_settings( 'cache_enabled', false ) ) {
            if ( ! isset( $hash ) ) {
                $hash = $this->get_request_hash( $request );
            }
            set_transient( 'appmaker_wc_products_response_' . $hash, $response, APPMAKER_WC::$api->get_settings( 'cache_time', 60 ) * 60 );
        }

        return $response;
    }


    /**
     * Get a single item from slug.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_products_from_slug( $request ) {
        $slug = $request['slug'];
        global $wpdb;
        $output = OBJECT;
        $product = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type= %s", $slug, 'product' ) );
        if ( $product ) {
            $id = (int) $product;
            $post = get_post( $id );
            $request['id'] = $id;

            if ( empty( $id ) || empty( $post->ID ) || ! in_array( $post->post_type, $this->get_post_types() ) ) {
                return new WP_Error( "woocommerce_rest_invalid_{$this->post_type}_id", __( 'Invalid id.', 'woocommerce' ), array( 'status' => 404 ) );
            }
            $data     = $this->prepare_item_for_response( $post, $request );
            return $data;
        }

        return new WP_Error( "woocommerce_rest_invalid_{$this->post_type}_id", __( 'Invalid slug.', 'woocommerce' ), array( 'status' => 404 ) );

    }

    /**
     * Get item from url for Deep Linking.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_Error|WP_REST_Response
     */

    public function get_item_from_url( $request ) {
        $url = $request['url'];
        $url=strtok($url, '?');
        $response['action']['type'] = 'OPEN_HOME';
        //if product
        $product_id = url_to_postid( $url);
        if( $product_id == 0 ) {       
            preg_match('/([^\/]+)[\/]?$/i', $url, $matches );			
			$slug  = $matches[1];	
            $page = get_page_by_path("$slug", OBJECT, 'product');
            if( $page && is_object( $page ) ) {
                $product_id = $page->ID;
            }            
        }
        $request['id'] = $product_id;
        $product_item=$this->get_item($request);
        if($product_id ==0 || is_wp_error($product_item)){
            $front_page_id = get_option('page_on_front');
            if( $product_id == 0 ) {
				preg_match('/([^\/]+)[\/]?$/i', $url, $matches );			
				$slug  = $matches[1];	
				$page = get_page_by_path("$slug", OBJECT, 'page');
                if( empty ($page) ) {
                    $page = get_page_by_path("$slug", OBJECT, 'post');					
                }				
				if( $page && is_object( $page ) ) {
					$product_id = $page->ID;
				}            
             }
            if( $product_id != 0 && $product_id != $front_page_id ) {
                $base_url = site_url();
				$options  = get_option( 'appmaker_wc_settings' );
				$api_key  = $options['api_key'];
                $parse_url = parse_url($request['url']);
				$post_url      = $base_url . '/?rest_route=/appmaker-wc/v1/' . 'posts' . '/' . $product_id . '/content' . '&api_key=' . $api_key;
                if (strpos($request['url'], 'lost-password') == true) { 
                    $post_url = $request['url'];
                }
                if(isset($parse_url['query'])){
                    $post_url = $post_url.'&'.$parse_url['query'];
                }
				$post_url      = add_query_arg( array('from_app' => true , 'key' => true), $post_url );				
                $response['action']['type'] = 'OPEN_IN_WEB_VIEW';
                $response['action']['params'] =  array( 'url' => $post_url ) ;
            }else 
               $response['action']['type'] = 'OPEN_HOME';
        }
        else {
            $response['action']['type'] = 'OPEN_PRODUCT';
            $response['action']['params'] = $this->get_item($request);
            return $response;
        }
        $slug_cat=basename($url);
        $cat = get_term_by( 'slug',$slug_cat,'product_cat','ARRAY_A' );
        $cat_id = $cat['term_id'];
        $request['category'] = $cat_id;
        $category_name = '';
		if( $cat_id ){
			$category_name = get_the_category_by_ID( $cat_id );
		}
        if ( ! empty( $cat_id ) ) {
            $response['action']['type'] = 'LIST_PRODUCT';
            $response['action']['params'] = array(
                'category' => $cat_id,
                'title'    => !empty($category_name)? html_entity_decode($category_name):$category_name,
                
            );
            return $response;
        }
        global $woocommerce;
        $cart_url = $woocommerce->cart->get_cart_url();
        $url_basename = basename( $url );
        $cart_basename = basename( $cart_url );
        if ( strpos( $cart_basename,$url_basename ) !== false ) {
            $product_controller = APPMAKER_WC::$api->APPMAKER_WC_REST_Cart_Controller;
            $response['action']['type'] = 'OPEN_CART';
            $response['action']['params'] = $product_controller->get_cart_items();
            return $response;
        } else {
            return $response;
        }

    }

    // add comment to a product

    public function add_comment( $request ) {
       $user_id =  get_current_user_id();
       $user_name =  get_user_meta( $user_id, 'nickname', true );
       $user_email = get_user_meta($user_id,'billing_email',true);

       if(empty($request['rating']) || $request['rating'] == 0 || empty($request['content'])) {
           return new WP_Error("invalid_review", __('All fields are necessary', 'appmaker-woocommerce-mobile-app-manager'));
       }
       $commentdata = array(
            'comment_post_ID'      => $request['id'],
            'comment_author'       => $user_name,
            'comment_author_email' => $user_email,
            'comment_author_url'   => '',
            'comment_content'      => $request['content'],
            'comment_type'         => 'review',
            'comment_parent'       => 0,
            'user_id'              => get_current_user_id(),
            'comment_approved'     => 0,
       );
       //$commentdata['comment_approved'] =  wp_allow_comment( $commentdata, true );

        // Insert new comment and get the comment ID
        $comment_id = wp_insert_comment( $commentdata );
        update_comment_meta( $comment_id, 'rating', $request['rating'] );
       
        return array(
            'id'      => $comment_id,
            'status'  => true,
            'message' => 'Your comment is awaiting moderation.',
        );
    }

    /**
     * Check if a given request has access to read an item.
     *
     * @param  WP_REST_Request $request Full details about the request.
     *
     * @return WP_Error|boolean
     */
    public function get_item_permissions_check( $request ) {
        return $this->api_permissions_check( $request );
    }

    // submit form
    public function submit_form($request){
 
        $return = array();
        $return = apply_filters('appmaker_wc_submit_form',$request);
        return $return;

    }

    // input stepper
    public function input_stepper($request){
 
        $return = array();
        $return = apply_filters('appmaker_wc_input_stepper',$request);
        return $return;

    }

    public function get_products_list( $request ){

        $language = APPMAKER_WC::$api->get_settings( 'default_language', 'default' );		
        if(!empty($request['language'])){
            $language = $request['language'];
        }else if ( $language == 'default' ) {
            $language = false;
        }
        $key    = $request['key'];
        $data   = array();
        switch($key){
            case 'featured':
                $products = APPMAKER_WC_Helper::wc_get_featured_product_ids( $language );
                break;
            case 'sale':
                $products = APPMAKER_WC_Helper::wc_get_product_ids_on_sale( $language );
                break;
            case 'recent':
                $products  = APPMAKER_WC_Helper::get_recent_products( $language );                
                break;
            case 'best_selling':
                $products = APPMAKER_WC_Helper::get_best_selling_products( $language );
                break;
            case 'top_rated':
                $products = APPMAKER_WC_Helper::get_top_rated_products( $language );
                break;
            case 'custom' : //for product scroller
                $product_ids_array = array();                
                if( isset( $request['product_ids'] ) && ! empty ($request['product_ids']) && is_array( $request['product_ids']) ) { 
                    $request['product_ids'] = array_values($request['product_ids']);
                    foreach( $request['product_ids'] as $product_id ) {
                        $product_ids_array[$product_id ] = $product_id; 
                    }                   
                }                 
                $products = $product_ids_array;
                break;                
            default:
                $products = array();
				
        }

        if(!empty($products)){
            foreach ( $products as $id ) {           
                $product = APPMAKER_WC_Helper::get_product( $id );
                if ( ! empty( $product ) ) {
                    $data[] = APPMAKER_WC::$api->APPMAKER_WC_REST_Products_Controller->get_product_data( $product );
                }					
            }
        }
        return $data;        
    }

    public function get_tabbed_categories($request) {

        $taxonomy     = 'product_cat';
        $orderby      = 'name';		
        $parent		  =  isset($request['category']) ? $request['category'] : 0;
		$show_count   = 0;      // 1 for yes, 0 for no
		$pad_counts   = 0;      // 1 for yes, 0 for no
		$hierarchical = 1;      // 1 for yes, 0 for no
		$title        = '';
        $empty        = true;
        $exclude_category = get_term_by( 'slug', 'uncategorized', 'product_cat' );
        $requested_category  = isset( $request['category'] )? get_term( $request['category'] ) : '';
        $response = array( 

            'title'      => ! empty( $requested_category ) ? $requested_category->name : 'Categories',            
            'type'       => 'tab',            
            'tabs'       => array()
        );

		$args           = array(
			'taxonomy'     => $taxonomy,
			'orderby'      => $orderby,
			'show_count'   => $show_count,
			'pad_counts'   => $pad_counts,
			'hierarchical' => $hierarchical,
            'hide_empty'   => $empty,
            'parent'       => $parent
        );
        if( $exclude_category )
		{
			$args['exclude'] = $exclude_category->term_id;
		}
		if($parent !== 0 ){
			$args['child_of']=$parent;
        }       
        $all_categories = get_categories( $args ); //print_r($all_categories);exit;
        
        if( ! empty($all_categories) ){

            foreach ( $all_categories as $category ){
                $tabs[] = array(
                    'id'    => $category->term_id,
                    'title' => $this->decode_html( $category->name ),
                    'action' => array(
                        'type'   => 'LIST_PRODUCT',
                        'params' => array(
                          'category'=> $category->term_id,
                          'title'=> $this->decode_html( $category->name ),
                        ),
                    ),
                );
            }            
        } elseif( empty( $requested_category->children ) ){
            $tabs[] = array(
                'id'    => $requested_category->term_id,
                'title' => $this->decode_html( $requested_category->name ),
                'action' => array(
                    'type'   => 'LIST_PRODUCT',
                    'params' => array(
                      'category'=> $requested_category->term_id,
                      'title'=> $this->decode_html( $requested_category->name ),
                    ),
                ),
            );

        }    

        $response['tabs'] = $tabs; 
        return $response;
    }
    

}
