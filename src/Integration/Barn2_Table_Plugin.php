<?php
/**
 * Handle the integration with any Barn2 Table plugin
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Integration;

use Barn2\Plugin\Easy_Post_Types_Fields\Util,
	Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Registerable,
	Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Service;

/**
 * Class handling the integration
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Barn2_Table_Plugin implements Registerable, Service {

	/**
	 * A list of plugins that integrate with EPT
	 *
	 * @var array
	 */
	private $plugins;

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		/**
		 * Filter the list of shortcodes that use a Barn2 Table implementation
		 *
		 * The list is presented as an associative array where the key
		 * is the hook prefix used by the plugin and the value is its shortcode
		 *
		 * @param array $shortcodes The list of shortcodes
		 */
		$this->plugins = apply_filters(
			'ept_barn2_table_plugins',
			[
				'posts_table'   => [
					'prefix' => 'posts_table',
				],
				'doc_library'   => [
					'prefix'    => 'document_library_pro',
					'post_type' => 'dlp_document',
					'filter'    => [
						'hook' => 'document_library_pro_filled_args',
					],
				],
				'product_table' => [
					'prefix'    => 'wc_product_table',
					'post_type' => 'product',
				],
			]
		);

		foreach ( $this->plugins as $shortcode => $args ) {
			if ( isset( $args['filter'] ) ) {
				$filter   = $args['filter'];
				$priority = $filter['priority'] ?? 10;

				// for this filter callback, we use an anonymous function instead of a class method
				// so that we can inject the shortcode name of the current plugin
				add_filter(
					$filter['hook'],
					function( $atts ) use ( $shortcode ) {
						return apply_filters( "shortcode_atts_{$shortcode}", $atts, [], [], $shortcode );
					},
					$priority
				);
			}

			add_filter( "shortcode_atts_{$shortcode}", [ $this, 'shortcode_atts' ], 10, 4 );
			add_filter( "{$args['prefix']}_data_custom_field", [ $this, 'data_custom_field' ], 10, 3 );
		}
	}

	/**
	 * Filter the parameters of a Barn2 Table plugin shortcode
	 *
	 * This method goes through all the `tax:` and `cf:` slugs present in the
	 * `columns` and `filters` parameters and appropriately prefix all the
	 * custom fields and taxonomies that might be coming from EPT.
	 * This way users are free use the slugs they created without having to
	 * prefix them the way EPT does for internal reasons.
	 *
	 * @param  array  $out          The arguments of the shortcodes
	 * @param  array  $pairs        The default values for the shortcode arguments
	 * @param  array  $atts         The parameters originally passed to the shortcode
	 * @param  string $shortcode    The shortcode being processed
	 * @return array
	 */
	public function shortcode_atts( $out, $pairs, $atts, $shortcode ) {
		global $wp_post_types;

		$plugin_args = $this->plugins[ $shortcode ];
		if( ! isset( $out[ 'post_type' ] ) ) {
			$out[ 'post_type' ] = 'post';
		}

		if( ! isset( $out[ 'filters' ] ) ) {
			$out[ 'filters' ] = '';
		}

		$post_type   = isset( $plugin_args['post_type'] ) ? $plugin_args['post_type'] : $out['post_type'];

		if ( ! isset( $wp_post_types[ $post_type ] ) && isset( $wp_post_types[ "ept_{$post_type}" ] ) ) {
			$post_type = "ept_{$post_type}";
		}

		if ( isset( $wp_post_types[ $post_type ] ) ) {
			$out['post_type'] = $post_type;
			$post_type_object = Util::get_post_type_object( $post_type );

			if ( ! $post_type_object ) {
				return $out;
			}

			$out['columns'] = $this->prefix_taxs_and_fields( $out['columns'], $post_type );

			if ( is_null( filter_var( $out['filters'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) ) ) {
				$filters = $out['filters'];

				if ( 'custom' === $filters ) {
					$filters = isset( $out['filters_custom'] ) ? $out['filters_custom'] : '';
				}

				$out['filters'] = $this->prefix_taxs_and_fields( $filters, $post_type );
			}
		}

		return $out;
	}

	/**
	 * Use the appropriate prefix for the slugs of EPT entities
	 *
	 * This method gets a comma-separated list of slugs and checks whether the
	 * ones related to taxonomies (tax:) or custom fields (cf:) might be
	 * registered by EPT. If that is the case, the appropriate prefix is added.
	 * Otherwise, the slug is returned as it originally was.
	 *
	 * @param  string $comma_separated_list
	 * @param  string $post_type
	 * @return string The comma-separated list of adjusted slugs
	 */
	public function prefix_taxs_and_fields( $comma_separated_list, $post_type ) {
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

		return implode(
			',',
			array_map(
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
			)
		);
	}

	/**
	 * Filter the content of a custom field registered by EPT
	 *
	 * This method filters the data content of a custom field used in Posts
	 * Table Pro, returning the value appropriately formatted if the field
	 * was registered by EPT.
	 *
	 * @param  string $meta_value
	 * @param  string $meta_key
	 * @param  WP_Post $post
	 * @return string
	 */
	public function data_custom_field( $meta_value, $meta_key, $post ) {
		if ( 0 === strpos( $meta_key, $post->post_type ) ) {
			$post_type_object = Util::get_post_type_object( $post->post_type );

			if ( $post_type_object ) {
				$field_key        = str_replace( "{$post->post_type}_", '', $meta_key );
				$post_type_fields = (array) get_post_meta( $post_type_object->ID, '_ept_fields', true );
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

	/**
	 * Format the value of a custom field based on its type
	 *
	 * @param  string $value
	 * @param  array $field
	 * @return string
	 */
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
