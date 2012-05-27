$( document ).ready( function()
{
    $( "textarea" ).elastic();
    $( "form.protect" ).protect( 'Änderungen am Rezept werden dabei verloren gehen.' );
} );
