<?php
$base_url=site_url();
$css_url=$base_url.'/wp-content/plugins/waitlist-woocommerce/assets/css/xoo-wl-style.css?ver=1.4';
$js_url = $base_url.'/wp-content/plugins/waitlist-woocommerce/assets/js/xoo-wl-js.js?ver=1.4';
$adminurl= $base_url.'/wp-admin/admin-ajax.php';
global $xoo_wl_gl_enguest_value,$appmaker_product_id,$xoo_wl_gl_bntxt_value,$xoo_wl_gl_enqty_value,$appmaker_user_id;
?>
<html>
<head>
    <link rel='stylesheet' id='xoo-wl-style-css'  href="<?php echo $css_url; ?>" type='text/css' media='all' />
    <script
            src="https://code.jquery.com/jquery-3.4.0.min.js"
            integrity="sha256-BJeo0qm959uMBGb65z40ejJYGSgR7REI4+CW1fNKwOg="
            crossorigin="anonymous"></script>
    <script type='text/javascript'>
        /* <![CDATA[ */
        var xoo_wl_localize = {"adminurl":"<?php echo $adminurl;?>","animation":"fade-in","allow_backorders":null,"e_empty_email":"Email address cannot be empty.","e_invalid_email":"Please enter valid email address.","e_min_qty":"Quantity field cannot be empty"};
        /* ]]> */
    </script>
    <script type='text/javascript' src="<?php echo $js_url; ?>"></script>

</head>
<body>
<div class="xoo-wl-container">
    <div class="xoo-wl-opac"></div>
    <div class="xoo-wl-modale">
        <div class="xoo-wl-inmodal">
            <div class="xoo-wl-plouter">
                <span class="xoo-wl-preloader xoo-wl-icon-spinner2"></span>
            </div>
            <span class="xoo-wl-close xoo-wl-icon-cancel"></span>
            <span class="xoo-wl-success"></span>
            <div class="xoo-wl-main">
                <?php
                if(!$appmaker_user_id && !$xoo_wl_gl_enguest_value){
                    $html  = '<span class="xoo-wl-nlogin">'.__('You need to Login for joining waitlist.','waitlist-woocommerce').'</span>';
                    $html .= '<div class="xoo-wl-myacc">';
                    $html .= '</div>';
                    echo $html;
                }else{
                ?>
                <div class="xoo-wl-info">
                    <span class="xoo-wl-mhead"><?php _e($xoo_wl_gl_bntxt_value,'waitlist-woocommerce'); ?></span>
                    <span class="xoo-wl-minfo"><?php _e('We will inform you when the product arrives in stock. Just leave your valid email address below.','waitlist-woocommerce'); ?></span>
                    <span class="xoo-wl-error"></span>
                </div>
                <form method="POST" action="" class="xoo-wl-form">
                    <span class="xwl-emlab"><?php _e('Email','waitlist-woocommerce'); ?></span>
                    <input type="text" name="xoo-wl-email" class='xoo-wl-email' value="">
                    <?php
                    if($xoo_wl_gl_enqty_value){

                        echo '<span class="xwl-qtlab">'.__('Quantity','waitlist-woocommerce').'</span>'.
                            '<input type="number" name="xoo-wl-qty" class="xoo-wl-qty" value="1">';
                    }
                    ?>
                    <input type="hidden" name="xoo-wl-form-id" value ="<?php echo $appmaker_product_id; ?>" class="xoo-wl-form-id">
                    <span class="xoo-wl-emsec"><?php _e('We won\'t share your address with anybody else.','waitlist-woocommerce'); ?></span>
                    <button name="xoo-wl-submit" class="xoo-wl-submit"><?php _e('Email me when available','waitlist-woocommerce'); ?></button>
                </form>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
</body>
</html>