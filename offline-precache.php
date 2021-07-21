<?php
/**
 * @package OfflinePrecache
 * @ref Akismet way of code
 */
/*
Plugin Name: Offline Pre-Cache
Plugin URI: https://mageserv-ltd.com
Description: If browsing your website online or offline is what you need, so you can use this extension to precache your website to visitors devices so they can browse your website offline.
Version: 1.0.1
Author: Tiefanovic
Author URI: https://mageserv-ltd.com/
License: GPLv2 or later
Text Domain: offline-precache
 */
/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.

Copyright 2021 Mageserv, Inc.
*/
// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}
define( 'OFFLINE_PRECACHE_VERSION', '1.0.0' );
define( 'OFFLINE_PRECACHE__MINIMUM_WP_VERSION', '4.0' );
define( 'OFFLINE_PRECACHE__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
require_once( OFFLINE_PRECACHE__PLUGIN_DIR . 'class.offlineprecache.php' );
register_activation_hook( __FILE__, array( 'OfflinePrecache', 'plugin_activation' ) );

add_action( 'init', array( 'OfflinePrecache', 'init' ) );

if ( is_admin() ) {
	require_once( OFFLINE_PRECACHE__PLUGIN_DIR . 'class.offlineprecacheadmin.php' );
	add_action( 'init', array( 'OfflinePrecacheAdmin', 'init' ) );
}
