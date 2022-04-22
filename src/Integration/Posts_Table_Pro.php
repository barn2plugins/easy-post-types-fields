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
			$fields = $fields ? array_column( $fields, 'slug' ) : [];

			$columns = explode( ',', $out['columns'] );
			$columns = array_map(
				function( $column ) use ( $ept_post_type, $fields ) {
					$prefix = strtok( $column, ':' );

					if ( 'tax' === $prefix ) {
						$column = 'tax:' . $ept_post_type . '_' . substr( $column, 4 );
					} elseif ( 'cf' === $prefix ) {
						$field = strtok( ':' );
						$label = strtok( ':' );

						if ( in_array( $field, $fields, true ) ) {
							$label  = $label ? $label : ucfirst( $field );
							$column = implode( ':', [ $prefix, "{$ept_post_type}_{$field}", $label ] );
							$column = rtrim( $column, ':' );
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
}