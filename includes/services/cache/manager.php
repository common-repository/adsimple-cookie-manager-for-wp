<?php
	
	class AS_CM_Services_Cache_Manager extends AS_CM_Classes_Controller {
		
		protected static $instance = null;
		
		public static function hooks() {
			add_filter( AS_CM_Manager::$action . '_embed_code', [ __CLASS__, 'load_cache' ] );
			
			add_action( AS_CM_Manager::$action . '_options_page', [ __CLASS__, 'handler_request_clear_cache' ] );
		}
		
		/**
		 * @return string
		 *
		 * @since 2.0.6
		 */
		public static function get_action_clear_cache() {
			return AS_CM_Helpers_General::prepare_name( 'clear_cache' );
		}
		
		/**
		 * @since 2.0.6
		 */
		public static function handler_request_clear_cache() {
			if ( ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] != static::get_action_clear_cache() ) {
				return;
			}
			if ( ! static::is_cache_available() ) {
				AS_CM_Services_Notice::add_notice( 'status-clear-cache', __( "Cache disabled. You can not clear it.", AS_CM_Manager::$action ) );
				
				return;
			}
			
			if ( static::delete_cache() ) {
				AS_CM_Services_Notice::add_notice( 'status-clear-cache', __( "Cache was cleared.", AS_CM_Manager::$action ) );
			} else {
				AS_CM_Services_Notice::add_notice( 'status-clear-cache', __( "Error clear cache.", AS_CM_Manager::$action ) );
			}
		}
		
		/**
		 * @return false
		 *
		 * @since 2.0.6
		 */
		public static function is_cache_available() {
			if ( ! AS_CM_Controllers_Options::is_configured() ) {
				return false;
			}
			
			if ( ! AS_CM_Services_Cache_Loader::is_cache_dir_available() ) {
				return false;
			}
			
			$cache_options = AS_CM_Controllers_Options::get_option( 'cache' );
			
			if ( ! isset( $cache_options['available'] ) || ! $cache_options['available'] ) {
				return false;
			}
			
			return true;
		}
		
		/**
		 * @param string $embed_code
		 *
		 * @return string
		 *
		 * @since 2.0.6
		 */
		public static function load_cache( $embed_code ) {
			$links = AS_CM_Controllers_Options::get_option_embed_url();
			
			if ( empty( $links ) ) {
				return $embed_code;
			}
			
			if ( ! static::is_cache_available() ) {
				return $embed_code;
			}
			
			$cache_options = AS_CM_Controllers_Options::get_option( 'cache' );
			
			$constant = constant( $cache_options['period_type'] );
			if ( is_null( $constant ) ) {
				return $embed_code;
			}
			$timelife = $constant * $cache_options['period_value'];
			
			$is_expired = false;
			$cache      = '';
			
			if ( time() - $cache_options['timestamp'] > $timelife ) {
				$is_expired = true;
			}
			
			foreach ( $links as $link ) {
				$url_cache = static::load_cache_by_url( $link, $is_expired );
				$cache     .= $url_cache === false ? '' : $url_cache;
				unset( $url_cache );
			}
			
			if ( ! empty( $cache ) ) {
				//  fix for locale loading of ACM script
				$cache = 'eval(atob(\''.base64_encode( $cache ).'\'));';
				
				$cache = 'document.addEventListener(\'acmAfterDetectPosition\', function () { window.acm.in_head = true; }, false);' . "\r\n" . $cache;
				$cache
				       = 'document.addEventListener(\'acmAfterCheckAttr\', function () { window.acm.invalid_loading = false; if( window.acm.has_attr !== undefined ){ delete window.acm.has_attr; }}, false);'
				         . "\r\n" . $cache;
			}
			
			return '<script type="text/javascript" id="' . static::get_id_of_inline_script() . '">' . $cache . '</script>' . "\r\n";
		}
		
		/**
		 * @return string
		 *
		 * @since 2.0.6
		 */
		public static function get_id_of_inline_script() {
			return AS_CM_Helpers_General::prepare_name( '', false );
		}
		
		/**
		 * @return boolean
		 *
		 * @since 2.0.6
		 */
		public static function delete_cache() {
			if ( ! static::is_cache_available() ) {
				return false;
			}
			
			$links = AS_CM_Controllers_Options::get_option_embed_url();
			
			if ( empty( $links ) ) {
				return false;
			}
			
			$result = true;
			
			foreach ( $links as $link ) {
				$result = $result && static::delete_cache_by_url( $link );
			}
			
			if ( ! $result ) {
				return false;
			}
			
			do_action( AS_CM_Manager::$action . '_after_delete_cache' );
			
			return $result;
		}
		
		/**
		 * @param string $url
		 * @param boolean $is_expired
		 *
		 * @return string|false
		 *
		 * @since 2.0.6
		 */
		public static function load_cache_by_url( $url, $is_expired ) {
			$cache = new AS_CM_Services_Cache_Loader( $url );
			
			$need_cache_update = true;
			if ( $cache->is_cache_file_exist() && ! $is_expired ) {
				$need_cache_update = false;
			}
			
			if ( $need_cache_update ) {
				//  update cache
				$cache->load_from_external_url();
				$cache->save_to_file();
			}
			
			return $cache->get_cache();
		}
		
		/**
		 * @return string
		 *
		 * @since 2.0.6
		 */
		public static function get_clear_cache_url() {
			return ! static::is_cache_available() ? '' : AS_CM_Helpers_Transfer::get_link_with_params( [ 'action' => static::get_action_clear_cache() ], AS_CM_Controllers_Options::get_page_url() );
		}
		
		/**
		 * @param string $url
		 *
		 * @return string|false
		 *
		 * @since 2.0.6
		 */
		public static function delete_cache_by_url( $url ) {
			$cache = new AS_CM_Services_Cache_Loader( $url );
			
			return $cache->delete_cache();
		}
		
		private function __clone() {
		}
	}