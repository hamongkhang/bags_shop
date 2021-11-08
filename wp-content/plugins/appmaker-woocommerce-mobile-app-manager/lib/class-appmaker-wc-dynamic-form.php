<?php

class APPMAKER_WC_Dynamic_form {

	static $customer = false;
	static $_instance = false;
	private $response = array( 'items' => array(), 'order' => array(), 'dependencies' => array() );

	public function __construct() {
		if ( self::$customer == false && get_current_user_id() != false ) {
			self::$customer = get_userdata( get_current_user_id() );
		}
	}

	static function get_fields( $fields, $section ) {
		$form = new APPMAKER_WC_Dynamic_form();
		foreach ( $fields as $key => $field ) {
			$form->add_field( $field, $key, $section );
		}
		ksort($form->response['order']);
        $form->response['order'] = array_values($form->response['order']);
        $priority_value=0;
        foreach($form->response['order'] as $order_item){

            $form->response['items'][$order_item]['priority_value']=$priority_value;
            $priority_value++;
        }

		return apply_filters( 'appmaker_wc_dynamic_form_response', $form->response, $fields, $section );
	}

	private function add_field( $args, $key, $section ) {
		if ( isset( $args['label'] ) ) {
			$args['label'] = strip_tags( $args['label'] );
		}

		$this->response['items'][ $key ] = array();
		$defaults = array(
			'type' => 'text',
			'dependent' => false,
			'id' => $key,
			'label' => '',
			'description' => '',
			'placeholder' => '',
			'maxLength' => null,
			'minLength' => null,
			'required' => false,
			'options' => array(),
			'validate' => array(),
			'default_value' => '',
			'line_count' => 4,

		);

		if ( isset( $args['type'] ) && ! empty( $args['type'] ) ) {
			$args['type'] = preg_replace( '/wooccm/', '', $args['type'] );
		}
		if ( isset( $args['type'] ) && $args['type'] === 'select' ) {
			if ( isset( $args['options'] ) && is_string( $args['options'] ) ) {
				$options = explode( '||', $args['options'] );
				$args['options'] = array();
				foreach ( $options as $val ) {
				    $args['options'][ $val ] = $val;
				}
			}
		}

		$this->response['items'][ $key ] = apply_filters( 'appmaker_wc_field_args', wp_parse_args( $args, $defaults ), $args, $key, $section );

		if($key=='billing_phone'){
		    $this->response['items'][$key]['priority']=60;
        }
		if ( empty( $this->response['items'][ $key ]['label'] ) && ! empty( $this->response['items'][ $key ]['placeholder'] ) ) {
			$this->response['items'][ $key ]['label'] = $this->response['items'][ $key ]['placeholder'];
		}

		$this->response['items'][ $key ]['required'] = ($this->response['items'][ $key ]['required'] === 1 || $this->response['items'][ $key ]['required'] === '1' || $this->response['items'][ $key ]['required'] === true || $this->response['items'][ $key ]['required'] === 'true') ? true : false;

		if ( $this->response['items'][ $key ]['type'] == 'country' ) {
			$this->response['items'][ $key ]['type'] = 'select';
			$this->response['items'][ $key ]['mode'] = 'dropdown';
			$this->response['items'][ $key ]['options'] = ($key == 'shipping_country') ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();
		}

		if ( $this->response['items'][ $key ]['type'] == 'state' ) {
			$this->response['items'][ $key ]['dependent'] = true;
			$this->response['items'][ $key ]['type'] = 'dependent-select';
			$this->response['items'][ $key ]['mode'] = 'dropdown';
			$this->response['items'][ $key ]['options'] = ($key == 'shipping_state') ? $this->get_shipping_country_states() : $this->get_billing_country_states();

			$this->response['dependencies'][ $key ] = array(
				'on' => $section . '_country',
				'onEmpty' => array(
					'id' => $key . '_text',
				),
			);

			$args['type'] = 'text';
			$args['dependent'] = true;
			$args['priority']  = 65;
			$this->add_field( $args, $key . '_text', $section );
		}

		if ( $this->response['items'][ $key ]['type'] == 'multiselect' ) {
			$this->response['items'][ $key ]['id'] = $key . '[]';
		}

		if ( get_current_user_id() != 0 ) {
			$value = $this->get_user_value( $key );
			if ( $value != null && $value !== 'null' ) {
				$this->response['items'][ $key ]['default_value'] = $value;
			} else {
				$this->response['items'][ $key ]['default_value'] = (isset( $this->response['items'][ $key ]['default'] ) && ! is_null( $this->response['items'][ $key ]['default'] ) && $this->response['items'][ $key ]['default'] !== 'null') ? $this->response['items'][ $key ]['default'] : '';
            }
		} elseif ( isset( $this->response['items'][ $key ]['default'] ) && ! is_null( $this->response['items'][ $key ]['default'] ) && $this->response['items'][ $key ]['default'] != 'null' ) {
			$this->response['items'][ $key ]['default_value'] = $this->response['items'][ $key ]['default'];
		}
		//Setting default value if only one option is available

        if( is_array( $this->response['items'][ $key ]['options'] ) && !empty($this->response['items'][ $key ]['options']) && count($this->response['items'][ $key ]['options'])==1 && $this->response['items'][ $key ]['id'] != 'billing_state' && $this->response['items'][ $key ]['type'] == 'select'){

            foreach($this->response['items'][ $key ]['options'] as $option_key => $option_value) {
                $this->response['items'][$key]['default_value'] = $option_key;
                if(is_array($option_value)){
                    if(!empty($option_value['items']) && count($option_value['items'])==1 ){
                      foreach($option_value['items'] as $sub_key => $sub_value){
                          $this->response['items'][$key]['default_value'] =$sub_key;
                      }
                    }
                }
            }
        }

		if ( $this->response['items'][ $key ]['type'] == 'checkbox' || $this->response['items'][ $key ]['id'] == 'ship_to_different_address'  ) {
		    $this->response['items'][ $key ]['default_value'] = (boolean) $this->response['items'][ $key ]['default_value'];
		} else {
			$this->response['items'][ $key ]['default_value'] = (string) $this->response['items'][ $key ]['default_value'];
		}

		$this->response['items'][ $key ] = array_diff_key($this->response['items'][ $key ], array_flip(array(
			'class',
			'default',
			'autocomplete',
			'clear',
		)));
		if ( ! empty( $this->response['items'][ $key ]['validate'] ) && in_array( 'email', $this->response['items'][ $key ]['validate'] ) ) {
			$this->response['items'][ $key ]['validate'] = array(
				'type' => 'email',
				'message' => __( 'Please provide a valid email address.', 'woocommerce' ),
                'regex' => "[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?",
			);
			$this->response['items'][ $key ]['keyboardType'] = 'email-address';

		} elseif ( ! empty( $this->response['items'][ $key ]['validate'] ) && in_array( 'phone', $this->response['items'][ $key ]['validate'] ) ) {
            $ship_to_india = 0;
		    $shipping_countries = WC()->countries->get_shipping_countries();
			foreach ($shipping_countries as $code => $country){
			    if($code == 'IN'){
			        $ship_to_india = apply_filters('appmaker_wc_phone_validation', true );
                }
            }
		    $this->response['items'][ $key ]['validate'] = array(
				'type' => 'regex',
				'message' => __( 'Please enter a valid Phone.', 'appmaker-woocommerce-mobile-app-manager' ),
                'regex' => (($ship_to_india) && (count($shipping_countries) == 1)) ? '^\\d{10}$' : '^(\\+?[0-9]{2,4})?(\\s)?[0-9]{7,16}$',
			);
			$this->response['items'][ $key ]['keyboardType'] = 'phone-pad';
			$this->response['items'][ $key ]['returnKeyType'] = 'done';
		} elseif ( ! empty( $this->response['items'][ $key ]['validate'] ) && in_array( 'postcode', $this->response['items'][ $key ]['validate'] ) &&
			count( array_intersect( array( 'US', 'CR', 'NL', 'BN', 'IE', 'SO', 'MT', 'GB', 'AR', 'KZ', 'JM', 'PE', 'SZ' ), array_keys( WC()->countries->get_allowed_countries() ) ) ) < 1
		) {
			$this->response['items'][ $key ]['keyboardType'] = 'numeric';
		} elseif ( empty( $this->response['items'][ $key ]['validate'] ) || ! (isset( $this->response['items'][ $key ]['validate']['type'] ) && isset( $this->response['items'][ $key ]['validate']['message'] ) && isset( $this->response['items'][ $key ]['validate']['regex'] )) ) {
			$this->response['items'][ $key ]['validate'] = array();
		}
		//	$this->response["items"][ $key ]['required'] = false;

        if (!isset($args['priority'])){
            $args['priority'] = 120;
        }

        while(isset($this->response['order'][$args['priority']])){
            $args['priority']++;
        }
		$this->response['order'][$args['priority']] = $key;
		$this->add_field_dependencies( $key );
	}

	public function get_shipping_country_states() {
		$countries = WC()->countries->get_shipping_countries();
		$states_c = WC()->countries->get_shipping_country_states();

		return apply_filters( 'appmaker_wc_allowed_countries', $this->prepare_state_response( $countries, $states_c ) );
	}

	public function prepare_state_response( $countries, $states_c ) {
		$return = array();
		foreach ( $countries as $key => $country ) {
			$return[ $key ] = array( 'items' => array() );
			if ( isset( $states_c[ $key ] ) && is_array( $states_c[ $key ] ) ) {
				foreach ( $states_c[ $key ] as $state_key => $state ) {
					$return[ $key ]['items'][ $state_key ] = html_entity_decode( $state );
				}
			}
			if ( empty( $return[ $key ]['items'] ) ) {
				$return[ $key ]['items'] = null;
			}
		}

		return apply_filters( 'appmaker_wc_states_response', $return );
	}

	public function get_billing_country_states() {
		$countries = WC()->countries->get_allowed_countries();
		$states_c = WC()->countries->get_allowed_country_states();

		return apply_filters( 'appmaker_wc_allowed_countries', $this->prepare_state_response( $countries, $states_c ) );
	}

	public function get_user_value( $key ) {
		$return = null;
		if ( isset( self::$customer->{$key} ) && self::$customer->{$key} ) {
			$return = self::$customer->{$key};
		} elseif ( $key == 'billing_email' || $key == 'shipping_email' && ! empty( self::$customer->user_email ) ) {
			$return = self::$customer->user_email;
		} elseif ( $key == 'billing_first_name' || $key == 'shipping_first_name' && ! empty( self::$customer->user_firstname ) ) {
			$return = self::$customer->user_firstname;
		} elseif ( $key == 'billing_last_name' || $key == 'shipping_last_name' && ! empty( self::$customer->user_firstname ) ) {
			$return = self::$customer->user_lastname;
		}

		return apply_filters( 'appmaker_wc_form_user_value', $return, $key );
	}

	public function add_field_dependencies( $key ) {
		$dependency = apply_filters( 'appmaker_wc_dependency_' . $key, false, $key );
		if ( $dependency != false ) {
			$this->response['dependencies'][ $key ] = $dependency;
			$this->response['items'][ $key ]['dependent'] = true;
		}
	}

}
