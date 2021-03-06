jQuery(function(t) {
	var cat_ids;
	if (fdoe.is_prem == 1) {
		var cat_ids_raw = _.pluck(fdoe.cat_order, 'ID');
		cat_ids = cat_ids_raw.map(function(x) {
			return parseInt(x, 10);
		});
	}
	// Backbone model, collection and views
	if (
		Category = Backbone.Model.extend({
			defaults: {
				"updating": false
			},
			initialize: function() {
				this.set('id', this.get("cat_ID"));
			}
		}),
		Categories = Backbone.Collection.extend({
			parse: function(t) {
				return this.url = t.products
			},
			model: Category,
			filterByIds: function(idArray) {
				return this.reset(_.map(idArray, function(id) {
					return this.get(id);
				}, this));
			}
		}),
		Product = Backbone.Model.extend({
			defaults: {
				"updating": false
			}
		}),
		Products = Backbone.Collection.extend({
			parse: function(t) {
				return this.url = t.products
			},
			model: Product,
			firstAsCollection: function(numItems) {
				var models = this.first(numItems);
				return new Products(models);
			},
			restAsCollection: function(numItems) {
				var models = this.rest(numItems);
				return new Products(models);
			},
			filterById: function(idArray) {
				return this.reset(_.map(idArray, function(cat_id) {
					return this.get(cat_id);
				}, this));
			}
		}),
		CategoryView = Backbone.View.extend({
			tagName: "div",
			className: "cat_tbody scrollspy",
			isUpdating: !1,
			initialize: function() {
				this.$el.attr("id", "menucat_" + this.model.get("cat_ID")),
					this.template = _.template(t("#categoryTemplate").html())
			},
			render: function() {
				var t = _.extend(this.model.attributes, {});
				return this.$el.html(this.template(t)), this
			},
		}),
		TopmenuView = Backbone.View.extend({
			tagName: "li",
			isUpdating: !1,
			initialize: function() {
				this.$el.attr("id", "headingtop_menucat_" + this.model.get("cat_ID")),
					this.template = _.template(t("#topmenuTemplate").html())
			},
			render: function() {
				var t = _.extend(this.model.attributes, {});
				return this.$el.html(this.template(t)), this
			},
		}),
		SidemenuView = Backbone.View.extend({
			tagName: "div",
			className: 'fdoe_menuitem',
			isUpdating: !1,
			initialize: function() {
				this.$el.attr("id", "heading_menucat_" + this.model.get("cat_ID")),
					this.template = _.template(t("#topmenuTemplate").html())
			},
			render: function() {
				var t = _.extend(this.model.attributes, {});
				return this.$el.html(this.template(t)), this
			},
		}),
		CategoryListView = Backbone.View.extend({
			sortedproducts: null,
			container: null,
			el: ".fdoe-products",
			initialize: function() {
				this.listenTo(this.collection, "reset", this.reset), this.listenTo(this.collection, "reset", this.destroy_view)
			},
			render: function() {
				sortedproducts = new Products;
				container = document.createDocumentFragment();
				this.collection.categories.forEach(this.addOne, this);
				t('#the_menu').append(container);
				return sortedproducts;
			},
			reset: function() {
				this.$el.find("div.cat_tbody").remove(), this.render()
			},
			destroy_view: function() {
				this.undelegateEvents();
				this.$el.removeData().unbind();
				this.remove();
				Backbone.View.prototype.remove.call(this);
			},
			addOne: function(e) {
				var o = new CategoryView({
					model: e
				});
				var container2 = o.render().el;
				var m = this.collection.products.filter(function(b) {
					return _.indexOf(b.get("cat_id"), e.get("cat_ID"), false) !== -1;
				});
				sortedproducts.add(m);
				m.forEach(function(e) {
					var p = new ProductView({
						model: e
					});
					container2.append(p.render().el);
				}, container2);
				container.append(container2);
			},
		}),
		MenuView = Backbone.View.extend({
			container: null,
			container2: null,
			el: "#menu_headings",
			initialize: function() {
				this.listenTo(this.collection, "reset", this.reset), this.listenTo(this.collection, "reset", this.destroy_view)
			},
			render: function() {
				container2 = document.createDocumentFragment();
				container = document.createDocumentFragment();
				this.collection.categories.forEach(this.addOne, this);
				t('#menu_headings').append(container);
				t('#menu_headings_2').append(container2);
			},
			reset: function() {},
			destroy_view: function() {
				// COMPLETELY UNBIND THE VIEW
				this.undelegateEvents();
				this.$el.removeData().unbind();
				// Remove view from DOM
				this.remove();
				Backbone.View.prototype.remove.call(this);
			},
			addOne: function(e) {
				var topmenu = new TopmenuView({
					model: e
				});
				container.append(topmenu.render().el);
				var sidemenu = new SidemenuView({
					model: e
				});
				container2.append(sidemenu.render().el);
			},
		}),
		ProductView = Backbone.View.extend({
			tagName: "div",
			className: "fdoe-item",
			isUpdating: !1,
			initialize: function() {
				if ((fdoe.popup_variable == "yes" && this.model.get("is_variable")) || (fdoe.popup_simple == "yes" && this.model.get("is_simple"))) {
					this.$el.attr("role", "button"), this.$el.attr("data-toggle", "aromodal"), this.$el.attr("data-target", "#myModal_" + this.model.get("parent_id"))
				} else {
					this.$el.addClass('fdoe_is_button')
				}
				this.$el.addClass('fdoe-border-' + fdoe.fdoe_item_separator), this.$el.addClass(fdoe.layout), this.$el.attr("id", "fdoe_item_" + this.model.get("id") + _.random(0, 100)),
					this.template = _.template(t("#productTemplate").html())
			},
			render: function() {
				var t = _.extend(this.model.attributes, {});
				return this.$el.html(this.template(t)), this
			},
		}),
		ProductViewModal = Backbone.View.extend({
			tagName: "div",
			className: "fdoe-modal-wrapper",
			isUpdating: !1,
			initialize: function() {
				this.template = _.template(t("#productmodalTemplate").html())
			},
			render: function() {
				var t = _.extend(this.model.attributes, {});
				return this.$el.html(this.template(t)), this
			},
		}),
		ProductListViewModal = Backbone.View.extend({
			el: "#fdoe-product-modals-inner",
			initialize: function() {
				this.listenTo(this.collection, "reset", this.reset), this.listenTo(this.collection, "reset", this.destroy_view)
			},
			render: function() {
				this.$el.empty();
				var container = document.createDocumentFragment();
				this.collection.forEach(function(e) {
					var o = new ProductViewModal({
						model: e
					});
					container.appendChild(o.render().el);
				}, this);
				t('#fdoe-product-modals').append(container);
			},
			reset: function() {
				this.$el.find("div.fdoe-modal-wrap-test").remove(), this.render()
			},
			destroy_view: function() {
				// COMPLETELY UNBIND THE VIEW
				this.undelegateEvents();
				this.$el.removeData().unbind();
				// Remove view from DOM
				this.remove();
				Backbone.View.prototype.remove.call(this);
			},
			addOne: function(e) {
				var o = new ProductViewModal({
					model: e
				});
				t('#fdoe-product-modals').append(o.render().el);
			}
		}),
		"undefined" != typeof Food_Online_Items) {
		t(".woocommerce-pagination").hide();
		var i = new Products;
		var u = new Categories;
		u.add(fdoe.cats);
		var uuu = u.reject({
			category_count: 0
		});
		var uu = new Categories(uuu);
		i.add(Food_Online_Items.products);
		var q;
		if (fdoe.is_prem == 1 && typeof fdoe_short === 'undefined') {
			var m = uu.filterByIds(cat_ids);
			q = new Categories(m);
		} else if ((typeof fdoe_short !== 'undefined') && (typeof fdoe_short.cats !== 'undefined') && fdoe_short.cats.length !== 0) {
			var h = uu.filterByIds(fdoe_short.cats);
			q = new Categories(h);
		}
		/*else if ( (typeof fdoe_short.cats !== 'undefined') ) {
			var hh = uu.filterByIds(fdoe_short.cats);

			q = new Categories(hh);
		}*/
		else {
			q = uu;
		}
		var y = new CategoryListView({
			collection: {
				products: i,
				categories: q
			}
		});
		var top = new MenuView({
			collection: {
				categories: q
			}
		});
		top.render();
		var r = y.render();
		var ii = r.reject({
			single_shortcode: ''
		});
		var iii = new Products(ii);
		if ((fdoe.popup_simple == "yes")) {
			var simple = iii.where({
				is_simple: true
			});
			var x = new ProductListViewModal({
				collection: simple
			});
			x.render();
		}
		if ((fdoe.popup_variable == "yes")) {
			var vari = iii.where({
				is_variable: true
			});
			z = new ProductListViewModal({
				collection: vari
			});
			z.render();
		}
		jQuery('.fdoe').fadeIn(400).promise().done(function() {
			addmodals();
		});
		jQuery('#menu_headings_2').fadeIn(400);
	}
	if (fdoe.js_frontend == 1) {
		var jQueryfdoe_fragment_refresh = {
			url: wc_cart_fragments_params.wc_ajax_url.toString().replace('%%endpoint%%', 'get_refreshed_fragments'),
			type: 'POST',
			success: function(data) {
				if (data && data.fragments) {
					jQuery.each(data.fragments, function(key, value) {
						jQuery(key).replaceWith(value);
					});
					jQuery(document.body).trigger('wc_fragments_refreshed');
					jQuery('.fdoe_mini_cart').html(data.fragments['div.widget_shopping_cart_content']).show();
					jQuery('.fdoe_mini_cart_2').html(data.fragments['div.widget_shopping_cart_content']).show();
					if (fdoe.minicart_style != 'theme') {
						init_minicart();
					}
				}
			}
		};
		run_sequenze(do_style, do_sticky_bars, smooth_scrolling, extras, add_event_listeners, init_minicart);
	}

	function addmodals() {
		var v = r.where({
			single_shortcode: ''
		});
		var rest = new Products(v);
		var simple_2;
		var vari_2;
		var the_ids;
		var the_ids2;
		if ((fdoe.popup_simple == "yes")) {
			simple_2 = rest.where({
				is_simple: true
			});
			the_ids = _.pluck(simple_2, 'id');
		}
		if ((fdoe.popup_variable == "yes")) {
			vari_2 = rest.where({
				is_variable: true
			});
			the_ids2 = _.pluck(vari_2, 'id');
		}
		var union2 = _.union(the_ids2, the_ids);
		if (union2.length !== 0) {
			fdoe_inject_shortcode(union2);
		}
	}

	function fdoe_inject_shortcode(id) {
		var data = {
			'id': id,
		};
		jQuery.ajax({
			url: fdoe.wc_ajax_url.replace('%%endpoint%%', 'ajaxfdoe_make_product_shortcode'),
			method: "POST",
			data: data,
		}).success(function(response) {
			var new_modals = new Products;
			new_modals.add(response.content);
			var p = new ProductListViewModal({
				collection: new_modals
			});
			p.render();
		}).done(function() {
			extras();
			add_event_listeners();
			jQuery('.product-aromodal').on('show.bs.aromodal', function() {
				jQuery(this).find(('.variations_form')).wc_variation_form();
				jQuery(this).find('.wc-tabs-wrapper, .woocommerce-tabs, #rating').trigger('init');
			});
		});
	}

	function run_sequenze(cb_do_style, cb_do_sticky_bars, cb_smooth_scrolling, cb_extras, cb_add_event_listeners, cb_init_minicart) {
		cb_do_style();
		cb_do_sticky_bars(activate_scroll);
		//Check for Internet Explorer 11
		var isIE11 = !!window.MSInputMethodContext && !!document.documentMode;
		if (fdoe.smooth_scrolling == 'yes' && !isIE11) {
			cb_smooth_scrolling();
		}
		cb_extras();
		cb_add_event_listeners();
		if (fdoe.minicart_style != 'theme') {
			cb_init_minicart();
		}
	}

	function init_minicart() {
		//Mini Cart
		var is_touch_device = 'ontouchstart' in document.documentElement;
		if (!is_touch_device && fdoe.minicart_style == "popover") {
			// Minicart remove button aropopover
			t(document).aropopover({
				selector: '.woocommerce-mini-cart-item',
			});
			t('[data-toggle="aropopover"]').aropopover({
				delay: {
					show: 50,
					hide: 1800
				}
			}, {
				template: '<div class="aropopover" role="tooltip"><div class="arrow"></div><div class="aropopover-content fdoe_remove_aropopover"></div></div>'
			});
		} else if (is_touch_device || fdoe.minicart_style !== "popover") {
			// Minicart remove button aropopover
			t('.fdoe-mini-cart-remove').show();
			t('.fdoe-minicart-main-column').removeClass('arocol-xs-10').addClass('arocol-xs-12');
			t('#fdoe_mini_cart_id').addClass('minicart_items_basic');
			t('.fdoe_mini_cart_2 [data-toggle="aropopover"]').aropopover('destroy');
			t('.fdoe_mini_cart [data-toggle="aropopover"]').aropopover('destroy');
		}
	}

	function add_event_listeners() {
		// reset forms when open product aromodals
		jQuery('.product-aromodal').on('hidden.bs.aromodal', function() {
			jQuery(this).find('form.cart').trigger('reset');
			jQuery(this).find('form.cart').trigger('woocommerce-product-addons-update');
		});
		//hide product aromodal after added to cart
		jQuery('#cart_aromodal').off('show.bs.aromodal').on('show.bs.aromodal', function() {
			jQuery(".aromodal.product-aromodal").aromodal("hide");
		});
		// Hide cart aromodal on checkout
		jQuery(document).on('click', '#checkout_button_1', function() {
			jQuery("#cart_aromodal").aromodal('hide');
		});
		// Update mini cart on item removal
		jQuery(document.body).on('removed_from_cart', function(event) {
			event.preventDefault();
			jQuery.ajax(jQueryfdoe_fragment_refresh);
		});
		jQuery(document.body).on('added_to_cart', function(event) {
			event.preventDefault();
			jQuery.ajax(jQueryfdoe_fragment_refresh);
		});
		//Block mini cart when adding single product
		jQuery(document).on('click', '.fdoe_is_button .fdoe_add_item', function() {
			jQuery('.fdoe-mini-cart').addClass('processing').block({
				baseZ: 9000,
				message: '',
				overlayCSS: {
					backgroundColor: '#ffffff0d',
					opacity: 1,
					cursor: 'wait'
				},
				css: {
					color: '#707070',
					border: 'none',
					backgroundColor: '#ffffff0d',
					oapcity: 1
				}
			});
		});
		// Toggle class on clicked remove button
		jQuery(document).on('click', '.fdoe-mini-cart-remove a.fdoe_remove', function() {
			jQuery(this).parents('.fdoe_minicart_item').addClass('processing').block({
				baseZ: 9000,
				message: '',
				overlayCSS: {
					backgroundColor: '#ffffff0d',
					opacity: 1,
					cursor: 'wait'
				},
				css: {
					color: '#707070',
					border: 'none',
					backgroundColor: '#ffffff0d',
					oapcity: 1
				}
			});
		});
		jQuery(document).on('click', '.aropopover-content a.fdoe_remove', function() {
			jQuery(this).parents('.aropopover').addClass('fdoe_clicked');
			jQuery(this).parents('.aropopover').prev('.fdoe_minicart_item').addClass('processing').block({
				baseZ: 9000,
				message: '',
				overlayCSS: {
					backgroundColor: '#ffffff0d',
					opacity: 1,
					cursor: 'wait'
				},
				css: {
					color: '#707070',
					border: 'none',
					backgroundColor: '#ffffff0d',
					opacity: 1
				}
			});
		});
		// Readjust aromodal
		jQuery('.fdoe-aromodal').on('shown.bs.aromodal', function() {
			jQuery(this).aromodal('handleUpdate');
		});
		// Run init functions
		jQuery('#myModalfeatured').off('shown.bs.aromodal').on('shown.bs.aromodal', function() {
			fdoe_add_to_cart_init();
		});
		jQuery('.product-aromodal').on('shown.bs.aromodal', function() {
			fdoe_add_to_cart_init();
		});
	}

	function fdoe_add_to_cart_init() {
		jQuery('.single_add_to_cart_button').off('click').click(function(event) {
			event.preventDefault();
			var tthis = t(this);
			jQuery.blockUI({
				baseZ: 10001,
				message: "",
				overlayCSS: {
					backgroundColor: '#ffffff0d',
					opacity: 1
				},
			});
			// Ajax add to cart on the product page
			var jQueryform = jQuery(this).closest('form');
			jQuery.ajax({
				url: fdoe.wc_ajax_url.replace('%%endpoint%%', 'ajaxfdoe_add'),
				method: "POST",
				data: jQueryform.serialize()
			}).success(function() {
				jQuery.ajax(jQueryfdoe_fragment_refresh);
			}).done(function(response) {
				if (response.passed_vali !== false) {
					jQuery(".aromodal.product-aromodal").aromodal("hide");
					tthis.siblings('.fdoe_confirm_check').show();
				}
				jQuery.unblockUI();
			});
		});
		jQuery('.entry-summary form.cart').off('submit').on('submit', function(event) {
			event.preventDefault();
		});
	}

	function extras() {
		//for woocommerce-product-addons above version 3.0.0
		if (fdoe.addonabove3 == '1') {
			jQuery('li.fdoe_minicart_item.woocommerce-mini-cart-item').css('flex-direction', 'column');
			jQuery('.product-aromodal').on('show.bs.aromodal', function() {
				jQuery(this).find('form.cart').trigger('woocommerce-product-addons-update');
				if (jQuery('.wc-bookings-booking-form', this).length === false || true) {
					var qty_2 = parseFloat(jQuery('.cart', this).find('input.qty').val());
					var productname_new = t('.product_title', this).html();
					jQuery('.wc-pao-col1', this).first().html('<strong>' + qty_2 + 'x ' + productname_new + '</strong>');
					tthis = jQuery(this);
					jQuery('body').on('woocommerce-product-addons-update', function() {
						var qty_2 = parseFloat(jQuery('.cart', tthis).find('input.qty').val());
						var productname_new = jQuery('.product_title', tthis).html();
						jQuery('.wc-pao-col1', tthis).first().html('<strong>' + qty_2 + 'x ' + productname_new + '</strong>');
					});
				}
			});
		}
	}
	// Sticky right and left-left containers
	function do_sticky_bars(callback_activate_scroll) {
		if (fdoe.sticky_bar == 'yes') {
			t(".fdoe-sticky").aroaffix({
				offset: {
					top: function() {
						return (this.top = t('.fdoe-sticky').offset().top - t('#wpadminbar').height())
					},
					bottom: function() {
						return (this.bottom = t('.site-footer').outerHeight(true))
					}
				}
			});
			if (fdoe.is_user_logged_in == 1 && jQuery.type(t('.fdoe-sticky').css('top')) === "string") {
				t('.fdoe-sticky').css('top', parseInt(t('.fdoe-sticky').css('top'), 10) + t('#wpadminbar').height());
			}
			if (window.matchMedia('(min-width: 768px)').matches && fdoe.hide_minicart == 'no') {
				t(".fdoe-right-sticky").aroaffix({
					offset: {
						bottom: function() {
							return (this.bottom = t('.site-footer').outerHeight(true))
						},
						top: function() {
							return (this.top = t('.fdoe-right-sticky').offset().top - t('#wpadminbar').height())
						}
					}
				});
				if (fdoe.is_user_logged_in == 1 && jQuery.type(t('.fdoe-right-sticky').css('top')) === "string") {
					var right_aroaffix_top = (t('.fdoe-right-sticky').css('top')).replace("px", "");
					t('.fdoe-right-sticky').css('top', parseFloat(right_aroaffix_top) + t('#wpadminbar').height() + 'px');
				}
				var parentwidth = t("#fdoe-right-container").width();
				t(".fdoe-right-sticky").width(parentwidth);
			}
			var parentwidth2 = t("#fdoe-left-left-container").width();
			t("#menu_headings_2").width(parentwidth2);
		}
		callback_activate_scroll();
	}
	// Adding scrollspy for Menu category
	function activate_scroll() {
		var freeze = false;
		var fdoe_timeout;
		t(window).on('scroll', function() {
			var currentTop = t(window).scrollTop();
			var elems = t('.scrollspy');
			elems.each(function() {
				var elemTop = t(this).offset().top * 0.95;
				var elemBottom = elemTop + t(this).outerHeight();
				var docHeight = t(document).height();
				var winScrolled = t(window).height() + t(window).scrollTop();
				if (freeze === false && ((t(this).is('.scrollspy:nth-last-child(2)') && (docHeight - winScrolled) < t(this).outerHeight()) || (t(this).is('.scrollspy:last-child') && (docHeight - winScrolled) < 1) || currentTop >= elemTop && currentTop <= elemBottom)) {
					var id = t(this).attr('id');
					var navElem = t('#menu_headings_2 a[href="#' + id + '"]');
					navElem.parent().addClass('fdoe-active-link').siblings().removeClass('fdoe-active-link');
				}
			});
		});
		t('.fdoe_menuitem a').off('click').on('click', function() {
			clearTimeout(fdoe_timeout);
			freeze = true;
			t(this).parent('.fdoe_menuitem').addClass('fdoe-active-link').siblings().removeClass('fdoe-active-link');
			fdoe_timeout = setTimeout(function() {
				t('.fdoe_menuitem').trigger('fdoe_clicked_');
			}, 2000);
		});
		t('.fdoe_menuitem').on('fdoe_clicked_', function() {
			freeze = false;
			clearTimeout(fdoe_timeout);
		});
	}

	function smooth_scrolling() {
		document.querySelectorAll('.storefront-handheld-footer-bar a[href^="#"], .fdoe-handheld-footer-bar a[href^="#"], #the_main_container a[href^="#"]').forEach(function(anchor) {
			anchor.addEventListener('click', function(e) {
				e.preventDefault();
				document.querySelector(this.getAttribute('href')).scrollIntoView({
					behavior: 'smooth',
					block: "start",
					inline: "nearest"
				});
			});
		});
	}

	function do_style() {
		//////////
		if ((window.matchMedia('(max-width: 767px)').matches) || fdoe.show_left_menu == 1) {
			jQuery('#menu_headings').show();
		}
		if (jQuery('ul#menu_headings li').length > 0 || jQuery('#menu_headings_2 div').length > 0) {
			jQuery('.fdoe-item-icon').css("color", fdoe.menu_color);
			jQuery('.fdoe-menu-title-icon').css("color", fdoe.menu_color);
			jQuery('#menu_headings  a').css("color", fdoe.menu_color);
			jQuery('#menu_headings_2  a').css("color", fdoe.menu_color);
			if (jQuery('ul#menu_headings li').length == 1) {
				jQuery("#menu_headings").hide();
			}
		}
		////////////////
		jQuery('input.qty').addClass('features-form');
		/* CSS Modifications */
		/* Detach the Woocommerce products Header */
		if (!jQuery.trim(jQuery(".woocommerce-products-header").html())) {
			jQuery('.woocommerce-products-header').detach();
		}
		// Layout options CSS
		if (fdoe.layout == 'fdoe_twocols') {
			jQuery(".fdoe-item  .flex-container-row").append("<div class='fdoe_aggregate_row'></div>");
			jQuery('.fdoe-item  .flex-container-row .fdoe_thumb').each(function() {
				jQuery(this).parent().find('.fdoe_aggregate_row').append(jQuery(this));
			});
			jQuery('.fdoe-item  .flex-container-row .fdoe_item_price').each(function() {
				jQuery(this).parent().find('.fdoe_aggregate_row').append(jQuery(this));
			});
			jQuery('.fdoe-item  .flex-container-row .fdoe_add_item').each(function() {
				jQuery(this).parent().find('.fdoe_aggregate_row').append(jQuery(this));
			});
			jQuery('.fdoe_aggregate_row').wrap("<div class='fdoe_second_row'></div>");
			jQuery('.fdoe-item  .flex-container-row .fdoe_aggregate_row').each(function() {
				jQuery(this).find('.fdoe_add_price_item').wrapAll("<span class='fdoe_price_and_add'></div>");
			});
			jQuery('.fdoe_summary').css("margin-right", 'unset');
			jQuery('.fdoe_title').css("text-align", 'center');
			jQuery('.flex-container-row').css("align-items", 'unset');
			jQuery('.flex-container-row').css("flex-direction", 'column');
			jQuery('.fdoe_summary').css("order", '0');
			jQuery(".fdoe_aggregate_row").css("order", '1');
			if (fdoe.fdoe_show_images == 'hide') {
				jQuery(".fdoe_aggregate_row").css("justify-content", 'space-around');
			}
			if (fdoe.fdoe_item_border == 'hide') {
				jQuery(".fdoe_aggregate_row").css("justify-content", 'space-around');
			}
		} else {}
		//
		// Hide Minicart option
		if (fdoe.hide_minicart == 'yes') {
			if (fdoe.show_left_menu == 1) {
				jQuery("#fdoe-left-container").toggleClass('arocol-xs-12 arocol-sm-9 arocol-lg-9', false);
				jQuery("#fdoe-left-container").toggleClass('arocol-xs-12 arocol-sm-12 arocol-lg-12', true);
			} else {
				jQuery("#fdoe-left-container").toggleClass('arocol-sm-7 arocol-lg-7', false);
				jQuery("#fdoe-left-container").toggleClass('arocol-sm-9 arocol-lg-9', true);
				jQuery("#fdoe-left-left-container").toggleClass('arocol-sm-2', false);
				jQuery("#fdoe-left-left-container").toggleClass('arocol-sm-3', true);
			}
			jQuery('.fdoe_extra_checkout').css('display', 'block');
			jQuery(".fdoe_order_time").appendTo('.fdoe-flex-1').css({
				'float': 'right',
				'margin': '10px',
				'margin-top': '20px'
			}).removeClass('fdoe_hidden').fadeIn('slow');
			jQuery("#fdoe-right-container").remove();
		} else {
			jQuery("#fdoe-right-container").fadeIn('slow');
			jQuery(".fdoe_order_time").removeClass('fdoe_hidden').fadeIn('slow');
		}
		// Theme fixes for known problem with certain Themes
		if (fdoe.theme == 'Bridge' || fdoe.theme_parent == 'Bridge') {
			jQuery('.fdoe-aromodal').on('shown.bs.aromodal', function() {
				jQuery('body').addClass('fdoe-aromodal-open');
			});
			jQuery('.fdoe-aromodal').on('hidden.bs.aromodal', function() {
				jQuery('body').removeClass('fdoe-aromodal-open');
			});
		}
	}
});
