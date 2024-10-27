<?php
	
	class AS_CM_Services_REST extends AS_CM_Classes_Controller {
		/**
		 * REST server URL
		 *
		 * @var string
		 *
		 * @since 1.0.0
		 *
		 */
		const SERVER_URL = 'https://%s.adsimple.at/wp-json';
		
		/**
		 * link to consent manager service
		 *
		 * @var string
		 *
		 * @since 1.0.0
		 *
		 */
		const LINK_TO_COOKIE_MANAGER_SERVICE = 'https://%s.adsimple.at/dashboard/mein-consent-manager/';
		
		/**
		 * REST directory
		 *
		 * @var string
		 *
		 * @since 1.0.0
		 *
		 */
		const REST_DIRECTORY = 'as_csc';
		
		/**
		 * REST version
		 *
		 * @var string
		 *
		 * @since 1.0.0
		 *
		 */
		const REST_VERSION = 'v1';
		
		protected static $instance = null;
		
		/**
		 * get embed code for site
		 *
		 * @param string $key
		 * @param string $domain
		 * @param array  $types
		 *
		 * @return array|WP_Error
		 *
		 * @since 1.0.0
		 * @since 2.0.4 added types
		 *
		 */
		public static function get_embed_code( $key, $domain, $types = array( 'notice', 'list' ) ) {
			$data = self::send_request_to_server( 'embed_code',
			                                      $key,
			                                      array(
				                                      'id_key' => AS_CM_Helpers_Transfer::remove_environment_type_from_key( $key ),
				                                      'domain' => $domain,
				                                      'type'   => implode( ',', $types )
			                                      ) );
			
			if ( $data === FALSE || ! is_array( $data ) ) {
				return new WP_Error( 'error_loading',
				                     sprintf( __( 'Error loading data from %s. Please repeat your request later.',
				                                  AS_CM_Manager::$action ),
				                              '<a href="https://www.adsimple.at" target="_blank">adsimple.at</a>' ) );
			}
			
			switch ( $data['status'] ) {
				case 'without_pricing_plan':
					return new WP_Error( 'invalid_adsimple_id',
					                     sprintf( __( 'First you should select pricing plan on %s',
					                                  AS_CM_Manager::$action ),
					                              '<a href="' . static::get_prepared_url_based_on_environment( self::LINK_TO_COOKIE_MANAGER_SERVICE ) . '" target="_blank">adsimple.at</a>' ),
					                     array(
						                     'link' => array(
							                     'href'   => static::get_prepared_url_based_on_environment( self::LINK_TO_COOKIE_MANAGER_SERVICE ),
							                     'text'   => __( 'Select pricing plan', AS_CM_Manager::$action ),
							                     'target' => '_blank'
						                     )
					                     ) );
				case 'invalid_adsimple_id':
					return new WP_Error( 'invalid_adsimple_id', __( 'Invalid AdSimple® ID',
					                                                AS_CM_Manager::$action ) );
				case 'domain_not_exist':
					return new WP_Error( 'domain_not_exist', sprintf( __( "License doesn't include domain %s.",
						AS_CM_Manager::$action ),
						$domain ) );
				case 'success':
					if ( empty( $data['result'] ) ) {
						return new WP_Error( 'invalid_domain', sprintf( __( "Domain %s doesn't exist.",
						                                                    AS_CM_Manager::$action ),
						                                                $domain ) );
					}
					break;
			}
			
			return $data;
		}
		
		/**
		 * send request to REST server
		 *
		 * @param string      $method
		 * @param string|null $key
		 * @param array       $args
		 *
		 * @return boolean|array
		 *
		 * @since 1.0.0
		 * @since 2.0.2 added key
		 *
		 */
		public static function send_request_to_server( $method, $key = null, $args = array() ) {
			return AS_CM_Helpers_Transfer::send_request( self::get_full_url( $method, $key ), $args );
		}
		
		/**
		 * get full url
		 *
		 * @param string      $method
		 * @param string|null $key
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 * @since 2.0.2 added key
		 *
		 */
		public static function get_full_url( $method, $key = null ) {
			return static::get_prepared_url_based_on_environment( sprintf( '%s/%s/%s/%s',
			                                                               static::SERVER_URL,
			                                                               static::REST_DIRECTORY,
			                                                               static::REST_VERSION,
			                                                               $method ),
			                                                      $key );
		}
		
		/**
		 * get prepared url based on environment
		 *
		 * @param string      $url
		 * @param string|null $key
		 *
		 * @return string
		 *
		 * @since 2.0.2
		 */
		public static function get_prepared_url_based_on_environment( $url, $key = null ) {
			if ( is_null( $key ) ) {
				$key = AS_CM_Controllers_Options::get_option_adsimple_id();
			}
			
			$environment = AS_CM_Helpers_Transfer::get_environment( $key );
			
			return sprintf( $url, empty( $environment ) ? 'www' : $environment );
		}
		
		private function __clone() {
		}
	}