<?php
/*
*
* @author admin@teksonicmods.com
* @package lang_recruit_block_acp.php
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
	'RECRUIT'							=> 'Блок рекрутинга',
	'RECRUIT_EXPLAIN'					=> 'Раздел главных настроек блока рекрутинга.',
	'CLASS_EXPLAIN'						=> 'Раздел классовых настроек блока рекрутинга.',
	'REC_LEVEL'							=> 'Индикатор уровня рекрутинга',
	'REC_LEVEL_EXPLAIN'					=> 'Здесь вы можете выбрать какой вид подсказки использовать в блоке рекрутинга.<br><b>Уровень</b> - <i>none/Low/Med/High</i><br><b>Число</b> - <i>0,1,2,3 и т.д.</i><br><b>Оба</b> - <i>Low (1), High (15)</i>',
	'LEVEL' 							=> 'Уровень',
	'NUM' 								=> 'Число',
	'NUM_EXPLAIN'						=> '<code>(Допустимые числа: 0-99)</code>',
	'SUBMIT_EXPLAIN'					=> '<code>Любая кнопка "Отправить" сохранит всю введенную информацию.</code>',
	'BOTH' 								=> 'Оба',
	'YES'								=> 'Да',
	'NO'								=> 'Нет',
	'NONE'								=> 'None',
	'LOW'								=> 'Low',
	'MED'								=> 'Medium',
	'HIGH'								=> 'High',
	'BLOCK'								=> 'Показывать блок',
	'BLOCK_EXPLAIN'						=> 'Это может полностью скрыть блок если вы этого хотите',
	'IMAGES' 							=> 'Показывать иконки класса',
	'IMAGES_EXPLAIN'					=> 'Вы можете выбрать показывать ли иконки класса в блоке рекрутинга',
	'RECRUIT_FORUM'						=> 'Форум рекрутинга',
	'RECRUIT_FORUM_EXPLAIN'				=> 'Выберите форум, где размещаются заявки.',
	'CLASSCOLOR'						=> 'Использовать цвета класса',
	'CLASSCOLOR_EXPLAIN'				=> 'Это изменяет названия классов чтобы они были цветными.',
	'DEFAULT_LEVEL'						=> 'Уровень рекрутинга по умолчанию',
	'DEFAULT_LEVEL_EXPLAIN'				=> 'Setting this will default <strong>all</strong> classes/specs and superseeds the selections below, unless chossen selection is higher then the default.',
	'R_LINK'							=> 'Ссылка рекрутинга',
	'R_LINK_EXPLAIN'					=> 'Выберите куда будут перенаправлены соискатели.',
	'RECRUIT_LINK'						=> 'Своя ссылка рекрутинга',
	'RECRUIT_LINK_EXPLAIN'				=> 'Тут должна быть полная html ссылка. Включающая http://<br /><i>например: http://www.teksonicmods.com</i>',
		
	//Classes
	'DEATHKNIGHT'						=> 'Рыцарь смерти',
	'DRUID'								=> 'Друид',
	'HUNTER'							=> 'Охотник',
	'MAGE'								=> 'Маг',
	'PALADIN'							=> 'Паладин',
	'PRIEST'							=> 'Жрец',
	'ROGUE'								=> 'Разбойник',
	'SHAMAN'							=> 'Шаман',
	'WARLOCK'							=> 'Чернокнижник',
	'WARRIOR'							=> 'Воин',
	'MONK'								=> 'Монах',
	
	//Talents
	'BLOOD'								=> 'Кровь',
	'UNHOLY'							=> 'Нечастивость',
	'FROST'								=> 'Лед',	
	'BALANCE'							=> 'Баланс',
	'FERAL'								=> 'Сила зверя',
	'GUARDIAN'							=> 'Страж',
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
/**
* A copy of Handyman` s MOD version check, to view it on the Raid Progress Blocks main settings
*/
$lang = array_merge($lang, array(
	'ANNOUNCEMENT_TOPIC'						=> 'Объявление релиза',
	'CURRENT_VERSION'							=> 'Текущая версия',
	'DOWNLOAD_LATEST'							=> 'Скачать последнюю версию',
	'LATEST_VERSION'							=> 'Последняя версия',
	'NO_INFO'									=> 'Сервер проверки версии недоступен',
	'NOT_UP_TO_DATE'							=> '%s можно обновить',
	'RELEASE_ANNOUNCEMENT'						=> 'Топик объявления',
	'UP_TO_DATE'								=> '%s обновлен',
	'VERSION_CHECK'								=> 'Проверка версии',
));
?>