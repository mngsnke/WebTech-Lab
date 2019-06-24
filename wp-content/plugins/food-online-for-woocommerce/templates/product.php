

	<!-- The product template -->

	<script type="text/template" id="productTemplate">

<div class="flex-container-row">

		<%

	if ( fdoe.fdoe_show_images == "big")  {	%>

		<div data-label="" class="fdoe_thumb fdoe_thumb_big">

			<%= image.src %>

		</div>

		<% } else if( fdoe.fdoe_show_images == "rec"){

	%>

			<div data-label="" class="fdoe_thumb fdoe_thumb_normal">

				<%= image.src %>

			</div>

			<% }

			else if( fdoe.fdoe_show_images == "small"){

	%>

			<div data-label="" class="fdoe_thumb fdoe_thumb_small">

				<%= image.src %>

			</div>

			<% }

%>

				<div data-label="" class="fdoe_desc fdoe_summary">

					<div class="fdoe_title"> <h5><b> <%= title %></b><br><span class="fdoe_description"> <%= short_description %> </span></h5></div>



				</div>

						<% if (fdoe.layout == 'fdoe_onecol') {%>

						<span class='fdoe_price_and_add_onecol'>

							<div class="fdoe_item_price fdoe_desc fdoe_add_price_item">

							<%= price_html %>

							</div>

							<div class="fdoe_desc fdoe_add_item fdoe_add_price_item" >


										<% if( (is_variable && fdoe.popup_variable == "yes") || (is_simple && fdoe.popup_simple == "yes" )) {%>
										<%= cart_button %>

										<% }else if(in_stock){ %>
										<%= cart_button_add %>
										<% } %>



							</div>
						</span>
							<% }else{ %>
							<div class="fdoe_item_price fdoe_desc fdoe_add_price_item">

							<%= price_html %>

							</div>

							<div class="fdoe_desc fdoe_add_item fdoe_add_price_item" >


										<% if( (is_variable && fdoe.popup_variable == "yes") || (is_simple && fdoe.popup_simple == "yes" )) {%>
										<%= cart_button %>

										<% }else if(in_stock){ %>
										<%= cart_button_add %>
										<% } %>



							</div>
							<% } %>



</div>



	</script>
