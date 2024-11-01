<?php # -*- coding: utf-8 -*-

namespace TheDramatist\WooComCCInvoice\Email;

/**
 * Class InitEmail
 *
 * @package TheDramatist\WooComCCInvoice\Email
 */
class InitEmail {
	
	public function __construct() {
	
	}
	
	public function init() {
		add_filter(
			'woocommerce_email_classes',
			[ $this, 'instantiate_email' ]
		);
	}
	
	/**
	 *  Add a custom email to the list of emails WooCommerce should load
	 *
	 * @since 0.1
	 *
	 * @param $email_classes
	 *
	 * @return mixed
	 */
	public function instantiate_email( $email_classes ) {
		// add the email class to the list of email
		// classes that WooCommerce loads
		$email_classes['WooCom_CC_Invoice'] = new Email();
		return $email_classes;
	}
}