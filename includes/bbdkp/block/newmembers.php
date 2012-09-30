<?php
/**
 * newmembers block
 * 
 * @package bbDkp
 * @copyright 2012 bbdkp <https://github.com/bbDKP>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * 
 */

if (!defined('IN_PHPBB'))
{
   exit;
}
/**  begin newmembers block ***/

$template->assign_var('S_DISPLAY_NEWMEMBERS', true);

$number_of_max_last_members = $config['bbdkp_portal_maxnewmembers'];

$sql = 'SELECT user_id, username, user_regdate, user_colour
	FROM ' . USERS_TABLE . '
	WHERE user_type <> ' . USER_IGNORE . '
	AND user_inactive_time = 0
	ORDER BY user_regdate DESC';
	
$result = $db->sql_query_limit($sql, $number_of_max_last_members);

while( ($row = $db->sql_fetchrow($result)) && ($row['username'] != '') )
{
	$user_colour = ($row['user_colour']) ? ' style="color:#' . $row['user_colour'] .'"' : '';

	$template->assign_block_vars('latest_members', array(
		'USERNAME'		=> censor_text($row['username']),
		'USERNAME_COLOR'=> ($row['user_colour']) ? ' style="color:#' . $row['user_colour'] .'"' : '',
		'U_USERNAME'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $row['user_id']),
		'JOINED'		=> $user->format_date($row['user_regdate'], $format = 'd M'),
		)
	);
}
$db->sql_freeresult($result);

$template->assign_vars(array(
	'S_DISPLAY_NEWMEMBERS' => true, 
		
));

/**  end newmembers block ***/
?>