<?php
/**
 * Taxonomy list table, extending the WP_List_Table class
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
 * Class used to implement displaying taxonomies in a list table.
 */
class Taxonomy_List_Table extends WP_List_Table {
	/**
	 * The post type the taxonomies are assigned to
	 *
	 * @var WP_Post_Type
	 */
	private $post_type;

	/**
	 * The EPT post object with the information about this post type
	 *
	 * @var WP_Post
	 */
	private $post_type_object;

	/**
	 * The taxonomies of the current post type
	 *
	 * @var array
	 */
	protected $taxonomies = [];

	/**
	 * Constructor
	 *
	 * Define a list table for the taxonomies registered to a given post type
	 *
	 * @param  WP_Post_Type $post_type The current post type
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
			$taxonomies = get_post_meta( $this->post_type_object->ID, '_ept_taxonomies', true );

			$this->taxonomies = $taxonomies ?: [];
		}

		$internal_slugs = array_column( $this->taxonomies, 'slug' );

		if ( 'private' === $this->post_type_object->post_status ) {

			$this->taxonomies = array_merge( $this->taxonomies, Util::get_builtin_taxonomies( $this->post_type_object ) );
		}

	}

	/**
	 * {@inheritdoc}
	 */
	public function prepare_items() {
		/**
		 * Filter the number of items per page for the taxonomy list table
		 *
		 * @param int $items_per_page The number of items per page
		 */
		$per_page    = apply_filters( 'edit_ept_taxonomies_per_page', $this->get_items_per_page( 'edit_ept_taxonomies_per_page' ) );
		$total_items = count( $this->taxonomies );

		$this->set_pagination_args(
			[
				'total_items' => $total_items,
				'per_page'    => $per_page,
			]
		);
	}

	/**
	 * Determine whether a taxonomy is registered by EPT (i.e. custom)
	 * or by WordPress or a third-party plugin
	 *
	 * @param  array $taxonomy
	 * @return boolean
	 */
	public function is_custom( $taxonomy ) {
		return $taxonomy['is_custom'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function has_items() {
		return count( $this->taxonomies );
	}

	/**
	 * {@inheritdoc}
	 */
	public function no_items() {
		esc_html_e( 'No taxonomies for this post type yet', 'easy-post-types-fields' );
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
		$slug_tooltip = Util::get_tooltip(
			sprintf(
			// translators: a post type name followed by underscore
				__( 'The slug is a unique code that you can use to identify the custom taxonomy. For example, you can use it to display the data with the Posts Table Pro plugin. If you are using the slug in other ways &ndash; for example for development purposes &ndash; then you should add the prefix \'%1$s\' before the slug, for example \'%1$scategory\' instead of just \'category\'.', 'easy-post-types-fields' ),
				"{$this->post_type->name}_"
			)
		);
		$hierarchical_tooltip = Util::get_tooltip( __( 'Hierarchical taxonomies have a nested parent/child structure like WordPress post categories, whereas non-hierarchical taxonomies are flat like tags.', 'easy-post-types-fields' ) );

		$columns = [
			'name'         => _x( 'Name', 'column name', 'easy-post-types-fields' ),
			'slug'         => _x( 'Slug', 'column name', 'easy-post-types-fields' ) . ( 'publish' === $this->post_type_object->post_status ? $slug_tooltip : '' ),
			'hierarchical' => _x( 'Hierarchical', 'column name', 'easy-post-types-fields' ) . $hierarchical_tooltip,
		];

		/**
		 * Filter the heading of each column in the taxonomy list table
		 *
		 * The array passed to the filter callback is an associative array
		 * where the keys are the name of the columns and the values are the
		 * headings. The columns in the array are presented in the same order
		 * they have in the table.
		 *
		 * @param array $columns The list of columns
		 */
		return apply_filters( 'manage_ept_taxonomies_columns', $columns );
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
		if ( empty( $this->taxonomies ) ) {
			$this->taxonomies = get_post_types( [ 'public' => true ] );
		}

		foreach ( $this->taxonomies as $taxonomy ) {
			$this->single_row( $taxonomy );
		}
	}

	/**
	 * Output the name of the taxonomy for the current row
	 *
	 * @param array $taxonomy The taxonomy in the current row
	 * @param string $classes The classes for the cell element
	 * @param string $data The extra attributes for the cell element
	 * @param string $primary The name of the primary column
	 * @return void
	 */
	protected function _column_name( $taxonomy, $classes, $data, $primary ) { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$data .= " data-slug=\"{$taxonomy['slug']}\"";
		?>
		<td class="<?php echo esc_attr( $classes ); ?> taxonomy-name" <?php echo $data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php

		if ( $this->is_custom( $taxonomy ) ) {
			printf(
				'<a href="%s" class="row-title editinline" aria-label="%s">%s</a>',
				esc_url( $this->get_edit_post_link( $taxonomy ) ),
				// translators: the name of the taxonomy
				esc_attr( sprintf( __( '%s (Edit)', 'easy-post-types-fields' ), $taxonomy['name'] ) ),
				esc_attr( $taxonomy['name'] )
			);
		} else {
			echo esc_html( $taxonomy['name'] );
		}

			echo $this->handle_row_actions( $taxonomy, 'name', $primary ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		?>
		</td>
		<?php
	}

	/**
	 * Output the slug of the taxonomy for the current row
	 *
	 * @param  array $taxonomy The taxonomy associated with the current row
	 * @return void
	 */
	protected function column_slug( $taxonomy ) {
		$taxonomy_slug = 'private' === $this->post_type_object->post_status && $taxonomy['is_custom'] ? "{$this->post_type->name}_{$taxonomy['slug']}" : $taxonomy['slug'];
		echo esc_html( $taxonomy_slug );
	}

	/**
	 * Output 'Yes' or 'No' depending on the taxonomy being heirarchical or not
	 *
	 * @param  array $taxonomy The taxonomy associated with the current row
	 * @return void
	 */
	protected function column_hierarchical( $taxonomy ) {
		echo esc_html( true === $taxonomy['hierarchical'] ? __( 'Yes', 'easy-post-types-fields' ) : __( 'No', 'easy-post-types-fields' ) );
	}

	/**
	 * Output a single row of the table
	 *
	 * @param  array $taxonomy The taxonomy associated with the current row
	 * @return void
	 */
	public function single_row( $taxonomy ) {
		$class = '';

		if ( ! $this->is_custom( $taxonomy ) ) {
			$class = 'wp-locked';
		}

		?>
		<tr id="taxonomy-<?php echo esc_attr( $taxonomy['slug'] ); ?>" class="<?php echo esc_attr( $class ); ?>">
		<?php $this->single_row_columns( $taxonomy ); ?>
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
	 * @param array $taxonomy The taxonomy associated with the current row
	 * @param string $column_name The name of the current column
	 * @param string $primary The name of the primary column
	 */
	protected function handle_row_actions( $taxonomy, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return '';
		}

		$can_edit_post_type = current_user_can( 'manage_options' );
		$actions            = [];

		if ( $can_edit_post_type && $this->is_custom( $taxonomy ) ) {
			$actions['edit'] = sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				$this->get_edit_post_link( $taxonomy ),
				esc_attr( __( 'Edit', 'easy-post-types-fields' ) ),
				__( 'Edit', 'easy-post-types-fields' )
			);

			$actions['delete'] = sprintf(
				'<a href="" aria-label="%s" class="taxonomy-delete" data-_wpnonce="%s">%s</a>',
				$this->get_delete_post_link( $taxonomy ),
				wp_create_nonce( 'inlinedeletenonce' ),
				esc_attr( __( 'Delete', 'easy-post-types-fields' ) ),
				__( 'Delete', 'easy-post-types-fields' )
			);
		}

		$actions['manage'] = sprintf(
			'<a href="%s" aria-label="%s">%s</a>',
			$this->get_manage_terms_link( $taxonomy ),
			esc_attr( __( 'Manage terms', 'easy-post-types-fields' ) ),
			__( 'Manage terms', 'easy-post-types-fields' )
		);

		return $this->row_actions( $actions );
	}

	/**
	 * Get the URL for the Edit row action link
	 *
	 * @param  array $taxonomy The current row item
	 * @return string
	 */
	public function get_edit_post_link( $taxonomy ) {
		parse_str( $_SERVER['QUERY_STRING'], $query_args );

		return Util::get_manage_page_url( $query_args['post_type'], $query_args['section'], $taxonomy['slug'], 'edit' );
	}

	/**
	 * Get the URL for the Delete row action link
	 *
	 * @param  array $taxonomy The current row item
	 * @return string
	 */
	public function get_delete_post_link( $taxonomy ) {
		return '';
	}

	/**
	 * Get the URL for the Manage terms row action link
	 *
	 * @param  array $taxonomy The current row item
	 * @return string
	 */
	public function get_manage_terms_link( $taxonomy ) {
		$post_type = $this->post_type->name;

		return add_query_arg(
			[
				'taxonomy'  => $this->is_custom( $taxonomy ) ? "{$post_type}_{$taxonomy['slug']}" : $taxonomy['slug'],
				'post_type' => $post_type,
			],
			admin_url( 'edit-tags.php' )
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function display() {
		$singular = $this->_args['singular'];
		$new_link = Util::get_manage_page_url( $this->post_type->name, 'taxonomies', '', 'add' );

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
}
