<?php
namespace Barn2\Plugin\Easy_Post_Types_Fields;

/**
 * The class handling custom fields and taxonomies for a built-in or third party post type.
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class CPT_Default extends CPT {

	public function __construct( $id ) {
		$this->id = $id;

		if ( 'ept_post_type' !== get_post_type( $this->id ) ) {
			$this->is_registered = false;
			return;
		}

		$post_type_object    = get_post( $this->id );
		$this->slug          = $post_type_object->post_name;
		$this->post_type     = $this->slug;
		$this->name          = $post_type_object->post_title;
		$this->singular_name = $post_type_object->post_title;

		$this->register_post_type();
	}

	public function register_post_type() {
		$this->register_taxonomies();

		Util::maybe_flush_rewrite_rules( $this->post_type );
		$this->register_meta();

		add_action( "add_meta_boxes_{$this->post_type}", [ $this, 'register_cpt_metabox' ] );
		add_action( "save_post_{$this->post_type}", [ $this, 'save_post_fields' ] );
		add_action( 'pre_post_update', [ $this, 'save_post_fields' ] );
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

	public function register_cpt_metabox( $post = null ) {
		$fields = get_post_meta( $this->id, '_ept_fields', true );

		if ( empty( $fields ) ) {
			return;
		}

		add_meta_box( "ept_post_type_{$this->slug}_metabox", __( 'Custom fields', 'easy-post-types-fields' ), [ $this, 'output_meta_box' ], $this->post_type );
	}

	public function output_meta_box( $post ) {
		do_action( "ept_post_type_{$this->slug}_metabox" );

		// get the fields registered with the post type
		$fields    = get_post_meta( $this->id, '_ept_fields', true );
		$post_type = $this->post_type;

		if ( empty( $fields ) ) {
			return;
		}

		include 'Admin/views/html-meta-box.php';
	}

	public function attachment_fields_to_edit( $form_fields, $post ) {
		$fields = get_post_meta( $this->id, '_ept_fields', true );

		if ( is_array( $fields ) ) {
			foreach ( $fields as $field ) {
				$field['value'] = get_post_meta( $post->ID, "ept_{$field['slug']}", true );
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
			$key = "ept_{$field['slug']}";

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

	public function save_post_fields( $post_id ) {
		$postdata = sanitize_post( $_POST, 'db' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( ! isset( $postdata['post_type'] ) ) {
			return;
		}

		$fields = get_post_meta( $this->id, '_ept_fields', true );

		if ( empty( $fields ) ) {
			return;
		}

		foreach ( $fields as $field ) {
			$meta_key = "{$this->post_type}_{$field['slug']}";
			if ( isset( $postdata[ $meta_key ] ) && '' !== $postdata[ $meta_key ] ) {
				update_post_meta( $post_id, $meta_key, $postdata[ $meta_key ] );
			}
		}
	}

}
