<?php

namespace Barn2\Plugin\Easy_Post_Types_Fields;

use Barn2\EPT_Lib\Plugin\Simple_Plugin,
	Barn2\EPT_Lib\Registerable,
	Barn2\EPT_Lib\Service;

use WP_Query;

/**
 * CPT Factory registers all the CPT created with the plugin.
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class CPT_Factory implements Registerable, Service {

	/**
	 * The main plugin instance
	 *
	 * @var Simple_Plugin
	 */
	private $plugin;

	private $post_types;

	public function __construct( Simple_Plugin $plugin ) {
		$this->plugin     = $plugin;
		$this->post_types = [];
	}

	public function register() {
		$ept_post_types = new WP_Query(
			[
				'post_type'      => 'ept_post_type',
				'posts_per_page' => -1,
				'orderby'        => 'post_title',
				'post_status'    => [ 'publish', 'private' ],
				'order'          => 'ASC',
			]
		);

		foreach ( $ept_post_types->posts as $post_type ) {
			$this->post_types[] = new CPT( $post_type->ID );
		}
	}

}
