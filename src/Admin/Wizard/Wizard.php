<?php
/**
 * The main Setup Wizard class
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin\Wizard;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Setup_Wizard\Interfaces\Restartable;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Setup_Wizard\Setup_Wizard;

/**
 * {@inheritdoc}
 */
class Wizard extends Setup_Wizard implements Restartable {

	/**
	 * {@inheritdoc}
	 */
	public function on_restart() {
		check_ajax_referer( 'barn2_setup_wizard_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'error_message' => __( 'You are not authorized.', 'easy-post-types-fields' ) ], 403 );
		}
	}


	/**
	 * {@inheritdoc}
	 */
	public function enqueue_assets( $hook ) {

		if ( $hook !== 'toplevel_page_' . $this->get_slug() ) {
			return;
		}

		$slug = 'ept-wizard';

		$styling_dependencies = [ 'wp-components' ];

		$custom_asset = $this->get_custom_asset();

		if ( isset( $custom_asset['url'] ) ) {
			if ( isset( $custom_asset['dependencies'] ) && ! isset( $custom_asset['dependencies']['dependencies'] ) ) {
				$custom_asset_dependencies = $custom_asset['dependencies'];
			} else {
				$custom_asset_dependencies = $custom_asset['dependencies']['dependencies'];
			}

			if ( empty( $custom_asset_dependencies ) || ! is_array( $custom_asset_dependencies ) ) {
				wp_die( 'Custom asset dependencies should not be empty and should be an array.' );
			}

			wp_enqueue_script( $slug, $custom_asset['url'], $custom_asset_dependencies, $this->get_non_wc_version(), true );
			wp_add_inline_script( $slug, 'const barn2_setup_wizard = ' . wp_json_encode( $this->get_js_args() ), 'before' );
		}

		wp_enqueue_script( "{$slug}-library", $this->get_non_wc_asset(), $this->get_non_wc_dependencies(), $this->get_non_wc_version(), true );

		wp_enqueue_style( "setup-wizard", plugin_dir_url( __DIR__ ) . '../../assets/css/admin/wizard.css', false, $this->get_non_wc_version() );
		wp_enqueue_style( $slug, $this->get_library_url() . 'build/main.css', $styling_dependencies, filemtime( $this->get_library_path() . '/build/main.css' ) );
	}

}
