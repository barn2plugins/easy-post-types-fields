<?php
namespace Barn2\Plugin\Easy_Post_Types_Fields\Post_Types;

/**
 * The interface of a custom post type handler
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
interface Post_Type_Interface {

	/**
	 * Register the custom meta box associated with the post type
	 *
	 * @param  WP_Post $post The post currently being edited
	 * @return void
	 */
	public function register_cpt_metabox( $post = null );

	/**
	 * Output the HTML markup of the custom meta box
	 *
	 * @param  mixed $post
	 * @return void
	 */
	public function output_meta_box( $post );

	/**
	 * Store the post metadata
	 *
	 * @param  int|string $post_id
	 * @return void
	 */
	public function save_post_data( $post_id );
}
