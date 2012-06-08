<?php
/**
* This class manages Game, Race and Class 
* 
* Powered by bbdkp © 2009 The bbDKP Project Team
* If you use this software and find it to be useful, we ask that you
* retain the copyright notice below.  While not required for free use,
* it will help build interest in the bbDKP project.
*
* @package bbDKP.acp
* @version $Id$
* @copyright (c) 2009 bbdkp https://github.com/bbDKP
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*   'ACP_DKP_GAME'			=> 'Game, Race, Class',
*	'ACP_DKP_GAME_ADD'		=> 'Ajouter Game',
*	'ACP_DKP_RACE_ADD'		=> 'Ajouter Race',
*	'ACP_DKP_CLASS_ADD'		=> 'Ajouter Classe',  
*	'ACP_DKP_GAME_LIST'		=> 'Liste',
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


class acp_dkp_game_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_dkp_game',
			'title'		=> 'ACP_DKP_GAME',
			'version'	=> '1.2.7',
			'modes'		=> array(
				'listgames'		=> array('title' => 'ACP_DKP_GAME_LIST',  'auth' => 'acl_a_dkp', 'cat' => array('ACP_DKP_GAME') , 'display' => true),
				'addfaction'	=> array('title' => 'ACP_DKP_FACTION_ADD',   'auth' => 'acl_a_dkp', 'cat' => array('ACP_DKP_GAME') , 'display' => false),
				'addrace'		=> array('title' => 'ACP_DKP_RACE_ADD',   'auth' => 'acl_a_dkp', 'cat' => array('ACP_DKP_GAME') , 'display' => false),
				'addclass'		=> array('title' => 'ACP_DKP_CLASS_ADD',  'auth' => 'acl_a_dkp', 'cat' => array('ACP_DKP_GAME') , 'display' => false),
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
