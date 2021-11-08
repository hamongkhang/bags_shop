<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	// Exit if accessed directly.

class APPMAKER_WC_Third_party_support {
	static function init() {
		if ( ( function_exists( 'get_list_of_kecamatan' ) || function_exists( 'epeken_get_list_of_kecamatan' ) ) && ! class_exists('APPMAKER_WC_Epeken') ) {
			require 'checkout-form/class-appmaker-wc-epeken.php';
			APPMAKER_WC_Epeken::init();
		}
		if ( function_exists( 'WC_JNE' ) && ! class_exists('APPMAKER_WC_Jne') ) {
			require 'checkout-form/class-appmaker-wc-jne.php';
			APPMAKER_WC_Jne::init();
		}

		if ( function_exists( 'WC_TIKI' ) && ! class_exists('APPMAKER_WC_tiki') ) {
			require 'checkout-form/class-appmaker-wc-tiki.php';
		}

		// Payment Gateways.
		if ( ! class_exists( 'APPMAKER_WC_Gateway_Appmaker' ) ) {
			require 'payment-gateway/class-appmaker-wc-payment-gateway.php';
		}

		// Payment Gateways.
		if ( class_exists( 'woocommerce_pnpdirect' ) && ! class_exists('APPMAKER_WC_Gateway_PnpDirect') ) {
			require 'payment-gateway/class-appmaker-wc-pnpdirect.php';
		}

		// Payment Gateways.
		if ( class_exists( 'SPYR_AuthorizeNet_AIM' ) && ! class_exists('APPMAKER_WC_Gateway_AuthorizeNet_AIM')  ) {
			require 'payment-gateway/class-appmaker-wc-xmmlib.php';
		}

		// Payment Gateways.
		/*if ( class_exists( 'WC_Razorpay' ) ) {
			require 'payment-gateway/class-appmaker-wc-razorpay.php';
		}*/

		// Payment Gateways.
		if ( class_exists( 'WC_GetSimpl' ) && ! class_exists('APPMAKER_WC_Gateway_Simpl') ) {
			require 'payment-gateway/class-appmaker-wc-simpl.php';
		}

		if ( class_exists( 'PLL_Choose_Lang' ) && ! class_exists('APPMAKER_WC_Third_Polylang') ) {
			require 'misc/class-appmaker-wc-polylang.php';
		}

		if ( class_exists( 'SitePress' )  && ! class_exists('APPMAKER_WC_Third_WPML') ) {
			require 'misc/class-appmaker-wc-wpml.php';
		}

		if ( class_exists( 'WC_Points_Rewards' ) && ! class_exists('APPMAKER_WC_POINTS_AND_REWARDS') ) {
			require 'points-and-rewards/class-appmaker-wc-points-and-rewards.php';

		}

		if ( class_exists( 'WC_Bulk_Variations' ) && ! class_exists('APPMAKER_WC_BULK_VARIATIONS') ) {
			require 'bulk-variations/class-appmaker-wc-bulk-variations.php';
		}

		if ( class_exists( 'RegisterPlusReduxAutoLoginPlugin' ) && ! class_exists('APPMAKER_WC_Inviataion_Code') ) {
			require 'misc/class-appmaker-wc-invitaion-code.php';
		}

		if ( class_exists( 'WC_Product_Gift_Wrap' ) && ! class_exists('APPMAKER_WC_Product_Gift_Wrap') ) {
			require 'misc/class-appmaker-wc-product-gift-wrap.php';
		}

		if ( function_exists( 'picodecheck_ajax_submit' ) && ! class_exists('APPMAKER_WC_Pincode_Check') ) {
			require 'misc/class-appmaker-wc-pincode-check-pro.php';
		}

		if ( class_exists( 'WC_Vendors' )  && ! class_exists('APPMAKER_WC_Vendors') ) {
			require 'misc/class-appmaker-wc-vendors.php';
		}

		if ( class_exists( 'WC_Product_Addons' ) && ! class_exists( 'APPMAKER_WC_Product_Addons' ) ) {
			require 'misc/class-appmaker-wc-product-add-ons.php';
		}

		if ( class_exists( 'MGWB' ) && ! class_exists( 'APPMAKER_WC_Product_Brands' ) ) {
			require 'misc/class-appmaker-wc-product-brand.php';
		}

		if ( class_exists( 'WooCommerceWholeSalePrices' ) && ! class_exists( 'APPMAKER_WC_wholesale_price' ) ) {
			require 'misc/class-appmaker-wc-wholesale-price.php';
		}

		if ( class_exists( 'orddd_lite_common' ) && ! class_exists( 'APPMAKER_WC_Order_date' ) ) {
			require 'misc/class-appmaker-wc-order-date.php';
		}
		if ( class_exists( 'woocommerce_booking' ) && ! class_exists( 'APPMAKER_WC_booking_date' ) ) {
			require 'misc/class-appmaker-wc-booking-date.php';
		}
		if ( class_exists( 'WC_Bookings' ) && ! class_exists( 'APPMAKER_WC_bookings' ) ) {
			require 'misc/class-appmaker-wc-booking.php';
		}
		if ( class_exists( 'byconsolewooodt_widget' ) && ! class_exists( 'APPMAKER_WC_Delivery_Date_Time' ) ) {
			require 'misc/class-appmaker-wc-delivery-date-time.php';
		}
		if ( class_exists( 'order_delivery_date' ) && ! class_exists( 'APPMAKER_WC_Order_Delivery_Date' ) ) {
			require 'misc/class-appmaker-wc-order-delivery-date.php';
		}

		if ( class_exists( 'WCJ_Order_Min_Amount' ) && ! class_exists( 'APPMAKER_WC_Minimum_Order_Cart' ) ) {
			require 'misc/class-appmaker-wc-jetpack-min-order-cart.php';
		}

		if ( class_exists( 'JEM_Controller' ) && ! class_exists( 'APPMAKER_WC_Minimum_Order' ) ) {
			require 'misc/class-appmaker-wc-woocommece-minimum-order.php';
		}

		if ( class_exists( 'SmsAlertUtility' ) && ! class_exists( 'APPMAKER_WC_SMS_Verify' ) ) {
			require 'misc/class-appmaker-sms-verify.php';
		}
		if ( class_exists( 'WC_Cancel_Order' ) && ! class_exists( 'APPMAKER_WC_cancel_order' ) ) {
			require 'misc/class-appmaker-wc-cancel-order.php';
		}
		if ( function_exists( 'gglcptch_check' ) && ! class_exists( 'APPMAKER_WC_Google_captcha' ) ) {
			require 'misc/class-appmaker-wc-google-captcha.php';
		}

		if ( class_exists( 'Wcff' ) && ! class_exists( 'APPMAKER_WC_wcff_custom_fields' ) ) {
			require 'misc/class-appmaker-wc-wcff-custom-fields.php';
		}
		if ( ( function_exists( 'pcmfe_admin_form_field' ) || class_exists( 'pcfme_add_order_meta_class' ) ) && ! class_exists( 'APPMAKER_WC_checkout_fields' )  ) {

			require 'misc/class-appmaker-wc-checkout-fields.php';
		}

		if ( class_exists( 'SimpleVendor' ) && ! class_exists( 'APPMAKER_WC_Simple_Vendors' ) ) {
			require 'misc/class-appmaker-wc-simple-vendor.php';
		}
		if ( class_exists( 'Wad' ) ) {
			require 'misc/class-appmaker-wc-all-discounts.php';
		}
		if ( class_exists( 'WPSEO_Local_Search' ) && ! class_exists( 'APPMAKER_WC_yoast_seo' ) ) {
			require 'misc/class-appmaker-wc-yoast-seo.php';
		}
		if ( class_exists( 'WC_Ncr_No_Captcha_Recaptcha' ) && ! class_exists( 'APPMAKER_WC_captcha' ) ) {
			require 'misc/class-appmaker-wc-captcha.php';
		}
		if ( ( class_exists( 'Dokan_Registration' ) || class_exists( 'WeDevs_Dokan' ) ) && ! class_exists('APPMAKER_WC_dokan_lite') ) {
			require 'misc/class-appmaker-wc-dokan.php';
		}
		if ( class_exists( 'WPGDPRC' ) && ! class_exists( 'APPMAKER_WC_wpgdpr_compliance' ) ) {
			require 'misc/class-appmaker-wc-wpgdpr-compliance.php';
		}

		if ( class_exists( 'WCISPlugin' ) && ! class_exists( 'APPMAKER_WC_instant_search' ) ) {
			require 'misc/class-appmaker-wc-instant-search.php';
		}

		if ( class_exists( 'WCMp_Product' ) ) {
			require 'misc/class-appmaker-wc-wcmp.php';
		}
		// woocommerce gateway beanstream
		if ( class_exists( 'WC_Bambora_Loader' ) && ! class_exists( 'APPMAKER_WC_Gateway_beanstream' ) ) {
			require 'payment-gateway/class-appmaker-wc-beanstream.php';
		}
		// product specifications woocommerce
		if ( class_exists( 'DW_specs' ) ) {
			require 'misc/class-appmaker-wc-specifications.php';
		}
		// amazon pay
		if ( class_exists( 'WC_Amazon_Payments_Advanced' ) && ! class_exists( 'APPMAKER_WC_Gateway_amazonpay' ) ) {
			require 'payment-gateway/class-appmaker-wc-amazonpay.php';
		}
		// aftership-woocommerce tracking
		if ( class_exists( 'AfterShip' ) && ! class_exists( 'APPMAKER_WC_Order_Tracking' ) ) {
			require 'misc/class-appmaker-wc-aftership-order-tracking.php';
		}
		// woocommerce mailchimp integration
		if ( class_exists( 'WC_Mailchimp' ) && ! class_exists( 'APPMAKER_WC_mailchimp' ) ) {
			require 'misc/class-appmaker-wc-mailchimp.php';
		}
		// yith woocommerce order tracking
		if ( class_exists( 'YITH_WooCommerce_Order_Tracking' )  && ! class_exists( 'APPMAKER_WC_Yith_Order_Tracking' ) ) {
			require 'misc/class-appmaker-wc-yith-order-tracking.php';
		}
		if ( APPMAKER_WC::$api->get_settings( 'out_of_stock', 0 ) == 1  && ! class_exists('APPMAKER_WC_Out_Of_Stock_Order') ) {
			require 'misc/class-appmaker-wc-out-of-stock-order.php';
		}
		// product size chart for woocommerce
		if ( class_exists( 'productsize_chart' ) && ! class_exists( 'APPMAKER_WC_Product_Size_Chart' ) ) {
			require 'misc/class-appmaker-wc-size-chart.php';
		}
		// Improved badgets woocommerce -premium version
		if ( ( class_exists( 'WC_Improved_Sale_Badges_Init' ) || class_exists('XforWC_Improved_Badges') ) && ! class_exists( 'APPMAKER_WC_improved_badgets' ) ) {
			require 'misc/class-appmaker-wc-improved-badges.php';
		}
		// perfect woocommerce brands
		if ( class_exists( 'Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands' ) && ! class_exists( 'APPMAKER_WC_perfect_brands' ) ) {
			require 'misc/class-appmaker-wc-product-filter.php';
		}
		// collivery woocommerce
		if ( class_exists( 'MdsCheckoutFields' ) && ! class_exists( 'APPMAKER_WC_collivery_fields' ) ) {
			require 'misc/class-appmaker-wc-collivery.php';
		}

		// phphive-WooCommerce Bookings And Appointments
		if ( class_exists( 'phive_booking_initialze_premium' ) && ! class_exists( 'APPMAKER_WC_booking_and_appointments' ) ) {
			require 'misc/class-appmaker-wc-phphive-booking.php';
		}

		// woo custom fee
		if ( function_exists( 'wacf_check_woocommerce_plugin' )  && ! class_exists( 'APPMAKER_WC_custom_fee' ) ) {
			require 'misc/class-appmaker-wc-custom-fee.php';
		}

		// digits
		if ( ( function_exists( 'digits_load_plugin_textdomain' ) || class_exists('NWC_Meta_Box_Product_Data') ) && ! class_exists( 'APPMAKER_WC_digits_otp' ) ) {
			require 'misc/class-appmaker-wc-digits-otp.php';
		}

		// https://wordpress.org/plugins/advanced-nocaptcha-recaptcha/
		if ( class_exists( 'anr_captcha_class' ) && ! class_exists( 'APPMAKER_WC_invisible_captcha' ) ) {
			require 'misc/class-appmaker-wc-invisible-captcha.php';
		}
		// https://tw.wordpress.org/plugins/ecpay-invoice-for-woocommerce/
		if ( class_exists( 'WC_ECPayinvoice' ) && ! class_exists( 'APPMAKER_WC_ECPay_invoice' ) ) {
			require 'misc/class-appmaker-wc-ecway-invoice.php';
		}
		// Yith woocommerce points and rewards
		if ( class_exists( 'YITH_WC_Points_Rewards' ) && ! class_exists( 'APPMAKER_WC_YITH_POINTS_AND_REWARDS' ) ) {
			require 'points-and-rewards/class-appmaker-wc-yith-points-rewards.php';
		}
		// Booster for WooCommerce
		if ( class_exists( 'WC_Jetpack' ) && ! class_exists( 'APPMAKER_WC_booster' ) ) {
			require 'misc/class-appmaker-wc-booster.php';
		}
		// woocommerce waitlist
		if ( class_exists( 'Xoo_WL_Public' )  && ! class_exists( 'APPMAKER_WC_Waitlist' ) ) {
			require 'misc/class-appmaker-wc-waitlist.php';
		}
		// woocommerce and qtranslatex plugin
		if ( function_exists( 'qwc_init_language' )  && ! class_exists( 'APPMAKER_WC_qtranslatex' ) ) {
			require 'misc/class-appmaker-wc-qtranslatex.php';
		}
		// suntech payment gateways- atm and buy safe
		if ( class_exists( 'WC_Gateway_Suntech_Base' ) && ! class_exists( 'APPMAKER_WC_Gateway_suntech' ) ) {
			require 'payment-gateway/class-appmaker-wc-suntech-payments.php';
		}
		// Advanced woo search
		if ( class_exists( 'AWS_Search' ) && ! class_exists( 'APPMAKER_WC_woo_search' ) ) {
			require 'misc/class-appmaker-wc-advanced-woo-search.php';
		}
		// flexible checkout fields
		if ( class_exists( 'Flexible_Checkout_Fields_Disaplay_Options' ) && ! class_exists( 'APPMAKER_WC_flexible_checkout_field' ) ) {
			require 'misc/class-appmaker-wc-flexible-checkout-fields.php';
		}
		// woo wallet
		if ( class_exists( 'WooWallet' ) && ! class_exists( 'APPMAKER_WC_Woo_Wallet' ) ) {
			require 'payment-gateway/class-appmaker-wc-woo-wallet.php';
		}
		// woocommerce 360 image
		if ( class_exists( 'WC_360_Image_Display' ) && ! class_exists( 'APPMAKER_WC_image360' ) ) {
			require 'misc/class-appmaker-wc-360-image.php';
		}
		// rede woocommerce api
		if ( class_exists( 'WC_Rede' ) && ! class_exists( 'APPMAKER_WC_Gateway_rede' ) ) {
			require 'payment-gateway/class-appmaker-wc-rede.php';
		}
		// WC Simulador de parcelas e descontos
		if ( class_exists( 'WC_Simulador_Parcelas' ) && ! class_exists( 'APPMAKER_WC_simulador' ) ) {
			require 'misc/class-appmaker-wc-simulador-de-parcelas.php';
		}
		// order hours for woocommerce
		if ( class_exists( 'Zhours\Aspect\InstanceStorage' ) && ! class_exists( 'APPMAKER_WC_Order_hours' ) ) {
			require 'misc/class-appmaker-wc-order-hours.php';
		}
		// sumo payment plan
		if ( class_exists( 'SUMOPaymentPlans' ) && ! class_exists( 'APPMAKER_WC_SUMO_PAYMENT' ) ) {
			require 'misc/class-appmaker-wc-sumopayment.php';
		}
		 // WC Simulador frete
		if ( class_exists( 'WC_Shipping_Simulator' ) && ! class_exists( 'APPMAKER_WC_simulador_frete' ) ) {
			require 'misc/class-appmaker-wc-simulador-frete.php';
		}
		// WC city select
		if ( class_exists( 'WC_City_Select' ) && ! class_exists( 'APPMAKER_WC_city_select' ) ) {
			require 'checkout-form/class-appmaker-wc-city-select.php';
		}
		// woocommerce simple auction
		if ( class_exists( 'WooCommerce_simple_auction' ) && ! class_exists( 'APPMAKER_WC_simple_auction' ) ) {
			require 'misc/class-appmaker-wc-simple-auction.php';
		}
		 // WC paymes gateway
		if ( class_exists( 'WC_Paymes_Gateway' ) && ! class_exists( 'APPMAKER_WC_Paymes' ) ) {
			require 'payment-gateway/class-appmaker-wc-paymes.php';
		}
		 // knawat drop shipping plugin - image size issue
		if ( class_exists( 'Featured_Image_By_URL_Common' ) && ! class_exists( 'APPMAKER_WC_knawat_image' ) ) {
			require 'misc/class-appmaker-wc-knawat-image.php';
		}
		// wcfm multivendor plugin
		if ( class_exists( 'WCFMmp' ) && ! class_exists( 'APPMAKER_WC_WCFM_Vendors' ) ) {
			require 'misc/class-appmaker-wc-wcfm-vendor.php';
		}
		 // WooCommerce brands
		if ( class_exists( 'WC_Brands' ) && ! class_exists( 'APPMAKER_WC_Brands' ) ) {
			require 'misc/class-appmaker-wc-brands.php';
        }
        //visualizer	
        if(class_exists('Visualizer_Module')  && ! class_exists( 'APPMAKER_WC_Visualizer' ) ){	
            require 'misc/class-appmaker-wc-visualizer.php';	
        }
		// WooCommerce generate categories
		if ( ! class_exists( 'APPMAKER_WC_generate_categories' ) ) {
			require 'misc/class-appmaker-wc-generate-categories.php';
		}
		//Healthxp product Q&A
		if ( class_exists( 'APPMAKER_WC_HealthXp' ) && ! class_exists( 'APPMAKER_WC_HXP' ) ) {
			require 'misc/class-appmaker-wc-hxp.php';
		}

		//WooCommerce advanced messages
		if ( class_exists( 'WooCommerce_Advanced_Messages' ) && ! class_exists( 'APPMAKER_WC_wcam' ) ) {
			require 'misc/class-appmaker-wc-advanced-messages.php';
		}

		//woocommerce-estimated-delivery-date-per-product
		if ( class_exists( 'Pi_Edd' ) && ! class_exists( 'APPMAKER_WC_Estimated_Delivery' ) ) {
			require 'misc/class-appmaker-wc-estimated-delivery.php';
		}

		//stripe payment gateway        
		if ( class_exists( 'WC_Stripe' ) && ! class_exists( 'APPMAKER_WC_Stripe' ) ) {
			require 'payment-gateway/class-appmaker-wc-stripe.php';
		}

		//shipcloud for woocommerce
		if ( class_exists( 'WCSC_Woo' ) && ! class_exists( 'APPMAKER_WC_shipcloud' ) ) {
			require 'misc/class-appmaker-wc-shipcloud.php';
		}

		//WooCommerce License Delivery
		if ( class_exists( 'WooCommerce_License_Delivery' ) && ! class_exists( 'APPMAKER_WC_License_Delivery' ) ) {
			require 'misc/class-appmaker-wc-license-delivery.php';
		}	
		
		//Sumo reward points
		if ( class_exists( 'FPRewardSystem' ) && ! class_exists( 'APPMAKER_WC_SUMO_POINTS_AND_REWARDS' ) ) {
			require 'points-and-rewards/class-appmaker-wc-sumo-points-rewards.php';
		}

		//sumo affliate plugin
		if ( class_exists( 'FS_Affiliates' ) && ! class_exists( 'APPMAKER_WC_Sumo_Affliates' ) ) {
			require 'misc/class-appmaker-wc-sumo-affliates.php';
		}

		//WooCommerce order approval
		if ( class_exists( 'WCOA\classes\com\Order' ) && ! class_exists( 'APPMAKER_WC_Order_Approval' ) ) {
			require 'misc/class-appmaker-wc-order-approval.php';
		}	

		//Final User
		if ( class_exists( 'finaluser' ) && ! class_exists( 'APPMAKER_WC_finaluser' ) ) {
			require 'misc/class-appmaker-wc-finaluser.php';
		}

		//WooCommerce Subscription
		if ( class_exists( 'WC_Subscriptions' ) && ! class_exists( 'APPMAKER_WC_woocommerce_subscription' ) ) {
			require 'misc/class-appmaker-wc-woocommerce-subscription.php';
		}
		//Yith WC product brands
		if ( class_exists( 'YITH_WCBR' ) && ! class_exists( 'APPMAKER_WC_Yith_Brands' ) ) {
			require 'misc/class-appmaker-wc-yith-product-brands.php';
		}

		//flexible shipping
		if ( class_exists( 'WPDesk_Flexible_Shipping' ) && ! class_exists( 'APPMAKER_WC_flexible_shipping' ) ) {
			require 'misc/class-appmaker-wc-flexible-shipping.php';
		}

		//VarkTech pricing deals
		if ( class_exists( 'VTPRD_Controller' ) && ! class_exists( 'APPMAKER_WC_VarKTech' ) ) {
			require 'misc/class-appmaker-wc-varktech.php';
		}

		//https://yithemes.com/themes/plugins/yith-woocommerce-gift-cards/
		if ( class_exists( 'WC_Product_Gift_Card' ) && ! class_exists( 'APPMAKER_WC_YITH_GIFT_CARDS' ) ) {
			require 'misc/class-appmaker-wc-yith-gift-cards.php';
		}

		//https://codecanyon.net/item/woocommerce-brands/8039481
		if ( class_exists( 'woo_brands' ) && ! class_exists( 'APPMAKER_WC_Woo_Brands' ) ) {
			require 'misc/class-appmaker-wc-woo-brands.php';
		}
		//https://wordpress.org/plugins/woocommerce-unit-of-measure/
		if ( class_exists( 'WC_UOM_Public' ) && ! class_exists( 'APPMAKER_WC_Unit_Of_Measure' ) ) {
			require 'misc/class-appmaker-wc-unit-of-measure.php';
		}

		// yith woocommerce request a quote 
		if ( class_exists( 'YITH_Request_Quote' )  && ! class_exists( 'APPMAKER_WC_YITH_REQUEST_QUOTE' ) ) {
			require 'misc/class-appmaker-wc-yith-request-quote.php';
		}
		
		//WooCommerce restaurant plugin - http://wp.xperts.club
		if ( class_exists( 'XC_WOO_RESTAURANT' ) && ! class_exists( 'APPMAKER_WC_Woo_Restaurant' ) ) {
			require 'misc/class-appmaker-wc-woo-restaurant.php';
		}
		
		// WooCommerce multicurrency premium and free version https://wordpress.org/plugins/woo-multi-currency/
		if ( ( class_exists( 'WOOMULTI_CURRENCY' ) || class_exists('WOOMULTI_CURRENCY_F') ) && ! class_exists('APPMAKER_WC_WOOMULTI_CURRENCY') ) {
			require 'misc/class-appmaker-wc-woomulti-currency.php';
		}

		// WP Super Cache
		if ( function_exists( 'scossdl_off_get_options' ) && ! class_exists('APPMAKER_WP_SUPER_CACHE') ) {
			require 'misc/class-appmaker-wp-super-cache.php';
		}

		//  Password Protect WordPress Pro - https://passwordprotectwp.com/
		if ( class_exists( 'PPW_Pro_Password_Services' ) && ! class_exists('APPMAKER_WP_PASSWORD_PROTECT') ) {
			require 'misc/class-appmaker-wc-ppw-pro-password.php';
		}

		//woocommerce smart coupons - https://woocommerce.com/products/smart-coupons/
		if ( class_exists( 'WC_Smart_Coupons' ) && ! class_exists('APPMAKER_WC_SMART_COUPONS') ) {
			require 'misc/class-appmaker-wc-smart-coupons.php';
		}

		// WooCommerce Product Bundles -
		if ( class_exists( 'WC_Bundles' ) && ! class_exists('APPMAKER_WC_PRODUCT_BUNDLES') ) {
			require 'misc/class-appmaker-wc-product-bundles.php';
		}

		if ( class_exists( 'WWLC_User_Account' ) && ! class_exists('APPMAKER_WC_WHOLESALE_LEAD') ) {
			require 'misc/class-appmaker-wholesale-lead-capture.php';
		}
        //user email verification for woocommerce
		if ( class_exists( 'XLWUEV_Core' ) && ! class_exists('APPMAKER_WC_XLWUEV') ) {
			require 'misc/class-appmaker-wc-user-email-verification.php';
		}

		// YITH WOOCOMMERCE WISHLIST PREMIUM
		if ( function_exists( 'yith_wishlist_constructor' ) && ! class_exists('APPMAKER_WC_YITH_WISHLIST') ) {
			require 'misc/class-appmaker-wc-yith-wishlist.php';
		}

		// https://wordpress.org/plugins/wp-extended-search/
		if ( class_exists( 'WPES_Core' ) && ! class_exists('APPMAKER_WC_EXTENDED_SEARCH') ) {
			require 'misc/class-appmaker-wc-extended-search.php';
		}
        
		// WooCommerce Random Product Sorting with Pagination - https://wordpress.org/plugins/woo-random-product-sorting-with-pagination/
		if ( function_exists( 'do2_addRandomProductOrderSetting' ) && ! class_exists('APPMAKER_WC_RANDOM_SORTING') ) {
			require 'misc/class-appmaker-wc-random-sorting.php';
		}

		// relevanssi premium search support
		if ( function_exists( 'relevanssi_do_query' ) && ! class_exists('APPMAKER_WC_RELEVANSSI_SEARCH') ) {
			require 'misc/class-appmaker-wc-relevanssi.php';
		}

		//PPOM for WooCommerce by N-MEDIA
		if ( class_exists( 'NM_PersonalizedProduct' ) && ! class_exists('APPMAKER_WC_PPOM') ) {
			require 'misc/class-appmaker-wc-ppom.php';
		}

		if ( ! class_exists('APPMAKER_WC_Repeat_Order') ) {
			require 'misc/class-appmaker-wc-repeat-order.php';
		}
		
		APPMAKER_WC_Gateway_Appmaker::init();
	}

}

