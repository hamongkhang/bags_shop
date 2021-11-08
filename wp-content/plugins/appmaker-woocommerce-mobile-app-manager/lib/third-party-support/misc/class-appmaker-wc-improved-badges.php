<?php
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_improved_badgets
{

    public function __construct()
    {
        add_filter('appmaker_wc_product_data', array($this, 'discount_badge'), 2, 3);
    }

    public function discount_badge($data, $product, $expanded)
    {

        $discount = $data['sale_percentage'];
        $on_sale = $data['on_sale'];
        $isb_curr_set = array();
        $isb_price = array();

        if (class_exists('WC_Improved_Sale_Badges')) {
            $curr_badge = WC_Improved_Sale_Badges::get_badge();
        }
        if (empty($curr_badge) && class_exists('XforWC_Improved_Badges_Frontend')) {
            global $isb_set;
            $instance = XforWC_Improved_Badges_Frontend::instance();
            if (empty($isb_set)) {
                $instance->make_a_set();
            }
            $curr_badge = $instance->get_badge();
        }

        $isb_set['load_js'] = true;

        if (empty($curr_badge)) {
            $curr_badge = array(
                'style'        => $isb_set['style'],
                'color'        => $isb_set['color'],
                'position'     => $isb_set['position'],
                'special'      => $isb_set['special'],
                'special_text' => $isb_set['special_text']
            );
        }

        if (isset($curr_badge[0]['special']) && $curr_badge[0]['special'] !== '') {
            $isb_curr_set['special_text'] = (isset($curr_badge[0]['special_text']) && $curr_badge[0]['special_text'] !== '' ? wp_kses_post($curr_badge[0]['special_text']) : '');
        }

        $isb_price['type'] = $data['type'];

        // $isb_price['id'] = get_the_ID();

        $isb_price['regular'] = !empty($data['regular_price']) ? $data['regular_price'] : '0';

        $isb_price['sale'] = !empty($data['sale_price']) ? $data['sale_price'] : '0';

        $isb_price['difference'] =  $isb_price['regular'] - $isb_price['sale'];

        $difference_display = APPMAKER_WC_Helper::get_display_price($isb_price['difference']);
        if ($discount == false && $on_sale !== false) {
            $discount   = round(($isb_price['regular'] - $isb_price['sale']) * 100 / $isb_price['regular']) . '%';
        }
        //$isb_price['percentage'] = round( ( $isb_price['regular'] - $isb_price['sale'] ) * 100 / $isb_price['regular'] );



        if (!empty($isb_curr_set)) {
            $data['sale_percentage'] = $isb_curr_set['special_text'];
        } elseif ($discount != false) {
            switch ($curr_badge[0]['style']) {
                case  'isb_style_cloud':
                    $isb_price['type'] == 'simple' ? $data['sale_percentage'] = $discount  . ' ' . 'save' . ' ' . $difference_display : $data['sale_percentage'] = $discount  . ' ' . 'Up to' . ' ' . $difference_display;
                    break;

                case 'isb_style_inline':
                    $isb_price['type'] == 'simple' ? $data['sale_percentage'] = $discount  . ' ' . 'save' . ' ' . $difference_display : $data['sale_percentage'] = $discount  . ' ' . 'Up to' . ' ' . $difference_display;
                    break;
                default:
                    $data['sale_percentage'] = $discount;
            }
        }




        return $data;
    }
}
new APPMAKER_WC_improved_badgets();
