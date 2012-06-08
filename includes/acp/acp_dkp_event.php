<?php
/**
 * @package bbDKP.acp
 * @link http://www.bbdkp.com
 * @author Sajaki@gmail.com
 * @copyright 2009 bbdkp
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 1.2.7
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (! defined('EMED_BBDKP')) 
{
	$user->add_lang ( array ('mods/dkp_admin' ));
	trigger_error ( $user->lang['BBDKPDISABLED'] , E_USER_WARNING );
}

class acp_dkp_event extends bbDKP_Admin
{
	var $u_action;
	var $link;
	/** 
	* main ACP dkp event function
	* @param int $id the id of the node who parent has to be returned by function 
	* @param int $mode id of the submenu
	* @access public 
	*/
	function main($id, $mode)
	{
		global $db, $user, $template;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;
		$user->add_lang(array('mods/dkp_admin'));	
		$user->add_lang(array('mods/dkp_common'));	 
		$this->link = '<br /><a href="'.append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_event&amp;mode=listevents") . '"><h3>'. $user->lang['RETURN_DKPINDEX'] . '</h3></a>';

		 /***DKPSYS drop-down ***/
		$dkpsys_id = 1;
		$sql = 'SELECT dkpsys_id, dkpsys_name, dkpsys_default FROM ' . DKPSYS_TABLE . ' ORDER BY dkpsys_name';
		$resultdkpsys = $db->sql_query($sql);
		
		$form_key = 'acp_dkp_event';
		add_form_key($form_key);
					
		switch ($mode)
		{
			case 'addevent':
			/* select data */
			$update = false;

			if(isset($_GET[URI_EVENT]) )
			{
				$this->url_id = request_var(URI_EVENT, '');
			}
			 
			if ( $this->url_id )	 
			{
				// we have a GET
				$update = true;
				 
				$sql = 'SELECT b.dkpsys_name, b.dkpsys_id, a.event_name, a.event_value, a.event_id, a.event_color, a.event_imagename 
						FROM ' . EVENTS_TABLE . ' a, ' . DKPSYS_TABLE . " b 
						WHERE a.event_id = " . (int) $this->url_id . " AND b.dkpsys_id = a.event_dkpid";

				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result); 
				$db->sql_freeresult($result);
				if (!$row)
				{
					trigger_error($user->lang['ERROR_INVALID_EVENT_PROVIDED']);
				}
				else 
				{
					$this->event = array(
						'event_dkpsys_name'	 	 => $row['dkpsys_name'],
						'dkpsys_id'		 		 => $row['dkpsys_id'],
						'event_name'			 => $row['event_name'],
						'event_color'			 => $row['event_color'],
						'event_imagename'		 => $row['event_imagename'],
						'event_value'			 => $row['event_value'],
						'event_id'				 => $row['event_id']
					);
				}

				while ( $row2 = $db->sql_fetchrow($resultdkpsys) )
				{
					$template->assign_block_vars('event_dkpid_row', array(
					'VALUE' => $row2['dkpsys_id'],
					'SELECTED' => ( $row2['dkpsys_name'] == $row['dkpsys_name']) ? ' selected="selected"' : '',
					'OPTION'	 => ( !empty($row2['dkpsys_name']) ) ? $row2['dkpsys_name'] : '(None)')
					);
				}
				 
			}
			else
			{
				// we dont have a GET so put default values
				while ($row2 = $db->sql_fetchrow($resultdkpsys) )
				{
					//dkpsys_default
					$template->assign_block_vars('event_dkpid_row', array(
					 'VALUE' => $row2['dkpsys_id'],
					 'SELECTED' => ( $row2['dkpsys_default'] == 'Y' ) ? ' selected="selected"' : '',
					 'OPTION'	=> ( !empty($row2['dkpsys_name']) ) ? $row2['dkpsys_name'] : '(None)')
					);
				}
			}
			 
			$add		= (isset($_POST['add'])) ? true : false;
			$submit	= (isset($_POST['update'])) ? true : false;
			$delete	= (isset($_POST['delete'])) ? true : false; 
			$addraid	= (isset($_POST['newraid'])) ? true : false; 

			if ( $add || $submit || $addraid)
			{
					if (!check_form_key('acp_dkp_event'))
					{
						trigger_error('FORM_INVALID');
					}
			}
			
			if ($addraid)
			{
				redirect(append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_raid&amp;mode=addraid&amp;".URI_DKPSYS . '=' . 
				$this->event['dkpsys_id'] . '&amp;' . URI_EVENT . '=' . $this->event['event_id'] ));
			}
	
			if ($add)
			{
				$this->add_event();
			}
				 
			if ($submit)
			{
				$this->update_event();
			}	

			if ($delete)
			{	
				$this->delete_event();
			}	
			
			/* if bossprogress is installed */
			if (isset($config['bbdkp_bp_version']))
			{
				if (isset($this->event))
				{
					$s_zonelist_options = '<option value="--">--</option>';		
				}
				else 
				{
					$s_zonelist_options = '<option value="--" selected="selected">--</option>';
				}
			    $games = array(
                    'wow'        => $user->lang['WOW'], 
                    'lotro'      => $user->lang['LOTRO'], 
                    'eq'         => $user->lang['EQ'], 
                    'daoc'       => $user->lang['DAOC'], 
                    'vanguard' 	 => $user->lang['VANGUARD'],
                    'eq2'        => $user->lang['EQ2'],
                    'warhammer'  => $user->lang['WARHAMMER'],
                    'aion'       => $user->lang['AION'],
                    'FFXI'       => $user->lang['FFXI'],
                	'rift'       => $user->lang['RIFT'],
                	'swtor'      => $user->lang['SWTOR'], 
					'lineage2'   => $user->lang['LINEAGE2']	                	
                );
                $installed_games = array();
                foreach($games as $gameid => $gamename)
                {
                	//add value to dropdown when the game config value is 1
                	if ($config['bbdkp_games_' . $gameid] == 1)
                	{
                		$installed_games[] = $gameid; 
                	} 
                }
                
				// list of zones
				$sql_array = array(
				'SELECT'	=>	' z.id, l.name ', 
				'FROM'		=> array(
						ZONEBASE		=> 'z',
						BB_LANGUAGE		=> 'l',
							),
				'WHERE'		=> " z.id = l.attribute_id 
								AND l.attribute='zone' 
								AND l.game_id = z.game
								AND l.language= '" . $config['bbdkp_lang'] ."' 
								AND " . $db->sql_in_set('l.game_id', $installed_games), 
				'ORDER_BY'	=> 'sequence desc, id desc ',
				);
				
				$sql = $db->sql_build_query('SELECT', $sql_array);					
				$result = $db->sql_query($sql);
				while ( $row = $db->sql_fetchrow($result) )
				{
					if (!isset($this->event))
					{
						$s_zonelist_options .= '<option value="' . $row['name'] . '"> ' . $row['name'] . '</option>';	
					}
					else
					{
						$select = ($row['name'] == $this->event['event_name'] ) ? ' selected="selected" ' : ' ';
						$s_zonelist_options .= '<option value="' . $row['name'] . '" ' . $select . ' > ' . $row['name'] . '</option>';
					}
										
				}
					
				$template->assign_vars(array(
						'S_ZONEEVENT_OPTIONS'		=> $s_zonelist_options,
						'S_BP_SHOW'	=> true,
					));
				}
				else 
				{
				$template->assign_vars(array(
						'S_BP_SHOW'	=> false,
					));
				}
						
				$template->assign_vars(array(
						'EVENT_ID'	=> $this->url_id,
						'L_TITLE'	=> $user->lang['ACP_ADDEVENT'],
						'L_EXPLAIN' => $user->lang['ACP_ADDEVENT_EXPLAIN'],

						'EVENT_ID'	=> $this->url_id,
					 
						// Form values
						'EVENT_DKPPOOLNAME'	=> isset($this->event['event_dkpsys_name']) ? $this->event['event_dkpsys_name']: '',
						'EVENT_NAME'		=> isset($this->event['event_name']) ? $this->event['event_name']: '' ,
						'EVENT_VALUE'		=> isset($this->event['event_value']) ? $this->event['event_value']: '' ,
						'EVENT_COLOR'		=> isset($this->event['event_color']) ? (($this->event['event_color'] == '') ? '#123456' : $this->event['event_color']) : '#123456',
						'EVENT_IMAGENAME'	=> isset($this->event['event_imagename']) ? $this->event['event_imagename']: '' ,
					 
						'IMAGEPATH' 			=> isset($this->event['event_imagename']) ?  $phpbb_root_path . "images/event_images/" . $this->event['event_imagename'] . ".png" : '' ,   
                    	'S_EVENT_IMAGE_EXISTS' 	=> isset($this->event['event_imagename']) ? ((strlen($this->event['event_imagename']) > 1) ? true : false) : false ,       
				
						// Language
						'L_DKP_VALUE'		=> sprintf($user->lang['DKP_VALUE'], $config['bbdkp_dkp_name']),
					 
						// Form validation
						'FV_NAME'=> $this->fv->generate_error('event_name'),
						'FV_VALUE' => $this->fv->generate_error('event_value'),
					 
						// Javascript messages
						'MSG_NAME_EMPTY'=> $user->lang['FV_REQUIRED_NAME'],
						'MSG_VALUE_EMPTY' => $user->lang['FV_REQUIRED_VALUE'],
					 
						// Buttons
						'S_ADD' => ( !$this->url_id ) ? true : false
						)
					);
				 
				$this->page_title = 'ACP_ADDEVENT';
				$this->tpl_name = 'dkp/acp_'. $mode;
			 
			break;
 
			case 'listevents':

				$showadd = (isset($_POST['eventadd'])) ? true : false;
				
				if($showadd)
				{
					redirect(append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_event&amp;mode=addevent"));					
					break;
				}
				
				
				$sort_order = array(
					0 => array('dkpsys_name', 'dkpsys_name desc'),
					1 => array('event_name', 'dkpsys_name, event_name desc'),
					2 => array('event_value desc', 'dkpsys_name, event_value desc')
				);
				 
				$current_order = switch_order($sort_order);
			 
				$sql = 'SELECT count(*) as countevents FROM ' . EVENTS_TABLE;
				$result = $db->sql_query($sql);	
				$total_events = (int) $db->sql_fetchfield('countevents');
				$db->sql_freeresult($result);
			 
				$start = request_var('start',0);
				$sql = 'SELECT b.dkpsys_name, a.event_name, a.event_value, a.event_id, a.event_color, a.event_imagename 
						FROM ' . EVENTS_TABLE . ' a, ' . DKPSYS_TABLE . ' b 
						WHERE b.dkpsys_id = a.event_dkpid 
						ORDER BY '. $current_order['sql']; 
			 
				if ( !($events_result = $db->sql_query_limit($sql,	$config['bbdkp_user_elimit'], $start)	 ) )
				{
					trigger_error($user->lang['ERROR_INVALID_EVENT_PROVIDED'], E_USER_NOTICE);
				}
			 
				while ( $event = $db->sql_fetchrow($events_result) )
				{
					$template->assign_block_vars('events_row', array(
						'U_VIEW_EVENT' =>append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_event&amp;mode=addevent&amp;" . URI_EVENT ."={$event['event_id']}"),
						'DKPSYS_EVENT' => $event['dkpsys_name'],
						'COLOR' => $event['event_color'],
						'IMAGEPATH' 	=> $phpbb_root_path . "images/event_images/" . $event['event_imagename'] . ".png", 
                    	'S_EVENT_IMAGE_EXISTS' => (strlen($event['event_imagename']) > 1) ? true : false, 
					
						'IMAGENAME' => $event['event_imagename'],
						'NAME' => $event['event_name'],
						'VALUE' => $event['event_value'])
					);
				}
				$db->sql_freeresult($events_result);
			 
				$template->assign_vars(array(
					'L_TITLE'		=> $user->lang['ACP_LISTEVENTS'],
					'L_EXPLAIN'		=> $user->lang['ACP_LISTEVENTS_EXPLAIN'],
					'O_DKPSYS'		=> $current_order['uri'][0],
					'O_NAME'		=> $current_order['uri'][1],
					'O_VALUE'		=> $current_order['uri'][2], 
					'U_LIST_EVENTS' => append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_event&amp;mode=listevents&amp;"),		
					'START'			=> $start,
					'LISTEVENTS_FOOTCOUNT' => sprintf($user->lang['LISTEVENTS_FOOTCOUNT'], $total_events, $config['bbdkp_user_elimit']),
					'EVENT_PAGINATION'	=> generate_pagination(append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_event&amp;mode=listevents&amp;" . URI_ORDER . '='.$current_order['uri']['current']), $total_events, $config['bbdkp_user_elimit'],$start, true))

				);

				$this->page_title = 'ACP_LISTEVENTS';
				$this->tpl_name = 'dkp/acp_'. $mode;
			 
			break;

	 
		}
	}
	
	/**
	 * adds an event to the database
	 *
	 */
	function add_event()
	{
		global $user, $config, $db;
		
		$this_dkp_id = request_var('event_dkpid',0);
		
		$event_name = utf8_normalize_nfc(request_var('event_name','', true));
		if (isset($config['bbdkp_bp_version']))
		{
			if (isset($config['bbdkp_bp_version']))
			{
				$zone= utf8_normalize_nfc(request_var('zoneevent','', true));
				if ($zone != "--")
				{
					$event_name= $zone;
				}
			}
			
		}
		
		if (strlen($event_name) < 3)
		{
			 trigger_error($user->lang['ERROR_INVALID_EVENT_PROVIDED'] . $this->link, E_USER_WARNING);
		}
		
		$event_imagename = utf8_normalize_nfc(request_var('event_image','', true));
		$event_color = utf8_normalize_nfc(request_var('event_color','', true));
		$event_value= request_var('event_value', 0.0);

		// check existing
		$result = $db->sql_query("SELECT count(*) as evcount from " . EVENTS_TABLE . 
		" WHERE UPPER(event_name) = '" . strtoupper($db->sql_escape(utf8_normalize_nfc(request_var('event_name',' ', true))))	."' ;");
		$eventexistsrow = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		 
		if($eventexistsrow['evcount'] > 0 )
		{
			trigger_error($user->lang['ERROR_RESERVED_EVENTNAME']	. $this->link, E_USER_WARNING);
		}
		 
		$query = $db->sql_build_array('INSERT', array(	
			 'event_dkpid'		=> $this_dkp_id,	 
			 'event_name'		=> $event_name,
			 'event_imagename'	=> $event_imagename,	
			 'event_color'		=> $event_color,	 
			 'event_value'		=> $event_value,	
			 'event_added_by' 	=> $user->data['username'])	
		 );		
		$db->sql_query('INSERT INTO ' . EVENTS_TABLE . $query);

		/* get new key */
		$this_event_id = $db->sql_nextid();
		
		$log_action = array(
				 'header'		=> 'L_ACTION_EVENT_ADDED',
				 'id'			=> $this_event_id,
				 'L_NAME'		=> $event_name,
				 'L_VALUE'		=> $event_value,
				 'L_ADDED_BY' 	=> $user->data['username']);
			 
		$this->log_insert(array(
				 'log_type'	=> $log_action['header'],
				 'log_action' => $log_action)
			 );
		$success_message = sprintf($user->lang['ADMIN_ADD_EVENT_SUCCESS'], request_var('event_value', 0.0), $event_name);
		trigger_error($success_message . $this->link);
		
	}

	/**
	 * updates an existing event
	 *
	 */
	function update_event()
	{
		global $db, $user;
		$this->url_id = request_var('hidden_id',0);

		// get old event name, value from db
		$sql = 'SELECT event_dkpid, event_name, event_value
				FROM ' . EVENTS_TABLE . '
				WHERE event_id=' . (int) $this->url_id;
		 
		$result = $db->sql_query($sql);
		while ( $row = $db->sql_fetchrow($result) )
		{
			$this->old_event = array(
				'event_dkpid' 	=> $row['event_dkpid'],
				'event_name'	=> $row['event_name'],
				'event_value'	=> $row['event_value']
			);
		}
		$db->sql_freeresult($result);			

		$new_event_name = utf8_normalize_nfc(request_var('event_name','', true));
		if (isset($config['bbdkp_bp_version']))
		{
			$zone= utf8_normalize_nfc(request_var('zoneevent','', true));
			if ($zone != "--")
			{
					$new_event_name = $zone;
			}
		}

		if (strlen($new_event_name) < 3)
		{
			 trigger_error($user->lang['ERROR_INVALID_EVENT_PROVIDED'] . $this->link, E_USER_WARNING);
		}
			 
		$dkpid = request_var('event_dkpid','');
		if ($dkpid == '')
		{
			trigger_error($user->lang['ERROR_INVALID_EVENT_PROVIDED'] . $this->link, E_USER_WARNING);
		}
		
		//
		// Update the event
		//
		$query = $db->sql_build_array('UPDATE', array(
			'event_dkpid' => $dkpid, 
			'event_name'=> $new_event_name,
			'event_imagename' => utf8_normalize_nfc(request_var('event_image','', true)),
			'event_color' => utf8_normalize_nfc(request_var('event_color','', true)),
			'event_value' => request_var('event_value', 0.0))
		);
		
		$sql = 'UPDATE ' . EVENTS_TABLE . ' SET ' . $query . ' WHERE event_id=' . (int) $this->url_id;
		$db->sql_query($sql);
 
		//
		// Logging
		//
		$log_action = array(
			'header'		 => 'L_ACTION_EVENT_UPDATED',
			'id'			 => request_var(URI_EVENT,0),
			'L_NAME_BEFORE'=> $this->old_event['event_name'],
			'L_VALUE_BEFORE' => $this->old_event['event_value'],
			'L_NAME_AFTER' => $new_event_name, 
			'L_VALUE_AFTER'=> request_var('event_value', 0.0),
			'L_UPDATED_BY' => $user->data['username']);
		
		$this->log_insert(array(
			'log_type' => $log_action['header'],
			'log_action' => $log_action)
		);
		 
		$success_message = sprintf($user->lang['ADMIN_UPDATE_EVENT_SUCCESS'], request_var('event_value', 0.0), $new_event_name);
		trigger_error($success_message . $this->link);
	}
	
	/**
	 * deletes an event
	 *
	 */
	function delete_event()
	{

		global $db, $user;
		if(isset($_GET[URI_EVENT]))
		{
			
			// give a warning that raids cant be without event
			if (confirm_box(true))
			{
				 
				$sql = 'DELETE FROM ' . EVENTS_TABLE . '
						WHERE event_id = ' . request_var(URI_EVENT,0) ;
				$db->sql_query($sql);	

				$clean_event_name = str_replace("'","", $this->event['event_name']);
		 
				$log_action = array(
					'header'	=> 'L_ACTION_EVENT_DELETED',
					'id'		=> request_var(URI_EVENT,0),
					'L_NAME'	=> $clean_event_name,
					'L_VALUE' 	=> $this->event['event_value']);
				
				$this->log_insert(array(
					'log_type' => $log_action['header'],
					'log_action' => $log_action)
					);

				$success_message = sprintf($user->lang['ADMIN_DELETE_EVENT_SUCCESS'], $this->event['event_value'], $this->event['event_name']);
				trigger_error($success_message . adm_back_link($this->u_action));
			}
			else
			{
				$s_hidden_fields = build_hidden_fields(array(
					'delete'	=> true,
					'event_id'	=> request_var(URI_EVENT,0) ,
					)
				);

				$template->assign_vars(array(
					'S_HIDDEN_FIELDS'	 => $s_hidden_fields)
				);

				confirm_box(false, $user->lang['CONFIRM_DELETE_EVENT'], $s_hidden_fields);
			}
		}
	}
	
}

?>