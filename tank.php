<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Tank requires PHP 5.4 or newer
if ( version_compare( PHP_VERSION, '5.4.0', '<' ) ) {
	exit( 'Tank for WordPress requires PHP version 5.4 or higher.' );
}

// Load Composer autoload if it exists.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require 'vendor/autoload.php';
}

// Register the WordPress autoload.
// It will load files that has `class-` or `trait-` as prefix.
register_wp_autoload( 'Tank\\', __DIR__ . '/src' );
