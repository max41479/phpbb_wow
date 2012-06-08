<?php
/**
 * News block
 * 
 * @package bbDkp
 * @copyright 2009 bbdkp <https://github.com/bbDKP>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * 
 */

if (!defined('IN_PHPBB'))
{
   exit;
}

if (!function_exists('generate_text_for_display')) 
{
	include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
}

include($phpbb_root_path . 'includes/bbcode.' . $phpEx);
define('LOAD_REIMG', true); 
define('REIMG_POST_ROW', 'news_row.MESSAGE');
// Container for user details, only process once
$post_list = $attachments = $attach_list = $rowset = $update_count = $post_edit_list = array();
$has_attachments = $display_notice = false;

// load viewtopic and dkp language array
$user->add_lang(array('viewtopic', 'mods/dkp_common'));

// forum to get news from
$forum_id = isset($config['bbdkp_news_forumid']) ? $config['bbdkp_news_forumid'] : 2;
// total newsitems to retrieve from forum
$n_news = isset($config['bbdkp_n_news']) ? $config['bbdkp_n_news'] : 5;

// retrieve news from this page (generated from pagination function and retrieved from $_GET)
$start = request_var('start', 0); 
$bbcode_bitfield = $force_encoding = '';

//get forumname
$sql = 'SELECT forum_name FROM ' . FORUMS_TABLE . ' where forum_id = ' . $forum_id;
$result = $db->sql_query($sql);
$forumname = $db->sql_fetchfield('forum_name');                 
$db->sql_freeresult($result);

// calculate total number of articles to retrieve using phpbb constants
$sql = 'SELECT count(*) as newscount FROM ' . TOPICS_TABLE . '
		WHERE forum_id = ' . $forum_id . ' 
        AND topic_status <> ' . ITEM_MOVED . '
        AND topic_approved = 1';
$result = $db->sql_query_limit($sql, $n_news);
$totalnews = (int) $db->sql_fetchfield('newscount'); 
$db->sql_freeresult($result);

$l_edited_by = ''; 
$edit_reason = '' ; 
// retrieve $newsperpage topics starting from page $start
$newsperpage = $n_news;
$previous_date = null;
$sql = 'SELECT * FROM ' . TOPICS_TABLE . '
		WHERE forum_id = ' . $forum_id . ' 
        AND topic_status <> ' . ITEM_MOVED . '
        AND topic_approved = 1
        ORDER BY topic_time DESC';
$result = $db->sql_query_limit($sql, $newsperpage, $start);
// retrieve posts belonging to the topics
while ( $news = $db->sql_fetchrow($result) )
{

	$template->assign_block_vars('date_row', array(
		'DATE' => date('F j, Y', $news['topic_time'])
	));
     
	$sql = 'SELECT * FROM ' . POSTS_TABLE . ' n
			WHERE topic_id =  '. $news["topic_id"] .' 
			AND post_time =  '. $news["topic_time"] .' ';
	$result2 = $db->sql_query($sql);
	while ( $row = $db->sql_fetchrow($result2) )
    {
     	
		$topic = $row['post_text'];
		$bbcode_options   = (($row['enable_bbcode']) ? OPTION_FLAG_BBCODE : 0) + 
							(($row['enable_smilies']) ? OPTION_FLAG_SMILIES : 0) + 
							(($row['enable_magic_url']) ? OPTION_FLAG_LINKS : 0);
		$message      = generate_text_for_display($row['post_text'], $row['bbcode_uid'], $row['bbcode_bitfield'], $bbcode_options);
        $message      = smiley_text($message);
        
		// Pull attachment data 
		$attachments = array();
		if( $config['allow_attachments'] && $row['post_id'] )
		{
			// Pull attachment data
			$sql = 'SELECT *
				FROM ' . ATTACHMENTS_TABLE . '
				WHERE post_msg_id = '. $row['post_id'] .'
				AND in_message = 0
				ORDER BY filetime DESC';
			$result3 = $db->sql_query($sql);
			while ($row3 = $db->sql_fetchrow($result3))
			{
				$attachments[] = $row3;
			}
			$db->sql_freeresult($result3);
		}
		
		$img = (!empty($attachments) && $config['allow_attachments']) ? $user->img('icon_topic_attach', $user->lang['TOTAL_ATTACHMENTS']) : ''; 
		 
    	if (!empty($attachments))
		{
			parse_attachments($forum_id, $message, $attachments, $update_count);
		}
        
        // check config if we want edits to show in portal
        if ($config['bbdkp_portal_showedits'])
        {
        	// reinitialise var for next loop
			$edit_reason = '';
        	// build the 'user edited' comment
		    if (($row['post_edit_count'] && $config['display_last_edited']) || $row['post_edit_reason'])
			{
				// Get username that edited post, if it has been edited
				if ($row['post_edit_reason'] or $row['post_edit_user'])
				{
					$sql = 'SELECT DISTINCT u.user_id, u.username, u.user_colour
						FROM ' . POSTS_TABLE . ' p, ' . USERS_TABLE . ' u
						WHERE p.post_id = ' . $row['post_id'] . '
							AND p.post_edit_count <> 0
							AND p.post_edit_user <> 0
							AND p.post_edit_user = u.user_id';
					$result4 = $db->sql_query($sql);
					while ($user_edit_row = $db->sql_fetchrow($result4))
					{
						$userid_edit = $user_edit_row['user_id'];
						$username_edit = $user_edit_row['username'];
						$usercolour_edit = $user_edit_row['user_colour'];
					}
					$db->sql_freeresult($result4);
				}
				
				// get the number of edits
				$l_edit_time_total = ($row['post_edit_count'] == 1) ? $user->lang['EDITED_TIME_TOTAL'] :  $user->lang['EDITED_TIMES_TOTAL'];
				
				// if there was a reason then fetch it
				if ($row['post_edit_reason'])
				{
					$edit_reason = $row['post_edit_reason']; 
					
					// User having edited the post also being the post author?
					if (!$row['post_edit_user'] || $row['post_edit_user'] == $row['poster_id'])
					{
						$display_username = get_username_string('full', $row['poster_id'], $username_edit, $usercolour_edit, $row['post_username']);
					}
					else
					{
						$display_username = get_username_string('full', $row['post_edit_user'], $post_edit_list[$row['post_edit_user']]['username'], $post_edit_list[$row['post_edit_user']]['user_colour']);
					}
		
					$l_edited_by = sprintf($l_edit_time_total, $display_username, $user->format_date($row['post_edit_time'], false, true), $row['post_edit_count']);
				}
				else
				{
					//no reason
					if ($row['post_edit_user'] && !isset($user_cache[$row['post_edit_user']]))
					{
						$user_cache[$row['post_edit_user']] = $post_edit_list[$row['post_edit_user']];
					}
		
					// User having edited the post also being the post author?
					if (!$row['post_edit_user'] || $row['post_edit_user'] == $row['poster_id'])
					{
						$display_username = get_username_string('full', $row['poster_id'], $username_edit, $usercolour_edit, $row['post_username']);
					}
					else
					{
						$display_username = get_username_string('full', $row['post_edit_user'], $user_cache[$row['post_edit_user']]['username'], $user_cache[$row['post_edit_user']]['user_colour']);
					}
		
					$l_edited_by = sprintf($l_edit_time_total, $display_username, $user->format_date($row['post_edit_time'], false, true), $row['post_edit_count']);
				} 
			}
			else
			{
				$l_edited_by = '';
			}
        }
        
    }
    $db->sql_freeresult($result2);
    
	$template->assign_block_vars('news_row', array( 
		'DATE' => date('F j, Y', $news['topic_time']),
		'HEADLINE' 	=> censor_text($news['topic_title']), 
		'AUTHOR' 	=> get_username_string('full', $news['topic_poster'], $news['topic_first_poster_name'], $news['topic_first_poster_colour']), 
		'LINK'		=> append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $news['forum_id'] . '&amp;t=' . $news['topic_id']), 
		'TIME' 		=> $user->format_date($news['topic_time']),
		'VIEWS'		=> $news['topic_views'], 
		
		'MESSAGE' 	=> $message,
		'MORE' 		=> $news['topic_replies_real'],
		'U_VIEWFORUM' => append_sid("{$phpbb_root_path}viewforum.$phpEx", 'f=' . $news['forum_id']),  
		'EDITED_MESSAGE'	=> $l_edited_by,
		'EDIT_REASON'		=> $edit_reason,
		'ATTACH_ICON_IMG'	=> $img,
		'S_HAS_ATTACHMENTS'	=> (!empty($attachments)) ? true : false,
		
	));
	
	// Display parsed Attachments for this post
	if (!empty($attachments[$row['post_id']]))
	{
		foreach ($attachments[$row['post_id']] as $attachment)
		{
			$template->assign_block_vars('news_row.attachment', array(
				'DISPLAY_ATTACHMENT'	=> $attachment)
			);
		}
	}
	
	// Update topic view and if necessary attachment view counters but only for humans and if this is the first 'page view'
	if (isset($user->data['session_page']) && !$user->data['is_bot'] &&
		 (strpos($user->data['session_page'], '&t=' . $news['topic_id']) === false || isset($user->data['session_created'])))
	{
		// Update the attachment download counts
		if (sizeof($update_count))
		{
			$sql = 'UPDATE ' . ATTACHMENTS_TABLE . '
				SET download_count = download_count + 1
				WHERE ' . $db->sql_in_set('attach_id', array_unique($update_count));
			$db->sql_query($sql);
		}
	}
	
}
$db->sql_freeresult($result);
// call pagination function
$pagination = generate_pagination(append_sid("{$phpbb_root_path}portal.$phpEx"), $totalnews, $newsperpage, $start, true); 

// insert template vars
$template->assign_vars(
	array(
	'INFORUMNAME' => $forumname, 
	'TOTALNEWS' => sprintf($user->lang['LISTNEWS_FOOTCOUNT'], $totalnews,$newsperpage),  
	'NEWS_PAGINATION' => $pagination, 

));


?>