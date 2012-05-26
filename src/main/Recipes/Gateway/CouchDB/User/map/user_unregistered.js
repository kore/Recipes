function( doc )
{
    if ( doc.type == "user" &&
         doc.valid !== "0" &&
         doc.valid !== "1" )
    {
        emit( doc.valid, doc._id );
    }
}
