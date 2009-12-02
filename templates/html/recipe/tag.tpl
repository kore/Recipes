{use $model, $root = $model->request->root}
{tr_context "recipes"}
<h2>{tr "Tag %tag" vars "tag" => $model->tag}</h2>

<ul>
{foreach $model->recipes as $recipe}
    <li>
        <h4><a href="{$root}/{$model->request->controller}/view/{$recipe->id}">{$recipe->title}</a></h4>
        <p>{$recipe->description}</p>
    </li>
{/foreach}
</ul>
