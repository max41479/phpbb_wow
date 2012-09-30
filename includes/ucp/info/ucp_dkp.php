<?php
/** 
*
* @package ucp
* @copyright (c) 2007 phpBB Group 
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

class ucp_dkp_info
{
	function module()
	{
		return array(
			'filename'	=> 'ucp_dkp',
			'title'		=> 'UCP_DKP',
			'version'	=> '1.2.8',
			'modes'		=> array(
				'characters'	=> array('title' => 'UCP_DKP_CHARACTERS', 'auth' => 'acl_u_dkp', 'cat' => array('UCP_DKP')),
				'characteradd'	=> array('title' => 'UCP_DKP_CHARACTER_ADD', 'auth' => 'acl_u_dkp', 'cat' => array('UCP_DKP')),				
				),
			);
	}
							
	function install()
	{
	}
								
	function uninstall()
	{
	}

}
?>