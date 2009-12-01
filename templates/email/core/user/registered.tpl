{use $model}
Hello {include arbit_get_template( 'html/user/name.tpl' ) send $model->user as $user}

Thanks for registering at our arbit instance. Please use the following link to
confirm your registration request:

{arbit_url($model->request->controller, 'confirm', $model->user->id . '/' . $model->user->valid)}

If you did not register yourself, here are additional information about the
user who registered with your email address:

IP:        {arbit_env('REMOTE_ADDR')}
UserAgent: {arbit_env('HTTP_USER_AGENT')}
Time:      {date_format_timestamp('l, d-M-y H:i:s T')}

Hope to see you soon.

{return
	'Arbit registration confirmation' as $subject
}
