<?php
/**
* This class manages member DKP
* 
* @package bbDKP.acp
* @author sajaki9@gmail.com
* @version $Id$
* @copyright (c) 2009 bbdkp https://github.com/bbDKP
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
class acp_dkp_mdkp_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_dkp_mdkp',
			'title'		=> 'ACP_DKP_MDKP',
			'version'	=> '1.2.7',
			'modes'		=> array(
				'mm_editmemberdkp'	=> array('title' => 'ACP_DKP_EDITMEMBERDKP',  'auth' => 'acl_a_dkp', 'cat' => array('ACP_DKP_MDKP'), 'display' => false ),
				'mm_listmemberdkp'	=> array('title' => 'ACP_DKP_LISTMEMBERDKP', 'auth' => 'acl_a_dkp',  'cat' => array('ACP_DKP_MDKP'), 'display' => true),
		        'mm_transfer'	    => array('title' => 'ACP_DKP_MEMBER_TRF', 'auth' => 'acl_a_dkp', 'cat' => array('ACP_DKP_MEMBER')),
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
