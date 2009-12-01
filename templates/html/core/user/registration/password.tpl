{var $values = arbit_get_form_values( array(
    'login' => 'string',
    'email' => 'string',
) )}
<legend>Password registration</legend>

<label>
	<input type="text" class="required" name="login" value="{$values['login']}" />
	Username
</label>
<label>
	<input type="password" class="required" name="password_0" />
	Password
</label>
<label>
	<input type="password" class="required" name="password_1" />
	Repeat password
</label>
<label>
	<input type="text" class="required" name="email" value="{$values['email']}" />
	Email
</label>

<label>
	<input type="submit" name="submit" value="Register" />
</label>
