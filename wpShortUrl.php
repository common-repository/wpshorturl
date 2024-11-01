<?php

/**
* Plugin Name:     wp Short URL - Best Wordpress URL shortener
* Plugin URI:      https://wordpress.org/plugins/WpShortUrl/
* Description:     Create, shorten and track your customized branded links.
* Author:          wpShortURL.com
* Author URI:      https://wpshorturl.com/
* Text Domain:     wpshorturl
* Domain Path:     /languages
* Version:         1.1.1
 *
 * @package         WpShortUrl
 */


if (!defined('WSURL_PATH')) :
    define('WSURL_PATH', plugin_dir_path(__FILE__));
endif;

if (!defined('WSURL_URL')) :
    define('WSURL_URL', plugin_dir_url(__FILE__));
endif;

if (!defined('WSURL_PLUGIN_FILE')) :
    define('WSURL_PLUGIN_FILE', __FILE__);
endif;


/**
 * Only For Developer
 */
function wsurl_debug($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

/**
 * Including all dependencies
 */
require_once WSURL_PATH . "includes/index.php";
require_once WSURL_PATH . "admin/index.php";
require_once WSURL_PATH . "public/index.php";
