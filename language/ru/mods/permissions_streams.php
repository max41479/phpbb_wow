<?php
/** 
*
* @author max41479 cod41479@list.ru
* @package ucp
* @version 1.0.0
* @copyright (c) 2007 phpBB Group 
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

// Define categories 
$lang['permission_cat']['streams'] = 'Streams';

// bbDKP Permissions
$lang = array_merge($lang, array(
	'acl_u_view_streams'		=> array('lang' => 'Может видеть свои стримы в UCP', 'cat' => 'streams'),
	'acl_u_manage_streams'		=> array('lang' => 'Может управлять своими стримами в UCP', 'cat' => 'streams'),
	'acl_u_add_stream'			=> array('lang' => 'Может добавлять свои стримы в UCP', 'cat' => 'streams'),
	'acl_u_delete_stream'		=> array('lang' => 'Может удалять свои стримы в UCP', 'cat' => 'streams'),
	'acl_u_edit_stream'			=> array('lang' => 'Может редактировать свои стримы в UCP', 'cat' => 'streams'),
));

?>
