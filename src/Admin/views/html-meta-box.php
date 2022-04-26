<?php
/**
 * The HTML markup of the EPT Custom Fields meta box
 *
 * @param Post_Type_List_Table $post_type_list_table The list table instance (a subclass of WP_List_Table)
 * @param Barn2\EPT_Lib\Plugin\Plugin $plugin The main instance of the plugin
 * @param string $new_link The link to add a new post type
 */
namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

defined( 'ABSPATH' ) || exit;

?>
<table class="ept-fields ept-fields-<?php echo esc_attr( $post_type ); ?>">
	<tbody>
		<?php

		foreach ( $fields as $field ) {
			$meta_key   = "{$post_type}_{$field['slug']}";
			$meta_value = get_post_meta( $post->ID, $meta_key, true );

			?>
			<tr>
				<th scope="row"><?php echo esc_html( $field['name'] ); ?></th>
				<td>
					<?php
					switch ( $field['type'] ) {
						case 'image':
						case 'text':
							$attributes = apply_filters( "ept_text_input_attributes_{$post_type}", [], $field, $post_type );
							array_walk(
								$attributes,
								function( &$v, $k ) {
									$v = sprintf( '%s="%s"', $k, esc_attr( $v ) );
								}
							);
							$attributes = implode( ' ', $attributes );

							?>

							<input type="text" name="<?php echo esc_attr( $meta_key ); ?>" <?php echo $attributes; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> value="<?php echo esc_attr( $meta_value ); ?>"/>

							<?php
							break;

						case 'editor':
							wp_editor( htmlspecialchars_decode( $meta_value ), $meta_key, apply_filters( "ept_editor_{$post_type}", [ 'textarea_rows' => 5 ], $field, $post_type ) );
							break;

					}
					?>
				</td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>
