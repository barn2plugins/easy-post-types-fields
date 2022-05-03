<?php
/**
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin\Wizard;

use Barn2\Plugin\Easy_Post_Types_Fields\Util;
use Barn2\EPT_Lib\Plugin\Simple_Plugin;
use Barn2\EPT_Lib\Registerable;
use Barn2\EPT_Lib\Util as Lib_Util;

class Setup_Wizard implements Registerable {

	private $plugin;

	private $wizard;

	public function __construct( Simple_Plugin $plugin ) {
		$this->plugin = $plugin;
		$request      = Util::get_page_request();
		$action_steps = [
			'setup' => [ 'Welcome', 'EPT_Name', 'EPT_Features', 'Upsell', 'Completed' ],
			'add'   => [ 'EPT_Name', 'EPT_Features', 'Completed' ],
		];
		$action       = isset( $request['action'] ) && isset( $action_steps[ $request['action'] ] ) ? $request['action'] : 'setup';

		$steps = array_map(
			function( $s ) {
				$step_class = __NAMESPACE__ . '\Steps\\' . $s;
				return new $step_class();
			},
			$action_steps[ $action ]
		);

		$wizard = new Wizard( $this->plugin, $steps, false );

		$wizard->configure(
			[
				'admin_url'   => admin_url(),
				'skip_url'    => admin_url( 'admin.php?page=ept_post_types' ),
				'utm_id'      => 'ept',
				'woocommerce' => false,
			]
		);

		$script_dependencies = Lib_Util::get_script_dependencies( $this->plugin, 'admin/wizard-library.min.js' );
		$wizard->set_non_wc_asset(
			$plugin->get_dir_url() . 'assets/js/admin/wizard-library.min.js',
			$script_dependencies['dependencies'],
			$script_dependencies['version']
		);
		$wizard->add_custom_asset(
			$plugin->get_dir_url() . 'assets/js/admin/wizard-custom.min.js',
			$script_dependencies
		);

		$this->wizard = $wizard;

		add_filter(
			'admin_body_class',
			function( $class ) use ( $action ) {
				$class .= ' ' . $this->plugin->get_slug() . "-wizard-$action";

				return $class;
			}
		);
	}

	public function register() {
		$this->wizard->boot();
	}

	public function enqueue_additional_scripts( $hook_suffix ) {
		// wp_enqueue_style( 'ept-setup-wizard-addons', $this->plugin->get_dir_url() . 'assets/css/admin/ept-wizard.min.css', [ $this->wizard->get_slug() ], $this->plugin->get_version() );
		// wp_enqueue_script( 'ept-setup-wizard', $this->plugin->get_dir_url() . 'assets/js/admin/wizard.min.js', [], $this->plugin->get_version(), true );
	}

}
