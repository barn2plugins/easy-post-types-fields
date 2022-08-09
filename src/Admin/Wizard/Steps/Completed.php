<?php
/**
 * The class defining the Completed step of the Setup Wizard
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin\Wizard\Steps;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Barn2\Setup_Wizard\Steps\Ready;

/**
 * {@inheritdoc}
 */
class Completed extends Ready {

	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		parent::__construct();
		$this->set_name( esc_html__( 'Ready', 'easy-post-types-fields' ) );
		// translators: the plural name of a post type
		$this->set_description( __( 'Now, you can either add extra fields to your post type, or start adding new %s straight away.', 'easy-post-types-fields' ) );
		// translators: the singular name of a post type
		$this->set_title( esc_html__( 'The %s post type is ready!', 'easy-post-types-fields' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {
		return [
			'post_type'     => [
				'type'  => 'text',
				'value' => '',
			],
			'name'          => [
				'type'  => 'text',
				'value' => '',
			],
			'singular_name' => [
				'type'  => 'text',
				'value' => '',
			],
		];
	}

}
