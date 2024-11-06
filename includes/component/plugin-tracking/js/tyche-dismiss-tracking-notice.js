jQuery(document).ready( function() {
	jQuery( '.notice.is-dismissible' ).each( function() {
		var $this = jQuery( this ),
			$button = jQuery( '<button type="button" class="notice-dismiss"><span class="screen-reader-text"></span></button>' ),
			btnText = wp.i18n.dismiss || '';
		
		// Ensure plain text
		$button.find( '.screen-reader-text' ).text( btnText );

		$this.append( $button );

		/**
		 * Event when close icon is clicked.
		 * @fires event:notice-dismiss
		 * @since 6.8
		*/
		$button.on( 'click.notice-dismiss', function( event ) {
			event.preventDefault();
			$this.fadeTo( 100 , 0, function() {
				//alert();
				let data = {};
				if ( $this.hasClass( 'orddd-upgrade-to-pro-notice' ) ) {
					data = {
						action: 'ordd_lite_dismiss_upgrade_to_pro',
						upgrade_to_pro_type: 'purchase',
						security: orddd_ts_dismiss_notice.tracking_notice
					};
				} else if ( $this.hasClass( 'orddd-pro-expired-notice' ) ) {
					data = {
						action: 'ordd_lite_dismiss_upgrade_to_pro',
						upgrade_to_pro_type: 'expired',
						security: orddd_ts_dismiss_notice.tracking_notice
					};
				} else if ( $this.hasClass( 'ordd_lite-tracker' ) ) {
					data = {
						action: orddd_ts_dismiss_notice.ts_prefix_of_plugin + "_tracker_dismiss_notice",
						tracking_notice : orddd_ts_dismiss_notice.tracking_notice,
					};;
				}

				if ( Object.keys(data).length !== 0 ) {
					jQuery(this).slideUp( 100, function() {
						jQuery(this).remove();				
						jQuery.post(
							orddd_ts_dismiss_notice.ts_admin_url,
							data,
							function( response ) {}
						);
					});
				}
			});
		});
	});
});