<?php
/**
*
* @package acp
* @version $Id: rep_version_check.php 48 2007-09-23 20:23:14Z Handyman $
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package recruit_version_check
*/
class recruit_version_check
{
	function version()
	{
		return array(
			'author'	=> 'Teksonic',
			'title'		=> 'Recruitment Block',
			'tag'		=> 'recruit_block',
			'version'	=> '2.1.0',
			'file'		=> array('dev.teksonicmods.com', 'ver', 'recruit_block.xml'),
		);
	}
}

?>