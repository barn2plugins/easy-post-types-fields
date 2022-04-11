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

		const populateData = ( $destination, $source ) => {
			$( '.hidden>div', $source ).each( (index, item ) => {
				const $this = $(item),
					  $dest = $( `[name="${$this.attr('class')}"]`, $destination );

				if ( 'checkbox' === $this.attr('type') ) {
					$dest.prop( 'checked', 'true' === $this.text() );
				} else {
					$dest.val( $this.text() );
				}
			})
		}

		$(document).on('click', '#the-list tr a.editinline, tfoot tr button.editinline', (event) => {
			event.preventDefault();

			const $table       = $(event.target).closest('table'),
				  $noItems     = $('#the-list tr.no-items', $table),
				  $inlineEdit  = $('#inlineedit tr#inline-edit'),
				  $editingForm = $inlineEdit.clone();

			let $targetRow = $noItems,
				hideRow    = true;

			if ( 0 === $noItems.length ) {
				$targetRow = $('#the-list tr').last();
				hideRow = false;
			}
	
			if ( 'A' === event.target.tagName ) {
				$targetRow = $(event.target).closest('tr');
				hideRow = true;
				populateData( $editingForm, $targetRow );
			}
			
			$targetRow.toggle(!hideRow).after(
				hideRow ? $('<tr>', {class:'hidden'}) : null,
				$editingForm.show()
			).addClass('editing');
			$('tfoot', $table).hide();
		})

		$(document).on('click', '#the-list .inline-edit-save button.cancel', (event) => {
			const $table      = $(event.target).closest('table'),
				  $noItems    = $('#the-list tr.no-items', $table),
				  $targetRow  = $('#the-list tr.editing', $table),
				  $inlineEdit = $('#the-list tr#inline-edit');

			$inlineEdit.prev('.hidden').remove().end().remove();
			$('tfoot', $table).show();
			$targetRow.show().removeClass('editing');

			if ( 1 === $( 'tbody tr', $table ).length ) {
				$noItems.show();
			}
		})

		if ( $.fn.tipTip ) {
			$( '.barn2-help-tip' ).tipTip({
				'attribute': 'data-tip',
				'fadeIn': 50,
				'fadeOut': 50,
				'delay': 200,
				'keepAlive': true
			});		
		}
	})
})( jQuery, ept_params )
