<?php
/**
 * The HTML markup of the Manage page
 *
 * @param Post_Type_List_Table $post_type_list_table The list table instance (a subclass of WP_List_Table)
 * @param Barn2\EPT_Lib\Plugin\Plugin $plugin The main instance of the plugin
 * @param string $new_link The link to add a new post type
 */
namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

defined( 'ABSPATH' ) || exit;

?>

<form action="<?php echo esc_url( $form_action ); ?>" method="post" class="ept-list-item">
	<fieldset>
		<label>
			<span class="label"><?php esc_html_e( 'Name', 'easy-post-types-fields' ); ?></span>
			<span class="input">
				<input type="text" placeholder="e.g. Articles" name="name" value="<?php echo esc_attr( $name ); ?>" />
			</span>
		</label>
		<label>
			<span class="label"><?php esc_html_e( 'Singular name', 'easy-post-types-fields' ); ?></span>
			<span class="input">
				<input class="sluggable" type="text" placeholder="e.g. Article" name="singular_name" value="<?php echo esc_attr( $singular_name ); ?>" />
			</span>
		</label>
		<label>
			<span class="label"><?php esc_html_e( 'Slug', 'easy-post-types-fields' ); ?></span>
			<span class="input">
				<input class="slug" type="text" name="slug" maxlength="<?php echo esc_attr( $max ); ?>" value="<?php echo esc_attr( $slug ); ?>" />
			</span>
		</label>
		<input type="hidden" name="previous_slug" value="<?php echo esc_attr( $previous_slug ); ?>" />
	</fieldset>
	<?php

	wp_nonce_field( 'save_list_item_postdata' );
	submit_button(
		sprintf(
			__( '%s post type', 'easy-post-types-fields' ),
			'add' === $request['action'] ? __( 'Add', 'easy-post-types-fields' ) : __( 'Update', 'easy-post-types-fields' )
		),
		'primary',
		'submit',
		false
	);

	?>

	<a href="<?php echo esc_url( $form_action ); ?>" class="button"><?php esc_html_e( 'Cancel', 'easy-post-types-fields' ); ?></a>
</form>

<?php