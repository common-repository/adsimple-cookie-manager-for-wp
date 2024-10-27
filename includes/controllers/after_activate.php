<?php
	
	class AS_CM_Controllers_After_Activate extends AS_CM_Classes_Controller {
		protected static $instance = null;
		
		/**
		 * redirect option name
		 *
		 * @var string
		 *
		 * @since 1.0.0
		 *
		 */
		const REDIRECT_OPTION_NAME = 'after_activation';
		
		/**
		 * load hooks
		 *
		 * @since 1.0.0
		 *
		 */
		protected static function hooks() {
			add_action( 'admin_init',
			            array(
				            __CLASS__,
				            'redirect_after_activation'
			            ) );
		}
		
		/**
		 * redirect to options page after activation
		 *
		 * @since 1.0.0
		 *
		 */
		public static function redirect_after_activation() {
			if ( ! static::get_activation_key_value() ) {
				return;
			}
			
			static::deactivate();
			
			AS_CM_Helpers_Transfer::redirect( AS_CM_Controllers_Options::get_page_url() );
		}
		
		/**
		 * set activation key
		 *
		 * @since 1.0.0
		 *
		 */
		public static function set_activation_key() {
			update_option( static::get_redirect_option_name(), TRUE );
		}
		
		/**
		 * action on deactivate plugin
		 *
		 * @since 1.0.0
		 *
		 */
		public static function deactivate() {
			delete_option( static::get_redirect_option_name() );
		}
		
		/**
		 * get activation key value
		 *
		 * @return boolean
		 *
		 * @since 1.0.0
		 *
		 */
		public static function get_activation_key_value() {
			return (boolean) get_option( static::get_redirect_option_name(), FALSE );
		}
		
		/**
		 * get full name of redirect option
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 *
		 */
		public static function get_redirect_option_name() {
			return AS_CM_Controllers_Options::get_full_option_name( static::REDIRECT_OPTION_NAME );
		}
		
		private function __clone() {
		}
	}