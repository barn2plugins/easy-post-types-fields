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
		$this->set_title( esc_html__( 'Complete Setup', 'easy-post-types-fields' ) );
		$this->set_description( esc_html__( 'Congratulations, you have finished setting up the plugin!', 'easy-post-types-fields' ) );
	}

	public function setup_fields() {
		return [
			'post_type'     => [
				'type'  => 'text',
				'value' => 'ept_testimonial',
			],
			'name'          => [
				'type'  => 'text',
				'value' => 'Testimonials',
			],
			'singular_name' => [
				'type'  => 'text',
				'value' => 'Testimonial',
			],
		];
	}

}
