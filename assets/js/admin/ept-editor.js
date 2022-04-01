(( $, params, undefined ) => {
	$(() => {
		$( '#ept_plural_name_wrap label' ).removeClass( 'screen-reader-text' );

		$( '#ept_plural_name_wrap input' ).on( 'input', ( event ) => {
			if ( '' === event.target.value ) {
				$( '#ept_plural_name_wrap label' ).removeClass( 'screen-reader-text' );
				return;
			}
		
			$( '#ept_plural_name_wrap label' ).addClass( 'screen-reader-text' );
		} ).trigger('input');

		$( '.post_type-name .row-actions .delete a' ).on( 'click', ( event ) => {
			if ( ! confirm( params.i18n.confirm_delete ) ) {
				event.preventDefault();
				return false;
			}

			if ( Number( event.target.dataset.post_count ) > 0 && ! confirm( params.i18n.last_confirm_delete ) ) {
				event.preventDefault();
			}
		});	
	})
})( jQuery, ept_params )
