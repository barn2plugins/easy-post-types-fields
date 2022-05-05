<?php
/**
 * A class determining the conditions under which the Setup Wizard should be started
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin\Wizard;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Barn2\Setup_Wizard\Starter as Setup_Wizard_Starter;

/**
 * {@inheritdoc}
 */
class Starter extends Setup_Wizard_Starter {

	/**
	 * {@inheritdoc}
	 */
	public function should_start() {
		$setup_happened = get_option( 'easy-post-types-fields-setup-wizard_completed' ) ?: false;
		return ! $setup_happened;
	}

}
