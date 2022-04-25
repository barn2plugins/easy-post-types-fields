<?php

namespace Barn2\Plugin\Easy_Post_Types_Fields\Integration;

use Barn2\Plugin\Easy_Post_Types_Fields\Util,
	Barn2\EPT_Lib\Registerable,
	Barn2\EPT_Lib\Service;

/**
 * CPT Factory registers all the CPT created with the plugin.
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Pro implements Registerable, Service {

	public function register() {
		add_filter( 'shortcode_atts_posts_table', [ $this, 'posts_table_shortcode_atts' ], 10, 4 );
		add_filter( 'posts_table_data_custom_field', [ $this, 'get_custom_field' ], 10, 3 );

	}

	public function posts_table_shortcode_atts( $out, $pairs, $atts, $shortcode ) {
		global $wp_post_types;

		$ept_post_type = "ept_{$out['post_type']}";

		if ( ! isset( $wp_post_types[ $out['post_type'] ] ) && isset( $wp_post_types[ $ept_post_type ] ) ) {
			$out['post_type'] = $ept_post_type;
			$post_type_object = Util::get_post_type_object( $ept_post_type );

			if ( ! $post_type_object ) {
				return $out;
			}

			$fields = get_post_meta( $post_type_object->ID, '_ept_fields', true );
			$slugs  = $fields ? array_column( $fields, 'slug' ) : [];

			$fields = array_combine(
				$slugs,
				$fields
			);

			$columns = explode( ',', $out['columns'] );
			$columns = array_map(
				function( $column ) use ( $ept_post_type, $fields, $slugs ) {
					$prefix = strtok( $column, ':' );

					if ( 'tax' === $prefix ) {
						$column = 'tax:' . $ept_post_type . '_' . substr( $column, 4 );
					} elseif ( 'cf' === $prefix ) {
						$slug = strtok( ':' );
						$label = strtok( ':' );

						if ( in_array( $slug, $slugs, true ) ) {
							$field  = $fields[ $slug ];
							$label  = $label ? $label : $field['name'];
							$column = rtrim( implode( ':', [ $prefix, "{$ept_post_type}_{$slug}", $label ] ), ':' );
						}
					}

					return $column;
				},
				$columns
			);

			$out['columns'] = $columns;

			if ( is_null( filter_var( $out['filters'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) ) ) {
				$filters = $out['filters'];

				if ( 'custom' === $filters ) {
					$filters = isset( $out['filters_custom'] ) ? $out['filters_custom'] : '';
				}

				$filters        = explode( ',', $filters );
				$filters        = array_map(
					function( $filter ) use ( $ept_post_type ) {
						if ( 0 === strpos( $filter, 'tax:' ) ) {
							$filter = 'tax:' . $ept_post_type . '_' . substr( $filter, 4 );
						}

						return $filter;
					},
					$filters
				);
				$out['filters'] = $filters;
			}
		}

		return $out;
	}

	public function get_custom_field( $meta_value, $meta_key, $post ) {
		if ( 0 === strpos( $meta_key, $post->post_type ) ) {
			$post_type_object = Util::get_post_type_object( $post->post_type );

			if ( $post_type_object ) {
				$field_key        = str_replace( "{$post->post_type}_", '', $meta_key );
				$post_type_fields = get_post_meta( $post_type_object->ID, '_ept_fields', true );
				$field            = array_filter(
					$post_type_fields,
					function( $f ) use ( $field_key ) {
						return $field_key === $f['slug'];
					}
				);

				if ( $field ) {
					$field      = reset( $field );
					$meta_value = $this->format_field( $meta_value, $field );
				}
			}
		}

		return $meta_value;
	}

	public function format_field( $value, $field ) {
		switch ( $field['type'] ) {
			case 'gallery':
			case 'image':
				$attachment_ids = array_filter( explode( ',', $value ) );

				if ( ! empty( $attachment_ids ) ) {
					if ( count( array_filter( $attachment_ids, 'is_numeric' ) ) === count( $attachment_ids ) ) {
						$value = 1 === count( $attachment_ids ) ? wp_get_attachment_image( $value ) : do_shortcode( sprintf( '[gallery include="%s" link="file"]', $value ) );
					} elseif ( $attachment_id = attachment_url_to_postid( $value ) ) { // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure
						$value = wp_get_attachment_image( $attachment_id );
					} else {
						$value = sprintf( '<img src="%s" />', $value );
					}
				}
				break;

			case 'text':
			case 'editor':
			default:
				break;
		}

		return $value;
	}

}