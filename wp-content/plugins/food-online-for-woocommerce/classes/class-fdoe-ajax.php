<?php
if ( !class_exists( 'Food_Online_Ajax' ) ) {
    /**
		 * Main Class Food_Online_Ajax
		 *
		 * @since 2.0.3
		 */
    class Food_Online_Ajax
    {
            // The Constructor
        public function __construct()
        {

			 $this->reg_my_ajax_methods();
        }


	public function ajaxfdoe_make_product_shortcode(){

	do_action('fdoe_before_product_modal');

			$id        =  ( $_POST['id']  );
			$content= [];




			for ($i = 0; $i <= count($id)-1; $i++) {

			$content_part =array(
								 'id' => $id[$i],

								 'single_shortcode' => do_shortcode('[product_page id="' . $id[$i] . '" ]'),

								// 'cat_id' => $cat_id,
								 );
			$content[] = $content_part;


			}
			$data = array(
					'success' => true,
					'content' => $content,

			) ;

			wp_send_json( $data );



		}

public function ajaxfdoe_add()
{
	ob_start();
if ( class_exists( 'WC_Product_Addons_Display' ) ) {
	$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['add-to-cart'] ) );
$quantity          = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( $_POST['quantity'] );
$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
}
else{
	$passed_validation = null;
}
 $cart = ob_get_contents();
 	$data = array(
					'success' => true,
					'cart' => $cart,
					'passed_vali' => $passed_validation,
			) ;
 ob_end_clean();
wp_send_json( $data );
}


public function reg_my_ajax_methods()
  {
  $new_reflex = new ReflectionClass(get_class($this));
  foreach($new_reflex->getMethods() as $method)
   {
   if (strpos($method->name, 'ajaxfdoe') === 0)
    {
    $ref = new ReflectionMethod(get_class($this) , $method->name);
    add_action('wc_ajax_' . $method->name, array(
     $this,
     $method->name
    ) , 10, count($ref->getParameters()));
    }
   }
  }
    }
}
