<?php
/**
Plugin Name: JWP Data Handler
Description: Плагин реализует расширяемую систему для последовательного перебора данных с дальнейшей обработкой. 
Author: Eugene Jokerov
Version: 1.2
Author URI: http://wordpressor.org/
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'JWP_DH_MENU_SLUG', 'jwp-data-handler' );
define( 'JWP_DH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'JWP_DH_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// подключение необходимых классов
if ( ! class_exists( 'JWP_DH' ) ) {
	include_once JWP_DH_PLUGIN_DIR . '/includes/class-jwp-dh.php';
	include_once JWP_DH_PLUGIN_DIR . '/includes/class-jwp-dh-response.php';
	include_once JWP_DH_PLUGIN_DIR . '/includes/class-jwp-data-handler.php';
}

// инициализация плагина
JWP_DH::instance();

