function ( doc )
{
    if ( doc.type === "recipe" ) {
        emit( [doc.user, doc._id], doc.revisions.length == 1 ? 1 : 0 );

        for ( i in doc.revisions ) {
            if ( doc.revisions[i].user ) {
                emit( [doc.revisions[i].user, doc._id], i == 1 ? 1 : 0 );
            }
        }
    }
}
