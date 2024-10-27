<?php
	
	class AS_CM_Services_Cache_Loader extends AS_CM_Classes_Item {
		
		/**
		 * @var string
		 *
		 * @since 2.0.6
		 */
		const CACHE_FOLDER = 'cache';
		
		/**
		 * @var string
		 *
		 * @since 1.0.0
		 */
		protected $url;
		
		/**
		 * @var string
		 *
		 * @since 1.0.0
		 */
		protected $data;
		
		/**
		 * @param string $url
		 *
		 * @since 2.0.6
		 */
		public function __construct( $url ) {
			$this->set_url( $url );
		}
		
		/**
		 * @return bool
		 *
		 * @since 2.0.6
		 */
		public static function is_cache_dir_available() {
			$path = static::get_cache_dir_path();
			
			try {
				return (boolean) AS_CM_Helpers_File::check_folder_and_create( $path );
			} catch ( Exception $exception ) {
				return false;
			}
		}
		
		/**
		 * @since 2.0.6
		 */
		public static function get_cache_dir_path() {
			return sprintf( '%s/%s', AS_CM_Helpers_File::get_upload_dir(), static::CACHE_FOLDER );
		}
		
		/**
		 * @return string
		 *
		 * @since 2.0.6
		 */
		public function get_cache_path() {
			return sprintf( '%s/%s', static::get_cache_dir_path(), $this->get_cache_file_name() );
		}
		
		/**
		 * @return string
		 *
		 * @since 2.0.6
		 */
		public function get_file_name() {
			$full_name = '';
			try {
				$ar        = explode( '/', parse_url( $this->get_url(), PHP_URL_PATH ) );
				$full_name = array_pop( $ar );
			} catch ( Exception $exception ) {
			}
			
			if ( empty( $full_name ) ) {
				return '';
			}
			
			$full_name = pathinfo( $full_name );
			
			return ! empty( $full_name ) && isset( $full_name['filename'] ) ? $full_name['filename'] : '';
		}
		
		/**
		 * @return string
		 *
		 * @since 2.0.6
		 */
		public function get_cache_file_name() {
			return sprintf( '%s.txt', $this->get_file_name() );
		}
		
		/**
		 * @return boolean
		 *
		 * @since 2.0.6
		 */
		public function load_from_external_url() {
			$response = wp_remote_request( $this->get_url(), [ 'User-Agent' => 'AdSimple Cookie Manager Client/' . AS_CM_Manager::$version ] );
			
			if ( is_wp_error( $response ) ) {
				return false;
			}
			
			if ( wp_remote_retrieve_response_code( $response ) != 200 ) {
				return false;
			}
			
			$this->set_data( wp_remote_retrieve_body( $response ) );
			
			return true;
		}
		
		/**
		 * @return boolean
		 *
		 * @since 2.0.6
		 */
		public function save_to_file() {
			if ( ! static::is_cache_dir_available() ) {
				return false;
			}
			
			$data = $this->get_data();
			
			if ( empty( $data ) ) {
				return false;
			}
			
			$path = static::get_cache_dir_path();
			
			$result = AS_CM_Helpers_File::write_to_file( $path . '/' . $this->get_cache_file_name(), $data );
			
			if ( $result ) {
				static::save_timestamp_create_cache();
			}
			
			return $result;
		}
		
		/**
		 * @param string $url
		 *
		 * @since 2.0.6
		 */
		public function set_url( $url ) {
			$url = ltrim( $url, '/' );
			if ( ! preg_match( "~^(?:f|ht)tps?://~i", $url ) ) {
				$url = "http://" . $url;
			}
			
			$this->set_attr( 'url', $url );
		}
		
		/**
		 * @return string
		 *
		 * @since 2.0.6
		 */
		public function get_url() {
			return (string) $this->get_attr( 'url' );
		}
		
		/**
		 * @return string|false
		 *
		 * @since 2.0.6
		 */
		public function get_cache() {
			if ( ! $this->is_cache_file_exist() ) {
				return false;
			}
			
			return AS_CM_Helpers_File::read_file( $this->get_cache_path() );
		}
		
		/**
		 * @return boolean
		 *
		 * @since 2.0.6
		 */
		public function is_cache_file_exist() {
			return ! function_exists( 'file_exists' ) ? false : file_exists( $this->get_cache_path() );
		}
		
		/**
		 * @return string
		 *
		 * @since 2.0.6
		 */
		public function get_data() {
			return (string) $this->get_attr( 'data' );
		}
		
		/**
		 * @param string $data
		 *
		 * @since 2.0.6
		 */
		public function set_data( $data ) {
			$this->set_attr( 'data', $data );
		}
		
		/**
		 * @since 2.0.6
		 */
		public static function save_timestamp_create_cache() {
			$options = (array) AS_CM_Controllers_Options::get_option();
			
			if ( ! isset( $options['cache'] ) ) {
				return;
			}
			
			$options['cache']['timestamp'] = time();
			
			AS_CM_Controllers_Options::update_options( $options );
		}
		
		/**
		 * @return boolean
		 *
		 * @since 2.0.6
		 */
		public function delete_cache() {
			if ( $this->is_cache_file_exist() ) {
				return @unlink( $this->get_cache_path() );
			}
			
			return true;
		}
	}