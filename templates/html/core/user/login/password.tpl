{use $root, $project}
{var $values = arbit_get_form_values( array(
    'login'  => 'string',
    'keepme' => 'string',
) )}
<legend>Password login</legend>

<label>
	<input type="text" class="required" name="login" value="{$values['login']}" />
	Username
</label>
<label>
	<input type="password" class="required" name="password" />
	Password
</label>
<label>
	<input type="checkbox" name="keepme" value="1" {if $values['keepme']}checked="checked"{/if} />
	Keep me logged in
</label>

<label>
	<input type="submit" name="submit" value="Login" />
</label>

<div class="break"></div>

{* This action does not exist yet
<p>
	If you forgot your password, use this link to create a new one:
	<a href="{$root}/{$project}/core/password/forgot">
		Forgot password
	</a>
</p>
*}
