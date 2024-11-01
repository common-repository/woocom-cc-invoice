<?php # -*- coding: utf-8 -*-

namespace TheDramatist\WooComCCInvoice\Admin;

/**
 * Class Admin
 *
 * @package TheDramatist\WooComCCInvoice\Admin
 */
class Admin {
	
	public function __construct() {
	
	}
	
	public function init() {
		add_action(
			'woocom-cc-invoice_plugin_activate',
			[ $this, 'activation_hook' ]
		);
		add_action(
			'admin_menu',
			[ $this, 'admin_menu' ]
		);
		add_action(
			'admin_init',
			[ $this, 'admin_init' ]
		);
	}
	
	/**
	 * @return void
	 */
	public function activation_hook() {
		$opts = get_option( 'wci_opts' );
		if ( false !== $opts ) {
			return;
		}
		add_option( 'wci_opts', [
			'form_title'         => __(
				'Share Your Invoice',
				'woocom-cc-invoice'
			),
			'help_message'       => __(
				'Enter an email address to share your invoice.',
				'woocom-cc-invoice'
			),
			'button_text'        => __( 'Share Invoice', 'woocom-cc-invoice' ),
			'input_placeholder'  => __( 'Enter Email', 'woocom-cc-invoice' ),
			'success_message'    => __(
				'Success! Your invoice has been sent, send another?',
				'woocom-cc-invoice'
			),
			'email_message'      => __(
				'Invalid email address, please try again.',
				'woocom-cc-invoice'
			),
			'order_message'      => __(
				'Invalid order number, please refresh and try again.',
				'woocom-cc-invoice'
			),
			'account_message'    => __(
				'Invalid account, please make sure you are logged in and try again.',
				'woocom-cc-invoice'
			),
			'default_message'    => __(
				'Something went wrong, please refresh the page and try again.',
				'woocom-cc-invoice'
			),
			'order_received'     => 'on',
			'order_received_top' => 'on',
			'view_order'         => 'on',
		], '', false );
	}
	
	public function admin_menu() {
		add_submenu_page(
			'woocommerce',
			__( 'Invoice Sharing', 'woocom-cc-invoice' ),
			__( 'Invoice Sharing', 'woocom-cc-invoice' ),
			'edit_posts',
			'woocom-cc-invoice-settings',
			[ $this, 'admin_menu_render' ]
		);
	}
	
	/**
	 * Share Order Settings Page - Allows users to
	 * control various opts for the plugin.
	 *
	 * @return void Prints HTML form
	 */
	public function admin_menu_render() {
		?>
		<div class="wrap wci_wrap">
			<h2>
				<?php
				esc_html_e(
					'Invoice Sharing Settings',
					'woocom-cc-invoice'
				)
				?>
			</h2>
			<form action="opts.php" method="post">
				<?php settings_fields( 'wci_opts' ); ?>
				<?php do_settings_sections( 'wci_opts_page' ); ?>
				<input name="Submit" type="submit" class="button button-primary button-large" value="Save Changes" />
			</form>
		</div><!-- .wrap -->
		<?php
	}
	
	/**
	 * Register WooCom CC Invoice admin menu Settings
	 *
	 * @return void
	 */
	public function admin_init() {
		register_setting(
			'wci_opts',
			'wci_opts',
			[ $this, 'validate_opts' ]
		);
		add_settings_section(
			'wci_settings',
			'',
			[ $this, 'settings_help_text' ],
			'wci_opts_page'
		);
		add_settings_field(
			'wci_form_opts',
			'Update Form Values',
			[ $this, 'form_opts' ],
			'woocom-cc-invoice-opts-page',
			'wci_settings'
		);
		add_settings_field(
			'wci_messages_field',
			'Update Message Values',
			[ $this, 'message_input' ],
			'wci_opts_page',
			'wci_settings'
		);
		add_settings_field(
			'wci_output_select_field',
			'Upate Positioning',
			[ $this, 'page_input' ],
			'wci_opts_page',
			'wci_settings'
		);
	}
	
	/**
	 * Settings help text
	 *
	 * @return void
	 */
	public function settings_help_text() {
		// echo "Help Text";
	}
	
	/**
	 * Messages Input Section - Allow the user
	 * to customize the messages displayed
	 * throughout the plugin.
	 *
	 * @return void Prints HTML form
	 */
	public function form_opts() {
		$opts = get_option( 'wci_opts' );
		if ( false === $opts ) {
			$opts = [];
		}
		$defaults = [
			'form_title'        => __(
				'Share Your Invoice',
				''
			),
			'help_message'      => __(
				'Enter an email address to share your invoice.',
				''
			),
			'button_text'       => __(
				'Share Invoice',
				''
			),
			'input_placeholder' => __(
				'Enter Email',
				''
			),
		];
		$opts = wp_parse_args( $opts, $defaults );
		?>
		<div id="wci_form_opts">
			<label for="wci_opts[form_title]">
				<?php
				esc_html_e(
					'Form Title',
					'woocom-cc-invoice'
				)
				?>
			</label>
			<input
				type="text"
				name="wci_opts[form_title]"
				value="<?php echo esc_attr( $opts['form_title'] ); ?>"
			/>

			<label for="wci_opts[help_message]">
				<?php
				esc_html_e(
					'Help Message',
					'woocom-cc-invoice'
				)
				?>
			</label>
			<input
				type="text"
				name="wci_opts[help_message]"
				value="<?php echo esc_attr( $opts['help_message'] ); ?>"
			/>

			<label for="wci_opts[button_text]">
				<?php
				esc_html_e(
					'Button Text',
					'woocom-cc-invoice'
				)
				?>

			</label>
			<input
				type="text"
				name="wci_opts[button_text]"
				value="<?php echo esc_attr( $opts['button_text'] ); ?>"
			/>

			<label for="wci_opts[input_placeholder]">
				<?php
				esc_html_e(
					'Input Placeholder',
					'woocom-cc-invoice'
				)
				?>
			</label>
			<input
				type="text"
				name="wci_opts[input_placeholder]"
				value="<?php echo esc_attr( $opts['input_placeholder'] ); ?>"
			/>
		</div>
		<?php
	}
	
	/**
	 * Messages Input Section - Allow the user to
	 * customize the messages displayed
	 * throughout the plugin.
	 *
	 * @return void
	 */
	public function message_input() {
		$opts = get_option( 'wci_opts' );
		if ( false === $opts ) {
			$opts = [];
		}
		$defaults = [
			'success_message' => __(
				'Success! Your invoice has been sent, send another?',
				'woocom-cc-invoice'
			),
			'email_message'   => __(
				'Invalid email address, please try again.',
				'woocom-cc-invoice'
			),
			'order_message'   => __(
				'Invalid order number, please refresh and try again.',
				'woocom-cc-invoice'
			),
			'account_message' => __(
				'Invalid account, please make sure you are logged in and try again',
				'woocom-cc-invoice'
			),
			'default_message' => __(
				'Something went wrong, please refresh the page and try again.',
				'woocom-cc-invoice'
			),
		];
		$opts     = wp_parse_args( $opts, $defaults );
		?>
		<div id="wci_message_opts">

			<label for="wci_opts[success_message]">
				<?php
				esc_html_e(
					'Success Message',
					'woocom-cc-invoice'
				)
				?>
			</label>
			<input
				type="text"
				name="wci_opts[success_message]"
				value="<?php echo esc_attr( $opts['success_message'] ); ?>"
			/>

			<label for="wci_opts[email_message]">
				<?php
				esc_html_e(
					'Invalid Email Message',
					'woocom-cc-invoice'
				)
				?>
			</label>
			<input
				type="text"
				name="wci_opts[email_message]"
				value="<?php echo esc_attr( $opts['email_message'] ); ?>"
			/>

			<label for="wci_opts[order_message]">
				<?php
				esc_html_e(
					'Invalid Order Message',
					'woocom-cc-invoice'
				)
				?>
			</label>
			<input
				type="text"
				name="wci_opts[order_message]"
				value="<?php echo esc_attr( $opts['order_message'] ); ?>" />

			<label for="wci_opts[account_message]">
				<?php
				esc_html_e(
					'Invalid Account Message',
					'woocom-cc-invoice'
				)
				?>
			</label>
			<input
				type="text"
				name="wci_opts[account_message]"
				value="<?php echo esc_attr( $opts['account_message'] ); ?>"
			/>
			<label for="wci_opts[default_message]">
				<?php
				esc_html_e(
					'Default Error Message',
					'woocom-cc-invoice'
				)
				?>
			</label>
			<input
				type="text"
				name="wci_opts[default_message]"
				value="<?php echo esc_attr( $opts['default_message'] ); ?>" />
		</div>
		<?php
	}
	
	/*
	*	Page Options - Allow the user to decide which page to display
	*	the form on.
	*
	*	@return void Prints html form
	*/
	public function page_input() {
		$opts = get_option( 'wci_opts' );
		if ( false === $opts ) {
			$opts = [];
		}
		$defaults = [
			'order_received'     => 'on',
			'order_received_top' => 'on',
			'view_order'         => 'on',
		];
		$opts     = wp_parse_args( $opts, $defaults );
		?>

		<div id="wci_page_opts">

			<div>
				<input
					type="checkbox"
					name="wci_opts[order_received]"
					<?php
					echo 'on' === $opts['order_received'] ? 'checked' : ''
					?>
				/>
				<label for="wci_opts[order_received]">
					<?php
					esc_html_e(
						'Display on Order Received Page',
						'woocom-cc-invoice'
					);
					?>
				</label>
			</div>

			<div>
				<input
					type="checkbox"
					name="wci_opts[order_received_top]"
					<?php
					echo 'on' === $opts['order_received_top'] ? 'checked' : ''
					?>
				/>
				<label for="wci_opts[order_received_top]">
					<?php
					esc_html_e(
						'Top of Order Received Page? (Unchecked will display the form at the bottom)',
						'woocom-cc-invoice'
					);
					?>

				</label>
			</div>

			<div>
				<input
					type="checkbox"
					name="wci_opts[view_order]"
					<?php
					echo 'on' === $opts['view_order'] ? 'checked' : ''
					?>
				/>
				<label for="wci_opts[view_order]">
					<?php
					esc_html_e(
						'Display on View Order Page',
						'woocom-cc-invoice'
					);
					?>
				</label>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Validation Settings
	 *
	 * @param $input
	 *
	 * @return mixed
	 */
	public function validate_opts( $input ) {
		$input['form_title']        = strip_tags( $input['form_title'] );
		$input['help_message']      = strip_tags( $input['help_message'] );
		$input['button_text']       = strip_tags( $input['button_text'] );
		$input['input_placeholder'] = strip_tags( $input['input_placeholder'] );
		$input['success_message']   = strip_tags( $input['success_message'] );
		$input['email_message']     = strip_tags( $input['email_message'] );
		$input['order_message']     = strip_tags( $input['order_message'] );
		$input['account_message']   = strip_tags( $input['account_message'] );
		$input['default_message']   = strip_tags( $input['default_message'] );
		if ( ! isset( $input['order_received'] ) ) {
			$input['order_received'] = '';
		}
		if ( ! isset( $input['order_received_top'] ) ) {
			$input['order_received_top'] = '';
		}
		if ( ! isset( $input['view_order'] ) ) {
			$input['view_order'] = '';
		}
		return $input;
	}
}
