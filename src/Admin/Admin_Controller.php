<?php

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Plugin\Simple_Plugin;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Registerable;
use	Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Service\Standard_Service;
use	Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Service\Service_Container;
use	Barn2\Plugin\Easy_Post_Types_Fields\Util;

/**
 * General admin functions for Easy Post Types and Fields.
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Admin_Controller implements Registerable, Standard_Service {

	use Service_Container;

	/**
	 * The main plugin instance
	 *
	 * @var Simple_Plugin
	 */
	private $plugin;

	/**
	 * Constructor
	 *
	 * @param  Simple_Plugin $plugin The main instance of this plugin
	 * @return void
	 */
	public function __construct( Simple_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * {@inheritdoc}
	 */
	public function add_services() {
		$this->add_service( 'cpt_editor', new CPT_Editor( $this->plugin ) );
		$this->add_service( 'wizard', new Wizard\Setup_Wizard( $this->plugin ) );
		$this->add_service( 'review_notice', new Review_Notice( $this->plugin ) );
		$this->add_service( 'taxonomy_columns', new Taxonomy_Columns() );
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_action( 'admin_enqueue_scripts', [ $this, 'load_scripts' ] );
		add_filter( 'plugin_action_links_' . $this->plugin->get_basename(), [ $this, 'add_settings_link' ] );
		$this->register_services();
		$this->start_all_services();
	}

	/**
	 * Enqueue the stylesheet required by this plugin
	 *
	 * @param  string $hook The value of the page query argument
	 * @return void
	 */
	public function load_scripts( $hook ) {
		$screen = get_current_screen();

		if ( 'post' === $screen->base ) {
			wp_enqueue_style( 'ept-post-editor', plugin_dir_url( $this->plugin->get_file() ) . 'assets/css/admin/ept-post-editor.css', [], $this->plugin->get_version() );
		}
	}

	/**
	 * Add a link to the main page of this plugin to the list of links in the
	 * name column of the plugins table list
	 *
	 * @param  array $links The list of links being filtered
	 * @return array
	 */
	public function add_settings_link( $links ) {
		$settings_url = Util::get_manage_page_url();

		array_unshift( $links, sprintf( '<a href="%1$s">%2$s</a>', esc_url( $settings_url ), __( 'Settings', 'easy-post-types-fields' ) ) );

		return $links;
	}
}
