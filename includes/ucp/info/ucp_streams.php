<?php
/** 
*
* @author max41479 cod41479@list.ru
* @package ucp
* @version 1.0.0
* @copyright (c) 2007 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/
                            
/**
* @package module_install
*/
class ucp_streams_info
{
    function module()
    {
    return array(
        'filename'    => 'ucp_streams',
        'title'        => 'UCP_STREAMS',
        'version'    => '1.0.0',
        'modes'        => array(
            'my_streams'			=> array('title' => 'MY_STREAMS', 'auth' => 'acl_u_view_streams', 'cat' => array('STREAMS')),
			'manage_streams'	=> array('title' => 'MANAGE_STREAMS', 'auth' => 'acl_u_manage_streams', 'cat' => array('STREAMS')),
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