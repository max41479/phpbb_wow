<?php
if (!defined('IN_PHPBB'))
{
	exit;
}

class acp_pbwow2_info
{
    function module()
    {
        return array(
			'filename'	=> 'acp_pbwow2',
			'title'		=> 'ACP_PBWOW2_CATEGORY',
			'version'	=> '2.0.6',
			'modes'		=> array(
				'overview'		=> array('title' => 'ACP_PBWOW2_OVERVIEW', 'auth' => 'acl_a_board', 'cat' => array('ACP_PBWOW2_CATEGORY')),
				'config'		=> array('title' => 'ACP_PBWOW2_CONFIG', 'auth' => 'acl_a_board', 'cat' => array('ACP_PBWOW2_CATEGORY')),
				'poststyling'	=> array('title' => 'ACP_PBWOW2_POSTSTYLING', 'auth' => 'acl_a_board', 'cat' => array('ACP_PBWOW2_CATEGORY')),
				'ads'			=> array('title' => 'ACP_PBWOW2_ADS', 'auth' => 'acl_a_board', 'cat' => array('ACP_PBWOW2_CATEGORY')),
            ),
        );
    }
}
?>