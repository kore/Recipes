{use $model, $root = $model->request->root}
{tr_context "core/dashboard"}
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
<body class="dashboard">
	<h1>
		<a href="{$root}/dashboard">Arbit - project tracking</a>
	</h1>

	<div class="main">
		<ul class="dashboard">
		{foreach $model->projects as $project}
			{raw arbit_decorate($project)}
		{/foreach}
		</ul>
		<p class="footnote">
			* {tr "Project quality index - average project quality reported by the used modules."}
		</p>
		<div class="break"></div>
	</div>

	<div class="footer">
		<p>
			<a href="http://arbitracker.org">Arbit - project tracking</a>, {tr "licensed under" context "core"} <a href="http://www.gnu.org/licenses/gpl-3.0.txt">GPL 3</a>
		</p>
	</div>

	{if $model->debugMode}
		{include arbit_get_template( 'html/debug.tpl' )}
	{/if}
</body>
</html>
