{use $old, $new, $minimal = false}
{var $diff = arbit_diff($old, $new, 'line'), $text = '', $last = 0}
{foreach $diff as $token}
	{if $token->type === 1}
{arbit_wrap($token->value, '+ ')}
	{elseif $token->type === 2}
{arbit_wrap($token->value, '- ')}
	{/if}
{/foreach}
