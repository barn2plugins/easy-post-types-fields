<?php
/**
 * The HTML markup of the Promo page for Posts Table Pro
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

defined( 'ABSPATH' ) || exit;

?>

<div class="wrap barn2-plugins-settings">
	<div class="barn2-settings-inner promo-page">

		<?php
		require 'html-manage-page-header.php';
		?>

		<p>
			<?php
			echo wp_kses_post(
				sprintf(
					// translators: 1: the opening tag of an anchor element, 2: the closing tag of an anchor element
					__( 'Once you have created your post types, custom fields and taxonomies, you can use %1$sPosts Table Pro%2$s to display them in a user-friendly table.', 'easy-post-types-fields' ),
					'<a class="promo-link-inline" href="https://barn2.com/wordpress-plugins/posts-table-pro/?utm_source=wporg&utm_medium=freeplugin&utm_campaign=freepluginwporg&utm_content=ecpt-settings" target="_blank">',
					'</a>'
				)
			);
			?>
		</p>
		<p>
			<?php esc_html_e( 'Posts Table Pro is a dynamic WordPress table plugin which lists any type of WordPress content in a searchable table. As well as displaying any custom post type, you can choose which columns of information to show in the table - including your custom fields and taxonomies.', 'easy-post-types-fields' ); ?>
		</p>
		<p>
			<?php esc_html_e( 'Your website visitors can use the search box, sortable columns and filter dropdowns to find exactly what they are looking for.', 'easy-post-types-fields' ); ?>
		</p>
		<a class="promo-link" href="https://barn2.com/wordpress-plugins/posts-table-pro/?utm_source=wporg&utm_medium=freeplugin&utm_campaign=freepluginwporg&utm_content=ecpt-settings" target="_blank">
			<img class="promo-image" src="<?php echo esc_url( $image_url ); ?>" />
		</a>
	</div>
</div>

<?php
