{var $values = arbit_get_form_values( array(
    'openid' => 'string',
) )}
<legend>OpenID login</legend>

<label>
	<input type="text" name="openid" value="{$values['openid']}" />
	OpenID
</label>
<label>
	<input type="checkbox" name="keepme" value="1" />
	Keep me logged in
</label>

<label>
	<input type="submit" name="submit" value="Login" />
</label>
