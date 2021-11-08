<?php

if (!defined('ABSPATH')) {
    exit;
}

$i = 0;
$number_of_steps = ($show_login_step) ? count($steps) + 1 : count($steps);

?>

<!-- <div class="top-navigation"><a id="navigation-item-address" class="navigation-item no-deco active">
    
<svg id="icon-address" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 6V22L8 18L16 22L23 18V2L16 6L8 2L1 6Z" stroke="#A9AEB7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            <path d="M8 2V18" stroke="#A9AEB7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            <path d="M16 6V22" stroke="#A9AEB7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg></a><a id="navigation-item-order" class="navigation-item no-deco "><svg id="icon-review" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.709 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.7649 14.1003 1.98232 16.07 2.85999" stroke="#A9AEB7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            <path d="M22 4L12 14.01L9 11.01" stroke="#A9AEB7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg></a><a id="navigation-item-payment" class="navigation-item no-deco "><svg id="icon-payment" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M21 4H3C1.89543 4 1 4.89543 1 6V18C1 19.1046 1.89543 20 3 20H21C22.1046 20 23 19.1046 23 18V6C23 4.89543 22.1046 4 21 4Z" stroke="#A9AEB7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            <path d="M1 10H23" stroke="#A9AEB7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg></a></div> -->

<!-- The steps tabs -->
<div class="appmakercheckout-tabs-wrapper">
    <ul class="appmakercheckout-tabs-list appmakercheckout-<?php echo $number_of_steps; ?>-tabs">
        <?php if ($show_login_step) : ?>
            <li class="appmakercheckout-tab-item current appmakercheckout-login">
                <div class="appmakercheckout-tab-number"><?php echo $i = $i + 1; ?></div>
                <div class="appmakercheckout-tab-text"><?php echo $options['t_login']; ?></div>
            </li>
        <?php endif; ?>
        <?php
        foreach ($steps as $_id => $_step) :
            $class = (!$show_login_step && $i == 0) ? ' current' : '';
        ?>
            <li class="appmakercheckout-tab-item<?php echo $class; ?> appmakercheckout-<?php echo $_id; ?>">
                <div class="appmakercheckout-tab-number"><?php echo $i = $i + 1; ?></div>
                <div class="appmakercheckout-tab-text"><?php echo $_step['title']; ?></div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>