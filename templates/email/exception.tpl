{use $model}
Hello,

An exception occured during the processing of the following request:

- {arbit_env('REQUEST_URI')}

Charset:   {$model->charset}
MimeType:  {$model->mimetype}

The error was:

> {arbit_show( $model->exception )}

Information about the environment, while the error occured:

IP:        {arbit_env('REMOTE_ADDR')}
UserAgent: {arbit_env('HTTP_USER_AGENT')}
Time:      {date_format_timestamp('l, d-M-y H:i:s T')}

