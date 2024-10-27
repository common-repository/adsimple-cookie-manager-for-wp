<?php
	
	class AS_CM_Services_Notice {
		/**
		 * data for loading in notice
		 *
		 * @var array
		 *
		 * @since  1.0.0
		 *
		 */
		protected $args = array();
		
		/**
		 * pages for show admin notice
		 *
		 * @var array
		 *
		 * @since  1.0.0
		 *
		 */
		protected $areas = array();
		
		/**
		 * key of notice block
		 *
		 * @var string
		 *
		 * @since  1.0.0
		 *
		 */
		protected $key = '';
		
		/**
		 * action for dismiss notice
		 *
		 * @var string
		 *
		 * @since  1.0.0
		 *
		 */
		public static $action = 'dismiss_notice';
		
		/**
		 * name of field for user meta
		 *
		 * @var string
		 *
		 * @since  1.0.0
		 *
		 */
		protected static $dismissed_field_key = 'dismissed_notices';
		
		/**
		 * nonce for dismiss notice
		 *
		 * @var string
		 *
		 * @since  1.0.0
		 *
		 */
		protected static $nonce = 'dismiss_notice';
		
		/**
		 * list available pages by groups
		 *
		 * @var array|null
		 *
		 * @since 1.0.0
		 *
		 */
		protected static $available_pages = null;
		
		public function __construct( $key, $args = array(), $areas = array() ) {
			$this->args  = $args;
			$this->areas = $areas;
			$this->key   = $key;
			
			add_action( 'admin_notices', array( $this, 'notice' ) );
		}
		
		/**
		 * load hooks
		 *
		 * @since 1.0.0
		 *
		 */
		public static function hooks() {
			add_action( 'wp_ajax_' . static::get_action_key(), array( __CLASS__, 'handler_dismiss' ) );
		}
		
		/**
		 * render notice block
		 *
		 * @since 1.0.0
		 *
		 */
		public function notice() {
			if ( ! static::check_area( $this->areas, $this->key ) ) {
				return;
			}
			
			AS_CM_Helpers_View::get_template_part( 'options/notice',
			                                       array_merge( $this->args,
			                                                    array(
				                                                    'dismiss_url' => AS_CM_Helpers_Transfer::admin_ajax_link( static::get_action_key(), static::get_nonce_key(), array( 'key' => $this->key ) )
			                                                    ) ) );
		}
		
		/**
		 * get action key
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 *
		 */
		public static function get_action_key() {
			return AS_CM_Helpers_General::prepare_name( static::$action );
		}
		
		/**
		 * get nonce key
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 *
		 */
		public static function get_nonce_key() {
			return AS_CM_Helpers_General::prepare_name( static::$nonce );
		}
		
		/**
		 * get available pages by group
		 *
		 * @param string|null $area
		 *
		 * @since 1.0.0
		 *
		 * @return array
		 *
		 */
		public static function get_available_pages( $area = null ) {
			if ( is_null( static::$available_pages ) ) {
				$pages = array(
					'general'              => array(
						'plugins.php',
						'index.php',
						'options-general.php',
						'users.php'
					),
					AS_CM_Manager::$action => array(
						array(
							'page' => 'admin.php',
							'attr' => array( 'page' => AS_CM_Controllers_Options::get_slug() )
						)
					),
					'dashboard'            => array( 'index.php' ),
					'media'                => array( 'upload.php' ),
					'posts'                => array( 'edit.php' )
				);
				
				static::$available_pages = apply_filters( AS_CM_Manager::$action . '_notice_available_pages', $pages );
			}
			
			if ( $area == 'all' ) {
				return array_unique( call_user_func_array( 'array_merge', array_values( static::$available_pages ) ), SORT_REGULAR );
			}
			
			if ( is_null( $area ) ) {
				return static::$available_pages;
			}
			
			return isset( static::$available_pages[ $area ] ) ? static::$available_pages[ $area ] : null;
		}
		
		/**
		 * dismiss block for user
		 *
		 * @since 1.0.0
		 *
		 */
		public static function handler_dismiss() {
			if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], static::get_nonce_key() ) ) {
				die();
			}
			
			if ( ! isset( $_REQUEST['key'] ) || empty( $_REQUEST['key'] ) ) {
				die();
			}
			
			$user_id = get_current_user_id();
			
			if ( ! $user_id ) {
				die();
			}
			
			echo static::add_to_dismissed( htmlspecialchars( $_REQUEST['key'] ), $user_id ) ? 'success' : 'error';
			exit();
		}
		
		/**
		 * check area for showing notice block
		 *
		 * @param  array  $areas
		 * @param  string $key
		 *
		 * @return boolean
		 *
		 * @since 1.0.0
		 *
		 */
		public static function check_area( $areas, $key = null ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				return FALSE;
			}
			
			global $pagenow;
			
			foreach ( $areas as $area ) {
				
				$pages = static::get_available_pages( $area );
				if ( empty( $pages ) ) {
					continue;
				}
				
				foreach ( $pages as $page ) {
					if ( ! is_array( $page ) ) {
						$page = array( 'page' => $page, 'attr' => array() );
					}
					
					if ( $pagenow != $page['page'] ) {
						continue;
					}
					
					if ( ! empty( $page['attr'] ) ) {
						$flag = TRUE;
						
						foreach ( $page['attr'] as $key => $attr ) {
							if ( isset( $_GET[ $key ] ) && ( ( ! is_null( $attr ) && $_GET[ $key ] == $attr ) || is_null( $attr ) ) ) {
								$flag &= TRUE;
							} else {
								$flag &= FALSE;
							}
						}
						
						if ( $flag ) {
							return static::check_for_user( $key );
						}
					} else {
						return static::check_for_user( $key );
					}
				}
			}
			
			return FALSE;
		}
		
		/**
		 * check notice block for user
		 *
		 * @param  string $key
		 *
		 * @return boolean
		 *
		 * @since  1.0.0
		 *
		 */
		protected static function check_for_user( $key ) {
			$user_id = get_current_user_id();
			
			if ( ! is_null( $key ) && $user_id ) {
				$dismissed = get_user_meta( $user_id, static::get_dismissed_field_key(), TRUE );
				
				if ( array_key_exists( $key, (array) $dismissed ) ) {
					return FALSE;
				}
			}
			
			return TRUE;
		}
		
		/**
		 * get dismissed field key
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 *
		 */
		public static function get_dismissed_field_key() {
			return AS_CM_Helpers_General::prepare_name( static::$dismissed_field_key );
		}
		
		/**
		 * add block by key to dismissed for current user
		 *
		 * @param string           $key
		 * @param int|boolean|NULL $user_id
		 *
		 * @return boolean
		 *
		 * @since     1.0.0
		 *
		 */
		public static function add_to_dismissed( $key, $user_id = null ) {
			if ( $user_id === null ) {
				$user_id = get_current_user_id();
				if ( ! $user_id ) {
					return FALSE;
				}
			}
			
			$dismissed = get_user_meta( $user_id, static::get_dismissed_field_key(), TRUE );
			
			if ( empty( $dismissed ) ) {
				$dismissed = array();
			}
			
			$dismissed[ $key ] = current_time( 'mysql', 1 );
			
			return update_user_meta( $user_id, static::get_dismissed_field_key(), $dismissed );
		}
		
		/**
		 * add notice to system
		 *
		 * @param string $key
		 * @param string $text
		 * @param array  $pages
		 * @param bool   $link
		 * @param bool   $dismiss
		 * @param bool   $is_ajax
		 *
		 * @since 1.0.0
		 *
		 */
		public static function add_notice( $key, $text = '', $pages = array( 'all' ), $link = FALSE, $dismiss = FALSE, $is_ajax = FALSE ) {
			new self( $key, array(
				'text'    => $text,
				'link'    => $link,
				'is_ajax' => $is_ajax,
				'dismiss' => $dismiss
			), $pages );
		}
	}