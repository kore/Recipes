{use $model, $root = $model->request->root, $project = $model->request->controller}
{tr_context "recipes"}
<ul class="commands">
	<li><a href="{$root}/user/Login">{tr "Login"}</a></li>
</ul>
<div class="page">
	<h2>Register</h2>
	<p>
		{tr "Use this forms to register as a new user."}</p>

	{if $model->errors !== null }
	<ul class="errors">
	{foreach $model->errors as $error}
		<li>{arbit_show($error)}</li>
	{/foreach}
	</ul>
	{/if}

	<form method="post" action="{$root}/{$project}/register/{$model->selected}" onsubmit="return validateForm( this );">
		<fieldset>
			{include arbit_get_template( 'html/core/user/registration/' . $model->selected . '.tpl' )}
			<input type="hidden" name="_arbit_form_token" value="{arbit_form_token()}" />
			<div class="break"></div>
		</fieldset>
	</form>
</div>
