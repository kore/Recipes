function( doc )
{
    if ( doc.type == "recipe" )
    {
		emit( doc.title, doc._id );
    }
}
