/**
 * Range control.
 *
 * Stores the control's constructor on wp.customize.kiaRangeControl. The PHP class registers it
 * for its own control type straight after this file loads.
 *
 * @version 2.0.0
 */
( function( $, api ) {

	api.kiaRangeControl = api.Control.extend(
		{

			ready: function() {
				var control = this;

				this.container.on(
					'change',
					'input[data-input-type="range"]',
					function() {
						var value = $( this ).val();
						$( this ).prev( '.kia-range__number-input' ).val( value );
						control.setting.set( value );
					}
				);

				this.container.on(
					'click',
					'.kia-range__reset',
					function() {
						var input        = $( this ).prev( 'input[data-input-type="range"]' ),
							defaultValue = input.data( 'default-value' );

						input.val( defaultValue );
						input.prev( '.kia-range__number-input' ).val( defaultValue );
						input.change();
					}
				);

				this.container.on(
					'keyup',
					'.kia-range__number-input',
					function() {
						if ( this.value !== '' ) {
							if ( parseInt( this.value, 10 ) < parseInt( this.min, 10 ) ) {
								this.value = this.min;
							}
							if ( parseInt( this.value, 10 ) > parseInt( this.max, 10 ) ) {
								this.value = this.max;
							}
						}
					}
				);

			}
		}
	);

} )( jQuery, wp.customize );
