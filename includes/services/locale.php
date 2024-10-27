<?php
	
	class AS_CM_Services_Locale extends AS_CM_Classes_Controller {
		protected static $instance = null;
		
		/**
		 * current locale
		 *
		 * @var null|string
		 *
		 * @since 1.0.0
		 *
		 */
		protected static $current_locale = null;
		
		/**
		 * get current locale
		 *
		 * @return null|string
		 *
		 * @since 1.0.0
		 *
		 */
		public static function get_current_locale() {
			return self::$current_locale;
		}
		
		/**
		 * set current locale
		 *
		 * @param string $locale
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 *
		 */
		public static function set_current_locale( $locale ) {
			self::$current_locale = $locale;
			
			return $locale;
		}
		
		/**
		 * load textdomain for plugin
		 *
		 * @since 1.0.0
		 *
		 */
		public static function load_textdomain() {
			$locale = ( is_admin() && function_exists( 'get_user_locale' ) ) ? get_user_locale() : get_locale();
			$locale = apply_filters( 'locale', $locale );
			
			if ( in_array( $locale,
			               array(
				               'de_DE_formal',
				               'de_CH',
				               'de_CH_informal'
			               ) ) ) {
				$locale = 'de_DE';
			}
			
			return load_textdomain( AS_CM_Manager::$action,
			                        AS_CM_Helpers_General::get_full_path( 'languages/' . AS_CM_Manager::$action . '-' . $locale . '.mo' ) );
		}
		
		private function __clone() {
		}
	}