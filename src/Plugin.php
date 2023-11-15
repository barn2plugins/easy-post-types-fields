<?php
namespace Barn2\Plugin\Easy_Post_Types_Fields;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Plugin\Simple_Plugin,
	Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Registerable,
	Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Translatable,
	Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Util as Lib_Util;

/**
 * The main plugin class for Easy Post Types and Fields.
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Plugin extends Simple_Plugin implements Registerable, Translatable {

	const NAME    = 'Easy Post Types and Fields';
	const ITEM_ID = 430157;

	private $services;

	/**
	 * Constructor
	 *
	 * @param  string $file The path of the main plugin file
	 * @param  string $version The current version of the plugin
	 * @return void
	 */
	public function __construct( $file = null, $version = null ) {
		parent::__construct(
			[
				'name'               => self::NAME,
				'item_id'            => self::ITEM_ID,
				'version'            => $version,
				'file'               => $file,
				'settings_path'      => 'admin.php?page=ept_post_types',
				'documentation_path' => 'kb-categories/easy-post-types-fields-kb/?utm_source=settings&utm_medium=settings&utm_campaign=settingsinline&utm_content=ecpt-settings',
			]
		);

		$this->add_service( 'plugin_setup', new Admin\Plugin_Setup( $this->get_file(), $this ), true );

	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		parent::register();

		add_action( 'plugins_loaded', [ $this, 'add_services' ] );

		add_action( 'init', [ $this, 'register_services' ] );
		add_action( 'init', [ $this, 'load_textdomain' ], 5 );
	}

	public function add_services() {
		if ( Lib_Util::is_admin() ) {
			$this->add_service( 'admin/controller', new Admin\Admin_Controller( $this ) );
		}

		$this->add_service( 'post_type_factory', new Post_Type_Factory( $this ) );
		$this->add_service( 'ptp_integration', new Integration\Barn2_Table_Plugin() );
	}

	/**
	 * {@inheritdoc}
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'easy-post-types-fields', false, $this->get_slug() . '/languages' );
	}

	/**
	 * Return the local path of the `Admin` folder under the plugin root folder
	 *
	 * @param  string $file The subpath located in the Admin folder
	 * @return string
	 */
	public function get_admin_path( $file ) {
		return wp_normalize_path( $this->get_dir_path() . '/src/Admin/' . $file );
	}

}
