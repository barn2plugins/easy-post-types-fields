(( $, wp, undefined ) => {
	$(() => {
		const { __, sprintf } = wp.i18n;

		$( '#ept_plural_name_wrap label' ).removeClass( 'screen-reader-text' );

		$( '#ept_plural_name_wrap input' ).on( 'input', ( event ) => {
			if ( '' === event.target.value ) {
				$( '#ept_plural_name_wrap label' ).removeClass( 'screen-reader-text' );
				return;
			}
		
			$( '#ept_plural_name_wrap label' ).addClass( 'screen-reader-text' );
		} ).trigger('input');

		$( '.post_type-name .row-actions a.post-type-delete' ).on( 'click', ( event ) => {
			const $cell    = $(event.target).closest('td'),
				  postType = $('a.row-title', $cell).text();

			if ( ! confirm( sprintf( __( 'Are you sure you want to delete the %1$s post type?', 'easy-post-types-fields' ), postType ) ) ) {
				event.preventDefault();
				return false;
			}

			if ( Number( event.target.dataset.post_count ) > 0 && ! confirm( __( 'The database contains at least one post of this post type. By deleting this post type, WordPress will not be able to access those posts any longer. Are you sure you want to continue?', 'easy-post-types-fields' ) ) ) {
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
			});

			$( `input[name="previous_slug"]`, $destination ).val( $( '.hidden>div.slug', $source ).text() );
		}

		$(document).on('click', '#the-list a.taxonomy-delete, #the-list a.custom-field-delete', (event) => {
			event.preventDefault();

			const $table = $(event.target).closest('table'),
				  $row   = $(event.target).closest('tr'),
				  name   = $( 'a.row-title', $row ).text(),
				  slug   = $( 'td.column-slug', $row ).text(),
				  type   = 'taxonomy';

			if ( ! confirm( sprintf( __( 'Are you sure you want to delete the %1$s %2$s?', 'easy-post-types-fields' ), name, __( 'taxonomy', 'easy-post-types-fields' ) ) ) ) {
				return false;
			}

			const postData = {
				action: 'ept_inline_delete',
				_inline_delete: $(event.target).data('_wpnonce'),
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

		$(document).on('input', 'form.ept-list-item input[name="singular_name"]', (event) => {
			const slug = wp.url.cleanForSlug($(event.target).val())
			$(event.target).closest('fieldset').find('input[name="slug"]').val(slug);
		})

		$(document).on('change', 'form.ept-list-item input[name="slug"]', (event) => {
			const slug = wp.url.cleanForSlug($(event.target).val())
			$(event.target).val(slug);
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
})( jQuery, wp )
