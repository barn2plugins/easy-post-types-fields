<?php
/**
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin\Wizard;

use Barn2\Plugin\Easy_Post_Types_Fields\Util\Settings;
use Barn2\EPT_Lib\Plugin\Simple_Plugin;
use Barn2\EPT_Lib\Registerable;

class Setup_Wizard implements Registerable {

	private $plugin;

	private $wizard;

	public function __construct( Simple_Plugin $plugin ) {

		$this->plugin = $plugin;

		$steps = [
			new Steps\Upsell(),
			new Steps\Completed(),
		];

		$wizard = new Wizard( $this->plugin, $steps );

		$wizard->configure(
			[
				'skip_url' => admin_url( 'admin.php?page=ept_post_types' ),
				'utm_id'   => 'ept',
			]
		);

		$wizard->add_restart_link( 'ept_post_types', 'ept_post_types' );

		$this->wizard = $wizard;
	}

	public function register() {
		$this->wizard->boot();

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_additional_scripts' ], 21 );
	}

	public function enqueue_additional_scripts( $hook_suffix ) {
		if ( 'toplevel_page_' . $this->wizard->get_slug() !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style( 'ept-setup-wizard-addons', $this->plugin->get_dir_url() . 'assets/css/admin/ept-wizard.min.css', [ $this->wizard->get_slug() ], $this->plugin->get_version() );
	}

}
