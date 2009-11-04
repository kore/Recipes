function( doc )
{
    if ( doc.type == "group" )
    {
        for ( var i = 0; i < doc.users.length; ++i )
        {
            for ( var j = 0; j < doc.permissions.length; ++j )
            {
                emit( doc.users[i], doc.permissions[j] );
            }
        }
    }
}
