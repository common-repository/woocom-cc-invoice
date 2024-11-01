<?php # -*- coding: utf-8 -*-

namespace TheDramatist\WooComCCInvoice\Initialize;

/**
 * Class Initialize
 *
 * @package TheDramatist\WooComCCInvoice\Initialize
 */
class Initialize {
	
	public function __construct() {
	
	}
	
	public function init() {
		add_filter( 'woocommerce_thankyou', [ $this, 'thankyou_page' ] );
		add_filter(
			'woocommerce_order_details_after_order_table',
			[ $this, 'order_detail_page' ]
		);
		add_action( 'wp_ajax_nopriv_wci_invoice_cc', 'cc_invoive_nopriv' );
		add_action( 'wp_ajax_wci_invoice_cc', 'cc_invoive' );
	}
	
	/**
	 * Thank You Page Filter - add form to order received page
	 * if user checks it on the settings page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function thankyou_page() {
		$opts = get_option( 'wci_opts' );
		if (
			'on' === $opts['order_received'] and
			'on' === $opts['order_received_top']
		) {
			$this->input_form();
		}
	}
	
	/**
	 * Print WooCom CC Invoice Input Box
	 *
	 * @return void Prints WooCom CC Invoice form
	 */
	public function input_form() {
		$opts = get_option( 'wci_opts' );
		?>
		<div class="share_order_container">
			<h2>
				<?php echo esc_html( $opts['form_title'] ); ?>
			</h2>
			<label>
				<?php echo esc_html( $opts['help_message'] ); ?>
			</label>
			<input
				type="email"
				name="order_share_email"
				id="order_share_email"
				placeholder="
				<?php
				echo esc_attr( $opts['input_placeholder'] )
				?>
				"
			/>
			<input
				type="submit"
				value="<?php echo esc_attr( $opts['button_text'] ); ?>"
				id="submit_order_share"
				class="button button-primary button-large"
			/>
		</div>
		<?php
	}
	
	/**
	 * Display Share Form Filter - add form after the details
	 * page on as configured on the settings page.
	 *
	 * @return void
	 */
	public function order_detail_page() {
		$opts = get_option( 'wci_opts' );
		// Display on View Order Page
		if (
			is_page( 'my-account' ) and
			'on' === $opts['view_order']
		) {
			$this->input_form();
			// Display on bottom of Order Received Page
		} elseif (
			is_page( 'checkout' ) &&
			'on' === $opts['order_received'] &&
			'on' !== $opts['order_received_top']
		) {
			$this->input_form();
		}
	}
	
	/**
	 * WooCom CC Invoice Ajax Handler
	 *
	 * @return array results
	 *
	 */
	public function cc_invoive() {
		global $woocommerce;
		// Nonce needed.
		$email = stripslashes( $_REQUEST['email'] );
		$order = intval( stripslashes( $_REQUEST['order'] ) );
		$user  = wp_get_current_user();
		// Get order to validate against
		if ( is_int( $order ) ) {
			$order_result = new \WC_Order( $order );
		}
		// Validate the Email
		if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			$result['type'] = 'invalid_email';
			// Validate Order Exists
		} elseif ( $order_result->ID !== $order ) {
			$result['type'] = 'invalid_order';
			// Validate This is the Current User's Account
		} elseif (
			$user->data->user_email !== $order_result->billing_email
		) {
			$result['type'] = 'invalid_account';
			// If everything checks out, send the email
		} else {
			$mailer  = new \WC_Emails();
			$invoice = $mailer->emails['WooCom_CC_Invoice'];
			$invoice->trigger( $order, $email );
			$result['type'] = 'success';
		}
		// Send results back
		$result_json = wp_json_encode( $result );
		echo esc_html( $result_json );
		// reset jQuery
		wp_reset_query();
		die();
	}
	
	/**
	 * WooCom CC Invoice Ajax Handler - NoPriv
	 *
	 * @return array
	 *
	 */
	public function cc_invoive_nopriv() {
		$result['type'] = 'invalid_account';
		// Send results back
		$result_json = wp_json_encode( $result );
		echo esc_html( $result_json );
		// reset jQuery
		wp_reset_query();
		die();
	}
}