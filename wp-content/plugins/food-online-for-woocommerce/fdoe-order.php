<?php
/**
 * Plugin Name: Food Online for WooCommerce
 * Plugin URI: http://foodonlineplugin.com
 * Description: A restaurant ordering system for WooCommerce.
 * Version: 2.4.1
 * Author: Arosoft.se
 * Author URI: https://arosoft.se
 * Developer: Arosoft.se
 * Developer URI: https://arosoft.se
 * Text Domain: food-online-for-woocommerce
 * Domain Path: /languages
 * WC requires at least: 3.3
 * WC tested up to: 3.6
 * Copyright: Arosoft.se 2019
 * License: GPL v2 or later
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */
define( 'FOOD_ONLINE_VERSION', '2.4.1' );
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/*
 *Classes{
	Food_Online
	Food_Online_Ajax
	Food_Online_Del
	Food_Online_Settings
	Food_Online_Product
	Food_Online_Loop
	Food_Online_Shortcode
 }
 *
 */
register_activation_hook( __FILE__, array(
     'Food_Online',
    'activate'
) );
register_uninstall_hook( __FILE__, array(
     'Food_Online',
    'uninstall'
) );
if ( !class_exists( 'Food_Online' ) ) {
    /**
		 * Main Class Food_Online
		 *
		 * @since 1.0
		 */
    class Food_Online
    {

		 // to be run on plugin activation
        static function activate()
        {
            update_option( 'Food_Online_Activated_Plugin', 'Food_Online' );
			update_option('fdoe_popup_variable','yes');
			add_option('fdoe_override_shop', 'yes');
			add_option('fdoe_left_menu', 'no');
			add_option('fdoe_menu_titles_icon', 'fas fa-ellipsis-h');
			add_option('fdoe_color', '#bf5bb6');
			add_option('fdoe_item_icon', 'fas fa-plus-circle');
			add_option('fdoe_show_images', 'rec');
			add_option('fdoe_layout', 'fdoe_onecol');
			add_option('fdoe_hide_minicart', 'no');
			update_option('fdoe_is_prem', 'no');
			update_option('woocommerce_shop_page_display','');
		}
         // to be run on plugin uninstallation
         public static function uninstall()
        {
			if(get_option('fdoe_clean_settings','no') == 'yes'){
			delete_option('fdoe_override_shop');
			delete_option('fdoe_menu_titles_icon');
			delete_option('fdoe_color');
			delete_option('fdoe_item_icon');
			delete_option('fdoe_show_images');
			delete_option('fdoe_popup_image');
			delete_option('fdoe_popup_meta');
			delete_option('fdoe_disable_bootstrap');
			delete_option('fdoe_popup_meta');
			delete_option('fdoe_layout');
			delete_option('fdoe_hide_minicart');
			delete_option('fdoe_is_prem');
			}
        }
        protected static $_instance = null;

        public $product;
        public $loop;
        public $notices;
        protected $settings;
		public static $fdoe_doit = array(2500, 999);
		private $is_menu;

		private $is_prem;

		public static $categories_raw;
		public static $is_shortcode;


         // sets $categories to product categories
        public static function set_categories()
        {
			if(!isset(self::$categories_raw)){
            $taxonomy         = 'product_cat';
            $orderby          = 'name';
            $show_count       = 0; // 1 for yes, 0 for no
            $pad_counts       = 0; // 1 for yes, 0 for no
            $hierarchical     = 1; // 1 for yes, 0 for no
            $title            = '';
            $empty            = 0;
			$fields			  = 'all';
            $args             = array(
                 'taxonomy' => $taxonomy,
                'orderby' => $orderby,
                'show_count' => $show_count,
                'pad_counts' => $pad_counts,
                'hierarchical' => $hierarchical,
                'title_li' => $title,
                'hide_empty' => $empty,
				'fields'	=> $fields,
            );

            self::$categories_raw = get_categories( $args );

			if(get_option('fdoe_is_prem', 'no') == 'yes' &&  get_option('fdoe_cat_image', 'no') == 'yes' ){

			foreach (self::$categories_raw as $fdoe_cat) {
			$thumbnail_id = get_woocommerce_term_meta( $fdoe_cat->term_id, 'thumbnail_id', true );

			$image = wp_get_attachment_url( $thumbnail_id );


			$fdoe_cat -> image = $image;

			}


			}
			}

			return;
        }
        // Return product categories
        public static function get_categories_raw()
        {
            return self::$categories_raw;
        }
		public static function set_is_shortcode()
        {

            self::$is_shortcode = wc_post_content_has_shortcode( 'foodonline' );

			return;
        }
        // Return product categories
        public static function get_is_shortcode()
        {
            return self::$is_shortcode;
        }
        // The Constructor
        public function __construct()
        {
            add_action( 'admin_init', array(
                 $this,
                'check_environment'
            ) );
            add_action( 'admin_notices', array(
                 $this,
                'admin_notices'
            ), 15 );
            add_action( 'plugins_loaded', array(
                 $this,
                'init'
            ), 10 );
            add_action( 'init', array(
                 $this,
                'load_text_domain'
            ) );


        }
        // Enqueues admin scripts and styles
        public function enqueue_scripts_back_end()
        {
          if ( isset ($_GET['tab']) && $_GET['tab'] === 'fdoe' ) {
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'fdoe-admin-script-handle', $this->get_plugin_url( 'assets/js/fdoe-order-admin.js' ), array(
                 'wp-color-picker',
                'jquery'
            ), FOOD_ONLINE_VERSION , true );
			wp_enqueue_style( 'fdoe-admin-style', $this->get_plugin_url( 'assets/css/admin-style.css' ),array(), FOOD_ONLINE_VERSION );
			wp_enqueue_style( 'fdoe-order-font-3', $this->get_plugin_url( 'assets/fontawesome/css/fontawesome.min.css' ),array(), FOOD_ONLINE_VERSION );
			wp_enqueue_style( 'fdoe-order-font-4', $this->get_plugin_url( 'assets/fontawesome/css/solid.min.css' ),array(), FOOD_ONLINE_VERSION );
			wp_enqueue_style( 'fdoe-order-font-4', $this->get_plugin_url( 'assets/fontawesome/css/regular.min.css' ),array(), FOOD_ONLINE_VERSION );


        }
		}
        // Includes plugin files
        public function includes()
        {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            // For support of WooCommerce Add-On
          if (  is_plugin_active('woocommerce-product-addons/woocommerce-product-addons.php')) {
            include_once( ABSPATH . 'wp-content/plugins/woocommerce-product-addons/includes/class-wc-product-addons-display.php' );
          }
            require_once( 'classes/class-fdoe-product.php' );
            require_once( 'classes/class-fdoe-loop.php' );
			require_once( 'classes/class-fdoe-ajax.php' );
			require_once( 'classes/class-fdoe-shortcode.php' );
			require_once( 'classes/class-fdoe-orders.php' );
		// Check if to run the premium version
		update_option('fdoe_is_prem', 'no');
		$this -> is_prem = false;
		if( file_exists(plugin_dir_path(__FILE__) . 'prem') ){
			        require_once( 'prem/class-fdoe-del.php' );
					require_once( 'prem/class-fdoe-settings-prem.php' );
					update_option('fdoe_is_prem', 'yes');
					$this -> is_prem = true;
			}
			else{
				 require_once( 'classes/class-fdoe-settings.php' );
			}
        }
        // Returns new class instance
        public static function instance()
        {
            if ( !isset( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
       // Plugin initiation
        public function init()
        {
		// For support of WooCommerce Add-On
			add_action( 'wp_loaded',  function(){
			 if (  is_plugin_active('woocommerce-product-addons/woocommerce-product-addons.php')) {
				 remove_all_actions( 'woocommerce_before_variations_form', 10 );
				}
			});
            // check if environment is ok
            if ( self::get_environment_warning() ) {
                return;
            }
            if ( is_admin() && get_option( 'Food_Online_Activated_Plugin' ) == 'Food_Online' ) {
                delete_option( 'Food_Online_Activated_Plugin' );
            }
            $this->includes();
            add_action( 'wp_enqueue_scripts', array(
                 $this,
                'load_bootstrap_and_fontawesome'
            ),10);
            add_action( 'wp_enqueue_scripts', array(
                 $this,
                'enqueue_scripts'
            ), 99 );
			add_filter( 'style_loader_tag', array($this,'add_font_awesome_attributes'), 10, 2 );
            add_action( 'woocommerce_init', array(
                 $this,
                'woocommerce_init'
            ));
			add_action( 'pre_get_posts', array($this, 'fdoe_main_query' ),99);
			 if( get_option('fdoe_is_prem', 'no') == 'yes'  ){
			add_action( 'woocommerce_product_query', array($this,'custom_pre_get_posts_query' ),999);


			 }

            add_action( 'template_redirect', array(
                 $this,
                'check_for_404'
            ) );
            add_action( 'wp', array(
                 $this,
                'wp'
            ) ,99);
            add_action( 'admin_enqueue_scripts', array(
                 $this,
                'enqueue_scripts_back_end'
            ) );
            add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array(
                $this,
                'add_action_links'
            ) );


			add_action( 'fdoe_woocommerce_widget_shopping_cart_buttons',array($this, 'fdoe_custom_widget_shopping_cart_proceed_to_checkout'), 20 );
			add_filter( 'woocommerce_add_to_cart_fragments', array($this,'fdoe_woocommerce_header_add_to_cart_fragment') );
			$theme = wp_get_theme(); // gets the current theme
			if (  !('Storefront' == $theme->name || 'Storefront' == $theme->parent_theme )) {
			add_action( 'wp_footer', array($this, 'fdoe_handheld_footer_bar'),  999 );
			}else{
			add_filter( 'storefront_handheld_footer_bar_links', array($this,'fdoe_add_checkout_link_2') );
			add_filter( 'storefront_handheld_footer_bar_links', array($this,'fdoe_add_shop_link') );
			add_filter( 'storefront_handheld_footer_bar_links', array($this,'fdoe_add_cart_link') );
			add_filter( 'storefront_handheld_footer_bar_links', array($this,'fdoe_remove_handheld_footer_links') );
			set_theme_mod( 'storefront_product_pagination', false );
}
        }
        // Checks if environment is ok
        public function check_environment()
        {
            if ( is_admin() && get_option( 'Food_Online_Activated_Plugin' ) == 'Food_Online' ) {
                delete_option( 'Food_Online_Activated_Plugin' );
            }
            $environment_warning = self::get_environment_warning();
            if ( $environment_warning && is_plugin_active( plugin_basename( __FILE__ ) ) ) {
              $this->add_admin_notice( 'bad_environment', 'error', $environment_warning );
                deactivate_plugins( plugin_basename( __FILE__ ) );
            }
        }
        // Checks if WooCommerce is active and if not returns error message
         static function get_environment_warning()
        {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            if ( !defined( 'WC_VERSION' ) ) {
                return __( 'Food Online requires WooCommerce to be activated to work.', 'food-online-for-woocommerce' );
                die();
            }
			//if this is Premium

	/*		else if ( is_plugin_active( 'food-online-for-woocommerce/fdoe-order.php') ) {
                return __( 'Food Online Premium can not be activated when the free version is active.', 'food-online-for-woocommerce' );
                die();
            }*/

			// If this is free version

			else if ( is_plugin_active( 'food-online-premium/fdoe-order.php') ) {
                return __( 'Food Online can not be activated when the premuim version is active.', 'food-online-for-woocommerce' );
                die();
            }
            return false;
        }
        public function add_admin_notice( $slug, $class, $message )
        {
            $this->notices[ $slug ] = array(
                 'class' => $class,
                'message' => $message
            );
        }
        public function admin_notices()
        {
            foreach ( (array) $this->notices as $notice_key => $notice ) {
                echo "<div class='" . esc_attr( $notice[ 'class' ] ) . "'><p>";
                echo wp_kses( $notice[ 'message' ], array(
                     'a' => array(
                         'href' => array ()
                    )
                ) );
                echo '</p></div>';
            }
            unset( $notice_key );
        }
        public function wp()
        {
			add_action( 'fdoe_before_product_modal', array(
                 $this,
                'set_products_modal_hook'
            ) ,99);

            if(  (!is_admin() && is_shop() && get_option('fdoe_override_shop') == "yes") || wc_post_content_has_shortcode( 'foodonline' )  ){
			$this -> is_menu = true;
			self::set_is_shortcode();

			add_action( 'wp_footer', array(
                 $this,
                'load_templates'
            ) );
			add_action( 'woocommerce_before_shop_loop', array(
                 $this,
                'set_products_modal_hook'
            ) ,99);
			add_action( 'woocommerce_shortcode_before_products_loop', array(
                 $this,
                'set_products_modal_hook'
            ) ,99);



			add_action( 'wp_footer', array(
                 $this,
                'output_product_aromodals_backbone'
            ) );
			add_action( 'wp_footer', array(
                 $this,
                'fdoe_output_cart_aromodal'
            ) );
			remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering' );
			remove_action( 'woocommerce_after_shop_loop', 'woocommerce_catalog_ordering' );
			remove_action( 'woocommerce_before_shop_loop' , 'woocommerce_result_count' );
			remove_action( 'woocommerce_after_shop_loop' , 'woocommerce_result_count' );


			add_filter( 'woocommerce_show_page_title', '__return_false' );
			self::fdoe_storefront_sorting_remove();
                return;
			}else{
			$this -> is_menu = false;

            $this-> override_off();
			}
        }
        // Add setting link to Plugins page
        public function add_action_links( $links )
        {
			 if(get_option('fdoe_is_prem') == 'no'){
            $links_add = array(
                 '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=fdoe&section' ) . '">Settings</a>',
				 '<a href="http://foodonlineplugin.com">Go Premium</a>',
            );
			}
			else{
				 $links_add = array(
                 '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=layout&section' ) . '">Settings</a>');
			}
            return array_merge( $links, $links_add);
        }
        // Loads the templates
        public function load_templates()
        {
            include( 'templates/product.php' );
			include( 'templates/product-modal.php' );
			include( 'templates/categories.php' );
			include( 'templates/top_menu.php' );
        }

	    // Enqueues scripts and styles front end
        public function enqueue_scripts()
        {
	   if( WP_DEBUG === true ) {
                wp_enqueue_style( 'fdoe-order-style', $this->get_plugin_url( 'assets/css/style.css' ) ,array(), FOOD_ONLINE_VERSION);
                wp_enqueue_script( 'fdoe-order', $this->get_plugin_url( 'assets/js/fdoe-order.js' ), array(
                    'jquery',
                    'backbone',
					'wc-add-to-cart-variation',

                ), FOOD_ONLINE_VERSION, true );
            } else {
                wp_enqueue_style( 'fdoe-order-style', $this->get_plugin_url( 'assets/css/style.min.css' ), array(), FOOD_ONLINE_VERSION );
                wp_enqueue_script( 'fdoe-order', $this->get_plugin_url( 'assets/js/fdoe-order.min.js' ), array(
                    'jquery',
                    'backbone',
					'wc-add-to-cart-variation',



                ), FOOD_ONLINE_VERSION, true );
            }
			if(get_option('fdoe_product_popup_content') == 'custom'){

				wp_enqueue_style( 'fdoe-product-modal-style', $this->get_plugin_url( 'assets/css/cutom-style.css' ) ,array(), FOOD_ONLINE_VERSION);

			}
			if(wp_script_is('jquery-tiptip') == false){

				  wp_enqueue_script( 'jquery-tiptip', content_url( '/plugins/woocommerce/assets/js/jquery-tiptip/jquery.tipTip.min.js' ), array(
                     'jquery',


                ), FOOD_ONLINE_VERSION, true );


			}

            self::set_categories();
            $theme = wp_get_theme();

			// Check the version of WooCommerce Prodduct Addons installed
			if ( defined( 'WC_PRODUCT_ADDONS_VERSION' ) && version_compare( WC_PRODUCT_ADDONS_VERSION, '3.0.0', '>=' ) ) {
				$addonabove3 = true;

				if( WP_DEBUG === true ) {
				wp_dequeue_script( 'fdoe-order' );
				wp_enqueue_script( 'fdoe-order', $this->get_plugin_url( 'assets/js/fdoe-order.js' ), array(
                    'jquery',
                    'backbone',
					'wc-add-to-cart-variation',
					'woocommerce-addons'


                ), FOOD_ONLINE_VERSION, true );

				}else {

				 wp_dequeue_script( 'fdoe-order' );
				 wp_enqueue_script( 'fdoe-order', $this->get_plugin_url( 'assets/js/fdoe-order.min.js' ), array(
                    'jquery',
                    'backbone',
					'wc-add-to-cart-variation',
					'woocommerce-addons'


                ), FOOD_ONLINE_VERSION, true );


				}



			}else{
				$addonabove3 = false;
			}
            $menu_color = WC_Admin_Settings::get_option( 'fdoe_color' );
            $args       = array(
				'fdoe_item_separator' => get_option('fdoe_item_separator'),
                'is_checkout' => is_checkout(),
                'menu_color' => $menu_color,
                'fdoe_show_images' => WC_Admin_Settings::get_option( 'fdoe_show_images' ,'no'),
                'cats' => self::get_categories_raw(),
                'base_path' => get_option( 'siteurl' ),
                'cart_remove_url' => wc_get_cart_remove_url( 'fdoe_item_key' ),
                'wc_ajax_url' => WC_AJAX::get_endpoint( '%%endpoint%%' ),
				'layout' => WC_Admin_Settings::get_option('fdoe_layout'),
				'hide_minicart' => WC_Admin_Settings::get_option('fdoe_hide_minicart'),
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'theme' => $theme->name,
				'theme_parent' => $theme->parent_theme,
				'is_shop' => is_shop(),
				'is_product_category' => is_product_category(),
				'js_frontend' => $this->is_menu ? 1 : 0,
				'popup_simple'  => get_option('fdoe_popup_simple','yes'),
				'popup_variable' => get_option('fdoe_popup_variable','yes'),
				'show_left_menu' => get_option('fdoe_left_menu', 'no') == 'no' ? 1 : 0,
				'addonabove3' => $addonabove3,
				'sticky_bar' => get_option('fdoe_sticky_bar', 'no'),
				'minicart_style' => get_option('fdoe_minicart_style','popover'),
				'smooth_scrolling' => get_option('fdoe_smooth_scrolling','no'),
				'fdoe_pickup_fixed' => intval(get_option('fdoe_pickup_fixed','0')),
				'fdoe_pickup_var' => intval(get_option('fdoe_pickup_var','0')),
				'close_text' => __( 'Close', 'food-online-for-woocommerce' ),
				'menu_text' => __( 'Menu', 'food-online-for-woocommerce' ),
				'is_user_logged_in' => is_user_logged_in() ? 1 : 0,
				'is_prem' => get_option('fdoe_is_prem','no') == 'yes' ? 1 : 0,
				'cat_order' => get_option('fdoe_category_order',array()),
				'cat_icon' => get_option('fdoe_menu_titles_icon',''),
				'cat_image'	=> get_option('fdoe_cat_image', 'no'),

            );
            wp_localize_script( 'fdoe-order', 'fdoe', $args );
        }
function add_font_awesome_attributes( $html, $handle ) {
    if ( 'fdoe-order-font-2' === $handle ) {
        return str_replace( "media='all'",'rel="preload" media="all"', $html );
    }
    return $html;
}

		public function load_bootstrap_and_fontawesome(){

		if( $this->is_menu ){
		wp_enqueue_style( 'fdoe-order-boot-css', $this->get_plugin_url( 'assets/bootstrap/css/bootstrap.min.css' ) );
        wp_enqueue_script( 'fdoe-order-boot-js', $this->get_plugin_url( 'assets/bootstrap/js/bootstrap.min.js' ),array( 'jquery' ),true);
		}

			 	wp_enqueue_style( 'fdoe-order-font-1', $this->get_plugin_url( 'assets/fontawesome/css/fontawesome.min.css' ) );
				wp_enqueue_style( 'fdoe-order-font-2', $this->get_plugin_url( 'assets/fontawesome/css/solid.min.css' ) );
				wp_enqueue_style( 'fdoe-order-font-4', $this->get_plugin_url( 'assets/fontawesome/css/regular.min.css' ) );
		}
		function custom_pre_get_posts_query( $q ) {


		$cats_order =  get_option('fdoe_category_order',null);
		if(is_array($cats_order) && count($cats_order)>0){
		$cats_order_2 = array_column($cats_order,'ID');
			// Fix for PHP 7.0.32-33 where array_column is broken
			if(empty($cats_order_2)):
				$cats_order_2 = array_map(function ($each) {
					return $each['ID'];
					}, $cats);
				endif;

    $tax_query = (array) $q->get( 'tax_query' );

	$tax_query[] = array(
           'taxonomy' => 'product_cat',
           'field' => 'id',
           'terms' => $cats_order_2,
           'operator' => 'IN',

    );


    $q->set( 'tax_query', $tax_query );
		}

}


		 function fdoe_main_query($query ){
		// Check if shop page
		if(!is_ajax()){
		if( (($query->is_post_type_archive( 'product' ) || $query->is_page( wc_get_page_id( 'shop' ))) && get_option('fdoe_override_shop') == "yes" ) || wc_post_content_has_shortcode( 'foodonline' ) ){
			$num = get_option('fdoe_is_prem') == 'yes' ? max( sqrt(self::$fdoe_doit[0]) , self::$fdoe_doit[1] ) : min( sqrt(self::$fdoe_doit[0]) , self::$fdoe_doit[1] ) ;
			$query->set( 'posts_per_page', $num );
			$query->set('order', 'ASC' );
			$query->set('orderby', 'title');
			}
		}
			}
        public function woocommerce_init()
        {
            $this->product = new Food_Online_Product();
			$this->ajax = new Food_Online_Ajax();
			$this->orders = new Food_Online_Orders();

			$this->loop    = new Food_Online_Loop( $this->product );

			$this->shortcode = Food_Online_Shortcode::instance();
			$this->override_on();
		}
        // For use in future versions. Loads text domain files
        public function load_text_domain()
        {
            load_plugin_textdomain( 'food-online-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

        }
        // Disables the template override if 404 eror
        public function check_for_404()
        {
            if ( is_404() ){
                $this->override_off();
			}
        }
        // Turns on template overriding
        public function override_on()
        {
            add_filter( 'woocommerce_locate_template', array(
                 $this,
                'override_templates'
            ), 999, 3 );
            add_filter( 'wc_get_template_part', array(
                 $this,
                'override_template_part'
            ), 999, 3 );

			add_filter( 'wc_get_template', array( $this, 'override_cart_template'), 999, 5 );



        }
// add the filter



	public function override_cart_template( $located, $template_name, $args, $template_path, $default_path ) {

		if ( 'cart/mini-cart.php' == $template_name && WC_Admin_Settings::get_option('fdoe_minicart_style','popover') !== 'theme'    ) {

        $located = plugin_dir_path( __FILE__ ) . 'templates/cart/mini-cart.php';

	}

 else if(isset($this -> is_menu) && $this -> is_menu || is_ajax() ){


	if ( 'single-product/add-to-cart/simple.php' == $template_name ) {
        $located = plugin_dir_path( __FILE__ ) . 'templates/woocommerce/single-product/add-to-cart/simple.php';
		}

	else if ( 'single-product/related.php' == $template_name  ) {

        $located = plugin_dir_path( __FILE__ ) . 'templates/woocommerce/related/related.php';
	}


	else if ( 'single-product/product-image.php' == $template_name ) {


        $located = plugin_dir_path( __FILE__ ) . 'templates/woocommerce/single-product/image/product-image.php';
	}

	//Ratings
	else if( $this -> is_prem  ){

	 if (  'single-product/review-rating.php' == $template_name  ) {


        $located = plugin_dir_path( __FILE__ ) . 'prem/templates/woocommerce/single-product/review-rating.php';
	}
	else if (  'single-product/rating.php' == $template_name  ) {


        $located = plugin_dir_path( __FILE__ ) . 'prem/templates/woocommerce/single-product/rating.php';
	}
	else if (  'single-product/review.php' == $template_name   ) {


        $located = plugin_dir_path( __FILE__ ) . 'prem/templates/woocommerce/single-product/review.php';
	}
	}



 }
    return $located;
}
        // Turns off template overriding
        public function override_off()
        {
			// Do not remove  overriding when Ajax
			if( !is_ajax() ){
			remove_filter( 'wc_get_template', array(
				$this,
				'override_cart_template'
				),999 );
			 remove_filter( 'woocommerce_locate_template', array(
                 $this,
                'override_templates'
            ),999 );
            remove_filter( 'wc_get_template_part', array(
                 $this,
                'override_template_part'
            ),999 );
			}

        }
        // Gets path to templates
        public function get_template_path( $relativePath = '' )
        {
            return $this->base_path( '/templates/' . WC()->template_path() . $relativePath );
        }
        // Gets the base path
        protected function base_path( $relativePath = '' )
        {
            $rc = new \ReflectionClass( get_class( $this ) );
            return dirname( $rc->getFileName() ) . $relativePath;
        }
        // Performs the template overriding
         function override_templates( $template, $template_name, $template_path )
        {


			//
		if( isset($this->is_menu) && $this -> is_menu || is_ajax()){
            global $woocommerce;
            $_template = $template;
            if ( !$template_path ) {
                $template_path = $woocommerce->template_url;
            }
            $plugin_path = $this->get_template_path();
            if ( file_exists( $plugin_path . $template_name ) ) {
                $template = $plugin_path . $template_name;
            }
            if ( !$template ) {
                $template = locate_template( array(
                     $template_path . $template_name,
                    $template_name
                ) );
            }
            if ( !$template ) {
                $template = $_template;
            }
            return $template;
			}else{
				return $template;
			}
        }
        // Gets the url to the plugin
        protected function get_plugin_url( $relativePath = '' )
        {
            return untrailingslashit( plugins_url( $relativePath, $this->base_path_file() ) );
        }
        // Gets the class filenamne
        protected function base_path_file()
        {
            $rc = new \ReflectionClass( get_class( $this ) );
            return $rc->getFileName();
        }
        public function override_template_part( $template, $slug, $name )

{


				if( isset($this->is_menu) && $this -> is_menu || is_ajax()){
            if ( $name ) {
                $path = $this->get_template_path( "{$slug}-{$name}.php" );
            } else {
                $path = $this->get_template_path( "{$slug}.php" );
            }
            return file_exists( $path ) ? $path : $template;
			}else{
				return $template;
			}
        }
		// Adds new checkout button
 public function fdoe_custom_widget_shopping_cart_proceed_to_checkout()
{
    $original_link = wc_get_checkout_url();
    echo '<a href="' . esc_url( $original_link ) . '" class="button checkout from_menu" id="checkout_button_1">' . esc_html__( 'Go to Checkout', 'food-online-for-woocommerce' ) . '</a>';
}
// Adds checkout button in the handheld footer bar for Storefront Theme
public function fdoe_add_checkout_link_2( $links )
{
	 function fdoe_checkout_link_2()
{
    if( ( is_shop() && get_option('fdoe_override_shop') == 'yes')  || wc_post_content_has_shortcode( 'foodonline' )){
        echo '<a class="checkout from_menu" id="checkout_button_11" href="' . esc_url( wc_get_checkout_url() ) . '" >';
        echo ' <span class="fdoe_checkout_22">' . esc_html__( 'Checkout', 'food-online-for-woocommerce' ) . '</span> ';
        echo '</a>';
    }
}
    $new_links = array(
         'fdoe_checkout_2' => array(
             'priority' => 8,
             'callback' => 'fdoe_checkout_link_2'
        )
    );
    $links     = array_merge( $new_links, $links );
    return $links;
}
public function fdoe_add_shop_link( $links )
{
    $new_links = array(
         'shop' => array(
             'priority' => 10,
            'callback' => array(
                 $this,'fdoe_shop_link')
        )
    );
    $links     = array_merge( $new_links, $links );
    return $links;
}
public function fdoe_add_cart_link( $links )
{
	// Creates cart links for handheld devices in Storefront footer bar
 function fdoe_cart_link(){


    if(  (is_shop() && get_option('fdoe_override_shop') == "yes" ) || wc_post_content_has_shortcode( 'foodonline' )  ){
        $count = WC()->cart->cart_contents_count;
        echo ' <a  id="cart_aromodal_link" data-toggle="aromodal" data-target="#cart_aromodal" href="" title="">';
        echo ' <span class="fdoe_count">' . esc_html( $count ) . '</span> ';
        echo ' </a>';
    } else {
        $count = WC()->cart->cart_contents_count;
        echo '<a href="' . esc_url( get_permalink( wc_get_page_id( 'cart' ) ) ) . '">' ;
        echo ' <span class="fdoe_count">' . esc_html( $count ) . '</span>';
        echo ' </a>';
    }
}
    $new_links = array(
         'minicart' => array(
             'priority' => 10,
             'callback' => 'fdoe_cart_link'
        )
    );
    $links     = array_merge( $new_links, $links );
    return $links;
}
 public function fdoe_shop_link()
{

if( $this->is_menu  ){
	 echo ' <a   href="#menu_headings" >';
	 echo ' </a>';
	}
	elseif(get_option('fdoe_override_shop') == "yes") {
    echo '<a href="' . esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ) . '"></a>';
	}
	else {
	$pages = get_pages();
	foreach( $pages as $page ) {
		$content = $page->post_content;
     if ( has_shortcode( $content, 'foodonline' )  ) {
		echo '<a href="' . esc_url( get_permalink( $page->ID ) ) . '">' ;
		 break;
	 }
	}
}
return;
}
	/**
	 * Display a menu intended for use on handheld devices, inspired of Storefront
	 *
	 *
	 */
	public function fdoe_handheld_footer_bar() {
		 function fdoe_checkout_link_2()
{

    if(  (is_shop() && get_option('fdoe_override_shop') == "yes") || wc_post_content_has_shortcode( 'foodonline' )  ){
        echo '<a class="checkout from_menu" id="checkout_button_11" href="' . esc_url( wc_get_checkout_url() ) . '" >';
        echo ' <span class="fdoe_checkout_22">' . esc_html__( 'Checkout', 'food-online-for-woocommerce' ) . '</span> ';
        echo '</a>';
    }
}
 function fdoe_cart_link()
{
    if( ( is_shop() && get_option('fdoe_override_shop') == "yes") || wc_post_content_has_shortcode( 'foodonline' ) ){
        $count = WC()->cart->cart_contents_count;
        echo ' <a  id="cart_aromodal_link" data-toggle="aromodal" data-target="#cart_aromodal" href="" title="">';
        echo ' <span class="fdoe_count">' . esc_html( $count ) . '</span> ';
        echo ' </a>';
    } else {
        $count = WC()->cart->cart_contents_count;
        echo '<a href="' . esc_url( get_permalink( wc_get_page_id( 'cart' ) ) ) . '">' ;
        echo ' <span class="fdoe_count">' . esc_html( $count ) . '</span> ';
        echo ' </a>';
    }
}
		$links = array(
            'minicart' => array(
			'priority' => 8,
			'callback' => 'fdoe_cart_link',
		),
			'shop' => array(
			'priority' => 10,
			'callback' =>array(
                 $this,'fdoe_shop_link'),
		),
            'fdoe_checkout_2' => array(
			'priority' => 12,
			'callback' => 'fdoe_checkout_link_2',
		),
		);
		?>
<div class="fdoe-handheld-footer-bar">
	<ul class="columns-<?php echo count( $links ); ?>">
		<?php foreach ( $links as $key => $link ) : ?>
		<li class="<?php echo esc_attr( $key ); ?>">
			<?php
						if ( $link['callback'] ) {
							call_user_func( $link['callback'], $key, $link );
						}
						?> </li>
		<?php endforeach; ?> </ul>
</div>
<?php
	}
	// Updates the items count in Storefront footer bar
public function fdoe_woocommerce_header_add_to_cart_fragment( $fragments )
{
    global $woocommerce;
    ob_start();
?> <span class="fdoe_count"><?php echo $woocommerce->cart->cart_contents_count; ?>
 </span>
<?php
  $fragments[ 'span.fdoe_count' ] = ob_get_clean();
    return $fragments;
}
//Removes links from Storefront handheld footer bar
public function fdoe_remove_handheld_footer_links( $links )
{
    unset( $links[ 'my-account' ] );
    unset( $links[ 'search' ] );
    unset( $links[ 'cart' ] );
    return $links;
}
//Removes Storefront sorting option
public function fdoe_storefront_sorting_remove()
{
    remove_action( 'woocommerce_after_shop_loop', 'woocommerce_catalog_ordering', 10 );
    remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 10 );
	remove_action( 'woocommerce_before_shop_loop' , 'woocommerce_result_count' ,20);
	remove_action( 'woocommerce_after_shop_loop' , 'woocommerce_result_count' ,20);
	remove_action( 'woocommerce_before_shop_loop', 'storefront_woocommerce_pagination', 30 );
}

		// Output the mini cart in an aromodal for handheld devices
	public function fdoe_output_cart_aromodal()
{
ob_start();
    echo 	'<div class="fdoe-aromodals-wrap"><div class="aromodal cart-aromodal fdoe-aromodal fade" id="cart_aromodal"  tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display:none">
            <div class="aromodal-dialog aromodal-sm" role="document">
            <div class="aromodal-content">
            <div class="aromodal-header">
            <h5 class="aromodal-title" id="exampleModalLabel_cart">' . esc_html__( 'Your Order', 'food-online-for-woocommerce' ) . '</h5>
            </div>
            <div class="aromodal-body" >
            <div class="container">
			 <div class="row">
			<div class="arocol-xs-10 fdoe-minicart-main-column ">
            <div class="fdoe_mini_cart_2" id="fdoe_mini_cart_id_2" style="display:inline">
            ';
    woocommerce_mini_cart();
    echo '  </div>
            </div>
            </div>
            <div class="aromodal-footer" id="mini_cart_aromodal_footer" >
            <div class="container">
			 <div class="arocol-xs-10 fdoe-minicart-main-column">
			  <div class="row">
            <button type="button" class="button" data-dismiss="aromodal">' . esc_html__( 'Close', 'food-online-for-woocommerce' ) . '</button>
            </div>
			</div>
			 </div>
			</div>
            </div>
			</div>
            </div>
            </div>
            </div>
			</div>';
			$output = ob_get_clean();
			echo $output;
}



			// Output element wrapper and set hooks for product modals
		public function output_product_aromodals_backbone(){

		echo '<div class="fdoe-aromodals-wrap" id="fdoe-product-modals"><div  id="fdoe-product-modals-inner"></div></div>';

		}



		public function set_products_modal_hook(){

			// Food Online product modal template
			if(get_option('fdoe_product_popup_content') == 'custom' ){

				$old_image_option = get_option('fdoe_popup_image')== 'yes'? 'image':'';
				$old_meta_option = get_option('fdoe_popup_meta')=='yes'?'meta':'';
				$popup_content = WC_Admin_Settings::get_option('fdoe_product_popup_content_spec',array($old_image_option ,$old_meta_option ));


			remove_all_actions( 'woocommerce_before_single_product' );
			remove_all_actions( 'woocommerce_before_single_product_summary' );
			remove_all_actions( 'woocommerce_single_product_summary' );
			remove_all_actions('woocommerce_after_single_product_summary' );
			remove_all_actions( 'woocommerce_after_single_product' );

			add_action( 'woocommerce_single_product_summary_price_fdoe','woocommerce_template_single_price', 1 );

			add_action( 'woocommerce_single_product_summary_add_fdoe','woocommerce_template_single_add_to_cart', 30 );

			add_action('woocommerce_before_main_content','woocommerce_output_content_wrapper',10);
			add_action('woocommerce_before_main_content','woocommerce_breadcrumb',20);

			add_action( 'woocommerce_after_main_content','woocommerce_output_content_wrapper_end' );



			add_action( 'woocommerce_before_single_product_summary','woocommerce_show_product_sale_flash',10 );

			add_action( 'woocommerce_single_product_summary','woocommerce_template_single_title', 5 );


			 if ( in_array(  'image',$popup_content) !== false  ){
             add_action( 'woocommerce_single_product_summary_image_fdoe', 'woocommerce_show_product_images', 7 );
             }
			  if ( in_array(  'meta',$popup_content ) !== false  ){
             add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
             }
			  if ( in_array(  'rating',$popup_content ) !== false  && get_option('fdoe_is_prem', 'no') == 'yes'){
             add_action( 'woocommerce_single_product_summary','woocommerce_template_single_rating', 10 );
			 add_action( 'woocommerce_review_before', 'woocommerce_review_display_gravatar', 10 );

			add_filter('comments_template' ,'fdoe_comments_template',999,1);
			add_action( 'woocommerce_single_product_summary' ,'comments_template');

             }
			 if ( in_array( 'desc',$popup_content ) !== false   && get_option('fdoe_is_prem', 'no') == 'yes'){
            add_action( 'woocommerce_single_product_summary','woocommerce_template_single_excerpt', 20 );
             }





			}
			else{

			// This is for the Theme option
			// Keep theese action removals for compatibility with versions prior to 2.3.8
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
            remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );


			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

            remove_action( 'woocommerce_review_before', 'woocommerce_review_display_gravatar', 10 );
            remove_action( 'woocommerce_review_before_comment_meta', 'woocommerce_review_display_rating', 10 );
            remove_action( 'woocommerce_review_comment_text', 'woocommerce_review_display_comment_text', 10 );
            remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );




            remove_action( 'woocommerce_after_single_product_summary', 'storefront_upsell_display', 15 );
            remove_action( 'woocommerce_after_single_product_summary', 'storefront_single_product_pagination', 40 );

			}

		}




    } // End of main class
}
$GLOBALS[ 'wc_list_items' ] = Food_Online::instance();
// Mini cart adjustments
add_filter( 'wc_add_to_cart_message_html', '__return_false' );
