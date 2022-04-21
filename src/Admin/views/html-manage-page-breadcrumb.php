<?php
/**
 * The HTML markup of the breadcrumbs
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
