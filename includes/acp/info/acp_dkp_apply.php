<?php
/**
* This acp manages Guild Applications 
* Application form created by Kapli (bbDKP developer)
*
* @package bbDkp.acp
* @version $Id$
* @copyright (c) 2009 bbdkp http://code.google.com/p/bbdkp/
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
class acp_dkp_apply_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_dkp_apply',
			'title'		=> 'ACP_DKP_APPLY',
			'version'	=> '1.3.3',
			'modes'		=> array(
					'apply_settings'	=> array(
						'title' => 'ACP_DKP_APPLY', 
						'display' => 1, 
						'auth' => 'acl_a_dkp', 
						'cat' => array('ACP_DKP')),
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
