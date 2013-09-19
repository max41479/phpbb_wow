<?php
/**
* DO NOT CHANGE
*/
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
	'ERROR_MAX_STREAMS_EXCEEDED'			=> 'Извините, вы можете прикрепить только %s стрима(ов) к вашему phpBB аккаунту.',
	'NO_UCP_DEL_STREAMS'			=> 'Вы не можете удалять стримы.',
	'NO_UCP_UPD_STREAMS'			=> 'Вы не можете обновлять стримы.',
	'NO_UCP_ADD_STREAMS'			=> 'Вы не можете добавлять стримы.',
	'MY_STREAMS'					=> 'Мои стримы',
	'ADD_EDIT_STREAM'				=> 'Добавление/редактирование стрима',
	'UPDATE_STREAM'					=> 'Обновить стрим',
	'DELETE_STREAM'					=> 'Удалить стрим',
	'ADD_STREAM'					=> 'Добавить стрим',
	'ADD_STREAM_SUCCESS'			=> 'Ваш стрим был успешно добавлен: %s',
	'ADD_STREAM_FAIL'				=> 'При добавлении вашего стрима(%s) возникла ошибка.',
	'DELETE_STREAM_FAIL_NOT_EXISTS'	=> 'При удалении вашего стрима возникла ошибка: стрим с ID %s не существует.',
	'DELETE_STREAM_SUCCESS'			=> 'Стрим %s был успешно удален из базы данных.',
	'UPDATE_STREAM_FAIL'			=> 'При обновлении вашего стрима возникла ошибка: Вы не внесли никаких изменений.',
	'UPDATE_STREAM_SUCCESS'			=> 'Стрим %s, был успешно обновлен.',
	'ERROR_STREAM_NOT_EXIST'		=> 'Ошибка: стрима с ID %s не существует.',
	'CONFIRM_DELETE_STREAM'			=> 'Вы уверены что хотите удалить стрим %s ?',
	'MY_STREAMS_LIST'				=> 'Список ваших стримов',
	'EDIT'							=> 'Редактировать',
	'STREAM_EDITING'				=> 'Редактирование стрима',
	'DELETE_STREAM_FAIL_NO_OWNWER'	=> 'При удалении вашего стрима возникла ошибка: стрим с ID %s не ваш.',
	'UPDATE_STREAM_FAIL_NO_OWNWER'	=> 'При обновлении вашего стрима возникла ошибка: стрим с ID %s не ваш.',
	'ERROR_NO_STREAMS'				=> 'За вашей учетной записью phpBB не закреплено ни одного стрима',
	'STREAM_PLATFORM'				=> 'Сервер стрима',
	'CHANNEL_NAME'					=> 'Название канала',
	'STREAM_DESCRIPTION_TITLE'		=> 'Описание стрима :',
	'CHANNEL_NAME_EXPLAIN'			=> 'Например, если Ваш стрим доступен по ссылке - http://www.twitch.tv/<b>tonyhowk2</b>,<br />то названием канала будет <b>tonyhowk2</b> :',
	'STREAM_LINK'					=> 'Ссылка на тему форума с обсуждением вашего стрима :',
	));
?>