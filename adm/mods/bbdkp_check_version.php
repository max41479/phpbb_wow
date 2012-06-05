<?php
/**
*
* @package acp
* @copyright (c) 2007 StarTrekGuide
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package mod_version_check
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

class bbdkp_check_version
{
	function version()
	{
		global $config, $phpbb_root_path, $phpEx;
		
		return array(
			'author'	=> 'Sajaki',
			'title'		=> 'bbDKP',
			'tag'		=> 'bbDKP',
			'version'	=> (isset($config['bbdkp_version']) ? $config['bbdkp_version'] : 'not installed'), 
			'file'		=> array('bbdkp.com', 'versioncheck', 'bbdkp_versioncheck.xml'),
		);
	}
}

?>