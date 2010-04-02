{use $model, $root = $model->request->root}
{tr_context "recipes"}
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
