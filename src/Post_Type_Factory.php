<?php

namespace Barn2\Plugin\Easy_Post_Types_Fields;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Plugin\Simple_Plugin,
	Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Registerable,
	Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Service;

use WP_Query;

/**
 * CPT Factory registers all the CPT created with the plugin.
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Post_Type_Factory implements Registerable, Service {

	/**
	 * The main plugin instance
	 *
	 * @var Simple_Plugin
	 */
	private $plugin;

	private $post_types;

	/**
	 * Constructor
	 *
	 * @param  Simple_Plugin $plugin The main plugin object instance
	 * @return void
	 */
	public function __construct( Simple_Plugin $plugin ) {
		$this->plugin     = $plugin;
		$this->post_types = [];
	}

	/**
	 * {@inheritdoc}
	 */
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
			if ( 'publish' === $post_type->post_status ) {
				$this->post_types[] = new Post_Types\Custom_Post_Type( $post_type->ID );
			} else {
				if ( 'attachment' === $post_type->post_name ) {
					$this->post_types[] = new Post_Types\Attachment_Post_Type( $post_type->ID );
				} else {
					$this->post_types[] = new Post_Types\Default_Post_Type( $post_type->ID );
				}
			}
		}
	}

}
