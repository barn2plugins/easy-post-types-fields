<?php
/**
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin\Wizard\Steps;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Barn2\Setup_Wizard\Steps\Ready;

class Completed extends Ready {

	public function __construct() {
		parent::__construct();
		$this->set_name( esc_html__( 'Ready', 'easy-post-types-fields' ) );
		// translators: the plural name of a post type
		$this->set_description( __( 'What would you like to do next?', 'easy-post-types-fields' ) );
		// translators: the singular name of a post type
		$this->set_title( esc_html__( 'The %s post type is ready!', 'easy-post-types-fields' ) );
	}

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
