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
class Attachment_Post_Type extends Abstract_Post_Type {

	public function register() {
		add_filter( 'attachment_fields_to_edit', [ $this, 'attachment_fields_to_edit' ], 10, 2 );
		add_filter( 'attachment_fields_to_save', [ $this, 'attachment_fields_to_save' ], 10, 2 );
	}

	public function attachment_fields_to_edit( $form_fields, $post ) {
		$screen = get_current_screen();

		if ( is_null( $screen ) ) {
			foreach ( $this->fields as $field ) {
				unset( $form_fields[ $field ] );
			}

			foreach ( $this->taxonomies as $taxonomy ) {
				unset( $form_fields[ $taxonomy ] );
			}

			return $form_fields;
		}

		ob_start();
		?>

		<div id="ept_post_type_post_metabox" class="postbox">
			<style>table.compat-attachment-fields{width: 100%;}tr.compat-field-ept_fields>th{display:none;}</style>
			<div class="postbox-header">
				<h2 class="hndle ui-sortable-handle">Custom Fields</h2>
			</div>
			<div class="inside">

				<?php
				$this->output_meta_box( $post );
				?>
			</div>
		</div>

		<?php
		$meta_box = ob_get_clean();

		if ( $meta_box ) {
			$form_fields['ept_fields'] = [
				'label' => '',
				'input' => 'html',
				'html'  => $meta_box,
			];
		}

		return $form_fields;
	}

	public function attachment_fields_to_save( $post, $attachment ) {
		//phpcs:disable WordPress.Security.NonceVerification.Recommended

		$fields = get_post_meta( $this->id, '_ept_fields', true );

		foreach ( $fields as $field ) {
			$key = "{$this->post_type}_{$field['slug']}";

			if ( isset( $_REQUEST[ $key ] ) ) {
				update_post_meta( $post['ID'], $key, $_REQUEST[ $key ] );
			} else {
				delete_post_meta( $post['ID'], $key );
			}
		}

		if ( ! isset( $_REQUEST['tax_input'] ) ) {
			return $post;
		}

		foreach ( $_REQUEST['tax_input'] as $tax_name => $terms ) {
			if ( is_array( $terms ) ) {
				$terms = array_values( array_filter( array_map( 'intval', $terms ) ) );
				$ids   = wp_set_object_terms( $post['ID'], $terms, $tax_name, false );

				$taxonomy = get_taxonomy( $tax_name );
				_update_generic_term_count( $terms, $taxonomy );
			}
		}

		return $post;
	}
}
