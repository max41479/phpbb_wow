<?php
/**
* This class manages Items 
* 
* Powered by bbdkp, ported from Eqdkp
* If you use this software and find it to be useful, we ask that you
* retain the copyright notice below.  While not required for free use,
* it will help build interest in the bbDKP project.
*
* @package bbDKP.acp
* @author ippehe
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


class acp_dkp_item_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_dkp_item',
			'title'		=> 'ACP_DKP_ITEM',
			'version'	=> '1.2.8',
			'modes'		=> array(
				'edititem'			=> array('title' => 'ACP_DKP_ITEM_EDIT', 'auth' => 'acl_a_dkp', 'cat' => array('ACP_DKP_RAIDS'), 'display' => false ),
				'listitems'			=> array('title' => 'ACP_DKP_ITEM_LIST', 'auth' => 'acl_a_dkp', 'cat' => array('ACP_DKP_RAIDS') , 'display' => true ),
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
