<?php

if ( !defined( 'ABSPATH' ) ) {

    exit;

}

if ( !class_exists( 'Food_Online_Product' ) ) {

     /**

		 * Class Food_Online_Product

		 *

		 * @since 1.0

		 */

    class Food_Online_Product

    {

	public static $item_icon;
	public static $cats_to_include;
	public static $modal_settings;
	public static $counter = 1;

	public static $products_shortcode_list;




		public static function get_icon(){

			if(!isset(self::$item_icon)){

		 self::$item_icon = WC_Admin_Settings::get_option('fdoe_item_icon');


			}

		 return self::$item_icon;

		}
		public static function get_modal_settings(){

			if(!isset(self::$modal_settings)){

		 self::$modal_settings = array(  get_option('fdoe_popup_simple','yes') == 'yes',get_option('fdoe_popup_variable','yes') == 'yes' , get_option('fdoe_is_prem','no') == 'yes');


			}

		 return self::$modal_settings;

		}

		public static function get_cats_to_include(){

			if(!isset(self::$cats_to_include)){

				$cats = get_option('fdoe_category_order',array()) ;
				if ( get_option('fdoe_is_prem','yes') == 'yes' && is_array($cats) && !empty($cats)){

			self::$cats_to_include = array_column($cats,'ID');
			// Fix for PHP 7.0.32-33 where array_column is broken
			if(empty(self::$cats_to_include)):
				self::$cats_to_include = array_map(function ($each) {
					return $each['ID'];
					}, $cats);
				endif;

				}else if(!empty(Food_Online::get_categories_raw())){

					$cats = Food_Online::get_categories_raw() ;
					self::$cats_to_include = array_column($cats,'cat_ID');
			// Fix for PHP 7.0.32-33 where array_column is broken
			if(empty(self::$cats_to_include)):
				self::$cats_to_include = array_map(function ($each) {
					return $each['cat_ID'];
					}, $cats);
				endif;



		}else{
		self::$cats_to_include = array();
		}

			}
			return self::$cats_to_include;


		}





		// Get the single page shortcode
		private function do_product_shortcode( $is_var, $is_simple, $id){


			if(self::$counter < 50 ){
				$in_cat = false;
				if(!Food_Online::get_is_shortcode()){

				if(empty(self::get_cats_to_include()) || in_array($id, self::get_product_list(self::get_cats_to_include() ))){

					$in_cat = true;
				}else{

					return '';
				}




			}else if(Food_Online::get_is_shortcode()){

				if(empty(Food_Online_Shortcode::get_shortcode_order() ) || in_array($id, self::get_product_list(Food_Online_Shortcode::get_shortcode_order()))){

					$in_cat = true;
				}else{

					return '';
				}




		}else{
			return '';
		}
			$temp_modal_settings = self::get_modal_settings();
			$to_return = (( $is_var && $temp_modal_settings [1] && !$temp_modal_settings [2])  || (( $is_var && $temp_modal_settings[1] && $temp_modal_settings [2]) &&   $in_cat  ))
			||
			(( $is_simple && $temp_modal_settings [0] && !$temp_modal_settings [2])  || (( $is_simple && $temp_modal_settings [0] && $temp_modal_settings [2]) &&   $in_cat  ))
			? do_shortcode('[product_page id="' . $id . '" ]') : '';
			if($to_return != ''){
			self::$counter++;
			}
			return $to_return;
		}else{return '';}
		}


		public static function get_product_list($cats){

			if(!isset(self::$products_shortcode_list)  ){
				$products_shortcode_list = array();


			foreach($cats as $cat){
				if(is_countable($products_shortcode_list) && count($products_shortcode_list) > 49){break;}
				$term = get_term_by('id', (int)$cat, 'product_cat', 'ARRAY_A');


				$args = array(
				'category' => array($term['slug']),
				'return' => 'ids',
				'limit' => 50,
				'orderby' => 'title'
				);

				$products_shortcode_list = array_merge_recursive($products_shortcode_list, wc_get_products( $args ));




			}
			self::$products_shortcode_list = array_slice($products_shortcode_list,0,50);

			}

			return self::$products_shortcode_list;
			}
		 // Gets the products and returns them to the loop

        public function the_product()

        {



            $product          = wc_get_product();

            $this->id         = $product->get_id();

			$is_variable = $product->is_type( 'variable' );
			$is_simple = $product->is_type( 'simple' );

            global $woocommerce;


            $cat_id_simple = $product->get_category_ids();



			 $temp_modal_settings = self::get_modal_settings();
			  if($is_simple && !$temp_modal_settings[0]){
				$product_add_url = do_shortcode('[add_to_cart_url id="'.  $this->id   .'"]');
			$do_add_url =	'<span><a href="'.$product_add_url.'" class="add_to_cart_button ajax_add_to_cart product_type_simple" data-product_id="'. $this->id  .'" data-product_sku="'.$product->get_sku().'"   data-quantity="1" rel="nofollow">  <i  class="'.self::get_icon() .' fa-2x fdoe-item-icon" ></i></a></span>'
			;}else if($is_variable && !$temp_modal_settings[1]){
				$product_add_url = do_shortcode('[add_to_cart_url id="'.  $this->id   .'"]');
				$do_add_url =	'<span><a href="'.$product_add_url.'"  rel="nofollow">  <i  class="'.self::get_icon() .' fa-2x fdoe-item-icon" ></i></a></span>';

				}else{
					$do_add_url = '';

				}


        $item  = array(

				'featured' =>  $product-> is_featured(),

                'short_description' => $product->get_short_description(),

                'image' => array(

                     'src' => $product->get_image(array( 200, 200),$placeholder = true)

                ),

                'title' => $product->get_title(),

                'price_html' => $product->get_price_html(),

                'id' =>  $this->id,

                'variations' => array(),

                'cart_button' => '<span><i  class="'.self::get_icon() .' fa-2x fdoe-item-icon" ></i></span>',

				'cart_button_add' => $do_add_url,


				'cat_id' => (array) $cat_id_simple,


                'parent_id' =>  $this->id,

				'is_variable' => $is_variable,

				'is_simple' => $is_simple,

				'in_stock' => $product-> is_in_stock(),

				'single_shortcode' =>  self::do_product_shortcode( $is_variable, $is_simple,  $this->id   ),

            );



			$products[]    = apply_filters( 'fdoe_loop_single_item', $item, $product );
            global $wp_query, $post;


            $next_page = html_entity_decode( get_next_posts_page_link( $wp_query->max_num_pages ) );

            $next_page = $next_page ? add_query_arg( 'fdoe-ajax', '1', $next_page ) : '';

            $next_page = apply_filters( 'fdoe_next_page', $next_page );

            $data      = compact( 'next_page', 'products' );

            return apply_filters( 'fdoe_loop_products', $data );

        }

    }

}
