{use $model}
{var $iterator = array()}
Hi,

The components information for the project {$model->project} have been updated.
The following components are now available:

{$iterator = arbit_recursive_iterator($model->components)}
{foreach $iterator as $component => $childs}
{str_fill(' ', $iterator->depth * 2)}- {$component}
{/foreach}

Kind regards,
Arbit

