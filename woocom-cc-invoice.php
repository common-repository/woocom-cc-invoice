<?php # -*- coding: utf-8 -*-

/**
 * Plugin Name: WooCom CC Invoice
 * Description: Helps user to send CC of the invoice to other third party email
 * 				addresses.
 * Plugin URI:  https://github.com/rnaby
 * Author:      TheDramatist
 * Author URI:  http://rnaby.github.com/
 * Version:     1.0.0
 * License:		GPL-2.0
 * Text Domain: woocom-cc-invoice
 */

namespace TheDramatist\WooComCCInvoice;

/**
 * Initialize a hook on plugin activation.
 *
 * @return void
 */
function activate() {

	do_action( 'woocom-cc-invoice_plugin_activate' );
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\\activate' );

/**
 * Initialize a hook on plugin deactivation.
 *
 * @return void
 */
function deactivate() {

	do_action( 'woocom-cc-invoice_plugin_deactivate' );
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\\deactivate' );

/**
 * Initialize all the plugin things.
 *
 * @return mixed
 * @throws \Throwable
 */
function initialize() {

	try {
		/**
		 * Checking if vendor/autoload.php exists or not.
		 */
		if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
			/** @noinspection PhpIncludeInspection */
			require_once __DIR__ . '/vendor/autoload.php';
		}

		/**
		 * Loading translations.
		 */
		load_plugin_textdomain(
			'woocom-cc-invoice',
			true,
			basename( dirname( __FILE__ ) ) . '/languages'
		);

		/**
		 * Calling modules.
		 */
		return apply_filters(
			'woocom-cc-invoice_core_modules',
			[
				'AssetsEnqueue' => ( new Assets\AssetsEnqueue() )->init(),
				'EmailInit'     => ( new Email\InitEmail() )->init(),
				'Admin'         => ( new Admin\Admin() )->init(),
				'Initialize'    => ( new Initialize\Initialize() )->init(),
			]
		);

	} catch ( \Throwable $throwable ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			throw $throwable;
		}
		do_action( 'woocom-cc-invoice_error', $throwable );
	}
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\initialize' );
