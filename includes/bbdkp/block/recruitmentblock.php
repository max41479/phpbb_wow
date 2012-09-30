<?php
/* recruitment block
  @package bbDkp
  @copyright 2009 bbdkp <https://github.com/bbDKP>
  @license http://opensource.org/licenses/gpl-license.php GNU Public License
  @author Sajaki, Blazeflack, Malfate
 */

if (! defined('IN_PHPBB'))
{
	exit();
}

/**  begin recruitment block ***/
$color = array(
	array(0 , $user->lang['NA'], "#000000" , "") , 
	array(1 , $user->lang['CLOSED'] , "#AAAAAA" , "rec_closed.png") , 
	array(2 , $user->lang['LOW'] , "#FFBB44" , "rec_low.png") , 
	array(3 , $user->lang['MEDIUM'] ,"#FF3300" ,"rec_med.png") , 
	array(4 ,$user->lang['HIGH'] ,"#AA00AA" ,"rec_high.png"));

if ($config['bbdkp_recruitment'] == 1)
{
	$template->assign_block_vars('status', array(
		'MESSAGE' => $user->lang['RECRUIT_MESSAGE']));
	
	$rec_forum_id = $config['bbdkp_recruit_forumid'];
	
	// get recruitment statuses from class table
	
	$sql_array = array(
		'SELECT' => ' c.class_id, l.name as class_name, c.colorcode, 
	    				  c.imagename, c.dps, c.tank, c.heal ' , 
		'FROM' => array(
			CLASS_TABLE => 'c' , 
			BB_LANGUAGE => 'l') , 
		'WHERE' => " (c.dps !=0 OR c.heal != 0 OR c.tank != 0) 
					AND c.game_id = l.game_id 
					AND c.class_id > 0 AND l.attribute_id = c.class_id  
					AND l.language= '" . $config['bbdkp_lang'] . "' 
					AND l.attribute = 'class' " , 
		'ORDER_BY' => ' l.name ');
			
	$sql = $db->sql_build_query('SELECT', $sql_array);
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		$class[$row['class_id']] = $row['class_name'];
		$template->assign_block_vars('rec', array(
			'CLASSID' => $row['class_id'] , 
			'CLASS' => $row['class_name'] , 
			'IMAGENAME' => $row['imagename'] , 
			'CLASSCOLOR' => $row['colorcode'] , 
			'TANKCOLOR' => $color[$row['tank']][2] , 
			'TANKFORUM' => append_sid("{$phpbb_root_path}viewforum.$phpEx", 'f=' . $rec_forum_id) , 
			'TANKTEXT' => $color[$row['tank']][1] , 
			'TANK' => $color[$row['tank']][3] ,
			'S_TANK' => ((int) $row['tank'] == 0) ? false: true, 
			'DPSCOLOR' => $color[$row['dps']][2] , 
			'DPSFORUM' => append_sid("{$phpbb_root_path}viewforum.$phpEx", 'f=' . $rec_forum_id) , 
			'DPSTEXT' => $color[$row['dps']][1] , 
			'DPS' => $color[$row['dps']][3] , 
			'S_DPS' => ((int) $row['dps'] == 0) ? false: true,
			'HEALCOLOR' => $color[$row['heal']][2] , 
			'HEALFORUM' => append_sid("{$phpbb_root_path}viewforum.$phpEx", 'f=' . $rec_forum_id) , 
			'HEALTEXT' => $color[$row['heal']][1] , 
			'HEAL' => $color[$row['heal']][3],
			'S_HEAL' => ((int)  $row['heal'] == 0) ? false: true,	
		));
	}
	$db->sql_freeresult($result);
}
else
{
	$template->assign_block_vars('status', array(
		'S_DISPLAY_RECRUIT' => true , 
		'MESSAGE' => $user->lang['RECRUIT_CLOSED']));
}
$template->assign_vars(array(
	'S_DISPLAY_RECRUIT' => true));
/**  end recruitment block ***/
?>