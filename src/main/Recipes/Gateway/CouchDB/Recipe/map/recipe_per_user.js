function ( doc )
{
    if ( doc.type === "recipe" ) {
        emit( [doc.user, doc._id], null );

        for ( i in doc.revisions ) {
            emit( [doc.revisions[i].user, doc._id], null );
        }
    }
}
