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
$fields            = Util::get_post_type_custom_fields( $request_post_type );

$data = array_fill_keys( [ 'name', 'slug', 'type', 'previous_slug' ], '' );

$field = array_filter(
	$fields,
	function( $f ) use ( $request ) {
		return $request['slug'] === $f['slug'];
	}
);

if ( $field ) {
	$field                 = reset( $field );
	$data                  = array_merge( $data, $field );
	$data['previous_slug'] = $field['slug'];
}

if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'save_list_item_postdata' ) ) {
	$postdata = array_intersect_key( $data, $_POST );
	$data     = array_merge( $data, $_POST );
}

?>

<fieldset>
	<label>
		<span class="label"><?php esc_html_e( 'Name', 'easy-post-types-fields' ); ?></span>
		<span class="input">
			<input class="sluggable" type="text" placeholder="Field name (e.g. My custom field)" name="name" value="<?php echo esc_attr( $data['name'] ); ?>" />
		</span>
	</label>
	<label>
		<span class="label"><?php esc_html_e( 'Slug', 'easy-post-types-fields' ); ?></span>
		<span class="input">
			<input class="slug" type="text" name="slug" value="<?php echo esc_attr( $data['slug'] ); ?>" />
		</span>
	</label>
	<label>
		<span class="label"><?php esc_html_e( 'Type', 'easy-post-types-fields' ); ?></span>
		<span class="input">
			<select name="type">
				<option name="type-text" value="text" <?php selected( $data['type'], 'text' ); ?>><?php esc_html_e( 'Text', 'easy-post-types-fields' ); ?></option>
				<option name="type-editor" value="editor" <?php selected( $data['type'], 'editor' ); ?>><?php esc_html_e( 'WYSIWYG editor', 'easy-post-types-fields' ); ?></option>
			</select>
		</span>
	</label>
	<input type="hidden" name="previous_slug" value="<?php echo esc_attr( $data['previous_slug'] ); ?>" />
</fieldset>

