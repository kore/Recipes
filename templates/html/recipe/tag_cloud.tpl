{use $request, $tags}
{var $max = 0}
{* Build up classes array first so that we only show the TOP n *}
{foreach $tags as $tag => $value}
    {$max = math_max( $max, $value )}
{/foreach}

<ul class="cloud">
{foreach $tags as $tag => $value}
    <li class="tag{math_floor( $value / $max * 5 )}">
        <a href="{$request->root}/{$request->controller}/tag/{$tag}">{$tag}</a>
    </li>
{/foreach}
</ul>
