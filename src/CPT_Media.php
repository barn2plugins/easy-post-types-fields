<?php
namespace Barn2\Plugin\Easy_Post_Types_Fields;

/**
 * The class handling custom fields and taxonomies for the Media post type (attachment).
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class CPT_Media extends CPT_Default {

	public function register_post_type() {
		$this->register_taxonomies();

		Util::maybe_flush_rewrite_rules( $this->post_type );
		$this->register_meta();

		add_filter( 'attachment_fields_to_edit', [ $this, 'attachment_fields_to_edit' ], 10, 2 );
		add_filter( 'attachment_fields_to_save', [ $this, 'attachment_fields_to_save' ], 10, 2 );
	}

	public function register_taxonomies() {
		$taxonomies = get_post_meta( $this->id, '_ept_taxonomies', true );
		$post_type  = $this->post_type;

		if ( is_array( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$args = [
					'labels'       => [
						'name'          => $taxonomy['name'],
						'singular_name' => $taxonomy['singular_name'],
					],
					'hierarchical' => isset( $taxonomy['hierarchical'] ) ? $taxonomy['hierarchical'] : true,
				];

				new Taxonomy( $taxonomy['slug'], $post_type, $args );
			}

			return array_map(
				function( $t ) use ( $post_type ) {
					return "{$post_type}_{$t['slug']}";
				},
				$taxonomies
			);
		}

		return [];
	}

	public function register_meta() {
		$fields    = get_post_meta( $this->id, '_ept_fields', true );
		$post_type = $this->post_type;

		if ( is_array( $fields ) ) {
			foreach ( $fields as $field ) {
				new Field( $field, $this->post_type );
			}

			return array_map(
				function( $f ) use ( $post_type ) {
					return "{$post_type}_{$f['slug']}";
				},
				$fields
			);
		}

		return [];
	}

	public function attachment_fields_to_edit( $form_fields, $post ) {
		$fields = get_post_meta( $this->id, '_ept_fields', true );

		if ( is_array( $fields ) ) {
			foreach ( $fields as $field ) {
				$field['value'] = get_post_meta( $post->ID, "{$this->post_type}_{$field['slug']}", true );
				$field['label'] = $field['name'];
				unset( $field['name'] );
				$form_fields[ $field['slug'] ] = $field;
			}
		}

		return $form_fields;
	}

	public function attachment_fields_to_save( $post, $attachment ) {
		$fields = get_post_meta( $this->id, '_ept_fields', true );

		foreach ( $fields as $field ) {
			$key = "{$this->post_type}_{$field['slug']}";

			if ( isset( $attachment[ $field['slug'] ] ) ) {
				update_post_meta( $post['ID'], $key, $attachment[ $field['slug'] ] );
			} else {
				delete_post_meta( $post['ID'], $key );
			}
		}

		if ( ! isset( $_REQUEST['tax_input'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return $post;
		}

		foreach ( $_REQUEST['tax_input'] as $tax_name => $tax ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$term_ids = array_map(
				'intval',
				array_keys( $tax, '1', true )
			);
			wp_set_object_terms( $post['ID'], $term_ids, $tax_name, false );
			_update_generic_term_count( $term_ids, $tax_name );
		}

		return $post;
	}
}
