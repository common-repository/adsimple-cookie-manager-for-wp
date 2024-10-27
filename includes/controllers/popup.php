<?php
	
	class AS_CM_Controllers_Popup extends AS_CM_Classes_Controller {
		
		protected static $instance = null;
		
		/**
		 * load hooks
		 *
		 * @since 1.0.0
		 *
		 */
		protected static function hooks() {
			add_action( 'wp_head', [ __CLASS__, 'add_embed_code' ], - 10 );
		}
		
		/**
		 * add embed code to page
		 *
		 * @since 1.0.0
		 *
		 */
		public static function add_embed_code() {
			if ( is_admin() ) {
				return;
			}
			
			$code = AS_CM_Controllers_Options::get_option_embed_code();
			if ( empty( $code ) ) {
				return;
			}
			
			echo "\r\n" . apply_filters( AS_CM_Manager::$action . '_embed_code', $code ) . "\r\n";
		}
		
		private function __clone() {
		}
	}