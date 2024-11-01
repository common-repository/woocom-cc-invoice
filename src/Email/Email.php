<?php # -*- coding: utf-8 -*-

namespace TheDramatist\WooComCCInvoice\Email;

/**
 * Class Email
 *
 * @package TheDramatist\WooComCCInvoice\Email
 */
class Email extends WC_Email {
	
	public $find;
	public $replace;
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id          = 'customer_invoice';
		$this->title       = __( 'Customer invoice', 'woocom-cc-invoice' );
		$this->description = __(
			'Customer invoice emails can be sent
			to the user containing order info and payment links.',
			'woocom-cc-invoice'
		);
		$this->template_html  = 'emails/customer-invoice.php';
		$this->template_plain = 'emails/plain/customer-invoice.php';
		$this->subject = __(
			'Invoice for order {order_number} from {order_date}',
			'woocom-cc-invoice'
		);
		$this->heading = __(
			'Invoice for order {order_number}',
			'woocom-cc-invoice'
		);
		$this->subject_paid = __(
			'Your {site_title} order from {order_date}',
			'woocom-cc-invoice'
		);
		$this->heading_paid = __(
			'Order {order_number} details',
			'woocom-cc-invoice'
		);
		// Call parent constructor
		parent::__construct();
		$this->heading_paid = $this->get_option(
			'heading_paid',
			$this->heading_paid
		);
		$this->subject_paid = $this->get_option(
			'subject_paid',
			$this->subject_paid
		);
	}
	
	/**
	 * trigger function.
	 *
	 * @access public
	 *
	 * @param $order
	 * @param $email
	 *
	 * @return void
	 */
	public function trigger( $order, $email ) {
		if ( ! is_object( $order ) ) {
			$order = new WC_Order( absint( $order ) );
		}
		if ( $order ) {
			$this->object    = $order;
			$this->recipient = $this->object->billing_email;
				$this->find[]    = '{order_date}';
			$this->replace[] = date_i18n(
				wc_date_format(),
				strtotime( $this->object->order_date )
			);
				$this->find[]    = '{order_number}';
			$this->replace[] = $this->object->get_order_number();
		}
		if ( ! $this->get_recipient() ) {
			return;
		}
		$this->send(
			$email,
			$this->get_subject(),
			$this->get_content(),
			$this->get_headers(),
			$this->get_attachments()
		);
	}
	
	/**
	 * get_subject function.
	 *
	 * @access public
	 * @return string
	 */
	public function get_subject() {
		if (
			'processing' === $this->object->status
			|| 'completed' === $this->object->status
		) {
			return apply_filters(
				'woocommerce_email_subject_customer_invoice_paid',
				$this->format_string( $this->subject_paid ), $this->object
			);
		} else {
			return apply_filters(
				'woocommerce_email_subject_customer_invoice',
				$this->format_string( $this->subject ), $this->object
			);
		}
	}
	
	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_html() {
		ob_start();
		wc_get_template( $this->template_html, [
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => false,
		] );
		return ob_get_clean();
	}
	
	/**
	 * get_heading function.
	 *
	 * @access public
	 * @return string
	 */
	public function get_heading() {
		if (
			'processing' === $this->object->status
			|| 'completed' == $this->object->status
		) {
			return apply_filters(
				'woocommerce_email_heading_customer_invoice_paid',
				$this->format_string( $this->heading_paid ),
				$this->object
			);
		} else {
			return apply_filters(
				'woocommerce_email_heading_customer_invoice',
				$this->format_string( $this->heading ),
				$this->object
			);
		}
	}
	
	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();
		wc_get_template( $this->template_plain, [
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => true,
		] );
		return ob_get_clean();
	}
	
	/**
	 * Initialise Settings Form Fields
	 *
	 * @access public
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = [
			'subject'      => [
				'title'       => __( 'Email subject', 'woocom-cc-invoice' ),
				'type'        => 'text',
				'description' => sprintf(
					__( 'Defaults to <code>%s</code>', 'woocom-cc-invoice' ),
					$this->subject
				),
				'placeholder' => '',
				'default'     => '',
			],
			'heading'      => [
				'title'       => __( 'Email heading', 'woocom-cc-invoice' ),
				'type'        => 'text',
				'description' => sprintf(
					__( 'Defaults to <code>%s</code>', 'woocom-cc-invoice' ),
					$this->heading
				),
				'placeholder' => '',
				'default'     => '',
			],
			'subject_paid' => [
				'title'       => __(
					'Email subject (paid)',
					'woocom-cc-invoice'
				),
				'type'        => 'text',
				'description' => sprintf(
					__( 'Defaults to <code>%s</code>', 'woocom-cc-invoice' ),
					$this->subject_paid
				),
				'placeholder' => '',
				'default'     => '',
			],
			'heading_paid' => [
				'title'       => __(
					'Email heading (paid)',
					'woocom-cc-invoice'
				),
				'type'        => 'text',
				'description' => sprintf(
					__( 'Defaults to <code>%s</code>', 'woocom-cc-invoice' ),
					$this->heading_paid
				),
				'placeholder' => '',
				'default'     => '',
			],
			'email_type'   => [
				'title'       => __( 'Email type', 'woocom-cc-invoice' ),
				'type'        => 'select',
				'description' => __(
					'Choose which format of email to send.',
					'woocom-cc-invoice'
				),
				'default'     => 'html',
				'class'       => 'email_type',
				'opts'        => [
					'plain'     => __( 'Plain text', 'woocom-cc-invoice' ),
					'html'      => __( 'HTML', 'woocom-cc-invoice' ),
					'multipart' => __( 'Multipart', 'woocom-cc-invoice' ),
				],
			],
		];
	}
}