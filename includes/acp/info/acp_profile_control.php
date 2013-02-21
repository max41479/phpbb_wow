<?php

/**
*
* @package acp
* @version $Id: acp_profile_control.php v1.0.0 2009/11/21 12:53:34  Exp $
* @copyright (c) 2009 mtrs
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/
/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package module_install
*/

class acp_profile_control_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_profile_control',
			'title'		=> 'ACP_PROFILE_CONTROL',
			'version'	=> '0.0.5',
			'modes'		=> array(
				'profile_control'	=> array('title' => 'ACP_PROFILE_CONTROL', 'auth' => 'acl_a_profile', 'cat' => array('ACP_CAT_USERS'))),
		);
	}
}

?>