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
        <a href="{$root}/{$model->request->controller}/listExports/{$recipe->id}">
            <img src="{$root}/images/export.png" width="16" height="16" alt="{tr "Export recipe"}" />
            {tr "List exports"}
        </a>
    </li>
</ul>

<script type="text/ecmascript">
// <![CDATA[
$( document ).ready( function()
{"{"}
    var dialog = $('<div></div>')
        .html('\
            <form method="post" action="{$root}/{$model->request->controller}/attach/{$recipe->id}" enctype="multipart/form-data"\
                onsubmit="return validateForm( this );">\
            <fieldset>\
                <legend>{tr "Upload image"}</legend>\
\
                <input type="hidden" name="_arbit_form_token" value="{arbit_form_token()}" />\
                <input type="hidden" name="recipe" value="{$recipe->id}" />\
\
                <label>\
                    <input type="text" name="name" />\
                    {tr "Name"}\
                </label>\
\
                <label>\
                    <input type="file" name="attachment" />\
                    {tr "Image"}\
                </label>\
\
                <label>\
                    <input type="submit" name="attach" value="{tr "Upload"}" />\
                </label>\
            </fieldset>\
            </form>\
        ')
        .dialog({"{"}
            modal:    true,
            autoOpen: false,
            title:    '{tr "Upload image"}',
            width:    '600px',
        {"}"});

    $('#opener').click(function() {"{"}
        dialog.dialog('open');
    {"}"});
{"}"} );
// ]]>
</script>

<ul class="attachments">
{foreach $recipe->attachments as $attachment => $info}
    <li>
        <a href="{$root}/{$model->request->controller}/view/{$recipe->id}/{$attachment}">
            {$attachment}
        </a>
    </li>
{/foreach}
    <li>
        <a id="opener">{tr "Upload image"}</a>
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
