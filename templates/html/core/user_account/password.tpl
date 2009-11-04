{use $root, $user, $model}

<form method="post" action="{$root}/{$model->request->controller}/core/account/password"
    onsubmit="return validateForm( this );">
<fieldset>
	<legend>Change password</legend>

	<input type="hidden" name="_arbit_form_token" value="{arbit_form_token()}" />
	<label>
		<input type="password" name="old_password" class="required" />
		Old password
	</label>
	<label>
		<input type="password" name="password_0" class="required" />
		New password
	</label>
	<label>
		<input type="password" name="password_1" class="required" />
		Repeat password
	</label>

	<label>
		<input type="submit" name="password_change" value="Change password" />
	</label>
</fieldset>
</form>

