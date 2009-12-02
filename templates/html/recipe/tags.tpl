{use $model, $root = $model->request->root}
{var $max = 0,
     $grouped}
{tr_context "recipes"}
<h2>{tr "Tags"}</h2>

<h3>{tr "Most popular"}</h3>

{* Build up classes array first so that we only show the TOP n *}
{foreach $model->popular as $tag => $value}
    {$max = math_max( $max, $value )}
{/foreach}

<ul class="cloud">
{foreach $model->popular as $tag => $value}
    <li class="tag{math_floor( $value / $max * 5 )}">
        <a href="{$root}/{$model->request->controller}/tag/{$tag}">{$tag}</a>
    </li>
{/foreach}
</ul>

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

