<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Food_Online_Shortcode {

	protected static $_instance = null;

	protected $is_active = false;

	public static $shortcode_order;

	public function __construct() {

		add_shortcode( 'foodonline', array( $this, 'shortcode' ) );



	}

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
public static function save_shortcode_order($cats_array){

	if(!isset(self::$shortcode_order)){

		/*if(empty($cats_array)){
			$cats = Food_Online::get_categories_raw();
			if (  is_array($cats) && !empty($cats)){
			$cats_to_include = array_column($cats,'cat_ID');
			// Fix for PHP 7.0.32-33 where array_column is broken
			if(empty($cats_to_include)):
				$cats_to_include = array_map(function ($each) {
					return $each['cat_ID'];
					}, $cats);
				endif;
		$cats_array = $cats_to_include;
		}

		}*/
	self::$shortcode_order = $cats_array;

}
}
public static function get_shortcode_order(){

	return self::$shortcode_order;
}
public function shortcode( $atts ) {


if ( $this->is_active && get_option('fdoe_force_shortcode','no')=='no') {
			return;
		}




		$this->is_active = true;




		$options = shortcode_atts( array(
			'categories'         => null,
			'tags'               => null,


		), $atts );


		$tags = empty($options['tags']) ? '' : 'tag="'.$options['tags'].'"'  ;
		$cats = empty ($options['categories']) ? false : 'category="'. $options['categories'] .'"' ;
		$cats_array = [];
				if($cats !== false ){
		$pieces = explode(",", $options['categories']);

		foreach ($pieces as $piece ){

			$category = get_term_by( 'name', $piece, 'product_cat' );
			$cat_id = $category->term_id;
			$cats_array[] = $cat_id;

		}

			if( get_option('fdoe_is_prem','no') == 'no' && !empty(Food_Online::get_categories_raw())){

					$cats2 = Food_Online::get_categories_raw() ;
					$cats_array_2 = array_column($cats2,'cat_ID');
			// Fix for PHP 7.0.32-33 where array_column is broken
			if(empty($cats_array_2)):
				$cats_array_2 = array_map(function ($each) {
					return $each['cat_ID'];
					}, $cats2);
				endif;

				$cats_array = array_intersect($cats_array_2, $cats_array );
			}
		}
				self::save_shortcode_order($cats_array);
		$locs = array(
					  'cats' => self::get_shortcode_order(),
					  );

		wp_localize_script( 'fdoe-order', 'fdoe_short', $locs );


		ob_start();

		$pp  = get_option('fdoe_is_prem') == 'yes' ? max( sqrt(Food_Online::$fdoe_doit[0]) , Food_Online::$fdoe_doit[1] ) : min( sqrt(Food_Online::$fdoe_doit[0]) , Food_Online::$fdoe_doit[1] ) ;
		$l = 'limit='.$pp;

		echo do_shortcode('[products '.$cats.' '.$tags.' '.$l.' orderby="title"]');




		$content = ob_get_clean();




		return ($content);
	}



}
