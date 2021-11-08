
<div class="logo">
	<a href="https://appmaker.xyz/woocommerce?utm_source=woocommerce-plugin&utm_medium=top-bar&utm_campaign=after-plugin-install"><img src="https://storage.googleapis.com/stateless-appmaker-pages-wp/2019/02/32357a5c-c65321b4-logo.png" alt="Appmaker.xyz"/></a>
</div>
<div class="navbar">
	<ul>
		<li><a href="admin.php?page=appmaker-wc-admin&tab=configure" class="current">Configure</a></li>
		<li><a href="admin.php?page=appmaker-wc-admin&tab=testimonial">Testimonials</a></li>
		<li><a href="admin.php?page=appmaker-wc-admin&tab=case-study">Case Study</li>
		<li><a href="admin.php?page=appmaker-wc-admin&tab=book-demo">Book Demo</a></li>
	</ul>
</div>
<div class="row">
	<div class="column main">
	   <?php
	   /*
	   if($_GET['tab']=='configure'){
		   include_once('class-appmaker-configure.php');
		   }*/
	   if ( ! empty( $_GET['tab'] ) ) {
		   if ( $_GET['tab'] == 'book-demo' ) {
			   include_once 'class-appmaker-bookdemo.php';
		   } elseif ( $_GET['tab'] == 'testimonial' ) {
			   include_once 'class-appmaker-testimonial.php';
		   } elseif ( $_GET['tab'] == 'case-study' ) {
			   include_once 'class-appmaker-casestudy.php';
		   } else {
			   include_once 'class-appmaker-configure.php';
		   }
	   } elseif ( ! empty( $_GET['page'] ) && $_GET['page'] == 'appmaker-wc-admin' ) {
				include_once 'class-appmaker-configure.php';
	   }
		?>
	</div>
	<div class="column side">
		<?php
		$this->options = get_option( 'appmaker_wc_settings' );
		if ( ! empty( $this->options['project_id'] ) ) {
			$auto_login      = false;
			$button_name     = 'Manage App';
			$manage_url_base = 'https://beta.manage.appmaker.xyz';
			$manage_url      = $manage_url_base . '/apps/' . $this->options['project_id'] . '/?utm_source=woocommerce-plugin&utm_medium=side-bar&utm_campaign=after-plugin-install';
			if ( $auto_login ) {
				$manage_url = site_url( '?rest_route=/appmaker-wc/v1/manage-login&url=' . $manage_url_base . '&return_to=' . '/apps/' . $this->options['project_id'] );
			}
		} else {
			$button_name = 'Visit site';
			$manage_url  = 'https://appmaker.xyz/woocommerce?utm_source=woocommerce-plugin&utm_medium=side-bar&utm_campaign=after-plugin-install';
		}
		?>
		<a href="<?php echo $manage_url; ?>" class="button-custom" target="_blank"><?php echo $button_name; ?></a>
		<div class="main-box support">
			<div class="box-header">
				<h3>Did you Know?</h3>
			</div>
			<div class="box-body ">
				<p>Read the case study of how one of our clients generated <b> over 80% </b> of sales through Mobile app alone.   <a href="https://blog.appmaker.xyz/ente-book-a-case-study/?utm_source=woocommerce-plugin&utm_medium=side-bar&utm_campaign=after-plugin-install" target="_blank">(Full case study)</a> </p>
				<a href="https://blog.appmaker.xyz/?utm_source=woocommerce-plugin&utm_medium=side-bar&utm_campaign=after-plugin-install" target="_blank">Blog</a>
				<a href="https://appmaker.xyz/woocommerce/pricing/?utm_source=woocommerce-plugin&utm_medium=side-bar&utm_campaign=after-plugin-install" target="_blank">Pricing</a>
				<a href="mailto:mail@appmaker.xyz?subject=WooCommerce Plugin Support" target="_top" class="box-header" style="padding: 0 0 10px 0">Email us</a>
				Follow us on :
				<ul class="social-media">
					<li><a href="https://www.facebook.com/appmaker.xyz" style="color: #3b5999;" target="_blank">Facebook</a></li>
					<li><a href="https://www.instagram.com/appmaker.xyz/" style="color: #e4405f;" target="_blank">Instagram</a></li>
					<li><a href="https://twitter.com/appmaker_xyz" style="color: #55acee;" target="_blank">Twitter</a></li>
					<li><a href="https://www.youtube.com/channel/UCYpPbibUUkhxA79dk215DdQ" style="color: #cd201f;" target="_blank">Youtube</a></li>
				</ul>
			</div>
		</div>
		<div class="main-box how-work">
			<div class="box-header">
				<h3>How do we work?</h3>
			</div>
			<div class="box-body">
				<p>You can either opt for DIY(Do it yourself) model or our App design team build a full plugin compatible app and upload it on Playstore/Appstore as per requirement for your store. In both case <u><b><a href="https://appmaker.xyz/book-a-demo/?utm_source=woocommerce-plugin&utm_medium=side-bar&utm_campaign=after-plugin-install" target="_blank">book demo</a></b></u> with us. </p>
			</div>
		</div>
	</div>
</div>

