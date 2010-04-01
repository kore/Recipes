{use $model, $root = $model->request->root}
{tr_context "recipes"}
<?xml version="1.0"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html 
	xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="{$model->mimetype}; charset={$model->charset}" />

    <link rel="Stylesheet" type="text/css" href="{$root}/styles/screen.css" media="screen" />
    <link rel="Stylesheet" type="text/css" href="{$root}/styles/ui-lightness/jquery-ui-1.8.custom.css" media="screen" />

    <script src="{$root}/scripts/jquery-1.4.2.min.js" type="text/ecmascript"></script>
    <script src="{$root}/scripts/jquery-ui-1.8.custom.min.js" type="text/ecmascript"></script>
    <script src="{$root}/scripts/jquery.protect.js" type="text/ecmascript"></script>
    <script src="{$root}/scripts/jquery.elastic.js" type="text/ecmascript"></script>
    <script src="{$root}/scripts/form.js" type="text/ecmascript"></script>
    <script type="text/ecmascript">
    // <![CDATA[ {literal}
    $( document ).ready( function()
    {
        $( "textarea" ).elastic();
        $( "form.protect" ).protect( '{/literal}{tr "Unsaved changes to the recipe will be lost."}{literal}' );
    } );
    {/literal} // ]]>
    </script>

    <title>{tr "Recipe Database"}</title>
</head>
<body class="dashboard">
	<h1 class="{tr "en" context "locale"}">
		<a href="{$root}/">{tr "Recipe Database"}</a>
	</h1>

	<div class="navigation">
        <ul class="user">
        {if $model->loggedIn}
            <li><a href="{$root}/user/logout">{tr "Logout"}</a></li>
        {else}
            <li><a href="{$root}/user/login">{tr "Login"}</a></li>
        {/if}
        </ul>

        {if $model->loggedIn}
        <ul class="main">
            <li><a href="{$root}/recipes/overview">{tr "Overview"}</a></li>
            <li><a href="{$root}/recipes/tags">{tr "By Tag"}</a></li>
            <li><a href="{$root}/recipes/all">{tr "Index"}</a></li>
            <li><a href="{$root}/recipes/edit">{tr "Add recipe"}</a></li>
            <li><a href="{$root}/recipes/listRecipe">{tr "List"}</a></li>
        </ul>
        {/if}

        <div class="break"></div>
	</div>

	<div class="content">
        {raw arbit_decorate($model->view)}
	</div>

	<div class="footer">
	</div>

	{if $model->debugMode}
		{include arbit_get_template( 'html/debug.tpl' )}
	{/if}
</body>
</html>
