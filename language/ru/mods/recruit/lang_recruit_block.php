<?php
/*
*
* @author admin@teksonicmods.com
* @package lang_recruit_block.php
* @version $Id: v2.1.0
* @copyright (c) Teksonic @ (www.teksonicmods.com)
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	// Recruitment
	'RECRUIT'							=> 'Заявка',
	'REC_CLOSED'						=> 'Recruitment is currently closed! Thank you for your interest.',
		
	//Classes
	'DK'								=> 'Рыцарь&nbsp;смерти',
	'DRUID'								=> 'Друид',
	'HUNTER'							=> 'Охотник',
	'MAGE'								=> 'Маг',
	'PALADIN'							=> 'Паладин',
	'PRIEST'							=> 'Жрец',
	'ROGUE'								=> 'Разбойник',
	'SHAMAN'							=> 'Шаман',
	'WARLOCK'							=> 'Чернокнижник',
	'WARRIOR'							=> 'Воин',
	'MONK'							=> 'Монах',
	
	//Talents
	'BLOOD'								=> 'Кровь',
	'UNHOLY'							=> 'Нечестивость',
	'FROST'								=> 'Лед',	
	'BALANCE'							=> 'Баланс',
	'FERAL'								=> 'Сила зверя',
	'RESTORATION'						=> 'Исцеление',	
	'BEASTMASTERY'						=> 'Повелитель зверей',
	'MARKSMANSHIP'						=> 'Стрельба',
	'SURVIVAL'							=> 'Выживание',	
	'ARCANE'							=> 'Тайная магия',
	'FIRE'								=> 'Огонь',
	'HOLY'								=> 'Свет',
	'PROTECTION'						=> 'Защита',
	'RETRIBUTION'						=> 'Воздаяние',	
	'DISCIPLINE'						=> 'Послушание',
	'SHADOW'							=> 'Тьма',	
	'ASSASSINATION'						=> 'Ликвидация',
	'COMBAT'							=> 'Бой',
	'SUBTLETY'							=> 'Скрытность',	
	'ELEMENTAL'							=> 'Стихии',
	'ENHANCEMENT'						=> 'Совершенствование',
	'AFFLICTION'						=> 'Колдовство',
	'DEMONOLOGY'						=> 'Демонология',
	'DESTRUCTION'						=> 'Разрушение',	
	'ARMS'								=> 'Оружие',
	'FURY'								=> 'Неистовство',
	'BREWMASTER'						=> 'Brewmaster',	
	'WINDWALKER'						=> 'Windwalker',
	'MISTWEAVER'						=> 'Mistweaver',
	)
);
?>