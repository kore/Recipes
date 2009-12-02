{var $values = arbit_get_form_values( array(
    'login' => 'string',
    'email' => 'string',
) )}
{tr_context "recipes"}
<legend>{tr "Password registration"}</legend>

<label>
	<input type="text" class="required" name="login" value="{$values['login']}" />
	{tr "Username"}
</label>
<label>
	<input type="password" class="required" name="password_0" />
	{tr "Password"}
</label>
<label>
	<input type="password" class="required" name="password_1" />
	{tr "Repeat password"}
</label>
<label>
	<input type="text" class="required" name="email" value="{$values['email']}" />
	{tr "Email"}
</label>

<label>
	<input type="submit" name="submit" value="{tr "Register"}" />
</label>
