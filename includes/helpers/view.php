<?php
	
	class AS_CM_Helpers_View {
		/**
		 * get template part from views folder
		 *
		 * @param  string $template Template name
		 * @param  array  $args     Array of args
		 * @param  array  $options  Array of options
		 *
		 * @return string
		 *
		 * @since  1.0.0
		 *
		 */
		public static function get_template_part( $template, $args = array(), $options = array() ) {
			$options = shortcode_atts( array(
				                           'ext'         => 'php',
				                           'dashboard'   => TRUE,
				                           'to_variable' => FALSE,
				                           'full_path'   => FALSE,
			                           ),
			                           $options );
			
			if ( sizeof( $options ) != 0 ) {
				extract( $options );
			}
			
			if ( ! empty( $args ) ) {
				extract( $args );
			}
			
			/**
			 * @var bool   $full_path
			 * @var bool   $to_variable
			 * @var bool   $dashboard
			 * @var string $ext
			 */
			
			ob_start();
			include( $full_path ? $template : AS_CM_Helpers_General::get_full_assets_path( ( $dashboard ? 'dashboard/' : '' ). 'views/' . $template . '.' . $ext ) );
			
			$content = ob_get_contents();
			ob_end_clean();
			
			if ( $to_variable ) {
				return $content;
			}
			
			echo $content;
		}
		
		/**
		 * enqueue styles through wp_enqueue_style
		 *
		 * @param array $styles
		 *
		 * @since 1.0.0
		 *
		 */
		public static function enqueue_styles( $styles ) {
			self::enqueue_assets( $styles, 'style' );
		}
		
		/**
		 * enqueue assets element through WordPress methods
		 *
		 * @param array  $elements
		 * @param string $type
		 *
		 * @since 1.0.0
		 *
		 */
		public static function enqueue_assets( $elements, $type ) {
			if ( ! is_array( $elements ) ) {
				return;
			}
			
			$type = sprintf( 'enqueue_%s', $type );
			
			foreach ( $elements as $name => $element ) {
				self::$type( $name, $element );
			}
		}
		
		/**
		 * enqueue style through wp_enqueue_style
		 *
		 * @param string $name
		 * @param array  $style
		 *
		 * @since 1.0.0
		 *
		 */
		public static function enqueue_style( $name, $style ) {
			if ( $style !== FALSE && is_array( $style ) ) {
				if ( isset( $style['need_dequeue'] ) && $style['need_dequeue'] ) {
					
					wp_deregister_style( $name );
					wp_dequeue_style( $name );
				}
				
				wp_register_style( $name,
				                   strpos( $style['href'],
				                           '//' ) !== FALSE ? $style['href'] : AS_CM_Helpers_General::get_full_assets_url( 'styles/css/' . $style['href'], 'css', $style['is_dashboard'] ),
				                   isset( $style['deps'] ) ? $style['deps'] : array(),
				                   isset( $style['version'] ) ? $style['version'] : AS_CM_Manager::$version,
				                   isset( $style['media'] ) ? $style['media'] : 'all' );
			}
			
			if ( ! is_array( $style ) ) {
				$name = $style;
			}
			
			wp_enqueue_style( $name );
			
			if ( $style !== FALSE && is_array( $style ) ) {
				foreach ( array( 'conditional' ) as $attr ) {
					if ( isset( $style[ $attr ] ) ) {
						wp_style_add_data( $name, $attr, $style[ $attr ] );
					}
				}
			}
		}
		
		/**
		 * enqueue script through wp_enqueue_script
		 *
		 * href
		 * deps
		 * version
		 * is_footer
		 * is_dashboard
		 * conditional
		 * defer
		 * localize
		 * need_dequeue
		 *
		 * @param string $name
		 * @param array  $script
		 *
		 * @since 1.0.0
		 *
		 */
		public static function enqueue_script( $name, $script ) {
			if ( $script !== FALSE && is_array( $script ) ) {
				if ( isset( $script['need_dequeue'] ) && $script['need_dequeue'] ) {
					wp_deregister_script( $name );
					wp_dequeue_script( $name );
				}
				
				wp_register_script( $name,
				                    strpos( $script['href'],
				                            '//' ) !== FALSE ? $script['href'] : AS_CM_Helpers_General::get_full_assets_url( 'scripts/' . $script['href'], 'js' ,$script['is_dashboard'] ),
					( isset( $script['deps'] ) ? ( ! is_array( $script['deps'] ) ? array( $script['deps'] ) : $script['deps'] ) : array() ),
					( isset( $script['version'] ) ? $script['version'] : AS_CM_Manager::$version ),
					( isset( $script['in_footer'] ) && $script['in_footer'] ? TRUE : FALSE ) );
				
				if ( isset( $script['localize'] ) && ! is_null( $script['localize'] ) ) {
					wp_localize_script( $name, $script['localize']['name'], $script['localize']['data'] );
				}
			}
			
			if ( ! is_array( $script ) ) {
				$name = $script;
			}
			
			wp_enqueue_script( $name );
			
			if ( $script !== FALSE && is_array( $script ) ) {
				foreach (
					array(
						'conditional',
						'defer'
					) as $attr
				) {
					if ( isset( $script[ $attr ] ) ) {
						wp_script_add_data( $name, $attr, $script[ $attr ] );
					}
				}
			}
		}
		
		/**
		 * enqueue scripts through wp_enqueue_script
		 *
		 * @param array $scripts
		 *
		 * @since 1.0.0
		 *
		 */
		public static function enqueue_scripts( $scripts ) {
			self::enqueue_assets( $scripts, 'script' );
		}
	}