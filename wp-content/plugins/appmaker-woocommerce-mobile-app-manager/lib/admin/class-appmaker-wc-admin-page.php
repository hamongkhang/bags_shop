<?php
global $access_token;
$this->options = get_option('appmaker_wc_settings');
$auto_login = false;
$button_name     = 'Manage App';
$manage_url_base = 'https://manage.appmaker.xyz';
$manage_url      = $manage_url_base . '/apps/?ref=wc-plugin-settings';
$site_url   = get_site_url();
$site_name = get_bloginfo('name');
?>
<div class="min-h-screen bg-gray-100 py-6 flex flex-col justify-center sm:py-12">
	<div class="relative py-3 sm:max-w-xl sm:mx-auto">
		<div class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200">
			<div class="px-4 py-5 sm:px-6">
				<div class="flex-1 min-w-0 flex items-center gap-2 divide-gray-300 divide-x"><img style="border-style:none" src="https://wc-image.appmaker.xyz/stateless-appmaker-pages-wp//2020/02/appmaker-logo-blue.svg" alt="Appmaker.xyz" class="h-8"><span class="pl-2">Settings</span></div>
			</div>
			<div class="px-4 py-5 sm:px-6">
				<h1 class="font-bold text-xl mb-1">🙏 Thank you for choosing Appmaker!</h1>
				<p class="text-base text-gray-700">Appmaker plugin helps you connect your website with your app.</p>
			</div>
			<?php
			if( ! empty( $this->options['project_id'] ) &&  !isset( $_GET['tab'] ) ) {
				$project_id =$this->options['project_id'];
			?>
				<div class="px-4 py-5 sm:p-6 flex flex-col items-center">
				<svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-500" viewBox="0 0 20 20" fill="currentColor">
				<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
				</svg>
				<h4 class="font-medium text-lg text-gray-900">Website connected with app</h4>
				<p class="text-center max-w-sm text-sm text-gray-500">You can now customize the app from appmaker dashboard to make it your own</p>
				<a href="https://manage.appmaker.xyz/apps/<?php echo $project_id?>">
				<button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mt-6">Go to dashboard</button>
			    </a>
				<a href="admin.php?page=appmaker-wc-admin&tab=step1">
				<button type="button" class="inline-flex items-center text-sm font-regular text-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-sm mt-3 hover:underline">View access key</button>
			    </a>	
			</div>
					
			
			<?php }elseif ( empty( $_GET['tab'] ) || 'step1' === $_GET['tab'] ){?>
			<div class="px-4 py-4 sm:px-6 text-base text-gray-700 bg-blue-50"><span class="bg-green-100 px-2 rounded-full text-green-800 font-semibold">Note</span> If you have already finished customizing your app from Appmaker Dashboard, copy the Access Key below and paste it into the dashboard.</div>
				<div class="px-4 py-5 sm:p-6">
					<p class="text-base text-gray-700">To get started with your app,</p>
					<ol class="list-decimal list-inside text-base text-gray-900 flex flex-col space-y-3 divide-gray-300 divide-y divide-dashed">
						<li class="pt-3">Click on <a class="font-semibold">Create App</a> button below.
							<div><a target="_blank" href="https://appmaker.xyz/create-app?ref=wc-plugin-settings<?php echo "&url=".$site_url."&site_name=".$site_name ?>"  class="focus:text-white hover:text-white inline-block ml-4 mt-2 items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Create App</a></div>
						</li>
						<li class="pt-3">Once you are done customing your app, click on <q class="font-semibold">Connect Website</q> button from the dashboard menu.</li>
						<li class="pt-3">Copy the access key you see below and paste it inside the dashboard when prompted.<div class="mt-2"><label for="api_key" class="block text-sm font-medium text-gray-700">Access Key</label>
								<div class="mt-1 relative">
									<textarea id="api_key" name="api_key" rows="6" class="shadow-sm focus:ring-blue-500 bg-gray-900 text-green-500 font-mono focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-lg p-2"><?php echo $access_key  ?></textarea>
									<button id="appmaker-copy-button" type="button" onClick="(function(){
											appmakerCopyTextToClipboard('<?php echo $access_key; ?>');
											jQuery('#appmaker-copy-button').text('Copied');
										})();" class="absolute bottom-2 left-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
										<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
											<path d="M8 2a1 1 0 000 2h2a1 1 0 100-2H8z"></path>
											<path d="M3 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v6h-4.586l1.293-1.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L10.414 13H15v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5zM15 11h2a1 1 0 110 2h-2v-2z"></path>
										</svg>Copy key</button>
								</div>
							</div>
						</li>
						<li class="pt-3">Click on <q class="font-semibold">Activate App</q> on dashboard</li>
						<li class="pt-3">You will now be able to see the website contents within your App! 🎉</li>
					</ol>
				</div>
		    </div>
			<?php } ?>
	</div>
</div>



<?php
if (isset($_GET['edit'])) {
?>
	<div class="wrapper">
		<div class="connect-app-box">
			<form method="post" action="admin.php?page=appmaker-wc-admin&tab=step2">
				<?php
				// This prints out all hidden setting fields.
				settings_fields('appmaker_wc_key_options');
				do_settings_sections('appmaker-wc-setting-admin');
				if ($error) {
					printf('<div class="text-danger"> You must fill in all of the fields. </div>');
				}
				if (isset($_GET['edit'])) {
					submit_button('Activate');
				}
				?>
			</form>
		</div>
	</div>
	<!--Final step/Success screen. Button will redirect to corresponding app-->
<?php
}
?>