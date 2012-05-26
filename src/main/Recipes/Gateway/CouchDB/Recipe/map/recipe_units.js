function( doc )
{
    if ( doc.type == "recipe" )
    {
        for ( var group in doc.ingredients ) 
        {
            for( var key = 0; key < doc.ingredients[group].length; ++key ) 
            {
                emit( doc.ingredients[group][key]["unit"], 1 );
            }
        }
    }
}
