<?php
/**
* Application form created by Kapli (bbDKP developer)
* 
* @package bbDKP
* @copyright (c) 2009 bbDkp https://github.com/bbDKP/Apply
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @author Kapli, Malfate, Sajaki, Blazeflack, Twizted
* @version 1.3.6
*/


// do not change below this line
/**
* @ignore
*/ 
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/message_parser.' . $phpEx);

// set apply template id from $_GET: 
$template_id = request_var('template_id', 1);

// Start session management
$user->session_begin();
$auth->acl($user->data);

$error = array();
$current_time = $user->time_now; 

$user->setup(array('posting', 'mcp', 'viewtopic', 'mods/apply', 'mods/dkp_common', 'mods/dkp_admin'), false);

$form_key = 'make_apply';

// declare captcha class
if (!class_exists('phpbb_captcha_factory'))
{
	include($phpbb_root_path . 'includes/captcha/captcha_factory.' . $phpEx);
}

// make captcha object
$captcha =& phpbb_captcha_factory::get_instance($config['captcha_plugin']);

// if "enable visual confirmation for guest postings" is set to "ON"
// and the user is not registered then set up captcha  
if ($config['enable_post_confirm'] && !$user->data['is_registered'])  
{
	$captcha->init(CONFIRM_POST);
}

//check if visitor can access the form
$post_data = check_apply_form_access($template_id);

//request variables
$submit	= (isset($_POST['post'])) ? true : false;

if ($submit)
{
	// anon user and "enable visual confirmation for guest postings" is set to "ON" and post mode ?
	if ($config['enable_post_confirm'] && in_array('post', array('post')) && !$user->data['is_registered'] )
	{
		// first validate captcha 
		$vc_response = $captcha->validate();
		if ($vc_response)
		{
			$error[] = $vc_response;
		}
	}
	
	if (!check_form_key($form_key))
	{
		$error[] = $user->lang['FORM_INVALID'];
	}
	
	//check if user forgot to enter a required field other than those covered with js
	$sql = "SELECT * FROM " . APPTEMPLATE_TABLE . " where mandatory = 'True' ORDER BY qorder   ";
	$result = $db->sql_query_limit($sql, 100, 1);
	while ( $row = $db->sql_fetchrow($result))
	{
		if ($row['type']=='Checkboxes')
		{
			if ( request_var('templatefield_' .$row['qorder'],  array('' => '')) == '') 
			{
				$error[] = $user->lang['APPLY_REQUIRED'];
			}
		}
		else 
		{
			if ( request_var('templatefield_' . $row['qorder'], '') == '') 
			{
				// return user to index
				$error[] = $user->lang['APPLY_REQUIRED'];
			}
		
		}
		
	}
	$db->sql_freeresult($result);

	$candidate_name = utf8_normalize_nfc(request_var('candidate_name', ' ', true));
	
	// check for validate name. name can only be alphanumeric without spaces or special characters
	// this is to keep gibberish out of our dkpmember database
	//if this preg_match returns true then there is something other than letters
   if (preg_match('/[^a-zA-Zа-яёàäåâÅÂçÇéèëËêÊïÏîÎíÍìÌæŒæÆÅóòÓÒöÖôÔøØüÜ\s]+/iu', $candidate_name  ))
   {
	  $error[] = $user->lang['APPLY_ERROR_NAME']. $candidate_name . ' ';  
   }
	 
	if (!sizeof($error))
	{
		// continue to posting
		make_apply_posting($post_data, $current_time, $candidate_name, $template_id);
	}
	
}

fill_application_form($form_key, $post_data, $submit, $error, $captcha, $template_id);

/**
 * post application on forum
 *
 */
function make_apply_posting($post_data, $current_time, $candidate_name, $template_id)
{
	global $auth, $config, $db, $user, $phpbb_root_path, $phpEx;
	
	$board_url = generate_board_url() . '/';
	
	switch ($config['bbdkp_apply_gchoice'])
	{
		case '1':
			// add to template defined guild
			$sql = "SELECT guild_id from " . APPTEMPLATELIST_TABLE . " WHERE template_id  = " . $template_id;
			$result = $db->sql_query($sql);	
			$candidate_guild_id = $db->sql_fetchfield('guild_id');
			$db->sql_freeresult($result);
			
			$sql = "SELECT max(rank_id) as rank_id from " . MEMBER_RANKS_TABLE . " WHERE rank_id < 90 and guild_id = " . $candidate_guild_id;
			$result = $db->sql_query($sql);	
			$candidate_rank_id = max((int) $db->sql_fetchfield('rank_id'), 0);
			$db->sql_freeresult($result);
			break;
		case '0':
			// do not add to guild
		default:
			$candidate_guild_id = 0;
			$candidate_rank_id = 99;
			break;
	}
	
    $candidate_realm = utf8_normalize_nfc(request_var('candidate_realm', $config['bbdkp_apply_realm'], true)); 
	$candidate_level = utf8_normalize_nfc(request_var('candidate_level', ' ', true));
	$candidate_armory_link = utf8_normalize_nfc(request_var('candidate_armory_link', ' ', true));
	$candidate_spec = utf8_normalize_nfc(request_var('candidate_spec', ' ', true));
	$candidate_game = request_var('game_id', '');
	$candidate_genderid = request_var('candidate_gender', 0);
	$candidate_raceid = request_var('candidate_race_id', 0);
	
	//character class
	$sql_array = array(
		'SELECT'	=>	' r.race_id, r.image_female, r.image_male, l.name as race_name ', 	 
		'FROM'		=> array(
				RACE_TABLE		=> 'r',
				BB_LANGUAGE		=> 'l', 
				),
		'WHERE'		=> " l.game_id = r.game_id AND r.race_id = '". $candidate_raceid ."' AND r.game_id = '" . $candidate_game . "' 
		AND l.attribute_id = r.race_id  AND l.language= '" . $config['bbdkp_lang'] . "' AND l.attribute = 'race' ",					 
		);
	$sql = $db->sql_build_query('SELECT', $sql_array);		
	$result = $db->sql_query($sql);	
	$row = $db->sql_fetchrow($result);
	if(isset($row))
	{
		$race_name = $row['race_name']; 
		$race_image = (string) (($candidate_genderid == 0) ? $row['image_male'] : $row['image_female']); 
		$race_image = (strlen($race_image) > 1) ? $board_url . "images/race_images/" . $race_image . ".png" : ''; 
		$race_image_exists = (strlen($race_image) > 1) ? true : false;
	}
	unset($row);
	$db->sql_freeresult($result);
	
	$candidate_classid = request_var('candidate_class_id', 0);
	
	//character class
	$sql_array = array(
		'SELECT'	=>	' c.class_armor_type AS armor_type , c.colorcode, c.imagename,  c.class_id, l.name as class_name ', 	 
		'FROM'		=> array(
				CLASS_TABLE		=> 'c',
				BB_LANGUAGE		=> 'l', 
				),
		'WHERE'		=> " l.game_id = c.game_id AND c.class_id = '". $candidate_classid ."' AND c.game_id = '" . $candidate_game . "' 
		AND l.attribute_id = c.class_id  AND l.language= '" . $config['bbdkp_lang'] . "' AND l.attribute = 'class' ",					 
		);
	$sql = $db->sql_build_query('SELECT', $sql_array);		
	$result = $db->sql_query($sql);	
	$row = $db->sql_fetchrow($result);
	if(isset($row))
	{
		$class_name =	$row['class_name']; 
		$class_color =  (strlen($row['colorcode']) > 1) ? $row['colorcode'] : '';
		$class_color_exists =  (strlen($row['colorcode']) > 1) ?  true : false;
		$class_image = 	strlen($row['imagename']) > 1 ? $board_url . "images/roster_classes/" . $row['imagename'] . ".png" : '';
		$class_image_exists =    (strlen($row['imagename']) > 1) ? true : false;
	}
	unset($row);
	$db->sql_freeresult($result);
	
	$candidate_realmid = request_var('candidate_realm_id', 0);
	//character realm
	$sql_array = array(
		'SELECT'	=>	're.realm_id, re.realm_name', 	 
		'FROM'		=> array(
				REALM_TABLE		=> 're',
				),
		'WHERE'		=> "re.game_id = '" . $candidate_game . "' 
						AND re.realm_id = '". $candidate_realmid ."'
						AND re.realm_lang = '" . $config['bbdkp_lang'] . "'",
		);
	$sql = $db->sql_build_query('SELECT', $sql_array);		
	$result = $db->sql_query($sql);	
	$row = $db->sql_fetchrow($result);
	if(isset($row))
	{
		$candidate_realm = $row['realm_name']; 
	}
	unset($row);
	$db->sql_freeresult($result);
	
	// if user belongs to group that can add a character then attempt to register a dkp character
	// guests should never be able to register characters (i.e user anonymous)
	if($auth->acl_get('u_dkp_charadd') )
	{
		if(!class_exists('dkp_character'))
		{
			include($phpbb_root_path . 'includes/bbdkp/apply/dkp_character.' . $phpEx);
		}
		$candidate = new dkp_character();
		$candidate->guild = $candidate_guild_id;
		$candidate->guildrank = $candidate_rank_id;
		$candidate->name = $candidate_name;
		$candidate->level = $candidate_level;
		$candidate->realm = $candidate_realm;
		$candidate->game = $candidate_game;
		$candidate->genderid = $candidate_genderid;
		$candidate->raceid = $candidate_raceid;
		$candidate->classid = $candidate_classid;
		register_bbdkp($candidate);
	}
		
	// build post
	$apply_post = '';
	
	$apply_post .= '[size=150][b]' .$user->lang['APPLY_CHAR_OVERVIEW'] . '[/b][/size]'; 
	$apply_post .= '<br /><br />';
	
	// name
	$apply_post .= '[color='. $config['bbdkp_apply_pqcolor'] .']' . $user->lang['APPLY_NAME'] . '[/color]';
	if($class_color_exists)
	{
		$apply_post .= '[shadow=black][b][color='. $class_color .']' . $candidate_name . '[/color][/b][/shadow]' ;
	}
	else
	{
		$apply_post .= '[b]' . $candidate_name . '[/b]' ;
	}
	if($class_image_exists )
	{
		$apply_post .= '[imgltr=right]' .$class_image . '[/imgltr] ';
	}
	$apply_post .= '<br />'; 

	//Realm
	$apply_post .= '[color='. $config['bbdkp_apply_pqcolor'] .']' . $user->lang['APPLY_REALM1'] . '[/color]' . '[color='. $config['bbdkp_apply_pacolor'] .']' . $candidate_realm . '[/color]' ;
	$apply_post .= '<br />'; 

	// level
	/*$apply_post .= '[color='. $config['bbdkp_apply_pqcolor'] .']' . $user->lang['APPLY_LEVEL'] . '[/color]' . '[color='. $config['bbdkp_apply_pacolor'] .']' . $candidate_level. '[/color]' ;
	$apply_post .= '<br />'; 
	
	// class
	$apply_post .= '[color='. $config['bbdkp_apply_pqcolor'] .']' . $user->lang['APPLY_CLASS'] . '[/color] ';
	if($class_image_exists )
	{
		$apply_post .= '[img]' .$class_image . '[/img] ';
	}
	if($class_color_exists)
	{
		$apply_post .= ' [color='. $class_color .']' . $class_name . '[/color]' ;
	}
	else
	{
		$apply_post .= $class_name;
	}
	$apply_post .= '<br />'; 

	//race
	$apply_post .= '[color='. $config['bbdkp_apply_pqcolor'] .']' . $user->lang['APPLY_RACE'] . '[/color] ';
	if($race_image_exists )
	{
		$apply_post .= '[img]' .$race_image . '[/img] ';
	}
	if($class_color_exists)
	{
		$apply_post .= ' [color='. $class_color .']' . $race_name . '[/color]' ;
	}
	else
	{
		$apply_post .= $race_name;
	}
	$apply_post .= '<br />';*/
	
	// armory link
	$apply_post .= '[color='. $config['bbdkp_apply_pqcolor'] .']' . $user->lang['APPLY_ARMORY_LINK'] . '[/color]' . '[color='. $config['bbdkp_apply_pacolor'] .'][url=' . $candidate_armory_link. ']Armory link[/url][/color]' ;
	$apply_post .= '<br />'; 

	//spec
	$apply_post .= '[color='. $config['bbdkp_apply_pqcolor'] .']' . $user->lang['APPLY_SPEC'] . '[/color]' . '[color='. $config['bbdkp_apply_pacolor'] .']' . $candidate_spec . '[/color]' ;
	$apply_post .= '<br /><br />';
	
	// Motivation	
	$apply_post .= '[size=150][b]' .$user->lang['APPLY_CHAR_MOTIVATION'] . '[/b][/size]';
	$apply_post .= '<br /><br />';
	
	// complete with formatted questions and answers
	$sql = "SELECT * FROM " . APPTEMPLATE_TABLE . ' WHERE template_id = ' . $template_id .'  ORDER BY qorder' ;
	$result = $db->sql_query_limit($sql, 100, 0);
	while ( $row = $db->sql_fetchrow($result) )
	{
		if ( isset($_POST['templatefield_' . $row['qorder']]) )
		{
			
			switch ($row['type'])
			{
					
				case 'Checkboxes':
					 $cb_countis = count( request_var('templatefield_' . $row['qorder'], array(0 => 0)) );  
                     $cb_count = 0;
						                                           
                        $apply_post .= '[size=120][color='. $config['bbdkp_apply_pqcolor'] .'][b]' . $row['question'] . ': [/b][/color][/size]';
						$apply_post .= '<br />';
                        
                        $checkboxes = utf8_normalize_nfc( request_var('templatefield_' . $row['qorder'], array(0 => '') , true));
                        foreach($checkboxes as $value) 
                        {
                            $apply_post .= $value;
                            if ($cb_count < $cb_countis-1)
                            {
                                $apply_post .= ',  ';
                            }
                            $cb_count++;
                        }
                        $apply_post .= '<br /><br />';                         
					
					break;
				case 'Inputbox':
				case 'Textbox':
				case 'Textboxbbcode':					
				case 'Selectbox':					
				case 'Radiobuttons':			
					$fieldcontents = utf8_normalize_nfc(request_var('templatefield_' . $row['qorder'], ' ', true));	
						
					$apply_post .= '[size=120][color='. $config['bbdkp_apply_pqcolor'] .'][b]' . $row['question'] . ': [/b][/color][/size]';
					$apply_post .= '<br />';
					 
					$apply_post .=	$fieldcontents;
					
					$apply_post .= '<br /><br />'; 
					break;
					
					
			}

		}
	}
	$db->sql_freeresult($result);
	
	// variables to hold the parameters for submit_post
	$poll = $uid = $bitfield = $options = ''; 
	
	// parsed code
	generate_text_for_storage($apply_post, $uid, $bitfield, $options, true, true, true);

	// subject & username

	//$post_data['post_subject'] = utf8_normalize_nfc(request_var('headline', $user->data['username'], true));
	$post_data['post_subject']	= $candidate_name . " - "  . $class_name . " - ". $candidate_spec . " - ". $candidate_realm;
	$post_data['username']	= $user->data['username'];
	
	// Store message, sync counters
	
		$data = array( 
		'forum_id'			=> (int) $post_data['forum_id'],
		'topic_first_post_id'	=> 0,
		'topic_last_post_id'	=> 0,
		'topic_attachment'		=> 0,		
		'icon_id'			=> false,
		'enable_bbcode'		=> true,
		'enable_smilies'	=> true,
		'enable_urls'		=> true,
		'enable_sig'		=> true,
		'message'			=> $apply_post,
		'message_md5'		=> md5($apply_post),
		'bbcode_bitfield'	=> $bitfield,
		'bbcode_uid'		=> $uid,
		'post_edit_locked'	=> 0,
		'topic_title'		=> $post_data['post_subject'],
		'notify_set'		=> false,
		'notify'			=> false,
		'post_time' 		=> $current_time,
		'poster_ip'			=> $user->ip,
		'forum_name'		=> '',
		'post_edit_locked'	=> 1,
		'enable_indexing'	=> true,
		'post_approved'        => 1,
		);
		
		
		//submit post
		$post_url = submit_post('post', $post_data['post_subject'], $post_data['username'], POST_NORMAL, $poll, $data);
		
		$redirect_url = $post_url;
			
		if ($config['enable_post_confirm'] && (isset($captcha) && $captcha->is_solved() === true))
		{
			$captcha->reset();
		}
		
		//redirect to post
		meta_refresh(3, $redirect_url);

		$message = 'POST_STORED';
		$message = $user->lang[$message] . '<br /><br />' . sprintf($user->lang['VIEW_MESSAGE'], '<a href="' . $redirect_url . '">', '</a>');
		$message .= '<br /><br />' . sprintf($user->lang['RETURN_FORUM'], '<a href="' . append_sid("{$phpbb_root_path}viewforum.$phpEx", 'f=' . $data['forum_id']) . '">', '</a>');
		trigger_error($message);

}

/**
 * registers a bbDKP character 
 *
 * @param dkp_character $candidate
 */
function register_bbdkp(dkp_character $candidate)
{
	global $db, $auth, $user, $config, $phpbb_root_path, $phpEx;
	
	// check if user exceeded allowed character count, to prevent alt spamming
	$sql = 'SELECT count(*) as charcount
			FROM ' . MEMBER_LIST_TABLE . '	
			WHERE phpbb_user_id = ' . (int) $user->data['user_id'];
	$result = $db->sql_query($sql);
	$countc = $db->sql_fetchfield('charcount');
	$db->sql_freeresult($result);
	if ($countc >= $config['bbdkp_maxchars'])
	{
		//do nothing
		return;
	}
	
	// check if membername exists
	$sql = 'SELECT count(*) as memberexists 
			FROM ' . MEMBER_LIST_TABLE . "	
			WHERE ucase(member_name)= ucase('" . $db->sql_escape($candidate->name) . "')"; 
	$result = $db->sql_query($sql);
	$countm = $db->sql_fetchfield('memberexists');
	$db->sql_freeresult($result);
	if ($countm != 0)
	{
		// give a nice alert and stop right here.
		 trigger_error($user->lang['ERROR_MEMBEREXIST'], E_USER_WARNING);
	}
	
	$member_comment = 'candidate'; 
	
	// add the char
	if (! class_exists ( 'acp_dkp_mm' ))
	{
		include ($phpbb_root_path . 'includes/acp/acp_dkp_mm.' . $phpEx);
	}
	$acp_dkp_mm = new acp_dkp_mm ( );
		
	$member_id = $acp_dkp_mm->insertnewmember(
		$candidate->name,
		 1,
		$candidate->level,
		$candidate->raceid,
		$candidate->classid,
		$candidate->guildrank,
		$member_comment, 
		time(), 
		0, 
		$candidate->guild, 
		$candidate->genderid, 
		0, 
		' ',
		' ', 
		$candidate->realm, 
		$candidate->game, 
		$user->data['user_id']
	);
	
	return $member_id;
	
}

/**
 *  build Application form 
 *
 */
function fill_application_form($form_key, $post_data, $submit, $error, $captcha, $template_id)
{
	global $user, $template, $config, $phpbb_root_path, $phpEx, $auth, $db;
	
	// Page title & action URL, include session_id for security purpose
	$s_action = append_sid("{$phpbb_root_path}apply.$phpEx", "", true, $user->session_id);
	
	$page_title = $user->lang['APPLY_MENU'];

	// get WELCOME_MSG
	$sql = 'SELECT announcement_msg, bbcode_uid, bbcode_bitfield, bbcode_options FROM ' . APPHEADER_TABLE;
	$db->sql_query($sql);
	$result = $db->sql_query($sql);
	while ( $row = $db->sql_fetchrow($result) )
	{
		$welcome_message = $row['announcement_msg'];
		$bbcode_uid = $row['bbcode_uid'];
		$bbcode_bitfield = $row['bbcode_bitfield'];
		$bbcode_options = $row['bbcode_options'];
	}
	$welcome_message = generate_text_for_display($welcome_message, $bbcode_uid, $bbcode_bitfield, $bbcode_options);
	$db->sql_freeresult($result);
		
	if ($config['enable_post_confirm'] && !$user->data['is_registered'] ) 
    {
    	if ((!$submit || !$captcha->is_solved()) )
    	{
	        // ... display the CAPTCHA
	        $template->assign_vars(array(
	            'S_CONFIRM_CODE'                => true,
	            'CAPTCHA_TEMPLATE'              => $captcha->get_template(),
	        ));
    	}
    }
	
	$s_hidden_fields =array(); 
	// Add the confirm id/code pair to the hidden fields, else an error is displayed on next submit/preview
	if (isset($captcha))
	{
		if ($captcha->is_solved() !== false)
		{
			$s_hidden_fields .= build_hidden_fields($captcha->get_hidden_fields());
		}
	}
	
	// get list of possible games */ 
	if (!class_exists('bbDKP_Admin'))
	{
		require("{$phpbb_root_path}includes/bbdkp/bbdkp.$phpEx");
	}
	$bbdkp = new bbDKP_Admin();
	$installed_games = array();
	$i=0;
	foreach($bbdkp->games as $gameid => $gamename)
	{
		if ($config['bbdkp_games_' . $gameid] == 1)
		{
			$installed_games[$gameid] = $gamename;
			
			if($i==0) $gamepreset =  $gameid;	
			$i+=1;
			
			$template->assign_block_vars('game_row', array(
				'VALUE' => $gameid,
				'SELECTED' => ((isset($member['game_id']) ? $member['game_id'] : '') == $gameid ) ? ' selected="selected"' : '',
				'OPTION'   => $gamename, 
			));
		}
			
	}
     
	// Race dropdown
	// reloading is done from ajax to prevent redraw
	$sql_array = array(
	'SELECT'	=>	'  r.race_id, l.name as race_name ', 
	'FROM'		=> array(
			RACE_TABLE		=> 'r',
			BB_LANGUAGE		=> 'l',
				),
	'WHERE'		=> " r.race_id = l.attribute_id 
					AND r.game_id = '" . $gamepreset . "' 
					AND l.attribute='race' 
					AND l.game_id = r.game_id 
					AND l.language= '" . $config['bbdkp_lang'] ."'",
	);
	$sql = $db->sql_build_query('SELECT', $sql_array);
	$result = $db->sql_query($sql);
	while ( $row = $db->sql_fetchrow($result) )
	{
		$template->assign_block_vars('race_row', array(
		'VALUE' => $row['race_id'],
		'SELECTED' =>  '',
		'OPTION'   => ( !empty($row['race_name']) ) ? $row['race_name'] : '(None)')
		);
	}

	// Class dropdown
	// reloading is done from ajax to prevent redraw
	$sql_array = array(
		'SELECT'	=>	' c.class_id, l.name as class_name, c.class_hide,
						  c.class_min_level, class_max_level, c.class_armor_type , c.imagename, c.colorcode ', 
		'FROM'		=> array(
			CLASS_TABLE		=> 'c',
			BB_LANGUAGE		=> 'l', 
			),
		'WHERE'		=> " l.game_id = c.game_id  AND c.game_id = '" . $gamepreset . "' 
		AND l.attribute_id = c.class_id  AND l.language= '" . $config['bbdkp_lang'] . "' AND l.attribute = 'class' ",					 
	);
	
	$sql = $db->sql_build_query('SELECT', $sql_array);					
	$result = $db->sql_query($sql);
	while ( $row = $db->sql_fetchrow($result) )
	{
		$option = ( !empty($row['class_name']) ) ? $row['class_name'] : '(None)';		
		$template->assign_block_vars('class_row', array(
		'COLORCODE' => $row['colorcode'],
		'VALUE' => $row['class_id'],
		'SELECTED' => '',
		'OPTION'   => $option ));
		
	}
	
	// Realm dropdown
	// reloading is done from ajax to prevent redraw
	$sql_array = array(
		'SELECT'	=>	're.realm_id, re.realm_name',
		'FROM'		=>	array(
							REALM_TABLE		=> 're',
						),
		'WHERE'		=> "re.game_id = '" . $gamepreset . "' 
						AND re.realm_lang = '" . $config['bbdkp_lang'] . "'",
	);
	$sql = $db->sql_build_query('SELECT', $sql_array);
	$result = $db->sql_query($sql);
	while ( $row = $db->sql_fetchrow($result) )
	{
		$template->assign_block_vars('realm_row', array(
		'VALUE' => $row['realm_id'],
		'SELECTED' => '',
		'OPTION'   => ( !empty($row['realm_name']) ) ? $row['realm_name'] : '(None)'));
		
	}
	$db->sql_freeresult($result);
             	
	// Start assigning vars for main posting page ...
	// main questionnaire 
	$sql = "SELECT a.id, a.qorder, a.header, a.question, a.category, a.type, a.mandatory, a.options, a.template_id, a.lineid, a.defaultt, b.template_name, b.forum_id 
		FROM " . APPTEMPLATE_TABLE . ' a, ' . 
			APPTEMPLATELIST_TABLE . ' b 
			WHERE a.template_id = b.template_id 
			AND a.template_id = ' . $template_id . '
			ORDER BY a.qorder ASC ';
	$result = $db->sql_query($sql);
					
	while ( $row = $db->sql_fetchrow($result) )
	{
		$template->assign_block_vars('apptemplate', array(
				'QORDER'			=> $row['qorder'],
				'S_MANDATORY'		=> ($row['mandatory'] =='True') ? true:false ,
				'TITLE'				=> $row['header'],
				'QUESTION'			=> $row['question'],
				'TYPE'   			=> $row['type'],
				'FORUM_ID'			=> $row['forum_id'], 
				'DOMNAME'			=> 'templatefield_' . $row['qorder'],
				'CATEGORY'			=> $row['category'],
				'TABINDEX'			=> $row['qorder'],
				'DEFAULTT'			=> $row['defaultt']
				)
		);
		
		switch($row['type'])
		{
			case 'Selectbox':
			         $select_option = explode(',', $row['options']);
			         foreach($select_option as  $key =>  $value) 
			         {
			         	$template->assign_block_vars('apptemplate.selectboxoptions', array(
		         			'KEY'		=> $value,
		         			'VALUE'		=> $value,
			         	));
			         }           
				break;
			case 'Radiobuttons':
				$radio_option = explode(',', $row['options']);
				foreach($radio_option as $key => $value)
				{
					$template->assign_block_vars('apptemplate.radiobuttonoptions', array(
							'KEY'		=> $value,
							'VALUE'		=> $value,
					));
				}
				break;
			case 'Checkboxes':
				$check_option = explode(',', $row['options']);
				foreach($check_option as  $key => $value)
				{
					$template->assign_block_vars('apptemplate.checkboxoptions', array(
							'KEY'		=> $value,
							'VALUE'		=> $value,
					));
				}
				break;
		}
	}
	$db->sql_freeresult($result);
	
	$form_enctype = (@ini_get('file_uploads') == '0' || strtolower(@ini_get('file_uploads')) == 'off' || !$config['allow_attachments'] || !$auth->acl_get('u_attach') || !$auth->acl_get('f_attach', $post_data['forum_id'])) ? '' : ' enctype="multipart/form-data"';
	add_form_key($form_key);
	
	// assign global template vars to questionnaire
	$template->assign_vars(array(
		'WELCOME_MSG'			=> $welcome_message,	
		'MALE_CHECKED'			=> ' checked="checked"',
		'L_POST_A'				=> $page_title,
		'ERROR'					=> (sizeof($error)) ? implode('<br />', $error) : '',
		'S_POST_ACTION'     	=> $s_action,
		'S_HIDDEN_FIELDS'   	=> $s_hidden_fields,
		'APPLY_REALM'			=> str_replace("+", " ", $config['bbdkp_apply_realm']), 
		'FORMQCOLOR'			=> $config['bbdkp_apply_fqcolor'], 
		'S_FORM_ENCTYPE'		=> $form_enctype,
		// javascript
		'LA_ALERT_AJAX'		  => $user->lang['ALERT_AJAX'],
		'LA_ALERT_OLDBROWSER' => $user->lang['ALERT_OLDBROWSER'],
		'LA_MSG_NAME_EMPTY'	  => $user->lang['APPLY_REQUIRED_NAME'],
		'LA_MSG_LEVEL_EMPTY'  => $user->lang['APPLY_REQUIRED_LEVEL'],	
		)
	);
		
	// Output application form
	page_header($page_title);
	
	$template->set_filenames(array(
		'body' => 'dkp/application.html')
	);
	
	page_footer();
	
	
}
	
/**
 * check form access before even posting. 
 *
 * @return array $post_data
 */
function check_apply_form_access($template_id)
{
	global $auth, $db, $config, $user;		
	
	$user->add_lang(array('posting'));
	
	$sql = 'SELECT a.* FROM ' . FORUMS_TABLE . ' a, ' . APPTEMPLATELIST_TABLE .' b 
				WHERE a.forum_id = b.forum_id 
				AND b.template_id = ' . $template_id;
	$result = $db->sql_query($sql);
	$post_data = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	// Check permissions
	if ($user->data['is_bot'])
	{
		redirect(append_sid("{$phpbb_root_path}index.$phpEx"));
	}
		
	//set up style vars
	$user->setup(false, $post_data['forum_style']);	
	
	// check authorisations
	$is_authed = false;
	// user has posting permission to the forum ?  
	if ($auth->acl_get('f_post', $post_data['forum_id']))
	{
		//user is authorised for the forum
		$is_authed = true;
	}
	else
	{
		//user has no posting rights in the requested forum (template lang from mcp)
		if ($user->data['is_registered'])
		{
			trigger_error('USER_CANNOT_POST');
		}
		
		//it's a guest and theres no guest access for the forum so ask for a valid login
		login_box('', $user->lang['LOGIN_EXPLAIN_POST']);
	}
	
	// even if guest user has posting rights, we still want to check in our config 
	// if he actually may use the application
	if ($config['bbdkp_apply_guests'] == 'False' && !$user->data['is_registered'])
	{
		$is_authed = false;
	}
	
	// Is the user able to post within this forum? (i.e it's a category)
	if ($post_data['forum_type'] != FORUM_POST)
	{
		trigger_error('USER_CANNOT_FORUM_POST');
	}
	
	// is Forum locked ?
	if (($post_data['forum_status'] == ITEM_LOCKED || (isset($post_data['topic_status']) && $post_data['topic_status'] == ITEM_LOCKED)) && !$auth->acl_get('m_edit', $forum_id))
	{
		trigger_error(($post_data['forum_status'] == ITEM_LOCKED) ? 'FORUM_LOCKED' : 'TOPIC_LOCKED');
	}
	
	return $post_data;
		
	
}

/**
 * sends a personal message with the contents of the form
 */
function pm_sendform($message, $user_id = 2, $sender_id = 2)
{
	global $user, $config;
	global $phpEx, $phpbb_root_path;

	include_once($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
	include_once($phpbb_root_path . 'includes/message_parser.' . $phpEx);
	$sender = $this->get_user_info($sender_id);
	
	$message_parser = new parse_message(); 
  
	$data=array();
	$messenger->template('raidplan_delete', $row['user_lang']);
	$subject =  '[' . $user->lang['RAIDPLANNER']  . '] ' . 
	$user->lang['DELRAID'] . ': ' . $this->eventlist->events[$this->event_type]['event_name'] . ' ' . 
	$user->format_date($this->start_time, $config['rp_date_time_format'], true);
	 
	$userids = array($this->poster);
	$rlname = array();
	user_get_id_name($userids, $rlname);
	 
	$messenger->assign_vars(array(
			'RAIDLEADER'		=> $rlname[$this->poster],
			'USERNAME'			=> htmlspecialchars_decode($row['username']),
			'EVENT_SUBJECT'		=> $subject,
			'EVENT'				=> $this->eventlist->events[$this->event_type]['event_name'],
			'INVITE_TIME'		=> $user->format_date($this->invite_time, $config['rp_date_time_format'], true),
			'START_TIME'		=> $user->format_date($this->start_time, $config['rp_date_time_format'], true),
			'END_TIME'			=> $user->format_date($this->end_time, $config['rp_date_time_format'], true),
			'TZ'				=> $user->lang['tz'][(int) $user->data['user_timezone']],
			'U_RAIDPLAN'		=> generate_board_url() . "/dkp.$phpEx?page=planner&amp;view=raidplan&amp;raidplanid=".$this->id
	));
		
	$messenger->msg = trim($messenger->tpl_obj->assign_display('body'));
	$messenger->msg = str_replace("\r\n", "\n", $messenger->msg);
		
	$messenger->msg = utf8_normalize_nfc($messenger->msg);
	$uid = $bitfield = $options = ''; // will be modified by generate_text_for_storage
	$allow_bbcode = $allow_smilies = $allow_urls = true;
	generate_text_for_storage($messenger->msg, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
	$messenger->msg = generate_text_for_display($messenger->msg, $uid, $bitfield, $options);

	$data = array(
			'address_list'      => array('u' => array($row['user_id'] => 'to')),
			'from_user_id'      => $user->data['user_id'],
			'from_username'     => $user->data['username'],
			'icon_id'           => 0,
			'from_user_ip'      => $user->data['user_ip'],

			'enable_bbcode'     => true,
			'enable_smilies'    => true,
			'enable_urls'       => true,
			'enable_sig'        => true,
				
			'message'           => $messenger->msg,
			'bbcode_bitfield'   => $this->bbcode['bitfield'],
			'bbcode_uid'        => $this->bbcode['uid'],
	);
		
	if($config['rp_pm_rpchange'] == 1 &&  (int) $row['user_allow_pm'] == 1)
	{
		// send a PM
		submit_pm('post',$subject, $data, false);
	}
	

}


