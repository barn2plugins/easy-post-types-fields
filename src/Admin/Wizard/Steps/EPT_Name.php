<?php
/**
 * The class defining the Post Type Name step of the Setup Wizard
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin\Wizard\Steps;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Setup_Wizard\Step;

/**
 * {@inheritdoc}
 */
class EPT_Name extends Step {

	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		$this->set_id( 'ept_name' );
		$this->set_name( esc_html__( 'Name', 'easy-post-types-fields' ) );
		$this->set_description( __( 'First, let\'s choose the name for your post type. This will appear as a link on the left hand side of the WordPress admin.', 'easy-post-types-fields' ) );
		$this->set_title( esc_html__( 'What sort of content do you want to create?', 'easy-post-types-fields' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {
		return [
			'singular' => [
				'type'        => 'text',
				'label'       => __( 'Singular name', 'easy-post-types-fields' ),
				'placeholder' => __( 'e.g. Article', 'easy-post-types-fields' ),
			],
			'plural'   => [
				'type'        => 'text',
				'label'       => __( 'Plural name', 'easy-post-types-fields' ),
				'placeholder' => __( 'e.g. Articles', 'easy-post-types-fields' ),
			],
			'slug'     => [
				'type' => 'hidden',
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function submit() {
		$values = $this->get_submitted_values();

		if ( ! $values['singular'] || ! $values['plural'] ) {
			$this->send_error( esc_html__( 'Both fields must not be empty.', 'easy-post-types-fields' ) );
		}

		$slug = sanitize_title( $values['singular'] );

		if ( strlen( $slug ) > 17 ) {
			$this->send_error( esc_html__( 'The name of the post type cannot be longer than 17 characters.', 'easy-post-types-fields' ) );
		}

		$posts = get_posts(
			[
				'post_type' => 'ept_post_type',
				'name'      => $slug,
			]
		);

		if ( count( $posts ) ) {
			$this->send_error( esc_html__( 'A post type with this name is already present. Please input a different name.', 'easy-post-types-fields' ) );
		}

		wp_send_json_success( [ 'slug' => $slug ] );
	}

}
