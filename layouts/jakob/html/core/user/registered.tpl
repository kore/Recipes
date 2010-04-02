{use $model, $root = $model->request->root, $project = $model->request->controller}
<ul class="commands">
	<li><a href="{$root}/{$project}/login">{tr "Login"}</a></li>
	<li><a href="{$root}/user/register">{tr "Register"}</a></li>
</ul>
<div class="page">
	<h2>Thanks for registering</h2>

	{if $model->user->valid === "0"}
		<p>
			You account is currently disabled.
		</p>
		<p>
			This may mean, that it is waiting for an administrator to enable your
			account.
		</p>
	{elseif $model->user->valid === "1"}
		<p>
			Your account has been activated. Feel free to <a
			href="{$root}/{$model->request->controller}/login">login now</a>.
		</p>
	{else}
		<p>
			The confirmation link for your account has been send to you per mail.
			Please use the link provided in the email to confirm the registration
			and login afterwards.
		</p>
		<p>
			You can <a href="{$root}/{$model->request->controller}/login">login
			here</a> after you have completed the registration process.
		</p>
	{/if}
</div>