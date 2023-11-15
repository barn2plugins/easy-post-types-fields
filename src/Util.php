<?php
namespace Barn2\Plugin\Easy_Post_Types_Fields;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Util as Lib_Util;

use WP_Query;

/**
 * Contain all the utility methods used by the plugins
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Util {

	/**
	 * Return the relevant query arguments of the current URL
	 *
	 * @return array
	 */
	public static function get_page_request() {
		$request = array_intersect_key(
			$_GET, //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			array_flip( [ 'page', 'post_type', 'section', 'slug', 'action', 'view' ] )
		);

		return self::sanitize( $request, 'sanitize_title' );
	}

	/**
	 * Get the referer URL from the submitted $_POST data or from $_SERVER['HTTP_REFERER']
	 *
	 * @param  string $nonce_action The action to verify the nonce
	 * @return string|bool The URL of the referer. False if none is found.
	 */
	public static function get_referer( $nonce_action ) {
		$referer = false;

		if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], $nonce_action ) ) {
			$referer = isset( $_POST['_first_referer'] ) ? filter_var( $_POST['_first_referer'], FILTER_VALIDATE_URL, FILTER_FLAG_QUERY_REQUIRED ) : false;
		}

		if ( ! $referer || 0 !== strpos( $referer, self::get_manage_page_url() ) ) {
			$referer = wp_get_referer();
		}

		$parsed_referer = wp_parse_url( $referer, PHP_URL_QUERY );

		if ( $parsed_referer ) {
			parse_str( $parsed_referer, $query_args );

			if ( isset( $query_args['page'] ) && 'ept_post_types' === $query_args['page'] ) {
				return $referer;
			}
		}

		return false;
	}

	/**
	 * Get the URL of the manage page with the appropriate query arguments
	 *
	 * @param  string|WP_Post_Type $post_type The slug of a post type
	 * @param  string $section The current section: either 'taxonomies' or 'fields'
	 * @param  string $slug The slug of the taxonomy or field being edited
	 * @param  string $action The action being performed: either 'add' or 'edit'
	 * @param  string $view The current view: either 'ept' or 'other'
	 * @return string
	 */
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

	/**
	 * Get the slug of a post type
	 *
	 * The post type can be passed as a string, in which case the function
	 * simply checks whether it corresponds to an actual post type and returns
	 * it. Alternatively, a WP_Post_Type object could be passed.
	 * Finally, a WP_Post could be passed, in which case the function checks
	 * whether the post is of `ept_post_type` type and returns its post_name
	 *
	 * @param  string|WP_Post_Type|WP_Post $post_type A post type
	 * @return string|boolean
	 */
	public static function get_post_type_name( $post_type ) {
		if ( is_a( $post_type, 'WP_Post' ) && 'ept_post_type' === $post_type->post_type ) {
			return $post_type->post_name;
		}

		if ( is_a( $post_type, 'WP_Post_Type' ) ) {
			return $post_type->name;
		}

		if ( is_string( $post_type ) && self::get_post_type_by_name( $post_type ) ) {
			return $post_type;
		}

		return false;
	}

	/**
	 * Get a WP_Post_Type object by its name
	 *
	 * @param  string $name
	 * @return WP_Post_Type|boolean
	 */
	public static function get_post_type_by_name( $name ) {
		global $wp_post_types;

		if ( isset( $wp_post_types[ $name ] ) ) {
			return $wp_post_types[ $name ];
		}

		return false;
	}

	/**
	 * Determine whether a post type is custom or if it is registered by
	 * WordPress or any other third-party plugin
	 *
	 * @param  string|WP_Post_Type|WP_Post $post_type The post type being checked
	 * @return boolean
	 */
	public static function is_custom_post_type( $post_type ) {
		$post_type = self::get_post_type_name( $post_type );

		return $post_type && 0 === strpos( $post_type, 'ept_' );
	}

	/**
	 * The WP_Post object associated with the post type
	 *
	 * @param  string|WP_Post_Type|WP_Post $post_type The post type being checked
	 * @return WP_Post|boolean
	 */
	public static function get_post_type_object( $post_type ) {
		$post_type = self::get_post_type_name( $post_type );
		$custom    = self::is_custom_post_type( $post_type );
		$args      = [
			'posts_per_page' => 1,
			'post_type'      => 'ept_post_type',
			'name'           => str_replace( 'ept_', '', $post_type ),
			'post_status'    => $custom ? 'publish' : 'private',
		];
		$query     = new WP_Query( $args );

		if ( $query->have_posts() ) {
			return $query->post;
		} else {
			return self::maybe_store_utility_post_type( $post_type );
		}

		return false;
	}

	/**
	 * Store the post type definition as a post of `ept_post_type` type
	 *
	 * EPT uses the post status only to differentiate custom post types defined
	 * by EPT from any other post types. A custom post type defined by EPT
	 * is stored in the database with the 'publish' post_status. Any other post
	 * types are stored with 'private' post_status.
	 *
	 * This function stores a new post for each post type that is registered by
	 * WordPress or any other third-party plugin. Storing also those post types
	 * as WP_Post objects is necessary to register custom fields and taxonomies
	 * to those post types.
	 *
	 * @param  string $post_type The slug of the post type
	 * @return WP_Post|boolean The WP_Post object or false if storing the post was not successful
	 */
	public static function maybe_store_utility_post_type( $post_type ) {
		$post_type = self::get_post_type_by_name( $post_type );

		if ( $post_type ) {
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

	/**
	 * Get a list of custom taxonomies registered to a post type
	 *
	 * @param  string|WP_Post_Type|WP_Post $post_type The post type
	 * @return array|boolean
	 */
	public static function get_custom_taxonomies( $post_type ) {
		$post_type_object = self::get_post_type_object( $post_type );

		if ( $post_type_object ) {
			return array_filter( (array) get_post_meta( $post_type_object->ID, '_ept_taxonomies', true ) );
		}

		return false;
	}

	/**
	 * Get a list of taxonomies registered by WordPress or any other
	 * third-party plugin
	 *
	 * @param  string|WP_Post_Type|WP_Post $post_type The post type
	 * @return array|boolean
	 */
	public static function get_builtin_taxonomies( $post_type ) {
		$post_type = self::get_post_type_name( $post_type );

		$custom_taxonomies = self::get_custom_taxonomies( $post_type );
		$internal_slugs    = array_column( $custom_taxonomies, 'slug' );
		$prefix            = "{$post_type}_";

		return array_map(
			function ( $t ) {
				return [
					'name'          => $t->labels->name,
					'singular_name' => $t->labels->singular_name,
					'slug'          => $t->name,
					'hierarchical'  => $t->hierarchical,
					'is_custom'     => false,
				];
			},
			array_filter(
				get_object_taxonomies( $post_type, 'objects' ),
				function( $t ) use ( $internal_slugs, $prefix ) {
					return $t->publicly_queryable && $t->show_ui && ! in_array( str_replace( $prefix, '', $t->name ), $internal_slugs, true );
				}
			)
		);
	}

	/**
	 * Get a list of custom fields registered to a post type
	 *
	 * @param  string|WP_Post_Type|WP_Post $post_type The post type
	 * @return array
	 */
	public static function get_custom_fields( $post_type ) {
		$post_type        = self::get_post_type_name( $post_type );
		$post_type_object = self::get_post_type_object( $post_type );
		$fields           = [];

		if ( $post_type_object ) {
			$fields = array_filter( (array) get_post_meta( $post_type_object->ID, '_ept_fields', true ) );
		}

		return $fields;
	}

	/**
	 * Get the HTML markup of the breadcrumbs of a page based on the query
	 * arguments of the current request
	 *
	 * @return string
	 */
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

	/**
	 * Get the HTML markup of a tooltip
	 *
	 * @param  string $tooltip_text The text contained in the tooltip
	 * @return string
	 */
	public static function get_tooltip( $tooltip_text ) {
		wp_enqueue_script( 'barn2-tiptip' );

		return '<span class="barn2-help-tip" data-tip="' . wp_kses_post( $tooltip_text ) . '"></span>';
	}

	/**
	 * Get the default list of features supported by a custom post type
	 *
	 * @return array
	 */
	public static function get_default_post_type_support() {
		return [ 'title', 'editor', 'excerpt', 'author', 'thumbnail' ];
	}

	/**
	 * Get the full list of features supported by a custom post type
	 *
	 * The list is returned as an associative array with the slug of the
	 * features being the keys and their labels being the values
	 *
	 * @return array
	 */
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

	/**
	 * Set a transient when a post type or one of its taxonomies are updated
	 *
	 * Every time a post type or its taxonomies are updated, the rewrite rules
	 * need to be flushed so that the permalinks reflect the change. Each post
	 * type object will check if a transient with its name is present after
	 * registering the post type or its taxonomies. If it finds the transient,
	 * it will flush the rewrite rules and delete the transient so that any
	 * subsequent initialization of the post type doesn't need to flush the
	 * rewrite rules, which is an expensive operation.
	 *
	 * @param  string $name The name of the post type being updated
	 * @return void
	 */
	public static function set_update_transient( $name ) {
		set_transient( "ept_{$name}_updated", true );
	}

	/**
	 * Return a list of custom field types
	 *
	 * @return array
	 */
	public static function get_custom_field_types() {
		return [
			'text'   => __( 'Text', 'easy-post-types-fields' ),
			'editor' => __( 'Visual Editor', 'easy-post-types-fields' ),
		];
	}

	/**
	 * Return the markup of the support, documentation and additional links
	 * in the header of the plugin page on the admin area
	 *
	 * @return void
	 */
	public static function support_links() {
		$links = [
			Lib_Util::format_link( ept()->get_documentation_url(), __( 'Documentation', 'easy-post-types-fields' ), true ),
			Lib_Util::format_link( ept()->get_support_url(), __( 'Support', 'easy-post-types-fields' ), true ),
		];

		printf( '<p>%s</p>', implode( ' | ', $links ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Sanitize a string using the sanitization function
	 *
	 * This method works on scalar strings or on array of strings recursively
	 *
	 * @param  string|array $var A string or an array of strings
	 * @param  string $sanitize_function The sanitization function. Defaults to 'sanitizie_title'
	 * @return string|array The sanitized string or array
	 */
	public static function sanitize( $var, $sanitize_function = 'sanitize_title' ) {
		foreach ( $var as &$value ) {
			if ( is_array( $value ) ) {
				$value = self::sanitize( $value, $sanitize_function );
			} else {
				$value = call_user_func( $sanitize_function, $value );
			}
		}

		return $var;
	}
}
