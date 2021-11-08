<?php
$this->options = get_option( 'appmaker_wc_settings' );
if ( empty( $this->options['project_id'] ) ) {
    ?>
    <div class="main-box settings">
        <div class="box-header">
            <h3>What's next?</h3>
        </div>
        <div class="box-body">
            <p>To Activate the plugin, generate API keys from <u><b><a href="https://create.appmaker.xyz/" target="_blank">https://create.appmaker.xyz/</a></b></u> and paste below or copy generated  <i>API credential</i> from manage dashboard at <u><b><a href="https://beta.manage.appmaker.xyz/" target="_blank">https://beta.manage.appmaker.xyz/</a></u></b>.</p>
        </div>       
        
    </div>
<?php } ?>
    <div class="main-box api-detail">
        <div class="box-body">
            <form method="post" action="">
                <?php
                // This prints out all hidden setting fields.
                settings_fields( 'appmaker_wc_key_options' );
                do_settings_sections( 'appmaker-wc-setting-admin' );
                submit_button($name='Activate');
                ?>
            </form>
        </div>
    </div>
