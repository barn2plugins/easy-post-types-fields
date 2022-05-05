<?php
/**
 * The HTML markup of the post type edit form
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

use Barn2\Plugin\Easy_Post_types_Fields\Util;

defined( 'ABSPATH' ) || exit;

$form_request_args = $request;
$post_type_obj     = Util::get_post_type_object( $request['post_type'] );
$data              = array_fill_keys( [ 'name', 'singular_name', 'slug', 'supports' ], '' );
$data['supports']  = Util::get_default_post_type_support();

if ( $post_type_obj ) {
	$data = [
		'name'          => get_post_meta( $post_type_obj->ID, '_ept_plural_name', true ),
		'singular_name' => $post_type_obj->post_title,
		'slug'          => $post_type_obj->post_name,
		'supports'      => get_post_meta( $post_type_obj->ID, '_ept_supports', true ),
		'previous_slug' => $post_type_obj->post_name,
	];
}

if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'save_list_item_postdata' ) ) {
	$postdata = array_intersect_key( $_POST, $data );
	$data     = array_merge( $data, $postdata );
}

?>

<form action="" method="post" class="ept-list-item">
	<fieldset>
		<label>
			<span class="label"><?php esc_html_e( 'Singular name', 'easy-post-types-fields' ); ?></span>
			<span class="input">
				<input class="sluggable" type="text" required placeholder="e.g. Article" name="singular_name" value="<?php echo esc_attr( $data['singular_name'] ); ?>" />
			</span>
		</label>
		<label>
			<span class="label"><?php esc_html_e( 'Plural name', 'easy-post-types-fields' ); ?></span>
			<span class="input">
				<input type="text" required placeholder="e.g. Articles" name="name" value="<?php echo esc_attr( $data['name'] ); ?>" />
			</span>
		</label>
		<label>
			<span class="label"><?php esc_html_e( 'Slug', 'easy-post-types-fields' ); ?></span>
			<span class="input">
				<input class="slug" type="text" required name="slug" maxlength="<?php echo esc_attr( $maxlength ); ?>" value="<?php echo esc_attr( $data['slug'] ); ?>" />
			</span>
		</label>
		<span>
			<span class="label"><?php esc_html_e( 'Features', 'easy-post-types-fields' ); ?></span>
			<span class="input cb-container">
				<?php
				foreach ( Util::get_post_type_support() as $feature => $feature_label ) {
					?>
					<label class="checkbox-label">
						<input type="checkbox" name="supports[<?php echo esc_attr( $feature ); ?>]" <?php disabled( 'title' === $feature ); ?> <?php checked( 'title' === $feature || in_array( $feature, $data['supports'], true ) ); ?> />
						<?php echo esc_html( $feature_label ); ?>
					</label>
					<?php
				}
				?>
			</span>
		</span>
		<input type="hidden" name="previous_slug" value="<?php echo esc_attr( $data['previous_slug'] ); ?>" />
	</fieldset>
	<?php

	wp_nonce_field( 'save_list_item_postdata' );
	submit_button(
		__( 'Update post type', 'easy-post-types-fields' ),
		'primary',
		'submit',
		false
	);

	?>

	<input type="hidden" name="_first_referer" value="<?php echo esc_url( $referer ); ?>" />
	<a href="<?php echo esc_url( $referer ); ?>" class="button"><?php esc_html_e( 'Cancel', 'easy-post-types-fields' ); ?></a>
</form>

<?php
