<?php
/**
 * Setup the Wizard for the current plugin instance
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

use Barn2\Plugin\Easy_Post_Types_Fields\Admin\Wizard\Starter;
use Barn2\Plugin\Easy_Post_Types_Fields\Plugin;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Registerable;

/**
 * {@inheritdoc}
 */
class Plugin_Setup implements Registerable {
	/**
	 * Plugin's entry file
	 *
	 * @var string
	 */
	private $file;

	/**
	 * Wizard starter.
	 *
	 * @var Starter
	 */
	private $starter;

	/**
	 * Plugin instance
	 *
	 * @var Licensed_Plugin
	 */
	private $plugin;

	/**
	 * Get things started
	 *
	 * @param string $file
	 * @param Plugin $plugin The main plugin instance
	 */
	public function __construct( $file, Plugin $plugin ) {
		$this->file    = $file;
		$this->plugin  = $plugin;
		$this->starter = new Starter( $this->plugin );
	}

	/**
	 * Register the service
	 *
	 * @return void
	 */
	public function register() {
		register_activation_hook( $this->file, [ $this, 'on_activate' ] );
		add_action( 'admin_init', [ $this, 'after_plugin_activation' ] );
		register_uninstall_hook( $this->file, self::on_uninstall() ); 
	}

	/**
	 * On plugin activation determine if the setup wizard should run.
	 *
	 * @return void
	 */
	public function on_activate() {
		// Network wide.
		// phpcs:disable
		$network_wide = ! empty( $_GET['networkwide'] )
			? (bool) $_GET['networkwide']
			: false;
		// phpcs:enable

		if ( $this->starter->should_start() ) {
			$this->starter->create_transient();
		}
	}

	/**
	 * Do nothing.
	 *
	 * @return void
	 */
	public function on_deactivate() {}

	/** 
	 * Delete the option responsible for checking setup wizard run 
	 * 
	 * @return void 
	 */
	public static function on_uninstall() {
		delete_option( "_easy-post-types-fields_setup_wizard_seen" ); 
	}

	/**
	 * Detect the transient and redirect to wizard.
	 *
	 * @return void
	 */
	public function after_plugin_activation() {
		if ( ! $this->starter->detected() ) {
			return;
		}

		$this->starter->delete_transient();
		$this->starter->create_option(); 
		$this->starter->redirect();
	}
}
