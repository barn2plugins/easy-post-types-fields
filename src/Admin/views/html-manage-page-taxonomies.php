<?php
/**
 * The HTML markup of the Manage page
 *
 * @param Taxonomy_List_Table $taxonomy_list_table The taxonomy list table instance (a subclass of WP_List_Table)
 * @param string $new_link The link to add a new post type
 */
namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

use Barn2\Plugin\Easy_Post_Types_Fields\Util;

defined( 'ABSPATH' ) || exit;

$request_post_type = Util::get_post_type_by_name( $request['post_type'] );
$list_table        = new List_Tables\Taxonomy_List_Table( $request_post_type );

?>

<form id="posts-filter" method="get">
	<?php
	if ( isset( $request['action'] ) ) {
		?>
		<h2 class="screen-reader-text">Taxonomy editor</h2>
		<?php

		require 'html-manage-page-taxonomy.php';
	} else {
		?>
		<h2 class="screen-reader-text">Taxonomies list</h2>
		<?php
		$list_table->display();
	}
	?>
</form>

<?php
