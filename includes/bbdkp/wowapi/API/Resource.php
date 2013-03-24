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
 * @package   bbDKP-WoWAPI
 * @author	  Chris Saylor
 * @author	  Daniel Cannon <daniel@danielcannon.co.uk>
 * @author	  Andy Vandenberghe <sajaki9@gmail.com> 
 * @copyright Copyright (c) 2011, Chris Saylor, Daniel Cannon,  Andy Vandenberghe
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link	  https://github.com/bbDKP/WoWAPI
 * @link 	  http://blizzard.github.com/api-wow-docs 
 * @version   1.0.4
 */


/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (!class_exists('Curl')) 
{
	require($phpbb_root_path . "includes/bbdkp/wowapi/Component/Curl.$phpEx");
}

/**
 * Resource skeleton
 * 
 * @throws ResourceException If no methods are defined.
 */
abstract class Resource
{
	/**
	 * List of region urls
	 * @var string
	 */
	protected $api_url = array(
		'eu' => 'http://eu.battle.net/api/wow/',
		'us' => 'http://us.battle.net/api/wow/',
		'kr' => 'http://kr.battle.net/api/wow/',
		'tw' => 'http://tw.battle.net/api/wow/',
		'cn' => 'http://www.battlenet.com.cn/api/wow/',
		'sea' => 'http://sea.battle.net/api/wow/'
	);
	
	/**
	 * Battlenet regions
	 * 
	 * @var string
	 */
	public $region; 
	
	/**
	 * Methods allowed by this resource (or available).
	 *
	 * @var array
	 */
	protected $methods_allowed;

	/**
	 * Curl object instance.
	 *
	 * @var Curl
	 */
	protected $Curl;
	
	/**
	 * @param string $region Server region
	 */
	public function __construct($region='us') 
	{
		global $user;
		$user->add_lang ( array ('mods/wowapi'));
		if (empty($this->methods_allowed)) 
		{
			trigger_error($user->lang['NO_METHODS']);
		}
		$this->region = $region;
		$this->Curl = new Curl();
	}
	
	public function GetURI($region)
	{
		return $this->api_url[$this->region];
	}
	
	/**
	 * Returns the URI for use with the request object
	 *
	 * @param string $method
	 * @return string API URI
	 */
	private function getResourceUri($method)
	{
		$uri = $this->GetURI($this->region);
		$uri .= strtolower(get_class($this));
		$uri .= '/'.$method;
		return $uri;
	}

	/**
	 * Consumes the resource by method and returns the results of the request.
	 *
	 * @param string $method Request method
	 * @param array $params Parameters
	 * @throws ResourceException If request method is not allowed
	 * @return array Request data
	 */
	public function consume($method, $params=array()) 
	{
		global $user;
		$user->add_lang ( array ('mods/wowapi' ) );
		
		// either a valid method is required or an asterisk 
		if (!in_array($method, $this->methods_allowed)  && !in_array('*', $this->methods_allowed) ) 
		{
			trigger_error($user->lang['WOWAPI_METH_NOTALLOWED']);
		}
		$url = $this->getResourceUri($method);
		
		if (isset($params['data']) && !empty($params['data'])) 
		{
			if (is_array($params['data'])) 
			{
				$optfields = '';
				foreach($params['data'] as $key => $value) 
				{
					$optfields .= $key.'='.$value.'&';
				}
				$optfields = rtrim($data, '&');
			} 
			else 
			{
				$optfields = $params['data'];
			}
			
			$url .= '?' . $optfields; 
		}
		
		$data = $this->Curl->makeRequest($url, 'GET');
		$data['response']['error'] = '';
		$data['response']['reason'] = '';
		
		//cURL returned an error code
		if ($this->Curl->errno !== CURLE_OK) 
		{
			$data['response']['error'] = $this->Curl->error . ': ' . $this->Curl->errno;
		}
		
		//Battle.net returned a HTTP error code
		if (isset($data['response_headers']['http_code'])) 
		{
			switch ($data['response_headers']['http_code'] )
			{
				case 400:
					$data['response']['error'] = $user->lang['WOWAPIERR400'] . ': ' . $data['response']['reason'];
					break;
				case 401:
					$data['response']['error'] = $user->lang['WOWAPIERR401'] . ': ' . $data['response']['reason'];
					break;					
				case 403:
					$data['response']['error'] = $user->lang['WOWAPIERR403'] . ': ' . $data['response']['reason'];
					break;					
				case 404:
					$data['response']['error'] = $user->lang['WOWAPIERR404'] . ': ' . $data['response']['reason'];
					break;					
				case 500:
					$data['response']['error'] = $user->lang['WOWAPIERR500'] . ': ' . $data['response']['reason'];
					break;
				case 501:
					$data['response']['error'] = $user->lang['WOWAPIERR501'] . ': ' . $data['response']['reason'];
					break;
				case 502:
					$data['response']['error'] = $user->lang['WOWAPIERR502'] . ': ' . $data['response']['reason'];
					break;					
				case 503:
					$data['response']['error'] = $user->lang['WOWAPIERR503'] . ': ' . $data['response']['reason'];
					break;					
				case 504:
					$data['response']['error'] = $user->lang['WOWAPIERR504'] . ': ' . $data['response']['reason'];
					break;					
			}
		}
		return $data['response'];
	}

	
}
