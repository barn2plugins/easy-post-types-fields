<?php
namespace Barn2\Plugin\Easy_Post_Types_Fields;

use WP_Query;

class Util {

	public static function get_page_request() {
		$request = array_intersect_key(
			$_GET, //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			array_flip( [ 'page', 'post_type', 'section', 'slug', 'action', 'view' ] )
		);

		return $request;
	}

	public static function get_manage_page_url( $post_type = '', $section = '', $slug = '', $action = '', $view = '' ) {
		if ( is_a( $post_type, 'WP_Post_Type' ) ) {
			$post_type = $post_type->name;
		}

		$args = array_filter(
			[
				'page'      => 'ept_post_types',
				'post_type' => $post_type,
				'section'   => $section,
				'slug'      => $slug,
				'action'    => $action,
				'view'      => $view,
			]
		);

		$request = self::get_page_request();

		if ( isset( $request['view'] ) && false !== $view ) {
			$args['view'] = $request['view'];
		}

		return add_query_arg( $args, admin_url( 'admin.php' ) );
	}

	public static function is_custom_post_type( $post_type ) {
		if ( is_a( $post_type, 'WP_Post_Type' ) ) {
			$post_type = $post_type->name;
		}

		return 0 === strpos( $post_type, 'ept_' );
	}

	public static function get_post_type_by_name( $name ) {
		global $wp_post_types;

		if ( isset( $wp_post_types[ $name ] ) ) {
			return $wp_post_types[ $name ];
		}

		return false;
	}

	public static function get_post_type_object( $post_type ) {
		$post_type_name = $post_type;

		if ( is_a( $post_type, 'WP_Post_Type' ) ) {
			$post_type_name = $post_type->name;
		}

		$custom = self::is_custom_post_type( $post_type_name );
		$args   = [
			'posts_per_page' => 1,
			'post_type'      => 'ept_post_type',
			'name'           => str_replace( 'ept_', '', $post_type_name ),
			'post_status'    => $custom ? 'publish' : 'private',
		];
		$query  = new WP_Query( $args );

		if ( $query->have_posts() ) {
			return $query->post;
		} else {
			return self::maybe_store_utility_post_type( $post_type );
		}

		return false;
	}

	public static function maybe_store_utility_post_type( $post_type ) {
		if ( ! self::is_custom_post_type( $post_type ) ) {
			$post_type_id = wp_insert_post(
				[
					'post_type'      => 'ept_post_type',
					'post_title'     => $post_type->labels->singular_name,
					'post_name'      => $post_type->name,
					'post_status'    => 'private',
					'comment_status' => 'closed',
				]
			);

			if ( $post_type_id ) {
				return get_post( $post_type_id );
			}
		}

		return false;
	}

	public static function get_post_type_custom_fields( $post_type ) {
		$post_type_object = self::get_post_type_object( $post_type );
		$fields           = [];

		if ( $post_type_object ) {
			$fields = get_post_meta( $post_type_object->ID, '_ept_fields', true );
		}

		return $fields;
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

			$href  = isset( $request['section'] ) && self::is_custom_post_type( $request['post_type'] ) ? self::get_manage_page_url( $post_type ) : '';
			$crumb = [
				'label' => $post_type->label,
			];

			if ( $href ) {
				$crumb['href'] = $href;
			}

			$breadcrumbs[] = $crumb;

			if ( isset( $request['section'] ) ) {
				$href  = isset( $request['action'] ) ? self::get_manage_page_url( $post_type, $request['section'] ) : '';
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

	public static function get_tooltip( $tooltip_text ) {
		wp_enqueue_script( 'barn2-tiptip' );

		return '<span class="barn2-help-tip" data-tip="' . wp_kses_post( $tooltip_text ) . '"></span>';
	}

	public static function get_default_post_type_support() {
		return [ 'title', 'editor', 'excerpt', 'author', 'thumbnail' ];
	}

	public static function get_post_type_support() {
		return [
			'title'           => __( 'Title', 'easy-post-types-fields' ),
			'editor'          => __( 'Content', 'easy-post-types-fields' ),
			'excerpt'         => __( 'Excerpt', 'easy-post-types-fields' ),
			'author'          => __( 'Author', 'easy-post-types-fields' ),
			'thumbnail'       => __( 'Featured image', 'easy-post-types-fields' ),
			'comments'        => __( 'Comments', 'easy-post-types-fields' ),
			'page-attributes' => __( 'Page attributes', 'easy-post-types-fields' ),
			'revisions'       => __( 'Revisions', 'easy-post-types-fields' ),
		];
	}

	public static function set_update_transient( $name, $entity = 'post_type' ) {
		set_transient( "ept_{$entity}_{$name}_updated", true );
	}

	public static function maybe_flush_rewrite_rules( $name, $entity = 'post_type' ) {
		if ( get_transient( "ept_{$entity}_{$name}_updated" ) ) {
			flush_rewrite_rules();
			delete_transient( "ept_{$entity}_{$name}_updated" );
		}
	}

	public static function get_custom_field_types() {
		return [
			'text'   => __( 'Text', 'easy-post-types-fields' ),
			'editor' => __( 'Visual Editor', 'easy-post-types-fields' ),
			// 'image'  => __( 'Image', 'easy-post-types-fields' ),
		];
	}
}
