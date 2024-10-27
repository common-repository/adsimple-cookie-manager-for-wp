<?php
	/*
	*  Plugin Name:     AdSimple Cookie Consent Manager
	*  Description:     Dieses Plugin bietet Ihnen die Möglichkeit in nur drei Schritten den AdSimple® Consent Manager auf Ihrer WordPress-Website einzubinden.
	*  Version:         2.0.13
	*  Author:          AdSimple
	*  Author URI:      https://adsimple.at
	*  Text Domain:     as_cm
	*  Domain Path:     /languages/
	*/
	
	add_action( 'plugins_loaded', [
			'AS_CM_Manager',
			'_instance',
		], 20 );
	
	register_activation_hook( __FILE__, [
			'AS_CM_Manager',
			'activate_plugin',
		] );
	
	register_deactivation_hook( __FILE__, [
			'AS_CM_Manager',
			'deactivate_plugin',
		] );
	
	class AS_CM_Manager {
		
		private static $_instance = null;
		
		/**
		 * path to plugin folder
		 *
		 * @var string
		 *
		 * @since 1.0.0
		 *
		 */
		static $path;
		
		/**
		 * URL to plugin folder
		 *
		 * @var string
		 *
		 * @since 1.0.0
		 *
		 */
		static $url;
		
		/**
		 * version of plugin
		 *
		 * @var string
		 *
		 * @since 1.0.0
		 *
		 */
		static $version;
		
		/**
		 * slug
		 *
		 * @var string
		 *
		 * @since 1.0.0
		 *
		 */
		static $action = '';
		
		/**
		 * version environment of PHP, WP
		 *
		 * @var array
		 *
		 * @since  1.0.0
		 *
		 */
		protected static $environment
			= [
				'PHP'       => '5.3',
				'WordPress' => '4.2.0',
			];
		
		/**
		 * status of checking environment
		 *
		 * @var null|string
		 *
		 * @since 1.0.0
		 *
		 */
		protected static $environment_status = null;
		
		private function __construct() {
			self::handler_loading_base_part();
			
			$locale = AS_CM_Services_Locale::_instance();
			
			add_action( 'plugins_loaded', [
					$locale,
					'load_textdomain',
				], 30 );
			
			add_action( 'plugins_loaded', [
					__CLASS__,
					'load_plugin_parts',
				], 60 );
		}
		
		/**
		 * load plugin parts
		 *
		 * @since 1.0.0
		 *
		 */
		public static function load_plugin_parts() {
			/* INIT CONTROLLERS */
			AS_CM_Helpers_General::load_controller( 'options', 'AS_CM_Controllers_Options' );
			AS_CM_Helpers_General::load_controller( 'popup', 'AS_CM_Controllers_Popup' );
			AS_CM_Helpers_General::load_controller( 'after_activate', 'AS_CM_Controllers_After_Activate' );
			AS_CM_Helpers_General::load_controller( 'design', 'AS_CM_Controllers_Design' );
			AS_CM_Helpers_General::load_controller( 'shortcodes', 'AS_CM_Controllers_Shortcodes' );
			AS_CM_Helpers_General::load_controller( 'extensions/rocket', 'AS_CM_Controllers_Extensions_Rocket' );
			/* END INIT CONTROLLERS */
			
			/* INIT SERVICES */
			AS_CM_Helpers_General::load_controller( '../services/notice', 'AS_CM_Services_Notice', [ 'function' => 'hooks' ] );
			AS_CM_Helpers_General::load_controller( '../services/cache/manager', 'AS_CM_Services_Cache_Manager', [ 'function' => 'hooks' ] );
			/* END INIT SERVICES */
		}
		
		/**
		 * load base logic of plugin
		 *
		 * @since 1.0.0
		 *
		 */
		protected static function load_base_logic() {
			/* LOAD HELPERS */
			require_once( self::$path . 'includes/helpers/general.php' );
			AS_CM_Helpers_General::load( 'helpers/transfer' );
			AS_CM_Helpers_General::load( 'helpers/view' );
			AS_CM_Helpers_General::load( 'helpers/file' );
			/* END LOAD HELPERS */
			
			/* LOAD CLASSES */
			AS_CM_Helpers_General::load( 'classes/collection' );
			AS_CM_Helpers_General::load( 'classes/controller' );
			AS_CM_Helpers_General::load( 'classes/item' );
			/* END LOAD CLASSES */
			
			/* LOAD MODELS */
			/* END LOAD MODELS */
			
			/* LOAD SERVICES */
			AS_CM_Helpers_General::load( 'services/locale' );
			AS_CM_Helpers_General::load( 'services/rest' );
			AS_CM_Helpers_General::load( 'services/notice' );
			AS_CM_Helpers_General::load( 'services/cache/loader' );
			AS_CM_Helpers_General::load( 'services/cache/manager' );
			/* END LOAD SERVICES */
			
			/* LOAD CONTROLLERS */
			AS_CM_Helpers_General::load( 'controllers/options' );
			AS_CM_Helpers_General::load( 'controllers/popup' );
			AS_CM_Helpers_General::load( 'controllers/after_activate' );
			AS_CM_Helpers_General::load( 'controllers/design' );
			/* END LOAD CONTROLLERS */
		}
		
		/**
		 * set general data
		 *
		 * @since 1.0.0
		 *
		 */
		private static function set_general_data() {
			self::$path = dirname( __FILE__ ) . '/';
			self::$url  = plugins_url( '', __FILE__ );
			
			self::$version = self::get_plugin_info( 'Version', __FILE__ );
			self::$action  = self::get_plugin_info( 'TextDomain', __FILE__ );
		}
		
		/**
		 * get information about plugin by type
		 *
		 * @param  string     $name Type of data field.
		 *                          Types https://codex.wordpress.org/File_Header
		 * @param string|null $path
		 *
		 * @return string
		 *
		 * @since  1.0.0
		 *
		 */
		public static function get_plugin_info( $name, $path = null ) {
			/** WordPress Plugin Administration API */
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			
			$data = get_plugin_data( is_null( $path ) ? __FILE__ : $path );
			
			if ( $name == 'Release' ) {
				$d = explode( '.', $data['Version'] );
				
				return $d[ sizeof( $d ) - 1 ];
			}
			
			return $data[ $name ];
		}
		
		/**
		 * deactivate plugin
		 *
		 * @since 1.0.0
		 *
		 */
		public static function deactivate_plugin() {
			self::handler_loading_base_part();
			self::deactivate();
		}
		
		/**
		 * handler loading base part of plugin
		 *
		 * @since 1.0.0
		 *
		 */
		public static function handler_loading_base_part() {
			self::set_general_data();
			self::check_environment();
			
			if ( self::get_environment_status() !== null ) {
				self::init_environment_error();
			}
			
			self::load_base_logic();
		}
		
		/**
		 * deactivate plugin logic
		 *
		 * @since 1.0.0
		 *
		 */
		public static function deactivate() {
			AS_CM_Controllers_Options::deactivate();
			AS_CM_Controllers_After_Activate::deactivate();
		}
		
		/**
		 * activate plugin
		 *
		 * @since 1.0.0
		 *
		 */
		public static function activate_plugin() {
			self::handler_loading_base_part();
			self::activate();
		}
		
		/**
		 * activate plugin on site
		 *
		 * @since 1.0.0
		 *
		 */
		public static function activate() {
			AS_CM_Controllers_After_Activate::set_activation_key();
		}
		
		/**
		 * get info about environment
		 *
		 * @since  1.0.0
		 *
		 */
		public static function check_environment() {
			global $wp_version;
			
			$flag = null;
			
			foreach (
				[
					'PHP'       => [ 'version' => PHP_VERSION ],
					'WordPress' => [ 'version' => $wp_version ],
				] as $key => $data
			) {
				
				$environment = self::get_environment( $key );
				
				if ( ! is_null( $environment ) ) {
					if ( isset( $data['class'] ) && ! class_exists( $data['class'] ) ) {
						self::$environment_status = $key;
						
						return;
					}
					
					if ( version_compare( $data['version'], $environment, '<' ) ) {
						self::$environment_status = $key;
						
						return;
					}
				}
			}
		}
		
		/**
		 * get environment status
		 *
		 * @return null|string
		 *
		 * @since 1.0.0
		 *
		 */
		public static function get_environment_status() {
			return self::$environment_status;
		}
		
		/**
		 * return environment by key
		 *
		 * @param  string $key
		 *
		 * @return string|null
		 *
		 * @since  1.0.0
		 *
		 */
		public static function get_environment( $key ) {
			return isset( self::$environment[ $key ] ) ? self::$environment[ $key ] : null;
		}
		
		/**
		 * init environment error
		 *
		 * @since 1.0.0
		 *
		 */
		protected static function init_environment_error() {
			$message = sprintf( __( '<p>The <strong>%s</strong> plugin requires %s version %s or greater.</p>', AS_CM_Manager::$action ), self::get_plugin_info( 'Name' ),
				self::get_environment_status(), self::get_environment( self::get_environment_status() ) );
			
			deactivate_plugins( plugin_basename( __FILE__ ) );
			
			wp_die( $message, __( 'Plugin Activation Error', AS_CM_Manager::$action ), [ 'back_link' => true ] );
		}
		
		private function __clone() {
		}
		
		public static function _instance() {
			if ( null === self::$_instance ) {
				self::$_instance = new self();
			}
			
			return self::$_instance;
		}
	}