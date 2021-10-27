<?php

if (version_compare($GLOBALS['wp_version'], '4.7-alpha', '<')) {
    require get_template_directory() . '/inc/back-compat.php';
    return;
}
function change_price_filter_step() {
        return 1;
}
add_filter( 'woocommerce_price_filter_widget_step', 'change_price_filter_step', 10, 3 );

/*Select product in theme options*/
function ftc_posts_array_nofi_func( $post_type = 'post' ) {
if (!class_exists('WooCommerce')){
    return '';
}
    $args = array(
        'post_type'        => $post_type,
        'posts_per_page'   => -1,
        'suppress_filters' => true,
        'cache_results'    => false, // suppress errors when large number of posts (memory).
    );

    $posts_arr = array();
    $posts_obj = get_posts( $args );
    if ( ! empty( $posts_obj ) ) {

        foreach ( $posts_obj as $single_post ) {

            if ( ! is_object( $single_post ) ) {
                continue;
            }

            $posts_arr[ $single_post->post_name ] = wp_strip_all_tags( $single_post->post_title );
        }
    } else {
        $posts_arr[] = '';
    }

    return $posts_arr;

}
add_filter( 'ftc_posts_array_nofi', 'ftc_posts_array_nofi_func');

add_action( 'woocommerce_before_shop_loop', 'ps_selectbox', 25 );
function ps_selectbox() {
    $per_page = filter_input(INPUT_GET, 'perpage', FILTER_SANITIZE_NUMBER_INT);     
    echo '<div class="woocommerce-perpage">';
    echo '<span>Per Page: </span>';
    echo '<select onchange="if (this.value) window.location.href=this.value">';   
    $orderby_options = array(
        '8' => '8',
        '16' => '16',
        '32' => '32',
        '64' => '64'
    );
    foreach( $orderby_options as $value => $label ) {
        echo "<option ".selected( $per_page, $value )." value='?perpage=$value'>$label</option>";
    }
    echo '</select>';
    echo '</div>';
}
add_action( 'pre_get_posts', 'ps_pre_get_products_query' );
function ps_pre_get_products_query( $query ) {
   $per_page = filter_input(INPUT_GET, 'perpage', FILTER_SANITIZE_NUMBER_INT);
   if( $query->is_main_query() && !is_admin() && is_post_type_archive( 'product' ) ){
        $query->set( 'posts_per_page', $per_page );
    }
}

if( ! function_exists( 'ftc_nofication_notice' ) && class_exists('FTC_Elements') ) {
    add_action( 'wp_footer', 'ftc_nofication_notice');
    function ftc_nofication_notice(){
        global $smof_data;
        if(isset($smof_data['ftc_select_product']) && !empty($smof_data['ftc_select_product']) && class_exists('WooCommerce')) {
            echo '<div class="ftc_notification_product">';
             if(isset($smof_data['ftc_title_nofication']) && !empty($smof_data['ftc_title_nofication'])){
                echo '<div class="ftc-title-nofication">'.esc_attr($smof_data['ftc_title_nofication']).'</div>';
            }
            echo '<div class="ftc_nofication_notice woocommerce '.esc_attr($smof_data['ftc_position_nofi']).'" data-time="'.esc_attr($smof_data['ftc_time_slider']).'" data-effect="'.esc_attr($smof_data[
                'ftc_effect_slider']).'" >';

            $products_in = $smof_data['ftc_select_product'];
            $args = apply_filters( 'ftc_elements_query_args', -1, '', '', 'random', 0, $products_in );
            $products = get_posts( $args );
            foreach ( $products as $post ) {
                setup_postdata( $post );
                $link_pro = esc_url( get_permalink( $post->ID ) );
                $href = admin_url('admin-ajax.php', is_ssl()?'https':'http') . '?ajax=true&action=load_quickshop_content&product_id='.$post->ID;

                ?>
                <div class="ftc-product product">
                    <?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

                    <div class="images">
                        <a href="<?php echo $href ?>" class="quickview">
                            <?php
                            do_action( 'woocommerce_before_shop_loop_item_title' );
                            ?>
                        </a>
                        <?php
                        do_action( 'woocommerce_shop_loop_item_title' );
                        ?>

                    </div>
                    <div class="item-description">
                      <?php   
                      remove_action('woocommerce_after_shop_loop_item', 'ftc_template_loop_product_title', 20);
                      ?>
                      <h3 class="product-title"><a href="<?php echo $link_pro ?>"><?php echo get_the_title($post->ID); ?></a></h3>

                      <?php 
                      woocommerce_template_loop_rating();
                      woocommerce_template_loop_price(); 

                      ?>

                  </div>

              </div>
              <?php
          }

          
          echo '</div>';
          echo '<div class="ftc-nofication-close">x</div>';
          echo '</div>';
      }
  }
}

function megamenu_add_theme_default($themes) {
    $themes["default"] = array(
        'title' => 'FTC - Mega Menu',
        'container_background_from' => 'rgba(255, 255, 255, 0)',
        'container_background_to' => 'rgba(255, 255, 255, 0)',
        'arrow_up' => 'disabled',
        'arrow_down' => 'disabled',
        'arrow_left' => 'dash-f139',
        'menu_item_background_hover_from' => 'rgb(160, 121, 54)',
        'menu_item_background_hover_to' => 'rgb(160, 121, 54)',
        'menu_item_spacing' => '10px',
        'menu_item_link_color' => 'rgb(255, 255, 255)',
        'menu_item_link_weight' => '500',
        'menu_item_link_text_transform' => 'uppercase',
        'menu_item_link_color_hover' => 'rgb(255, 255, 255)',
        'menu_item_link_weight_hover' => '500',
        'menu_item_link_border_radius_top_left' => '5px',
        'menu_item_link_border_radius_top_right' => '5px',
        'menu_item_link_border_radius_bottom_left' => '5px',
        'menu_item_link_border_radius_bottom_right' => '5px',
        'panel_background_from' => 'rgb(255, 255, 255)',
        'panel_background_to' => 'rgb(255, 255, 255)',
        'panel_width' => '#content',
        'panel_header_font_weight' => 'normal',
        'panel_header_border_color' => '#555',
        'panel_padding_top' => '7px',
        'panel_padding_bottom' => '7px',
        'panel_font_size' => '14px',
        'panel_font_color' => '#666',
        'panel_font_family' => 'inherit',
        'panel_second_level_font_color' => 'rgb(0, 0, 0)',
        'panel_second_level_font_color_hover' => 'rgb(160, 121, 54)',
        'panel_second_level_text_transform' => 'uppercase',
        'panel_second_level_font' => 'inherit',
        'panel_second_level_font_size' => '16px',
        'panel_second_level_font_weight' => '500',
        'panel_second_level_font_weight_hover' => '500',
        'panel_second_level_text_decoration' => 'none',
        'panel_second_level_text_decoration_hover' => 'none',
        'panel_second_level_padding_top' => '5px',
        'panel_second_level_padding_bottom' => '5px',
        'panel_second_level_border_color' => '#555',
        'panel_third_level_font_color' => '#666',
        'panel_third_level_font_color_hover' => 'rgb(160, 121, 54)',
        'panel_third_level_font' => 'inherit',
        'panel_third_level_font_size' => '14px',
        'panel_third_level_font_weight' => '500',
        'panel_third_level_font_weight_hover' => '500',
        'panel_third_level_padding_top' => '5px',
        'panel_third_level_padding_bottom' => '5px',
        'panel_third_level_border_color' => 'rgb(235, 235, 235)',
        'panel_third_level_border_color_hover' => 'rgb(235, 235, 235)',
        'panel_third_level_border_right' => 'px',
        'panel_third_level_border_bottom' => '1px',
        'flyout_menu_background_from' => 'rgb(255, 255, 255)',
        'flyout_menu_background_to' => 'rgb(255, 255, 255)',
        'flyout_padding_top' => '7px',
        'flyout_padding_right' => 'px',
        'flyout_padding_bottom' => '7px',
        'flyout_link_weight' => 'inherit',
        'flyout_background_from' => 'rgb(255, 255, 255)',
        'flyout_background_to' => 'rgb(255, 255, 255)',
        'flyout_background_hover_from' => 'rgb(255, 255, 255)',
        'flyout_background_hover_to' => 'rgb(255, 255, 255)',
        'flyout_link_size' => '14px',
        'flyout_link_color' => '#666',
        'flyout_link_color_hover' => '#666',
        'flyout_link_family' => 'inherit',
        'responsive_breakpoint' => '991px',
        'shadow' => 'on',
        'shadow_color' => 'rgb(255, 255, 255)',
        'toggle_background_from' => '#222',
        'toggle_background_to' => '#222',
        'mobile_background_from' => 'rgb(255, 255, 255)',
        'mobile_background_to' => 'rgb(255, 255, 255)',
        'mobile_menu_item_link_font_size' => '14px',
        'mobile_menu_item_link_color' => 'rgb(34, 34, 34)',
        'mobile_menu_item_link_text_align' => 'left',
        'mobile_menu_item_link_color_hover' => 'rgb(234, 25, 66)',
        'mobile_menu_item_background_hover_from' => 'rgb(255, 255, 255)',
        'mobile_menu_item_background_hover_to' => 'rgb(255, 255, 255)',
        'disable_mobile_toggle' => 'on',
        'custom_css' => '/** Push menu onto new line **/ 
#{$wrap} { 
    clear: both; 
}',
    );
    return $themes;
}
add_filter("megamenu_themes", "megamenu_add_theme_default");
function mytheme_add_cpt_support() {

    //if exists, assign to $cpt_support var
    $cpt_support = get_option( 'elementor_cpt_support' );
    
    //check if option DOESN'T exist in db
    if( ! $cpt_support ) {
        $cpt_support = [ 'page', 'post', 'ftc_footer', 'ftc_portfolio' ]; //create array of our default supported post types
        update_option( 'elementor_cpt_support', $cpt_support ); //write it to the database
    }
    
    //if it DOES exist, but portfolio is NOT defined
    else  {
         $cpt_support = [ 'page', 'post', 'ftc_footer', 'ftc_portfolio' ]; //append to array
        update_option( 'elementor_cpt_support', $cpt_support ); //update database
    }
    
    //otherwise do nothing, portfolio already exists in elementor_cpt_support option
}
add_action( 'after_switch_theme', 'mytheme_add_cpt_support' );


add_action('wp_ajax_woocommerce_ajax_add_to_cart', 'woocommerce_ajax_add_to_cart');
add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', 'woocommerce_ajax_add_to_cart');
        
function woocommerce_ajax_add_to_cart() {

            $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
            $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
            $variation_id = absint($_POST['variation_id']);
            $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
            $product_status = get_post_status($product_id);

            if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id) && 'publish' === $product_status) {

                do_action('woocommerce_ajax_added_to_cart', $product_id);

                if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
                    wc_add_to_cart_message(array($product_id => $quantity), true);
                }

                WC_AJAX :: get_refreshed_fragments();
            } else {

                $data = array(
                    'error' => true,
                    'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id));

                echo wp_send_json($data);
            }

            wp_die();
        }

add_action( 'wp_footer', 'ftc_nofication_added_to_cart');

function ftc_nofication_added_to_cart(){
    echo '<span class="ftc-single-added">'.esc_html__('Added to cart','karo').'</span>';
}

/* * * Include TGM Plugin Activation ** */
require_once get_template_directory() . '/inc/includes/class-tgm-plugin-activation.php';

/* * * Theme Options ** */

require_once get_template_directory() . '/inc/register_sidebar.php';
require_once get_template_directory() . '/admin/base_options.php';
require_once get_template_directory() . '/admin/theme_options.php';

/*Nonce for ajax*/
add_action('wp_enqueue_scripts', 'ftc_ajax_platform_script_enqueue');
add_action('admin_enqueue_scripts', 'ftc_ajax_platform_script_enqueue');
function ftc_ajax_platform_script_enqueue() {
    wp_enqueue_script(
        'platform',
        get_template_directory_uri(). '/assets/js/platform.js',
        array('jquery'), '1.0', true);

    wp_localize_script('platform', 'ftc_platform', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'ajax_nonce' => wp_create_nonce('platform_security')
    ));
}

function is_elementor(){
 global $post;
 if(class_exists('Elementor\Plugin')){
    return \Elementor\Plugin::$instance->db->is_built_with_elementor($post->ID);
} 
}
// Wishlist
if(class_exists('YITH_WCWL')){
    add_filter('body_class', function($classes){
        return array_merge( $classes, array( 'yith-wishlist' ) );
    });
}
if(isset($smof_data['ftc_prod_advanced_zoom']) && $smof_data['ftc_prod_advanced_zoom'] == 'type_2'){
  add_filter('body_class', function($classes){
    return array_merge( $classes, array( 'ftc-single-grid' ) );
});
}
// end
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */

/*Setting role in WPbakery*/
add_action( 'vc_before_init', 'Use_wpBakery' );
function Use_wpBakery() {
    $vc_list = array('page','ftc_footer');
    vc_set_default_editor_post_types($vc_list);
}

/* * * Is Active WooCommmerce ** */
if (!function_exists('ftc_has_woocommerce')) {

    function ftc_has_woocommerce() {
        $_actived = apply_filters('active_plugins', get_option('active_plugins'));
        if (in_array("woocommerce/woocommerce.php", $_actived) || class_exists('WooCommerce')) {
            return true;
        }
        return false;
    }

}
/* * * Update Filter Price ** */

function update_woocommerce_version() {
    if(class_exists('WooCommerce')) {
        global $woocommerce;

        if(version_compare(get_option('woocommerce_db_version', null), $woocommerce->version, '!=')) {
            update_option('woocommerce_db_version', $woocommerce->version);

            if(! wc_update_product_lookup_tables_is_running()) {
                wc_update_product_lookup_tables();
            }
        }	
    }	
}
add_action('init', 'update_woocommerce_version');

/* Header Mobile Navigation */
if( ! function_exists( 'giftsshop_header_mobile_navigation' ) ) {
    function giftsshop_header_mobile_navigation() {
        global $smof_data;
        ?>
        <?php if( !is_page_template('page-blank.php') ): ?>
            <div class="ftc-mobile-wrapper">
                <div class="mutil-lang-cur">
                    <?php if( isset($smof_data['ftc_header_language']) && $smof_data['ftc_header_language'] ): ?>
                        <div class="ftc-sb-language"><?php echo wp_kses_post(ftc_wpml_language_selector()); ?></div>
                    <?php endif; ?>
                    <?php if( isset($smof_data['ftc_header_currency']) && $smof_data['ftc_header_currency'] ): ?>
                        <div class="header-currency"><?php echo wp_kses_post(ftc_woocommerce_multilingual_currency_switcher()); ?></div>
                    <?php endif; ?>
                </div>
                <?php if( isset($smof_data['ftc_enable_search']) && $smof_data['ftc_enable_search'] ): ?>
                    <?php ftc_get_search_form_by_category(); ?>
                <?php endif; ?>
                <div class= "menu-text"> 
                    <button type="button" class="btn btn-toggle-canvas btn-danger" data-toggle="offcanvas">
                        <i class="fa fa-close"></i>
                    </button>
                    <i class="fa fa-bars"></i>
                    <?php esc_html_e('Menu', 'karo') ?>
                </div>

                <div class="mobile-menu-wrapper">
                    <?php wp_nav_menu( array( 'theme_location' => 'primary' , 'menu' => 'main menu' , 'menu_id' => 'main-menu', 'menu_class' => 'ftc-smartmenu ftc-simple') ); ?>
                </div>
                <?php
                global $smof_data, $woocommerce;
                if (isset($smof_data['ftc_mobile_header_layout']) && $smof_data['ftc_mobile_header_layout']): 
                   ?>
                   <div class="menu-mobile">
                      <div class="mobile-wishlist">
                         <?php if( class_exists('YITH_WCWL')): ?>
                            <div class="ftc-my-wishlist"><?php print_r(ftc_tini_wishlist()); ?></div>
                        <?php endif; ?>

                    </div>
                    <div class="mobile-account">
                     <?php 
                     $_user_logged = is_user_logged_in();
                     ob_start();
                     ?>
                     <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id') ) ); ?>" title="<?php esc_html_e('Login','karo'); ?>">
                        <?php if ($_user_logged): ?>
                           <?php esc_html_e('Account','karo'); ?>
                       <?php endif; ?>
                       <?php if (!$_user_logged): ?>
                           <?php esc_html_e('Login','karo'); ?>
                       <?php endif; ?>
                   </a>
               </div>
           </div>
       <?php endif; ?>
   </div>
   <?php
endif;
}
}
/******************/

/* * * Show Page Slider ** */

function ftc_show_page_slider() {
    global $ftc_page_datas;
    $revolution_exists = class_exists('RevSliderSlider');
    switch ($ftc_page_datas['ftc_page_slider']) {
        case '1':
        if ($revolution_exists && $ftc_page_datas['ftc_rev_slider']) {
            $rev_db = new RevSliderDB();
            $response = $rev_db->fetch(RevSliderGlobals::$table_sliders, 'id=' . $ftc_page_datas['ftc_rev_slider']);
            if (!empty($response)) {
                RevSliderOutput::putSlider($ftc_page_datas['ftc_rev_slider'], '');
            }
        }
        break;
        default:
        break;
    }
}


/* * * Logo Mobile** */
if (!function_exists('ftc_theme_mobile_logo')) {

    function ftc_theme_mobile_logo() {
        global $smof_data;
        $logo_image = isset($smof_data['ftc_logo_mobile']['url']) ? esc_url($smof_data['ftc_logo_mobile']['url']) : '';
        $logo_text = isset($smof_data['ftc_text_logo']) ? stripslashes(esc_attr($smof_data['ftc_text_logo'])) : '';
        ?>
        <div class="logo">
            <a href="<?php echo esc_url(home_url('/')); ?>">
                <!-- Main logo mobile -->
                <?php if (strlen($logo_image) > 0): ?>
                    <img src="<?php echo esc_url($logo_image); ?>" alt="<?php echo!empty($logo_text) ? esc_attr($logo_text) : get_bloginfo('name'); ?>" title="<?php echo!empty($logo_text) ? esc_attr($logo_text) : get_bloginfo('name'); ?>" class="normal-logo-mobile" />
                <?php endif; ?>

                <!-- Logo Text -->
                <?php
                if (strlen($logo_image) == 0) {
                    echo esc_html($logo_text);
                }
                ?>
            </a>
        </div>
        <?php
    }

}   

/* Ajax search */
add_action('wp_ajax_ftc_ajax_search', 'ftc_ajax_search');
add_action('wp_ajax_nopriv_ftc_ajax_search', 'ftc_ajax_search');
if (!function_exists('ftc_ajax_search')) {

    function ftc_ajax_search() {
        global $wpdb, $post, $smof_data;

        // $search_for_product = ftc_has_woocommerce();
        // if ($search_for_product) {
        //     $taxonomy = 'product_cat';
        //     $post_type = 'product';
        // } else {
        //     $taxonomy = 'category';
        //     $post_type = 'post';
        // }
$post_type = array('post', 'page', 'product');
        $num_result = isset($smof_data['ftc_ajax_search_number_result']) ? (int) $smof_data['ftc_ajax_search_number_result'] : 10;

        $search_string = sanitize_text_field($_POST['search_string']);
        $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';

        $args = array(
            'post_type' => $post_type
            , 'post_status' => 'publish'
            , 's' => $search_string
            , 'posts_per_page' => $num_result
            ,'tax_query'        => array()
        );

         $args['meta_key'] = '_price';
    $args['orderby'] = 'meta_value_num';
    $args['order'] = 'asc'; 

        // if ($search_for_product) {
        //     $args['meta_query'] = WC()->query->get_meta_query();
        //     $args['tax_query'] = WC()->query->get_tax_query();
        // }

        // if ($category != '') {
        //     $args['tax_query'] = array(
        //         array(
        //             'taxonomy' => $taxonomy
        //             , 'terms' => $category
        //             , 'field' => 'slug'
        //         )
        //     );
        // }

        $results = new WP_Query($args);

        if ($results->have_posts()) {
            $extra_class = '';
            if (isset($results->post_count, $results->found_posts) && $results->found_posts > $results->post_count) {
                $extra_class = 'view-all-results';
            }

            $html = '<ul class="ftc_list_search ' . $extra_class . '">';
            while ($results->have_posts()) {
                $results->the_post();
                $link = get_permalink($post->ID);

                $image = '';
                if ($post_type == 'product') {
                    $product = wc_get_product($post->ID);
                    $image = get_the_post_thumbnail($post->ID, array(100,100));
                } else if (has_post_thumbnail($post->ID)) {
                    $image = get_the_post_thumbnail($post->ID, array(100,100));
                }

                $html .= '<li>';
                $html .= '<div class="ftc-search-image">';
                $html .= '<a href="' . esc_url($link) . '">' . $image . '</a>';
                $html .= '</div>';
                $html .= '<div class="ftc-search-meta item-description">';
                $html .= '<a href="' . esc_url($link) . '" class="product_title product-name">' . ftc_search_highlight_string($post->post_title, $search_string) . '</a>';
                if ($post_type == 'product') {
                    if ($price_html = $product->get_price_html()) {
                        $html .= '<span class="price">' . $price_html . '</span>';
                    }
                }
                $html .= '</div>';
                $html .= '</li>';
            }
            $html .= '</ul>';

            if (isset($results->post_count, $results->found_posts)) {
                $view_all_text = sprintf(esc_html__('View all %d results', 'karo'), $results->found_posts);
                $text = '?term=&s='.$search_string.'&post_type=product&taxonomy=product_cat' ;
                $html .= '<div class="view-all">';
                $html .= '<a href="'.home_url().$text.'">' . $view_all_text . '</a>';
                $html .= '</div>';
            }

            wp_reset_postdata();

            $return = array();
            $return['html'] = $html;
            $return['search_string'] = $search_string;
            wp_die(json_encode($return));
        }
        else{
            $html = '<div class="eror-search"><span class="error">'.esc_html__('No item found.', 'karo').'</span></div';
            $return = array();
            $return['html'] = $html;
            $return['search_string'] = $search_string;
            wp_die(json_encode($return));
        }

        wp_die('');
    }
}

if (!function_exists('ftc_search_highlight_string')) {

    function ftc_search_highlight_string($string, $search_string) {
        
        $new_string = '';
        $pos_left = stripos($string, $search_string);
        if ($pos_left !== false) {
            $pos_right = $pos_left + strlen($search_string);
            $new_string_right = substr($string, $pos_right);
            $search_string_insensitive = substr($string, $pos_left, strlen($search_string));
            $new_string_left = stristr($string, $search_string, true);
            $new_string = $new_string_left . '<span class="hightlight">' . $search_string_insensitive . '</span>' . $new_string_right;
        } else {
            $new_string = $string;
        }
        return $new_string;
    }

}

/* * * Include files in woo folder ** */
$file_names = array('functions', 'term', 'grid_list_toggle', 'hooks','quickshop');
foreach ($file_names as $file) {
    $file_path = get_template_directory() . '/inc/woo/' . $file . '.php';
    if (file_exists($file_path)) {
        require_once $file_path;
    }
}

/* Custom Sidebar */
add_action('sidebar_admin_page', 'ftc_custom_sidebar_form');

function ftc_custom_sidebar_form() {
    ?>
    <form action="<?php echo admin_url('widgets.php'); ?>" method="post" id="ftc-form-add-sidebar">
        <input type="text" name="sidebar_name" id="sidebar_name" placeholder="<?php esc_html_e('Custom Sidebar Name', 'karo') ?>" />
        <button class="button-primary" id="ftc-add-sidebar"><?php esc_html_e('Add Sidebar', 'karo') ?></button>
    </form>
    <?php
}

function ftc_get_custom_sidebars() {
    $option_name = 'ftc_custom_sidebars';
    $custom_sidebars = get_option($option_name);
    return is_array($custom_sidebars) ? $custom_sidebars : array();
}

add_action('wp_ajax_ftc_add_custom_sidebar', 'ftc_add_custom_sidebar');

function ftc_add_custom_sidebar() {
    check_ajax_referer( 'platform_security', 'security' );

    if (isset($_POST['sidebar_name'])) {
        $option_name = 'ftc_custom_sidebars';
        if (!get_option($option_name) || get_option($option_name) == '') {
            delete_option($option_name);
        }

        $sidebar_name = sanitize_text_field($_POST['sidebar_name']);

        if (get_option($option_name)) {
            $custom_sidebars = ftc_get_custom_sidebars();
            if (!in_array($sidebar_name, $custom_sidebars)) {
                $custom_sidebars[] = $sidebar_name;
            }
            $result1 = update_option($option_name, $custom_sidebars);
        } else {
            $custom_sidebars = array();
            $custom_sidebars[] = $sidebar_name;
            $result2 = add_option($option_name, $custom_sidebars);
        }

        if ($result1) {
            wp_die('Updated');
        } elseif ($result2) {
            wp_die('Added');
        } else {
            wp_die('Error');
        }
    }
    wp_die('');
}

add_action('wp_ajax_ftc_delete_custom_sidebar', 'ftc_delete_custom_sidebar');

function ftc_delete_custom_sidebar() {
    check_ajax_referer( 'platform_security', 'security' );

    if (isset($_POST['sidebar_name'])) {
        $option_name = 'ftc_custom_sidebars';
        $del_sidebar = trim($_POST['sidebar_name']);
        $custom_sidebars = ftc_get_custom_sidebars();
        foreach ($custom_sidebars as $key => $value) {
            if ($value == $del_sidebar) {
                unset($custom_sidebars[$key]);
                break;
            }
        }
        $custom_sidebars = array_values($custom_sidebars);
        update_option($option_name, $custom_sidebars);
        wp_die('Deleted');
    }
    wp_die('');
}

/* * * Require Advance Options ** */
require_once get_template_directory() . '/inc/register_sidebar.php';
require_once get_template_directory() . '/inc/theme_control.php';


function ftc_setup() {
	add_editor_style('editor-styles');
  add_editor_style( 'assets/css/style-editor.css' );
  add_theme_support( 'dark-editor-style' );
  add_theme_support( 'style-editor' );
  add_theme_support( 'responsive-embeds' );
  // Add support for default block styles.
  add_theme_support( 'wp-block-styles' );
    // Add support for full and wide align images.
  add_theme_support( 'align-wide' );
  add_theme_support( 'wc-product-gallery-lightbox' );
  
    /*
     * Make theme available for translation.
     * Translations can be filed at WordPress.org. See: https://translate.wordpress.org/projects/wp-themes/ftc
     * If you're building a theme based on Karo, use a find and replace
     * to change 'karo' to the name of your theme in all the template files.
     */


    load_theme_textdomain('karo');

    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');
    /*Custom Color Gutenberg*/
    add_theme_support( 'editor-color-palette', array(
        array(
            'name' => __( 'strong magenta', 'karo' ),
            'slug' => 'strong-magenta',
            'color' => '#a156b4',
        ),
        array(
            'name' => __( 'light grayish magenta', 'karo' ),
            'slug' => 'light-grayish-magenta',
            'color' => '#d0a5db',
        ),
        array(
            'name' => __( 'very light gray', 'karo' ),
            'slug' => 'very-light-gray',
            'color' => '#eee',
        ),
        array(
            'name' => __( 'very dark gray', 'karo' ),
            'slug' => 'very-dark-gray',
            'color' => '#444',
        ),
    ) );
    /*Custom Font Gutenberg*/
    add_theme_support( 'editor-font-sizes', array(
        array(
            'name' => __( 'Small', 'karo' ),
            'size' => 12,
            'slug' => 'small'
        ),
        array(
            'name' => __( 'Normal', 'karo' ),
            'size' => 14,
            'slug' => 'normal'
        ),
        array(
            'name' => __( 'Large', 'karo' ),
            'size' => 36,
            'slug' => 'large'
        ),
        array(
            'name' => __( 'Huge', 'karo' ),
            'size' => 48,
            'slug' => 'huge'
        )
    ) );

    /*
     * Let WordPress manage the document title.
     * By adding theme support, we declare that this theme does not use a
     * hard-coded <title> tag in the document head, and expect WordPress to
     * provide it for us.
     */
    add_theme_support('title-tag');

    /*
     * Enable support for Post Thumbnails on posts and pages.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    add_image_size('ftc-featured-image', 2000, 1200, true);

    add_image_size('ftc-thumbnail-avatar', 100, 100, true);

    // Set the default content width.
    $GLOBALS['content_width'] = 1200;

    /* Translation */
    load_theme_textdomain('karo', get_template_directory() . '/languages');

    $locale = get_locale();
    $locale_file = get_template_directory() . "/languages/$locale.php";
    if (is_readable($locale_file)) {
        require_once( $locale_file );
    }

    // This theme uses wp_nav_menu() in two locations.
    register_nav_menus(array(
        'primary' => esc_html__('Primary Navigation', 'karo'),
        'vertical' => esc_html__('Vertical Navigation', 'karo'),
    ));

    /*
     * Switch default core markup for search form, comment form, and comments
     * to output valid HTML5.
     */
    add_theme_support('html5', array(
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    add_theme_support( 'editor-font-sizes', array(
        array(
            'name' => __( 'Small', 'karo' ),
            'size' => 12,
            'slug' => 'small'
        ),
        array(
            'name' => __( 'Normal', 'karo' ),
            'size' => 14,
            'slug' => 'normal'
        ),
        array(
            'name' => __( 'Large', 'karo' ),
            'size' => 36,
            'slug' => 'large'
        ),
        array(
            'name' => __( 'Huge', 'karo' ),
            'size' => 48,
            'slug' => 'huge'
        )
    ) );

    /*Color Gutenberg*/
    add_theme_support( 'editor-color-palette', array(
        array(
            'name' => __( 'strong magenta', 'karo' ),
            'slug' => 'strong-magenta',
            'color' => '#a156b4',
        ),
        array(
            'name' => __( 'light grayish magenta', 'karo' ),
            'slug' => 'light-grayish-magenta',
            'color' => '#d0a5db',
        ),
        array(
            'name' => __( 'very light gray', 'karo' ),
            'slug' => 'very-light-gray',
            'color' => '#eee',
        ),
        array(
            'name' => __( 'very dark gray', 'karo' ),
            'slug' => 'very-dark-gray',
            'color' => '#444',
        ),
    ) );

    /*
     * Enable support for Post Formats.
     *
     * See: https://codex.wordpress.org/Post_Formats
     */
    add_theme_support('post-formats', array(
        'image',
        'video',
        'gallery',
        'audio',
        'quote',
    ));

    // Add theme support for Custom Background
    $defaults = array(
        'default-color' => ''
        , 'default-image' => ''
    );
    add_theme_support('custom-background', $defaults);

    // Add theme support for Custom Logo.
    add_theme_support('custom-logo', array(
        'width' => 250,
        'height' => 250,
        'flex-width' => true,
    ));

    // Add theme support for selective refresh for widgets.
    add_theme_support('customize-selective-refresh-widgets');

    add_theme_support('woocommerce');

    if (!isset($content_width)) {
        $content_width = 1200;
    }

    /*
     * This theme styles the visual editor to resemble the theme style,
     * specifically font, colors, and column width.
     */
    add_editor_style(array('assets/css/editor-style.css', ftc_fonts_url()));
}

add_action('after_setup_theme', 'ftc_setup');
if(isset($smof_data['ftc_prod_advanced_zoom']) && $smof_data['ftc_prod_advanced_zoom'] != 'type_2'){
    add_action('after_setup_theme', 'ftc_setup_main_image');

    function ftc_setup_main_image(){
      add_theme_support( 'wc-product-gallery-zoom' );
      add_theme_support( 'wc-product-gallery-slider' );
  }
}
add_filter( 'woocommerce_single_product_carousel_options', 'ftc_update_woo_flexslider_options' );
/** 
 * Filer WooCommerce Flexslider options - Add Navigation Arrows
 */
function ftc_update_woo_flexslider_options( $options ) {

    $options['directionNav'] = true;

    return $options;
}

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function ftc_content_width() {

    $content_width = $GLOBALS['content_width'];

    // Get layout.
    $page_layout = get_theme_mod('page_layout');

    // Check if layout is one column.
    if ('one-column' === $page_layout) {
        if (ftc_is_frontpage()) {
            $content_width = 644;
        } elseif (is_page()) {
            $content_width = 740;
        }
    }

    // Check if is single post and there is no sidebar.
    if (is_single() && !is_active_sidebar('sidebar-1')) {
        $content_width = 740;
    }

    /**
     * Filter Karo content width of the theme.
     *
     * @since Karo 1.0
     *
     * @param $content_width integer
     */
    $GLOBALS['content_width'] = apply_filters('ftc_content_width', $content_width);
}

add_action('template_redirect', 'ftc_content_width', 0);

/**
 * Register custom fonts.
 */
function ftc_fonts_url() {
    $fonts_url = '';

    /**
     * Translators: If there are characters in your language that are not
     * supported by Libre Franklin, translate this to 'off'. Do not translate
     * into your own language.
     */
    $dosis = _x('on', 'Montserrat, Lato font: on or off', 'karo');

    if ('off' !== $dosis) {
        $font_families = array();

        $font_families[] = 'Lato:300i,400,400i,700|Montserrat:400,500,600,700,900';

        $query_args = array(
            'family' => urlencode(implode('|', $font_families))
        );

        $fonts_url = add_query_arg($query_args, 'https://fonts.googleapis.com/css');
    }

    return esc_url_raw($fonts_url);
}

/**
 * Add preconnect for Google Fonts.
 *
 * @since Karo 1.0
 *
 * @param array  $urls           URLs to print for resource hints.
 * @param string $relation_type  The relation type the URLs are printed.
 * @return array $urls           URLs to print for resource hints.
 */
function ftc_resource_hints($urls, $relation_type) {
    if (wp_style_is('ftc-fonts', 'queue') && 'preconnect' === $relation_type) {
        $urls[] = array(
            'href' => 'https://fonts.gstatic.com',
            'crossorigin',
        );
    }

    return $urls;
}

add_filter('wp_resource_hints', 'ftc_resource_hints', 10, 2);

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... and
 * a 'Continue reading' link.
 *
 * @since Karo 1.0
 *
 * @return string 'Continue reading' link prepended with an ellipsis.
 */
function ftc_excerpt_more($link) {
    if (is_admin()) {
        return $link;
    }

    $link = sprintf('<p class="link-more"><a href="%1$s" class="more-link">%2$s</a></p>', esc_url(get_permalink(get_the_ID())),
        /* translators: %s: Name of current post */ sprintf(__('Continue reading<span class="screen-reader-text"> "%s"</span>', 'karo'), get_the_title(get_the_ID()))
    );
    return ' &hellip; ' . $link;
}

add_filter('excerpt_more', 'ftc_excerpt_more');

/**
 * Enqueue scripts and styles.
 */
function ftc_scripts() {
    wp_enqueue_script('magnific-popup', get_template_directory_uri() . '/assets/js/jquery.magnific-popup.min.js', array(), null, true); 
    wp_enqueue_script('infinite-scroll', get_template_directory_uri() . '/assets/js/infinite-scroll.pkgd.min.js', array(), null, true);
    wp_enqueue_script('threesixty', get_template_directory_uri() . '/assets/js/threesixty.min.js', array(), null, true);
    wp_enqueue_script( 'swipebox-min', get_template_directory_uri().'/assets/js/jquery.swipebox.min.js', array(), null, true);
    wp_enqueue_script( 'swipebox', get_template_directory_uri().'/assets/js/jquery.swipebox.js', array(), null, true);
    wp_enqueue_script( 'xzooom', get_template_directory_uri().'/assets/js/xzoom.js', array(), null, true);
    wp_enqueue_script( 'xzooom-min', get_template_directory_uri().'/assets/js/xzoom.min.js', array(), null, true);

    wp_enqueue_style('editor-styles', get_template_directory_uri() . '/assets/css/style-editor.css');
    wp_enqueue_script( 'quick-view', get_template_directory_uri().'/assets/js/quick_view.js', array(), null, true);
    global $smof_data, $ftc_page_datas;

    wp_deregister_style('font-awesome');
    wp_deregister_style('yith-wcwl-font-awesome');
    wp_register_style('font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.css');
    wp_enqueue_style('font-awesome');
    wp_register_style('simple-line-icons', get_template_directory_uri() . '/assets/css/simple-line-icons.css');
    wp_enqueue_style('simple-line-icons');

    wp_register_style('owl-carousel', get_template_directory_uri() . '/assets/css/owl.carousel.min.css');
    wp_enqueue_style('owl-carousel');

    // Add custom fonts, used in the main stylesheet.
    wp_enqueue_style('ftc-fonts', ftc_fonts_url(), array(), null);

    // Theme stylesheet.
    wp_enqueue_style('ftc-style', get_stylesheet_uri());

    // Load the dark colorscheme.
    if ('dark' === get_theme_mod('colorscheme', 'light') || is_customize_preview()) {
        wp_enqueue_style('ftc-colors-dark', get_theme_file_uri('/assets/css/colors-dark.css'), array('ftc-style'), '1.0');
    }
    wp_enqueue_style('style-editor', get_theme_file_uri('/assets/css/style-editor.css'));
    wp_enqueue_style('style-editor');
    wp_register_style('ftc-reset', get_template_directory_uri() . '/assets/css/default.css');
    wp_enqueue_style('ftc-reset');

    wp_register_style('ftc-responsive', get_template_directory_uri() . '/assets/css/responsive.css');
    wp_enqueue_style('ftc-responsive');
    wp_register_style('ftc-prettyphoto', get_template_directory_uri() . '/assets/css/prettyphoto.css');
    wp_enqueue_style('ftc-prettyphoto');

    wp_register_script('owl-carousel', get_theme_file_uri('/assets/js/owl.carousel.min.js'), array(), null, true);
    wp_enqueue_script('owl-carousel');
    
    wp_register_script('jquery.prettyphoto', get_theme_file_uri('/assets/js/jquery.prettyphoto.js'), array(), null, true);
    wp_enqueue_script('jquery.prettyphoto');

    wp_enqueue_script('cookie', get_template_directory_uri().'/assets/js/jquery.cookie.min.js',array( 'jquery' ), null, true );
    // Load Libraries.
    wp_enqueue_script( 'sticky', get_template_directory_uri() . '/assets/js/jquery.sticky.js' , array(), null, true );

    wp_enqueue_script('isotope', get_theme_file_uri('/assets/js/isotope.min.js'), array(), null, true);
    wp_enqueue_script('throttle', get_theme_file_uri('/assets/js/jquery.ba-throttle-debounce.min.js'), array(), null, true);
    wp_enqueue_script('countto', get_theme_file_uri('/assets/js/jquery.countto.js'), array(), null, true);
    wp_enqueue_script('hoverin', get_theme_file_uri('/assets/js/jquery.hoverintent.js'), array(), null, true);
    wp_enqueue_script('mbytplayer', get_theme_file_uri('/assets/js/jquery.mb.ytplayer.js'), array(), null, true);
    wp_enqueue_script('parallax', get_theme_file_uri('/assets/js/jquery.parallax.js'), array(), null, true);

    wp_enqueue_script('tweenlite', get_theme_file_uri('/assets/js/tweenlite.min.js'), array(), null, true);
    wp_enqueue_script('tweenmax', get_theme_file_uri('/assets/js/tweenmax.min.js'), array(), null, true);
    wp_enqueue_script('waypoint', get_theme_file_uri('/assets/js/waypoint.min.js'), array(), null, true);
    /*smarth menu*/
    wp_enqueue_script( 'smartmenus', get_template_directory_uri().'/assets/js/ap-image-zoom.js', array(), null, true);
    wp_enqueue_script( 'ap-zoom', get_template_directory_uri().'/assets/js/ap-image-zoom.min.js', array(), null, true);
    wp_enqueue_script( 'ap-zoom-min', get_template_directory_uri().'/assets/js/jquery.smartmenus.js', array(), null, true);

    // Load the html5 shiv.
    wp_enqueue_script('html5', get_theme_file_uri('/assets/js/html5.js'), array(), '3.7.3');
    wp_script_add_data('html5', 'conditional', 'lt IE 9');

    wp_enqueue_script('ftc-skip-link-focus-fix', get_theme_file_uri('/assets/js/skip-link-focus-fix.js'), array(), '1.0', true);

    if( wp_is_mobile() ){
        wp_enqueue_script('mobile-js', get_template_directory_uri() . '/assets/js/mobile.js', array(), null, true);
    }
    global $smof_data;

    if (isset($smof_data['ftc_style_for_elementor']) ) {
        $topic = $smof_data['ftc_style_for_elementor'] ;
        if ($topic != 'jewelry') {

          wp_register_style('ftc-header-element-style', get_template_directory_uri() . '/header/'.$topic.'/header-style.css');
          wp_enqueue_style('ftc-header-element-style');
      }
  }

  $ftc_l10n = array(
    'quote' => ftc_get_svg(array('icon' => 'quote-right')),
);

  if (has_nav_menu('top')) {
    wp_enqueue_script('ftc-navigation', get_theme_file_uri('/assets/js/navigation.js'), array(), '1.0', true);
    $ftc_l10n['expand'] = esc_html__('Expand child menu', 'karo');
    $ftc_l10n['collapse'] = esc_html__('Collapse child menu', 'karo');
    $ftc_l10n['icon'] = ftc_get_svg(array('icon' => 'angle-down', 'fallback' => true));
}


// if (is_singular('product') && isset($smof_data['ftc_prod_cloudzoom']) && $smof_data['ftc_prod_cloudzoom']) {
//     wp_register_script('cloud-zoom', get_template_directory_uri() . '/assets/js/cloud-zoom.js', array('jquery'), null, true);
//     wp_enqueue_script('cloud-zoom');
// }

wp_enqueue_script('jquery-scrollto', get_theme_file_uri('/assets/js/jquery.scrollto.js'), array('jquery'), '2.1.2', true);

wp_enqueue_script('ftc-global', get_theme_file_uri('/assets/js/custom.js'), array('jquery'), '1.0', true);
wp_localize_script('ftc-skip-link-focus-fix', 'ftcScreenReaderText', $ftc_l10n);


if (defined('ICL_LANGUAGE_CODE')) {
    $ajax_uri = admin_url('admin-ajax.php?lang=' . ICL_LANGUAGE_CODE, 'relative');
} else {
    $ajax_uri = admin_url('admin-ajax.php', 'relative');
}

$data = array(
    'ajax_uri' => $ajax_uri,
    '_ftc_enable_responsive' => isset($smof_data['ftc_responsive']) ? (int) $smof_data['ftc_responsive'] : 1,
    '_ftc_enable_ajax_search' => isset($smof_data['ftc_ajax_search']) ? (int) $smof_data['ftc_ajax_search'] : 1,
    'cookies_version' => isset($smof_data['cookies_version']) ? (int) $smof_data['cookies_version'] : 1
);
wp_localize_script('ftc-global', 'ftc_shortcode_params', $data);


wp_enqueue_script('jquery-caroufredsel', get_template_directory_uri() . '/assets/js/jquery.caroufredsel-6.2.1.min.js', array(), null, true);


wp_enqueue_script('wc-add-to-cart-variation');

if (is_singular() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
}

}

add_action('wp_enqueue_scripts', 'ftc_scripts', 1000);

/* Cookie Notice */
if( ! function_exists( 'ftc_cookies_popup' ) ) {
    add_action( 'wp_footer', 'ftc_cookies_popup');

    function ftc_cookies_popup() {
        global $smof_data;
        if( isset($smof_data['cookies_info']) && !$smof_data['cookies_info'] ) return;

        if( isset($smof_data['cookies_title']) && $smof_data['cookies_title'] != '' || isset($smof_data['cookies_text']) && $smof_data['cookies_text'] != '' ){

            ?>
            <div class="ftc-cookies-popup">
                <div class="ftc-cookies-inner">
                    <div class="cookies-info-text">
                       <a href="#" class="cookies-title"> 
                        <?php if( isset($smof_data['cookies_title']) && $smof_data['cookies_title'] != ''){
                            echo esc_html(do_shortcode($smof_data['cookies_title'])) ;
                        }
                        ?>
                    </a>
                    <p>
                        <?php if( isset($smof_data['cookies_text']) && $smof_data['cookies_text'] != ''){
                            echo esc_html(do_shortcode($smof_data['cookies_text'])) ;
                        }
                        ?>
                    </p>
                </div>
                <div class="cookies-buttons">
                    <a href="#" class="btn btn-size-small btn-color-primary cookies-accept-btn"><?php esc_html_e( 'Yes, I Accept ', 'karo' ); ?></a>
                </div>
            </div>
        </div>
        <?php
    }
}
}
/**
 * Use front-page.php when Front page displays is set to a static page.
 *
 * @since Karo 1.0
 *
 * @param string $template front-page.php.
 *
 * @return string The template to be used: blank if is_home() is true (defaults to index.php), else $template.
 */
function ftc_front_page_template($template) {
    return is_home() ? '' : $template;
}

add_filter('frontpage_template', 'ftc_front_page_template');

/**
 * Implement the Custom Header feature.
 */
require get_parent_theme_file_path('/inc/custom-header.php');

/**
 * Custom template tags for this theme.
 */
require get_parent_theme_file_path('/inc/template-tags.php');

/**
 * Additional features to allow styling of the templates.
 */
require get_parent_theme_file_path('/inc/template-functions.php');

/**
 * Customizer additions.
 */
require get_parent_theme_file_path('/inc/customizer.php');
/**
 * Filter by color.
 */
require get_parent_theme_file_path('/inc/filter_by_color_options.php');

/**
 * SVG icons functions and filters.
 */
require get_parent_theme_file_path('/inc/icon-functions.php');

/* * * Visual Composer plugin ** */
if (class_exists('Vc_Manager') && class_exists('WPBakeryVisualComposerAbstract')) {
    $file_names = array('vc_map', 'update_param');
    foreach ($file_names as $file) {
        $file_path = get_template_directory() . '/inc/vc_extension/' . $file . '.php';
        if (file_exists($file_path)) {
            require_once $file_path;
        }
    }

    vc_set_shortcodes_templates_dir(get_template_directory() . '/inc/vc_extension/templates');

    /* Disable VC Frontend Editor */
    // vc_disable_frontend();
}

/* * * Save Of Options - Save Dynamic css ** */
add_action('of_save_options_after', 'ftc_update_dynamic_css', 10000);
if (!function_exists('ftc_update_dynamic_css')) {

    function ftc_update_dynamic_css($data = array()) {

        if (!is_array($data)) {
            return -1;
        }
        if (is_array($data['data'])) {
            $data = $data['data'];
        } else {
            return -1;
        }

        $upload_dir = wp_upload_dir();
        $filename_dir = trailingslashit($upload_dir['basedir']) . strtolower(str_replace(' ', '', wp_get_theme()->get('Name'))) . '.css';
        ob_start();
        include get_template_directory() . '/inc/dynamic_style.php';
        $dynamic_css = ob_get_contents();
        ob_end_clean();

        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once( ABSPATH . '/wp-admin/includes/file.php' );
            WP_Filesystem();
        }

        $creds = request_filesystem_credentials($filename_dir, '', false, false, array());
        if (!WP_Filesystem($creds)) {
            return false;
        }

        if ($wp_filesystem) {
            $wp_filesystem->put_contents(
                $filename_dir, $dynamic_css, FS_CHMOD_FILE
            );
        }
    }

}

function ftc_register_custom_css() {
    global $smof_data;
    ob_start();
    include_once get_template_directory() . '/inc/dynamic_style.php';
    if(isset($smof_data['ftc_style_for_elementor'])){
        $topic = $smof_data['ftc_style_for_elementor'] ;
        if($topic != 'jewelry'){
            include_once get_template_directory() . '/inc/dynamic/'.$topic.'.php';
        }
    }
    $dynamic_css = ob_get_contents();
    ob_end_clean();
    wp_add_inline_style('ftc-style', $dynamic_css);
}
add_action('wp_enqueue_scripts', 'ftc_register_custom_css', 9999);


/* * * Register Back End Scripts ** */

function ftc_register_admin_scripts(){
    wp_enqueue_media();
    wp_register_style( 'font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.css' );
    wp_enqueue_style( 'font-awesome' );
    
    wp_register_style( 'ftc-admin-style', get_template_directory_uri() . '/assets/css/admin-style.css' );
    wp_enqueue_style( 'ftc-admin-style' );
    wp_register_style('ftc-theme-options', get_template_directory_uri() . '/admin/css/options.css');
    wp_enqueue_style('ftc-theme-options');
    
    wp_register_script( 'ftc-admin-script', get_template_directory_uri().'/assets/js/admin-main.js', array('jquery'), null, true);
    wp_enqueue_script( 'ftc-admin-script' );
    $data = array(
        'ajax_uri' => admin_url( 'admin-ajax.php' ),
    );
    wp_localize_script('ftc-admin-script', 'ftc_shortcode_params', $data);
}
add_action('admin_enqueue_scripts', 'ftc_register_admin_scripts');


/* * * Favicon ** */
if (!function_exists('ftc_theme_favicon')) {

    function ftc_theme_favicon() {
        if (function_exists('wp_site_icon') && function_exists('has_site_icon') && has_site_icon()) {
            return;
        }
        global $smof_data;
        $favicon = isset($smof_data['ftc_favicon'] ['url']) ? esc_url($smof_data['ftc_favicon'] ['url']) : '';
        if (strlen($favicon) > 0):
            ?>
            <link rel="shortcut icon" href="<?php echo esc_url($favicon); ?>" />
            <?php
        endif;
    }

}

/* * * Logo ** */
if (!function_exists('ftc_theme_logo')) {

    function ftc_theme_logo() {
        global $smof_data;
        $logo_image = isset($smof_data['ftc_logo'] ['url']) ? esc_url($smof_data['ftc_logo'] ['url']) : '';
        $logo_text = isset($smof_data['ftc_text_logo']) ? stripslashes(esc_attr($smof_data['ftc_text_logo'])) : '';
        ?>
        <div class="logo">
            <a href="<?php echo esc_url(home_url('/')); ?>">
                <!-- Main logo -->
                <?php if (strlen($logo_image) > 0): ?>
                     <img src="<?php echo esc_url($logo_image); ?>" alt="<?php echo!empty($logo_text) ? esc_attr($logo_text) : get_bloginfo('name'); ?>" title="<?php echo!empty($logo_text) ? esc_attr($logo_text) : get_bloginfo('name'); ?>" class="normal-logo" width="" height="" />
                <?php endif; ?>

                <!-- Logo Text -->
                <?php
                if (strlen($logo_image) == 0) {
                    echo esc_html($logo_text);
                }
                ?>
            </a>
        </div>
        <?php
    }

}

/* * * Product Search Form by Category ** */
if (!function_exists('ftc_get_search_form_by_category')) {

    function ftc_get_search_form_by_category() {
        $search_for_product = ftc_has_woocommerce();
        // if ($search_for_product) {
        //     $taxonomy = 'product_cat';
        //     $post_type = 'product';
        //     $orderby = 'relevance';
        //     $placeholder_text = esc_html__('Search ...', 'karo');
        // } else {
            $taxonomy = 'category';
            $post_type = 'post';
            $orderby = '';
            $placeholder_text = esc_html__('Search', 'karo');
        // }

        $options = '<option value="">' . esc_html__('All categories', 'karo') . '</option>';
        $options .= ftc_search_by_category_get_option_html($taxonomy, 0, 0);

        $rand = rand(0, 1000);
        $form = '<div class="ftc-search">
        <button class="fa fa-search search-button" type="submit"><span>' . esc_html__('Search','karo') . '</span></button>
        <form method="get" id="searchform' . $rand . '" action="' . esc_url(home_url('/')) . '">
        <select class="select-category" name="term">' . $options . '</select>
        <div class="ftc_search_ajax">
        <div class="ajax-search-content">
        <input type="text" value="' . get_search_query() . '" name="s" id="s' . $rand . '" placeholder="' . $placeholder_text . '" autocomplete="off" />


        <input type="hidden" name="post_type" value="' . $post_type . '" />
        <input type="hidden" name="taxonomy" value="' . $taxonomy . '" />
        <input type="hidden" name="orderby" value="' . $orderby . '" />
        </div>
        </div>
        </form></div>';

        print_r($form);
    }

}

if (!function_exists('ftc_search_by_category_get_option_html')) {

    function ftc_search_by_category_get_option_html($taxonomy = 'product_cat', $parent = 0, $level = 0) {
        $options = '';
        $spacing = '';
        for ($i = 0; $i < $level * 3; $i++) {
            $spacing .= '&nbsp;';
        }

        $args = array(
            'number' => ''
            , 'hide_empty' => 1
            , 'orderby' => 'name'
            , 'order' => 'asc'
            , 'parent' => $parent
        );

        $select = '';
        $categories = get_terms($taxonomy, $args);
        if (is_search() && isset($_GET['term']) && $_GET['term'] != '') {
            $select = $_GET['term'];
        }
        $level++;
        if (is_array($categories)) {
            foreach ($categories as $cat) {
                $options .= '<option value="' . $cat->slug . '" ' . selected($select, $cat->slug, false) . '>' . $spacing . $cat->name . '</option>';
                $options .= ftc_search_by_category_get_option_html($taxonomy, $cat->term_id, $level);
            }
        }

        return $options;
    }

}


/* Ajax search */
add_action('wp_ajax_ftc_ajax_search', 'ftc_ajax_search');
add_action('wp_ajax_nopriv_ftc_ajax_search', 'ftc_ajax_search');
if (!function_exists('ftc_ajax_search')) {

    function ftc_ajax_search() {

        check_ajax_referer( 'platform_security', 'security' );
        global $wpdb, $post, $smof_data;

        $search_for_product = ftc_has_woocommerce();
        if ($search_for_product) {
            $taxonomy = 'product_cat';
            $post_type = 'product';
        } else {
            $taxonomy = 'category';
            $post_type = 'post';
        }

        $num_result = isset($smof_data['ftc_ajax_search_number_result']) ? (int) $smof_data['ftc_ajax_search_number_result'] : 10;
        $desc_limit_words = isset($smof_data['ftc_prod_cat_grid_desc_words']) ? (int) $smof_data['ftc_prod_cat_grid_desc_words'] : 10;

        $search_string = sanitize_text_field($_POST['search_string']);
        $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';

        $args = array(
            'post_type' => $post_type
            , 'post_status' => 'publish'
            , 's' => $search_string
            , 'posts_per_page' => $num_result
        );

        if ($search_for_product) {
            $args['meta_query'] = array(
                array(
                    'key' => '_visibility'
                    , 'value' => array('catalog', 'visible')
                    , 'compare' => 'IN'
                )
            );
        }

        if ($category != '') {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => $taxonomy
                    , 'terms' => $category
                    , 'field' => 'slug'
                )
            );
        }

        $results = new WP_Query($args);

        if ($results->have_posts()) {
            $extra_class = '';
            if (isset($results->post_count, $results->found_posts)) {
                $extra_class = 'has-view-all';
            }

            $html = '<ul class="' . $extra_class . '">';
            while ($results->have_posts()) {
                $results->the_post();
                $link = get_permalink($post->ID);

                $image = '';
                if ($post_type == 'product') {
                    $product = wc_get_product($post->ID);
                    $image = $product->get_image();
                } else if (has_post_thumbnail($post->ID)) {
                    $image = get_the_post_thumbnail($post->ID, 'thumbnail');
                }

                $html .= '<li>';
                $html .= '<div class="thumbnail">';
                $html .= '<a href="' . esc_url($link) . '">' . $image . '</a>';
                $html .= '</div>';
                $html .= '<div class="meta">';
                $html .= '<a href="' . esc_url($link) . '" class="title">' . ftc_search_highlight_string($post->post_title, $search_string) . '</a>';
                $html .= '<div class="description">' . ftc_the_excerpt_max_words($desc_limit_words, '', true, ' ...', false) . '</div>';
                if ($post_type == 'product') {
                    if ($price_html = $product->get_price_html()) {
                        $html .= '<span class="price">' . $price_html . '</span>';
                    }
                }
                $html .= '</div>';
                $html .= '</li>';
            }
            $html .= '</ul>';

            if (isset($results->post_count, $results->found_posts)) {
                $view_all_text = sprintf(esc_html__('View all %d results', 'karo'), $results->found_posts);

                $html .= '<div class="view-all-wrapper">';
                $html .= '<a href="'.home_url().'">' . $view_all_text . '</a>';
                $html .= '</div>';
            }

            wp_reset_postdata();

            $return = array();
            $return['html'] = $html;
            $return['search_string'] = $search_string;
            wp_die(json_encode($return));
        }

        wp_die('');
    }

}

if (!function_exists('ftc_search_highlight_string')) {

    function ftc_search_highlight_string($string, $search_string) {
        $new_string = '';
        $pos_left = stripos($string, $search_string);
        if ($pos_left !== false) {
            $pos_right = $pos_left + strlen($search_string);
            $new_string_right = substr($string, $pos_right);
            $search_string_insensitive = substr($string, $pos_left, strlen($search_string));
            $new_string_left = stristr($string, $search_string, true);
            $new_string = $new_string_left . '<span class="hightlight">' . $search_string_insensitive . '</span>' . $new_string_right;
        } else {
            $new_string = $string;
        }
        return $new_string;
    }

}

/* Match with ajax search results */
add_filter('woocommerce_get_catalog_ordering_args', 'ftc_woocommerce_get_catalog_ordering_args_filter');
if (!function_exists('ftc_woocommerce_get_catalog_ordering_args_filter')) {

    function ftc_woocommerce_get_catalog_ordering_args_filter($args) {
        global $smof_data;
        if (is_search() && !isset($_GET['orderby']) && get_option('woocommerce_default_catalog_orderby') == 'menu_order' && isset($smof_data['ftc_ajax_search']) && $smof_data['ftc_ajax_search']) {
            $args['orderby'] = '';
            $args['order'] = '';
        }
        return $args;
    }

}
/* * * Page Layout Columns Class ** */

if (!function_exists('ftc_page_layout_columns_class')) {

    function ftc_page_layout_columns_class($page_column) {
        $data = array();

        if (empty($page_column)) {
            $page_column = '0-1-0';
        }

        $layout_config = explode('-', $page_column);
        $left_sidebar = (int) $layout_config[0];
        $right_sidebar = (int) $layout_config[2];
        $main_class = ($left_sidebar + $right_sidebar) == 2 ? 'ftc-col-12' : ( ($left_sidebar + $right_sidebar) == 1 ? 'col-sm-9 col-xs-12' : 'col-sm-12 col-xs-12' );

        $data['left_sidebar'] = $left_sidebar;
        $data['right_sidebar'] = $right_sidebar;
        $data['main_class'] = $main_class;
        $data['left_sidebar_class'] = 'col-sm-3';
        $data['right_sidebar_class'] = 'col-sm-3';

        return $data;
    }

}

/* * * Social Sharing ** */
if (!function_exists('ftc_template_social_sharing')) {

    function ftc_template_social_sharing() {
        if (is_active_sidebar('product-detail-social-icon')) {
            dynamic_sidebar('product-detail-social-icon');
        }
    }

}
if (!function_exists('ftc_the_excerpt_max_words')) {

    function ftc_the_excerpt_max_words($word_limit = -1, $post = '', $strip_tags = true, $extra_str = '', $echo = true) {
        if ($post) {
            $excerpt = ftc_get_the_excerpt_by_id($post->ID);
        } else {
            $excerpt = get_the_excerpt();
        }

        if ($strip_tags) {
            $excerpt = wp_strip_all_tags($excerpt);
            $excerpt = strip_shortcodes($excerpt);
        }

        if ($word_limit != -1)
            $result = ftc_string_limit_words($excerpt, $word_limit);
        else
            $result = $excerpt;

        $result .= $extra_str;

        if ($echo) {
            print_r(do_shortcode($result)) ;
        }
        return $result;
    }

}

if (!function_exists('ftc_get_the_excerpt_by_id')) {

    function ftc_get_the_excerpt_by_id($post_id = 0) {
        global $wpdb;
        $query = "SELECT post_excerpt, post_content FROM $wpdb->posts WHERE ID = %d LIMIT 1";
        $result = $wpdb->get_results($wpdb->prepare($query, $post_id), ARRAY_A);
        if ($result[0]['post_excerpt']) {
            return $result[0]['post_excerpt'];
        } else {
            return $result[0]['post_content'];
        }
    }

}
function ftc_comment($comment, $args, $depth) {
    if ( 'div' === $args['style'] ) {
        $tag       = 'div';
        $add_below = 'comment';
    } else {
        $tag       = 'li';
        $add_below = 'div-comment';
    }?>
    <<?php print_r($tag); ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?> id="comment-<?php comment_ID() ?>"><?php 
    if ( 'div' != $args['style'] ) { ?>
        <div id="div-comment-<?php comment_ID() ?>" class="comment-body"><?php
    } ?>
    <div class="comment-author vcard"><?php 
    if ( $args['avatar_size'] != 0 ) {
        echo get_avatar( $comment, $args['avatar_size'] ); 
    } ?>
</div>
<div class="total-comment">
    <div class="name"><?php 
    printf( __( '<span class="fn">%s</span> ','karo' ), get_comment_author_link() ); 
    ?>
    <span> . </span>
    <?php
    printf( __( '<span class="fn">%s</span> ','karo' ),  get_comment_date() ); 
    ?>
    <span> . </span>
</div>

<?php 
if ( $comment->comment_approved == '0' ) { ?>
    <em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.','karo' ); ?></em><br/><?php 
} ?>
<div class="comment-text">
    <?php comment_text(); ?>
</div>
<div class="reply"><?php 
comment_reply_link( 
    array_merge( 
        $args, 
        array( 
            'add_below' => $add_below, 
            'depth'     => $depth, 
            'max_depth' => $args['max_depth'] 
        ) 
    ) 
); ?>
</div></div><?php 
if ( 'div' != $args['style'] ) : ?>
    </div><?php 
endif;
}
/* * * Get excerpt ** */
if (!function_exists('ftc_string_limit_words')) {
 function ftc_string_limit_words($string, $word_limit)
 {
   $words = explode(' ', $string, ($word_limit + 1));
   if(count($words) > $word_limit) {
       array_pop($words);
   //add a ... at last article when more than limit word count
       echo implode(' ', $words)."."; } else {
   //otherwise
           echo implode(' ', $words); }
       }
   }


   /* * * Array Attribute Compare ** */
   if (!function_exists('ftc_array_atts')) {

    function ftc_array_atts($pairs, $atts) {
        $atts = (array) $atts;
        $out = array();
        foreach ($pairs as $name => $default) {
            if (array_key_exists($name, $atts)) {
                if (is_array($atts[$name]) && is_array($default)) {
                    $out[$name] = ftc_array_atts($default, $atts[$name]);
                } else {
                    $out[$name] = $atts[$name];
                }
            } else {
                $out[$name] = $default;
            }
        }
        return $out;
    }

}

/* * * Breadcrumbs ** */
if (!function_exists('ftc_breadcrumbs')) {

    function ftc_breadcrumbs() {
        global $smof_data;

        $is_rtl = is_rtl() || ( isset($smof_data['ftc_enable_rtl']) && $smof_data['ftc_enable_rtl'] );

        if (ftc_has_woocommerce()) {
            if (function_exists('woocommerce_breadcrumb') && function_exists('is_woocommerce') && is_woocommerce()) {
                woocommerce_breadcrumb(array('wrap_before' => '<div class="ftc-breadcrumbs-content">', 'delimiter' => '<span>' . ($is_rtl ? '\\' : '/') . '</span>', 'wrap_after' => '</div>'));
                return;
            }
        }

        if (function_exists('bbp_breadcrumb') && function_exists('is_bbpress') && is_bbpress()) {
            $args = array(
                'before' => '<div class="ftc-breadcrumbs-content">'
                , 'after' => '</div>'
                , 'sep' => $is_rtl ? '\\' : '/'
                , 'sep_before' => '<span class="brn_arrow">'
                , 'sep_after' => '</span>'
                , 'current_before' => '<span class="current">'
                , 'current_after' => '</span>'
            );

            bbp_breadcrumb($args);
            /* Remove bbpress breadcrumbs */
            add_filter('bbp_no_breadcrumb', '__return_true', 999);
            return;
        }

        $delimiter = '<span class="brn_arrow">' . ($is_rtl ? '\\' : '/') . '</span>';

        $front_id = get_option('page_on_front');
        if (!empty($front_id)) {
            $home = get_the_title($front_id);
        } else {
            $home = esc_html__('Home', 'karo');
        }
        $ar_title = array(
            'search' => esc_html__('Search results for ', 'karo')
            , '404' => esc_html__('Error 404', 'karo')
            , 'tagged' => esc_html__('Tagged ', 'karo')
            , 'author' => esc_html__('Articles posted by ', 'karo')
            , 'page' => esc_html__('Page', 'karo')
            , 'portfolio' => esc_html__('Portfolio', 'karo')
        );

        $before = '<span class="current">'; /* tag before the current crumb */
        $after = '</span>'; /* tag after the current crumb */
        global $wp_rewrite;
        $rewriteUrl = $wp_rewrite->using_permalinks();
        if (!is_home() && !is_front_page() || is_paged()) {

            echo '<div class="ftc-breadcrumbs-content">';

            global $post;
            $homeLink = esc_url(home_url('/'));
            echo '<a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';

            if (is_category()) {
                global $wp_query;
                $cat_obj = $wp_query->get_queried_object();
                $thisCat = $cat_obj->term_id;
                $thisCat = get_category($thisCat);
                $parentCat = get_category($thisCat->parent);
                if ($thisCat->parent != 0) {
                    echo get_category_parents($parentCat, true, ' ' . $delimiter . ' ');
                }
                print_r($before); print_r(single_cat_title('', false)); print_r($after);
            } elseif (is_search()) {
                print_r($before); print_r($ar_title['search'] . '"' . get_search_query() . '"'); print_r($after);
            } elseif (is_day()) {
                echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
                echo '<a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
                print_r($before); print_r(get_the_time('d')); print_r($after);
            } elseif (is_month()) {
                echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
                print_r($before); print_r(get_the_time('F')); print_r($after);
            } elseif (is_year()) {
                print_r($before); print_r(get_the_time('Y')); print_r($after);
            } elseif (is_single() && !is_attachment()) {
                if (get_post_type() != 'post') {
                    $post_type = get_post_type_object(get_post_type());
                    $slug = $post_type->rewrite;
                    $post_type_name = $post_type->labels->singular_name;
                    if (strcmp('Portfolio Item', $post_type->labels->singular_name) == 0) {
                        $post_type_name = $ar_title['portfolio'];
                    }
                    if ($rewriteUrl) {
                        echo '<a href="' . $homeLink . $slug['slug'] . '/">' . $post_type_name . '</a> ' . $delimiter . ' ';
                    } else {
                        echo '<a href="' . $homeLink . '?post_type=' . get_post_type() . '">' . $post_type_name . '</a> ' . $delimiter . ' ';
                    }

                    print_r($before); print_r(get_the_title()); print_r($after);
                } else {
                    $cat = get_the_category();
                    $cat = $cat[0];
                    echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
                    print_r($before); print_r(get_the_title()); print_r($after);
                }
            } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                $post_type_name = $post_type->labels->singular_name;
                if (strcmp('Portfolio Item', $post_type->labels->singular_name) == 0) {
                    $post_type_name = $ar_title['portfolio'];
                }
                if (is_tag()) {
                    print_r($before); print_r($ar_title['tagged'] . '"'); print_r(single_tag_title('', false) . '"'); print_r($after);
                } elseif (is_taxonomy_hierarchical(get_query_var('taxonomy'))) {
                    if ($rewriteUrl) {
                        echo '<a href="' . $homeLink . $slug['slug'] . '/">' . $post_type_name . '</a> ' . $delimiter . ' ';
                    } else {
                        echo '<a href="' . $homeLink . '?post_type=' . get_post_type() . '">' . $post_type_name . '</a> ' . $delimiter . ' ';
                    }

                    $curTaxanomy = get_query_var('taxonomy');
                    $curTerm = get_query_var('term');
                    $termNow = get_term_by('name', $curTerm, $curTaxanomy);
                    $pushPrintArr = array();
                    if ($termNow !== false) {
                        while ((int) $termNow->parent != 0) {
                            $parentTerm = get_term((int) $termNow->parent, get_query_var('taxonomy'));
                            array_push($pushPrintArr, '<a href="' . get_term_link((int) $parentTerm->term_id, $curTaxanomy) . '">' . $parentTerm->name . '</a> ' . $delimiter . ' ');
                            $curTerm = $parentTerm->name;
                            $termNow = get_term_by('name', $curTerm, $curTaxanomy);
                        }
                    }
                    $pushPrintArr = array_reverse($pushPrintArr);
                    array_push($pushPrintArr, $before . get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'))->name . $after);
                    echo implode($pushPrintArr);
                } else {
                    print_r($before) ; print_r($post_type_name) ; print_r($after);
                }
            } elseif (is_attachment()) {
                if ((int) $post->post_parent > 0) {
                    $parent = get_post($post->post_parent);
                    $cat = get_the_category($parent->ID);
                    if (count($cat) > 0) {
                        $cat = $cat[0];
                        echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
                    }
                    echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
                }
                print_r($before); print_r(get_the_title()); print_r($after);
            } elseif (is_page() && !$post->post_parent) {
                print_r($before); print_r(get_the_title()); print_r($after);
            } elseif (is_page() && $post->post_parent) {
                $parent_id = $post->post_parent;
                $breadcrumbs = array();
                while ($parent_id) {
                    $page = get_post($parent_id);
                    $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
                    $parent_id = $page->post_parent;
                }
                $breadcrumbs = array_reverse($breadcrumbs);
                foreach ($breadcrumbs as $crumb)
                    print_r($crumb . ' '); print_r($delimiter . ' ');
                print_r($before); print_r(get_the_title()); print_r($after);
            } elseif (is_tag()) {
                print_r($before); print_r($ar_title['tagged'] . '"'); print_r(single_tag_title('', false) . '"'); print_r($after);
            } elseif (is_author()) {
                global $author;
                $userdata = get_userdata($author);
                print_r($before); print_r($ar_title['author']); print_r($userdata->display_name); print_r($after);
            } elseif (is_404()) {
                print_r($before); print_r($ar_title['404']); print_r($after);
            }

            if (get_query_var('paged')) {
                if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() || is_page_template() || is_post_type_archive() || is_archive()) {
                    print_r($before . ' (');
                }
                print_r($ar_title['page'] . ' '); print_r(get_query_var('paged'));
                if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() || is_page_template() || is_post_type_archive() || is_archive()) {
                    echo ')' . $after;
                }
            } else {
                if (get_query_var('page')) {
                    if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() || is_page_template() || is_post_type_archive() || is_archive()) {
                        print_r($before . ' (');
                    }
                    print_r($ar_title['page'] . ' '); print_r(get_query_var('page'));
                    if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() || is_page_template() || is_post_type_archive() || is_archive()) {
                        echo ')' . $after;
                    }
                }
            }
            echo '</div>';
        }

        wp_reset_postdata();
    }

}

function ftc_breadcrumbs_title($show_breadcrumb = false, $show_page_title = false, $page_title = '', $extra_class_title = '') {
    global $smof_data,$extra_class,$layout;
    if( isset($smof_data['enable_ftc_breadcrumb']) && $smof_data['enable_ftc_breadcrumb']){
        if ($show_breadcrumb || $show_page_title) {
            $breadcrumb_bg = '';
            if ( isset($smof_data['ftc_enable_breadcrumb_background_image']) && $smof_data['ftc_enable_breadcrumb_background_image']) {
                $breadcrumb_bg = esc_url($smof_data['ftc_bg_breadcrumbs'] ['url']);
            }

            $style = '';

            if ($breadcrumb_bg != '') {
                $style = 'style="background-image: url(' . $breadcrumb_bg . ')"';
                if (isset($smof_data['ftc_breadcrumb_bg_parallax']) && $smof_data['ftc_breadcrumb_bg_parallax']) {
                    $extra_class .= ' ftc-breadcrumb-parallax';
                }
            }
            if (isset($smof_data['ftc_breadcrumb_layout']) && $smof_data['ftc_breadcrumb_layout']) {
              $layout= $smof_data['ftc_breadcrumb_layout'];
          }
          echo '<div class="ftc-breadcrumb '.$layout.'" ' . $style . '>';
          if (isset($smof_data['ftc_enable_breadcrumb_background_image']) && $smof_data['ftc_enable_breadcrumb_background_image']) {
              echo '<div class="ftc-breadcrumb-title container">';
          }
          else{
            echo '<div class="ftc-breadcrumb-title-noback container">';
        }
        if ($show_page_title) {
            echo '<h1 class="product_title page-title entry-title ' . $extra_class_title . '">' . $page_title . '</h1>';
        }
        if ($show_breadcrumb) {
            ftc_breadcrumbs();
        }
        if(is_tax( get_object_taxonomies( 'product' ) ) || is_post_type_archive('product') || is_singular('product')){
            if(isset($smof_data['ftc_enable_category_breadcrumb']) && $smof_data['ftc_enable_category_breadcrumb'] ){
                echo '<div class="ftc-breadcrumbs-category">';
                dynamic_sidebar('list-categories-breadcrumbs');
                echo '</div>';
            }
        }
        echo '</div></div>';
    }
}
}

/* * * Add header dynamic css ** */
add_action('wp_head', 'ftc_add_header_dynamic_css', 1000);
if (!function_exists('ftc_add_header_dynamic_css')) {

    function ftc_add_header_dynamic_css($is_iframe = false) {
        if (!$is_iframe) {
            return;
        }
        $upload_dir = wp_upload_dir();
        $filename_dir = trailingslashit($upload_dir['basedir']) . strtolower(str_replace(' ', '', wp_get_theme()->get('Name'))) . '.css';
        $filename = trailingslashit($upload_dir['baseurl']) . strtolower(str_replace(' ', '', wp_get_theme()->get('Name'))) . '.css';
        if (is_ssl()) {
            $filename = str_replace('http://', 'https://', $filename);
        }
        if (file_exists($filename_dir)) {
            wp_register_style('header_dynamic', $filename);
            wp_enqueue_style('header_dynamic');
        }
    }

}


/* * * Register google font ** */

function ftc_register_google_font($iframe = false) {
    global $smof_data;
    $fonts = array();

    if ( isset($smof_data['ftc_body_font_enable_google_font']) && $smof_data['ftc_body_font_enable_google_font']) {
        $fonts[] = array(
            'name' => $smof_data['ftc_body_font_google']['font-family'] 
            , 'bold' => '300,400,500,600,700,800,900'
        );
    }

    if ( isset($smof_data['ftc_secondary_body_font_enable_google_font']) && $smof_data['ftc_secondary_body_font_enable_google_font']) {
        $fonts[] = array(
            'name' => $smof_data['ftc_secondary_body_font_google']['font-family'] 
            , 'bold' => '300,400,500,600,700,800,900'
        );
    }

    /* Default fonts */
    $fonts[] = array(
        'name' => 'Lato'
        , 'bold' => '300,400,500,600,700,800,900'
    );

    $fonts[] = array(
        'name' => 'Raleway'
        , 'bold' => '300,400,500,600,700,800,900'
    );

    foreach ($fonts as $font) {
        ftc_load_google_font($font['name'], $font['bold'], $iframe);
    }
}

function ftc_load_google_font($font_name = '', $font_bold = '300,400,500,600,700,800,900', $iframe = false) {
    if (strlen($font_name) > 0) {
        $font_name_id = sanitize_title($font_name);

        $font_url = add_query_arg('family', urlencode($font_name . ':' . $font_bold . '&subset=latin,latin-ext'), '//fonts.googleapis.com/css');
        if (!$iframe) {
            wp_enqueue_style("gg-{$font_name_id}", $font_url);
        } else {
            echo '<link rel="stylesheet" type="text/css" id="gg_' . $font_name_id . '" media="all" href="' . esc_url($font_url) . '" />';
        }
    }
}
if ( ! function_exists( 'carna_popup_newsletter' ) ) {
    function carna_popup_newsletter() {
       global $smof_data; 
       if(isset($smof_data['ftc_bg_popup_image']['url']) && !empty($smof_data['ftc_bg_popup_image']['url']))
        echo '<div class="popupshadow" style="display:none"></div>';
    echo '<div class="newsletterpopup" style="display:none; background-image: url('. esc_url($smof_data['ftc_bg_popup_image']['url']) .')">';
    echo '<span class="close-popup"></span>
    <div class="wp-newletter">';
    dynamic_sidebar('popup-newletter');
    echo '</div>';
    echo '<span class="dont_show_popup"><input id="ftc_dont_show_again" type="checkbox"><label for="ftc_dont_show_again">' .esc_attr__('Don\'t show popup again', 'karo'). '</label></span>';
    echo '</div>';
}
}

/* Install Required Plugins */
add_action('tgmpa_register', 'ftc_register_required_plugins');

function ftc_register_required_plugins() {
    $plugin_dir_path = get_template_directory() . '/inc/plugins/';
    $ver = wp_get_theme(); 
    $version = $ver->get('Version');
    $domain = $ver->get('TextDomain');
    /**
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(
        array(
            'name' => 'ThemeFTC', // The plugin name.
            'slug' => 'themeftc', // The plugin slug (typically the folder name).
            'source' => $plugin_dir_path . 'themeftc.zip', // The plugin source.
            'required' => true, // If false, the plugin is only 'recommended' instead of required.
            'version' => '1.1.2', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
            'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url' => '', // If set, overrides default API URL and points to an external URL.
        )
        , array(
            'name' => 'WooCommerce', // The plugin name.
            'slug' => 'woocommerce', // The plugin slug (typically the folder name).
            'source' => '', // The plugin source.
            'required' => false, // If false, the plugin is only 'recommended' instead of required.
        )
        , array(
            'name' => 'WPBakery Visual Composer', // The plugin name.
            'slug' => 'js_composer', // The plugin slug (typically the folder name).
            'source' => 'http://demo.themeftc.com/plugins/js_composer.zip', // The plugin source.
            'required' => true, // If false, the plugin is only 'recommended' instead of required.
            'version' => '6.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
            'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url' => '', // If set, overrides default API URL and points to an external URL.
        )
        , array(
            'name' => 'Elementor', // The plugin name.
            'slug' => 'elementor', // The plugin slug (typically the folder name)
            'required' => false , // If false, the plugin is only 'recommended' instead of required.
        )
        , array(
            'name' => 'Revolution Slider', // The plugin name.
            'slug' => 'revslider', // The plugin slug (typically the folder name).
            'source' => 'http://demo.themeftc.com/plugins/revslider.zip', // The plugin source.
            'required' => false, // If false, the plugin is only 'recommended' instead of required.
            'version' => '6.1.5', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
            'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url' => '', // If set, overrides default API URL and points to an external URL.
        )
        ,array(
            'name' => 'FTC Importer', // The plugin name.
            'slug' => 'ftc_importer', // The plugin slug (typically the folder name).
            'source'             => 'https://karo.themeftc.com/content/ftc-importer-karo-'.$version .'.zip', // The plugin source.
            'required' => true, // If false, the plugin is only 'recommended' instead of required.
            'version' => '1.2.0', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
            'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url' => '', // If set, overrides default API URL and points to an external URL.
        )
        ,array(
            'name' => 'ThemeFTC for Elementor', // The plugin name.
            'slug' => 'themeftc-for-elementor', // The plugin slug (typically the folder name).
            'source'             => $plugin_dir_path . 'themeftc-for-elementor.zip', // The plugin source.
            'required' => true, // If false, the plugin is only 'recommended' instead of required.
            'version' => '1.0.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
            'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url' => '', // If set, overrides default API URL and points to an external URL.
        )
        , array(
            'name' => 'Mega Main Menu', // The plugin name.
            'slug' => 'mega_main_menu', // The plugin slug (typically the folder name).
            'source' => 'http://demo.themeftc.com/plugins/mega_main_menu.zip', // The plugin source.
            'required' => false, // If false, the plugin is only 'recommended' instead of required.
            'version' => '2.1.5', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
            'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url' => '', // If set, overrides default API URL and points to an external URL.
        )
        , array(
            'name' => 'Contact Form 7', // The plugin name.
            'slug' => 'contact-form-7', // The plugin slug (typically the folder name).
            'required' => false, // If false, the plugin is only 'recommended' instead of required.
        )
        , array(
            'name' => 'YITH WooCommerce Wishlist', // The plugin name.
            'slug' => 'yith-woocommerce-wishlist', // The plugin slug (typically the folder name).
            'required' => false, // If false, the plugin is only 'recommended' instead of required.
        )
        , array(
            'name' => 'YITH WooCommerce Compare', // The plugin name.
            'slug' => 'yith-woocommerce-compare', // The plugin slug (typically the folder name).
            'required' => false, // If false, the plugin is only 'recommended' instead of required.
        )
        
        , array(
            'name' => 'Mailchimp for WordPress', // The plugin name.
            'slug' => 'mailchimp-for-wp', // The plugin slug (typically the folder name).
            'required' => false, // If false, the plugin is only 'recommended' instead of required.
        )
        , array(
            'name' => 'Redux Framework', // The plugin name.
            'slug' => 'redux-framework', // The plugin slug (typically the folder name).
            'required' => false, // If false, the plugin is only 'recommended' instead of required.
        )
        , array(
            'name' => 'YITH WooCommerce Ajax Product Filter', // The plugin name.
            'slug' => 'yith-woocommerce-ajax-navigation', // The plugin slug (typically the folder name).
            'required' => false, // If false, the plugin is only 'recommended' instead of required.
        )
        , array(
            'name' => 'WooCommerce Variation Swatches', // The plugin name.
            'slug' => 'woo-variation-swatches', // The plugin slug (typically the folder name).
            'required' => false, // If false, the plugin is only 'recommended' instead of required.
        )
    );

    /*
     * Array of configuration settings. Amend each line as needed.
     *
     * TGMPA will start providing localized text strings soon. If you already have translations of our standard
     * strings available, please help us make TGMPA even better by giving us access to these translations or by
     * sending in a pull-request with .po file(s) with the translations.
     *
     * Only uncomment the strings in the config array if you want to customize the strings.
     */
    $config = array(
        'id' => 'tgmpa', // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '', // Default absolute path to bundled plugins.
        'menu' => 'tgmpa-install-plugins', // Menu slug.
        'parent_slug' => 'themes.php', // Parent menu slug.
        'capability' => 'edit_theme_options', // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
        'has_notices' => true, // Show admin notices or not.
        'dismissable' => true, // If false, a user cannot dismiss the nag message.
        'dismiss_msg' => '', // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false, // Automatically activate plugins after installation or not.
        'message' => '', // Message to output right before the plugins table.
    );

    tgmpa($plugins, $config);
}
?>