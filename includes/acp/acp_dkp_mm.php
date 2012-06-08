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
 * This class manages member general info
 * 
 */
class acp_dkp_mm extends bbDKP_Admin
{
	public $u_action;
	public $member;
	public $old_member;
	public $link = ' ';

	public function main ($id, $mode)
	{
		global $user, $template, $db, $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;
		$user->add_lang(array('mods/dkp_admin'));
		$user->add_lang(array('mods/dkp_common'));
		switch ($mode)
		{
			/***************************************
			*
			* List members
			*
			/***************************************/
			case 'mm_listmembers':
				$this->link = '<br /><a href="' . append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm&amp;mode=mm_listmembers") . '"><h3>Return to Index</h3></a>';
				
				// add member button redirect
				$showadd = (isset($_POST['memberadd'])) ? true : false;
				if ($showadd)
				{
					redirect(append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm&amp;mode=mm_addmember"));
					break;
				}
				
				// set activation flag
				$activate = (isset($_POST['deactivate'])) ? true : false;
				if ($activate)
				{
					if (! check_form_key('mm_listmembers'))
					{
						trigger_error('FORM_INVALID');
					}
					$activate_members = request_var('activate_id', array(0));
					$member_window = request_var('hidden_member', array(0));
					$db->sql_transaction('begin');
					//if checkbox set then activate
					$sql1 = 'UPDATE ' . MEMBER_LIST_TABLE . "
                        SET member_status = '1' 
                        WHERE " . $db->sql_in_set('member_id', $activate_members, false, true);
					$db->sql_query($sql1);
					//if checkbox not set and in window then deactivate
					$sql2 = 'UPDATE ' . MEMBER_LIST_TABLE . "
                        SET member_status = '0' 
                        WHERE  " . $db->sql_in_set('member_id', $activate_members, true, true) . "
						AND  " . $db->sql_in_set('member_id', $member_window, false, true);
					$db->sql_query($sql2);
					$db->sql_transaction('commit');
				}
				
				// batch delete
				$del_batch = (isset($_POST['delete'])) ? true : false;
				if ($del_batch)
				{
					$members_tbdel = request_var('delete_id', array(0));
					$this->member_batch_delete($members_tbdel);
					unset($members_tbdel);
				}
				
				// guild dropdown query
				$sql = 'SELECT id, name, realm, region  
                       FROM ' . GUILD_TABLE . ' 
                       ORDER BY id desc';
				$resultg = $db->sql_query($sql);

				// show other guild
				$submit = (isset ( $_POST ['member_guild_id'] ) || isset ( $_GET ['member_guild_id'] ) ) ? true : false;
				/* check if page was posted back */
				if ($submit)
				{
					// user selected dropdow - get guildid 
					$guild_id = request_var('member_guild_id', 0);
					// fill popup and set selected to Post value
					while ($row = $db->sql_fetchrow($resultg))
					{
						$template->assign_block_vars('guild_row', array(
							'VALUE' => $row['id'] , 
							'SELECTED' => ($row['id'] == $guild_id) ? ' selected="selected"' : '' , 
							'OPTION' => (! empty($row['name'])) ? $row['name'] : '(None)'));
					}
					$db->sql_freeresult($resultg);
				}
				else // default pageloading
				{
					$sql = 'SELECT id FROM ' . GUILD_TABLE . ' ORDER BY id DESC';
					$result = $db->sql_query_limit($sql, 1);
					while ($row = $db->sql_fetchrow($result))
					{
						$guild_id = $row['id'];
					}
					$db->sql_freeresult($result);
					// fill popup and set selected to default selection
					while ($row = $db->sql_fetchrow($resultg))
					{
						$template->assign_block_vars('guild_row', array(
							'VALUE' => $row['id'] , 
							'SELECTED' => ($row['id'] == $guild_id) ? ' selected="selected"' : '' , 
							'OPTION' => $row['name']));
					}
					$db->sql_freeresult($resultg);
				}
				$previous_data = '';
				
				//get total members
				$sql_array = array(
					'SELECT' => 'count(*) as membercount ' , 
					'FROM' => array(
						MEMBER_LIST_TABLE => 'm' , 
						MEMBER_RANKS_TABLE => 'r' , 
						CLASS_TABLE => 'c' , 
						RACE_TABLE => 'a' , 
						BB_LANGUAGE => 'l' , 
						GUILD_TABLE => 'g') , 
					'LEFT_JOIN' => array(
						array(
							'FROM' => array(
								USERS_TABLE => 'u') , 
							'ON' => 'u.user_id = m.phpbb_user_id ')) , 
					'WHERE' => " (m.member_rank_id = r.rank_id)
				    				and m.game_id = l.game_id 
				    				AND l.attribute_id = c.class_id and l.game_id = c.game_id AND l.language= '" . $config['bbdkp_lang'] . "' AND l.attribute = 'class'
									AND (m.member_guild_id = g.id)
									AND (m.member_guild_id = r.guild_id)
									AND (m.member_guild_id = " . $guild_id . ')
									AND m.game_id =  a.game_id
									AND m.game_id =  c.game_id
									AND m.member_race_id =  a.race_id
									AND (m.member_class_id = c.class_id)');
				$sql = $db->sql_build_query('SELECT', $sql_array);
				$result = $db->sql_query($sql);
				$total_members = (int) $db->sql_fetchfield('membercount');
				$db->sql_freeresult($result);
				
				//get window				
				$start = request_var('start', 0, false);
				$sort_order = array(
					0 => array('member_name' , 'member_name desc') , 
					1 => array('username' , 'username desc') , 
					2 => array('member_level' , 'member_level desc') , 
					3 => array('member_class' , 'member_class desc') , 
					4 => array('rank_name' , 'rank_name desc') , 
					5 => array('member_joindate' , 'member_joindate desc') , 
					6 => array('member_outdate' , 'member_outdate desc') , 
					7 => array('member_race' ,	'member_race desc'));
				
				$current_order = switch_order($sort_order);
				$sort_index = explode('.', $current_order['uri']['current']);
				$previous_source = preg_replace('/( (asc|desc))?/i', '', $sort_order[$sort_index[0]][$sort_index[1]]);
				$show_all = ((isset($_GET['show'])) && request_var('show', '') == 'all') ? true : false;
				
				$sql_array = array(
					'SELECT' => 'm.* , u.username, u.user_id, u.user_colour, g.name, l.name as member_class, r.rank_id, 
				    				r.rank_name, r.rank_prefix, r.rank_suffix,
									 c.colorcode , c.imagename, m.member_gender_id, a.image_female, a.image_male' , 
					'FROM' => array(
						MEMBER_LIST_TABLE => 'm' , 
						MEMBER_RANKS_TABLE => 'r' , 
						CLASS_TABLE => 'c' , 
						RACE_TABLE => 'a' , 
						BB_LANGUAGE => 'l' , 
						GUILD_TABLE => 'g') , 
					'LEFT_JOIN' => array(
						array(
							'FROM' => array(
								USERS_TABLE => 'u') , 
							'ON' => 'u.user_id = m.phpbb_user_id ')) , 
					'WHERE' => " (m.member_rank_id = r.rank_id)
				    				and m.game_id = l.game_id 
				    				AND l.attribute_id = c.class_id and l.game_id = c.game_id AND l.language= '" . $config['bbdkp_lang'] . "' AND l.attribute = 'class'
									AND (m.member_guild_id = g.id)
									AND (m.member_guild_id = r.guild_id)
									AND (m.member_guild_id = " . $guild_id . ')
									AND m.game_id =  a.game_id
									AND m.game_id =  c.game_id
									AND m.member_race_id =  a.race_id
									AND (m.member_class_id = c.class_id)' , 
					'ORDER_BY' => $current_order['sql']);
								
				$sql = $db->sql_build_query('SELECT', $sql_array);
				$members_result = $db->sql_query_limit($sql, $config['bbdkp_user_llimit'], $start);
				if (! ($members_result))
				{
					trigger_error($user->lang['ERROR_MEMBERNOTFOUND'], E_USER_WARNING);
				}
				$lines = 0;
				$member_count = 0;
				
				while ($row = $db->sql_fetchrow($members_result))
				{
					$phpbb_user_id = (int) $row['phpbb_user_id'];
					$race_image = (string) (($row['member_gender_id'] == 0) ? $row['image_male'] : $row['image_female']);
					$member_count += 1;
					$lines +=1;
					$template->assign_block_vars('members_row', array(
						'S_READONLY' => ($row['rank_id'] == 90 || $row['rank_id'] == 99) ? true : false , 
						'STATUS' => ($row['member_status'] == 1) ? 'checked="checked" ' : '' , 
						'ID' => $row['member_id'] , 
						'COUNT' => $member_count , 
						'NAME' => $row['rank_prefix'] . $row['member_name'] . $row['rank_suffix'] , 
						'USERNAME' => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']) , 
						'RANK' => $row['rank_name'] , 
						'LEVEL' => ($row['member_level'] > 0) ? $row['member_level'] : '&nbsp;' , 
						'ARMOR' => (! empty($row['armor_type'])) ? $row['armor_type'] : '&nbsp;' , 
						'COLORCODE' => ($row['colorcode'] == '') ? '#123456' : $row['colorcode'] , 
						'CLASS_IMAGE' => (strlen($row['imagename']) > 1) ? $phpbb_root_path . "images/class_images/" . $row['imagename'] . ".png" : '' , 
						'S_CLASS_IMAGE_EXISTS' => (strlen($row['imagename']) > 1) ? true : false , 
						'RACE_IMAGE' => (strlen($race_image) > 1) ? $phpbb_root_path . "images/race_images/" . $race_image . ".png" : '' , 
						'S_RACE_IMAGE_EXISTS' => (strlen($race_image) > 1) ? true : false , 
						'CLASS' => ($row['member_class'] != 'NULL') ? $row['member_class'] : '&nbsp;' , 
						'JOINDATE' => date($config['bbdkp_date_format'], $row['member_joindate']) , 
						'OUTDATE' => ($row['member_outdate'] == 0) ? '' : date($config['bbdkp_date_format'], $row['member_outdate']) , 
						'U_VIEW_USER' => append_sid("{$phpbb_admin_path}index.$phpEx", "i=users&amp;icat=13&amp;mode=overview&amp;u=$phpbb_user_id") , 
						'U_VIEW_MEMBER' => append_sid("{$phpbb_admin_path}index.$phpEx", 'i=dkp_mm&amp;mode=mm_addmember&amp;' . URI_NAMEID . '=' . $row['member_id']) , 
						'U_DELETE_MEMBER' => append_sid("{$phpbb_admin_path}index.$phpEx", 'i=dkp_mm&amp;mode=mm_addmember&amp;delete=1&amp;' . URI_NAMEID . '=' . $row['member_id'])));
					$previous_data = $row[$previous_source];
				}
				
				$db->sql_freeresult($members_result);
				$footcount_text = sprintf($user->lang['LISTMEMBERS_FOOTCOUNT'], $lines);
				$memberpagination = generate_pagination(append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm&amp;mode=mm_listmembers&amp;o=" . $current_order['uri']['current'] . "&amp;member_guild_id=".$guild_id), $total_members, $config['bbdkp_user_llimit'], $start, true);
				$form_key = 'mm_listmembers';
				add_form_key($form_key);
				
				$template->assign_vars(array(
					'GUILDID' => $guild_id, 
					'START' => $start, 
					'F_MEMBERS' => append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm") . '&amp;mode=mm_addmember' , 
					'F_MEMBERS_LIST' => append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm") . '&amp;mode=mm_listmembers' , 
					'L_TITLE' => $user->lang['ACP_MM_LISTMEMBERS'] , 
					'L_EXPLAIN' => $user->lang['ACP_MM_LISTMEMBERS_EXPLAIN'] , 
					'O_NAME' => append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm&amp;mode=mm_listmembers&amp;o=" . $current_order['uri'][0] . "&amp;" . URI_GUILD . "=" . $guild_id) , 
					'O_USERNAME' => append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm&amp;mode=mm_listmembers&amp;o=" . $current_order['uri'][1] . "&amp;" . URI_GUILD . "=" . $guild_id) , 
					'O_LEVEL' => append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm&amp;mode=mm_listmembers&amp;o=" . $current_order['uri'][2] . "&amp;" . URI_GUILD . "=" . $guild_id) , 
					'O_CLASS' => append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm&amp;mode=mm_listmembers&amp;o=" . $current_order['uri'][3] . "&amp;" . URI_GUILD . "=" . $guild_id) , 
					'O_RANK' => append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm&amp;mode=mm_listmembers&amp;o=" . $current_order['uri'][4] . "&amp;" . URI_GUILD . "=" . $guild_id) , 
					'O_JOINDATE' => append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm&amp;mode=mm_listmembers&amp;o=" . $current_order['uri'][5] . "&amp;" . URI_GUILD . "=" . $guild_id) , 
					'O_OUTDATE' => append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm&amp;mode=mm_listmembers&amp;o=" . $current_order['uri'][6] . "&amp;" . URI_GUILD . "=" . $guild_id) , 
					'U_LIST_MEMBERS' => append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm&amp;mode=mm_listmembers&amp;") , 
					'LISTMEMBERS_FOOTCOUNT' => $footcount_text , 
					'MEMBER_PAGINATION' => $memberpagination));
				$this->page_title = 'ACP_MM_LISTMEMBERS';
				$this->tpl_name = 'dkp/acp_' . $mode;
				break;
				
			/***************************************/
			// add member 
			/***************************************/
			case 'mm_addmember':
				$this->link = '<br /><a href="' . append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm&amp;mode=mm_listmembers") . '"><h3>' . $user->lang['RETURN_MEMBERLIST'] . '</h3></a>';
				$member_id = 0;
				$submit = (isset($_POST['add'])) ? true : false;
				$update = (isset($_POST['update'])) ? true : false;
				$generate_armorylink = (isset($_POST['generate_armorylink'])) ? true : false;
				$delete = (isset($_GET['delete']) || isset($_POST['delete'])) ? true : false;
				if ($submit || $update || $generate_armorylink)
				{
					if (! check_form_key('mm_addmember'))
					{
						trigger_error('FORM_INVALID');
					}
				}
				//
				// add guildmember handler 
				if ($submit)
				{
					// get member name and guild
					$member_name = utf8_normalize_nfc(request_var('member_name', '', true));
					$guild_id = request_var('member_guild_id', 0);
					
					// check if membername exists
					$sql = 'SELECT count(*) as memberexists 
							FROM ' . MEMBER_LIST_TABLE . "	
							WHERE UPPER(member_name)= UPPER('" . $db->sql_escape($member_name) . "') 
							AND member_guild_id = " . $guild_id;
					$result = $db->sql_query($sql);
					$countm = $db->sql_fetchfield('memberexists');
					$db->sql_freeresult($result);
					if ($countm != 0)
					{
						trigger_error($user->lang['ERROR_MEMBEREXIST'] . $this->link, E_USER_WARNING);
					}
					
					// set member active
					$member_status = request_var('activated', 0) > 0 ? 1 : 0;
					// get rank					  
					$rank_id = request_var('member_rank_id', 99);
					// check if rank exists
					$sql = 'SELECT count(*) as rankccount 
							FROM ' . MEMBER_RANKS_TABLE . '
							WHERE rank_id=' . (int) $rank_id . ' and guild_id = ' . (int) $guild_id;
					$result = $db->sql_query($sql);
					$countm = $db->sql_fetchfield('rankccount');
					$db->sql_freeresult($result);
					if ($countm == 0)
					{
						trigger_error($user->lang['ERROR_INCORRECTRANK'] . $this->link, E_USER_WARNING);
					}
					$member_lvl = request_var('member_level', 0);
					
					// check level
					$sql = 'SELECT max(class_max_level) as maxlevel FROM ' . CLASS_TABLE;
					$result = $db->sql_query($sql);
					$maxlevel = $db->sql_fetchfield('maxlevel');
					$db->sql_freeresult($result);
					if ($member_lvl > $maxlevel)
					{
						$member_lvl = $maxlevel;
					}
					$game_id = request_var('game_id', '');
					$race_id = request_var('member_race_id', 0);
					$class_id = request_var('member_class_id', 0);
					$gender = isset($_POST['gender']) ? request_var('gender', '') : '0';
					$member_comment = utf8_normalize_nfc(request_var('member_comment', '', true));
					$joindate = mktime(0, 0, 0, request_var('member_joindate_mo', 0), request_var('member_joindate_d', 0), request_var('member_joindate_y', 0));
					
					//is there leavedate?
					$leavedate = 0;
					if (request_var('member_outdate_mo', 0) + request_var('member_outdate_d', 0) != 0)
					{
						$leavedate = mktime(0, 0, 0, request_var('member_outdate_mo', 0), request_var('member_outdate_d', 0), request_var('member_outdate_y', 0));
					}
					$achievpoints = 0;
					$url = utf8_normalize_nfc(request_var('member_armorylink', '', true));
					$phpbb_user_id = request_var('phpbb_user_id', 0);
					$sql = 'SELECT realm, region FROM ' . GUILD_TABLE . ' WHERE id = ' . (int) $guild_id;
					$result = $db->sql_query($sql);
					$realm = '';
					$region = '';
					while ($row = $db->sql_fetchrow($result))
					{
						$realm = $row['realm'];
						$region = $row['region'];
					}
					$member_id = $this->insertnewmember($member_name, $member_status, $member_lvl, $race_id, $class_id, $rank_id, $member_comment, $joindate, $leavedate, $guild_id, $gender, $achievpoints, $url, ' ', $realm, $game_id, $phpbb_user_id);
					if ($member_id > 0)
					{
						//record added. now update some stats
						$success_message = sprintf($user->lang['ADMIN_ADD_MEMBER_SUCCESS'], ucwords($member_name));
						trigger_error($success_message . $this->link, E_USER_NOTICE);
					}
					else
					{
						$failure_message = sprintf($user->lang['ADMIN_ADD_MEMBER_FAIL'], ucwords($member_name), $member_id);
						trigger_error($failure_message . $this->link, E_USER_WARNING);
					}
				}
				//get member_id if not created
				if ($member_id == 0)
				{
					$member_id = request_var('hidden_member_id', 0);
				}
				if ($member_id == 0)
				{
					$member_id = request_var(URI_NAMEID, 0);
				}
				
				//
				// update guild member handler 
				//
				if ($update)
				{
					// old data array
					$sql = 'SELECT *  FROM ' . MEMBER_LIST_TABLE . ' WHERE member_id=' . $member_id;
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						$this->old_member = array(
							'member_name' => $row['member_name'] , 
							'member_level' => $row['member_level'] , 
							'member_race_id' => $row['member_race_id'] , 
							'member_class_id' => $row['member_class_id'] , 
							'member_guild_id' => $row['member_guild_id'] , 
							'member_comment' => $row['member_comment'] , 
							'phpbb_user_id' => $row['phpbb_user_id']);
					}
					$db->sql_freeresult($result);
					$gender = isset($_POST['gender']) ? request_var('gender', '') : '0';
					
					// if user chooses other name then check if it already exists. if so refuse update
					// namechange to existing membername is not allowed 
					$member_name = utf8_normalize_nfc(request_var('member_name', '', true));
					if($member_name != $this->old_member['member_name'])
					{
						$sql = 'SELECT count(*) as memberexists 
								FROM ' . MEMBER_LIST_TABLE . '	
								WHERE member_id <> ' . $member_id . " 
								AND UPPER(member_name)= UPPER('" . $db->sql_escape($member_name) . "')";
						$result = $db->sql_query($sql);
						$countm = $db->sql_fetchfield('memberexists');
						$db->sql_freeresult($result);
						if ($countm != 0)
						{
							trigger_error(sprintf($user->lang['ADMIN_UPDATE_MEMBER_FAIL'], ucwords($member_name)) . $this->link, E_USER_WARNING);
						}
					}
					
					// get rank					  
					$rank_id = request_var('member_rank_id', 99);
					// check if rank exists
					$sql = 'SELECT count(*) as rankccount 
							FROM ' . MEMBER_RANKS_TABLE . '	 
							WHERE rank_id=' . (int) $rank_id . ' and guild_id = ' . request_var('member_guild_id', 0);
					$result = $db->sql_query($sql);
					$countm = $db->sql_fetchfield('rankccount');
					$db->sql_freeresult($result);
					if ($countm == 0)
					{
						trigger_error($user->lang['ERROR_INCORRECTRANK'] . $this->link, E_USER_WARNING);
					}
					
					// check level
					$level = request_var('member_level', 0);
					$sql = 'SELECT max(class_max_level) as maxlevel FROM ' . CLASS_TABLE;
					$result = $db->sql_query($sql);
					$maxlevel = $db->sql_fetchfield('maxlevel');
					$db->sql_freeresult($result);
					if ($level > $maxlevel)
					{
						$level = $maxlevel;
					}
					
					$joindate = mktime(0, 0, 0, request_var('member_joindate_mo', 0), request_var('member_joindate_d', 0), request_var('member_joindate_y', 0));
					$leavedate = 0;
					if (request_var('member_outdate_mo', 0) + request_var('member_outdate_d', 0) != 0)
					{
						$leavedate = mktime(0, 0, 0, request_var('member_outdate_mo', 0), request_var('member_outdate_d', 0), request_var('member_outdate_y', 0));
					}
					
					// set member active
					$member_status = request_var('activated', 0) > 0 ? 1 : 0;
					$phpbb_user_id = request_var('phpbb_user_id', 0);
					
					// update the data including the phpbb userid
					$query = $db->sql_build_array('UPDATE', array(
						'member_name' => $member_name , 
						'member_status' => $member_status , 
						'member_level' => $level , 
						'member_race_id' => request_var('member_race_id', 0) , 
						'member_class_id' => request_var('member_class_id', 0) , 
						'member_rank_id' => $rank_id , 
						'member_gender_id' => $gender , 
						'member_comment' => utf8_normalize_nfc(request_var('member_comment', '', true)) , 
						'member_guild_id' => request_var('member_guild_id', 0) , 
						'member_outdate' => $leavedate , 
						'member_joindate' => $joindate , 
						'phpbb_user_id' => $phpbb_user_id , 
						'game_id' => request_var('game_id', '')));
					
					$db->sql_query('UPDATE ' . MEMBER_LIST_TABLE . ' SET ' . $query . ' WHERE member_id= ' . $member_id);

					// log it
					$log_action = array(
						'header' => 'L_ACTION_MEMBER_UPDATED' , 
						'L_NAME_BEFORE' => $this->old_member['member_name'] , 
						'L_LEVEL_BEFORE' => $this->old_member['member_level'] , 
						'L_RACE_BEFORE' => $this->old_member['member_race_id'] , 
						'L_CLASS_BEFORE' => $this->old_member['member_class_id'] , 
						'L_NAME_AFTER' => $member_name , 
						'L_LEVEL_AFTER' => request_var('member_level', 0) , 
						'L_RACE_AFTER' => request_var('member_race_id', 0) , 
						'L_CLASS_AFTER' => request_var('member_class_id', 0) , 
						'L_UPDATED_BY' => $user->data['username']);
					$this->log_insert(array(
						'log_type' => $log_action['header'] , 
						'log_action' => $log_action));
					$success_message = sprintf($user->lang['ADMIN_UPDATE_MEMBER_SUCCESS'], $member_name);
					trigger_error($success_message . $this->link);
				}
				
				// make armory link for existing members (only for wow)
				if ($generate_armorylink)
				{
					$sql = 'SELECT *  FROM ' . MEMBER_LIST_TABLE . ' m, ' . GUILD_TABLE . ' g WHERE g.id = m.member_guild_id AND member_id=' . (int) $member_id;
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						$this->old_member = array(
							'member_name' => $row['member_name'] , 
							'member_level' => $row['member_level'] , 
							'game_id' => $row['game_id'] , 
							'member_gender_id' => $row['member_gender_id'] , 
							'member_class_id' => $row['member_class_id'] , 
							'member_race_id' => $row['member_race_id'] , 
							'region' => $row['region'] , 
							'realm' => $row['realm']);
					}
					$db->sql_freeresult($result);
					
					// setting up the links
					$memberportraiturl = ' ';
					
					if ($this->old_member['game_id'] == 'wow' || $this->old_member['game_id'] == 'aion')
					{
						$memberportraiturl = $this->generate_portraitlink($this->old_member['game_id'], $this->old_member['member_race_id'], $this->old_member['member_class_id'], $this->old_member['member_gender_id'], $this->old_member['member_level']);
					}
					$memberarmoryurl = ' ';
					
					if ($this->old_member['game_id'] == 'wow')
					{
						$memberarmoryurl = $this->generate_armorylink(
							$this->old_member['game_id'], 
							$this->old_member['region'], 
							$this->old_member['realm'], 
							$this->old_member['member_name']);
					}
					
					$data = array(
						'member_armory_url' => $memberarmoryurl , 
						'member_portrait_url' => $memberportraiturl);
					$sql = 'UPDATE ' . MEMBER_LIST_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $data) . ' WHERE member_id=' . (int) $member_id;
					$db->sql_query($sql);
				}
				
				//
				// delete guildmember handler 
				// deletes Everything!
				//
				if ($delete)
				{
					if (confirm_box(true))
					{
						// recall hidden vars
						$del_member = request_var('del_member_id', 0);
						$del_membername = utf8_normalize_nfc(request_var('del_member_name', '', true));
						$sql = 'SELECT * FROM ' . MEMBER_LIST_TABLE . ' WHERE member_id= ' . (int) $del_member;
						$result = $db->sql_query($sql);
						while ($row = $db->sql_fetchrow($result))
						{
							$this->old_member = array(
								'member_name' => $row['member_name'] , 
								'member_level' => $row['member_level'] , 
								'member_race_id' => $row['member_race_id'] , 
								'member_class_id' => $row['member_class_id']);
						}
						$db->sql_freeresult($result);
						$log_action = array(
							'header' => sprintf($user->lang['ACTION_MEMBER_DELETED'], $del_membername) , 
							'L_NAME' => $this->old_member['member_name'] , 
							'L_LEVEL' => $this->old_member['member_level'] , 
							'L_RACE' => $this->old_member['member_race_id'] , 
							'L_CLASS' => $this->old_member['member_class_id']);
						$this->log_insert(array(
							'log_type' => $log_action['header'] , 
							'log_action' => $log_action));
						
						//@todo if zerosum then put excess points in guildbank
						$sql = 'DELETE FROM ' . RAID_DETAIL_TABLE . ' where member_id = ' . (int) $del_member;
						$db->sql_query($sql);
						$sql = 'DELETE FROM ' . RAID_ITEMS_TABLE . ' where member_id = ' . (int) $del_member;
						$db->sql_query($sql);
						$sql = 'DELETE FROM ' . MEMBER_DKP_TABLE . ' where member_id = ' . (int) $del_member;
						$db->sql_query($sql);
						$sql = 'DELETE FROM ' . ADJUSTMENTS_TABLE . ' where member_id = ' . (int) $del_member;
						$db->sql_query($sql);
						$sql = 'DELETE FROM ' . MEMBER_LIST_TABLE . ' where member_id = ' . (int) $del_member;
						$db->sql_query($sql);
						$success_message = sprintf($user->lang['ADMIN_DELETE_MEMBERS_SUCCESS'], $this->old_member['member_name']);
						trigger_error($success_message . $this->link);
					}
					else
					{
						$sql = "SELECT member_name FROM " . MEMBER_LIST_TABLE . ' WHERE member_id = ' . $member_id;
						$result = $db->sql_query($sql);
						$member_name = $db->sql_fetchfield('member_name', false, $result);
						$db->sql_freeresult($result);
						$s_hidden_fields = build_hidden_fields(array(
							'delete' => true , 
							'del_member_id' => $member_id , 
							'del_member_name' => $member_name));
						confirm_box(false, sprintf($user->lang['CONFIRM_DELETE_MEMBER'], $member_name), $s_hidden_fields);
					}
					$S_ADD = true;
				}
				
				/*
				 * fill template 
				 */
				if ($member_id > 0)
				{
					// edit mode
					// build member array if clicked on name in listing
					//	
					$S_ADD = false;
					$sql_array = array(
						'SELECT' => 'm.*, c.colorcode , c.imagename,  c1.name AS member_class, l1.name AS member_race, 
										r.image_female, r.image_male, 
										g.id as guild_id, g.name as guild_name, g.realm , g.region' , 
						'FROM' => array(
							MEMBER_LIST_TABLE => 'm' , 
							CLASS_TABLE => 'c' , 
							BB_LANGUAGE => 'l1' , 
							RACE_TABLE => 'r' , 
							GUILD_TABLE => 'g') , 
						'LEFT_JOIN' => array(
							array(
								'FROM' => array(
									BB_LANGUAGE => 'c1') , 
								'ON' => "c1.attribute_id = c.class_id AND c1.game_id = c.game_id AND c1.language= '" . $config['bbdkp_lang'] . "' AND c1.attribute = 'class'")) , 
						'WHERE' => "
						 l1.attribute_id = r.race_id AND l1.game_id = r.game_id AND l1.language= '" . $config['bbdkp_lang'] . "' AND l1.attribute = 'race'
						AND m.game_id = c.game_id
						AND m.member_class_id = c.class_id 
						AND m.game_id = r.game_id
						AND m.member_race_id = r.race_id 
						AND m.member_guild_id = g.id 
						AND member_id = " . (int) $member_id);
					$sql = $db->sql_build_query('SELECT', $sql_array);
					$result = $db->sql_query($sql);
					$row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);
					if ($row)
					{
						$race_image = (string) (($row['member_gender_id'] == 0) ? $row['image_male'] : $row['image_female']);
						$this->member = array(
							'member_id' => $row['member_id'] , 
							'member_name' => $row['member_name'] , 
							'member_race_id' => $row['member_race_id'] , 
							'member_race' => $row['member_race'] , 
							'member_class_id' => $row['member_class_id'] , 
							'member_class' => $row['member_class'] , 
							'member_level' => $row['member_level'] , 
							'member_rank_id' => $row['member_rank_id'] , 
							'member_comment' => $row['member_comment'] , 
							'member_gender_id' => $row['member_gender_id'] , 
							'member_joindate' => $row['member_outdate'] , 
							'member_joindate_d' => date('j', $row['member_joindate']) , 
							'member_joindate_mo' => date('n', $row['member_joindate']) , 
							'member_joindate_y' => date('Y', $row['member_joindate']) , 
							'member_outdate' => $row['member_outdate'] , 
							'member_outdate_d' => date('j', $row['member_outdate']) , 
							'member_outdate_mo' => date('n', $row['member_outdate']) , 
							'member_outdate_y' => date('Y', $row['member_outdate']) , 
							'member_guild_name' => $row['guild_name'] , 
							'member_guild_id' => $row['guild_id'] , 
							'member_guild_realm' => $row['realm'] , 
							'member_guild_region' => $row['region'] , 
							'member_armory_url' => $row['member_armory_url'] , 
							'member_portrait_url' => $phpbb_root_path . $row['member_portrait_url'] , 
							'phpbb_user_id' => $row['phpbb_user_id'] , 
							'member_status' => $row['member_status'] , 
							'game_id' => $row['game_id'] , 
							'colorcode' => $row['colorcode'] , 
							'race_image' => (strlen($race_image) > 1) ? $phpbb_root_path . "images/race_images/" . $race_image . ".png" : '' , 
							'class_image' => (strlen($row['imagename']) > 1) ? $phpbb_root_path . "images/class_images/" . $row['imagename'] . ".png" : '');
					}
					else
					{
						trigger_error($user->lang['ERROR_MEMBERNOTFOUND'], E_USER_WARNING);
					}
				}
				else
				{
					// add mode
					$S_ADD = true;
				}
				
				//guild dropdown
				$sql = 'SELECT a.id, a.name, a.realm, a.region 
				FROM ' . GUILD_TABLE . ' a, ' . MEMBER_RANKS_TABLE . ' b 
				where a.id = b.guild_id
				group by a.id, a.name, a.realm, a.region
				order by a.id desc';
				$result = $db->sql_query($sql);
				if (isset($this->member))
				{
					while ($row = $db->sql_fetchrow($result))
					{
						$template->assign_block_vars('guild_row', array(
							'VALUE' => $row['id'] , 
							'SELECTED' => ($this->member['member_guild_id'] == $row['id']) ? ' selected="selected"' : '' , 
							'OPTION' => $row['name']));
					}
				}
				else
				{
					$i = 0;
					while ($row = $db->sql_fetchrow($result))
					{
						if ($i == 0)
						{
							$noguild_id = (int) $row['id'];
						}
						$template->assign_block_vars('guild_row', array(
							'VALUE' => $row['id'] , 
							'SELECTED' => '' , 
							'OPTION' => $row['name']));
						$i += 1;
					}
				}
				$db->sql_freeresult($result);
				
				// Rank drop-down -> for initial load
				// reloading is done from ajax to prevent redraw
				//
				// this only shows the VISIBLE RANKS
				// if you want to add someone to an unvisible rank make the rank visible first, 
				// add him and then make rank invisible again.
				//
				if (isset($this->member['member_guild_id']))
				{
					$sql = 'SELECT rank_id, rank_name
					FROM ' . MEMBER_RANKS_TABLE . ' 
					WHERE rank_hide = 0  
					AND rank_id < 90 
					AND guild_id =	' . $this->member['member_guild_id'] . ' ORDER BY rank_id';
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						$template->assign_block_vars('rank_row', array(
							'VALUE' => $row['rank_id'] , 
							'SELECTED' => ($this->member['member_rank_id'] == $row['rank_id']) ? ' selected="selected"' : '' , 
							'OPTION' => (! empty($row['rank_name'])) ? $row['rank_name'] : '(None)'));
					}
				}
				else
				{
					// no member is set, get the ranks from the highest numbered guild
					$sql = 'SELECT rank_id, rank_name
					FROM ' . MEMBER_RANKS_TABLE . ' 
					WHERE rank_hide = 0 
					AND rank_id < 90 					
					AND guild_id = ' . $noguild_id . ' ORDER BY rank_id desc';
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						$template->assign_block_vars('rank_row', array(
							'VALUE' => $row['rank_id'] , 
							'SELECTED' => '' , 
							'OPTION' => (! empty($row['rank_name'])) ? $row['rank_name'] : '(None)'));
					}
				}
				
				// phpbb User dropdown
				$phpbb_user_id = isset($this->member['phpbb_user_id']) ? $this->member['phpbb_user_id'] : 0;
				$sql_array = array(
					'SELECT' => ' u.user_id, u.username ' , 
					'FROM' => array(
						USERS_TABLE => 'u') , 
					// exclude bots and guests, order by name -- ticket  129
					'WHERE' => " u.group_id != 6 and u.group_id != 1 " , 
					'ORDER_BY' => " u.username ASC");
				$sql = $db->sql_build_query('SELECT', $sql_array);
				$result = $db->sql_query($sql);
				$s_phpbb_user = '<option value="0"' . (($phpbb_user_id == 0) ? ' selected="selected"' : '') . '>--</option>';
				while ($row = $db->sql_fetchrow($result))
				{
					$selected = ($row['user_id'] == $phpbb_user_id) ? ' selected="selected"' : '';
					$s_phpbb_user .= '<option value="' . $row['user_id'] . '"' . $selected . '>' . $row['username'] . '</option>';
				}
				
				// Game dropdown
				// list installed games
				$games = array(
					'wow' => $user->lang['WOW'] , 
					'lotro' => $user->lang['LOTRO'] , 
					'eq' => $user->lang['EQ'] , 
					'daoc' => $user->lang['DAOC'] , 
					'vanguard' => $user->lang['VANGUARD'] , 
					'eq2' => $user->lang['EQ2'] , 
					'warhammer' => $user->lang['WARHAMMER'] , 
					'aion' => $user->lang['AION'] , 
					'FFXI' => $user->lang['FFXI'] , 
					'rift' => $user->lang['RIFT'] , 
					'swtor' => $user->lang['SWTOR'] , 
					'lineage2' => $user->lang['LINEAGE2']);
				
				$installed_games = array();
				foreach ($games as $gameid => $gamename)
				{
					//add value to dropdown when the game config value is 1
					if ($config['bbdkp_games_' . $gameid] == 1)
					{
						$template->assign_block_vars('game_row', array(
							'VALUE' => $gameid , 
							'SELECTED' => ($this->member['game_id'] == $gameid) ? ' selected="selected"' : '' , 
							'OPTION' => $gamename));
						$installed_games[] = $gameid;
					}
				}
				
				
				//
				// Race dropdown
				// reloading is done from ajax to prevent redraw
				$gamepreset = (isset($this->member['game_id']) ? $this->member['game_id'] : $installed_games[0]);
				$sql_array = array(
					'SELECT' => '  r.race_id, l.name as race_name ' , 
					'FROM' => array(
						RACE_TABLE => 'r' , 
						BB_LANGUAGE => 'l') , 
					'WHERE' => " r.race_id = l.attribute_id 
								AND r.game_id = '" . $gamepreset . "' 
								AND l.attribute='race' 
								AND l.game_id = r.game_id 
								AND l.language= '" . $config['bbdkp_lang'] . "'");
				$sql = $db->sql_build_query('SELECT', $sql_array);
				$result = $db->sql_query($sql);
				if (isset($this->member))
				{
					while ($row = $db->sql_fetchrow($result))
					{
						$template->assign_block_vars('race_row', array(
							'VALUE' => $row['race_id'] , 
							'SELECTED' => ($this->member['member_race_id'] == $row['race_id']) ? ' selected="selected"' : '' , 
							'OPTION' => (! empty($row['race_name'])) ? $row['race_name'] : '(None)'));
					}
				}
				else
				{
					while ($row = $db->sql_fetchrow($result))
					{
						$template->assign_block_vars('race_row', array(
							'VALUE' => $row['race_id'] , 
							'SELECTED' => '' , 
							'OPTION' => (! empty($row['race_name'])) ? $row['race_name'] : '(None)'));
					}
				}
				$db->sql_freeresult($result);
				
				
				//
				// Class dropdown
				// reloading is done from ajax to prevent redraw
				$sql_array = array(
					'SELECT' => ' c.class_id, l.name as class_name, c.class_hide,
									  c.class_min_level, class_max_level, c.class_armor_type , c.imagename ' , 
					'FROM' => array(
						CLASS_TABLE => 'c' , 
						BB_LANGUAGE => 'l') , 
					'WHERE' => " l.game_id = c.game_id  AND c.game_id = '" . $gamepreset . "' 
					AND l.attribute_id = c.class_id  AND l.language= '" . $config['bbdkp_lang'] . "' AND l.attribute = 'class' ");
				$sql = $db->sql_build_query('SELECT', $sql_array);
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					if ($row['class_min_level'] <= 1)
					{
						$option = (! empty($row['class_name'])) ? $row['class_name'] . " 
						 Level (" . $row['class_min_level'] . " - " . $row['class_max_level'] . ")" : '(None)';
					}
					else
					{
						$option = (! empty($row['class_name'])) ? $row['class_name'] . " 
						 Level " . $row['class_min_level'] . "+" : '(None)';
					}
					if (isset($this->member))
					{
						$template->assign_block_vars('class_row', array(
							'VALUE' => $row['class_id'] , 
							'SELECTED' => ($this->member['member_class_id'] == $row['class_id']) ? ' selected="selected"' : '' , 
							'OPTION' => $option));
					}
					else
					{
						$template->assign_block_vars('class_row', array(
							'VALUE' => $row['class_id'] , 
							'SELECTED' => '' , 
							'OPTION' => $option));
					}
				}
				$db->sql_freeresult($result);
				
				// set the genderdefault to male if a new form is opened, otherwise take rowdata.
				$genderid = isset($this->member) ? $this->member['member_gender_id'] : '0';
				
				
				// build presets for joindate pulldowns
				$now = getdate();
				$s_memberjoin_day_options = '<option value="0"	>--</option>';
				for ($i = 1; $i < 32; $i ++)
				{
					$day = isset($this->member['member_joindate_d']) ? $this->member['member_joindate_d'] : $now['mday'];
					$selected = ($i == $day) ? ' selected="selected"' : '';
					$s_memberjoin_day_options .= "<option value=\"$i\"$selected>$i</option>";
				}
				$s_memberjoin_month_options = '<option value="0">--</option>';
				for ($i = 1; $i < 13; $i ++)
				{
					$month = isset($this->member['member_joindate_mo']) ? $this->member['member_joindate_mo'] : $now['mon'];
					$selected = ($i == $month) ? ' selected="selected"' : '';
					$s_memberjoin_month_options .= "<option value=\"$i\"$selected>$i</option>";
				}
				$s_memberjoin_year_options = '<option value="0">--</option>';
				for ($i = $now['year'] - 10; $i <= $now['year']; $i ++)
				{
					$yr = isset($this->member['member_joindate_y']) ? $this->member['member_joindate_y'] : $now['year'];
					$selected = ($i == $yr) ? ' selected="selected"' : '';
					$s_memberjoin_year_options .= "<option value=\"$i\"$selected>$i</option>";
				}
				// build presets for outdate pulldowns
				$s_memberout_day_options = '<option value="0"' . (isset($this->member['member_outdate']) ? (($this->member['member_outdate'] != 0) ? '' : ' selected="selected"') : ' selected="selected"') . '>--</option>';
				for ($i = 1; $i < 32; $i ++)
				{
					if (isset($this->member['member_outdate_d']) && $this->member['member_outdate'] != 0)
					{
						$day = $this->member['member_outdate_d'];
						$selected = ($i == $day) ? ' selected="selected"' : '';
					}
					else
					{
						$selected = '';
					}
					$s_memberout_day_options .= "<option value=\"$i\"$selected>$i</option>";
				}
				$s_memberout_month_options = '<option value="0"' . (isset($this->member['member_outdate']) ? (($this->member['member_outdate'] != 0) ? '' : ' selected="selected"') : ' selected="selected"') . '>--</option>';
				for ($i = 1; $i < 13; $i ++)
				{
					if (isset($this->member['member_outdate']) && $this->member['member_outdate'] != 0)
					{
						$month = $this->member['member_outdate_mo'];
						$selected = ($i == $month) ? ' selected="selected"' : '';
					}
					else
					{
						$selected = '';
					}
					$s_memberout_month_options .= "<option value=\"$i\"$selected>$i</option>";
				}
				$s_memberout_year_options = '<option value="0"' . (isset($this->member['member_outdate']) ? (($this->member['member_outdate'] != 0) ? '' : ' selected="selected"') : ' selected="selected"') . '>--</option>';
				for ($i = $now['year'] - 10; $i <= $now['year'] + 10; $i ++)
				{
					if (isset($this->member['member_outdate']) && $this->member['member_outdate'] != 0)
					{
						$yr = $this->member['member_outdate_y'];
						$selected = ($i == $yr) ? ' selected="selected"' : '';
					}
					else
					{
						$selected = '';
					}
					$s_memberout_year_options .= "<option value=\"$i\"$selected>$i</option>";
				}
				
				unset($now);
				$form_key = 'mm_addmember';
				add_form_key($form_key);
				$template->assign_vars(array(
					'L_TITLE' => $user->lang['ACP_MM_ADDMEMBER'] , 
					'L_EXPLAIN' => $user->lang['ACP_MM_ADDMEMBER_EXPLAIN'] , 
					'F_ADD_MEMBER' => append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm&amp;mode=mm_addmember&amp;") , 
					'STATUS' => isset($this->member) ? (($this->member['member_status'] == 1) ? 'checked="checked" ' : '') : 'checked="checked" ' , 
					'MEMBER_NAME' => isset($this->member) ? $this->member['member_name'] : '' , 
					'MEMBER_ID' => isset($this->member) ? $this->member['member_id'] : '' , 
					'MEMBER_LEVEL' => isset($this->member) ? $this->member['member_level'] : '' , 
					'MALE_CHECKED' => ($genderid == '0') ? ' checked="checked"' : '' , 
					'FEMALE_CHECKED' => ($genderid == '1') ? ' checked="checked"' : '' , 
					'MEMBER_COMMENT' => isset($this->member) ? $this->member['member_comment'] : '' , 
					'S_CAN_HAVE_ARMORY' => isset($this->member) ? ($this->member['game_id'] == 'wow' || $this->member['game_id'] == 'aion' ? true : false) : false , 
					'MEMBER_URL' => isset($this->member) ? $this->member['member_armory_url'] : '' , 
					'MEMBER_PORTRAIT' => isset($this->member) ? $this->member['member_portrait_url'] : '' , 
					'S_MEMBER_PORTRAIT_EXISTS' => (strlen($this->member['member_portrait_url']) > 1) ? true : false , 
					'S_CAN_GENERATE_ARMORY' => isset($this->member) ? ($this->member['game_id'] == 'wow' ? true : false) : false , 
					'COLORCODE' => ($this->member['colorcode'] == '') ? '#123456' : $this->member['colorcode'] , 
					'CLASS_IMAGE' => $this->member['class_image'] , 
					'S_CLASS_IMAGE_EXISTS' => (strlen($this->member['class_image']) > 1) ? true : false , 
					'RACE_IMAGE' => $this->member['race_image'] , 
					'S_RACE_IMAGE_EXISTS' => (strlen($this->member['race_image']) > 1) ? true : false , 
					'S_JOINDATE_DAY_OPTIONS' => $s_memberjoin_day_options , 
					'S_JOINDATE_MONTH_OPTIONS' => $s_memberjoin_month_options , 
					'S_JOINDATE_YEAR_OPTIONS' => $s_memberjoin_year_options , 
					'S_OUTDATE_DAY_OPTIONS' => $s_memberout_day_options , 
					'S_OUTDATE_MONTH_OPTIONS' => $s_memberout_month_options , 
					'S_OUTDATE_YEAR_OPTIONS' => $s_memberout_year_options , 
					'S_PHPBBUSER_OPTIONS' => $s_phpbb_user , 
					// javascript
					'LA_ALERT_AJAX' => $user->lang['ALERT_AJAX'] , 
					'LA_ALERT_OLDBROWSER' => $user->lang['ALERT_OLDBROWSER'] , 
					'LA_MSG_NAME_EMPTY' => $user->lang['FV_REQUIRED_NAME'] , 
					'UA_FINDRANK' => append_sid($phpbb_admin_path . "style/dkp/findrank.$phpEx") , 
					'UA_FINDCLASSRACE' => append_sid($phpbb_admin_path . "style/dkp/findclassrace.$phpEx") , 
					'S_ADD' => $S_ADD));
				$this->page_title = 'ACP_MM_ADDMEMBER';
				$this->tpl_name = 'dkp/acp_' . $mode;
				break;
				
			/***************************************/
			// ranks setup
			/***************************************/
			case 'mm_ranks':
				
				$this->link = '<br /><a href="' . append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm&amp;mode=mm_ranks") . '"><h3>'. $user->lang['RETURN_RANK']. '</h3></a>';
				$sql = 'SELECT max(id) as idmax FROM ' . GUILD_TABLE;
				$result = $db->sql_query($sql);
				$maxguildid = (int) $db->sql_fetchfield('idmax');
				$db->sql_freeresult($result);
				$guild_id = request_var('guild_id', $maxguildid);
				$submit = (isset($_POST['update'])) ? true : false;
				$deleterank = (isset($_GET['deleterank'])) ? true : false;
				$add = (isset($_POST['add'])) ? true : false;
				
				if ($add || $submit)
				{
					if (! check_form_key('mm_ranks'))
					{
						trigger_error('FORM_INVALID');
					}
				}
				if ($submit)
				{
					// update
					$modrank = utf8_normalize_nfc(request_var('ranks', array(
						0 => ''), true));
					foreach ($modrank as $rank_id => $rank_name)
					{
						// get old rank array
						$sql = 'SELECT rank_name, rank_hide, rank_prefix, rank_suffix
							FROM ' . MEMBER_RANKS_TABLE . '  
							WHERE rank_id = ' . (int) $rank_id . ' and guild_id = ' . (int) $guild_id;
						$result = $db->sql_query($sql);
						while ($row = $db->sql_fetchrow($result))
						{
							$old_rank = array(
								'rank_name' => $row['rank_name'] , 
								'rank_hide' => $row['rank_hide'] , 
								'rank_prefix' => $row['rank_prefix'] , 
								'rank_suffix' => $row['rank_suffix']);
						}
						$db->sql_freeresult($result);
						// get new rank array 
						$rank_prefix = utf8_normalize_nfc(request_var('prefix', array(
							(int) $rank_id => ''), true));
						$rank_suffix = utf8_normalize_nfc(request_var('suffix', array(
							(int) $rank_id => ''), true));
						$sql_ary = array(
							'rank_name' => $rank_name , 
							'rank_hide' => (isset($_POST['hide'][$rank_id])) ? 1 : 0 , 
							'rank_prefix' => $rank_prefix[$rank_id] , 
							'rank_suffix' => $rank_suffix[$rank_id]);
						// compare old with new, 
						if ($old_rank == $sql_ary)
						{
							// no difference
						}
						else
						{
							// difference so update and log it
							$sql = 'UPDATE ' . MEMBER_RANKS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
				   	      WHERE rank_id=' . (int) $rank_id . ' and guild_id = ' . (int) $guild_id;
							$db->sql_query($sql);
							// log it
							$log_action = array(
								'header' => 'L_ACTION_RANK_UPDATED' , 
								'L_NAME_BEFORE' => $old_rank['rank_name'] , 
								'L_HIDE_BEFORE' => $old_rank['rank_hide'] , 
								'L_PREFIX_BEFORE' => $old_rank['rank_prefix'] , 
								'L_SUFFIX_BEFORE' => $old_rank['rank_suffix'] , 
								'L_NAME_AFTER' => $sql_ary['rank_name'] , 
								'L_HIDE_AFTER' => $sql_ary['rank_hide'] , 
								'L_PREFIX_AFTER' => $sql_ary['rank_prefix'] , 
								'L_SUFFIX_AFTER' => $sql_ary['rank_suffix'] , 
								'L_UPDATED_BY' => $user->data['username']);
							$this->log_insert(array(
								'log_type' => $log_action['header'] , 
								'log_action' => $log_action));
						}
					}
					$success_message = $user->lang['ADMIN_RANKS_UPDATE_SUCCESS'];				
					trigger_error($success_message . $this->link);
				}
				if ($deleterank)
				{
					if (confirm_box(true))
					{
						$guild_id = request_var('hidden_guild_id', 'x');
						$rank_id = request_var('hidden_rank_id', 'x');
						$guild_name = request_var('hidden_guild_name', 'x');
						$old_rank_name = request_var('hidden_rank_name', 'x');
						// hardcoded exclusion of ranks 90/99
						$sql = 'DELETE FROM ' . MEMBER_RANKS_TABLE . ' WHERE rank_id != 90 and rank_id != 99 and rank_id=' . $rank_id . ' and guild_id = ' . $guild_id;
						$db->sql_query($sql);
						// log the action
						$log_action = array(
							'header' => 'L_ACTION_RANK_DELETED' , 
							'id' => (int) $rank_id , 
							'L_NAME' => $old_rank_name , 
							'L_ADDED_BY' => $user->data['username']);
						$this->log_insert(array(
							'log_type' => $log_action['header'] , 
							'log_action' => $log_action));
					}
					else
					{
						$rank_id = request_var('ranktodelete', 'x');
						$guild_id = request_var('guild_id', 'x');
						// delete the rank only if there are no members left 
						$sql = 'SELECT count(*) as countm FROM ' . MEMBER_LIST_TABLE . ' 
						where member_rank_id = ' . $rank_id . ' and member_guild_id = ' . $guild_id;
						$result = $db->sql_query($sql);
						$countm = $db->sql_fetchfield('countm');
						$db->sql_freeresult($result);
						if ($countm != 0)
						{
							trigger_error($user->lang['ERROR_RANKMEMBERS'] . $this->link, E_USER_WARNING);
						}
						$sql = "select a.rank_name, b.name  from " . MEMBER_RANKS_TABLE . ' a , ' . GUILD_TABLE . ' b  
						where a.guild_id = b.id and a.rank_id = ' . $rank_id . ' and b.id = ' . $guild_id;
						$result = $db->sql_query($sql);
						while ($row = $db->sql_fetchrow($result))
						{
							$old_rank_name = $row['rank_name'];
							$guild_name = $row['name'];
						}
						$db->sql_freeresult($result);
						$s_hidden_fields = build_hidden_fields(array(
							'deleterank' => true , 
							'hidden_rank_id' => $rank_id , 
							'hidden_guild_id' => $guild_id , 
							'hidden_guild_name' => $guild_name , 
							'hidden_rank_name' => $old_rank_name));
						confirm_box(false, sprintf($user->lang['CONFIRM_DELETE_RANKS'], $old_rank_name, $guild_name), $s_hidden_fields);
					}
				}
				if ($add)
				{
					//check if rankname exists
					$nrank_name = utf8_normalize_nfc(request_var('nrankname', '', true));
					if ($nrank_name == '')
					{
						trigger_error($user->lang('ERROR_RANK_NAME_EMPTY'), E_USER_WARNING);
					}
					//check if guildid is valid
					if ($guild_id == 0)
					{
						trigger_error($user->lang('ERROR_INVALID_GUILDID'), E_USER_WARNING);
					}
					//check if rank exists				    
					$nrankid = request_var('nrankid', 0);
					$sql = 'SELECT count(*) as rankcount FROM ' . MEMBER_RANKS_TABLE . ' 
                   	WHERE rank_id != 99 
                   	AND rank_id = ' . (int) $nrankid . ' 
                   	AND guild_id = ' . (int) $guild_id . ' 
                   	ORDER BY rank_id, rank_hide ASC ';
					$result = $db->sql_query($sql);
					if ((int) $db->sql_fetchfield('rankcount', false, $result) == 1)
					{
						trigger_error(sprintf($user->lang('ERROR_RANK_EXISTS'), $nrankid, $guild_id) . $this->link, E_USER_WARNING);
					}
					$db->sql_freeresult($result);
					$nrank_hide = (isset($_POST['nhide'])) ? 1 : 0;
					$nprefix = utf8_normalize_nfc(request_var('nprefix', '', true));
					$nsuffix = utf8_normalize_nfc(request_var('nsuffix', '', true));
					$this->insertnewrank($nrankid, $nrank_name, $nrank_hide, $nprefix, $nsuffix, $guild_id);

					// display success                    
					$success_message = $user->lang['ADMIN_RANKS_ADDED_SUCCESS'];
					trigger_error($success_message . $this->link);
				}
				
				// template filling 
				$sql = 'SELECT id, name FROM ' . GUILD_TABLE . ' ORDER BY id desc';
				$resultg = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($resultg))
				{
					$template->assign_block_vars('guild_row', array(
						'VALUE' => $row['id'] , 
						'SELECTED' => ($row['id'] == $guild_id) ? ' selected="selected"' : '' , 
						'OPTION' => $row['name']));
				}
				$db->sql_freeresult($resultg);
				
				// rank 99 is the out-rank
				$sql = 'SELECT rank_id, rank_name, rank_hide, rank_prefix, rank_suffix, guild_id FROM ' . MEMBER_RANKS_TABLE . ' 
	        		WHERE guild_id = ' . $guild_id . ' 
	        		ORDER BY rank_id, rank_hide  ASC ';
				
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					$prefix = $row['rank_prefix'];
					$suffix = $row['rank_suffix'];
					$template->assign_block_vars('ranks_row', array(
						'RANK_ID' => $row['rank_id'] , 
						'RANK_NAME' => $row['rank_name'] , 
						'RANK_PREFIX' => $prefix , 
						'RANK_SUFFIX' => $suffix , 
						'HIDE_CHECKED' => ($row['rank_hide'] == 1) ? 'checked="checked"' : '' , 
						'S_READONLY' => ($row['rank_id'] == 90 || $row['rank_id'] == 99) ? true : false , 
						'U_DELETE_RANK' => append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm&amp;mode=mm_ranks&amp;deleterank=1&amp;ranktodelete=" . $row['rank_id'] . "&amp;guild_id=" . $guild_id)));
				}
				$db->sql_freeresult($result);
				$form_key = 'mm_ranks';
				add_form_key($form_key);
				$template->assign_vars(array(
					'F_EDIT_RANKS' => append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm&amp;mode=mm_ranks") , 
					'GUILD_ID' => $guild_id));
				$this->page_title = 'ACP_MM_RANKS';
				$this->tpl_name = 'dkp/acp_' . $mode;
				break;
				
				
			/***************************************/
			// List Guilds
			/***************************************/
			case 'mm_listguilds':
				$this->link = '<br /><a href="' . append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm&amp;mode=mm_listguilds") . '"><h3>'.$user->lang['RETURN_GUILDLIST'].'</h3></a>';
				$showadd = (isset($_POST['guildadd'])) ? true : false;
				
				if ($showadd)
				{
					redirect(append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm&amp;mode=mm_addguild"));
					break;
				}
				$sort_order = array(
					0 => array(	'id' , 'id desc') , 
					1 => array('name' , 'name desc') , 
					2 => array('realm desc' , 'realm desc') , 
					3 => array('region' , 'region desc') , 
					4 => array('roster' , 'roster desc'));

				$current_order = switch_order($sort_order);
				$guild_count = 0;
				$previous_data = '';
				$sort_index = explode('.', $current_order['uri']['current']);
				$previous_source = preg_replace('/( (asc|desc))?/i', '', $sort_order[$sort_index[0]][$sort_index[1]]);
				$show_all = ((isset($_GET['show'])) && request_var('show', '') == 'all') ? true : false;
				
				// we select only guilds with id greater than zero
				$sql = 'SELECT id, name, realm, region, roster FROM ' . GUILD_TABLE . ' where id > 0  ORDER BY ' . $current_order['sql'];
				if (! ($guild_result = $db->sql_query($sql)))
				{
					trigger_error($user->lang['ERROR_GUILDNOTFOUND'], E_USER_WARNING);
				}
				$lines = 0;
				while ($row = $db->sql_fetchrow($guild_result))
				{
					$guild_count ++;
					$template->assign_block_vars('guild_row', array(
						'ID' => $row['id'] , 
						'NAME' => $row['name'] , 
						'REALM' => $row['realm'] , 
						'REGION' => $row['region'] , 
						'SHOW_ROSTER' => ($row['roster'] == 1 ? 'yes' : 'no') , 
						'U_VIEW_GUILD' => append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm&amp;mode=mm_addguild&amp;" . URI_GUILD . '=' . $row['id'])));
					$previous_data = $row[$previous_source];
				}
				
				$form_key = 'mm_listguilds';
				add_form_key($form_key);
				$template->assign_vars(array(
					'F_GUILD' => append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm") . '&amp;mode=mm_addguild' , 
					'L_TITLE' => $user->lang['ACP_MM_LISTGUILDS'] , 
					'L_EXPLAIN' => $user->lang['ACP_MM_LISTGUILDS_EXPLAIN'] , 
					'BUTTON_VALUE' => $user->lang['DELETE_SELECTED_GUILDS'] , 
					'O_ID' => $current_order['uri'][0] , 
					'O_NAME' => $current_order['uri'][1] , 
					'O_REALM' => $current_order['uri'][2] , 
					'O_REGION' => $current_order['uri'][3] , 
					'O_ROSTER' => $current_order['uri'][4] , 
					'U_LIST_GUILD' => append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm&amp;mode=mm_listguilds") , 
					'GUILDMEMBERS_FOOTCOUNT' => sprintf($user->lang['GUILD_FOOTCOUNT'], $guild_count)));
				$this->page_title = 'ACP_MM_LISTGUILDS';
				$this->tpl_name = 'dkp/acp_' . $mode;
				break;
			
			/*************************************
			 * ************ Add Guild ************
			 *************************************/
			case 'mm_addguild':
				$this->link = '<br /><a href="' . append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_mm&amp;mode=mm_listguilds") . '"><h3>'.$user->lang['RETURN_GUILDLIST'].'</h3></a>';
				/* select data */
				$update = false;
				if (isset($_GET[URI_GUILD]))
				{
					$this->url_id = request_var(URI_GUILD, 0);
				}
				
				$regionlist = array(
					'US' => 'US' , 
					'EU' => 'EU');
				
				if ($this->url_id != 0)
				{
					// we have a GET
					$update = true;
					$sql = 'SELECT id, name, realm, region, roster
					FROM ' . GUILD_TABLE . '  
					WHERE id = ' . $this->url_id;
					$result = $db->sql_query($sql);
					$row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);
					if (! $row)
					{
						trigger_error($user->lang['ERROR_GUILDNOTFOUND'], E_USER_WARNING);
					}
					else
					{
						// load guild object
						$this->guild = array(
							'guild_id' => $row['id'] , 
							'guild_name' => $row['name'] , 
							'guild_realm' => $row['realm'] , 
							'guild_region' => $row['region'] , 
							'guild_showroster' => $row['roster']);
						foreach ($regionlist as $key => $value)
						{
							$template->assign_block_vars('region_row', array(
								'VALUE' => $value , 
								'SELECTED' => ($this->guild['guild_region'] == $key) ? ' selected="selected"' : '' , 
								'OPTION' => (! empty($key)) ? $key : '(None)'));
						}
					}
				}
				else
				{
					// NEW PAGE                          
					foreach ($regionlist as $key => $value)
					{
						$template->assign_block_vars('region_row', array(
							'VALUE' => $value , 
							'SELECTED' => '' , 
							'OPTION' => (! empty($key)) ? $key : '(None)'));
					}
				}
				
				$add = (isset($_POST['add'])) ? true : false;
				$submit = (isset($_POST['update'])) ? true : false;
				$delete = (isset($_POST['delete'])) ? true : false;
				if ($add || $submit)
				{
					if (! check_form_key('addguild'))
					{
						trigger_error('FORM_INVALID');
					}
				}
				if ($add)
				{
					$guild_name = utf8_normalize_nfc(request_var('guild_name', '', true));
					$realm_name = utf8_normalize_nfc(request_var('realm', '', true));
					$region = request_var('region_id', '');
					$showroster = request_var('showroster', 0);
					if ($guild_name == null || $realm_name == null)
					{
						trigger_error($user->lang['ERROR_GUILDEMPTY'] . $this->link, E_USER_WARNING);
					}
					else
					{
						// check existing guild-realmname
						$result = $db->sql_query("SELECT count(*) as evcount from " . GUILD_TABLE . " 
							WHERE UPPER(name) = '" . strtoupper($db->sql_escape($guild_name)) . "'
							AND UPPER(realm) = '" . strtoupper($db->sql_escape($realm_name)) . "'");
						$grow = $db->sql_fetchrow($result);
						if ($grow['evcount'] != 0)
						{
							trigger_error($user->lang['ERROR_GUILDTAKEN'] . $this->link, E_USER_WARNING);
						}
						// we always add guilds with an id greater then zero. this way, the guild with id=zero is the "guildless" guild
						// the zero guild is added by default in a new install. 
						// do not delete the zero record in the guild table or you will see that guildless members 
						// become invisible in the roster and in the memberlist or in any list member selection that makes 
						// an inner join with the guild table. 
						if ($this->insertnewguild($guild_name, $realm_name, $region, $showroster) > 0)
						{
							$success_message = sprintf($user->lang['ADMIN_ADD_GUILD_SUCCESS'], $guild_name);
							trigger_error($success_message . $this->link, E_USER_NOTICE);
						}
						else
						{
							$success_message = sprintf($user->lang['ADMIN_ADD_GUILD_FAIL'], $guild_name);
							trigger_error($success_message . $this->link, E_USER_WARNING);
						}
					}
				}
				
				//updating
				if ($submit)
				{
					// get the guild id from the url parameter (via GET)
					if (isset($_GET[URI_GUILD]))
					{
						$this->url_id = request_var(URI_GUILD, 0);
					}
					else
					{
						trigger_error($user->lang['error_invalid_guild_provided'], E_USER_WARNING);
					}
					// get old value
					$sql = 'SELECT id, name, realm, region, roster 
					FROM ' . GUILD_TABLE . '  
					WHERE id = ' . (int) $this->url_id;
					$result = $db->sql_query($sql);
					// if we have a wrong id then error, this should not happen. 
					if (! $row)
					{
						trigger_error($user->lang['ERROR_GUILDNOTFOUND'], E_USER_WARNING);
					}
					
					// loop through object until sql_fetchrow returns false, fill object
					while ($row = $db->sql_fetchrow($result))
					{
						$this->old_guild = array(
							'guild_id' => $row['id'] , 
							'guild_name' => $row['name'] , 
							'guild_realm' => $row['realm'] , 
							'guild_region' => $row['region'] , 
							'guild_showroster' => $row['roster']);
					}
					
					$db->sql_freeresult($result);
					$new_guild_name = utf8_normalize_nfc(request_var('guild_name', ' ', true));
					$new_realm_name = utf8_normalize_nfc(request_var('realm', ' ', true));
					$new_region_name = request_var('region_id', ' ');
					$new_showroster = request_var('showroster', 0);

					// check if already exists 
					if($new_guild_name != $this->old_guild['guild_name'] || $new_realm_name != $this->old_guild['guild_realm'])
					{
						// check existing guild-realmname
						$result = $db->sql_query("SELECT count(*) as evcount from " . GUILD_TABLE . " 
							WHERE UPPER(name) = '" . strtoupper($db->sql_escape($new_guild_name)) . "'
							AND UPPER(realm) = '" . strtoupper($db->sql_escape($new_realm_name)) . "'");
						$grow = $db->sql_fetchrow($result);
						if ($grow['evcount'] != 0)
						{
							//throw error
							trigger_error($user->lang['ERROR_GUILDTAKEN'] . $this->link, E_USER_WARNING);
						}
					}
					
					$query = $db->sql_build_array('UPDATE', array(
						'name' => $new_guild_name , 
						'realm' => $new_realm_name , 
						'region' => $new_region_name , 
						'roster' => $new_showroster));
					$sql = 'UPDATE ' . GUILD_TABLE . ' SET ' . $query . ' WHERE id=' . (int) $this->url_id;
					$db->sql_query($sql);
					
					$success_message = sprintf($user->lang['ADMIN_UPDATE_GUILD_SUCCESS'], $this->url_id);
					trigger_error($success_message . $this->link);
				}
				
				if ($delete)
				{
					if (isset($_GET[URI_GUILD]))
					{
						// give a warning 
						if (confirm_box(true))
						{
							$guildid = request_var(URI_GUILD, 0);
							if ($guildid < 2)
							{
								trigger_error($user->lang['ERROR_GUILDIDRESERVED'], E_USER_WARNING);
							}

							// check if guild has members
							$sql = 'SELECT COUNT(*) as mcount FROM  ' . MEMBER_LIST_TABLE . '
                                       WHERE member_guild_id = ' . $guildid;
							$result = $db->sql_query($sql);
							if ((int) $db->sql_fetchfield('mcount') >= 1)
							{
								trigger_error($user->lang['ERROR_GUILDHASMEMBERS'], E_USER_WARNING);
							}
							
							$sql = 'DELETE FROM ' . MEMBER_RANKS_TABLE . '
                                       WHERE guild_id = ' . $guildid;
							$db->sql_query($sql);
							$sql = 'DELETE FROM ' . GUILD_TABLE . '
                                       WHERE id = ' . $guildid;
							$db->sql_query($sql);
							$success_message = sprintf($user->lang['ADMIN_DELETE_GUILD_SUCCESS'], $this->guild['guild_id']);
							trigger_error($success_message . adm_back_link($this->u_action), E_USER_NOTICE);
						}
						else
						{
							$s_hidden_fields = build_hidden_fields(array(
								'delete' => true , 
								'event_id' => request_var(URI_GUILD, 0)));
							$template->assign_vars(array(
								'S_HIDDEN_FIELDS' => $s_hidden_fields));
							confirm_box(false, $user->lang['CONFIRM_DELETE_GUILD'], $s_hidden_fields);
						}
					}
				}
				$form_key = 'addguild';
				add_form_key($form_key);
				$template->assign_vars(array(
					// Form values                       
					'GUILD_ID' => $this->url_id , 
					'GUILD_NAME' => isset($this->guild['guild_name']) ? $this->guild['guild_name'] : '' , 
					'REALM' => isset($this->guild['guild_realm']) ? $this->guild['guild_realm'] : '' , 
					'REGION' => isset($this->guild['guild_region']) ? $this->guild['guild_region'] : '' , 
					'SHOW_ROSTER' => isset($this->guild['guild_showroster']) ? (($this->guild['guild_showroster'] == 1) ? 'checked="checked"' : '') : '' , 
					// Language
					'L_TITLE' => $user->lang['ACP_MM_ADDGUILD'] , 
					'L_EXPLAIN' => $user->lang['ACP_MM_ADDGUILD_EXPLAIN'] , 
					'L_ADD_GUILD_TITLE' => (! $this->url_id) ? $user->lang['ADD_GUILD'] : $user->lang['EDIT_GUILD'] , 
					// Javascript messages
					'MSG_NAME_EMPTY' => $user->lang['FV_REQUIRED_NAME'] , 
					'S_ADD' => (! $this->url_id) ? true : false));
				$this->page_title = $user->lang['ACP_MM_ADDGUILD'];
				$this->tpl_name = 'dkp/acp_' . $mode;
				break;
				
			default:
				$this->page_title = 'ACP_DKP_MAINPAGE';
				$this->tpl_name = 'dkp/acp_mainpage';
				$success_message = 'Error';
				trigger_error($success_message . $this->link, E_USER_WARNING);
		}
	}

	/**
	 * function to batch delete members, called from listing
	 *
	 * @param array $members_to_delete
	 */
	public function member_batch_delete ($members_to_delete)
	{
		global $db, $user;
		
		if (! is_array($members_to_delete))
		{
			return;
		}
		
		if (sizeof($members_to_delete) == 0)
		{
			return;
		}
		
		if (confirm_box(true))
		{
			// recall hidden vars
			$members_to_delete = request_var('delete_id', array(0 => 0));
			$member_names = utf8_normalize_nfc(request_var('members', array(0 => ' '), true));
			$sql = 'SELECT * FROM ' . MEMBER_LIST_TABLE . ' WHERE ' . $db->sql_in_set('member_id', array_keys($members_to_delete));
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				$sql = 'DELETE FROM ' . RAID_DETAIL_TABLE . ' where member_id = ' . (int) $row['member_id'];
				$db->sql_query($sql);
				$sql = 'DELETE FROM ' . RAID_ITEMS_TABLE . ' where member_id = ' . (int) $row['member_id'];
				$db->sql_query($sql);
				$sql = 'DELETE FROM ' . MEMBER_DKP_TABLE . ' where member_id = ' . (int) $row['member_id'];
				$db->sql_query($sql);
				$sql = 'DELETE FROM ' . ADJUSTMENTS_TABLE . ' where member_id = ' . (int) $row['member_id'];
				$db->sql_query($sql);
				$sql = 'DELETE FROM ' . MEMBER_LIST_TABLE . ' where member_id = ' . (int) $row['member_id'];
				$db->sql_query($sql);
				//@todo if zerosum then put excess points in guildbank
				$this->old_member = array(
					'member_name' => $row['member_name'] , 
					'member_level' => $row['member_level'] , 
					'member_race_id' => $row['member_race_id'] , 
					'member_class_id' => $row['member_class_id']);
				$log_action = array(
					'header' => sprintf($user->lang['ACTION_MEMBER_DELETED'], $row['member_name']) , 
					'L_NAME' => $this->old_member['member_name'] , 
					'L_LEVEL' => $this->old_member['member_level'] , 
					'L_RACE' => $this->old_member['member_race_id'] , 
					'L_CLASS' => $this->old_member['member_class_id']);
				$this->log_insert(array(
					'log_type' => $log_action['header'] , 
					'log_action' => $log_action));
			}
			$db->sql_freeresult($result);
			$str_members = implode($member_names, ',');
			$success_message = sprintf($user->lang['ADMIN_DELETE_MEMBERS_SUCCESS'], $str_members);
			trigger_error($success_message . $this->link, E_USER_NOTICE);
		}
		else
		{
			$sql = "SELECT member_name, member_id FROM " . MEMBER_LIST_TABLE . " WHERE " . $db->sql_in_set('member_id', array_keys($members_to_delete));
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				$member_names[] = $row['member_name'];
			}
			$db->sql_freeresult($result);
			$s_hidden_fields = build_hidden_fields(array(
				'delete' => true , 
				'delete_id' => $members_to_delete , 
				'members' => $member_names));
			$str_members = implode($member_names, ',');
			confirm_box(false, sprintf($user->lang['CONFIRM_DELETE_MEMBER'], $str_members), $s_hidden_fields);
		}
	}

	/*
	 * generates a standard portrait image url for wow /aion based on characterdata
	 */
	public function generate_portraitlink ($game_id, $race_id, $class_id, $gender_id, $level)
	{
		$memberportraiturl = '';
		if ($game_id == 'aion')
		{
			$memberportraiturl = 'images/roster_portraits/aion/' . $race_id . '_' . $gender_id . '.jpg';
		}
		elseif ($game_id == 'wow')
		{
			if ($level <= "59")
			{
				$maxlvlid = "wow-default";
			}
			elseif ($level <= 69)
			{
				$maxlvlid = "wow";
			}
			elseif ($level <= 79)
			{
				$maxlvlid = "wow-70";
			}
			else
			{
				// level 85 is not yet iconified
				$maxlvlid = "wow-80";
			}
			$memberportraiturl = 'images/roster_portraits/' . $maxlvlid . '/' . $gender_id . '-' . $race_id . '-' . $class_id . '.gif';
		}
		return $memberportraiturl;
	}

	/*
	 * generates armory link (only wow)
	 */
	public function generate_armorylink ($game_id, $region, $realm, $name)
	{
		$site = '';
		switch ($region)
		{
			case 'EU':
				$site = 'http://eu.battle.net/wow/en/character/';
				break;
			case 'US':
				$site = 'http://us.battle.net/wow/en/character/';
				break;
			default:
				$site = 'http://eu.battle.net/wow/en/character/';
		}
		$realm = str_replace(' ', '-', $realm);
		return $site . urlencode($realm) . '/' . urlencode($name) . '/simple';
	}

	/**
	 * get membername given an id
	 *
	 * @param int $member_id
	 * @return string
	 */
	public function get_member_name ($member_id)
	{
		global $db;
		$sql = 'SELECT member_name
                FROM ' . MEMBER_LIST_TABLE . "
                WHERE member_id = " . (int) $member_id;
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$membname = $row['member_name'];
		}
		$db->sql_freeresult($result);
		if (isset($membname))
		{
			return $membname;
		}
		else
		{
			return '';
		}
	}

	/**
	 * get id given a membername and guild
	 *
	 * @param string $membername
	 * @param int $guild_id optional
	 * @return int
	 */
	public function get_member_id ($membername, $guild_id = 0)
	{
		global $db;
		if($guild_id !=0)
		{
			$sql = 'SELECT member_id
	                FROM ' . MEMBER_LIST_TABLE . "
	                WHERE member_name ='" . $db->sql_escape($membername) . "'
	                AND member_guild_id = " . (int) $db->sql_escape($guild_id);
			
		}
		else
		{
			$sql = 'SELECT member_id
	                FROM ' . MEMBER_LIST_TABLE . "
	                WHERE member_name ='" . $db->sql_escape($membername) . "'";
		}
		
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$membid = $row['member_id'];
			break;
		}
		$db->sql_freeresult($result);
		if (isset($membid))
		{
			return $membid;
		}
		else
		{
			return 0;
		}
	}

	/***
	 * function for inserting a new guild
	 * you have to perform argument and ifexist validations befre you call this function!
	 * is also called from armory plugin
	 * 
	 */
	public function insertnewguild ($guild_name, $realm_name, $region, $showroster, $aionlegionid = 0, $aionserverid = 0)
	{
		global $db, $user, $config;
		$this_guild_id = $db->sql_query("SELECT MAX(id) as id FROM " . GUILD_TABLE . ";");
		$this_guild_id = $db->sql_fetchrow($this_guild_id);
		$this_guild_id = (int) $this_guild_id['id'] + 1;
		
		$query = $db->sql_build_array('INSERT', array(
			'id' => $this_guild_id , 
			'name' => $guild_name , 
			'realm' => $realm_name , 
			'region' => $region , 
			'roster' => $showroster , 
			'aion_legion_id' => $aionlegionid , 
			'aion_server_id' => $aionserverid));
		$db->sql_query('INSERT INTO ' . GUILD_TABLE . $query);
		
		$log_action = array(
			'header' => 'L_ACTION_GUILD_ADDED' , 
			'id' => $this_guild_id , 
			'L_USER' => $user->data['user_id'] , 
			'L_USERCOLOUR' => $user->data['user_colour'] , 
			'L_NAME' => $guild_name , 
			'L_REALM' => $realm_name , 
			'L_ADDED_BY' => $user->data['username']);
		
		$this->log_insert(array(
			'log_type' => $log_action['header'] , 
			'log_action' => $log_action));
		return $this_guild_id;
	}

	/***
	 * function for deleting rank
	 * $nrankid = int
	 * $guild_id = int
	 * $override = boolean true to delete even when members exist
	 * 				boolean false to not delete when members exist
	 * is also called from armory plugin
	 */
	public function deleterank ($nrankid, $guild_id, $override)
	{
		global $db, $user, $config;
		if (! $override)
		{
			// check if rank is used  
			$sql = 'SELECT count(*) as rankcount FROM ' . MEMBER_LIST_TABLE . ' WHERE 
            		 member_rank_id   = ' . (int) $nrankid . ' and
            		 member_guild_id =  ' . (int) $guild_id;
			$result = $db->sql_query($sql);
			if ((int) $db->sql_fetchfield('rankcount') >= 1)
			{
				trigger('Cannot delete rank ' . $nrankid . '. There are members with this rank in guild . ' . $guild_id, E_USER_WARNING);
			}
		}
		// ok proceed to delete
		$sql = 'DELETE FROM ' . MEMBER_RANKS_TABLE . ' WHERE 
        		 rank_id   = ' . (int) $nrankid . ' and
        		 guild_id =  ' . (int) $guild_id;
		$db->sql_query($sql);
		// log the action
		$log_action = array(
			'header' => 'L_ACTION_RANK_DELETED' , 
			'id' => (int) $nrankid , 
			'GUILD_ID' => (int) $guild_id , 
			'L_ADDED_BY' => $user->data['username']);
		$this->log_insert(array(
			'log_type' => $log_action['header'] , 
			'log_action' => $log_action));
		return true;
	}

	/***
	 * function for inserting a new rank
	 * you have to perform argument and ifexist validations before you call this function!
	 * $nrankid = int
	 * $guild_id = int
	 * $nrank_name = string
	 * $nprefix = string
	 * $nsuffix = string
	 * is also called from armory plugin
	 * 
	 */
	public function insertnewrank ($nrankid, $nrank_name, $nrank_hide, $nprefix, $nsuffix, $guild_id)
	{
		global $db, $user, $config;
		// build insert array                     
		$query = $db->sql_build_array('INSERT', array(
			'rank_id' => (int) $nrankid , 
			'rank_name' => $nrank_name , 
			'rank_hide' => $nrank_hide , 
			'rank_prefix' => $nprefix , 
			'rank_suffix' => $nsuffix , 
			'guild_id' => (int) $guild_id));
		// insert new rank                    	
		$db->sql_query('INSERT INTO ' . MEMBER_RANKS_TABLE . $query);
		// log the action
		$log_action = array(
			'header' => 'L_ACTION_RANK_ADDED' , 
			'id' => (int) $nrankid , 
			'L_NAME' => $nrank_name , 
			'L_ADDED_BY' => $user->data['username']);
		$this->log_insert(array(
			'log_type' => $log_action['header'] , 
			'log_action' => $log_action));
		return true;
	}

	/***
	 * function for inserting a new member
	 * you have to perform argument and ifexist validations before you call this function!
	 * is also called from Raidtracker and Armory plugin. 
	 * $memberarmoryurl, game_id, phpbb_user_id are optional
	 * 
	 * @returns the new memberid or false
	 * 
	 */
	public function insertnewmember ($member_name, $member_status, $member_lvl, $race_id, $class_id, $rank_id, 
	$member_comment, $joindate, $leavedate, $guild_id, $gender, $achievpoints, $memberarmoryurl = ' ', $memberportraiturl = ' ', $realm = '', $game_id = 'wow', $phpbb_user_id = 0)
	{
		global $db, $user, $config;
		if ($member_status != 1)
		{
			$member_status = 1;
		}
		// Check for existing member name
		$sql = "SELECT member_id 
				FROM " . MEMBER_LIST_TABLE . " 
				WHERE member_name = '" . $db->sql_escape($member_name) . "'
				AND member_guild_id = " . $guild_id;
		$result = $db->sql_query($sql);
		$member_id = 0;
		while ($row = $db->sql_fetchrow($result))
		{
			$member_id = $row['member_id'];
		}
		// already created ?
		if ($member_id > 0)
		{
			return false;
		}
		// check level and set to maxlevel if null
		if ($member_lvl == 0)
		{
			// get maxlevel
			$sql = "SELECT max(class_max_level) as maxlevel FROM " . CLASS_TABLE . " where game_id = '" . $game_id . "'";
			$result = $db->sql_query($sql);
			$member_lvl = (int) $db->sql_fetchfield('maxlevel', false, $result);
		}
		if (($game_id == 'wow' || $game_id == 'aion') && $memberportraiturl == ' ')
		{
			$memberportraiturl = $this->generate_portraitlink($game_id, $race_id, $class_id, $gender, $member_lvl);
		}
		if ($game_id == 'wow' & $memberarmoryurl == ' ')
		{
			if ($config['bbdkp_default_region'] == '')
			{
				// if region is not set then put EU...
				set_config('bbdkp_default_region', 'EU', true);
			}
			$memberarmoryurl = $this->generate_armorylink($game_id, $config['bbdkp_default_region'], $realm, $member_name);
		}
		if ($realm == '')
		{
			$realm = $config['bbdkp_default_realm'];
		}
		
		$query = $db->sql_build_array('INSERT', array(
			'member_name' => ucwords($member_name) , 
			'member_status' => $member_status , 
			'member_level' => $member_lvl , 
			'member_race_id' => $race_id , 
			'member_class_id' => $class_id , 
			'member_rank_id' => $rank_id , 
			'member_comment' => (string) $member_comment , 
			'member_joindate' => (int) $joindate , 
			'member_outdate' => (int) $leavedate , 
			'member_guild_id' => $guild_id , 
			'member_gender_id' => $gender , 
			'member_achiev' => $achievpoints , 
			'member_armory_url' => (string) $memberarmoryurl , 
			'phpbb_user_id' => (int) $phpbb_user_id , 
			'game_id' => (string) $game_id , 
			'member_portrait_url' => (string) $memberportraiturl));
		
		$log_action = array(
			'header' 	 => 'L_ACTION_MEMBER_ADDED' , 
			'L_NAME' 	 => $member_name , 
			'L_LEVEL' 	 => $member_lvl , 
			'L_RACE' 	 => $race_id , 
			'L_CLASS' 	 => $class_id , 
			'L_ADDED_BY' => $user->data['username']);
		
		$db->sql_query('INSERT INTO ' . MEMBER_LIST_TABLE . $query);
		
		$member_id = $db->sql_nextid();
		
		$this->log_insert(array(
			'log_type' => $log_action['header'] , 
			'log_action' => $log_action));
		return $member_id;
	}

	/***
	 * 
	 * function for removing member from guild but leave him in the system. this is called from armory plugin
	 *
	 */
	public function removemember ($member_name, $guild_id)
	{
		global $db, $user, $config;
		// find id for existing member name
		$sql = "SELECT * 
				FROM " . MEMBER_LIST_TABLE . " 
				WHERE member_name = '" . $db->sql_escape($member_name) . "' and member_guild_id = " . (int) $guild_id;
		$result = $db->sql_query($sql);
		// get old data
		while ($row = $db->sql_fetchrow($result))
		{
			$this->old_member = array(
				'member_id' => $row['member_id'] , 
				'member_rank_id' => $row['member_rank_id'] , 
				'member_guild_id' => $row['member_guild_id'] , 
				'member_comment' => $row['member_comment']);
		}
		$db->sql_freeresult($result);
		$sql_arr = array(
			'member_rank_id' => 99 , 
			'member_comment' => "Member left " . date("F j, Y, g:i a") . ' by Armory plugin' , 
			'member_outdate' => $this->time , 
			'member_guild_id' => 0);
		$sql = 'UPDATE ' . MEMBER_LIST_TABLE . '
        SET ' . $db->sql_build_array('UPDATE', $sql_arr) . '
        WHERE member_id = ' . (int) $this->old_member['member_id'] . ' and member_guild_id = ' . (int) $this->old_member['member_guild_id'];
		$db->sql_query($sql);
		$log_action = array(
			'header' => 'L_ACTION_MEMBER_UPDATED' , 
			'L_NAME' => $member_name , 
			'L_RANK_BEFORE' => $this->old_member['member_rank_id'] , 
			'L_COMMENT_BEFORE' => $this->old_member['member_comment'] , 
			'L_RANK_AFTER' => 99 , 
			'L_COMMENT_AFTER' => "Member left " . date("F j, Y, g:i a") . ' by Armory plugin' , 
			'L_UPDATED_BY' => $user->data['username']);
		$this->log_insert(array(
			'log_type' => $log_action['header'] , 
			'log_action' => $log_action));
		return true;
	}

	/***
	 * function for updating a new member
	 * is also called from armory plugin for updating existing guildmembers
	 * guildid is not updated
	 * url is not updated
	 */
	public function updatemember ($member_id, $member_name, $member_lvl, $race_id, $class_id, $rank_id, $member_comment, 
	$guild_id, $gender, $achievpoints, $memberarmoryurl = ' ', $memberportraiturl = ' ', $game_id = 'wow', $member_status = 1)
	{
		global $db, $user, $config;
		
		if ($member_id == 0)
		{
			return false;
		}
		
		// get existing data
		$sql = 'SELECT * FROM ' . MEMBER_LIST_TABLE . ' WHERE member_id = ' . (int) $member_id;
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$this->old_member = array(
				'game_id' => $row['game_id'] , 
				'member_name' => (string) $row['member_name'] ,
				'member_level' => (int) $row['member_level'] , 
				'member_race_id' => (int) $row['member_race_id'] , 
				'member_rank_id' => (int) $row['member_rank_id'] , 
				'member_class_id' => (int) $row['member_class_id'] , 
				'member_gender_id' => (int) $row['member_gender_id'] , 
				'member_achiev' => (int) $row['member_achiev'] , 
				'member_armory_url' => $row['member_armory_url'] , 
				'member_portrait_url' => $row['member_portrait_url'] , 
				'member_joindate' => (int) $row['member_joindate'] , 
				'member_outdate' => (int) $row['member_outdate'] , 
				'member_status' => (int) $row['member_status']);
		}
		$db->sql_freeresult($result);

		// check level and set to maxlevel if zero
		if ($member_lvl == 0)
		{
			// get maxlevel
			$sql = "SELECT max(class_max_level) as maxlevel FROM " . CLASS_TABLE;
			$result = $db->sql_query($sql);
			$member_lvl = (int) $db->sql_fetchfield('maxlevel', false, $result);
		}
		
		if (($game_id == 'wow' || $game_id == 'aion') && $memberportraiturl == ' ')
		{
			$memberportraiturl = $this->generate_portraitlink($game_id, $race_id, $class_id, $gender, $member_lvl);
		}
		
		if ($game_id == 'wow' & $memberarmoryurl == ' ')
		{
			if ($config['bbdkp_default_region'] == '')
			{
				// if region is not set then put EU...
				set_config('bbdkp_default_region', 'EU', true);
			}
			$realm = $config['bbdkp_default_realm'];
			$memberarmoryurl = $this->generate_armorylink($game_id, $config['bbdkp_default_region'], $realm, $member_name);
		}
		
		if ($achievpoints == 0)
		{
			$achievpoints = $this->old_member['member_achiev'];
		}
		
		// Get first and last raiding dates
		$sql = "SELECT b.member_id, MIN(a.raid_start) as startdate , MAX(a.raid_start) as enddate
			FROM " . RAIDS_TABLE . " a INNER JOIN " . RAID_DETAIL_TABLE . " b on a.raid_id = b.raid_id 
			WHERE  b.member_id = " . $member_id . " group by b.member_id ";
		$result = $db->sql_query($sql);
		$startraiddate = (int) $db->sql_fetchfield('startdate', false, $result);
		$endraiddate = (int) $db->sql_fetchfield('enddate', false, $result);
		$db->sql_freeresult($result);
		if ($startraiddate != 0 && ($this->old_member['member_joindate'] == 0 || $this->old_member['member_joindate'] > $startraiddate))
		{
			$joindate = $startraiddate;
		}
		else
		{
			$joindate = $this->old_member['member_joindate'];
		}
		$leavedate = $this->old_member['member_outdate'];
		if ($this->old_member['member_outdate'] < $endraiddate || $this->old_member['member_outdate'] > time())
		{
			$leavedate = mktime(0, 0, 0, 12, 31, 2030);
		}
		$sql_arr = array(
			'game_id' => $game_id , 
			'member_name' => (string) $member_name ,		
			'member_level' => (int) $member_lvl , 
			'member_race_id' => (int) $race_id , 
			'member_rank_id' => (int) $rank_id , 
			'member_class_id' => (int) $class_id , 
			'member_gender_id' => (int) $gender , 
			'member_achiev' => (int) $achievpoints , 
			'member_armory_url' => trim($memberarmoryurl) , 
			'member_portrait_url' => trim($memberportraiturl) , 
			'member_joindate' => (int) $joindate , 
			'member_outdate' => (int) $leavedate , 
			'member_status' => (int) $member_status);
		
		if ($sql_arr != $this->old_member)
		{
			// we have changes, so update 
			$sql = 'UPDATE ' . MEMBER_LIST_TABLE . '
            SET ' . $db->sql_build_array('UPDATE', $sql_arr) . '
            WHERE member_id = ' . (int) $member_id;
			$db->sql_query($sql);
			
			// update the comment - its not included in array comparison because it always changes.
			$sql = 'UPDATE ' . MEMBER_LIST_TABLE . "
            SET member_comment  = '" . $db->sql_escape($member_comment) . "'
            WHERE member_id = " . (int) $member_id;
			
			$db->sql_query($sql);
			
			$log_action = array(
				'header' => 'L_ACTION_MEMBER_UPDATED' , 
				'L_NAME' => $member_name , 
				'L_NAME_BEFORE' => $this->old_member['member_name'] , 
				'L_LEVEL_BEFORE' => $this->old_member['member_level'] , 
				'L_RACE_BEFORE' => $this->old_member['member_race_id'] , 
				'L_RANK_BEFORE' => $this->old_member['member_rank_id'] , 
				'L_CLASS_BEFORE' => $this->old_member['member_class_id'] , 
				'L_GENDER_BEFORE' => $this->old_member['member_gender_id'] , 
				'L_ACHIEV_BEFORE' => $this->old_member['member_achiev'] , 
				'L_NAME_AFTER' => $member_name,
				'L_LEVEL_AFTER' => $member_lvl , 
				'L_RACE_AFTER' => $race_id , 
				'L_RANK_AFTER' => $rank_id , 
				'L_CLASS_AFTER' => $class_id , 
				'L_GENDER_AFTER' => $gender , 
				'L_ACHIEV_AFTER' => $achievpoints , 
				'L_UPDATED_BY' => $user->data['username']);
			
			$this->log_insert(array(
				'log_type' => $log_action['header'] , 
				'log_action' => $log_action));
			return true;
		}
		else
		{
			// no change
			return false;
		}
	}
}
?>
