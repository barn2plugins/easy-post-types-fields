<?php
/**
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin\Wizard\Steps;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Barn2\Setup_Wizard\Step;

class EPT_Features extends Step {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->set_id( 'ept_features' );
		$this->set_name( esc_html__( 'Features', 'easy-post-types-fields' ) );
		// translators: the plural name of a post type
		$this->set_description( __( 'Choose which of the standard features you will use to store data about your %s. Later, you can also create custom fields and taxonomies for storing additional information.', 'easy-post-types-fields' ) );
		$this->set_title( esc_html__( 'What type of information do you need for your %s?', 'easy-post-types-fields' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {
		return [
			'title'           => [
				'type'     => 'checkbox',
				'label'    => __( 'Title', 'easy-post-types-fields' ),
				'value'    => true,
				'disabled' => true,
				// 'premium'  => true,
			],
			'editor'          => [
				'type'  => 'checkbox',
				'label' => __( 'Content', 'easy-post-types-fields' ),
				'value' => true,
			],
			'excerpt'         => [
				'type'  => 'checkbox',
				'label' => __( 'Excerpt', 'easy-post-types-fields' ),
				'value' => true,
			],
			'author'          => [
				'type'  => 'checkbox',
				'label' => __( 'Author', 'easy-post-types-fields' ),
				'value' => true,
			],
			'thumbnail'       => [
				'type'  => 'checkbox',
				'label' => __( 'Featured image', 'easy-post-types-fields' ),
				'value' => true,
			],
			// 'custom-fields'   => [
			// 	'type'  => 'checkbox',
			// 	'label' => __( 'Custom fields', 'easy-post-types-fields' ),
			// ],
			'comments'        => [
				'type'  => 'checkbox',
				'label' => __( 'Comments', 'easy-post-types-fields' ),
			],
			// 'post-formats'    => [
			// 	'type'  => 'checkbox',
			// 	'label' => __( 'Post formats', 'easy-post-types-fields' ),
			// ],
			'page-attributes' => [
				'type'  => 'checkbox',
				'label' => __( 'Page attributes', 'easy-post-types-fields' ),
			],
			'revisions'       => [
				'type'  => 'checkbox',
				'label' => __( 'Revisions', 'easy-post-types-fields' ),
			],
			// 'trackbacks'      => [
			// 	'type'  => 'checkbox',
			// 	'label' => __( 'Trackbacks', 'easy-post-types-fields' ),
			// ],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function submit() {
		$values       = $this->get_submitted_values();
		$supports     = array_keys( array_filter( $values ) );
		$post_type_id = wp_insert_post(
			[
				'post_type'      => 'ept_post_type',
				'post_title'     => filter_var( $_POST['singular'], FILTER_DEFAULT ),
				'post_status'    => 'publish',
				'comment_status' => 'closed',
				'meta_input'     => [
					'_ept_plural_name' => filter_var( $_POST['plural'], FILTER_DEFAULT ),
					'_ept_supports'    => $supports,
				],
			]
		);

		if ( is_wp_error( $post_type_id ) ) {
			$this->send_error( esc_html( $post_type_id->get_error_message() ) );
		}

		if ( 0 === $post_type_id ) {
			$this->send_error( esc_html__( 'It is not possible to create the post type with the current configuration.', 'easy-post-types-fields' ) );
		}

		flush_rewrite_rules();
		wp_send_json_success();
	}

}
