<?php
/**
 * The main plugin file for Easy Post Types and Fields.
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 *
 * @wordpress-plugin
 * Plugin Name:     Easy Post Types and Fields
 * Plugin URI:      https://wordpress.org/plugins/easy-post-types-fields/
 * Description:     Create custom post types, fields and taxonomies.
 * Version:         1.1.8
 * Author:          Barn2 Plugins
 * Author URI:      https://barn2.com
 * Text Domain:     easy-post-types-fields
 * Domain Path:     /languages
 *
 * Copyright:       Barn2 Media Ltd
 * License:         GNU General Public License v3.0
 * License URI:     https://www.gnu.org/licenses/gpl.html
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields;

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const PLUGIN_VERSION = '1.1.8';
const PLUGIN_FILE    = __FILE__;

// Autoloader.
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Helper function to access the shared plugin instance.
 *
 * @return Plugin
 */
function ept() {
	return Plugin_Factory::create( PLUGIN_FILE, PLUGIN_VERSION );
}

/**
 * Return the absolute path of the plugin
 *
 * @param  string $path A subpath the plugin dir
 * @return string
 */
function get_dir_path( $path = '' ) {
	return wp_normalize_path( ept()->get_dir_path() . "/$path" );
}

// Load the plugin.
ept()->register();
