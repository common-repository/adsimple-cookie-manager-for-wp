<?php
	
	class AS_CM_Helpers_General {
		/**
		 * load by path
		 * if class and function not empty, call function
		 *
		 * @param  string $path
		 * @param  array  $options
		 *
		 * @since  1.0.0
		 *
		 */
		public static function load( $path, $options = array() ) {
			extract( shortcode_atts( array(
				                         'class'     => '',
				                         'function'  => '',
				                         'full_path' => FALSE,
				                         'one_time'  => TRUE,
				                         'variables' => array(),
				                         'extension' => 'php',
				                         'action'    => '',
				                         'priority'  => '',
				                         'args'      => ''
			                         ),
			                         $options ) );
			
			/**
			 * @var array   $variables
			 * @var boolean $one_time
			 * @var boolean $full_path
			 * @var int     $priority
			 * @var array   $args
			 * @var string  $extension
			 */
			
			if ( sizeof( $variables ) != 0 ) {
				extract( $variables );
			}
			
			if ( ( ! empty( $class ) && ! class_exists( $class ) ) || empty( $class ) ) {
				$path = ( $full_path ? $path : self::get_full_include_path( $path ) ) . '.' . $extension;
				
				if ( $one_time ) {
					require_once( $path );
				} else {
					require( $path );
				}
			}
			
			if ( ! empty( $function ) && ! empty( $class ) ) {
				if ( empty( $action ) ) {
					call_user_func( array(
						                $class,
						                $function
					                ) );
				} else {
					add_action( $action,
					            array(
						            $class,
						            $function
					            ),
					            $priority,
					            $args );
				}
			}
		}
		
		/**
		 * load controller
		 *
		 * @param string $path
		 * @param string $class
		 * @param array  $options
		 *
		 * @since 1.0.0
		 *
		 */
		public static function load_controller( $path, $class, $options = array() ) {
			$options = shortcode_atts( array(
				                           'class'     => $class,
				                           'function'  => '_instance',
				                           'action'    => 'init',
				                           'priority'  => '30',
				                           'full_path' => TRUE
			                           ),
			                           $options );
			
			self::load( static::get_full_include_path( 'controllers/' . $path ), $options );
		}
		
		/**
		 * get full path to folder
		 *
		 * @param string $path
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 */
		public static function get_full_path( $path ) {
			return sprintf( '%s%s', AS_CM_Manager::$path, $path );
		}
		
		/**
		 * get full path to folder
		 *
		 * @param string $path
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 *
		 */
		public static function get_full_include_path( $path ) {
			return self::get_full_path( 'includes/' . $path );
		}
		
		/**
		 * get full path to assets folder
		 *
		 * @param string $path
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 *
		 */
		public static function get_full_assets_path( $path ) {
			return self::get_full_path( 'assets/' . $path );
		}
		
		/**
		 * get full url to assets folder
		 *
		 * @param string  $path
		 * @param string  $ext
		 * @param boolean $dashboard
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 *
		 */
		public static function get_full_assets_url( $path, $ext = '', $dashboard = TRUE ) {
			return self::get_full_url( 'assets/' . ( $dashboard ? 'dashboard' : 'frontend' ) . "/" . $path . '.' . $ext );
		}
		
		/**
		 * get full url
		 *
		 * @param string $path
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 */
		public static function get_full_url( $path ) {
			return sprintf( '%s/%s', AS_CM_Manager::$url, $path );
		}
		
		/**
		 * stripcslashes
		 *
		 * @param mixed $value
		 *
		 * @return mixed
		 *
		 * @since 1.0.0
		 *
		 */
		public static function stripcslashes( $value ) {
			if ( ! is_array( $value ) ) {
				return stripcslashes( $value );
			}
			
			return array_map( function( $element )
			{
				return self::stripcslashes( $element );
			},
				$value );
		}
		
		/**
		 * prepare name
		 *
		 * @param string $base
		 * @param bool   $prefix
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 *
		 */
		public static function prepare_name( $base, $prefix = TRUE ) {
			return sprintf( '%s%s_%s', $prefix ? '_' : '', AS_CM_Manager::$action, $base );
		}
		
		/**
		 * esc sql for array
		 *
		 * @param array|string $elements
		 *
		 * @return array|string
		 *
		 * @since 1.0.0
		 *
		 */
		public static function esc_sql( $elements = array() ) {
			global $wpdb;
			
			if ( ! is_array( $elements ) ) {
				return $wpdb->remove_placeholder_escape( esc_sql( $elements ) );
			}
			
			return array_map( function( $element )
			{
				return self::esc_sql( $element );
			},
				$elements );
		}
	}