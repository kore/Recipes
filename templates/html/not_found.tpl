{use $model, $root = $model->request->root}
<h2>
    Not found
</h2>

<p>
    The URL you requested does not exist on this server. Maybe you mistyped its
    adress or the ressource has been removed.
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
