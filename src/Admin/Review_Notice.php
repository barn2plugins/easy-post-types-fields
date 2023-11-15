<?php

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Registerable,
	Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Service;

defined( 'ABSPATH' ) || exit;

/**
 * Add a notice to request a review.
 *
 * @package   Barn2/easy-post-types-fields
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Review_Notice implements Registerable, Service {

	private $plugin;

	/**
	 * Constructor.
	 *
	 * @param Plugin $plugin
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		if ( defined( 'DISABLE_NAG_NOTICES' ) ) {
			return;
		}

		add_action( 'transition_post_status', [ $this, 'check_ept_post_type_count' ], 10, 3 );
		add_action( 'admin_notices', [ $this, 'maybe_add_notice' ] );
		add_action( 'wp_ajax_ept_dismiss_notice', [ $this, 'maybe_dismiss_notice' ] );
	}

	/**
	 * Count the number of published documents.
	 *
	 * @param string $new
	 * @param string $old
	 * @param WP_Post $post
	 */
	public function check_ept_post_type_count( $new, $old, $post ) {
		if ( get_option( 'ept_review_notice_triggered' ) ) {
			return;
		}

		if ( 'ept_post_type' !== $post->post_type ) {
			return;
		}

		$post_type_count = (array) wp_count_posts( 'ept_post_type' );
		$count           = $post_type_count['publish'] ?? 0;

		if ( (int) $count > 0 ) {
			update_option( 'ept_review_notice_triggered', true, false );
		}
	}

	/**
	 * Handle the dismiss AJAX action.
	 */
	public function maybe_dismiss_notice() {
		$action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( ! $action || 'ept_dismiss_notice' !== $action ) {
			return;
		}

		check_ajax_referer( 'ept_dismiss_review_notice', 'nonce', true );

		update_option( 'ept_review_notice_dismissed', true, false );
	}

	/**
	 * Maybe add the notice.
	 */
	public function maybe_add_notice() {
		global $pagenow, $current_screen;

		if ( ! in_array( $pagenow, [ 'index.php', 'edit.php', 'edit-tags.php', 'plugins.php', 'admin.php' ], true ) ) {
			return;
		}

		if ( $pagenow === 'index.php' && $current_screen->id !== 'dashboard' ) {
			return;
		}

		if ( $pagenow === 'admin.php' && $current_screen->parent_base !== 'ept_post_types' ) {
			return;
		}

		if ( in_array( $pagenow, [ 'edit.php', 'edit-tags.php' ], true ) && 0 !== strpos( $current_screen->post_type, 'ept_' ) ) {
			return;
		}

		if ( get_option( 'ept_review_notice_triggered' ) || get_option( 'ept_review_notice_dismissed' ) ) {
			return;
		}

		$this->print_script();
		$this->print_style();

		?>
		<div id="ept-review-notice" class="notice">
			<div class="ept-review-notice-left">
				<h1><?php esc_html_e( 'Are you enjoying Easy Post Types and Fields?', 'easy-post-types-fields' ); ?></h1>
				<p><?php esc_html_e( 'Congratulations, you\'ve just published your first custom post type! If you have time, it would be great if you could leave us a review and let us know what you think of the plugin.', 'easy-post-types-fields' ); ?></p>

				<div class="ept-review-notice-actions">
					<a href="https://wordpress.org/support/plugin/easy-post-types-fields/reviews/#new-post" target="_blank" class="ept-add-review button button-primary"><?php esc_html_e( 'Add Review', 'easy-post-types-fields' ); ?></a>
					<a class="ept-dismiss-notice"><?php esc_html_e( 'Dismiss', 'easy-post-types-fields' ); ?></a>
				</div>

				<span class="ept-review-notice-meta"><?php esc_html_e( 'Barn2', 'easy-post-types-fields' ); ?></span>
			</div>

			<div class="ept-review-notice-right">
				<?php printf( '<img src=%s" />', esc_url( $this->plugin->get_dir_url() . '/assets/images/review.png' ) ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Print the script for dismissing the notice.
	 */
	private function print_script() {

		// Create a nonce.
		$nonce = wp_create_nonce( 'ept_dismiss_review_notice' );
		?>
		<script>
		window.addEventListener( 'load', function() {
			var dismissBtn = document.querySelector( '.ept-dismiss-notice' );

			// Add an event listener to the dismiss button.
			dismissBtn.addEventListener( 'click', function( event ) {
				var httpRequest = new XMLHttpRequest(),
					postData    = '';

				// Build the data to send in our request.
				// Data has to be formatted as a string here.
				postData += 'id=ept-review-notice';
				postData += '&action=ept_dismiss_notice';
				postData += '&nonce=<?php echo esc_html( $nonce ); ?>';

				httpRequest.open( 'POST', '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>' );
				httpRequest.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' )
				httpRequest.send( postData );

				// handle the notice fadeout
				var reviewNotice = document.getElementById( 'ept-review-notice' );
				var fadeEffect = setInterval(function () {
					if ( ! reviewNotice.style.opacity ) {
						reviewNotice.style.opacity = 1;
					}

					if ( reviewNotice.style.opacity > 0 ) {
						reviewNotice.style.opacity -= 0.1;
					} else {
						clearInterval( fadeEffect );
						reviewNotice.style.display = 'none';
					}
				}, 20 );
			});
		});
		</script>
		<?php
	}

	/**
	 * Print the styles.
	 */
	private function print_style() {

		?>
		<style>
			#ept-review-notice {
				display: flex;
				justify-content: space-between;
				max-width: 1200px;
				margin: 10px 0 15px;
				padding: 5px 30px 10px;
				background: #ffffff;
				border: 1px solid #c3c4c7;
				border-left-width: 4px;
				border-left-color: #000;
				box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
			}
			.ept-review-notice-left {
				position: relative;
			}
			.ept-review-notice-right {
				display: flex;
				justify-content: center;
				align-items: center;
			}
			.ept-review-notice-right img {
				width: 154px;
				padding-left: 110px;
			}
			@media screen and (max-width: 992px) {
				.ept-review-notice-right img {
					padding-left: 60px;
				}
			}
			#ept-review-notice h1 {
				margin-bottom: 0;
				font-size: 21px;
				font-weight: 400;
				line-height: 1.2;
			}
			.ept-review-notice-actions {
				display: flex;
				align-items: center;
			}
			.ept-add-review {
				padding: 0 20px !important;
			}
			.ept-dismiss-notice {
				display: flex;
				margin-left: 11px;
				font-size: 14px;
				line-height: 20px;
				cursor: pointer;
			}
			.ept-dismiss-notice::before {
				content: "\f335";
				font: normal 20px/20px dashicons;
			}
			.ept-review-notice-meta {
				display: block;
				padding-top: 10px;
				color: #83868b;
			}
		</style>
		<?php
	}
}
