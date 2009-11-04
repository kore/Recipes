{use $model, $root = $model->request->root}
{var $userIds = array()}
{cycle $background = array('light', 'dark')}
<h2>User management</h2>

{if !arbit_may('core_users_manage')}
<p>
	You do not have sufficient permissions to manage users.
</p>
{else}
<p>
	You can edit the user group association on this page and disable user
	accounts.
</p>

<h3>Groups</h3>

<form method="post" action="{$root}/{$model->request->controller}/core/user" class="wide">
<fieldset>
	<legend>Group associations</legend>

	<input type="hidden" name="_arbit_form_token" value="{arbit_form_token()}" />

	<table>
		<thead>
			<tr>
				<th></th>
			{foreach $model->groups as $group}
				{if $group->id === 'group-anonymous' ||
				    $group->id === 'group-users' }
					{continue}
				{/if}
				<th title="{$group->description}">
					{$group->name}
				</th>
			{/foreach}
			</tr>
		</thead>
		<tbody>
			<tr>
				<th colspan="{array_count($model->groups) - 1}">
					All users
				</th>
			</tr>
	{foreach $model->users as $user increment $background}
			<tr class="{$background}">
                <th>{include arbit_get_template( 'html/user/name.tpl' ) send $user}</th>
			{foreach $model->groups as $group}
				{if $group->id === 'group-anonymous' ||
				    $group->id === 'group-users' }
					{continue}
				{/if}
				{$userIds = array()}
				{if $group->users}
					{foreach $group->users as $groupUser}
						{$userIds[] = $groupUser->_id}
					{/foreach}
				{/if}
				<td>
					<input type="checkbox" name="permission[{$group->id}][{$user->id}]" value="1"
						{if array_contains($userIds, $user->id)}checked="checked"{/if}
						/>
				</td>
			{/foreach}
			</tr>
	{/foreach}
		</tbody>
	</table>

	<label>
		<input type="submit" name="store_permissions" value="Store" />
	</label>
</fieldset>
</form>
{/if}

