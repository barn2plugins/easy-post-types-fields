<?php
namespace Barn2\Plugin\Easy_Post_Types_Fields\Post_Types;

/**
 * The class registering a new Custom Post Type.
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
interface Post_Type_Interface {

	public function register_cpt_metabox( $post = null );

	public function output_meta_box( $post );

	public function save_post_data( $post_id );
}
