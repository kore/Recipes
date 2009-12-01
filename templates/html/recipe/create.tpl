{use $model, $root = $model->request->root}
{tr_context "recipes"}
<h2>{tr "Add recipe"}</h2>

{include arbit_get_template( 'html/core/errors.tpl' )
	send $model->errors as $errors}

{include arbit_get_template( 'html/core/success.tpl' )
	send $model->success as $success}

<form method="post" action="{$root}/{$model->request->controller}/{$model->request->action}/add"
      onsubmit="return validateForm( this );">
<fieldset>
	<legend>{tr "Add recipe"}</legend>

	<input type="hidden" name="_arbit_form_token" value="{arbit_form_token()}" />

    <div class="recipe">
		<h2><input type="text" class="required h1" name="name" value="{tr "Recipe title"}" /></h2>
		<h4 class="subtitle">{tr "For"} <input type="text" name="amount" class="required number" value="4"/> {tr "persons"}.</h4>
    
		<p><textarea name="description" rows="5">{tr "Short description of the recipe"}</textarea></p>

        <h3>{tr "Ingredients"}</h3>

<script type="text/ecmascript">
// <![CDATA[
var group = 0;
var ingredients = [];

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
    addIngredientBlock();
} );
{/literal} // ]]>
</script>

        <ul class="ingredients">
        </ul>

        <h3>{tr "Instructions"}</h3>
        <dl>
            <dt>{tr "Preparation time"}</dt>
            <dd><input type="text" name="preparation" class="number" value="0"/> minutes</dd>
            <dt>{tr "Cooking time"}</dt>
            <dd><input type="text" name="cooking" class="number" value="60"/> minutes</dd>
        </dl>
		<p><textarea name="instructions" class="required" rows="10">{tr "Preparation instructions"}</textarea></p>
    </div>

	<label>
		<input type="submit" name="create" value="{tr "Store recipe"}" />
	</label>
</fieldset>
</form>

