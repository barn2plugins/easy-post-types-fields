<?php
namespace Barn2\Plugin\Easy_Post_Types_Fields;

/**
 * The class registering a new Custom Taxonomy.
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Taxonomy {

	/**
	 * The key of this taxonomy, including the post_type name
	 *
	 * @var string
	 */
	private $taxonomy;

	/**
	 * The slug of this taxonomy
	 *
	 * @var string
	 */
	private $slug;

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
	 * Whether the taxonomy is hierarchical (e.g. categories) or not (e.g. tags)
	 *
	 * @var bool
	 */
	private $hierarchical;

	/**
	 * The post type of the CPT
	 *
	 * @var string
	 */
	private $post_type;

	/**
	 * The arguments for the taxonomy registration
	 *
	 * @var array
	 */
	private $args = [];

	/**
	 * Whether the taxonomy has been successfully registered or not
	 *
	 * @var bool
	 */
	private $is_registered;

	/**
	 * Constructor
	 *
	 * @param  array $taxonomy A list of taxonomy properties
	 * @param  string $post_type The slug of the post type the taxonomy is going to be associated with
	 * @param  array $args An optional list of arguments for the taxonomy registration
	 * @return void
	 */
	public function __construct( $taxonomy, $post_type, $args = [] ) {
		$this->post_type = $post_type;
		$this->slug      = $taxonomy;
		$this->taxonomy  = "{$this->post_type}_$taxonomy";

		$this->hierarchical  = isset( $args['hierarchical'] ) && $args['hierarchical'];
		$name                = $this->hierarchical ? 'Categories' : 'Tags';
		$singular_name       = $this->hierarchical ? 'Category' : 'Tag';
		$this->name          = isset( $args['labels'] ) && isset( $args['labels']['name'] ) ? $args['labels']['name'] : $name;
		$this->singular_name = isset( $args['labels'] ) && isset( $args['labels']['singular_name'] ) ? $args['labels']['singular_name'] : $singular_name;

		if ( $this->prepare_arguments( $args ) ) {
			$this->register_taxonomy();
		}
	}

	/**
	 * Prepare the arguments for the taxonomy registration
	 *
	 * @param  array $args The input arguments
	 * @return array The arguments as adjusted by this method
	 */
	public function prepare_arguments( $args ) {
		if ( empty( $this->args ) ) {
			$default_args = [
				'public'            => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
			];

			$post_type_slug  = str_replace( 'ept_', '', $this->post_type );
			$args['rewrite'] = [
				'slug' => "{$post_type_slug}-{$this->slug}",
			];

			/**
			 * Filter the default labels of a custom taxonomy
			 *
			 * The variable part of the hook is the slug of the taxonomy
			 * (which is prefixed with `$post_type`). For example, the full slug
			 * of a `category` taxonomy registered to an `article` custom post
			 * type will be `ept_article_category`.
			 *
			 * @param array $default_labels The list of default labels for this taxonomy
			 */
			$args['labels'] = apply_filters(
				"ept_taxonomy_{$this->taxonomy}_labels",
				$this->default_labels()
			);

			/**
			 * Filter the arguments to register a custom taxonomy
			 *
			 * The variable part of the hook is the slug of the taxonomy
			 * (which is prefixed with `$post_type`). For example, the full slug
			 * of a `category` taxonomy registered to an `article` custom post
			 * type will be `ept_article_category`.
			 *
			 * @param array $args The list of argumets to register this taxonomy
			 */
			$this->args = apply_filters(
				"ept_taxonomy_{$this->taxonomy}_args",
				wp_parse_args(
					$args,
					$default_args
				)
			);
		}

		return $this->args;
	}

	/**
	 * Return the default labels for the taxonomy registration
	 *
	 * All the labels are defined on the basis of the singular and plural names
	 *
	 * @return array
	 */
	public function default_labels() {
		$name_field_description   = __( 'The name is how it appears on your site.', 'easy-post-types-fields' );
		$slug_field_description   = __( 'The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'easy-post-types-fields' );
		$parent_field_description = __( 'Assign a parent term to create a hierarchy. The term Jazz, for example, would be the parent of Bebop and Big Band.', 'easy-post-types-fields' );
		$desc_field_description   = __( 'The description is not prominent by default; however, some themes may show it.', 'easy-post-types-fields' );

		$default_labels = [
			'name'                       => $this->name,
			'singular_name'              => $this->singular_name,
			// translators: the plural name of a taxonomy
			'search_items'               => $this->define_label( __( 'Search %s', 'easy-post-types-fields' ) ),
			// translators: the plural name of a taxonomy
			'popular_items'              => $this->define_label( __( 'Popular %s', 'easy-post-types-fields' ) ),
			// translators: the plural name of a taxonomy
			'all_items'                  => $this->define_label( __( 'All %s', 'easy-post-types-fields' ) ),
			// translators: the singular name of a taxonomy
			'parent_item'                => $this->define_singular_label( __( 'Parent %s', 'easy-post-types-fields' ) ),
			// translators: the singular name of a taxonomy
			'parent_item_colon'          => $this->define_singular_label( __( 'Parent %s:', 'easy-post-types-fields' ) ),
			// translators: the plural name of a taxonomy
			'name_field_description'     => $this->define_label( $name_field_description ),
			// translators: the plural name of a taxonomy
			'slug_field_description'     => $this->define_label( $slug_field_description ),
			// translators: the plural name of a taxonomy
			'parent_field_description'   => $this->define_label( $parent_field_description ),
			// translators: the plural name of a taxonomy
			'desc_field_description'     => $this->define_label( $desc_field_description ),
			// translators: the singular name of a taxonomy
			'edit_item'                  => $this->define_singular_label( __( 'Edit %s', 'easy-post-types-fields' ) ),
			// translators: the singular name of a taxonomy
			'view_item'                  => $this->define_singular_label( __( 'View %s', 'easy-post-types-fields' ) ),
			// translators: the singular name of a taxonomy
			'update_item'                => $this->define_singular_label( __( 'Update %s', 'easy-post-types-fields' ) ),
			// translators: the singular name of a taxonomy
			'add_new_item'               => $this->define_singular_label( __( 'Add New %s', 'easy-post-types-fields' ) ),
			// translators: the singular name of a taxonomy
			'new_item_name'              => $this->define_singular_label( __( 'New %s Name', 'easy-post-types-fields' ) ),
			// translators: the plural name of a taxonomy
			'separate_items_with_commas' => $this->define_label( __( 'Separate %s with commas', 'easy-post-types-fields' ), true ),
			// translators: the plural name of a taxonomy
			'add_or_remove_items'        => $this->define_label( __( 'Add or remove %s', 'easy-post-types-fields' ), true ),
			// translators: the plural name of a taxonomy
			'choose_from_most_used'      => $this->define_label( __( 'Choose from the most used %s', 'easy-post-types-fields' ), true ),
			// translators: the plural name of a taxonomy
			'not_found'                  => $this->define_label( __( 'No %s found.', 'easy-post-types-fields' ), true ),
			// translators: the plural name of a taxonomy
			'no_terms'                   => $this->define_label( __( 'No %s', 'easy-post-types-fields' ), true ),
			// translators: the plural name of a taxonomy
			'filter_by_item'             => $this->define_label( __( 'Filter by %s', 'easy-post-types-fields' ), true ),
			// translators: the plural name of a taxonomy
			'items_list_navigation'      => $this->define_label( __( '%s list navigation', 'easy-post-types-fields' ) ),
			// translators: the plural name of a taxonomy
			'items_list'                 => $this->define_label( __( '%s list', 'easy-post-types-fields' ) ),
			// translators: the plural name of a taxonomy
			'most_used'                  => $this->define_label( __( 'Most Used', 'easy-post-types-fields' ) ),
			// translators: the plural name of a taxonomy
			'back_to_items'              => $this->define_label( __( '&larr; Go to %s', 'easy-post-types-fields' ) ),
			// translators: the singular name of a taxonomy
			'item_link'                  => $this->define_singular_label( __( '%s Link', 'easy-post-types-fields' ) ),
			// translators: the singular name of a taxonomy
			'item_link_description'      => $this->define_singular_label( __( 'A link to a %s.', 'easy-post-types-fields' ), true ),
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

	/**
	 * Register the taxonomy to its post type
	 *
	 * @return void
	 */
	public function register_taxonomy() {
		register_taxonomy(
			$this->taxonomy,
			$this->post_type,
			$this->args
		);
	}
}
