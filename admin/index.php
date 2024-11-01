<?php

/**
 * Admin Menu Item
 */
function wsu_register_admin_menu_page_callback()
{
    add_menu_page(
        __('Short URL', 'wpshorturl'),
        'Short URL',
        'manage_options',
        'wp-short-url',
        'wsu_admin_menu_render_callback',
        WSURL_URL . 'assets/img/admin-menu.png',
        75
    );

    add_submenu_page(
        'wp-short-url',
        'Short URL',
        'Short URL',
        'manage_options',
        'wp-short-url'
    );
    add_submenu_page(
        'wp-short-url',
        'Categories',
        'Categories',
        'manage_options',
        'wp-short-url-categories',
        'wsu_admin_submenumenu_render_callback'
    );
}
add_action('admin_menu', 'wsu_register_admin_menu_page_callback');

function wsu_admin_menu_render_callback()
{
    $template = WSURL_PATH . 'admin/admin-list.php';
    require $template;
}

function wsu_admin_submenumenu_render_callback()
{
    $template = WSURL_PATH . 'admin/category-list.php';
    require $template;
}


/**
 * Enqueue Scripts for Admin
 */

function wsu_enqueue_admin_style_script()
{
    if (isset($_GET['page']) && ($_GET['page'] != 'wp-short-url' &&  $_GET['page'] != 'wp-short-url-categories')) :
        return;
    endif;
    $version = strtotime('now');

    // loading css
    wp_register_style('wsu-style', WSURL_URL . 'assets/css/wsu-admin.css', false, $version);
    wp_enqueue_style('wsu-style');

    //loading chart js
    wp_register_script('Chart-js', WSURL_URL . 'assets/js/Chart.min.js', '', '3.4.0', true);
    wp_enqueue_script('Chart-js');

    wp_enqueue_script('jquery-ui-autocomplete');


    wp_enqueue_script('wsu-clipboard', WSURL_URL . 'assets/js/clipboard.min.js', array('jquery-core'), $version, true);

    // loading js
    wp_register_script('wsu-script', WSURL_URL . 'assets/js/wsu-admin.js', array('jquery-core'), $version, true);
    wp_enqueue_script('wsu-script');




    wp_localize_script('wsu-script', 'wsu', ['ajaxurl' => admin_url('admin-ajax.php'),]);
}
add_action('admin_enqueue_scripts', 'wsu_enqueue_admin_style_script');

