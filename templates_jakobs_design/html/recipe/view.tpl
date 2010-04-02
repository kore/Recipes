{use $model, $recipe = $model->recipe, $root = $model->request->root}
{tr_context "recipes"}
{if $recipe->tags}
<ul class="tags">
{foreach $recipe->tags as $tag}
    <li><a href="{$root}/{$model->request->controller}/tag/{$tag}">{$tag}</a></li>
{/foreach}
</ul>
{/if}

<ul class="commands">
    <li>
        <a href="{$root}/{$model->request->controller}/edit/{$recipe->id}">
            {tr "Edit"}
        </a>
    </li>
    <li>
        <a onclick="return confirm( '{tr "Do you really want to delete this recipe?"}' );" href="{$root}/{$model->request->controller}/delete/{$recipe->id}">
            {tr "Delete"}
        </a>
    </li>
    <li>
        <a href="{$root}/{$model->request->controller}/listRecipe/{$recipe->id}">
            {tr "On list"}
        </a>
    </li>
    <li>
        <a href="{$root}/{$model->request->controller}/listExports/{$recipe->id}">
            {tr "Export"}
        </a>
    </li>
</ul>

<div class="page">
	<h2>{$recipe->title} <a class="edit" href="{$root}/{$model->request->controller}/edit/{$recipe->id}">[{tr "Edit recipe"}]</a></h2>
	<h4 class="subtitle">{tr "for %count persons" vars "count" => $recipe->amount}</h4>

	<p>{$recipe->description}</p>

	<h3>{tr "Ingredients"}</h3>

	<ul class="ingredients">
	{foreach $recipe->ingredients as $section => $ingredients}
		<li><h4>{$section}</h4>
			<table class="ingredients">
			{foreach $ingredients as $ingredient}
				<tr><td class="amount">{$ingredient['amount']} {$ingredient['unit']}</td><td><a href="{$root}/{$model->request->controller}/ingredient/{$ingredient['ingredient']}">{$ingredient['ingredient']}</a></td></tr>
			{/foreach}
			</table>
		</li>
	{/foreach}
	</ul>

	<h3>{tr "Instructions"}</h3>

	<dl>
		<dt>{tr "Preparation time"}</dt>
		<dd>{$recipe->preparation} {tr "minutes"}</dd>
		<dt>{tr "Cooking time"}</dt>
		<dd>{$recipe->cooking} {tr "minutes"}</dd>
	</dl>

	{if $recipe->html}
		{raw $recipe->html}
	{else}
		{arbit_simple_markup( $recipe->instructions )}
	{/if}

	<a href="{$root}/{$model->request->controller}/edit/{$recipe->id}">{tr "Edit recipe"}</a>
</div>
