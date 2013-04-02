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
 * @package   WoWAPI-phpBB3
 * @author	  Andy Vandenberghe <sajaki9@gmail.com> 
 * @copyright Copyright (c) 2011, Chris Saylor, Daniel Cannon,  Andy Vandenberghe
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link	  https://github.com/bbDKP/WoWAPI-PHP-SDK/
 * 
 * 
 * http://blizzard.github.com/api-wow-docs/#id3381550
 * 
 * The guild profile API is the primary way to access guild information. This guild profile API can be used to fetch a single guild at a time through an HTTP GET request to a url describing the guild profile resource. By default, a basic dataset will be returned and with each request and zero or more additional fields can be retrieved. To access this API, 
 * craft a resource URL pointing to the guild whos information is to be retrieved.
	URL = Host + "/api/wow/guild/" + Realm + "/" + GuildName
	Realm = <proper realm name> | <normalized realm name>
	There are no required query string parameters when accessing this resource, although the "fields" query string parameter can optionally be passed to indicate that one or more of the optional datasets is to be retrieved. Those additional fields are listed in the subsection titled "Optional Fields".
	
	An example Guild Profile request with several addtional fields.
	GET /api/wow/guild/Lightbringer/bête noire
		{
		  "lastModified":1316889445000,
		  "name":"Bête Noire",
		  "realm":"Lightbringer",
		  "level":25,
		  "side":0,
		  "achievementPoints":1380,
		  "emblem":{
		    "icon":141,
		    "iconColor":"ff101517",
		    "border":0,
		    "borderColor":"ff0f1415",
		    "backgroundColor":"ffffffff"
		  }
		}

		extra request : 
		http://eu.battle.net/api/wow/guild/Lightbringer/bête noire?fields=achievements,members	
 */

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (!class_exists('Resource')) 
{
	require($phpbb_root_path . "includes/bbdkp/wowapi/Resource/Resource.$phpEx");
}

/**
 * Guild resource.
 *
 */
class Guild extends Resource 
{
   
	/**
	 * accepted methods : none in this resource (asterisk) 
	 *
	 * @var array
	 */
	protected $methods_allowed = array('*');

	/**
	 * standard fields are name, level, faction and achievement points.
	 * 
	 * available extra fields from guild: 
	 * members: a list of characters that are a member of the guild 
	 * achievements : a set of data structures that describe the achievements earned by the guild.
	 * news : a set of data structures that describe the news feed of the guild. (currently not used)
	 * 
	 *
	 * @var array
	 */
	private $extrafields = array(
	    'members',
	    'achievements',
		'news'
	  );
	  
	/**
	  * return the private fields
	  *
	  * @return array
	  */
	 public function getFields()
	 {
	 	return $this->extrafields;
	 }

	/**
	 * fetch guild results
	 * example : http://eu.battle.net/api/wow/guild/Lightbringer/Godless
	 * example : http://eu.battle.net/api/wow/guild/Lightbringer/Bête Noire?fields=achievements,members
	 * becomes : http://eu.battle.net/api/wow/guild/Lightbringer/b%C3%AAte%20noire?fields=achievements,members	
	 * 
	 * @param (string) $name
	 * @param (string) $realm
	 * @param (array) $fields
	 * @return mixed
	 */
	public function getGuild($name = '', $realm = '', $fields=array()) 
	{
		global $user;
		$user->add_lang ( array ('mods/wowapi' ));
	
		if(empty($name))
		{
			trigger_error($user->lang['WOWAPI_NO_GUILD']);
		}
		
		/* caution input has to be utf8 */
		/* replace space with %20 as per RFC 3986 URI encoding http://us.battle.net/wow/en/forum/topic/3050125211 */
		$name = rawurlencode($name);
		if (empty($realm)) 
		{
			trigger_error($user->lang['WOWAPI_NO_REALMS']);
		}
		
		//$name = str_replace(' ', '_', $realm);
		$realm = rawurlencode($realm);
		
		// URL = Host + "/api/wow/guild/" + Realm + "/" + GuildName
		$field_str = '';
		if (is_array($fields) && count($fields) > 0) 
		{
			$field_str = 'fields=' . implode(',', $fields);
			//check if correct keys were requested
			$keys = $this->getFields();
			if (count( array_intersect($fields, $keys)) == 0 )
			{
				trigger_error(sprintf($user->lang['WOWAPI_INVALID_FIELD'], $field_str));
			}
			
			$data = $this->consume( $realm. '/'. $name, array(
				'data' => $field_str
			));
		}
		else
		{
			$data = $this->consume( $realm. '/'. $name);
		}

			
		return $data;
	}
}
