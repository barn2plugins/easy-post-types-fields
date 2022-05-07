<?php
/**
 * The HTML markup of the Promo page for Password Protected Categories
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
					__( 'Do you need to control who can access your custom post types? You can easily do this with the %1$sPassword Protected Categories%2$s plugin.', 'easy-post-types-fields' ),
					'<a class="promo-link-inline" href="https://barn2.com/wordpress-plugins/password-protected-categories/?utm_source=wporg&utm_medium=freeplugin&utm_campaign=freepluginwporg&utm_content=ecpt-settings" target="_blank">',
					'</a>'
				)
			);
			?>
		</p>
		<p>
			<?php esc_html_e( 'Password Protected Categories lets you restrict access to any or all of your categories or taxonomies.', 'easy-post-types-fields' ); ?>
		</p>
		<a class="promo-link" href="https://barn2.com/wordpress-plugins/password-protected-categories/?utm_source=wporg&utm_medium=freeplugin&utm_campaign=freepluginwporg&utm_content=ecpt-settings" target="_blank">
			<img class="promo-image" src="<?php echo esc_url( $image_url ); ?>" />
		</a>
	</div>
</div>

<?php
