<?php
/**
 * The HTML markup of the breadcrumbs
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

defined( 'ABSPATH' ) || exit;

if ( $breadcrumbs ) {
	?>

	<h2 class="wp-heading-inline ept-page-breadcrumbs">
		<?php echo $breadcrumbs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</h2>

	<?php
}
