<?php
/**
*
* @package Status streams
* @copyright (c) 2013 Max41479
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* Status streams
*/
function streams_online()
{
	global $db;
	set_config('streams_online_last_gc', time(), true);
	$online_streams = array();
	$offline_streams = array();
	
	$sql_array = array(
		'SELECT'	=> 's.stream_channel_name, s.stream_platform_id, s.stream_id',
		'FROM'		=> array(
			STREAMS_TABLE			=> 's',
		),
	);
	$sql = $db->sql_build_query('SELECT', $sql_array);
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result))
	{
		$stream_online = false;
		$stream_platform_id = $row['stream_platform_id'];
		$stream_channel_name = $row['stream_channel_name'];
		switch ($stream_platform_id)
		{
			case '1':
				$stream_online = twitch_checker($stream_channel_name);
				break;
			case '2':
				$stream_online = cybergame_checker($stream_channel_name);
				break;
			case '3':
				$stream_online = goodgame_checker($stream_channel_name);
				break;
		}
		
		if ($stream_online == true)
		{
			$online_streams[] = $row['stream_id'];
		}
		else
		{
			$offline_streams[] = $row['stream_id'];
		}
	}
	
	$sql_array = array(
			'online'	=> '1',
	);
	$sql = 'UPDATE ' . STREAMS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_array) . ' WHERE ' . $db->sql_in_set('stream_id', $online_streams);
	$db->sql_query($sql);
	
	$sql_array = array(
			'online'	=> '0',
	);
	$sql = 'UPDATE ' . STREAMS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_array) . ' WHERE ' . $db->sql_in_set('stream_id', $offline_streams);
	$db->sql_query($sql);

}

function twitch_checker($user_name)
{
	$json_file = file_get_contents("http://api.justin.tv/api/stream/list.json?channel=$user_name");
	$json_array = json_decode($json_file, true);
	$stream_online = false;
	if (empty($json_array))
	{
		$stream_online = false;
	}
	else if (strtolower($json_array[0]['name']) == strtolower("live_user_$user_name")) 
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
	}
	else if (($json_array['online']) == ("1"))
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
	}
	else if (($json_array[$key[0]]['status']) == ("Live"))
	{
		$stream_online = true;
	}
	return $stream_online;
}

?>