<?php
namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

use Barn2\Plugin\Easy_Post_Types_Fields\Util,
	Barn2\EPT_Lib\Plugin\Simple_Plugin,
	Barn2\EPT_Lib\Registerable,
	Barn2\EPT_Lib\Service,
	Barn2\EPT_Lib\Admin\Plugin_Promo;
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

	public function __construct( Simple_Plugin $plugin ) {
		$this->plugin = $plugin;
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
		add_filter( 'wp_insert_post_data', [ $this, 'save_post_type' ], 10, 3 );

		( new Plugin_Promo( $this->plugin ) )->register();
	}

	public function change_title_text( $title ) {
		$screen = get_current_screen();

		if ( 'ept_post_type' === $screen->post_type ) {
			$title = __( 'Singular name of the post type (e.g. `Project` or `Book`)', 'easy-post-types-fields' );
		}

		return $title;
	}

	public function save_post_type( $data, $postdata, $rawpostdata ) {
		if ( isset( $postdata['ept_plural_name'] ) ) {
			update_post_meta( $postdata['ID'], '_ept_plural_name', $postdata['ept_plural_name'] );
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
			wp_enqueue_script( 'ept-editor', plugin_dir_url( $this->plugin->get_file() ) . 'assets/js/admin/ept-editor.min.js', [ 'jquery' ], $this->plugin->get_version(), true );
			wp_enqueue_style( 'ept-editor', plugin_dir_url( $this->plugin->get_file() ) . 'assets/css/admin/ept-editor.min.css', [], $this->plugin->get_version() );

			$ept_params = [
				'i18n' => [
					'confirm_delete'      => __( 'Are you sure you want to delete this post type?', 'easy-post-types-fields' ),
					'last_confirm_delete' => __( 'The database contains at least one post of this post type. By deleting this post type, WordPress will not be able to access those posts any longer. This operation cannot be undone. Are you sure you want to continue?', 'easy-post-types-fields' ),
				]
			];

			wp_add_inline_script( 'ept-editor', sprintf( 'var ept_params = %s;', json_encode( $ept_params ) ), 'before' );
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

	public function print_page_description() {
		?>
		<p>
			<?php esc_html_e( 'Use this page to manage your custom post types. You can add and edit post types, custom fields and taxonomies.', 'easy-post-types-fields' ); ?>
		</p>
		<?php
	}

	public function admin_menu() {
		add_menu_page( 'Post Types', 'Post Types', 'manage_options', 'ept_post_types', [ $this, 'add_manage_page' ], 'dashicons-feedback', 21 );
		add_submenu_page( 'ept_post_types', 'Manage', 'Manage', 'manage_options', 'ept_post_types', [ $this, 'add_manage_page' ] );
		add_submenu_page( 'ept_post_types', 'Help', 'Help', 'manage_options', 'ept_post_types-help', [ $this, 'add_help_page' ] );
	}

	public function add_manage_page() {
		$plugin      = $this->plugin;
		$breadcrumbs = Util::get_page_breadcrumbs();
		$request     = Util::get_page_request();
		$content     = isset( $request['section'] ) ? $request['section'] : 'post_types';
		$new_link    = add_query_arg(
			[
				'page'   => isset( $request['section'] ) ? $request['page'] : $plugin->get_slug() . '-setup-wizard',
				'action' => 'add',
			],
			'admin.php'
		);

		if ( isset( $request['section'] ) ) {
			$new_link = add_query_arg(
				[
					'post_type' => $request['post_type'],
					'section'   => $request['section'],
				],
				$new_link
			);
		}

		if ( 'post_types' === $content && isset( $request['post_type'] ) ) {
			$content = 'post_type';
		}

		include $this->plugin->get_admin_path( 'views/html-manage-page.php' );
	}

	public function add_help_page() {
		$plugin = $this->plugin;

		include $this->plugin->get_admin_path( 'views/html-help-page.php' );
	}
}
