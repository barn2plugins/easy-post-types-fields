<?php
/**
 * The HTML markup of the page header
 *
 * @param Post_Type_List_Table $post_type_list_table The list table instance (a subclass of WP_List_Table)
 * @param Barn2\EPT_Lib\Plugin\Plugin $plugin The main instance of the plugin
 * @param string $new_link The link to add a new post type
 */
namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

defined( 'ABSPATH' ) || exit;

?>

<h1 class="wp-heading-inline">
	<?php echo esc_html( $page_title ); ?>
</h1>
<hr class="wp-header-end" />

<?php
if ( isset( $page_description ) && $page_description ) {
	?>

	<p>
		<?php echo wp_kses_post( $page_description ); ?>
	</p>

<?php
}