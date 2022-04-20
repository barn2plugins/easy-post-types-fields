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

if ( $breadcrumbs ) {
	?>

	<h2 class="wp-heading-inline ept-page-breadcrumbs">
		<?php echo $breadcrumbs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</h2>
	<?php
} else {
	?>

	<h1 class="wp-heading-inline">
		<?php echo esc_attr( __( 'Easy Post Types and Fields', 'easy-post-types-fields' ) ); ?>
	</h1>

	<?php
}

?>
<hr class="wp-header-end">
<p></p>
<?php
