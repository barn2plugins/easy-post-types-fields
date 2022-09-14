<?php
/**
 * The class defining the Upsell step of the Setup Wizard
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin\Wizard\Steps;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Barn2\Setup_Wizard\Step;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Barn2\Setup_Wizard\Steps\Cross_Selling;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Barn2\Setup_Wizard\Util;

/**
 * {@inheritdoc}
 */
class Upsell extends Cross_Selling {
	// URL of the api from where upsells are pulled from.
	const REST_URL = 'https://barn2.com/wp-json/upsell/v1/get/';

	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		$this->set_id( 'more' );
		$this->set_name( esc_html__( 'More', 'easy-post-types-fields' ) );
		$this->set_description(
			sprintf(
				// translators: %1$s: URL to All Access Pass page %2$s: URL to the KB about the upgrading process
				__( 'Enhance your store with these fantastic plugins from Barn2, or get them all with an <a href="%1$s" target="_blank">All Access Pass<a/>! (<a href="%2$s" target="_blank">learn how here</a>)', 'easy-post-types-fields' ),
				Util::generate_utm_url( 'https://barn2.com/wordpress-plugins/bundles/', 'ept' ),
				Util::generate_utm_url( 'https://barn2.com/kb/how-to-upgrade-license/', 'ept' )
			)
		);
		$this->set_title( esc_html__( 'Extra features', 'easy-post-types-fields' ) );
	}
}
