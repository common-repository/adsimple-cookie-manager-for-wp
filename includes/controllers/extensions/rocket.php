<?php
	
	class AS_CM_Controllers_Extensions_Rocket extends AS_CM_Classes_Controller {
		
		protected static $instance = null;
		
		/**
		 * load hooks
		 *
		 * @since 2.0.6
		 *
		 */
		protected static function hooks() {
			add_filter( 'rocket_excluded_inline_js_content', [ __CLASS__, 'exclude_cache_from_wp_rocket' ] );
			add_action( 'init', [ __CLASS__, 'init_task_add_exception_to_wp_rocket' ], 40 );
			add_action( AS_CM_Manager::$action . '_after_delete_cache', [ __CLASS__, 'auto_clear_cache' ] );
		}
		
		/**
		 * @since 2.0.6
		 */
		public static function auto_clear_cache() {
			if ( function_exists( 'rocket_clean_domain' ) ) {
				rocket_clean_domain();
			}
		}
		
		/**
		 * init task add exception to wp rocket
		 *
		 * @since 2.0.2
		 */
		public static function init_task_add_exception_to_wp_rocket() {
			$url = AS_CM_Controllers_Options::get_option_embed_url();
			
			if ( empty( $url ) ) {
				return;
			}
			
			add_filter( 'rocket_exclude_defer_js', [ __CLASS__, 'exclude_files' ] );
			add_filter( 'rocket_minify_excluded_external_js', [ __CLASS__, 'add_exception_to_wp_rocket' ] );
		}
		
		
		/**
		 * exclude files from wp rocket
		 *
		 * @param array $excluded_files
		 *
		 * @return array
		 *
		 * @since 2.0.2
		 * @since 2.0.5 changed to array of url
		 * @since 2.0.6 moved to separately controller
		 */
		public static function exclude_files( $excluded_files = [] ) {
			if ( ! is_array( $excluded_files ) ) {
				$excluded_files = [];
			}
			
			$excluded_files = array_merge( $excluded_files, AS_CM_Controllers_Options::get_option_embed_url() );
			
			return $excluded_files;
		}
		
		/**
		 * add exception to wp rocket
		 *
		 * @param  array $external_js_hosts Array of external domains
		 *
		 * @return array                    Extended array of external domains
		 *
		 * @since 2.0.2
		 * @since 2.0.5 changed to array of url
		 * @since 2.0.6 moved to separately controller
		 *
		 */
		public static function add_exception_to_wp_rocket( $external_js_hosts ) {
			if ( ! is_array( $external_js_hosts ) ) {
				$external_js_hosts = [];
			}
			
			$external_js_hosts = array_merge( $external_js_hosts, AS_CM_Controllers_Options::get_option_embed_url() );
			
			return $external_js_hosts;
		}
		
		/**
		 * @param array $pattern
		 *
		 * @return array
		 *
		 * @since 2.0.6
		 */
		public static function exclude_cache_from_wp_rocket( $pattern ) {
			$pattern[] = AS_CM_Services_Cache_Manager::get_id_of_inline_script();
			$pattern[] = 'window.acm';
			
			return $pattern;
		}
		
		private function __clone() {
		}
	}