<?php
/**
 * The HTML markup of the Manage page
 *
 * @param Post_Type_List_Table $post_type_list_table The list table instance (a subclass of WP_List_Table)
 * @param Barn2\EPT_Lib\Plugin\Plugin $plugin The main instance of the plugin
 * @param string $new_link The link to add a new post type
 */
namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

defined( 'ABSPATH' ) || exit;

$post_type_list_table = new List_Tables\Post_Type_List_Table();

$post_type_list_table->views();
?>

<form id="posts-filter" method="get">
	<h2 class="screen-reader-text">Posts list</h2>

	<?php

	$post_type_list_table->display();

	?>

</form>


<?php
