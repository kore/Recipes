{use $model, $root = $model->request->root, $project = $model->request->controller}
{tr_context "recipes"}
<h2>{tr "Login"}</h2>
<p>
	{tr "Login with your credentials using the method you registered with."}
</p>

{include 'html/core/errors.tpl' 
	send $model->errors as $errors}

<form method="post" action="{$root}/{$project}/login/{$model->selected}" onsubmit="return validateForm( this );">
	<fieldset>
		{include arbit_get_template( 'html/core/user/login/' . $model->selected . '.tpl' )
			send $root, $project}
		<input type="hidden" name="_arbit_form_token" value="{arbit_form_token()}" />
		<div class="break"></div>
	</fieldset>
</form>

