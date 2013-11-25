<?php
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
 
// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('common');

$page_title = $user->lang['STREAMS'];
page_header($page_title);
//---------------------------------------
	$streams_online_count = 0;
	// make a listing of all streams
	$sql_array = array(
		'SELECT'	=> 's.*, p.stream_platform_name, p.stream_platform, f.pf_character, f.pf_pbclass',
		'FROM'		=> array(
			STREAMS_TABLE				=> 's',
			STREAM_PLATFORMS_TABLE		=> 'p',
			USERS_TABLE					=> 'u',
			PROFILE_FIELDS_DATA_TABLE	=> 'f',
			//BB_LANGUAGE					=> 'l',
		),
		'WHERE'		=> "p.stream_platform_id = s.stream_platform_id AND s.phpbb_user_id = u.user_id AND f.user_id = u.user_id",
		'ORDER_BY'	=> "s.stream_channel_name",
	);
	$sql = $db->sql_build_query('SELECT', $sql_array);
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result))
	{
		if ($row['pf_pbclass'] === null)
		{
			$class = 0;
		}
		else
		{
			$class = $row['pf_pbclass'] - 1;
		}
		
		switch ($class) {
			case 0:
				$class_name = 'unknown';
			break;
			case 1:
				$class_name = 'warrior';
			break;
			case 2:
				$class_name = 'paladin';
			break;
			case 3:
				$class_name = 'hunter';
			break;
			case 4:
				$class_name = 'rogue';
			break;
			case 5:
				$class_name = 'priest';
			break;
			case 6:
				$class_name = 'death-knight';
			break;
			case 7:
				$class_name = 'shaman';
			break;
			case 8:
				$class_name = 'mage';
			break;
			case 9:
				$class_name = 'warlock';
			break;
			case 10:
				$class_name = 'monk';
			break;
			case 11:
				$class_name = 'druid';
			break;
		}
		$character = $row['pf_character'];
		$stream_status = $row['online'];
		$stream_id = $row['stream_id'];
		$stream_platform_id = $row['stream_platform_id'];
		$stream_channel_name = $row['stream_channel_name'];
		
		if ($stream_status == true)
		{
			$stream_status_message = $user->lang['ONLINE'];
			$stream_status_img = 'on.png';
		}
		else
		{
			$stream_status_message = $user->lang['OFFLINE'];
			$stream_status_img = 'off.png';
		}
		$template->assign_block_vars('stream_list_row', array(
			'STREAM_DESCRIPTION'	=> $row['stream_description'],
			//'USERNAME'				=> $row['username'],
			//'STREAM_PLATFORM_ICON'	=> $row['stream_platform_icon'],
			'STREAM_STATUS'			=> $stream_status_message,
			'STREAM_ID'				=> $stream_id,
			'CHARACTER'				=> $character,
			'STREAM_STATUS_IMG'		=> $stream_status_img,
			'CLASS'					=> $class,
			'CLASS_NAME'			=> $class_name,
			
			)
		);
	}
	if (!isset($_GET['stream_id']))
	{
		$twitch = 0;
		$cybergame = 0;
		$goodgame = 0;
		$streamer_name = '';
		$channel = '';
	}
	else
	{
		$stream_id = $_GET['stream_id'];
		$sql_arr = array(
			'SELECT'	=> 's.*, f.pf_character',
			'FROM'		=> array(
				STREAMS_TABLE		=> 's',
				PROFILE_FIELDS_DATA_TABLE	=> 'f'
				//GROUPS_TABLE    => 'g'
				),
			'WHERE'		=> 's.stream_id = ' . $stream_id . ' AND f.user_id = s.phpbb_user_id',
		);
		$sql = $db->sql_build_query('SELECT', $sql_arr);
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		
		$stream_platform_id = $row['stream_platform_id'];
		switch ($stream_platform_id) {
			case 1:
				$twitch = 1;
				$cybergame = 0;
				$goodgame = 0;
			break;
			case 2:
				$twitch = 0;
				$cybergame = 1;
				$goodgame = 0;
			break;
			case 3:
				$twitch = 0;
				$cybergame = 0;
				$goodgame = 1;
			break;
		}
		$channel = $row['stream_channel_name'];
		$streamer_name = $row['pf_character'];
	}
	
	
	
	$goodgame_stream_id = goodgame_get_stream_id($channel);
	//var_dump($goodgame_stream_id);
	$title = $user->lang['STREAMS'];
	$template->assign_vars(array(
		'STREAMER_NAME'			=> $streamer_name,
		'TITLE'					=> $title,
		'TWITCH'				=> $twitch,
		'CYBERGAME'				=> $cybergame,
		'GOODGAME'				=> $goodgame,
		'GOODGAME_STREAM_ID'	=> $goodgame_stream_id,
		'CHANNEL'				=> $channel,
	));




//--------------------------------------

$template->set_filenames(array(
    'body' => 'streams_body.html',
));
 
make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));
page_footer();
?>