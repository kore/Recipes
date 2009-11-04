{use $model, $root = $model->request->root}
{cycle $background = array('light', 'dark')}
{var $iterator = array(), $depth = 0, $token = arbit_form_token()}
<h2>{$model->conf->name}</h2>

<p>
	{$model->conf->description}
</p>

<h3>Authentification mechanisms</h3>

<ul>
{foreach $model->conf->auth as $name => $type}
	<li>{$name}</li>
{/foreach}
</ul>

<h3>Administrators</h3>

<ul>
{foreach $model->administrator->users as $user}
	<li>{$user->name}</li>
{/foreach}
</ul>

<h3>Project versions</h3>

{if arbit_may('core_versions_edit')}
<p>
	You should be carefull when removing versions, as this may cause
	inconsistencies.
</p>

<form method="post" action="{$root}/{$model->request->controller}/core/project" class="wide">
<fieldset>
	<legend>Available versions</legend>

	<input type="hidden" name="_arbit_form_token" value="{$token}" />

	<table>
		<thead>
			<tr>
				<th>Version</th>
				<th>State</th>
				<th>Actions</th>
			</tr>
		</thead>
	{foreach $model->project->versions as $version => $state increment $background}
		<tr class="{$background}">
			<td>{$version}</td>
			<td class="form">
				<label>
					<select size="1" name="state[{$version}]">
						<option {if $state == 0}selected="selected"{/if} value="0">Inactive</option>
						<option {if $state == 1}selected="selected"{/if} value="1">Active</option>
					</select>
					State
				</label>
			</td>
			<td class="form">
				<button class="up" type="submit" name="up" value="{$version}" title="Move up" />
				<button class="down" type="submit" name="down" value="{$version}" title="Move down" />
				<button class="delete" type="submit" name="delete" value="{$version}" title="Remove version"
					onclick="return confirm('Really remove version {$version}?');"
				/>
			</td>
		</tr>
	{/foreach}
	</table>

	<label>
		<input type="submit" name="change_version_state" value="Save" />
	</label>
</fieldset>
</form>

<h4>Add version</h4>
<form method="post" action="{$root}/{$model->request->controller}/core/project"
    onsubmit="return validateForm( this );">
<fieldset>
	<legend>Add a version</legend>

	<input type="hidden" name="_arbit_form_token" value="{$token}" />

	<label>
		<input type="text" class="required" name="version" />
		Version
	</label>
	<label>
		<select size="1" name="before">	
		{foreach $model->project->versions as $version => $state}
			<option value="{$version}">{$version}</option>
		{/foreach}
			<option value="-1" selected="selected">After last version</option>
		</select>
		Before version
	</label>

	<label>
		<input type="submit" name="create_version" value="Create" />
	</label>
</fieldset>
</form>
{else}
<ul>
{foreach $model->project->versions as $version => $state}
	<li class="state-{$state}">{$version}</li>
{/foreach}
</ul>
{/if}

<h3>Project components</h3>

{if arbit_may('core_components_edit')}
<p>
	This is the project component tree. The components are just used as tags,
	so no order is implied. You may create child tags / components for each
	component.
</p>

<form method="post" action="{$root}/{$model->request->controller}/core/project" class="wide">
<fieldset>
	<legend>Available components</legend>

	<input type="hidden" name="_arbit_form_token" value="{$token}" />
{/if}

{if array_count($model->project->components) <= 0}
	<p>
		No components available yet.
	</p>
{else}
	{include arbit_get_template('html/core/project/compontents.tpl')
		send $model->project->components as $components}
{/if}

{if arbit_may('core_components_edit')}
</fieldset>
</form>

<h4>Add component</h4>
<form method="post" action="{$root}/{$model->request->controller}/core/project"
    onsubmit="return validateForm( this );">
<fieldset>
	<legend>Add a component</legend>

	<input type="hidden" name="_arbit_form_token" value="{$token}" />

	<label>
		<input type="text" class="required" name="component" />
		Component name
	</label>
	<label>
		<select size="1" name="parent">	
			<option value="0" selected="selected">Root</option>
	{if is_array($model->project->components) &&
	    array_count($model->project->components)}
		{$iterator = arbit_recursive_iterator($model->project->components)}
		{foreach $iterator as $component => $childs}
			<option value="{$component}">{str_fill(' ', $iterator->depth * 2 + 2)}{$component}</option>
		{/foreach}
	{/if}
		</select>
		Parent
	</label>

	<label>
		<input type="submit" name="create_component" value="Create" />
	</label>
</fieldset>
</form>
{/if}

