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
				Internal error
			</h2>

			<p>
				An internal unhandled error occured. We are sorry for the inconvenience.
			</p>
		{if $model->debugMode}
            {* Hide exception from request struct to limit excessive dump sizes *}
            {$model->request->variables['exception'] = 'See below for exception trace'}
			<p>
				You may attach <a
					href="data:text/plain;base64,{str_base64_encode( "Request:\n" . var_dump( $model->request ) . "\n\nException:\n" . $model->exception )}">
				this file</a> to a bugreport, which contains all relevant
				information about the given request. Please introspect the
				file manually to insure no sensitive information is disclosed.
			</p>
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
