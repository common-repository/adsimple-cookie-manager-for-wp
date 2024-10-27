<?php
	
	class AS_CM_Classes_Item {
		
		/**
		 * @param string $name
		 *
		 * @return string|array|int|mixed
		 *
		 * @since 2.0.6
		 *
		 */
		public function get_attr( $name ) {
			return property_exists( $this, $name ) ? $this->$name : null;
		}
		
		/**
		 * @param string $name
		 * @param mixed  $value
		 * @param bool   $forcibly
		 *
		 * @return mixed
		 *
		 * @since 2.0.6
		 *
		 */
		public function set_attr( $name, $value, $forcibly = false ) {
			$method = 'set_attr_' . $name;
			if ( method_exists( $this, $method ) && ! $forcibly ) {
				$this->$method( $value );
			} elseif ( property_exists( $this, $name ) ) {
				$this->$name = $value;
			}
			
			return $value;
		}
		
		/**
		 * @param array
		 *
		 * @since 2.0.6
		 *
		 */
		public function set_attrs( $params = [] ) {
			if ( ! is_array( $params ) || ! $params ) {
				return;
			}
			
			foreach ( $params as $key => $value ) {
				$this->set_attr( $key, $value );
			}
		}
		
		/**
		 * @param string $name
		 * @param mixed  $default
		 *
		 * @since 2.0.6
		 *
		 */
		public function reload_attr( $name, $default = null ) {
			$this->set_attr( $name, $default );
		}
	}