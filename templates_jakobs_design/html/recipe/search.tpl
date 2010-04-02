{use $model, $root = $model->request->root}
{tr_context "recipes"}
<div class="page">
	<h2>{tr "Search recipes"}</h2>

	{include arbit_get_template( 'html/recipe/search_box.tpl' )
		send $model->request as $request, $model->term as $term}

	{if !$model->result->count}
		<p>{tr "Issue search had no results."}</p>
	{else}
	<h3>{tr "%number results in search for \"%term\""
		vars
			"number" => $model->result->count,
			"term"   => $model->term}</h3>
	<ol class="search" start="{$model->offset + 1}">
		{foreach $model->result->documents as $document}
		<li>
			{include arbit_get_template( 'html/recipe/view_short.tpl' )
				send $model->request as $request, $document->document as $recipe}
		</li>
		{/foreach}
	</ol>

	{include arbit_get_template( 'html/recipe/scrollbar.tpl' )
		send $model->result->count as $count,
			 10 as $limit,
			 $model->offset as $offset,
			 $root . "/" . $model->request->controller . "/" . $model->request->action . "/search?search=" . url_encode( $model->term ) . "&" as $baseUrl}
	{/if}
</div>
