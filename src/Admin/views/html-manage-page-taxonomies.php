<?php
/**
 * The HTML markup of the taxonomy input form
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

use Barn2\Plugin\Easy_Post_Types_Fields\Util;

defined( 'ABSPATH' ) || exit;

$request_post_type    = Util::get_post_type_by_name( $request['post_type'] );
$current_taxonomy     = isset( $request['slug'] ) ? get_taxonomy( "{$request['post_type']}_{$request['slug']}" ) : false;
$hierarchical_tooltip = Util::get_tooltip( __( 'Hierarchical taxonomies have a nested parent/child structure like WordPress post categories, whereas non-hierarchical taxonomies are flat like tags.', 'easy-post-types-fields' ) );

$data = array_fill_keys( [ 'name', 'singular_name', 'slug', 'hierarchical', 'previous_slug' ], '' );

if ( $current_taxonomy ) {
	$data = [
		'name'          => $current_taxonomy->labels->name,
		'singular_name' => $current_taxonomy->labels->singular_name,
		'slug'          => $request['slug'],
		'hierarchical'  => $current_taxonomy->hierarchical,
		'previous_slug' => $request['slug'],
	];
}

if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'save_list_item_postdata' ) ) {
	$postdata = array_intersect_key( $_POST, $data );
	$data     = array_merge( $data, $postdata );
}

?>

<fieldset>
	<label>
		<span class="label"><?php esc_html_e( 'Singular name', 'easy-post-types-fields' ); ?></span>
		<span class="input">
			<input class="sluggable" type="text" required placeholder="<?php esc_attr_e( 'e.g. Category', 'easy-post-types-fields' ); ?>" name="singular_name" value="<?php echo esc_attr( $data['singular_name'] ); ?>" />
		</span>
	</label>
	<label>
		<span class="label"><?php esc_html_e( 'Plural name', 'easy-post-types-fields' ); ?></span>
		<span class="input">
			<input type="text" required placeholder="<?php esc_attr_e( 'e.g. Categories', 'easy-post-types-fields' ); ?>" name="name" value="<?php echo esc_attr( $data['name'] ); ?>" />
		</span>
	</label>
	<label>
		<span class="label"><?php esc_html_e( 'Slug', 'easy-post-types-fields' ); ?></span>
		<span class="input">
			<input class="slug" type="text" required name="slug" maxlength="<?php echo esc_attr( $maxlength ); ?>" value="<?php echo esc_attr( $data['slug'] ); ?>" />
		</span>
	</label>
	<label>
		<span class="label"><?php esc_html_e( 'Hierarchical', 'easy-post-types-fields' ); ?></span>
		<?php echo wp_kses_post( $hierarchical_tooltip ); ?>
		<span class="input">
			<input type="checkbox" name="hierarchical" <?php checked( $data['hierarchical'] ); ?> />
		</span>
	</label>
	<input type="hidden" name="previous_slug" value="<?php echo esc_attr( $data['previous_slug'] ); ?>" />
</fieldset>
