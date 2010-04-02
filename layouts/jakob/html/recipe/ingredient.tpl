{use $model, $root = $model->request->root}
{tr_context "recipes"}
<ul class="commands">
	<li><a href="{$root}/recipes/edit">{tr "Add recipe"}</a></li>
	<li><a href="{$root}/recipes/tags">{tr "Tagindex"}</a></li>
	<li><a href="{$root}/recipes/all">{tr "Alphabetically"}</a></li>
</ul>
<div class="page">
	<h2>{tr "Ingredient %ingredient" vars "ingredient" => $model->tag}</h2>

	<ul>
	{foreach $model->recipes as $recipe}
		<li>
			{include arbit_get_template( 'html/recipe/view_short.tpl' )
				send $model->request as $request, $recipe}
		</li>
	{/foreach}
	</ul>
</div>
