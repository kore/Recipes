$( document ).ready( function()
{
    $( "textarea" ).elastic();
    $( "form.protect" ).protect( 'Änderungen am Rezept werden dabei verloren gehen.' );

    $( "ul.images a" ).unbind( "click" );
    $( "ul.images a" ).colorbox( {
        rel: "thumbnail",
        scale: true,
        maxWidth: "80%",
        maxHeight: "80%"
    } );

} );
