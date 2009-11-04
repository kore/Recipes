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

	<title>Arbit - project tracking</title>
</head>
<body class="exception">
	<h1>
		<a href="{$root}/dashboard">Arbit - project tracking</a>
	</h1>

	<div class="main">
		<div class="content">
			<h2>
				Not found
			</h2>

			<p>
				The URL you requested does not exist on this server. Maybe you
				mistyped its adress or the ressource has been removed.
			</p>
		{if $model->debugMode}
			<p>
				The request, which caused the error is:
			</p>
				{raw arbit_dump($model->request)}
			<p>
				The exception, which caused the error is:
			</p>
			<code>
				{$model->exception}
			</code>
		{/if}
		</div>
	</div>

	<div class="footer">
		<a href="http://arbitracker.org">Arbit - project tracking</a>, licensed under <a href="http://www.gnu.org/licenses/gpl-3.0.txt">GPL 3</a>
	</div>

	{if $model->debugMode}
		{include arbit_get_template( 'html/debug.tpl' )}
	{/if}
</body>
</html>
