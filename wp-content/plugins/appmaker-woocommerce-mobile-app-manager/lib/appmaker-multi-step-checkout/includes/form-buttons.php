<?php
/**
 * The buttons under the steps
 *
 * @package AppmakerCheckout
 */

defined( 'ABSPATH' ) || exit;

$buttons_class = apply_filters( 'appmakercheckout_buttons_class', 'button alt' );

?>

<!-- The steps buttons -->
<div class="fixed-footer">
	<button id="appmakercheckout-prev" class="<?php echo $buttons_class; // phpcs:ignore ?> button-inactive appmakercheckout-nav-button" type="button"><?php echo $options['t_previous']; // phpcs:ignore ?></button>
	<?php if ( $show_login_step ) : ?>
		<button id="appmakercheckout-skip-login" class="<?php echo $buttons_class; // phpcs:ignore ?> button-active current appmakercheckout-nav-button" type="button"><?php echo $options['t_skip_login']; // phpcs:ignore ?></button>
	<?php endif; ?>
	<button id="appmakercheckout-next" class="<?php echo $buttons_class; // phpcs:ignore ?> button-active current appmakercheckout-nav-button" type="button"><?php echo $options['t_next']; // phpcs:ignore ?></button>
	<button id="appmakercheckout-submit" style="display: none;" class="<?php echo $buttons_class; // phpcs:ignore ?> button-active current appmakercheckout-nav-button" type="button"><?php echo $options['t_next']; // phpcs:ignore ?></button>
</div>
