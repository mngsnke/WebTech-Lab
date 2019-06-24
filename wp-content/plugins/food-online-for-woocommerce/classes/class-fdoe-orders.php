<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( !class_exists( 'Food_Online_Orders' ) ) {
    /**
		 * Main Class Food_Online_Orders
		 *
		 * @since 2.3.1
		 */
    class Food_Online_Orders
    {
            // The Constructor
        public function __construct()
        {
             add_action( 'wp', array(
                 $this,
                'init'
            ), 10 );
			 add_action( 'init', array(
                 $this,
                'init2'
            ), 10 );
        }
		public function init2(){
			 add_action( 'wp_ajax_nopriv_get_store_location', array($this,'get_store_location') );
			add_action( 'wp_ajax_get_store_location',array($this, 'get_store_location') );
		}
    public function init(){
         if( (is_shop() && get_option('fdoe_override_shop') == "yes") || wc_post_content_has_shortcode( 'foodonline' ) ){
        add_action( 'fdoe_loop_end_3', array($this, 'output_order_time'));
    }
    }
    public function output_order_time(){
		$is_prem = get_option('fdoe_is_prem','no');
        $for_pickup = get_option('fdoe_ready_for_pickup_show','none');
        $for_delivery = get_option('fdoe_ready_for_delivery_show','none');
		$is_free = $is_prem == 'no' || get_option('fdoe_enable_delivery_switcher','no') == 'no'  ? 'style="display:flex; justify-content:center; align-items:center;"' :'';
	if($for_pickup !== 'none' || $for_delivery !== 'none'){
ob_start();
echo 	'<div class="fdoe_order_time fdoe_hidden" >';
// For pickup orders
switch ($for_pickup) {
    case 'fixedtime':

        echo '  <span class="fdoe_pickup_time" '.$is_free.'><i class="far fa-clock "></i>' .'&nbsp;'. esc_html(intval( get_option('fdoe_pickup_fixed',''))).__( ' minutes', 'food-online-for-woocommerce' ).'</span>';
        break;
     case 'variable':

        $time =  intval(get_option('fdoe_pickup_fixed','0'))+intval(get_option('fdoe_pickup_var','0') ) * intval(self::get_processing_orders());
        echo '  <span class="fdoe_pickup_time" '.$is_free.'><i class="far fa-clock "></i>' .'&nbsp;'.esc_html($time) .__( ' minutes', 'food-online-for-woocommerce' ).'</span>';
        break;
    default:
}
// For delivery orders
if($is_prem == 'yes'):
switch ($for_delivery) {
    case 'fixedtime':
		$symbole = get_option('shipping_vehicle','DRIVING') == 'DRIVING' ? '<i class="fas fa-truck"></i>' : '<i class="fas fa-bicycle"></i>';
        echo '  <span class="fdoe_delivery_time" '.$is_free.'>'.$symbole .'&nbsp;'. esc_html(intval( get_option('fdoe_pickup_fixed',''))).__( ' minutes', 'food-online-for-woocommerce' ).'</span>';
        break;
     case 'variable':
		$symbole = get_option('shipping_vehicle','DRIVING') == 'DRIVING' ? '<i class="fas fa-truck"></i>' : '<i class="fas fa-bicycle"></i>';
        $time =  intval(get_option('fdoe_pickup_fixed','0'))+intval(get_option('fdoe_pickup_var','0') ) * intval(self::get_processing_orders());
        echo '  <span class="fdoe_delivery_time" '.$is_free.'>'.$symbole .'&nbsp;'.esc_html($time) .__( ' minutes', 'food-online-for-woocommerce' ).'</span>';
        break;
    case 'fixed_ship':

		if(get_option('fdoe_shipping_time','fixedtime') == 'fixedtime'){
			$symbole = get_option('shipping_vehicle','DRIVING') == 'DRIVING' ? '<i class="fas fa-truck"></i>' : '<i class="fas fa-bicycle"></i>';
        $time =  intval(get_option('fdoe_pickup_fixed','0')) + intval(get_option('fdoe_shipping_fixed','0'));
        echo '  <span class="fdoe_delivery_time" '.$is_free.'>'. $symbole.'&nbsp;'.esc_html($time) .__( ' minutes', 'food-online-for-woocommerce' ).'</span>';
		}
        break;
	case 'variable_ship':

		if(get_option('fdoe_shipping_time','fixedtime') == 'fixedtime'){
			$symbole = get_option('shipping_vehicle','DRIVING') == 'DRIVING' ? '<i class="fas fa-truck"></i>' : '<i class="fas fa-bicycle"></i>';
        $time =  intval(get_option('fdoe_pickup_fixed','0'))+intval(get_option('fdoe_pickup_var','0') ) * intval(self::get_processing_orders()) + intval(get_option('fdoe_shipping_fixed','0'));
        echo '<span class="fdoe_delivery_time" '.$is_free.'>'.$symbole .'&nbsp;'.esc_html($time) .__( ' minutes', 'food-online-for-woocommerce' ).'</span>';
		}
        break;
    default:
}
		endif;
	echo '</div>';
$output = ob_get_clean();
echo $output;
	 }
    }
    public static function get_processing_orders(){
$args = array(
    'status' => 'processing',
);
$orders = wc_get_orders( $args );
        return is_countable($orders) ? count($orders ) : 0;
    }
     public function get_shipping_time(){
        $shipping = get_option('fdoe_shipping_time','none');
     switch ($shipping) {
    case 'none':
       return 0;
        break;
     case 'fixedtime':
        $time =  intval(get_option('fdoe_shipping_fixed','0'));
       return $time;
        break;
    case 'calculate':
        $time =  intval(get_option('fdoe_pickup_fixed','0')) + self::get_shipping_time();
        break;
    default:
}
return;
    }
    public function get_store_location(){
// Collect the store address
$store_address     = get_option( 'woocommerce_store_address' ,'');
$store_address_2   = get_option( 'woocommerce_store_address_2','' );
$store_city        = get_option( 'woocommerce_store_city','' );
$store_postcode    = get_option( 'woocommerce_store_postcode','' );
$store_raw_country = get_option( 'woocommerce_default_country','' );
$split_country = explode( ":", $store_raw_country );
// Country and state
$store_country = $split_country[0];
$store_state   = isset($split_country[1]) ?  $split_country[1] : '';
        $args = array(
                      'store_address' => $store_address,
					  'store_city'	=> $store_city,
					  'store_postcode' => $store_postcode,
					  'store_country'	=> $store_country,
					  'store_state'	=> $store_state,
                      );
       wp_send_json($args);
    }
    }
}
