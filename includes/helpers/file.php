<?php
	
	class AS_CM_Helpers_File {
		
		/**
		 * @param string $path
		 *
		 * @return string|false
		 *
		 * @since 2.0.6
		 *
		 */
		public static function check_folder_and_create( $path ) {
			try {
				if ( ! is_dir( $path ) ) {
					if ( ! @mkdir( $path, 0777, true ) ) {
						return false;
					}
				}
				
				return $path;
			} catch ( Exception $exception ) {
				return false;
			}
		}
		
		/**
		 * @param string $path
		 * @param string $data
		 *
		 * @return boolean
		 *
		 * @since 2.0.6
		 *
		 */
		public static function write_to_file( $path, $data ) {
			try {
				$file = fopen( $path, 'w' );
				if ( ! $file ) {
					return false;
				}
				$result = fwrite( $file, $data );
				fclose( $file );
				
				return $result === false ? false : true;
			} catch ( Exception $e ) {
				return false;
			}
		}
		
		/**
		 * @param string $path
		 *
		 * @return false|string
		 *
		 * @since 2.0.6
		 */
		public static function read_file( $path ) {
			try {
				$file = fopen( $path, "r" );
				
				if ( ! $file ) {
					return false;
				}
				
				$result = fread( $file, filesize( $path ) );
				
				fclose( $file );
				
				return $result === false ? false : (string) $result;
			} catch ( Exception $exception ) {
				return false;
			}
		}
		
		/**
		 * @return string
		 *
		 * @since 2.0.6
		 */
		public static function get_upload_dir() {
			return rtrim( sprintf( '%s/%s/', wp_upload_dir()['basedir'], AS_CM_Manager::$action ), '/' );
		}
		
	}