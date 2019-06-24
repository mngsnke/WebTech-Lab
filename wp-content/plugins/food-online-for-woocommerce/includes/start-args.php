<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// The Plugin Settings
if ( !isset( $settings_args ) ) {
    $settings_args = array(
         array(
             'name' => __( 'User Visibility', 'food-online-for-woocommerce' ),
            'type' => 'title',
            'id' => 'fdoe_visibility',
              'desc' => __( 'Either show the Menu on shop page or use shortcode [foodonline] on any page.', 'food-online-for-woocommerce' ),
        ),
        array(
             'name' => __( 'Main shop page', 'food-online-for-woocommerce' ),
            'id' => 'fdoe_override_shop',
            'type' => 'checkbox',
            'css' => 'min-width:300px;',
            'desc' => __( 'Show the menu on main shop page', 'food-online-for-woocommerce' ),
            'default' => 'yes'
        ),
         array(
             'name' => __( 'Hide the Cart', 'food-online-for-woocommerce' ),
            'id' => 'fdoe_hide_minicart',
            'type' => 'checkbox',
            'css' => 'min-width:300px;',
            'desc' => __( 'Hide the Cart from Menu page?', 'food-online-for-woocommerce' ),
            'default' => 'no'
        ),
        array(
             'type' => 'sectionend',
            'id' => 'fdoe_visibility'
        ),
        array(
             'name' => __( 'Display Options', 'food-online-for-woocommerce' ),
            'type' => 'title',
            'id' => 'fdoe_display_options'
        ),
        array(
             'name' => __( 'Menu Layout', 'food-online-for-woocommerce' ),
            'id' => 'fdoe_layout',
            'default' => 'fdoe_onecol',
            'type' => 'select',
            'options' => array(
                 'fdoe_onecol' => __( 'One Column', 'food-online-for-woocommerce' ),
                'fdoe_twocols' => __( 'Two Columns', 'food-online-for-woocommerce' )
            ),
            'css' => 'max-width:200px;',
            'desc' => __( 'Display the Menu items in one or two columns?', 'food-online-for-woocommerce' )
        ),
        array(
             'name' => __( 'Menu Color', 'food-online-for-woocommerce' ),
            'id' => 'fdoe_color',
            'type' => 'text',
            'class' => 'fdoe-color-picker',
            'default' => '#bf5bb6'
        ),
         array(
             'name' => __( 'Menu Background Color', 'food-online-for-woocommerce' ),
            'id' => 'fdoe_color_back',
            'type' => 'text',
            'class' => 'fdoe-color-picker',
            'default' => '#fff'
        ),
        array(
                             'name' => __( 'Menu Items Separator', 'food-online-for-woocommerce' ),
                            'id' => 'fdoe_item_separator',
                            'default' => 'dashed',
                            'type' => 'select',
                            'options' => array(
                                                'dashed' => __( 'Dashed', 'food-online-for-woocommerce' ),
                                                'solid' => __( 'Solid', 'food-online-for-woocommerce' ),
                                                 'dotted' => __( 'Dotted', 'food-online-for-woocommerce' ),
                                                 'hidden' => __( 'None', 'food-online-for-woocommerce' )
                                                 ),
                             'css' => 'max-width:200px;',
                            'desc' => __( 'Style of the Menu items separator?', 'food-online-for-woocommerce' )
                        ),
         array(
             'name' => __( 'Menu Items Separator Color', 'food-online-for-woocommerce' ),
            'id' => 'fdoe_border_color',
            'type' => 'text',
            'class' => 'fdoe-color-picker',
            'default' => '#ddd'
        ),
         array(
                             'name' => __( 'Menu Titles Icon', 'food-online-for-woocommerce' ),
                            'id' => 'fdoe_menu_titles_icon',
                            'default' => 'fas fa-ellipsis-h',
                            'class' => 'fdoe-icon-picker',
                            'type' => 'select',
                            'options' => array(
                                                'fas fa-ellipsis-h' => __( 'Dots', 'food-online-for-woocommerce' ),
                                                'fas fa-minus' => __( 'Lines', 'food-online-for-woocommerce' ),
                                                 'fas fa-seedling' => __( 'Leef', 'food-online-for-woocommerce' ),
                                                 'fas fa-genderless' => __( 'Ring', 'food-online-for-woocommerce' ),
                                                 'fas fa-star' => __( 'Star', 'food-online-for-woocommerce' ),
                                                 'fas fa-pepper-hot' => __( 'Chili Pepper', 'food-online-for-woocommerce' ),
                                                 'fas fa-pizza-slice' => __( 'Pizza', 'food-online-for-woocommerce' ),
                                                 'fas fa-hamburger' => __( 'Hamburger', 'food-online-for-woocommerce' ),
                                                 'fas fa-apple-alt' => __( 'Apple', 'food-online-for-woocommerce' ),
                                                '' => __( 'None', 'food-online-for-woocommerce' )
                                                 ),
                             'css' => 'max-width:200px;',
                            'desc' => __( 'Icon to display with the menu titles?', 'food-online-for-woocommerce' )
                        ),
                    array(
                             'name' => __( 'Menu Item Icon', 'food-online-for-woocommerce' ),
                            'id' => 'fdoe_item_icon',
                            'default' => 'fas fa-plus-circle',
                            'class' => 'b2b-icon-picker',
                            'type' => 'select',
                            'options' => array(
                                                'fas fa-plus-circle' => __( 'Standard', 'food-online-for-woocommerce' ),
                                                 'fas fa-plus' => __( 'Plus', 'food-online-for-woocommerce' ),
                                                'fas fa-plus-square' => __( 'Square Shape Plus', 'food-online-for-woocommerce' ),
                                                 'fas fa-seedling' => __( 'Leef', 'food-online-for-woocommerce' ),
                                                 'fas fa-shopping-basket' => __( 'Shopping Basket', 'food-online-for-woocommerce' ),
                                                  'fas fa-shopping-cart' => __( 'Shopping Cart', 'food-online-for-woocommerce' ),
                                                  'fas fa-cart-plus' => __( 'Shopping Cart with plus', 'food-online-for-woocommerce' ),
                                                  'fas fa-utensils' => __( 'Knife & Fork', 'food-online-for-woocommerce' ),
                                                '' => __( 'None', 'food-online-for-woocommerce' )
                                                 ),
                             'css' => 'max-width:200px;',
                            'desc' => __( 'Item "Add" icon in the Menu?', 'food-online-for-woocommerce' )
                        ),
        array(
             'name' => __( 'Menu Item Image', 'food-online-for-woocommerce' ),
            'id' => 'fdoe_show_images',
            'default' => 'rec',
            'type' => 'select',
            'options' => array(
                  'small' => __( 'Small size', 'food-online-for-woocommerce' ),
                 'rec' => __( 'Normal Size', 'food-online-for-woocommerce' ),
                'big' => __( 'Big size', 'food-online-for-woocommerce' ),
                'hide' => __( 'Hide images', 'food-online-for-woocommerce' )
            ),
            'css' => 'max-width:200px;',
            'desc' => __( 'Choose how to show images for products in menu?', 'food-online-for-woocommerce' )
        ),
          array(
            'name' => __('Left Menu', 'food-online-for-woocommerce'),
            'id' => 'fdoe_left_menu',
            'type' => 'checkbox',
            'default' => 'no',
            'css' => 'min-width:300px;',
            'desc' => __('Show categories in left-bar menu?', 'food-online-for-woocommerce')
        ),
            array(
            'name' => __('Sticky', 'food-online-for-woocommerce'),
            'id' => 'fdoe_sticky_bar',
            'type' => 'checkbox',
            'default' => 'no',
            'css' => 'min-width:300px;',
            'desc' => __('Make left-bar/right-bar sticky?', 'food-online-for-woocommerce')
        ),
            array(
            'name' => __('Smooth Scrolling', 'food-online-for-woocommerce'),
            'id' => 'fdoe_smooth_scrolling',
            'type' => 'checkbox',
            'default' => 'no',
            'css' => 'min-width:300px;',
            'desc' => __('Use smooth scrolling navigating in the Menu?', 'food-online-for-woocommerce')
        ),
        array(
             'type' => 'sectionend',
            'id' => 'fdoe_display_options'
        ),
         array(
             'name' => __( 'Minicart Style', 'food-online-for-woocommerce' ),
            'type' => 'title',
            'id' => 'fdoe_minicart_style'
        ),
        array(
             'name' => __( 'Minicart Style', 'food-online-for-woocommerce' ),
            'id' => 'fdoe_minicart_style',
            'default' => 'popover',
            'type' => 'select',
            'options' => array(
                  'popover' => __( 'Popup Style', 'food-online-for-woocommerce' ),
                 'basic' => __( 'Basic Style', 'food-online-for-woocommerce' ),
                'theme' => __( 'Theme Template', 'food-online-for-woocommerce' ),
            ),
            'css' => 'max-width:200px;',
            'desc' => __( 'Choose how to style the minicart?', 'food-online-for-woocommerce' )
    ),
         array(
             'type' => 'sectionend',
            'id' => 'fdoe_minicart_style'
        ),
            array(
             'name' => __( 'Product Popup Options', 'food-online-for-woocommerce' ),
            'type' => 'title',
            'id' => 'fdoe_popup_options'
        ),
           array(
             'name' => __( 'Pop-up simple products?', 'food-online-for-woocommerce' ),
            'id' => 'fdoe_popup_simple',
            'type' => 'checkbox',
            'css' => 'min-width:300px;',
            'desc' => __( 'Use pop-up for simple products when add-to-cart.', 'food-online-for-woocommerce' ),
            'default' => 'yes'
        ),
          array(
             'name' => __( 'Pop-up variable products?', 'food-online-for-woocommerce' ),
            'id' => 'fdoe_popup_variable',
            'type' => 'checkbox',
            'css' => 'min-width:300px;',
            'desc' => __( 'Use pop-up for variable products when add-to-cart.', 'food-online-for-woocommerce' ),
            'default' => 'yes'
        ),
          array(
             'name' => __( 'Product Popup Source', 'food-online-for-woocommerce' ),
            'id' => 'fdoe_product_popup_content',
            'default' => 'custom',
            'type' => 'select',
            'options' => array(
                 'theme' => __( 'Theme Template', 'food-online-for-woocommerce' ),
                 'custom' => __( 'Food Online Template', 'food-online-for-woocommerce' ),

            ),
            'css' => 'max-width:200px;',
            'desc' => __( 'Choose how to display content in product popup?', 'food-online-for-woocommerce' )
    ),
            array(
             'name' => __( 'Product Popup Content', 'food-online-for-woocommerce' ),
            'id' => 'fdoe_product_popup_content_spec',
            'default' => array(),
            'type' => 'multiselect',
            'options' => array(
                 'image' => __( 'Product Image', 'food-online-for-woocommerce' ),
                 'meta' => __( 'Product Meta', 'food-online-for-woocommerce' ),

            ),
            'css' => 'max-width:200px;height:auto;',
            'desc' => __( 'Select content for the product popup.', 'food-online-for-woocommerce' )
    ),



          array(
             'type' => 'sectionend',
            'id' => 'fdoe_popup_options'
        ),
          array(
             'name' => __( 'Advanced Settings', 'food-online-for-woocommerce' ),
            'type' => 'title',
            'id' => 'fdoe_advanced'
        ),

          array(
             'name' => __( 'Multiple shortcode instances?', 'food-online-for-woocommerce' ),
            'id' => 'fdoe_force_shortcode',
            'type' => 'checkbox',
            'css' => 'min-width:300px;',
            'desc' => __( 'Allow multiple shortcode instances?', 'food-online-for-woocommerce' ),
            'default' => 'no'
        ),
           array(
             'name' => __( 'Delete settings?', 'food-online-for-woocommerce' ),
            'id' => 'fdoe_clean_settings',
            'type' => 'checkbox',
            'css' => 'min-width:300px;',
            'desc' => __( 'Delete all settings on plugin removal?', 'food-online-for-woocommerce' ),
            'default' => 'no'
        ),
        array(
             'type' => 'sectionend',
            'id' => 'fdoe_advanced'
        )
    );
}
if (!isset($settings_args_third)) {
    $settings_args_third = array(
        array(
            'name' => __('Time to finished order', 'food-online-for-woocommerce'),
            'type' => 'title',
            'id' => 'fdoe_time_to_delivery',
             'desc' => __( 'Show an approximate time for an order to be ready.', 'food-online-for-woocommerce' ),
        ),
 array(
            'name' => __('Fixed time per order', 'food-online-for-woocommerce'),
            'id' => 'fdoe_pickup_fixed',
            'css' => 'max-width:100px;',
            'default' => '0',
            'type' => 'text',
            'desc' => __('Set a fixed time in minutes for preparing an order.', 'food-online-for-woocommerce')
        ),
          array(
            'name' => __('Extra time per processing order', 'food-online-for-woocommerce'),
            'id' => 'fdoe_pickup_var',
            'css' => 'max-width:100px;',
            'default' => '0',
            'type' => 'text',
            'desc' => __('Set extra time in minutes per order already processing.', 'food-online-for-woocommerce')
        ),
        array(
            'name' => __('Show time until ready for pickup?', 'food-online-for-woocommerce'),
            'id' => 'fdoe_ready_for_pickup_show',
            'type' => 'select',
            'options' => array(
                'none' => __('Hide', 'food-online-for-woocommerce'),
                'fixedtime' => __('By a fixed time', 'food-online-for-woocommerce'),
                'variable' => __('By orders in progress & fixed time', 'food-online-for-woocommerce')
            ),
            'css' => 'max-width:200px;',
            'desc' => __('Show the time until a pickup order is ready.', 'food-online-for-woocommerce'),
            'default' => 'none'
        ),
        array(
            'type' => 'sectionend',
             'id' => 'fdoe_time_to_delivery',
        ),
        );
}
