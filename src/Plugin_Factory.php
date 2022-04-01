<?php

namespace Barn2\Plugin\Easy_Post_Types_Fields;

/**
 * Factory to create/return the shared plugin instance.
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Plugin_Factory {

	private static $plugin = null;

	/**
	 * Create/return the shared plugin instance.
	 *
	 * @param string $file
	 * @param string $version
	 * @return Barn2\Plugin\Easy_Post_Types_Fields\Plugin
	 */
	public static function create( $file, $version ) {
		if ( null === self::$plugin ) {
			self::$plugin = new Plugin( $file, $version );
		}
		return self::$plugin;
	}

}
