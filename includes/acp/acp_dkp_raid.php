<?php
/**
 * @package bbDKP.acp
 * @link http://www.bbdkp.com
 * @author Sajaki@gmail.com
 * @copyright 2009 bbdkp
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 1.2.8
 */

/**
 * @ignore
 */
if (! defined ( 'IN_PHPBB' )) 
{
	exit ();
}
if (! defined('EMED_BBDKP')) 
{
	$user->add_lang ( array ('mods/dkp_admin' ));
	trigger_error ( $user->lang['BBDKPDISABLED'] , E_USER_WARNING );
}

/**
*  This acp class manages Manual Raids
*
* @package bbDKP.acp
*/
class acp_dkp_raid extends bbDKP_Admin 
{
	private $link;
	public $u_action;
	
	/**
	 * main Raid function
	 */
	public function main($id, $mode) 
	{
		global $db, $user, $auth, $template, $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;
		$user->add_lang ( array ('mods/dkp_admin' ) );
		$user->add_lang ( array ('mods/dkp_common' ) );
		$this->link = '<br /><a href="' . append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_raid&amp;mode=listraids" ) . '"><h3>'.$user->lang['RETURN_DKPINDEX'].'</h3></a>';

		//do event test.
		$sql = 'SELECT count(*) as eventcount FROM ' . DKPSYS_TABLE . ' a , ' . EVENTS_TABLE . ' b 
			where a.dkpsys_id = b.event_dkpid 
			AND b.event_status = 1';
		$result = $db->sql_query ( $sql );
		$eventcount = $db->sql_fetchfield('eventcount');
		$db->sql_freeresult( $result );
		if($eventcount==0)
		{
			trigger_error ( $user->lang['ERROR_NOEVENTSDEFINED'], E_USER_WARNING );
		}
		
		switch ($mode)
		{
			case 'addraid' :
				/* newpage */
				$submit = (isset ( $_POST ['add'] )) ? true : false;
				if($submit)
				{
					// add raid to database
					$this->addraid();
				}
				// show add raid form
				$this->newraid();
				$this->page_title = 'ACP_DKP_RAID_ADD';
				$this->tpl_name = 'dkp/acp_' . $mode;
				break;
				
			case 'editraid' :
				
				$update = (isset ( $_POST ['update'] )) ? true : false;
				$delete = (isset ( $_POST ['delete'] )) ? true : false;
				$addraider = (isset ( $_POST ['addattendee'] )) ? true : false;
				$editraider = (isset ( $_GET ['editraider'] ) ) ? true : false;
				$updateraider = (isset ( $_POST ['editraider'] ) ) ? true : false;
				$deleteraider = (isset ( $_GET ['deleteraider'] )) ? true : false;
				$additem = (isset ( $_POST ['additem'] )) ? true : false;
				$deleteitem = (isset ( $_GET ['deleteitem'] )) ? true : false;
				$decayraid = (isset ( $_POST ['decayraid'] )) ?true : false;
				$raid_id = request_var ( 'hidden_id', 0 );

				/* handle actions */
				if($update)
				{
					//update raid
					$this->updateraid($raid_id);
					$this->displayraid($raid_id);
				}
				
				elseif($delete)
				{
					//delete the raid
					$this->deleteraid($raid_id);
				}
				
				elseif($additem)
				{
					//show form for adding items
					redirect(append_sid("{$phpbb_admin_path}index.$phpEx", 'i=dkp_item&amp;mode=edititem&amp;' . URI_RAID .'=' . $raid_id));
				}
		
				elseif($deleteitem)
				{
					$this->deleteitem(); 
				}
				
				elseif($addraider)
				{
					//adds raider
					$this->addraider($raid_id);
					$this->displayraid($raid_id);
				}
				
				elseif($editraider || $updateraider)
				{
					//show the form for editing a raider (get params from $get)
					$attendee_id = request_var(URI_NAMEID, 0); 
					$raid_id = request_var (URI_RAID, 0);
					$this->editraider($raid_id, $attendee_id);
				}
				
				elseif($deleteraider)
				{
					//show the form for editing a raider (get params from $get)
					$raid_id = request_var (URI_RAID, 0);
					$attendee_id = request_var(URI_NAMEID, 0);
					$this->deleteraider($raid_id, $attendee_id);
				}
				
				elseif($decayraid)
				{
					$dkpid = request_var('hidden_dkpid', 0);
					$this->decayraid($raid_id, $dkpid);
					$this->displayraid($raid_id);
				}
				
				else
				{
					// show edit form
					$raid_id = request_var (URI_RAID, 0);
					$this->displayraid($raid_id);
				}
				$this->page_title = 'ACP_DKP_RAID_EDIT';
				$this->tpl_name = 'dkp/acp_' . $mode;
				
				break;
				
			case 'listraids' :
				$raid_id = request_var (URI_RAID, 0);
				if($raid_id != 0)
				{
					$this->duplicate_raid($raid_id);	
				}
				$this->listraids();
				$this->page_title = 'ACP_DKP_RAID_LIST';
				$this->tpl_name = 'dkp/acp_' . $mode;
				break;		
		}
	
	}
	
	/** 
	 * display new raid creation screen
	 * 
	 */
	private function newraid()
	{
		global $db, $user, $config, $template, $phpbb_admin_path, $phpEx ;
		$dkpsys_id=0; 
		if (isset($_GET[URI_DKPSYS]) )
		{
			//user clicked on add raid from event editscreen
			$dkpsys_id = request_var ( URI_DKPSYS, 0 );
		}
		
		if (isset ( $_POST['dkpsys_id']) )
		{
			// getting dkp from pulldown
			$dkpsys_id = request_var ( 'dkpsys_id', 0 );
		}
	
		if($dkpsys_id==0)
		{
			//get default dkp pool
			$sql1 = 'SELECT dkpsys_id, dkpsys_name, dkpsys_default 
	                 FROM ' . DKPSYS_TABLE . ' a , ' . EVENTS_TABLE . " b 
					 where a.dkpsys_id = b.event_dkpid and dkpsys_default = 'Y' AND b.event_status = 1 ";
			$result1 = $db->sql_query ($sql1);
			// get the default dkp value (dkpsys_default = 'Y') from DB
			while ( $row = $db->sql_fetchrow ( $result1 ) ) 
			{
				$dkpsys_id = $row['dkpsys_id'];
			}
			$db->sql_freeresult( $result1 );
		}
		
		if($dkpsys_id==0)
		{ 	
			// get first row
			$sql1 = 'SELECT dkpsys_id, dkpsys_name , dkpsys_default 
                      FROM ' . DKPSYS_TABLE . ' a , ' . EVENTS_TABLE . ' b 
					  where a.dkpsys_id = b.event_dkpid AND b.event_status = 1 ';
			$result1 = $db->sql_query_limit ( $sql1, 1, 0 );
			while ( $row = $db->sql_fetchrow ( $result1 ) ) 
			{
				$dkpsys_id = $row['dkpsys_id'];
			}
			$db->sql_freeresult( $result1 );
		}
		
		//fill dkp dropdown
		$sql = 'SELECT dkpsys_id, dkpsys_name FROM ' . DKPSYS_TABLE . ' a , ' . EVENTS_TABLE . ' b 
				where a.dkpsys_id = b.event_dkpid AND b.event_status = 1 group by dkpsys_id, dkpsys_name ORDER BY dkpsys_name';
		$result = $db->sql_query ( $sql );
		while ( $row = $db->sql_fetchrow ( $result ) ) 
		{
			$template->assign_block_vars ( 'dkpsys_row', array (
				'VALUE' 	=> $row['dkpsys_id'], 
				'SELECTED' 	=> ($row['dkpsys_id'] == $dkpsys_id) ? ' selected="selected"' : '', 
				'OPTION' 	=> (! empty ( $row['dkpsys_name'] )) ? $row['dkpsys_name'] : '(None)' ) 
			);
		}
		$db->sql_freeresult($result);
		
		/* event listbox */
		// calculate number format
		$max_value = 0.00;
		$sql = 'SELECT max(event_value) AS max_value FROM ' . EVENTS_TABLE . ' where event_status = 1 AND event_dkpid = ' . $dkpsys_id; 
		$result = $db->sql_query ($sql);
		$max_value = (float) $db->sql_fetchfield('max_value', false, $result);
		$float = @explode ( '.', $max_value );
		$format = '%0' . @strlen ( $float [0] ) . '.2f';
		$db->sql_freeresult($result);
		
		$sql = ' SELECT event_id, event_name, event_value 
		FROM ' . EVENTS_TABLE . ' WHERE event_status = 1 AND 
		event_dkpid = ' . $dkpsys_id . ' ORDER BY event_name';
		$result = $db->sql_query($sql);
		$eventvalue= 0;
		while ($row = $db->sql_fetchrow($result))
		{
			$select_check = false;
			if (isset ($_GET[URI_EVENT]))
			{
				$select_check = ( $row['event_id'] == request_var(URI_EVENT, 0)) ? true : false;
				$eventvalue = $row['event_value']; 
			}
			
			$template->assign_block_vars ( 
				'events_row', array (
					'VALUE' => $row['event_id'], 
					'SELECTED' => ($select_check) ? ' selected="selected"' : '', 
					'OPTION' => $row['event_name'] . ' - (' . sprintf ( $format, $row['event_value'] ) . ')' 
			));
		}
		
		$db->sql_freeresult($result);
		
		/* getting left memberlist only with rank not hidden */
		$sql_array = array(
    		'SELECT'    => 'm.member_id ,m.member_name ',
 
	    	'FROM'      => array(
    		    MEMBER_LIST_TABLE 	  => 'm',
        		MEMBER_RANKS_TABLE    => 'r', 
    			),
 
    		'WHERE'     =>  ' m.member_guild_id = r.guild_id
    	    				 AND m.member_rank_id = r.rank_id
    	    				 AND r.rank_hide != 1', 
    		'ORDER_BY' => 'm.member_name',
		);
		
		$membercount = 0; 
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query ( $sql );
		while ( $row = $db->sql_fetchrow ( $result ) ) 
		{
			$class_colorcode = $row['member_id'] == '' ? '#123456' : $row['member_id']; 
			$membercount++;
			$template->assign_block_vars ( 'members_row', array (
				'VALUE' 	=> $row['member_id'], 
				'OPTION' 	=> $row['member_name'],
			));
		}
		$db->sql_freeresult( $result );
		
		if ($membercount==0)
		{
			// if no members defined yet stop here
			trigger_error ( $user->lang['ERROR_NOGUILDMEMBERSDEFINED'], E_USER_WARNING );
		}
		
		// build presets for raiddate and hour pulldown
		
		//RAID START DATE
		$now = getdate();
		$s_raid_day_options = '<option value="0">--</option>';
		for ($i = 1; $i < 32; $i++)
		{
			$day = $now['mday'] ;
			$selected = ($i == $day ) ? ' selected="selected"' : '';
			$s_raid_day_options .= "<option value=\"$i\"$selected>$i</option>";
		}

		$s_raid_month_options = '<option value="0">--</option>';
		for ($i = 1; $i < 13; $i++)
		{
			$month = $now['mon'] ;
			$selected = ($i == $month ) ? ' selected="selected"' : '';
			$s_raid_month_options .= "<option value=\"$i\"$selected>$i</option>";
		}

		$s_raid_year_options = '<option value="0">--</option>';
		for ($i = $now['year'] - 10; $i <= $now['year']; $i++)
		{
			$yr = $now['year'] ;
			$selected = ($i == $yr ) ? ' selected="selected"' : '';
			$s_raid_year_options .= "<option value=\"$i\"$selected>$i</option>";
		}
		
		//raid time
		$s_raid_hh_options = '<option value="0">--</option>';
		for ($i = 0; $i < 24; $i++)
		{
			$hh = $now['hours'] ;
			$selected = ($i == $hh ) ? ' selected="selected"' : '';
			$s_raid_hh_options .= "<option value=\"$i\"$selected>$i</option>";
		}

		$s_raid_mi_options = '<option value="0">--</option>';
		for ($i = 0; $i <= 59; $i++)
		{
			$mi = $now['minutes'] ;
			$selected = ($i == $mi ) ? ' selected="selected"' : '';
			$s_raid_mi_options .= "<option value=\"$i\"$selected>$i</option>";
		}

		$s_raid_s_options = '<option value="0">--</option>';
		for ($i = 0; $i <= 59; $i++)
		{
			$s = $now['seconds'] ;
			$selected = ($i == $s ) ? ' selected="selected"' : '';
			$s_raid_s_options .= "<option value=\"$i\"$selected>$i</option>";
		}
		
		// RAID END DATE
		//end raid time
		$hourduration = max(0, round( (float) $config['bbdkp_standardduration'],0));
		$minutesduration = max(0, ((float) $config['bbdkp_standardduration'] - floor((float) $config['bbdkp_standardduration'])) * 60 );
		$endtime = mktime(idate("H") + $hourduration, idate("i") + $minutesduration);
				
		$s_raidend_day_options = '<option value="0">--</option>';
		for ($i = 1; $i < 32; $i++)
		{
			$day = idate('d', $endtime);
			$selected = ($i == $day ) ? ' selected="selected"' : '';
			$s_raidend_day_options .= "<option value=\"$i\"$selected>$i</option>";
		}

		$s_raidend_month_options = '<option value="0">--</option>';
		for ($i = 1; $i < 13; $i++)
		{
			$month = idate('m', $endtime);
			$selected = ($i == $month ) ? ' selected="selected"' : '';
			$s_raidend_month_options .= "<option value=\"$i\"$selected>$i</option>";
		}

		$s_raidend_year_options = '<option value="0">--</option>';
		for ($i = $now['year'] - 10; $i <= $now['year']; $i++)
		{
			$yr = idate('Y', $endtime);
			$selected = ($i == $yr ) ? ' selected="selected"' : '';
			$s_raidend_year_options .= "<option value=\"$i\"$selected>$i</option>";
		}
		
		$s_raidend_hh_options = '<option value="0">--</option>';
		for ($i = 0; $i < 24; $i++)
		{
			$hh = idate('H', $endtime);
			$selected = ($i == $hh ) ? ' selected="selected"' : '';
			$s_raidend_hh_options .= "<option value=\"$i\"$selected>$i</option>";
		}
		
		$s_raidend_mi_options = '<option value="0">--</option>';
		for ($i = 0; $i <= 59; $i++)
		{
			$mi = idate('i', $endtime);
			$selected = ($i == $mi ) ? ' selected="selected"' : '';
			$s_raidend_mi_options .= "<option value=\"$i\"$selected>$i</option>";
		}

		$s_raidend_s_options = '<option value="0">--</option>';
		for ($i = 0; $i <= 59; $i++)
		{
			$s = $now['seconds'] ;
			$selected = ($i == $s ) ? ' selected="selected"' : '';
			$s_raidend_s_options .= "<option value=\"$i\"$selected>$i</option>";
		}
		
		//difference between start & end in seconds
	    $timediff = $endtime - mktime($now['hours'], $now['minutes'], $now['seconds'], $now['mon'], $now['mday'], $now['year']) ; 
	    $b = date('r', mktime($now['hours'], $now['minutes'], $now['seconds'], $now['mon'], $now['mday'], $now['year']));
	    $e = date('r', $endtime);
	    	
		// express difference in minutes
		$timediff=round($timediff/60, 2) ;
		$time_bonus = 0; 
		//if we have a $config interval bigger than 0 minutes then calculate time bonus
		if(	(int) $config['bbdkp_timeunit'] > 0)
		{
			$time_bonus = round($config['bbdkp_dkptimeunit'] * $timediff / $config['bbdkp_timeunit'], 2) ;	
		}
		
		add_form_key('acp_dkp_addraid');
		
		$template->assign_vars ( array (
				'U_BACK'			=> append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_raid&amp;mode=listraids" ),
				'L_TITLE' 			=> $user->lang ['ACP_ADDRAID'], 
				'L_EXPLAIN' 		=> $user->lang ['ACP_ADDRAID_EXPLAIN'], 
				'F_ADD_RAID' 		=> append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_raid&amp;mode=addraid" ), 
				'U_ADD_EVENT' 		=> append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_event&amp;mode=addevent" ), 
				'RAID_VALUE'		=> $eventvalue, 

				//raiddate START
				'S_RAIDDATE_DAY_OPTIONS'	=> $s_raid_day_options,
				'S_RAIDDATE_MONTH_OPTIONS'	=> $s_raid_month_options,
				'S_RAIDDATE_YEAR_OPTIONS'	=> $s_raid_year_options,
						
				//start
				'S_RAIDSTART_H_OPTIONS'		=> $s_raid_hh_options,
				'S_RAIDSTART_MI_OPTIONS'	=> $s_raid_mi_options,
				'S_RAIDSTART_S_OPTIONS'		=> $s_raid_s_options,

				//raiddate END
				'S_RAIDENDDATE_DAY_OPTIONS'	=> $s_raidend_day_options,
				'S_RAIDENDDATE_MONTH_OPTIONS'	=> $s_raidend_month_options,
				'S_RAIDENDDATE_YEAR_OPTIONS'	=> $s_raidend_year_options,

				//end
				'S_RAIDEND_H_OPTIONS'		=> $s_raidend_hh_options,
				'S_RAIDEND_MI_OPTIONS'		=> $s_raidend_mi_options,
				'S_RAIDEND_S_OPTIONS'		=> $s_raidend_s_options,

				'RAID_DURATION' 			=> $config['bbdkp_standardduration'],
				'DKPTIMEUNIT'				=> $config['bbdkp_dkptimeunit'], 
				'TIMEUNIT' 					=> $config['bbdkp_timeunit'],
		 		'DKPPERTIME'				=> sprintf($user->lang['DKPPERTIME'], $config['bbdkp_dkptimeunit'], $config['bbdkp_timeunit'] ), 
				// Form values
				'RAID_DKPSYSID' 			=> $dkpsys_id, 
				'TIME_BONUS'				=> $time_bonus, 

			 	'S_SHOWTIME' 	=> ($config['bbdkp_timebased'] == '1') ? true : false,
		
              	'L_DATE' => $user->lang ['DATE'] . ' dd/mm/yyyy', 
				'L_TIME' => $user->lang ['TIME'] . ' hh:mm:ss', 
				
				// Javascript messages
				'MSG_ATTENDEES_EMPTY' => $user->lang ['FV_REQUIRED_ATTENDEES'], 
				'MSG_NAME_EMPTY' 	  => $user->lang ['FV_REQUIRED_EVENT_NAME'], 
		));
	}
	
	/**
	 * displays a raid
	 * 
	 * @param int $raid_id the raid to display
	 */
	private function displayraid($raid_id)
	{
		global $db, $user, $config, $template, $phpbb_admin_path, $phpEx, $phpbb_root_path ;
		
		/*** get general raid info  ***/
		$sql_array = array (
			'SELECT' => ' d.dkpsys_name, e.event_dkpid, e.event_id, e.event_name, e.event_value, 
						  r.raid_id, r.raid_start, r.raid_end, r.raid_note, 
						  r.raid_added_by, r.raid_updated_by ', 
			'FROM' => array (
				DKPSYS_TABLE 		=> 'd' ,
				RAIDS_TABLE 		=> 'r' , 
				EVENTS_TABLE 		=> 'e',
				), 
			'WHERE' => " d.dkpsys_id = e.event_dkpid and r.event_id = e.event_id and r.raid_id=" . (int) $raid_id, 
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
				'raid_start' 		=> $row['raid_start'],
				'raid_end' 			=> $row['raid_end'], 
				'raid_note' 		=> $row['raid_note'], 
				'raid_added_by' 	=> $row['raid_added_by'], 
				'raid_updated_by' 	=> $row['raid_updated_by'] );
		}
		$db->sql_freeresult ($result);
		
		/* event pulldown */
		$max_value = 0.00;
		$sql = 'SELECT max(event_value) AS max_value FROM ' . EVENTS_TABLE . ' where event_status = 1 and event_dkpid = ' . $raid['event_dkpid']; 
		$result = $db->sql_query ($sql);
		$max_value = (float) $db->sql_fetchfield('max_value', false, $result);
		$float = @explode ( '.', $max_value );
		$format = '%0' . @strlen ( $float [0] ) . '.2f';
		$db->sql_freeresult($result);
		
		$sql = ' SELECT  event_id, event_name, event_value 
				 FROM ' . EVENTS_TABLE . ' WHERE event_status = 1 and event_dkpid = ' . $raid['event_dkpid'] . ' ORDER BY event_name';
		$result = $db->sql_query($sql);
		$event_value = 0;
		while ($row = $db->sql_fetchrow($result))
		{
			$select_check = ( $row['event_id'] == $raid['event_id']) ? true : false;
			if (isset ($_POST[URI_EVENT]))
			{
				$select_check = ( $row['event_id'] == request_var(URI_EVENT, 0)) ? true : false;
			}
			
			$template->assign_block_vars ( 
				'events_row', array (
					'VALUE' => $row['event_id'], 
					'SELECTED' => ($select_check) ? ' selected="selected"' : '', 
					'OPTION' => $row['event_name'] . ' - (' . sprintf ( $format, $row['event_value'] ) . ')' 
			));
			
		}
		$db->sql_freeresult($result);

	
		// build presets for raiddate and hour pulldown
		$now = getdate();
		// raid start
		
		$s_raid_day_options = '<option value="0">--</option>';
		for ($i = 1; $i < 32; $i++)
		{
			$day = isset($raid['raid_start']) ? date('j', $raid['raid_start']) : $now['mday'] ;
			$selected = ($i == $day ) ? ' selected="selected"' : '';
			$s_raid_day_options .= "<option value=\"$i\"$selected>$i</option>";
		}

		$s_raid_month_options = '<option value="0">--</option>';
		for ($i = 1; $i < 13; $i++)
		{
			$month = isset($raid['raid_start']) ? date('n', $raid['raid_start']) : $now['mon'] ;
			$selected = ($i == $month ) ? ' selected="selected"' : '';
			$s_raid_month_options .= "<option value=\"$i\"$selected>$i</option>";
		}

		$s_raid_year_options = '<option value="0">--</option>';
		for ($i = $now['year'] - 10; $i <= $now['year']; $i++)
		{
			$yr = isset($raid['raid_start']) ?  date('Y',$raid['raid_start']) : $now['year'] ;
			$selected = ($i == $yr ) ? ' selected="selected"' : '';
			$s_raid_year_options .= "<option value=\"$i\"$selected>$i</option>";
		}
		
		// raid end
		$s_raidend_day_options = '<option value="0">--</option>';
		for ($i = 1; $i < 32; $i++)
		{
			$day = isset($raid['raid_end']) ? date('j', $raid['raid_end']) : $now['mday'] ;
			$selected = ($i == $day ) ? ' selected="selected"' : '';
			$s_raidend_day_options .= "<option value=\"$i\"$selected>$i</option>";
		}

		$s_raidend_month_options = '<option value="0">--</option>';
		for ($i = 1; $i < 13; $i++)
		{
			$month = isset($raid['raid_end']) ? date('n', $raid['raid_end']) : $now['mon'] ;
			$selected = ($i == $month ) ? ' selected="selected"' : '';
			$s_raidend_month_options .= "<option value=\"$i\"$selected>$i</option>";
		}

		$s_raidend_year_options = '<option value="0">--</option>';
		for ($i = $now['year'] - 10; $i <= $now['year']; $i++)
		{
			$yr = isset($raid['raid_end']) ?  date('Y',$raid['raid_end']) : $now['year'] ;
			$selected = ($i == $yr ) ? ' selected="selected"' : '';
			$s_raidend_year_options .= "<option value=\"$i\"$selected>$i</option>";
		}
		
		
		//start raid time
		$s_raid_hh_options = '<option value="0"	>--</option>';
		for ($i = 0; $i < 24; $i++)
		{
			$hh = isset($raid['raid_start']) ? date('H', $raid['raid_start']) : $now['hours'] ;
			$selected = ($i == $hh ) ? ' selected="selected"' : '';
			$s_raid_hh_options .= "<option value=\"$i\"$selected>$i</option>";
		}

		$s_raid_mi_options = '<option value="0">--</option>';
		for ($i = 1; $i <= 59; $i++)
		{
			$mi = isset($raid['raid_start']) ? date('i', $raid['raid_start']) : $now['minutes'] ;
			$selected = ($i == $mi ) ? ' selected="selected"' : '';
			$s_raid_mi_options .= "<option value=\"$i\"$selected>$i</option>";
		}

		$s_raid_s_options = '<option value="0">--</option>';
		for ($i = 1; $i <= 59; $i++)
		{
			$s = isset($raid['raid_start']) ?  date('s',$raid['raid_start']) : $now['seconds'] ;
			$selected = ($i == $s ) ? ' selected="selected"' : '';
			$s_raid_s_options .= "<option value=\"$i\"$selected>$i</option>";
		}
		
		//end raid time
		$s_raidend_hh_options = '<option value="0"	>--</option>';
		for ($i = 0; $i < 24; $i++)
		{
			$hh = isset($raid['raid_end']) ? date('H', $raid['raid_end']) : $now['hours'] ;
			$selected = ($i == $hh ) ? ' selected="selected"' : '';
			$s_raidend_hh_options .= "<option value=\"$i\"$selected>$i</option>";
		}

		$s_raidend_mi_options = '<option value="0">--</option>';
		for ($i = 1; $i <= 59; $i++)
		{
			$mi = isset($raid['raid_end']) ? date('i', $raid['raid_end']) : $now['minutes'] ;
			$selected = ($i == $mi ) ? ' selected="selected"' : '';
			$s_raidend_mi_options .= "<option value=\"$i\"$selected>$i</option>";
		}

		$s_raidend_s_options = '<option value="0">--</option>';
		for ($i = 1; $i <= 59; $i++)
		{
			$s = isset($raid['raid_end']) ?  date('s',$raid['raid_end']) : $now['seconds'] ;
			$selected = ($i == $s ) ? ' selected="selected"' : '';
			$s_raidend_s_options .= "<option value=\"$i\"$selected>$i</option>";
		}

		// get raid details
		$sort_order = array (
				0 => array ('member_name desc', 'member_name desc' ),
				1 => array ('raid_value', 'raid_value desc' ), 
				2 => array ('time_bonus', 'time_bonus desc' ), 
				3 => array ('zerosum_bonus', 'zerosum_bonus desc' ),
				4 => array ('raid_decay', 'raid_decay desc' ),
				5 => array ('total desc', 'total desc' ),
				);
		
		$current_order = switch_order ( $sort_order );	
		$sql_array = array(
    		'SELECT'    => 'm.member_id ,m.member_name, c.colorcode, c.imagename, l.name, m.member_gender_id, a.image_female, a.image_male, 
    						r.raid_value, r.time_bonus, r.zerosum_bonus, 
    						r.raid_decay, (r.raid_value + r.time_bonus + r.zerosum_bonus - r.raid_decay) as total  ',
	    	'FROM'      => array(
    		    MEMBER_LIST_TABLE 	=> 'm',
    		    RACE_TABLE  		=> 'a',
        		RAID_DETAIL_TABLE   => 'r',
        		CLASS_TABLE 		=> 'c',
				BB_LANGUAGE 		=> 'l', 
    			),
 
    		'WHERE'     =>  " c.class_id = m.member_class_id and c.game_id = m.game_id
    						AND c.class_id = l.attribute_id and l.game_id = c.game_id AND l.attribute='class' 
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
			$race_image = (string) (($row['member_gender_id']==0) ? $row['image_male'] : $row['image_female']);
					
			$raid_details[$row['member_id']]['member_id'] = $row['member_id'];
			$raid_details[$row['member_id']]['colorcode'] = $row['colorcode'];
			$raid_details[$row['member_id']]['imagename'] = $row['imagename'];
			$raid_details[$row['member_id']]['classname'] = $row['name'];
			$raid_details[$row['member_id']]['raceimage'] = $race_image;
			$raid_details[$row['member_id']]['member_name'] = $row['member_name'];
			$raid_details[$row['member_id']]['raid_value'] = $row['raid_value'];
			$raid_details[$row['member_id']]['time_bonus'] = $row['time_bonus'];
			$raid_details[$row['member_id']]['zerosum_bonus'] = $row['zerosum_bonus'];
			$raid_details[$row['member_id']]['raid_decay'] = $row['raid_decay'];
		}
		$db->sql_freeresult( $result );
		$raid['raid_detail'] = $raid_details;
		
		$raid_value = 0.00;
		$time_bonus = 0.00;
		$zerosum_bonus = 0.00;
		$raid_decay = 0.00;
		$raid_total = 0.00;
		$countattendees = 0;
		foreach($raid_details as $member_id => $raid_detail)
		{
			// fill attendees table
			$template->assign_block_vars ('raids_row', array (
				'U_VIEW_ATTENDEE' => append_sid ("{$phpbb_admin_path}index.$phpEx" , "i=dkp_mdkp&amp;mode=mm_editmemberdkp&amp;" . URI_NAMEID . "={$member_id}&amp;" . URI_DKPSYS. "=" . $raid['event_dkpid']), 
				'U_EDIT_ATTENDEE' => append_sid ("{$phpbb_admin_path}index.$phpEx", "i=dkp_raid&amp;mode=editraid&amp;editraider=1&amp;". URI_RAID . "=" .$raid_id . "&amp;" . URI_NAMEID . "=" . $member_id),
				'U_DELETE_ATTENDEE' => append_sid ("{$phpbb_admin_path}index.$phpEx", "i=dkp_raid&amp;mode=editraid&amp;deleteraider=1&amp;". URI_RAID . "=" .$raid_id . "&amp;" . URI_NAMEID . "=" . $member_id),
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
			$raid_total = $raid_value + $time_bonus + $zerosum_bonus - $raid_decay;
			$countattendees += 1;
		}

		// populate addraider pulldown
		// sql to get all members not participating in this raid
		// semi-join between the members and raiders
		// and with rank set to not hidden
		$s_memberlist_options = '';
		$sql_array = array(
	    'SELECT'    => 	' l.member_id, l.member_name ', 
	    'FROM'      => array(
				MEMBER_LIST_TABLE 		=> 'l',
        		MEMBER_RANKS_TABLE    => 'r', 
					),
		'WHERE'		=> ' l.member_guild_id = r.guild_id
			 AND l.member_rank_id = r.rank_id
			 AND r.rank_hide != 1
			 AND l.member_id != ' . $config['bbdkp_bankerid']  . ' 
			 AND NOT EXISTS ( SELECT NULL FROM ' . RAID_DETAIL_TABLE . ' ra WHERE l.member_id = ra.member_id and ra.raid_id = ' . $raid_id . ' ) and l.member_status = 1 ' ,
			'ORDER_BY'	=> 'member_name asc ',
	    );
	    
	    $sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);
		while ( $row = $db->sql_fetchrow($result) )
		{
			$s_memberlist_options .= '<option value="' . $row['member_id'] . '"> ' . $row['member_name'] . '</option>';                    
		}
		$db->sql_freeresult($result);
		
		// populate item buyer list

		// if bbtips plugin exists load it 
		if ($this->bbtips == true)
		{
			if ( !class_exists('bbtips')) 
			{
				require($phpbb_root_path . 'includes/bbdkp/bbtips/parse.' . $phpEx); 
			}
			$bbtips = new bbtips;
		}
		
		//prepare item list sql
		$isort_order = array (
				0 => array ('l.member_name', 'member_name desc' ), 
				1 => array ('i.item_name', 'item_name desc' ), 
				2 => array ('i.item_value ', 'item_value desc' ),
				);
				
		// here we pass a nondefault header id to the sort function to sort the right table
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
	    'WHERE'     =>  'c.game_id = l.game_id and c.class_id = l.member_class_id 
	    				 AND l.member_race_id =  a.race_id and a.game_id = l.game_id  
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
		    if ($this->bbtips == true)
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
			'U_VIEW_BUYER' 	=> (! empty ( $row ['member_name'] )) ? append_sid ("{$phpbb_admin_path}index.$phpEx", "i=dkp_raid&amp;mode=editraid&amp;editraider=1&amp;". URI_RAID . "=" .$raid_id . "&amp;" . URI_NAMEID . "=" . $row['member_id']) : '',
			'U_VIEW_ITEM' 	=> append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_item&amp;mode=edititem&amp;" . URI_ITEM . "={$row['item_id']}&amp;" . URI_RAID . "={$raid_id}" ),
			'U_DELETE_ITEM' => append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_raid&amp;mode=editraid&amp;deleteitem=1&amp;" . URI_ITEM . "={$row['item_id']}&amp;" . URI_DKPSYS. "=" . $raid['event_dkpid']  ),
			'ITEMVALUE' 	=> $row['item_value'],
			'DECAYVALUE' 	=> $row['item_decay'],
			'TOTAL' 		=> $row['item_total'],
			));

			$number_items++; 
			$item_value += $row['item_value'];
			$item_decay += $row['item_decay'];
			$item_total += $row['item_total'];
		}		
		
		// add form key
		add_form_key('acp_dkp_addraid');
		
		//fill template
		$template->assign_vars ( array (
			'U_BACK'			=> append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_raid&amp;mode=listraids" ),
			'L_TITLE' 			=> $user->lang ['ACP_ADDRAID'], 
			'F_EDIT_RAID' 		=> append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_raid&amp;mode=editraid&amp;". URI_RAID . "=" .$raid_id ),
			'F_ADDATTENDEE' 	=> append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_raid&amp;mode=editraid&amp;". URI_RAID . "=" .$raid_id ),
			'RAIDTITLE' 		=> sprintf($user->lang['RAIDDESCRIPTION'], $raid_id, $raid['event_name'], 
							  	 $user->format_date($raid['raid_start'])), 
			'EVENT_VALUE'		=> $event_value, 
			'RAID_VALUE' 		=> $raid_value, 
			'INDIVIDUAL_RAID_VALUE' => $raid_value / (($countattendees > 0 ) ? $countattendees :1) ,
			'INDIVIDUAL_TIMEVALUE' 	=> $time_bonus  / (($countattendees > 0 ) ? $countattendees :1),
			'TIMEVALUE' 		=> $time_bonus ,							  	 
			'ZSVALUE' 			=> $zerosum_bonus,
			'DECAYVALUE' 		=> $raid_decay,
			'TOTAL'				=> $raid_total,
							  	 
			'RAID_NOTE' 		=> $raid['raid_note'], 
			'RAID_ID' 			=> $raid_id, 
			'EVENT_DKPID'		=> $raid['event_dkpid'], 
			'RAID_DKPPOOL' 		=> $raid['dkpsys_name'], 
			'DKPTIMEUNIT'		=> $config['bbdkp_dkptimeunit'], 
			'TIMEUNIT' 			=> $config['bbdkp_timeunit'],
	 		'DKPPERTIME'		=> sprintf($user->lang['DKPPERTIME'], $config['bbdkp_dkptimeunit'], $config['bbdkp_timeunit'] ), 
			'ITEM_VALUE'		=> $item_value, 
			'ITEMDECAYVALUE'	=> $item_decay,
			'ITEMTOTAL'			=> $item_total,
							  	 							  	 
			'S_MEMBERLIST_OPTIONS'  	=> $s_memberlist_options, 
							  	 
			// raid start day
			'S_RAIDSTARTDATE_DAY_OPTIONS'	=> $s_raid_day_options,
			'S_RAIDSTARTDATE_MONTH_OPTIONS'	=> $s_raid_month_options,
			'S_RAIDSTARTDATE_YEAR_OPTIONS'	=> $s_raid_year_options,

			// raid start day
			'S_RAIDENDDATE_DAY_OPTIONS'		=> $s_raidend_day_options,
			'S_RAIDENDDATE_MONTH_OPTIONS'	=> $s_raidend_month_options,
			'S_RAIDENDDATE_YEAR_OPTIONS'	=> $s_raidend_year_options,
							  	 
			//start
			'S_RAIDSTART_H_OPTIONS'		=> $s_raid_hh_options,
			'S_RAIDSTART_MI_OPTIONS'	=> $s_raid_mi_options,
			'S_RAIDSTART_S_OPTIONS'		=> $s_raid_s_options,
			
			//end
			'S_RAIDEND_H_OPTIONS'		=> $s_raidend_hh_options,
			'S_RAIDEND_MI_OPTIONS'		=> $s_raidend_mi_options,
			'S_RAIDEND_S_OPTIONS'		=> $s_raidend_s_options,

			// attendees			
			'O_NAME' 			  => $current_order ['uri'] [0], 
			'O_RAIDVALUE' 		  => $current_order ['uri'] [1],
			'O_TIMEVALUE' 		  => $current_order ['uri'] [2],
			'O_ZSVALUE' 		  => $current_order ['uri'] [3],
			'O_DECAYVALUE' 		  => $current_order ['uri'] [4],
			'O_TOTALVALUE' 		  => $current_order ['uri'] [5], 
			
			//items			
			'O_BUYER' 		  	  => $icurrent_order ['uri'] [0],
			'O_ITEMNAME' 		  => $icurrent_order ['uri'] [1],
			'O_ITEMTOTAL' 		  => $icurrent_order ['uri'] [2], 

			'LISTRAIDS_FOOTCOUNT' => sprintf ( $user->lang ['LISTATTENDEES_FOOTCOUNT'], $countattendees),
			'ITEMSFOOTCOUNT' => sprintf ( $user->lang['RAIDITEMS_FOOTCOUNT'], $number_items),
							  	 
			'L_DATE' => $user->lang ['DATE'] . ' dd/mm/yyyy', 
			'L_TIME' => $user->lang ['TIME'] . ' hh:mm:ss', 
			
			'ADDEDBY'	 		=> sprintf ( $user->lang ['ADDED_BY'], $raid['raid_added_by']),
		  	'UPDATEDBY' 		=> ($raid['raid_updated_by'] != ' ') ? sprintf ( $user->lang ['UPDATED_BY'], $raid['raid_updated_by']) : '..',

			//switches
			'S_SHOWADDATTENDEE'	=> ($s_memberlist_options == '') ? false: true, 
			'S_EDITRAID'		=> true, 
			'S_SHOWZS' 			=> ($config['bbdkp_zerosum'] == '1') ? true : false, 
			'S_SHOWTIME' 		=> ($config['bbdkp_timebased'] == '1') ? true : false,
			'S_SHOWDECAY' 		=> ($config['bbdkp_decay'] == '1') ? true : false,
							  	 							  	 
			'S_ADDRAIDER'   	=> false,
			'S_SHOWITEMPANE' 	=> ($number_items > 0 ) ? true : false,				  
			
			// Javascript messages
			'MSG_ATTENDEES_EMPTY' => $user->lang ['FV_REQUIRED_ATTENDEES'], 
			
			));
				
	}
	
	/**
	 * lists all raids
	 * 
	 */
	private function listraids()
	{
		global $db, $user, $config, $template, $phpbb_admin_path, $phpEx;
		// add dkpsys button redirect
		$showadd = (isset($_POST['raidadd'])) ? true : false;
        if($showadd)
        {
			redirect(append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_raid&amp;mode=addraid"));            		
         	break;
        }
            	
		/***  DKPSYS drop-down query ***/
        $dkpsys_id = 0;
		$sql = 'SELECT dkpsys_id, dkpsys_name , dkpsys_default 
                FROM ' . DKPSYS_TABLE . ' a , ' . EVENTS_TABLE . ' b 
				WHERE a.dkpsys_id = b.event_dkpid 
				AND b.event_status = 1  
				GROUP BY dkpsys_id, dkpsys_name, dkpsys_default';
		$result = $db->sql_query ( $sql );
		
		$submit = (isset ( $_POST ['dkpsys_id'] ) || isset ( $_GET ['dkpsys_id'] ) ) ? true : false;
		if ($submit)
		{
			$dkpsys_id = request_var ( 'dkpsys_id', 0 );
		} 
		else 
		{
			while ( $row = $db->sql_fetchrow ( $result ) ) 
			{
				if($row['dkpsys_default'] == "Y"  )
				{
					$dkpsys_id = $row['dkpsys_id'];
				}
			}
			
			if ($dkpsys_id == 0)
			{
				$result = $db->sql_query_limit ( $sql, 1 );
				while ( $row = $db->sql_fetchrow ( $result ) ) 
				{
					$dkpsys_id = $row['dkpsys_id'];
				}
			}
		}
		
		$result = $db->sql_query ( $sql );
		while ( $row = $db->sql_fetchrow ( $result ) ) 
		{
			$template->assign_block_vars ( 'dkpsys_row', 
				array (
				'VALUE' => $row['dkpsys_id'], 
				'SELECTED' => ($row['dkpsys_id'] == $dkpsys_id) ? ' selected="selected"' : '', 
				'OPTION' => (! empty ( $row['dkpsys_name'] )) ? $row['dkpsys_name'] : '(None)' ) );
		}
		$db->sql_freeresult( $result );
		/***  end drop-down query ***/
		
		$sql_array = array (
			'SELECT' => ' count(*) as raidcount', 
			'FROM' => array (
				RAIDS_TABLE 		=> 'r' , 
				EVENTS_TABLE 		=> 'e',		
				), 
			'WHERE' => " r.event_id = e.event_id and e.event_dkpid = " . ( int ) $dkpsys_id, 
		);
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);
		$total_raids = (int) $db->sql_fetchfield('raidcount');
		$db->sql_freeresult ($result);
		
		$start = request_var ( 'start', 0, false );
		$sort_order = array (
				0 => array ('r.raid_start desc', 'raid_start' ),
				0 => array ('r.raid_end desc', 'raid_end' ),
				1 => array ('e.event_name', 'event_name desc' ), 
				2 => array ('r.raid_note', 'raid_note desc' ), 
				3 => array ('sum(ra.raid_value) desc', 'sum(ra.raid_value)' ),
				4 => array ('sum(ra.time_value) desc', 'sum(ra.time_value)' ),
				5 => array ('sum(ra.zs_value) desc', 'sum(ra.zs_value)' ),
				6 => array ('sum(ra.raiddecay) desc', 'sum(ra.raiddecay)' ),
				7 => array ('sum(ra.raid_value + ra.time_bonus  +ra.zerosum_bonus - ra.raid_decay) desc', 'sum(ra.raid_value + ra.time_bonus  +ra.zerosum_bonus - ra.raid_decay)' ),
				);
		
		$current_order = switch_order ( $sort_order );		
		$sql_array = array (
			'SELECT' => ' sum(ra.raid_value) as raid_value, sum(ra.time_bonus) as time_value, 
						  sum(ra.zerosum_bonus) as zs_value, sum(ra.raid_decay) as raiddecay, 
						  sum(ra.raid_value + ra.time_bonus  +ra.zerosum_bonus - ra.raid_decay) as total, 
						  e.event_dkpid, e.event_name,  
						  r.raid_id, r.raid_start, r.raid_end, r.raid_note, 
						  r.raid_added_by, r.raid_updated_by ', 
			'FROM' => array (
				RAID_DETAIL_TABLE	=> 'ra' ,
				RAIDS_TABLE 		=> 'r' , 
				EVENTS_TABLE 		=> 'e',		
				), 
			'WHERE' => "  ra.raid_id = r.raid_id AND e.event_status = 1 AND r.event_id = e.event_id AND e.event_dkpid = " . ( int ) $dkpsys_id,
			'GROUP_BY' => 'e.event_dkpid, e.event_name,  
						  r.raid_id,  r.raid_start, r.raid_end, r.raid_note, 
						  r.raid_added_by, r.raid_updated_by',	
			'ORDER_BY' => $current_order ['sql'], 
		);
		$sql = $db->sql_build_query('SELECT', $sql_array);

		$raids_result = $db->sql_query_limit ( $sql, $config ['bbdkp_user_rlimit'], $start );
		if (! $raids_result) 
		{
			trigger_error ( $user->lang['ERROR_INVALID_RAID'], E_USER_WARNING );
		}
		
		while ( $row = $db->sql_fetchrow ( $raids_result ) ) 
		{
			$template->assign_block_vars ( 'raids_row', array (
				'DATE' => (! empty ( $row['raid_start'] )) ? date ( $config ['bbdkp_date_format'], $row['raid_start'] ) : '&nbsp;', 
				'NAME' => $row['event_name'], 
				'NOTE' => (! empty ( $row['raid_note'] )) ? $row['raid_note'] : '&nbsp;', 
				'RAIDVALUE'  => $row['raid_value'], 
				'TIMEVALUE'  => $row['time_value'],
				'ZSVALUE' 	 => $row['zs_value'],
				'DECAYVALUE' => $row['raiddecay'], 
				'TOTAL' 	 => $row['total'], 
				'U_VIEW_RAID' => append_sid ( "index.$phpEx?i=dkp_raid&amp;mode=editraid&amp;" . URI_RAID . "={$row['raid_id']}" ), 
				'U_COPY_RAID' => append_sid ( "index.$phpEx?i=dkp_raid&amp;mode=listraids&amp;" . URI_RAID . "={$row['raid_id']}" ),
				)
			);
		}
		
		$template->assign_vars ( array (
			'L_TITLE' 			  => $user->lang ['ACP_LISTRAIDS'], 
			'L_EXPLAIN' 		  => $user->lang ['ACP_LISTRAIDS_EXPLAIN'], 
			'O_DATE' 			  => $current_order ['uri'] [0], 
			'O_NAME' 			  => $current_order ['uri'] [1], 
			'O_NOTE' 			  => $current_order ['uri'] [2], 
			'O_RAIDVALUE' 		  => $current_order ['uri'] [3],
			'O_TIMEVALUE' 		  => $current_order ['uri'] [4],
			'O_ZSVALUE' 		  => $current_order ['uri'] [5],
			'O_DECAYVALUE' 		  => $current_order ['uri'] [6],
			'O_TOTALVALUE' 		  => $current_order ['uri'] [7], 
			'S_SHOWTIME' 		  => ($config['bbdkp_timebased'] == '1') ? true : false,
			'S_SHOWZS' 			  => ($config['bbdkp_zerosum'] == '1') ? true : false, 
			'S_SHOWDECAY' 		  => ($config['bbdkp_decay'] == '1') ? true : false,
			'U_LIST_RAIDS' 		  => append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_raid&amp;mode=listraids&amp;dkpsys_id=". $dkpsys_id ), 
			'START' 			  => $start, 
			'LISTRAIDS_FOOTCOUNT' => sprintf ( $user->lang ['LISTRAIDS_FOOTCOUNT'], $total_raids, $config ['bbdkp_user_rlimit'] ), 
			'RAID_PAGINATION' 	  => generate_pagination ( append_sid 
					( "{$phpbb_admin_path}index.$phpEx", "i=dkp_raid&amp;mode=listraids&amp;dkpsys_id=". $dkpsys_id ."&amp;o=" . $current_order ['uri'] ['current']) , 
					$total_raids, $config ['bbdkp_user_rlimit'], $start, true ), 
			'ICON_RCOPY'		  => '<img src="' . $phpbb_admin_path . 'images/file_new.gif" alt="' . $user->lang['DUPLICATE_RAID'] . '" title="' . $user->lang['DUPLICATE_RAID'] . '" />',
			));
			
	}
	
	/**
	 * duplicates a passed raid without its attached loot.   
	 *
	 * @param int $raid_id
	 */
	private function duplicate_raid($raid_id)
	{
		global $db, $user, $config, $phpbb_admin_path, $phpEx;
		$sql_array = array (
			'SELECT' => '  e.event_dkpid, e.event_name, r.event_id, r.raid_note, r.raid_start, r.raid_end, r.raid_added_by, r.raid_updated_by', 
			'FROM' => array (
				RAIDS_TABLE 		=> 'r' , 
				EVENTS_TABLE 		=> 'e',		
			), 
			'WHERE' => " e.event_id = r.event_id AND r.raid_id=" . (int) $raid_id, 
		);
		
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query ($sql);
		while ( $row = $db->sql_fetchrow ( $result ) ) 
		{
			$dkpsys_id = $row['event_dkpid'];
			$raid = array (
				'event_id' 			=> $row['event_id'],
				'event_dkpid' 		=> $row['event_dkpid'],
				'event_name'		=> $row['event_name'],
				'raid_note' 		=> $user->lang['DUPLICATED'] . ': ' . $row['raid_note'], 
				'raid_start' 		=> $row['raid_start'],
				'raid_end' 			=> $row['raid_end'], 
				'raid_added_by' 	=> $row['raid_added_by'], 
				'raid_updated_by' 	=> $row['raid_updated_by']);
		}
		$db->sql_freeresult ($result);
		
		$sql_array = array (
			'SELECT' => ' ra.member_id, ra.raid_value, ra.time_bonus, ra.raid_decay', 
			'FROM' => array (
				RAID_DETAIL_TABLE 	=> 'ra' ,
				), 
			'WHERE' => " ra.raid_id = " . (int) $raid_id, 
		);
		
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query ($sql);
		$raid_details= array();
		while ( $row = $db->sql_fetchrow ( $result ) ) 
		{
			$raid_details[] = array (
				'member_id' 		=> (int)  $row['member_id'], 
				'raid_value' 		=> (float) $row['raid_value'],
				'time_bonus' 		=> (float) $row['time_bonus'], 
				'raid_decay' 		=> (float) $row['raid_decay']);
		}
		$db->sql_freeresult ($result);
		
		/*
		 * start inserting raid
		 */
		$db->sql_transaction('begin');
		//
		// Insert the raid
		// raid id is auto-increment so it is increased automatically
		//
		$query = $db->sql_build_array ( 'INSERT', array (
				'event_id' 		=> (int) $raid['event_id'], 
				'raid_note' 	=> (string) $raid['raid_note'], 
				'raid_start' 	=> (int) $raid['raid_start'],
				'raid_end' 		=> (int) $raid['raid_end'], 
				'raid_added_by' => (string) $user->data['username']) 
		);
			
		$db->sql_query ( "INSERT INTO " . RAIDS_TABLE . $query );
		$raid ['raid_id'] = $db->sql_nextid();

		// insert the attendees
		if(sizeof($raid_details) > 0)
    	{
	    	$line = array();
	        foreach ( $raid_details as $raid_detail )
	        {
	            $line[] = array(
	                'raid_id'      => (int)   $raid['raid_id'],
	                'member_id'    => (int)   $raid_detail['member_id'],
		            'raid_value'   => (float) $raid_detail['raid_value'],
		            'time_bonus'   => (float) $raid_detail['time_bonus'],
	            	'raid_decay'   => (float) $raid_detail['raid_decay']
					);

				$this->add_dkp ($raid_detail['raid_value'], $raid_detail['time_bonus'], $raid['raid_start'] , $raid['event_dkpid'] , $raid_detail['member_id']);
	        }
	        $db->sql_multi_insert(RAID_DETAIL_TABLE, $line);
    	}
    	
    	$db->sql_transaction('commit');
    	meta_refresh(1, append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_raid&amp;mode=listraids&amp;dkpsys_id=". $dkpsys_id ));

    	$success_message = sprintf ( $user->lang ['ADMIN_DUPLICATE_RAID_SUCCESS'], 
		$user->format_date($this->time), $raid['event_name'] ) . '<br />';
		trigger_error($success_message);
	}
	
	/**
	 * adds raid to database
	 * 
	 * 
	 */
	public function addraid()
	{
		global $db, $user, $config, $template, $phpEx ;
		if (confirm_box ( true )) 
		{
			// recall hidden vars
			$raid = array(
				'raid_note' 		=> utf8_normalize_nfc (request_var ( 'hidden_raid_note', ' ', true )), 
				'raid_event'		=> utf8_normalize_nfc (request_var ( 'hidden_raid_name', ' ', true )), 
				'raid_value' 		=> request_var ('hidden_raid_value', 0.00 ), 
				'raid_timebonus'	=> request_var ('hidden_raid_timebonus', 0.00 ),
				'raid_start' 		=> request_var ('hidden_startraid_date', 0), 
				'raid_end'			=> request_var ('hidden_endraid_date', 0),
				'event_id' 			=> request_var ('hidden_event_id', 0),
				'raid_attendees' 	=> request_var ('hidden_raid_attendees', array ( 0 => 0 )), 
			); 
			
			// Get event info
			$sql = "SELECT event_id, event_name, event_dkpid, event_value FROM " . EVENTS_TABLE . "  WHERE 
	                event_id = " . $raid['event_id'];
			$result = $db->sql_query ( $sql );
			while ( $row = $db->sql_fetchrow ($result) ) 
			{
				if ($raid['raid_value'] == 0.00)
				{
					$raid['raid_value'] = max ( $row['event_value'], 0.00 );
				}
				
				$raid['event_dkpid'] = $row['event_dkpid'];
				$raid['event_name'] = $row['event_name'];
			}
			$db->sql_freeresult( $result );
			
			/*
			 * start transaction
			 */
			$db->sql_transaction('begin');
			//
			// Insert the raid
			// raid id is auto-increment so it is increased automatically
			//
			$query = $db->sql_build_array ( 'INSERT', array (
					'event_id' 		=> (int) $raid['event_id'], 
					'raid_start' 	=> (int) $raid['raid_start'],
					'raid_end' 		=> (int) $raid['raid_end'], 
					'raid_note' 	=> (string) $raid['raid_note'], 
					'raid_added_by' => (string) $user->data['username'] ) 
			);
			
			$db->sql_query ( "INSERT INTO " . RAIDS_TABLE . $query );
			$raid ['raid_id'] = $db->sql_nextid();
			// Attendee handling
			
			// Insert the raid detail
			$raiddetail = $this->add_raiddetail($raid);
	
			//
			// pass the raidmembers array, raid value, and dkp pool.
			foreach ( (array) $raid['raid_attendees'] as $member_id )
			{
				$this->add_dkp ($raid['raid_value'], $raid['raid_timebonus'], $raid['raid_start'] , $raid['event_dkpid'] , $member_id);
			}
			
			// commit
			$db->sql_transaction('commit');
			
			//
			// Logging
			//
			$log_action = array (
				'header' => 'L_ACTION_RAID_ADDED', 
				'id' 			=> $raid ['raid_id'], 
				'L_EVENT' 		=> $raid['event_name'], 
				'L_ATTENDEES' 	=> implode ( ', ', $raid ['raid_attendees'] ), 
				'L_NOTE' 		=> $raid ['raid_note'], 
				'L_VALUE' 		=> $raid['raid_value'], 
				'L_ADDED_BY' 	=> $user->data ['username']);
			
			$this->log_insert (array(
				'log_type' 		=> $log_action ['header'], 
				'log_action' 	=> $log_action ));
			
			//
			// Success message
			//
			$success_message = sprintf ( $user->lang ['ADMIN_ADD_RAID_SUCCESS'], 
				$user->format_date($this->time), $raid['event_name'] ) . '<br />';
				
			//
			// Update active / inactive player status if needed
			//
			if ($config ['bbdkp_hide_inactive'] == 1) 
			{
				$success_message .= '<br /><br />' . $user->lang ['ADMIN_RAID_SUCCESS_HIDEINACTIVE'];
				$success_message .= ' ' . (($this->update_player_status ( $raid['event_dkpid'] )) ? 
					strtolower ( $user->lang ['DONE'] ) : strtolower ( $user->lang ['ERROR'] ));
			}

			//show message and redirect to raid after 3 seconds
			$link = append_sid ( "index.$phpEx?i=dkp_raid&amp;mode=editraid&amp;" . URI_RAID . "={$raid ['raid_id']}" );
	    	meta_refresh(1, $link);
			trigger_error ( $success_message . $this->link, E_USER_NOTICE );
				
		}
		else
		{
			$event_id = request_var ( 'event_id', 0 );
			if (($event_id == 0)) 
			{
				trigger_error ( $user->lang ['ERROR_INVALID_EVENT_PROVIDED'], E_USER_WARNING );
			}
			
			// store raidinfo as hidden vars
			
			$s_hidden_fields = build_hidden_fields(array(
					'event_id'					=> $event_id, //for when user says no
					'hidden_raid_note' 			=> utf8_normalize_nfc ( request_var ( 'raid_note', ' ', true ) ), 
					'hidden_event_id' 			=> $event_id,
					'hidden_raid_event'			=> utf8_normalize_nfc ( request_var ( 'event_name',	' ', true  ) ), 
					'hidden_raid_value' 		=> request_var ( 'raid_value', 0.00 ),
					'hidden_raid_timebonus' 	=> request_var ( 'time_bonus', 0.00 ),
					'hidden_startraid_date' 	=> mktime(request_var('sh', 0), request_var('smi', 0), request_var('ss', 0), 
					  					   			request_var('mo', 0), request_var('d', 0), request_var('Y', 0)), 
					'hidden_endraid_date' 		=> mktime(request_var('eh', 0), request_var('emi', 0), request_var('es', 0), 
					  					   			request_var('emo', 0), request_var('ed', 0), request_var('eY', 0)), 
					'hidden_raid_attendees' 	=> request_var ( 'raid_attendees', array ( 0 => 0 )), 
					'add'    					=> true, 
			)
			);
			
			$sql='SELECT event_name FROM ' . EVENTS_TABLE . ' WHERE event_id = ' . $event_id; 
			$result = $db->sql_query($sql);
			$eventname = (string) $db->sql_fetchfield('event_name');
			$db->sql_freeresult($result);
			
			confirm_box(false, sprintf($user->lang['CONFIRM_CREATE_RAID'], $eventname) , $s_hidden_fields);				
			
		}
		
		
	}
	
	/**
	 * update a raid
	 * 
	 * @param int $raid_id the raid to update
	 */
	public function updateraid($raid_id)
	{
		global $db, $user, $config, $template,$phpbb_admin_path, $phpEx;
		if(!check_form_key('acp_dkp_addraid'))
		{
			trigger_error($user->lang['FV_FORMVALIDATION'], E_USER_WARNING);	
		}
		
		// get old raid data
		$sql_array = array (
			'SELECT' => ' e.event_dkpid, e.event_name, e.event_id,   
						  r.raid_id, r.raid_start, r.raid_end, r.raid_note, 
						  r.raid_added_by, r.raid_updated_by ', 
			'FROM' => array (
				RAIDS_TABLE 		=> 'r' , 
				EVENTS_TABLE 		=> 'e',		
				), 
			'WHERE' => "  r.event_id = e.event_id and r.raid_id = " . (int) $raid_id, 
		);
		$sql = $db->sql_build_query('SELECT', $sql_array);
		
		$result = $db->sql_query ($sql);
		while ( $row = $db->sql_fetchrow ( $result ) ) 
		{
			$old_raid = array (
				'event_id' 		=> (int) $row['event_id'], 
				'event_dkpid' 	=> (int) $row['event_dkpid'],
			 	'event_name' 	=> (string) $row['event_name'],
				'raid_note' 	=> (string) $row['raid_note'], 
				'raid_start' 	=> (int) $row['raid_start'],
				'raid_end' 		=> (int) $row['raid_end'],
				'raid_added_by' => (int) $row['raid_added_by'],
			);
		}
		$db->sql_freeresult( $result );
		
		// get updated data		
		$raid = array (
			'event_id' 	 => request_var ( 'event_id', $old_raid['event_id'] ), 
			'raid_value' => request_var ( 'raid_value', 0.00 ),
			'time_bonus' => request_var ( 'time_bonus', 0.00 ),  
			'raid_note'  => utf8_normalize_nfc ( request_var ( 'raid_note', ' ', true ) ), 
			'raid_start' => mktime(request_var('sh', 0), request_var('smi', 0), request_var('ss', 0), 
			  					request_var('mo', 0), request_var('d', 0), request_var('Y', 0)), 
			'raid_end' 	 => mktime(request_var('eh', 0), request_var('emi', 0), request_var('es', 0), 
								request_var('emo', 0), request_var('ed', 0), request_var('eY', 0)), 			  					
		);
		
		$db->sql_transaction('begin');

		// Update the raid
		$query = $db->sql_build_array ( 'UPDATE', array (
			'event_id' 			=> (int) $raid['event_id'],
			'raid_start' 		=> $raid['raid_start'],
			'raid_end' 			=> $raid['raid_end'],
			'raid_note' 		=> $raid['raid_note'], 
			'raid_updated_by' 	=> $user->data ['username'] ) );
		$db->sql_query ( 'UPDATE ' . RAIDS_TABLE . ' SET ' . $query . " WHERE raid_id = " . (int) $raid_id );
		
		// update raiddetail
		//get old data
		$sql = ' SELECT member_id, raid_value, time_bonus, raid_decay FROM ' . 
			RAID_DETAIL_TABLE . ' WHERE raid_id = ' . (int) $raid_id . ' ORDER BY member_id' ;
		$result = $db->sql_query($sql);
		if ($result)
		{
			while ( $row = $db->sql_fetchrow ($result)) 
			{
				$old_raid_value = (float) - $row['raid_value'];
				$old_time_bonus = (float) - $row['time_bonus'];
				$old_total = $old_raid_value + $old_time_bonus; 
				
				$query = $db->sql_build_array ( 'UPDATE', array (
					'raid_value' 		=> $raid['raid_value'],
					'time_bonus' 		=> $raid['time_bonus'], 
				));

				$sql = 'UPDATE ' . RAID_DETAIL_TABLE . ' SET ' . $query . " WHERE raid_id = " . 
				( int ) $raid_id . ' and member_id = ' . (int) $row['member_id'] ;
				$db->sql_query ($sql);

				$this->add_dkp($old_raid_value, $old_time_bonus, $raid['raid_start'], $old_raid['event_dkpid'], $row['member_id'], -1);
				$this->add_dkp($raid['raid_value'], $raid['time_bonus'], $raid['raid_start'], $old_raid['event_dkpid'], $row['member_id'], 1);
				
			}
		}
		
		$db->sql_transaction('commit');

		$db->sql_freeresult($result);
		//
		// Logging
		//
		$log_action = array (
			'header' => 'L_ACTION_RAID_UPDATED', 
			'id' => $raid_id, 
			'L_EVENT_BEFORE' => $old_raid ['event_id'], 
			'L_EVENT_AFTER'  => $raid['event_id'], 
			'L_UPDATED_BY' 	 => $user->data ['username'] );
		
		$this->log_insert ( array (
			'log_type' => $log_action ['header'], 
			'log_action' => $log_action ));
		
		//
		// Success message
		//
		$success_message = sprintf ( $user->lang ['ADMIN_UPDATE_RAID_SUCCESS'], request_var ( 'mo', ' ' ), request_var ( 'd', ' ' ), request_var ( 'Y', ' ' ), request_var ( 'event_id', 0 ));
		
		// Update player status if needed
		if ($config ['bbdkp_hide_inactive'] == 1) 
		{
			$success_message .= '<br /><br />' . $user->lang ['ADMIN_RAID_SUCCESS_HIDEINACTIVE'];
			$success_message .= ' ' . (($this->update_player_status ( $old_raid['event_dkpid'])) ? strtolower ( $user->lang ['DONE'] ) : 
			strtolower ( $user->lang ['ERROR'] ));
		}
		$this->link = '<br /><a href="' . append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_raid&amp;mode=editraid&amp;". URI_RAID . "=" .$raid_id ) . '"><h3>'.$user->lang['RETURN_RAID'].'</h3></a>';

		$link = append_sid ( "index.$phpEx?i=dkp_raid&amp;mode=editraid&amp;" . URI_RAID . "={$raid_id}" );
	   	meta_refresh(1, $link);
		trigger_error ( $success_message . $this->link, E_USER_NOTICE );
		
	}
	
	/** 
	 * 
	 * delete a raid
	 * 
	 * @param int $raid_id
	 */
	private function deleteraid($raid_id)
	{
		global $db, $user, $config, $template, $phpEx;
		
		if (confirm_box ( true )) 
		{
			$old_raid = request_var('raid', array('' => ''));

			// first remove cost of items and zerosum from this raid
			$this->remove_loot($old_raid);

			// loop raid detail and update dkp accounts, decrease earned, raidcount
			// then delete raid detail
			$sql = ' SELECT member_id, raid_value, time_bonus, zerosum_bonus, raid_decay FROM ' . RAID_DETAIL_TABLE . ' 
		             WHERE raid_id= ' . (int) $old_raid['raid_id'] . ' ORDER BY member_id' ;
			$result = $db->sql_query($sql);
			if ($result)
			{

				$members = array(); 
				while ( $row = $db->sql_fetchrow ($result)) 
				{
					$this->remove_dkp(
						$row['member_id'], $row['raid_value'], $row['time_bonus'], 
						$row['zerosum_bonus'], $old_raid['event_dkpid'], $row['raid_decay']  );							
					$members[] = $row['member_id']; 
				}
				
				// set new first and last raiddates, decrease raidcount
				foreach($members as $member_id)
				{
					$this->update_raiddate($member_id, $old_raid['event_dkpid']);
				}
				
				// delete raid detail
				$sql = 'DELETE FROM ' . RAID_DETAIL_TABLE . ' WHERE raid_id = ' . 
					$old_raid['raid_id']  . ' and ' . $db->sql_in_set('member_id', $members);
				$db->sql_query($sql);

			}

			// finally remove the raid itself
			$db->sql_query ( 'DELETE FROM ' . RAIDS_TABLE . " WHERE raid_id= " . ( int ) $old_raid['raid_id']  );
			
			// Logging
			$log_action = array (
				'header' => 
				'L_ACTION_RAID_DELETED', 
				'id' => $old_raid['raid_id'] , 
				'L_EVENT' => $old_raid ['event_id'], 
				);
			$this->log_insert ( array ('log_type' => $log_action ['header'], 'log_action' => $log_action ) );
			
			$success_message = $user->lang ['ADMIN_DELETE_RAID_SUCCESS'];
			
			// Update player status if needed
			if ($config ['bbdkp_hide_inactive'] == 1) 
			{
				$success_message .= '<br /><br />' . $user->lang ['ADMIN_RAID_SUCCESS_HIDEINACTIVE'];
				$success_message .= ' ' . (($this->update_player_status ( $old_raid['event_dkpid'] )) ? 
				strtolower ( $user->lang ['DONE'] ) : 
				strtolower ( $user->lang ['ERROR'] ));
			}
			
			trigger_error ( $success_message . $this->link, E_USER_NOTICE );
		
		} 
		else 
		{
			$sql_array = array (
				'SELECT' => ' e.event_id, event_dkpid, e.event_name, r.raid_start', 
				'FROM' => array (
					RAIDS_TABLE 		=> 'r' , 
					EVENTS_TABLE 		=> 'e',		
					), 
				'WHERE' => " r.event_id = e.event_id and r.raid_id=" . ( int ) $raid_id, 
			);
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query ($sql);
		
			while ( $row = $db->sql_fetchrow ( $result ) ) 
			{
				$old_raid = array (
					'event_id' 		=> $row['event_id'],
					'event_dkpid' 	=> $row['event_dkpid'], 
					'event_name' 	=> $row['event_name'], 
					'raid_id' 		=> $raid_id,
					'raid_start' 	=> $row['raid_start'],  
				);
			}
			$db->sql_freeresult( $result );
			
			$s_hidden_fields = build_hidden_fields ( array (
				'delete' 			=> true, 
				'raid'				=> $old_raid,
				));
			$template->assign_vars ( array ('S_HIDDEN_FIELDS' => $s_hidden_fields ) );
			
			confirm_box ( false, $user->lang ['CONFIRM_DELETE_RAID'], $s_hidden_fields );
		}
	
	}	
	
    /**
    * raid_detail handler : Insert raid detail
    * @param $members_array array of member_id
    * @param raid_value
    * @param time_bonus
    * @param $raid_id
    */
    private function add_raiddetail($raid)
    {
        if(sizeof($raid['raid_attendees']) == 0)
    	{
    		return;	
    	}
    	
        global $db;
        $raid_detail = array();
        foreach ( $raid['raid_attendees'] as $member_id )
        {
            $raid_detail[] = array(
                'raid_id'      => (int) $raid['raid_id'],
                'member_id'   => (int)  $member_id,
	            'raid_value'   => (float) $raid['raid_value'],
	            'time_bonus'   => (float) $raid['raid_timebonus'],
				);
        }
        $db->sql_multi_insert(RAID_DETAIL_TABLE, $raid_detail);
        return $raid_detail;
    }
   
	/**
	 * 
	 * this function adds attendee 
	 */ 
	private function addraider($raid_id)
	{
		if(!check_form_key('acp_dkp_addraid'))
		{
			trigger_error($user->lang['FV_FORMVALIDATION'], E_USER_WARNING);	
		}
		global $db; 
        $raid_value = request_var('raid_value', 0.00); 
        $time_bonus = request_var('time_bonus', 0.00); 
		$dkpid = request_var('hidden_dkpid', 0); 
		$member_id =  request_var('attendee_id', 0); 
		$raid_start = mktime(request_var('sh', 0), request_var('smi', 0), request_var('ss', 0), request_var('mo', 0), request_var('d', 0), request_var('Y', 0)); 
		
		$db->sql_transaction('begin');
		
		$raid_detail = array(
                'raid_id'      => (int) $raid_id,
                'member_id'   =>  $member_id,
	            'raid_value'   => $raid_value,
	            'time_bonus'   => $time_bonus,
				);
		$db->sql_multi_insert(RAID_DETAIL_TABLE, $raid_detail);

		$this->add_dkp ($raid_value, $time_bonus, $raid_start, $dkpid, $member_id);
					
		$db->sql_transaction('commit');
		
		return true;
	}


	/**
    * 
    */
	
	
	/**
     * function add_dkp : 
     * adds raid value as earned to each raider, 
     * increase raidcount if action = 1
     * set last and first raiddates for the attending raiders
	 *
	 * @param decimal $raid_value
	 * @param decimal $time_bonus
	 * @param int $raid_start
	 * @param int $dkpid
	 * @param int $member_id
	 * @param integer $action (-1 if delete, 0 if existing raid, +1 if new raid)
	 */
    public function add_dkp($raid_value, $time_bonus, $raid_start, $dkpid, $member_id, $action = 1)
    {
		global $db;
		
       // check if user has dkp record ?
		$sql = 'SELECT count(member_id) as present FROM ' . MEMBER_DKP_TABLE . '  
				WHERE member_id = ' . $member_id . ' 
				AND member_dkpid = ' . $dkpid;
		$result = $db->sql_query($sql);
        $present = (int) $db->sql_fetchfield('present', false, $result);
        $db->sql_freeresult($result);
	 	
        if ($present == 1)
        {
			$this->update_dkprecord($raid_value, $time_bonus, $raid_start, $dkpid, $member_id, $action); 
        }
        elseif ($present == 0)
        {
			$this->add_dkprecord($raid_value, $time_bonus, $raid_start, $dkpid, $member_id ); 
        }
    }
    
    /**
     * 
     * updates dkp record
     */
    
    /**
     * updates a dkp record
     *
     * @param decimal $raid_value
     * @param decimal $timebonus
     * @param int $raidstart
     * @param int $dkpid
     * @param int $member_id
     * @param integer $action (-1 if delete, 0 if existing raid, +1 if new raid)
     * @return boolean
     */
    private function update_dkprecord($raid_value, $timebonus, $raidstart, $dkpid, $member_id, $action = 1)
    {
    	global $db; 

    	$sql = 'SELECT member_firstraid, member_lastraid FROM ' . MEMBER_DKP_TABLE . ' WHERE member_id = ' . $member_id . ' AND  member_dkpid = ' . $dkpid;  
        $result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result) )  
		{
			$firstraid = (int) max(0, $row['member_firstraid']); 
			$lastraid = (int) max(0, $row['member_lastraid']); 
		}
		$db->sql_freeresult($result);
		
		$sql  = 'UPDATE ' . MEMBER_DKP_TABLE . ' 
		SET member_earned = member_earned + ' . (string) $raid_value . ' + ' . (string) $timebonus . ' , 
		member_raid_value = member_raid_value + ' . (string) $raid_value . ', 
		member_time_bonus = member_time_bonus + ' . (string) $timebonus . ', ';
		
		// update firstraid if it's later than this raid's starting time
		if ( $firstraid > $raidstart )
		{
		   $sql .= 'member_firstraid = ' . $raidstart . ', ';
		}
		
		// Do update their lastraid if it's earlier than this raid's starting time
		if ( $lastraid < $raidstart )
		{
		   $sql .= 'member_lastraid = ' . $raidstart. ', ';
		}
		
		switch($action)
		{
			case -1:
				$sql .= ' member_raidcount = member_raidcount - 1 ';
				break;
			case 0;
				break;
			case 1:
				$sql .= ' member_raidcount = member_raidcount + 1 ';
				break;
		}
		
		$sql .' WHERE member_dkpid = ' . (int)  $dkpid . '
		AND member_id = ' . (int) $member_id;
       $db->sql_query($sql);
       return true;
    }
    
    /**
     * adds dkp record
     *
     * @param decimal $raid_value
     * @param decimal $timebonus
     * @param int $raidstart
     * @param int $dkpid
     * @param int $member_id
     * @return boolean
     */
    private function add_dkprecord($raid_value, $timebonus, $raidstart, $dkpid, $member_id)
    {
    	
    	global $db, $user; 
         // insert new dkp record
         $query = $db->sql_build_array('INSERT', array(
            'member_dkpid'       =>  $dkpid,
            'member_id'          => $member_id,
            'member_earned'      => (float) $raid_value + (float) $timebonus,
         	'member_raid_value'  => (float) $raid_value ,
         	'member_time_bonus'  => (float) $timebonus ,
            'member_status'      => 1,
            'member_firstraid'   => $raidstart,
            'member_lastraid'    => $raidstart,
            'member_raidcount'   => 1
          ));
         $db->sql_query('INSERT INTO ' . MEMBER_DKP_TABLE . $query);
         return true;
    	
    }
 

	/**
	 * this function deletes 1 attendee from a raid 
	 * 
	 * dkp account is then updated
	 * 
	 */ 
	private function deleteraider($raid_id, $attendee_id)
	{
		global $db, $user, $config, $template, $phpbb_admin_path, $phpEx;
		$link = '<br /><a href="' . append_sid ("{$phpbb_admin_path}index.$phpEx", "i=dkp_raid&amp;mode=editraid&amp;". URI_RAID . "=" .$raid_id) . '"><h3>'.$user->lang['RETURN_RAID'].'</h3></a>';
		 
		if (confirm_box(true))
		{
			//recall vars 
			$raid_id = request_var('raid_idx', 0); 
			$member_id = request_var('attendee', 0);  

			//get old raid info
			$sql_array = array (
			'SELECT' => ' e.event_dkpid, ra.raid_value, ra.time_bonus, ra.zerosum_bonus, ra.raid_decay ', 
			'FROM' => array (
				EVENTS_TABLE 		=> 'e',		
				RAIDS_TABLE 		=> 'r' , 
				RAID_DETAIL_TABLE 	=> 'ra' , 
				), 
			'WHERE' => "  r.event_id = e.event_id and r.raid_id = " . (int) $raid_id . ' and r.raid_id  = ra.raid_id ',  
			);
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$oraid_value = $otime_bonus =$zerosum = 0.00;
			$result = $db->sql_query ($sql);
			while ( $row = $db->sql_fetchrow ( $result ) ) 
			{
				$dkpid = $row['event_dkpid']; 
				$oraid_value = $row['raid_value'] + 0.00; 
				$otime_bonus = $row['time_bonus'] + 0.00;
				$zerosum = $row['zerosum_bonus'] + 0.00 ; 
				$decay = $row['raid_decay'] + 0.00 ;
			}
			$db->sql_freeresult( $result );
			
			$db->sql_transaction('begin');
			
			// amend their dkp account
			$this->remove_dkp($member_id, $oraid_value, $otime_bonus, $zerosum, $dkpid, $decay);

			// delete from raiddetail
			$sql = 'DELETE FROM ' . RAID_DETAIL_TABLE . ' WHERE raid_id= ' . $raid_id . ' and member_id = ' . $member_id ;  
			$db->sql_query($sql);

			// update last & firstdates			
			$this->update_raiddate($member_id, $dkpid);
			
			$db->sql_transaction('commit');
			
			trigger_error( sprintf( $user->lang['ADMIN_RAID_ATTENDEE_DELETED_SUCCESS'],  utf8_normalize_nfc(request_var('attendeename', '', true)) , $raid_id) . $link, E_USER_WARNING);
		}
		else
		{
			//check if player got loot, if true refuse deletion 
			$sql = 'SELECT count(*) as countitems FROM ' . RAID_ITEMS_TABLE . ' where member_id = ' . $attendee_id . ' and raid_id = ' .  $raid_id; 
			$result = $db->sql_query($sql);
			$countitems = (int) $db->sql_fetchfield('countitems');
			$db->sql_freeresult($result);
			if ($countitems > 0)
			{
				trigger_error( sprintf( $user->lang['ADMIN_RAID_ATTENDEE_DELETED_FAILED'],  utf8_normalize_nfc(request_var('attendeename', '', true)) , $raid_id) . $link, E_USER_WARNING);				
			}
			
			// select vars to be passed to confirm box
			$sql = 'SELECT member_name from ' . MEMBER_LIST_TABLE . ' where member_id = ' . (int) $attendee_id; 
			$result = $db->sql_query($sql);
			$member_name = (string) $db->sql_fetchfield('member_name');
			$db->sql_freeresult($result);
			
			$s_hidden_fields = build_hidden_fields(array(
				'deleteraider'	=> true,
				'raid_idx'		=> $raid_id,
				'attendee'		=> $attendee_id,
				'attendeename'	=> $member_name,
				)
			);
			confirm_box(false, sprintf($user->lang['CONFIRM_DELETE_ATTENDEE'], $member_name, $raid_id), $s_hidden_fields);
		}
		
		return true;
	}
	
	/**
    * remove_dkp : removes raid value from dkp account
    * called when deleting raid
    *
    * @param $members_array
    * @param $raid_value
    * @param $dkpid
    */
    private function remove_dkp($member_id, $oraid_value, $otime_bonus, $ozerozum, $dkpid, $odecay)
    {
        global $db, $config;
		$sql = 'SELECT member_raidcount, member_raid_value, member_time_bonus, member_zerosum_bonus, member_earned, member_raid_decay   
		FROM ' . MEMBER_DKP_TABLE . ' WHERE member_id = ' . $member_id . ' AND  member_dkpid = ' . $dkpid;  
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result) )  
		{
		 	$xraid_value 		= $row['member_raid_value'];
		 	$xtime_bonus 		= $row['member_time_bonus'];
		 	$xzerosum 	 		= $row['member_zerosum_bonus'];
		 	$xdecay 	 		= $row['member_raid_decay']; 
		 	$xearned 	 		= $row['member_earned'];
		 	$member_raidcount 	= $row['member_raidcount'];
		}
        $db->sql_freeresult($result);
        
		$raid_value = $xraid_value - $oraid_value;
		$time_bonus = $xtime_bonus - $otime_bonus;
		$zerosum = $xzerosum - $ozerozum;
		
		// give the deleted zero sum amounts back to the guildbank
		$db->sql_query ( 'UPDATE ' . MEMBER_DKP_TABLE . ' SET  
			member_zerosum_bonus = member_zerosum_bonus  + ' . $ozerozum . ', 
			member_earned = member_earned + ' . $ozerozum . ' 
			where member_dkpid = ' . (int) $dkpid . ' 
			and member_id =  ' . $config['bbdkp_bankerid'] );
        
		$earned = $xearned - $oraid_value - $otime_bonus - $ozerozum; 
		$decay = $xdecay - $odecay;
		$newraidcount = max(0, $member_raidcount - 1);
		
		$query = $db->sql_build_array ( 'UPDATE', array (
		'member_raidcount' 		=> $newraidcount,
		'member_raid_value' 	=> $raid_value, 
		'member_time_bonus' 	=> $time_bonus, 
		'member_zerosum_bonus' 	=> $zerosum, 
		'member_earned' 		=> $earned,
		'member_raid_decay'		=> $decay,
        ));
          
		$db->sql_query ( 'UPDATE ' . MEMBER_DKP_TABLE . ' SET ' . $query . ' 
			WHERE member_dkpid = ' . (int) $dkpid . ' and member_id =  ' . $member_id  );
        $db->sql_freeresult($result);
    }
  
    
	/**
    * update_raiddate : update dkp record first and lastraids
    *
    * @param $member_id
    * @param $dkpid
    */
    private function update_raiddate($member_id, $dkpid)
    {
        global $db, $user;
        
        // get first & last raids
        $sql_array = array (
		'SELECT' => 'MIN(r.raid_start) AS member_firstraid, MAX(r.raid_start) AS member_lastraid, ra.member_id ', 
		'FROM' => array (
			RAIDS_TABLE => 'r', 
			RAID_DETAIL_TABLE => 'ra' ,
			EVENTS_TABLE => 'e' 
			), 
		'WHERE' => ' ra.raid_id = r.raid_id 
					AND r.event_id = e.event_id 
					AND e.event_dkpid = ' . $dkpid . '
					AND ra.member_id  = ' . $member_id,   
		'GROUP_BY' => 'member_id', 
		);
		
		$sql = $db->sql_build_query ( 'SELECT', $sql_array );
		$result = $db->sql_query ( $sql );
		$member_firstraid = 0;
		$member_lastraid = 0;
		while ( $row = $db->sql_fetchrow ($result)) 
		{
			$member_firstraid = $row['member_firstraid'];
			$member_lastraid = $row['member_lastraid'];  
		}
		$db->sql_freeresult ($result);
	
		$first_raid = ( isset($member_firstraid) ? $member_firstraid : 0 );
        $last_raid = ( isset($member_lastraid) ? $member_lastraid : 0 );  

         $query = $db->sql_build_array ( 'UPDATE', array (
			'member_firstraid' 		=> $first_raid,
			'member_lastraid' 		=> $last_raid, 
         ));
          
		$db->sql_query ( 'UPDATE ' . MEMBER_DKP_TABLE . ' SET ' . $query . ' 
	            WHERE member_id = ' . $member_id . ' 
	            AND  member_dkpid = ' . $dkpid);
    }
    
  
    	
	/**
	 * this function shows raider editform 
	 * 
	 * @param $raid_id the raid to edit
	 * @param $attendee_id  the raider to edit
	 */ 
	private function editraider($raid_id, $attendee_id)
	{
		global $db, $user, $config, $template, $phpbb_admin_path, $phpEx;
		if (isset ( $_POST ['editraider'] ) )
		{
			// update his raid record
			$attendee_id = request_var('hidden_memberid', 0); 
			$raid_id = request_var ('hidden_raidid', 0);
			$dkpid = request_var ('hidden_dkpid', 0);
			
			$old_raid_value = request_var('old_raid_value', 0.00);
			$old_time_bonus = request_var('old_time_bonus', 0.00);
			$old_zerosum_bonus = request_var('old_zerosum_bonus', 0.00);
			$old_raid_decay = request_var('old_raid_decay', 0.00);

			$raid_value = request_var('raid_value', 0.00);
			$time_bonus = request_var('time_bonus', 0.00);
			$zerosum_bonus = request_var('zerosum_bonus', 0.00);
			$raid_decay = request_var('raid_decay', 0.00);
			
			$d_raid_value = $old_raid_value - $raid_value;
			$d_time_bonus = $old_time_bonus - $time_bonus;
			$d_zerosum_bonus = $old_zerosum_bonus - $zerosum_bonus;
			$d_tot = $d_raid_value + $d_time_bonus + $d_zerosum_bonus; 
			$d_raid_decay = $old_raid_decay - $raid_decay;
			
			$query = $db->sql_build_array ( 'UPDATE', array (
				'raid_value' 		=> $raid_value,
				'time_bonus' 		=> $time_bonus, 
				'zerosum_bonus' 	=> $zerosum_bonus,
				'raid_decay' 		=> $raid_decay
			));
			
			$db->sql_transaction('begin');
			
			$db->sql_query ( 'UPDATE ' . RAID_DETAIL_TABLE . ' SET ' . $query . " WHERE raid_id = " . ( int ) $raid_id . ' and member_id = ' . (int) $attendee_id );

			// update his dkp account		
            $sql  = 'UPDATE ' . MEMBER_DKP_TABLE . ' 
	         SET member_raid_value = member_raid_value + ' .  (string) $d_raid_value. ', 
	         member_time_bonus = member_time_bonus + ' . (string)  $d_time_bonus . ', member_zerosum_bonus = member_zerosum_bonus + ' . (string)  $d_zerosum_bonus . ',
	         member_earned = member_earned + ' . (string) $d_tot . ' , member_raid_decay = member_raid_decay + ' . (string) $d_raid_decay  . ' 	          
	         WHERE member_dkpid = ' . (string)  $dkpid . ' AND member_id = ' . (string) $attendee_id;
            
            $db->sql_query($sql);
			$db->sql_transaction('commit');
			$this->displayraid($raid_id);
			return true;
		}
		
		$sql_array = array(
	    'SELECT'    => 	' e.event_dkpid, e.event_name,r.raid_start, ra.member_id, l.member_name, ra.raid_value, ra.time_bonus, ra.zerosum_bonus, ra.raid_decay ', 
	    'FROM'      => array(
				MEMBER_LIST_TABLE 	=> 'l',
				RAID_DETAIL_TABLE   => 'ra',
				RAIDS_TABLE   		=> 'r',
				EVENTS_TABLE   		=> 'e',
					),
		'WHERE'		=> ' e.event_id = e.event_id and r.raid_id = ra.raid_id and l.member_id = ra.member_id and ra.raid_id = ' . (int) $raid_id . ' and  ra.member_id = ' . (int) $attendee_id, 
	    );
	    
	    $sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);
		while ( $row = $db->sql_fetchrow($result) )
		{
			$event_dkpid = $row['event_dkpid'];
			$member_name=$row['member_name'];
			$raid_value=$row['raid_value'];
			$time_bonus=$row['time_bonus'];
			$zerosum_bonus=$row['zerosum_bonus'];
			$raid_decay=$row['raid_decay'];
			$eventname=$row['event_name'];
			$raidstart=$row['raid_start'];
		}
		$db->sql_freeresult($result);
		
		$template->assign_vars ( array (
			'U_BACK'			=> append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_raid&amp;mode=editraid&amp;". URI_RAID . "=" .$raid_id ),
			'EVENT_DKPID'		=> $event_dkpid, 
			'MEMBERID'			=> $attendee_id,
			'MEMBERNAME'		=> $member_name,
			'RAID_VALUE' 		=> $raid_value, 
			'TIMEVALUE' 		=> $time_bonus,
			'ZEROSUMSVALUE' 	=> $zerosum_bonus,
			'DECAYVALUE' 		=> $raid_decay,
			'TOTAL'				=> $raid_value+$time_bonus+$zerosum_bonus-$raid_decay,
			'RAID_ID' 			=> $raid_id,
			'RAIDTITLE' 		=> sprintf($user->lang['RAIDERDESCRIPTION'], $raid_id, $eventname, $user->format_date($raidstart), $member_name),  
			'S_EDITRAIDER'   	=> true,
			'S_SHOWZS'			=> ($config['bbdkp_zerosum'] == '1') ? true : false, 
		));
		
		return true;
	}
	
	
	/**
	 * Set active or inactive based on last raid. only for current raids dkp pool
	 * Update active inactive player status column member_status
	 * active = 1 inactive = 0
     *
	 * @param int $dkpid 
	 * @return bool 
	 */
	public function update_player_status($dkpid)
	{
		global $db, $user, $config;
		
		$inactive_time = mktime (0, 0, 0, date ( 'm' ), date ( 'd' ) - $config ['bbdkp_inactive_period'], date ( 'Y' ) );
		
		$active_members = array ();
		$inactive_members = array ();
		
		// Don't do active/inactive adjustments if we don't need to.
		if (($config ['bbdkp_active_point_adj'] != 0) || ($config ['bbdkp_inactive_point_adj'] != 0))
		{
			// adapt status and set adjustment points 
			$sql_array = array (
				'SELECT' => 'a.member_id, b.member_name, a.member_status, a.member_lastraid', 
				'FROM' => array (
					MEMBER_DKP_TABLE => 'a', 
					MEMBER_LIST_TABLE => 'b' 
					), 
				'WHERE' => ' a.member_id = b.member_id AND a.member_dkpid =' . $dkpid 
			);
			$adj_value = 0.00;
			$adj_reason = '';
			$sql = $db->sql_build_query ( 'SELECT', $sql_array );
			$result = $db->sql_query ( $sql );
			while ( $row = $db->sql_fetchrow ( $result ) )
			{
				unset ( $adj_value ); // destroy local
				unset ( $adj_reason );
				
				// Active -> Inactive
				if (((float) $config ['bbdkp_inactive_point_adj'] != 0.00) && ($row['member_status'] == 1) && ($row['member_lastraid'] < $inactive_time))
				{
					$adj_value = $config ['bbdkp_inactive_point_adj'];
					$adj_reason = 'Inactive adjustment';
					$inactive_members [] = $row['member_id'];
					$inactive_membernames [] = $row['member_name'];
				} 
				// Inactive -> Active
				elseif (( (float) $config ['bbdkp_active_point_adj'] != 0.00) && ($row['member_status'] == 0) && ($row['member_lastraid'] >= $inactive_time))
				{
					$adj_value = $config ['bbdkp_active_point_adj'];
					$adj_reason = 'Active adjustment';
					$active_members [] = $row['member_id'];
					$active_membernames [] = $row['member_name'];
				}
				
				//
				// Insert individual adjustment
				if ((isset ( $adj_value )) && (isset ( $adj_reason )))
				{
					$group_key = $this->gen_group_key ( $this->time, $adj_reason, $adj_value );
					$query = $db->sql_build_array ( 'INSERT', 
						array (
							'adjustment_dkpid' 		=> $dkpid, 
							'adjustment_value' 		=> $adj_value, 
							'adjustment_date' 		=> $this->time, 
							'member_id' 			=> $row['member_id'], 
							'adjustment_reason' 	=> $adj_reason, 
							'adjustment_group_key' 	=> $group_key, 
							'adjustment_added_by' 	=> $user->data ['username'] ));
					
					$db->sql_query ( 'INSERT INTO ' . ADJUSTMENTS_TABLE . $query );
				}
			}
			$db->sql_freeresult( $result );
			
			// Update members to inactive and put dkp adjustment
			if (sizeof ( $inactive_members ) > 0)
			{
				$adj_value = (float) $config ['bbdkp_inactive_point_adj'];
				$adj_reason = 'Inactive adjustment';
				
				$sql = 'UPDATE ' . MEMBER_DKP_TABLE . '
				    SET member_status = 0, member_adjustment = member_adjustment + ' . (string) $adj_value . ' 
	                WHERE member_dkpid = ' . $dkpid . '  AND ' . $db->sql_in_set ( 'member_id', $inactive_members ) ;
				$db->sql_query($sql);

				$log_action = array (
					'header' 		=> 'L_ACTION_INDIVADJ_ADDED', 
					'L_ADJUSTMENT' 	=> $config ['bbdkp_inactive_point_adj'], 
					'L_MEMBERS' 	=> implode ( ', ', $inactive_membernames ), 
					'L_REASON' 		=> $user->lang['INACTIVE_POINT_ADJ'],  
					'L_ADDED_BY'	=> $user->data ['username'] );
				
				$this->log_insert ( array (
					'log_type' 		=> $log_action ['header'], 
					'log_action' 	=> $log_action ));
			 }
			
			// Update active members' adjustment
			if (sizeof ( $active_members ) > 0)
			{
				$adj_value = (float) $config ['bbdkp_active_point_adj'];
				
				$sql = 'UPDATE ' . MEMBER_DKP_TABLE . '
				      SET member_status = 1, member_adjustment = member_adjustment + ' . (string) $adj_value . ' 
	                WHERE member_dkpid = ' . $dkpid . '  AND ' . $db->sql_in_set ( 'member_id', $active_members );
				
				$db->sql_query($sql);
				
				$log_action = array (
					'header' 		=> 'L_ACTION_INDIVADJ_ADDED', 
					'L_ADJUSTMENT' 	=> $config ['bbdkp_active_point_adj'], 
					'L_MEMBERS' 	=> implode ( ', ', $active_membernames ), 
					'L_REASON' 		=> $user->lang['ACTIVE_POINT_ADJ'], 
					'L_ADDED_BY' 	=> $user->data ['username'] );
				
				$this->log_insert ( array ('log_type' => $log_action ['header'], 'log_action' => $log_action ) );
			}
		}
		else
		{
			// only adapt status 
			
			// Active -> Inactive
			$db->sql_query ( 'UPDATE ' . MEMBER_DKP_TABLE . " SET member_status = 0 WHERE member_dkpid = " . $dkpid . "
	     		AND (member_lastraid <  " . $inactive_time . ") AND (member_status= 1)" );
			
			// Inactive -> Active
			$db->sql_query ( 'UPDATE ' . MEMBER_DKP_TABLE . " SET member_status = 1 WHERE member_dkpid = " . $dkpid . "  
	   			AND (member_lastraid >= " . $inactive_time . ") AND (member_status= 0)" );
		}
		
		return true;
	}
	
	/**
	 * called when deleting a whole raid : removes all loot from raid and updates dkp account
	 * 
	 * @param int $raid_id the raid from which all loot will be removed.
	 */
	private function remove_loot($oldraid)
	{
		global $db, $phpEx, $phpbb_root_path;
		
		if ( !class_exists('acp_dkp_item')) 
		{
			require($phpbb_root_path . 'includes/acp/acp_dkp_item.' . $phpEx); 
		}
		$acp_dkp_item = new acp_dkp_item;

		$sql = 'SELECT i.*, m.member_name FROM ' . 
				RAID_ITEMS_TABLE . ' i, ' . 
				MEMBER_LIST_TABLE . ' m 
				WHERE i.member_id = m.member_id 
				and raid_id = ' . (int) $oldraid['raid_id'];
		$result = $db->sql_query ( $sql );
		
		// loop the items collection
		while ( $row = $db->sql_fetchrow( $result ) ) 
		{
				$old_item = array (
				'item_id' 		=>  (int) $row['item_id'] ,  
				'dkpid'			=>  $oldraid['event_dkpid'], 
				'item_name' 	=>  (string) $row['item_name'] , 
				'member_id' 	=>  (int) 	$row['member_id'] , 
				'member_name' 	=>  (string) $row['member_name'] ,
				'raid_id' 		=>  (int) 	$row['raid_id'], 
				'item_date' 	=>  (int) 	$row['item_date'] , 
				'item_value' 	=>  (float) $row['item_value'], 
				'item_decay' 	=>  (float) $row['item_decay'] , 
				'item_zs' 		=>  (bool)   $row['item_zs'],
				);
		
				$acp_dkp_item->deleteitem_db($old_item);
		
		}
		$db->sql_freeresult ($result);

		return true;
			
	}
	

	/**
	 * Deletes one item
	 * called from raid acp item list (red button)
	 *  
	 */	
	private function deleteitem()
	{
		global $db, $user, $template, $phpEx, $phpbb_root_path;
		
		if (confirm_box ( true )) 
		{
			//retrieve info
			$old_item = request_var('hidden_old_item', array(''=>''));			

			if ( !class_exists('acp_dkp_item')) 
			{
				require($phpbb_root_path . 'includes/acp/acp_dkp_item.' . $phpEx); 
			}
			$acp_dkp_item = new acp_dkp_item;

			$acp_dkp_item->deleteitem_db($old_item); 
									
			$log_action = array (
				'header' 	=> 'L_ACTION_ITEM_DELETED',
				'L_NAME' 	=> $old_item ['item_name'], 
				'L_BUYER' 	=> $old_item ['member_name'],
				'L_RAID_ID' => $old_item ['raid_id'], 
				'L_VALUE' 	=> $old_item ['item_value'] );
			
			$this->log_insert ( array (
				'log_type' 		=> $log_action ['header'], 
				'log_action' 	=> $log_action ) );
			
			$success_message = sprintf ( $user->lang ['ADMIN_DELETE_ITEM_SUCCESS'], 
			$old_item ['item_name'], $old_item ['member_name'], $old_item ['item_value'] );
			
			trigger_error ( $success_message . $this->link, E_USER_NOTICE );
		
		} 
		else
		{
			$dkpid = request_var(URI_DKPSYS,0); 
			$item_id = request_var(URI_ITEM, 0);
			if($item_id==0)
			{	
				trigger_error ( $user->lang ['ERROR_INVALID_ITEM_PROVIDED'] , E_USER_WARNING);
			}
			
			$sql = 'SELECT * FROM ' . RAID_ITEMS_TABLE . ' i, ' . MEMBER_LIST_TABLE . ' m WHERE 
				i.member_id = m.member_id and i.item_id= ' . (int) $item_id;
			$result = $db->sql_query ( $sql );
			$old_item = array();
			while ( $row = $db->sql_fetchrow ( $result ) ) 
			{
				$old_item = array (
				'item_id' 		=>  (int) $item_id , 
				'dkpid'			=>  $dkpid, 
				'item_name' 	=>  (string) $row['item_name'] , 
				'member_id' 	=>  (int) 	$row['member_id'] , 
				'member_name' 	=>  (string)	$row['member_name'] ,
				'raid_id' 		=>  (int) 	$row['raid_id'], 
				'item_date' 	=>  (int) 	$row['item_date'] , 
				'item_value' 	=>  (float) $row['item_value'], 
				'item_decay' 	=>  (float) $row['item_decay'] , 
				'item_zs' 		=>  (bool)   $row['item_zs'],
				);
			}
			$db->sql_freeresult ($result);
			
			$s_hidden_fields = build_hidden_fields ( array (
				'deleteitem' 	  => true, 
				'hidden_old_item' => $old_item
			));

			$template->assign_vars ( array (
				'S_HIDDEN_FIELDS' => $s_hidden_fields ) );
			confirm_box ( false, sprintf($user->lang ['CONFIRM_DELETE_ITEM'], $old_item ['item_name'], $old_item ['member_name']  ), $s_hidden_fields );
		}
				
	}
		
	
	/**
	 * function to decay one specific raid
	 * calling this function multiple time will not lead to cumulative decays, just the delta is applied.
	 * 
	 * @param int $raid_id the raid id to decay
	 * @param int $dkpid dkpid for adapting accounts
	 */
	private function decayraid($raid_id, $dkpid)
	{
		global $config, $db;
		//loop raid detail, pass earned and timediff to decay function, update raid detail
		
		//get old raidinfo
		$sql_array = array (
			'SELECT' => ' r.raid_start, ra.member_id, (ra.raid_value + ra.time_bonus + ra.zerosum_bonus) as earned, ra.raid_decay ', 
			'FROM' => array (
				RAIDS_TABLE 		=> 'r' ,
				RAID_DETAIL_TABLE	=> 'ra' , 
				), 
			'WHERE' => " r.raid_id = ra.raid_id and r.raid_id=" . ( int ) $raid_id, 
		);
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query ($sql);
		$raidstart = 0;
		$raid = array();
		while ( ($row = $db->sql_fetchrow ( $result )) ) 
		{
			$raidstart =  $row['raid_start']; 
			$raid[$row['member_id']] = array (
				'member_id' 	=> $row['member_id'], 
				'earned' 		=> $row['earned'], 
				'raid_decay' 	=> $row['raid_decay'], 
				);
		}
		$db->sql_freeresult ($result);

		//get timediff
		$now = getdate();
		$timediff = mktime($now['hours'], $now['minutes'], $now['seconds'], $now['mon'], $now['mday'], $now['year']) - $raidstart  ;

		// loop raid detail
		foreach($raid as $member_id => $raiddetail)
		{
			// get new decay : may be different per player due to it being calculated on earned
			$decay = $this->decay($raiddetail['earned'], $timediff, 1); 
			
			// update raid detail to new decay value
			$sql = 'UPDATE ' . RAID_DETAIL_TABLE . ' SET raid_decay = ' . $decay[0] . ', decay_time = ' . $decay[1] . ' WHERE raid_id = ' . ( int ) $raid_id . ' 
			and member_id = ' . $raiddetail['member_id'] ;
			$db->sql_query ( $sql );
			
			// update dkp account, deduct old, add new decay
			$sql = 'UPDATE ' . MEMBER_DKP_TABLE . ' SET member_raid_decay = member_raid_decay - ' . $raiddetail['raid_decay'] . ' + ' . $decay[0] . " 
				WHERE member_id = " . ( int ) $member_id . ' 
				and member_dkpid = ' . $dkpid ;
			$db->sql_query ( $sql );
		}

		//now loop raid items detail
		$sql = 'SELECT i.item_id, i.member_id, i.item_value, i.item_decay FROM ' . RAID_ITEMS_TABLE . ' i where i.raid_id = ' .  $raid_id; 
		$result = $db->sql_query ($sql);
		$items= array();
		while ( ($row = $db->sql_fetchrow ( $result )) ) 
		{
			$items[$row['item_id']] = array (
				'member_id'		=> $row['member_id'],
				'item_value' 	=> $row['item_value'],
			 	'item_decay' 	=> $row['item_decay'],
				);
		}
		$db->sql_freeresult ($result);
		
		foreach($items as $item_id => $item)
		{
			// get new itemdecay
			$itemdecay = $this->decay($item['item_value'], $timediff, 2); 
			
			//  update item detail to new decay value
			$sql = 'UPDATE ' . RAID_ITEMS_TABLE . ' SET item_decay = ' . $itemdecay[0] . ', decay_time = ' . $itemdecay[1] . ' WHERE item_id = ' . $item_id;
			$db->sql_query ( $sql);
			
			// update dkp account, deduct old, add new decay
			$sql = 'UPDATE ' . MEMBER_DKP_TABLE . ' SET member_item_decay = member_item_decay - ' . $item['item_decay'] . ' + ' . $itemdecay[0] . " 
				WHERE member_id = " . ( int ) $item['member_id'] . ' and member_dkpid = ' . $dkpid ;
			$db->sql_query ( $sql );
		}
		
		return true;
		
		
	}
	
	/**
	 * calculates decay on epoch timedifference (seconds) and earned
	 * we decay the sum of raid value, time bonus and zerosumpoints 
	 * 
	 * @param int $value = the value to decay
	 * @param int $timediff = diff in seconds since raidstart
	 * @param int $mode = 1 for raid, 2 for items
	 * 
	 */
	public function decay($value, $timediff, $mode)
	{
		global $config, $db;
		$i=0;
		switch ($mode)
		{
			case 1:
				// get raid decay rate in pct
				$i = (float) $config['bbdkp_raiddecaypct']/100;
				break;
			case 2:
				// get item decay rate in pct
				$i = (float) $config['bbdkp_itemdecaypct']/100;
				break;
		}

		// get decay frequency
		$freq = $config['bbdkp_decayfrequency'];	
		if ($freq==0)
		{
			//frequency can't be 0. throw error
			trigger_error($user->lang['FV_FREQUENCY_NOTZERO'],E_USER_WARNING );	
		}
		
		//pick decay frequency type (0=days, 1=weeks, 2=months) and convert timediff to that
		$t=0;
		switch ($config['bbdkp_decayfreqtype'])
		{
			case 0:
				//days
				$t = (float) $timediff / 86400; 
				break;
			case 1:
				//weeks
				$t = (float) $timediff / (86400*7);
				break;
			case 2:
				//months
				$t = (float) $timediff / (86400*30.44);
				break;	 
		}
		
		// take the integer part of time and interval division base 10, 
		// since we only decay after a set interval
		$n = intval($t/$freq, 10); 
		
		//calculate rounded raid decay, defaults to rounds half up PHP_ROUND_HALF_UP, so 9.495 becomes 9.50
		$decay = round($value * (1 - pow(1-$i, $n)), 2); 
		
		return array($decay, $n) ;

	}
	
	/**
	 * Recalculates and updates decay
	 * loops all raids - caution this may run a long time
	 * 
	 * @param $mode 1 for recalculating, 0 for setting decay to zero.
	 */
	public function sync_decay($mode, $origin= '')
	{
		global $user, $db;
		switch ($mode)
		{
			case 0:
				//  Decay = OFF : set all decay to 0
				//  update item detail to new decay value
				$sql = 'UPDATE ' . RAID_DETAIL_TABLE . ' SET raid_decay = 0 ' ;
				$db->sql_query ( $sql );
				
				// update dkp account, deduct old, add new decay
				$sql = 'UPDATE ' . MEMBER_DKP_TABLE . ' SET member_raid_decay = 0, member_item_decay = 0';
				$db->sql_query ( $sql );
				
				$sql = 'UPDATE ' . RAID_ITEMS_TABLE . ' SET item_decay = 0'; 
				$db->sql_query ( $sql);
				
				if ($origin != 'cron')
				{
					//no logging for cronjobs
					$log_action = array (
						'header' 		=> 'L_ACTION_DECAYOFF',
						'L_USER' 		=>  $user->data['user_id'],
						'L_USERCOLOUR' 	=>  $user->data['user_colour'], 
						'L_ORIGIN' 		=>  $origin
						);
					
					$this->log_insert ( array (
					'log_type' 		=> $log_action ['header'], 
					'log_action' 	=> $log_action ) );
				}
				
				return true;
				break;
				
			case 1:
				// Decay is ON : synchronise
				// loop all raids
				$sql = 'SELECT e.event_dkpid, r.raid_id FROM '. RAIDS_TABLE. ' r, ' . EVENTS_TABLE . ' e WHERE e.event_id = r.event_id ' ;
				$result = $db->sql_query ($sql);
				$countraids=0;
				while ( ($row = $db->sql_fetchrow ( $result )) ) 
				{
					$this->decayraid($row['raid_id'], $row['event_dkpid']);
					$countraids++;
				}
				$db->sql_freeresult ($result);
				
				if ($countraids > 0 && $origin != 'cron' )
				{
					//no logging for cronjobs due to users just not getting it.
					$log_action = array (
					'header' 	=> 'L_ACTION_DECAYSYNC',
					'L_USER' 	=>  $user->data['user_id'],
					'L_USERCOLOUR' 	=>  $user->data['user_colour'], 
					'L_RAIDS' 	=> $countraids,
					'L_ORIGIN' 		=>  $origin 
					);
				
					$this->log_insert ( array (
					'log_type' 		=> $log_action ['header'], 
					'log_action' 	=> $log_action ) );
				}
				
				return $countraids;
				
				break;
			
		}
		
		
	}
	
}

?>
