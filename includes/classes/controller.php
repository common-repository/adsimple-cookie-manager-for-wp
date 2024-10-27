<?php
	
	class AS_CM_Classes_Controller {
		protected static $instance = null;
		
		protected function __construct()
		{
			static::hooks();
		}
		
		/**
		 * load hooks
		 *
		 * @since 1.0.0
		 *
		 */
		protected static function hooks()
		{
		
		}
		
		/**
		 * action on deactivate plugin
		 *
		 * @since 1.0.0
		 *
		 */
		public static function deactivate(){
		
		}
		
		/**
		 * init object of class
		 *
		 * @return null
		 *
		 * @since 1.0.0
		 *
		 */
		public static function _instance()
		{
			if ( static::$instance === null ) {
				static::$instance = new static();
			}
			
			return static::$instance;
		}
	}