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

$query_by_pool = '';
$query_by_armor = '';
$query_by_class = '';
$filter = '';

$dkpsys_id = dkppulldown();
$classarray = armor();
$startd = request_var ( 'startdkp', 0 );
$arg='';
if ($query_by_pool)
{
    $arg = '&amp;' . URI_DKPSYS. '=' . $dkpsys_id;
}

if(	$query_by_armor or $query_by_class)
{
	$arg .= '&amp;filter=' . $filter; 
}
else 
{
	$arg .= '&amp;filter=all';
}

$u_listmembers = append_sid ( "{$phpbb_root_path}dkp.$phpEx", 'page=standings' . $arg );

/*
 * list installed games
 */
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
	'lineage2'      => $user->lang['LINEAGE2']	
);
              
$installed_games = array();
foreach($games as $gameid => $gamename)
{
	if ($config['bbdkp_games_' . $gameid] == 1)
	{
		$installed_games[$gameid] = $gamename; 
	} 
}

$show_all = ((isset ( $_GET ['show'] )) && (request_var ( 'show', '' ) == 'all')) ? true : false;

$memberarray = get_standings($dkpsys_id, $installed_games, $startd, $show_all);

// Obtain a list of columns for sorting the array
if (count ($memberarray))
{
	foreach ($memberarray as $key => $member)
	{
		$member_name [$key] = $member ['member_name'];
		$rank_name [$key] = $member ['rank_name'];  
		$member_level [$key] = $member ['member_level']; 
		$member_class [$key] = $member ['member_class']; 
		$armor_type [$key] = $member ['armor_type']; 
		$member_raid_value [$key] = $member ['member_raid_value'];
		
		if($config['bbdkp_timebased'] == 1)
		{
			$member_time_bonus [$key] ['member_time_bonus'] = $member ['member_time_bonus'];
			
		}
		if($config['bbdkp_zerosum'] == 1)
		{
			$member_zerosum_bonus [$key] ['member_zerosum_bonus'] = $member ['member_zerosum_bonus'];
		}
		
		$member_earned [$key] = $member ['member_earned']; //*
		$member_adjustment [$key] = $member ['member_adjustment']; //*
		
		if($config['bbdkp_decay'] == 1)
		{
			$member_raid_decay[$key]['member_raid_decay'] = $member['member_raid_decay']; 
			$member_item_decay[$key]['member_item_decay'] = $member['member_item_decay']; 
		}
		
		if($config['bbdkp_epgp'] == 1)
		{
			$ep[$key]['ep'] = $member['ep']; 
			$gp[$key]['gp'] = $member['gp']; 
			$pr[$key]['pr'] = $member['pr']; 
		}
		
		$member_spent [$key] = $member ['member_spent'];
		$member_current [$key] = $member ['member_current']; 
		$member_lastraid [$key] = $member ['member_lastraid']; 
		$attendanceP1 [$key] = $member ['attendanceP1']; 
	}
	
	
	// do the multi-dimensional sorting
	$sortorder = request_var ( URI_ORDER, 0 );
	switch ($sortorder)
	{
		case - 1 : //name
			array_multisort ( $member_name, SORT_DESC, $memberarray );
			break;
		case 1 : //name
			array_multisort ( $member_name, SORT_ASC, $memberarray );
			break;
		case - 2 : //rank
			array_multisort ( $rank_name, SORT_DESC, $member_name, SORT_DESC, $memberarray );
			break;
		case 2 : //rank
			array_multisort ( $rank_name, SORT_ASC, $member_name, SORT_ASC, $memberarray );
			break;
		case - 3 : //level
			array_multisort ( $member_level, SORT_DESC, $member_name, SORT_DESC, $memberarray );
			break;
		case 3 : //level
			array_multisort ( $member_level, SORT_ASC, $member_name, SORT_ASC, $memberarray );
			break;
		case 4 : //class
			array_multisort ( $member_class, SORT_ASC, $member_level, SORT_ASC, $member_name, SORT_ASC, $memberarray );
			break;
		case - 4 : //class
			array_multisort ( $member_class, SORT_DESC, $member_level, SORT_DESC, $member_name, SORT_DESC, $memberarray );
			break;
		case 5 : //armor
			array_multisort ( $member_name, SORT_ASC, $memberarray );
			break;
		case - 5 : //armor
			array_multisort ( $member_name, SORT_DESC, $memberarray );
			break;
			
		case 6 : //member_raid_value
			array_multisort ( $member_raid_value, SORT_DESC, $member_name, SORT_ASC, $memberarray );
			break;
		case - 6 : //member_raid_value
			array_multisort ( $member_raid_value , SORT_ASC, $member_name, SORT_DESC, $memberarray );
			break;			
			
		case 7 : //bbdkp_dkphour
			array_multisort ( $member_time_bonus, SORT_DESC, $member_name, SORT_ASC, $memberarray );
			break;
		case - 7 : //bbdkp_dkphour
			array_multisort ( $member_time_bonus , SORT_ASC, $member_name, SORT_DESC, $memberarray );
			break;			

		case 8 : //member_zerosum_bonus
			array_multisort ( $member_zerosum_bonus, SORT_DESC, $member_name, SORT_ASC, $memberarray );
			break;
		case - 8 : //member_zerosum_bonus
			array_multisort ( $member_zerosum_bonus , SORT_ASC, $member_name, SORT_DESC, $memberarray );
			break;

		case 9 : //earned
			array_multisort ( $member_earned, SORT_DESC, $member_name, SORT_ASC, $memberarray );
			break;
		case - 9 : //earned
			array_multisort ( $member_earned, SORT_ASC, $member_name, SORT_DESC, $memberarray );
			break;
			
		case 10 : //adjustment
			array_multisort ( $member_adjustment, SORT_DESC, $member_name, SORT_ASC, $memberarray );
			break;
		case - 10 : //adjustment
			array_multisort ( $member_adjustment, SORT_ASC, $member_name, SORT_DESC, $memberarray );
			break;
			
		case 11 : //ep decay
			array_multisort ( $member_raid_decay, SORT_DESC, $member_name, SORT_ASC, $memberarray );
			break;
		case - 11 : //ep decay
			array_multisort ( $member_raid_decay, SORT_ASC, $member_name, SORT_DESC, $memberarray );
			break;			
			
		case 12 : //ep 
			array_multisort ( $ep, SORT_DESC, $member_name, SORT_ASC, $memberarray );
			break;
		case - 12 : //ep 
			array_multisort ( $ep, SORT_ASC, $member_name, SORT_DESC, $memberarray );
			break;
			
		case 13 : //spent
			array_multisort ( $member_spent, SORT_DESC, $member_name, SORT_ASC, $memberarray );
			break;
		case - 13 : //spent
			array_multisort ( $member_spent, SORT_ASC, $member_name, SORT_DESC, $memberarray );
			break;
			
		case 14 : //item_decay 
			array_multisort ( $member_item_decay, SORT_DESC, $member_name, SORT_ASC, $memberarray );
			break;
		case - 14 : //item_decay 
			array_multisort ( $member_item_decay, SORT_ASC, $member_name, SORT_DESC, $memberarray );
			break;
			
		case 15 : // gp
			array_multisort ( $gp, SORT_DESC, $member_name, SORT_ASC, $memberarray );
			break;
		case - 15 : // gp 
			array_multisort ( $gp, SORT_ASC, $member_name, SORT_DESC, $memberarray );
			break;
			
		case 16 : // Pr
			array_multisort ( $pr, SORT_DESC, $member_name, SORT_ASC, $memberarray );
			break;
		case - 16 : // Pr 
			array_multisort ( $pr, SORT_ASC, $member_name, SORT_DESC, $memberarray );
			break;			
			
		case 17 : //current
			array_multisort ( $member_current, SORT_DESC, $member_name, SORT_ASC, $memberarray );
			break;
		case - 17 : //current
			array_multisort ( $member_current, SORT_ASC, $member_name, SORT_DESC, $memberarray );
			break;
			
		case 18 : //lastraid
			array_multisort ( $member_lastraid, SORT_DESC, $member_name, SORT_ASC, $memberarray );
			break;
		case - 18 : //lastraid
			array_multisort ( $member_lastraid, SORT_ASC, $member_name, SORT_DESC, $memberarray );
			break;

		case 19 : //raidattendance P1
			array_multisort ( $attendanceP1, SORT_DESC, $member_name, SORT_ASC, $memberarray );
			break;
		case - 19 : //raidattendance P1
			array_multisort ( $attendanceP1, SORT_ASC, $member_name, SORT_DESC, $memberarray );
			break;

	}
}

// loop sorted member array and dump to template
foreach ( $memberarray as $key => $member )
{
	
	$u_rank_search = append_sid ( "{$phpbb_root_path}dkp.$phpEx" , 'page=standings&amp;rank=' . urlencode ( $member ['rank_name'] ) );
	
	// append inactive switch
	$u_rank_search .= (($config ['bbdkp_hide_inactive'] == 1) && (! $show_all)) ? '&amp;show=' : '&amp;show=all';
	
	// append armor or class filter
	$u_rank_search .= ($filter != $user->lang['ALL']) ? '&amp;filter=' . $filter : '';
	
	$templatearray = array (
		'COLORCODE' 	=> $member ['colorcode'],
		'DKPNAME'		=> $member ['dkpsys_name'],  
		'CLASS_IMAGE' 	=> $member['class_image'],
		'S_CLASS_IMAGE_EXISTS' =>  $member['class_image_exists'],
		'RACE_IMAGE' 	=> $member['race_image'],
		'S_RACE_IMAGE_EXISTS' =>  $member['race_image_exists'],
		'DKPCOLOUR1' 	=> ($member ['member_adjustment'] >= 0) ? 'positive' : 'negative', 
		'DKPCOLOUR2' 	=> ($member ['member_current'] >= 0) ? 'positive' : 'negative', 
		'ID' 			=> $member ['member_id'], 
		'COUNT' 		=> $member ['count'], 
		'NAME' 			=> $member ['rank_prefix'] . (($member ['member_status'] == '0') ? 
			'<em>' . $member ['member_name'] . '</em>' : 
			$member ['member_name']) . $member ['rank_suffix'], 
		'RANK_NAME' => $member ['rank_name'],
		'RANK_HIDE' => $member ['rank_hide'],
		'RANK_SEARCH' => $u_rank_search, 
		'LEVEL' => ($member ['member_level'] > 0) ? $member ['member_level'] : '&nbsp;', 
		'CLASS' => (! empty ( $member ['member_class'] )) ? $member ['member_class'] : '&nbsp;', 
		'ARMOR' => (! empty ( $member ['armor_type'] )) ? $member ['armor_type'] : '&nbsp;', 
		'EARNED' => $member ['member_earned'], 
		'ADJUSTMENT' => $member ['member_adjustment'], 
		'SPENT' => $member ['member_spent'],
		'CURRENT' => $member ['member_current'], 
		'LASTRAID' => (! empty ( $member ['member_lastraid'] )) ? 
			date ( 'd.m.y', $member ['member_lastraid'] ) : 
			'&nbsp;', 
		'RAIDS_P1_DAYS' => $member ['attendanceP1'], 
		'U_VIEW_MEMBER' => append_sid ( "{$phpbb_root_path}dkp.$phpEx",
			'page=viewmember' .  
			'&amp;' . URI_NAMEID . '=' . $member ['member_id'] . 
			'&amp;' . URI_DKPSYS . '=' . $member ['member_dkpid']), 
	);
		
		if($config['bbdkp_timebased'] == 1)
		{
			$templatearray['TIMEBONUS'] = $member ['member_time_bonus'];
		}
		if($config['bbdkp_zerosum'] == 1)
		{
			$templatearray['ZEROSUM'] = $member ['member_zerosum_bonus'];
		}
				
		if($config['bbdkp_decay'] == 1)
		{
			$templatearray['RAIDDECAY'] = $member ['member_raid_decay'];
			$templatearray['ITEMDECAY'] = $member ['member_item_decay'];
		}
		
		if($config['bbdkp_epgp'] == 1)
		{
			$templatearray['EP'] = $member ['ep'];
			$templatearray['GP'] = $member ['gp'];
			$templatearray['PR'] = $member ['pr'];
		}
		
		$template->assign_block_vars ( 'members_row', $templatearray);
		
}

//
leaderboard ( $memberarray, $classarray );

// Added to the end of the sort links
$uri_addon = '';
$uri_addon .= '&amp;filter=' . urlencode ( $filter );
$uri_addon .= (isset ( $_GET ['show'] )) ? '&amp;show=' . request_var ( 'show', '' ) : '';

/* sorting links */
$sortlink = array ();
for($i = 1; $i <= 20; $i ++)
{
	if (isset ( $sortorder ) && $sortorder == $i)
	{
		$j = - $i;
	} 
	else
	{
		$j = $i;
	}
	
	{
		if ($query_by_pool)
		{
			$sortlink [$i] = append_sid ( "{$phpbb_root_path}dkp.$phpEx", 'page=standings&amp;' . URI_ORDER. '=' . $j . $uri_addon . '&amp;' . URI_DKPSYS . '=' . $dkpsys_id );
		} 
		else
		{
			$sortlink [$i] = append_sid ( "{$phpbb_root_path}dkp.$phpEx", 'page=standings&amp;' . URI_ORDER. '=' . $j . $uri_addon . '&amp;' . URI_DKPSYS . '=' . $user->lang['ALL'] );
		}
	}

}
// calculate pagination
$sortorder = request_var ( URI_ORDER, 0 );
$dkppagination = generate_pagination2($u_listmembers . '&amp;o=' . $sortorder , 
$allmember_count , $config ['bbdkp_user_llimit'], $startd, true, 'startdkp'  );

// footcount link
if (($config ['bbdkp_hide_inactive'] == 1) && (! $show_all))
{
	$flink = '<a href="' . append_sid ( "{$phpbb_root_path}dkp.$phpEx", 
		'page=standings' .	
		'&amp;' . URI_ORDER . '=' . $j . '&amp;show=all' . 
		'&amp;' . URI_DKPSYS . '=' . $dkpsys_id ) . '" class="rowfoot">';
	$footcount_text = sprintf ( $user->lang ['LISTMEMBERS_ACTIVE_FOOTCOUNT'], count($memberarray) , $flink );
} 
else
{
	$footcount_text = sprintf ( $user->lang ['LISTMEMBERS_FOOTCOUNT'], count($memberarray) );
}


$template->assign_vars ( array (
	'F_MEMBERS' => $u_listmembers, 
	'F_DKPSYS_NAME' => (isset ( $dkpsysname ) == true) ? $dkpsysname : $user->lang['ALL'], 
	'F_DKPSYS_ID' => (isset ( $dkpsys_id ) == true) ? $dkpsys_id : 0, 
	'O_NAME' => $sortlink [1], 
	'O_RANK' => $sortlink [2], 
	'O_LEVEL' => $sortlink [3], 
	'O_CLASS' => $sortlink [4], 
	'O_ARMOR' => $sortlink [5], 
	'O_MEMBER_RAID_VALUE' => $sortlink [6], 
	'O_EARNED' => $sortlink [9],
	'O_ADJUSTMENT' => $sortlink [10], 
	'O_SPENT' => $sortlink [13],
	'O_CURRENT' => $sortlink [17],
	'O_LASTRAID' => $sortlink [18], 
	'O_RAIDS_P1_DAYS' => $sortlink [19], 
	'O_RAIDS_P2_DAYS' => $sortlink [20], 
	'RAIDS_P1_DAYS' => sprintf ( $user->lang ['RAIDS_X_DAYS'], $config ['bbdkp_list_p1'] ), 
	'S_SHOWZS' 		=> ($config['bbdkp_zerosum'] == '1') ? true : false, 
	'S_SHOWDECAY' 	=> ($config['bbdkp_decay'] == '1') ? true : false,
	'S_SHOWEPGP' 	=> ($config['bbdkp_epgp'] == '1') ? true : false,
 	'S_SHOWTIME' 	=> ($config['bbdkp_timebased'] == '1') ? true : false,
	'S_QUERYBYPOOL' => $query_by_pool, 
	'S_DISPLAY_STANDINGS' => true,
	'DKPPAGINATION' 	=> $dkppagination ,
	'FOOTCOUNT' => (isset ( $_POST ['compare'] )) ? 
		sprintf ( $footcount_text, sizeof (request_var ( 'compare_ids', array ('' => 0 )))) : 
		$footcount_text, 
));

 
if($config['bbdkp_timebased'] == 1) 
{
	$template->assign_var('O_DKP_HOUR', $sortlink [7]);
	
}

if($config['bbdkp_zerosum'] == 1)
{
	$template->assign_var('O_ZEROSUM_BONUS', $sortlink [8]);
}
if($config['bbdkp_decay'] == 1)
{
	$template->assign_vars ( array (
	'O_EPDECAY' => $sortlink [11],
	'O_ITEMDECAY' => $sortlink [14]
	));
}
if($config['bbdkp_epgp'] == 1)
{
	$template->assign_vars ( array (
	'O_EP' => $sortlink [12],
	'O_GP' => $sortlink [15],
	'O_PR' => $sortlink [16],
	));
	
}


// Output page
page_header ( $user->lang ['LISTMEMBERS_TITLE'] );

// end 

/**
 * this function builds a grid with PR or earned (after decay)
 *
 * @param int $dkpsys_id
 * @param bool $query_by_pool
 * @param bool $show_all
 */
function leaderboard($memberarray, $classarray)
{
	// get all classes that have dkp members
	global $db, $template, $config;
	global $phpbb_root_path, $phpbb_admin_path, $phpEx;

	$classes = array ();
	foreach ($classarray as $k => $class)
	{
		$template->assign_block_vars ( 'class', 
			array (
				'CLASSNAME' 	=> $class ['class_name'], 
				'CLASSIMGPATH'	=> (strlen($class['imagename']) > 1) ? $class['imagename'] . ".png" : '',
				'COLORCODE' 	=> $class['colorcode']
				) 
			);
		
		
		foreach ($memberarray as  $member)
		{
			if($member['class_id'] == $class['class_id'] && $member['game_id'] == $class['game_id'])
			{
				//dkp data per class
				$dkprowarray= array (
					'NAME' => ($member ['member_status'] == '0') ? '<em>' . $member ['member_name'] . '</em>' : $member ['member_name'] , 
					'CURRENT' => $member ['member_current'], 
					'DKPCOLOUR' => ($member ['member_current'] >= 0) ? 'positive' : 'negative', 
					'U_VIEW_MEMBER' => append_sid ( "{$phpbb_root_path}dkp.$phpEx", 'page=viewmember&amp;'. 
							URI_NAMEID . '=' . $member ['member_id'] . '&amp;' . 
							URI_DKPSYS . '=' . $member['member_dkpid'] ) );
					
				if($config['bbdkp_epgp'] == 1)
				{
					$dkprowarray[ 'PR'] = $member ['pr'] ;
				}
				
				$template->assign_block_vars ( 'class.dkp_row', $dkprowarray );
			}
				
		}
		
		$template->assign_vars ( array (
			'S_SHOWLEAD' => true, 
		));	
	}
	
	if(count($classarray)==0)
	{
		$template->assign_vars ( array (
			'S_SHOWLEAD' => false,
		));
	}

	unset($memberarray);
	unset($classarray);
}

/**
 * prepares dkp dropdown,
 *
 * @return int $dkpsys_id
 */
function dkppulldown()
{
	global $user, $db, $template, $query_by_pool;
	
	$query_by_pool = false;
	$defaultpool = 99; 
	$dkpvalues = array();
	
	$dkpvalues[0] = $user->lang['ALL']; 
	$dkpvalues[1] = '--------'; 
		// find only pools with dkp records
	$sql_array = array(
		'SELECT'    => 'a.dkpsys_id, a.dkpsys_name, a.dkpsys_default', 
		'FROM'		=> array( 
					DKPSYS_TABLE => 'a', 
					MEMBER_DKP_TABLE => 'd',
					), 
		'WHERE'  => ' a.dkpsys_id = d.member_dkpid', 
		'GROUP_BY'  => 'a.dkpsys_id, a.dkpsys_name, a.dkpsys_default'
	); 
	$sql = $db->sql_build_query('SELECT', $sql_array);
	
	$result = $db->sql_query ($sql);
	$index = 3;
	while ( $row = $db->sql_fetchrow ( $result ) )
	{
		$dkpvalues[$index]['id'] = $row ['dkpsys_id']; 
		$dkpvalues[$index]['text'] = $row ['dkpsys_name']; 
		if (strtoupper ( $row ['dkpsys_default'] ) == 'Y')
		{
			$defaultpool = $row ['dkpsys_id'];
		}
		$index +=1;
	}
	$db->sql_freeresult ( $result );
	
	$dkpsys_id = 0;
	if(isset( $_POST ['pool']) or isset ( $_GET [URI_DKPSYS] ) )
	{
		if (isset( $_POST ['pool']) )
		{
			$pulldownval = request_var('pool',  $user->lang['ALL']);
			if(is_numeric($pulldownval))
			{
				$query_by_pool = true;
				$dkpsys_id = intval($pulldownval); 	
			}
		}
		elseif (isset ( $_GET [URI_DKPSYS] ))
		{
	
			$pulldownval = request_var(URI_DKPSYS,  $user->lang['ALL']);
			if(is_numeric($pulldownval))
			{
				$query_by_pool = true;
				$dkpsys_id = request_var(URI_DKPSYS, 0);
			}
			else
			{
				$query_by_pool = false;
				$dkpsys_id = $defaultpool;
			}
		}
	}
	else 
	{
		// if no parameters passed to this page then show default pool		
		$query_by_pool = true;
		$dkpsys_id = $defaultpool; 
	}
	
	foreach ($dkpvalues as $key => $value)
	{
		if(!is_array($value))
		{
			$template->assign_block_vars ( 'pool_row', array (
				'VALUE' => $value, 
				'SELECTED' => (!$query_by_pool && $value != '--------') ? ' selected="selected"' : '',
				'DISABLED' => ($value == '--------' ) ? ' disabled="disabled"' : '',  
				'OPTION' => $value, 
			));
		}
		else 
		{
			$template->assign_block_vars ( 'pool_row', array (
				'VALUE' => $value['id'], 
				'SELECTED' => ($dkpsys_id == $value['id']  && $query_by_pool ) ? ' selected="selected"' : '', 
				'OPTION' => $value['text'], 
			));
			
		}
	}

	return $dkpsys_id;
}


/**
 * prepares armor dropdown
 *
 */
function armor()
{
	global $config, $user, $db, $template, $query_by_pool;
	
	global $query_by_armor, $query_by_class, $filter;
	
	/***** begin armor-class pulldown ****/
	$classarray = array();
	$filtervalues = array();
	$armor_type = array();
	$classname = array();
	
	$filtervalues ['all'] = $user->lang['ALL']; 
	$filtervalues ['separator1'] = '--------';
	
	// generic armor list
	$sql = 'SELECT class_armor_type FROM ' . CLASS_TABLE . ' GROUP BY class_armor_type';
	$result = $db->sql_query ( $sql, 604000 );
	while ( $row = $db->sql_fetchrow ( $result ) )
	{
		$filtervalues [strtoupper($row ['class_armor_type'])] = $user->lang[strtoupper($row ['class_armor_type'])];
		$armor_type [strtoupper($row ['class_armor_type'])] = $user->lang[strtoupper($row ['class_armor_type'])];
	}
	$db->sql_freeresult ( $result );
	$filtervalues ['separator2'] = '--------';
	
	// get classlist
	$sql_array = array(
	  'SELECT'    => 	'  c.game_id, c.class_id, l.name as class_name, c.class_min_level, 
	  c.class_max_level, c.imagename, c.colorcode ', 
	  'FROM'      => array(
	       CLASS_TABLE 	=> 'c',
	       BB_LANGUAGE		=> 'l', 
	       MEMBER_LIST_TABLE	=> 'i', 
	       MEMBER_DKP_TABLE	=> 'd', 
	   	),
	  'WHERE'		=> " c.class_id > 0 and l.attribute_id = c.class_id and c.game_id = l.game_id
	   AND l.language= '" . $config['bbdkp_lang'] . "' AND l.attribute = 'class' 
	   AND i.member_class_id = c.class_id and i.game_id = c.game_id 
	   AND d.member_id = i.member_id ",   				    	
	  'GROUP_BY'	=> 'c.game_id, c.class_id, l.name, c.class_min_level, c.class_max_level, c.imagename, c.colorcode',
	  'ORDER_BY'	=> 'c.game_id, c.class_id ',
	   );
	   
	$sql = $db->sql_build_query('SELECT', $sql_array);   
	$result = $db->sql_query ( $sql, 604000);
	$classarray = array();
	while ( $row = $db->sql_fetchrow ( $result ) )
	{
		$classarray[] = $row;
		$filtervalues [$row['game_id'] . '_class_' . $row ['class_id']] = $row ['class_name'];
		$classname [$row['game_id'] . '_class_' . $row ['class_id']] = $row ['class_name'];
	}
	$db->sql_freeresult ( $result );
	
	$query_by_armor = 0;
	$query_by_class = 0;
	$submitfilter = (isset ( $_GET ['filter'] ) or isset ( $_POST ['filter'] )) ? true : false;
	if ($submitfilter)
	{
		$filter = request_var ( 'filter', '' );
		
		if ($filter == "all")
		{
			// select all
			$query_by_armor = 0;
			$query_by_class = 0;
		} 
		elseif (array_key_exists ( $filter, $armor_type ))
		{
			// looking for an armor type
			$filter = preg_replace ( '/ Armor/', '', $filter );
			$query_by_armor = 1;
			$query_by_class = 0;
		} 
		elseif (array_key_exists ( $filter, $classname ))
		{
			// looking for a class
			$query_by_class = 1;
			$query_by_armor = 0;
		}
	}
	 else
	{
		// select all
		$query_by_armor = 0;
		$query_by_class = 0;
		$filter = 'all';
	}
	
	// dump filtervalues to dropdown template
	foreach ( $filtervalues as $fid => $fname )
	{
		$template->assign_block_vars ( 'filter_row', array (
			'VALUE' => $fid, 
			'SELECTED' => ($fid == $filter && $fname !=  '--------' ) ? ' selected="selected"' : '',
			'DISABLED' => ($fname == '--------' ) ? ' disabled="disabled"' : '', 
			'OPTION' => (! empty ( $fname )) ? $fname : $user->lang['ALL'] ) );
	}
	
	/***** end armor - class pulldown ****/
	return $classarray;
}

/**
 * gets array with members to display
 *
 * @param int $dkpsys_id
 * @param array $installed_games
 * @param int $startd
 * @return array $memberarray
 */
function get_standings($dkpsys_id, $installed_games, $startd, $show_all)
{
	
	global $config, $user, $db, $template, $query_by_pool, $phpbb_root_path;
	global $query_by_armor, $query_by_class, $filter;
	
	$sql_array = array(
	    'SELECT'    => 	'l.game_id, m.member_dkpid, d.dkpsys_name, m.member_id, m.member_status, m.member_lastraid, 
	    				sum(m.member_raid_value) as member_raid_value, 
	    				sum(m.member_earned) as member_earned, 
	    				sum(m.member_adjustment - m.adj_decay) as member_adjustment,
	    				sum(m.member_spent) as member_spent, 
						sum(m.member_earned + m.member_adjustment - m.member_spent - m.adj_decay ) AS member_current,
	   					l.member_name, l.member_level, l.member_race_id ,l.member_class_id, l.member_rank_id ,
	       				r.rank_name, r.rank_hide, r.rank_prefix, r.rank_suffix, 
	       				l1.name AS member_class, c.class_id, 
	       				c.colorcode, c.class_armor_type AS armor_type, c.imagename, 
	       				l.member_gender_id, a.image_female, a.image_male, 
						c.class_min_level AS min_level,
						c.class_max_level AS max_level', 
	 
	    'FROM'      => array(
	        MEMBER_DKP_TABLE 	=> 'm',
	        DKPSYS_TABLE 		=> 'd',
	        MEMBER_LIST_TABLE 	=> 'l',
	        MEMBER_RANKS_TABLE  => 'r',
	        RACE_TABLE  		=> 'a',
	        CLASS_TABLE    		=> 'c',
	        BB_LANGUAGE			=> 'l1', 
	    	),
	 
	    'WHERE'     =>  "(m.member_id = l.member_id)  
	    		AND l1.attribute_id =  c.class_id AND l1.language= '" . $config['bbdkp_lang'] . "' AND l1.attribute = 'class' and c.game_id = l1.game_id 
				AND (c.class_id = l.member_class_id and c.game_id=l.game_id) 
				AND (l.member_race_id =  a.race_id and a.game_id=l.game_id)
				AND (r.rank_id = l.member_rank_id) 
				AND (m.member_dkpid = d.dkpsys_id) 
				AND (l.member_guild_id = r.guild_id)
				AND r.rank_hide = 0 " ,
	    'GROUP_BY' => 'l.game_id, m.member_dkpid, d.dkpsys_name, m.member_id, m.member_status, m.member_lastraid, 
	   				l.member_name, l.member_level, l.member_race_id ,l.member_class_id, l.member_rank_id ,
	       			r.rank_name, r.rank_hide, r.rank_prefix, r.rank_suffix, 
	       			l1.name, c.class_id, 
	       			c.colorcode, c.class_armor_type , c.imagename, 
	       			l.member_gender_id, a.image_female, a.image_male, 
					c.class_min_level ,
					c.class_max_level ', 
	);
	
	
	if($config['bbdkp_timebased'] == 1)
	{
		$sql_array[ 'SELECT'] .= ', sum(m.member_time_bonus) as member_time_bonus ';
	}
	
	if($config['bbdkp_zerosum'] == 1)
	{
		$sql_array[ 'SELECT'] .= ', sum(m.member_zerosum_bonus) as member_zerosum_bonus';
	}
	
	if($config['bbdkp_decay'] == 1)
	{
		$sql_array[ 'SELECT'] .= ', 
			sum(m.member_raid_decay) as member_raid_decay, 
			sum(m.member_item_decay) as member_item_decay ';
	}
	
	if($config['bbdkp_epgp'] == 1)
	{
		$sql_array[ 'SELECT'] .= ", 
			sum(m.member_earned + m.member_adjustment - m.adj_decay) AS ep,  
			sum(m.member_spent - m.member_item_decay  + ". floatval($config['bbdkp_basegp']) . " ) AS gp, 
		CASE  WHEN SUM(m.member_spent - m.member_item_decay  + " . max(0, $config['bbdkp_basegp']) . " ) = 0 
		THEN  1 
		ELSE  ROUND(SUM(m.member_earned + m.member_adjustment - m.adj_decay) / 
			  SUM(" . max(0, $config['bbdkp_basegp']) . " + m.member_spent - m.member_item_decay),2) END AS pr " ;
	}
	
	//check if inactive members will be shown
	if ($config ['bbdkp_hide_inactive'] == '1' && !$show_all )
	{
		// don't show inactive members
		$sql_array[ 'WHERE'] .= ' AND m.member_status = 1 ';
	}
	
	if  (isset($_POST['compare']) && isset($_POST['compare_ids']))
	{
		 $compare =  request_var('compare_ids', array('' => 0)) ;
		 $sql_array['WHERE'] .= ' AND ' . $db->sql_in_set('m.member_id', $compare, false, true);
	}
	
	if ($query_by_pool)
	{
		$sql_array['WHERE'] .= ' AND m.member_dkpid = ' . $dkpsys_id . ' ';
	}
	
	
	if (isset ( $_GET ['rank'] ))
	{
		$sql_array['WHERE'] .= " AND r.rank_name='" . request_var ( 'rank', '' ) . "'";
	}
	
	if ($query_by_class == 1)
	{
		//wow_class_8 = Mage
		//lotro_class_5=Hunter
		foreach($installed_games as $k=>$gamename)
		{
			//x is for avoiding output zero which may be outcome of false
			if (strpos('x'.$filter,$k) > 0)
			{
			  $class_id = substr($filter, strlen($k)+7);
			  $sql_array['WHERE'] .= " AND c.class_id =  '" . $db->sql_escape ( $class_id ) . "' ";
			  $sql_array['WHERE'] .= " AND c.game_id =  '" . $db->sql_escape ( $k ) . "' ";
			  break 1;  	
			}
		}
		 
	}
	
	if ($query_by_armor == 1)
	{
		$sql_array['WHERE'] .= " AND c.class_armor_type =  '" . $db->sql_escape ( $filter ) . "'";
	}
		
	// default sorting
	if($config['bbdkp_epgp'] == 1)
	{
		$sql_array[ 'ORDER_BY'] = "CASE WHEN SUM(m.member_spent - m.member_item_decay  + ". floatval($config['bbdkp_basegp']) . "  ) = 0 
		THEN 1
		ELSE ROUND(SUM(m.member_earned + m.member_adjustment - m.adj_decay) / 
		SUM(" . max(0, $config['bbdkp_basegp']) .' + m.member_spent - m.member_item_decay),2) END DESC ' ;
	}
	else 
	{
		$sql_array[ 'ORDER_BY'] = 'sum(m.member_earned + m.member_adjustment - m.member_spent - m.adj_decay) desc, l.member_name asc ' ;
	}
	
	
	$sql = $db->sql_build_query('SELECT_DISTINCT', $sql_array);
	if (! ($members_result = $db->sql_query ( $sql )))
	{
		trigger_error ($user->lang['MNOTFOUND']);
	}
	
	global $allmember_count;
	$allmember_count = 0;
	while ( $row = $db->sql_fetchrow ( $members_result ) )
	{
		++$allmember_count;
	}
	
	$members_result = $db->sql_query_limit ( $sql, $config ['bbdkp_user_llimit'], $startd ); 
	$memberarray = array ();
	$member_count =0;
	while ( $row = $db->sql_fetchrow ( $members_result ) )
	{
		$race_image = (string) (($row['member_gender_id']==0) ? $row['image_male'] : $row['image_female']);
		
		++$member_count;
		$memberarray [$member_count] ['game_id'] = $row ['game_id'];
		$memberarray [$member_count] ['class_id'] = $row ['class_id'];
		$memberarray [$member_count] ['dkpsys_name'] = $row ['dkpsys_name']; 
		$memberarray [$member_count] ['member_id'] = $row ['member_id'];
		$memberarray [$member_count] ['count'] = $member_count;
		$memberarray [$member_count] ['member_name'] = $row ['member_name'];
		$memberarray [$member_count] ['member_status'] = $row ['member_status'];
		$memberarray [$member_count] ['rank_prefix'] = $row ['rank_prefix'];
		$memberarray [$member_count] ['rank_suffix'] = $row ['rank_suffix'];
		$memberarray [$member_count] ['rank_name'] = $row ['rank_name'];
		$memberarray [$member_count] ['rank_hide'] = $row ['rank_hide'];
		$memberarray [$member_count] ['member_level'] = $row ['member_level'];
		$memberarray [$member_count] ['member_class'] = $row ['member_class'];
		$memberarray [$member_count] ['colorcode'] = $row ['colorcode'];
		$memberarray [$member_count] ['class_image'] = (strlen($row['imagename']) > 1) ? $phpbb_root_path . "images/class_images/" . $row['imagename'] . ".png" : '';
		$memberarray [$member_count] ['class_image_exists'] = (strlen($row['imagename']) > 1) ? true : false; 
		$memberarray [$member_count] ['race_image'] = (strlen($race_image) > 1) ? $phpbb_root_path . "images/race_images/" . $race_image . ".png" : '';
		$memberarray [$member_count] ['race_image_exists'] = (strlen($race_image) > 1) ? true : false; 		
		
		$memberarray [$member_count] ['armor_type'] = $row ['armor_type'];
		$memberarray [$member_count] ['member_raid_value'] = $row ['member_raid_value'];
		if($config['bbdkp_timebased'] == 1)
		{
			$memberarray [$member_count] ['member_time_bonus'] = $row ['member_time_bonus'];
			
		}
		if($config['bbdkp_zerosum'] == 1)
		{
			$memberarray [$member_count] ['member_zerosum_bonus'] = $row ['member_zerosum_bonus'];
		}
		$memberarray [$member_count] ['member_earned'] = $row ['member_earned'];
		
		$memberarray [$member_count] ['member_adjustment'] = $row ['member_adjustment'];
		
		if($config['bbdkp_decay'] == 1)
		{
			$memberarray [$member_count] ['member_raid_decay'] = $row ['member_raid_decay'];
			$memberarray [$member_count] ['member_item_decay'] = $row ['member_item_decay'];
		}
		
		$memberarray [$member_count] ['member_spent'] = $row ['member_spent'];
		$memberarray [$member_count] ['member_current'] = $row ['member_current'];
		
		if($config['bbdkp_epgp'] == 1)
		{
			$memberarray [$member_count] ['ep'] = $row ['ep'];
			$memberarray [$member_count] ['gp'] = $row ['gp'];
			$memberarray [$member_count] ['pr'] = $row ['pr'];
		}
		
		$memberarray [$member_count] ['member_lastraid'] = $row ['member_lastraid'];
		$memberarray [$member_count] ['attendanceP1'] = raidcount ( true, $row ['member_dkpid'], $config ['bbdkp_list_p1'], $row ['member_id'],2,false );
		$memberarray [$member_count] ['member_dkpid'] = $row ['member_dkpid'];
	
	}
	$db->sql_freeresult ( $members_result );

	return $memberarray;
}


?>