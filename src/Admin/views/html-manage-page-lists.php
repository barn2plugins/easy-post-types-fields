<?php
/**
 * The HTML markup of the Custom Fields or Taxonomies pages
 */
namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

defined( 'ABSPATH' ) || exit;

if ( isset( $request['action'] ) ) {

	?>

	<h2 class="screen-reader-text">
		<?php
		// translators: either `Taxonomy` or `Custom field`
		echo esc_html( sprintf( __( '%s editor', 'easy-post-types-fields' ), $singular_name ) );
		?>
	</h2>
	<form action="" method="post" class="ept-list-item">

		<?php

		require "html-manage-page-$section.php";

		wp_nonce_field( 'save_list_item_postdata' );
		submit_button(
			sprintf(
				// translators: 1: 'Add' or 'Update', 2: 'custom field' or 'taxonomy'
				__( '%1$s %2$s', 'easy-post-types-fields' ),
				'add' === $request['action'] ? __( 'Add', 'easy-post-types-fields' ) : __( 'Update', 'easy-post-types-fields' ),
				'taxonomies' === $section ? __( 'taxonomy', 'easy-post-types-fields' ) : __( 'custom field', 'easy-post-types-fields' )
			),
			'primary',
			'submit',
			false
		);
		?>

		<a href="<?php echo esc_url( $_SERVER['HTTP_REFERER'] ); ?>" class="button"><?php esc_html_e( 'Cancel', 'easy-post-types-fields' ); ?></a>
	</form>

	<?php

} else {
	?>
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
	</form>
	<?php
}
// $list_table->inline_edit();

