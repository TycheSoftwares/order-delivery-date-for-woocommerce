/**
 * This function allows to dismiss the notices which are shown from the plugin.
 *
 * @namespace orddd_lite
 * @since 2.6
 */
// Make notices dismissible
jQuery( document ).ready(
	function() {
			jQuery( '.notice.is-dismissible' ).each(
				function() {
					var $this = jQuery( this ),
					$button   = jQuery( '<button type="button" class="notice-dismiss"><span class="screen-reader-text"></span></button>' ),
					btnText   = wp.i18n.dismiss || '';

					// Ensure plain text
					$button.find( '.screen-reader-text' ).text( btnText );

					$this.append( $button );
					/**
					 * Event when close icon is clicked.
					 *
					 * @fires event:notice-dismiss
					 * @since 2.6
					*/
					$button.on(
						'click.notice-dismiss',
						function( event ) {
							event.preventDefault();
							$this.fadeTo(
								100 ,
								0,
								function() {
									// alert();
									jQuery( this ).slideUp(
										100,
										function() {
											jQuery( this ).remove();
											var data      = {
												action: "admin_notices"
											};
											var admin_url = jQuery( "#admin_url" ).val();
											jQuery.post(
												admin_url + "/admin-ajax.php",
												data,
												function( response ) {
												}
											);
										}
									);
								}
							);
						}
					);
				}
			);
	}
);
