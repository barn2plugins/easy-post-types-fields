<?php
/**
 * The HTML markup of the Custom Fields or Taxonomies pages
 */
namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

defined( 'ABSPATH' ) || exit;

?>

<p>
	<?php
	echo wp_kses_post( $page_description );
	?>
</p>

<form id="posts-filter" method="get">
	<?php
	if ( isset( $request['action'] ) ) {
		?>
		<h2 class="screen-reader-text">
			<?php
			// translators: either `Taxonomy` or `Custom field`
			echo esc_html( sprintf( __( '%s editor', 'easy-post-types-fields' ), $singular_name ) );
			?>
		</h2>
		<?php

		require "html-manage-page-$section.php";
	} else {
		?>
		<h2 class="screen-reader-text">
			<?php
			// translators: either `Taxonomy` or `Custom field`
			echo esc_html( sprintf( __( '%s list', 'easy-post-types-fields' ), $singular_name ) );
			?>
		</h2>
		<?php
		$list_table->display();
	}
	?>
</form>
<?php
$list_table->inline_edit();

