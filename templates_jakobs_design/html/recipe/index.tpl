{use $model, $root = $model->request->root}
{var $grouped}
{tr_context "recipes"}
<div class="page">
	<h2>{tr "Index"}</h2>

	<h3>{tr "All recipes"}</h3>
	{foreach $model->list as $recipe}
		{$grouped[str_lower( str_left( $recipe->title, 1 ) )][] = $recipe}
	{/foreach}

	<table class="tags">
		<tr>
			<td>
				<ul>
				{foreach array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i' ) as $group}
					{if is_set($grouped[$group])}
					<li><h4>{$group}</h4>
						<ul>
						{foreach $grouped[$group] as $recipe}
							<li><a href="{$root}/{$model->request->controller}/view/{$recipe->id}">{$recipe->title}</a></li>
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
						{foreach $grouped[$group] as $recipe}
							<li><a href="{$root}/{$model->request->controller}/view/{$recipe->id}">{$recipe->title}</a></li>
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
						{foreach $grouped[$group] as $recipe}
							<li><a href="{$root}/{$model->request->controller}/view/{$recipe->id}">{$recipe->title}</a></li>
						{/foreach}
						</ul>
					</li>
					{/if}
				{/foreach}
				</ul>
			</td>
		</tr>
	</table>
</div>
