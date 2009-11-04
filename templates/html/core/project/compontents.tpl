{use $components}
<ul>
{foreach $components as $component => $childs}
	<li>
		{$component}
		{if arbit_may('core_components_edit')}
			<button class="delete" type="submit" name="delete_component" value="{$component}"
				title="Remove component {$component}"
				onclick="return confirm('Really remove component {$component}?');"
			/>
		{/if}
		{if array_count($childs) > 0}
			{include arbit_get_template('html/core/project/compontents.tpl')
				send $childs as $components}
		{/if}
	</li>
{/foreach}
</ul>

