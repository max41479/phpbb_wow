<?php
/**
 * Recent topics block
 * 
 * @package bbDkp
 * @copyright 2012 bbdkp <https://github.com/bbDKP>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * Contains Code ported from Easyportal (C) 2008 Noxwizard
 * 
 */

if (!defined('IN_PHPBB'))
{
   exit;
}
/**  begin recent topics block ***/

// get authorised forums
$can_read_forum = $auth->acl_getf('f_read');	//Get the forums the user can read from
$forums_auth_ary = array();
foreach($can_read_forum as $key => $forum)
{
    if($forum['f_read'] != 0)
    {
        $forums_auth_ary[] = $key;
    }
}

unset($can_read_forum);
$fetchtopics = array();

$fetchtopics = fetch_topics($forums_auth_ary, $config['bbdkp_portal_rtno'], $config['bbdkp_portal_rtlen']);

if(!empty($fetchtopics))
{
	for ($i = 0; $i < sizeof($fetchtopics); $i++)
	{
		$template->assign_block_vars('recent_topic_row', array(
			'U_TITLE'		=> append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $fetchtopics[$i]['forum_id'] . '&amp;t=' . 
				$fetchtopics[$i]['topic_id'] . '&amp;p=' . $fetchtopics[$i]['post_id'] . '#p' . $fetchtopics[$i]['post_id']),
			'L_TITLE'		=> $fetchtopics[$i]['topic_title'],
			'U_POSTER'		=> $fetchtopics[$i]['user_link'],
			'S_POSTTIME'	=> $fetchtopics[$i]['topic_last_post_time'],
			'POSTED_BY'		=> sprintf($user->lang['POSTED_BY_ON'], $fetchtopics[$i]['user_link'], $fetchtopics[$i]['topic_last_post_time']),
			)
		);
	}
}
else
{
	$template->assign_vars(array(		
		'NO_RECENT'	=> $user->lang['NO_RECENT_TOPICS']
		)
	);
}

	$template->assign_vars(array(		
		'S_DISPLAY_RT' => true, 
		)
	);




/**
* Retrieve a set of topics and trim the names if necessary
*/
function fetch_topics($forum_id_ary, $num_topics, $num_chars)
{
	global $db, $user;

	//No authed forums, or desired number of topics is zero or less
	if(!sizeof($forum_id_ary) || $num_topics < 1)
	{
		return array();
	}

	// Get the latest topics
	$sql = 'SELECT topic_id, topic_title, topic_last_post_id, topic_last_post_time, forum_id, topic_last_poster_id, topic_last_poster_name, topic_last_poster_colour  
	    FROM ' . TOPICS_TABLE . '
	    WHERE topic_type <> ' . POST_GLOBAL . '
	        AND topic_approved = 1
	        AND ' . $db->sql_in_set('forum_id', $forum_id_ary) . '
	    ORDER BY topic_last_post_time DESC';

	$result = $db->sql_query_limit($sql, $num_topics);
	$row = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);

	$topics = array();
	$i = 0;
	foreach($row as $topic)
	{
		// Trim the topic title and add ellipse
		if ($num_chars != 0 and strlen($topic['topic_title']) > $num_chars)
	    {
	        $topic['topic_title'] = substr($topic['topic_title'], 0, $num_chars) . '...';
	    }

		$topics[$i]['forum_id'] = $topic['forum_id'];
		$topics[$i]['post_id'] = $topic['topic_last_post_id'];
		$topics[$i]['topic_id'] = $topic['topic_id'];
		$topics[$i]['topic_title'] = $topic['topic_title'];
		$topics[$i]['topic_last_post_id'] = $topic['topic_last_post_id'];
		$topics[$i]['topic_last_post_time'] = $user->format_date($topic['topic_last_post_time']);
		$topics[$i]['user_link'] = get_username_string('full', $topic['topic_last_poster_id'], $topic['topic_last_poster_name'], $topic['topic_last_poster_colour']);
		$i++;
	}
	
	return $topics;
}
/**  end recent topics block ***/

?>