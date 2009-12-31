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
            <img src="{$root}/images/edit.png" width="16" height="16" alt="{tr "Edit recipe"}" />
            {tr "Edit recipe"}
        </a>
    </li>
    <li>
        <a onclick="return confirm( '{tr "Do you really want to delete this recipe?"}' );" href="{$root}/{$model->request->controller}/delete/{$recipe->id}">
            <img src="{$root}/images/remove.png" width="16" height="16" alt="{tr "Delete recipe"}" />
            {tr "Delete recipe"}
        </a>
    </li>
    <li>
        <a href="{$root}/{$model->request->controller}/listRecipe/{$recipe->id}">
            <img src="{$root}/images/add.png" width="16" height="16" alt="{tr "Put on list"}" />
            {tr "Put on list"}
        </a>
    </li>
    <li>
        <form method="GET" action="{$root}/{$model->request->controller}/export/{$recipe->id}" class="export">
            <label>
                <input type="text" name="amount" value="4" />
                {tr "Amount"}
            </label>
            <label>
                <select name="format" size="1">
                    <option value=".pdf">{tr "PDF"}</option>
                    <option value=".odt">{tr "Word"}</option>
                    <option value=".txt">{tr "Text"}</option>
                    <option value=".html">{tr "HTML"}</option>
                    <option value=".xml">{tr "Docbook"}</option>
                </select>
                {tr "Format"}
            </label>
            <input type="submit" value="{tr "Export"}" />
            <div class="break"/>
        </form>
    </li>
</ul>

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
