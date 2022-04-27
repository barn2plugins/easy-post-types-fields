<?php
/**
 * The HTML markup of the page header
 *
 * @param Post_Type_List_Table $post_type_list_table The list table instance (a subclass of WP_List_Table)
 * @param Barn2\EPT_Lib\Plugin\Plugin $plugin The main instance of the plugin
 * @param string $new_link The link to add a new post type
 */
namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

use Barn2\Plugin\Easy_Post_Types_Fields\Util;

defined( 'ABSPATH' ) || exit;

?>

<div class='barn2-plugins-header'>
	<div class="barn2-plugins-header-wrapper">
		<h1 class='barn2-plugins-header-heading'>
			<?php esc_html_e( 'Easy Post Types and Fields', 'easy-post-types-fields' ); ?>
		</h1>
		<div class="links-area">
			<?php Util::support_links(); ?>
		</div>
	</div>
</div>


<h1 class="wp-heading-inline">
	<?php
	echo esc_html( $page_title );

	if ( $new_link ) {
		?>

		<a href="<?php echo esc_url( $new_link ); ?>" class="page-title-action"><?php esc_html_e( 'Add new', 'easy-post-types-fields' ); ?></a>

		<?php
	}
	?>
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