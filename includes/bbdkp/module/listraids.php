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

/**** begin dkpsys pulldown  ****/	
$query_by_pool = false;
$defaultpool = 99; 
$dkpvalues[0] = $user->lang['ALL']; 
$dkpvalues[1] = '--------'; 
$sql_array = array(
	'SELECT'    => 'a.dkpsys_id, a.dkpsys_name, a.dkpsys_default', 
	'FROM'		=> array( 
				DKPSYS_TABLE => 'a', 
				EVENTS_TABLE => 'e',
				RAIDS_TABLE => 'r',
				), 
	'WHERE'  => ' a.dkpsys_id = e.event_dkpid and e.event_id=r.event_id', 
	'GROUP_BY'  => 'a.dkpsys_id, a.dkpsys_name, a.dkpsys_default', 
	'ORDER_BY'  => 'a.dkpsys_name'
); 
$sql = $db->sql_build_query('SELECT', $sql_array);
$result = $db->sql_query ( $sql );
$index = 3;
while ( $row = $db->sql_fetchrow ( $result ) )
{
	$dkpvalues[$index]['id'] = $row ['dkpsys_id']; 
	$dkpvalues[$index]['text'] = $row ['dkpsys_name']; 
	if (strtoupper ( $row ['dkpsys_default'] ) == 'Y')
	{
		$defaultpool = $row ['dkpsys_id'];
	}
	$index +=1;
}
$db->sql_freeresult ( $result );

$dkp_id = 0; 
if(isset( $_POST ['pool']) or isset ( $_GET [URI_DKPSYS] ) )
{
	if (isset( $_POST ['pool']) )
	{
		$pulldownval = request_var('pool',  $user->lang['ALL']);
		if(is_numeric($pulldownval))
		{
			$query_by_pool = true;
			$dkp_id = intval($pulldownval); 	
		}
	}
	elseif (isset ( $_GET [URI_DKPSYS] ))
	{
		$query_by_pool = true;
		$dkp_id = request_var(URI_DKPSYS, 0); 
	}
}
else 
{
	$query_by_pool = true;
	$dkp_id = $defaultpool; 
}


foreach ( $dkpvalues as $key => $value )
{
	if(!is_array($value))
	{
		$template->assign_block_vars ( 'pool_row', array (
			'VALUE' => $value, 
			'SELECTED' => ($value == $dkp_id && $value != '--------') ? ' selected="selected"' : '',
			'DISABLED' => ($value == '--------' ) ? ' disabled="disabled"' : '',  
			'OPTION' => $value, 
		));
	}
	else 
	{
		$template->assign_block_vars ( 'pool_row', array (
			'VALUE' => $value['id'], 
			'SELECTED' => ($dkp_id == $value['id']) ? ' selected="selected"' : '', 
			'OPTION' => $value['text'], 
		));
		
	}
}

$query_by_pool = ($dkp_id != 0) ? true : false;
/**** end dkpsys pulldown  ****/	 
	 
$start = request_var('start', 0);   

$total_raids=0;

// get sort order 
$sort_order = array
(
    0 => array('raid_start desc', 'raid_start'),
    1 => array('dkpsys_name', 'dkpsys_name desc'),
    2 => array('event_name', 'event_name desc'),
    3 => array('raid_note', 'raid_note desc'),
    4 => array('raid_value desc', 'raid_value')
);
 
$current_order = switch_order($sort_order);
//get total nr of raids 
$sql_array = array(
    'SELECT'    => 	' COUNT(*) as numraids  ', 
    'FROM'      => array(
		EVENTS_TABLE			=> 'e', 	        
		RAIDS_TABLE 			=> 'r'	         
    	),
    'WHERE'		=> 'r.event_id = e.event_id ',
   );

if ($query_by_pool == true)
{
	$sql_array['WHERE'] .= ' AND e.event_dkpid = ' . $dkp_id; 
}
$sql = $db->sql_build_query('SELECT', $sql_array);
$result = $db->sql_query($sql);
$total_raids = (int) $db->sql_fetchfield('numraids');
$db->sql_freeresult ( $result );

// how many raids per page
$raidlines = $config['bbdkp_user_rlimit'] ;

if ($query_by_pool)
{
    $pagination = generate_pagination( append_sid("{$phpbb_root_path}dkp.$phpEx" , 'page=listraids&amp;' . URI_DKPSYS . '=' . $dkp_id . 
    '&amp;o='.  $current_order['uri']['current'] ), $total_raids, $config['bbdkp_user_rlimit'], $start, true);
    
    $u_list_raids =  append_sid("{$phpbb_root_path}dkp.$phpEx" , 'page=listraids&amp;' . URI_DKPSYS . '='. $dkp_id);
}
else 
{
    $pagination = generate_pagination( append_sid("{$phpbb_root_path}dkp.$phpEx" , 'page=listraids&amp;' . URI_DKPSYS .  '=All&amp;o='.  
    $current_order['uri']['current'] ), $total_raids, $config['bbdkp_user_rlimit'], $start, true);
    
    $u_list_raids =  append_sid("{$phpbb_root_path}dkp.$phpEx" , 'page=listraids&amp;');
}

$sql_array = array (
		'SELECT' => ' e.event_dkpid, e.event_id, e.event_name, e.event_color, 
					  r.raid_id, r.raid_start, r.raid_note, 
					  r.raid_added_by, r.raid_updated_by, 
					  SUM(ra.raid_value) as raid_value, SUM(ra.time_bonus) as time_value, 
					  SUM(ra.zerosum_bonus) as zs_value, SUM(ra.raid_decay) as raiddecay, 
					  SUM(ra.raid_value + ra.time_bonus  +ra.zerosum_bonus - ra.raid_decay) as total,
					  COUNT(ra.member_id) as attendees ', 
		'FROM' => array (
			RAID_DETAIL_TABLE	=> 'ra' ,
			RAIDS_TABLE 		=> 'r' , 
			EVENTS_TABLE 		=> 'e',		
			), 
		'WHERE' => ' ra.raid_id = r.raid_id and r.event_id = e.event_id ',
		'GROUP_BY' => 'e.event_dkpid, e.event_id, e.event_name, e.event_color, 
					  r.raid_id, r.raid_start, r.raid_note, 
					  r.raid_added_by, r.raid_updated_by ',	
		'ORDER_BY' => $current_order ['sql'], 
);
	
if ($query_by_pool == true)
{
	$sql_array['WHERE'] .= ' AND e.event_dkpid = ' . $dkp_id; 
}

$sql = $db->sql_build_query('SELECT', $sql_array);
$raids_result = $db->sql_query_limit($sql, $raidlines , $start);
if ( !$raids_result)
{
   trigger_error ( $user->lang['ERROR_INVALID_RAID'] , E_USER_WARNING );
}

while ( $row = $db->sql_fetchrow($raids_result) )
{
    $template->assign_block_vars('raids_row', array(
        'DATE' => ( !empty($row['raid_start']) ) ? date($config['bbdkp_date_format'], $row['raid_start']) : '&nbsp;',
        'U_VIEW_RAID'  => append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=viewraid&amp;' . URI_RAID . '='.$row['raid_id']),
    	'U_VIEW_EVENT' => append_sid("{$phpbb_root_path}dkp.$phpEx" , 'page=viewevent&amp;' . URI_EVENT . '='.  $row['event_id'] . '&amp;' . URI_DKPSYS . '=' . $row['event_dkpid']),
		'POOL' => ( !empty($row['dkpsys_name']) ) ? $row['dkpsys_name'] : '&lt;<i>Not Found</i>&gt;',
    	'NAME' => ( !empty($row['event_name']) ) ? $row['event_name'] : '&lt;<i>Not Found</i>&gt;',
    	'EVENTCOLOR' => ( !empty($row['event_color']) ) ? $row['event_color'] : '#123456',
    	'NOTE' => ( !empty($row['raid_note']) ) ? $row['raid_note'] : '&nbsp;',
    	
    	'ATTENDEES' 	=> $row['attendees'],
        'RAIDVALUE' 	=> $row['raid_value'],
        'TIMEVALUE' 	=> $row['time_value'],
	    'ZSVALUE' 		=> $row['zs_value'],
	    'RAIDDECAY' 	=> $row['raiddecay'],
	    'TOTAL' 		=> $row['total'],
        )
    );
}

$sortlink = array();
for ($i=0; $i<=4; $i++)
{
    if ($query_by_pool)
    {
        $sortlink[$i] = append_sid($phpbb_root_path . 'dkp.'.$phpEx, 'page=listraids&amp;o=' . $current_order['uri'][$i] . '&amp;start=' . $start . '&amp;' . URI_DKPSYS . '=' . $dkp_id ); 
    }
    else 
    {
        $sortlink[$i] = append_sid($phpbb_root_path  . 'dkp.'.$phpEx, 'page=listraids&amp;o=' . $current_order['uri'][$i] . '&amp;start=' . $start . '&amp;' . URI_DKPSYS . '=All'  ); 
    }
}

$template->assign_block_vars('dkpnavlinks', array(
'DKPPAGE' 		=> $user->lang['MENU_RAIDS'],
'U_DKPPAGE' 	=> $u_list_raids,
));


$template->assign_vars(array(
    'S_SHOWZS' 			=> ($config['bbdkp_zerosum'] == '1') ? true : false, 
	'S_SHOWTIME' 		=> ($config['bbdkp_timebased'] == '1') ? true : false,
	'S_SHOWDECAY' 		=> ($config['bbdkp_decay'] == '1') ? true : false,
    
    'O_DATE'  => $sortlink[0],
    'O_POOL'  => $sortlink[1],
    'O_NAME'  => $sortlink[2],
    'O_NOTE'  => $sortlink[3],
    'O_VALUE' => $sortlink[4],
    
    'U_LIST_RAIDS' => $u_list_raids , 
    'LISTRAIDS_FOOTCOUNT' => sprintf($user->lang['LISTRAIDS_FOOTCOUNT'], $total_raids, $config['bbdkp_user_rlimit']),

    'START' => $start,
    'RAID_PAGINATION' => $pagination, 
	'S_DISPLAY_RAIDS' => true
));

// Output page
page_header($user->lang['RAIDS']);

?>