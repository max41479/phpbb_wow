<?php
function status_streams()
{
	global $template, $db;
	
	$streams_online = false;
	$streams_online_count = 0;
	// make a listing of all streams
	$sql_array = array(
		'SELECT'	=> 's.online, s.stream_channel_name, s.stream_platform_id, s.associated_thread, s.stream_description, p.stream_platform_name, p.stream_platform_icon, p.stream_platform, s.phpbb_user_id, u.username',
		'FROM'		=> array(
			STREAMS_TABLE			=> 's',
			STREAM_PLATFORMS_TABLE	=> 'p',
			USERS_TABLE				=> 'u',
		),
		'WHERE'		=> "p.stream_platform_id = s.stream_platform_id AND s.phpbb_user_id = u.user_id",
		'ORDER_BY'	=> "s.stream_channel_name",
	);
	$sql = $db->sql_build_query('SELECT', $sql_array);
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result))
	{
		$stream_link = '';
		$stream_online = $row['online'];
		$stream_platform_id = $row['stream_platform_id'];
		$stream_channel_name = $row['stream_channel_name'];
		
		if ($stream_online == true)
		{
			$streams_online = true;
			$streams_online_count++;
		}
		
		if ($row['associated_thread'] == '//')
		{
			$stream_link = $row['stream_platform'] . $row['stream_channel_name'];
		}
		else
		{
			$stream_link = $row['associated_thread'];
		}
		$template->assign_block_vars('stream_list_row', array(
			'STREAM_DESCRIPTION'	=> $row['stream_description'],
			'LINK'					=> $stream_link,
			'USERNAME'				=> $row['username'],
			'STREAM_PLATFORM_ICON'	=> $row['stream_platform_icon'],
			'STREAM_STATUS'			=> $stream_online,
			)
		);
	}
	$template->assign_vars(array(
		'STREAMS_ONLINE'	=> $streams_online,
		'STREAMS_ONLINE_COUNT'	=> $streams_online_count,
	));
}

$phpbb_hook->register(array('template', 'display'), 'status_streams');
?>