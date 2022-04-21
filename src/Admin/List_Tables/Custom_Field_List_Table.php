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
class Custom_Field_List_Table extends WP_List_Table {

	/**
	 * The post type the fields are registered to
	 *
	 * @var string
	 */
	private $post_type;

	/**
	 * The fields of the current post type
	 *
	 * @var array
	 */
	protected $fields;

	public function __construct( $post_type ) {
		parent::__construct(
			[
				'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
			]
		);

		$this->post_type  = $post_type;
		$post_type_object = Util::get_post_type_object( $post_type );
		$fields           = get_post_meta( $post_type_object->ID, '_ept_fields', true );
		$this->fields     = $fields ?: [];
	}

	public function prepare_items() {
		$per_page    = apply_filters( 'edit_ept_fields_per_page', $this->get_items_per_page( 'edit_ept_fields_per_page' ) );
		$total_items = count( $this->fields );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);
	}

	public function has_items() {
		return count( $this->fields );
	}

	public function no_items() {
		esc_html_e( 'No custom fields for this post type yet', 'easy-post-types-fields' );
	}

	protected function get_views() {
		return [];
	}

	protected function get_bulk_actions() {
		return [];
	}

	protected function get_table_classes() {
		global $mode;

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
		if ( empty( $this->fields ) ) {
			$fields       = get_post_meta( $this->id, '_ept_fields', true );
			$this->fields = $fields ?: [];
		}

		foreach ( $this->fields as $field ) {
			$this->single_row( $field );
		}
	}

	protected function _column_name( $field, $classes, $data, $primary ) {
		$data .= " data-slug=\"{$field['slug']}\"";
		?>
		<td class="<?php echo esc_attr( $classes ); ?> post_type-name" <?php echo $data; ?>>
			<?php
			printf(
				'<a class="row-title" href="%s" aria-label="%s">%s</a>',
				esc_url( $this->get_edit_post_link( $field ) ),
				// translators: a custom field name
				esc_attr( sprintf( __( '%s (Edit)', 'easy-post-types-fields' ), $field['name'] ) ),
				esc_attr( $field['name'] )
			);

			echo $this->handle_row_actions( $field, 'name', $primary ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</td>
		<?php
	}

	public function column_default( $item, $column_name ) {
	}

	public function single_row( $field ) {
		$class = '';

		?>
		<tr id="field-<?php echo $field['slug']; ?>" class="<?php echo esc_attr( $class ); ?>">
			<?php $this->single_row_columns( $field ); ?>
		</tr>
		<?php
	}

	protected function get_primary_column_name() {
		return 'name';
	}

	protected function handle_row_actions( $field, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return '';
		}

		// Restores the more descriptive, specific name for use within this method.
		$can_edit_post_type = current_user_can( 'manage_options' );
		$actions            = [];

		if ( $can_edit_post_type ) {
			$actions['edit'] = sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				$this->get_edit_post_link( $field ),
				esc_attr( __( 'Edit', 'easy-post-types-fields' ) ),
				__( 'Edit', 'easy-post-types-fields' )
			);

			$actions['delete'] = sprintf(
				'<a href="" aria-label="%s" class="field-delete" data-_wpnonce="%s">%s</a>',
				$this->get_delete_post_link( $field ),
				wp_create_nonce( 'inlinedeletenonce' ),
				esc_attr( __( 'Delete', 'easy-post-types-fields' ) ),
				__( 'Delete', 'easy-post-types-fields' )
			);
		}

		return $this->row_actions( $actions );
	}

	public function get_edit_post_link( $field ) {
		parse_str( $_SERVER['QUERY_STRING'], $query_args );
		$query_args['slug']   = $field['slug'];
		$query_args['action'] = 'edit';

		return Util::get_manage_page_url( $query_args['post_type'], $query_args['section'], $query_args['slug'], $query_args['action'] );
	}

	public function get_delete_post_link( $post_type ) {
		return '';
	}

	public function display() {
		$singular = $this->_args['singular'];
		$new_link = Util::get_manage_page_url( $this->post_type->name, 'fields', '', 'add' );

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
}
