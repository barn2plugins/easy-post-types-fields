<?php
namespace Barn2\Plugin\Easy_Post_Types_Fields\Post_Types;

/**
 * The class registering a new Custom Post Type and handling its custom fields and taxonomies.
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Custom_Post_Type extends Abstract_Post_Type {

	/**
	 * The arguments for the post type registration
	 *
	 * @var array
	 */
	private $args = [];

	/**
	 * {@inheritDoc}
	 */
	public function init() {
		if ( $this->prepare_arguments() ) {
			$this->activate_post_type();
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function activate_post_type() {
		$post_type = register_post_type(
			$this->post_type,
			$this->args
		);

		if ( is_wp_error( $post_type ) ) {
			return;
		}

		parent::activate_post_type();
	}

	/**
	 * Prepare the arguments for the custom post type registration
	 *
	 * EPT only collects the singular and plural name and the slug of the post
	 * type. All the other arguments are defined with default values that can
	 * be adjusted using the `ept_post_type_{$this->slug}_args` filter.
	 *
	 * @return array
	 */
	public function prepare_arguments() {
		if ( empty( $this->args ) ) {
			$args         = [];
			$default_args = [
				'public'               => true,
				'exclude_from_search'  => false,
				'publicly_queryable'   => true,
				'show_in_menu'         => true,
				'show_in_nav_menus'    => true,
				'show_in_admin_bar'    => true,
				'show_in_rest'         => true,
				'menu_position'        => 27,
				'menu_icon'            => 'dashicons-list-view',
				'supports'             => false,
				'register_meta_box_cb' => [ $this, 'register_cpt_metabox' ],
				'query_var'            => false,
				'can_export'           => false,
				'delete_with_user'     => false,
				'has_archive'          => true,
			];

			/**
			 * Filter the labels used to register the custom post type.
			 *
			 * The variable part of the hook name is the slug of the post type.
			 *
			 * @param array $default_labels An associative array of labels
			 */
			$args['labels'] = apply_filters(
				"ept_post_type_{$this->slug}_labels",
				$this->default_labels()
			);

			$supports         = get_post_meta( $this->id, '_ept_supports', true );
			$args['supports'] = $supports ?: [ 'title', 'editor' ];
			$args['rewrite']  = [
				'slug'       => sanitize_title( $this->name ),
				'with_front' => false,
			];

			$this->register_taxonomies();
			$args['taxonomies'] = $this->taxonomies;

			/**
			 * Filter the arguments to register a custom post type
			 *
			 * The variable part of the hook name is the slug of the post type.
			 *
			 * @param array $args The arguments that define the custom post type
			 */
			$this->args = apply_filters(
				"ept_post_type_{$this->slug}_args",
				wp_parse_args(
					$args,
					$default_args
				)
			);
		}

		return $this->args;
	}

	/**
	 * Return the default labels for the post type registration
	 *
	 * All the labels are defined on the basis of the singular and plural names
	 *
	 * @return array
	 */
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
			'all_items'                => $this->define_label( __( 'All %s', 'easy-post-types-fields' ) ),
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

	/**
	 * Define a label using the plural name
	 *
	 * @param  string $label The label where the name is used
	 * @param  boolean $to_lower Whether to make the name lower case or not
	 * @return string
	 */
	public function define_label( $label, $to_lower = false ) {
		$name = $to_lower ? strtolower( $this->name ) : $this->name;

		return ucfirst( sprintf( $label, $name ) );
	}

	/**
	 * Define a label using the singular name
	 *
	 * @param  string $label The label where the name is used
	 * @param  boolean $to_lower Whether to make the name lower case or not
	 * @return string
	 */
	public function define_singular_label( $label, $to_lower = false ) {
		$singular_name = $to_lower ? strtolower( $this->singular_name ) : $this->singular_name;

		return ucfirst( sprintf( $label, $singular_name ) );
	}

}
