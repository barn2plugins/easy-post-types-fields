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
			$prefix            = "{$this->post_type->name}_";
			$locked_taxonomies = array_map(
				function ( $t ) {
					return [
						'name'          => $t->labels->name,
						'singular_name' => $t->labels->singular_name,
						'slug'          => $t->name,
						'hierarchical'  => $t->hierarchical,
						'is_custom'     => false,
					];
				},
				array_filter(
					get_object_taxonomies( $post_type->name, 'objects' ),
					function( $t ) use ( $internal_slugs, $prefix ) {
						return $t->publicly_queryable && ! in_array( str_replace( $prefix, '', $t->name ), $internal_slugs, true );
					}
				)
			);

			$this->taxonomies = array_merge( $this->taxonomies, $locked_taxonomies );
		}

	}

	public function prepare_items() {
		$per_page    = apply_filters( 'edit_ept_taxonomies_per_page', $this->get_items_per_page( 'edit_ept_taxonomies_per_page' ) );
		$total_items = count( $this->taxonomies );

		$this->set_pagination_args(
			[
				'total_items' => $total_items,
				'per_page'    => $per_page,
			]
		);
	}

	public function is_custom( $taxonomy ) {
		return $taxonomy['is_custom'];
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
		$hierarchical_tooltip = Util::get_tooltip( __( 'Hierarchical taxonomies have a nested parent/child structure like WordPress post categories, whereas non-hierarchical taxonomies are flat like tags.', 'easy-post-types-fields' ) );

		$columns = [
			'name'         => _x( 'Name', 'column name', 'easy-post-types-fields' ),
			'slug'         => _x( 'Slug', 'column name', 'easy-post-types-fields' ),
			'hierarchical' => _x( 'Hierarchical', 'column name', 'easy-post-types-fields' ) . $hierarchical_tooltip,
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

		foreach ( $this->taxonomies as $taxonomy ) {
			$this->single_row( $taxonomy );
		}
	}

	protected function _column_name( $taxonomy, $classes, $data, $primary ) {
		$data .= " data-slug=\"{$taxonomy['slug']}\"";
		?>
		<td class="<?php echo esc_attr( $classes ); ?> taxonomy-name" <?php echo $data; ?>>
			<?php

			if ( $this->is_custom( $taxonomy ) ) {
				printf(
					'<a href="" class="row-title editinline" aria-label="%s">%s</a>',
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

	protected function _column_slug( $taxonomy, $classes, $data, $primary ) {
		$taxonomy_slug = 'publish' === $this->post_type_object->post_status ? $taxonomy['slug'] : "{$this->post_type->name}_{$taxonomy['slug']}";

		?>
		<td class="<?php echo esc_attr( $classes ); ?> taxonomy-slug" <?php echo $data; ?>><?php echo esc_html( $taxonomy_slug ); ?></td>
		<?php
	}

	protected function _column_hierarchical( $taxonomy, $classes, $data, $primary ) {
		?>
		<td class="<?php echo esc_attr( $classes ); ?> taxonomy-slug" <?php echo $data; ?>><?php echo esc_html( true === $taxonomy['hierarchical'] ? __( 'Yes', 'easy-post-types-fields' ) : __( 'No', 'easy-post-types-fields' ) ); ?></td>
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
		<tr id="taxonomy-<?php echo esc_attr( $taxonomy['slug'] ); ?>" class="<?php echo esc_attr( $class ); ?>">
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

	public function get_edit_post_link( $taxonomy ) {
		parse_str( $_SERVER['QUERY_STRING'], $query_args );

		return Util::get_manage_page_url( $query_args['post_type'], $query_args['section'], $taxonomy['slug'], 'edit' );
	}

	public function get_delete_post_link( $taxonomy ) {
		return '';
	}

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

	public function display() {
		$singular = $this->_args['singular'];
		$new_link = Util::get_manage_page_url( $this->post_type->name, 'taxonomies', '', 'add' );

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
