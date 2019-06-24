<?php

if ( !defined( 'ABSPATH' ) ) {

    exit;

}

if ( !class_exists( 'Food_Online_Settings' ) ){

    // Adds the plugin settings tab to the WooCommerce settings page

    function Food_Online_Add_Tab()

    {

         /**

		 * Class Food_Online_Settings

		 *

		 * @since 1.0

		 */

        class Food_Online_Settings extends WC_Settings_Page

        {

            // The constructor

            public function __construct()

            {

                $this->id    = 'fdoe';

                $this->label = __( 'Food Online', 'food-online-for-woocommerce' );

                add_filter( 'woocommerce_settings_tabs_array', array(

                     $this,

                    'add_settings_page'

                ), 20 );

                add_action( 'woocommerce_settings_' . $this->id, array(

                     $this,

                    'output'

                ) );

                add_action( 'woocommerce_settings_save_' . $this->id, array(

                     $this,

                    'save'

                ) );

                add_action( 'woocommerce_sections_' . $this->id, array(

                     $this,

                    'output_sections'

                ) );



                add_filter( 'woocommerce_admin_settings_sanitize_option', array(

                     $this,

                    'sanitize_callback'

                ), 10, 3 );

				add_action('admin_notices', array( $this, 'premium_admin_notice'));

            }

		   function premium_admin_notice(){

    if ( isset ($_GET['tab']) && $_GET['tab'] == 'fdoe' ) {
		if( mt_rand (1,2) == 1):
		echo '<div class="notice notice-success is-dismissible">

            <div class="fdoe_premium">

            	<table>

                	<tbody><tr>

                    	<td width="70%">

                        	<p style="font-size:1.3em"><strong><i>Food Online Premium </i></strong>provides more features</p>

                            <ul class="fa-ul" id="fdoe_premium_ad">

								<li ><span class="fa-li" ><i class="fas fa-check" style="color:green"></i></span>	Display more than 50 Menu Items</li>


                            	<li ><span class="fa-li" ><i class="fas fa-check" style="color:green"></i></span>	Delivery or Pick-Up selector at Menu page</li>

							<li ><span class="fa-li" ><i class="fas fa-check" style="color:green"></i></span>	Calculate delivery time with Google Maps</li>

								<li ><span class="fa-li" ><i class="fas fa-check" style="color:green"></i></span>	Choose to show more content in product popups</li>


                                <li> <span class="fa-li" ><i class="fas fa-check" style="color:green"></i></span>	Delivery addresses validation with Google Maps & Minimum order value for delivery</li>

                                <li ><span class="fa-li" ><i class="fas fa-check" style="color:green"></i></span>	Decide which categories to show in Menu</li>

								<li ><span class="fa-li" ><i class="fas fa-check" style="color:green"></i></span>	Decide the order of categories in Menu</li>

                                <li ><span class="fa-li" ><i class="fas fa-check" style="color:green"></i></span>	Up-Sell products reminder at checkout</li>





								  <li >	and more...</li>



								 <li> Or if you just like our free plugin, give us a <a target="_blank" rel="noopener noreferrer" href=" https://wordpress.org/support/plugin/food-online-for-woocommerce/reviews?rate=5#new-post"><span id="fdoe_star_rating"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span> five star rating</a></li>

                            </ul>

                        </td>

                        <td>

                            <a target="_blank" rel="noopener noreferrer" href="http://www.foodonlineplugin.com" class=" button_premium" ><p style="font-size:1.2em">Upgrade To Premium </p><p>Learn More <i class="fas fa-arrow-right"></i></p></a>

                        </td>

                    </tr>

                </tbody></table>

            </div>

         </div>';
		endif;
		if( mt_rand (1,3) == 1):
        echo '<div class="notice notice-info is-dismissible">

            <div class="fdoe_premium">

            	<table>

                	<tbody><tr>

                    	<td width="100%">

                        	<p style="font-size:1.3em"><strong><i>New! Shipping Zones by Drawing </i></strong>let you draw your own shipping zones</p>

                            <ul class="fa-ul" id="fdoe_premium_ad">

								<li ><span class="fa-li" ><i class="fas fa-check" style="color:#00a0d2"></i></span>	Draw your own shipping zones for WooCommerce into a map</li>

                            	<li ><span class="fa-li" ><i class="fas fa-check" style="color:#00a0d2"></i></span>	Define a shipping cost for every zone</li>



                                <li ><span class="fa-li" ><i class="fas fa-check" style="color:#00a0d2"></i></span>	Compatible with Food Online</li>
								 <a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/plugins/shipping-zones-by-drawing-for-woocommerce/" class=" " ><p style="display: inline-block;
    padding: 12px 20px;
    border-radius: 8px;
    border: 0;
    font-weight: bold;
    letter-spacing: 0.0625em;
    text-decoration: none;
    background: #00a0d2;
    color: #fff;
    text-align: center;">Get it for free!</p><p></p></a>


                            </ul>

                        </td>



                    </tr>

                </tbody></table>

            </div>

         </div>';
		 endif;

    }

}

            public function save()

            {

                global $current_section;

                $settings = $this->get_settings( $current_section );

                WC_Admin_Settings::save_fields( $settings );

            }

            public function output()

            {

                global $current_section;

                $settings = $this->get_settings( $current_section );

                WC_Admin_Settings::output_fields( $settings );

            }

            public function sanitize_callback( $value, $option, $raw_value )

            {

                global $current_section;

                return $value;

            }

            public function get_settings_( $current_section = '' )

            {

                include( plugin_dir_path( __DIR__ ) . 'includes/start-args.php' );

                $settings = apply_filters( 'fdoe_section1_settings', $settings_args );

                return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );

            }
			 public function get_settings( $current_section = '' )
            {
                if ( 'third' == $current_section ){
                    include (plugin_dir_path( __DIR__ ) . 'includes/start-args.php');
                    $settings = apply_filters( 'fdoe_section1_settings', $settings_args_third);
                } else {
                    include (plugin_dir_path( __DIR__ ) . 'includes/start-args.php');
                    $settings = apply_filters( 'fdoe_section1_settings', $settings_args);


                }
                return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );
            }
			public function get_sections( )
            {
                $sections = array(
                     '' => __( 'Settings', 'food-online-for-woocommerce' ),

                    'third' => __( 'Order Time Management', 'food-online-for-woocommerce' )
                );
                return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
            }

        }

        return new Food_Online_Settings();

    }

    add_filter( 'woocommerce_get_settings_pages', 'Food_Online_Add_Tab', 15 );

}
