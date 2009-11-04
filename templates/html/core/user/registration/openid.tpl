{var $values = arbit_get_form_values( array(
    'openid' => 'string',
) )}
<legend>OpenID registration</legend>

<label>
	<input type="text" name="openid" value="{$values['openid']}" />
	OpenID
</label>

<label>
	<input type="submit" name="submit" value="Register" />
</label>
