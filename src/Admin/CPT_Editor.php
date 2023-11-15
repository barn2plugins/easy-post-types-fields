<?php
/**
 * The main page of the plugin.
 *
 * This class handles most of the functionalities featured by the plugin
 * and is responsible for the output of the page as well as all the logic
 * behind the data storage of all the plugin settings.
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

use Barn2\Plugin\Easy_Post_Types_Fields\Plugin,
	Barn2\Plugin\Easy_Post_Types_Fields\Util,
	Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Plugin\Simple_Plugin,
	Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Registerable,
	Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Service,
	Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Util as Lib_Util;

use WP_Error;
use WP_Query;

/**
 * Define the CPT editor.
 *
 * In the context of this CPT editor, the `$post` object represents a CPT definition.
 * The post title is the singular name of the CPT.
 * Easy Post Types and Fields will register a custom post type for each definition added
 * to the ept_content_type post list table.
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class CPT_Editor implements Service, Registerable {

	/**
	 * The main plugin instance
	 *
	 * @var Plugin
	 */
	private $plugin;

	/**
	 * A list of errors triggered during validation
	 *
	 * @var WP_Error
	 */
	private $errors;

	/**
	 * Constructor
	 *
	 * @param  Simple_Plugin $plugin The main instance of this plugin
	 * @return void
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
		$this->errors = new WP_Error();
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		register_post_type(
			'ept_post_type',
			[
				'labels'              => [
					'name'                     => __( 'Post types', 'easy-post-types-fields' ),
					'singular_name'            => __( 'Post type', 'easy-post-types-fields' ),
					'add_new'                  => __( 'Add new', 'easy-post-types-fields' ),
					'add_new_item'             => __( 'Add new post type', 'easy-post-types-fields' ),
					'edit_item'                => __( 'Edit post type', 'easy-post-types-fields' ),
					'new_item'                 => __( 'New post type', 'easy-post-types-fields' ),
					'view_item'                => __( 'View post type', 'easy-post-types-fields' ),
					'view_items'               => __( 'View post types', 'easy-post-types-fields' ),
					'search_items'             => __( 'Search custom post types', 'easy-post-types-fields' ),
					'not_found'                => __( 'No custom post types have been created yet.', 'easy-post-types-fields' ),
					'parent_item_colon'        => null,
					'all_items'                => __( 'All post types', 'easy-post-types-fields' ),
					'attributes'               => __( 'Post Type Attributes', 'easy-post-types-fields' ),
					'filter_items_list'        => __( 'Filter post types list', 'easy-post-types-fields' ),
					'filter_by_date'           => __( 'Filter by date', 'easy-post-types-fields' ),
					'items_list_navigation'    => __( 'Post types list navigation', 'easy-post-types-fields' ),
					'items_list'               => __( 'Post types list', 'easy-post-types-fields' ),
					'item_published'           => __( 'Post type published.', 'easy-post-types-fields' ),
					'item_published_privately' => __( 'Post type published privately.', 'easy-post-types-fields' ),
					'item_reverted_to_draft'   => __( 'Post type reverted to draft.', 'easy-post-types-fields' ),
					'item_scheduled'           => __( 'Post scheduled.', 'easy-post-types-fields' ),
					'item_updated'             => __( 'Post updated.', 'easy-post-types-fields' ),
					'item_link_description'    => __( 'A link to a post type.', 'easy-post-types-fields' ),
				],
				'description'         => __( 'Define a custom post type', 'easy-post-types-fields' ),
				'public'              => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'show_in_menu'        => false,
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => true,
				'show_in_rest'        => false,
				'menu_position'       => null,
				'menu_icon'           => '',
				'supports'            => [ 'title' ],
				'taxonomies'          => [],
				'rewrite'             => false,
				'query_var'           => false,
				'can_export'          => false,
				'delete_with_user'    => false,
			]
		);

		add_action( 'admin_enqueue_scripts', [ $this, 'load_scripts' ] );
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );

		add_action( 'wp_ajax_ept_inline_delete', [ $this, 'inline_delete' ] );

		add_action( 'admin_init', [ $this, 'save_post_data' ] );
		add_action( 'admin_notices', [ $this, 'admin_notices' ] );

		add_filter( 'menu_order', [ $this, 'menu_order' ] );
		add_filter( 'custom_menu_order', '__return_true' );
	}

	/**
	 * Enqueue the stylesheet and script required by this page
	 *
	 * @param  string $hook The value of the page query argument
	 * @return void
	 */
	public function load_scripts( $hook ) {
		$screen = get_current_screen();

		if ( in_array( $screen->id, [ 'toplevel_page_ept_post_types', 'ept_post_type', 'post-types_page_ept_post_types-promo-display', 'post-types_page_ept_post_types-promo-protect' ], true ) ) {
			wp_enqueue_script( 'ept-jquery-tiptip', plugin_dir_url( $this->plugin->get_file() ) . 'lib/assets/js/jquery-tiptip/jquery.tipTip.min.js', [ 'jquery' ], $this->plugin->get_version(), true );
			wp_enqueue_script( 'ept-editor', plugin_dir_url( $this->plugin->get_file() ) . 'assets/js/admin/ept-editor.js', [ 'jquery', 'wp-i18n', 'wp-url' ], $this->plugin->get_version(), true );
			wp_enqueue_style( 'ept-editor', plugin_dir_url( $this->plugin->get_file() ) . 'assets/css/admin/ept-editor.css', [], $this->plugin->get_version() );
		}
	}

	/**
	 * Add the top-level menu pointing to the main Manage page
	 *
	 * @return void
	 */
	public function admin_menu() {
		add_menu_page( 'Post Types', 'Post Types', 'manage_options', 'ept_post_types', [ $this, 'output_manage_page' ], 'dashicons-feedback', 26 );
		add_submenu_page( 'ept_post_types', 'Manage', 'Manage', 'manage_options', 'ept_post_types', [ $this, 'output_manage_page' ] );

		if ( ! Lib_Util::is_barn2_plugin_active( 'Barn2\Plugin\Posts_Table_Pro\ptp' ) ) {
			add_submenu_page( 'ept_post_types', 'Display', 'Display', 'manage_options', 'ept_post_types-promo-display', [ $this, 'output_promo_display_page' ] );
		}

		if ( ! Lib_Util::is_barn2_plugin_active( 'Barn2\Plugin\Password_Protected_Categories\ppc' ) ) {
			add_submenu_page( 'ept_post_types', 'Protect', 'Protect', 'manage_options', 'ept_post_types-promo-protect', [ $this, 'output_promo_protect_page' ] );
		}
	}

	public function menu_order( $menu_order ) {
		$ept_menu = array_filter(
			$menu_order,
			function( $menu ) {
				return 'ept_post_types' === $menu;
			}
		);

		if ( empty( $ept_menu ) ) {
			return $menu_order;
		}

		$ept_menu_index = array_keys( $ept_menu )[0];

		$post_type_menus = array_filter(
			$menu_order,
			function( $menu ) {
				return false !== strpos( $menu, '?post_type=ept_' );
			}
		);

		$new_order = array_diff_key( $menu_order, $ept_menu, $post_type_menus );

		$new_menu = array_merge(
			array_slice( $new_order, 0, $ept_menu_index ),
			$ept_menu,
			$post_type_menus,
			array_slice( $new_order, $ept_menu_index )
		);

		return $new_menu;
	}

	/**
	 * Get the data of the two sections of the Manage page
	 *
	 * The array returned by this function the page description shown under the
	 * title, the singular name of the entity relevant to the requested page
	 * and the appropriate WP_List_Table subclass used to render the list of
	 * entities.
	 *
	 * @param  array $request The list of query arguments relevant to the Manage page
	 * @return array
	 */
	public function get_page_data( $request ) {
		$sections          = [
			'fields'     => [
				'list_table_class' => 'Custom_Field',
				'plural'           => __( 'Custom fields', 'easy-post-types-fields' ),
				'singular'         => __( 'Custom field', 'easy-post-types-fields' ),
				// translators: 1: the plural name of the post type, 2: the opening tab of an anchor element, 3: the closing tag of an anchor element, 4: the singular name of the post type, 5: the plural name of the post type,
				'description'      => __( 'Use custom fields to store extra data about your %1$s, such as a reference number or link. Custom fields are for data that is unique to each %4$s. If you want to use the data to organize or group your %5$s then you should create a %2$staxonomy%3$s instead.', 'easy-post-types-fields' ),
			],
			'taxonomies' => [
				'list_table_class' => 'Taxonomy',
				'plural'           => __( 'Taxonomies', 'easy-post-types-fields' ),
				'singular'         => __( 'Taxonomy', 'easy-post-types-fields' ),
				// translators: the plural name of the post type
				'description'      => __( 'Taxonomies let you organize and group your %1$s. For example, you might want to organize them by category, tag, year, author, or industry. If you need to add data that is unique to each %4$s then you should create a %2$scustom field%3$s instead.', 'easy-post-types-fields' ),
			],
		];
		$section           = $request['section'];
		$section_labels    = $sections[ $section ];
		$list_table_class  = __NAMESPACE__ . '\List_Tables\\' . $sections[ $section ]['list_table_class'] . '_List_Table';
		$request_post_type = Util::get_post_type_by_name( $request['post_type'] );
		$list_table        = new $list_table_class( $request_post_type );
		$singular_name     = $sections[ $section ]['singular'];

		parse_str( $_SERVER['QUERY_STRING'], $query_args );

		$query_args['section'] = 'fields' === $section ? 'taxonomies' : 'fields';
		$cross_link            = Util::get_manage_page_url( $query_args['post_type'], $query_args['section'] );

		$page_description = sprintf(
			$section_labels['description'],
			$request_post_type->labels->name,
			"<a href=\"$cross_link\">",
			'</a>',
			$request_post_type->labels->singular_name,
			strtolower( $request_post_type->labels->name )
		);

		return [
			$page_description,
			$singular_name,
			$list_table
		];
	}

	/**
	 * Output the HTML markup of the main plugin page
	 *
	 * After collecting all the necessary information, thie method includes
	 * the main HTML view file, which in turn includes the appropriate subviews,
	 * depending on the section and action being requested.
	 *
	 * @return void
	 */
	public function output_manage_page() {
		$request          = Util::get_page_request();
		$page_title       = __( 'Post Types', 'easy-post-types-fields' );
		$page_description = __( 'Use this page to manage your custom post types. You can add and edit post types, custom fields and taxonomies.', 'easy-post-types-fields' );
		$plugin           = $this->plugin;
		$breadcrumbs      = Util::get_page_breadcrumbs();
		$content          = isset( $request['section'] ) ? 'lists' : 'post_types';
		$section          = isset( $request['section'] ) ? $request['section'] : 'add';
		$nonce_action     = 'save_list_item_postdata';
		$referer          = Util::get_referer( $nonce_action );

		// The maximum length of a post type name is 21 characters (which includes the `ept_` prefix used for a custom post type)
		$maxlength = 17;
		$new_link  = add_query_arg(
			[
				'page'   => isset( $request['section'] ) ? $request['page'] : $plugin->get_slug() . '-setup-wizard',
				'action' => 'add',
			],
			'admin.php'
		);

		if ( 'post_types' === $content && isset( $request['post_type'] ) ) {
			$content = 'post_type';
		}

		if ( isset( $request['section'] ) ) {
			if ( ! isset( $request['post_type'] ) ) {
				wp_die( wp_kses_post( 'The address is missing the <code>post_type</code> parameter', 'easy-post-types-fields' ) );
			}

			list( $page_description, $singular_name, $list_table ) = $this->get_page_data( $request );

			$new_link = add_query_arg(
				[
					'post_type' => $request['post_type'],
					'section'   => $request['section'],
				],
				$new_link
			);

			$page_title = 'taxonomies' === $request['section'] ?
				__( 'Manage Taxonomies', 'easy-post-types-fields' ) :
				__( 'Manage Custom Fields', 'easy-post-types-fields' );

			$maxlength = 32 - strlen( $request['post_type'] );
		} else {
			if ( isset( $request['post_type'] ) ) {
				$page_title       = __( 'Edit post type', 'easy-post-types-fields' );
				$page_description = '';
			}
		}

		if ( isset( $request['action'] ) ) {
			$current_action = 'add' === $request['action'] ?
				__( 'Add', 'easy-post-types-fields' ) :
				__( 'Edit', 'easy-post-types-fields' );

			$page_title = 'taxonomies' === $request['section'] ?
				// translators: either 'Add' or 'Edit'
				__( '%s Taxonomy', 'easy-post-types-fields' ) :
				// translators: either 'Add' or 'Edit'
				__( '%s Custom Field', 'easy-post-types-fields' );

			$page_title       = sprintf( $page_title, $current_action );
			$page_description = '';
			$new_link         = '';
		}

		require $this->plugin->get_admin_path( 'views/html-manage-page.php' );
	}

	/**
	 * Output the HTML markup of the promo for Posts Table Pro
	 *
	 * @return void
	 */
	public function output_promo_display_page() {
		$page_title  = __( 'Display your post types and fields with Posts Table Pro', 'easy-post-types-fields' );
		$image_url   = $this->plugin->get_dir_url() . 'assets/images/promo-display.png';
		$breadcrumbs = '';
		$new_link    = '';

		require $this->plugin->get_admin_path( 'views/html-promo-display.php' );
	}

	/**
	 * Output the HTML markup of the promo for Password Protected Categories
	 *
	 * @return void
	 */
	public function output_promo_protect_page() {
		$page_title  = __( 'Restrict access to parts of your website with Password Protected Categories', 'easy-post-types-fields' );
		$image_url   = $this->plugin->get_dir_url() . 'assets/images/promo-protect.png';
		$breadcrumbs = '';
		$new_link    = '';

		require $this->plugin->get_admin_path( 'views/html-promo-protect.php' );
	}

	/**
	 * Add all the errors in the admin notices section
	 *
	 * @return void
	 */
	public function admin_notices() {
		if ( ! $this->errors->has_errors() ) {
			return;
		}

		?>
		<div class="error notice">
			<?php
			foreach ( $this->errors->get_error_messages() as $error ) {
				?>
				<p><?php echo wp_kses_post( $error ); ?></p>
				<?php
			}
			?>
		</div>
		<?php
	}

	/**
	 * Delete a custom field or a taxonomy from the database.
	 *
	 * This method is call via AJAX but also causes a page reload so that
	 * the deleted entity is reflected across the whole admin area.
	 * For example, when deleting a taxonomy, its name would still be listed
	 * in the menu of the custom post type until a full page reload.
	 *
	 * @return void
	 */
	public function inline_delete() {
		check_ajax_referer( 'inlinedeletenonce', '_inline_delete' );

		$type             = sanitize_key( $_POST['type'] );
		$slug             = sanitize_title( $_POST['slug'] );
		$post_type        = sanitize_title( $_POST['post_type'] );
		$meta_key         = 'taxonomies' === $type ? '_ept_taxonomies' : '_ept_fields';
		$post_type_object = Util::get_post_type_object( $post_type );

		if ( $post_type_object ) {
			$items = get_post_meta( $post_type_object->ID, $meta_key, true );

			if ( ! $items ) {
				$items = [];
			}

			$new_items = array_filter(
				$items,
				function( $item ) use ( $slug ) {
					return $item['slug'] !== $slug;
				}
			);

			update_post_meta( $post_type_object->ID, $meta_key, $new_items );
			wp_send_json_success();
		}

		wp_send_json_error( [ 'error_message' => __( 'The post type is missing or an error occurred when completing this operation.', 'easy-post-types-fields' ) ] );
	}

	/**
	 * Save the post data
	 *
	 * This method calls the appropriate method depending on the type of
	 * entity being saved. The value of `$data_type` determines which method
	 * should be called.
	 *
	 * @return void
	 */
	public function save_post_data() {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'save_list_item_postdata' ) ) {
			return;
		}

		$this->errors = new WP_Error();

		$postdata = [
			'slug'          => isset( $_POST['slug'] ) ? sanitize_title( $_POST['slug'] ) : false,
			'name'          => isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : false,
			'singular_name' => isset( $_POST['singular_name'] ) ? sanitize_text_field( $_POST['singular_name'] ) : false,
			'supports'      => isset( $_POST['supports'] ) ? array_keys( $_POST['supports'] ) : [],
			'hierarchical'  => isset( $_POST['hierarchical'] ) ? filter_var( $_POST['hierarchical'], FILTER_VALIDATE_BOOLEAN ) : false,
			'type'          => isset( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : false,
			'previous_slug' => isset( $_POST['previous_slug'] ) ? sanitize_title( $_POST['previous_slug'] ) : false,
		];

		$request   = Util::get_page_request();
		$data_type = 'post_type';

		if ( isset( $request['section'] ) ) {
			$data_type = $request['section'];
		}

		$this->{"save_$data_type"}( $postdata, $request );
	}

	/**
	 * Store a custom post type in the database
	 *
	 * @param  array $data The post type data being submitted, appropriately sanitized
	 * @param  array $request The current page request
	 * @return void
	 */
	public function save_post_type( $data, $request ) {
		$post_type_id     = 0;
		$post_type_object = Util::get_post_type_object( $request['post_type'] );

		if ( $post_type_object ) {
			$post_type_id = $post_type_object->ID;
		}

		if ( empty( $data['slug'] ) ) {
			$this->errors->add( 'empty_slug', __( 'The slug of a post type is required.', 'easy-post-types-fields' ) );
			return;
		}

		if ( 17 < strlen( $data['slug'] ) ) {
			$this->errors->add( 'slug_too_long', __( 'The slug of a post type must be 17 characters or less.', 'easy-post-types-fields' ) );
			return;
		}

		if ( empty( $data['singular_name'] ) ) {
			$this->errors->add( 'empty_singular_name', __( 'The singular name of a post type is required.', 'easy-post-types-fields' ) );
			return;
		}

		if ( empty( $data['name'] ) ) {
			$this->errors->add( 'empty_name', __( 'The plural name of a post type is required.', 'easy-post-types-fields' ) );
			return;
		}

		$conflicts_query = new WP_Query(
			[
				'posts_per_page' => 1,
				'post_type'      => 'ept_post_type',
				'post__not_in'   => [ $post_type_id ],
				'name'           => $data['slug'],
			]
		);

		if ( $conflicts_query->post ) {
			$this->errors->add( 'conflicting_post_type', __( 'A post type with the same slug is already registered. Please choose a different slug.', 'easy-post-types-fields' ) );
			return;
		}

		$supports = array_merge( [ 'title' ], $data['supports'] );

		$args = [
			'ID'             => $post_type_id,
			'post_title'     => $data['singular_name'],
			'post_name'      => $data['slug'],
			'post_type'      => 'ept_post_type',
			'post_status'    => 'publish',
			'comment_status' => 'closed',
			'meta_input'     => [
				'_ept_plural_name' => $data['name'],
				'_ept_supports'    => $supports,
			],
		];

		$post_type_id = wp_insert_post( $args, false, false );

		if ( is_wp_error( $post_type_id ) ) {
			$this->errors->add( $post_type_id->get_error_code(), $post_type_id->get_error_message(), $post_type_id->get_error_data() );
			return;
		}

		Util::set_update_transient( $request['post_type'] );
		wp_safe_redirect( Util::get_manage_page_url() );
	}

	/**
	 * Store a custom taxonomy in the database
	 *
	 * Taxonomies are stored as postmeta data of the custom post type which is
	 * stored as a `ept_post_type` type of post. The meta_key used to store
	 * taxonomies is `_ept_taxonomies`.
	 * This method also validates the data being submitted checking if the
	 * database contains a different taxonomy registered to the same post type
	 * with the same name. In case the validation fails, the method returns a
	 * success=false type of response to the AJAX caller, with the appropriate
	 * message explaining what happened.
	 *
	 * @param  array $data The post type data being submitted, appropriately sanitized
	 * @param  array $request The current page request
	 * @return void
	 */
	public function save_taxonomies( $data, $request ) {
		$post_type_object = Util::get_post_type_object( $request['post_type'] );

		if ( $post_type_object ) {
			if ( empty( $data['slug'] ) ) {
				$this->errors->add( 'empty_slug', __( 'The slug of a taxonomy is required.', 'easy-post-types-fields' ) );
				return;
			}

			/**
			 * Taxonomies cannot have slugs of more than 32 characters
			 * EPT prefixes the slug of every taxonomy with the slug of the
			 * correspondent post type, followed by an underscore
			 * the underscore is accounted for reducing the original maximum
			 * length to 31
			 */
			$maxlength = 31 - strlen( $request['post_type'] );

			if ( $maxlength < strlen( $data['slug'] ) ) {
				// translators: the maximum number of characters of the slug
				$this->errors->add( 'slug_too_long', sprintf( __( 'The slug of a taxonomy for this post type must be %d characters or less.', 'easy-post-types-fields' ), $maxlength ) );
				return;
			}

			if ( empty( $data['singular_name'] ) ) {
				$this->errors->add( 'empty_singular_name', __( 'The singular name of a taxonomy is required.', 'easy-post-types-fields' ) );
				return;
			}

			if ( empty( $data['name'] ) ) {
				$this->errors->add( 'empty_name', __( 'The plural name of a taxonomy is required.', 'easy-post-types-fields' ) );
				return;
			}

			$taxonomies = Util::get_custom_taxonomies( $request['post_type'] );

			$new_taxonomy     = [
				'name'          => $data['name'],
				'singular_name' => $data['singular_name'],
				'slug'          => $data['slug'],
				'hierarchical'  => $data['hierarchical'],
				'is_custom'     => true,
			];
			$slug             = $data['slug'];
			$other_taxonomies = $taxonomies;

			if ( $data['previous_slug'] ) {
				$other_taxonomies = array_filter(
					$taxonomies,
					function( $t ) use ( $data ) {
						return $t['slug'] !== $data['previous_slug'];
					}
				);
			}

			if ( $data['previous_slug'] !== $data['slug'] ) {
				$conflicting_taxonomies = [];

				if ( 'private' === $post_type_object->post_status ) {
					$conflicting_taxonomies = array_filter(
						Util::get_builtin_taxonomies( $request['post_type'] ),
						function( $t ) use ( $slug ) {
							return $t['slug'] === $slug;
						}
					);
				}

				$conflicting_taxonomies = array_merge(
					array_filter(
						$other_taxonomies,
						function( $t ) use ( $slug ) {
							return $t['slug'] === $slug;
						}
					),
					$conflicting_taxonomies
				);

				if ( ! empty( $conflicting_taxonomies ) ) {
					$this->errors->add( 'conflicting_taxonomy', __( 'A taxonomy with the same slug is already registered to this post type. Please choose a different slug.', 'easy-post-types-fields' ) );
					return;
				}
			}

			$new_taxonomies = array_merge( $other_taxonomies, [ $new_taxonomy ] );

			usort(
				$new_taxonomies,
				function( $a, $b ) {
					return $a['name'] > $b['name'] ? 1 : -1;
				}
			);

			update_post_meta( $post_type_object->ID, '_ept_taxonomies', $new_taxonomies );

			Util::set_update_transient( $request['post_type'] );
			wp_safe_redirect( Util::get_manage_page_url( $request['post_type'], $request['section'] ) );
		}
	}

	/**
	 * Store a custom field in the database
	 *
	 * Taxonomies are stored as postmeta data of the custom post type which is
	 * stored as a `ept_post_type` type of post. The meta_key used to store
	 * taxonomies is `_ept_fields`.
	 * This method also validates the data being submitted checking if the
	 * database contains a different field registered to the same post type
	 * with the same name. In case the validation fails, the method returns a
	 * success=false type of response to the AJAX caller, with the appropriate
	 * message explaining what happened.
	 *
	 * @param  array $data The post type data being submitted, appropriately sanitized
	 * @param  array $request The current page request
	 * @return void
	 */
	public function save_fields( $data, $request ) {
		$post_type_object = Util::get_post_type_object( $request['post_type'] );

		if ( $post_type_object ) {
			if ( empty( $data['slug'] ) ) {
				$this->errors->add( 'empty_slug', __( 'The slug of a custom field is required.', 'easy-post-types-fields' ) );
				return;
			}

			if ( empty( $data['name'] ) ) {
				$this->errors->add( 'empty_name', __( 'The name of a custom field is required.', 'easy-post-types-fields' ) );
				return;
			}

			$fields = get_post_meta( $post_type_object->ID, '_ept_fields', true );

			if ( ! $fields ) {
				$fields = [];
			}

			$new_field    = [
				'name'      => $data['name'],
				'slug'      => $data['slug'],
				'type'      => $data['type'],
				'is_custom' => true,
			];
			$slug         = $data['slug'];
			$other_fields = $fields;

			if ( $data['previous_slug'] ) {
				$other_fields = array_filter(
					$fields,
					function( $t ) use ( $data ) {
						return $t['slug'] !== $data['previous_slug'];
					}
				);
			}

			if ( $data['previous_slug'] !== $data['slug'] ) {
				$conflicting_fields = array_filter(
					$other_fields,
					function( $t ) use ( $slug ) {
						return $t['slug'] === $slug;
					}
				);

				if ( ! empty( $conflicting_fields ) ) {
					$this->errors->add( 'conflicting_field', __( 'A field with the same slug is already registered to this post type. Please choose a different slug.', 'easy-post-types-fields' ) );
					return;
				}
			}

			$new_fields = array_merge( $other_fields, [ $new_field ] );

			usort(
				$new_fields,
				function( $a, $b ) {
					return $a['name'] > $b['name'] ? 1 : -1;
				}
			);

			update_post_meta( $post_type_object->ID, '_ept_fields', $new_fields );

			wp_safe_redirect( Util::get_manage_page_url( $request['post_type'], $request['section'] ) );
		}
	}
}
