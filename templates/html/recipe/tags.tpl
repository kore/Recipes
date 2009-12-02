{use $model, $root = $model->request->root}
{var $grouped}
{tr_context "recipes"}
<h2>{tr "Tags"}</h2>

<h3>{tr "Most popular"}</h3>

{include arbit_get_template( 'html/recipe/tag_cloud.tpl' )
	send $model->request as $request, $model->popular as $tags}

<h3>{tr "All tags"}</h3>
{foreach $model->all as $tag => $value}
    {$grouped[str_lower( str_left( $tag, 1 ) )][] = $tag}
{/foreach}

<table class="tags">
    <tr>
        <td>
            <ul>
            {foreach array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i' ) as $group}
                {if is_set($grouped[$group])}
                <li><h4>{$group}</h4>
                    <ul>
                    {foreach $grouped[$group] as $tag}
                        <li><a href="{$root}/{$model->request->controller}/tag/{$tag}">{$tag}</a></li>
                    {/foreach}
                    </ul>
                </li>
                {/if}
            {/foreach}
            </ul>
        </td>
        <td>
            <ul>
            {foreach array( 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q' ) as $group}
                {if is_set($grouped[$group])}
                <li><h4>{$group}</h4>
                    <ul>
                    {foreach $grouped[$group] as $tag}
                        <li><a href="{$root}/{$model->request->controller}/tag/{$tag}">{$tag}</a></li>
                    {/foreach}
                    </ul>
                </li>
                {/if}
            {/foreach}
            </ul>
        </td>
        <td>
            <ul>
            {foreach array( 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z' ) as $group}
                {if is_set($grouped[$group])}
                <li><h4>{$group}</h4>
                    <ul>
                    {foreach $grouped[$group] as $tag}
                        <li><a href="{$root}/{$model->request->controller}/tag/{$tag}">{$tag}</a></li>
                    {/foreach}
                    </ul>
                </li>
                {/if}
            {/foreach}
            </ul>
        </td>
    </tr>
</table>

