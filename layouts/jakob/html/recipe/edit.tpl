{use $model, $root = $model->request->root}
{tr_context "recipes"}
<ul class="commands">
	{if $model->recipe}
		<li>
			<a href="{$root}/{$model->request->controller}/view/{$model->recipe->id}">
				{tr "View"}
			</a>
		</li>
		<li>
			<a onclick="return confirm( '{tr "Do you really want to delete this recipe?"}' );" href="{$root}/{$model->request->controller}/delete/{$model->recipe->id}">
				{tr "Delete"}
			</a>
		</li>
		<li>
			<a href="{$root}/{$model->request->controller}/listExports/{$model->recipe->id}">
				{tr "Export"}
			</a>
		</li>
	{else}
		<li>
			<a href="{$root}/{$model->request->controller}">
				{tr "Abort"}
			</a>
		</li>
	{/if}
</ul>
<div class="page">
	<h2>{tr "Edit recipe"}</h2>

	{include arbit_get_template( 'html/core/errors.tpl' )
		send $model->errors as $errors}

	{include arbit_get_template( 'html/core/success.tpl' )
		send $model->success as $success}

	<form class="protect" method="post" action="{$root}/{$model->request->controller}/{$model->request->action}{if $model->recipe}/{$model->recipe->id}{/if}"
		  onsubmit="return validateForm( this );">
	<fieldset>
		<legend>{tr "Edit recipe"}</legend>

		<input type="hidden" name="_arbit_form_token" value="{arbit_form_token()}" />

		<div class="recipe">
			<h2><input type="text" class="required h1" name="title" value="{if $model->recipe}{$model->recipe->title}{else}{tr "Recipe title"}{/if}" /></h2>
			<h4 class="subtitle">{tr "For"} <input type="text" name="amount" class="required number" value="{if $model->recipe}{$model->recipe->amount}{else}4{/if}"/> {tr "persons"}.</h4>
		
			<p><textarea name="description" rows="3">{if $model->recipe}{$model->recipe->description}{else}{tr "Short description of the recipe"}{/if}</textarea></p>

			<h3>{tr "Ingredients"}</h3>

	<script type="text/ecmascript">
	// <![CDATA[
	var root        = '{$root}';
	var group       = 0;
	var ingredients = [];
	var data        = {if $model->recipe && $model->recipe->ingredients}{raw json_encode( $model->recipe->ingredients )}{else}null{/if};

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
	{/literal} // ]]>
	</script>

	{if !$model->recipe}
		<script type="text/ecmascript">
		// <![CDATA[ {literal}
		$( document ).ready( function()
		{
			$('input, textarea').each( function() {
				var $$ = $(this);
				$$.data( 'original-value', $$.val() );
				$$.bind( 'focus', function(e) {
					var $$ = $(this);
					if ( $$.val() == $$.data( 'original-value' ) ) {
						$$.val("");
					}
				});
				$$.bind( 'blur', function(e) {
					var $$ = $(this);
					if ( $$.val() == "" ) {
						$$.val( $$.data( 'original-value' ) );
					}
				});
			}); 
		} );
		{/literal} // ]]>
		</script>
	{/if}


			<ul class="ingredients">
			</ul>

			<h3>{tr "Instructions"}</h3>
			<dl>
				<dt>{tr "Preparation time"}</dt>
				<dd><input type="text" name="preparation" class="number" value="{if $model->recipe}{$model->recipe->preparation}{else}0{/if}"/> {tr "minutes"}</dd>
				<dt>{tr "Cooking time"}</dt>
				<dd><input type="text" name="cooking" class="number" value="{if $model->recipe}{$model->recipe->cooking}{else}60{/if}"/> {tr "minutes"}</dd>
			</dl>
			<p><textarea name="instructions" class="required" rows="5">{if $model->recipe}{$model->recipe->instructions}{else}{tr "Preparation instructions"}{/if}</textarea></p>

			<p>{tr "Tagged with:"} <input type="text" class="tags" name="tags" value="{if $model->recipe}{str_join( ', ', $model->recipe->tags)}{else}{tr "cake, easter, vanilla"}{/if}" /></p>
		</div>

		<label>
			<input type="submit" name="store" value="{tr "Store recipe"}" />
		</label>
	</fieldset>
	</form>
</div>
