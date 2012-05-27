function addIngredient( targetGroup )
{
	var item = $(
        itemHtml
            .replace( /%item/g, ingredients[targetGroup] )
            .replace( /%group/g, targetGroup )
	);

	registerAutocomplete( 
		'ingredients', 
		item.find( 'input.ingredient' )
	);
	registerAutocomplete( 
		'units', 
		item.find( 'input.unit' )
	);
	
    $( "li#group_" + targetGroup + " ul" ).append( item );

    ++ingredients[targetGroup];
}

function addIngredientBlock()
{
    $( "ul.ingredients" ).append( groupHtml.replace( /%group/g, group ) );
    ingredients[group] = 0;
    addIngredient( group );
    addIngredient( group );
    addIngredient( group );
    ++group;
}

function registerAutocomplete( type, selector ) {
    $( selector ).autocomplete({
        source: function( request, callback )
            {
                var callback = callback;

                $.get( root + "/recipes/" + type + "/" + request.term + ".js", function ( data, textStatus )
                    {
                        var terms = [];
                        $.each( data.properties.view.properties["array"], function( key, value )
                            {
                                terms.push( key );
                            }
                        );
                        callback( terms );
                    },
                    "json"
                );
            }
        }
    );
}

$( document ).ready( function()
{
    if ( data == null )
    {
        addIngredientBlock();
    }
    else
    {
        // Create blocks from existing data
        $.each( data, function( title, values )
        {
            var currentGroup = group;
            addIngredientBlock();
            $( "li#group_" + currentGroup + " h4 input" ).attr( "value", title );

            // Fill up ingredient items with existing data
            $.each( values, function( key, properties )
            {
                if ( key > 1 )
                {
                    addIngredient( currentGroup );
                }

                $.each( properties, function( name, value )
                {
                    $( "li#ingredient_" + currentGroup + "_" + key + " input[class*=\"" + name + "\"]" ).attr( "value", value ); 
                } );
            } );
        } );
    }
	
	registerAutocomplete( 'ingredients', 'input.ingredient' );
	registerAutocomplete( 'units', 'input.unit' );
} );

