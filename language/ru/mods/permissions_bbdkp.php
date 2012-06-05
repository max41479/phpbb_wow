<?php
/**
 * bbDKP Permission Set English
 * 
 * @author sajaki
 * @package bbDKP
 * @copyright 2009 bbdkp <https://github.com/bbDKP>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * 
 */

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// Define categories 
$lang['permission_cat']['bbdkp'] = 'bbDKP';

// Adding new permission set
$lang['permission_type']['bbdkp_'] = 'bbDKP Permissions';


// bbDKP Permissions
$lang = array_merge($lang, array(
	'acl_a_dkp'		=> array('lang' => 'Can access bbDKP ACP', 'cat' => 'bbdkp'),
	'acl_u_dkp'		=> array('lang' => 'Can see DKP pages', 'cat' => 'bbdkp'),
	'acl_u_dkpucp'	=> array('lang' => 'Can claim characters in UCP', 'cat' => 'bbdkp'),
	'acl_u_dkp_charadd'	=> array('lang' => 'Can add own characters in UCP', 'cat' => 'bbdkp'),
	'acl_u_dkp_charupdate'	=> array('lang' => 'Can update own characters in UCP', 'cat' => 'bbdkp'),
	'acl_u_dkp_chardelete'	=> array('lang' => 'Can delete own characters in UCP', 'cat' => 'bbdkp'),
));

?>
