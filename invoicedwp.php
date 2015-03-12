<?php
/**
 * Plugin Name:     Invoiced WP
 * Plugin URI:      invoicedwp.com
 * Description:     Create invoices and pay for them
 * Version:         1.0.0
 * Author:          WP Ronin
 * Author URI:      wp-ronin.com
 * Text Domain:     invoicewp
 *
 * @package         Invoiced WP
 * @author          Ryan Pletcher
 * @copyright       Copyright (c) 2015
 *
 * IMPORTANT! Ensure that you make the following adjustments
 * before releasing your extension:
 *
 * - Replace all instances of plugin-name with the name of your plugin.
 *   By WordPress coding standards, the folder name, plugin file name,
 *   and text domain should all match. For the purposes of standardization,
 *   the folder name, plugin file name, and text domain are all the
 *   lowercase form of the actual plugin name, replacing spaces with
 *   hyphens.
 *
 * - Replace all instances of Plugin_Name with the name of your plugin.
 *   For the purposes of standardization, the camel case form of the plugin
 *   name, replacing spaces with underscores, is used to define classes
 *   in your extension.
 *
 * - Replace all instances of PLUGINNAME with the name of your plugin.
 *   For the purposes of standardization, the uppercase form of the plugin
 *   name, removing spaces, is used to define plugin constants.
 *
 * - Replace all instances of Plugin Name with the actual name of your
 *   plugin. This really doesn't need to be anywhere other than in the
 *   EDD Licensing call in the hooks method.
 *
 * - Find all instances of @todo in the plugin and update the relevant
 *   areas as necessary.
 *
 * - All functions that are not class methods MUST be prefixed with the
 *   plugin name, replacing spaces with underscores. NOT PREFIXING YOUR
 *   FUNCTIONS CAN CAUSE PLUGIN CONFLICTS!
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'IWP' ) ) {

    /**
     * Main IWP class
     *
     * @since       1.0.0
     */
    class IWP { 


        /**
         * @var         IWP $instance The one true IWP
         * @since       1.0.0
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true IWP
         */
        public static function instance() {

            if( !self::$instance ) {
                self::$instance = new IWP();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
                self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {
            define( 'IWP_PATH', plugin_dir_path( __FILE__ ) );
            define( 'IWP_VERSION', '1.0.0' );
            define( 'IWP_FILE', plugin_basename( __FILE__ ) );
            define( 'IWP_URL', plugins_url( '', IWP_FILE ) );

        }


        /**
         * Include necessary files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {
            global $iwp_options;

            require_once IWP_PATH . 'admin/settings.php';
            //$iwp_options = iwp_get_settings();

            // Include scripts
            require_once IWP_PATH . 'includes/scripts.php';
            require_once IWP_PATH . 'includes/functions.php';

            require_once IWP_PATH . 'admin/functions.php';
            
            require_once IWP_PATH . 'admin/admin-pages.php';
            require_once IWP_PATH . 'admin/form-callbacks.php';
            require_once IWP_PATH . 'admin/meta.php';

            

            /**
             * @todo        The following files are not included in the boilerplate, but
             *              the referenced locations are listed for the purpose of ensuring
             *              path standardization in EDD extensions. Uncomment any that are
             *              relevant to your extension, and remove the rest.
             */
            // require_once IWP_PATH . 'includes/shortcodes.php';
            // require_once IWP_PATH . 'includes/widgets.php';
        }


        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         *
         * @todo        The hooks listed in this section are a guideline, and
         *              may or may not be relevant to your particular extension.
         *              Please remove any unnecessary lines, and refer to the
         *              WordPress codex and EDD documentation for additional
         *              information on the included hooks.
         *
         *              This method should be used to add any filters or actions
         *              that are necessary to the core of your extension only.
         *              Hooks that are relevant to meta boxes, widgets and
         *              the like can be placed in their respective files.
         *
         *              IMPORTANT! If you are releasing your extension as a
         *              commercial extension in the EDD store, DO NOT remove
         *              the license check!
         */
        private function hooks() {
            // Register settings
            //add_filter( 'edd_settings_extensions', array( $this, 'settings' ), 1 );

            add_action( 'init', 'iwp_setup_init' );
            
            if( is_admin() ) {    
                add_action( 'admin_menu', array( $this, 'iwp_setup_admin_menu' ), 1000, 0 );
                add_action( 'add_meta_boxes', array( $this, 'iwp_setup_admin_meta' ) );
                
                add_filter( 'manage_invoicedwp_posts_columns', 'iwp_custom_columns' );
                add_action( 'manage_posts_custom_column', 'iwp_display_custom_columns' );


            }
        }



        /**
         * Add the Pushover Notifications item to the Settings menu
         * @return void
         * @access public
         */
        public function iwp_setup_admin_menu() {
            global $iwp_dashboard_page, $iwp_settings_page,$iwp_sysinfo_page;

            $iwp_dashboard_page = add_submenu_page( 'edit.php?post_type=invoicedwp', __( 'Dashboard', 'iwp-txt' ), __( 'Dashboard', 'iwp-txt' ), 'manage_options', 'iwp-display-dashboard', 'iwp_display_dashboard' );
            $iwp_settings_page = add_submenu_page( 'edit.php?post_type=invoicedwp', __( 'Options', 'iwp-txt' ), __( 'Options', 'iwp-txt' ), 'manage_options', 'iwp-display-options', 'iwp_display_options' );
            $iwp_sysinfo_page = add_submenu_page( 'edit.php?post_type=invoicedwp', __( 'System Info', 'iwp-txt' ), __( 'System Info', 'iwp-txt' ), 'manage_options', 'iwp-system-info', 'iwp_display_sysinfo' );
        }


        /**
             * Add the Pushover Notifications item to the Settings menu
             * @return void
             * @access public
             */
            public function iwp_setup_admin_meta() {

                add_meta_box( 'iwp_client', __( 'Client Information' ), 'iwp_client', 'invoicedwp', 'side', 'low' );
                add_meta_box( 'iwp_details', __( 'Billing Details' ), 'iwp_details', 'invoicedwp', 'normal', 'low' );
                
            }



        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = IWP_PATH . '/languages/';
            $lang_dir = apply_filters( 'iwp_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'invoicedwp' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'invoicedwp', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/invoicedwp/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/edd-plugin-name/ folder
                load_textdomain( 'invoicedwp', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/edd-plugin-name/languages/ folder
                load_textdomain( 'invoicedwp', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'invoicedwp', false, $lang_dir );
            }
        }


        /**
         * Add settings
         *
         * @access      public
         * @since       1.0.0
         * @param       array $settings The existing EDD settings array
         * @return      array The modified EDD settings array
         */
        public function settings( $settings ) {
           
        }
        
        
        /*
    	 * Activation function fires when the plugin is activated.
    	 *
    	 * This function is fired when the activation hook is called by WordPress,
    	 * 
    	 */
    	public static function activation() {
        /*Activation functions here*/

        }

        /**
     * Register/Whitelist our settings on the settings page, allow extensions and other plugins to hook into this
     * @return void
     * @access public
     */
    public function iwp_register_settings() {
        register_setting( 'iwp-options', 'iwp_options' ); 

        do_action( 'iwp_register_additional_settings' );
    }

        
        
    }


/**
 * The main function responsible for returning the one true IWP
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \IWP The one true IWP
 *
 * @todo        Inclusion of the activation code below isn't mandatory, but
 *              can prevent any number of errors, including fatal errors, in
 *              situations where your extension is activated but EDD is not
 *              present.
 */
function IWP_load() {
        return IWP::instance();
}

/**
 * The activation hook is called outside of the singleton because WordPress doesn't
 * register the call from within the class hence, needs to be called outside and the
 * function also needs to be static.
 */
register_activation_hook( __FILE__, array( 'IWP', 'activation' ) );


add_action( 'plugins_loaded', 'IWP_load' );

} // End if class_exists check
