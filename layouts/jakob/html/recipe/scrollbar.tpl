{use $count, $offset, $limit, $baseUrl}
{var $page = 1}
<ul class="pager">
    {while $page < ( $count / $limit ) + 1}
        <li>
            <a 
                {if $page * $limit - $limit == $offset}class="selected"{/if}
                href="{$baseUrl}offset={$page * $limit - $limit}">
                {$page}
            </a>
        </li>
        {$page++}
    {/while}
</ul>
