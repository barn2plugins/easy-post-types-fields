<?php
/**
 * The HTML markup of the meta box presenting
 * the custom fields registered by EPT
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
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
							/**
							 * Filter the attributes added to the HTML element
							 *
							 * The attributes are defined as an associative array where the keys are the name of the attributes
							 * and the values are the values of the attributes.
							 *
							 * @param array $args The array of attributes
							 * @param string $field The slug of the current field
							 * @param string $post_type The slug of the current post type
							 */

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
							/**
							 * Filter the arguments passed to the `wp_editor` function
							 * that output the WYSIWYG editor in the custom field meta box.
							 *
							 * @param array $args An associative array of arguments passed to wp_editor
							 * @param string $field The slug of the current field
							 * @param string $post_type The slug of the current post type
							 */
							$editor_args = apply_filters( "ept_editor_{$post_type}", [ 'textarea_rows' => 5 ], $field, $post_type );
							wp_editor( htmlspecialchars_decode( $meta_value ), $meta_key, $editor_args );
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
