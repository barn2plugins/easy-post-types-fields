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

	/**
	 * Constructor
	 *
	 * @param array $field The field properties
	 * @param string $post_type The slug of the post type this field is registered to
	 * @param array $args A list of arguments for the custom field registration
	 * @return void
	 */
	public function __construct( $field, $post_type, $args = [] ) {
		$this->post_type = $post_type;
		$this->slug      = $field['slug'];
		$this->key       = "{$this->post_type}_{$field['slug']}";
		$this->name      = $field['name'];

		if ( $this->prepare_arguments( $args ) ) {
			$this->register_field();
		}
	}

	/**
	 * Prepare the arguments for the custom field registration
	 *
	 * The list of arguments is returned by the method after filter callbacks
	 * are invoked to alter the default definition of the arguments
	 *
	 * @param  array $args The list of arguments
	 * @return array
	 */
	public function prepare_arguments( $args ) {
		if ( empty( $this->args ) ) {
			$default_args = [
				'type'         => 'string',
				'single'       => true,
				'show_in_rest' => true,
			];

			/**
			 * Filter the arguments to register a custom field
			 *
			 * The variable part of the hook is the slug of the field
			 * (which is prefixed with `$post_type`). For example, the full slug
			 * of a `link` field registered to an `article` custom post
			 * type will be `ept_article_link`. When adding a custom
			 * field to a post type registered by WordPress or by a third-party
			 * plugin, the prefix is simply the slug of the post type. For
			 * example, the full slug of a custom 'link' field registered to
			 * the 'post' post type would be `post_link`.
			 *
			 * @param array $args The list of argumets to register this custom field
			 */
			$this->args = apply_filters(
				"ept_field_{$this->key}_args",
				wp_parse_args(
					$args,
					$default_args
				)
			);
		}

		return $this->args;
	}

	/**
	 * Register a custom field to a post type
	 *
	 * @return void
	 */
	public function register_field() {
		register_post_meta(
			$this->post_type,
			$this->key,
			$this->args
		);
	}
}
