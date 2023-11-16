<?php
/**
 * Post Type list table, extending the WP_List_Table class
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin\List_Tables;

use Barn2\Plugin\Easy_Post_Types_Fields\Util;
use WP_List_Table;
use WP_Query;

/**
 * Class used to implement displaying post typess in a list table.
 */
class Post_Type_List_Table extends WP_List_Table {
	/**
	 * All the post types registered in WordPress
	 *
	 * @var array
	 */
	protected $all_post_types;

	/**
	 * The post types being shown in the current view
	 *
	 * @var array
	 */
	protected $post_types;


	/**
	 * The current post type
	 *
	 * @var string
	 */
	protected $post_type;


	/**
	 * Constructor
	 *
	 * @param array $args The arguments to build the list table
	 * @return void
	 */
	public function __construct( $args = [] ) {
		parent::__construct(
			[
				'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
			]
		);

		global $wp_post_types;
		$this->post_type      = 'ept_post_type';
		$this->all_post_types = $wp_post_types;
		unset( $this->all_post_types['ept_post_type'] );

		$this->post_types = $this->get_filtered_post_types( $this->get_current_view() );

		usort(
			$this->post_types,
			function( $pt_1, $pt_2 ) {
				return $pt_1->labels->name > $pt_2->labels->name ? 1 : -1;
			}
		);

		usort(
			$this->post_types,
			function( $pt_1, $pt_2 ) {
				return ! $this->is_custom( $pt_1 ) && $this->is_custom( $pt_2 ) ? 1 : -1;
			}
		);
	}

	/**
	 * Get an array of post types pertinent to a view passed as a parameter
	 *
	 * @param  string $view The current post type view
	 * @return array
	 */
	public function get_filtered_post_types( $view = '' ) {
		return array_filter(
			$this->all_post_types,
			function( $pt ) use ( $view ) {
				switch ( $view ) {
					case 'top':
						return $pt->show_in_menu;

					case 'public':
						return $pt->public || $pt->show_in_menu;

					case 'common':
						return $pt->public;

					case 'other':
						return $pt->public && ! $this->is_custom( $pt );

					case 'all':
						return true;

					case 'ept':
					default:
						return $this->is_custom( $pt );
				}
			}
		);
	}

	/**
	 * Return the `view` query argument of the current request
	 *
	 * @return string
	 */
	public function get_current_view() {
		$request = Util::get_page_request();
		return isset( $request['view'] ) ? $request['view'] : 'ept';
	}

	/**
	 * {@inheritdoc}
	 */
	public function prepare_items() {
		/**
		 * Filter the number of items per page for the post type list table
		 *
		 * @param int $items_per_page The number of items per page
		 */
		$per_page    = apply_filters( 'edit_ept_post_types_per_page', $this->get_items_per_page( 'edit_ept_post_types_per_page' ) );
		$total_items = count( $this->post_types );

		$this->set_pagination_args(
			[
				'total_items' => $total_items,
				'per_page'    => $per_page,
			]
		);
	}

	/**
	 * Determine whether a post type is registered by EPT (i.e. custom)
	 * or by WordPress or a third-party plugin
	 *
	 * @param  WP_Post_Type $post_type
	 * @return boolean
	 */
	public function is_custom( $post_type ) {
		$post_type_object = Util::get_post_type_object( $post_type );
		return 'publish' === $post_type_object->post_status;
	}

	/**
	 * {@inheritdoc}
	 */
	public function has_items() {
		return count( $this->post_types );
	}

	/**
	 * {@inheritdoc}
	 */
	public function no_items() {
		esc_html_e( 'No custom post types found', 'easy-post-types-fields' );
	}

	/**
	 * Get the HTML markup of the manage page link with the requested view
	 *
	 * @param  string $view The value of the view query argument
	 * @param  string $label The text of the view link
	 * @param  string $class The classes of the anchor element
	 * @return string
	 */
	protected function get_view_page_link( $view, $label, $class = '' ) {
		$view = 'ept' === $view ? false : $view;
		$url  = Util::get_manage_page_url( '', '', '', '', $view );

		$class_html   = '';
		$aria_current = '';

		if ( ! empty( $class ) ) {
			$class_html = sprintf(
				' class="%s"',
				esc_attr( $class )
			);

			if ( 'current' === $class ) {
				$aria_current = ' aria-current="page"';
			}
		}

		return sprintf(
			'<a href="%s"%s%s>%s</a>',
			esc_url( $url ),
			$class_html,
			$aria_current,
			$label
		);
	}

	/**
	 * Get a single link for the status links array of the list table
	 *
	 * @param  string $view The value of the view query argument
	 * @param  string $label The text of the view link
	 * @return string
	 */
	public function get_status_link( $view, $label ) {
		$class           = $view === $this->get_current_view() ? 'current' : '';
		$view_post_types = $this->get_filtered_post_types( $view );
		$post_type_count = count( $view_post_types );
		$innter_html     = sprintf(
			$label,
			number_format_i18n( $post_type_count )
		);

		return $this->get_view_page_link( $view, $innter_html, $class );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_views() {
		$views = [
			/* translators: %s: Number of posts. */
			'ept'   => __( 'Easy Post Types <span class="count">(%s)</span>', 'easy-post-types-fields' ),
			/* translators: %s: Number of posts. */
			'other' => __( 'Other Post Types <span class="count">(%s)</span>', 'easy-post-types-fields' ),
		];

		foreach ( $views as $view => $label ) {
			$status_links[ $view ] = $this->get_status_link(
				$view,
				$label
			);
		}

		return $status_links;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_bulk_actions() {
		return [];
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_table_classes() {
		global $mode;

		$mode_class = esc_attr( 'table-view-' . $mode );

		return [
			'widefat',
			'striped',
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_columns() {
		$slug_tooltip   = Util::get_tooltip( __( 'The slug is a unique code that you can use to identify the custom post type. For example, you can use it to display the data with the Posts Table Pro plugin. If you are using the slug in other ways &ndash; for example for development purposes &ndash; then you should add the prefix \'ept_\' before the slug, for example \'ept_article\' instead of just \'article\'.', 'easy-post-types-fields' ) );
		$action_tooltip = Util::get_tooltip( __( 'Use custom fields for storing unique data about your custom posts, and use taxonomies for organizing and grouping the custom posts.', 'easy-post-types-fields' ) );
		$count_tooltip  = Util::get_tooltip( __( 'The current number of posts for the custom post type.', 'easy-post-types-fields' ) );

		$columns = [
			'name'       => _x( 'Name', 'column name', 'easy-post-types-fields' ),
			'slug'       => _x( 'Slug', 'column name', 'easy-post-types-fields' ) . ( 'ept' === $this->get_current_view() ? $slug_tooltip : '' ),
			'fields'     => _x( 'Custom Fields', 'column name', 'easy-post-types-fields' ),
			'taxonomies' => _x( 'Taxonomies', 'column name', 'easy-post-types-fields' ),
			'actions'    => _x( 'Actions', 'column name', 'easy-post-types-fields' ) . $action_tooltip,
			'count'      => _x( 'Count', 'column name', 'easy-post-types-fields' ) . $count_tooltip,
		];

		/**
		 * Filter the heading of each column in the post type list table
		 *
		 * The array passed to the filter callback is an associative array
		 * where the keys are the name of the columns and the values are the
		 * headings. The columns in the array are presented in the same order
		 * they have in the table.
		 *
		 * @param array $columns The list of columns
		 */
		return apply_filters( 'manage_ept_post_types_columns', $columns );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_column_info() {
		if ( isset( $this->_column_headers ) && is_array( $this->_column_headers ) ) {
			/*
			 * Backward compatibility for `$_column_headers` format prior to WordPress 4.3.
			 *
			 * In WordPress 4.3 the primary column name was added as a fourth item in the
			 * column headers property. This ensures the primary column name is included
			 * in plugins setting the property directly in the three item format.
			 */
			$column_headers = [ [], [], [], $this->get_primary_column_name() ];
			foreach ( $this->_column_headers as $key => $value ) {
				$column_headers[ $key ] = $value;
			}

			return $column_headers;
		}

		$columns = $this->get_columns();

		$primary               = $this->get_primary_column_name();
		$this->_column_headers = [ $columns, [], [], $primary ];

		return $this->_column_headers;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_sortable_columns() {
		return [];
	}

	/**
	 * {@inheritdoc}
	 */
	public function display_rows() {
		if ( empty( $this->post_types ) ) {
			$this->post_types = get_post_types( [ 'public' => true ] );
		}

		foreach ( $this->post_types as $post_type ) {
			$this->single_row( $post_type );
		}
	}

	/**
	 * Output the name of the post type for the current row
	 *
	 * @param WP_Post_Type $post_type The post type in the current row
	 * @param string $classes The classes for the cell element
	 * @param string $data The extra attributes for the cell element
	 * @param string $primary The name of the primary column
	 * @return void
	 */
	protected function _column_name( $post_type, $classes, $data, $primary ) { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		?>
		<td class="<?php echo esc_attr( $classes ); ?> post_type-name" <?php echo $data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php
			if ( $this->is_custom( $post_type ) ) {
				printf(
					'<a class="row-title" href="%s" aria-label="%s">%s</a>',
					esc_url( Util::get_manage_page_url( $post_type ) ),
					// translators: a post type name
					esc_attr( sprintf( __( '%s (Edit)', 'easy-post-types-fields' ), $post_type->labels->name ) ),
					esc_attr( $post_type->labels->name )
				);
			} else {
				echo $post_type->labels->name; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			echo $this->handle_row_actions( $post_type, 'name', $primary ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</td>
		<?php
	}

	/**
	 * Output the buttons in the 'Actions' column for the current row
	 *
	 * @param WP_Post_Type $post_type The post type in the current row
	 * @param string $classes The classes for the cell element
	 * @param string $data The extra attributes for the cell element
	 * @param string $primary The name of the primary column
	 * @return void
	 */
	protected function _column_actions( $post_type, $classes, $data, $primary ) { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$fields_link = Util::get_manage_page_url( $post_type, 'fields' );
		$tax_link    = Util::get_manage_page_url( $post_type, 'taxonomies' );
		$all_link    = add_query_arg( 'post_type', $post_type->name, admin_url( 'edit.php' ) );

		?>
		<td class="<?php echo esc_attr( $classes ); ?> post_type-actions" <?php echo esc_attr( $data ); ?>>
			<a href="<?php echo esc_attr( $fields_link ); ?>" class="button"><?php esc_html_e( 'Custom Fields', 'easy-post-types-fields' ); ?></a>
			<a href="<?php echo esc_attr( $tax_link ); ?>" class="button"><?php esc_html_e( 'Taxonomies', 'easy-post-types-fields' ); ?></a>
		</td>
		<?php
	}

	/**
	 * Get the number of posts of any given post type
	 *
	 * The count excludes auto-drafts and revisions by default
	 *
	 * @param  WP_Post_Type $post_type
	 * @return int
	 */
	public function get_post_count( $post_type ) {
		$post_count = (array) wp_count_posts( $post_type->name );
		unset( $post_count['auto-draft'], $post_count['revision'] );

		return array_reduce(
			$post_count,
			function( $r, $a ) {
				return $r + $a;
			},
			0
		);

	}

	/**
	 * Output the slug of the current post type
	 *
	 * @param WP_Post_Type $post_type The post type in the current row
	 * @return void
	 */
	protected function column_slug( $post_type ) {
		echo esc_html( str_replace( 'ept_', '', $post_type->name ) );
	}

	/**
	 * Output the taxonomies of the current post type
	 *
	 * Taxonomies are presented as links to each respective edit page
	 *
	 * @param WP_Post_Type $post_type The post type in the current row
	 * @return void
	 */
	protected function column_taxonomies( $post_type ) {
		$post_type_object = Util::get_post_type_object( $post_type );

		if ( ! $post_type_object ) {
			return;
		}

		$taxonomies = get_post_meta( $post_type_object->ID, '_ept_taxonomies', true );

		if ( empty( $taxonomies ) ) {
			$taxonomies = '—';
		} else {
			$taxonomies = array_map(
				function( $t ) use ( $post_type ) {
					return sprintf(
						'<a href="%1$s">%2$s</a>',
						Util::get_manage_page_url( $post_type->name, 'taxonomies', $t['slug'], 'edit' ),
						$t['name']
					);
				},
				$taxonomies
			);
			$taxonomies = implode( ', ', $taxonomies );
		}

		echo wp_kses_post( $taxonomies );
	}

	/**
	 * Output the custom fields of the current post type
	 *
	 * Custom fields are presented as links to each respective edit page
	 *
	 * @param WP_Post_Type $post_type The post type in the current row
	 * @return void
	 */
	protected function column_fields( $post_type ) {
		$post_type_object = Util::get_post_type_object( $post_type );

		if ( ! $post_type_object ) {
			return;
		}

		$fields = get_post_meta( $post_type_object->ID, '_ept_fields', true );

		if ( empty( $fields ) ) {
			$fields = '—';
		} else {
			$fields = array_map(
				function( $f ) use ( $post_type ) {
					return sprintf(
						'<a href="%1$s">%2$s</a>',
						Util::get_manage_page_url( $post_type->name, 'fields', $f['slug'], 'edit' ),
						$f['name']
					);
				},
				$fields
			);
			$fields = implode( ', ', $fields );
		}

		echo wp_kses_post( $fields );
	}

	/**
	 * Output the number of posts of the current post type
	 *
	 * Each number is presented as a link to the list table for the current post type
	 *
	 * @param WP_Post_Type $post_type The post type in the current row
	 * @return void
	 */
	protected function column_count( $post_type ) {
		$count_link = sprintf(
			'<a href="%s">%s</a>',
			add_query_arg( 'post_type', $post_type->name, admin_url( 'edit.php' ) ),
			$this->get_post_count( $post_type )
		);

		echo wp_kses_post( $count_link );
	}

	/**
	 * Output a single row of the table
	 *
	 * @param WP_Post_Type $post_type The post type associated with the current row
	 * @return void
	 */
	public function single_row( $post_type ) {
		$class = '';

		?>
		<tr id="post_type-<?php echo esc_attr( $post_type->name ); ?>" class="<?php echo esc_attr( $class ); ?>">
			<?php $this->single_row_columns( $post_type ); ?>
		</tr>
		<?php
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_primary_column_name() {
		return 'name';
	}

	/**
	 * Add the actions for the current row in the primary column
	 *
	 * @param WP_Post_Type $post_type The post type associated with the current row
	 * @param string $column_name The name of the current column
	 * @param string $primary The name of the primary column
	 */
	protected function handle_row_actions( $post_type, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return '';
		}

		$can_edit_post_type = current_user_can( 'manage_options' );
		$actions            = [];

		if ( $can_edit_post_type && $this->is_custom( $post_type ) ) {
			$actions['edit'] = sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				Util::get_manage_page_url( $post_type ),
				esc_attr( __( 'Edit', 'easy-post-types-fields' ) ),
				__( 'Edit', 'easy-post-types-fields' )
			);

			$actions['delete'] = sprintf(
				'<a href="%s" class="post-type-delete" aria-label="%s" data-post_count="%d">%s</a>',
				$this->get_delete_post_link( $post_type ),
				esc_attr( __( 'Delete', 'easy-post-types-fields' ) ),
				$this->get_post_count( $post_type ),
				__( 'Delete', 'easy-post-types-fields' )
			);
		}

		$actions['manage'] = sprintf(
			'<a href="%s" aria-label="%s">%s</a>',
			add_query_arg( 'post_type', $post_type->name, admin_url( 'edit.php' ) ),
			/* translators: %s: Post type plural name. */
			sprintf( esc_attr__( 'All %s', 'easy-post-types-fields' ), $post_type->label ),
			/* translators: %s: Post type plural name. */
			sprintf( esc_html__( 'All %s', 'easy-post-types-fields' ), $post_type->label )
		);

		return $this->row_actions( $actions );
	}

	/**
	 * Get the URL for the Edit row action link
	 *
	 * @param  WP_Post_Type $post_type The current row item
	 * @return string
	 */
	public function get_edit_post_link( $post_type ) {
		if ( $this->is_custom( $post_type ) ) {
			$posts = get_posts(
				[
					'post_type' => 'ept_post_type',
					'name'      => str_replace( 'ept_', '', $post_type->name ),
				]
			);

			if ( empty( $posts ) ) {
				return false;
			}

			$post = reset( $posts );

			return get_edit_post_link( $post->ID );
		}

		return false;
	}

	/**
	 * Get the URL for the Delete row action link
	 *
	 * @param  WP_Post_Type $post_type The current row item
	 * @return string|boolean
	 */
	public function get_delete_post_link( $post_type ) {
		if ( $this->is_custom( $post_type ) ) {
			$post_type_object = Util::get_post_type_object( $post_type );

			if ( $post_type_object ) {
				$delete_link = add_query_arg( 'action', 'delete', admin_url( sprintf( 'post.php?post=%d', $post_type_object->ID ) ) );
				return wp_nonce_url( $delete_link, "delete-post_{$post_type_object->ID}" );
			}

			return false;
		}

		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function display() {
		$singular = $this->_args['singular'];

		$this->screen->render_screen_reader_content( 'heading_list' );

		?>
		<table class="wp-list-table <?php echo esc_attr( implode( ' ', $this->get_table_classes() ) ); ?>">
			<thead>
				<tr>
					<?php $this->print_column_headers(); ?>
				</tr>
			</thead>

			<tbody id="the-list"
				<?php
				if ( $singular ) {
					echo " data-wp-lists='list:$singular'"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
				>
				<?php $this->display_rows_or_placeholder(); ?>
			</tbody>
		</table>

		<?php
	}
	/**
	 * Outputs the hidden row displayed when inline editing
	 *
	 * @since 3.1.0
	 *
	 * @global string $mode List table view mode.
	 */
	public function inline_edit() {
	}
}
