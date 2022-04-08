<?php
namespace Barn2\Plugin\Easy_Post_Types_Fields;

class Util {

	public static function get_page_request() {
		return $_REQUEST; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	public static function get_manage_page_url( $args = [], $post_type = null, $section = '' ) {
		$default_args = [
			'page' => 'ept_post_types',
		];

		if ( $post_type ) {
			$default_args['post_type'] = $post_type->name;
		}

		if ( $section ) {
			$default_args['section'] = $section;
		}

		$args = wp_parse_args(
			$args,
			$default_args
		);

		return add_query_arg( $args, admin_url( 'admin.php' ) );
	}

	public static function get_post_type_by_name( $name ) {
		global $wp_post_types;

		if ( isset( $wp_post_types[ $name ] ) ) {
			return $wp_post_types[ $name ];
		}

		return $false;
	}

	public static function get_page_breadcrumbs() {
		$request     = self::get_page_request();
		$breadcrumbs = [
			[
				'href'  => self::get_manage_page_url(),
				'label' => __( 'Post types', 'easy-post-types-fields' ),
			]
		];

		if ( isset( $request['post_type'] ) ) {
			$post_type = self::get_post_type_by_name( $request['post_type'] );

			if ( ! $post_type ) {
				return '';
			}

			$href  = isset( $request['section'] ) ? self::get_manage_page_url( [], $post_type ) : '';
			$crumb = [
				'label' => $post_type->label,
			];

			if ( $href ) {
				$crumb['href'] = $href;
			}

			$breadcrumbs[] = $crumb;

			if ( isset( $request['section'] ) ) {
				$href  = isset( $request['action'] ) ? self::get_manage_page_url( [], $post_type, $request['section'] ) : '';
				$label = 'fields' === $request['section'] ? __( 'Custom fields', 'easy-post-types-fields' ) : __( 'Taxonomies', 'easy-post-types-fields' );
				$crumb = [
					'label' => $label,
				];

				if ( $href ) {
					$crumb['href'] = $href;
				}

				$breadcrumbs[] = $crumb;

				if ( isset( $request['action'] ) ) {
					$breadcrumbs[] = [
						'label' => 'add' === $request['action'] ? __( 'Add', 'easy-post-types-fields' ) : __( 'Edit', 'easy-post-types-fields' ),
					];
				}
			}
		}

		if ( 1 === count( $breadcrumbs ) ) {
			return '';
		}

		$breadcrumbs = array_map(
			function( $crumb ) {
				if ( isset( $crumb['href'] ) ) {
					return sprintf(
						'<a href="%s">%s</a>',
						esc_url( $crumb['href'] ),
						esc_html( $crumb['label'] )
					);
				} else {
					return esc_html( $crumb['label'] );
				}
			},
			$breadcrumbs
		);

		return implode( ' &gt; ', $breadcrumbs );
	}
}
