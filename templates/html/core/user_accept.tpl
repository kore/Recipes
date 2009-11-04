{use $model, $root = $model->request->root}
{var $token = arbit_form_token()}
{cycle $background = array('light', 'dark')}
<h2>User management</h2>

{if !arbit_may('core_users_accept')}
<p>
	You do not have sufficant permissions to manage users.
</p>
{else}
<p>
	You can modify the validation state of users in this view, and may
	deactivate users.
</p>

<h3>Groups</h3>

<fieldset>
	<legend>Users</legend>

	<table>
		<thead>
			<tr>
				<th>User</th>
				<th>State</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th colspan="2">
					All users
				</th>
			</tr>
	{foreach $model->users as $user increment $background}
			<tr class="{$background}">
				<th>{include arbit_get_template( 'html/user/name.tpl' ) send $user}</th>
				<td>
					<form method="post" action="{$root}/{$model->request->controller}/core/accept" class="wide">
					<fieldset>
						<legend style="display: none;">User validation state</legend>
						<input type="hidden" name="_arbit_form_token" value="{$token}" />
						<label>
							<input type="hidden" name="user" value="{$user->id}" />
							<select size="1" name="state">
								<option {if $user->valid === "0"}selected="selected"{/if} value="0">Deactivated</option>
								<option {if $user->valid === "1"}selected="selected"{/if} value="1">Valid</option>
								<option {if $user->valid !== "0" && $user->valid !== "1"}selected="selected"{/if} value="2">Unconfirmed</option>
							</select>
							Curent state
						</label>
						<label>
							<input type="submit" name="store" value="Change state" />
						</label>
					</fieldset>
					</form>
				</td>
			</tr>
	{/foreach}
		</tbody>
	</table>
</fieldset>
{/if}

