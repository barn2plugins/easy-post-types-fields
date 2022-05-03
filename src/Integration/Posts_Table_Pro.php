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

		$post_type = $out['post_type'];

		if ( ! isset( $wp_post_types[ $post_type ] ) && isset( $wp_post_types[ "ept_{$post_type}" ] ) ) {
			$post_type = "ept_{$post_type}";
		}

		if ( isset( $wp_post_types[ $post_type ] ) ) {
			$out['post_type'] = $post_type;
			$post_type_object = Util::get_post_type_object( $post_type );

			if ( ! $post_type_object ) {
				return $out;
			}

			$out['columns'] = $this->translate_tax_and_fields( $out['columns'], $post_type );

			if ( is_null( filter_var( $out['filters'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) ) ) {
				$filters = $out['filters'];

				if ( 'custom' === $filters ) {
					$filters = isset( $out['filters_custom'] ) ? $out['filters_custom'] : '';
				}

				$out['filters'] = $this->translate_tax_and_fields( $filters, $post_type );
			}
		}

		return $out;
	}

	public function translate_tax_and_fields( $comma_separated_list, $post_type ) {
		$taxonomies = Util::get_custom_taxonomies( $post_type );
		$fields     = Util::get_custom_fields( $post_type );
		$slugs      = $fields ? array_column( $fields, 'slug' ) : [];
		$fields     = array_combine(
			$slugs,
			$fields
		);
		$entities   = [
			'tax' => $taxonomies,
			'cf'  => $fields,
		];

		return array_map(
			function( $column ) use ( $post_type, $entities, $slugs ) {
				$prefix = strtok( $column, ':' );
				$slug   = str_replace( "{$post_type}_", '', strtok( ':' ) );
				$label  = strtok( ':' );

				if ( in_array( $prefix, [ 'tax', 'cf' ], true ) ) {
					$item = array_values(
						array_filter(
							$entities[ $prefix ],
							function( $i ) use ( $slug ) {
								return $slug === $i['slug'];
							}
						)
					);

					if ( $item && 1 === count( $item ) ) {
						$label  = $label ? $label : $item[0]['name'];
						$column = rtrim( implode( ':', [ $prefix, "{$post_type}_{$slug}", $label ] ), ':' );
					}
				}

				return $column;
			},
			explode( ',', $comma_separated_list )
		);
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