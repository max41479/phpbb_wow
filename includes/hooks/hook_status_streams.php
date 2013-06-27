<?php
function status_streams()
{
	global $phpbb_root_path, $phpEx, $template, $user;
	
	function twitch_checker($user)
	{
		$json_file = file_get_contents("http://api.justin.tv/api/stream/list.json?channel=$user");
		$json_array = json_decode($json_file, true);
		if (empty($json_array))
		{
			$stream_online = false;
		}else if (strtolower($json_array[0]['name']) == strtolower("live_user_$user")) 
		{
			$stream_online = true;
		}
		return $stream_online;
	}
	
	function cybergame_checker($user)
	{
		$json_file = file_get_contents("http://api.cybergame.tv/w/streams2.php?channel=$user");
		$json_array = json_decode($json_file, true);
		if (($json_array['online']) == ("1")) 
		{
			$stream_online = true;
		}else
		{
			$stream_online = false;
		}
		return $stream_online;
	}
	
	$template->assign_vars(array(
		'STREAM1'						=> twitch_checker("max41479"),	//max41479
		'STREAM2'						=> cybergame_checker("shakor"),	//shakor
		'STREAM3'						=> twitch_checker("tonyhowk2"),	//Zluchnik
	));
}

$phpbb_hook->register(array('template', 'display'), 'status_streams');
?>