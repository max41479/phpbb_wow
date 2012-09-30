<?php
/**
 * @package bbDKP.module
 * @link http://www.bbdkp.com
 * @author Sajaki@gmail.com
 * @copyright 2009 bbdkp
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 1.2.8
 */

/**
 * @ignore
 */
if ( !defined('IN_PHPBB') OR !defined('IN_BBDKP') )
{
	exit;
}


if ( !isset($_GET[URI_RAID]) )
{
	trigger_error ($user->lang['MNOTFOUND']);
}
$raid_id = request_var(URI_RAID,0); 

/********************************
 * page info
 ********************************/	
$navlinks_array = array(
	array(
	 'DKPPAGE'		=> $user->lang['MENU_RAIDS'],
	 'U_DKPPAGE'	=> append_sid("{$phpbb_root_path}dkp.$phpEx", '&amp;page=listraids'),
	),
	array(
	 'DKPPAGE'		=> $user->lang['MENU_VIEWRAID'],
	 'U_DKPPAGE'	=> append_sid("{$phpbb_root_path}dkp.$phpEx", '&amp;page=listraids&amp;' . URI_RAID . '=' . $raid_id),
	),
);

foreach($navlinks_array as $name)
{
	$template->assign_block_vars('dkpnavlinks', array(
		'DKPPAGE' => $name['DKPPAGE'],
		'U_DKPPAGE' => $name['U_DKPPAGE'],
	));
}
	
/********************************
 * Right Raid information block
 ********************************/ 

/*** get general raid info  ***/
$sql_array = array (
	'SELECT' => ' d.dkpsys_name, e.event_dkpid, e.event_id, e.event_name, e.event_value, e.event_imagename, 
				  r.raid_id, r.raid_start, r.raid_end, r.raid_note, 
				  r.raid_added_by, r.raid_updated_by ', 
	'FROM' => array (
		DKPSYS_TABLE 		=> 'd' ,
		RAIDS_TABLE 		=> 'r' , 
		EVENTS_TABLE 		=> 'e',
		), 
	'WHERE' => " d.dkpsys_id = e.event_dkpid and r.event_id = e.event_id and r.raid_id=" . ( int ) $raid_id, 
);

$sql = $db->sql_build_query('SELECT', $sql_array);
$result = $db->sql_query ($sql);
while ( $row = $db->sql_fetchrow ( $result ) ) 
{
	$raid = array (
		'dkpsys_name' 		=> $row['dkpsys_name'],
		'event_dkpid' 		=> $row['event_dkpid'],
		'event_id' 			=> $row['event_id'], 
		'event_name' 		=> $row['event_name'], 
		'event_value' 		=> $row['event_value'],
		'event_imagename' 	=> $row['event_imagename'],
		'raid_start' 		=> $row['raid_start'],
		'raid_end' 			=> $row['raid_end'], 
		'raid_note' 		=> $row['raid_note'], 
		'raid_added_by' 	=> $row['raid_added_by'], 
		'raid_updated_by' 	=> $row['raid_updated_by'] );
}
$db->sql_freeresult ($result);
		
$sql = $db->sql_build_query('SELECT', $sql_array);
if ( !($raid_result = $db->sql_query($sql)) )
{
	trigger_error ($user->lang['MNOTFOUND']);
}
	
if ( !$raid = $db->sql_fetchrow($raid_result) )
{
	trigger_error ($user->lang['MNOTFOUND']);
}
$db->sql_freeresult($raid_result);

$dkpid = (int) $raid['event_dkpid'];

// Calculate the difference in hours between the 2 timestamps
$hours = intval(($raid['raid_end'] - $raid['raid_start'])/3600) ;
// add hours to duration
$duration = str_pad($hours, 2, "0", STR_PAD_LEFT). ":"; 
// get number of minutes
$minutes = intval(($raid['raid_end'] - $raid['raid_start'] / 60) % 60); 
// add minutes
$duration .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ":";
// get seconds past minute
$seconds = intval( ($raid['raid_end'] - $raid['raid_start']) % 60);
// add seconds to duration
$duration .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

$raid_id = (int) $raid['raid_id']; 
$title =  sprintf($user->lang['RAID_ON'], $raid['event_name'], date('F j, Y', $raid['raid_start']));

$template->assign_vars(array(
	'L_RAID_ON' 		  => sprintf($user->lang['RAID_ON'], $raid['event_name'], date('F j, Y', $raid['raid_start'])),
	'RAIDSTART' 		  => date('H:i:s', $raid['raid_start']),
	'RAIDEND' 		  	  => (!empty($raid['raid_end']) ) ? date('H:i:s', $raid['raid_end']): ' '  ,
	'DURATION' 		  	  => $duration, 
	'RAID_ADDED_BY'		  => sprintf($user->lang['ADDED_BY'], 	(!empty($raid['raid_added_by']) ) ? $raid['raid_added_by'] : 'N/A'),
	'RAID_UPDATED_BY'	  => ($raid['raid_updated_by'] != ' ') ? sprintf ( $user->lang ['UPDATED_BY'], $raid['raid_updated_by']) : ' ',  
	'RAID_NOTE'			  => ( !empty($raid['raid_note']) ) ? $raid['raid_note'] : '&nbsp;',
	'IMAGEPATH' 			=> $phpbb_root_path . "images/event_images/" . $raid['event_imagename'] . ".png", 
    'S_EVENT_IMAGE_EXISTS' 	=> (strlen($raid['event_imagename']) > 1) ? true : false, 
	'S_SHOWZS' 			=> ($config['bbdkp_zerosum'] == '1') ? true : false, 
	'S_SHOWTIME' 		=> ($config['bbdkp_timebased'] == '1') ? true : false,
	'S_SHOWDECAY' 		=> ($config['bbdkp_decay'] == '1') ? true : false,
	'S_SHOWEPGP' 		=> ($config['bbdkp_epgp'] == '1') ? true : false,
	'F_RAID'			=> append_sid("{$phpbb_root_path}dkp.$phpEx" , 'page=viewraid&amp;'. URI_RAID . '=' . request_var(URI_RAID, 0))
));

/**********************************************
 * point listing
 **********************************************/ 

$sort_order = array (
		0 => array ('member_name asc', 'member_name desc' ),
		1 => array ('raid_value asc', 'raid_value desc' ), 
		2 => array ('time_bonus asc', 'time_bonus desc' ), 
		3 => array ('zerosum_bonus asc', 'zerosum_bonus desc' ),
		4 => array ('raid_decay asc', 'raid_decay desc' ),
		5 => array ('total asc', 'total desc' ),
);
$current_order = switch_order ($sort_order);	
$sql_array = array(
	'SELECT'    => 'm.member_id ,m.member_name, c.colorcode, c.imagename, l.name, c.class_id, 
					m.member_gender_id, a.image_female, a.image_male, 
					r.raid_value, r.time_bonus, r.zerosum_bonus, 
					r.raid_decay, (r.raid_value + r.time_bonus + r.zerosum_bonus - r.raid_decay) as total  ',
	'FROM'      => array(
    		    MEMBER_LIST_TABLE 	=> 'm',
    		    RACE_TABLE  		=> 'a',
        		RAID_DETAIL_TABLE   => 'r',
        		CLASS_TABLE 		=> 'c',
				BB_LANGUAGE 		=> 'l', 
    			),
 
	'WHERE'     =>  " c.game_id = m.game_id AND c.class_id = m.member_class_id 
			AND c.class_id = l.attribute_id and c.game_id = l.game_id AND l.attribute='class' 
			AND m.member_race_id =  a.race_id and m.game_id = a.game_id 
			AND l.language= '" . $config['bbdkp_lang'] ."'  
			AND m.member_id = r.member_id and r.raid_id = " . (int) $raid_id  , 
	'ORDER_BY' 	=>  $current_order ['sql'],
);
$sql = $db->sql_build_query('SELECT', $sql_array);
$result = $db->sql_query ( $sql );
$raid_details = array ();
while ( $row = $db->sql_fetchrow ( $result ) ) 
{
	$raid_details[] = array(
		'member_id' => $row['member_id'],
		'colorcode' => $row['colorcode'],
		'imagename' => $row['imagename'],
		'classname' => $row['name'],
		'class_id' 	=> $row['class_id'],
		'raceimage' => (string) (($row['member_gender_id']==0) ? $row['image_male'] : $row['image_female']),
		'member_name' => $row['member_name'],
		'raid_value' => $row['raid_value'],
		'time_bonus' => $row['time_bonus'],
		'zerosum_bonus' => $row['zerosum_bonus'],
		'raid_decay' => $row['raid_decay'],
	);
}
$db->sql_freeresult( $result );
$raid['raid_detail'] = $raid_details;

$raid_value = 0.00;
$time_bonus = 0.00;
$zerosum_bonus = 0.00;
$raid_decay = 0.00;
$raid_total = 0.00;
$countattendees = 0;

		
foreach($raid_details as  $raid_detail)
{
	// fill attendees table
	$template->assign_block_vars ('raids_row', array (
		'U_VIEW_ATTENDEE' => append_sid ("{$phpbb_root_path}dkp.$phpEx" , 'page=viewmember&amp;' . URI_NAMEID . "={$raid_detail['member_id']}&amp;" . URI_DKPSYS. "=" . $raid['event_dkpid']), 
		'NAME' 		 => $raid_detail['member_name'], 
		'COLORCODE'  => ($raid_detail['colorcode'] == '') ? '#123456' : $raid_detail['colorcode'],
        'CLASS_IMAGE' 	=> (strlen($raid_detail['imagename']) > 1) ? $phpbb_root_path . "images/class_images/" . $raid_detail['imagename'] . ".png" : '',  
		'S_CLASS_IMAGE_EXISTS' => (strlen($raid_detail['imagename']) > 1) ? true : false,
       	'RACE_IMAGE' 	=> (strlen($raid_detail['raceimage']) > 1) ? $phpbb_root_path . "images/race_images/" . $raid_detail['raceimage'] . ".png" : '',  
		'S_RACE_IMAGE_EXISTS' => (strlen($raid_detail['raceimage']) > 1) ? true : false, 			 				
		'CLASS_NAME' => $raid_detail['classname'],  
		'RAIDVALUE'  => $raid_detail['raid_value'], 
		'TIMEVALUE'  => $raid_detail['time_bonus'],
		'ZSVALUE' 	 => $raid_detail['zerosum_bonus'],
		'DECAYVALUE' => $raid_detail['raid_decay'], 
		'TOTAL' 	 => $raid_detail['raid_value'] + $raid_detail['time_bonus']  + $raid_detail['zerosum_bonus'] - $raid_detail['raid_decay'], 
		)
	);
	$raid_value += $raid_detail['raid_value'];
	$time_bonus += $raid_detail['time_bonus'];
	$zerosum_bonus += $raid_detail['zerosum_bonus'];
	$raid_decay += $raid_detail['raid_decay'];
	
	$countattendees += 1;
}
$raid_total = $raid_value + $time_bonus + $zerosum_bonus - $raid_decay;

// count blocks
$blocksize = 7;
$x = ceil(count($raid_details) / $blocksize);
//loop blocks
for ( $i = 0; $i < $x; $i++ )
{
	$block_vars = array();
	//loop columns
	for ( $j = 0; $j < $blocksize; $j++ )
	{
		$offset = $i + $x * $j;
		$attendee = ( isset($raid_details[$offset]) ) ? $raid_details[$offset] : '';
		if ( $attendee != '' )
		{
			$block_vars += array(
			  'COLUMN'.$j.'_NAME' => '<strong><a style="color: '. $raid_details[$offset]['colorcode'].';" href="' . append_sid("{$phpbb_root_path}dkp.$phpEx", "page=viewmember&amp;" . URI_NAMEID . '=' . 
			$raid_details[$offset]['member_id'] . '&amp;' . URI_DKPSYS . '=' . $dkpid) . '">' . $raid_details[$offset]['member_name'] . '</a></strong>'
			);
		}
		else
		{
			$block_vars += array(
				'COLUMN'.$j.'_NAME' => ''
			);
		}
		// Are we showing this column?
		$s_column = 's_column'.$j;
		${$s_column} = true;
	}
	$template->assign_block_vars('attendees_row', $block_vars);
}
$column_width = floor(100 / $blocksize);

$template->assign_vars(array(
	// attendees			
	'O_NAME' 		=> $current_order ['uri'] [0], 
	'O_RAIDVALUE' 	=> $current_order ['uri'] [1],
	'O_TIMEVALUE' 	=> $current_order ['uri'] [2],
	'O_ZSVALUE' 	=> $current_order ['uri'] [3],
	'O_DECAYVALUE' 	=> $current_order ['uri'] [4],
	'O_TOTALVALUE' 	=> $current_order ['uri'] [5], 
	'RAIDVALUE'		=> $raid_value, 
	'TIMEVALUE'		=> $time_bonus, 
	'ZSVALUE'		=> $zerosum_bonus, 
	'RAIDDECAY'		=> $raid_decay, 
	'TOTAL'			=> $raid_total, 
	'S_COLUMN0' => ( isset($s_column0) ) ? true : false,
	'S_COLUMN1' => ( isset($s_column1) ) ? true : false,
	'S_COLUMN2' => ( isset($s_column2) ) ? true : false,
	'S_COLUMN3' => ( isset($s_column3) ) ? true : false,
	'S_COLUMN4' => ( isset($s_column4) ) ? true : false,
	'S_COLUMN5' => ( isset($s_column5) ) ? true : false,
	'S_COLUMN6' => ( isset($s_column6) ) ? true : false,
	'S_COLUMN7' => ( isset($s_column7) ) ? true : false,
	'S_COLUMN8' => ( isset($s_column8) ) ? true : false,
	'S_COLUMN9' => ( isset($s_column9) ) ? true : false,
	'COLUMN_WIDTH' => ( isset($column_width) ) ? $column_width : 0,
	'COLSPAN'	   => $blocksize,
	'ATTENDEES_FOOTCOUNT' => sprintf($user->lang['VIEWRAID_ATTENDEES_FOOTCOUNT'], $countattendees),
));

/*********************************
*	Drops block
**********************************/

//prepare item list sql
$isort_order = array (
	0 => array ('l.member_name', 'member_name desc' ), 
	1 => array ('i.item_name', 'item_name desc' ), 
	2 => array ('i.item_value ', 'item_value desc' ),
);
				
$icurrent_order = switch_order ($isort_order, 'ui');
		
// item selection
$sql_array = array(
    'SELECT'    => 'i.item_id, i.item_name, i.item_gameid, i.member_id, i.item_zs, 
   				l.member_name, c.colorcode, c.imagename, l.member_gender_id, 
   				a.image_female, a.image_male, i.item_date, i.raid_id, i.item_value, 
   				i.item_decay, i.item_value - i.item_decay as item_total',
    'FROM'      => array(
        CLASS_TABLE 		=> 'c', 
        RACE_TABLE  		=> 'a',
        MEMBER_LIST_TABLE 	=> 'l', 
        RAID_ITEMS_TABLE    => 'i',
    ),
    'WHERE'     =>  'c.game_id = l.game_id  and c.class_id = l.member_class_id 
    				and l.member_race_id =  a.race_id and a.game_id = l.game_id
    				and l.member_id = i.member_id and i.raid_id = ' . $raid_id,  
    'ORDER_BY'  => $icurrent_order ['sql'], 
);

$sql = $db->sql_build_query('SELECT', $sql_array);
$result = $db->sql_query ( $sql );
$number_items = 0;
$item_value = 0.00;
$item_decay = 0.00;
$item_total = 0.00;

while ( $row = $db->sql_fetchrow ($result)) 
{
    if ($bbDKP_Admin->bbtips == true)
	{
		if ($row['item_gameid'] > 0 )
		{
			$item_name = $bbtips->parse('[itemdkp]' . $row['item_gameid']  . '[/itemdkp]'); 
		}
		else 
		{
			$item_name = $bbtips->parse('[itemdkp]' . $row['item_name']  . '[/itemdkp]');
		}
	}
	else
	{
		$item_name = $row['item_name'];
	}
		
$race_image = (string) (($row['member_gender_id']==0) ? $row['image_male'] : $row['image_female']);

$template->assign_block_vars ( 'items_row', array (
	'DATE' 			=> (! empty ( $row ['item_date'] )) ? $user->format_date($row['item_date']) : '&nbsp;', 

	'COLORCODE'  	=> ($row['colorcode'] == '') ? '#123456' : $row['colorcode'],
    'CLASS_IMAGE' 	=> (strlen($row['imagename']) > 1) ? $phpbb_root_path . "images/class_images/" . $row['imagename'] . ".png" : '',  
	'S_CLASS_IMAGE_EXISTS' => (strlen($row['imagename']) > 1) ? true : false, 				

	'RACE_IMAGE' 	=> (strlen($race_image) > 1) ? $phpbb_root_path . "images/race_images/" . $race_image . ".png" : '',  
	'S_RACE_IMAGE_EXISTS' => (strlen($race_image) > 1) ? true : false, 			 				
	'BUYER' 		=> (! empty ( $row ['member_name'] )) ? $row ['member_name'] : '&lt;<i>Not Found</i>&gt;', 
	'ITEMNAME'      => $item_name,
	'ITEM_ID'		=> $row['item_id'],
	'ITEM_ZS'      	=> ($row['item_zs'] == 1) ? ' checked="checked"' : '',
	'U_VIEW_BUYER' => append_sid ("{$phpbb_root_path}dkp.$phpEx" , "page=viewmember&amp;" . URI_NAMEID . "={$row['member_id']}&amp;" . URI_DKPSYS. "=" . $raid['event_dkpid']),
	'ITEMVALUE' 	=> $row['item_value'],
	'DECAYVALUE' 	=> $row['item_decay'],
	'TOTAL' 		=> $row['item_total'],
	));

	$number_items++; 
	$item_value += $row['item_value'];
	$item_decay += $row['item_decay'];
	$item_total += $row['item_total'];
}		


$template->assign_vars(array(
	'S_SHOWITEMPANE' 	=> ($number_items > 0 ) ? true : false,		
	'ITEM_VALUE'	 	 => $item_value,
	'ITEMDECAYVALUE'	 => $item_decay,
	'ITEMTOTAL'			 => $item_total,
	'RAIDNET'			 => $raid_total - $item_total,
	'ITEM_FOOTCOUNT'	 => sprintf($user->lang['VIEWRAID_DROPS_FOOTCOUNT'], $number_items) ,
	'S_DISPLAY_VIEWRAIDS' => true,
));

/*****************************
*	class block
******************************/
$classes = array();

// item selection
$sql_array = array(
    'SELECT'    => ' c.game_id, c.class_id, c1.name, c.colorcode, c.imagename ',
    'FROM'      => array(
        CLASS_TABLE 		=> 'c', 
        MEMBER_LIST_TABLE 	=> 'l', 
        BB_LANGUAGE    		=> 'c1',
    ),
    'WHERE'     =>  "c.game_id = l.game_id  and c.class_id = l.member_class_id  
    				AND c1.attribute_id = l.member_class_id and c1.game_id = l.game_id
    				AND c1.language= '" . $config['bbdkp_lang'] . "' AND c1.attribute = 'class'",
    'GROUP_BY'  => 'c.game_id, c.class_id, c1.name, c.colorcode, c.imagename',
    'ORDER_BY' => 'c1.name'
);

$sql = $db->sql_build_query('SELECT', $sql_array);
$result = $db->sql_query($sql);
while ( $row = $db->sql_fetchrow($result) )
{
	$classes[$row['class_id']]['classname'] = $row['name'];
	$classes[$row['class_id']]['colorcode'] = $row['colorcode'];
	$classes[$row['class_id']]['imagename'] = $row['imagename'];
	$classes[$row['class_id']]['group'] = ' ';
	$classes[$row['class_id']]['count'] = 0;
}
$db->sql_freeresult($result);
	
foreach($raid_details as $attendee)
{
	$classes[$attendee['class_id']]['group'] .= $attendee['member_name'] . ' ';
	$classes[$attendee['class_id']]['count'] += 1;
}
	
foreach ( $classes as $id => $class )
{
	$percentage =  ( $countattendees > 0 ) ? round(($class['count'] / $countattendees) * 100) : 0;
	$template->assign_block_vars('class_row', array(
		'CLASS'			=> $class['classname'],
		'CLASSIMAGE'	=> $class['imagename'],
		'CLASSCOLOR'	=> $class['colorcode'],
		'BAR'			=> create_bar((   $class['count'] * 10), $class['count'] . ' (' . $percentage . '%)', $class['colorcode']  ),
		'ATTENDEES' => '<span style="color: ' . $class['colorcode']  . '"><strong>' . $class['group'] . '</strong></span>' )		  );
}
unset($raid_details);
unset($classes);

// Output page
page_header($title);

?>