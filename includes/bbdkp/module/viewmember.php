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

$member_id = request_var(URI_NAMEID, 0);

// pulldown
$query_by_pool = false;
$defaultpool = 99; 

$dkpvalues[0] = $user->lang['ALL']; 
$dkpvalues[1] = '--------'; 

$sql_array = array(
	'SELECT'    => 'a.dkpsys_id, a.dkpsys_name, a.dkpsys_default', 
	'FROM'		=> array( 
				DKPSYS_TABLE => 'a', 
				MEMBER_DKP_TABLE => 'd',
				), 
	'WHERE'  => ' a.dkpsys_id = d.member_dkpid 
				and d.member_id =  ' . $member_id
); 
$sql = $db->sql_build_query('SELECT', $sql_array);
$result = $db->sql_query ( $sql );
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

$dkp_id = 0;
if(isset( $_POST ['pool']) or isset ( $_GET [URI_DKPSYS] ) )
{
	//from pulldown
	if (isset( $_POST ['pool']) )
	{
		$pulldownval = request_var('pool',  $user->lang['ALL']);
		if(is_numeric($pulldownval))
		{
			$query_by_pool = true;
			$dkp_id = intval($pulldownval); 	
		}
	}
	elseif (isset ( $_GET [URI_DKPSYS] ))
	{
		$query_by_pool = true;
		$dkp_id = request_var(URI_DKPSYS, 0); 
	}
}
else 
{
	$query_by_pool = true;
	$dkp_id = $defaultpool; 
}

foreach ( $dkpvalues as $key => $value )
{
	if(!is_array($value))
	{
		$template->assign_block_vars ( 'pool_row', array (
			'VALUE' => $value, 
			'SELECTED' => ($value == $dkp_id && $value != '--------') ? ' selected="selected"' : '',
			'DISABLED' => ($value == '--------' ) ? ' disabled="disabled"' : '',  
			'OPTION' => $value, 
		));
	}
	else 
	{
		$template->assign_block_vars ( 'pool_row', array (
			'VALUE' => $value['id'], 
			'SELECTED' => ($dkp_id == $value['id']) ? ' selected="selected"' : '', 
			'OPTION' => $value['text'], 
		));
		
	}
}

$query_by_pool = ($dkp_id != 0) ? true : false;
/**** end dkpsys pulldown  ****/


/*****************************
/***   make member array
******************************/
$sql_array = array(
	'SELECT'    => '
    	a.*, 
    	g.name as guildname, g.realm, g.region, 
		m.member_id, 
		m.member_raid_value,
		m.member_time_bonus,
		m.member_zerosum_bonus, 
		m.member_earned,
		m.member_adjustment, 
		m.member_spent,
		m.member_item_decay,
		m.member_raid_decay, 
		m.adj_decay, 
		(m.member_earned + m.member_adjustment - m.member_spent ) AS member_current,
		(m.member_earned + m.member_adjustment) AS ep	,
		(m.member_earned + m.member_adjustment - m.adj_decay) AS ep_net	,
		(m.member_spent + ' . max(0, $config['bbdkp_basegp']) . ') AS gp,
		m.member_spent - m.member_item_decay as gp_net, 
		CASE WHEN (m.member_spent - m.member_item_decay + ' . max(0, $config['bbdkp_basegp']) . ' ) = 0 
		THEN 1 
		ELSE ROUND((m.member_earned + m.member_adjustment - m.adj_decay) / (' . max(0, $config['bbdkp_basegp']) .' + m.member_spent - m.member_item_decay),2) end as pr,
		m.member_firstraid,
		m.member_lastraid,
		r1.name AS member_race,
		s.dkpsys_name, 
		l.name AS member_class, 
		r.rank_name, 
		r.rank_prefix, 
		r.rank_suffix, 
		c.class_armor_type AS armor_type ,
		c.colorcode, 
		c.imagename, 
		a.member_gender_id, race.image_female, race.image_male ', 
 
    'FROM'      => array(
        MEMBER_LIST_TABLE 	=> 'a',
        GUILD_TABLE			=> 'g', 
        MEMBER_DKP_TABLE    => 'm',
        MEMBER_RANKS_TABLE  => 'r',
		CLASS_TABLE 		=> 'c',
        RACE_TABLE  		=> 'race',
		BB_LANGUAGE			=> 'l', 
        DKPSYS_TABLE    	=> 's',
    ),
    
     'LEFT_JOIN' => array(
        array(
            'FROM'  => array(BB_LANGUAGE => 'r1'),
            'ON'    => "r1.attribute_id = a.member_race_id AND r1.language= '" . 
        		$config['bbdkp_lang'] . "' AND r1.attribute = 'race' and r1.game_id = a.game_id " 
            )
        ),
 
    'WHERE'     =>  " a.member_rank_id = r.rank_id 
    				AND a.member_guild_id = r.guild_id  
					AND a.member_id = m.member_id 
					AND a.member_class_id = c.class_id and a.game_id = c.game_id
					AND a.member_race_id =  race.race_id and a.game_id = race.game_id
					AND m.member_dkpid = s.dkpsys_id
					AND a.member_guild_id = g.id     
					AND l.attribute_id = c.class_id and l.game_id = c.game_id AND l.language= '" . $config['bbdkp_lang'] . "' AND l.attribute = 'class'    
					AND s.dkpsys_id = " . $dkp_id . '   
				    AND a.member_id = ' . $member_id,
				);
			 
$sql = $db->sql_build_query('SELECT', $sql_array);
if ( !($result = $db->sql_query($sql)) )
{
	trigger_error($user->lang['ERROR_MEMBERNOTFOUND']);
}

if ( !$row = $db->sql_fetchrow($result) )
{
	trigger_error($user->lang['ERROR_MEMBERNOTFOUND']);
}
$db->sql_freeresult($result);

// make object
$member = array(
	'guildname'            => $row['guildname'],
	'region'			   => $row['region'],  
	'realm'				   => $row['realm'],  
	'member_id'            => $row['member_id'],
	'member_dkpname'	   => $row['dkpsys_name'],  
	'member_name'          => $row['member_name'],
	'member_firstraid'     => $row['member_firstraid'],
	'member_lastraid'  	   => $row['member_lastraid'],
	'member_raid_value'    => $row['member_raid_value'],
	'member_time_bonus'    => $row['member_time_bonus'],
	'member_zerosum_bonus' => $row['member_zerosum_bonus'],
	'member_earned'        => $row['member_earned'],
	'member_adjustment'    => $row['member_adjustment'],
	'adj_decay'			   => $row['adj_decay'],			
	'member_current'       => $row['member_current'],
	'ep'    			   => $row['ep'],
	'member_raid_decay'	   => $row['member_raid_decay'], 
	'ep_net'    		   => $row['ep_net'],
	'member_spent'         => $row['member_spent'],
	'bgp'				   => $config['bbdkp_basegp'], 
	'gp'     			   => $row['gp'],
	'member_item_decay'    => $row['member_item_decay'],
	'gp_net'     		   => $row['gp_net'],
	'pr'     			   => $row['pr'],
	'member_race_id'    => $row['member_race_id'], 
	'member_race'       => $row['member_race'],
	'member_class_id'   => $row['member_class_id'],
	'member_class'      => $row['member_class'],
	'member_level'      => $row['member_level'], 
	'member_rank_id'    => $row['member_rank_id'],
	'member_rank'		=> $row['rank_name'],
	'classimage'		=> $row['imagename'],
	'raceimage'			=> (string) (($row['member_gender_id']==0) ? $row['image_male'] : $row['image_female']), 
	'colorcode'			=> $row['colorcode'], 
);	

//output
$template->assign_vars(array(
	'NAME'	   		=> $member['member_name'],
	'GUILD'	   		=> $member['guildname'], 
	'REGION'   		=> $member['region'], 
	'REALM'	   		=> $member['realm'],  

	'RAIDVAL'       => $member['member_raid_value'],
	'TIMEBONUS'     => $member['member_time_bonus'],
	'ZEROSUM'      	=> $member['member_zerosum_bonus'],
	'EARNED'        => $member['member_earned'],
	'EP'    		=> $member['ep'],

	'RAIDDECAY'		=> $member['member_raid_decay'],
	'EPNET'			=> (float) $member['ep_net'],
	'ADJUSTMENT'    => $member['member_adjustment'],
	'C_ADJUSTMENT'  => ($member['member_adjustment'] > 0) ? 'positive' : 'negative', 
	
	'SPENT'         => $member['member_spent'],
	'ITEMDECAY'     => $member['member_item_decay'],
	'GP'     		=> $member['gp'],
	'BGP'     		=> $member['bgp'],
	'GPNET'     	=> $member['gp_net'] + $member['bgp'],

	'CURRENT'       => $member['member_current'],
	'C_CURRENT'       => ($member['member_current'] > 0) ? 'positive' : 'negative',
	'PR'     		=> $member['pr'],

	'ADJDECAY'		=> $member['adj_decay'], 
	'TOTAL_DECAY'	=> $member['member_raid_decay'] - $member['member_item_decay'] + $member['adj_decay'],
	'C_TOTAL_DECAY'	=> ($member['member_raid_decay'] -$member['member_item_decay'] + $member['adj_decay']) > 0 ? 'negative' : 'positive' ,


	'NETCURRENT'    => $member['ep_net'] - $member['gp_net'] - max(0, $config['bbdkp_basegp']) ,
	'C_NETCURRENT'      => (($member['member_current'] + $member['member_item_decay'] - max(0, $config['bbdkp_basegp']) ) > 0   )  ? 'positive' : 'negative',
	
	'MEMBER_LEVEL'    => $member['member_level'],
	'MEMBER_DKPID'    => $dkp_id,
	'MEMBER_DKPNAME'  => $member['member_dkpname'],
	'MEMBER_RACE'     => $member['member_race'],
	'MEMBER_CLASS'    => $member['member_class'],
	'COLORCODE'       => $member['colorcode'],
	'CLASS_IMAGE'       => (strlen($member['classimage']) > 1) ? $phpbb_root_path . "images/class_images/" . $member['classimage'] . ".png" : '', 
	'S_CLASS_IMAGE_EXISTS' =>  (strlen($member['classimage']) > 1) ? true : false,
	'RACE_IMAGE'       => (strlen($member['raceimage']) > 1) ? $phpbb_root_path . "images/race_images/" . $member['raceimage'] . ".png" : '', 
	'S_RACE_IMAGE_EXISTS' =>  (strlen($member['raceimage']) > 1) ? true : false,

	'MEMBER_RANK'     => $member['member_rank'],

	'S_SHOWZS' 		=> ($config['bbdkp_zerosum'] == '1') ? true : false, 
	'S_SHOWDECAY' 	=> ($config['bbdkp_decay'] == '1') ? true : false,
	'S_SHOWEPGP' 	=> ($config['bbdkp_epgp'] == '1') ? true : false,
 	'S_SHOWTIME' 	=> ($config['bbdkp_timebased'] == '1') ? true : false,
	
	'U_VIEW_MEMBER' => append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=viewmember&amp;' . URI_NAMEID . '=' . $member_id .'&amp;' . URI_DKPSYS . '=' . $dkp_id), 
	'POINTNAME'		=> $config['bbdkp_dkp_name'],

));

/* Get attendance */
$range1 = $config['bbdkp_list_p1'];
$range2 = $config['bbdkp_list_p2']; 
$range3 = $config['bbdkp_list_p3']; 	


$mc1 = raidcount(true, $dkp_id, $range1, $member_id, 0, false);
$mc2 = raidcount(true, $dkp_id, $range2, $member_id, 0, false);
$mc3 = raidcount(true, $dkp_id, $range3, $member_id, 0, false);
$mclife = raidcount(true, $dkp_id, 0, $member_id, 0, true);

$pc1	= raidcount(true, $dkp_id, $range1, $member_id, 1, false);
$pc2	= raidcount(true, $dkp_id, $range2, $member_id, 1, false);
$pc3	= raidcount(true, $dkp_id, $range3, $member_id, 1, false);
$pclife = raidcount(true, $dkp_id, 0, $member_id, 1, true);
	
$pct1 =	 ( $pc1 > 0 ) ? round(($mc1 / $pc1) * 100, 1) : 0;
$pct2 =	 ( $pc2 > 0 ) ? round(($mc2 / $pc2) * 100, 1) : 0;
$pct3 =	 ( $pc3 > 0 ) ? round(($mc3 / $pc3) * 100, 1) : 0;
$pctlife = ( $pclife > 0 ) ? round(($mclife / $pclife) * 100, 1) : 0;

$template->assign_vars(array(
	'RAIDS_X1_DAYS'	  => sprintf($user->lang['RAIDS_X_DAYS'], $range1),
	'RAIDS_X2_DAYS'	  => sprintf($user->lang['RAIDS_X_DAYS'], $range2),
	'RAIDS_X3_DAYS'	  => sprintf($user->lang['RAIDS_X_DAYS'], $range3),
	'RAIDS_LIFETIME'  => sprintf($user->lang['RAIDS_LIFETIME'],
		 date($config['bbdkp_date_format'], $member['member_firstraid']),
		 date($config['bbdkp_date_format'], $member['member_lastraid'])),
											
	'C_RAIDS_X1_DAYS'  => $mc1 .'/'. $pc1 .' : '. $pct1,
	'C_RAIDS_X2_DAYS'  => $mc2 .'/'. $pc2 .' : '. $pct2,
	'C_RAIDS_X3_DAYS'  => $mc3 .'/'. $pc3 .' : '. $pct3,
	'C_RAIDS_LIFETIME' => $mclife .'/'. $pclife .' : '. $pctlife,
));



/****************************
/*	 Get Attended Raids	*
*****************************/

if (!isset($_GET['rstart']))  
{
	$rstart=0; 
}
else
{
	$rstart = request_var('rstart',0) ;
}

$sql_array = array(
	'SELECT'	=>	'r.raid_id, e.event_name, e.event_dkpid, r.raid_start, r.raid_note, 
	ra.raid_value, ra.raid_decay, ra.time_bonus, ra.zerosum_bonus, 
   (ra.raid_value + ra.time_bonus + ra.zerosum_bonus - ra.raid_decay) as netearned ', 
	'FROM'	=> array(
		EVENTS_TABLE			=> 'e',				
		RAIDS_TABLE				=> 'r',
		RAID_DETAIL_TABLE	=> 'ra',
		),

	'WHERE'		=>	'
	     ra.raid_id = r.raid_id
		 AND e.event_id = r.event_id
		 AND ra.member_id=' . $member_id . '
		 AND e.event_dkpid=' . (int) $dkp_id, 
	'ORDER_BY'	=> 'r.raid_start DESC',
	  );
		  
$sql = $db->sql_build_query('SELECT', $sql_array);

//calculate first window 
$current_earned=0;
if($rstart > 0)
{
	if (!$raids_result = $db->sql_query_limit($sql, $rstart , 0))
	{
	   trigger_error ($user->lang['MNOTFOUND']);
	}
	$current_earned = $member['member_earned'] + $member['member_time_bonus'] + $member['member_zerosum_bonus'];
	while ( $raid = $db->sql_fetchrow($raids_result))
	{
		$current_earned = $current_earned - $raid['netearned'];
	}
}
else 
{
	$current_earned = $member['member_earned'] + $member['member_time_bonus'] + $member['member_zerosum_bonus'];
}

$raidlines = $config['bbdkp_user_rlimit'] ;
// calculate second window
if (!$raids_result = $db->sql_query_limit($sql, $raidlines, $rstart))
{
   trigger_error ($user->lang['MNOTFOUND']);
}

while ( $raid = $db->sql_fetchrow($raids_result))
{
	$template->assign_block_vars('raids_row', array(
		'DATE'			 => ( !empty($raid['raid_start']) ) ? date($config['bbdkp_date_format'], $raid['raid_start']) : '&nbsp;',
		'U_VIEW_RAID'	 => append_sid("{$phpbb_root_path}dkp.$phpEx" , 'page=viewraid&amp;' . URI_RAID . '='.$raid['raid_id']),
		'NAME'			 => $raid['event_name'],
		'NOTE'			 => ( !empty($raid['raid_note']) ) ? $raid['raid_note'] : '&nbsp;',
		'RAIDVAL'		 => $raid['raid_value'],
		'TIMEBONUS'		 => $raid['time_bonus'],
		'ZSBONUS'		 => $raid['zerosum_bonus'],
		'RAIDDECAY'		 => $raid['raid_decay'],
		'EARNED'		 => $raid['netearned'],
		'CURRENT_EARNED' => sprintf("%.2f", $current_earned))
	);
	$current_earned -= $raid['netearned'];
}

// get number of attended raids
$db->sql_freeresult($raids_result);

$total_attended_raids = raidcount(true,$dkp_id,0,$member_id, 0,true);

/**********************************
/***   Item purchase history  *****
***********************************/

if (!isset($_GET['istart']))
{
	
	$istart=0; 
}
else
{
	$istart = request_var('istart', 0);
}

$sql_array = array(
	'SELECT'	=>	'i.item_id, i.item_name, i.item_value, i.item_date, i.raid_id, i.item_gameid, e.event_name ', 
	'FROM'		=> array(
		EVENTS_TABLE	=> 'e',
		RAID_ITEMS_TABLE	=> 'i',
		RAIDS_TABLE		=> 'r',
		),

	'WHERE'		=>	' e.event_id = r.event_id
		  AND e.event_dkpid=' .	 (int) $dkp_id . ' 
		  AND r.raid_id = i.raid_id		 
		  AND i.member_id = ' . $member_id, 
	'ORDER_BY'	=> 'i.raid_id DESC, i.item_date DESC',
	  );
$sql = $db->sql_build_query('SELECT', $sql_array);

//calculate first window 
$current_spent = 0;
if($istart > 0)
{
	if (!$items_result = $db->sql_query_limit($sql, $istart , 0))
	{
	   trigger_error ($user->lang['MNOTFOUND']);
	}
	$current_spent = $member['member_spent'];
	
	while ( $item = $db->sql_fetchrow($items_result))
	{
		$current_spent = $current_spent - $item['item_value'];
	}
}
else 
{
	$current_spent = $member['member_spent'];
}

$itemlines = $config['bbdkp_user_ilimit'];
$items_result = $db->sql_query_limit($sql, $itemlines, $istart);
if ( !$items_result)
{
	trigger_error ($user->lang['MNOTFOUND']);
}

while ( $item = $db->sql_fetchrow($items_result) )
{
	if ($bbDKP_Admin->bbtips == true)
	{
		if ($item['item_gameid'] > 0 )
		{
			$item_name = '<strong>' . $bbtips->parse('[itemdkp]' . $item['item_gameid']	 . '[/itemdkp]') . '</strong>' ; 
		}
		else 
		{
			$item_name = '<strong>' . $bbtips->parse ( '[itemdkp]' . $item ['item_name'] . '[/itemdkp]' . '</strong>'  );
		}
		
	}
	else
	{
		$item_name = '<strong>' . $item['item_name'] . '</strong>';
	}
	
	$template->assign_block_vars('items_row', array(
		'DATE'			=> ( !empty($item['item_date']) ) ? date($config['bbdkp_date_format'], $item['item_date']) : '&nbsp;',
		'U_VIEW_ITEM'	=> append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=viewitem&amp;' . URI_ITEM . '=' . $item['item_id']),
		'U_VIEW_RAID'	=> append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=viewraid&amp;' . URI_RAID . '=' . $item['raid_id']),
		'NAME'			=> $item_name, 
		'RAID'			=> ( !empty($item['event_name']) ) ? $item['event_name'] : '&lt;<i>Not Found</i>&gt;',
		'SPENT'			=> $item['item_value'],
		'CURRENT_SPENT' => sprintf("%.2f", $current_spent))
	);
	$current_spent -= $item['item_value'];
}
$db->sql_freeresult($items_result);

$sql_array = array(
	'SELECT'	=>	'count(*) as itemcount	', 
	'FROM'		=> array(
		EVENTS_TABLE		=> 'e',
		RAIDS_TABLE			=> 'r',
		RAID_ITEMS_TABLE			=> 'i',				
	),

	'WHERE'		=>	" e.event_id = r.event_id
		 AND e.event_dkpid=" . (int) $dkp_id . '	 
		 AND r.raid_id = i.raid_id
		 AND i.member_id  = ' . $member_id, 
	  );
$sql6 = $db->sql_build_query('SELECT', $sql_array);
$result6 = $db->sql_query($sql6);
$total_purchased_items = $db->sql_fetchfield('itemcount');
$db->sql_freeresult($result6);	
	
$raidpag  = generate_pagination2(append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=viewmember&amp;' . URI_DKPSYS.'='.$dkp_id. '&amp;' . URI_NAMEID. '='.$member_id. '&amp;istart=' .$istart), 
$total_attended_raids, $raidlines, $rstart, 1, 'rstart');
	 
$itpag =   generate_pagination2(append_sid("{$phpbb_root_path}dkp.$phpEx" ,'page=viewmember&amp;'.  URI_DKPSYS.'='.$dkp_id. '&amp;' . URI_NAMEID. '='.$member_id. '&amp;rstart='.$rstart ),
$total_purchased_items,	 $itemlines, $istart, 1 ,'istart');

$template->assign_vars(array(
	'RAID_PAGINATION'	  => $raidpag, 
	'RSTART'			  => $rstart,	
	'RAID_FOOTCOUNT'	  => sprintf($user->lang['VIEWMEMBER_RAID_FOOTCOUNT'], $total_attended_raids, $raidlines),
	'ITEM_PAGINATION'	  => $itpag, 
	'ISTART'			  => $istart, 
	'ITEM_FOOTCOUNT'	  => sprintf($user->lang['VIEWMEMBER_ITEM_FOOTCOUNT'], $total_purchased_items, $itemlines),
	'ITEMS'				  => ( is_null($total_purchased_items) ) ? false : true,
));

/***************************************
 **** Individual Adjustment History	  **
 ***************************************/
$sql = 'SELECT adjustment_value, adjustment_date, adjustment_reason, adj_decay 
		FROM ' . ADJUSTMENTS_TABLE . '	
		WHERE member_id = ' . $member_id . ' 
		AND	 adjustment_dkpid = ' . (int) $dkp_id . ' 
		ORDER BY adjustment_date DESC ';
		
if ( !($adjustments_result = $db->sql_query($sql)) )
{
	trigger_error ($user->lang['MNOTFOUND']);
}

$adjust = null;

while ( $adjustment = $db->sql_fetchrow($adjustments_result) )
{
	$adjust++;
	$template->assign_block_vars('adjustments_row', array(
		'DATE'					  => ( !empty($adjustment['adjustment_date']) ) ? date($config['bbdkp_date_format'], $adjustment['adjustment_date']) : '&nbsp;',
		'REASON'				  => ( !empty($adjustment['adjustment_reason']) ) ? $adjustment['adjustment_reason'] : '&nbsp;',
		'ADJDECAY' 				  => $adjustment['adj_decay'],
		'NETDECAY' 				  => $adjustment['adjustment_value'] - $adjustment['adj_decay'],
		'C_INDIVIDUAL_ADJUSTMENT' => $adjustment['adjustment_value'],
		'INDIVIDUAL_ADJUSTMENT'	  => $adjustment['adjustment_value'])
	);
}

   $template->assign_vars(array(
   	'ADJUSTMENT_FOOTCOUNT' => sprintf($user->lang['VIEWMEMBER_ADJUSTMENT_FOOTCOUNT'], $adjust),
	'HASADJUSTMENT'			=> ( is_null($adjust) ) ? false : true,

));

/****************************************
*****		ATTENDANCE BY EVENT		   ***
****************************************/
$raid_counts = array();

// Find the raidcount after firstdate for this player
$sql_array = array(
	'SELECT'	=>	' e.event_id, e.event_name, count(r.raid_id) AS raid_count ', 
	'FROM'		=> array(
		EVENTS_TABLE			=> 'e',				
		RAIDS_TABLE				=> 'r',
		RAID_DETAIL_TABLE	=> 'ra', 
		),
	'WHERE'		=> ' r.event_id = e.event_id
		AND e.event_dkpid = ' . (int) $dkp_id . '	 
		AND ra.raid_id = r.raid_id 
		AND ra.member_id=' . (int) $member_id .	 '
		AND r.raid_start >= ' . (int) $member['member_firstraid'], 
	 'GROUP_BY' => 'ra.member_id, e.event_name'
);
$sql = $db->sql_build_query('SELECT', $sql_array);
$result = $db->sql_query($sql);
while ( $row = $db->sql_fetchrow($result) )
{
	// The count now becomes the percent
	$raid_counts[ $row['event_name'] ] = $row['raid_count'];
	$event_ids[ $row['event_name'] ] = $row['event_id'];
}
$db->sql_freeresult($result);

// Find the global raidcount after player firstraid
$sql_array = array(
	'SELECT'	=>	' e.event_name, count(r.raid_id) AS raid_count ', 
	'FROM'		=> array(
		EVENTS_TABLE			=> 'e',				
		RAIDS_TABLE				=> 'r',
		),
	'WHERE'		=> ' r.event_id = e.event_id 
		AND e.event_dkpid = ' . (int) $dkp_id . '	 
		AND r.raid_start >= ' . (int) $member['member_firstraid'], 
	 'GROUP_BY' => 'e.event_name'
);
$sql = $db->sql_build_query('SELECT', $sql_array);
$result = $db->sql_query($sql);
while ( $row = $db->sql_fetchrow($result) )
{
	if ( isset($raid_counts[$row['event_name']]) )
	{
		$percent = round(($raid_counts[ $row['event_name'] ] / $row['raid_count']) * 100);
		$raid_counts[$row['event_name']] = array(
			'percent' => $percent, 
			'count' => $raid_counts[ $row['event_name'] ]);
		unset($percent);
	}
}
$db->sql_freeresult($result);

$sort_order = array(
	0 => array('event_name', 'event_name desc'),
	1 => array('raid_count desc', 'raid_count')
);
$current_order = switch_order($sort_order);

// Since we can't sort in SQL for this case, we have to sort
// by the array
switch ( $current_order['sql'] )
{
	// Sort by key
	case 'event_name':
		ksort($raid_counts);
		break;
	case 'event_name desc':
		krsort($raid_counts);
		break;
	// Sort by value (keeping relational keys intact)
	case 'raid_count':
		asort($raid_counts);
		break;
	case 'raid_count desc':
		arsort($raid_counts);
		break;
}

reset($raid_counts);
foreach ( $raid_counts as $event => $data )
{
	$template->assign_block_vars('event_row', array(
		'EVENT'		   => $event,
		'U_VIEW_EVENT' => append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=viewevent&amp;' . URI_EVENT . '=' . $event_ids[$event] . '&amp;' . URI_DKPSYS . '=' . $dkp_id) ,
		//'BAR'		   => create_bar($data['percent'] . '%', $data['count'] . ' (' . $data['percent'] . '%)')
		)
	);
}

$navlinks_array = array(
array(
 'DKPPAGE' => $user->lang['MENU_STANDINGS'],
 'U_DKPPAGE' => append_sid("{$phpbb_root_path}dkp.$phpEx" , 'page=standings'),
),

array(
 'DKPPAGE' => sprintf($user->lang['MENU_VIEWMEMBER'], $member['member_name']) ,
 'U_DKPPAGE' => append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=viewmember&amp;' . URI_NAMEID . '=' . $member_id .'&amp;' . URI_DKPSYS . '=' . $dkp_id . '&amp;'), 
),
);

foreach( $navlinks_array as $name )
{
	$template->assign_block_vars('dkpnavlinks', array(
		'DKPPAGE'	=> $name['DKPPAGE'],
		'U_DKPPAGE' => $name['U_DKPPAGE'],
	));
}

$template->assign_vars(array(
	'L_EVENTS_FOOTCOUNT'			=> sprintf($user->lang['VIEWMEMBER_EVENT_FOOTCOUNT'], count($raid_counts)), 
	'O_EVENT'	=> $current_order['uri'][0],
	'O_PERCENT' => $current_order['uri'][1],
	'S_COMP' => ( isset($s_comp) ) ? false : true,
	'S_DISPLAY_VIEWMEMBER' => true,
	
));
 unset($raid_counts, $event_ids);
$db->sql_freeresult($adjustments_result);

// Output page
page_header($user->lang['MEMBER']);

?>
