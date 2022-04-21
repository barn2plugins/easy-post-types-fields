<?php
namespace Barn2\Plugin\Easy_Post_Types_Fields;

/**
 * The class registering a new Custom Post Type.
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Field {

	/**
	 * The key of this taxonomy, including the post_type name
	 *
	 * @var string
	 */
	private $key;

	/**
	 * The slug of this taxonomy
	 *
	 * @var string
	 */
	private $slug;

	/**
	 * The name (generally plural) of the CPT as defined in $args['labels']['name']
	 *
	 * @var string
	 */
	private $name;

	/**
	 * The type of this field (it can be 'text' or 'editor')
	 *
	 * @var bool
	 */
	private $type;

	/**
	 * The post type of the CPT
	 *
	 * @var string
	 */
	private $post_type;

	/**
	 * The arguments for the custom field registration
	 *
	 * @var array
	 */
	private $args = [];

	/**
	 * Whether the custom field has been successfully registered or not
	 *
	 * @var bool
	 */
	private $is_registered;

	public function __construct( $field, $post_type, $args = [] ) {
		$this->post_type = $post_type;
		$this->slug      = $field['slug'];
		$this->key       = "{$this->post_type}_{$field['slug']}";
		$this->name      = $field['name'];

		if ( $this->prepare_arguments( $args ) ) {
			$this->register_field();
		}
	}

	public function prepare_arguments( $args ) {
		if ( empty( $this->args ) ) {
			$default_args = [
				'object_subtype' => $this->post_type,
				'type'           => 'string',
				'single'         => true,
				'show_in_rest'   => true,
			];

			$this->args = apply_filters(
				"{$this->key}_meta_args",
				wp_parse_args(
					$args,
					$default_args
				)
			);
		}

		return $this->args;
	}

	public function register_field() {
		register_meta(
			'post',
			$this->key,
			$this->args
		);
	}
}
