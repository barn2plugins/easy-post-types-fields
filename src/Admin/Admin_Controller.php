<?php

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

use Barn2\EPT_Lib\Plugin\Simple_Plugin,
	Barn2\EPT_Lib\Registerable,
	Barn2\EPT_Lib\Service,
	Barn2\EPT_Lib\Service_Container,
	Barn2\Plugin\Easy_Post_Types_Fields\Util;

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

		add_action( 'admin_enqueue_scripts', [ $this, 'load_scripts' ] );
		add_filter( 'plugin_action_links_' . $this->plugin->get_basename(), [ $this, 'add_settings_link' ] );
	}

	public function load_scripts( $hook ) {
		$screen = get_current_screen();

		if ( 'post' === $screen->base ) {
			wp_enqueue_style( 'ept-post-editor', plugin_dir_url( $this->plugin->get_file() ) . 'assets/css/admin/ept-post-editor.min.css', [], $this->plugin->get_version() );
		}
	}

	public function add_settings_link( $links ) {
		$settings_url = Util::get_manage_page_url();

		array_unshift( $links, sprintf( '<a href="%1$s">%2$s</a>', esc_url( $settings_url ), __( 'Settings', 'easy-post-types-fields' ) ) );

		return $links;
	}
}
