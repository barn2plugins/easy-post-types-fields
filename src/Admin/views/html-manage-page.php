<?php
/**
 * The HTML markup of the main Manage page
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
	<div class="barn2-settings-inner">
		<?php
		require 'html-manage-page-header.php';
		require "html-manage-page-$content.php";
		?>
	</div>
</div>

<?php
