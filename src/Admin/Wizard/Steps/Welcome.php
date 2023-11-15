<?php
/**
 * The class defining the Welcome step of the Setup Wizard
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin\Wizard\Steps;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Setup_Wizard\Steps\Welcome_Free;

/**
 * {@inheritdoc}
 */
class Welcome extends Welcome_Free {

	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		$this->set_id( 'welcome' );
		$this->set_name( esc_html__( 'Welcome', 'easy-post-types-fields' ) );
		$this->set_title( esc_html__( 'Welcome to Easy Post Types and Fields', 'easy-post-types-fields' ) );
		$this->set_description( esc_html__( 'Use this setup wizard to quickly create a post type (e.g. Events, Members, Articles, or something else). Your post type will have its own section in the WordPress admin. If you want to add fields to existing post types, then skip the setup wizard and go to the Other Post Types section.', 'easy-post-types-fields' ) );
	}
}
