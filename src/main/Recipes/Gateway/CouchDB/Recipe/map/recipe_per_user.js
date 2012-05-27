function ( doc )
{
    if ( doc.type === "recipe" ) {
        emit( [doc.user, doc._id], null );

        for ( i in doc.revisions ) {
            if ( doc.revisions[i].user ) {
                emit( [doc.revisions[i].user, doc._id], null );
            }
        }
    }
}
