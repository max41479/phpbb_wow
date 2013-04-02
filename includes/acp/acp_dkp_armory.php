<?php
/**
 * Armory ACP 1.1.7
 * @requires bbDKP 1.2.7
 * @package bbDKP.acp
 * @author Sajaki
 * @copyright 2009 bbdkp https://github.com/bbDKP/Armory-Importer
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * 
 */

/**
 * @ignore
 */
if (! defined ( 'IN_PHPBB' ))
{
	exit ();
}

if (! defined('EMED_BBDKP')) 
{
	$user->add_lang ( array ('mods/dkp_admin' ));
	trigger_error ( $user->lang['BBDKPDISABLED'] , E_USER_WARNING );
}

class acp_dkp_armory extends bbDkp_Admin
{
	var $u_action;
	
	function main($id, $mode)
	{
		global $cache, $db, $user, $template;
		global $config, $phpbb_root_path, $phpEx;
		$user->add_lang ( array ('mods/dkp_roster', 'mods/dkp_admin' , 'mods/wowapi') );
		$this->u_action = append_sid ("index.$phpEx", "i=dkp_armory&amp;mode=armory");

		// Check for required extensions
		if (!function_exists('curl_init')) 
		{
			trigger_error($user->lang['CURL_REQUIRED'], E_USER_WARNING);
		}

		if (!function_exists('json_decode')) 
		{
			trigger_error($user->lang['JSON_REQUIRED'], E_USER_WARNING);			
		}

		switch ($mode)
		{
			case 'armory' :
				
				$submit =  (isset ( $_POST ['armoryconfig'] )) ? true : false;
				if($submit)
				{	
					set_config('bbdkp_armory_achiev', request_var('achi', 0), true);
					set_config('bbdkp_min_armorylevel', $minlvl = request_var('min_level', 10), true);
					set_config('bbdkp_armory_site', request_var ( 'site_id', 'us' ), true);
					set_config('bbdkp_default_realm', request_var ( 'realm_name', 'Lightbringer' ), true);
					$cache->destroy('config');
					meta_refresh(1, $this->u_action);
					trigger_error ( $user->lang ['MSG_UPDATED'] . adm_back_link ( $this->u_action ), E_USER_NOTICE );
					
				}
				
				/***********************
				 *  Get Guild handler
				 **********************/
				$get_guild = (isset ( $_POST ['downloadguild'] )) ? true : false;
				if ($get_guild) 
				{
					$this->call_guild();
				}
				
				/***********************
				 *  Get char handler
				 **********************/
				$get_char = (isset ( $_POST ['downloadchar'] )) ? true : false;
				if ($get_char)
				{
					$this->call_character();
				} 
				
				/* ----------- prepare guild dropdown ------------ */
				$sql = 'SELECT id, name, realm, region FROM ' . GUILD_TABLE . ' where id != 0';
				$result = $db->sql_query ( $sql );
				while ( $row = $db->sql_fetchrow ( $result ))
				{
					$template->assign_block_vars ( 'guild_row', array (
						'VALUE' => $row ['id'], 
						'SELECTED' => '', 
						'OPTION' => $row ['name'] ));
				}
				$db->sql_freeresult ( $result );
				
				// prepare char dropdown
				$sql = 'SELECT member_id, member_name FROM ' . MEMBER_LIST_TABLE;
				$result = $db->sql_query ($sql);
				while ( $row = $db->sql_fetchrow ( $result ) )
				{
					$template->assign_block_vars ( 'member_row', array (
						'VALUE' 	=> $row ['member_id'], 
						'SELECTED' 	=> '', 
						'OPTION' 	=> $row ['member_name'] ));
				}
				$db->sql_freeresult ( $result );
				
				
				$armorylist = array (
					'wow'  =>  $user->lang['DOWNLOAD_TYPE_WOW'],
				);
				
				foreach ($armorylist as $key => $value)
				{
					$select = ($key == $config['bbdkp_armory_type'] ) ? ' selected="selected" ' : ' ';
					$template->assign_block_vars ( 'armorylist_row', 
						array (
						'VALUE' 	=> $key, 
						'SELECTED' 	=> $select, 
						'OPTION' 	=> $value
						 ));
				}

				$sites = array (
					'us'  =>  'North America',
					'eu'  => 'Europe',
					'kr'  => 'Korea',
					'sea' => 'South East Asia',
					'tw'  => 'Taiwan',
				);
				
				foreach ($sites as $key => $value)
				{
					$select = ($key == $config['bbdkp_armory_site'] ) ? ' selected="selected" ' : ' ';
					$template->assign_block_vars ( 'site_row', 
						array (
						'VALUE' 	=> $key, 
						'SELECTED' 	=> $select, 
						'OPTION' 	=> $value
						 ));
				}
				
				// fill template with game-specific info
				
				if ($config['bbdkp_games_wow'] == 1)
				{
					$template->assign_vars ( array (
						'ARMORY_SELECTION' 	=> $user->lang ['DOWNLOAD_TYPE_WOW'], 
						'S_RSTYLE' 			=> 1, 
						'REALM_NAME'		=> $config['bbdkp_default_realm'],
						'MIN_LVLDL'			=> $config['bbdkp_min_armorylevel'], 
						'L_TITLE' 			=> $user->lang ['ACP_DKP_ARMORY'] . ' ' . $config['bbdkp_plugin_armoryupdater'],  
						'F_ACHISACTIVATE' 	=> $config['bbdkp_armory_achiev'],  
						));
					$this->page_title = $user->lang ['ACP_DKP_ARMORY'] . ' ' . $config['bbdkp_plugin_armoryupdater'];
				}
				else
				{
					//sorry, game not supported
					$template->assign_vars ( array (
							'ARMORY_SELECTION' 	=> $user->lang ['DOWNLOAD_TYPE_NOT_SUPPORTED'], 
							'S_RSTYLE' 			=> 3, 
							'L_TITLE' 			=> $user->lang ['ACP_DKP_ARMORY']  . ' ' . $config['bbdkp_plugin_armoryupdater'] ) );
						$this->page_title = $user->lang ['ACP_DKP_ARMORY'] . ' ' . $config['bbdkp_plugin_armoryupdater'];
					
				}
								
				$this->tpl_name = 'dkp/acp_' . $mode;
				break;
		} // end switch mode
	

	} //main end
	
	/**
	 * 
	 */
	private function call_guild()
	{
		global $user, $db, $phpbb_root_path, $phpEx;
		
		if (confirm_box ( true ))
		{
			$guild_id = request_var ( 'guild_id_existing', 0 );
			$site = request_var ( 'site_id','' );
			$realm_name = utf8_normalize_nfc ( trim ( request_var ( 'realm_name', ' ', true ) ) );
			$guild_name = utf8_normalize_nfc ( trim ( request_var ( 'guild_name', ' ', true ) ) );
			if (!class_exists('WowAPI'))
			{
			   require($phpbb_root_path . 'includes/bbdkp/wowapi/WowAPI.' . $phpEx);
			}
			
			// initialise the guild api class
			$api2= new WowAPI('guild', $site);
			$params = array('members', 'achievements');
			$data = $api2->Guild->getGuild($guild_name, $realm_name, $params);
			
			if(isset($data['error']))
			{
				if (strlen($data['error']) > 1)
				{
					trigger_error ( $data['error'], E_USER_WARNING );
				}
			}
			
			// check if guild exists
			if ($guild_id > 0)
			{
				$this->updateguild($data, $site, $guild_id);
			} 
			else
			{
				$this->addnewguild($data, $site);	
			}
		
		} 
		else // get confirmation 
		{
			
			$guild_id = 0;
			$guild_name = utf8_normalize_nfc ( trim ( request_var ( 'guild_name', ' ', true ) ) );
			if (trim ( $guild_name ) != '')
			{
				// user typed a name
				if (! isset ( $_POST ['realm_name'] ))
				{
					trigger_error ( $user->lang ['MSG_REALM_EMPTY'], E_USER_WARNING );
				}
				$realm_name = utf8_normalize_nfc ( trim ( request_var ( 'realm_name', ' ', true ) ) );
				$site = request_var ( 'site_id', ' ' );
				//attempt to gind a guild_id
				$sql = "SELECT id, realm, region 
       					     FROM " . GUILD_TABLE . " where name = '" . $db->sql_escape($guild_name)  . "' and CHAR_LENGTH(realm) > 0  ";
				$result = $db->sql_query ($sql);
				while ( $row = $db->sql_fetchrow ( $result ) )
				{	
					// guild found. override vars with existing database values
					$guild_id = $row ['id'];
					$realm_name = $row ['realm'];
					$site = strtolower($row ['region']);
				}
				$db->sql_freeresult ( $result );
			}
			else
			{
				// user changed pulldown resulting in a $POST
				$guild_id = request_var ( 'guild_id_existing', 0 );
				if ($guild_id == 0)
				{
					trigger_error ( $user->lang ['MSG_INVALIDGUILD'] . adm_back_link ( $this->u_action ), E_USER_WARNING );
				}
				
				// get search parameters from db
				$sql = "SELECT name, realm, region 
       					     FROM " . GUILD_TABLE . " where id =" . $guild_id . ' and CHAR_LENGTH(realm) > 0  ';
				$result = $db->sql_query ( $sql );
				while ( $row = $db->sql_fetchrow ( $result ) )
				{
					$guild_name = $row ['name'];
					$realm_name = $row ['realm'];
					$site = strtolower($row ['region']);
				}
				$db->sql_freeresult ( $result );
				if (! isset ( $realm_name ))
				{
					trigger_error ( $user->lang ['MSG_INVALIDGUILD'] . adm_back_link ( $this->u_action ), E_USER_WARNING );
				}
			}
		
			$s_hidden_fields = build_hidden_fields ( array (
				'guild_name' 		=> $guild_name, 
				'guild_id_existing' => $guild_id, 
				'realm_name' 		=> $realm_name, 
				'site_id' 			=> $site, 
				'downloadguild' 	=> true 
				));
			
			confirm_box ( false, sprintf ( $user->lang ['ARM_DOWNLOADGUILD'], $guild_name, $realm_name ), $s_hidden_fields );
			meta_refresh(1, $this->u_action);
		}
	}
	
	/***
	 * adds a new guild
	 * "$data" = Array [8]	
		lastModified = (int) 1317233557000	
		name = (string:10) Bête Noire	
		realm = (string:12) Lightbringer	
		level = (int) 25	
		side = (int) 0	
		achievementPoints = (int) 1380	
		members = Array [475]	
			0 = Array [2]	
				character = Array [8]	
					name = (string:7) Kremaer	
					realm = (string:12) Lightbringer	
					class = (int) 9	
					race = (int) 1	
					gender = (int) 0	
					level = (int) 82	
					achievementPoints = (int) 0	
					thumbnail = (string:34) lightbringer/34/1669922-avatar.jpg	
				rank = (int) 4	
		emblem = Array [5]	
			icon = (int) 141	
			iconColor = (string:8) ff101517	
			border = (int) 0	
			borderColor = (string:8) ff0f1415	
			backgroundColor = (string:8) ffffffff	
	 * 
	 * 
	 */
	private function addnewguild($guild, $site)
	{
		global $config, $user, $db, $phpbb_root_path, $phpEx;
		$link = '<br /><a href="' . append_sid ( "index.$phpEx", "i=dkp_armory&amp;mode=armory" ) . '"><h3>' .
		 $user->lang ['RETURN_DKPINDEX'] . '</h3></a>';
		if (! class_exists ( 'acp_dkp_mm' ))
		{
			include ($phpbb_root_path . 'includes/acp/acp_dkp_mm.' . $phpEx);
		}
		$members = new acp_dkp_mm ( );

		$guild_id = $members->insertnewguild ($guild['name'], $guild['realm'], $site, 1 );
		$new = array ();
		$membersadded = 0;
		$rankcount = array();
		
		foreach ( $guild['members'] as $new )
		{
			$rankcount[$new['rank']] = 1;
		}
		unset($new); 
		foreach($rankcount as $key => $count)
		{
			$members->insertnewrank ( $key, 'Rank'.$key, 0, '', '', $guild_id );			
		}
		$message = '';
		
		foreach ( $guild['members'] as $new )
		{
			$rank_id = $new['rank'];
			$member_name = $new['character']['name'];
			$member_lvl = ( int ) $new['character']['level'];
			$gender = ( int ) $new['character']['gender'];
			$race_id = ( int ) $new['character']['race'];
			$class_id = ( int ) $new['character']['class'];
			$achievpoints = ( int ) $new['character']['achievementPoints'];
			$url = sprintf('http://%s.battle.net/wow/en/', $site) . 'character/' . $guild['realm']. '/' . $new['character']['name'] . '/simple';
			$member_status = 1;
			$member_comment = sprintf($user->lang['ACP_SUCCESSMEMBERADD'],  date("F j, Y, g:i a") );
			$joindate = $this->time;
			$leavedate = mktime ( 0, 0, 0, 12, 31, 2030 );
			
			if( (int) $new['character']['level'] >= (int) $config['bbdkp_min_armorylevel'])
			{
				if ($members->insertnewmember ( $member_name, $member_status, 
					$member_lvl, $race_id, $class_id, $rank_id, $member_comment, $joindate, $leavedate, 
					$guild_id, $gender, $achievpoints, $url ))
					{
						$membersadded += 1;
						$message .= sprintf($user->lang['ACTION_NEWCHAR'], $member_name . '@'. $site. '-' . $guild['realm']);
					}
				
			}
			
		}
		
		$message . sprintf($user->lang['ACP_SUCCESSADDGUILD'], $guild['name'], $guild['realm'],$site, $membersadded );
		
		if($config['bbdkp_armory_achiev'] == 1)
		{
			$message .= $this->update_achievements($guild, $site);
		}
		
		unset($guild);
		unset($new);
		unset($data3);
		unset($members);
		
		trigger_error ( $message . $link, E_USER_NOTICE );

		
	}
	

	/*
	 * updates existing guild
	 * 
	 */
	private function updateguild($guild, $site, $guild_id)
	{
		global $config, $user, $db, $phpbb_root_path, $phpEx;
		
		if($guild_id == 0 || isset($guild['status']))
		{
			trigger_error ( $user->lang ['MSG_INVALIDGUILD'], E_USER_WARNING );
		}
		
		$link = '<br /><a href="' . append_sid ( "index.$phpEx", "i=dkp_armory&amp;mode=armory" ) . '"><h3>' . $user->lang ['RETURN_DKPINDEX'] . '</h3></a>';
		// include member class 
		if (! class_exists ( 'acp_dkp_mm' ))
		{
			include ($phpbb_root_path . 'includes/acp/acp_dkp_mm.' . $phpEx);
		}
		$members = new acp_dkp_mm ( );
		
		$membersadded = 0;
		$membersremoved = 0;
		$membersupdated = 0;
		/* ----------- manage the members ------------ */
		/*  loop existing members
        *	 -if found 		-> update name, rank, etc
        *	 -if not found	-> update rank to out
        *  end loop
        */
		//put new members in array
		$newmembers = array ();
		foreach ( $guild['members'] as $new )
		{
			$newmembers [$new['character']['name']] = $new['character']['name'];
		}
		asort ( $newmembers );
		
		// get old members
		$sql = ' select * from ' . MEMBER_LIST_TABLE . ' WHERE 
    		member_guild_id =  ' . ( int ) $guild_id . ' AND member_rank_id < 90';
		$result = $db->sql_query ( $sql );
		$oldmembers = array ();
		
		//loop old members to check for exmembers
		while ( $row = $db->sql_fetchrow ( $result ) )
		{
			$oldmembers [$row ['member_name']] = $row ['member_id'];
			if (! array_key_exists ( $row ['member_name'], $newmembers ))
			{
				if ($members->removemember ( $row ['member_name'], $guild_id ))
				{
					$membersremoved += 1;
				}
			}
		}
		$db->sql_freeresult ( $result );
		$message = '';
		// check for new members
		foreach ( $guild['members'] as $new )
		{
			if (! array_key_exists ( $new['character']['name'], $oldmembers ))
			{
				// new member arrived, add it if it's high lvl enough
				if( (int) $new['character']['level'] >= (int) $config['bbdkp_min_armorylevel'])
				{
					$rank_id = $new['rank'];
					$rankcount[(int) $new['rank']] = 1; 
					$member_name = $new['character']['name'];
					$member_lvl = ( int ) $new['character']['level'];
					$gender = ( int ) $new['character']['gender'];
					$race_id = ( int ) $new['character']['race'];
					$class_id = ( int ) $new['character']['class'];
					$achievpoints = ( int ) $new['character']['achievementPoints'];
					$imageurl = sprintf('http://%s.battle.net/static-render/'. strtolower($site) . '/' . $new['character']['thumbnail'] , strtolower($site));
					$memberarmoryurl = sprintf('http://%s.battle.net/wow/en/', $site) . 'character/' . $guild['realm']. '/' . $new['character']['name'] . '/simple';
					$member_status = 1;
					$member_comment = sprintf($user->lang['ACP_SUCCESSMEMBERADD'],  date("F j, Y, g:i a") );
					$joindate = $this->time;
					$leavedate = mktime ( 0, 0, 0, 12, 31, 2030 );
					
					if ($members->insertnewmember ( $member_name, $member_status, $member_lvl, 
					$race_id, $class_id, $rank_id, $member_comment, $joindate, 
					$leavedate, $guild_id, $gender, $achievpoints, $memberarmoryurl, $imageurl ))
					{
						$membersadded += 1;
						$message .= sprintf($user->lang['ACTION_NEWCHAR'], $member_name . '@'. $site. '-' . $guild['realm']);
					}

				}
				
			}
			else
			{
				// update member 
				// leave status, joindate, leavedate as is
				$rank_id = $new['rank'];
				$rankcount[$new['rank']] = 1; 
				$member_name = $new['character']['name'];
				$member_lvl = ( int ) $new['character']['level'];
				$gender = ( int ) $new['character']['gender'];
				$race_id = ( int ) $new['character']['race'];
				$class_id = ( int ) $new['character']['class'];
				$achievpoints = ( int ) $new['character']['achievementPoints'];
				$memberarmoryurl = sprintf('http://%s.battle.net/wow/en/', $site) . 'character/' . $guild['realm']. '/' . $new['character']['name'] . '/simple';
				$imageurl = sprintf('http://%s.battle.net/static-render/'. strtolower($site) . '/' . $new['character']['thumbnail'] , strtolower($site));
				
				$sql = 'SELECT * from ' . MEMBER_LIST_TABLE . " 
					    WHERE member_name ='" . $db->sql_escape ( $member_name ) . "'";
				$result = $db->sql_query ( $sql );
				$member_data = $db->sql_fetchrow($result);
				$old_comment = $member_data['member_comment'];
				$member_id = (int) $member_data['member_id'];
				// append comment
				$member_comment = $old_comment;
				$game_id = 'wow';
				
				$db->sql_freeresult ($result);
				if ($members->updatemember ($member_id,  $member_name, $member_lvl, 
					$race_id, $class_id, $rank_id, $member_comment, $guild_id, $gender, $achievpoints, $memberarmoryurl, $imageurl, $game_id ))
				{
					$membersupdated += 1;
					$message .= sprintf($user->lang['ACTION_UPDCHAR'], $member_name . '@'. $site. '-' . $guild['realm']);
				}
			}
		}
		
		unset($new);
		
		$message . '<br />' . sprintf($user->lang['ACP_SUCCESSUPDGUILD'],  $guild['name'] ,  
			$guild['realm'] , $site, $membersadded, $membersupdated, $membersremoved);
			
		if($config['bbdkp_armory_achiev'] == 1)
		{
			$message .= '<br />' . $this->update_achievements($guild, $site);
		}
		
		$sql = ' select rank_id from ' . MEMBER_RANKS_TABLE . ' WHERE 
				 guild_id =  ' . ( int ) $guild_id . ' AND rank_id < 90 order by rank_id ';
		$result = $db->sql_query ( $sql );
		$oldranks = array ();
		while ( $row = $db->sql_fetchrow ( $result ) )
		{
			$oldranks [(int) $row ['rank_id']] = 1;
		}
		$db->sql_freeresult ( $result );
		
		ksort($rankcount);
		if ($rankcount != $oldranks)
		{
			$this->rankhandle ( $rankcount, $oldranks, $guild_id );
		}
		unset($rankcount);
		unset($data3);
		unset($guild);
		
		trigger_error ( $message . $link, E_USER_NOTICE );
		
	}
	
	
	/**
	 * now call character API to update individual fields like achievement because guild API sets zero (!)
	 *
	 * @param array $guild
	 * @param string $site
	 * @return string
	 */
	private function update_achievements($guild, $site)
	{
		global $config, $user;
		$message = '';
		$achievcounter = 0;
		foreach ( $guild['members'] as $member )
		{
			if( (int) $member['character']['level'] >= (int) $config['bbdkp_min_armorylevel'])
			{
				$data3 = $this->call_API_char($site,  $member['character']['name'], $guild['realm']);
				
				if((string) $data3['error'] == '')
				{
					$this->updatecharacter( $data3, $guild['realm'], $site, true); 	
					$message .= sprintf($user->lang['ACTION_ACHIEV'], $member['character']['name'] . '@'. $site. '-' . $guild['realm']);
				}
				else
				{
					$message .= '<i>' . $data3['error'] . ' : ' . $member['character']['name'] . '@'. $site. '-' . $guild['realm'] . '</i><br />';
				}
			}
			
			unset($data3);
			
			$achievcounter +=1;
			/*if ($achievcounter == 10)
			{
				return $message;
			}
			*/
		}
		
		unset($member);
		return $message;
	}
	
	
	/*
     * handles differing old and new ranks
     * 
     */
	private function rankhandle($newranks, $oldranks, $guild_id)
	{
		// load member class 
		if (! class_exists ( 'acp_dkp_mm' ))
		{
			// we need this class for accessing member functions
			include ($phpbb_root_path . 'includes/acp/acp_dkp_mm.' . $phpEx);
		}
		$members = new acp_dkp_mm ( );
		
		//loop new ranks and check if they exist on oldrank
		//if not then create them
		foreach ( $newranks as $key => $value )
		{
			if (! array_key_exists ( $key, $oldranks ))
			{
				//insert new ranks
				$members->insertnewrank ( $key, 'Rank' . $key, 0, '', '', $guild_id );
			}
		}
		
		//loop old ranks and check if they exist in newrank
		// if not then delete them
		foreach ( $oldranks as $key => $value  )
		{
			if (! array_key_exists ( $key, $newranks ))
			{
				 //override
				 $members->deleterank( $key, $guild_id, true);
			}
		}
	
	}
	
	
	/**
	 * calls armory to get a character
	 *
	 */
	private function call_character()
	{
		global $config, $user, $db, $phpbb_root_path, $phpEx;
		
		if (confirm_box ( true ))
		{
			$site = request_var ( 'site_id','' );
			$char_name = utf8_normalize_nfc ( trim ( request_var ( 'char_name', ' ', true ) ) );
			$realm_name = utf8_normalize_nfc ( trim ( request_var ( 'realm_name', ' ', true ) ) );						
				 
			$data3 = $this->call_API_char($site, $char_name, $realm_name);

			if(!is_array($data3))
			{
				trigger_error($user->lang['WOWAPIERR404']);
			}
			
			if(count($data3) == 0)
			{
				trigger_error($user->lang['WOWAPIERR404']);
			}
			
			// check if character status is 'Character not found'
			if( array_key_exists('status', $data3))
			{
				trigger_error($user->lang['WOWAPIERR404'] . ': ' . $data3['reason']);
			}
		
			if($data3['level'] >= $config['bbdkp_min_armorylevel'])
			{
				$sql = 'SELECT  count(*) as count from ' . MEMBER_LIST_TABLE . " 
     					    WHERE member_name ='" . $db->sql_escape ( $data3['name'] ) . "'";
				$result = $db->sql_query ( $sql );
				$charcount = ( int ) $db->sql_fetchfield ( 'count' );
				$db->sql_freeresult ( $result );
				
				if ($charcount == 0)
				{
					$this->addcharacter( $data3,$realm_name,$site); 
				} 
				elseif ($charcount == 1)
				{
					$this->updatecharacter( $data3,$realm_name,$site, false); 
				}
			}
			
		}
		else // get confirmation 
		{
				
			$char_id = 0;
			$char_name = utf8_normalize_nfc ( trim ( request_var ( 'char_name', '', true ) ) );
			if (trim ( $char_name ) != '')
			{
				// user input 
				if (! isset ( $_POST ['realm_name'] ))
				{
					trigger_error ( $user->lang ['MSG_REALM_EMPTY'], E_USER_WARNING );
				}
				$realm_name = utf8_normalize_nfc ( trim ( request_var ( 'realm_name', ' ', true ) ) );
				$site = request_var ( 'site_id', ' ' );					
			} 
			else
			{
				// select member value id from pulldown 
				$char_id = request_var ( 'member_char_name', 0 );
				if ($char_id == 0)
				{
					trigger_error ( $user->lang['MSG_INVALID_CHAR'] . adm_back_link ( $this->u_action ), E_USER_WARNING );
				}
				// get search parameters from db
				$sql = "SELECT  a.member_id, a.member_name, b.realm, b.region   
        					    FROM " . MEMBER_LIST_TABLE . " a, " . GUILD_TABLE . " b 
        					    WHERE  a.member_id =" . ( int ) $char_id . "
        					    AND b.id = a.member_guild_id";
				$result = $db->sql_query ( $sql );
				if(!$result)
				{
					trigger_error ( $user->lang['MSG_INVALID_CHAR'] . adm_back_link ( $this->u_action ), E_USER_WARNING );
				}
				while ( $row = $db->sql_fetchrow ( $result ) )
				{
					$char_name = $row ['member_name'];
					$realm_name = $row ['realm'];
					$site = strtolower($row ['region']);
				}
				
				$db->sql_freeresult ( $result );
				if (! isset ( $realm_name ))
				{
					trigger_error ( $user->lang['MSG_INVALID_CHAR'] . adm_back_link ( $this->u_action ), E_USER_WARNING );
				}
				
			}
			
			$s_hidden_fields = build_hidden_fields ( array (
				'char_name' 		=> $char_name, 
				'member_char_name' 	=> $char_id, 
				'realm_name'		=> $realm_name, 
				'site_id' 			=> $site, 
				'downloadchar' 		=> true ) );
			confirm_box ( false, sprintf ( $user->lang ['ARM_DOWNLOADCHAR'], $char_name, $realm_name, $site ), $s_hidden_fields );
			meta_refresh(1, $this->u_action);
		}
		
	}
	
	/**
	 * calls API for characterupdate
	 *
	 * @param string $site
	 * @param string $char_name
	 * @param string $realm_name
	 * @return array
	 */
	private function call_API_char($site,$char_name, $realm_name )
	{
		/*
		 * character array structure
		 * 
		 * no rank information  !
		 * 
		"$data3" = Array [12]	
			lastModified = (int) 1319397866000	
			name = (string:5) Xeeni	
			realm = (string:12) Lightbringer	
			class = (int) 5	
			race = (int) 1	
			gender = (int) 1	
			level = (int) 85	
			achievementPoints = (int) 3215	
			thumbnail = (string:35) lightbringer/227/1868771-avatar.jpg	
			guild = Array [6]	
				name = (string:10) Bête Noire	
				realm = (string:12) Lightbringer	
				level = (int) 25	
				members = (int) 454	
				achievementPoints = (int) 1410	
				emblem = Array [5]	
					icon = (int) 141	
					iconColor = (string:8) ff101517	
					border = (int) 0	
					borderColor = (string:8) ff0f1415	
					backgroundColor = (string:8) ffffffff	
			stats = Array [52]	
				health = (int) 97359	
				powerType = (string:4) mana	
				power = (int) 65955	
				str = (int) 46	
				agi = (int) 54	
				sta = (int) 3881	
				int = (int) 3043	
				spr = (int) 1230	
				attackPower = (int) 36	
				rangedAttackPower = (int) 0	
				mastery = (double) 12.31169	
				masteryRating = (int) 773	
				crit = (double) 8.930528	
				critRating = (int) 984	
				hitPercent = (double) 1.182261	
				hitRating = (int) 142	
				hasteRating = (int) 510	
				expertiseRating = (int) 0	
				spellPower = (int) 3033	
				spellPen = (int) 0	
				spellCrit = (double) 11.415535	
				spellCritRating = (int) 984	
				spellHitPercent = (double) 1.3861	
				spellHitRating = (int) 142	
				mana5 = (double) 2164	
				mana5Combat = (double) 1936	
				armor = (int) 7259	
				dodge = (double) 3.360632	
				dodgeRating = (int) 0	
				parry = (double) 0	
				parryRating = (int) 0	
				block = (double) 0	
				blockRating = (int) 0	
				resil = (int) 0	
				mainHandDmgMin = (double) 119	
				mainHandDmgMax = (double) 176	
				mainHandSpeed = (double) 2.885	
				mainHandDps = (double) 51.196106	
				mainHandExpertise = (int) 0	
				offHandDmgMin = (double) 3	
				offHandDmgMax = (double) 4	
				offHandSpeed = (double) 1.923	
				offHandDps = (double) 1.597207	
				offHandExpertise = (int) 0	
				rangedDmgMin = (double) 673	
				rangedDmgMax = (double) 1250	
				rangedSpeed = (double) 1.539	
				rangedDps = (double) 624.85333	
				rangedCrit = (double) 8.930528	
				rangedCritRating = (int) 984	
				rangedHitPercent = (double) 1.182261	
				rangedHitRating = (int) 142	
			professions = Array [2]	
				primary = Array [2]	
					0 = Array [6]	
						id = (int) 171	
						name = (string:7) Alchemy	
						icon = (string:13) trade_alchemy	
						rank = (int) 498	
						max = (int) 525	
						recipes = Array [176]	
					1 = Array [6]	
						id = (int) 182	
						name = (string:9) Herbalism	
						icon = (string:15) trade_herbalism	
						rank = (int) 456	
						max = (int) 525	
						recipes = Array [0]	
				secondary = Array [4]	
					0 = Array [6]	
						id = (int) 129	
						name = (string:9) First Aid	
						icon = (string:26) spell_holy_sealofsacrifice	
						rank = (int) 491	
						max = (int) 525	
						recipes = Array [18]	
					1 = Array [6]	
						id = (int) 794	
						name = (string:11) Archaeology	
						icon = (string:17) trade_archaeology	
						rank = (int) 0	
						max = (int) 0	
						recipes = Array [0]	
					2 = Array [6]	
						id = (int) 356	
						name = (string:7) Fishing	
						icon = (string:13) trade_fishing	
						rank = (int) 293	
						max = (int) 375	
						recipes = Array [0]	
					3 = Array [6]	
						id = (int) 185	
						name = (string:7) Cooking	
						icon = (string:16) inv_misc_food_15	
						rank = (int) 451	
						max = (int) 525	
						recipes = Array [101]	

		 * 
		 */
		global $phpbb_root_path, $phpEx;
		//Initialising the class      
		if (!class_exists('WowAPI'))
		{
		   require($phpbb_root_path . 'includes/bbdkp/wowapi/WowAPI.' . $phpEx);
		}
		// initialise the guild api class
		$api2= new WowAPI('character', $site);
		// available extra fields :
		// 'guild','stats','talents','items','reputation','titles','professions','appearance',
		// 'companions','mounts','pets','achievements','progression','pvp','quests'
		$params = array('guild', 'stats', 'professions');
		///$params = array('guild','stats','talents','items','reputation','titles','professions');
		$data3 = $api2->Character->getCharacter($char_name, $realm_name, $params);
		return $data3;		
	}
	
	/***
	 * adds a single new character from ACP
	 * 
	 */
	private function addcharacter($char,$realm_name,$site)
	{
		global $config, $user, $db, $phpbb_root_path, $phpEx;
		
		$link = '<br /><a href="' . append_sid ( "index.$phpEx", "i=dkp_armory&amp;mode=armory" ) . '"><h3>' . 
			$user->lang ['RETURN_DKPINDEX'] . '</h3></a>';
		// include member class 
		if (! class_exists ( 'acp_dkp_mm' ))
		{
			// we need this class for accessing member functions
			include ($phpbb_root_path . 'includes/acp/acp_dkp_mm.' . $phpEx);
		}
		$members = new acp_dkp_mm ( );
		
		// check if guild exists and get guild id 
		$sql = 'SELECT id, name, realm, region, roster
		        FROM ' . GUILD_TABLE . "  
		        WHERE name = '" . $db->sql_escape ( $char['guild']['name'] ) . "'";
		$result = $db->sql_query ($sql);
		$row = $db->sql_fetchrow ($result);
		if (! $row)
		{
			$guildid = $members->insertnewguild ($char['guild']['name'], $realm_name, $site, 1, 0, 0 );
		}
		else
		{
			// load guild object
			$guildid = $row ['id'];
		}
		$db->sql_freeresult ( $result );
		
		//check if rank exists
		$sql = ' select max(rank_id) as maxrank from ' . MEMBER_RANKS_TABLE . ' WHERE rank_id < 90 AND guild_id =  ' . ( int ) $guildid;
		$result = $db->sql_query ($sql);
		$rankid = (int) $db->sql_fetchfield('maxrank');
		$db->sql_freeresult ( $result );
		
		// new member arrived
		/*
		 * 	lastModified = (int) 1316989942000	
			name = (string:6) Sajaki	
			realm = (string:12) Lightbringer	
			class = (int) 1	
			race = (int) 1	
			gender = (int) 0	
			level = (int) 85	
			achievementPoints = (int) 3570	
			thumbnail = (string:36) lightbringer/144/29142672-avatar.jpg	
			guild = Array [6]	
				name = (string:10) Bête Noire	
				realm = (string:12) Lightbringer	
				level = (int) 25	
				members = (int) 474	
				achievementPoints = (int) 1380	
				emblem = Array [5]	
			stats = Array [52]	
				health = (int) 118807	
				powerType = (string:4) rage	
				power = (int) 100	
				str = (int) 2884	
				agi = (int) 397	
				sta = (int) 5413	
				int = (int) 37	
				spr = (int) 64	
				attackPower = (int) 6003	
				rangedAttackPower = (int) 472	
				mastery = (double) 12.2838	
				masteryRating = (int) 768	
				crit = (double) 2.706249	
				critRating = (int) 193	
				hitPercent = (double) 4.595833	
		 * 
		 */
		
			// update member
			$member_name = $char ['name'];
			$member_lvl = ( int ) $char ['level'];
			$race_id = ( int ) $char ['race'];
			$class_id = ( int ) $char ['class'];
			$gender = ( int ) $char ['gender'];
			$achievpoints = ( int ) $char ['achievementPoints'];
			$professions = serialize ($char ['professions']);
			$stats = serialize ($char ['stats']);
			$member_comment = sprintf($user->lang['ACP_SUCCESSMEMBERADD'],  date("F j, Y, g:i a") );
			$jointime = $this->time;
			$armoryurl = sprintf('http://%s.battle.net/wow/en/', $site) . 'character/' . $realm_name. '/' . $char ['name'] . '/simple';
			$outdate = mktime ( 0, 0, 0, 12, 31, 2030 );
			
		if ($members->insertnewmember ( 
			$member_name, //member_name
			1, //member_status
			$member_lvl, //member_level
			$race_id, //member_race_id
			$class_id, //member_class_id
			$rankid, //member_rank_id
			$member_comment, //member_comment
			$jointime, //member_joindate
			$outdate, //member_outdate
			$guildid, //member_guild_id
			$gender, //member_gender_id
			$achievpoints, //member_achiev
			$armoryurl,
			$realm_name,
			'wow',
			0
			)) //member_armory_url
		{
			
			$success_message = sprintf($user->lang['ACP_SUCCESSMEMBERADDNAMED'],$member_name, date("F j, Y, g:i a") ) ;
			trigger_error ( $success_message . $link, E_USER_NOTICE );
		}
		
		
		
		
	}
	
	
	/*
	 * updates a single character
	 * 
	 */
	private function updatecharacter($char,$realm_name,$site, $silent=true) 
	{
		if(!is_array($char))
		{
			return;
		}
		
		if(count($char) == 0)
		{
			return;
		}
		
		// check if character status is 'Character not found'
		if( array_key_exists('status', $char))
		{
			// if there is a status then return
			return;
		}
		
		global $config, $user, $db, $phpbb_root_path, $phpEx;
		$link = '<br /><a href="' . append_sid ( "index.$phpEx", "i=dkp_armory&amp;mode=armory" ) . '"><h3>' . 
			$user->lang ['RETURN_DKPINDEX'] . '</h3></a>';
		// include member class 
		if (! class_exists ( 'acp_dkp_mm' ))
		{
			// we need this class for accessing member functions
			include ($phpbb_root_path . 'includes/acp/acp_dkp_mm.' . $phpEx);
		}
		$members = new acp_dkp_mm();
		
		// update member
		$member_name = $char ['name'];
		$member_lvl = ( int ) $char ['level'];
		$race_id = ( int ) $char ['race'];
		$class_id = ( int ) $char ['class'];
		$gender = ( int ) $char ['gender'];
		$achievpoints = ( int ) $char ['achievementPoints'];
		$professions = serialize ($char ['professions']);
		$stats = serialize ($char ['stats']);
		
		//rank and guild_id will remain the same
		$sql = 'SELECT * from ' . MEMBER_LIST_TABLE . " 
		    WHERE member_name ='" . $db->sql_escape ($member_name) . "'";
		$result = $db->sql_query ( $sql );
		$member_data = $db->sql_fetchrow($result);
		$member_id = (int) $member_data['member_id'];
		$guild_id = ( int ) $member_data['member_guild_id'];
		$rank_id = ( int ) $member_data['member_rank_id'] ;
		$old_comment = $member_data['member_comment'];
		// append comment
		$member_comment = $old_comment;
		
		$db->sql_freeresult ( $result );
		if ($members->updatemember ($member_id, $member_name, $member_lvl, 
			$race_id, $class_id, $rank_id, $member_comment, $guild_id, $gender, $achievpoints ) == true)
		{
			if(!$silent)
			{
				$success_message = sprintf($user->lang['ACP_SUCCESSMEMBERUPD'], $char ['name'], date("F j, Y, g:i a"));
				trigger_error ( $success_message . $link, E_USER_NOTICE );
			}
			
		}
		else 
		{
			if(!$silent)
			{
				$success_message = sprintf($user->lang['ACP_FAILEDMEMBERUPD'], $member_name );
				trigger_error ( $success_message . $link, E_USER_NOTICE );
			}
			
		}
						
								
		
	}
	
	
	
}

?>