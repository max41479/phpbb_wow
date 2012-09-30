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
if (! defined('IN_PHPBB'))
{
	exit();
}
if (! defined('EMED_BBDKP'))
{
	$user->add_lang(array(
		'mods/dkp_admin'));
	trigger_error($user->lang['BBDKPDISABLED'], E_USER_WARNING);
}

/**
 * This class manages guildmembers dkp adjustments
 * 
 */
class acp_dkp_adj extends bbDKP_Admin
{
	public $u_action;
	private $old_adjustment;
	private $adjustment;
	private $link; 
	/** 
	 * main ACP dkp adjustment function
	 * @param int $id the id of the node who parent has to be returned by function 
	 * @param int $mode id of the submenu
	 * @access public 
	 */
	public function main ($id, $mode)
	{
		global $db, $user, $template;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;
		if (! class_exists('acp_dkp_mm'))
		{
			// we need this class for getting member id from name and viceversa
			include ($phpbb_root_path . 'includes/acp/acp_dkp_mm.' . $phpEx);
			$class_members = new acp_dkp_mm();
		}
		$user->add_lang(array('mods/dkp_admin', 'mods/dkp_common'));
		$this->link = '<br /><a href="' . append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp&amp;mode=mainpage") . '"><h3>' . $user->lang['RETURN_DKPINDEX'] . '</h3></a>';
		switch ($mode)
		{
			/************************************
			 * LIST INDIVIDUAL ADJUSTMENTS
			 ************************************/
			case 'listiadj':
				$showadd = (isset($_POST['addiadj'])) ? true : false;
				if ($showadd)
				{
					redirect(append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_adj&amp;mode=addiadj"));
					break;
				}
				/**  DKPSYS drop-down query ***/
				// only show pools with adjustments 							
				$sql = 'SELECT dkpsys_id, dkpsys_name , dkpsys_default 
		          FROM ' . DKPSYS_TABLE . ' a, ' . ADJUSTMENTS_TABLE . ' j 
		          WHERE a.dkpsys_id = j.adjustment_dkpid 
		          GROUP BY dkpsys_id, dkpsys_name , dkpsys_default';
				$result = $db->sql_query($sql);
				$dkpsys_id = 0;
				$submit = (isset($_POST['dkpsys_id']) || isset($_GET['dkpsys_id'])) ? true : false;
				if ($submit)
				{
					$dkpsys_id = request_var('dkpsys_id', 0);
				}
				else
				{
					while ($row = $db->sql_fetchrow($result))
					{
						if ($row['dkpsys_default'] == "Y")
						{
							$dkpsys_id = $row['dkpsys_id'];
						}
					}
					if ($dkpsys_id == 0)
					{
						$result = $db->sql_query_limit($sql, 1);
						while ($row = $db->sql_fetchrow($result))
						{
							$dkpsys_id = $row['dkpsys_id'];
						}
					}
				}
				$db->sql_freeresult($result);
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					$template->assign_block_vars('dkpsys_row', array(
						'VALUE' => $row['dkpsys_id'] , 
						'SELECTED' => ($row['dkpsys_id'] == $dkpsys_id) ? ' selected="selected"' : '' , 
						'OPTION' => (! empty($row['dkpsys_name'])) ? $row['dkpsys_name'] : '(None)'));
				}
				$db->sql_freeresult($result);
				
				/*** end DKPSYS drop-down ***/
				$sort_order = array(
					0 => array('adjustment_date desc' , 'adjustment_date') , 
					1 => array(	'adjustment_dkpid' , 'adjustment_dkpid desc') , 
					2 => array('dkpsys_name' , 'dkpsys_name desc') , 
					3 => array('member_name' , 'member_name desc') , 
					4 => array('adjustment_reason' , 'adjustment_reason desc') , 
					5 => array('adjustment_value desc' , 'adjustment_value') , 
					6 => array('adjustment_added_by' , 'adjustment_added_by desc'));
					
				$sql2 = 'SELECT count(*) as total_adjustments 
					FROM ' . ADJUSTMENTS_TABLE . ' 
					WHERE member_id IS NOT NULL 
					and adjustment_dkpid 	= ' . (int) $dkpsys_id;
				
				$member_filter = utf8_normalize_nfc(request_var('member_name', '', true));
				if ($member_filter != '')
				{
					$member_id_filter = intval($class_members->get_member_id(trim($member_filter)));
					$sql2 .= ' and member_id  = ' . $member_id_filter;
				}
				
				$result2 = $db->sql_query($sql2);
				$total_adjustments = (int) $db->sql_fetchfield('total_adjustments');
				$db->sql_freeresult($result2);
				$current_order = switch_order($sort_order);
				$u_list_adjustments = append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_adj&amp;mode=listiadj") . '&amp;' . URI_PAGE;
				$start = request_var('start', 0);
				$s_group_adj = false;
				$sql_array = array(
					'SELECT' => 'a.adjustment_dkpid, a.adjustment_reason, 
			    				b.dkpsys_name, a.adjustment_id, a.adj_decay, a.decay_time, a.can_decay, 
			    				a.adjustment_value, a.member_id, l.member_name,  
			    				a.adjustment_date, a.adjustment_added_by, c.colorcode, c.imagename ' , 
					'FROM' => array(
						ADJUSTMENTS_TABLE => 'a' , 
						DKPSYS_TABLE => 'b' , 
						MEMBER_LIST_TABLE => 'l' , 
						CLASS_TABLE => 'c') , 
					'WHERE' => ' 
			    		b.dkpsys_id = a.adjustment_dkpid 
			    		AND c.class_id = l.member_class_id  
			    		AND l.game_id= c.game_id 
						AND a.adjustment_dkpid 	= ' . (int) $dkpsys_id . '  
						AND a.member_id = l.member_id
						AND a.member_id IS NOT NULL ' , 
					'ORDER_BY' => $current_order['sql']);
				
				if ($member_filter != '')
				{
					$sql_array['WHERE'] .= ' AND l.member_name ' . $db->sql_like_expression($db->any_char . $member_filter . $db->any_char);
				}
				$sql = $db->sql_build_query('SELECT', $sql_array);
				$count = 0;
				$result = $db->sql_query($sql);
				$hasrows = false;
				while ($adj = $db->sql_fetchrow($result))
				{
					$hasrows = true;
					$count = $count + 1;
				}
				$db->sql_freeresult($result);
				$result = $db->sql_query_limit($sql, $config['bbdkp_user_alimit'], $start, 0);
				while ($adj = $db->sql_fetchrow($result))
				{
					$template->assign_block_vars('adjustments_row', array(
						'U_ADD_ADJUSTMENT' => append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_adj&amp;mode=addiadj") . '&amp;' . URI_ADJUSTMENT . '=' . $adj['adjustment_id'] . '&amp;' . URI_DKPSYS . '=' . $adj['adjustment_dkpid'] , 
						'DATE' => date($config['bbdkp_date_format'], $adj['adjustment_date']) , 
						'DKPID' => $adj['adjustment_dkpid'] , 
						'DKPPOOL' => $adj['dkpsys_name'] , 
						'COLORCODE' => ($adj['colorcode'] == '') ? '#123456' : $adj['colorcode'] , 
						'CLASS_IMAGE' => (strlen($adj['imagename']) > 1) ? $phpbb_root_path . "images/class_images/" . $adj['imagename'] . ".png" : '' , 
						'S_CLASS_IMAGE_EXISTS' => (strlen($adj['imagename']) > 1) ? true : false , 
						'U_VIEW_MEMBER' => (isset($adj['member_name'])) ? append_sid("{$phpbb_root_path}dkp.$phpEx", "page=viewmember&amp;" . URI_NAMEID . '=' . $adj['member_id'] . '&amp;' . URI_DKPSYS . '=' . $adj['adjustment_dkpid']) : '' , 
						'MEMBER' => (isset($adj['member_name'])) ? $adj['member_name'] : '' , 
						'REASON' => (isset($adj['adjustment_reason'])) ? $adj['adjustment_reason'] : '' , 
						'ADJUSTMENT' => $adj['adjustment_value'] , 
						'ADJ_DECAY' => $adj['adj_decay'] , 
						'can_decay' => $adj['can_decay'] , 
						'ADJUSTMENT_NET' => $adj['adjustment_value'] - $adj['adj_decay'] , 
						'DECAY_TIME' => $adj['decay_time'] , 
						'C_ADJUSTMENT' => ($adj['adjustment_value'] > 0 ? "positive" : "negative") , 
						'ADDED_BY' => (isset($adj['adjustment_added_by'])) ? $adj['adjustment_added_by'] : ''));
				}
				$db->sql_freeresult($result);
				$listadj_footcount = sprintf($user->lang['LISTADJ_FOOTCOUNT'], $total_adjustments, $config['bbdkp_user_alimit']);
				$pagination = generate_pagination(append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_adj&amp;mode=listiadj&amp;dkpsys_id=" . $dkpsys_id) . '&amp;' . URI_PAGE, $total_adjustments, $config['bbdkp_user_alimit'], $start, true);
				$template->assign_vars(array(
					'L_TITLE' => $user->lang['ACP_LISTIADJ'] , 
					'L_EXPLAIN' => $user->lang['ACP_LISTIADJ_EXPLAIN'] , 
					'S_SHOW' => ($hasrows == true) ? true : false , 
					'O_DATE' => $current_order['uri'][0] , 
					'O_DKPID' => $current_order['uri'][1] , 
					'O_DKPPOOL' => $current_order['uri'][2] , 
					'O_MEMBER' => $current_order['uri'][3] , 
					'O_REASON' => $current_order['uri'][4] , 
					'O_ADJUSTMENT' => $current_order['uri'][5] , 
					'O_ADDED_BY' => $current_order['uri'][6] , 
					'U_LIST_ADJUSTMENTS' => $u_list_adjustments , 
					'MEMBER_NAME' => $member_filter , 
					'START' => $start , 
					'S_GROUP_ADJ' => $s_group_adj , 
					'LISTADJ_FOOTCOUNT' => $listadj_footcount , 
					'ADJUSTMENT_PAGINATION' => $pagination));
				$this->page_title = 'ACP_LISTIADJ';
				$this->tpl_name = 'dkp/acp_' . $mode;
				break;
				
			/************************************
			 * 
			 * MANAGE INDIVIDUAL ADJUSTMENTS
			 * 
			 ************************************/
			case 'addiadj':
				$form_key = 'acp_dkp_adj';
				add_form_key($form_key);
				/***  DKPSYS drop-down ***/
				$dkpsys_id = 1;
				$sql = 'SELECT dkpsys_id, dkpsys_name, dkpsys_default 
                    FROM ' . DKPSYS_TABLE . '
                    ORDER BY dkpsys_name';
				$resultdkpsys = $db->sql_query($sql);
				$this->adjustment = array(
					'can_decay' => request_var('adj_decayable', 1) , 
					'adjustment_value' => request_var('adjustment_value', 0.00) , 
					'adjustment_reason' => utf8_normalize_nfc(request_var('adjustment_reason', ' ', true)) , 
					'member_names' => utf8_normalize_nfc(request_var('member_names', array(0 => ' '), true)));
						
				$adjust_id = request_var(URI_ADJUSTMENT, 0);
				$dkpsys_id = request_var(URI_DKPSYS, 0);
				if ($adjust_id != 0 && $dkpsys_id != 0)
				{
					// we have a get, process it and fill template default values
					$sql_array = array(
						'SELECT' => 'a.adjustment_value, 
								a.adjustment_dkpid, 
								a.adjustment_date, 
								a.adjustment_reason, 
								a.member_id, 
								m.member_name,
								a.adjustment_group_key, 
								a.can_decay' , 
						'FROM' => array(
							ADJUSTMENTS_TABLE => 'a' , 
							MEMBER_LIST_TABLE => 'm') , 
						'WHERE' => 'a.member_id = m.member_id   
								and a.adjustment_id = ' . $adjust_id . ' 
								AND a.adjustment_dkpid = ' . $dkpsys_id);
					$sql = $db->sql_build_query('SELECT', $sql_array);
					$result = $db->sql_query($sql);
					if (! $row = $db->sql_fetchrow($result))
					{
						trigger_error($user->lang['ERROR_INVALID_ADJUSTMENT'], E_USER_NOTICE);
					}
					$db->sql_freeresult($result);
					// add values to dropdown
					while ($row2 = $db->sql_fetchrow($resultdkpsys))
					{
						$template->assign_block_vars('adj_dkpid_row', array(
							'VALUE' => $row2['dkpsys_id'] , 
							'SELECTED' => ($row2['dkpsys_id'] == $row['adjustment_dkpid']) ? ' selected="selected"' : '' , 
							'OPTION' => (! empty($row2['dkpsys_name'])) ? $row2['dkpsys_name'] : '(None)'));
					}
					$this->time = $row['adjustment_date'];
					$this->adjustment = array(
						'can_decay' => $row['can_decay'] , 
						'adjustment_value' => $row['adjustment_value'] , 
						'adjustment_reason' => $row['adjustment_reason']);
					$members = array();
					$sql = " SELECT a.member_id , m.member_name 
								FROM " . ADJUSTMENTS_TABLE . " a , " . MEMBER_LIST_TABLE . " m  
								WHERE a.member_id =  m.member_id
								AND a.adjustment_dkpid = '" . (int) $row['adjustment_dkpid'] . "' 
								AND a.adjustment_group_key='" . $db->sql_escape($row['adjustment_group_key']) . "'";
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						$members[] = $row['member_name'];
					}
					$db->sql_freeresult($result);
					$this->adjustment['member_names'] = (isset($_POST['member_names'])) ? request_var('member_names', array(
						0 => ' '), true) : $members;
					unset($row, $members, $sql);
				}
				else
				{
					// we dont have a GET so put default dkp pool in pulldown
					while ($row2 = $db->sql_fetchrow($resultdkpsys))
					{
						//dkpsys_default
						$template->assign_block_vars('adj_dkpid_row', array(
							'VALUE' => $row2['dkpsys_id'] , 
							'SELECTED' => ($row2['dkpsys_default'] == 'Y') ? ' selected="selected"' : '' , 
							'OPTION' => (! empty($row2['dkpsys_name'])) ? $row2['dkpsys_name'] : '(None)'));
						if ($row2['dkpsys_default'] == 'Y')
						{
							$dkpsys_id = $row2['dkpsys_id'];
						}
					}
				}
				$submit = (isset($_POST['add'])) ? true : false;
				$update = (isset($_POST['update'])) ? true : false;
				$delete = (isset($_POST['delete'])) ? true : false;
				if ($submit || $update)
				{
					if (! check_form_key('acp_dkp_adj'))
					{
						trigger_error('FORM_INVALID');
					}
				}
				/************************************
				// ADD INDIVIDUAL ADJUSTMENT
				 ************************************/
				if ($submit)
				{
					// check form
					$errors_exist = $this->error_check_i();
					// Errors exist, redisplay the form
					if ($errors_exist)
					{
						trigger_error($user->lang['FV_FORMVALIDATION'], E_USER_WARNING);
					}
					$adjval = request_var('adjustment_value', 0.0);
					$adjreason = utf8_normalize_nfc(request_var('adjustment_reason', '', true));
					$member_names = request_var('member_names', array(0 => ' '), true);
					$candecay = request_var('adj_decayable', 1);
					//
					// get value from Pulldown !
					//
					$dkpsys_id = request_var('adj_dkpid', 0);
					$group_key = $this->gen_group_key($this->time, request_var('adjustment_reason', ' ', true), $adjval);
					//
					// Add adjustment to selected members
					//
					foreach ($member_names as $member_name)
					{
						$member_id = $class_members->get_member_id(utf8_normalize_nfc($member_name));
						$this->add_new_adjustment($dkpsys_id, $member_id, $group_key, $adjval, $adjreason, $candecay);
					}
					//
					// Logging
					//
					$log_action = array(
						'header' => 'L_ACTION_INDIVADJ_ADDED' , 
						'L_ADJUSTMENT' => $adjval , 
						'L_REASON' => $adjreason , 
						'L_MEMBERS' => implode(', ', $member_names) , 
						'L_ADDED_BY' => $user->data['username']);
					$this->log_insert(array(
						'log_type' => $log_action['header'] , 
						'log_action' => $log_action));
					$success_message = sprintf($user->lang['ADMIN_ADD_IADJ_SUCCESS'], $config['bbdkp_dkp_name'], $adjval, implode(', ', $member_names));
					trigger_error($success_message . $this->link);
				}
				
				/************************************
				// UPDATE INDIVIDUAL ADJUSTMENT
				 ************************************/
				if ($update)
				{
					$errors_exist = $this->error_check_i();
					// Errors exist, redisplay the form
					if ($errors_exist)
					{
						trigger_error($user->lang['FV_FORMVALIDATION'], E_USER_WARNING);
					}
					$dkpsys_id = request_var('hidden_dkpid', 0);
					$adjust_id = request_var('hidden_id', 0);
					$adjval = request_var('adjustment_value', 0.0);
					$adjreason = utf8_normalize_nfc(request_var('adjustment_reason', '', true));
					$member_names = utf8_normalize_nfc(request_var('member_names', array(0 => ' ')));
					$candecay = request_var('adj_decayable', 1);
					// remove old adjustment
					$this->remove_old_adjustment($adjust_id, $dkpsys_id);
					//
					// Generate a new group key
					//
					$group_key = $this->gen_group_key($this->time, $adjreason, $adjval);
					//
					// Add the new adjustment to selected members
					//
					$newdkpsys_id = request_var('adj_dkpid', 0);
					foreach ($member_names as $member_name)
					{
						$member_id = $class_members->get_member_id($member_name);
						$this->add_new_adjustment($newdkpsys_id, $member_id, $group_key, $adjval, $adjreason, $candecay);
					}
					//
					// Logging
					//
					$log_action = array(
						'header' => 'L_ACTION_INDIVADJ_UPDATED' , 
						'id' => $adjust_id , 
						'L_ADJUSTMENT_BEFORE' => $this->old_adjustment['adjustment_value'] , 
						'L_REASON_BEFORE' => $this->old_adjustment['adjustment_reason'] , 
						'L_MEMBERS_BEFORE' => implode(', ', $this->old_adjustment['member_names']) , 
						'L_ADJUSTMENT_AFTER' => $adjval , 
						'L_REASON_AFTER' => $adjreason , 
						'L_MEMBERS_AFTER' => implode(', ', $member_names) , 
						'L_UPDATED_BY' => $user->data['username']);
					$this->log_insert(array(
						'log_type' => $log_action['header'] , 
						'log_action' => $log_action));
					$success_message = sprintf($user->lang['ADMIN_UPDATE_IADJ_SUCCESS'], $config['bbdkp_dkp_name'], $adjval, implode(', ', $member_names));
					trigger_error($success_message . $this->link);
				}
				/************************************
				// DELETE INDIVIDUAL ADJUSTMENT
				 ************************************/
				if ($delete)
				{
					if (confirm_box(true))
					{
						// get form vars
						$adjust_id = request_var('xhidden_id', 0);
						$dkpsys_id = request_var('xhidden_dkpid', 0);
						$this->remove_old_adjustment($adjust_id, $dkpsys_id);
						//
						// Logging
						$log_action = array(
							'header' => 'L_ACTION_INDIVADJ_DELETED' , 
							'id' => $adjust_id , 
							'L_ADJUSTMENT' => $this->old_adjustment['adjustment_value'] , 
							'L_REASON' => $this->old_adjustment['adjustment_reason'] , 
							'L_MEMBERS' => implode(', ', $this->old_adjustment['member_names']));
						$this->log_insert(array(
							'log_type' => $log_action['header'] , 
							'log_action' => $log_action));
						//
						// Success messages
						$success_message = sprintf($user->lang['ADMIN_DELETE_IADJ_SUCCESS'], $config['bbdkp_dkp_name'], $this->old_adjustment['adjustment_value'], implode(', ', $this->old_adjustment['member_names']));
						trigger_error($success_message . $this->link);
					}
					else
					{
						$s_hidden_fields = build_hidden_fields(array(
							'delete' => true , 
							'xhidden_id' => request_var('hidden_id', 0) , 
							'xhidden_dkpid' => request_var('hidden_dkpid', 0)));
						$template->assign_vars(array(
							'S_HIDDEN_FIELDS' => $s_hidden_fields));
						confirm_box(false, $user->lang['CONFIRM_DELETE_IADJ'], $s_hidden_fields);
					}
				}
				$sql = 'SELECT member_name
				FROM ' . MEMBER_LIST_TABLE . '
				ORDER BY member_name';
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					if ($adjust_id)
					{
						$selected = (@in_array($row['member_name'], $this->adjustment['member_names'])) ? ' selected="selected"' : '';
					}
					else
					{
						$selected = (@in_array($row['member_name'], utf8_normalize_nfc(request_var('member_names', array(0 => ' '))))) ? ' selected="selected"' : '';
					}
					$template->assign_block_vars('members_row', array(
						'VALUE' => $row['member_name'] , 
						'SELECTED' => $selected , 
						'OPTION' => $row['member_name']));
				}
				$db->sql_freeresult($result);
				$template->assign_vars(array(
					'L_TITLE' => $user->lang['ACP_ADDIADJ'] , 
					'L_EXPLAIN' => $user->lang['ACP_ADDIADJ_EXPLAIN'] , 
					// Form vars
					'F_ADD_ADJUSTMENT' => append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_adj&amp;mode=addiadj") , 
					'ADJUSTMENT_ID' => $adjust_id , 
					'DKP_ID' => $dkpsys_id , 
					// Form values
					'ADJUSTMENT_VALUE' => $this->adjustment['adjustment_value'] , 
					'ADJUSTMENT_REASON' => $this->adjustment['adjustment_reason'] , 
					'MO' => date('m', $this->time) , 
					'D' => date('d', $this->time) , 
					'Y' => date('Y', $this->time) , 
					'H' => date('h', $this->time) , 
					'MI' => date('i', $this->time) , 
					'S' => date('s', $this->time) , 
					'CAN_DECAY_NO_CHECKED' => ($this->adjustment['can_decay'] == 0) ? ' checked="checked"' : '' , 
					'CAN_DECAY_YES_CHECKED' => ($this->adjustment['can_decay'] == 1) ? ' checked="checked"' : '' , 
					// Form validation
					'FV_MEMBERS' => $this->fv->generate_error('member_names') , 
					'FV_ADJUSTMENT' => $this->fv->generate_error('adjustment_value') , 
					'FV_MO' => $this->fv->generate_error('mo') , 
					'FV_D' => $this->fv->generate_error('d') , 
					'FV_Y' => $this->fv->generate_error('y') , 
					// Javascript messages
					'MSG_VALUE_EMPTY' => $user->lang['FV_REQUIRED_ADJUSTMENT'] , 
					// Buttons
					'S_ADD' => (! $adjust_id) ? true : false));
				$this->page_title = 'ACP_ADDIADJ';
				$this->tpl_name = 'dkp/acp_' . $mode;
				break;
		}
	}

	/** 
	 * remove old dkp adjustment values 
	 *  
	 */
	private function remove_old_adjustment ($adjust_id, $dkpsys_id)
	{
		global $db, $phpbb_root_path, $phpEx;
		$dkpsys_id = intval($dkpsys_id);
		$adjust_id = intval($adjust_id);
		if (! class_exists('acp_dkp_mm'))
		{
			include ($phpbb_root_path . 'includes/acp/acp_dkp_mm.' . $phpEx);
			$class_members = new acp_dkp_mm();
		}
		$adjustment_ids = array();
		$old_members = array();
		$sql_array = array(
			'SELECT' => 'a2.*' , 
			'FROM' => array(
				ADJUSTMENTS_TABLE => 'a1') , 
			'LEFT_JOIN' => array(
				array(
					'FROM' => array(
						ADJUSTMENTS_TABLE => 'a2') , 
					'ON' => 'a1.adjustment_group_key = a2.adjustment_group_key 
	            			and a1.adjustment_dkpid = a2.adjustment_dkpid ')) , 
			'WHERE' => 'a1.adjustment_dkpid=  ' . $dkpsys_id . ' 
	    				AND a1.adjustment_id= ' . $adjust_id);
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$adjustment_ids[] = $row['adjustment_id'];
			$old_memberids[] = $row['member_id'];
			$old_membernames[] = $row['member_id'];
			$this->old_adjustment = array(
				'adjustment_value' => $row['adjustment_value'] , 
				'adjustment_date' => $row['adjustment_date'] , 
				'member_ids' => $old_memberids , 
				'member_names' => $old_membernames , 
				'adjustment_reason' => $row['adjustment_reason'] , 
				'adj_decay' => $row['adj_decay']);
		}
		//
		// Remove the adjustment value from adjustments table
		//
		$sql = 'DELETE FROM ' . ADJUSTMENTS_TABLE . '
        		WHERE adjustment_dkpid = ' . $dkpsys_id . '  and ' . $db->sql_in_set('adjustment_id', $adjustment_ids, false, true);
		$db->sql_query($sql);
		$sql = 'UPDATE ' . MEMBER_DKP_TABLE . '
                SET member_adjustment = member_adjustment - ' . (float) $this->old_adjustment['adjustment_value'] . ',
                adj_decay = adj_decay - ' . (float) $this->old_adjustment['adj_decay'] . '   
                WHERE  member_dkpid = ' . $dkpsys_id . ' AND ' . $db->sql_in_set('member_id', $this->old_adjustment['member_ids'], false, true);
		$db->sql_query($sql);
	}

	/** 
	 * add a new dkp adjustment
	 * 
	 */
	public function add_new_adjustment ($dkpid, $member_id, $group_key, $adjval, $adjreason, $candecay = 0)
	{
		global $user, $db;
		// no global scope
		$member_id = (int) $member_id;
		$adjval = (float) $adjval;
		$dkpsys_id = (int) $dkpid;
		if ($member_id == 0)
		{
			trigger_error($user->lang['ERROR_MEMBERNOTFOUND'], E_USER_WARNING);
		}
		//
		// does member have a dkp record ?
		//
		$sql = 'SELECT count(member_id) as membercount FROM  ' . MEMBER_DKP_TABLE . '
                WHERE member_id = ' . $member_id . '  
         		AND member_dkpid = ' . $dkpsys_id;
		$result = $db->sql_query($sql);
		$membercount = (int) $db->sql_fetchfield('membercount');
		if ($membercount == 1)
		{
			// (s)he does. lets update
			$sql = 'UPDATE ' . MEMBER_DKP_TABLE . '
                SET member_adjustment = member_adjustment + ' . $adjval . "
                WHERE member_id='" . $member_id . "'
        		AND member_dkpid = " . $dkpsys_id;
			$db->sql_query($sql);
			unset($sql);
		}
		elseif ($membercount == 0)
		{
			// new kid on the block
			$query = $db->sql_build_array('INSERT', array(
				'member_dkpid' => $dkpsys_id , 
				'member_id' => $member_id , 
				'member_earned' => 0.00 , 
				'member_spent' => 0.00 , 
				'member_adjustment' => $adjval , 
				'member_status' => 1 , 
				'member_firstraid' => 0 , 
				'member_lastraid' => 0 , 
				'member_raidcount' => 0));
			$db->sql_query('INSERT INTO ' . MEMBER_DKP_TABLE . $query);
		}
		
		//
		// Add the adjustment to the database
		//
		$query = $db->sql_build_array('INSERT', array(
			'adjustment_dkpid' => $dkpsys_id , 
			'adjustment_value' => $adjval , 
			'adjustment_date' => $this->time , 
			'member_id' => $member_id , 
			'adjustment_reason' => $adjreason , 
			'adjustment_group_key' => $group_key , 
			'can_decay' => $candecay , 
			'adjustment_added_by' => $user->data['username']));
		$db->sql_query('INSERT INTO ' . ADJUSTMENTS_TABLE . $query);
	}

	/** 
	 * validationfunction for adjustment values : required and numeric, date is in range
	 * @access private 
	 */
	private function error_check_i ()
	{
		global $user;
		if (! isset($_POST['member_names']))
		{
			$this->fv->errors['member_names'] = $user->lang['FV_REQUIRED_MEMBERS'];
		}
		$this->fv->is_number(request_var('adjustment_value', 0.00), $user->lang['FV_NUMBER_ADJUSTMENT']);
		$this->fv->is_filled(request_var('adjustment_value', 0.00), $user->lang['FV_REQUIRED_ADJUSTMENT']);
		$this->fv->is_within_range(request_var('mo', 0), 1, 12, $user->lang['FV_RANGE_MONTH']);
		$this->fv->is_within_range(request_var('d', 0), 1, 31, $user->lang['FV_RANGE_DAY']);
		$this->fv->is_within_range(request_var('y', 0), 1998, 2015, $user->lang['FV_RANGE_YEAR']);
		$this->time = mktime(0, 0, 0, request_var('mo', 0), request_var('d', 0), request_var('y', 0));
		return $this->fv->is_error();
	}

	/**
	 * Recalculates and updates adjustment decay
	 * @param $mode 1 for recalculating, 0 for setting decay to zero.
	 */
	public function sync_adjdecay ($mode, $origin = '')
	{
		global $user, $db;
		switch ($mode)
		{
			case 0:
				//  Decay = OFF : set all decay to 0
				//  update item detail to new decay value
				$sql = 'UPDATE ' . ADJUSTMENTS_TABLE . ' SET adj_decay = 0 ';
				$db->sql_query($sql);
				$sql = 'UPDATE ' . MEMBER_DKP_TABLE . ' SET adj_decay = 0 ';
				$db->sql_query($sql);
				if ($origin == 'cron')
				{
					$origin = $user->lang['DECAYCRON'];
				}
				return true;
				break;
			case 1:
				// Decay is ON : synchronise
				// loop all ajustments
				$sql = 'SELECT adjustment_dkpid, adjustment_id, member_id , adjustment_date, adjustment_value, adj_decay FROM ' . ADJUSTMENTS_TABLE . ' WHERE can_decay = 1';
				$result = $db->sql_query($sql);
				$countadj = 0;
				while (($row = $db->sql_fetchrow($result)))
				{
					$this->decayadj($row['adjustment_id'], $row['adjustment_dkpid'], $row['member_id'], $row['adjustment_date'], $row['adjustment_value'], $row['adj_decay']);
					$countadj ++;
				}
				$db->sql_freeresult($result);
				return $countadj;
				break;
		}
	}

	/**
	 * function to decay one specific adjustment
	 * @param int adj_id the adjustment id to decay
	 * @param int $dkpid dkpid for adapting accounts
	 */
	private function decayadj ($adj_id, $dkpid, $member_id, $adjdate, $value, $olddecay)
	{
		global $config, $db;
		$now = getdate();
		$timediff = mktime($now['hours'], $now['minutes'], $now['seconds'], $now['mon'], $now['mday'], $now['year']) - $adjdate;
		$i = (float) $config['bbdkp_adjdecaypct'] / 100;
		// get decay frequency
		$freq = $config['bbdkp_decayfrequency'];
		if ($freq == 0)
		{
			//frequency can't be 0. throw error
			trigger_error($user->lang['FV_FREQUENCY_NOTZERO'], E_USER_WARNING);
		}
		//pick decay frequency type (0=days, 1=weeks, 2=months) and convert timediff to that
		$t = 0;
		switch ($config['bbdkp_decayfreqtype'])
		{
			case 0:
				//days
				$t = (float) $timediff / 86400;
				break;
			case 1:
				//weeks
				$t = (float) $timediff / (86400 * 7);
				break;
			case 2:
				//months
				$t = (float) $timediff / (86400 * 30.44);
				break;
		}
		// take the integer part of time and interval division base 10, 
		// since we only decay after a set interval
		$n = intval($t / $freq, 10);
		//calculate rounded adjustment decay, defaults to rounds half up PHP_ROUND_HALF_UP, so 9.495 becomes 9.50
		$decay = round($value * (1 - pow(1 - $i, $n)), 2);
		// update adj detail to new decay value
		$sql = 'UPDATE ' . ADJUSTMENTS_TABLE . ' 
			SET adj_decay = ' . $decay . ", decay_time = " . $n . " 
			WHERE adjustment_id = " . (int) $adj_id;
		$db->sql_query($sql);
		// update dkp account, deduct old, add new decay
		$sql = 'UPDATE ' . MEMBER_DKP_TABLE . ' SET adj_decay = adj_decay - ' . $olddecay . ' + ' . $decay . " 
			WHERE member_id = " . (int) $member_id . ' 
			and member_dkpid = ' . $dkpid;
		$db->sql_query($sql);
		return true;
	}
}
?>