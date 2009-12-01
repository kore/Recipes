{use $model, $root = $model->request->root}
{tr_context "recipes"}
<h2>{tr "Add recipe"}</h2>

{include arbit_get_template( 'html/core/errors.tpl' )
	send $model->errors as $errors}

{include arbit_get_template( 'html/core/success.tpl' )
	send $model->success as $success}

<form method="post" action="{$root}/{$model->request->controller}/{$model->request->action}/add"
      onsubmit="return validateForm( this );">
<fieldset>
	<legend>{tr "Add recipe"}</legend>

	<input type="hidden" name="_arbit_form_token" value="{arbit_form_token()}" />

	<label>
		<input type="text" class="required" name="name" />
		{tr "Name"}
	</label>

	<label>
		<textarea name="description" rows="5"></textarea>
		{tr "Short description"}
	</label>

	<label>
		<input type="text" name="amount" class="required number"/>
		{tr "Amount of portions / persons"}
	</label>

<script type="text/ecmascript">
// <![CDATA[ {literal}
var group = 1;
var ingredient = 1;
{/literal} // ]]>
</script>

	<label>
        <ul class="ingredients">
            <li>
                <input type="text" name="ingredients[1][title]" class="title" value="Main"/>
                <ul>
                    <li>
                        <input type="text" name="ingredients[1][1][amount]" class="amount number"/>
                        <input type="text" name="ingredients[1][1][unit]" class="unit number"/>
                        <input type="text" name="ingredients[1][1][ingredient]" class="ingredient"/>
                    </li>
                    <li>
                        <input type="text" name="ingredients[1][2][amount]" class="amount number"/>
                        <input type="text" name="ingredients[1][2][unit]" class="unit number"/>
                        <input type="text" name="ingredients[1][2][ingredient]" class="ingredient"/>
                    </li>
                </ul>
            </li>
        </ul>
		{tr "Ingredients"}
	</label>

	<label>
		<textarea name="instructions" class="required" rows="10"></textarea>
		{tr "Instructions"} (<a href="http://docutils.sourceforge.net/rst.html">{tr "RST markup"}</a>)
	</label>

	<label>
		<input type="text" name="preparation" class="number"/>
		{tr "Preparation time"}
	</label>

	<label>
		<input type="text" name="cooking" class="number"/>
		{tr "Cooking time"}
	</label>

	<label>
		<input type="submit" name="create" value="{tr "Add recipe"}" />
	</label>
</fieldset>
</form>

