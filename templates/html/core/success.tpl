{use $success=array()}
{if $success !== null && array_count($success)}
<ul class="success">
{foreach $success as $msg}
	<li>{arbit_show($msg)}</li>
{/foreach}
</ul>
{/if}
