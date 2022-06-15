<?php
/**
 * Custom fields list table, extending the WP_List_Table class
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin\List_Tables;

use Barn2\Plugin\Easy_Post_Types_Fields\Util;
use WP_List_Table;

/**
 * Class used to implement displaying custom fields in a list table.
 */
class Custom_Field_List_Table extends WP_List_Table {
	/**
	 * The post type the fields are registered to
	 *
	 * @var WP_Post_Type
	 */
	private $post_type;

	/**
	 * The post type the fields are registered to
	 *
	 * @var WP_Post
	 */
	private $post_type_object;

	/**
	 * The fields of the current post type
	 *
	 * @var array
	 */
	protected $fields = [];

	/**
	 * Constructor.
	 *
	 * The constructor of a custom field list table accepts the WP_Post_Type
	 * object the custom fields refer are registered to.
	 *
	 * @param  WP_Post_Type $post_type
	 * @return void
	 */
	public function __construct( $post_type ) {
		parent::__construct(
			[
				'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
			]
		);

		$this->post_type        = $post_type;
		$this->post_type_object = Util::get_post_type_object( $post_type );

		if ( $this->post_type_object ) {
			$fields       = get_post_meta( $this->post_type_object->ID, '_ept_fields', true );
			$this->fields = $fields ?: [];
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function prepare_items() {
		/**
		 * Filter the number of items per page for the custom field list table
		 *
		 * @param int $items_per_page The number of items per page
		 */
		$per_page    = apply_filters( 'edit_ept_fields_per_page', $this->get_items_per_page( 'edit_ept_fields_per_page' ) );
		$total_items = count( $this->fields );

		$this->set_pagination_args(
			[
				'total_items' => $total_items,
				'per_page'    => $per_page,
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function has_items() {
		return count( $this->fields );
	}

	/**
	 * {@inheritdoc}
	 */
	public function no_items() {
		esc_html_e( 'No custom fields for this post type yet', 'easy-post-types-fields' );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_views() {
		return [];
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

		return [
			'widefat',
			'striped',
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_columns() {
		$slug_tooltip = Util::get_tooltip(
			sprintf(
				// translators: a post type name followed by underscore
				__( 'The slug is a unique code that you can use to identify the custom taxonomy. For example, you can use it to display the data with the Posts Table Pro plugin. If you are using the slug in other ways &ndash; for example for development purposes &ndash; then you should add the prefix \'%1$s\' before the slug, for example \'%1$scategory\' instead of just \'category\'.', 'easy-post-types-fields' ),
				"{$this->post_type->name}_"
			)
		);

		$columns = [
			'name' => _x( 'Name', 'column name', 'easy-post-types-fields' ),
			'slug' => _x( 'Slug', 'column name', 'easy-post-types-fields' ) . ( 'publish' === $this->post_type_object->post_status ? $slug_tooltip : '' ),
			'type' => _x( 'Type', 'column name', 'easy-post-types-fields' ),
		];

		/**
		 * Filter the heading of each column in the custom field list table
		 *
		 * The array passed to the filter callback is an associative array
		 * where the keys are the name of the columns and the values are the
		 * headings. The columns in the array are presented in the same order
		 * they have in the table.
		 *
		 * @param array $columns The columns of the list table
		 */
		return apply_filters( 'manage_ept_fields_columns', $columns );
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
		if ( empty( $this->fields ) ) {
			$fields       = get_post_meta( $this->id, '_ept_fields', true );
			$this->fields = $fields ?: [];
		}

		foreach ( $this->fields as $field ) {
			$this->single_row( $field );
		}
	}

	/**
	 * Output the name of the field for the current row
	 *
	 * @param array $field The field in the current row
	 * @param string $classes The classes for the cell element
	 * @param string $data The extra attributes for the cell element
	 * @param string $primary The name of the primary column
	 * @return void
	 */
	protected function _column_name( $field, $classes, $data, $primary ) { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$data .= " data-slug=\"{$field['slug']}\"";
		?>
		<td class="<?php echo esc_attr( $classes ); ?> post_type-name" <?php echo $data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
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

	/**
	 * Output the slug of the field for the current row
	 *
	 * @param array $field The field associated with the current row
	 * @return void
	 */
	protected function column_slug( $field ) {
		echo esc_html( $field['slug'] );
	}

	/**
	 * Output the type of field for the current row
	 *
	 * @param array $field The field associated with the current row
	 * @return void
	 */
	protected function column_type( $field ) {
		$types = Util::get_custom_field_types();
		$type  = isset( $types[ $field['type'] ] ) ? $types[ $field['type'] ] : '';

		echo esc_html( $type );
	}

	/**
	 * Output a single row of the table
	 *
	 * @param array $field The field associated with the current row
	 * @return void
	 */
	public function single_row( $field ) {
		$class = '';

		?>
		<tr id="field-<?php echo esc_attr( $field['slug'] ); ?>" class="<?php echo esc_attr( $class ); ?>">
			<?php $this->single_row_columns( $field ); ?>
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
	 * @param array $field The field associated with the current row
	 * @param string $column_name The name of the current column
	 * @param string $primary The name of the primary column
	 */
	protected function handle_row_actions( $field, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return '';
		}

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

	/**
	 * Get the URL for the Edit row action link
	 *
	 * @param  array $field The current row item
	 * @return string
	 */
	public function get_edit_post_link( $field ) {
		parse_str( $_SERVER['QUERY_STRING'], $query_args );
		$query_args['slug']   = $field['slug'];
		$query_args['action'] = 'edit';

		return Util::get_manage_page_url( $query_args['post_type'], $query_args['section'], $query_args['slug'], $query_args['action'] );
	}

	/**
	 * Get the URL for the Delete row action link
	 *
	 * @param  array $field The current row item
	 * @return string
	 */
	public function get_delete_post_link( $field ) {
		return '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function display() {
		$singular = $this->_args['singular'];
		$new_link = Util::get_manage_page_url( $this->post_type->name, 'fields', '', 'add' );

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
					echo esc_attr( " data-wp-lists='list:$singular'" );
				}
				?>
				>
				<?php $this->display_rows_or_placeholder(); ?>
			</tbody>

		</table>
		<?php
	}
}
