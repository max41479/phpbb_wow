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

/**** begin dkpsys pulldown  ****/
$query_by_pool = false;
$defaultpool = 99;

$dkpvalues [0] = $user->lang ['ALL'];
$dkpvalues [1] = '--------';
// select dkp pools with drops
$sql_array = array(
	'SELECT'    => 'a.dkpsys_id, a.dkpsys_name, a.dkpsys_default', 
	'FROM'		=> array( 
				DKPSYS_TABLE => 'a', 
				EVENTS_TABLE => 'e',
				RAIDS_TABLE => 'r',
				RAID_ITEMS_TABLE => 'ri',
				), 
	'WHERE'  => ' a.dkpsys_id = e.event_dkpid and e.event_id=r.event_id and ri.raid_id = r.raid_id', 
	'GROUP_BY'  => 'a.dkpsys_id, a.dkpsys_name, a.dkpsys_default'
);
$sql = $db->sql_build_query('SELECT', $sql_array);
$result = $db->sql_query ( $sql );
$index = 3;
while ( $row = $db->sql_fetchrow ( $result ) )
{
	$dkpvalues [$index] ['id'] = $row ['dkpsys_id'];
	$dkpvalues [$index] ['text'] = $row ['dkpsys_name'];
	if (strtoupper ( $row ['dkpsys_default'] ) == 'Y')
	{
		$defaultpool = $row ['dkpsys_id'];
	}
	$index += 1;
}
$db->sql_freeresult ( $result );

$dkp_id = 0;
if (isset ( $_POST ['pool'] ) or isset ( $_POST ['getdksysid'] ) or isset ( $_GET [URI_DKPSYS] ))
{
	if (isset ( $_POST ['pool'] ))
	{
		$pulldownval = request_var ( 'pool', $user->lang ['ALL'] );
		if (is_numeric ( $pulldownval ))
		{
			$query_by_pool = true;
			$dkp_id = intval ( $pulldownval );
		}
	}
	elseif (isset ( $_GET [URI_DKPSYS] ))
	{
		$query_by_pool = true;
		$dkp_id = request_var ( URI_DKPSYS, 0 );
	}
}
else 
{
	$query_by_pool = true;
	$dkp_id = $defaultpool; 
}

foreach ( $dkpvalues as $key => $value )
{
	if (! is_array ( $value ))
	{
		$template->assign_block_vars ( 'pool_row', array (
			'VALUE' => $value, 
			'SELECTED' => ($value == $dkp_id && $value != '--------') ? ' selected="selected"' : '', 
			'DISABLED' => ($value == '--------') ? ' disabled="disabled"' : '', 
			'OPTION' => $value ) );
	} else
	{
		$template->assign_block_vars ( 'pool_row', array (
			'VALUE' => $value ['id'], 
			'SELECTED' => ($dkp_id == $value ['id']) ? ' selected="selected"' : '', 
			'OPTION' => $value ['text'] ) );
	
	}
}

$query_by_pool = ($dkp_id != 0) ? true : false;
/**** end dkpsys pulldown  ****/

/**
 *
 * Item Purchase History (all items)
 * Item Value listing  (item values)
 *
 **/
$mode = request_var ( 'mode', 'values' );

$sql_array = array();
switch ($mode)
{
	case 'values' :
		$sql_array['SELECT'] = ' COUNT(DISTINCT item_name) as itemcount ' ;
		$s_history = false;
		break;
	case 'history' :
		$sql_array['SELECT'] = ' COUNT(*) as itemcount ' ;
		$s_history = true;
		break;
}

$sql_array['FROM'] = array( 	
	EVENTS_TABLE 		=> 'e', 
    RAIDS_TABLE 		=> 'r', 
	RAID_ITEMS_TABLE 		=> 'i', 
);
$sql_array['WHERE'] = ' e.event_id = r.event_id AND r.raid_id = i.raid_id '; 
if ($query_by_pool)
{
	$sql_array['WHERE'] .= ' AND event_dkpid = ' . $dkp_id . ' ';
}
$sql = $db->sql_build_query('SELECT', $sql_array);
$result = $db->sql_query ( $sql);
$total_items = $db->sql_fetchfield ( 'itemcount');
$db->sql_freeresult ($result);

$start = request_var ( 'start', 0 );
switch ($mode)
{
	case 'values' :
		$u_list_items = append_sid ( "{$phpbb_root_path}dkp.$phpEx", 'page=listitems' );
		$listitems_footcount = sprintf ( $user->lang ['LISTITEMS_FOOTCOUNT'], $total_items, $config ['bbdkp_user_ilimit'] );
		break;
	case 'history' :
		$u_list_items = append_sid ( "{$phpbb_root_path}dkp.$phpEx", 'page=listitems&amp;mode=history' );
		$listitems_footcount = sprintf ( $user->lang ['LISTPURCHASED_FOOTCOUNT'], $total_items, $config ['bbdkp_user_ilimit'] );
		break;
}

$sort_order = array (
	0 => array ('item_date desc', 'item_date asc' ), 
	1 => array ('item_name asc', 'item_name desc' ), 
	2 => array ('event_name asc', 'event_name desc' ), 
	3 => array ('item_value desc', 'item_value asc' ) );

if ($mode == 'history')
{
	$sort_order[4] = array ('member_name asc', 'member_name desc' ); 	
}

$current_order = switch_order ($sort_order);

if ($query_by_pool)
{
	$pagination = generate_pagination ( $u_list_items . '&amp;' . URI_DKPSYS . '=' . $dkp_id . '&amp;o=' . $current_order ['uri'] ['current'] , 
	$total_items, $config ['bbdkp_user_ilimit'], $start, true );
} 
else
{
	$pagination = generate_pagination ( $u_list_items . '&amp;' . URI_DKPSYS . '=All&amp;o=' . $current_order ['uri'] ['current'] , 
	$total_items, $config ['bbdkp_user_ilimit'], $start, true );
}

switch ($mode)
{
	case 'values' :
		$sql_array = array (
			'SELECT' => '
				e.event_dkpid, e.event_name,  e.event_color, i.item_id, i.item_name, 
				i.member_id, i.item_gameid, i.item_date, i.raid_id, 
				MIN(i.item_value) AS item_value, 
				SUM(i.item_decay) as item_decay, 
				SUM(i.item_value - i.item_decay) as item_total, 
				SUM(item_zs) as item_zs  ', 
			'FROM' => array (
				EVENTS_TABLE => 'e', 
				RAIDS_TABLE => 'r', 
				RAID_ITEMS_TABLE => 'i', 
				), 
			'WHERE' => ' r.event_id = e.event_id AND i.raid_id = r.raid_id', 
			'GROUP_BY' => 'e.event_dkpid, e.event_name,  e.event_color, i.item_id, i.item_name, 
				i.member_id, i.item_gameid, i.item_date, i.raid_id ', 
			'ORDER_BY' => $current_order ['sql'] );
		
		break;
	
	case 'history' :
		$sql_array = array (
			'SELECT' => '
				 e.event_dkpid, e.event_name, e.event_color,   
				 i.raid_id, i.item_value, i.item_gameid, i.item_id, i.item_name, i.item_date, i.member_id, 
				 l.member_name, c.colorcode, c.imagename, c.class_id, l.member_gender_id, a.image_female, a.image_male, 
				 SUM(i.item_decay) as item_decay, 
				 SUM(i.item_value - i.item_decay) as item_total, 
				 SUM(item_zs) as item_zs ', 
    		'FROM' => array (
				EVENTS_TABLE => 'e', 
				RAIDS_TABLE => 'r', 
				RAID_ITEMS_TABLE => 'i', 
			    CLASS_TABLE	=> 'c', 
		        RACE_TABLE  		=> 'a',
				MEMBER_LIST_TABLE => 'l'), 

			'WHERE' => ' e.event_id = r.event_id  
					AND r.raid_id = i.raid_id
    				AND i.member_id = l.member_id
           			AND l.member_class_id = c.class_id
           			AND l.member_race_id =  a.race_id 
           			AND l.game_id = a.game_id
           			AND l.game_id = c.game_id', 
			'GROUP_BY' => 'e.event_dkpid, e.event_name, e.event_color,   
				 i.raid_id, i.item_value, i.item_gameid, i.item_id, i.item_name, i.item_date, i.member_id, 
				 l.member_name, c.colorcode, c.imagename, c.class_id, l.member_gender_id, a.image_female, a.image_male ', 
           	'ORDER_BY' => $current_order ['sql']);
		
		break;
}

if ($query_by_pool)
{
	$sql_array ['WHERE'] .= ' AND e.event_dkpid = ' . $dkp_id . ' ';
}

$sql = $db->sql_build_query ( 'SELECT', $sql_array );
$items_result = $db->sql_query_limit ( $sql, $config ['bbdkp_user_ilimit'], $start );


if (! $items_result)
{
	$user->add_lang ( array ('mods/dkp_admin' ) );
	trigger_error ( $user->lang ['ERROR_INVALID_ITEM_PROVIDED'], E_USER_WARNING );
}

$number_items = 0;
$item_value = 0.00;
$item_decay = 0.00;
$item_total = 0.00;

while ( $item = $db->sql_fetchrow ( $items_result ) )
{
	
	if ($bbDKP_Admin->bbtips == true)
	{

		if ($item['item_gameid'] > 0 )
		{
			$valuename = $bbtips->parse('[itemdkp]' . $item['item_gameid']  . '[/itemdkp]'); 
		}
		else 
		{
			$valuename = $bbtips->parse ( '[itemdkp]' . $item ['item_name'] . '[/itemdkp]' );
		}
				
	} 
	else
	{
		$valuename = $item ['item_name'];
	}


	if ($mode == 'history')
	{
		$race_image = (string) (($item['member_gender_id']==0) ? $item['image_male'] : $item['image_female']);
		
		$template->assign_block_vars ( 'items_row', array (
			'DATE' 			=> (! empty ( $item ['item_date'] )) ? date($config['bbdkp_date_format'], $item ['item_date'] ) : '&nbsp;', 
			'ITEMNAME' 		=> $valuename, 
			'U_VIEW_ITEM' 	=> append_sid ( "{$phpbb_root_path}dkp.$phpEx", "page=viewitem&amp;" . URI_ITEM . '=' . $item ['item_id'] ), 
			'RAID' 			=> (! empty ( $item ['event_name'] )) ? $item ['event_name'] : '&lt;<i>'. $user->lang['NOT_AVAILABLE'] .'</i>&gt;', 
			'U_VIEW_RAID' 	=> append_sid ( "{$phpbb_root_path}dkp.$phpEx", "page=viewraid&amp;" . URI_RAID . '=' . $item ['raid_id'] ), 
			'EVENTCOLOR' => ( !empty($item['event_color']) ) ? $item['event_color'] : '#123456',
			
			'ITEM_ZS'      	=> ($item['item_zs'] == 1) ? ' checked="checked"' : '',
			'ITEMVALUE' 	=> sprintf("%.2f", $item['item_value'])   ,
			'DECAYVALUE' 	=> sprintf("%.2f", $item['item_decay']),
			'TOTAL' 		=> sprintf("%.2f", $item['item_total']),
			'BUYER' 		=> $item ['member_name'], 
		
			'U_VIEW_BUYER' 	=> append_sid ( "{$phpbb_root_path}dkp.$phpEx", "page=viewmember&amp;" . URI_NAMEID . '=' . $item ['member_id'] . '&amp;' . URI_DKPSYS . '=' . $item ['event_dkpid'] ), 
			'RACE_IMAGE' 	=> (strlen($race_image) > 1) ? $phpbb_root_path . "images/race_images/" . $race_image . ".png" : '',  
			'S_RACE_IMAGE_EXISTS' => (strlen($race_image) > 1) ? true : false,

			'CLASSCOLOR' 	=> $item['colorcode'], 
			'CLASS_IMAGE' 	=> (strlen($item['imagename']) > 1) ? $phpbb_root_path . "images/class_images/" . $item['imagename'] . ".png" : '',  
			'S_CLASS_IMAGE_EXISTS' => (strlen($item['imagename']) > 1) ? true : false, 				
		));	
	}
	else 
	{
		$template->assign_block_vars ( 'items_row', array (
			'DATE' 			=> (! empty ( $item ['item_date'] )) ? date($config['bbdkp_date_format'], $item ['item_date'] ) : '&nbsp;', 
			'ITEMNAME' 		=> $valuename, 
			'U_VIEW_ITEM' 	=> append_sid ( "{$phpbb_root_path}dkp.$phpEx", "page=viewitem&amp;" .  URI_ITEM . '=' . $item ['item_id'] ), 
			'RAID' 			=> (! empty ( $item ['event_name'] )) ? $item ['event_name'] : '&lt;<i>Not Found</i>&gt;', 
			'EVENTCOLOR' => ( !empty($item['event_color']) ) ? $item['event_color'] : '#123456',
			'U_VIEW_RAID' 	=> append_sid ( "{$phpbb_root_path}dkp.$phpEx", "page=viewraid&amp;" . URI_RAID . '=' . $item ['raid_id'] ), 
			
			'ITEM_ZS'      	=> ($item['item_zs'] == 1) ? ' checked="checked"' : '',
			'ITEMVALUE' 	=> sprintf("%.2f", $item['item_value'])   ,
			'DECAYVALUE' 	=> sprintf("%.2f", $item['item_decay']),
			'TOTAL' 		=> sprintf("%.2f", $item['item_total']),
		));	
		
	}
	$number_items++; 
	$item_value += $item['item_value'];
	$item_decay += $item['item_decay'];
	$item_total += $item['item_total'];		

}
$db->sql_freeresult ( $items_result );

// breadcrumbs menu                                      
$navlinks_array = array (
	array (
	'DKPPAGE' => ($mode == 'history') ? $user->lang ['MENU_ITEMHIST'] : $user->lang ['MENU_ITEMVAL'], 
	'U_DKPPAGE' => $u_list_items ) );

foreach ( $navlinks_array as $name )
{
	$template->assign_block_vars ( 'dkpnavlinks', array (
	'DKPPAGE' => $name ['DKPPAGE'], 
	'U_DKPPAGE' => $name ['U_DKPPAGE'] ) );
}

$template->assign_vars ( array (
	'F_LISTITEM' => $u_list_items, 
	'O_DATE' => append_sid ( "{$phpbb_root_path}dkp.$phpEx", 'page=listitems&amp;mode=' . $mode . '&amp;o=' . $current_order ['uri'] [0] . '&amp;start=' . $start . '&amp;' . URI_DKPSYS . '=' . ($query_by_pool ? $dkp_id : 'All') ), 
	'O_NAME' => append_sid ( "{$phpbb_root_path}dkp.$phpEx", 'page=listitems&amp;mode=' . $mode . '&amp;o=' . $current_order ['uri'] [3] . '&amp;start=' . $start . '&amp;' . URI_DKPSYS . '=' . ($query_by_pool ? $dkp_id : 'All') ),  
	'O_RAID' => append_sid ( "{$phpbb_root_path}dkp.$phpEx", 'page=listitems&amp;mode=' . $mode . '&amp;o=' . $current_order ['uri'] [1] . '&amp;start=' . $start . '&amp;' . URI_DKPSYS . '=' . ($query_by_pool ? $dkp_id : 'All') ),
	'O_VALUE' => append_sid ( "{$phpbb_root_path}dkp.$phpEx", 'page=listitems&amp;mode=' . $mode . '&amp;o=' . $current_order ['uri'] [2] . '&amp;start=' . $start . '&amp;' . URI_DKPSYS . '=' . ($query_by_pool ? $dkp_id : 'All') ), 
	'S_HISTORY' => $s_history, 
	'S_SHOWZS' 		=> ($config['bbdkp_zerosum'] == '1') ? true : false, 
	'S_SHOWTIME' 	=> ($config['bbdkp_timebased'] == '1') ? true : false,
	'S_SHOWDECAY' 	=> ($config['bbdkp_decay'] == '1') ? true : false,
	'S_SHOWEPGP' 	=> ($config['bbdkp_epgp'] == '1') ? true : false,
	'TOTAL_ITEMVALUE' 	=> sprintf("%.2f",$item_value)   ,
	'TOTAL_ITEMDECAY' 	=> sprintf("%.2f", $item_decay),
	'TOTAL_ITEMTOTAL' 	=> sprintf("%.2f", $item_total),

	'LISTITEMS_FOOTCOUNT' => $listitems_footcount, 
	'ITEM_PAGINATION' => $pagination ,
	'S_DISPLAY_LISTITEMS' => true,
));

if ($mode == 'history')
{
	$template->assign_vars ( array (
	'O_BUYER' => append_sid ( "{$phpbb_root_path}dkp.$phpEx", 'page=listitems&amp;mode=' . $mode . '&amp;o=' . $current_order ['uri'] [4] . '&amp;start=' . $start . '&amp;' . URI_DKPSYS . '=' . ($query_by_pool ? $dkp_id : 'All') ))
);
	
}

$title = ($mode == 'history') ? $user->lang ['MENU_ITEMHIST'] : $user->lang ['MENU_ITEMVAL'];

// Output page
page_header ( $title );
?>