<?php
namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin\List_Tables;

use Barn2\Plugin\Easy_Post_Types_Fields\Util;
use Barn2\EPT_Lib\Admin\Settings_API_Helper;
use WP_List_Table;
use WP_Query;

/**
 * List Table API: WP_Posts_List_Table class
 *
 * @package WordPress
 * @subpackage Administration
 * @since 3.1.0
 */

/**
 * Core class used to implement displaying posts in a list table.
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
	 * A list of post types created by this plugin
	 *
	 * @var array
	 */
	private $custom_post_types;

	public function __construct( $args = [] ) {
		global $wp_post_types;

		parent::__construct(
			array(
				'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
			)
		);

		$this->post_type      = 'ept_post_type';
		$this->all_post_types = $wp_post_types;
		unset( $this->all_post_types['ept_post_type'] );

		$ept_post_types          = new WP_Query( [ 'post_type' => 'ept_post_type', 'posts_per_page' => -1 ] );
		$ept_post_types          = $ept_post_types->posts;
		$this->custom_post_types = array_map(
			function( $cpt ) {
				return "ept_$cpt->post_name";
			},
			$ept_post_types
		);

		$this->post_types = $this->get_filtered_post_types( $this->get_current_view() );

		usort(
			$this->post_types,
			function( $pt_1, $pt_2 ) {
				return $pt_1->labels->singular_name > $pt_2->labels->singular_name ? 1 : -1;
			}
		);

		usort(
			$this->post_types,
			function( $pt_1, $pt_2 ) {
				return ! $this->is_custom( $pt_1 ) && $this->is_custom( $pt_2 ) ? 1 : -1;
			}
		);
	}

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
						return $pt->publicly_queryable;

					case 'other':
						return $pt->publicly_queryable && ! $this->is_custom( $pt );

					case 'all':
						return true;

					case 'ept':
					default:
						return $this->is_custom( $pt );
				}
			}
		);
	}

	public function get_current_view() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		return isset( $_REQUEST['view'] ) ? $_REQUEST['view'] : 'ept';
		// phpcs:enable
	}

	public function prepare_items() {
		$per_page    = apply_filters( 'edit_ept_post_types_per_page', $this->get_items_per_page( 'edit_ept_post_types_per_page' ) );
		$total_items = count( $this->post_types );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);
	}

	public function is_custom( $post_type ) {
		return in_array( $post_type->name, $this->custom_post_types, true );
	}

	public function has_items() {
		return count( $this->post_types );
	}

	public function no_items() {
		esc_html_e( 'No custom post types found', 'easy-post-types-fields' );
	}

	protected function get_view_page_link( $view, $label, $class = '' ) {
		$view = 'ept' === $view ? '' : $view;
		$args = [];

		if ( $view ) {
			$args['view'] = $view;
		}

		$url = Util::get_manage_page_url( $args );

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

	protected function get_views() {
		$views = [
			/* translators: %s: Number of posts. */
			'ept'   => __( 'Easy Post Types <span class="count">(%s)</span>', 'easy-post-types-fields' ),
			/* translators: %s: Number of posts. */
			'other' => __( 'Other post types <span class="count">(%s)</span>', 'easy-post-types-fields' ),
		];

		foreach ( $views as $view => $label ) {
			$status_links[ $view ] = $this->get_status_link(
				$view,
				$label
			);
		}

		return $status_links;
	}

	protected function get_bulk_actions() {
		return [];
	}

	public function current_action() {
		if ( isset( $_REQUEST['delete_all'] ) || isset( $_REQUEST['delete_all2'] ) ) {
			return 'delete_all';
		}

		return parent::current_action();
	}

	protected function get_table_classes() {
		global $mode;

		$mode_class = esc_attr( 'table-view-' . $mode );

		return [
			'widefat',
			'striped',
		];
	}

	public function get_columns() {
		$action_tooltip = Util::get_tooltip( __( 'Use custom fields for storing unique data about your custom posts, and use taxonomies for organizing and grouping the custom posts.', 'easy-post-types-fields' ) );

		$columns = [
			'name'       => _x( 'Name', 'column name', 'easy-post-types-fields' ),
			'fields'     => _x( 'Custom Fields', 'column name', 'easy-post-types-fields' ),
			'taxonomies' => _x( 'Taxonomies', 'column name', 'easy-post-types-fields' ),
			'actions'    => _x( 'Actions', 'column name', 'easy-post-types-fields' ) . $action_tooltip,
			'count'      => _x( 'Post count', 'column name', 'easy-post-types-fields' ),
		];

		return apply_filters( 'manage_ept_post_types_columns', $columns );
	}

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

	protected function get_sortable_columns() {
		return [];
	}

	public function display_rows() {
		if ( empty( $this->post_types ) ) {
			$this->post_types = get_post_types( [ 'public' => true ] );
		}

		foreach ( $this->post_types as $post_type ) {
			$this->single_row( $post_type );
		}
	}

	protected function _column_lock( $post_type ) {
		?>
		<th scope="row" class="check-column">
			<?php
			if ( ! $this->is_custom( $post_type ) ) {
				?>
				<div class="locked-indicator" style="margin-top:-5px;">
					<span class="locked-indicator-icon" aria-hidden="true"></span>
					<span class="screen-reader-text">
						<?php
						_e( 'This post type is locked', 'easy-post-types-fields' ); // phpcs:ignore WordPress.Security.EscapeOutput.UnsafePrintingFunction
						?>
					</span>
				</div>
				<?php
			}
			?>
		</th>
		<?php
	}

	protected function _column_name( $post_type, $classes, $data, $primary ) {
		?>
		<td class="<?php echo esc_attr( $classes ); ?> post_type-name" <?php echo $data; ?>>
			<?php
			if ( $this->is_custom( $post_type ) ) {
				printf(
					'<a class="row-title" href="%s" aria-label="%s">%s</a>',
					$this->get_edit_post_link( $post_type ),
					esc_attr( sprintf( __( '%s (Edit)', 'easy-post-types-fields' ), $post_type->labels->singular_name ) ),
					esc_attr( $post_type->labels->singular_name )
				);
			} else {
				echo $post_type->labels->singular_name;
			}

			echo $this->handle_row_actions( $post_type, 'name', $primary );
			?>
		</td>
		<?php
	}

	protected function _column_actions( $post_type, $classes, $data, $primary ) {
		$fields_link = Util::get_manage_page_url( [], $post_type, 'fields' );
		$tax_link    = Util::get_manage_page_url( [], $post_type, 'taxonomies' );
		$all_link    = add_query_arg( 'post_type', $post_type->name, admin_url( 'edit.php' ) );

		?>
		<td class="<?php echo esc_attr( $classes ); ?> post_type-actions" <?php echo esc_attr( $data ); ?>>
			<a href="<?php echo esc_attr( $fields_link ); ?>" class="button"><?php esc_html_e( 'Custom Fields', 'easy-post-types-fields' ); ?></a>
			<a href="<?php echo esc_attr( $tax_link ); ?>" class="button"><?php esc_html_e( 'Taxonomies', 'easy-post-types-fields' ); ?></a>
			<?php // translators: the plural name of a post type ?>
			<a href="<?php echo esc_attr( $all_link ); ?>" class="button"><?php echo esc_html( sprintf( __( 'All %s', 'easy-post-types-fields' ), $post_type->label ) ); ?></a>
		</td>
		<?php
	}

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

	protected function _column_count( $post_type, $classes, $data, $primary ) {
		$count_link = sprintf(
			'<a href="%s">%s</a>',
			add_query_arg( 'post_type', $post_type->name, admin_url( 'edit.php' ) ),
			$this->get_post_count( $post_type )
		);

		?>
		<td class="<?php echo esc_attr( $classes ); ?> post_type-actions" <?php echo esc_attr( $data ); ?>>
			<?php echo $count_link; ?>
		</td>
		<?php
	}

	public function column_default( $item, $column_name ) {
	}

	public function single_row( $post_type ) {
		$class = '';

		if ( ! $this->is_custom( $post_type ) ) {
			$class = 'wp-locked';
		}

		?>
		<tr id="post_type-<?php echo $post_type->name; ?>" class="<?php echo esc_attr( $class ); ?>">
			<?php $this->single_row_columns( $post_type ); ?>
		</tr>
		<?php
	}

	protected function get_primary_column_name() {
		return 'name';
	}

	protected function handle_row_actions( $post_type, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return '';
		}

		// Restores the more descriptive, specific name for use within this method.
		$can_edit_post_type = current_user_can( 'manage_options' );
		$actions            = [];

		if ( $can_edit_post_type && $this->is_custom( $post_type ) ) {
			$actions['edit'] = sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				Util::get_manage_page_url( [], $post_type ),
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

		$actions['fields'] = sprintf(
			'<a href="%s" aria-label="%s">%s</a>',
			Util::get_manage_page_url( [], $post_type, 'fields' ),
			esc_attr( __( 'Custom Fields', 'easy-post-types-fields' ) ),
			__( 'Custom Fields', 'easy-post-types-fields' )
		);

		$actions['taxonomies'] = sprintf(
			'<a href="%s" aria-label="%s">%s</a>',
			Util::get_manage_page_url( [], $post_type, 'taxonomies' ),
			esc_attr( __( 'Taxonomies', 'easy-post-types-fields' ) ),
			__( 'Taxonomies', 'easy-post-types-fields' )
		);

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

	public function get_delete_post_link( $post_type ) {
		if ( $this->is_custom( $post_type ) ) {
			$post_type_object = Util::get_post_type_object( $post_type );

			if ( $post_type_object ) {
				return get_delete_post_link( $post_type_object->ID, '', true );
			}

			return false;
		}

		return false;
	}

	public function display() {
		$singular = $this->_args['singular'];

		$this->screen->render_screen_reader_content( 'heading_list' );

		?>
		<table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
			<thead>
				<tr>
					<?php $this->print_column_headers(); ?>
				</tr>
			</thead>

			<tbody id="the-list"
				<?php
				if ( $singular ) {
					echo " data-wp-lists='list:$singular'";
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
