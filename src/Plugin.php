<?php
namespace Barn2\Plugin\Easy_Post_Types_Fields;

use Barn2\EPT_Lib\Plugin\Simple_Plugin,
	Barn2\EPT_Lib\Registerable,
	Barn2\EPT_Lib\Translatable,
	Barn2\EPT_Lib\Util as Lib_Util;

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

	public function __construct( $file = null, $version = null ) {
		parent::__construct(
			[
				'name'               => self::NAME,
				'item_id'            => self::ITEM_ID,
				'version'            => $version,
				'file'               => $file,
				'settings_path'      => 'admin.php?page=ept_post_types',
				'documentation_path' => 'kb/easy-post-types-fields-documentation/?utm_source=settings&utm_medium=settings&utm_campaign=settingsinline&utm_content=ept-settings',
			]
		);

		$this->services = [
			'cpt_factory' => new CPT_Factory( $this ),
		];

		if ( Lib_Util::is_admin() ) {
			$this->services['admin/controller'] = new Admin\Admin_Controller( $this );
		}

		if ( Lib_Util::is_front_end() ) {

		}
	}

	/**
	 * Registers the plugin with WordPress.
	 */
	public function register() {
		$plugin_setup = new Admin\Plugin_Setup( $this->get_file(), $this );
		$plugin_setup->register();

		add_action( 'init', [ $this, 'load_plugin' ] );
		add_action( 'init', [ $this, 'load_textdomain' ], 5 );
	}

	public function load_plugin() {
		Lib_Util::register_services( $this->services );
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'easy-post-types-fields', false, $this->get_slug() . '/languages' );
	}

}
