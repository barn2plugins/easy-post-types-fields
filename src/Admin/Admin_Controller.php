<?php

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

use Barn2\EPT_Lib\Plugin\Simple_Plugin,
	Barn2\EPT_Lib\Registerable,
	Barn2\EPT_Lib\Service,
	Barn2\EPT_Lib\Service_Container;

/**
 * General admin functions for Easy Post Types and Fields.
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Admin_Controller implements Registerable, Service {

	use Service_Container;

	/**
	 * The main plugin instance
	 *
	 * @var Simple_Plugin
	 */
	private $plugin;

	public function __construct( Simple_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	public function get_services() {
		$services = [
			'cpt_editor' => new CPT_Editor( $this->plugin ),
			'wizard'     => new Wizard\Setup_Wizard( $this->plugin ),
		];

		return $services;
	}

	public function register() {
		$this->register_services();
	}

}
