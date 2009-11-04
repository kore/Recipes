{var $values = arbit_get_form_values( array(
    'login' => 'string',
    'email' => 'string',
) )}
<legend>Password registration</legend>

<label>
	<input type="text" name="login" value="{$values['login']}" />
	Username
</label>
<label>
	<input type="password" name="password_0" />
	Password
</label>
<label>
	<input type="password" name="password_1" />
	Repeat password
</label>
<label>
	<input type="text" name="email" value="{$values['email']}" />
	Email
</label>

<label>
	<input type="submit" name="submit" value="Register" />
</label>
