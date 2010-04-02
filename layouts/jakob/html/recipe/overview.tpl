{use $model, $root = $model->request->root}
{tr_context "recipes"}
<ul class="commands">
	<li><a href="{$root}/recipes/edit">{tr "Add recipe"}</a></li>
	<li><a href="{$root}/recipes/tags">{tr "Tagindex"}</a></li>
	<li><a href="{$root}/recipes/all">{tr "Alphabetically"}</a></li>
	<li><a href="{$root}/recipes/listRecipe">{tr "List"}</a></li>
</ul>
<div class="page">
	<h2>{tr "Recipes"}</h2>

	<p><a href="{$root}/{$model->request->controller}/edit/"><img width="18" height="17" src="{$root}/images/jakob/new_recipe.png" title="{tr "Create a new recipe"}"/> {tr "Create a new recipe"}</a></p>

	<h3>{tr "Most popular tags"}</h3>

	{include arbit_get_template( 'html/recipe/tag_cloud.tpl' )
		send $model->request as $request, $model->popular as $tags}

	<h3>{tr "Search recipes"}</h3>

	{include arbit_get_template( 'html/recipe/search_box.tpl' )
		send $model->request as $request}
</div>
