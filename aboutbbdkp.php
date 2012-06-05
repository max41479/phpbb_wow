<?php
/**
 * bbdkp about popup
 * 
 * @package bbDKP
 * @copyright (c) 2009 bbDKP <https://github.com/bbDKP>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author ippeh

 * 
 */

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/dkp_common');
global $config, $db;

if(!defined("EMED_BBDKP"))
{
    trigger_error($user->lang['BBDKPDISABLED'], E_USER_WARNING); 
}
 // Build the data in arrays..
$template->assign_vars(array(
        'PBPBBVERSION' 		=> $config['version'],
        'BBDKPVERSION' 		=> $config['bbdkp_version'],
		'RTVERSION' 		=> (isset($config['bbdkp_raidtracker']) ? $config['bbdkp_raidtracker'] : ''),
		'APPLYVERSION' 		=> (isset($config['bbdkp_apply_version']) ? $config['bbdkp_apply_version'] : ''),
		'ARMORYVERSION' 	=> (isset($config['bbdkp_plugin_armoryupdater']) ? $config['bbdkp_plugin_armoryupdater'] : ''),
		'BBTIPSVERSION' 	=> (isset($config['bbdkp_plugin_bbtips_version']) ? $config['bbdkp_plugin_bbtips_version'] : ''),
));

$sql = "select * from " . PLUGINS_TABLE;
$result = $db->sql_query($sql);
while ( $row = $db->sql_fetchrow($result) )
{
	$template->assign_block_vars('plugins_row', array(
	            'NAME'        		 => $row['name'],
	            'VERSION'   		 => $row['version'],
	            'AUTHOR' 			 => $row['orginal_copyright'],
	            'MAINTAINER'    	 => $row['bbdkp_copyright'],)
	        );
}
$db->sql_freeresult($result);

$title = 'About';

// Output page
page_header($title);

$template->set_filenames(array(
	'body' => 'dkp/about.html')
);

page_footer();

?>
