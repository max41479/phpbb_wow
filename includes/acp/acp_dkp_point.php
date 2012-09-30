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
 * This acp class manages point settings
 *
 */
class acp_dkp_point extends bbDKP_Admin
{

	function main ($id, $mode)
	{
		global $db, $user, $auth, $template, $sid, $cache;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;
		$user->add_lang(array(
			'mods/dkp_admin'));
		$user->add_lang(array(
			'mods/dkp_common'));
		$link = '<br /><a href="' . append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_point&amp;mode=pointconfig") . '"><h3>' . $user->lang['RETURN_DKPINDEX'] . '</h3></a>';
		switch ($mode)
		{
			case 'pointconfig':
				$submit = (isset($_POST['update'])) ? true : false;
				if ($submit)
				{
					if (! check_form_key('acp_dkp'))
					{
						trigger_error($user->lang['FV_FORMVALIDATION'], E_USER_WARNING);
					}
					//decay         
					set_config('bbdkp_decay', request_var('decay_activate', 0), true);
					set_config('bbdkp_itemdecaypct', request_var('itemdecaypct', 0), true);
					set_config('bbdkp_raiddecaypct', request_var('raiddecaypct', 0), true);
					set_config('bbdkp_decayfrequency', request_var('decayfreq', 0), true);
					set_config('bbdkp_decayfreqtype', request_var('decayfreqtype', 0), true);
					set_config('bbdkp_adjdecaypct', request_var('adjdecaypct', 0), true);
					//time
					set_config('bbdkp_timebased', request_var('timebonus_activate', 0.00), true);
					set_config('bbdkp_dkptimeunit', request_var('dkptimeunit', 0.00), true);
					set_config('bbdkp_timeunit', request_var('timeunit', 0.00), true);
					set_config('bbdkp_standardduration', request_var('standardduration', 0.00), true);
					//zerosum
					if (request_var('zerosum_activate', 0) == 0)
					{
						set_config('bbdkp_zerosum', 0, true);
					}
					if (request_var('zerosum_activate', 0) == 1 && request_var('epgp_activate', 0) == 1)
					{
						set_config('bbdkp_zerosum', 1, true);
						//epgp and zerosum are mutually exclusive, zerosum will prevail if selected
						set_config('bbdkp_epgp', 0, true);
					}
					if (request_var('zerosum_activate', 0) == 0 && request_var('epgp_activate', 0) == 1)
					{
						set_config('bbdkp_zerosum', 0, true);
						set_config('bbdkp_epgp', 1, true);
					}
					if (request_var('zerosum_activate', 0) == 1 && request_var('epgp_activate', 0) == 0)
					{
						set_config('bbdkp_zerosum', 1, true);
						set_config('bbdkp_epgp', 0, true);
					}
					if (request_var('zerosum_activate', 0) == 0 && request_var('epgp_activate', 0) == 0)
					{
						set_config('bbdkp_zerosum', 0, true);
						set_config('bbdkp_epgp', 0, true);
					}
					set_config('bbdkp_bankerid', request_var('zerosumbanker', 0), true);
					set_config('bbdkp_zerosumdistother', request_var('zerosumdistother', 0), true);
					set_config('bbdkp_basegp', request_var('basegp', 0.0), true);
					set_config('bbdkp_minep', request_var('minep', 0.0), true);
					set_config('bbdkp_decaycron', request_var('decay_scheduler', 0), true);
					$cache->destroy('config');
					trigger_error('Settings saved.' . $link, E_USER_NOTICE);
				}
				$zerosum_synchronise = (isset($_POST['zerosum_synchronise'])) ? true : false;
				$decay_synchronise = (isset($_POST['decay_synchronise'])) ? true : false;
				$dkp_synchronise = (isset($_POST['syncdkp'])) ? true : false;
				// resynchronise DKP
				if ($dkp_synchronise)
				{
					if (confirm_box(true))
					{
						if (! class_exists('acp_dkp_sys'))
						{
							require ($phpbb_root_path . 'includes/acp/acp_dkp_sys.' . $phpEx);
						}
						$acp_dkp_sys = new acp_dkp_sys();
						$acp_dkp_sys->syncdkpsys();
					}
					else
					{
						$s_hidden_fields = build_hidden_fields(array(
							'syncdkp' => true));
						$template->assign_vars(array(
							'S_HIDDEN_FIELDS' => $s_hidden_fields));
						confirm_box(false, sprintf($user->lang['RESYNC_DKP_CONFIRM']), $s_hidden_fields);
					}
				}
				// recalculate zerosum
				if ($zerosum_synchronise)
				{
					if (confirm_box(true))
					{
						if (! class_exists('acp_dkp_raid'))
						{
							require ($phpbb_root_path . 'includes/acp/acp_dkp_item.' . $phpEx);
						}
						$acp_dkp_item = new acp_dkp_item();
						$count = $acp_dkp_item->sync_zerosum($config['bbdkp_zerosum']);
					}
					else
					{
						$s_hidden_fields = build_hidden_fields(array(
							'zerosum_synchronise' => true));
						$template->assign_vars(array(
							'S_HIDDEN_FIELDS' => $s_hidden_fields));
						confirm_box(false, sprintf($user->lang['RESYNC_ZEROSUM_CONFIRM']), $s_hidden_fields);
					}
				}
				if ($decay_synchronise)
				{
					if (confirm_box(true))
					{
						// decay this item
						if (! class_exists('acp_dkp_raid'))
						{
							require ($phpbb_root_path . 'includes/acp/acp_dkp_raid.' . $phpEx);
						}
						$acp_dkp_raid = new acp_dkp_raid();
						$count = $acp_dkp_raid->sync_decay($config['bbdkp_decay']);
						if (! class_exists('acp_dkp_adj'))
						{
							require ($phpbb_root_path . 'includes/acp/acp_dkp_adj.' . $phpEx);
						}
						$acp_dkp_adj = new acp_dkp_adj();
						$count1 = $acp_dkp_adj->sync_adjdecay($config['bbdkp_decay']);
						trigger_error(sprintf($user->lang['RESYNC_DECAY_SUCCESS'], $count + $count1) . $link, E_USER_NOTICE);
					}
					else
					{
						$s_hidden_fields = build_hidden_fields(array(
							'decay_synchronise' => true));
						$template->assign_vars(array(
							'S_HIDDEN_FIELDS' => $s_hidden_fields));
						confirm_box(false, sprintf($user->lang['RESYNC_DECAY_CONFIRM']), $s_hidden_fields);
					}
				}
				$freqtypes = array(
					0 => $user->lang['FREQ0'] , 
					1 => $user->lang['FREQ1'] , 
					2 => $user->lang['FREQ2']);
				$s_freqtype_options = '';
				foreach ($freqtypes as $key => $type)
				{
					$selected = ($config['bbdkp_decayfreqtype'] == $key) ? ' selected="selected"' : '';
					$s_freqtype_options .= '<option value="' . $key . '" ' . $selected . '> ' . $type . '</option>';
				}
				$s_bankerlist_options = '';
				$sql = 'SELECT member_id, member_name FROM ' . MEMBER_LIST_TABLE . " WHERE member_status = '1' order by member_name asc";
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					$selected = ($config['bbdkp_bankerid'] == $row['member_id']) ? ' selected="selected"' : '';
					$s_bankerlist_options .= '<option value="' . $row['member_id'] . '" ' . $selected . '> ' . $row['member_name'] . '</option>';
				}
				add_form_key('acp_dkp');
				$template->assign_vars(array(
					'DKP_NAME' => $config['bbdkp_dkp_name'] , 
					//epgp
					'F_EPGPACTIVATE' => $config['bbdkp_epgp'] , 
					'BASEGP' => $config['bbdkp_basegp'] , 
					'MINEP' => $config['bbdkp_minep'] , 
					//decay
					'F_DECAYACTIVATE' => $config['bbdkp_decay'] , 
					'ITEMDECAYPCT' => $config['bbdkp_itemdecaypct'] , 
					'RAIDDECAYPCT' => $config['bbdkp_raiddecaypct'] , 
					'ADJDECAYPCT' => $config['bbdkp_adjdecaypct'] , 
					'DECAYFREQ' => $config['bbdkp_decayfrequency'] , 
					'S_FREQTYPE_OPTIONS' => $s_freqtype_options , 
					'F_DECAYSCHEDULER' => $config['bbdkp_decaycron'] , 
					//time dkp
					'F_TIMEBONUSACTIVATE' => $config['bbdkp_timebased'] , 
					'DKPTIMEUNIT' => $config['bbdkp_dkptimeunit'] , 
					'TIMEUNIT' => $config['bbdkp_timeunit'] , 
					'STANDARDDURATION' => $config['bbdkp_standardduration'] , 
					//zs
					'F_ZEROSUMACTIVATE' => $config['bbdkp_zerosum'] , 
					'S_BANKER_OPTIONS' => $s_bankerlist_options , 
					'F_ZEROSUM_DISTOTHER' => $config['bbdkp_zerosumdistother'] , 
					'DECAYIMGEXAMPLE' => $phpbb_root_path . "adm/style/dkp/decayexample.png"));
				$this->page_title = 'ACP_DKP_POINT_CONFIG';
				$this->tpl_name = 'dkp/acp_dkp_' . $mode;
				break;
		}
	}
}
?>