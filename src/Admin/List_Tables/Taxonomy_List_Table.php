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
	protected $taxonomies = [];

	public function __construct( $post_type ) {
		parent::__construct(
			[
				'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
			]
		);

		$this->post_type  = $post_type;
		$post_type_object = Util::get_post_type_object( $post_type );

		if ( $post_type_object ) {
			$taxonomies = get_post_meta( $post_type_object->ID, '_ept_taxonomies', true );

			$this->taxonomies = $taxonomies ?: [];
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
		return in_array(
			$taxonomy['slug'],
			array_map(
				function( $t ) {
					return $t['slug'];
				},
				$this->taxonomies
			),
			true
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
			<div class="hidden" id="inline_<?php echo esc_attr( "{$taxonomy['post_type']}_{$taxonomy['slug']}" ); ?>">
				<div class="name"><?php echo esc_attr( $taxonomy['name'] ); ?></div>
				<div class="plural_name"><?php echo esc_attr( $taxonomy['plural_name'] ); ?></div>
				<div class="slug"><?php echo esc_attr( $taxonomy['slug'] ); ?></div>
				<div class="hierarchical"><?php echo esc_attr( $taxonomy['hierarchical'] ? 'true' : 'false' ); ?></div>
				<div class="previous_slug"><?php echo esc_attr( $taxonomy['slug'] ); ?></div>
			</div>
		</td>
		<?php
	}

	protected function _column_slug( $taxonomy, $classes, $data, $primary ) {
		?>
		<td class="<?php echo esc_attr( $classes ); ?> taxonomy-slug" <?php echo $data; ?>><?php echo esc_html( $taxonomy['slug'] ); ?></td>
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
				'<a href="" aria-label="%s" class="editinline">%s</a>',
				esc_attr( __( 'Edit', 'easy-post-types-fields' ) ),
				__( 'Edit', 'easy-post-types-fields' )
			);

			$actions['delete'] = sprintf(
				'<a href="" aria-label="%s" class="taxonomy-delete">%s</a>',
				$this->get_delete_post_link( $taxonomy ),
				esc_attr( __( 'Delete', 'easy-post-types-fields' ) ),
				__( 'Delete', 'easy-post-types-fields' )
			);
		}

		return $this->row_actions( $actions );
	}

	public function get_delete_post_link( $taxonomy ) {
		if ( $this->is_custom( $taxonomy ) ) {
			return '';
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
						<button type="button" class="editinline page-title-action ept-post-table-action">
							<?php esc_html_e( 'Add new taxonomy', 'easy-post-types-fields' ); ?>
						</button>
						<?php wp_nonce_field( 'inlinedeletenonce', '_inline_delete', false ); ?>
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
		$screen = get_current_screen();

		?>
		<form method="get">
			<table style="display: none"><tbody id="inlineedit">
				<?php

				$classes = "inline-edit-row inline-edit-row-taxonomy quick-edit-row quick-edit-row-taxonomy inline-edit-{$screen->post_type}";

				?>
				<tr id="inline-edit" class="<?php echo $classes; ?>" style="display: none">
					<td colspan="<?php echo $this->get_column_count(); ?>" class="colspanchange">
						<div class="inline-edit-wrapper" role="region" aria-labelledby="inline-edit-legend">
							<fieldset class="inline-edit-col-left">
								<div class="inline-edit-col">
									<label>
										<span class="title"><?php _e( 'Name', 'easy-post-types-fields' ); ?></span>
										<span class="input-text-wrap">
											<input type="text" placeholder="Taxonomy singular name (e.g. Category)" name="name" class="ptitle" value="" />
										</span>
									</label>
									<label>
										<span class="title"><?php _e( 'Plural name', 'easy-post-types-fields' ); ?></span>
										<span class="input-text-wrap">
											<input type="text" placeholder="Taxonomy plural name (e.g. Categories)" name="plural_name" class="ptitle" value="" />
										</span>
									</label>
									<label>
										<span class="title"><?php _e( 'Slug', 'easy-post-types-fields' ); ?></span>
										<span class="input-text-wrap">
											<input type="text" placeholder="" name="slug" class="ptitle" value="" />
										</span>
									</label>
									<label>
										<span class="title"><?php _e( 'Hierarchical', 'easy-post-types-fields' ); ?></span>
										<span class="input-text-wrap">
											<input type="checkbox" name="hierarchical" class="ptitle" value="" />
										</span>
									</label>
									<input type="hidden" name="previous_slug" value="" />
									<input type="hidden" name="post_type" value="<?php echo esc_attr( $this->post_type->name ); ?>" />
									<input type="hidden" name="type" value="taxonomy" />
								</div>
							</fieldset>
							<div class="submit inline-edit-save">
								<?php wp_nonce_field( 'inlineeditnonce', '_inline_edit', false ); ?>
								<button type="button" class="button button-primary save"><?php _e( 'Update' ); ?></button>
								<button type="button" class="button cancel"><?php _e( 'Cancel' ); ?></button>

								<span class="spinner"></span>

								<input type="hidden" name="screen" value="<?php echo esc_attr( $screen->id ); ?>" />
								<div class="notice notice-error notice-alt inline hidden">
									<p class="error"></p>
								</div>
							</div>
						</div> <!-- end of .inline-edit-wrapper -->
					</td>
				</tr>
			</tbody></table>
		</form>
		<?php
	}
}
