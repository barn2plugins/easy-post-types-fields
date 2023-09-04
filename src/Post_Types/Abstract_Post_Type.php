<?php
/**
 * Contain the abstract class handling a custom post type
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Post_Types;

use Barn2\Plugin\Easy_Post_Types_Fields\Taxonomy;
use Barn2\Plugin\Easy_Post_Types_Fields\Field;

use function Barn2\Plugin\Easy_Post_Types_Fields\ept;

/**
 * The abstract class handling a new Custom Post Type.
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
abstract class Abstract_Post_Type implements Post_Type_Interface {

	/**
	 * The ID of the EPT post containing the CPT definition
	 *
	 * @var int
	 */
	protected $id;

	/**
	 * The name (generally plural) of the CPT as defined in $args['labels']['name']
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The singular name of the CPT as defined in $args['labels']['singular_name']
	 *
	 * @var string
	 */
	protected $singular_name;

	/**
	 * The post type of the CPT
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * The taxonomies registered for this post types
	 *
	 * @var array[]
	 */
	protected $taxonomies = [];

	/**
	 * The fields registered for this post types
	 *
	 * @var array[]
	 */
	protected $fields = [];

	/**
	 * The main post type class constructor
	 *
	 * Custom post types registered by this plugin
	 * and publicly queryable post types added by other plugins
	 * are stored in the post table with the 'ept_post_type' post type.
	 *
	 * @param  int|string $id The ID of the post holding the post type properties
	 * @return void
	 */
	public function __construct( $id ) {
		$this->id = $id;

		if ( 'ept_post_type' !== get_post_type( $this->id ) ) {
			$this->is_registered = false;
			return;
		}

		$post_type_object = get_post( $this->id );

		if ( $post_type_object ) {
			$this->slug          = $post_type_object->post_name;
			$this->post_type     = sprintf( '%s%s', ( 'publish' === $post_type_object->post_status ? 'ept_' : '' ), $post_type_object->post_name );
			$this->singular_name = $post_type_object->post_title;
			$this->name          = get_post_meta( $this->id, '_ept_plural_name', true );

			if ( '' === $this->name ) {
				$this->name = $this->singular_name;
			}

			$this->init();
		}
	}

	/**
	 * Initialize the post type with all its custom fields and taxonomies
	 *
	 * This method simply call the `activate_post_type` method
	 * but can be redefined in a subclass to make the activation
	 * conditional to other processes
	 *
	 * @return void
	 */
	public function init() {
		$this->activate_post_type();
	}

	/**
	 * Activate
	 *
	 * @return void
	 */
	protected function activate_post_type() {
		if ( empty( $this->taxonomies ) ) {
			$this->register_taxonomies();
		}

		$this->maybe_flush_rewrite_rules();
		$this->register_meta();

		if ( $this->fields || $this->taxonomies ) {
			$this->register();
		}
	}

	/**
	 * Register the hooks activating the post type and its custom fields and taxonomies
	 *
	 * @return void
	 */
	protected function register() {
		add_action( "save_post_{$this->post_type}", [ $this, 'save_post_data' ] );
		add_action( 'pre_post_update', [ $this, 'save_post_data' ] );
	}

	/**
	 * Register the taxonomies associated with the post type
	 *
	 * @return void
	 */
	protected function register_taxonomies() {
		$taxonomies = get_post_meta( $this->id, '_ept_taxonomies', true );
		$post_type  = $this->post_type;

		if ( is_array( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$args = [
					'labels'       => [
						'name'          => $taxonomy['name'],
						'singular_name' => $taxonomy['singular_name'],
					],
					'hierarchical' => isset( $taxonomy['hierarchical'] ) ? $taxonomy['hierarchical'] : true,
				];

				new Taxonomy( $taxonomy['slug'], $post_type, $args );
			}

			$this->taxonomies = array_map(
				function( $t ) use ( $post_type ) {
					return "{$post_type}_{$t['slug']}";
				},
				$taxonomies
			);
		}
	}

	/**
	 * Register the custom fields associated with the post type
	 *
	 * @return void
	 */
	protected function register_meta() {
		$fields    = get_post_meta( $this->id, '_ept_fields', true );
		$post_type = $this->post_type;

		if ( is_array( $fields ) ) {
			foreach ( $fields as $field ) {
				new Field( $field, $this->post_type );
			}

			$this->fields = array_map(
				function( $f ) use ( $post_type ) {
					return "{$post_type}_{$f['slug']}";
				},
				$fields
			);
		}
	}

	/**
	 * Register the custom meta box associated with the post type
	 *
	 * @param  WP_Post $post The post currently being edited
	 * @return void
	 */
	public function register_cpt_metabox( $post = null ) {
		if ( empty( $this->fields ) ) {
			return;
		}

		add_meta_box( "ept_post_type_{$this->slug}_metabox", __( 'Custom Fields', 'easy-post-types-fields' ), [ $this, 'output_meta_box' ], $this->post_type );
	}

	/**
	 * Output the HTML markup of the custom meta box
	 *
	 * @param  mixed $post
	 * @return void
	 */
	public function output_meta_box( $post ) {
		// get the fields registered with the post type
		$fields    = get_post_meta( $this->id, '_ept_fields', true );
		$post_type = $this->post_type;

		if ( empty( $fields ) ) {
			return;
		}

		/**
		 * Fires before the content of the metabox is output
		 *
		 * The variable portion of the hook is the slug of the current post type.
		 * Custom post types are always prefixed with `ept_` while built-in and
		 * third-party post types use their original slug.
		 */
		do_action( "ept_post_type_{$post_type}_before_metabox" );

		include ept()->get_admin_path( 'views/html-meta-box.php' );

		/**
		 * Fires after the content of the metabox is output
		 *
		 * The variable portion of the hook is the slug of the current post type.
		 * Custom post types are always prefixed with `ept_` while built-in and
		 * third-party post types use their original slug.
		 */
		do_action( "ept_post_type_{$post_type}_after_metabox" );
	}

	/**
	 * Store the post metadata
	 *
	 * @param  int|string $post_id
	 * @return void
	 */
	public function save_post_data( $post_id ) {
		$postdata = sanitize_post( $_POST, 'db' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( ! isset( $postdata['post_type'] ) ) {
			return;
		}

		$fields = get_post_meta( $this->id, '_ept_fields', true );

		if ( empty( $fields ) ) {
			return;
		}

		foreach ( $fields as $field ) {
			$meta_key = "{$this->post_type}_{$field['slug']}";
			if ( isset( $postdata[ $meta_key ] ) && '' !== $postdata[ $meta_key ] ) {
				update_post_meta( $post_id, $meta_key, $postdata[ $meta_key ] );
			}
			else {
				delete_post_meta( $post_id, $meta_key );
			}
		}
	}

	/**
	 * Flush the rewrite rules
	 *
	 * Since flushing the rewrite rules is an expensive operation, this method
	 * determines whether a flush is necessary based on a transient that is set
	 * every time a post type or a taxonomy is edited and removed right after
	 * the rewrite rules are flushed
	 *
	 * @return void
	 */
	public function maybe_flush_rewrite_rules() {
		$transient_name = sprintf( 'ept_%s_updated', $this->post_type );

		if ( get_transient( $transient_name ) ) {
			flush_rewrite_rules();
			delete_transient( $transient_name );
		}
	}
}
