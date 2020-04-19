<?php
/*
Plugin Name: Little Monkey - Simple image optimizer
Plugin URI: https://willcastro.com.br
description: Optimize your images, convert png and jpg to webp, or simply resize and compress all your images
Version: 1.0
Author: William Castro <contato@willcastro.com.br>
Author URI: https://willcastro.com.br
*/

define( 'WP_DEBUG', true );


// Verify if accessing directly
defined('ABSPATH') || die('Can not access files from browser.');


// Define constants
define('LITTLEMONKEY_NAME', 'LittleMonkey');
define('LITTLEMONKEY_VERSION', '1.0.0');
define('LITTLEMONKEY_SLUG', 'littlemonkey_optimizer');
define('LITTLEMONKEY_FILE', __FILE__);
define('LITTLEMONKEY_PATH', realpath(plugin_dir_path(LITTLEMONKEY_FILE)) . '/');
define('LITTLEMONKEY_URL', plugin_dir_url(LITTLEMONKEY_FILE));
define('LITTLEMONKEY_VIEWS', LITTLEMONKEY_PATH . 'resources/views/');
define('LITTLEMONKEY_ASSETS', LITTLEMONKEY_URL . 'resources/assets/');

require_once 'classes/LittleMonkey.php';

add_action('plugins_loaded', array(LittleMonkey::get_instance(), 'init'));
