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

if ( isset($_GET[URI_EVENT]) && isset($_GET[URI_DKPSYS])  )
{
    $eventid = request_var(URI_EVENT, 0); 
    $dkpid = request_var(URI_DKPSYS, 0); 
    $selfurl = append_sid("{$phpbb_root_path}dkp.$phpEx" , 'page=viewevent&amp;' . URI_EVENT . '='.  $eventid . '&amp;' . URI_DKPSYS . '='. $dkpid ) ;
    
     //get event info
    $sql = 'SELECT event_dkpid, event_id, event_name, event_value, event_imagename   
	        FROM ' . EVENTS_TABLE . ' 
	        WHERE event_id = ' . $eventid;
	$result = $db->sql_query($sql);
	while ( $row = $db->sql_fetchrow($result))
	{
		if(strlen( $row['event_imagename']) > 1)
		{
			$eventimg = $phpbb_root_path . "images/event_images/" . $row['event_imagename'] . ".png"; 
		}
		else 
		{
			$eventimg = $phpbb_root_path . "images/event_images/dummy.png";
		}
		
		$eventname =  
		$template->assign_vars(array(
        	'EVENTNAME' 	 => $row['event_name'],
        	'VALUE' 		 => $row['event_value'], 
			'IMAGEPATH' 	=> $eventimg,  
			'RECORDED_RAID_HISTORY' => sprintf($user->lang['RECORDED_RAID_HISTORY'], $row['event_name']),
	    ));

	}
	$db->sql_freeresult($result);
  
    //  list the raids 
   $sort_order = array(
        0 => array('raid_start desc', 'raid_start'),
        1 => array('raid_note', 'raid_note desc'),
        2 => array('raid_value desc', 'raid_value')
    );
    
    $current_order = switch_order ( $sort_order );		
	$sql_array = array (
		'SELECT' => ' e.event_dkpid, e.event_name,  
					  r.raid_id, r.raid_start, r.raid_note, 
					  r.raid_added_by, r.raid_updated_by, 
					  SUM(ra.raid_value) as raid_value, SUM(ra.time_bonus) as time_value, 
					  SUM(ra.zerosum_bonus) as zs_value, SUM(ra.raid_decay) as raiddecay, 
					  SUM(ra.raid_value + ra.time_bonus  + ra.zerosum_bonus - ra.raid_decay) as total', 
		'FROM' => array (
			RAID_DETAIL_TABLE	=> 'ra' ,
			RAIDS_TABLE 		=> 'r' , 
			EVENTS_TABLE 		=> 'e',		
			), 
		'WHERE' => " ra.raid_id = r.raid_id and r.event_id = e.event_id and e.event_id = " . ( int ) $eventid,
		'GROUP_BY' => 'e.event_dkpid, e.event_name,  
					  r.raid_id,  r.raid_start, r.raid_note, 
					  r.raid_added_by, r.raid_updated_by',	
		'ORDER_BY' => $current_order ['sql'], 
	);
	
	$sql = $db->sql_build_query('SELECT', $sql_array);
    $result = $db->sql_query($sql);
    $raid_count=0;
    while ($row = $db->sql_fetchrow($result))
	{
		$raid_count++;
	}
    
    if($raid_count > 0)
    {
		$startr = request_var ( 'startr', 0 );
		// get requested window
		$raid_ids = array();
		$result = $db->sql_query_limit ( $sql, $config ['bbdkp_user_rlimit'], $startr );
	    while ( $row = $db->sql_fetchrow($result) )
	    {
	        $raids[$row['raid_id']] = array(
	        	'event_name'  	=> $row['event_name'],
	            'raid_id'    	=> $row['raid_id'],
	            'raid_start'  	=> $row['raid_start'],
	            'raid_note'  	=> $row['raid_note'],
	            'raid_value' 	=> $row['raid_value'],
	        	'time_value' 	=> $row['time_value'],
		        'zs_value' 		=> $row['zs_value'],
		        'raiddecay' 	=> $row['raiddecay'],
		        'total' 		=> $row['total'],
	        );
	        
	        $raid_ids[] = $row['raid_id'];
	    }
	    $db->sql_freeresult($result);
	    // Find the attendees at each raid
	    $sql = 'SELECT raid_id, count(member_id) AS countatt 
	            FROM ' . RAID_DETAIL_TABLE . ' 
	            WHERE ' . $db->sql_in_set('raid_id', $raid_ids) . ' 
	            GROUP BY raid_id';
	    $result = $db->sql_query($sql);
	
	    while ( $row = $db->sql_fetchrow($result) )
	    {
	        $raids[$row['raid_id']]['numattendees'] = $row['countatt'];
	    }
	    $db->sql_freeresult($result);
	    
	 
	    //calculate the average event attendance and droprate 
	    // Find the item drops for each raid
	    $sql = 'SELECT raid_id, count(item_id) AS countatt 
	            FROM ' . RAID_ITEMS_TABLE . ' 
	            WHERE ' . $db->sql_in_set('raid_id', $raid_ids) . ' GROUP BY raid_id';
	    $result = $db->sql_query($sql);
	    while ( $row = $db->sql_fetchrow($result) )
	    {
	        $raids[$row['raid_id']]['numitems'] = $row['countatt'];
	    }
	    $db->sql_freeresult($result);
	    
	    $total_drop_count = 0;
	    $total_attendees_count = 0;
	    $total_earned = 0;
	    
	    // Loop through the raids for this event
	    $total_raid_count = sizeof($raids);
	    foreach ( $raids as $raid_id => $raid )
	    {
	          $drop_count = ( isset($raid['numitems']) ) ? $raid['numitems'] : 0;
	          $attendees_count = ( isset($raid['numattendees']) ) ? $raid['numattendees'] : 0;
	          
	          $template->assign_block_vars('raids_row', array(
	            'U_VIEW_RAID' => append_sid("{$phpbb_root_path}dkp.$phpEx" , 'page=viewraid&amp;'. URI_RAID . '='.$raid['raid_id']),
	            'DATE'        => date($config['bbdkp_date_format'], $raid['raid_start']), 
	            'ATTENDEES'   => $attendees_count,
	            'DROPS'       => $drop_count,
	            'NOTE'        => ( !empty($raid['raid_note']) ) ? $raid['raid_note'] : '&nbsp;',
	            'RAIDVALUE'   => $raid['raid_value'],
		        'TIMEVALUE'   => $raid['time_value'],
		        'ZSVALUE'     => $raid['zs_value'],
		        'DECAYVALUE'  => $raid['raiddecay'],
		        'TOTAL'       => $raid['total'],
	          )
	        );
	        
	        $total_drop_count += $drop_count;
	        $total_attendees_count += $attendees_count;
	        $total_earned += $raid['raid_value'];       
	    }
	 
	    // Prevent div by 0
	    $average_attendees = ( $total_raid_count > 0 ) ? round($total_attendees_count / $total_raid_count, 2) : 0;
	    $average_drops     = ( $total_drop_count > 0 ) ? round($total_drop_count / $total_raid_count,2 )      : 0;
	
	    $raidpagination = generate_pagination2($selfurl . '&amp;o1=' . $current_order ['uri'] ['current'] , $raid_count, $config ['bbdkp_user_rlimit'], $startr, true, 'startr'  );
		
		/***
	     *  
	     *  list the dropped items 
	     *  
	     **/  
	    $start = request_var('start' ,0);
	        
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
		    'WHERE'     =>  'c.class_id = l.member_class_id
		    				AND c.game_id = l.game_id  
		    				AND l.member_race_id =  a.race_id
		    				AND a.game_id = l.game_id   
		    				and l.member_id = i.member_id AND ' . $db->sql_in_set('raid_id', $raid_ids),  
		);
		$sql = $db->sql_build_query('SELECT', $sql_array);
		
	    $result = $db->sql_query_limit($sql, $config['bbdkp_user_ilimit'], $start);
	
		$number_items = 0;
		$item_value = 0.00;
		$item_decay = 0.00;
		$item_total = 0.00;
			
	    while ( $row = $db->sql_fetchrow($result) )
	    {
			if ($bbDKP_Admin->bbtips == true)
			{
				$item_name = '<b>' . $bbtips->parse('[itemdkp]' . $row['item_name']  . '[/itemdkp]') . '</b>'; 
			}
			else
			{
				$item_name = '<b>' . $row['item_name']. '</b>';
			}
			$race_image = (string) (($row['member_gender_id']==0) ? $row['image_male'] : $row['image_female']);
	        $template->assign_block_vars('items_row', array(
	          'DATE'          => date($config['bbdkp_date_format'], $row['item_date']),
	          'U_VIEW_RAID'   => append_sid("{$phpbb_root_path}dkp.$phpEx" , 'page=viewraid&amp;' . URI_RAID . '=' . $row['raid_id']) ,
			  
	          'COLORCODE'  	=> ($row['colorcode'] == '') ? '#123456' : $row['colorcode'],
	          'CLASS_IMAGE' 	=> (strlen($row['imagename']) > 1) ? $phpbb_root_path . "images/class_images/" . $row['imagename'] . ".png" : '',  
			  'S_CLASS_IMAGE_EXISTS' => (strlen($row['imagename']) > 1) ? true : false, 				
	          'RACE_IMAGE' 	=> (strlen($race_image) > 1) ? $phpbb_root_path . "images/race_images/" . $race_image . ".png" : '',  
			  'S_RACE_IMAGE_EXISTS' => (strlen($race_image) > 1) ? true : false, 			 				
			  'BUYER' 		=> (! empty ( $row ['member_name'] )) ? $row ['member_name'] : '&lt;<i>Not Found</i>&gt;', 
	        
	          'U_VIEW_MEMBER' => append_sid("{$phpbb_root_path}dkp.$phpEx" , 'page=viewmember&amp;' . URI_NAMEID . '=' . $row['member_id'] . '&amp;' . URI_DKPSYS . '='. $dkpid) ,
	          'ITEMNAME'      => $item_name, 
	          'U_VIEW_ITEM'   => append_sid("{$phpbb_root_path}dkp.$phpEx" , 'page=viewitem&amp;' . URI_ITEM . '=' . $row['item_id']) ,
	          'ITEM_ZS'      	=> ($row['item_zs'] == 1) ? ' checked="checked"' : '',
			  'ITEMVALUE' 	=> $row['item_value'],
			  'DECAYVALUE' 	=> $row['item_decay'],
			  'TOTAL' 		=> $row['item_total'],
	        ));
	        
			$number_items++; 
			$item_value += $row['item_value'];
			$item_decay += $row['item_decay'];
			$item_total += $row['item_total'];
	    }
	       
	    $itempagination = generate_pagination($selfurl, $total_drop_count, $config['bbdkp_user_ilimit'], $start, true);
	    
	    $template->assign_vars(array(
			'RAIDPAGINATION' 	  => $raidpagination ,    
	        'O_DATE'  			  => $current_order['uri'][0],
	        'O_NOTE'  			  => $current_order['uri'][1],
	        'O_VALUE' 			  => $current_order['uri'][2],
	        
	    	'DKPPOOL'			  => ( !empty($event['dkpsys_name']) ) ? $event['dkpsys_name'] : 'N/A',
	        'EVENT_ADDED_BY'      => ( !empty($event['event_added_by']) ) ? $event['event_added_by'] : 'N/A',
	        'EVENT_UPDATED_BY'    => ( !empty($event['event_updated_by']) ) ? $event['event_updated_by'] : 'N/A',
	        'AVERAGE_ATTENDEES'   => $average_attendees,
	        'AVERAGE_DROPS'       => $average_drops,
	        'TOTAL_EARNED'        => sprintf("%.2f", $total_earned),
	        'VIEWEVENT_FOOTCOUNT' => sprintf($user->lang['VIEWEVENT_FOOTCOUNT'], $total_raid_count),
			
	        'S_SHOWZS' 			=> ($config['bbdkp_zerosum'] == '1') ? true : false, 
			'S_SHOWTIME' 		=> ($config['bbdkp_timebased'] == '1') ? true : false,
			'S_SHOWDECAY' 		=> ($config['bbdkp_decay'] == '1') ? true : false,
	    
	        'L_RECORDED_DROP_HISTORY' => sprintf($user->lang['RECORDED_DROP_HISTORY'], $eventname),
	        'ITEM_FOOTCOUNT'      => sprintf($user->lang['VIEWITEM_FOOTCOUNT'], $total_drop_count, $total_drop_count),
	        'START' 			=> $start,
	    	'ITEM_PAGINATION' 	=> $itempagination,
	    	'S_DISPLAY_VIEWEVENT' => true, 
	    
	    )
	    );
    	
    }
    else
    {
    	$template->assign_vars(array(
    		'S_DISPLAY_VIEWEVENT' => true, 
    		'U_VIEW_EVENT'        => $selfurl 
    	));
    }

    // build breadcrumbs menu                              
    $navlinks_array = array(
    array(
	     'DKPPAGE' => $user->lang['MENU_EVENTS'],
	     'U_DKPPAGE' => append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=listevents'),
    ),

    array(
	     'DKPPAGE' => $eventname,
	     'U_DKPPAGE' => $selfurl ,
    ),
    );

    foreach( $navlinks_array as $name )
    {
	    $template->assign_block_vars('dkpnavlinks', array(
		    'DKPPAGE' => $name['DKPPAGE'],
		    'U_DKPPAGE' => $name['U_DKPPAGE'],
    ));
    }
        
	// Output page
	page_header($user->lang['MENU_VIEWEVENT'] . ' ' . $eventname);
}
else
{
    trigger_error($user->lang['ERROR_EMPTY_EVENTNAME']);
}
?>
