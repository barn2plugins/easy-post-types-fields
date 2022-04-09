<?php
/**
 * The HTML markup of the Manage page
 *
 * @param Post_Type_List_Table $post_type_list_table The list table instance (a subclass of WP_List_Table)
 * @param Barn2\EPT_Lib\Plugin\Plugin $plugin The main instance of the plugin
 * @param string $new_link The link to add a new post type
 */
namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

use Barn2\Plugin\Easy_Post_Types_Fields\Util;

defined( 'ABSPATH' ) || exit;

$request_post_type = Util::get_post_type_by_name( $request['post_type'] );

submit_button(
	sprintf(
		__( '%s field', 'easy-post-types-fields' ),
		'add' === $request['action'] ? __( 'Add' ) : __( 'Update' )
	)
);

