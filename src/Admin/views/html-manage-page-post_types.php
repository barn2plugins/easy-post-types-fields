<?php
/**
 * The HTML markup of the post type listings
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
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
