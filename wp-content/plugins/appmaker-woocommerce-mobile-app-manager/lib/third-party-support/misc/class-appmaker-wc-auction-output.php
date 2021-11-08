<?php
/**
 * Auction bid
 *
 */

global $woocommerce, $post;
$product = $product_obj;
if(!(method_exists( $product, 'get_type') && $product->get_type() == 'auction')){
	return;
}
$current_user = wp_get_current_user();
$product_id =  $product->get_id();
$user_max_bid = $product->get_user_max_bid($product_id ,$current_user->ID );
$max_min_bid_text = $product->get_auction_type() == 'reverse' ? __( 'Your min bid is', 'wc_simple_auctions' ) : __( 'Your max bid is', 'wc_simple_auctions' );
$gmt_offset = get_option('gmt_offset') > 0 ? '+'.get_option('gmt_offset') : get_option('gmt_offset')

?>
	
<p class="auction-condition" style ='font-weight:bold'><?php echo apply_filters('conditiond_text', __( 'Item condition:', 'wc_simple_auctions' ), $product); ?><span class="curent-bid"> <?php  _e($product->get_condition(),'wc_simple_auctions' )  ?></span></p>

<?php if(($product->is_closed() === FALSE ) and ($product->is_started() === TRUE )) : ?>			


	<div class='auction-ajax-change' style ='font-weight:bold' >
	    
		<p class="auction-end"><?php echo apply_filters('time_left_text', __( 'Auction ends:', 'wc_simple_auctions' ), $product); ?> <?php echo  date_i18n( get_option( 'date_format' ),  strtotime( $product->get_auction_end_time() ));  ?>  <?php echo  date_i18n( get_option( 'time_format' ),  strtotime( $product->get_auction_end_time() ));  ?> <br/><?php printf(__('Timezone: %s','wc_simple_auctions') , get_option('timezone_string') ? get_option('timezone_string') : __('UTC ','wc_simple_auctions').$gmt_offset) ?>
		</p>

		<?php if ($product->get_auction_sealed() != 'yes'){ ?>
		    <p class="auction-bid"><?php echo $product->get_price_html() ?> </p>
			
			<?php if(($product->is_reserved() === TRUE) &&( $product->is_reserve_met() === FALSE )  ) : ?>
				<p class="reserve hold"  data-auction-id="<?php echo esc_attr( $product_id ); ?>" ><?php echo apply_filters('reserve_bid_text', __( "Reserve price has not been met", 'wc_simple_auctions' )); ?></p>
			<?php endif; ?>	
			
			<?php if(($product->is_reserved() === TRUE) &&( $product->is_reserve_met() === TRUE )  ) : ?>
				<p class="reserve free"  data-auction-id="<?php echo esc_attr( $product_id ); ?>"><?php echo apply_filters('reserve_met_bid_text', __( "Reserve price has been met", 'wc_simple_auctions' )); ?></p>
			<?php endif; ?>
		<?php } elseif($product->get_auction_sealed() == 'yes'){?>
				<p class="sealed-text"><?php echo apply_filters('sealed_bid_text', __( "This auction is <a href='#'>sealed</a>.", 'wc_simple_auctions' )); ?>
					<span class='sealed-bid-desc' style="display:none;"><?php _e( "In this type of auction all bidders simultaneously submit sealed bids so that no bidder knows the bid of any other participant. The highest bidder pays the price they submitted. If two bids with same value are placed for auction the one which was placed first wins the auction.", 'wc_simple_auctions' ) ?></span>
				</p>
				<?php 
				if (!empty($product->get_auction_start_price())) {?>
					<?php if($product->get_auction_type() == 'reverse' ) : ?>
							<p class="sealed-min-text"><?php echo apply_filters('sealed_min_text', sprintf(__( "Maximum bid for this auction is %s.", 'wc_simple_auctions' ), wc_price($product ->get_auction_start_price()))); ?></p>
					<?php else : ?>
							<p class="sealed-min-text"><?php echo apply_filters('sealed_min_text', sprintf(__( "Minimum bid for this auction is %s.", 'wc_simple_auctions' ), wc_price($product ->get_auction_start_price()))); ?></p>			
					<?php endif; ?>			
				<?php } ?>	
		<?php } ?>	

		<?php if($product->get_auction_type() == 'reverse' ) : ?>
			<p class="reverse"><?php echo apply_filters('reverse_auction_text', __( "This is reverse auction.", 'wc_simple_auctions' )); ?></p>
		<?php endif; ?>	
		<?php if ($product->get_auction_sealed() != 'yes'){ ?>
			<?php if ($product->get_auction_proxy() &&  $product->get_auction_max_current_bider() && get_current_user_id() == $product->get_auction_max_current_bider()) {?>

				<p class="max-bid"><?php  _e( $max_min_bid_text , 'wc_simple_auctions' ) ?> <?php echo wc_price($product->get_auction_max_bid()) ?>
			<?php } ?>
		<?php } elseif($user_max_bid > 0){ ?>
			<p class="max-bid"><?php  _e( $max_min_bid_text , 'wc_simple_auctions' ) ?> <?php echo wc_price($user_max_bid) ?>
		<?php } ?>	
		<?php do_action('woocommerce_before_bid_form'); ?>		
	</div>			 	

<?php elseif (($product->is_closed() === FALSE ) and ($product->is_started() === FALSE )):?>
	
	<div class="auction-time future" id="countdown"><?php echo apply_filters('auction_starts_text', __( 'Auction starts in:', 'wc_simple_auctions' ), $product); ?> 
		<div class="auction-time-countdown future" data-time="<?php echo $product->get_seconds_to_auction() ?>" data-format="<?php echo get_option( 'simple_auctions_countdown_format' ) ?>"></div>
	</div>
	
	<p class="auction-starts"><?php echo apply_filters('time_text', __( 'Auction starts:', 'wc_simple_auctions' ), $product_id); ?> <?php echo  date_i18n( get_option( 'date_format' ),  strtotime( $product->get_auction_start_time() ));  ?>  <?php echo  date_i18n( get_option( 'time_format' ),  strtotime( $product->get_auction_start_time() ));  ?></p>
	<p class="auction-end"><?php echo apply_filters('time_text', __( 'Auction ends:', 'wc_simple_auctions' ), $product_id); ?> <?php echo  date_i18n( get_option( 'date_format' ),  strtotime( $product->get_auction_end_time() ));  ?>  <?php echo  date_i18n( get_option( 'time_format' ),  strtotime( $product->get_auction_end_time() ));  ?> </p>
	
<?php endif; ?>