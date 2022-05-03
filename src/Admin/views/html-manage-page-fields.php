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

$fields = Util::get_custom_fields( $request['post_type'] );
$data   = array_fill_keys( [ 'name', 'slug', 'type', 'previous_slug' ], '' );
$field  = array_filter(
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
	$postdata = array_intersect_key( $_POST, $data );
	$data     = array_merge( $data, $postdata );
}

?>

<fieldset>
	<label>
		<span class="label"><?php esc_html_e( 'Name', 'easy-post-types-fields' ); ?></span>
		<span class="input">
			<input class="sluggable" type="text" required placeholder="Field name (e.g. My custom field)" name="name" value="<?php echo esc_attr( $data['name'] ); ?>" />
		</span>
	</label>
	<label>
		<span class="label"><?php esc_html_e( 'Slug', 'easy-post-types-fields' ); ?></span>
		<span class="input">
			<input class="slug" type="text" required name="slug" value="<?php echo esc_attr( $data['slug'] ); ?>" />
		</span>
	</label>
	<label>
		<span class="label"><?php esc_html_e( 'Type', 'easy-post-types-fields' ); ?></span>
		<span class="input">
			<select name="type">
				<?php
				foreach ( Util::get_custom_field_types() as $field_type => $field_label ) {
					?>

					<option name="type-<?php echo esc_attr( $field_type ); ?>" value="<?php echo esc_attr( $field_type ); ?>" <?php selected( $data['type'], $field_type ); ?>><?php echo esc_html( $field_label ); ?></option>

					<?php
				}
				?>
			</select>
		</span>
	</label>
	<input type="hidden" name="previous_slug" value="<?php echo esc_attr( $data['previous_slug'] ); ?>" />
</fieldset>
