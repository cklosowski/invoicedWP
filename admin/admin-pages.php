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

		$general_settings = is_array( get_option( 'iwp_settings_general' ) )    ? get_option( 'iwp_settings_general' )  	: array();
		$gateway_settings = is_array( get_option( 'iwp_settings_gateways' ) )   ? get_option( 'iwp_settings_gateways' ) 	: array();
		$email_settings   = is_array( get_option( 'iwp_settings_emails' ) )     ? get_option( 'iwp_settings_emails' )   	: array();
		$style_settings   = is_array( get_option( 'iwp_settings_styles' ) )     ? get_option( 'iwp_settings_styles' )   	: array();
		$tax_settings     = is_array( get_option( 'iwp_settings_taxes' ) )      ? get_option( 'iwp_settings_taxes' )    	: array();
		$ext_settings     = is_array( get_option( 'iwp_settings_extensions' ) ) ? get_option( 'iwp_settings_extensions' )	: array();
		$license_settings = is_array( get_option( 'iwp_settings_licenses' ) )   ? get_option( 'iwp_settings_licenses' )		: array();
		$misc_settings    = is_array( get_option( 'iwp_settings_misc' ) )       ? get_option( 'iwp_settings_misc' )			: array();

		$settings = array_merge( $general_settings, $gateway_settings, $email_settings, $style_settings, $tax_settings, $ext_settings, $license_settings, $misc_settings );

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
	$tabs['gateways'] = __( 'Payment Gateways', 'iwp' );
	$tabs['emails']   = __( 'Emails', 'iwp' );
	$tabs['styles']   = __( 'Styles', 'iwp' );
	$tabs['taxes']    = __( 'Taxes', 'iwp' );

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
		/** General Settings */
		'general' => apply_filters( 'iwp_settings_general',
			array(
				'test_mode' => array(
					'id' => 'test_mode',
					'name' => __( 'Test Mode', 'iwp' ),
					'desc' => __( 'While in test mode no live transactions are processed. To fully use test mode, you must have a sandbox (test) account for the payment gateway you are testing.', 'iwp' ),
					'type' => 'checkbox'
				),
				'purchase_page' => array(
					'id' => 'purchase_page',
					'name' => __( 'Checkout Page', 'iwp' ),
					'desc' => __( 'This is the checkout page where buyers will complete their purchases. The [download_checkout] short code must be on this page.', 'iwp' ),
					'type' => 'select',
                    'options' => iwp_get_pages(),
                    'select2' => true,
                    'placeholder' => __( 'Select a page', 'iwp' )
				),
				'success_page' => array(
					'id' => 'success_page',
					'name' => __( 'Success Page', 'iwp' ),
					'desc' => __( 'This is the page buyers are sent to after completing their purchases. The [iwp_receipt] short code should be on this page.', 'iwp' ),
					'type' => 'select',
					'options' => iwp_get_pages(),
                    'select2' => true,
                    'placeholder' => __( 'Select a page', 'iwp' )
				),
				'failure_page' => array(
					'id' => 'failure_page',
					'name' => __( 'Failed Transaction Page', 'iwp' ),
					'desc' => __( 'This is the page buyers are sent to if their transaction is cancelled or fails', 'iwp' ),
					'type' => 'select',
					'options' => iwp_get_pages(),
                    'select2' => true,
                    'placeholder' => __( 'Select a page', 'iwp' )
				),
				'purchase_history_page' => array(
					'id' => 'purchase_history_page',
					'name' => __( 'Purchase History Page', 'iwp' ),
					'desc' => __( 'This page shows a complete purchase history for the current user, including download links', 'iwp' ),
					'type' => 'select',
					'options' => iwp_get_pages(),
                    'select2' => true,
                    'placeholder' => __( 'Select a page', 'iwp' )
				),
				/*'base_country' => array(
					'id' => 'base_country',
					'name' => __( 'Base Country', 'iwp' ),
					'desc' => __( 'Where does your store operate from?', 'iwp' ),
					'type' => 'select',
                    'options' => iwp_get_country_list(),
                    'select2' => true,
                    'placeholder' => __( 'Select a country', 'iwp' )
				),*/
				'base_state' => array(
					'id' => 'base_state',
					'name' => __( 'Base State / Province', 'iwp' ),
					'desc' => __( 'What state / province does your store operate from?', 'iwp' ),
					'type' => 'shop_states',
                    'select2' => true,
                    'placeholder' => __( 'Select a state', 'iwp' )
				),
				'currency_settings' => array(
					'id' => 'currency_settings',
					'name' => '<strong>' . __( 'Currency Settings', 'iwp' ) . '</strong>',
					'desc' => __( 'Configure the currency options', 'iwp' ),
					'type' => 'header'
				),
				/*'currency' => array(
					'id' => 'currency',
					'name' => __( 'Currency', 'iwp' ),
					'desc' => __( 'Choose your currency. Note that some payment gateways have currency restrictions.', 'iwp' ),
					'type' => 'select',
                    'options' => iwp_get_currencies(),
                    'select2' => true
				),*/
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
				'api_settings' => array(
					'id' => 'api_settings',
					'name' => '<strong>' . __( 'API Settings', 'iwp' ) . '</strong>',
					'desc' => '',
					'type' => 'header'
				),
				'api_allow_user_keys' => array(
					'id' => 'api_allow_user_keys',
					'name' => __( 'Allow User Keys', 'iwp' ),
					'desc' => __( 'Check this box to allow all users to generate API keys. Users with the \'manage_shop_settings\' capability are always allowed to generate keys.', 'iwp' ),
					'type' => 'checkbox'
				),
				'tracking_settings' => array(
					'id' => 'tracking_settings',
					'name' => '<strong>' . __( 'Tracking Settings', 'iwp' ) . '</strong>',
					'desc' => '',
					'type' => 'header'
				),
				'allow_tracking' => array(
					'id' => 'allow_tracking',
					'name' => __( 'Allow Usage Tracking?', 'iwp' ),
					'desc' => __( 'Allow Easy Digital Downloads to anonymously track how this plugin is used and help us make the plugin better. Opt-in and receive a 20% discount code for any purchase from the <a href="https://easydigitaldownloads.com/extensions" target="_blank">Easy Digital Downloads store</a>. Your discount code will be emailed to you.', 'iwp' ),
					'type' => 'checkbox'
				),
				'uninstall_on_delete' => array(
					'id' => 'uninstall_on_delete',
					'name' => __( 'Remove Data on Uninstall?', 'iwp' ),
					'desc' => __( 'Check this box if you would like EDD to completely remove all of its data when the plugin is deleted.', 'iwp' ),
					'type' => 'checkbox'
				)
			)
		),
		/** Payment Gateways Settings */
		'gateways' => apply_filters('iwp_settings_gateways',
			array(
				/*'gateways' => array(
					'id' => 'gateways',
					'name' => __( 'Payment Gateways', 'iwp' ),
					'desc' => __( 'Choose the payment gateways you want to enable.', 'iwp' ),
					'type' => 'gateways',
					'options' => iwp_get_payment_gateways()
				),
				'default_gateway' => array(
					'id' => 'default_gateway',
					'name' => __( 'Default Gateway', 'iwp' ),
					'desc' => __( 'This gateway will be loaded automatically with the checkout page.', 'iwp' ),
					'type' => 'gateway_select',
					'options' => iwp_get_payment_gateways()
				),*/
				'accepted_cards' => array(
					'id' => 'accepted_cards',
					'name' => __( 'Accepted Payment Method Icons', 'iwp' ),
					'desc' => __( 'Display icons for the selected payment methods', 'iwp' ) . '<br/>' . __( 'You will also need to configure your gateway settings if you are accepting credit cards', 'iwp' ),
					'type' => 'payment_icons',
					'options' => apply_filters('iwp_accepted_payment_icons', array(
							'mastercard' => 'Mastercard',
							'visa' => 'Visa',
							'americanexpress' => 'American Express',
							'discover' => 'Discover',
							'paypal' => 'PayPal'
						)
					)
				),
				'paypal' => array(
					'id' => 'paypal',
					'name' => '<strong>' . __( 'PayPal Settings', 'iwp' ) . '</strong>',
					'desc' => __( 'Configure the PayPal settings', 'iwp' ),
					'type' => 'header'
				),
				'paypal_email' => array(
					'id' => 'paypal_email',
					'name' => __( 'PayPal Email', 'iwp' ),
					'desc' => __( 'Enter your PayPal account\'s email', 'iwp' ),
					'type' => 'text',
					'size' => 'regular'
				),
				'paypal_page_style' => array(
					'id' => 'paypal_page_style',
					'name' => __( 'PayPal Page Style', 'iwp' ),
					'desc' => __( 'Enter the name of the page style to use, or leave blank for default', 'iwp' ),
					'type' => 'text',
					'size' => 'regular'
				),
				'disable_paypal_verification' => array(
					'id' => 'disable_paypal_verification',
					'name' => __( 'Disable PayPal IPN Verification', 'iwp' ),
					'desc' => __( 'If payments are not getting marked as complete, then check this box. This forces the site to use a slightly less secure method of verifying purchases.', 'iwp' ),
					'type' => 'checkbox'
				)
			)
		),
		/** Emails Settings */
		'emails' => apply_filters('iwp_settings_emails',
			array(
				/*'email_template' => array(
					'id' => 'email_template',
					'name' => __( 'Email Template', 'iwp' ),
					'desc' => __( 'Choose a template. Click "Save Changes" then "Preview Purchase Receipt" to see the new template.', 'iwp' ),
					'type' => 'select',
					'options' => iwp_get_email_templates()
				),*/
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
				'purchase_subject' => array(
					'id' => 'purchase_subject',
					'name' => __( 'Purchase Email Subject', 'iwp' ),
					'desc' => __( 'Enter the subject line for the purchase receipt email', 'iwp' ),
					'type' => 'text',
					'std'  => __( 'Purchase Receipt', 'iwp' )
				),
				/*'purchase_receipt' => array(
					'id' => 'purchase_receipt',
					'name' => __( 'Purchase Receipt', 'iwp' ),
					'desc' => __('Enter the email that is sent to users after completing a successful purchase. HTML is accepted. Available template tags:', 'iwp') . '<br/>' . iwp_get_emails_tags_list(),
					'type' => 'rich_editor',
					'std'  => __( "Dear", "iwp" ) . " {name},\n\n" . __( "Thank you for your purchase. Please click on the link(s) below to download your files.", "iwp" ) . "\n\n{download_list}\n\n{sitename}"
				),*/
				'sale_notification_header' => array(
					'id' => 'sale_notification_header',
					'name' => '<strong>' . __('New Sale Notifications', 'iwp') . '</strong>',
					'desc' => __('Configure new sale notification emails', 'iwp'),
					'type' => 'header'
				),
				'sale_notification_subject' => array(
					'id' => 'sale_notification_subject',
					'name' => __( 'Sale Notification Subject', 'iwp' ),
					'desc' => __( 'Enter the subject line for the sale notification email', 'iwp' ),
					'type' => 'text',
					'std' => 'New download purchase - Order #{payment_id}'
				),
				/*'sale_notification' => array(
					'id' => 'sale_notification',
					'name' => __( 'Sale Notification', 'iwp' ),
					'desc' => __( 'Enter the email that is sent to sale notification emails after completion of a purchase. HTML is accepted. Available template tags:', 'iwp' ) . '<br/>' . iwp_get_emails_tags_list(),
					'type' => 'rich_editor',
					'std' => iwp_get_default_sale_notification_email()
				),*/
				'admin_notice_emails' => array(
					'id' => 'admin_notice_emails',
					'name' => __( 'Sale Notification Emails', 'iwp' ),
					'desc' => __( 'Enter the email address(es) that should receive a notification anytime a sale is made, one per line', 'iwp' ),
					'type' => 'textarea',
					'std'  => get_bloginfo( 'admin_email' )
				),
				'disable_admin_notices' => array(
					'id' => 'disable_admin_notices',
					'name' => __( 'Disable Admin Notifications', 'iwp' ),
					'desc' => __( 'Check this box if you do not want to receive emails when new sales are made.', 'iwp' ),
					'type' => 'checkbox'
				)
			)
		),
		/** Styles Settings */
		'styles' => apply_filters('iwp_settings_styles',
			array(
				'disable_styles' => array(
					'id' => 'disable_styles',
					'name' => __( 'Disable Styles', 'iwp' ),
					'desc' => __( 'Check this to disable all included styling of buttons, checkout fields, and all other elements.', 'iwp' ),
					'type' => 'checkbox'
				),
				'button_header' => array(
					'id' => 'button_header',
					'name' => '<strong>' . __( 'Buttons', 'iwp' ) . '</strong>',
					'desc' => __( 'Options for add to cart and purchase buttons', 'iwp' ),
					'type' => 'header'
				),
				/*'button_style' => array(
					'id' => 'button_style',
					'name' => __( 'Default Button Style', 'iwp' ),
					'desc' => __( 'Choose the style you want to use for the buttons.', 'iwp' ),
					'type' => 'select',
					'options' => iwp_get_button_styles()
				),
				'checkout_color' => array(
					'id' => 'checkout_color',
					'name' => __( 'Default Button Color', 'iwp' ),
					'desc' => __( 'Choose the color you want to use for the buttons.', 'iwp' ),
					'type' => 'color_select',
					'options' => iwp_get_button_colors()
				)*/
			)
		),
		/** Taxes Settings */
		'taxes' => apply_filters('iwp_settings_taxes',
			array(
				'enable_taxes' => array(
					'id' => 'enable_taxes',
					'name' => __( 'Enable Taxes', 'iwp' ),
					'desc' => __( 'Check this to enable taxes on purchases.', 'iwp' ),
					'type' => 'checkbox',
				),
				'tax_rates' => array(
					'id' => 'tax_rates',
					'name' => '<strong>' . __( 'Tax Rates', 'iwp' ) . '</strong>',
					'desc' => __( 'Enter tax rates for specific regions.', 'iwp' ),
					'type' => 'tax_rates'
				),
				'tax_rate' => array(
					'id' => 'tax_rate',
					'name' => __( 'Fallback Tax Rate', 'iwp' ),
					'desc' => __( 'Enter a percentage, such as 6.5. Customers not in a specific rate will be charged this rate.', 'iwp' ),
					'type' => 'text',
					'size' => 'small'
				),
				'prices_include_tax' => array(
					'id' => 'prices_include_tax',
					'name' => __( 'Prices entered with tax', 'iwp' ),
					'desc' => __( 'This option affects how you enter prices.', 'iwp' ),
					'type' => 'radio',
					'std' => 'no',
					'options' => array(
						'yes' => __( 'Yes, I will enter prices inclusive of tax', 'iwp' ),
						'no'  => __( 'No, I will enter prices exclusive of tax', 'iwp' )
					)
				),
				'display_tax_rate' => array(
					'id' => 'display_tax_rate',
					'name' => __( 'Display Tax Rate on Prices', 'iwp' ),
					'desc' => __( 'Some countries require a notice when product prices include tax.', 'iwp' ),
					'type' => 'checkbox',
				),
				'checkout_include_tax' => array(
					'id' => 'checkout_include_tax',
					'name' => __( 'Display during checkout', 'iwp' ),
					'desc' => __( 'Should prices on the checkout page be shown with or without tax?', 'iwp' ),
					'type' => 'select',
					'std' => 'no',
					'options' => array(
						'yes' => __( 'Including tax', 'iwp' ),
						'no'  => __( 'Excluding tax', 'iwp' )
					)
				)
			)
		),
		/** Extension Settings */
		'extensions' => apply_filters('iwp_settings_extensions',
			array()
		),
		'licenses' => apply_filters('iwp_settings_licenses',
			array()
		),
		/** Misc Settings */
		'misc' => apply_filters('iwp_settings_misc',
			array(
				'enable_ajax_cart' => array(
					'id' => 'enable_ajax_cart',
					'name' => __( 'Enable Ajax', 'iwp' ),
					'desc' => __( 'Check this to enable AJAX for the shopping cart.', 'iwp' ),
					'type' => 'checkbox',
					'std'  => '1'
				),
				'redirect_on_add' => array(
					'id' => 'redirect_on_add',
					'name' => __( 'Redirect to Checkout', 'iwp' ),
					'desc' => __( 'Immediately redirect to checkout after adding an item to the cart?', 'iwp' ),
					'type' => 'checkbox'
				),
				'enforce_ssl' => array(
					'id' => 'enforce_ssl',
					'name' => __( 'Enforce SSL on Checkout', 'iwp' ),
					'desc' => __( 'Check this to force users to be redirected to the secure checkout page. You must have an SSL certificate installed to use this option.', 'iwp' ),
					'type' => 'checkbox'
				),
				'logged_in_only' => array(
					'id' => 'logged_in_only',
					'name' => __( 'Disable Guest Checkout', 'iwp' ),
					'desc' => __( 'Require that users be logged-in to purchase files.', 'iwp' ),
					'type' => 'checkbox'
				),
				'show_register_form' => array(
					'id' => 'show_register_form',
					'name' => __( 'Show Register / Login Form?', 'iwp' ),
					'desc' => __( 'Display the registration and login forms on the checkout page for non-logged-in users.', 'iwp' ),
					'type' => 'select',
					'options' => array(
						'both' => __( 'Registration and Login Forms', 'iwp' ),
						'registration' => __( 'Registration Form Only', 'iwp' ),
						'login' => __( 'Login Form Only', 'iwp' ),
						'none' => __( 'None', 'iwp' )
					),
					'std' => 'none'
				),
				'item_quantities' => array(
					'id' => 'item_quantities',
					'name' => __('Item Quantities', 'iwp'),
					'desc' => __('Allow item quantities to be changed.', 'iwp'),
					'type' => 'checkbox'
				),
				'allow_multiple_discounts' => array(
					'id' => 'allow_multiple_discounts',
					'name' => __('Multiple Discounts', 'iwp'),
					'desc' => __('Allow customers to use multiple discounts on the same purchase?', 'iwp'),
					'type' => 'checkbox'
				),
				'enable_cart_saving' => array(
					'id' => 'enable_cart_saving',
					'name' => __( 'Enable Cart Saving', 'iwp' ),
					'desc' => __( 'Check this to enable cart saving on the checkout.', 'iwp' ),
					'type' => 'checkbox'
				),
				'field_downloads' => array(
					'id' => 'field_downloads',
					'name' => '<strong>' . __( 'File Downloads', 'iwp' ) . '</strong>',
					'desc' => '',
					'type' => 'header'
				),
				/*'download_method' => array(
					'id' => 'download_method',
					'name' => __( 'Download Method', 'iwp' ),
					'desc' => sprintf( __( 'Select the file download method. Note, not all methods work on all servers.', 'iwp' ), iwp_get_label_singular() ),
					'type' => 'select',
					'options' => array(
						'direct' => __( 'Forced', 'iwp' ),
						'redirect' => __( 'Redirect', 'iwp' )
					)
				),
				'symlink_file_downloads' => array(
					'id' => 'symlink_file_downloads',
					'name' => __( 'Symlink File Downloads?', 'iwp' ),
					'desc' => __( 'Check this if you are delivering really large files or having problems with file downloads completing.', 'iwp' ),
					'type' => 'checkbox'
				),
				'file_download_limit' => array(
					'id' => 'file_download_limit',
					'name' => __( 'File Download Limit', 'iwp' ),
					'desc' => sprintf( __( 'The maximum number of times files can be downloaded for purchases. Can be overwritten for each %s.', 'iwp' ), iwp_get_label_singular() ),
					'type' => 'number',
					'size' => 'small'
				),*/
				'download_link_expiration' => array(
					'id' => 'download_link_expiration',
					'name' => __( 'Download Link Expiration', 'iwp' ),
					'desc' => __( 'How long should download links be valid for? Default is 24 hours from the time they are generated. Enter a time in hours.', 'iwp' ),
					'type' => 'number',
					'size' => 'small',
					'std'  => '24',
					'min'  => '0'
				),
				'disable_redownload' => array(
					'id' => 'disable_redownload',
					'name' => __( 'Disable Redownload?', 'iwp' ),
					'desc' => __( 'Check this if you do not want to allow users to redownload items from their purchase history.', 'iwp' ),
					'type' => 'checkbox'
				),
				'accounting_settings' => array(
					'id' => 'accounting_settings',
					'name' => '<strong>' . __( 'Accounting Settings', 'iwp' ) . '</strong>',
					'desc' => '',
					'type' => 'header'
				),
				'enable_skus' => array(
					'id' => 'enable_skus',
					'name' => __( 'Enable SKU Entry', 'iwp' ),
					'desc' => __( 'Check this box to allow entry of product SKUs. SKUs will be shown on purchase receipt and exported purchase histories.', 'iwp' ),
					'type' => 'checkbox'
				),
				'enable_sequential' => array(
					'id' => 'enable_sequential',
					'name' => __( 'Sequential Order Numbers', 'iwp' ),
					'desc' => __( 'Check this box to sequential order numbers.', 'iwp' ),
					'type' => 'checkbox'
				),
				'sequential_start' => array(
					'id' => 'sequential_start',
					'name' => __( 'Sequential Starting Number', 'iwp' ),
					'desc' => __( 'The number that sequential order numbers should start at.', 'iwp' ),
					'type' => 'number',
					'size' => 'small',
					'std'  => '1'
				),
				'sequential_prefix' => array(
					'id' => 'sequential_prefix',
					'name' => __( 'Sequential Number Prefix', 'iwp' ),
					'desc' => __( 'A prefix to prepend to all sequential order numbers.', 'iwp' ),
					'type' => 'text'
				),
				'sequential_postfix' => array(
					'id' => 'sequential_postfix',
					'name' => __( 'Sequential Number Postfix', 'iwp' ),
					'desc' => __( 'A postfix to append to all sequential order numbers.', 'iwp' ),
					'type' => 'text',
				),
				'terms' => array(
					'id' => 'terms',
					'name' => '<strong>' . __( 'Terms of Agreement', 'iwp' ) . '</strong>',
					'desc' => '',
					'type' => 'header'
				),
				'show_agree_to_terms' => array(
					'id' => 'show_agree_to_terms',
					'name' => __( 'Agree to Terms', 'iwp' ),
					'desc' => __( 'Check this to show an agree to terms on the checkout that users must agree to before purchasing.', 'iwp' ),
					'type' => 'checkbox'
				),
				'agree_label' => array(
					'id' => 'agree_label',
					'name' => __( 'Agree to Terms Label', 'iwp' ),
					'desc' => __( 'Label shown next to the agree to terms check box.', 'iwp' ),
					'type' => 'text',
					'size' => 'regular'
				),
				'agree_text' => array(
					'id' => 'agree_text',
					'name' => __( 'Agreement Text', 'iwp' ),
					'desc' => __( 'If Agree to Terms is checked, enter the agreement terms here.', 'iwp' ),
					'type' => 'rich_editor'
				),
				'checkout_label' => array(
					'id' => 'checkout_label',
					'name' => __( 'Complete Purchase Text', 'iwp' ),
					'desc' => __( 'The button label for completing a purchase.', 'iwp' ),
					'type' => 'text',
					'std' => __( 'Purchase', 'iwp' )
				),
				'add_to_cart_text' => array(
					'id' => 'add_to_cart_text',
					'name' => __( 'Add to Cart Text', 'iwp' ),
					'desc' => __( 'Text shown on the Add to Cart Buttons.', 'iwp' ),
					'type' => 'text',
					'std'  => __( 'Add to Cart', 'iwp' )
				)
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
