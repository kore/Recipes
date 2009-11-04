{use $model, $root = $model->request->root}

{if $model->menu}
<ul class="navigation">
{foreach $model->menu as $name => $id}
	<li><a 
		href="{$root}/{$model->request->controller}/{$model->request->action}/{$id}" 
		{if $id === $model->request->subaction}class="selected"{/if}
		title="{$name}">
			{$name}
	</a></li>
{/foreach}
</ul>
{/if}

<div class="content">
	{* @TODO: This check should not be necessary in production *}
	{if $model->content !== null}
		{raw arbit_decorate($model->content)}
	{/if}
</div>
