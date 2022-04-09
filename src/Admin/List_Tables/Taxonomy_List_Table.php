<?php
namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin\List_Tables;

use Barn2\Plugin\Easy_Post_Types_Fields\Util;
use WP_List_Table;

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
class Taxonomy_List_Table extends WP_List_Table {

	/**
	 * The post type the taxonomies are assigned to
	 *
	 * @var WP_Post_Type
	 */
	private $post_type;

	/**
	 * The taxonomies of the current post type
	 *
	 * @var array
	 */
	protected $taxonomies;

	public function __construct( $post_type ) {
		parent::__construct(
			[
				'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
			]
		);

		$this->post_type  = $post_type;
		$post_type_object = Util::get_post_type_object( $post_type );
		$taxonomies       = get_post_meta( $post_type_object->ID, '_ept_taxonomies', true );
		$this->taxonomies = $taxonomies ?: [];
	}

	public function prepare_items() {
		$per_page    = apply_filters( 'edit_ept_taxonomies_per_page', $this->get_items_per_page( 'edit_ept_taxonomies_per_page' ) );
		$total_items = count( $this->taxonomies );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);
	}

	public function has_items() {
		return count( $this->taxonomies );
	}

	public function no_items() {
		esc_html_e( 'No taxonomies for this post type yet', 'easy-post-types-fields' );
	}

	protected function get_views() {
		return [];
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
		$columns = [
			'name' => _x( 'Name', 'column name', 'easy-post-types-fields' ),
		];

		return apply_filters( 'manage_ept_taxonomies_columns', $columns );
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
		$this->_column_headers = array( $columns, [], [], $primary );

		return $this->_column_headers;
	}

	protected function get_sortable_columns() {
		return [];
	}

	public function display_rows() {
		if ( empty( $this->taxonomies ) ) {
			$this->taxonomies = get_post_types( [ 'public' => true ] );
		}

		foreach ( $this->taxonomies as $taxonomt ) {
			$this->single_row( $taxonomy );
		}
	}

	protected function _column_lock( $taxonomy ) {
		?>
		<th scope="row" class="check-column">
			<?php
			if ( ! $this->is_custom( $taxonomy ) ) {
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

	protected function _column_name( $taxonomy, $classes, $data, $primary ) {
		?>
		<td class="<?php echo esc_attr( $classes ); ?> post_type-name" <?php echo esc_attr( $data ); ?>>
			<?php
			if ( $this->is_custom( $$taxonomy ) ) {
				printf(
					'<a class="row-title" href="%s" aria-label="%s">%s</a>',
					$this->get_edit_post_link( $taxonomy ),
					esc_attr( sprintf( __( '%s (Edit)', 'easy-post-types-fields' ), $taxonomy->labels->singular_name ) ),
					esc_attr( $taxonomy->labels->singular_name )
				);
			} else {
				echo $taxonomy->labels->singular_name;
			}

			echo $this->handle_row_actions( $taxonomy, 'name', $primary );
			?>
		</td>
		<?php
	}

	public function column_default( $item, $column_name ) {
	}

	public function single_row( $taxonomy ) {
		$class = '';

		if ( ! $this->is_custom( $taxonomy ) ) {
			$class = 'wp-locked';
		}

		?>
		<tr id="post_type-<?php echo $taxonomy->name; ?>" class="<?php echo esc_attr( $class ); ?>">
			<?php $this->single_row_columns( $taxonomy ); ?>
		</tr>
		<?php
	}

	protected function get_primary_column_name() {
		return 'name';
	}

	protected function handle_row_actions( $taxonomy, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return '';
		}

		// Restores the more descriptive, specific name for use within this method.
		$can_edit_post_type = current_user_can( 'manage_options' );
		$actions            = [];

		if ( $can_edit_post_type && $this->is_custom( $taxonomy ) ) {
			$actions['edit'] = sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				Util::get_manage_page_url( [], $post_type ),
				esc_attr( __( 'Edit', 'easy-post-types-fields' ) ),
				__( 'Edit', 'easy-post-types-fields' )
			);

			$actions['delete'] = sprintf(
				'<a href="%s" aria-label="%s" data-post_count="%d">%s</a>',
				$this->get_delete_post_link( $post_type ),
				esc_attr( __( 'Delete', 'easy-post-types-fields' ) ),
				$this->get_post_count( $post_type ),
				__( 'Delete', 'easy-post-types-fields' )
			);
		}

		$actions['fields'] = sprintf(
			'<a href="%s" aria-label="%s">%s</a>',
			Util::get_manage_page_url( [], $post_type, 'fields' ),
			/* translators: %s: Post title. */
			esc_attr( __( 'Fields', 'easy-post-types-fields' ) ),
			__( 'Fields', 'easy-post-types-fields' )
		);

		$actions['taxonomies'] = sprintf(
			'<a href="%s" aria-label="%s">%s</a>',
			Util::get_manage_page_url( [], $post_type, 'fields' ),
			/* translators: %s: Post title. */
			esc_attr( __( 'Taxonomies', 'easy-post-types-fields' ) ),
			__( 'Taxonomies', 'easy-post-types-fields' )
		);

		$actions['manage'] = sprintf(
			'<a href="%s" aria-label="%s">%s</a>',
			add_query_arg( 'post_type', $post_type->name, admin_url( 'edit.php' ) ),
			/* translators: %s: Post title. */
			sprintf( esc_attr__( 'Manage %s', 'easy-post-types-fields' ), $post_type->label ),
			sprintf( esc_html__( 'Manage %s', 'easy-post-types-fields' ), $post_type->label )
		);

		return $this->row_actions( $actions );
	}

	public function get_edit_post_link( $taxonomy ) {
		if ( $this->is_custom( $taxonomy ) ) {
			$posts = get_posts(
				[
					'post_type' => 'ept_post_type',
					'name'      => str_replace( 'ept_', '', $taxonomy->name ),
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

	public function get_delete_post_link( $taxonomy ) {
		if ( $this->is_custom( $taxonomy ) ) {
			$posts = get_posts(
				[
					'post_type' => 'ept_post_type',
					'name'      => str_replace( 'ept_', '', $taxonomy->name ),
				]
			);

			if ( empty( $posts ) ) {
				return false;
			}

			$post = reset( $posts );

			return get_delete_post_link( $post->ID, '', true );
		}

		return false;
	}

	public function display() {
		$singular = $this->_args['singular'];
		$new_link = add_query_arg(
			[
				'page'      => 'ept_post_types',
				'post_type' => $this->post_type->name,
				'section'   => 'taxonomies',
				'action'    => 'add',
			],
			admin_url( 'admin.php' )
		);

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

			<tfoot>
				<tr>
					<th scope="col" colspan="<?php echo esc_attr( count( $this->get_columns() ) ); ?>">
						<a href="<?php echo esc_url( $new_link ); ?>" class="page-title-action ept-post-table-action">
							<?php esc_html_e( 'Add new taxonomy', 'easy-post-types-fields' ); ?>
						</a>
					</th>
				</tr>
			</tfoot>
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
