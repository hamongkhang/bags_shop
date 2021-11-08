<?php
/**
 * Created by IntelliJ IDEA.
 * User: shifa
 * Date: 7/20/18
 * Time: 10:29 AM
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/*class APPMAKER_WC_WAD{

    public function __construct()
    {
        add_filter('appmaker_wc_product_fields', array($this, 'quantity_table'), 2, 2);
    }

    public function quantity_table($fields, $product)
    {
        $table = array();
        $check=0;
        $product_id = get_the_ID();
        $product_obj = wc_get_product($product_id);
        $quantity_pricing = get_post_meta($product_id, "o-discount", true);
        $rules_type = get_proper_value($quantity_pricing, "rules-type", "intervals");
        // ob_start();

        if (isset($quantity_pricing["enable"]) && isset($quantity_pricing["rules"])) {
            if ($rules_type == "intervals") {
                if ($product_obj->get_type() == "variable") {
                    $available_variations = $product_obj->get_available_variations();
                    foreach ($available_variations as $variation) {
                        $product_price = $variation["display_price"];

                        //get_quantity_pricing_table($variation["variation_id"], $quantity_pricing, $product_price);
                    }
                } else {
                    //$product_price = $product_obj->price;
                    //$product_price = wad_get_product_price($product_obj); //$product_obj->get_price();
                    $product_price = $product_obj->get_price();

                    // get_quantity_pricing_table($product_id, $quantity_pricing, $product_price, true);
                }
            } else if ($rules_type == "steps") {

                if ($product_obj->get_type() == "variable") {
                    $available_variations = $product_obj->get_available_variations();
                    foreach ($available_variations as $variation) {
                        $product_price = $variation["display_price"]; $check = 1;
                        // get_steps_quantity_pricing_table($variation["variation_id"], $quantity_pricing, $product_price);
                    }
                } else {
                    //$product_price = $product_obj->price;
                    //$product_price = wad_get_product_price($product_obj); //$product_obj->get_price();
                    $product_price = $product_obj->get_price(); $check = 1;
                    //get_steps_quantity_pricing_table($product_id, $quantity_pricing, $product_price, true);
                }
            }
            if ($check!=1) {
                foreach ($quantity_pricing["rules"] as $rule) {
                    if ($quantity_pricing["type"] == "fixed") {
                        $price = $product_price - $rule["discount"];
                    } else if ($quantity_pricing["type"] == "percentage") {
                        $price = $product_price - ($product_price * $rule["discount"]) / 100;
                    } else if ($quantity_pricing["type"] == "n-free") {
                        if ($rule["min"])
                            $quantity_to_check = $rule["min"];
                        else
                            $quantity_to_check = $rule["max"];

                        $price = $normal_price = wad_get_product_free_gift_price($product_price, $quantity_to_check, $rule["discount"]);
                    }
                   foreach($rule as $key => $value){
                        $table['min']= $value;
                   }
                    $table['max'] = empty($rule['max']) ? 'And more' : $rule['max'];
                    $table['unit_price'] = wc_price($price);
                }
            } else {
                foreach ($quantity_pricing["rules-by-step"] as $rule) {
                    if ($quantity_pricing["type"] == "fixed") {
                        $price = $product_price - $rule["discount"];
                    } else if ($quantity_pricing["type"] == "percentage") {
                        $price = $product_price - ($product_price * $rule["discount"]) / 100;
                    }
                    $table['unit_price']=wc_price($price);
                }

            }

        }
        return array_merge( $table, $fields );
    }




}
new APPMAKER_WC_WAD();
*/