<?php
/**
 * loot block
 * 
 * @package bbDkp
 * @copyright 2009 bbdkp <https://github.com/bbDKP>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * 
 */

if (!defined('IN_PHPBB'))
{
   exit;
}

$bbDkp_Admin = new bbDkp_Admin;
if ($bbDkp_Admin->bbtips == true)
{
	if ( !class_exists('bbtips')) 
	{
		require($phpbb_root_path . 'includes/bbdkp/bbtips/parse.' . $phpEx); 
	}
	$bbtips = new bbtips;
}

$n_items = $config['bbdkp_n_items'];

/**  begin loot block ***/
$sql = "SELECT item_name, item_gameid FROM " . RAID_ITEMS_TABLE . ' ORDER BY item_date DESC ';
$result = $db->sql_query_limit($sql, $n_items, 0);
while ($row = $db->sql_fetchrow($result))
{         
	if ($bbDkp_Admin->bbtips == true)
	{
		if ($row['item_gameid'] > 0 )
		{
			$item_name = $bbtips->parse('[itemdkp]' . $row['item_gameid']  . '[/itemdkp]'); 
		}
		else 
		{
			$item_name = $bbtips->parse('[itemdkp]' . $row['item_name']  . '[/itemdkp]');
		}
	}
	else
	{
		$item_name = $row['item_name'];
	}
	$template->assign_block_vars('itemit', array(
	    'ITEMI1' => $item_name, 
	));  
}

$db->sql_freeresult($result);
$template->assign_vars(array(
	'S_DISPLAY_LOOT' 	=> true, 
));

/**  end loot block ***/



?>