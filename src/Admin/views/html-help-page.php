<?php
/**
 * The HTML markup of the Help page
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

defined( 'ABSPATH' ) || exit;

?>

<div class="barn2-plugins-settings">
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_attr_e( 'Get Help with Easy Post Types and Fields', 'easy-post-types-fields' ); ?></h1>
		<hr class="wp-header-end">
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
					// translators: opening and closing tag of the anchor element
					__( 'Easy Post Types and Fields is developed and supported by %1$sBarn2 Plugins%2$s.', 'easy-post-types-fields' ),
					'<a href="https://barn2.com/?utm_source=settings&utm_medium=settings&utm_campaign=settingsinline&utm_content=ecpt-settings-help" target="_blank">',
					'</a>'
				)
			);
			?>
		</p>
		<ul class="ul-disc">
			<li>
				<?php
				echo wp_kses_post(
					sprintf(
						// translators: opening and closing tag of the anchor element
						__( 'The %1$splugin documentation%2$s is an excellent resource to help you learn how to use custom post types, fields and taxonomies.', 'easy-post-types-fields' ),
						'<a href="https://barn2.com/kb-categories/easy-custom-post-types-kb/?utm_source=settings&utm_medium=settings&utm_campaign=settingsinline&utm_content=ecpt-settings-help" target="_blank">',
						'</a>'
					)
				);
				?>
			</li>
			<li>
				<?php
				echo wp_kses_post(
					sprintf(
						// translators: opening and closing tag of the anchor element
						__( 'If you need help displaying the custom post types and data on the front end of your website then we recommend the %1$sPosts Table Pro plugin%2$s.', 'easy-post-types-fields' ),
						'<a href="https://barn2.com/kb-categories/easy-custom-post-types-kb/?utm_source=settings&utm_medium=settings&utm_campaign=settingsinline&utm_content=ecpt-settings-help" target="_blank">',
						'</a>'
					)
				);
				?>
			</li>
			<li>
				<?php
				echo wp_kses_post(
					sprintf(
						// translators: opening and closing tag of the anchor element
						__( 'To get help with a specific issue, you can ask in the %1$ssupport forum%2$s or %3$ssubmit a support request%4$s.', 'easy-post-types-fields' ),
						'<a href="https://wordpress.org/support/plugin/easy-post-types-fields/" target="_blank">',
						'</a>',
						'<a href="https://barn2.com/wordpress-plugins/easy-cpt-support-request/?utm_source=settings&utm_medium=settings&utm_campaign=settingsinline&utm_content=ecpt-settings-help" target="_blank">',
						'</a>'
					)
				);
				?>
			</li>
		</ul>
	</div>

	<?php
	do_action( 'barn2_after_plugin_settings', $plugin->get_id() );
	?>
</div>

<?php
