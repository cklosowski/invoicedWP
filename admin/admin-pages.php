<?php
/**
 * Display the General settings tab
 * @return void
 */
function iwp_display_options() {
	global $iwp_options;
	//$license 	= get_option( '_iwp_license_key' );
	//$status 	= get_option( '_iwp_license_key_status' );

	$active_tab = isset( $_GET[ 'tab' ] ) && array_key_exists( $_GET['tab'], iwp_get_settings_tabs() ) ? $_GET[ 'tab' ] : 'general';

	ob_start();
	?>
	<div class="wrap">
		<h2 class="nav-tab-wrapper">
			<?php
			foreach( iwp_get_settings_tabs() as $tab_id => $tab_name ) {

				$tab_url = add_query_arg( array(
					'settings-updated' => false,
					'tab' => $tab_id
				) );

				$active = $active_tab == $tab_id ? ' nav-tab-active' : '';

				echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">';
					echo esc_html( $tab_name );
				echo '</a>';
			}
			?>
		</h2>
		<div id="tab_container">
			<form method="post" action="options.php">
				<table class="form-table">
				<?php
				settings_fields( 'iwp_settings' );
				do_settings_fields( 'iwp_settings_' . $active_tab, 'iwp_settings_' . $active_tab );
				?>
				</table>
				<?php submit_button(); ?>
			</form>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();

	do_action( 'iwp_general_settings_after' );

}

/**
 * Get Settings
 *
 * Retrieves all plugin settings
 *
 * @since 1.0
 * @return array EDD settings
 */
function iwp_get_settings() {

	$settings = get_option( 'iwp_settings' );

	if( empty( $settings ) ) {

		// Update old settings with new single option

		$general_settings 	= is_array( get_option( 'iwp_settings_general' ) )    ? get_option( 'iwp_settings_general' )  	: array();
		$business_settings 	= is_array( get_option( 'iwp_settings_business' ) )   ? get_option( 'iwp_settings_buisness' ) 	: array();
		$email_settings   	= is_array( get_option( 'iwp_settings_emails' ) )     ? get_option( 'iwp_settings_emails' )   	: array();
		$ext_settings     	= is_array( get_option( 'iwp_settings_extensions' ) ) ? get_option( 'iwp_settings_extensions' )	: array();
		$license_settings 	= is_array( get_option( 'iwp_settings_licenses' ) )   ? get_option( 'iwp_settings_licenses' )	: array();
		$misc_settings    	= is_array( get_option( 'iwp_settings_misc' ) )       ? get_option( 'iwp_settings_misc' )		: array();

		$settings = array_merge( $general_settings, $business_settings, $email_settings, $ext_settings, $license_settings, $misc_settings );

		update_option( 'iwp_settings', $settings );

	}
	return apply_filters( 'iwp_get_settings', $settings );
}

/**
 * Add all settings sections and fields
 *
 * @since 1.0
 * @return void
*/
function iwp_register_settings() {

	if ( false == get_option( 'iwp_settings' ) ) {
		add_option( 'iwp_settings' );
	}

	foreach( iwp_get_registered_settings() as $tab => $settings ) {

		add_settings_section(
			'iwp_settings_' . $tab,
			__return_null(),
			'__return_false',
			'iwp_settings_' . $tab
		);

		foreach ( $settings as $option ) {

			$name = isset( $option['name'] ) ? $option['name'] : '';

			add_settings_field(
				'iwp_settings[' . $option['id'] . ']',
				$name,
				function_exists( 'iwp_' . $option['type'] . '_callback' ) ? 'iwp_' . $option['type'] . '_callback' : 'iwp_missing_callback',
				'iwp_settings_' . $tab,
				'iwp_settings_' . $tab,
				array(
					'section'     => $tab,
					'id'          => isset( $option['id'] )          ? $option['id']      : null,
					'desc'        => ! empty( $option['desc'] )      ? $option['desc']    : '',
					'name'        => isset( $option['name'] )        ? $option['name']    : null,
					'size'        => isset( $option['size'] )        ? $option['size']    : null,
					'options'     => isset( $option['options'] )     ? $option['options'] : '',
					'std'         => isset( $option['std'] )         ? $option['std']     : '',
					'min'         => isset( $option['min'] )         ? $option['min']     : null,
					'max'         => isset( $option['max'] )         ? $option['max']     : null,
                    'step'        => isset( $option['step'] )        ? $option['step']    : null,
                    'select2'     => isset( $option['select2'] )     ? $option['select2'] : null,
                    'placeholder' => isset( $option['placeholder'] ) ? $option['placeholder'] : null
				)
			);
		}

	}

	// Creates our settings in the options table
	register_setting( 'iwp_settings', 'iwp_settings', 'iwp_settings_sanitize' );

}
add_action('admin_init', 'iwp_register_settings');

/**
 * Missing Callback
 *
 * If a function is missing for settings callbacks alert the user.
 *
 * @since 1.3.1
 * @param array $args Arguments passed by the setting
 * @return void
 */
function iwp_missing_callback($args) {
	printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'iwp' ), $args['id'] );
}


/**
 * Retrieve settings tabs
 *
 * @since 1.8
 * @return array $tabs
 */
function iwp_get_settings_tabs() {

	$settings = iwp_get_registered_settings();

	$tabs             = array();
	$tabs['general']  = __( 'General', 'iwp' );
	$tabs['business'] = __( 'Business Info', 'iwp' );
	$tabs['emails']   = __( 'Emails', 'iwp' );

	if( ! empty( $settings['extensions'] ) ) {
		$tabs['extensions'] = __( 'Extensions', 'iwp' );
	}
	if( ! empty( $settings['licenses'] ) ) {
		$tabs['licenses'] = __( 'Licenses', 'iwp' );
	}

	$tabs['misc']      = __( 'Misc', 'iwp' );

	return apply_filters( 'iwp_settings_tabs', $tabs );
}

/**
 * Retrieve the array of plugin settings
 *
 * @since 1.8
 * @return array
*/
function iwp_get_registered_settings() {

	/**
	 * 'Whitelisted' EDD settings, filters are provided for each settings
	 * section to allow extensions and other plugins to add their own settings
	 */
	$iwp_settings = array(
		//* General Settings */
		'general' => apply_filters( 'iwp_settings_general',
			array(

				'partial_payments' => array(
					'id' => 'partial_payments',
					'name' => __( 'Allow partial payments', 'iwp' ),
					'desc' => __( '', 'iwp' ),
					'type' => 'checkbox'
				),
				'partial_payment_default' => array(
					'id' => 'partial_payment_default',
					'name' => __( 'Partial payments allowed by default', 'iwp' ),
					'desc' => __( '', 'iwp' ),
					'type' => 'checkbox'
				),
				'show_recurring_billing' => array(
					'id' => 'show_recurring_billing',
					'name' => __( 'Show recurring billing options', 'iwp' ),
					'desc' => __( '', 'iwp' ),
					'type' => 'checkbox'
				),
				'enforce_https' => array(
					'id' => 'enforce_https',
					'name' => __( 'Enforce HTTPS on invoice pages', 'iwp' ),
					'desc' => __( '', 'iwp' ),
					'type' => 'checkbox'
				),
				'minimum_level' => array(
					'id' => 'minimum_level',
					'name' => __( 'Minimum user level to manage Invoices', 'iwp' ),
					'desc' => __( '', 'iwp' ),
					'type' => 'select',
					'options' => iwp_get_roles()
				),
				'currency_settings' => array(
					'id' => 'currency_settings',
					'name' => '<strong>' . __( 'Currency Settings', 'iwp' ) . '</strong>',
					'desc' => __( 'Configure the currency options', 'iwp' ),
					'type' => 'header'
				),
				'currency' => array(
					'id' => 'currency',
					'name' => __( 'Currency', 'iwp' ),
					'desc' => __( 'Choose your currency. Note that some payment gateways have currency restrictions.', 'iwp' ),
					'type' => 'select',
                    'options' => iwp_get_currencies(),
                    'select2' => true
				),
				'currency_position' => array(
					'id' => 'currency_position',
					'name' => __( 'Currency Position', 'iwp' ),
					'desc' => __( 'Choose the location of the currency sign.', 'iwp' ),
					'type' => 'select',
					'options' => array(
						'before' => __( 'Before - $10', 'iwp' ),
						'after' => __( 'After - 10$', 'iwp' )
					)
				),
				'thousands_separator' => array(
					'id' => 'thousands_separator',
					'name' => __( 'Thousands Separator', 'iwp' ),
					'desc' => __( 'The symbol (usually , or .) to separate thousands', 'iwp' ),
					'type' => 'text',
					'size' => 'small',
					'std' => ','
				),
				'decimal_separator' => array(
					'id' => 'decimal_separator',
					'name' => __( 'Decimal Separator', 'iwp' ),
					'desc' => __( 'The symbol (usually , or .) to separate decimal points', 'iwp' ),
					'type' => 'text',
					'size' => 'small',
					'std' => '.'
				),

			)
		),
		//* Business Settings */
		'business' => apply_filters('iwp_settings_business',
			array(
				'business_info' => array(
					'id' => 'business_info',
					'name' => '<strong>' . __( 'Business Information', 'iwp' ) . '</strong>',
					'desc' => __( 'Enter your businesses information below', 'iwp' ),
					'type' => 'header'
				),
				'business_name' => array(
					'id' => 'business_name',
					'name' => __( 'Business Name', 'iwp' ),
					'type' => 'text',
				),
				'business_address1' => array(
					'id' => 'address1',
					'name' => __( 'Address Line 1', 'iwp' ),
					'type' => 'text',
				),
				'business_address2' => array(
					'id' => 'address2',
					'name' => __( 'Address Line 2', 'iwp' ),
					'type' => 'text',
				),
				'business_city' => array(
					'id' => 'business_city',
					'name' => __( 'City', 'iwp' ),
					'type' => 'text',
				),
				'business_country' => array(
					'id' => 'business_country',
					'name' => __( 'Country', 'iwp' ),
					'desc' => __( 'Where does you operate from?', 'iwp' ),
					'type' => 'select',
                    'options' => iwp_get_country_list(),
                    'select2' => true,
                    'placeholder' => __( 'Select a country', 'iwp' )
				),
				'business_state' => array(
					'id' => 'business_state',
					'name' => __( 'State / Province', 'iwp' ),
					'desc' => __( 'What state / province does your store operate from?', 'iwp' ),
					'type' => 'shop_states',
                    'select2' => true,
                    'placeholder' => __( 'Select a state', 'iwp' )
				),
				'business_zip_code' => array(
					'id' => 'zip_code',
					'name' => __( 'Zip / Postal Code', 'iwp' ),
					'type' => 'number',
					'size' => 'small'
				),
				'registration_number' => array(
					'id' => 'registration_number',
					'name' => __( 'Registration Number', 'iwp' ),
					'type' => 'text',
				),
				'taxvat_number' => array(
					'id' => 'taxvat_number',
					'name' => __( 'Tax/VAT Number', 'iwp' ),
					'type' => 'text',
				),
			)
		),
		/** Email Settings */
		'emails' => apply_filters('iwp_settings_email',
			array(
				'email_logo' => array(
					'id' => 'email_logo',
					'name' => __( 'Logo', 'iwp' ),
					'desc' => __( 'Upload or choose a logo to be displayed at the top of the purchase receipt emails. Displayed on HTML emails only.', 'iwp' ),
					'type' => 'upload'
				),
				'email_settings' => array(
					'id' => 'email_settings',
					'name' => '',
					'desc' => '',
					'type' => 'hook'
				),
				'from_name' => array(
					'id' => 'from_name',
					'name' => __( 'From Name', 'iwp' ),
					'desc' => __( 'The name purchase receipts are said to come from. This should probably be your site or shop name.', 'iwp' ),
					'type' => 'text',
					'std'  => get_bloginfo( 'name' )
				),
				'from_email' => array(
					'id' => 'from_email',
					'name' => __( 'From Email', 'iwp' ),
					'desc' => __( 'Email to send purchase receipts from. This will act as the "from" and "reply-to" address.', 'iwp' ),
					'type' => 'text',
					'std'  => get_bloginfo( 'admin_email' )
				),

				'new_invoice_header' => array(
					'id' => 'new_invoice_header',
					'name' => '<strong>' . __('New Invoice Email Template', 'iwp') . '</strong>',
					'desc' => __('Configure new invoice notification emails', 'iwp'),
					'type' => 'header'
				),
				'new_invoice_subject' => array(
					'id' => 'new_invoice_subject',
					'name' => __( 'New Invoice Subject', 'iwp' ),
					'desc' => __( 'Enter the subject line for the new invoice email', 'iwp' ),
					'type' => 'text',
					'std'  => __( '[New Invoice] {subject}', 'iwp' )
				),
				'new_invoice' => array(
					'id' => 'new_invoice',
					'name' => __( 'New Invoice', 'iwp' ),
					'desc' => __('Enter the email that is sent to users after completing an invoice is created. HTML is accepted. Available template tags:', 'iwp') . '<br/>' . iwp_get_emails_tags_list(),
					'type' => 'rich_editor',
					'std'  => __( "Dear", "iwp" ) . " {name},\n\n" . __( "Thank you for your purchase. Please click on the link(s) below to download your files.", "iwp" ) . "\n\n{download_list}\n\n{sitename}"
				),
				'reminder_email_header' => array(
					'id' => 'reminder_email_header',
					'name' => '<strong>' . __('Reminder Email Template', 'iwp') . '</strong>',
					'desc' => __('Configure reminder email', 'iwp'),
					'type' => 'header'
				),
				'reminder_notification_subject' => array(
					'id' => 'reminder_notification_subject',
					'name' => __( 'Reminder Notification Subject', 'iwp' ),
					'desc' => __( 'Enter the subject line for the reminder notification email', 'iwp' ),
					'type' => 'text',
					'std' => '[Reminder] {subject}'
				),
				'reminder_notification' => array(
					'id' => 'reminder_notification',
					'name' => __( 'Reminder Notification', 'iwp' ),
					'desc' => __( 'Enter the email that is sent to reminder notification emails after completion of a purchase. HTML is accepted. Available template tags:', 'iwp' ) . '<br/>' . iwp_get_emails_tags_list(),
					'type' => 'rich_editor',
					'std' => iwp_get_default_sale_notification_email()
				),

				'receipt_email_header' => array(
					'id' => 'receipt_email_header',
					'name' => '<strong>' . __('Receipt Template', 'iwp') . '</strong>',
					'desc' => __('Configure receipt email', 'iwp'),
					'type' => 'header'
				),
				'receipt_email_subject' => array(
					'id' => 'receipt_email_subject',
					'name' => __( 'Receipt Email Subject', 'iwp' ),
					'desc' => __( 'Enter the subject line for the receipt email', 'iwp' ),
					'type' => 'text',
					'std' => '[Payment Received] {subject}'
				),
				'receipt_notification' => array(
					'id' => 'receipt_notification',
					'name' => __( 'Receipt Notification', 'iwp' ),
					'desc' => __( 'Enter the email that is sent to receipt notification emails after completion of a payment. HTML is accepted. Available template tags:', 'iwp' ) . '<br/>' . iwp_get_emails_tags_list(),
					'type' => 'rich_editor',
					'std' => iwp_get_default_sale_notification_email()
				),
				'quote_email_header' => array(
					'id' => 'quote_email_header',
					'name' => '<strong>' . __('Quote Template', 'iwp') . '</strong>',
					'desc' => __('Configure quote email', 'iwp'),
					'type' => 'header'
				),
				'quote_email_subject' => array(
					'id' => 'quote_email_subject',
					'name' => __( 'Quote Email Subject', 'iwp' ),
					'desc' => __( 'Enter the subject line for the quote email', 'iwp' ),
					'type' => 'text',
					'std' => '[Quote] {subject}'
				),
				'quote_notification' => array(
					'id' => 'quote_notification',
					'name' => __( 'Quote', 'iwp' ),
					'desc' => __( 'Enter the email that is sent to quote notification emails. HTML is accepted. Available template tags:', 'iwp' ) . '<br/>' . iwp_get_emails_tags_list(),
					'type' => 'rich_editor',
					'std' => iwp_get_default_sale_notification_email()
				),
				
				'disable_admin_notices' => array(
					'id' => 'disable_admin_notices',
					'name' => __( 'Disable Admin Notifications', 'iwp' ),
					'desc' => __( 'Check this box if you do not want to receive emails when new sales are made.', 'iwp' ),
					'type' => 'checkbox'
				)
			)
		),		
		/** Extension Settings */
		'extensions' => apply_filters('iwp_settings_extensions',
			array()
		),
		/** License Settings */
		'licenses' => apply_filters('iwp_settings_licenses',
			array()
		),
		/** Misc Settings */
		'misc' => apply_filters('iwp_settings_misc',
			array(

			)
		)
	);

	return apply_filters( 'iwp_registered_settings', $iwp_settings );
}

/**
 * Retrieve a list of all published pages
 *
 * On large sites this can be expensive, so only load if on the settings page or $force is set to true
 *
 * @since 1.9.5
 * @param bool $force Force the pages to be loaded even if not on settings
 * @return array $pages_options An array of the pages
 */
function iwp_get_pages( $force = false ) {

	$pages_options = array( '' => '' ); // Blank option

	if( ( ! isset( $_GET['page'] ) || 'iwp-settings' != $_GET['page'] ) && ! $force ) {
		return $pages_options;
	}

	$pages = get_pages();
	if ( $pages ) {
		foreach ( $pages as $page ) {
			$pages_options[ $page->ID ] = $page->post_title;
		}
	}

	return $pages_options;
}



/**
 * Display the System Info Tab
 * @return void
 */
function iwp_display_sysinfo() {
	global $wpdb;
	global $iwp_options;
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div><h2><?php _e( 'InvoicedWP - System Info', 'iwp-txt' ); ?></h2>
		<textarea style="font-family: Menlo, Monaco, monospace; white-space: pre" onclick="this.focus();this.select()" readonly cols="150" rows="35">
	SITE_URL:                 <?php echo site_url() . "\n"; ?>
	HOME_URL:                 <?php echo home_url() . "\n"; ?>

	IWP Version:             <?php echo IWP_VER . "\n"; ?>
	WordPress Version:        <?php echo get_bloginfo( 'version' ) . "\n"; ?>

	IWP SETTINGS:
	<?php
	foreach ( $iwp_options as $name => $value ) {
	if ( $value == false )
		$value = 'false';

	if ( $value == '1' )
		$value = 'true';

	echo $name . ': ' . maybe_serialize( $value ) . "\n";
	}
	?>

	ACTIVE PLUGINS:
	<?php
	$plugins = get_plugins();
	$active_plugins = get_option( 'active_plugins', array() );

	foreach ( $plugins as $plugin_path => $plugin ) {
		// If the plugin isn't active, don't show it.
		if ( ! in_array( $plugin_path, $active_plugins ) )
			continue;

	echo $plugin['Name']; ?>: <?php echo $plugin['Version'] ."\n";

	}
	?>

	CURRENT THEME:
	<?php
	if ( get_bloginfo( 'version' ) < '3.4' ) {
		$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );
		echo $theme_data['Name'] . ': ' . $theme_data['Version'];
	} else {
		$theme_data = wp_get_theme();
		echo $theme_data->Name . ': ' . $theme_data->Version;
	}
	?>


	Multi-site:               <?php echo is_multisite() ? 'Yes' . "\n" : 'No' . "\n" ?>

	ADVANCED INFO:
	PHP Version:              <?php echo PHP_VERSION . "\n"; ?>
	MySQL Version:            <?php echo mysql_get_server_info() . "\n"; ?>
	Web Server Info:          <?php echo $_SERVER['SERVER_SOFTWARE'] . "\n"; ?>

	PHP Memory Limit:         <?php echo ini_get( 'memory_limit' ) . "\n"; ?>
	PHP Post Max Size:        <?php echo ini_get( 'post_max_size' ) . "\n"; ?>
	PHP Time Limit:           <?php echo ini_get( 'max_execution_time' ) . "\n"; ?>

	WP_DEBUG:                 <?php echo defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' . "\n" : 'Disabled' . "\n" : 'Not set' . "\n" ?>

	WP Table Prefix:          <?php echo "Length: ". strlen( $wpdb->prefix ); echo " Status:"; if ( strlen( $wpdb->prefix )>16 ) {echo " ERROR: Too Long";} else {echo " Acceptable";} echo "\n"; ?>

	Show On Front:            <?php echo get_option( 'show_on_front' ) . "\n" ?>
	Page On Front:            <?php $id = get_option( 'page_on_front' ); echo get_the_title( $id ) . ' #' . $id . "\n" ?>
	Page For Posts:           <?php $id = get_option( 'page_on_front' ); echo get_the_title( $id ) . ' #' . $id . "\n" ?>

	Session:                  <?php echo isset( $_SESSION ) ? 'Enabled' : 'Disabled'; ?><?php echo "\n"; ?>
	Session Name:             <?php echo esc_html( ini_get( 'session.name' ) ); ?><?php echo "\n"; ?>
	Cookie Path:              <?php echo esc_html( ini_get( 'session.cookie_path' ) ); ?><?php echo "\n"; ?>
	Save Path:                <?php echo esc_html( ini_get( 'session.save_path' ) ); ?><?php echo "\n"; ?>
	Use Cookies:              <?php echo ini_get( 'session.use_cookies' ) ? 'On' : 'Off'; ?><?php echo "\n"; ?>
	Use Only Cookies:         <?php echo ini_get( 'session.use_only_cookies' ) ? 'On' : 'Off'; ?><?php echo "\n"; ?>

	UPLOAD_MAX_FILESIZE:      <?php if ( function_exists( 'phpversion' ) ) echo ini_get( 'upload_max_filesize' ); ?><?php echo "\n"; ?>
	POST_MAX_SIZE:            <?php if ( function_exists( 'phpversion' ) ) echo ini_get( 'post_max_size' ); ?><?php echo "\n"; ?>
	WordPress Memory Limit:   <?php echo WP_MEMORY_LIMIT; ?><?php echo "\n"; ?>
	DISPLAY ERRORS:           <?php echo ( ini_get( 'display_errors' ) ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A'; ?><?php echo "\n"; ?>
	FSOCKOPEN:                <?php echo ( function_exists( 'fsockopen' ) ) ? __( 'Your server supports fsockopen.', 'iwp-txt' ) : __( 'Your server does not support fsockopen.', 'iwp-txt' ); ?><?php echo "\n"; ?>
		</textarea>
	</div>
	<?php
}