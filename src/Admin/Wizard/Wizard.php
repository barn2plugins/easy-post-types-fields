<?php
/**
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin\Wizard;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Barn2\Setup_Wizard\Interfaces\Restartable;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Barn2\Setup_Wizard\Setup_Wizard;

/**
 * WPS Setup wizard.
 */
class Wizard extends Setup_Wizard implements Restartable {

	/**
	 * On wizard restart, detect which pages should be automatically unhidden.
	 *
	 * @return void
	 */
	public function on_restart() {
		check_ajax_referer( 'barn2_setup_wizard_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'error_message' => __( 'You are not authorized.', 'easy-post-types-fields' ) ], 403 );
		}

		wp_send_json_success(
			[
				'step' => true
			]
		);

	}

}
