function( doc )
{
    if ( doc.type == "group" )
    {
        emit( doc.name, doc._id );
    }
}
