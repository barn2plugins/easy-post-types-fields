<?php
namespace Barn2\Plugin\Easy_Post_Types_Fields;

/**
 * The class registering a new Custom Post Type.
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class CPT {

	/**
	 * The ID of the EPT post containing the CPT definition
	 *
	 * @var int
	 */
	private $id;

	/**
	 * The name (generally plural) of the CPT as defined in $args['labels']['name']
	 *
	 * @var string
	 */
	private $name;

	/**
	 * The singular name of the CPT as defined in $args['labels']['singular_name']
	 *
	 * @var string
	 */
	private $singular_name;

	/**
	 * The post type of the CPT
	 *
	 * @var string
	 */
	private $post_type;

	/**
	 * The arguments for the post type registration
	 *
	 * @var array
	 */
	private $args = [];

	public function __construct( $id ) {
		$this->id = $id;

		if ( 'ept_post_type' !== get_post_type( $this->id ) ) {
			$this->is_registered = false;
			return;
		}

		$post_type_object    = get_post( $this->id );
		$this->post_type     = "ept_$post_type_object->post_name";
		$this->name          = get_post_meta( $this->id, '_ept_plural_name', true );
		$this->singular_name = $post_type_object->post_title;

		if ( $this->prepare_arguments() ) {
			$this->register_post_type();
		}
	}

	public function prepare_arguments() {
		if ( empty( $this->args ) ) {
			$args         = [];
			$default_args = [
				'public'               => true,
				'exclude_from_search'  => false,
				'publicly_queryable'   => true,
				'show_in_menu'         => true,
				'show_in_nav_menus'    => true,
				'show_in_admin_bar'    => false,
				'show_in_rest'         => true,
				'menu_position'        => 26,
				'menu_icon'            => 'dashicons-list-view',
				'supports'             => false,
				'register_meta_box_cb' => [ $this, 'register_cpt_metabox' ],
				'query_var'            => false,
				'can_export'           => false,
				'delete_with_user'     => false,
			];

			$args['labels'] = apply_filters(
				"ept_post_type_{$this->singular_name}_labels",
				$this->default_labels()
			);

			$supports           = get_post_meta( $this->id, '_ept_supports', true );
			$args['supports']   = $supports ?: [ 'title', 'editor' ];
			$args['rewrite']    = [
				'slug'       => '/' . sanitize_title( $this->name ),
				'with_front' => false,
			];
			$taxonomies         = $this->register_taxonomies();
			$args['taxonomies'] = $taxonomies ?: [];

			$this->args = apply_filters(
				"ept_post_type_{$this->singular_name}_args",
				wp_parse_args(
					$args,
					$default_args
				)
			);
		}

		return $this->args;
	}

	public function default_labels() {
		$default_labels = [
			'name'                     => $this->name,
			'singular_name'            => $this->singular_name,
			// translators: the singular post type name
			'add_new_item'             => $this->define_singular_label( __( 'Add New %s', 'easy-post-types-fields' ) ),
			// translators: the singular post type name
			'edit_item'                => $this->define_singular_label( __( 'Edit %s', 'easy-post-types-fields' ) ),
			// translators: the singular post type name
			'new_item'                 => $this->define_singular_label( __( 'New %s', 'easy-post-types-fields' ) ),
			// translators: the singular post type name
			'view_item'                => $this->define_singular_label( _x( 'View %s', 'singular', 'easy-post-types-fields' ) ),
			// translators: the plural post type name
			'view_items'               => $this->define_label( _x( 'View %s', 'plural', 'easy-post-types-fields' ) ),
			// translators: the plural post type name
			'search_items'             => $this->define_label( __( 'Search %s', 'easy-post-types-fields' ) ),
			// translators: the plural post type name
			'not_found'                => $this->define_label( __( 'No %s found.', 'easy-post-types-fields' ), true ),
			// translators: the plural post type name
			'not_found_in_trash'       => $this->define_label( __( 'No %s found in Trash.', 'easy-post-types-fields' ), true ),
			// translators: the singular post type name
			'parent_item_colon'        => $this->define_singular_label( __( 'Parent %s:', 'easy-post-types-fields' ) ),
			// translators: the plural post type name
			'all_items'                => $this->define_label( __( 'All %s', 'easy-post-types-fields' ), true ),
			// translators: the singular post type name
			'archives'                 => $this->define_singular_label( __( '%s Archives', 'easy-post-types-fields' ) ),
			// translators: the singular post type name
			'attributes'               => $this->define_singular_label( __( '%s Attributes', 'easy-post-types-fields' ) ),
			// translators: the singular post type name
			'insert_into_item'         => $this->define_singular_label( __( 'Insert into %s', 'easy-post-types-fields' ), true ),
			// translators: the singular post type name
			'uploaded_to_this_item'    => $this->define_singular_label( __( 'Uploaded to this %s', 'easy-post-types-fields' ), true ),
			// translators: the plural post type name
			'filter_items_list'        => $this->define_label( __( 'Filter %s list', 'easy-post-types-fields' ), true ),
			// translators: the plural post type name
			'items_list_navigation'    => $this->define_label( __( '%s list navigation', 'easy-post-types-fields' ), true ),
			// translators: the plural post type name
			'items_list'               => $this->define_label( __( '%s list', 'easy-post-types-fields' ), true ),
			// translators: the singular post type name
			'item_published'           => $this->define_singular_label( __( '%s published.', 'easy-post-types-fields' ), true ),
			// translators: the singular post type name
			'item_published_privately' => $this->define_singular_label( __( '%s published privately.', 'easy-post-types-fields' ), true ),
			// translators: the singular post type name
			'item_reverted_to_draft'   => $this->define_singular_label( __( '%s reverted to draft.', 'easy-post-types-fields' ), true ),
			// translators: the singular post type name
			'item_scheduled'           => $this->define_singular_label( __( '%s scheduled.', 'easy-post-types-fields' ), true ),
			// translators: the singular post type name
			'item_updated'             => $this->define_singular_label( __( '%s updated.', 'easy-post-types-fields' ), true ),
			// translators: the singular post type name
			'item_link'                => $this->define_singular_label( __( '%s Link', 'easy-post-types-fields' ) ),
			// translators: the singular post type name
			'item_link_description'    => $this->define_singular_label( __( 'A link to a %s.', 'easy-post-types-fields' ), true ),
		];

		return $default_labels;
	}

	public function define_label( $label, $to_lower = false ) {
		$name = $to_lower ? strtolower( $this->name ) : $this->name;

		return ucfirst( sprintf( $label, $name ) );
	}

	public function define_singular_label( $label, $to_lower = false ) {
		$singular_name = $to_lower ? strtolower( $this->singular_name ) : $this->singular_name;

		return ucfirst( sprintf( $label, $singular_name ) );
	}

	public function register_post_type() {
		$post_type = register_post_type(
			$this->post_type,
			$this->args
		);

		if ( is_wp_error( $post_type ) ) {
			return;
		}

		$this->register_meta();
	}

	public function register_taxonomies() {
		$taxonomies = get_post_meta( $this->id, '_ept_taxonomies', true );

		if ( is_array( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$args = [
					'labels'       => [
						'name'          => $taxonomy['name'],
						'singular_name' => $taxonomy['singular_name'],
					],
					'hierarchical' => isset( $taxonomy['hierarchical'] ) ? $taxonomy['hierarchical'] : true,
				];

				new Taxonomy( $taxonomy['slug'], $this->post_type, $args );
			}

			return array_map(
				function( $t ) {
					return "{$t['post_type']}_{$t['slug']}";
				},
				$taxonomies
			);
		}

		return [];
	}

	public function register_meta() {
		$fields = get_post_meta( $this->id, '_ept_fields', true );

		if ( is_array( $fields ) ) {
			foreach ( $fields as $field ) {
				new Field( $field, $this->post_type );
			}

			return array_map(
				function( $t ) {
					return "{$t['post_type']}_{$t['slug']}";
				},
				$fields
			);
		}

		return [];
	}

	public function register_cpt_metabox() {
		// translators: A post type name
		$title = sprintf( __( '%s metadata' ), $this->singular_name );
		add_meta_box( "ept_post_type_{$this->singular_name}_metabox", $title, [ $this, 'output_meta_box' ] );
	}

	public function output_meta_box( $post ) {
		do_action( "ept_post_type_{$this->singular_name}_metabox" );

		// get the fields registered with the post type
		$fields    = get_post_meta( $this->id, '_ept_fields', true );
		$post_type = $this->post_type;

		if ( empty( $fields ) ) {
			return;
		}

		include 'Admin/views/html-meta-box.php';
	}

}
