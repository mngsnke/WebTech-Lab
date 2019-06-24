

	<!-- The category template -->

	<script type="text/template" id="categoryTemplate">

	<div class="menu_titles"><span class="fdoe_title_text"> <i class=" <%= fdoe.cat_icon %> fa-xs fdoe-menu-title-icon"></i>

    <%= cat_name %>

	<i class=" <%= fdoe.cat_icon %> fa-xs fdoe-menu-title-icon"></i></span>

    <% if ( fdoe.is_prem && fdoe.cat_image == 'yes' && image){ %>

        <div class="menu_titles_image">

		   <img src="<%= image %>" alt="<%=  cat_name %>" />


			</div>
   <% } %>



	</div>

	</script>
