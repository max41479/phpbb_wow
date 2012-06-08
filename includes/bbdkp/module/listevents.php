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

$sort_order = array(
    0 => array('event_name', 'event_dkpid, event_name desc'),
    1 => array('event_value desc', 'event_dkpid, event_value desc')
);

$current_order = switch_order($sort_order);
$total_events= 0;
$start = request_var('start', 0);

/*** get dkp pools with events with raids ***/
$sql_array = array (
	'SELECT' => ' dkpsys_id, dkpsys_name ', 
	'FROM' => array (
		DKPSYS_TABLE		=> 'd',
		EVENTS_TABLE 		=> 'e',		
		RAIDS_TABLE 		=> 'r' , 
		), 
	'WHERE' => 'd.dkpsys_id = e.event_dkpid 
				and r.event_id = e.event_id ',
	'GROUP_BY' => 'dkpsys_id, dkpsys_name ', 
	'ORDER_BY' => 'dkpsys_name'
);

$sql = $db->sql_build_query('SELECT', $sql_array);
$dkppool_result = $db->sql_query($sql); 
$total_events = 0;

while ( $pool = $db->sql_fetchrow($dkppool_result) )
{
	$template->assign_block_vars(
    	'dkpsys_row', array(
    	'NAME' => $pool['dkpsys_name'],
    	'ID' => $pool['dkpsys_id']
    ));

	/*** get events ***/
	$sql_array = array (
		'SELECT' => ' e.event_dkpid, e.event_id, e.event_name, e.event_value,  e.event_color, e.event_imagename, 
		COUNT(r.raid_id) as raidcount, MAX(raid_start) as newest, MIN(raid_start) as oldest ', 
		'FROM' => array (
			EVENTS_TABLE 		=> 'e',		
			RAIDS_TABLE 		=> 'r', 
			), 
		'WHERE' => 'e.event_dkpid = ' . (int) $pool['dkpsys_id'] . 
					' and r.event_id = e.event_id ',
		'ORDER_BY' => 'e.event_name', 
		'GROUP_BY' => 'e.event_dkpid, e.event_id, e.event_name, e.event_value, e.event_color, e.event_imagename', 
	);
	$sql = $db->sql_build_query('SELECT', $sql_array);	
	
	$events_result = $db->sql_query_limit($sql, $config['bbdkp_user_elimit'], $start);
	while ( $event = $db->sql_fetchrow($events_result))
	{
		$total_events ++;
	    $template->assign_block_vars(
	    	'dkpsys_row.events_row', array(
	        	'U_VIEW_EVENT' =>  append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=viewevent&amp;' . URI_EVENT . '='.$event['event_id'] . '&amp;'.URI_DKPSYS.'='.$event['event_dkpid']) ,
	        	'NAME' 			=> $event['event_name'],
	        	'VALUE' 		=> $event['event_value'], 
				'EVENTCOLOR'  	=> $event['event_color'],
                'EVENTIMAGE'  	=> $event['event_imagename'],	    
	        	'RAIDCOUNT' 	=> $event['raidcount'],
	        	'OLDEST' 		=> date($config['bbdkp_date_format'], $event['oldest'])  ,
	    		'NEWEST' 		=> date($config['bbdkp_date_format'], $event['newest']))
	    );

	}
	$db->sql_freeresult($events_result);
}
$db->sql_freeresult($dkppool_result);

$u_listevents = append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=listevents');

$navlinks_array = array(
array(
 'DKPPAGE' => $user->lang['MENU_EVENTS'],
 'U_DKPPAGE' => $u_listevents,
));

foreach( $navlinks_array as $name )
{
	$template->assign_block_vars('dkpnavlinks', array(
	'DKPPAGE' => $name['DKPPAGE'],
	'U_DKPPAGE' => $name['U_DKPPAGE'],
	));
}

$template->assign_vars(array(
    'O_NAME' => $current_order['uri'][0],
    'O_VALUE' => $current_order['uri'][1],
    'U_LIST_EVENTS' => $u_listevents, 
    'START' => $start,
    'LISTEVENTS_FOOTCOUNT' => sprintf($user->lang['LISTEVENTS_FOOTCOUNT'], $total_events , $config['bbdkp_user_elimit']),
    'EVENT_PAGINATION' => generate_pagination( append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=listevents&amp;o='.$current_order['uri']['current']),
             $total_events, $config['bbdkp_user_elimit'], $start, true),
	'S_DISPLAY_LISTEVENTS' => true,             
));

$title = $user->lang['EVENTS'];

// Output page
page_header($title);

?>
