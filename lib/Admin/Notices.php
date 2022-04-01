<?php

namespace Barn2\EPT_Lib\Admin;

/**
 * Extends the WPTRT Notices class to allow additional HTML in the admin notice.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Notices extends \WPTRT\AdminNotices\Notices {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'wptrt_admin_notices_allowed_html', [ __CLASS__, 'filter_allowed_html' ] );
	}

	/**
	 * Filter allowed html in notices.
	 *
	 * @param array $allowed_html
	 * @return array
	 */
	public static function filter_allowed_html( $allowed_html ) {
		$allowed_html['a']['target'] = [];
		return $allowed_html;
	}

}
