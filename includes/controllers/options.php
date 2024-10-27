<?php
	
	class AS_CM_Controllers_Options extends AS_CM_Classes_Controller {
		
		/**
		 * slug for options page
		 *
		 * @var string
		 *
		 * @since  1.0.0
		 *
		 */
		const ADMIN_PAGE = 'settings';
		
		/**
		 * name of options
		 *
		 * @var string
		 *
		 * @since 1.0.0
		 *
		 */
		const OPTION_NAME = 'options';
		
		/**
		 * list of options values
		 *
		 * @var null|array
		 *
		 * @since 1.0.0
		 *
		 */
		protected static $values = null;
		
		protected static $instance = null;
		
		/**
		 * load hooks
		 *
		 * @since 1.0.0
		 *
		 */
		protected static function hooks() {
			add_action( 'admin_menu', [ __CLASS__, 'init_options_page' ], 5 );
			
			add_action( AS_CM_Manager::$action . '_render_dashboard_options', [ __CLASS__, 'add_options_page_content' ], 20 );
			
			add_action( 'admin_init', [ __CLASS__, 'save_fields' ], 50 );
			add_action( AS_CM_Manager::$action . '_save_options', [ __CLASS__, 'save_adsimple_id_after_install' ] );
			add_action( AS_CM_Manager::$action . '_save_options', [ __CLASS__, 'save_cache_options_fields' ], 20 );
			add_action( 'admin_init', [ __CLASS__, 'show_notice_if_empty_embed_code' ], 100 );
		}
		
		/**
		 * show notice if empty embed code
		 *
		 * @since 1.0.0
		 *
		 */
		public static function show_notice_if_empty_embed_code() {
			$embed_code = self::get_option_embed_code();
			
			if ( empty( $embed_code ) ) {
				AS_CM_Services_Notice::add_notice( 'empty-embed-code', __( "Please enter AdSimple® ID to configure plugin and inserting popup to site.", AS_CM_Manager::$action ),
					[ 'general', 'media', 'posts' ], [
						'href' => self::get_page_url(),
						'text' => __( 'Set AdSimple® ID', AS_CM_Manager::$action ),
					] );
			}
		}
		
		/**
		 * @since 2.0.6
		 */
		public static function save_adsimple_id_after_install() {
			$options = (array) static::get_option();
			$field   = static::get_fields( 'adsimple_id' );
			
			if ( ! isset( $_REQUEST[ $field['name'] ] ) ) {
				return;
			}
			
			$options[ $field['key'] ] = AS_CM_Helpers_General::esc_sql( $_REQUEST[ $field['name'] ] );
			
			if ( empty( $options[ $field['key'] ] ) ) {
				AS_CM_Services_Notice::add_notice( 'status-save-adsimple-id', __( "AdSimple® ID can not be empty.", AS_CM_Manager::$action ) );
				
				return;
			}
			
			$answer = AS_CM_Services_REST::get_embed_code( $options[ $field['key'] ], get_bloginfo( 'url' ) );
			
			if ( is_wp_error( $answer ) ) {
				$error_data = $answer->get_error_data( 'invalid_adsimple_id' );
				
				AS_CM_Services_Notice::add_notice( 'status-save-adsimple-id', $answer->get_error_message(), [ AS_CM_Manager::$action ], isset( $error_data['link'] ) ? $error_data['link'] : false );
				
				return;
			}
			
			$options['embed_code'] = $answer['result'];
			
			static::update_options( $options );
			
			try {
				AS_CM_Services_Notice::add_notice( 'status-save-adsimple-id', __( "We successfully saved your AdSimple® ID.", AS_CM_Manager::$action ) );
			} catch ( \Exception $exception ) {
				AS_CM_Services_Notice::add_notice( 'status-save-adsimple-id', $exception->getMessage() );
			}
		}
		
		/**
		 * @since 2.0.6
		 */
		public static function save_cache_options_fields() {
			if ( ! AS_CM_Controllers_Options::is_configured() ) {
				return;
			}
			
			$options = (array) static::get_option();
			
			$field             = static::get_fields( 'cache' );
			$data              = $field['default'];
			$data['timestamp'] = 0;
			$keys              = [];
			
			foreach ( array_keys( $data ) as $key ) {
				$name = AS_CM_Helpers_General::prepare_name( $field['key'] . '_' . $key );
				if ( isset( $_REQUEST[ $name ] ) ) {
					$keys[]       = $key;
					$data[ $key ] = AS_CM_Helpers_General::esc_sql( $_REQUEST[ $name ] );
				}
				unset( $name );
			}
			
			if ( empty( $keys ) ) {
				return;
			}
			
			$options['cache'] = $data;
			AS_CM_Services_Cache_Manager::delete_cache();
			
			static::update_options( $options );
			
			do_action( AS_CM_Manager::$action . '_after_save_cache_options' );
			
			AS_CM_Services_Notice::add_notice( 'status-save-cache-options', __( "We successfully saved cache options.", AS_CM_Manager::$action ) );
		}
		
		/**
		 * @since 1.0.0
		 * @since 2.0.6 added hook _save_options
		 */
		public static function save_fields() {
			global $pagenow;
			
			if ( $pagenow != 'admin.php' ) {
				return;
			}
			
			if ( ! isset( $_GET['page'] ) ) {
				return;
			}
			
			if ( $_GET['page'] != static::get_slug() ) {
				return;
			}
			
			if ( ! current_user_can( 'administrator' ) ) {
				AS_CM_Services_Notice::add_notice( 'status-save-adsimple-id', __( "You don't have access to make this.", AS_CM_Manager::$action ) );
				
				return;
			}
			
			do_action( AS_CM_Manager::$action . '_options_page' );
			
			if ( empty( $_POST ) ) {
				return;
			}
			
			do_action( AS_CM_Manager::$action . '_save_options' );
		}
		
		/**
		 * init options
		 */
		public static function init_options_page() {
			add_menu_page( __( 'Consent Manager', AS_CM_Manager::$action ), __( 'Consent Manager', AS_CM_Manager::$action ), 'manage_options', static::get_slug(),
				[ __CLASS__, 'render_options_page_content' ], AS_CM_Helpers_General::get_full_assets_url( 'images/icon', 'png' ), 65 );
		}
		
		/**
		 * render general wrapper for page content
		 *
		 * @since 1.0.0
		 *
		 */
		public static function render_options_page_content() {
			AS_CM_Helpers_View::get_template_part( 'options/wrapper' );
		}
		
		/**
		 * render options page content
		 */
		public static function add_options_page_content() {
			AS_CM_Helpers_View::get_template_part( 'options/content', [ 'field' => static::get_fields( 'adsimple_id' ) ] );
		}
		
		/**
		 * action on deactivate plugin
		 *
		 * @since 1.0.0
		 *
		 */
		public static function deactivate() {
			delete_option( static::get_options_name() );
		}
		
		/**
		 * get full option name
		 *
		 * @param string $option
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 *
		 */
		public static function get_full_option_name( $option ) {
			return AS_CM_Helpers_General::prepare_name( $option );
		}
		
		/**
		 * get full name of options
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 *
		 */
		public static function get_options_name() {
			return static::get_full_option_name( static::OPTION_NAME );
		}
		
		/**
		 * get embed code
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 *
		 */
		public static function get_option_embed_code() {
			$embed_code = static::get_option( 'embed_code' );
			if ( ! is_string( $embed_code ) ) {
				return '';
			}
			return $embed_code;
		}
		
		/**
		 * get option embed url
		 *
		 * @return array
		 *
		 * @since 2.0.2
		 * @since 2.0.5 changed to array
		 */
		public static function get_option_embed_url() {
			$count = preg_match_all( '/src=(["\'])(.*?)\1/', static::get_option_embed_code(), $match );
			
			return $count === false ? [] : ( isset( $match[2] ) ? $match[2] : [] );
		}
		
		/**
		 * get embed code
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 *
		 */
		public static function get_option_adsimple_id() {
			return (string) static::get_option( 'adsimple_id' );
		}
		
		/**
		 * @return boolean
		 *
		 * @since 2.0.6
		 */
		public static function is_configured() {
			$key  = static::get_option_adsimple_id();
			$code = static::get_option_embed_code();
			
			return ! empty( $key ) && ! empty( $code );
		}
		
		/**
		 * get dashboard slug
		 *
		 * @param string $name
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 *
		 */
		public static function get_slug( $name = '' ) {
			return AS_CM_Helpers_General::prepare_name( static::ADMIN_PAGE . ( ! empty( $name ) ? '_' . $name : '' ), false );
		}
		
		/**
		 * get page url
		 *
		 * @param string $name
		 * @param array  $args
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 *
		 */
		public static function get_page_url( $name = '', $args = [] ) {
			return AS_CM_Helpers_Transfer::get_link_with_params( array_merge( [
				'page' => static::get_slug( $name ),
			], $args ), admin_url( 'admin.php' ) );
		}
		
		/**
		 * get option value from DB
		 *
		 * @param  string $name
		 *
		 * @return string|mixed
		 *
		 * @since  1.0.0
		 *
		 */
		public static function get_option( $name = '' ) {
			static::load_options_values();
			
			if ( empty( $name ) ) {
				return static::$values;
			}
			
			return apply_filters( AS_CM_Manager::$action . '_option_value', isset( static::$values[ $name ] ) ? static::$values[ $name ] : static::get_default_value( $name ), $name );
		}
		
		/**
		 * load all options values for OPTION_NAME from DB
		 *
		 * @param boolean $reset
		 *
		 * @since 1.0.0
		 *
		 */
		protected static function load_options_values( $reset = false ) {
			if ( static::$values === null || $reset === true ) {
				static::$values = get_option( static::get_options_name(), [] );
			}
		}
		
		/**
		 * get default value of field
		 *
		 * @param string $name
		 *
		 * @return mixed|null
		 */
		public static function get_default_value( $name ) {
			foreach ( static::get_fields() as $field ) {
				if ( $field['key'] == $name && isset( $field['default'] ) ) {
					return $field['default'];
				}
			}
			
			return null;
		}
		
		/**
		 * get fields
		 *
		 * @param string $key
		 *
		 * @return array|null
		 *
		 * @since 1.0.0
		 *
		 */
		public static function get_fields( $key = '' ) {
			$fields = [
				[
					'key'     => 'adsimple_id',
					'label'   => __( 'AdSimple® ID', AS_CM_Manager::$action ),
					'attr'    => [
						'type' => 'text',
					],
					'default' => '',
				],
				[
					'key'     => 'cache',
					'label'   => '',
					'attr'    => [],
					'default' => [ 'available' => false, 'period_type' => 'HOUR_IN_SECONDS', 'period_value' => 24, 'timestamp' => 0 ],
				],
				[
					'key'     => 'embed_code',
					'label'   => __( 'Embed code', AS_CM_Manager::$action ),
					'attr'    => [
						'type' => 'textarea',
					],
					'default' => '',
				],
			];
			
			foreach ( $fields as $index => $field ) {
				$field['name']    = AS_CM_Helpers_General::prepare_name( $field['key'] );
				$fields[ $index ] = $field;
				
				if ( ! empty( $key ) && $field['key'] == $key ) {
					return $field;
				}
			}
			
			return empty( $key ) ? $fields : null;
		}
		
		private function __clone() {
		}
		
		/**
		 * update options in DB
		 *
		 * @param array $options
		 *
		 * @since 1.0.0
		 *
		 */
		public static function update_options( $options ) {
			self::$values = $options;
			
			update_option( static::get_options_name(), $options );
		}
		
		/**
		 * @return array
		 *
		 * @since 1.0.0
		 */
		public static function get_available_period_types() {
			return [
				'DAY_IN_SECONDS'    => __( 'days', AS_CM_Manager::$action ),
				'HOUR_IN_SECONDS'   => __( 'hours', AS_CM_Manager::$action ),
				'MINUTE_IN_SECONDS' => __( 'minutes', AS_CM_Manager::$action ),
			];
		}
	}