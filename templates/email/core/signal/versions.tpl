{use $model}
Hi,

The version information for the project {$model->project} have been updated.
The following versions are now available:

{foreach $model->versions as $version => $state}
- {$version} {if $state == 0}(inactive){/if}

{/foreach}

Kind regards,
Arbit

