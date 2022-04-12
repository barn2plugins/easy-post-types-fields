(( $, wp, params, undefined ) => {
	$(() => {
		const __ = wp.i18n.__;

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
					  $dest = $( `input[name="${$this.attr('class')}"]`, $destination );

				if ( 'checkbox' === $dest.attr('type') ) {
					$dest.prop( 'checked', 'true' === $this.text() );
				} else {
					$dest.val( $this.text() );
				}
			})

			$( `input[name="previous_slug"]`, $destination ).val( $( '.hidden>div.slug', $source ).text() );
		}

		const updateRow = ( $destination, $source ) => {
			$( 'input', $source ).each( (index, item) => {
				const $this = $(item),
					  $dest = $(`.hidden>div.${$this.attr('name')}`, $destination);

				let value = $this.val();

				if ( 'checkbox' === $this.attr('type') ) {
					$dest.text( $this.prop( 'checked' ) ? 'true' : 'false' );
					value = $this.prop( 'checked' ) ? __( 'Yes', 'easy-post-types-fields' ) : __( 'No', 'easy-post-types-fields' );
				} else {
					$dest.text( $this.val() );
				}

				let cellSelector = `td.column-${$this.attr('name')}`;

				if ( 'singular_name' === $this.attr('name') ) {
					cellSelector += ' a.row-title';
				}

				$( cellSelector, $destination ).text( value )
			});
		}

		const updateData = ( $destination, $source ) => {
			return new Promise( (resolve, reject) => {
				const postData = {
					action: 'ept_inline_edit',
				};
	
				$( 'input', $source ).each( (index, item ) => {
					const $this = $(item);
					let value = $this.val();

					if ( 'checkbox' === $this.attr('type') ) {
						value = $this.prop( 'checked' );
					}

					postData[$(item).attr('name')] = value;
				});
	
				$( '.spinner', $source ).addClass( 'is-active' );
	
				$.post(
					ajaxurl,
					$.param(postData),
					(response) => {
						$( '.spinner', $source ).removeClass( 'is-active' );
	
						if (response.success) {
							resolve(!postData.previous_slug)
						} else {
							reject( response.data.error_message )
						}
					}
				);
			})
		}

		$(document).on('click', '#the-list tr .delete a', (event) => {
			event.preventDefault();

			const $table = $(event.target).closest('table'),
				  $row   = $(event.target).closest('tr'),
				  name   = $( 'a.row-title', $row ).text(),
				  slug   = $( 'td.column-slug', $row ).text(),
				  type   = 'taxonomy';

			if ( ! confirm( wp.i18n.sprintf( wp.i18n.__( 'Are you sure you want to delete the %1$s %2$s?', 'easy-post-types-fields' ), name, wp.i18n.__( 'taxonomy', 'easy-post-types-fields' ) ) ) ) {
				return false;
			}

			const postData = {
				action: 'ept_inline_delete',
				_inline_delete: $( '#_inline_delete', $table ).val(),
				slug,
				type,
				post_type: (new URLSearchParams(location.search) ).get('post_type')
			};

			$.post(
				ajaxurl,
				$.param(postData),
				(response) => {
					if (response.success) {
						$row.remove();
						location.reload();
					}
				}
			);
		})

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

		$(document).on('click', '#the-list .inline-edit-save button.save', (event) => {
			const $table      = $(event.target).closest('table'),
				  $noItems    = $('#the-list tr.no-items', $table),
				  $targetRow  = $('#the-list tr.editing', $table),
				  $inlineEdit = $('#the-list tr#inline-edit');

			updateData( $targetRow, $inlineEdit )
			.then( (isNew) => {
				if ( isNew ) {
					location.reload();
					return;
				}

				updateRow( $targetRow, $inlineEdit )
				$inlineEdit.prev('.hidden').remove().end().remove();
				$('tfoot', $table).show();
				$targetRow.show().removeClass('editing');
	
				if ( 1 === $( 'tbody tr', $table ).length ) {
					$noItems.show();
				}
			}, (error) => {
				const $errorNotice = $( '#the-list .inline-edit-save .notice-error' ),
					  $error       = $( '.error', $errorNotice );

				$errorNotice.removeClass( 'hidden' );
				$error.text( error );
				wp.a11y.speak( error );
			});
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
})( jQuery, wp, ept_params )
