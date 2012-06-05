<?php
/**
*
* auto_backup [English]
*
* @package language
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'ACP_AUTO_BACKUP_INDEX_TITLE'		=> 'Auto Backup',
	'ACP_AUTO_BACKUP'					=> 'Auto Backup',
	'ACP_AUTO_BACKUP_SETTINGS'			=> 'Настройки Auto Backup',
	'ACP_AUTO_BACKUP_SETTINGS_EXPLAIN'	=> 'Тут вы может задать все настройки по умолчанию для Auto Backup. Все резервные копии будут созданы в директории <samp>store/</samp>. В зависимости от конфигурации вашего сервера вам может быть доступно сжатие резервной копии в нескольких форматах. Вы можете восстановить резервную копию через модуль <em>Restore</em>.',
	'LOG_AUTO_BACKUP_SETTINGS_CHANGED'	=> '<strong>Изменены настройки Auto Backup</strong>',
	'AUTO_BACKUP_SETTINGS_CHANGED'		=> 'Настройки Auto Backup Settings изменены.',
	'AUTO_BACKUP_ENABLE'				=> 'Включить Auto Backup',
	'AUTO_BACKUP_ENABLE_EXPLAIN'		=> 'Вы можете включить\отключить Auto Backup в любой момент.',
	'AUTO_BACKUP_FREQ'					=> 'Частота создания резервных копий',
	'AUTO_BACKUP_FREQ_EXPLAIN'			=> 'Задайте частоту создания резервных копий. Значение должно быть больше 0.',
	'AUTO_BACKUP_FREQ_ERROR'			=> 'Введено некорректное значение для частоты создания резервных копий.<br />Значение должно бы больше <strong>0</strong>.',
	'AUTO_BACKUP_COPIES'				=> 'Копий хранится',
	'AUTO_BACKUP_COPIES_EXPLAIN'		=> 'Число резервных копий, которые будут храниться на сервере. Значение 0 отключает удаление старых копий.',
	'AUTO_BACKUP_COPIES_ERROR'			=> 'Введено некорректное значение для числа резервных копий.<br />Значение должно быть больше или равно <strong>0</strong>.',
	'AUTO_BACKUP_FILETYPE'				=> 'Тип файла',
	'AUTO_BACKUP_FILETYPE_EXPLAIN'		=> 'Выберите тип файла для резервной копии.',
	'AUTO_BACKUP_GZIP'					=> 'gzip',
	'AUTO_BACKUP_BZIP2'					=> 'bzip2',
	'AUTO_BACKUP_TEXT'					=> 'text',
	'AUTO_BACKUP_NEXT'					=> 'Следующая копия',
	'AUTO_BACKUP_NEXT_EXPLAIN'			=> 'Следующая резервная копия будет создана',
	'AUTO_BACKUP_TIME'					=> 'Время для создания копии',
	'AUTO_BACKUP_TIME_EXPLAIN'			=> 'Задайте время, в которое должна создаваться резервная копия (год-месяц-день-час-минута).<br />Примечание: вы должны задать момент времени в будущем',
	'AUTO_BACKUP_TIME_ERROR'			=> 'Введено некорректное время создания резервных копий.<br />Значение часов должно быть меньше <strong>24</strong>.<br />Значение минут должно быть меньше <strong>60</strong>.',
	'AUTO_BACKUP_DATE_TIME'				=> 'YYYY-MM-DD-hh-mm',
	'AUTO_BACKUP_OPTIMIZE'				=> 'Оптимизировать DB перед резервным копированием',
	'AUTO_BACKUP_OPTIMIZE_EXPLAIN'		=> 'Оптимизировать только неоптимизированные таблицы перед резервным копированием DB.',
	
));

?>