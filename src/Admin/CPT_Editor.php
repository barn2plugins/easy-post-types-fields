<?php
namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

use Barn2\Plugin\Easy_Post_Types_Fields\Util,
	Barn2\EPT_Lib\Plugin\Simple_Plugin,
	Barn2\EPT_Lib\Registerable,
	Barn2\EPT_Lib\Service,
	Barn2\EPT_Lib\Admin\Plugin_Promo;

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
	 * @var Simple_Plugin
	 */
	private $plugin;

	/**
	 * A list of errors triggered during validation
	 *
	 * @var WP_Error
	 */
	private $errors;

	public function __construct( Simple_Plugin $plugin ) {
		$this->plugin = $plugin;
		$this->errors = new WP_Error();
	}

	public function register() {
		register_post_type(
			'ept_post_type',
			[
				'labels'               => [
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
				'description'          => __( 'Define a custom post type', 'easy-post-types-fields' ),
				'public'               => false,
				'exclude_from_search'  => true,
				'publicly_queryable'   => false,
				'show_in_menu'         => false,
				'show_in_nav_menus'    => false,
				'show_in_admin_bar'    => false,
				'show_in_rest'         => false,
				'menu_position'        => null,
				'menu_icon'            => '',
				'supports'             => [ 'title' ],
				'register_meta_box_cb' => [ $this, 'register_cpt_metabox' ],
				'taxonomies'           => [],
				'rewrite'              => false,
				'query_var'            => false,
				'can_export'           => false,
				'delete_with_user'     => false,
			]
		);

		add_action( 'admin_enqueue_scripts', [ $this, 'load_scripts' ] );
		add_filter( 'enter_title_here', [ $this, 'change_title_text' ] );
		add_action( 'edit_form_after_title', [ $this, 'print_name_fields' ], 1 );
		add_filter( 'views_edit-ept_content_type', '__return_empty_array' );
		add_filter( 'bulk_actions-edit-ept_content_type', '__return_empty_array' );
		add_filter( 'disable_months_dropdown', [ $this, 'disable_months_dropdown' ], 10, 2 );
		add_filter( 'manage_edit-ept_content_type_columns', [ $this, 'manage_columns' ] );
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_filter( 'wp_insert_post_data', [ $this, 'save_post_type_data' ], 10, 3 );

		add_action( 'wp_ajax_ept_inline_delete', [ $this, 'inline_delete' ] );

		add_action( 'admin_init', [ $this, 'save_post_data' ] );
		add_action( 'admin_notices', [ $this, 'admin_notices' ] );

		( new Plugin_Promo( $this->plugin ) )->register();
	}

	public function change_title_text( $title ) {
		$screen = get_current_screen();

		if ( 'ept_post_type' === $screen->post_type ) {
			$title = __( 'Singular name of the post type (e.g. `Project` or `Book`)', 'easy-post-types-fields' );
		}

		return $title;
	}

	public function save_post_type_data( $data, $postdata, $rawpostdata ) {
		if ( isset( $postdata['ept_plural_name'] ) ) {
			update_post_meta( $postdata['ID'], '_ept_plural_name', $postdata['ept_plural_name'] );
			flush_rewrite_rules();
		}

		return $data;
	}

	public function register_cpt_metabox() {
		add_meta_box( 'ept_content_types', __( 'Definition', 'easy-post-types-fields' ), [ $this, 'cpt_metabox_content' ], 'ept_content_types', 'normal', 'default' );
	}

	public function cpt_metabox_content( $post ) {
		?>
		<h1>The metabox content goes here!</h1>
		<?php
	}

	public function print_name_fields( $post ) {
		if ( 'ept_post_type' !== get_post_type( $post ) ) {
			return;
		}

		$plural_name = get_post_meta( $post->ID, '_ept_plural_name', true );
		$placeholder = __( 'Plural name of the post type (e.g. `Projects` or `Books`)', 'easy-post-types-fields' );

		?>
		<div id="ept_plural_name_wrap">
			<label class="screen-reader-text" id="plural-name-prompt-text" for="ept_plural_name">
				<?php
				echo $placeholder; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</label>
			<input type="text" name="ept_plural_name" size="30" value="<?php echo esc_attr( $plural_name ); ?>" id="ept_plural_name" spellcheck="true" autocomplete="off" />
		</div>
		<?php
	}

	public function load_scripts( $hook ) {
		$screen = get_current_screen();

		if ( in_array( $screen->id, [ 'toplevel_page_ept_post_types', 'ept_post_type', 'post-types_page_ept_post_types-help' ], true ) ) {
			wp_enqueue_script( 'ept-editor', plugin_dir_url( $this->plugin->get_file() ) . 'assets/js/admin/ept-editor.min.js', [ 'jquery', 'wp-i18n', 'wp-url' ], $this->plugin->get_version(), true );
			wp_enqueue_style( 'ept-editor', plugin_dir_url( $this->plugin->get_file() ) . 'assets/css/admin/ept-editor.min.css', [], $this->plugin->get_version() );
		}
	}

	public function disable_months_dropdown( $disable, $post_type ) {
		if ( 'ept_content_type' === $post_type ) {
			return true;
		}

		return $disable;
	}

	public function manage_columns( $column_headers ) {
		$index = array_search( 'title', array_keys( $column_headers ), true );

		$column_headers['title'] = __( 'Name', 'easy-post-types-fields' );
		unset( $column_headers['date'] );

		if ( false !== $index ) {
			$index = count( $column_headers );
		}

		$additional_columns = [
			'custom' => __( 'Custom', 'easy-post-types-fields' ),
		];

		return array_merge(
			array_slice( $column_headers, 0, $index ),
			$additional_columns,
			array_slice( $column_headers, $index )
		);
	}

	public function admin_menu() {
		add_menu_page( 'Post Types', 'Post Types', 'manage_options', 'ept_post_types', [ $this, 'add_manage_page' ], 'dashicons-feedback', 26 );
		add_submenu_page( 'ept_post_types', 'Manage', 'Manage', 'manage_options', 'ept_post_types', [ $this, 'add_manage_page' ] );
		add_submenu_page( 'ept_post_types', 'Help', 'Help', 'manage_options', 'ept_post_types-help', [ $this, 'add_help_page' ] );
	}

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

	public function add_manage_page() {
		$page_title  = __( 'Easy Post Types and Fields', 'easy-post-types-fields' );
		$plugin      = $this->plugin;
		$breadcrumbs = Util::get_page_breadcrumbs();
		$request     = Util::get_page_request();
		$content     = isset( $request['section'] ) ? 'lists' : 'post_types';
		$section     = isset( $request['section'] ) ? $request['section'] : 'add';
		$new_link    = add_query_arg(
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

		}

		if ( isset( $request['action'] ) ) {
			$current_action = 'add' === $request['action'] ?
				__( 'Add', 'easy-post-types-fields' ) :
				__( 'Edit', 'easy-post-types-fields' );

			$page_title = 'taxonomies' === $request['section'] ?
				// translators: either 'Add' or 'Edit'
				__( '%s taxonomy', 'easy-post-types-fields' ) :
				// translators: either 'Add' or 'Edit'
				__( '%s custom field', 'easy-post-types-fields' );

			$page_title       = sprintf( $page_title, $current_action );
			$page_description = '';
		}

		include $this->plugin->get_admin_path( 'views/html-manage-page.php' );
	}

	public function add_help_page() {
		$plugin = $this->plugin;

		include $this->plugin->get_admin_path( 'views/html-help-page.php' );
	}

	public function admin_notices() {
		if ( ! $this->errors->has_errors() ) {
			return;
		}

		?>
		<div class="error notice">
			<?php
			foreach ( $this->errors->get_error_messages() as $error ) {
				?>
				<p><?php echo $error; ?></p>
				<?php
			}
			?>
		</div>
		<?php
	}


	public function inline_delete() {
		check_ajax_referer( 'inlinedeletenonce', '_inline_delete' );

		$post_data        = $_POST;
		$type             = $post_data['type'];
		$meta_key         = 'taxonomy' === $type ? '_ept_taxonomies' : '_ept_fields';
		$post_type_object = Util::get_post_type_object( $post_data['post_type'] );

		if ( $post_type_object ) {
			$items = get_post_meta( $post_type_object->ID, $meta_key, true );

			if ( ! $items ) {
				$items = [];
			}

			$new_items = array_filter(
				$items,
				function( $item ) use ( $post_data ) {
					return $item['slug'] !== $post_data['slug'];
				}
			);

			update_post_meta( $post_type_object->ID, $meta_key, $new_items );
			wp_send_json_success();
		}

		wp_send_json_error( [ 'error_message' => __( 'The post type is missing or an error occurred when completing this operation.', 'easy-post-types-fields' ) ] );
	}

	public function inline_delete_taxonomy( $post_data ) {
	}

	public function save_post_data() {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'save_list_item_postdata' ) ) {
			return;
		}

		$this->errors = new WP_Error();

		$postdata  = $_POST;
		$request   = Util::get_page_request();
		$data_type = 'post_type';

		if ( isset( $request['section'] ) ) {
			$data_type = $request['section'];
		}

		$this->{"save_$data_type"}( $postdata, $request );
	}

	public function save_post_type( $data, $request ) {
		$post_type_id     = 0;
		$post_type_object = Util::get_post_type_object( $request['post_type'] );

		if ( $post_type_object ) {
			$post_type_id = $post_type_object->ID;
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

		$args = [
			'ID'             => $post_type_id,
			'post_title'     => $data['singular_name'],
			'post_name'      => $data['slug'],
			'post_type'      => 'ept_post_type',
			'post_status'    => 'publish',
			'comment_status' => 'closed',
			'meta_input'     => [
				'_ept_plural_name' => filter_var( $data['name'], FILTER_DEFAULT ),
				'_ept_supports'    => array_keys( $data['supports'] ),
			],
		];

		$post_type_id = wp_insert_post( $args, false, false );

		if ( is_wp_error( $post_type_id ) ) {
			$this->errors->add( $post_type_id->get_code(), $post_type_id->get_message() );
			return;
		}

		Util::set_update_transient( $request['post_type'] );
		wp_safe_redirect( Util::get_manage_page_url() );
	}

	public function save_taxonomies( $data, $request ) {
		$post_type_object = Util::get_post_type_object( $request['post_type'] );

		if ( $post_type_object ) {
			$taxonomies = get_post_meta( $post_type_object->ID, '_ept_taxonomies', true );

			if ( ! $taxonomies ) {
				$taxonomies = [];
			}

			$new_taxonomy     = [
				'name'          => $data['name'],
				'singular_name' => $data['singular_name'],
				'slug'          => sanitize_title( $data['slug'] ),
				'hierarchical'  => filter_var( $data['hierarchical'], FILTER_VALIDATE_BOOLEAN ),
				'post_type'     => $request['post_type'],
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
				$conflicting_taxonomies = array_filter(
					$other_taxonomies,
					function( $t ) use ( $slug ) {
						return $t['slug'] === $slug;
					}
				);

				if ( ! empty( $conflicting_taxonomies ) ) {
					$this->errors->add( 'conflicting_taxonomy', __( 'A taxonomy with the same slug is already registered to this post type. Please choose a different slug.', 'easy-post-types-fields' ) );
				}
			}

			if ( $this->errors->has_errors() ) {
				return;
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

	public function save_fields( $data, $request ) {
		$post_type_object = Util::get_post_type_object( $request['post_type'] );

		if ( $post_type_object ) {
			$fields = get_post_meta( $post_type_object->ID, '_ept_fields', true );

			if ( ! $fields ) {
				$fields = [];
			}

			$new_field    = [
				'name'      => $data['name'],
				'slug'      => sanitize_title( $data['slug'] ),
				'type'      => $data['type'],
				'post_type' => $request['post_type'],
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
				}
			}

			if ( $this->errors->has_errors() ) {
				return;
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
