(function ( $ ) {
    $( document ).ready( function () {
        $( document ).on( 'click', '.elmc-notice-expiring-products .notice-dismiss', () => {
            let data = {
                action: 'elmc_dismiss_products_expiring_notice',
                security: elmc_admin.dismiss_products_expiring_notice.nonce
            };

            $.post( ajaxurl, data );
        } );

        $( '.elmc-notice-update' ).each( function () {
            let forPlugin      = $( this ).data( 'for-plugin' );
            let pluginCheckbox = `#checkbox_${forPlugin}`;

            if ($( pluginCheckbox ).length) {
                let tr = $( pluginCheckbox ).parents( 'tr' );

                setTimeout( function () {
                    $( `[data-for-plugin='${forPlugin}']` ).appendTo( tr.find( 'td.plugin-title p:first' ) );
                }, 50 );
            }
        } );
    } );
})( jQuery );
