{use $old, $new, $minimal = false}
{var $diff = arbit_diff($old, $new), $text = '', $last = 0}
{foreach $diff as $token}
	{$text = ''}
	{if is_array($token)}
		{foreach $token as $word}
			{if $word->type !== 1}
				{$text .= ' ' . $word->value}
			{/if}
		{/foreach}
{arbit_wrap($text, '- ')}
	{$text = ''}
		{foreach $token as $word}
			{if $word->type !== $last}
				{if $last === 1}
					{$text .= '"'}
				{elseif $last === 2}
					{$text .= ']'}
				{/if}
			{/if}
			{if $word->type === 1}
				{if $last !== 1}
					{$text .= ' +"' . $word->value}
				{else}
					{$text .= ' ' . $word->value}
				{/if}
			{elseif $word->type === 2}
				{if $last !== 2}
					{$text .= ' [..'}
				{else}
					{$text .= '.'}
				{/if}
			{else}
				{$text .= ' ' . $word->value}
			{/if}
			{$last = $word->type}
		{/foreach}
{arbit_wrap($text, '+ ')}
	{elseif $token->type === 1}
{arbit_wrap($token->value, '+ ')}
	{elseif $token->type === 2}
{arbit_wrap($token->value, '- ')}
	{/if}

{/foreach}
