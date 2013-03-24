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
 * @link 	  http://blizzard.github.com/api-wow-docs
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

/**
 * Battle.net WoW API PHP SDK
 *
 * @throws Exception If requirements are not met.
 */
class WowAPI 
{
	protected $region = array(
		'us', 'eu', 'kr', 'tw', 'cn', 'sea'
	);
		
	protected $API = array(
		'guild', 'realm', 'character'
	);


	/**
	 * Realm object instance
	 *
	 */
	public $Realm;
	
	/**
	 * Guild object instance
	 *
	 * @var class
	 */
	public $Guild;

	
	/**
	 * Character object instance
	 *
	 * @var class
	 */
	public $Character;
	
	/**
	 * WoWAPI Class constructor
	 * 
	 * @param string $resource, 
	 * @param string $region
	 * 
	 */
	public function __construct($API, $region) 
	{
		global $user, $phpEx, $phpbb_root_path; 
		$user->add_lang ( array ('mods/wowapi'));
		
		// check for correct API call
		if (!in_array($API, $this->API)) 
		{
			trigger_error($user->lang['WOWAPI_API_NOTIMPLEMENTED']);
		}
		
		if (!in_array($region, $this->region))
		{
			trigger_error($user->lang['WOWAPI_REGION_NOTALLOWED']);
		}
		
		// Check for required extensions
		if (!function_exists('curl_init')) 
		{
			trigger_error($user->lang['CURL_REQUIRED'], E_USER_WARNING);

		}

		if (!function_exists('json_decode')) 
		{
			trigger_error($user->lang['JSON_REQUIRED'], E_USER_WARNING);			
		}
		
		
		switch ($API)
		{
			case 'realm':
				if (!class_exists('Realm')) 
				{
					require($phpbb_root_path . "includes/bbdkp/wowapi/API/Realm.$phpEx");
				}
				$this->Realm = new Realm($region);
				break;
			case 'guild':
				if (!class_exists('Guild')) 
				{
					require($phpbb_root_path . "includes/bbdkp/wowapi/API/Guild.$phpEx");
				}				
				$this->Guild = new Guild($region);
				break;
			case 'character':
				if (!class_exists('Character')) 
				{
					require($phpbb_root_path . "includes/bbdkp/wowapi/API/Character.$phpEx");
				}				
				$this->Character = new Character($region);
				break;
				
		}
	}
}
