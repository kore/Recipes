{use $model}
{tr_context "admin/caches"}
{tr "Caches for '%project':" vars "project" => $model->project}

{foreach $model->caches as $cache}
- {$cache}
{/foreach}
