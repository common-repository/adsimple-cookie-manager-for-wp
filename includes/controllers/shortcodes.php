<?php
	
	class AS_CM_Controllers_Shortcodes extends AS_CM_Classes_Controller {
		
		protected static $instance = null;
		
		/**
		 * @var string
		 *
		 * @since 2.0.4
		 */
		CONST SHORTCODE_COOKIE_LIST = 'acm_cookie_list';
		
		/**
		 * load hooks
		 *
		 * @since 2.0.4
		 *
		 */
		protected static function hooks() {
			add_shortcode( self::SHORTCODE_COOKIE_LIST, [ __CLASS__, 'init_cookie_list_shortcode' ] );
			
			add_action( 'admin_head', [
				__CLASS__,
				'admin_tinymce',
			] );
			
			add_action( 'wp_ajax_' . self::get_action_get_tinymce_js(), [
				__CLASS__,
				'get_tinymce_generator',
			] );
		}
		
		/**
		 * @since 2.0.4
		 */
		public static function get_tinymce_generator() {
			header( 'Content-Type: application/javascript' );
			
			AS_CM_Helpers_View::get_template_part( 'shortcodes/tinymce', [
				'key'       => self::get_tinymce_key(),
				'icon'      => AS_CM_Helpers_General::prepare_name( 'button', false ),
				'shortcode' => static::SHORTCODE_COOKIE_LIST,
			] );
			
			exit();
		}
		
		/**
		 * load button settings for tinymce
		 *
		 * @since 2.0.4
		 *
		 */
		public static function admin_tinymce() {
			if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
				return;
			}
			
			if ( 'true' == get_user_option( 'rich_editing' ) ) {
				/**
				 * add js for tinymce
				 *
				 * @param array $plugins
				 *
				 * @return array $plugins
				 *
				 * @since 2.0.4
				 *
				 */
				add_filter( 'mce_external_plugins', function ( $plugins )
				{
					return array_merge( $plugins, [
						self::get_tinymce_key() => AS_CM_Helpers_Transfer::admin_ajax_link( static::get_action_get_tinymce_js() ),
					] );
				} );
				
				/**
				 * add button to tinymce
				 *
				 * @param array $buttons
				 *
				 * @return array $buttons
				 *
				 * @since 2.0.4
				 *
				 */
				add_filter( 'mce_buttons', function ( $buttons )
				{
					array_push( $buttons, self::get_tinymce_key() );
					
					return $buttons;
				} );
				
			}
		}
		
		/**
		 * @return string
		 *
		 * @since  2.0.4
		 *
		 */
		public static function get_tinymce_key() {
			return AS_CM_Helpers_General::prepare_name( 'shortcode' );
		}
		
		/**
		 * @return string
		 *
		 * @since 2.0.4
		 */
		public static function get_action_get_tinymce_js() {
			return AS_CM_Helpers_General::prepare_name( 'shortcode-tinymce', false );
		}
		
		/**
		 * @return string
		 *
		 * @since 2.0.4
		 */
		public static function init_cookie_list_shortcode() {
			return '<div class="js-acm-cookie-list"></div>';
		}
		
		private function __clone() {
		}
	}