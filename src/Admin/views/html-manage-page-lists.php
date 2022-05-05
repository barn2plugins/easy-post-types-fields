<?php
/**
 * The HTML markup of the custom field or taxonomy listings
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
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

		wp_nonce_field( $nonce_action );
		submit_button(
			sprintf(
				// translators: 1: 'Add' or 'Update', 2: 'custom field' or 'taxonomy' phpcs:ignore Squiz.PHP.CommentedOutCode.Found
				__( '%1$s %2$s', 'easy-post-types-fields' ),
				'add' === $request['action'] ? __( 'Add', 'easy-post-types-fields' ) : __( 'Update', 'easy-post-types-fields' ),
				'taxonomies' === $section ? __( 'taxonomy', 'easy-post-types-fields' ) : __( 'custom field', 'easy-post-types-fields' )
			),
			'primary',
			'submit',
			false
		);
		?>

		<input type="hidden" name="_first_referer" value="<?php echo esc_url( $referer ); ?>" />
		<a href="<?php echo esc_url( $referer ); ?>" class="button"><?php esc_html_e( 'Cancel', 'easy-post-types-fields' ); ?></a>
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
