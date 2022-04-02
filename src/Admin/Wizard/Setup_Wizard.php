<?php
/**
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin\Wizard;

use Barn2\EPT_Lib\Plugin\Simple_Plugin;
use Barn2\EPT_Lib\Registerable;
use Barn2\EPT_Lib\Util as Lib_Util;

class Setup_Wizard implements Registerable {

	private $plugin;

	private $wizard;

	public function __construct( Simple_Plugin $plugin ) {

		$this->plugin = $plugin;

		$steps = [
			new Steps\Welcome(),
			new Steps\Upsell(),
			new Steps\Completed(),
		];

		$wizard = new Wizard( $this->plugin, $steps );


		$args = [
			'admin_url' => admin_url(),
			'skip_url'  => admin_url( 'admin.php?page=ept_post_types' ),
			'utm_id'    => 'ept',
		];

		if ( isset( $_REQUEST['action'] ) ) {
			$args['action'] = $_REQUEST['action'];
		}

		$wizard->configure( $args );

		$wizard->add_custom_asset(
			$plugin->get_dir_url() . 'assets/js/admin/wizard.min.js',
			Lib_Util::get_script_dependencies( $this->plugin, 'admin/wizard.min.js' )
		);

		$this->wizard = $wizard;
	}

	public function register() {
		$this->wizard->boot();

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_additional_scripts' ], 21 );
	}

	public function enqueue_additional_scripts( $hook_suffix ) {
		// wp_enqueue_style( 'ept-setup-wizard-addons', $this->plugin->get_dir_url() . 'assets/css/admin/ept-wizard.min.css', [ $this->wizard->get_slug() ], $this->plugin->get_version() );
		// wp_enqueue_script( 'ept-setup-wizard', $this->plugin->get_dir_url() . 'assets/js/admin/wizard.min.js', [], $this->plugin->get_version(), true );
	}

}
