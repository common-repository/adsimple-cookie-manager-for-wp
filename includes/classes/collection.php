<?php
	
	class AS_CM_Classes_Collection {
		/**
		 * index of current element
		 *
		 * @var integer
		 *
		 * @since  1.0.0
		 *
		 */
		protected $current_index = - 1;
		
		/**
		 * count of elements
		 *
		 * @var NULL|integer
		 *
		 * @since  1.0.0
		 *
		 */
		protected $count = null;
		
		/**
		 * list of elements
		 *
		 * @var array
		 *
		 * @since  1.0.0
		 *
		 */
		protected $elements = array();
		
		/**
		 * list of  id
		 *
		 * @var null|array
		 *
		 * @since  1.0.0
		 *
		 */
		protected $ids = null;
		
		public function __construct( $data = array() ) { }
		
		/**
		 * create sampling from array of elements
		 *
		 * @param array  $elements
		 * @param string $class
		 *
		 * @return AS_CM_Classes_Collection
		 *
		 * @since 1.0.0
		 *
		 */
		public static function __construct_from_array( $elements = array(), $class = '' ) {
			$sampling = new self();
			
			$sampling->add_elements( $elements, $class );
			
			return $sampling;
		}
		
		/**
		 * add elements
		 *
		 * @param array  $elements
		 * @param string $class
		 *
		 * @since 1.0.0
		 *
		 */
		public function add_elements( $elements = array(), $class = '' ) {
			foreach ( $elements as $element ) {
				if ( empty( $class ) ) {
					$this->elements[] = $element;
				} else {
					$this->elements[] = new $class( null, $element );
				}
			}
		}
		
		/**
		 * add element
		 *
		 * @param mixed $element
		 *
		 * @since 1.0.0
		 *
		 */
		public function add( $element ) {
			$this->elements[] = $element;
			$this->reset();
		}
		
		/**
		 * sort by
		 *
		 * @param string $method
		 *
		 * @since 1.0.0
		 *
		 */
		public function sort_by( $method ) {
			if ( ! $this->have() ) {
				return;
			}
			
			$elements = $this->get_elements();
			$this->clear();
			
			usort( $elements,
				function( $a, $b ) use ( $method )
				{
					if ( $a->$method() == $b->$method() ) {
						return 0;
					}
					
					return ( $a->$method() < $b->$method() ) ? - 1 : 1;
				} );
			
			$this->add_elements( $elements );
		}
		
		/**
		 * get total count
		 *
		 * @return int
		 *
		 * @since  1.0.0
		 *
		 */
		public function get_count() {
			if ( is_null( $this->count ) ) {
				$this->count = sizeof( $this->elements );
			}
			
			return $this->count;
		}
		
		/**
		 * has elements?
		 *
		 * @return boolean
		 *
		 * @since  1.0.0
		 *
		 */
		public function have() {
			return $this->get_count() > 0;
		}
		
		/**
		 * get next element
		 *
		 * @return mixed
		 *
		 * @since  1.0.0
		 *
		 */
		public function next() {
			$new_index = $this->current_index + 1;
			
			if ( isset( $this->elements[ $new_index ] ) ) {
				$this->current_index ++;
				
				return $this->elements[ $new_index ];
			}
			
			return FALSE;
		}
		
		/**
		 * get index
		 *
		 * @param int $plus
		 *
		 * @return int
		 *
		 * @since  1.0.0
		 *
		 */
		public function get_index( $plus = 0 ) {
			return $this->current_index + $plus;
		}
		
		/**
		 * convert elements array to ids
		 *
		 * @return array
		 *
		 * @since  1.0.0
		 *
		 */
		public function get_ids() {
			if ( ! is_null( $this->ids ) ) {
				return $this->ids;
			}
			
			$this->reset();
			$this->ids = array();
			
			if ( $this->have() ) {
				while( $element = $this->next() ) {
					$this->ids[] = $element->get_id();
				}
			}
			
			return $this->ids;
		}
		
		/**
		 * return array of elements
		 *
		 * @return array
		 *
		 * @since  1.0.0
		 *
		 */
		public function get_elements() {
			return $this->elements;
		}
		
		/**
		 * get element by index
		 *
		 * @param  int $index
		 *
		 * @return mixed
		 *
		 * @since  1.0.0
		 *
		 */
		public function get_element( $index ) {
			$elements = $this->get_elements();
			
			return isset( $elements[ $index ] ) ? $elements[ $index ] : null;
		}
		
		/**
		 * filter by param
		 *
		 * @param array $values
		 *
		 * @since 1.0.0
		 *
		 */
		public function filter_by_id( $values = array() ) {
			if ( ! $this->have() || sizeof( $values ) <= 0 ) {
				return;
			}
			
			while( $element = $this->next() ) {
				if ( ! in_array( $element->get_id(), $values ) ) {
					unset( $this->elements[ $this->get_index() ] );
				}
			}
			
			$this->reset();
		}
		
		/**
		 * reset index of current element
		 *
		 * @since 1.0.0
		 *
		 */
		public function reset_current_index() {
			$this->current_index = - 1;
		}
		
		/**
		 * reset indexes of elements
		 *
		 * @since 1.0.0
		 *
		 */
		public function reset_indexes() {
			$this->elements = array_values( $this->get_elements() );
		}
		
		/**
		 * reset
		 *
		 * @since 1.0.0
		 *
		 */
		public function reset() {
			$this->reset_current_index();
			$this->reset_indexes();
			
			$this->count = null;
			$this->ids   = null;
		}
		
		/**
		 * clear
		 *
		 * @since 1.0.0
		 *
		 */
		public function clear() {
			$this->reset();
			
			$this->elements = array();
		}
		
		/**
		 * convert to array by method
		 *
		 * @param string  $method
		 *
		 * @return array
		 *
		 * @since 1.0.0
		 *
		 */
		public function convert_to_array( $method) {
			$data = array();
			
			$this->reset();
			
			if ( ! $this->have() ) {
				return $data;
			}
			
			while( $element = $this->next() ) {
				if ( method_exists( $element, $method ) ) {
					$elements = call_user_func( array( $element, $method ) );
					if ( ! empty( $elements ) ) {
						$data[] = $elements;
					}
					unset( $elements );
				}
			}
			
			$this->reset();
			
			return $data;
		}
	}