<?php
/**
 * bbdkp ucp language file 
 * 
 * @package bbDKP
 * @copyright 2010 bbdkp <https://www.github.com/bbDKP>
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

// Create the lang array if it does not already exist
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// Merge the following language entries into the lang array
$lang = array_merge($lang, array(
    'UCP_DKP_CHARACTERS'		=> 'Characters',
	'UCP_DKP_CHARACTER_LIST'	=> 'My Characters',
	'UCP_DKP_CHARACTER_ADD'		=> 'Add Character',
	'UCP_DKP'					=> 	'bbDKP Userpanel',  

));

?>