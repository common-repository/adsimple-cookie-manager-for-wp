<?php
	
	class AS_CM_Helpers_Transfer {
		
		/**
		 * get link with params
		 *
		 * @param array  $params
		 * @param string $link
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 *
		 */
		public static function get_link_with_params( $params = [], $link = '' ) {
			return empty( $params ) ? $link : add_query_arg( $params, $link );
		}
		
		/**
		 * generate admin ajax link
		 *
		 * @param string      $action
		 * @param string|null $nonce_key
		 * @param  array      $args
		 *
		 *
		 * @return string
		 *
		 * @since  1.0.0
		 * @since  2.0.4 added $action, $nonce_key
		 *
		 */
		public static function admin_ajax_link( $action, $nonce_key = null, $args = [] ) {
			$args['action'] = $action;
			
			if ( ! is_null( $nonce_key ) ) {
				$args['nonce'] = wp_create_nonce( $nonce_key );
			}
			
			return self::get_link_with_params( $args, admin_url( 'admin-ajax.php' ) );
		}
		
		/**
		 * get response through wp_remote_request
		 *
		 * @param string $url
		 * @param array  $args
		 *
		 * @return boolean|string
		 *
		 * @since 1.0.0
		 *
		 */
		public static function get_remote_request( $url, $args ) {
			$response = wp_remote_request( $url, $args );
			
			if ( is_wp_error( $response ) ) {
				return false;
			}
			
			if ( $response['response']['code'] != 200 && $response['response']['code'] != 201 ) {
				return false;
			}
			
			$body = wp_remote_retrieve_body( $response );
			
			if ( empty( $body ) ) {
				return false;
			}
			
			return $body;
		}
		
		/**
		 * get response from server
		 *
		 * @param string  $url
		 * @param array   $data
		 * @param string  $method
		 * @param boolean $json
		 * @param int     $timeout
		 *
		 * @return boolean|array
		 *
		 * @since 1.0.0
		 *
		 */
		public static function send_request( $url, $data = [], $method = 'GET', $json = true, $timeout = 30 ) {
			$args = [];
			
			if ( $method != 'GET' ) {
				$args = [
					'method' => $method,
					'body'   => $data,
				];
			} else {
				$url = add_query_arg( $data, $url );
			}
			
			$args['timeout'] = $timeout;
			
			$response = self::get_remote_request( $url, $args );
			
			if ( $response === false ) {
				return false;
			}
			
			return $json ? json_decode( $response, true ) : $response;
		}
		
		/**
		 * redirect to URL
		 *
		 * @param string $to
		 * @param array  $variables
		 *
		 * @since 1.0.0
		 *
		 */
		public static function redirect( $to, $variables = [] ) {
			wp_redirect( self::get_link_with_params( $variables, $to ) );
			exit();
		}
		
		/**
		 * get environment from key
		 *
		 * @param string $key
		 *
		 * @return string
		 *
		 * @since 2.0.2
		 *
		 */
		public static function get_environment( $key ) {
			$environment = '';
			
			if ( empty( $key ) ) {
				return $environment;
			}
			
			$key = explode( '.', $key );
			
			if ( sizeof( $key ) == 1 ) {
				return $environment;
			}
			
			return $key[0];
		}
		
		/**
		 * remove environment type from key
		 *
		 * @param string $key
		 *
		 * @return string
		 *
		 * @since 2.0.2
		 *
		 */
		public static function remove_environment_type_from_key( $key ) {
			$key = explode( '.', $key );
			
			return isset( $key[1] ) ? $key[1] : $key[0];
		}
	}