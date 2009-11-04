{use $model, $root = $model->request->root, $user = $model->user}
{var $value = null}
<h2>User management</h2>

{if !$model->loggedIn}
<p>
	You can't manage an account, while you are not logged in.
</p>
{else}
<p>
	You can modify your account details in this view.
</p>

<h3>Account</h3>

{include arbit_get_template( 'html/core/errors.tpl' )
	send $model->errors as $errors}

{include arbit_get_template( 'html/core/success.tpl' )
	send $model->success as $success}

<form method="post" action="{$root}/{$model->request->controller}/core/account">
<fieldset>
	<legend>Account data</legend>

	<input type="hidden" name="_arbit_form_token" value="{arbit_form_token()}" />
	<label>
		<input type="text" name="login" value="{$user->login}" disabled="disabled" />
		Login
	</label>
	<label>
		<input type="text" name="fullname" value="{$user->name}" />
		Name
	</label>
	<label>
		<input type="text" name="email" value="{$user->email}" disabled="disabled" />
		Email
	</label>

	<label>
		<input type="submit" name="account_change" value="Store changes" />
	</label>
</fieldset>
</form>

{include arbit_get_template( 'html/core/user_account/' . $user->auth_type . '.tpl' )
	send $root, $model, $user}
{/if}

<form method="post" action="{$root}/{$model->request->controller}/core/account">
<fieldset>
	<legend>User settings</legend>

	<input type="hidden" name="_arbit_form_token" value="{arbit_form_token()}" />
	<label>
		<select size="1" name="date_timezone">
		{$value = 'UTC'}
		{if $user->settings && is_set($user->settings['date_timezone'])}
			{$value = $user->settings['date_timezone']}
		{/if}
		{foreach date_timezone_list() as $timezone}
			<option value="{$timezone}"{if $timezone == $value} selected="selected"{/if}>{$timezone}</option>
		{/foreach}
		</select>
		Timezone
	</label>
	<label>
		{$value = 'D, d M y H:i:s O'}
		{if $user->settings && is_set($user->settings['date_format'])}
			{$value = $user->settings['date_format']}
		{/if}
		<select size="1" name="date_format">
		{foreach array(
				"D, d M y H:i:s O" => "RFC 822 (example: Mon, 15 Aug 05 15:52:01 +0000)",
				"l, d-M-y H:i:s T" => "RFC 850 (example: Monday, 15-Aug-05 15:52:01 UTC)",
				"D, d M Y H:i:s O" => "RFC 1123 (example: Mon, 15 Aug 2005 15:52:01 +0000)",
				"Y-m-d\TH:i:sP"    => "World Wide Web Consortium (example: 2005-08-15T15:52:01+00:00)",
			) as $format => $description}
			<option value="{$format}"{if $format == $value} selected="selected"{/if}>{$description}</option>
		{/foreach}
		</select>
		Date fromat
	</label>

	<label>
		<input type="submit" name="settings_change" value="Store changes" />
	</label>
</fieldset>
</form>

