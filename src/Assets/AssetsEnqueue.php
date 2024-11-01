<?php # -*- coding: utf-8 -*-

namespace TheDramatist\WooComCCInvoice\Assets;

/**
 * Class AssetsEnqueue
 *
 * @package TheDramatist\WooComCCInvoice\Assets
 */
class AssetsEnqueue {
	
	public $plugin_dir = '';
	
	/**
	 * AssetsEnqueue constructor.
	 */
	public function __construct() {
		$this->plugin_dir = plugin_dir_url( __FILE__ );
	}
	
	/**
	 * Enqueueing scripts and styles.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', [ $this, 'styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ] );
	}
	
	/**
	 * Enqueueing styles.
	 *
	 * @return void
	 */
	public function styles() {
		wp_enqueue_style(
			'woocom-cc-invoice-css',
			$this->plugin_dir . '../../assets/css/woocom-cc-invoice.css',
			null,
			'1.0.0',
			'all'
		);
	}
	
	/**
	 * Enqueueing scripts.
	 *
	 * @return void
	 */
	public function scripts() {
		// Registering the script.
		wp_register_script(
			'woocom-cc-invoice-js',
			$this->plugin_dir . '../../assets/js/woocom-cc-invoice.js',
			[ 'jquery' ],
			'1.0.0',
			true
		);
		// Local JS data
		$opts          = get_option( 'wci_opts' );
		$local_js_data = [
			'ajax_url'        => admin_url( 'admin-ajax.php' ),
			'success_message' => $opts['success_message'],
			'email_message'   => $opts['email_message'],
			'order_message'   => $opts['order_message'],
			'account_message' => $opts['account_message'],
			'default_message' => $opts['default_message'],
		];
		// Pass data to myscript.js on page load
		wp_localize_script(
			'woocom-cc-invoice-js',
			'WPAjaxObj',
			$local_js_data
		);
		// Enqueueing JS file.
		wp_enqueue_script( 'woocom-cc-invoice-js' );
	}
}