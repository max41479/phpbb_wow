<?php
function status_streams()
{
	global $template, $db;
	
	$streams_online = false;
	$streams_online_count = 0;
	// make a listing of all streams
	$sql_array = array(
		'SELECT'	=> 's.online, s.stream_platform_id, s.associated_thread, p.stream_platform_name, s.phpbb_user_id',
		'FROM'		=> array(
			STREAMS_TABLE			=> 's',
			STREAM_PLATFORMS_TABLE	=> 'p',
			USERS_TABLE				=> 'u',
		),
		'WHERE'		=> "p.stream_platform_id = s.stream_platform_id AND s.phpbb_user_id = u.user_id",
		'ORDER_BY'	=> "s.phpbb_user_id",
	);
	$sql = $db->sql_build_query('SELECT', $sql_array);
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result))
	{
		$stream_link = '';
		$stream_online = $row['online'];
		$stream_platform_id = $row['stream_platform_id'];
		
		if ($stream_online == true)
		{
			$streams_online = true;
			$streams_online_count++;
		}
	}
	$template->assign_vars(array(
		'STREAMS_ONLINE'	=> $streams_online,
		'STREAMS_ONLINE_COUNT'	=> $streams_online_count,
	));
}

function goodgame_get_stream_id($user_name)
{
	$json_file = file_get_contents("http://goodgame.ru/api/getchannelstatus?fmt=json&id=$user_name");
	$json_array = json_decode($json_file, true);
	$key = array_keys($json_array);
	$goodgame_stream_id = 1;
	if (empty($json_array)) 
	{
		$goodgame_stream_id = 1;
	}
	else
	{
		$goodgame_stream_id = $json_array[$key[0]]['stream_id'];
	}
	
	return $goodgame_stream_id;
}

$phpbb_hook->register(array('template', 'display'), 'status_streams');
?>