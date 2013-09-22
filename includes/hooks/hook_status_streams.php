<?php
function status_streams()
{
	global $template, $db;
	
	$streams_online = false;
	// make a listing of all streams
	$sql_array = array(
		'SELECT'	=> 's.*, s.stream_channel_name, s.stream_platform_id, s.associated_thread, s.stream_description, p.stream_platform_name, p.stream_platform_icon, p.stream_platform, s.phpbb_user_id, u.username',
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
		$stream_status = false;
		$stream_platform_id = $row['stream_platform_id'];
		$stream_channel_name = $row['stream_channel_name'];
		switch ($stream_platform_id)
		{
			case '1':
				$stream_status = twitch_checker($stream_channel_name);
				break;
			case '2':
				$stream_status = cybergame_checker($stream_channel_name);
				break;
			case '3':
				$stream_status = goodgame_checker($stream_channel_name);
				break;
		}
		
		if ($stream_status == true)
		{
			$streams_online = true;
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
			'STREAM_STATUS'			=> $stream_status,
			)
		);
	}
	$template->assign_vars(array(
		'STREAMS_ONLINE'	=> $streams_online,
	));
}

function twitch_checker($user_name)
{
	$json_file = file_get_contents("http://api.justin.tv/api/stream/list.json?channel=$user_name");
	$json_array = json_decode($json_file, true);
	$stream_online = false;
	if (empty($json_array))
	{
		$stream_online = false;
	}else if (strtolower($json_array[0]['name']) == strtolower("live_user_$user_name")) 
	{
		$stream_online = true;
	}
	return $stream_online;
}

function cybergame_checker($user_name)
{
	$json_file = file_get_contents("http://api.cybergame.tv/w/streams2.php?channel=$user_name");
	$json_array = json_decode($json_file, true);
	$stream_online = false;
	if (empty($json_array)) 
	{
		$stream_online = false;
	}else if (($json_array['online']) == ("1"))
	{
		$stream_online = true;
	}
	return $stream_online;
}

function goodgame_checker($user_name)
{
	$json_file = file_get_contents("http://goodgame.ru/api/getchannelstatus?fmt=json&id=$user_name");
	$json_array = json_decode($json_file, true);
	$key = array_keys($json_array);
	$stream_online = false;
	if (empty($json_array)) 
	{
		$stream_online = false;
	}else if (($json_array[$key[0]]['status']) == ("Live"))
	{
		$stream_online = true;
	}
	return $stream_online;
}

$phpbb_hook->register(array('template', 'display'), 'status_streams');
?>