<?php
/**
 * @package bbDKP.module
 * @link http://www.bbdkp.com
 * @author Sajaki@gmail.com
 * @copyright 2009 bbdkp
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 1.2.7
 */

/**
 * @ignore
 */
if ( !defined('IN_PHPBB') OR !defined('IN_BBDKP') )
{
	exit;
}

$user->add_lang(array('posting'));
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

$mode = request_var('mode', '');
$submit = (isset ( $_POST ['post'] )) ? true : false;

$time = time() + $user->timezone + $user->dst - date('Z');
$update = false;

if ($submit)
{
	if (! check_form_key ( 'addnews' ))
	{
		trigger_error ( 'FORM_INVALID' );
	}

	$update = ( isset ( $_GET [URI_NEWS])) ? true : false;  
	
	$text = utf8_normalize_nfc ( request_var ( 'news_message', '', true ) );
	$uid = $bitfield = $options = ''; // will be modified by generate_text_for_storage
	$allow_bbcode = $allow_urls = $allow_smilies = true;
	generate_text_for_storage ( $text, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies );
	if ($update == false)
	{
		//new message
		$sql_ary = array (
			'news_headline' => utf8_normalize_nfc ( request_var ( 'news_headline', '', true ) ), 
			'news_message' => $text, 
			'news_date' => $time, 
			'bbcode_uid' => (string) $uid, 
			'bbcode_bitfield' => (string) $bitfield, 
			'bbcode_options' => (string) $options, 
			'user_id' => $user->data ['user_id'] );
		
		$sql = 'INSERT INTO ' . NEWS_TABLE . ' ' . $db->sql_build_array ( 'INSERT', $sql_ary );
		$db->sql_query ( $sql );
		
		redirect(append_sid ( "{$phpbb_root_path}dkp.$phpEx", 'page=news'));
	} 
	else
	{
		
		$news_id = request_var(URI_NEWS, 0);
		// update
		$query = $db->sql_build_array ( 
		'UPDATE', array (
			'news_headline' => utf8_normalize_nfc ( request_var ( 'news_headline', '', true ) ), 
			'news_message' 	=> $text, 
			'news_date' 	=> $time,
			'bbcode_uid' => (string) $uid, 
			'bbcode_bitfield' => (string) $bitfield, 
			'bbcode_options' => (string) $options, 		
			'user_id' 		=> $user->data ['user_id'] ) );

		$db->sql_query ('UPDATE ' . NEWS_TABLE . ' SET ' . $query . ' WHERE news_id=' . (int) $news_id);
		
		redirect(append_sid ( "{$phpbb_root_path}dkp.$phpEx", 'page=news'));
	}
}

// show the buttons ?
$can_delete = false ;
$can_add = false ;
$can_edit = false ;
	
// go to edit or newpost mode
if ($mode == 'edit' || $mode == 'newpost')
{
	if (isset ( $_GET [URI_NEWS]))
	{
		// get post contents if we have an id
		$news_id = request_var(URI_NEWS,0 ); 
		$sql = 'SELECT n.news_id, n.news_date, n.news_headline, n.news_message, n.bbcode_uid, n.bbcode_bitfield, n.bbcode_options
	        FROM ' . NEWS_TABLE . ' n WHERE n.news_id = ' . (int) $news_id;
		$result = $db->sql_query($sql);
		if (! $row = $db->sql_fetchrow ( $result ))
		{
			trigger_error ( $user->lang ['ERROR_INVALID_NEWS'], E_USER_WARNING );
		}
		
		$message = $row['news_message'];
		decode_message($message, $row['bbcode_uid']);
		
		$template->assign_vars(
		array(
			'S_POST_ACTION'				=> true,
			'ID' 		=> $row ['news_id'],
			'DATE' 		=> date ( 'F j, Y', $row ['news_date']), 
			'HEADLINE' 	=> $row ['news_headline'], 
			'TIME' 		=> date ( 'h:ia T', $row ['news_date']), 
			'MESSAGE' 	=> $message, 
			'U_DELETE' 	=> append_sid ( "{$phpbb_root_path}dkp.$phpEx", 'page=news&amp;mode=delete&amp;'.URI_NEWS.'=' . $row['news_id'] ), 
			'U_EDIT' 	=> append_sid ( "{$phpbb_root_path}dkp.$phpEx", 'page=news&amp;mode=edit&amp;'.URI_NEWS.'=' . $row['news_id']),
		));
		
		$db->sql_freeresult($result);
	}

	$template->assign_vars(array(
		'S_POST_ACTION'				=> true,	
	)
	);
	
	// HTML, BBCode, Smilies, Images and Flash status
	$bbcode_status	= ($config['allow_bbcode']) ? true : false;
	$img_status		= ($bbcode_status) ? true : false;
	$flash_status	= ($bbcode_status && $config['allow_post_flash']) ? true : false;
	$url_status		= ($config['allow_post_links']) ? true : false;
	$smilies_status	= ($bbcode_status && $config['allow_smilies']) ? true : false;
	
	if ($smilies_status)
	{
		$display_link = false;
		$sql = 'SELECT smiley_id
			FROM ' . SMILIES_TABLE . '
			WHERE display_on_posting = 0';
		$result = $db->sql_query_limit($sql, 1, 0, 3600);
		if ($row = $db->sql_fetchrow($result))
		{
			$display_link = true;
		}
		$db->sql_freeresult($result);
		$last_url = '';
	
		$sql = 'SELECT *
			FROM ' . SMILIES_TABLE . '
			WHERE display_on_posting = 1  
			ORDER BY smiley_order';
		$result = $db->sql_query($sql, 3600);
		$smilies = array();
		while ($row = $db->sql_fetchrow($result))
		{
			if (empty($smilies[$row['smiley_url']]))
			{
				$smilies[$row['smiley_url']] = $row;
			}
		}
		$db->sql_freeresult($result);
		if (sizeof($smilies))
		{
			foreach ($smilies as $row)
			{
				$template->assign_block_vars('smiley', array(
					'SMILEY_CODE'	=> $row['code'],
					'A_SMILEY_CODE'	=> addslashes($row['code']),
					'SMILEY_IMG'	=> $phpbb_root_path . $config['smilies_path'] . '/' . $row['smiley_url'],
					'SMILEY_WIDTH'	=> $row['smiley_width'],
					'SMILEY_HEIGHT'	=> $row['smiley_height'],
					'SMILEY_DESC'	=> $row['emotion'])
				);
			}
		}
	}
	
	
	$template->assign_vars(array(
	'BBCODE_STATUS'				=> ($bbcode_status) ? 
		sprintf($user->lang['BBCODE_IS_ON'], '<a href="' . append_sid("{$phpbb_root_path}faq.$phpEx", 'mode=bbcode') . '">', '</a>') : 
		sprintf($user->lang['BBCODE_IS_OFF'], '<a href="' . append_sid("{$phpbb_root_path}faq.$phpEx", 'mode=bbcode') . '">', '</a>'),
	'IMG_STATUS'				=> ($img_status) ? $user->lang['IMAGES_ARE_ON'] : $user->lang['IMAGES_ARE_OFF'],
	'FLASH_STATUS'				=> ($flash_status) ? $user->lang['FLASH_IS_ON'] : $user->lang['FLASH_IS_OFF'],
	'SMILIES_STATUS'			=> ($smilies_status) ? $user->lang['SMILIES_ARE_ON'] : $user->lang['SMILIES_ARE_OFF'],
	'URL_STATUS'				=> ($bbcode_status && $url_status) ? $user->lang['URL_IS_ON'] : $user->lang['URL_IS_OFF'],
	'S_BBCODE_ALLOWED'			=> $bbcode_status,
	'S_SMILIES_ALLOWED'			=> $smilies_status,
	'S_LINKS_ALLOWED'			=> $url_status,
	'S_BBCODE_IMG'				=> $img_status,
	'S_BBCODE_URL'				=> $url_status,
	'S_BBCODE_FLASH'			=> $flash_status,
	'S_BBCODE_QUOTE'			=> true,
		
	));

	

}

elseif (isset ( $_GET [URI_NEWS] ) && $mode=='delete')
{
	//ask permission
	if (confirm_box ( true ))
	{
		$sql = 'DELETE FROM ' . NEWS_TABLE . ' WHERE news_id=' . ( int ) request_var ( URI_NEWS, 0 );
		$db->sql_query ( $sql );
		$success_message = $user->lang ['ADMIN_DELETE_NEWS_SUCCESS'];
		trigger_error ( $success_message );
	} 
	else
	{
		$s_hidden_fields = build_hidden_fields (
		 array (
		 'delete' => true, 
		 'news_id' => (int) request_var ( URI_NEWS, 0 )));
		confirm_box ( false, $user->lang ['CONFIRM_DELETE_NEWS'], $s_hidden_fields );
	}
	redirect(append_sid ( "{$phpbb_root_path}dkp.$phpEx", 'page=news'));
	
}

else 
{
	// show postings
	/* viewnews */
	
	$sql2 = 'SELECT * FROM ' . NEWS_TABLE;
	$total_news = 0;
	$result2 = $db->sql_query ( $sql2 );
	while ( $row = $db->sql_fetchrow ( $result2 ) )
	{
		$total_news ++;
	}
	$db->sql_freeresult ($result2);
	
	$start = request_var ( 'start', 0 );
	$previous_date = null;
	
	$sql = 'SELECT n.news_id, n.news_date, n.news_headline, n.news_message, n.bbcode_uid, n.bbcode_bitfield, n.bbcode_options, u.username
	        FROM ' . NEWS_TABLE . ' n, ' . USERS_TABLE . ' u
	        WHERE (n.user_id = u.user_id)
	        ORDER BY news_date DESC';
	$result = $db->sql_query_limit ( $sql, $config ['bbdkp_user_nlimit'], $start );
	
	while ( $row = $db->sql_fetchrow ( $result ) )
	{
		$message = $row ['news_message'];
		$options = 7;
		$message = generate_text_for_display ( $message, $row ['bbcode_uid'], $row ['bbcode_bitfield'], $options);
		
		$template->assign_block_vars ( 
			'news_row', array (
				'ID' 		=> $row ['news_id'],
				'DATE' 		=> date ( 'F j, Y', $row ['news_date']), 
				'HEADLINE' 	=> censor_text(  $row ['news_headline']), 
				'AUTHOR' 	=> $row ['username'], 
				'TIME' 		=> date ( 'h:ia T', $row ['news_date']), 
				'MESSAGE' 	=> $message, 
				'U_DELETE' 	=> append_sid ( "{$phpbb_root_path}dkp.$phpEx", 'page=news&amp;mode=delete&amp;'.URI_NEWS.'=' . $row['news_id'] ), 
				'U_EDIT' 	=> append_sid ( "{$phpbb_root_path}dkp.$phpEx", 'page=news&amp;mode=edit&amp;'.URI_NEWS.'=' . $row['news_id'] ),
	
		
		));
	}
	$db->sql_freeresult ( $result );
	
	// show the buttons ?
	$can_delete = false ;
	$can_add = false ;
	$can_edit = false ;
	
	if ($auth->acl_get('a_dkp'))
	{
		$can_delete = true ;
		$can_add = true ;
		$can_edit = true ;
	}
	
	
	$template->assign_vars ( array (
		'NEWS_PAGINATION' => generate_pagination ( append_sid ( "{$phpbb_root_path}dkp.$phpEx", 'page=news'), $total_news, $config ['bbdkp_user_nlimit'], $start, true ),  
		'COUNTNEWS'					=> $total_news,
		'U_POST_NEW_TOPIC'			=> append_sid ( "{$phpbb_root_path}dkp.$phpEx", 'page=news&amp;mode=newpost'),
	));
	
}

$form_key = 'addnews';
add_form_key ( $form_key );

// navigation links
$navlinks_array = array (array (
	'DKPPAGE' => $user->lang ['MENU_NEWS'], 
	'U_DKPPAGE' => append_sid ( "{$phpbb_root_path}dkp.$phpEx", 'page=news' ) ) );

foreach ( $navlinks_array as $name )
{
	$template->assign_block_vars ( 
		'dkpnavlinks', array (
			'DKPPAGE' => $name ['DKPPAGE'], 
			'U_DKPPAGE' => $name ['U_DKPPAGE'] ) );
}


$template->assign_vars(array(
	'S_ADD' 					=> ! $update, 
	'S_UPDATE' 					=> $update, 
	'S_DELETE_ALLOWED'			=> $can_delete,
	'S_ADD_ALLOWED'				=> $can_add,
	'S_EDIT_ALLOWED'			=> $can_edit,
	'S_DISPLAY_NEWS' 			=> true
)
);

// Build custom bbcodes array
display_custom_bbcodes();

page_header ( $user->lang ['MENU_NEWS'] );

?>