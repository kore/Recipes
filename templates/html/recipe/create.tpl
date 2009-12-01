{use $model, $root = $model->request->root}
{tr_context "recipes"}
<h2>{tr "Add recipe"}</h2>

{include arbit_get_template( 'html/core/errors.tpl' )
	send $model->errors as $errors}

{include arbit_get_template( 'html/core/success.tpl' )
	send $model->success as $success}

<form method="post" action="{$root}/{$model->request->controller}/{$model->request->action}{if $model->recipe}/{$model->recipe->id}{/if}"
      onsubmit="return validateForm( this );">
<fieldset>
	<legend>{tr "Add recipe"}</legend>

	<input type="hidden" name="_arbit_form_token" value="{arbit_form_token()}" />

    <div class="recipe">
		<h2><input type="text" class="required h1" name="title" value="{if $model->recipe}{$model->recipe->title}{else}{tr "Recipe title"}{/if}" /></h2>
		<h4 class="subtitle">{tr "For"} <input type="text" name="amount" class="required number" value="{if $model->recipe}{$model->recipe->amount}{else}4{/if}"/> {tr "persons"}.</h4>
    
		<p><textarea name="description" rows="3">{if $model->recipe}{$model->recipe->description}{else}{tr "Short description of the recipe"}{/if}</textarea></p>

        <h3>{tr "Ingredients"}</h3>

<script type="text/ecmascript">
// <![CDATA[
var group = 0;
var ingredients = [];
var data = {if $model->recipe}{raw json_encode( $model->recipe->ingredients )}{else}null{/if};

var groupHtml = "<li id=\"group_%group\">\
    <h4>\
        <input type=\"text\" name=\"ingredients[%group][title]\" class=\"title\" value=\"{tr "Main ingredients"}\"/>\
        <image class=\"button\" onclick=\"addIngredientBlock()\" width=\"14\" height=\"14\" src=\"{$root}/images/add.png\" alt=\"Add\" />\
        <image class=\"button\" onclick=\"confirm( '{tr "Are you sure that you want to remove the whole section?"}' ) && $( 'li#group_%group' ).remove()\" width=\"14\" height=\"14\" src=\"{$root}/images/remove.png\" alt=\"Remove\" />\
    </h4>\
    <ul></ul>\
</li>";

var itemHtml  = "<li id=\"ingredient_%group_%item\">\
    <input type=\"text\" name=\"ingredients[%group][%item][amount]\" class=\"amount number\"/>\
    <input type=\"text\" name=\"ingredients[%group][%item][unit]\" class=\"unit number\"\"/>\
    <input type=\"text\" name=\"ingredients[%group][%item][ingredient]\" class=\"ingredient\"/>\
    <image class=\"button\" onclick=\"addIngredient( %group )\" width=\"14\" height=\"14\" src=\"{$root}/images/add.png\" alt=\"Add\" />\
    <image class=\"button\" onclick=\"confirm( '{tr "Are you sure that you want to remove the ingredient?"}' ) && $( 'li#ingredient_%group_%item' ).remove()\" width=\"14\" height=\"14\" src=\"{$root}/images/remove.png\" alt=\"Remove\" />\
</li>";

 {literal}
function addIngredient( targetGroup )
{
    $( "li#group_" + targetGroup + " ul" ).append(
        itemHtml
            .replace( /%item/g, ingredients[targetGroup] )
            .replace( /%group/g, targetGroup )
    );
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
                if ( key > 2 )
                {
                    addIngredientBlock();
                }

                $.each( properties, function( name, value )
                {
                    $( "li#ingredient_" + currentGroup + "_" + key + " input[class*=\"" + name + "\"]" ).attr( "value", value ); 
                } );
            } );
        } );
    }
} );
{/literal} // ]]>
</script>

        <ul class="ingredients">
        </ul>

        <h3>{tr "Instructions"}</h3>
        <dl>
            <dt>{tr "Preparation time"}</dt>
            <dd><input type="text" name="preparation" class="number" value="{if $model->recipe}{$model->recipe->preparation}{else}0{/if}"/> minutes</dd>
            <dt>{tr "Cooking time"}</dt>
            <dd><input type="text" name="cooking" class="number" value="{if $model->recipe}{$model->recipe->cooking}{else}60{/if}"/> minutes</dd>
        </dl>
		<p><textarea name="instructions" class="required" rows="5">{if $model->recipe}{$model->recipe->instructions}{else}{tr "Preparation instructions"}{/if}</textarea></p>
    </div>

	<label>
		<input type="submit" name="store" value="{tr "Store recipe"}" />
	</label>
</fieldset>
</form>

