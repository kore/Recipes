{use $request, $recipe}
<h4><a href="{$request->root}/{$request->controller}/view/{$recipe->id}">{$recipe->title}</a></h4>
<p>{$recipe->description}</p>
