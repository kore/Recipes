{use $model, $root = $model->request->root}
{tr_context "core/dashboard"}

{* Aggregate module state and quality factors *}
{var $projectState = true,
	 $projectQuality = array_count( $model->quality )}
{foreach $model->state as $state}
	{if !$state}
		{$projectState = false}
	{/if}
{/foreach}
<li class="{if $projectState}success{else}failed{/if}">
{if $projectQuality}
	<object class="pqi" type="image/svg+xml" data="{$root}/dashboard/quality/{$model->id}" width="80" height="80">
		{tr "You need an SVG enabled browser to see this." context "core"}
	</object>
{/if}
	<h3>
		<a href="{$root}/{$model->id}" title="{$model->name}">{$model->name}</a>
	</h3>
	<p>
		{$model->description}
	</p>
{if $model->messages}
	<ul class="messages">
	{foreach $model->messages as $module => $message}
		<li class="{if $model->state[$module]}success{else}failed{/if}">{$module}: {arbit_show($message)}</li>
	{/foreach}
	</ul>
{/if}
	<div class="break"></div>
</li>
