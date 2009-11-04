{use $model, $root = $model->request->root}
{var $token = arbit_form_token()}
{cycle $background = array('light', 'dark')}
<h2>Project permissions</h2>

<p>
	You can assign permissions to selected groups on this page, and also create
	new groups or delete existing groups.
</p>

<h3>Permissions</h3>

<form method="post" action="{$root}/{$model->request->controller}/core/permissions" class="wide">
<fieldset>
	<legend>Group permissions</legend>

	<input type="hidden" name="_arbit_form_token" value="{$token}" />

	<table>
		<thead>
			<tr>
				<th></th>
			{foreach $model->groups as $group}
				<th title="{$group->description}">
					{$group->name}
				</th>
			{/foreach}
			</tr>
		</thead>
	{foreach $model->permissions as $module => $permissions}
		<tbody>
			<tr>
				<th colspan="{1 + array_count($model->groups)}">
					{$module}
				</th>
			</tr>
		{foreach $permissions as $name => $description increment $background}
			<tr class="{$background}">
				<th title="{$description}">{$name}</th>
			{foreach $model->groups as $group}
				<td>
					<input type="checkbox" name="permission[{$group->id}][{$name}]" value="1"
						{if !arbit_may('core_groups_edit')}disabled="disabled" {/if}
						{if is_array($group->permissions) && array_contains($group->permissions, $name)}checked="checked" {/if}
						/>
				</td>
			{/foreach}
			</tr>
		{/foreach}
		</tbody>
	{/foreach}
	</table>

	{if arbit_may('core_groups_edit')}
	<label>
		<input type="submit" name="store_permissions" value="Store" />
	</label>
	{/if}
</fieldset>
</form>

{if arbit_may('core_groups_create')}
<h3>Groups</h3>

<form method="post" action="{$root}/{$model->request->controller}/core/permissions"
    onsubmit="return validateForm( this );">
<fieldset>
	<legend>Create new group</legend>

	<input type="hidden" name="_arbit_form_token" value="{$token}" />

	<label>
		<input type="text" class="required" name="name" />
		Name
	</label>
	<label>
		<textarea class="required" rows="3" cols="40" name="description" />
		Description
	</label>

	<label>
		<input type="submit" name="create_group" value="Create" />
	</label>
</fieldset>
</form>

<form method="post" action="{$root}/{$model->request->controller}/core/permissions"
	onsubmit="return confirm('Really delete group?');">
<fieldset>
	<legend>Remove a group</legend>

	<input type="hidden" name="_arbit_form_token" value="{$token}" />

	<label>
		<select size="1" name="group">
		{foreach $model->groups as $group}
			<option value="{$group->id}">{$group->name}</option>
		{/foreach}
		</select>
		Name
	</label>

	<label>
		<input type="submit" name="remove_group" value="Delete group" />
	</label>
</fieldset>
</form>
{/if}

