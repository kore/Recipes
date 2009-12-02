{use $model, $root = $model->request->root}
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
    <pre>
        {$model->exception}
    </pre>
{/if}
