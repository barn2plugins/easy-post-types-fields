<?php
/**
 * The HTML markup of the Custom Fields or Taxonomies pages
 */
namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

defined( 'ABSPATH' ) || exit;

if ( isset( $request['action'] ) ) {
	$form_request_args = $request;
	unset( $form_request_args['action'] );

	$form_action = add_query_arg( $form_request_args, admin_url( 'admin.php' ) );
	$data_type   = 'taxonomies' === $section ? 'taxonomy' : 'field';

	?>

	<h2 class="screen-reader-text">
		<?php
		// translators: either `Taxonomy` or `Custom field`
		echo esc_html( sprintf( __( '%s editor', 'easy-post-types-fields' ), $singular_name ) );
		?>
	</h2>
	<form action="<?php echo esc_url( $form_action ); ?>" method="post" class="ept-list-item">

		<?php

		require "html-manage-page-$data_type.php";

		wp_nonce_field( 'save_list_item_postdata' );
		submit_button(
			sprintf(
				// translators: 'Add' or 'Update' followed by 'custom field' or 'taxonomy'
				__( '%s %s', 'easy-post-types-fields' ),
				'add' === $request['action'] ? __( 'Add', 'easy-post-types-fields' ) : __( 'Update', 'easy-post-types-fields' ),
				'taxonomy' === $data_type ? __( 'taxonomy', 'easy-post-types-fields' ) : __( 'custom field', 'easy-post-types-fields' )
			),
			'primary',
			'submit',
			false
		);
		?>

		<a href="<?php echo esc_url( $form_action ); ?>" class="button"><?php esc_html_e( 'Cancel', 'easy-post-types-fields' ); ?></a>
	</form>

	<?php

} else {
	?>
	<p>
		<?php
		echo wp_kses_post( $page_description );
		?>
	</p>
	<h2 class="screen-reader-text">
		<?php
		// translators: either `Taxonomy` or `Custom field`
		echo esc_html( sprintf( __( '%s list', 'easy-post-types-fields' ), $singular_name ) );
		?>
	</h2>
	<form method="get">
		<?php
		$list_table->display();
		?>

		<a href="<?php echo esc_url( $new_link ); ?>" class="button"><?php echo esc_html( sprintf( __( 'Add new %s', 'easy-post-types-fields' ), strtolower( $singular_name ) ) ); ?></a>

	</form>
	<?php
}
// $list_table->inline_edit();

