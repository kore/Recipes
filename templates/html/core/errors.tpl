{use $errors=array()}
{if $errors !== null && array_count($errors)}
<ul class="errors">
{foreach $errors as $error}
	<li>{arbit_show($error)}</li>
{/foreach}
</ul>
{/if}
