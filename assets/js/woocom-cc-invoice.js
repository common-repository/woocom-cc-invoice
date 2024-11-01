(
	function( $ ) {
		// Strict variable declaration in JS
		'use strict';
		// After the document is ready
		$( function() {
			$( '#submit_order_share' ).on( 'click', function() {
				var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				// Get Submitted Email
				var email = $( '#order_share_email' ).val();
				// Get order number
				var order = $( '.order_details .order strong' )
					.text()
					.replace( '#', '' );

				if ( order === '' ) {
					order = $( '.order-number' )
						.text()
						.replace( '#', '' );
				}

				// If the email is invalid, show an error
				if ( !regex.test( email ) ) {
					$( ".order_message" )
						.remove();

					$( ".share_order_container" )
						.append( "<div class='order_message'>"
							+ WPAjaxObj.email_message
							+ "</div>"
						);

					// Otherwise process the request
				} else {
					$( ".order_message" ).remove();

					var data = {
						action  : 'wci_invoice_cc',
						dataType: 'json',
						email   : email,
						order   : order
					};

					// Process Ajax Request
					$.get( WPAjaxObj.ajax_url, data, function( data ) {
						var results = JSON.parse( data );
						// console.log( results );
						// Clear the order message
						$( ".order_message" ).remove();
						// Success
						if ( results.type === 'success' ) {
							// Clear the input box and show a success message
							$( '#order_share_email' ).val( '' );
							$( ".share_order_container" )
								.append( "<div class='order_message success'>"
									+ WPAjaxObj.success_message
									+ "</div>"
								);

							// Email Error
						} else if ( results.type === 'invalid_email' ) {
							$( ".share_order_container" )
								.append( "<div class='order_message'>"
									+ WPAjaxObj.email_message
									+ "</div>"
								);
							// Order Number Error
						} else if ( results.type === 'invalid_order' ) {
							$( ".share_order_container" )
								.append( "<div class='order_message'>"
									+ WPAjaxObj.order_message
									+ "</div>"
								);
							// Account Error
						} else if ( results.type === 'invalid_account' ) {
							$( ".share_order_container" )
								.append( "<div class='order_message'>"
									+ WPAjaxObj.account_message
									+ "</div>"
								);
							// Misc Error
						} else {
							$( ".share_order_container" )
								.append( "<div class='order_message'>"
									+ WPAjaxObj.default_message
									+ "</div>"
								);
						}
					} );
				}
			} );
		} );
	}
)( jQuery );