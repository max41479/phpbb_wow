<?php
/**
 * returns race & class xml based on ajax call 
 * @package bbDkp.acp
 * @copyright (c) 2011 https://github.com/bbDKP
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * 
 * will return an xml like this 
 * <?xml version="1.0" encoding="UTF-8"?>
<document>
<racelist><race><race_id>0</race_id><race_name>Unknown</race_name></race><race><race_id>1</race_id><race_name>Human</race_name></race><race><race_id>2</race_id><race_name>Orc</race_name></race><race><race_id>3</race_id><race_name>Dwarf</race_name></race><race><race_id>4</race_id><race_name>Night Elf</race_name></race><race><race_id>5</race_id><race_name>Undead</race_name></race><race><race_id>6</race_id><race_name>Tauren</race_name></race><race><race_id>7</race_id><race_name>Gnome</race_name></race><race><race_id>8</race_id><race_name>Troll</race_name></race><race><race_id>9</race_id><race_name>Goblin</race_name></race><race><race_id>10</race_id><race_name>Blood Elf</race_name></race><race><race_id>11</race_id><race_name>Draenei</race_name></race><race><race_id>22</race_id><race_name>Worgen</race_name></race></racelist>
<classlist><class><class_id>0</class_id><class_name>Unknown</class_name></class><class><class_id>1</class_id><class_name>Warrior</class_name></class><class><class_id>2</class_id><class_name>Paladin</class_name></class><class><class_id>3</class_id><class_name>Hunter</class_name></class><class><class_id>4</class_id><class_name>Rogue</class_name></class><class><class_id>5</class_id><class_name>Priest</class_name></class><class><class_id>6</class_id><class_name>Death Knight</class_name></class><class><class_id>7</class_id><class_name>Shaman</class_name></class><class><class_id>8</class_id><class_name>Mage</class_name></class><class><class_id>9</class_id><class_name>Warlock</class_name></class><class><class_id>11</class_id><class_name>Druid</class_name></class>
</classlist>
</document>
 * 
 */
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../../../../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
$game_id = request_var('game_id', '');
//first get the races-species
$sql_array = array(
	'SELECT'	=>	'  r.race_id, l.name as race_name ', 
	'FROM'		=> array(
			RACE_TABLE		=> 'r',
			BB_LANGUAGE		=> 'l',
			),
	'WHERE'		=> " r.race_id = l.attribute_id 
					AND r.game_id = '" . $game_id . "' 
					AND l.attribute='race' 
					AND l.game_id = r.game_id 
					AND l.language= '" . $config['bbdkp_lang'] ."'",
	'ORDER_BY'	=> 'l.name',
	);
$sql = $db->sql_build_query('SELECT', $sql_array);
$result = $db->sql_query($sql);
//now get classes
$sql_array = array(
	'SELECT'	=>	' c.class_id, l.name as class_name ', 	 
	'FROM'		=> array(
			CLASS_TABLE		=> 'c',
			BB_LANGUAGE		=> 'l', 
			),
	'WHERE'		=> " l.game_id = c.game_id AND c.game_id = '" . $game_id . "' 
	AND l.attribute_id = c.class_id  AND l.language= '" . $config['bbdkp_lang'] . "' AND l.attribute = 'class' ",					 
	);
$sql = $db->sql_build_query('SELECT', $sql_array);		
$result1 = $db->sql_query($sql);					
header('Content-type: text/xml');
// preparing xml
$xml = '<?xml version="1.0" encoding="UTF-8"?>
<document>
<racelist>';
while ( $row = $db->sql_fetchrow($result)) 
{
	 $xml .= '<race>'; 
	 $xml .= "<race_id>" . $row['race_id'] . "</race_id>";
	 $xml .= "<race_name>" . $row['race_name'] . "</race_name>";
	 $xml .= '</race>'; 	 
}
$xml .= '</racelist>';
$xml .= '
<classlist>';
while ( $row1 = $db->sql_fetchrow($result1)) 
{
	 $xml .= '<class>'; 
	 $xml .= "<class_id>" . $row1['class_id'] . "</class_id>";
	 $xml .= "<class_name>" . $row1['class_name'] . "</class_name>";
	 $xml .= '</class>'; 	 
}
$xml .= '
</classlist>
</document>';
$db->sql_freeresult($result);
$db->sql_freeresult($result1);
//return xml to ajax
echo($xml); 
?>