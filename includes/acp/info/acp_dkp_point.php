<?php
/**
* 
* @package bbDKP.acp
* @author Sajaki
* @copyright (c) 2012 bbdkp https://github.com/bbDKP
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

class acp_dkp_point_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_dkp_point',
			'title'		=> 'ACP_DKP_POINT_CONFIG',
			'version'	=> '1.2.7',
			'modes'		=> array(
				'pointconfig'			=> array(
					'title' => 'ACP_DKP_POINT_CONFIG', 	
					'auth' => 'acl_a_dkp', 
					'cat' => array('ACP_DKP_MAINPAGE')),
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
