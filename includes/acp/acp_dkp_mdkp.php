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
if (! defined ( 'IN_PHPBB' ))
{
	exit ();
}

if (! defined ( 'EMED_BBDKP' ))
{
	$user->add_lang ( array (
		'mods/dkp_admin' ) );
	trigger_error ( $user->lang ['BBDKPDISABLED'], E_USER_WARNING );
}

/**
 * This class manages member DKP
 * 
 */
class acp_dkp_mdkp extends bbDKP_Admin
{

	public $u_action;
	private $link;
	
	public function main($id, $mode)
	{
		global $db, $user, $auth, $template, $sid, $cache;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;
		$user->add_lang ( array ('mods/dkp_admin' ) );
		$user->add_lang ( array ('mods/dkp_common' ) );
		$this->link = '<br /><a href="' . append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_mdkp&mode=mm_listmemberdkp" ) . '"><h3>' . $user->lang ['RETURN_DKPINDEX'] . '</h3></a>';
		
		switch ($mode)
		{
			/*-----------------------
				  LIST DKP
			-------------------------*/
			case 'mm_listmemberdkp':

				/* initialise */

				$dkpsys_id = 0;
				$submit = (isset ( $_POST ['dkpsys_id'] )) ? true : false;
				
				/***  DKPSYS drop-down query ***/
				$sql = 'SELECT dkpsys_id, dkpsys_name , dkpsys_default 
			        FROM ' . DKPSYS_TABLE . '  
			        GROUP BY dkpsys_id, dkpsys_name , dkpsys_default  ';
				if ($submit)
				{
					$dkpsys_id = request_var ( 'dkpsys_id', 0 );
				}
				else
				{
					$result = $db->sql_query ( $sql );
					while ( $row = $db->sql_fetchrow ( $result ) )
					{
						if ($row ['dkpsys_default'] == "Y")
						{
							$dkpsys_id = $row ['dkpsys_id'];
						}
					}
					$db->sql_freeresult ($result);
					
					if ($dkpsys_id == 0)
					{
						$result = $db->sql_query_limit ( $sql, 1 );
						while ( $row = $db->sql_fetchrow ( $result ) )
						{
							$dkpsys_id = $row ['dkpsys_id'];
						}
						$db->sql_freeresult ($result);
					}
					
				}
				
				$result = $db->sql_query ( $sql );
				while ( $row = $db->sql_fetchrow ( $result ) )
				{
					$template->assign_block_vars ( 'dkpsys_row', array (
						'VALUE' => $row ['dkpsys_id'], 
						'SELECTED' => ($row ['dkpsys_id'] == $dkpsys_id) ? ' selected="selected"' : '', 
						'OPTION' => (! empty ( $row ['dkpsys_name'] )) ? $row ['dkpsys_name'] : '(None)' ) );
					$dkpsys_name [$row ['dkpsys_id']] = $row ['dkpsys_name'];
				}
				$db->sql_freeresult ( $result );
				/***  end drop-down query ***/
				
				/* check if page was posted back */
				$activate = (isset ( $_POST ['submit_activate'] )) ? true : false;
				if ($activate)
				{
					$active_members = request_var ( 'activate_ids', array (0));
					$db->sql_transaction ( 'begin' );
					
					$sql1 = 'UPDATE ' . MEMBER_DKP_TABLE . "
                        SET member_status = '1' 
                        WHERE  member_dkpid  = " . $dkpsys_id . ' 
                        AND ' . $db->sql_in_set ( 'member_id', $active_members, false, true );
					$db->sql_query ( $sql1 );
					
					$sql2 = 'UPDATE ' . MEMBER_DKP_TABLE . "
                        SET member_status = '0' 
                        WHERE  member_dkpid  = " . $dkpsys_id . ' 
                        AND ' . $db->sql_in_set ( 'member_id', $active_members, true, true );
					$db->sql_query ( $sql2 );
					
					$db->sql_transaction ( 'commit' );
				}
				
				$member_count = 0;
				
				$sql_array = array (
					'SELECT' => 'm.member_id,  a.member_name, a.member_level, m.member_dkpid, 
						m.member_raid_value, m.member_earned, m.member_adjustment, m.member_spent,  
						(m.member_earned + m.member_adjustment - m.member_spent + m.member_item_decay - m.adj_decay) AS member_current,
						m.member_status, m.member_lastraid,
						s.dkpsys_name, l.name AS member_class, r.rank_name, r.rank_prefix, r.rank_suffix, c.colorcode , c.imagename', 
					'FROM' => array (
						MEMBER_LIST_TABLE => 'a', 
						MEMBER_DKP_TABLE => 'm', 
						MEMBER_RANKS_TABLE => 'r', 
						CLASS_TABLE => 'c', 
						BB_LANGUAGE => 'l', 
						DKPSYS_TABLE => 's' ), 
					'WHERE' => "(a.member_rank_id = r.rank_id)
		    			AND (a.member_guild_id = r.guild_id)   
						AND (a.member_id = m.member_id) 
						AND (a.member_class_id = c.class_id and a.game_id = c.game_id)  
						AND (m.member_dkpid = s.dkpsys_id)   
						AND l.attribute_id = c.class_id  
						AND l.game_id = c.game_id AND l.language= '" . $config ['bbdkp_lang'] . "' AND l.attribute = 'class'    		
						AND (s.dkpsys_id = " . (int) $dkpsys_id . ')' );
				
				/***  sort  ***/
				
				$previous_data = '';
				$sort_order = array (
					0 => array ('m.member_status', 'm.member_status desc' ), 
					1 => array ('a.member_name', 'a.member_name desc' ), 
					2 => array ('r.rank_name', 'r.rank_name desc' ), 
					3 => array ('a.member_level desc', 'a.member_level' ), 
					4 => array ('a.member_class', 'a.member_class desc' ), 
					5 => array ('m.member_raid_value desc', 'm.member_raid_value' ), 
					8 => array ('m.member_earned', 'm.member_earned desc' ), 
					10 => array ('m.member_adjustment desc', 'm.member_adjustment' ), 
					12 => array ('m.member_spent desc', 'm.member_spent' ), 
					16 => array ('member_current desc', 'member_current' ), 
					17 => array ('m.member_lastraid desc', 'm.member_lastraid' ) );
				
				if ($config ['bbdkp_timebased'] == 1)
				{
					$sql_array ['SELECT'] .= ', m.member_time_bonus';
					$sort_order [6] = array ('m.member_time_bonus desc', 'm.member_time_bonus' );
				}
				
				if ($config ['bbdkp_zerosum'] == 1)
				{
					$sql_array ['SELECT'] .= ', m.member_zerosum_bonus';
					$sort_order [7] = array ('member_zerosum_bonus desc', 'member_zerosum_bonus' );
				}
				
				if ($config ['bbdkp_decay'] == 1)
				{
					$sql_array ['SELECT'] .= ', m.member_raid_decay , m.adj_decay, m.member_item_decay ';
					$sort_order [9] = array ('(m.member_raid_decay +  m.adj_decay) desc', ' (m.member_raid_decay +  m.adj_decay) ' );
					$sort_order [13] = array ('m.member_item_decay desc', 'member_item_decay' );
				}
				
				if ($config ['bbdkp_epgp'] == 1)
				{
					$sql_array ['SELECT'] .= ', 
					(m.member_earned + m.member_adjustment - m.adj_decay) AS ep, 
					(m.member_spent - m.member_item_decay  + ' . max ( 0, $config ['bbdkp_basegp'] ) . ' ) AS gp, 
					CASE when (m.member_spent - m.member_item_decay + ' . max ( 0, $config ['bbdkp_basegp'] ) . ' ) = 0 then 1  
					ELSE round((m.member_earned + m.member_adjustment - m.adj_decay) / 
					(' . max ( 0, $config ['bbdkp_basegp'] ) . ' + m.member_spent - m.member_item_decay),2) end as pr ';
					$sort_order [11] = array ('ep desc', 'ep' );
					$sort_order [14] = array ('gp desc', 'gp' );
					$sort_order [15] = array ('pr desc', 'pr' );
				}
				
				$current_order = switch_order ( $sort_order );
				$previous_data = '';
				$sort_index = explode ( '.', $current_order ['uri'] ['current'] );
				$previous_source = preg_replace ( '/( (asc|desc))?/i', '', $sort_order [$sort_index [0]] [$sort_index [1]] );
				
				$sql_array ['ORDER_BY'] = $current_order ['sql'];
				
				$sql = $db->sql_build_query ( 'SELECT', $sql_array );
				$members_result = $db->sql_query ( $sql );
				
				$lines = 0;
				
				$members_row = array ();
				
				while ( $row = $db->sql_fetchrow ( $members_result ) )
				{
					++ $member_count;
					++ $lines;
					
					$members_row = array (
						'STATUS' => ($row ['member_status'] == 1) ? 'checked="checked" ' : '', 
						'ID' => $row ['member_id'], 
						'DKPID' => $row ['member_dkpid'], 
						'DKPSYS_S' => $dkpsys_id, 
						'DKPSYS_NAME' => $row ['dkpsys_name'], 
						'CLASS' => ($row ['member_class'] != 'NULL') ? $row ['member_class'] : '&nbsp;', 
						'COLORCODE' => ($row ['colorcode'] == '') ? '#123456' : $row ['colorcode'], 
						'CLASS_IMAGE' => (strlen ( $row ['imagename'] ) > 1) ? $phpbb_root_path . "images/class_images/" . $row ['imagename'] . ".png" : '', 
						'NAME' => $row ['rank_prefix'] . $row ['member_name'] . $row ['rank_suffix'], 
						'S_CLASS_IMAGE_EXISTS' => (strlen ( $row ['imagename'] ) > 1) ? true : false, 
						'RANK' => $row ['rank_name'], 
						'LEVEL' => ($row ['member_level'] > 0) ? $row ['member_level'] : '&nbsp;', 
						'RAIDVAL' => $row ['member_raid_value'], 
						'EARNED' => $row ['member_earned'], 
						'ADJUSTMENT' => $row ['member_adjustment'], 
						'SPENT' => $row ['member_spent'], 
						'CURRENT' => $row ['member_current'], 
						'LASTRAID' => (! empty ( $row ['member_lastraid'] )) ? date ( $config ['bbdkp_date_format'], $row ['member_lastraid'] ) : '&nbsp;', 
						'U_VIEW_MEMBER' => append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_mdkp&amp;mode=mm_editmemberdkp" ) . '&amp;member_id=' . $row ['member_id'] . '&amp;' . URI_DKPSYS . '=' . $row ['member_dkpid'] );
		
					if ($config ['bbdkp_timebased'] == 1)
					{
						$members_row ['TIMEBONUS'] = $row ['member_time_bonus'];
					
					}
					
					if ($config ['bbdkp_zerosum'] == 1)
					{
						$members_row ['ZEROSUM'] = $row ['member_zerosum_bonus'];
					
					}
					
					if ($config ['bbdkp_decay'] == 1)
					{
						$members_row ['RAIDDECAY'] = $row ['member_raid_decay'] + $row ['adj_decay'];
						$members_row ['ITEMDECAY'] = $row ['member_item_decay'];
					}
					
					if ($config ['bbdkp_epgp'] == 1)
					{
						$members_row ['EP'] = $row ['ep'];
						$members_row ['GP'] = $row ['gp'];
						$members_row ['PR'] = $row ['pr'];
					}
					
					$template->assign_block_vars ( 'members_row', $members_row );
					
					// unset array 

					unset ( $members_row );
				}
				
				$db->sql_freeresult ( $members_result );
				
				/***  Labels  ***/
				$footcount_text = sprintf ( $user->lang ['LISTMEMBERS_FOOTCOUNT'], $lines );
				
				$output = array (
					'F_MEMBERS' => append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_mdkp&amp;mode=mm_listmemberdkp&amp;" ) . '&amp;mode=mm_editmemberdkp', 
					'L_TITLE' => $user->lang ['ACP_DKP_LISTMEMBERDKP'], 
					'L_EXPLAIN' => $user->lang ['ACP_MM_LISTMEMBERDKP_EXPLAIN'], 
					'BUTTON_NAME' => 'delete', 
					'BUTTON_VALUE' => $user->lang ['DELETE_SELECTED_MEMBERS'], 
					'O_STATUS' => $current_order ['uri'] [0], 
					'O_NAME' => $current_order ['uri'] [1], 
					'O_RANK' => $current_order ['uri'] [2], 
					'O_LEVEL' => $current_order ['uri'] [3], 
					'O_CLASS' => $current_order ['uri'] [4], 
					'O_RAIDVALUE' => $current_order ['uri'] [5], 
					'O_EARNED' => $current_order ['uri'] [8], 
					'O_ADJUSTMENT' => $current_order ['uri'] [10], 
					'O_SPENT' => $current_order ['uri'] [12], 
					'O_CURRENT' => $current_order ['uri'] [16], 
					'O_LASTRAID' => $current_order ['uri'] [17], 
					'S_SHOWZS' => ($config ['bbdkp_zerosum'] == '1') ? true : false, 
					'S_SHOWDECAY' => ($config ['bbdkp_decay'] == '1') ? true : false, 
					'S_SHOWEPGP' => ($config ['bbdkp_epgp'] == '1') ? true : false, 
					'S_SHOWTIME' => ($config ['bbdkp_timebased'] == '1') ? true : false, 
					'U_LIST_MEMBERDKP' => append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_mdkp&amp;" . URI_DKPSYS . "=" . $dkpsys_id . "&amp;mode=mm_listmemberdkp" ) . '&amp;mod=list&amp;', 
					'S_NOTMM' => false, 
					'LISTMEMBERS_FOOTCOUNT' => $footcount_text, 
					'DKPSYS' => $dkpsys_id, 
					'DKPSYSNAME' => $dkpsys_name [$dkpsys_id] );
		
				if ($config ['bbdkp_timebased'] == 1)
				{
					$output ['O_TIMEBONUS'] = $current_order ['uri'] [6];
				
				}
				
				if ($config ['bbdkp_zerosum'] == 1)
				{
					$output ['O_ZSBONUS'] = $current_order ['uri'] [7];
				
				}
				
				if ($config ['bbdkp_decay'] == 1)
				{
					$output ['O_RDECAY'] = $current_order ['uri'] [9];
					$output ['O_IDECAY'] = $current_order ['uri'] [13];
				}
				
				if ($config ['bbdkp_epgp'] == 1)
				{
					$output ['O_EP'] = $current_order ['uri'] [11];
					$output ['O_GP'] = $current_order ['uri'] [14];
					$output ['O_PR'] = $current_order ['uri'] [15];
				}
				
				$template->assign_vars ( $output );
				
				$this->page_title = 'ACP_DKP_LISTMEMBERDKP';
				$this->tpl_name = 'dkp/acp_' . $mode;
				break;
			
			/************************************

				  DKP EDIT

			 *************************************/
			case 'mm_editmemberdkp' :
				// invisible module

				$member_id = request_var ( URI_NAMEID, 0 );
				$dkp_id = request_var ( URI_DKPSYS, 0 );
				
				$update = (isset ( $_POST ['update'] )) ? true : false;
				$delete = (isset ( $_POST ['delete'] )) ? true : false;
				if ($update || $delete)
				{
					if (! check_form_key ( 'mm_editmemberdkp' ))
					{
						trigger_error ( 'FORM_INVALID' );
					}
				}
				
				if ($update)
				{
					$member_id = request_var ( 'hidden_id', 0 );
					$dkp_id = request_var ( 'hidden_dkpid', 0 );
					
					$sql_array = array (
						'SELECT' => 'l.member_name, m.member_id, 
		    				m.member_raid_value, m.member_time_bonus, m.member_zerosum_bonus, 
		    				m.member_earned,
		    				m.member_raid_decay, m.member_adjustment ,
		    				m.member_spent, m.member_item_decay ', 
		    			'FROM' => array (
							MEMBER_DKP_TABLE => 'm', 
							MEMBER_LIST_TABLE => 'l' ), 
						'WHERE' => 'm.member_id = l.member_id and m.member_id=' . $member_id . ' 
							AND m.member_dkpid=' . $dkp_id );
					
					$sql = $db->sql_build_query ( 'SELECT', $sql_array );
					$result = $db->sql_query ( $sql );
					while ( $row = $db->sql_fetchrow ( $result ) )
					{
						$this->old_member = array (
						'member_name' => $row ['member_name'], 
						'member_id' => $row ['member_id'], 
						'member_earned' => $row ['member_earned'], 
						'member_raid_value' => $row ['member_raid_value'], 
						'member_time_bonus' => $row ['member_time_bonus'], 
						'member_zerosum_bonus' => $row ['member_zerosum_bonus'], 
						'member_spent' => $row ['member_spent'] );
					}
					$db->sql_freeresult ( $result );
					
					$db->sql_transaction ( 'begin' );
					$query = $db->sql_build_array ( 'UPDATE', array (
						'member_raid_value' => request_var ( 'raid_value', 0.00 ), 
						'member_time_bonus' => request_var ( 'time_value', 0.00 ), 
						'member_zerosum_bonus' => request_var ( 'zerosum', 0.00 ), 
						'member_earned' => request_var ( 'earned', 0.00 ), 
						'member_raid_decay' => request_var ( 'rdecay', 0.00 ), 
						'member_spent' => request_var ( 'spent', 0.00 ), 
						'member_item_decay' => request_var ( 'idecay', 0.00 ) ) );
					
					$db->sql_query ( 'UPDATE ' . MEMBER_DKP_TABLE . ' 
							SET ' . $query . ' 
					        WHERE member_id = ' . $this->old_member ['member_id'] . '
							AND member_dkpid= ' . ( int ) $dkp_id );
					$db->sql_transaction ( 'commit' );
					
					$log_action = array (
						'header' => 'L_ACTION_MEMBERDKP_UPDATED', 
						'L_USER' => $user->data ['user_id'], 
						'L_USERCOLOUR' => $user->data ['user_colour'], 
						'L_NAME' => $this->old_member ['member_name'], 
						'L_EARNED_BEFORE' => $this->old_member ['member_earned'], 
						'L_SPENT_BEFORE' => $this->old_member ['member_spent'], 
						'L_EARNED_AFTER' => request_var ( 'earned', 0.00 ), 
						'L_SPENT_AFTER' => request_var ( 'spent', 0.00 ) );
					
					$this->log_insert ( array (
						'log_type' => $log_action ['header'], 'log_action' => $log_action ) );
					
					$success_message = sprintf ( $user->lang ['ADMIN_UPDATE_MEMBERDKP_SUCCESS'], $this->old_member ['member_name'] );
					trigger_error ( $success_message . $this->link );
				}
				elseif ($delete)
				{
					
					if (((isset ( $_POST ['hidden_id'] )) and (isset ( $_POST ['hidden_dkpid'] ))) == true)
					{
						
						$del_member = request_var ( 'hidden_id', 0 );
						$del_dkpid = request_var ( 'hidden_dkpid', 0 );
						
						// get data on dkp to be deleted

						$sql_array = array (
							'SELECT' => 'm.member_name, d.member_id, d.member_earned, d.member_spent, d.member_adjustment', 
							'FROM' => array (
								MEMBER_LIST_TABLE => 'm', 
								MEMBER_DKP_TABLE => 'd' ), 
							'WHERE' => "m.member_id = ' . $del_member . '

			       				AND d.member_dkpid = " . $del_dkpid . '

			   					AND d.member_id = m.member_id' );
						
						$sql = $db->sql_build_query ( 'SELECT', $sql_array );
						$result = $db->sql_query ( $sql );
						while ( $row = $db->sql_fetchrow ( $result ) )
						{
							$this->old_member = array (
								'member_id' => $del_member, 
								'member_name' => $row ['member_name'], 
								'member_earned' => ( float ) $row ['member_earned'], 
								'member_spent' => ( float ) $row ['member_spent'], 
								'member_adjustment' => (float) $row ['member_adjustment'] );
						}
						$db->sql_freeresult ( $result );
						
						if (confirm_box (true))
						{
							// begin transaction

							$db->sql_transaction ('begin');
							
							$names = $del_member;
							//remove member from attendees table but only if linked to raids in selected dkp pool

							$sql = 'DELETE FROM ' . RAID_DETAIL_TABLE . '
									WHERE member_id= ' . $del_member . ' 
									AND raid_id IN( SELECT r.raid_id 
										FROM ' . RAIDS_TABLE . ' r, ' . EVENTS_TABLE . ' e 

										WHERE r.event_id = e.event_id 
										AND e.event_dkpid = ' . ( int ) $del_dkpid . ')';
							$db->sql_query ( $sql );
							
							$sql = 'DELETE FROM ' . RAID_ITEMS_TABLE . ' 
								WHERE member_id = ' . $del_member . ' and raid_id 
								IN ( 

								SELECT raid_id FROM ' . RAIDS_TABLE . ' r , ' . 
								EVENTS_TABLE . ' e 
								WHERE r.event_id  = e.event_id 
								AND e.event_dkpid = ' . ( int ) $del_dkpid . ')';
							$db->sql_query ( $sql );
							
							//delete player adjustments

							$sql = 'DELETE FROM ' . ADJUSTMENTS_TABLE . '
									WHERE member_id =' . $del_member . '
									AND adjustment_dkpid= ' . $del_dkpid;
							$db->sql_query ( $sql );
							
							//delete player dkp points

							$sql = 'DELETE FROM ' . MEMBER_DKP_TABLE . ' 
								WHERE member_id = ' . $del_member . ' AND member_dkpid= ' . $del_dkpid;
							$db->sql_query ( $sql );
							
							//commit

							$db->sql_transaction ( 'commit' );
							
							$log_action = array (
								'header' => 'ACTION_MEMBERDKP_DELETED', 
									'L_NAME' => $this->old_member ['member_name'], 
									'L_EARNED' => $this->old_member ['member_earned'], 
									'L_SPENT' => $this->old_member ['member_spent'], 
									'L_ADJUSTMENT' => $this->old_member ['member_adjustment'] );
							
							$this->log_insert ( array (
								'log_type' => $log_action ['header'], 
								'log_action' => $log_action ) );
							
							$success_message = sprintf ( $user->lang ['ADMIN_DELETE_MEMBERDKP_SUCCESS'], $del_member, $del_dkpid );
							trigger_error ( $success_message . $this->link );
						}
						else
						{
							$s_hidden_fields = build_hidden_fields ( array (
								'delete' => true, 
								'hidden_id' => $del_member, 
								'hidden_dkpid' => $del_dkpid, 
								'old_member' => $this->old_member ));
							confirm_box ( false, $user->lang ['CONFIRM_DELETE_MEMBERDKP'], $s_hidden_fields );
						
						}
					}
					else
					{
						$success_message = sprintf ( $user->lang ['ADMIN_DELETE_MEMBERDKP_FAILED'], 'UNKNOWN', 'UNKNOWN' );
						trigger_error ( $success_message . $this->link, E_USER_WARNING );
					}
					
					redirect ( append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_mdkp&amp;mode=mm_listmemberdkp&amp;" ) );
				
				}
				
				/* template filling */
				
				// Get their correct earned
				$sql_array = array (
					'SELECT' => 'sum(ra.raid_value) AS raid_value, sum(ra.time_bonus) AS time_bonus, 
				    		sum(ra.zerosum_bonus) AS zerosum_bonus, sum(ra.raid_decay) AS raid_decay   ', 
				    'FROM' => array (
						EVENTS_TABLE => 'e', 
						RAIDS_TABLE => 'r', 
						RAID_DETAIL_TABLE => 'ra' ), 
					'WHERE' => ' ra.raid_id = r.raid_id 
				    	and e.event_id = r.event_id
						and ra.member_id=' . $member_id . '
						and e.event_dkpid=' . (int) $dkp_id );
				
				$sql = $db->sql_build_query ( 'SELECT', $sql_array );
				$result = $db->sql_query ( $sql );
				while ( $row = $db->sql_fetchrow ( $result ) )
				{
					$correct_raid_value = $row ['raid_value'];
					$correct_time_bonus = $row ['time_bonus'];
					$correct_zerosum_bonus = $row ['zerosum_bonus'];
					$correct_raid_decay = $row ['raid_decay'];
				}
				$db->sql_freeresult ( $sql );
				
				// Get their correct spent

				$sql_array = array (
					'SELECT' => 'SUM(i.item_value) AS item_value, 
							SUM(i.item_decay) AS item_decay  ', 
					'FROM' => array (
						EVENTS_TABLE => 'e', 
						RAIDS_TABLE => 'r', 
						RAID_ITEMS_TABLE => 'i' ), 
					'WHERE' => 'e.event_id = r.event_id 
	    				and r.raid_id = i.raid_id 
						and i.member_id=' . $member_id . '
						and e.event_dkpid=' . ( int ) $dkp_id );
				$sql = $db->sql_build_query ( 'SELECT', $sql_array );
				$result = $db->sql_query ( $sql );
				while ( $row = $db->sql_fetchrow ( $result ) )
				{
					$correct_spent = $row ['item_value'];
					$correct_itemdecay = $row ['item_decay'];
				}
				$db->sql_freeresult ( $sql );
				
				// get Actual dkp points from account

				$sql_array = array (
					'SELECT' => '
				    	a.*, 
						m.member_id, 
						m.member_dkpid, 
						m.member_raid_value,
						m.member_time_bonus,
						m.member_zerosum_bonus, 
						m.member_earned,
						m.member_raid_decay, 
						m.member_adjustment, 
						(m.member_earned + m.member_adjustment - m.adj_decay) AS ep	,
						m.member_spent,
						m.member_item_decay,
						(m.member_spent - m.member_item_decay  + ' . max ( 0, $config ['bbdkp_basegp'] ) . ' ) AS gp,
						(m.member_earned + m.member_adjustment - m.member_spent + m.member_item_decay - m.adj_decay ) AS member_current,
						case when (m.member_spent - m.member_item_decay + ' . max ( 0, $config ['bbdkp_basegp'] ) . ' ) = 0 then 1 
						else round( (m.member_earned + m.member_adjustment - m.adj_decay) 
							/ ( ' . max ( 0, $config ['bbdkp_basegp'] ) . ' + m.member_spent - m.member_item_decay),2) end as pr,
						m.adj_decay, 
						m.member_lastraid,
						r1.name AS member_race,
						s.dkpsys_name, 
						l.name AS member_class, 
						r.rank_name, 
						r.rank_prefix, 
						r.rank_suffix, 
						c.class_armor_type AS armor_type ,
						c.colorcode, 
						c.imagename ', 
					'FROM' => array (
						MEMBER_LIST_TABLE => 'a', 
						MEMBER_DKP_TABLE => 'm', 
						MEMBER_RANKS_TABLE => 'r', 
						CLASS_TABLE => 'c', 
						BB_LANGUAGE => 'l', 
						DKPSYS_TABLE => 's' ), 
					
					'LEFT_JOIN' => array (
						array (
							'FROM' => array (
								BB_LANGUAGE => 'r1' ), 
							'ON' => "r1.attribute_id = a.member_race_id 
								AND r1.language= '" . $config ['bbdkp_lang'] . "' 
								AND r1.attribute = 'race' 
								AND r1.game_id = a.game_id" ) ), 
							'WHERE' => " a.member_rank_id = r.rank_id 
			    				AND a.member_guild_id = r.guild_id  
								AND a.member_id = m.member_id 
								AND a.game_id = c.game_id 
								AND a.member_class_id = c.class_id  
								AND m.member_dkpid = s.dkpsys_id   
								AND l.game_id = c.game_id and l.attribute_id = c.class_id AND l.language= '" . $config ['bbdkp_lang'] . "' AND l.attribute = 'class'    
								AND s.dkpsys_id = " . $dkp_id . '   
							    AND a.member_id = ' . $member_id );
				$sql = $db->sql_build_query ( 'SELECT', $sql_array );
				
				$result = $db->sql_query ( $sql );
				$row = $db->sql_fetchrow ( $result );
				
				// make object

				$this->member = array (
					'member_id' => $row ['member_id'], 
					'member_dkpid' => $row ['member_dkpid'], 
					'member_dkpname' => $row ['dkpsys_name'], 
					'member_name' => $row ['member_name'], 
					'member_raid_value' => $row ['member_raid_value'], 
					'member_time_bonus' => $row ['member_time_bonus'], 
					'member_zerosum_bonus' => $row ['member_zerosum_bonus'], 
					'member_earned' => $row ['member_earned'], 
					'member_raid_decay' => $row ['member_raid_decay'], 
					'member_adjustment' => $row ['member_adjustment'], 
					'ep' => $row ['ep'], 
					'member_spent' => $row ['member_spent'], 
					'member_item_decay' => $row ['member_item_decay'], 
					'gp' => $row ['gp'], 
					'pr' => $row ['pr'], 
					'adj_decay' => $row ['adj_decay'], 
					'member_current' => $row ['member_current'], 
					'member_race_id' => $row ['member_race_id'], 
					'member_race' => $row ['member_race'], 
					'member_class_id' => $row ['member_class_id'], 
					'member_class' => $row ['member_class'], 
					'member_level' => $row ['member_level'], 
					'member_rank_id' => $row ['member_rank_id'], 
					'member_rank' => $row ['rank_name'], 
					'imagename' => $row ['imagename'], 
					'colorcode' => $row ['colorcode'] );
				$db->sql_freeresult ( $result );
				/******************/
				$form_key = 'mm_editmemberdkp';
				add_form_key ( $form_key );
				
				$template->assign_vars ( array (
					'L_TITLE' => $user->lang ['ACP_DKP_EDITMEMBERDKP'], 
					'L_EXPLAIN' => $user->lang ['ACP_MM_EDITMEMBERDKP_EXPLAIN'], 
					'F_EDIT_MEMBER' => append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_mdkp&amp;mode=mm_editmemberdkp&amp;" ), 
					'MEMBER_NAME' => $this->member ['member_name'], 
					'V_MEMBER_ID' => (isset ( $_POST ['add'] )) ? '' : $this->member ['member_id'], 
					'V_MEMBER_DKPID' => (isset ( $_POST ['add'] )) ? '' : $this->member ['member_dkpid'], 
					'MEMBER_ID' => $this->member ['member_id'], 
					
					'RAIDVAL' => $this->member ['member_raid_value'], 
					'TIMEBONUS' => $this->member ['member_time_bonus'], 
					'ZEROSUM' => $this->member ['member_zerosum_bonus'], 
					'EARNED' => $this->member ['member_earned'], 
					'RAIDDECAY' => $this->member ['member_raid_decay'], 
					'ADJUSTMENT' => $this->member ['member_adjustment'], 
					'RAIDDECAY' => $this->member ['member_raid_decay'], 
					'ADJDECAY' => $this->member ['adj_decay'], 
					'EP' => $this->member ['ep'], 
					'SPENT' => $this->member ['member_spent'], 
					'ITEMDECAY' => $this->member ['member_item_decay'], 
					'GP' => $this->member ['gp'], 
					'PR' => $this->member ['pr'], 
					
					'MEMBER_EARNED' => $this->member ['member_earned'], 
					'MEMBER_SPENT' => $this->member ['member_spent'], 
					'MEMBER_ADJUSTMENT' => $this->member ['member_adjustment'], 
					'MEMBER_CURRENT' => (! empty ( $this->member ['member_current'] )) ? $this->member ['member_current'] : '0.00', 
					'MEMBER_LEVEL' => $this->member ['member_level'], 
					'MEMBER_DKPID' => $this->member ['member_dkpid'], 
					'MEMBER_DKPNAME' => $this->member ['member_dkpname'], 
					'MEMBER_RACE' => $this->member ['member_race'], 
					'MEMBER_CLASS' => $this->member ['member_class'], 
					'COLORCODE' => $this->member ['colorcode'], 
					'IMAGENAME' => (strlen ( $this->member ['imagename'] ) > 1) ? $phpbb_root_path . "images/class_images/" . $this->member ['imagename'] . ".png" : '', 
					'MEMBER_RANK' => $this->member ['member_rank'], 
					'CORRECT_RAIDVAL' => (! empty ( $correct_raid_value )) ? $correct_raid_value : '0.00', 
					'CORRECT_TIMEBONUS' => (! empty ( $correct_time_bonus )) ? $correct_time_bonus : '0.00', 
					'CORRECT_ZEROSUM' => (! empty ( $correct_zerosum_bonus )) ? $correct_zerosum_bonus : '0.00', 
					'CORRECT_RAIDDECAY' => (! empty ( $correct_raid_decay )) ? $correct_raid_decay : '0.00', 
					'CORRECT_MEMBER_SPENT' => (! empty ( $correct_spent )) ? $correct_spent : '0.00', 
					'CORRECT_ITEMDECAY' => (! empty ( $correct_itemdecay )) ? $correct_itemdecay : '0.00', 
					'CORRECT_EARNED' => $correct_raid_value + $correct_time_bonus + $correct_zerosum_bonus - $correct_raid_decay, 
					'S_SHOWZS' => ($config ['bbdkp_zerosum'] == '1') ? true : false, 
					'S_SHOWDECAY' => ($config ['bbdkp_decay'] == '1') ? true : false, 
					'S_SHOWEPGP' => ($config ['bbdkp_epgp'] == '1') ? true : false, 
					'S_SHOWTIME' => ($config ['bbdkp_timebased'] == '1') ? true : false )
				 );
				
				$this->page_title = 'ACP_DKP_EDITMEMBERDKP';
				$this->tpl_name = 'dkp/acp_' . $mode;
				break;
			
			/***************************************/
			// member dkp transfer
			// this transfers dkp account from one member account to another member account.
			// the old member account will still exist
			/***************************************/
			
			case 'mm_transfer' :
				$submit = (isset ( $_POST ['transfer'] )) ? true : false;
				$submitdkp = (isset ( $_POST ['dkpsys_id'] ) || isset ( $_GET ['dkpsys_id'] )) ? true : false;
				
				/***  DKPSYS drop-down query ***/
				$sql = 'SELECT dkpsys_id, dkpsys_name , dkpsys_default 

		                FROM ' . DKPSYS_TABLE . '  
		                GROUP BY dkpsys_id, dkpsys_name , dkpsys_default  ';
				$result = $db->sql_query ( $sql );
				$dkpsys_id = 0;
				
				if ($submitdkp)
				{
					$dkpsys_id = request_var ( 'dkpsys_id', 0 );
				}
				else
				{
					while ( $row = $db->sql_fetchrow ( $result ) )
					{
						if ($row ['dkpsys_default'] == "Y")
						{
							$dkpsys_id = $row ['dkpsys_id'];
						}
					}
					
					if ($dkpsys_id == 0)
					{
						$result = $db->sql_query_limit ( $sql, 1 );
						while ( $row = $db->sql_fetchrow ( $result ) )
						{
							$dkpsys_id = $row ['dkpsys_id'];
						}
					}
				}
				
				$result = $db->sql_query ( $sql );
				while ( $row = $db->sql_fetchrow ( $result ) )
				{
					$template->assign_block_vars ( 'dkpsys_row', array (
						'VALUE' => $row ['dkpsys_id'], 
						'SELECTED' => ($row ['dkpsys_id'] == $dkpsys_id) ? ' selected="selected"' : '', 
						'OPTION' => (! empty ( $row ['dkpsys_name'] )) ? $row ['dkpsys_name'] : '(None)' ) );
					$dkpsys_name [$row ['dkpsys_id']] = $row ['dkpsys_name'];
				}
				$db->sql_freeresult ( $result );
				/***  end drop-down query ***/
				
				if ($submit && $submitdkp == false)
				{
					$this->transfer_dkp ($dkpsys_id);
				}
				
				// build template

				// from member dkp table

				$member_from = request_var ( 'transfer_from', 0 );
				$member_to = request_var ( 'transfer_to', 0 );
				
				$sql = 'SELECT m.member_id, l.member_name 
						FROM ' . MEMBER_LIST_TABLE . ' l, ' . MEMBER_DKP_TABLE . ' m 
						WHERE m.member_id = l.member_id 
						AND m.member_dkpid = ' . $dkpsys_id . '
						ORDER BY l.member_name';
				$resultfrom = $db->sql_query ($sql);
				$maara = 0;
				while ( $row = $db->sql_fetchrow ( $resultfrom ) )
				{
					$maara ++;
					$template->assign_block_vars ( 'transfer_from_row', array (
						'VALUE' => $row ['member_id'], 
						'SELECTED' => ($member_from == $row ['member_id']) ? ' selected="selected"' : '',
						'OPTION' => $row ['member_name'] ) );
				
				}
				$db->sql_freeresult ( $resultfrom );
				
				// to member table 

				$sql = 'SELECT m.member_id, l.member_name FROM ' .
						 MEMBER_LIST_TABLE . ' l, ' . MEMBER_DKP_TABLE . ' m, ' . MEMBER_RANKS_TABLE . ' k   
						WHERE l.member_rank_id = k.rank_id 
						AND k.rank_hide != 1 
						AND m.member_id = l.member_id 
						AND m.member_dkpid = ' . $dkpsys_id . '
						ORDER BY l.member_name';
				$resultto = $db->sql_query ( $sql );
				$teller_to = 0;
				while ( $row = $db->sql_fetchrow ( $resultto ) )
				{
					$teller_to ++;
					$template->assign_block_vars ( 'transfer_to_row', array (
						'VALUE' => $row ['member_id'], 
						'SELECTED' => ($member_to == $row ['member_id']) ? ' selected="selected"' : '',
						'OPTION' => $row ['member_name'] ) );
				}
				$db->sql_freeresult ( $resultto );
				
				$show = true;
				if ($maara == 0)
				{
					$show = false;
				}
				
				$template->assign_vars ( array (
					'L_TITLE' => $user->lang ['ACP_MM_TRANSFER'], 
					'ERROR_MSG' => $user->lang ['ERROR_NODKPACCOUNT'], 
					'L_EXPLAIN' => $user->lang ['TRANSFER_MEMBER_HISTORY_DESCRIPTION'], 
					'S_SHOW' => $show, 
					'F_TRANSFER' => append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_mdkp&amp;mode=mm_transfer" ), 
					'F_DKP' => append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_mdkp&amp;mode=mm_transfer&ampi=setdkp" ), 
					'L_SELECT_1_OF_X_MEMBERS' => sprintf ( $user->lang ['SELECT_1OFX_MEMBERS'], $maara ), 
					'L_SELECT_1_OF_Y_MEMBERS' => sprintf ( $user->lang ['SELECT_1OFX_MEMBERS'], $teller_to ) ) );
				$this->page_title = 'ACP_MM_TRANSFER';
				$this->tpl_name = 'dkp/acp_' . $mode;
				
				break;
			
			default :
				$this->page_title = 'ACP_DKP_MAINPAGE';
				$this->tpl_name = 'dkp/acp_mainpage';
				$success_message = 'Error';
				trigger_error ( $success_message . $this->link );
		
		}
	}

	/**
	 * transfer dkp to other member
	 *
	 */
	function transfer_dkp($dkpsys_id)
	{
		global $user, $db;
		
		if (confirm_box ( true ))
		{
			//fetch hidden variables
			$member_from = request_var ( 'hidden_idfrom', 0 );
			$member_to = request_var ( 'hidden_idto', 0 );
			$dkpsys_id = request_var ( 'hidden_dkpid', 0 );
			
			//declare transfer array

			$transfer = array ();
			
			/* 1) collect adjustments to transfer */
			$sql = 'SELECT 
				sum(adjustment_value) as adjustments, 
				sum(adj_decay) as adj_decay,
				adjustment_dkpid FROM ' . ADJUSTMENTS_TABLE . ' 
				where member_id = ' . $member_from . '
				AND adjustment_dkpid = ' . $dkpsys_id . ' 
				GROUP BY adjustment_dkpid';
			$result = $db->sql_query ( $sql, 0 );
			
			$transfer = array ();
			while ( $row = $db->sql_fetchrow ( $result ) )
			{
				$transfer ['adjustments'] = ( float ) $row ['adjustments'];
				$transfer ['adj_decay'] = ( float ) $row ['adj_decay'];
			}
			$db->sql_freeresult ( $result );
			
			/* 2) collect item cost, decay and zspoints to transfer  */
			$sql = 'SELECT sum(i.item_value) as itemvalue,
				sum(i.item_decay) as item_decay,
				sum(i.item_zs) as item_zs, 
				e.event_dkpid FROM ' . RAID_ITEMS_TABLE . ' i,  ' . RAIDS_TABLE . ' r,  ' . EVENTS_TABLE . ' e
		     			where e.event_id=r.event_id
		     			and e.event_dkpid = ' . $dkpsys_id . '
		     			and r.raid_id=i.raid_id 
		     			and i.member_id = ' . $member_from . ' 

				GROUP BY e.event_dkpid';
			$result = $db->sql_query ( $sql, 0 );
			while ( $row = $db->sql_fetchrow ( $result ) )
			{
				$transfer ['itemcost'] = ( float ) $row ['itemvalue'];
				$transfer ['item_decay'] = ( float ) $row ['item_decay'];
				$transfer ['item_zs'] = ( float ) $row ['item_zs'];
			}
			$db->sql_freeresult ( $result );
			
			/* 3) calculate battlepoints earned, raidcount, first, last raiddate by dkp pool to transfer to new dkp account 

			 exclude raids where the member_to was also participating to avoid double counting raids */
			$sql = 'SELECT sum(ra.raid_value) as raidvalue, 
						   sum(ra.time_bonus) as time_bonus,
						   sum(ra.zerosum_bonus) as zerosum_bonus,
						   sum(ra.raid_decay) as raid_decay,
						   max(r.raid_start) as maxraiddate, 
						   min(r.raid_start) as minraiddate, 
						   count(ra.member_id) as raidcount, 
						   e.event_dkpid 

				FROM ' . RAID_DETAIL_TABLE . ' ra,  ' . RAIDS_TABLE . ' r,  ' . EVENTS_TABLE . ' e
		     			WHERE e.event_id = r.event_id
		     			AND r.raid_id = ra.raid_id 
		     			and e.event_dkpid = ' . $dkpsys_id . '
		     			AND ra.member_id = ' . $member_from . ' 
		     			AND r.raid_id not IN( select raid_id from ' . RAID_DETAIL_TABLE . ' where member_id = ' . $member_to . ')
				GROUP BY e.event_dkpid';
			$result = $db->sql_query ( $sql, 0 );
			
			while ( $row = $db->sql_fetchrow ( $result ) )
			{
				$transfer ['raidvalue'] = ( float ) $row ['raidvalue'];
				$transfer ['time_bonus'] = ( float ) $row ['time_bonus'];
				$transfer ['zerosum_bonus'] = ( float ) $row ['zerosum_bonus'];
				$transfer ['raid_decay'] = ( float ) $row ['raid_decay'];
				$transfer ['maxraiddate'] = ( int ) $row ['maxraiddate'];
				$transfer ['minraiddate'] = ( int ) $row ['minraiddate'];
				$transfer ['raidcount'] = ( int ) $row ['raidcount'];
			}
			$db->sql_freeresult ( $result );
			
			// begin transaction

			$db->sql_transaction ( 'begin' );
			
			/* 4) now update dkp table */
			
			// check if pool record exists in dkp table for $member_to

			$sql = 'SELECT count(*) as memberpoolcount FROM ' . MEMBER_DKP_TABLE . ' 

	            WHERE member_id = ' . $member_to . ' and member_dkpid = ' . $dkpsys_id;
			$result = $db->sql_query ( $sql, 0 );
			$total_rowto = ( int ) $db->sql_fetchfield ( 'memberpoolcount' );
			$db->sql_freeresult ( $result );
			
			if ($total_rowto == 1)
			{
				// exists so update row

				$sql = 'SELECT member_raid_value, member_time_bonus, member_zerosum_bonus, member_earned, member_raid_decay, 

		                   	member_spent, member_item_decay, member_adjustment, member_firstraid, member_lastraid, member_raidcount , adj_decay 

		                   	FROM ' . MEMBER_DKP_TABLE . ' WHERE member_id = ' . ( int ) $member_to . ' and member_dkpid = ' . $dkpsys_id;
				$result = $db->sql_query ( $sql, 0 );
				while ( $row = $db->sql_fetchrow ( $result ) )
				{
					$oldmember_raid_value = ( float ) $row ['member_raid_value'];
					$oldmember_time_bonus = ( float ) $row ['member_time_bonus'];
					$oldmember_zerosum_bonus = ( float ) $row ['member_zerosum_bonus'];
					$oldmember_earned = ( float ) $row ['member_earned'];
					$oldmember_raid_decay = ( float ) $row ['member_raid_decay'];
					$oldmember_adjustment = ( float ) $row ['member_adjustment'];
					$oldmember_adj_decay = ( float ) $row ['adj_decay'];
					$oldmember_spent = ( float ) $row ['member_spent'];
					$oldmember_item_decay = ( float ) $row ['member_item_decay'];
					$oldmember_firstraid = ( int ) $row ['member_firstraid'];
					$oldmember_lastraid = ( int ) $row ['member_lastraid'];
					$oldmember_raidcount = ( int ) $row ['member_raidcount'];
					
					if (isset ( $transfer ['minraiddate'] ))
					{
						$newfirstraid = ($oldmember_firstraid <= $transfer ['minraiddate']) ? $oldmember_firstraid : $transfer ['minraiddate'];
					}
					else
					{
						$newfirstraid = $oldmember_firstraid;
					}
					
					if (isset ( $transfer ['maxraiddate'] ))
					{
						$newlastraid = ($oldmember_lastraid <= $transfer ['maxraiddate']) ? $oldmember_lastraid : $transfer ['maxraiddate'];
					}
					else
					{
						$newlastraid = $oldmember_lastraid;
					}
				
				}
				$db->sql_freeresult ( $result );
				
				//build update query

				$query = $db->sql_build_array ( 'UPDATE', array (
					'member_raid_value' => $oldmember_raid_value + (isset ( $transfer ['member_raid_value'] ) ? $transfer ['member_raid_value'] : 0.00), 
					'member_time_bonus' => $oldmember_time_bonus + (isset ( $transfer ['member_time_bonus'] ) ? $transfer ['member_time_bonus'] : 0.00), 
					'member_zerosum_bonus' => $oldmember_zerosum_bonus + (isset ( $transfer ['member_zerosum_bonus'] ) ? $transfer ['member_zerosum_bonus'] : 0.00), 
					'member_earned' => $oldmember_earned + (isset ( $transfer ['member_raid_value'] ) ? $transfer ['member_raid_value'] : 0.00) + (isset ( $transfer ['member_time_bonus'] ) ? $transfer ['member_time_bonus'] : 0.00) + (isset ( $transfer ['member_zerosum_bonus'] ) ? $transfer ['member_zerosum_bonus'] : 0.00), 
					'member_raid_decay' => $oldmember_raid_decay + (isset ( $transfer ['raid_decay'] ) ? $transfer ['raid_decay'] : 0.00), 
					'member_adjustment' => $oldmember_adjustment + (isset ( $transfer ['adjustments'] ) ? $transfer ['adjustments'] : 0.00), 
					'adj_decay' => $oldmember_adj_decay + (isset ( $transfer ['adj_decay'] ) ? $transfer ['adj_decay'] : 0.00), 
					'member_spent' => $oldmember_spent + (isset ( $transfer ['itemcost'] ) ? $transfer ['itemcost'] : 0.00) + (isset ( $transfer ['item_zs'] ) ? $transfer ['item_zs'] : 0.00), 
					'member_item_decay' => $oldmember_item_decay + (isset ( $transfer ['item_decay'] ) ? $transfer ['item_decay'] : 0.00), 
					'member_firstraid' => $newfirstraid, 
					'member_lastraid' => $newlastraid, 
					'member_raidcount' => $oldmember_raidcount + (isset ( $transfer ['raidcount'] ) ? $transfer ['raidcount'] : 0) ) );
				$sql = 'UPDATE ' . MEMBER_DKP_TABLE . ' SET ' . $query . ' 
					WHERE member_id = ' . $member_to . ' 
					AND member_dkpid = ' . $dkpsys_id;
				$db->sql_query ( $sql );
			
			}
			elseif ($total_rowto == 0)
			{
				//insert

				$query = $db->sql_build_array ( 'INSERT', array (
					'member_dkpid' => $dkpsys_id, 
					'member_id' => $member_to, 
					'member_raid_value' => (isset ( $transfer ['member_raid_value'] ) ? $transfer ['member_raid_value'] : 0.00), 
					'member_time_bonus' => (isset ( $transfer ['member_time_bonus'] ) ? $transfer ['member_time_bonus'] : 0.00), 
					'member_zerosum_bonus' => (isset ( $transfer ['member_zerosum_bonus'] ) ? $transfer ['member_zerosum_bonus'] : 0.00), 
					'member_earned' => (isset ( $transfer ['member_raid_value'] ) ? $transfer ['member_raid_value'] : 0.00) + (isset ( $transfer ['member_time_bonus'] ) ? $transfer ['member_time_bonus'] : 0.00) + (isset ( $transfer ['member_zerosum_bonus'] ) ? $transfer ['member_zerosum_bonus'] : 0.00), 
					'member_raid_decay' => (isset ( $transfer ['raid_decay'] ) ? $transfer ['raid_decay'] : 0.00), 
					'member_adjustment' => (isset ( $transfer ['adjustments'] ) ? $transfer ['adjustments'] : 0.00), 
					'adj_decay' => (isset ( $transfer ['adj_decay'] ) ? $transfer ['adj_decay'] : 0.00), 
					'member_spent' => (isset ( $transfer ['itemcost'] ) ? $transfer ['itemcost'] : 0.00) + (isset ( $transfer ['item_zs'] ) ? $transfer ['item_zs'] : 0.00), 
					'member_item_decay' => (isset ( $transfer ['item_decay'] ) ? $transfer ['item_decay'] : 0.00), 
					'member_status' => 1, 
					'member_firstraid' => (isset ( $transfer ['minraiddate'] ) ? $transfer ['minraiddate'] : 0), 
					'member_lastraid' => (isset ( $transfer ['maxraiddate'] ) ? $transfer ['maxraiddate'] : 0), 
					'member_raidcount' => (isset ( $transfer ['raidcount'] ) ? $transfer ['raidcount'] : 0) ) );
				$sql = 'INSERT INTO ' . MEMBER_DKP_TABLE . $query;
				$db->sql_query ( $sql );
			}
			
			// set old member account to 0

			$query = $db->sql_build_array ( 'UPDATE', array (
				'member_raid_value' => 0.00, 
				'member_time_bonus' => 0.00, 
				'member_zerosum_bonus' => 0.00, 
				'member_earned' => 0.00, 
				'member_raid_decay' => 0.00, 
				'member_adjustment' => 0.00, 
				'adj_decay' => 0.00, 
				'member_spent' => 0.00, 
				'member_item_decay' => 0.00, 
				'member_firstraid' => '0', 
				'member_lastraid' => '0', 
				'member_raidcount' => 0 ) );
			
			$sql = 'UPDATE ' . MEMBER_DKP_TABLE . ' SET ' . $query . '
				 WHERE member_id = ' . $member_from . ' 
				 AND member_dkpid = ' . $dkpsys_id;
			$db->sql_query ( $sql );
			
			/*	

			// delete old account
				$sql = 'DELETE FROM ' . MEMBER_DKP_TABLE . '
						WHERE member_id = '. (int) $member_from  . ' and member_dkpid = ' . $dkpsys_id;
				$db->sql_query($sql);

			*/
			
			/* 5) transfer old attendee name to new member */
			// if $member_from participated in a raid the $member_to did too, delete the entry. (unique key) 

			$sql_array = array (
				'SELECT' => 'r.raid_id', 
				'FROM' => array (
					RAID_DETAIL_TABLE => 'rd', 
					RAIDS_TABLE => 'r', 
					EVENTS_TABLE => 'e' ), 
				'WHERE' => 'e.event_dkpid = ' . $dkpsys_id . '

					AND e.event_id = r.event_id 

					AND r.raid_id = rd.raid_id 

					AND rd.member_id = ' . $member_from, 'ORDER_BY' => 'raid_id' );
			$sql = $db->sql_build_query ( 'SELECT', $sql_array );
			$result = $db->sql_query ( $sql, 0 );
			$raid_ids = array ();
			while ( $row = $db->sql_fetchrow ( $result ) )
			{
				$raid_ids [] = $row ['raid_id'];
			}
			
			if (count ( $raid_ids ) > 0)
			{
				// 6) delete from these raids all member b if they also participated (otherwise you get unique key violation)

				$sql = 'DELETE FROM ' . RAID_DETAIL_TABLE . ' WHERE member_id =' . $member_to . ' 

					AND ' . $db->sql_in_set ( 'raid_id', $raid_ids, true, true );
				$db->sql_query ( $sql );
				
				// 7) now update the memberid to b

				$sql = 'UPDATE ' . RAID_DETAIL_TABLE . ' SET member_id =' . $member_to . ' WHERE member_id=' . $member_from . '

						AND ' . $db->sql_in_set ( 'raid_id', $raid_ids, true, true );
				$db->sql_query ( $sql );
				
				/* 8) transfer items to new owner */
				$sql = 'UPDATE ' . RAID_ITEMS_TABLE . ' SET member_id =' . $member_to . ' WHERE member_id=' . $member_from . ' 

					AND ' . $db->sql_in_set ( 'raid_id', $raid_ids, true, true );
				$db->sql_query ( $sql );
			}
			
			// 9) update the adjustments table for this dkpid

			$sql = 'UPDATE ' . ADJUSTMENTS_TABLE . ' SET member_id=' . $member_to . ' WHERE member_id= ' . $member_from . '

					AND adjustment_dkpid = ' . $dkpsys_id;
			$db->sql_query ( $sql );
			
			//commit 

			$db->sql_transaction ( 'commit' );
			
			//pick up this info from the hidden variables

			$member_from_name = utf8_normalize_nfc ( request_var ( 'hidden_name_from', '', true ) );
			$member_to_name = utf8_normalize_nfc ( request_var ( 'hidden_name_to', '', true ) );
			
			//log the action

			$log_action = array (
				'header' => 'L_ACTION_HISTORY_TRANSFER', 
				'L_FROM' => $member_from_name, 
				'L_TO' => $member_to_name );
			
			$this->log_insert ( array (
				'log_type' => $log_action ['header'], 
				'log_action' => $log_action ) );
			
			$success_message = sprintf ( $user->lang ['ADMIN_TRANSFER_HISTORY_SUCCESS'], 
				$member_from_name, $member_to_name, $member_from_name, $dkpsys_id );
			trigger_error ( $success_message . $this->link );
		
		}
		else
		{
			// first check if user tries to transfer from one to the same 

			$member_from = request_var ( 'transfer_from', 0 );
			$member_to = request_var ( 'transfer_to', 0 );
			if ($member_from == $member_to)
			{
				trigger_error ( $user->lang ['ERROR_TRFSAME'], E_USER_WARNING );
			}
			
			if ($member_from == 0 || $member_to == 0)
			{
				trigger_error ( $user->lang ['ERROR_NOSELECT'], E_USER_WARNING );
			}
			
			// prepare some logging information 

			$sql = 'SELECT member_name FROM ' . MEMBER_LIST_TABLE . ' 
					WHERE member_id =  ' . $member_from;
			$result = $db->sql_query ( $sql, 0 );
			$member_from_name = ( string ) $db->sql_fetchfield ( 'member_name' );
			$db->sql_freeresult ( $result );
			
			$sql = 'SELECT member_name FROM ' . MEMBER_LIST_TABLE . ' 
					WHERE member_id =  ' . $member_to;
			$result = $db->sql_query ( $sql, 0 );
			$member_to_name = ( string ) $db->sql_fetchfield ( 'member_name' );
			$db->sql_freeresult ( $result );
			
			$s_hidden_fields = build_hidden_fields ( array (
				'transfer' => true, 
				'hidden_name_from' => $member_from_name, 
				'hidden_name_to' => $member_to_name, 
				'hidden_idfrom' => $member_from, 
				'hidden_idto' => $member_to, 
				'hidden_dkpid' => $dkpsys_id ) );
			confirm_box ( false, sprintf ( $user->lang ['CONFIRM_TRANSFER_MEMBERDKP'],
			 $member_from_name, $member_to_name ), $s_hidden_fields );
		
		}
	
	}

}

?>