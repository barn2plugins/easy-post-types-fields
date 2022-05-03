<?php
namespace Barn2\Plugin\Easy_Post_Types_Fields\Post_Types;

/**
 * The class handling custom fields and taxonomies for a built-in or third party post type.
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Default_Post_Type extends Abstract_Post_Type {

	/**
	 * {@inheritDoc}
	 */
	protected function register() {
		add_action( "add_meta_boxes_{$this->post_type}", [ $this, 'register_cpt_metabox' ] );
		parent::register();
	}
}
