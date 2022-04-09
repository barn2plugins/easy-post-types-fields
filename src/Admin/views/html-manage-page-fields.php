<?php
/**
 * The HTML markup of the Manage page
 *
 * @param Post_Type_List_Table $post_type_list_table The list table instance (a subclass of WP_List_Table)
 * @param Barn2\EPT_Lib\Plugin\Plugin $plugin The main instance of the plugin
 * @param string $new_link The link to add a new post type
 */
namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

use Barn2\Plugin\Easy_Post_Types_Fields\Util;

defined( 'ABSPATH' ) || exit;

$request_post_type = Util::get_post_type_by_name( $request['post_type'] );
$field_list_table  = new List_Tables\Custom_Field_List_Table( $request_post_type );

?>

<form id="posts-filter" method="get">
	<?php
	if ( isset( $request['action'] ) ) {
		?>
		<h2 class="screen-reader-text">Field editor</h2>
		<?php

		require 'html-manage-page-field.php';

	} else {
		?>
		<h2 class="screen-reader-text">Fields list</h2>
		<?php
		$field_list_table->display();
		?>
		<a href="<?php echo esc_url( $new_link ); ?>" class="page-title-action"><?php esc_html_e( 'Add New', 'easy-post-types-fields' ); ?></a>
		<?php
	}
	?>
</form>

<?php
