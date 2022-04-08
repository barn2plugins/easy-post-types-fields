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

if ( $breadcrumbs ) {
	?>

	<h4 class="ept-page-breadcrumbs">
		<?php echo $breadcrumbs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</h4>

	<?php
} else {
	?>

	<h1 class="wp-heading-inline"><?php echo esc_attr( __( 'Manage post types', 'easy-post-types-fields' ) ); ?></h1>
	<a href="<?php echo esc_url( $new_link ); ?>" class="page-title-action">Add New</a>

	<?php
}

?>
<hr class="wp-header-end">
<?php
