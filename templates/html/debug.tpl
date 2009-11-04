{var
	$debug      = arbit_get_debug_messages(),
	$severities = array(
		1   => 'Debug',
		2   => 'Success',
		4   => 'Failed',
		8   => 'Info',
		16  => 'Notice',
		32  => 'Warning',
		64  => 'Error',
		128 => 'Fatal',
	)
}
<div class="debug">
	<h2>Debug output</h2>
{if $debug}
	<ul class="debug">
	{foreach $debug as $message}
		<li>
			<h3 class="{str_lower( $severities[$message['severity']] )}">{$severities[$message['severity']]} in {$message['source']} / {$message['category']}</h3>
			<h4>File: {$message['file']} +{$message['line']} {if $message['method']}({$message['method']}){/if}</h4>
			<p>{$message['message']}</p>
		</li>
	{/foreach}
	</ul>
{/if}
</div>
