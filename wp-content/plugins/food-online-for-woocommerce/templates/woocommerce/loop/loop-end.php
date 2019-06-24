<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}?>

							<?php global $woocommerce; ?> </div>

					</div>

				</div>

				<div class="fdoe_extra_checkout" style="display:none">

					<?php

    $original_link = wc_get_checkout_url();

    echo '<a href="' . esc_url( $original_link ) . '" class="button checkout from_menu" id="checkout_button_1">' . esc_html__( 'Go to Checkout', 'food-online-for-woocommerce' ) . '</a>';

?> </div>

				<!-- The right column containing the mini cart -->

				<div class=" arocol-sm-3 arocol-lg-3 fdoe-less-gut" id="fdoe-right-container" style="display:none">
					<div class="fdoe-right-sticky">

					<?php do_action('fdoe_loop_end_1'); ?>
					<?php do_action('fdoe_loop_end_3'); ?>

					<div class=fdoe_mini_cart_outer>

						<h4 class="Minicart_heading">

							<?php echo __( 'Your Order', 'food-online-for-woocommerce' ); ?> </h4>

						<div class="fdoe_mini_cart" id="fdoe_mini_cart_id">

							<div class="widget_shopping_cart_content">

								<?php //woocommerce_mini_cart(); ?> </div>

						</div>

					</div>
					</div>
				</div>

			</div>

		</div>

	</div>

</div>

<!--   <div class="container-fluid" id="the_main_container"> -->

<script>

	// Collect the looped products into a javascript array

	var Food_Online_Items = <?php echo json_encode(Food_Online::instance()->loop->get_loop_items()) ?>;



</script>

<?php do_action('fdoe_loop_end_2'); ?>
