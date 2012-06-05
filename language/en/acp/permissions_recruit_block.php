<?php

/*
*
* @author admin@teksonicmods.com
* @package permission_recruit_block.php
* @version $Id: v2.0.1
* @copyright (c) Teksonic @ (www.teksonicmods.com)
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

// Adding new category
$lang['permission_cat']['raid'] = 'Raid';

// Adding new permission set
$lang['permission_type']['raid_'] = 'Raid Tools';

// Admin Permissions
$lang = array_merge($lang, array(
	'acl_a_recruit_block_manage'			=> array('lang' => 'Can alter Recruitment Block settings', 'cat' => 'raid'),
));

?>