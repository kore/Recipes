{use $user = null, $ip = null} {var $name} {*
{if is_array( $user )}
    {if is_set( $user['name'] )}
        {$name = $user['name']}
    {else}
        {$name = $user['login']}
    {/if}
{elseif $user}
    {if $user->name}
        {$name = $user->name}
    {else}
        {$name = $user->login}
    {/if}
{elseif $ip}
    {$name = arbit_tr( "IP: %ip", array( "ip" => $ip ), "modules/tracker" )}
{else}
    {$name = arbit_tr( "Unknown", array(), "modules/tracker" ) }
{/if}

Now the wrapped variant, so no spaces are echoed ... please keep in sync:

*}{if is_array( $user )} {if is_set( $user['name'] )} {$name = $user['name']}
{else} {$name = $user['login']} {/if} {elseif $user} {if $user->name} {$name =
$user->name} {else} {$name = $user->login} {/if} {elseif $ip} {$name =
arbit_tr( "IP: %ip", array( "ip" => $ip ), "modules/tracker" )} {else} {$name =
arbit_tr( "Unknown", array(), "modules/tracker" ) } {/if} {return $name as $user}
