{use $request, $term = null}
{tr_context "recipes"}
<form method="get" action="{$request->root}/{$request->controller}/search"
      onsubmit="return validateForm( this );">
<fieldset>
	<legend>{tr "Search recipes"}</legend>

    <label>
        <input type="text" name="search" value="{$term}" />
        {tr "Search"}
    </label>

	<label>
        <input type="submit" value="{tr "Search"}" />
    </label>
</fieldset>
</form>
