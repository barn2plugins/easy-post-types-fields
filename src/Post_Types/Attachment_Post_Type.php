<?php
namespace Barn2\Plugin\Easy_Post_Types_Fields\Post_Types;

/**
 * The class handling custom fields and taxonomies for the Media post type (attachment).
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Attachment_Post_Type extends Default_Post_Type {

	/**
	 * {@inheritDoc}
	 */
	protected function register() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			require_once ABSPATH . '/wp-admin/includes/screen.php';
		}

		$screen = get_current_screen();

		if ( $screen && 'post' === $screen->base ) {
			// the regular post editor
			add_action( "add_meta_boxes_{$this->post_type}", [ $this, 'register_cpt_metabox' ] );
			parent::register();
		}
	}

}
