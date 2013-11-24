<?php

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/* Here we hook into the phpBB template chain in order to insert some extra values, which we can use in templates. */
function pbwow_global_style_append(&$hook, $handle, $include_once = true)
{
	//global $template, $cache, $user;
	global $template, $user, $phpbb_root_path;
	
	$pbwow_config = get_pbwow_config();
	if(isset($pbwow_config) && is_array($pbwow_config))
	{
		extract($pbwow_config);
	} else {
		return;
	}
	
	$board_url = generate_board_url() . '/';
	$web_path = (defined('PHPBB_USE_BOARD_URL_PATH') && PHPBB_USE_BOARD_URL_PATH) ? $board_url : $phpbb_root_path;
	$jspath = (isset($user->theme['template_inherit_path']) && $user->theme['template_inherit_path']) ? "{$web_path}styles/" . rawurlencode($user->theme['template_inherit_path']) . '/template/js/' : "{$web_path}styles/" . rawurlencode($user->theme['template_path']) . '/template/js/';

	if(isset($wowtips_script))
	{
		$src = $append = '';
		switch($wowtips_script)
		{
			case 1:
				$src = ($tooltips_local) ? $jspath . 'db.wowhead.js' : 'http://static.wowhead.com/widgets/power.js';
				$append = '<script>var wowhead_tooltips = { "colorlinks": true, "iconizelinks": true, "renamelinks": true }</script>';
			break;
			case 2:
				$src = ($tooltips_local) ? $jspath . 'db.openwow.js' : 'http://cdn.openwow.com/api/tooltip.js';
			break;
			case 3:
				$src = '';
			break;
			case 4:
				$src = ($tooltips_local) ? $jspath . 'db.vanilla.js' : 'http://db.vanillagaming.org/templates/wowhead/js/power.js';
			break;
		}
		$wowtips_script = (!empty($src)) ? '<script type="text/javascript" src="' . $src . '"></script>' : '';
		$wowtips_script .= (!empty($append)) ? $append : '';
	}

	if(isset($d3tips_script))
	{
		$src = '';
		$region = (isset($tooltips_region) && $tooltips_region > 0) ? 'eu' : 'us';
		switch($d3tips_script)
		{
			case 1:
				$src = ($tooltips_local) ? $jspath . 'db.battlenet.js' : 'http://'.$region.'.battle.net/d3/static/js/tooltips.js';
			break;
			case 2:
				$src = ($tooltips_local) ? $jspath . 'db.d3db.js' : 'http://d3db.com/static/js/external.js';
			break;
		}
		$d3tips_script = (!empty($src)) ? '<script type="text/javascript" src="' . $src . '"></script>' : '';
	}	

	$zamtips_script = '';
	if(isset($zamtips_enable) && ($zamtips_enable))
	{
		$src = ($tooltips_local) ? $jspath . 'db.zam.js' : 'http://zam.zamimg.com/j/tooltips.js';
		$zamtips_script = (!empty($src)) ? '<script type="text/javascript" src="' . $src . '"></script>' : '';
	}
	
	if(is_array($template->_tpldata['.']['0']))
	{
		$values = $template->_tpldata['.']['0'];

		if(isset($values['CREDIT_LINE']))
		{
			$values['CREDIT_LINE'] = $values['CREDIT_LINE'] . '<br />Using <a href="http://pbwow.com/" target="_blank">PBWoW 2</a> style. All trademarks referenced herein are the properties of their respective owners.';
		}
		if(isset($values['SCRIPT_NAME']) && ($values['SCRIPT_NAME'] == 'index') && !isset($values['S_INDEX']))
		{
			$values += array('S_INDEX_PAGE' => true);
		}
		if($topbar_enable && isset($topbar_code))
		{
			$values += array(
				'TOPBAR_CODE' => html_entity_decode($topbar_code)
			);
			if($topbar_fixed)
			{
				$values += array(
					'S_TOPBAR_FIXED' => true,
				);
			}
		}
		if($headerlinks_enable && isset($headerlinks_code))
		{
			$values += array(
				'HEADERLINKS_CODE' => html_entity_decode($headerlinks_code)
			);
		}
		if($navmenu_enable)
		{
			$values += array(
				'S_NAVMENU' => html_entity_decode($headerlinks_code)
			);
		}
		if($ie6message_enable && isset($ie6message_code))
		{
			$values += array(
				'IE6MESSAGE_CODE' => html_entity_decode($ie6message_code)
			);
		}
		if($videobg_enable)
		{
			$values += array(
				'S_VIDEOBG' => true,
			);
			if($videobg_allpages)
			{
				$values += array(
					'S_VIDEOBG_ALL' => true,
				);
			}
		}
		if($bg_fixed)
		{
			$values += array(
				'S_BG_FIXED' => true,
			);
		}
		if(isset($wowtips_script))
		{
			$values += array(
				'WOWTIPS_SCRIPT' => $wowtips_script,
			);
		}
		if(isset($d3tips_script))
		{
			$values += array(
				'D3TIPS_SCRIPT' => $d3tips_script,
			);
		}
		if(isset($zamtips_script))
		{
			$values += array(
				'ZAMTIPS_SCRIPT' => $zamtips_script,
			);
		}
		if($tooltips_footer)
		{
			$values += array(
				'S_TOOLTIPS_FOOTER' => true,
			);
		}
		if($ads_index_enable && isset($ads_index_code))
		{
			$values += array(
				'ADS_INDEX_CODE' => html_entity_decode($ads_index_code)
			);
		}
		if($ads_top_enable && isset($ads_top_code))
		{
			$values += array(
				'ADS_TOP_CODE' => html_entity_decode($ads_top_code)
			);
		}
		if($ads_bottom_enable && isset($ads_bottom_code))
		{
			$values += array(
				'ADS_BOTTOM_CODE' => html_entity_decode($ads_bottom_code)
			);
		}
		if($ads_side_enable && isset($ads_side_code))
		{
			$values += array(
				'ADS_SIDE_CODE' => html_entity_decode($ads_side_code)
			);
		}
		if($tracking_enable && isset($tracking_code))
		{
			$values += array(
				'TRACKING_CODE' => html_entity_decode($tracking_code)
			);
		}
	}
	$template->_tpldata['.']['0'] = $values;
	
	if(isset($template->_tpldata['recent_topics']) && is_array($template->_tpldata['recent_topics']) && (count($template->_tpldata['recent_topics']) > 0))
	{
		foreach($template->_tpldata['recent_topics'] as &$entry)
		{
			if(!isset($entry['TOPIC_AUTHOR_COLOUR']))
			{
				preg_match('/(#[A-Fa-f0-9]{6}|#[A-Fa-f0-9]{3})/',$entry['TOPIC_AUTHOR_FULL'],$matches);
				$entry += array('TOPIC_AUTHOR_COLOUR' => (isset($matches[0]) ? $matches[0] : ''));
			}
		}
	}
	
	if($navmenu_enable)
		{
		if(isset($template->_tpldata['navlinks']) && isset($template->_tpldata['jumpbox_forums']) && (count($template->_tpldata['jumpbox_forums']) > 1)) {
			$breadcrumb_popup = '';
			$navlinks_data = &$template->_tpldata['navlinks'];
			$tree = build_jumpbox_tree($template->_tpldata['jumpbox_forums']);
			
			$parents = array();
			foreach ($navlinks_data as $crumb)
			{
				$parents[] = $crumb['FORUM_ID'];
			}
			foreach ($navlinks_data as $level => &$crumb)
			{
				$breadcrumb_popup = '<div class="nav-popup"><ul>';
				$breadcrumb_popup .= generate_advanced_breadcrumb($tree, $crumb['FORUM_ID'], $level, $parents);
				$breadcrumb_popup .= '</ul></div>';
				$crumb['POPUP'] = $breadcrumb_popup;
			}
		}
	}
}
$phpbb_hook->register(array('template','display'), 'pbwow_global_style_append', 'last');

/**
* Returns the user's avatar url, without <img> tags and such. This is usefull when we want 
* to display the avatar as a background image in a <div> or other HTML object.
*/
function get_user_avatar_src($avatar, $avatar_type)
{
	global $user, $config, $phpbb_root_path, $phpEx;

	if (empty($avatar) || !$avatar_type)
	{
		return '';
	}

	$avatar_img = '';

	switch ($avatar_type)
	{
		case AVATAR_UPLOAD:
			$avatar_img = $phpbb_root_path . "download/file.$phpEx?avatar=";
		break;

		case AVATAR_GALLERY:
			$avatar_img = $phpbb_root_path . $config['avatar_gallery_path'] . '/';
		break;
	}

	$avatar_img .= $avatar;
	return $avatar_img;
}

/**
* This is an exact copy of the get_user_rank function, as found in functions_display.php
* It has been put here so it can be called from any page, which is needed for some PBWoW
* features. It also greatlly reduces the risk of undefined function errors.
*
* @param int $user_rank the current stored users rank id
* @param int $user_posts the users number of posts
* @param string &$rank_title the rank title will be stored here after execution
* @param string &$rank_img the rank image as full img tag is stored here after execution
* @param string &$rank_img_src the rank image source is stored here after execution
*
* Note: since we do not want to break backwards-compatibility, this function will only 
* properly assign ranks to guests if you call it for them with user_posts == false
*/
function get_user_rank_global($user_rank, $user_posts, &$rank_title, &$rank_img, &$rank_img_src)
{
	global $ranks, $config, $phpbb_root_path;

	if (empty($ranks))
	{
		global $cache;
		$ranks = $cache->obtain_ranks();
	}

	if (!empty($user_rank))
	{
		$rank_title = (isset($ranks['special'][$user_rank]['rank_title'])) ? $ranks['special'][$user_rank]['rank_title'] : '';
		$rank_img = (!empty($ranks['special'][$user_rank]['rank_image'])) ? '<img src="' . $phpbb_root_path . $config['ranks_path'] . '/' . $ranks['special'][$user_rank]['rank_image'] . '" alt="' . $ranks['special'][$user_rank]['rank_title'] . '" title="' . $ranks['special'][$user_rank]['rank_title'] . '" />' : '';
		$rank_img_src = (!empty($ranks['special'][$user_rank]['rank_image'])) ? $phpbb_root_path . $config['ranks_path'] . '/' . $ranks['special'][$user_rank]['rank_image'] : '';
	}
	else if ($user_posts !== false)
	{
		if (!empty($ranks['normal']))
		{
			foreach ($ranks['normal'] as $rank)
			{
				if ($user_posts >= $rank['rank_min'])
				{
					$rank_title = $rank['rank_title'];
					$rank_img = (!empty($rank['rank_image'])) ? '<img src="' . $phpbb_root_path . $config['ranks_path'] . '/' . $rank['rank_image'] . '" alt="' . $rank['rank_title'] . '" title="' . $rank['rank_title'] . '" />' : '';
					$rank_img_src = (!empty($rank['rank_image'])) ? $phpbb_root_path . $config['ranks_path'] . '/' . $rank['rank_image'] : '';
					break;
				}
			}
		}
	}
}

/* Process the user's CPF input, and generate the appropriate avatar url and other game related attributes */
function process_pbwow_cpf($tpl_fields)
{
	global $phpbb_hook;

	if ($phpbb_hook->call_hook(__FUNCTION__, $tpl_fields))
	{
		if ($phpbb_hook->hook_return(__FUNCTION__))
		{
			return $phpbb_hook->hook_return_result(__FUNCTION__);
		}
	}

	$portrait = $path = $faction = '';
	$valid = false; // determines whether a specific race/class combination is valid (for the game)
	$avail = false; // determines whether (old) avatars are available for the race/class combination
	
	if(!empty($tpl_fields['row'])){
		$r = (isset($tpl_fields['row']['PROFILE_PBRACE_VALUEID'])) ? $tpl_fields['row']['PROFILE_PBRACE_VALUEID'] : 0 ; // Get the WoW race ID
		$c = (isset($tpl_fields['row']['PROFILE_PBCLASS_VALUEID'])) ? $tpl_fields['row']['PROFILE_PBCLASS_VALUEID'] : 0 ; // Get the WoW class ID
		$g = (isset($tpl_fields['row']['PROFILE_PBGENDER_VALUEID'])) ? $tpl_fields['row']['PROFILE_PBGENDER_VALUEID'] : 0 ; // Get the WoW gender ID
		$l = (isset($tpl_fields['row']['PROFILE_PBLEVEL_VALUE'])) ? $tpl_fields['row']['PROFILE_PBLEVEL_VALUE'] : 0 ; // Get the WoW level
		$d = (isset($tpl_fields['row']['PROFILE_PBDCLASS_VALUEID'])) ? $tpl_fields['row']['PROFILE_PBDCLASS_VALUEID'] : 0 ; // Get the Diablo class ID

		if($r !== 0) {
			
			/* Remapping options */
			// $R = $r;
			// $r = ($R == 1) ? 4 : $r; // first item in CPF (with "none" = 0), map to race 4 (Night Elf)
			// $r = ($R == 2) ? 9 : $r; // second item in CPF, map to race 9 (Goblin)
			// $r = ($R == 3) ? 12 : $r; // third item in CPF, map to race 12 (Worgen)
			// $r = ($R == 4) ? 2 : $r; // fourth item in CPF, map to race 2 (Orc)
			// etc. etc.
			
			// $C = $c;
			// $c = ($C == 1) ? 1 : $c; // first item in CPF (with "none" = 0), map to class 1 (Warrior)
			// $c = ($C == 2) ? 4 : $c; // second item in CPF, map to class 4 (Rogue)
			// $c = ($C == 3) ? 6 : $c; // third item in CPF, map to class 6 (Death Knight)
			// etc. etc.
			
			/* For reference 
			r = 1 > Human
			r = 2 > Orc
			r = 3 > Dwarf
			r = 4 > Night Elf
			r = 5 > Undead
			r = 6 > Tauren
			r = 7 > Gnome
			r = 8 > Troll
			r = 9 > Goblin
			r = 10 > Blood Elf
			r = 11 > Draenei
			r = 12 > Worgen
			r = 13 > Pandaren
			
			c = 1 > Warrior
			c = 2 > Paladin
			c = 3 > Hunter
			c = 4 > Rogue
			c = 5 > Priest
			c = 6 > Death Knight
			c = 7 > Shaman
			c = 8 > Mage
			c = 9 > Warlock
			c = 10 > Monk
			c = 11 > Druid
			*/
			
			$faction = 3;
			switch($r)
			{
				case 1: // Human
					$valid = (in_array($c, array(1,2,3,4,5,6,8,9,10))) ? true : false;
					$avail = (in_array($c, array(1,2,4,5,6,8,9))) ? true : false;
					$faction = 1;
				break;
				
				case 2: // Orc
					$valid = (in_array($c, array(1,3,4,6,7,8,9,10))) ? true : false;
					$avail = (in_array($c, array(1,3,4,6,7,9))) ? true : false;
					$faction = 2;
				break;
				
				case 3: // Dwarf
					$valid = (in_array($c, array(1,2,3,4,5,6,7,8,9,10))) ? true : false;
					$avail = (in_array($c, array(1,2,3,4,5,6))) ? true : false;
					$faction = 1;
				break;
				
				case 4: // Night Elf
					$valid = (in_array($c, array(1,3,4,5,6,8,10,11))) ? true : false;
					$avail = (in_array($c, array(1,3,4,5,6,11))) ? true : false;
					$faction = 1;
				break;
				
				case 5: // Undead
					$valid = (in_array($c, array(1,3,4,5,6,8,9,10))) ? true : false;
					$avail = (in_array($c, array(1,4,5,6,8,9))) ? true : false;
					$faction = 2;
				break;
				
				case 6: // Tauren
					$valid = (in_array($c, array(1,2,3,5,6,7,10,11))) ? true : false;
					$avail = (in_array($c, array(1,3,6,7,11))) ? true : false;
					$faction = 2;
				break;
				
				case 7: // Gnome
					$valid = (in_array($c, array(1,4,5,6,8,9,10))) ? true : false;
					$avail = (in_array($c, array(1,4,6,8,9))) ? true : false;
					$faction = 1;
				break;
				
				case 8:  // Troll
					$valid = (in_array($c, array(1,3,4,5,6,7,8,9,10,11))) ? true : false;
					$avail = (in_array($c, array(1,3,4,5,6,7,8))) ? true : false;
					$faction = 2;
				break;

				case 9: // Goblin
					$valid = (in_array($c, array(1,3,4,5,6,7,8,9))) ? true : false;
					//$avail = (in_array($c, array())) ? true : false;
					$faction = 2;
				break;
				
				case 10:  // Blood Elf
					$valid = (in_array($c, array(1,2,3,4,5,6,8,9,10))) ? true : false;
					$avail = (in_array($c, array(2,3,4,5,6,8,9))) ? true : false;
					$faction = 2;
				break;
				
				case 11: // Draenei
					$valid = (in_array($c, array(1,2,3,5,6,7,8,10))) ? true : false;
					$avail = (in_array($c, array(1,2,3,5,6,7,8))) ? true : false;
					$faction = 1;
				break;
				
				case 12:  // Worgen
					$valid = (in_array($c, array(1,3,4,5,6,8,9,11))) ? true : false;
					//$avail = (in_array($c, array())) ? true : false;
					$faction = 1;
				break;
				
				case 13: // Pandaren
					$valid = (in_array($c, array(1,3,4,5,7,8,10))) ? true : false;
					//$avail = (in_array($c, array())) ? true : false;
					$faction = 3;
				break;
			}
			
			$g = max(0, $g-1); // 0 = none, 1 = male, 2 = female, but we need a 0/1 map

			if($valid && $avail) {
				if ($l >= 90) {
					$path = 'wow-80'; // Don't have any higher (yet)
				} 
				elseif ($l >= 80) {
						$path = 'wow-80';
				}					
				elseif ($l >= 70) {
						$path = 'wow-70';
				}
				elseif ($l >= 60) {
						$path = 'wow-60';
				}
				else {
					$path = 'wow-default';
				}
				
				$portrait = $path . '/' . $g . '-' . $r . '-' . $c . '.gif';
			} 
			elseif($valid && !$avail) {
				$portrait = 'wow-default-new/' . $r . '-' . $g . '.jpg'; // Missing but valid
			}
			else {
				$portrait = 'wow-default-new/' . $r . '-' . $g . '.jpg';  // Invalid, completely messed up
			}
			
			$tpl_fields['row'] += array(
				'PROFILE_PBAVATAR_VALUE'	=> $portrait,
				'PROFILE_PBFACTION_VALUE'	=> $faction,
				'S_PROFILE_PBAVATAR'		=> true,
				'S_PROFILE_PBFACTION'		=> true
			);
		}
		elseif($d !== 0) {
			
			$portrait = 'beta-avatar.jpg';
			
			$tpl_fields['row'] += array(
				'PROFILE_PBAVATAR_VALUE'	=> $portrait,
				'PROFILE_PBFACTION_VALUE'	=> 3,
				'S_PROFILE_PBAVATAR'		=> true,
				'S_PROFILE_PBFACTION'		=> true
			);
		}
	}

	return $tpl_fields;
}
$phpbb_hook->add_hook('process_pbwow_cpf');

/* Determine if any PBWoW2 special styling should be applied to a user, based on his rank */
function check_rank_special_styling($rank, &$styling, &$color)
{
	$pbwow_config = get_pbwow_config();
	
	$cfg_blizz_ranks = explode(',', $pbwow_config['blizz_ranks']);
	$cfg_blizz_color = $pbwow_config['blizz_color'];
	$cfg_propass_ranks = explode(',', $pbwow_config['propass_ranks']);
	$cfg_propass_color = $pbwow_config['propass_color'];
	$cfg_red_ranks = explode(',', $pbwow_config['red_ranks']);
	$cfg_red_color = $pbwow_config['red_color'];
	$cfg_green_ranks = explode(',', $pbwow_config['green_ranks']);
	$cfg_green_color = $pbwow_config['green_color'];

	$styling = $color = '';

	if(isset($cfg_green_ranks) && strlen($cfg_green_ranks > 0) && ($pbwow_config['green_enable']))
	{
		if(in_array($rank, $cfg_green_ranks)) {
			$styling = 'green';
			if(isset($cfg_green_color)) {
				$color = $cfg_green_color;
			}
		}
	}
	if(isset($cfg_propass_ranks) && strlen($cfg_propass_ranks > 0) && ($pbwow_config['propass_enable']))
	{
		if(in_array($rank, $cfg_propass_ranks)) {
			$styling = 'propass';
			if(isset($cfg_propass_color)) {
				$color = $cfg_propass_color;
			}
		}
	}
	if(isset($cfg_red_ranks) && strlen($cfg_red_ranks > 0) && ($pbwow_config['red_enable']))
	{
		if(in_array($rank, $cfg_red_ranks)) {
			$styling = 'red';
			if(isset($cfg_red_color)) {
				$color = $cfg_red_color;
			}
		}
	}
	if(isset($cfg_blizz_ranks) && strlen($cfg_blizz_ranks > 0) && ($pbwow_config['blizz_enable']))
	{
		if(in_array($rank, $cfg_blizz_ranks)) {
			$styling = 'blizz';
			if(isset($cfg_blizz_color)) { //  && strlen($cfg_blizz_color > 0) not working?!?
				$color = $cfg_blizz_color;
			}
		}
	}
}

/* Generate a forum array tree, based on an unordered array like the jumpbox data */
function build_jumpbox_tree($list) {
	$tree = $parent_memory = array();
	$prev_id = $prev_level = 0;
	
	$prepare = array();

	foreach($list as $item => $vars) {
		$forum_id = $vars['FORUM_ID'];
		$level = (isset($vars['level']) && is_array($vars['level'])) ? count($vars['level']) : 0;

		if ($level == 0) {
			$parent_memory = array(0);
		} elseif ($level > $prev_level) {
			$parent_memory[$level] = $prev_id;
		} elseif ($level < $prev_level) {
			unset($parent_memory[$prev_level]);
			unset($parent_memory[$prev_level + 1]); // clean up
			unset($parent_memory[$prev_level + 2]); // clean up
		}

		$current = (isset($vars['SELECTED']) && !empty($vars['SELECTED']) ? true : false);
		
		$values = array('parent_id' => $parent_memory[$level], 'level' => $level, 'forum_name' => $vars['FORUM_NAME'], 'current' => $current);
		
		switch($level) {
			case 0:
				$tree[$forum_id] = $values;
			break;
			case 1:
				$tree[$parent_memory[$level]]['children'][$forum_id] = $values;
			break;
			case 2:
				$tree[$parent_memory[$level-1]]['children'][$parent_memory[$level]]['children'][$forum_id] = $values;
			break;
			case 3:
				$tree[$parent_memory[$level-2]]['children'][$parent_memory[$level-1]]['children'][$parent_memory[$level]]['children'][$forum_id] = $values;
			break;
		}

		$prev_id = $forum_id;
		$prev_level = $level;
	}

	unset($tree[-1]);
	return $tree;
}

/* Generates menu blocks based on the forum array tree, so use for popup menus */
function generate_advanced_breadcrumb($tree, $crumb_current, $crumb_level = 0, $parents = array()) {
	$link = './viewforum.php?f=';
	$html = $childhtml = '';
	
	foreach ($tree as $id => $vars)
	{
		/*if (($crumb_level > $vars['level']) && $crumb_level !== 0)
		{
			continue;
		}*/
		
		if (isset($vars['children'])) {
			$childhtml = generate_advanced_breadcrumb($vars['children'], $crumb_current, $crumb_level, $parents);
		} else {
			$childhtml = '';
		}

		$parent_id = $vars['parent_id'];
		$level = $vars['level'];

		if (($crumb_level <= $vars['level'] && in_array($parent_id, $parents)) || $crumb_level == 0)
		{
			$class = (!empty($childhtml)) ? 'children' : '';
			$class .= ($vars['current'] == true || $id == $crumb_current) ? ' current' : '';

			$html .= '<li' . ((!empty($class)) ? (' class="' . $class . '">') : ('>'));

			$html .= '<a href="' . $link . $id .'">' . $vars['forum_name'] . '</a>';

			if (!empty($childhtml)) {
				$html .= '<div class="fly-out"><ul>';
				$html .= $childhtml;
				$html .= '</ul></div>';
			}

			$html .= "</li>\n";
		} else {
			$html .= $childhtml;
		}
	}

	return $html;
}

/* Parses and modifies the topic preview output to include PBWoW 2 functionality such as game avatars */
function modify_topic_preview($row, $block, $profile_fields_cache, $tp_avatars = false, $cheat_cache = false)
{
	global $template, $phpbb_root_path, $cp, $config;
	
	if (class_exists('phpbb_topic_preview') && $config['load_cpf_viewtopic'])
	{
		// retroactive modification when we missed blockvar assignment, only called once
		if (!$row && !empty($template->_tpldata['recent_topics']) && !empty($cheat_cache)) {
			foreach ($template->_tpldata['recent_topics'] as &$rtrow)
			{
				$p1 = $cheat_cache[$rtrow['TOPIC_ID']]['tfp'];
				$p2 = $cheat_cache[$rtrow['TOPIC_ID']]['tlp'];
				
				$cp_p1 = (isset($profile_fields_cache[$p1])) ? $cp->generate_profile_fields_template('show', false, $profile_fields_cache[$p1]) : array();
				$cp_p2 = ($p2 == $p1) ? $cp_p2 = $cp_p1 : (isset($profile_fields_cache[$p2])) ? $cp->generate_profile_fields_template('show', false, $profile_fields_cache[$p2]) : array();
				$rtrow += array(
					'TOPIC_PREVIEW_PBAVATAR' => ($tp_avatars && isset($cp_p1['row']) && sizeof($cp_p1['row']) && isset($cp_p1['row']['PROFILE_PBAVATAR_VALUE'])) ? $cp_p1['row']['PROFILE_PBAVATAR_VALUE']: '',
					'TOPIC_PREVIEW_PBAVATAR2' => ($tp_avatars && isset($cp_p2['row']) && sizeof($cp_p2['row']) && isset($cp_p2['row']['PROFILE_PBAVATAR_VALUE'])) ? $cp_p2['row']['PROFILE_PBAVATAR_VALUE']: '',
					'TOPIC_PREVIEW_COLOUR2'	=> (!empty($rtrow['LAST_POST_AUTHOR_COLOUR'])) ? $rtrow['LAST_POST_AUTHOR_COLOUR'] : '',
				);
				if (!empty($rtrow['TOPIC_AUTHOR_FULL'])) {
					preg_match('/(#[A-Fa-f0-9]{6}|#[A-Fa-f0-9]{3})/',$rtrow['TOPIC_AUTHOR_FULL'],$matches);
					$rtrow += array('TOPIC_PREVIEW_COLOUR' => (isset($matches[0]) ? $matches[0] : ''));
				}
				
				//var_dump($rtrow);
			}
		// normal blockvar operations, called for each row
		} else {
			$p1 = $row['topic_poster'];
			$p2 = $row['topic_last_poster_id'];

			$cp_p1 = (isset($profile_fields_cache[$p1])) ? $cp->generate_profile_fields_template('show', false, $profile_fields_cache[$p1]) : array();
			$cp_p2 = ($p2 == $p1) ? $cp_p2 = $cp_p1 : (isset($profile_fields_cache[$p2])) ? $cp->generate_profile_fields_template('show', false, $profile_fields_cache[$p2]) : array();
			
			$template->alter_block_array($block, array(
				'TOPIC_PREVIEW_PBAVATAR' => ($tp_avatars && isset($cp_p1['row']) && sizeof($cp_p1['row']) && isset($cp_p1['row']['PROFILE_PBAVATAR_VALUE'])) ? $cp_p1['row']['PROFILE_PBAVATAR_VALUE']: '',
				'TOPIC_PREVIEW_PBAVATAR2' => ($tp_avatars && isset($cp_p2['row']) && sizeof($cp_p2['row']) && isset($cp_p2['row']['PROFILE_PBAVATAR_VALUE'])) ? $cp_p2['row']['PROFILE_PBAVATAR_VALUE']: '',
				'TOPIC_PREVIEW_COLOUR'	=> (!empty($row['first_user_colour'])) ? '#'.$row['first_user_colour'] : '',
				'TOPIC_PREVIEW_COLOUR2'	=> (!empty($row['last_user_colour'])) ? '#'.$row['last_user_colour'] : '',
			), true, 'change');
		}
	}
}

function get_pbwow_config()
{
	global $db, $cache, $phpbb_root_path, $phpEx;

	$pbwow_config = $cache->get('pbwow_config');

	if ($pbwow_config == false)
	{
		$pbwow_config = $cached_pbwow_config = array();

		if (!class_exists('phpbb_db_tools'))
		{
			include("$phpbb_root_path/includes/db/db_tools.$phpEx");
		}
		$db_tool = new phpbb_db_tools($db);
	
		if($db_tool->sql_table_exists(PBWOW2_CONFIG_TABLE)){

			$sql = 'SELECT config_name, config_value
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
	}
	return $pbwow_config;
}

?>