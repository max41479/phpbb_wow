<?php
/**
 * returns item xml based on ajax call 
 * @package bbDkp.acp
 * @copyright (c) 2011 bbDkp <https://github.com/bbDKP>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License

 */
define('IN_PHPBB', true);
define('ADMIN_START', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../../../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
// handle GET request
$item_name = utf8_normalize_nfc(request_var('itemSearch', '', true)); 
$sql = 'SELECT item_name, item_gameid, item_value   
		FROM ' . RAID_ITEMS_TABLE . ' WHERE  
		item_name  '. $db->sql_like_expression($db->any_char . $item_name . $db->any_char)  . ' 
		GROUP BY item_name, item_gameid,item_value
		ORDER BY item_name asc';
$result = $db->sql_query($sql);
header('Content-type: text/xml');
// preparing xml
$xml = '<?xml version="1.0" encoding="UTF-8"?>
<itemlist>';
while ( $row = $db->sql_fetchrow($result)) 
{
	 $xml .= '<item>'; 
	 $xml .= "<item_name>" . $row['item_name'] . "</item_name>";
	 $xml .= "<item_gameid>" . $row['item_gameid'] . "</item_gameid>";
	 $xml .= "<item_value>" . $row['item_value'] . "</item_value>";
	 $xml .= '</item>'; 	 
}
$xml .= '</itemlist>';
$db->sql_freeresult($result);
//return xml to ajax
echo($xml); 
?>
