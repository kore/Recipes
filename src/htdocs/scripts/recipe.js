/*global jQuery: false */

jQuery( document ).ready( function()
{
    "use strict";

    jQuery( "textarea" ).elastic();
    jQuery( "form.protect" ).protect( 'Änderungen am Rezept werden dabei verloren gehen.' );

    jQuery( "ul.images a" ).unbind( "click" );
    jQuery( "ul.images a" ).colorbox( {
        rel: "thumbnail",
        scale: true,
        maxWidth: "80%",
        maxHeight: "80%"
    } );

} );
