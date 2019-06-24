<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( !class_exists( 'Food_Online_Loop' ) ) {
		/**
		 * Class Food_Online_Loop
		 *
		 * @since 1.0
		 */
    class Food_Online_Loop
    {
        protected $product;
        protected $products = array();

		public static $do_not_duplicate = array();
        // The constructor
        public function __construct( $product )
        {
            $this->product = $product;
			
        }
       // Stores the products array for backbone
        public function the_product()
        {

			 extract( $this->product->the_product() );


			     $this->products  = array_merge( $this->products, $products );

        }

        // Returns an array of products for backbone
        public function get_loop_items()
        {
            return array(
                 'products' => $this-> products
            );
        }

    }
}
