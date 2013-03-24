<?php
/**
 * Battle.net WoW API PHP SDK
 *
 * This software is not affiliated with Battle.net, and all references
 * to Battle.net and World of Warcraft are copyrighted by Blizzard Entertainment.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package   bbDKP-WOWAPI
 * @author	  Chris Saylor
 * @author	  Daniel Cannon <daniel@danielcannon.co.uk>
 * @author	  Andy Vandenberghe <sajaki9@gmail.com> 
 * @copyright Copyright (c) 2011, Chris Saylor, Daniel Cannon,  Andy Vandenberghe
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link	  https://github.com/bbDKP/WoWAPI
 * @version   1.0.4  
 */


/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

// Merge the following language entries into the lang array
$lang = array_merge($lang, array(

	//settings 
    'ERR_UNKNOWN'					=> 'Unknown error occurred.',
    'CURL_REQUIRED'					=> 'Curl is required for SDK usage.',
	'JSON_REQUIRED'					=> 'JSON PHP extension required for SDK usage.', 
	'NO_METHODS' 					=> 'No methods defined in this resource.',
	'WOWAPI_METH_NOTALLOWED'		=> 'Method not allowed.',
	'WOWAPI_REGION_NOTALLOWED'		=> 'Region not allowed.',		
	'WOWAPI_API_NOTIMPLEMENTED'		=> 'API not allowed.',
	'WOWAPI_NO_REALMS' 				=> 'No realm specified.', 
	'WOWAPI_NO_GUILD'				=> 'Guildname name not specified.',
	'WOWAPI_INVALID_FIELD'			=> 'Invalid field requested : %s',
	'WOWAPIERR400'					=> '400 Bad request',
	'WOWAPIERR401'					=> '401 Unauthorised',
	'WOWAPIERR403'					=> '403 Forbidden', 
	'WOWAPIERR404'					=> '404 Not Found', 
	'WOWAPIERR500'					=> '500 Internal Server Error',
 	'WOWAPIERR501'					=> '501 Not Implemented',
 	'WOWAPIERR502'					=> '502 Bad Gateway',
	'WOWAPIERR503'					=> '503 Service Unavailable',
	'WOWAPIERR504'					=> '504 Gateway Timeout',
	'WOWAPIERROTH'					=> 'Other error',
 	'WOWAPI_NO_CHARACTER'			=> 'Character name not specified.',
	'CURL_NOT_AVAILABLE'			=> 'Curl is not available on your server. :< ',
	
	
));

?>