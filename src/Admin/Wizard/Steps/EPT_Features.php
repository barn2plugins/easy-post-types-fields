<?php
/**
 * The class defining the Post Type Features step of the Setup Wizard
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin\Wizard\Steps;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Setup_Wizard\Step;
use Barn2\Plugin\Easy_Post_Types_Fields\Util;

/**
 * {@inheritdoc}
 */
class EPT_Features extends Step {

	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		$this->set_id( 'ept_features' );
		$this->set_name( esc_html__( 'Features', 'easy-post-types-fields' ) );
		// translators: the plural name of a post type
		$this->set_description( __( 'Choose which of the standard features you will use. Later, you can create custom fields and taxonomies for storing additional information.', 'easy-post-types-fields' ) );
		// translators: the plural name of a post type
		$this->set_title( esc_html__( 'What type of information do you need for your %s?', 'easy-post-types-fields' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {
		$fields           = [];
		$enabled_features = Util::get_default_post_type_support();

		foreach ( Util::get_post_type_support() as $feature => $feature_label ) {
			$fields[ $feature ] = [
				'type'        => 'checkbox',
				'label'       => $feature_label,
				'value'       => in_array( $feature, $enabled_features, true ),
				'description' => false,
				'border'      => false,
			];
		}

		$fields['title']['disabled'] = true;

		return $fields;
	}

	/**
	 * {@inheritdoc}
	 */
	public function submit() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'barn2_setup_wizard_nonce' ) ) {
			return;
		}

		$values       = $this->get_submitted_values();
		$supports     = array_keys(
			array_filter(
				$values,
				function( $v ) {
					return filter_var( $v, FILTER_VALIDATE_BOOLEAN );
				}
			)
		);
		$slug         = sanitize_title( $_POST['slug'] );
		$singular     = sanitize_text_field( $_POST['singular'] );
		$plural       = sanitize_text_field( $_POST['plural'] );
		$post_type_id = wp_insert_post(
			[
				'post_type'      => 'ept_post_type',
				'post_title'     => $singular,
				'post_status'    => 'publish',
				'comment_status' => 'closed',
				'meta_input'     => [
					'_ept_plural_name' => $plural,
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

		Util::set_update_transient( "ept_{$slug}" );
		wp_send_json_success();
	}

}
