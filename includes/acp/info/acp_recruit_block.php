<?php
/*
*
* @author admin@teksonicmods.com
* @package acp_recruit_block.php
* @version $Id: v2.1.0
* @copyright (c) Teksonic @ (www.teksonicmods.com)
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

class acp_recruit_block_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_recruit_block',
			'title'		=> 'ACP_RECRUIT_BLOCK_INFO',
			'version'	=> '2.0.2',
			'modes'		=> array(
				'options'		=> array('title' => 'ACP_RB_MAIN_OPTIONS_INFO', 'auth' => 'acl_a_recruit_block_manage', 'cat' => array('ACP_RECRUIT_BLOCK_INFO')),
				'classes'		=> array('title' => 'ACP_RB_CLASSES_INFO', 'auth' => 'acl_a_recruit_block_manage', 'cat' => array('ACP_RECRUIT_BLOCK_INFO')),
			),
		);
	}
}
?>