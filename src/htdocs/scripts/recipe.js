/*global jQuery: false */

jQuery( document ).ready( function()
{
    "use strict";

    jQuery( "textarea" ).elastic();
    jQuery( ".carousel" ).carousel();
    jQuery( "form.protect" ).protect( 'Änderungen am Rezept werden dabei verloren gehen.' );

    jQuery( ".carousel .item a" ).unbind( "click" );
    jQuery( ".carousel .item a" ).colorbox( {
        rel: "thumbnail",
        scale: true,
        maxWidth: "80%",
        maxHeight: "80%"
    } );

} );
