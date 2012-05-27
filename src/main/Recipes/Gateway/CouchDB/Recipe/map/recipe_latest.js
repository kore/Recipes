function ( doc ) {
    if ( doc.type === "recipe" &&
         doc.revisions[0] ) {
        emit( doc.revisions[0]._date, null );
    }
}
