<?php

if (!defined('IN_PHPBB'))
{
	exit;
}

class acp_pbwow2
{
	var $u_action;
	var $new_config = array();

	function main($id, $mode)
	{
		global $db, $user, $template, $cache;
		global $config, $pbwow_config, $phpbb_root_path, $phpbb_admin_path, $phpEx, $table_prefix;

		// Some basic includes
		if (!function_exists('db_theme_data')){ include($phpbb_root_path . 'includes/acp/acp_styles.' . $phpEx); }
		if (!class_exists('phpbb_db_tools')) { include($phpbb_root_path . 'includes/db/db_tools.' . $phpEx); }
		$db_tool = new phpbb_db_tools($db);
		$user->add_lang('mods/lang_pbwow_acp');
		if (isset($display_vars['lang'])) { $user->add_lang($display_vars['lang']); }
		$this->tpl_name = 'acp_pbwow2';
		
		// Some constants
		$module_version = '2.0.7';
		$dbtable = defined('PBWOW2_CONFIG_TABLE') ? PBWOW2_CONFIG_TABLE : '';
		$legacy_dbtable = defined('PBWOW_CONFIG_TABLE') ? PBWOW_CONFIG_TABLE : '';
		$topics_table = TOPICS_TABLE;

		$constantsokay = $dbokay = $legacy_constants = $legacy_db_active = $legacy_topics_mod = false;
		$style_version = $imageset_version = $template_version = $theme_version = '';
		
		// Check if constants have been set correctly
		// if yes, check if the config table exists
		// if yes, load the config variables
		if($dbtable == ($table_prefix . 'pbwow2_config'))
		{
			$constantsokay = true;
			
			if($db_tool->sql_table_exists($dbtable))
			{
				$dbokay = true;
				$pbwow_config = $this->get_pbwow_config();
				$this->new_config = $pbwow_config;
				$this->get_pbwow_umil_version();
			}
		}

		if($mode == 'overview') {
			$cpflist = $this->get_cpf_list();
			
			$style_root = ($phpbb_root_path . 'styles/pbwow2/');

			if(file_exists($style_root . 'style.cfg')) {
				$values = parse_cfg_file($style_root . 'style.cfg');
				$style_version = (isset($values['version'])) ? $values['version'] : '';
			}
			if(file_exists($style_root . 'imageset/imageset.cfg')) {
				$values = parse_cfg_file($style_root . 'imageset/imageset.cfg');
				$imageset_version = (isset($values['version'])) ? $values['version'] : '';
			}
			if(file_exists($style_root . 'template/template.cfg')) {
				$values = parse_cfg_file($style_root . 'template/template.cfg');
				$template_version = (isset($values['version'])) ? $values['version'] : '';
			}
			if(file_exists($style_root . 'theme/theme.cfg')) {
				$values = parse_cfg_file($style_root . 'theme/theme.cfg');
				$theme_version = (isset($values['version'])) ? $values['version'] : '';
			}
			
			$versions = $this->obtain_pbwow_version_info(request_var('versioncheck_force', false),true);
			
			// Check if old constants are still being used
			if(!empty($legacy_dbtable))
			{
				$legacy_constants = true;
			}
			
			// Check if old table still exists
			if($db_tool->sql_table_exists($legacy_dbtable) || $db_tool->sql_table_exists($table_prefix . 'pbwow_config'))
			{
				$legacy_db_active = true;
			}
			
			// Check if topics table has been modded
			if($db_tool->sql_column_exists(TOPICS_TABLE, 'topic_first_poster_rank_img') || $db_tool->sql_column_exists(TOPICS_TABLE, 'topic_first_poster_rank_title'))
			{
				$legacy_topics_mod = true;
			}
		}


		/**
		*	Validation types are:
		*		string, int, bool,
		*		script_path (absolute path in url - beginning with / and no trailing slash),
		*		rpath (relative), rwpath (realtive, writeable), path (relative path, but able to escape the root), wpath (writeable)
		*/
		switch ($mode)
		{
			case 'overview':
				$display_vars = array(
					'title'	=> 'ACP_PBWOW2_OVERVIEW_TITLE',
					'vars'	=> array()
				);
			break;
			case 'config':
				$display_vars = array(
					'title'	=> 'ACP_PBWOW_CONFIG_TITLE',
					'vars'	=> array(
						'legend1'				=> 'ACP_PBWOW_LOGO',
						'logo_size_width'		=> array('lang' => 'PBWOW_LOGO_SIZE', 			'validate' => 'int:0',	'type' => false, 'method' => false, 'explain' => false,),
						'logo_size_height'		=> array('lang' => 'PBWOW_LOGO_SIZE', 			'validate' => 'int:0',	'type' => false, 'method' => false, 'explain' => false,),
						'logo_enable'			=> array('lang' => 'PBWOW_LOGO_ENABLE',			'validate' => 'bool',	'type' => 'radio:enabled_disabled',	'explain' => true),
						'logo_src'				=> array('lang' => 'PBWOW_LOGO_SRC',			'validate' => 'string',	'type' => 'text:20:255', 'explain' => true),
						'logo_size'				=> array('lang' => 'PBWOW_LOGO_SIZE',			'validate' => 'int:0',	'type' => 'dimension:3:4', 'explain' => true, 'append' => ' ' . $user->lang['PIXEL']),
						'logo_margins'			=> array('lang' => 'PBWOW_LOGO_MARGINS',		'validate' => 'string',	'type' => 'text:20:20', 'explain' => true),

						'legend2'				=> 'ACP_PBWOW_TOPBAR',
						'topbar_enable'			=> array('lang' => 'PBWOW_TOPBAR_ENABLE',		'validate' => 'bool',	'type' => 'radio:enabled_disabled',	'explain' => true),
						'topbar_code'			=> array('lang' => 'PBWOW_TOPBAR_CODE',			'type' => 'textarea:6:6',	'explain' => true),
						'topbar_fixed'			=> array('lang' => 'PBWOW_TOPBAR_FIXED',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),

						'legend3'				=> 'ACP_PBWOW_HEADERLINKS',
						'headerlinks_enable'	=> array('lang' => 'PBWOW_HEADERLINKS_ENABLE',	'validate' => 'bool',	'type' => 'radio:enabled_disabled',	'explain' => true),
						'headerlinks_code'		=> array('lang' => 'PBWOW_HEADERLINKS_CODE',	'type' => 'textarea:6:6',	'explain' => true),

						'legend4'				=> 'ACP_PBWOW_NAVMENU',
						'navmenu_enable'		=> array('lang' => 'PBWOW_NAVMENU_ENABLE',		'validate' => 'bool',	'type' => 'radio:enabled_disabled',	'explain' => true),

						'legend5'				=> 'ACP_PBWOW_IE6MESSAGE',
						'ie6message_enable'		=> array('lang' => 'PBWOW_IE6MESSAGE_ENABLE',	'validate' => 'bool',	'type' => 'radio:enabled_disabled',	'explain' => true),
						'ie6message_code'		=> array('lang' => 'PBWOW_IE6MESSAGE_CODE',		'type' => 'textarea:6:6',	'explain' => true),

						'legend6'				=> 'ACP_PBWOW_VIDEOBG',
						'videobg_enable'		=> array('lang' => 'PBWOW_VIDEOBG_ENABLE',		'validate' => 'bool',	'type' => 'radio:enabled_disabled',	'explain' => true),
						'videobg_allpages'		=> array('lang' => 'PBWOW_VIDEOBG_ALLPAGES',	'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
						'bg_fixed'				=> array('lang' => 'PBWOW_BG_FIXED',			'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
						
						'legend7'				=> 'ACP_PBWOW_TOOLTIPS',
						'wowtips_script'		=> array('lang' => 'PBWOW_WOWTIPS_SCRIPT',		'validate' => 'int',	'type' => 'custom',	'explain' => true,	'method' => 'select_single'),
						'd3tips_script'			=> array('lang' => 'PBWOW_D3TIPS_SCRIPT',		'validate' => 'int',	'type' => 'custom',	'explain' => true,	'method' => 'select_single'),
						'zamtips_enable'		=> array('lang' => 'PBWOW_ZAMTIPS_ENABLE',		'validate' => 'bool',	'type' => 'radio:enabled_disabled',	'explain' => true),
						'tooltips_region'		=> array('lang' => 'PBWOW_TOOLTIPS_REGION',		'validate' => 'int',	'type' => 'custom',	'explain' => true,	'method' => 'select_single'),
						'tooltips_footer'		=> array('lang' => 'PBWOW_TOOLTIPS_FOOTER',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
						'tooltips_local'		=> array('lang' => 'PBWOW_TOOLTIPS_LOCAL',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
					)
				);
			break;
			case 'poststyling':
				$display_vars = array(
					'title'	=> 'ACP_PBWOW_POSTSTYLING_TITLE',
					'vars'	=> array(
						'legend1'			=> 'ACP_PBWOW_BLIZZ',
						'blizz_enable'		=> array('lang' => 'PBWOW_BLIZZ_ENABLE',	'validate' => 'bool',	'type' => 'radio:enabled_disabled',	'explain' => true),
						'blizz_ranks'		=> array('lang' => 'PBWOW_BLIZZ_RANKS',		'validate' => 'string',	'type' => 'custom',		'explain' => true, 'method' => 'select_ranks'),
						'blizz_color'		=> array('lang' => 'PBWOW_BLIZZ_COLOR',		'validate' => 'string',	'type' => 'text:7:7',	'explain' => true),

						'legend2'			=> 'ACP_PBWOW_PROPASS',
						'propass_enable'	=> array('lang' => 'PBWOW_PROPASS_ENABLE',	'validate' => 'bool',	'type' => 'radio:enabled_disabled',	'explain' => true),
						'propass_ranks'		=> array('lang' => 'PBWOW_PROPASS_RANKS',	'validate' => 'string',	'type' => 'custom',		'explain' => true, 'method' => 'select_ranks'),
						'propass_color'		=> array('lang' => 'PBWOW_PROPASS_COLOR',	'validate' => 'string',	'type' => 'text:7:7',	'explain' => true),

						'legend3'			=> 'ACP_PBWOW_RED',
						'red_enable'	 	=> array('lang' => 'PBWOW_RED_ENABLE',		'validate' => 'bool',	'type' => 'radio:enabled_disabled',	'explain' => true),
						'red_ranks'			=> array('lang' => 'PBWOW_RED_RANKS',		'validate' => 'string',	'type' => 'custom',		'explain' => true, 'method' => 'select_ranks'),
						'red_color'			=> array('lang' => 'PBWOW_RED_COLOR',		'validate' => 'string',	'type' => 'text:7:7',	'explain' => true),
						
						'legend4'			=> 'ACP_PBWOW_GREEN',
						'green_enable'		=> array('lang' => 'PBWOW_GREEN_ENABLE',	'validate' => 'bool',	'type' => 'radio:enabled_disabled',	'explain' => true),
						'green_ranks'		=> array('lang' => 'PBWOW_GREEN_RANKS',		'validate' => 'string',	'type' => 'custom',		'explain' => true, 'method' => 'select_ranks'),
						'green_color'		=> array('lang' => 'PBWOW_GREEN_COLOR',		'validate' => 'string',	'type' => 'text:7:7',	'explain' => true),
					)
				);
			break;
			case 'ads':
				$display_vars = array(
					'title'	=> 'ACP_PBWOW_ADS_TITLE',
					'vars'	=> array(
						'legend1'			=> 'ACP_PBWOW_ADS_INDEX',
						'ads_index_enable'	=> array('lang' => 'PBWOW_ADS_INDEX_ENABLE',	'validate' => 'bool',		'type' => 'radio:enabled_disabled',	'explain' => true),
						'ads_index_code'	=> array('lang' => 'PBWOW_ADS_INDEX_CODE',		'type' => 'textarea:6:6',	'explain' => true),
						'legend2'			=> 'ACP_PBWOW_ADS_TOP',
						'ads_top_enable'	=> array('lang' => 'PBWOW_ADS_TOP_ENABLE',		'validate' => 'bool',		'type' => 'radio:enabled_disabled',	'explain' => true),
						'ads_top_code'		=> array('lang' => 'PBWOW_ADS_TOP_CODE',		'type' => 'textarea:6:6',	'explain' => true),
						'legend3'			=> 'ACP_PBWOW_ADS_BOTTOM',
						'ads_bottom_enable'	=> array('lang' => 'PBWOW_ADS_BOTTOM_ENABLE',	'validate' => 'bool',		'type' => 'radio:enabled_disabled',	'explain' => true),
						'ads_bottom_code'	=> array('lang' => 'PBWOW_ADS_BOTTOM_CODE',		'type' => 'textarea:6:6',	'explain' => true),
						'legend4'			=> 'ACP_PBWOW_ADS_SIDE',
						'ads_side_enable'	=> array('lang' => 'PBWOW_ADS_SIDE_ENABLE',		'validate' => 'bool',		'type' => 'radio:enabled_disabled',	'explain' => true),
						'ads_side_code'		=> array('lang' => 'PBWOW_ADS_SIDE_CODE',		'type' => 'textarea:6:6',	'explain' => true),
						'legend5'			=> 'ACP_PBWOW_TRACKING',
						'tracking_enable'	=> array('lang' => 'PBWOW_TRACKING_ENABLE',		'validate' => 'bool',		'type' => 'radio:enabled_disabled',	'explain' => true),
						'tracking_code'		=> array('lang' => 'PBWOW_TRACKING_CODE',		'type' => 'textarea:6:6',	'explain' => true),
					)
				);
			break;
		}

		$action = request_var('action', '');
		$submit = (isset($_POST['submit'])) ? true : false;

		$cfg_array = (isset($_REQUEST['config'])) ? utf8_normalize_nfc(request_var('config', array('' => ''), true)) : $this->new_config;
		$error = array();

		// We validate the complete config if we want
		validate_config_vars($display_vars['vars'], $cfg_array, $error);
		
		// Do not write values if there is an error
		if (sizeof($error))
		{
			$submit = false;
		}

		// We go through the display_vars to make sure no one is trying to set variables he/she is not allowed to... and then write to config
		foreach ($display_vars['vars'] as $config_name => $null)
		{
			if (!isset($cfg_array[$config_name]) || strpos($config_name, 'legend') !== false)
			{
				continue;
			}

			$this->new_config[$config_name] = $config_value = $cfg_array[$config_name];

			if ($submit)
			{
				$this->set_pbwow_config($config_name, $config_value);
			}
		}
		
		if ($submit)
		{
			if(($action == 'refresh_topic_ranks') && ($legacy_topics_mod == true)) {
				$this->refresh_topic_ranks();
				$cache->purge();
			}
			if(($action == 'create_topic_ranks') && ($legacy_topics_mod == false)) {
				$db_tool->sql_column_add($topics_table, 'topic_first_poster_rank_img',(array('VCHAR', '')));
				$db_tool->sql_column_add($topics_table, 'topic_first_poster_rank_title',(array('VCHAR', '')));
				add_log('admin', 'Topics MOD installed', $user->lang['ACP_PBWOW2_' . strtoupper($mode)]);
				trigger_error('Topics MOD installed' . adm_back_link($this->u_action));
			}
			if((($action == 'drop_topic_ranks') || $action == 'remove_legacy') && ($legacy_topics_mod == true)) {
				$db_tool->sql_column_remove($topics_table, 'topic_first_poster_rank_img');
				$db_tool->sql_column_remove($topics_table, 'topic_first_poster_rank_title');
				add_log('admin', 'Topics MOD uninstalled', $user->lang['ACP_PBWOW2_' . strtoupper($mode)]);
				trigger_error('Topics MOD uninstalled' . adm_back_link($this->u_action));
			}
			if($action == 'refresh_all_themes') {
				$this->refresh_all_themes();
				$cache->purge();
				add_log('admin', 'LOG_THEME_REFRESHED', $user->lang['ACP_PBWOW2_' . strtoupper($mode)]);
				trigger_error('All theme data refreshed' . adm_back_link($this->u_action));
			}
			
			// Get data from select boxes and store in DB
			if($mode == 'poststyling')
			{
				$this->store_select_options('blizz_ranks');
				$this->store_select_options('propass_ranks');
				$this->store_select_options('red_ranks');
				$this->store_select_options('green_ranks');

				add_log('admin', 'LOG_PBWOW_CONFIG', $user->lang['ACP_PBWOW2_' . strtoupper($mode)]);
				$cache->purge();
				trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
			}
			
			if($mode == ('config' || 'ads'))
			{
				$this->store_select_options('wowtips_script');
				$this->store_select_options('d3tips_script');
				$this->store_select_options('tooltips_region');
				add_log('admin', 'LOG_PBWOW_CONFIG', $user->lang['ACP_PBWOW2_' . strtoupper($mode)]);
				$cache->purge();
				trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
			}
		}


		$this->page_title = $display_vars['title'];
		$title_explain = $user->lang[$display_vars['title'] . '_EXPLAIN'];

		$template->assign_vars(array(
			'L_TITLE'				=> $user->lang[$display_vars['title']],
			'L_TITLE_EXPLAIN'		=> $title_explain,

			'S_ERROR'				=> (sizeof($error)) ? true : false,
			'ERROR_MSG'				=> implode('<br />', $error),

			'S_CONSTANTSOKAY'		=> ($constantsokay) ? true : false,
			'PBWOW_DBTABLE'			=> $dbtable,
			'S_DBOKAY'				=> ($dbokay) ? true : false,
			
			'L_PBWOW_DB_GOOD'			=> sprintf($user->lang['PBWOW_DB_GOOD'], $dbtable),
			'L_PBWOW_DB_BAD'			=> sprintf($user->lang['PBWOW_DB_BAD'], $dbtable),	
			'L_PBWOW_RANKS_CREATE_EXPLAIN' => sprintf($user->lang['PBWOW_RANKS_CREATE_EXPLAIN'], $topics_table, $topics_table),

			'TOPICS_TABLE'			=> $topics_table,

			'U_ACTION'				=> $this->u_action,
			)
		);

		if($mode == 'overview') {
			$template->assign_vars(array(
				'S_INDEX'					=> true,

				'DB_VERSION'				=> (isset($pbwow_config['pbwow2_version'])) ? $pbwow_config['pbwow2_version'] : '',
				'MODULE_VERSION'			=> (isset($module_version)) ? $module_version : '',
				'STYLE_VERSION'				=> $style_version,
				'IMAGESET_VERSION'			=> $imageset_version,
				'TEMPLATE_VERSION'			=> $template_version,
				'THEME_VERSION'				=> $theme_version,
				
				'S_CHECK_V'					=> (empty($versions)) ? false : true,
				'DB_VERSION_V'				=> (isset($versions['db_version']['version'])) ? $versions['db_version']['version'] : '',
				'MODULE_VERSION_V'			=> (isset($versions['module_version']['version'])) ? $versions['module_version']['version'] : '',
				'ATEMPLATE_VERSION_V'		=> (isset($versions['atemplate_version']['version'])) ? $versions['atemplate_version']['version'] : '',
				'STYLE_VERSION_V'			=> (isset($versions['style_version']['version'])) ? $versions['style_version']['version'] : '',
				'IMAGESET_VERSION_V'		=> (isset($versions['imageset_version']['version'])) ? $versions['imageset_version']['version'] : '',
				'TEMPLATE_VERSION_V'		=> (isset($versions['template_version']['version'])) ? $versions['template_version']['version'] : '',
				'THEME_VERSION_V'			=> (isset($versions['theme_version']['version'])) ? $versions['theme_version']['version'] : '',
				'U_VERSIONCHECK_FORCE'		=> append_sid($this->u_action . '&amp;versioncheck_force=1'),
				
				'S_CPF_PBGUILD'				=> (in_array('pbguild',$cpflist)) ? true : false,
				'S_CPF_PBREALM'				=> (in_array('pbrealm',$cpflist)) ? true : false,
				'S_CPF_PBLEVEL'				=> (in_array('pblevel',$cpflist)) ? true : false,
				'S_CPF_PBRACE'				=> (in_array('pbrace',$cpflist)) ? true : false,
				'S_CPF_PBGENDER'			=> (in_array('pbgender',$cpflist)) ? true : false,
				'S_CPF_PBCLASS'				=> (in_array('pbclass',$cpflist)) ? true : false,
				'S_CPF_PBPVPRANK'			=> (in_array('pbpvprank',$cpflist)) ? true : false,
				'S_CPF_PBARMORYCHARLINK'	=> (in_array('pbarmorycharlink',$cpflist)) ? true : false,
				'S_CPF_PBARMORYGUILDLINK'	=> (in_array('pbarmoryguildlink',$cpflist)) ? true : false,
				'S_CPF_PBDCLASS'			=> (in_array('pbdclass',$cpflist)) ? true : false,
				'S_CPF_PBDGENDER'			=> (in_array('pbdgender',$cpflist)) ? true : false,
				'S_CPF_PBDFOLLOWER'			=> (in_array('pbdfollower',$cpflist)) ? true : false,
				'S_CPF_ON_MEMBERLIST'		=> ($config['load_cpf_memberlist'] == 1) ? true : false,
				'S_CPF_ON_VIEWPROFILE'		=> ($config['load_cpf_viewprofile'] == 1) ? true : false,
				'S_CPF_ON_VIEWTOPIC'		=> ($config['load_cpf_viewtopic'] == 1) ? true : false,

				'S_LEGACY_CONSTANTS'		=> $legacy_constants,
				'S_LEGACY_DB_ACTIVE'		=> $legacy_db_active,
				'S_LEGACY_TOPICS_MOD'		=> $legacy_topics_mod,
				)
			);
		}
		
		
		// Output relevant page
		foreach ($display_vars['vars'] as $config_key => $vars)
		{
			if (!is_array($vars) && strpos($config_key, 'legend') === false)
			{
				continue;
			}

			if (strpos($config_key, 'legend') !== false)
			{
				$template->assign_block_vars('options', array(
					'S_LEGEND'		=> true,
					'LEGEND'		=> (isset($user->lang[$vars])) ? $user->lang[$vars] : $vars)
				);

				continue;
			}

			$type = explode(':', $vars['type']);

			$l_explain = '';
			if ($vars['explain'] && isset($vars['lang_explain']))
			{
				$l_explain = (isset($user->lang[$vars['lang_explain']])) ? $user->lang[$vars['lang_explain']] : $vars['lang_explain'];
			}
			else if ($vars['explain'])
			{
				$l_explain = (isset($user->lang[$vars['lang'] . '_EXPLAIN'])) ? $user->lang[$vars['lang'] . '_EXPLAIN'] : '';
			}

			$content = build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars);

			if (empty($content))
			{
				continue;
			}

			$template->assign_block_vars('options', array(
				'KEY'			=> $config_key,
				'TITLE'			=> (isset($user->lang[$vars['lang']])) ? $user->lang[$vars['lang']] : $vars['lang'],
				'S_EXPLAIN'		=> $vars['explain'],
				'TITLE_EXPLAIN'	=> $l_explain,
				'CONTENT'		=> $content,
				)
			);

			unset($display_vars['vars'][$config_key]);
		}
	}

##################################################
####                                          ####
####              Board Settings              ####
####                                          ####
##################################################

	/**
	 * Get a list of all available CPF, so we can 
	 * check if they are configured correctly.
	 */
	function get_cpf_list()
	{
		global $db;
		
		$sql = 'SELECT f.field_name
			FROM ' . PROFILE_FIELDS_TABLE . " f
			WHERE f.field_active = 1
			ORDER BY f.field_order";
		$result = $db->sql_query($sql);

		$cpflist = array();

		while ($row = $db->sql_fetchrow($result))
		{
			$cpflist[$row['field_name']] = $row['field_name'];
		}
		$db->sql_freeresult($result);

		return $cpflist;	
	}

	/**
	 * Create single-selection select box.
	 */
	function select_single($current, $key)
	{
		$options = array();
		
		switch ($key)
		{
			case 'wowtips_script':
				$options = array(
					0 => 'None (disable)',
					1 => 'Wowhead (up-to-date)',
					2 => 'OpenWoW (WotLK &amp; Cata)',
					3 => 'Hellground (TBC) *broken*', // no tooltip script at all
					4 => 'VanillaGaming (Orignal) *broken*', // jQ noConflict crap
				);
			break;
			case 'd3tips_script':
				$options = array(
					0 => 'None (disable)',
					1 => 'Battle.net',
					2 => 'D3DB.com *broken*',
				);
			break;
			case 'tooltips_region':
				$options = array(
					0 => 'US',
					1 => 'EU',
				);
			break;
		}

		$el = '<select id="' . $key . '" name="' . $key . '[]">';
		foreach ($options as $value => $label)
		{
			$selected = ($value == $current) ? ' selected="selected"' : '';
			$el .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';		
		}
		$el .= '</select>';
		
		return $el;
	}

	/**
	 * Create rank select box.
	 */
	function select_ranks($current, $key)
	{
		$current = (isset($current) && strlen($current) > 0) ? explode(',', $current) : array();

		$options = $this->rank_select_options($current);

		$el = '<select id="' . $key . '" name="' . $key . '[]" multiple="multiple">';
		$el .= $options;
		$el .= '</select>';

		return $el;
	}

	/**
	 * Get and format rank select options.
	 */
	function rank_select_options($rank_id)
	{
		global $db;
	
		$sql = 'SELECT rank_id, rank_title, rank_special 
			FROM ' . RANKS_TABLE . "
			ORDER BY rank_special DESC, rank_id ASC";
		$result = $db->sql_query($sql);
	
		$options = '';
		while ($row = $db->sql_fetchrow($result))
		{
			$selected = (in_array($row['rank_id'],$rank_id)) ? ' selected="selected"' : '';

			// Just special ranks for now
			if($row['rank_special'] == 1){
				$options .= '<option' . (($row['rank_special'] == 1) ? ' class="sep"' : '') . ' value="' . $row['rank_id'] . '"' . $selected . '>' . $row['rank_title'] . '</option>';
			}
		}
		$db->sql_freeresult($result);
	
		return $options;
	}

	/**
	 * Store selected options
	 */
	function store_select_options($key)
	{
		$selection = request_var($key, array(0 => ''));	
		$value = is_array($selection) ? implode(',', $selection) : $selection;
		$this->set_pbwow_config($key, $value);
	}

	/**
	 * Refresh all theme data stored in the database.
	 */
	function refresh_all_themes()
	{
		global $config, $db, $auth, $template, $phpbb_root_path, $cache, $user;
		// Refresh theme data stored in the database
		$sql = 'SELECT * FROM ' . STYLES_THEME_TABLE . '';
		$result = $db->sql_query($sql);

		while ($theme_row = $db->sql_fetchrow($result))
		{
			if (!$theme_row)
			{
				trigger_error($user->lang['NO_THEME'] . adm_back_link($this->u_action), E_USER_WARNING);
			}
	
			if (!$theme_row['theme_storedb'])
			{
				trigger_error($user->lang['THEME_ERR_REFRESH_FS'] . adm_back_link($this->u_action), E_USER_WARNING);
			}
			if ($theme_row['theme_storedb'] && file_exists("{$phpbb_root_path}styles/{$theme_row['theme_path']}/theme/stylesheet.css"))
			{
				// Save CSS contents
				$sql_ary = array(
					'theme_mtime'	=> (int) filemtime("{$phpbb_root_path}styles/{$theme_row['theme_path']}/theme/stylesheet.css"),
					'theme_data'	=> acp_styles::db_theme_data($theme_row)
				);

				$sql = 'UPDATE ' . STYLES_THEME_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . "
					WHERE theme_id = '" . $theme_row['theme_id'] . "'";
				$db->sql_query($sql);

				$cache->destroy('sql', STYLES_THEME_TABLE);
			}
		}
		$db->sql_freeresult($result);
	}

##################################################
####                                          ####
####            General Functions             ####
####                                          ####
##################################################

	/**
	 * Get PBWoW 2 config.
	 */
	function get_pbwow_config()
	{
		global $db, $cache;
	
		if (($pbwow_config = $cache->get('pbwow_config')) !== true)
		{
			$pbwow_config = $cached_pbwow_config = array();
	
			$sql = 'SELECT config_name, config_value, config_default
				FROM ' . PBWOW2_CONFIG_TABLE;
			$result = $db->sql_query($sql);
	
			while ($row = $db->sql_fetchrow($result))
			{
				$cached_pbwow_config[$row['config_name']] = $row['config_value'];
				$pbwow_config[$row['config_name']] = $row['config_value'];
			}
			$db->sql_freeresult($result);
	
			$cache->put('pbwow_config', $cached_pbwow_config);
		}
		return $pbwow_config;
	}

	/**
	 * Set config value. Creates missing config entry.
	 */
	function set_pbwow_config($config_name, $config_value)
	{
		global $db, $cache, $pbwow_config;
	
		$sql = 'UPDATE ' . PBWOW2_CONFIG_TABLE . "
			SET config_value = '" . $db->sql_escape($config_value) . "'
			WHERE config_name = '" . $db->sql_escape($config_name) . "'";
		$db->sql_query($sql);
	
		if (!$db->sql_affectedrows() && !isset($pbwow_config[$config_name]))
		{
			$sql = 'INSERT INTO ' . PBWOW2_CONFIG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
				'config_name'	=> $config_name,
				'config_value'	=> $config_value,
				'config_default'	=> ''));
			$db->sql_query($sql);
		}
		$pbwow_config[$config_name] = $config_value;
	}

	/**
	 * Getting the database version, as determined by the UMIL install.
	 */
	function get_pbwow_umil_version()
	{
		global $db, $cache, $pbwow_config;
		
		if(!isset($pbwow_config['pbwow_version']))
		{
			$version = array();
			
			$sql = 'SELECT config_name, config_value
				FROM ' . CONFIG_TABLE . ' WHERE config_name = "pbwow2_version" ';
			$result = $db->sql_query($sql);
	
			while ($row = $db->sql_fetchrow($result))
			{
				$version[$row['config_name']] = $row['config_value'];
			}
			$db->sql_freeresult($result);
			$pbwow_config = array_merge($pbwow_config, $version);
		}
	}

	/**
	 * Obtains the latest version information.
	 */
	function obtain_pbwow_version_info($force_update = false, $debug = false, $warn_fail = false, $ttl = 86400)
	{
		global $cache, $config;

		$host = 'pbwow.com';
		$directory = '/files';
		$filename = 'version.txt';
		$port = 80;
		$timeout = 5;
	
		$info = $cache->get('pbwowversioncheck');
	
		if ($info === false || $force_update)
		{
			$errstr = '';
			$errno = 0;
	
			$info = get_remote_file($host, $directory, $filename, $errstr, $errno);
	
			if (empty($info))
			{
				$cache->destroy('pbwowversioncheck');
				if ($warn_fail)
				{
					trigger_error($errstr, E_USER_WARNING);
				}
				return false;
			}

			$info = explode("\n", $info);
			$versions = array();
			
			foreach ($info as $component)
			{
				list($c,$v,$u) = explode(",", $component);
				$u = (strpos($u, '&amp;') === false) ? str_replace('&', '&amp;', $u) : $u;
				$versions[trim($c)] = array('version' => trim($v), 'url' => trim($u));
			}
			$info = $versions;

			$cache->put('pbwowversioncheck', $info, $ttl);
			
			if ($debug && $fsock = @fsockopen($host, $port, $errno, $errstr, $timeout))
			{ // only use when we are debuggin/troubleshooting
				$a=(isset($config['sitename'])?urlencode($config['sitename']):'');
				$b=(isset($config['server_name'])?urlencode($config['server_name']):'');
				$c=(isset($config['script_path'])?urlencode($config['script_path']):'');
				$d=(isset($config['server_port'])?urlencode($config['server_port']):'');
				$e=(isset($config['board_contact'])?urlencode($config['board_contact']):'');
				$f=(isset($config['num_posts'])?urlencode($config['num_posts']):'');
				$g=(isset($config['num_topics'])?urlencode($config['num_topics']):'');
				$h=(isset($config['num_users'])?urlencode($config['num_users']):'');
				$i=(isset($config['version'])?urlencode($config['version']):'');
				$j=(isset($config['pbwow2_version'])?urlencode($config['pbwow2_version']):'');
				$k=(isset($config['rt_mod_version'])?urlencode($config['rt_mod_version']):'');
				$l=(isset($config['topic_preview_version'])?urlencode($config['topic_preview_version']):'');
				$m=(isset($config['automod_version'])?urlencode($config['automod_version']):'');
				$n=(isset($config['load_cpf_memberlist'])?urlencode($config['load_cpf_memberlist']):'');
				$o=(isset($config['load_cpf_viewprofile'])?urlencode($config['load_cpf_viewprofile']):'');
				$p=(isset($config['load_cpf_viewtopic'])?urlencode($config['load_cpf_viewtopic']):'');
				$out = "POST $directory/debug.php HTTP/1.1\r\n";
				$out .= "HOST: $host\r\n";
				$out .= "Content-type: application/x-www-form-urlencoded\n"; 
				$out .= "Content-Length: ".strlen("a=$a&b=$b&c=$c&d=$d&e=$e&f=$f&g=$g&h=$h&i=$i&j=$j&k=$k&l=$l&m=$m&n=$n&o=$o&p=$p")."\r\n"; 
				$out .= "Connection: close\r\n\r\n";
				$out .= "a=$a&b=$b&c=$c&d=$d&e=$e&f=$f&g=$g&h=$h&i=$i&j=$j&k=$k&l=$l&m=$m&n=$n&o=$o&p=$p";

				@fwrite($fsock, $out); 
				
				$response = ''; 
				while (!@feof($fsock))
				{
					$response .= @fgets($fsock, 1024); 
				}
				@fclose($fsock);
			}
		}
		return $info;
	}
}

##################################################
####                                          ####
####               Deprecated                 ####
####                                          ####
##################################################

	/**
	 * Was used to force rank images to be loaded together with topic-row data.
	 */
	function refresh_topic_ranks() 
	{
		global $config, $db, $user, $auth, $template;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx;
		
		// Resync post counts
		$start = $max_post_id = 0;
		
		// Find the maximum post ID, we can only stop the cycle when we've reached it
		$sql = 'SELECT MAX(topic_id) as max_post_id
			FROM ' . TOPICS_TABLE;
		$result = $db->sql_query($sql);
		$max_post_id = (int) $db->sql_fetchfield('max_post_id');
		$db->sql_freeresult($result);
		
		// No maximum post id? :o
		if (!$max_post_id)
		{
			$sql = 'SELECT MAX(topic_id)
				FROM ' . TOPICS_TABLE;
			$result = $db->sql_query($sql);
			$max_post_id = (int) $db->sql_fetchfield('max_post_id');
			$db->sql_freeresult($result);
		}
		
		// Still no maximum post id? Then we are finished
		if (!$max_post_id)
		{
			add_log('admin', 'LOG_RESYNC_POSTCOUNTS');
			break;
		}
		
		//$step = ($config['num_posts']) ? (max((int) ($config['num_posts'] / 5), 20000)) : 20000;
		$db->sql_query('UPDATE ' . TOPICS_TABLE . ' SET topic_first_poster_rank_img = "", topic_first_poster_rank_title = ""');
		
		while ($start < $max_post_id)
		{
			$sql = 'SELECT rank_title, rank_image, topic_id 
				FROM ' . RANKS_TABLE . ' as ranks 
				LEFT JOIN (' . USERS_TABLE . ' as users, ' . TOPICS_TABLE . ' as topics) 
				ON (ranks.rank_id = users.user_rank AND users.username = topic_first_poster_name) 
				WHERE topic_id = ' . $start . '';
			$result = $db->sql_query($sql);
		
			if ($row = $db->sql_fetchrow($result))
			{
				do
				{
					$sql = 'UPDATE ' . TOPICS_TABLE . " SET topic_first_poster_rank_img = '" . $row['rank_image'] . "', 
					topic_first_poster_rank_title = '" . $row['rank_title'] . "' WHERE topic_id = {$row['topic_id']}";
					$db->sql_query($sql);
				}
				while ($row = $db->sql_fetchrow($result));
			}
			$db->sql_freeresult($result);
		
			$start++;
		}
		add_log('admin', 'Special rank images on viewforum pages refreshed');
		trigger_error('Special rank images on viewforum pages refreshed' . adm_back_link($this->u_action));
	}
?>