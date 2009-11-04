{use $model, $root = $model->request->root, $project = $model->request->controller}
<h2>Register</h2>
<p>
	Use this forms to register as a new user. Depending on the project
	configuration there may be different types of user registration availabale.
	Select the one you like most.
</p>

{if $model->errors !== null }
<ul class="errors">
{foreach $model->errors as $error}
	<li>{arbit_show($error)}</li>
{/foreach}
</ul>
{/if}

<ul class="authtypes">
{foreach $model->mechanisms as $name => $type}
	<li {if $type === $model->selected}class="selected"{/if}
		id="{$type}"><a href="{$root}/{$project}/core/register/{$type}" title="{$name}">
		{$name}
	</a></li>
{/foreach}
</ul>

<form method="post" action="{$root}/{$project}/core/register/{$model->selected}">
	<fieldset>
		{include arbit_get_template( 'html/core/user/registration/' . $model->selected . '.tpl' )}
		<input type="hidden" name="_arbit_form_token" value="{arbit_form_token()}" />
		<div class="break"></div>
	</fieldset>
</form>

