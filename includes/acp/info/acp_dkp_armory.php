<?php
/**
 * Armory ACP
 * 
 * @package bbDkp.acp
 * @author ippehe
 * @copyright 2009 bbdkp https://github.com/bbDKP/Armory-Importer
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
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

class acp_dkp_armory_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_dkp_armory',
			'title'		=> 'ACP_DKP_MEMBER',
			'version'	=> '1.1.8',
			'modes'		=> array(
				'armory'			=> array('title' => 'ACP_DKP_ARMORY', 'auth' => 'acl_a_dkp', 'cat' => array('ACP_DKP_MEMBER')),
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
