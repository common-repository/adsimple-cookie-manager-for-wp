<?php
	
	class AS_CM_Controllers_Design extends AS_CM_Classes_Controller {
		protected static $instance = null;
		
		/**
		 * load hooks
		 *
		 * @since 1.0.0
		 *
		 */
		protected static function hooks() {
			add_action( 'admin_enqueue_scripts',
			            array(
				            __CLASS__,
				            'init_assets_for_dashboard_page'
			            ) );
		}
		
		/**
		 * init assets for dashboard page
		 *
		 * @param string $hook
		 */
		public static function init_assets_for_dashboard_page( $hook ) {
			AS_CM_Helpers_View::enqueue_styles( array(
				                                    AS_CM_Helpers_General::prepare_name( 'global' ) => array(
					                                    'href'         => 'global',
					                                    'is_dashboard' => TRUE
				                                    ),
			                                    ) );
			
			AS_CM_Helpers_View::enqueue_scripts( array(
				                                     AS_CM_Helpers_General::prepare_name( 'global' ) => array(
					                                     'href'         => 'global',
					                                     'is_dashboard' => TRUE,
					                                     'need_dequeue' => array( 'jquery' )
				                                     ),
			                                     ) );
			
			if ( $hook != 'toplevel_page_' . AS_CM_Controllers_Options::get_slug() ) {
				return;
			}
			
			AS_CM_Helpers_View::enqueue_styles( array(
				                                    AS_CM_Helpers_General::prepare_name( 'general' ) => array(
					                                    'href'         => 'general',
					                                    'is_dashboard' => TRUE
				                                    ),
			                                    ) );
		}
		
		private function __clone() {
		}
	}