<?php
/**
* This acp manages Guild Applications 
* Application form created by Kapli (bbDKP developer)
*
* @package bbDkp.acp
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
			'version'	=> '1.4.1',
			'modes'		=> array(
					'apply_settings'	=> array(
						'title' => 'ACP_DKP_APPLY', 
						'display' => 1, 
						'auth' => 'acl_a_dkp', 
						'cat' => array('ACP_DKP')),
					'apply_edittemplate'	=> array(
						'title' => 'ACP_DKP_APPLY_TEMPLATE_EDIT', 
						'display' => 0, //is hidden 
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
