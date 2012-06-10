/*global jQuery: false */

var itemHtml = itemHtml || "",
    groupHtml = groupHtml || "",
    data = data || null,
    group = group || 0,
    ingredients = ingredients || [];

function registerAutocomplete( type, selector ) {
    "use strict";

    jQuery( selector ).autocomplete({
        source: function( request, callback )
            {
                jQuery.get( "/recipe/" + type + "/" + request.term + ".js", function ( data, textStatus )
                    {
                        var terms = [];
                        jQuery.each( data, function( key, value )
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

function addIngredient( targetGroup )
{
    "use strict";

	var item = jQuery(
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
	
    jQuery( "li#group_" + targetGroup + " ul" ).append( item );

    ingredients[targetGroup] += 1;
}

function addIngredientBlock()
{
    "use strict";

    jQuery( "ul.ingredients" ).append( groupHtml.replace( /%group/g, group ) );
    ingredients[group] = 0;
    addIngredient( group );
    addIngredient( group );
    addIngredient( group );
    group += 1;
}

jQuery( document ).ready( function()
{
    "use strict";

    console.log( data );
    if ( data === null )
    {
        addIngredientBlock();
    }
    else
    {
        // Create blocks from existing data
        jQuery.each( data, function( title, values )
        {
            var currentGroup = group;
            addIngredientBlock();
            jQuery( "li#group_" + currentGroup + " h4 input" ).attr( "value", title );

            // Fill up ingredient items with existing data
            jQuery.each( values, function( key, properties )
            {
                if ( key > 1 )
                {
                    addIngredient( currentGroup );
                }

                jQuery.each( properties, function( name, value )
                {
                    jQuery( "li#ingredient_" + currentGroup + "_" + key + " input[class*=\"" + name + "\"]" ).attr( "value", value ); 
                } );
            } );
        } );
    }
	
	registerAutocomplete( 'ingredients', 'input.ingredient' );
	registerAutocomplete( 'units', 'input.unit' );
} );

