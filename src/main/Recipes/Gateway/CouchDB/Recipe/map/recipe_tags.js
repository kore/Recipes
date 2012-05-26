function( doc )
{
    if ( doc.type == "recipe" )
    {
        for( var key = 0; key < doc.tags.length; ++key ) 
        {
            emit( doc.tags[key], doc._id );
        }
    }
}
