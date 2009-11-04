{use $model, $root = $model->request->root}
<?xml version="1.0"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html 
	xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="{$model->mimetype}; charset={$model->charset}" />

	<link rel="Stylesheet" type="text/css" href="{$root}/styles/screen.css" media="screen" />
	<link rel="Stylesheet" type="text/css" href="{$root}/styles/print.css" media="print" />
{foreach $model->cssFiles as $file}
	<link rel="Stylesheet" type="text/css" href="{$root}/styles/{$file}" media="screen" />
{/foreach}

	<script src="{$root}/scripts/jquery-1.3.2.min.js" type="text/ecmascript"></script>
	<script src="{$root}/scripts/form.js" type="text/ecmascript"></script>

	<title>{$model->name}, tracked with Arbit</title>
</head>
<body class="project" id="{$model->project}">
	<h1>
		<a href="{$root}/dashboard">Arbit - project tracking</a>
	</h1>
	<h2 class="project">
		{$model->name}
	</h2>

{if $model->menu}
	<ul class="modules">
	{foreach $model->menu as $id => $name}
		<li>
			<a href="{$root}/{$model->project}/{$id}" title="{$name}"
				{if $id == $model->request->action}class="selected"{/if}>
				{$name}
			</a>
		</li>
	{/foreach}
	</ul>
{/if}

	<div class="main">
		<ul class="user">
		{if $model->loggedIn === false}
			<li><a href="{$root}/{$model->project}/core/login">Login</a></li>
			<li><a href="{$root}/{$model->project}/core/register">Register</a></li>
		{else}
			<li><a href="{$root}/{$model->project}/core/logout">Logout</a></li>
			<li><a href="{$root}/{$model->project}/core/account">My Account</a></li>
			<li><a href="{$root}/{$model->project}/core/project">The Project</a></li>
		{/if}
			<li><a href="{$root}/{$model->project}/core/about">About</a></li>
		</ul>

		{* @TODO: This check should not be necessary in production *}
		{if $model->module !== null}
			{raw arbit_decorate($model->module)}
		{/if}
		<div class="break"></div>
	</div>

	<div class="footer">
		<p>
			<a href="http://arbitracker.org">Arbit - project tracking</a>, licensed under <a href="http://www.gnu.org/licenses/gpl-3.0.txt">GPL 3</a>
		</p>
	</div>

	{if $model->debugMode}
		{include arbit_get_template( 'html/debug.tpl' )}
	{/if}
</body>
</html>
