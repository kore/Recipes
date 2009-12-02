{use $model, $root = $model->request->root}
{tr_context "recipes"}
<h2>{tr "Recipes"}</h2>

<p><a href="{$root}/{$model->request->controller}/edit/"><img width="14" height="14" src="{$root}/images/add.png" title="{tr "Create a new recipe"}"/> {tr "Create a new recipe"}</a></p>

<h3>{tr "Most popular tags"}</h3>

{include arbit_get_template( 'html/recipe/tag_cloud.tpl' )
	send $model->request as $request, $model->popular as $tags}

<h3>{tr "Search recipes"}</h3>

{include arbit_get_template( 'html/recipe/search_box.tpl' )
	send $model->request as $request}

