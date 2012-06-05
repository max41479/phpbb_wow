<?php
/**
 * returns rank xml based on ajax call 
 * @package bbDkp.acp
 * @copyright (c) 2009 bbDkp <https://github.com/bbDKP>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License

 */
define('IN_PHPBB', true);
define('ADMIN_START', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../../../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

$game_id = request_var('game_id', '');

$sql = "SELECT faction_id, faction_name FROM " . FACTION_TABLE . " where game_id = '" . $game_id . "' order by faction_id";
$result = $db->sql_query($sql);
header('Content-type: text/xml');
// preparing xml
$xml = '<?xml version="1.0" encoding="UTF-8"?>
<factionlist>';
while ( $row = $db->sql_fetchrow($result)) 
{
	 $xml .= '<faction>'; 
	 $xml .= "<faction_id>" . $row['faction_id'] . "</faction_id>";
	 $xml .= "<faction_name>" . $row['faction_name'] . "</faction_name>";
	 $xml .= '</faction>'; 	 
}
$xml .= '</factionlist>';
$db->sql_freeresult($result);
//return xml to ajax
echo($xml); 
?>
