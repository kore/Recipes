{use $model, $root = $model->request->root}
<?xml version="1.0"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html 
	xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="{$model->mimetype}; charset={$model->charset}" />

    <link rel="Stylesheet" type="text/css" href="{$root}/styles/screen.css" media="screen" />
    <script src="{$root}/scripts/form.js" type="text/ecmascript"></script>

    <title>Recipe Manager</title>
</head>
<body class="dashboard">
	<h1>
		<a href="{$root}/">Recipe Manager</a>
	</h1>

	<div class="navigation">
        <ul class="user">
        {if $model->loggedIn}
            <li><a href="{$root}/user/logout">Logout</a></li>
        {else}
            <li><a href="{$root}/user/login">Login</a></li>
        {/if}
        </ul>

        {if $model->loggedIn}
        <ul class="main">
            <li><a href="{$root}/recipes/overview">Overview</a></li>
            <li><a href="{$root}/recipes/tags">By Tag</a></li>
            <li><a href="{$root}/recipes/add">Add recipe</a></li>
        </ul>
        {/if}

        <div class="break"></div>
	</div>

	<div class="main">
        {raw arbit_decorate($model->view)}
	</div>

	<div class="footer">
	</div>

	{if $model->debugMode}
		{include arbit_get_template( 'html/debug.tpl' )}
	{/if}
</body>
</html>
