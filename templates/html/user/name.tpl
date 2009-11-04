{use $user = null, $ip = null}
{include arbit_get_template( 'html/user/get.tpl' ) send $user, $ip receive $user as $name}
{$name}
