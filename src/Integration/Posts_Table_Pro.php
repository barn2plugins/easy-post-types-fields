<?php

namespace Barn2\Plugin\Easy_Post_Types_Fields\Integration;

use Barn2\EPT_Lib\Plugin\Simple_Plugin,
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

			$columns = explode( ',', $out['columns'] );
			$columns = array_map(
				function( $column ) use ( $ept_post_type ) {
					if ( 0 === strpos( $column, 'tax:' ) ) {
						$column = 'tax:' . $ept_post_type . '_' . substr( $column, 4 );
					}

					return $column;
				},
				$columns
			);

			$out['columns'] = $columns;
		}

		return $out;
	}
}