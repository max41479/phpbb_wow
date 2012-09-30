<?php
/**
 * @package bbDKP.module
 * @link http://www.bbdkp.com
 * @author Sajaki@gmail.com
 * @copyright 2009 bbdkp
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 1.2.8
 */

/**
 * @ignore
 */
if ( !defined('IN_PHPBB') OR !defined('IN_BBDKP') )
{
	exit;
}

$game_id = request_var('displaygame', '');
$start = request_var('start' ,0);
$selfurl = append_sid("{$phpbb_root_path}dkp.$phpEx" , 'page=roster') ;
$newroster = new roster($game_id, $start, $selfurl);

class roster
{
	private $dataset;
	private $classes;
	private $mode;
	private $start;
	public $selfurl;
	private $current_order;
	private $game_id;
	private $member_count;
	private $games; 
	
	public function __construct($game_id, $start, $selfurl)
	{
		global $user, $config, $template;
		$this->selfurl = $selfurl;
		$this->mode = ($config['bbdkp_roster_layout'] == '0') ? 'listing' : 'class';
		$this->start=$start;
		// Include the abstract base
		if (!class_exists('bbDKP_Admin'))
		{
			require("{$phpbb_root_path}includes/bbdkp/bbdkp.$phpEx");
		}
		$bbdkp = new bbDKP_Admin();
		$this->game_id = $game_id;
		$this->games = $bbdkp->games; 
		$installed_games = array();
		foreach($this->games as $id => $gamename)
		{
			if ($config['bbdkp_games_' . $id] == 1)
			{
				$installed_games[$id] = $gamename; 
				if ($this->game_id == '')
				{
					$this->game_id = $id;
				}
			} 
			
		}
		
		// push common data to template
		foreach ($installed_games as $id => $gamename)
		{
			$template->assign_block_vars ( 'game_row', array (
				'VALUE' => $id, 
				'SELECTED' => ($id == $this->game_id) ? ' selected="selected"' : '',
				'OPTION' => $gamename));
		}
		
		$template->assign_vars(array(
		    'GUILDNAME'			=>  $config['bbdkp_guildtag'],
			'S_MULTIGAME'		=> (sizeof($installed_games) > 1) ? true:false, 
			'S_DISPLAY_ROSTER' => true,
			'F_ROSTER'			=> $this->selfurl, 
			'S_GAME'		    => $this->game_id,
		));
		
		$this->get_listingresult();
		
		//show chosen game
		if($this->mode == 'class')
		{
			$this->displaygrid();
		}
		else
		{
			$this->displaylisting();
		}
		
	}
		
	/*
	 * Displays the class grid
	 */
	public function displaygrid()
	{
		global $phpbb_root_path, $phpEx, $config, $template, $user;
		//class
		
		$this->get_classes();
	    if(count($this->classes) > 0)
	    {
			foreach($this->classes as $row )
			{
				$classes[$row['class_id']]['name'] 		= $row['class_name'];
				$classes[$row['class_id']]['imagename'] = $row['imagename'];
				$classes[$row['class_id']]['colorcode'] = $row['colorcode'];
			}
	
			foreach ($classes as  $classid => $class )
			{
				$classimgurl =  $phpbb_root_path . "images/roster_classes/" . $this->removeFromEnd($class['imagename'], '') .'.png'; 
				$classcolor = $class['colorcode']; 
		         
				$template->assign_block_vars('class', array(	
		       		'CLASSNAME'     => $class['name'], 
		       		'CLASSIMG'		=> $classimgurl,
		       		'COLORCODE'		=> $classcolor,
		         ));
		        $classmembers=1;
		        
		        foreach ( $this->dataset as $row)
		        {
		        	if($row['member_class_id'] == $classid)
		        	{
					    $race_image = (string) (($row['member_gender_id']==0) ? $row['image_male'] : $row['image_female']);
			
			         	$template->assign_block_vars('class.members_row', array(
			     			'COLORCODE'		=> $row['colorcode'],
			     			'CLASS'			=> $row['class_name'],
			     			'NAME'			=> $row['member_name'],
			     			'RACE'			=> $row['race_name'],
			     			'RANK'			=> $row['rank_prefix'] . $row['rank_name'] . $row['rank_suffix'] ,
			          		'LVL'			=> $row['member_level'],
			     		    'ARMORY'		=> $row['member_armory_url'],  
			         		'PHPBBUID'		=> get_username_string('full', $row['phpbb_user_id'], $row['username'], $row['user_colour']),
			     			'PORTRAIT'		=> $this->getportrait($row), 	
			     		    'ACHIEVPTS'		=> $row['member_achiev'], 
							'CLASS_IMAGE' 	=> (strlen($row['imagename']) > 1) ? $phpbb_root_path . "images/class_images/" . $row['imagename'] . ".png" : '',  
							'S_CLASS_IMAGE_EXISTS' => (strlen($row['imagename']) > 1) ? true : false, 
							'RACE_IMAGE' 	=> (strlen($race_image) > 1) ? $phpbb_root_path . "images/race_images/" . $race_image . ".png" : '',  
							'S_RACE_IMAGE_EXISTS' => (strlen($race_image) > 1) ? true : false, 
			     		));
			     		$classmembers++;
		        	}
		         }
		      }
		
			$rosterpagination = generate_pagination2($this->selfurl . '&amp;o=' . $this->current_order ['uri'] ['current'] , $this->member_count, $config ['bbdkp_user_llimit'], $this->start, true, 'start'  );
	    
			if (isset($this->current_order) && sizeof ($this->current_order) > 0)
			{
				$template->assign_vars(array(
					'ROSTERPAGINATION' 		=> $rosterpagination ,  			
					'U_LIST_MEMBERS0'	=> append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=roster&amp;'. URI_ORDER. '='. $this->current_order['uri'][0]),
				    'U_LIST_MEMBERS1'	=> append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=roster&amp;'. URI_ORDER. '='. $this->current_order['uri'][1]),
				    'U_LIST_MEMBERS2'	=> append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=roster&amp;'. URI_ORDER. '='. $this->current_order['uri'][2]),
				    'U_LIST_MEMBERS3'	=> append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=roster&amp;'. URI_ORDER. '='. $this->current_order['uri'][3]),
				    'U_LIST_MEMBERS4'	=> append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=roster&amp;'. URI_ORDER. '='. $this->current_order['uri'][4]),
				));
				
			}

			// add template constants
			$template->assign_vars(array(
			    'S_SHOWACH'			=> $config['bbdkp_show_achiev'], 
			    'LISTMEMBERS_FOOTCOUNT' => 'Total members : ' . $this->member_count,
			));
	    }
				
		// add navigationlinks
		$navlinks_array = array(
		array(
			 'DKPPAGE' => $user->lang['MENU_ROSTER'],
			 'U_DKPPAGE' => append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=roster'),
		));
		
		foreach( $navlinks_array as $name )
		{
			$template->assign_block_vars('dkpnavlinks', array(
			'DKPPAGE' => $name['DKPPAGE'],
			'U_DKPPAGE' => $name['U_DKPPAGE'],
			));
		}

		$template->assign_vars(array(
		    'S_RSTYLE'		    => '1',
		));
			
		$header = $user->lang['GUILDROSTER'];
		page_header($header);
	}
	
	/*
	 * Displays the listing
	 */
	public function displaylisting()
	{
		global $phpbb_root_path, $phpEx, $config, $template, $user;
			
		$a=0;
		// use pagination 
		foreach ($this->dataset as $row)
		{ 
		 	$a++;
			$race_image = (string) (($row['member_gender_id']==0) ? $row['image_male'] : $row['image_female']);
		    $template->assign_block_vars('members_row', array(
		    	'GAME'			=>  $this->games[$row['game_id']], 
				'COLORCODE'		=> $row['colorcode'],
				'CLASS'			=> $row['class_name'],
				'NAME'			=> $row['member_name'],
				'RACE'			=> $row['race_name'],
				'RANK'			=> $row['rank_prefix'] . $row['rank_name'] . $row['rank_suffix'] ,
				'LVL'			=> $row['member_level'],
				'ARMORY'		=> $row['member_armory_url'],  
				'PHPBBUID'		=> get_username_string('full', $row['phpbb_user_id'], $row['username'], $row['user_colour']),  
				'PORTRAIT'		=> $this->getportrait($this->game_id, $row), 		
			    'ACHIEVPTS'		=> $row['member_achiev'], 
				'CLASS_IMAGE' 	=> (strlen($row['imagename']) > 1) ? $phpbb_root_path . "images/class_images/" . $row['imagename'] . ".png" : '',  
				'S_CLASS_IMAGE_EXISTS' => (strlen($row['imagename']) > 1) ? true : false, 
				'RACE_IMAGE' 	=> (strlen($race_image) > 1) ? $phpbb_root_path . "images/race_images/" . $race_image . ".png" : '',  
				'S_RACE_IMAGE_EXISTS' => (strlen($race_image) > 1) ? true : false, 
		      	));
	    }
	
		$rosterpagination = generate_pagination2($this->selfurl . '&amp;o=' . 
			$this->current_order ['uri'] ['current'] , 
			$this->member_count, 
			$config ['bbdkp_user_llimit'], 
			$this->start, true, 'start'  );
			
	
		// add navigationlinks
		$navlinks_array = array(
		array(
			 'DKPPAGE' => $user->lang['MENU_ROSTER'],
			 'U_DKPPAGE' => append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=roster'),
		));
		
		foreach( $navlinks_array as $name )
		{
			$template->assign_block_vars('dkpnavlinks', array(
			'DKPPAGE' => $name['DKPPAGE'],
			'U_DKPPAGE' => $name['U_DKPPAGE'],
			));
		}
		
		$template->assign_vars(array(
			'ROSTERPAGINATION' 		=> $rosterpagination ,  			
			'O_NAME'	=> $this->selfurl .'&amp;'. URI_ORDER. '='. $this->current_order['uri'][0],
			'O_GAME'	=> $this->selfurl .'&amp;'. URI_ORDER. '='. $this->current_order['uri'][1],		
		    'O_CLASS'	=> $this->selfurl .'&amp;'. URI_ORDER. '='. $this->current_order['uri'][2],
		    'O_RANK'	=> $this->selfurl .'&amp;'. URI_ORDER. '='. $this->current_order['uri'][3],
		    'O_LEVEL'	=> $this->selfurl .'&amp;'. URI_ORDER. '='. $this->current_order['uri'][4],
		    'O_PHPBB'	=> $this->selfurl .'&amp;'. URI_ORDER. '='. $this->current_order['uri'][5],
			'O_ACHI'	=> $this->selfurl .'&amp;'. URI_ORDER. '='. $this->current_order['uri'][6]
		));
	
		
		// add template constants
		$template->assign_vars(array(
		    'S_RSTYLE'		    => '0',
		    'S_SHOWACH'			=> $config['bbdkp_show_achiev'], 
		    'LISTMEMBERS_FOOTCOUNT' => 'Total members : ' . $this->member_count,
		));
		
		$header = $user->lang['GUILDROSTER'];
		page_header($header);
	}
	
	protected function getportrait($row)
	{
		global $phpbb_root_path;
	
		// setting up the links
		switch ($this->game_id)
	    {
	    	case 'wow':
	    	 if ( $row['member_portrait_url'] != '')
	    	 {
	    	 	//get battle.NET icon
	    	 	$memberportraiturl =  $row['member_portrait_url'];		 
	    	 }
	    	 else 
	    	 {
			   if($row['member_level'] <= "59")
			   {
					$maxlvlid ="wow-default";
			   }
			   elseif($row['member_level'] <= 69)
			   {
					$maxlvlid ="wow";
			   }
			   elseif($row['member_level'] <= 79)
			   {
					$maxlvlid ="wow-70";
			   }
			   else
			   {
					// level 85 is not yet iconified
					$maxlvlid ="wow-80";
			   }
	       	   $memberportraiturl =  $phpbb_root_path .'images/roster_portraits/'. $maxlvlid .'/' . $row['member_gender_id'] . '-' . 
	       	    $row['member_race_id'] . '-' . $row['member_class_id'] . '.gif';
	    	 }
	               break;
	      	 case 'aion': 
		       $memberportraiturl =  $phpbb_root_path . 'images/roster_portraits/aion/' . $row['member_race_id'] . '_' . $row['member_gender_id'] . '.jpg';
	               break;     		        
	          default:
	           $memberportraiturl='';
		           break;
	        }
	        return $memberportraiturl;
		
	}
	
	
	protected function removeFromEnd($string, $stringToRemove) 
	{
	    $stringToRemoveLen = strlen($stringToRemove);
	    $stringLen = strlen($string);
	    $pos = $stringLen - $stringToRemoveLen;
	    $out = substr($string, 0, $pos);
	    return $out;
	}
	
	
	protected function get_listingresult($classid=0)
	{
		global $db, $config; 
		$sql_array = array();
		$sql_array['SELECT'] =  'm.game_id, m.member_guild_id,  m.member_name, m.member_level, m.member_race_id, e1.name as race_name, 
	           				 m.member_class_id, m.member_gender_id, m.member_rank_id, m.member_achiev, m.member_armory_url, m.member_portrait_url, 
	           				 r.rank_prefix , r.rank_name, r.rank_suffix, e.image_female, e.image_male,
	           				 g.name, g.realm, g.region, c1.name as class_name, c.colorcode, c.imagename, m.phpbb_user_id, u.username, u.user_colour  '; 
		
		 $sql_array['FROM'] = array(
	               MEMBER_LIST_TABLE    =>  'm',
	               CLASS_TABLE          =>  'c',
	               GUILD_TABLE          =>  'g',
	               MEMBER_RANKS_TABLE   =>  'r',
	               RACE_TABLE           =>  'e',
	               BB_LANGUAGE			 =>  'e1');
	
	        $sql_array['LEFT_JOIN'] = array(    
	        	 array(
					 'FROM'  => array(USERS_TABLE => 'u'),
					   'ON'    => 'u.user_id = m.phpbb_user_id '), 
		        array(
		            'FROM'  => array(BB_LANGUAGE => 'c1'),
		            'ON'    => "c1.attribute_id = c.class_id AND c1.language= '" . $config['bbdkp_lang'] . "' AND c1.attribute = 'class'  and c1.game_id = c.game_id "  
		      )); 
		                      
		$sql_array['WHERE'] = " c.class_id = m.member_class_id
	           				 AND c.game_id = m.game_id 
	           				 AND e.race_id = m.member_race_id
	           				 AND e.game_id = m.game_id 
	           				 AND g.id = m.member_guild_id
	           				 AND r.guild_id = m.member_guild_id  
	           				 AND r.rank_id = m.member_rank_id AND r.rank_hide = 0
	           				 AND m.member_status = 1
	           				 AND m.member_level >= ".  intval($config['bbdkp_minrosterlvl']) . " 
	           				 AND m.member_rank_id != 99
	           				 AND m.game_id = '" . $db->sql_escape($this->game_id) . "'
	           				 AND e1.attribute_id = e.race_id AND e1.language= '" . $config['bbdkp_lang'] . "' 
	           				 AND e1.attribute = 'race' and e1.game_id = e.game_id";
		
		$sort_order = array(
		    0 => array('m.member_name', 'm.member_name desc'),
		    1 => array('m.game_id', 'm.member_name desc'),
		    2 => array('m.member_class_id', 'm.member_class_id desc'),
		    3 => array('m.member_rank_id', 'm.member_rank_id desc'),
		    4 => array('m.member_level', 'm.member_level  desc'),
		    5 => array('u.username', 'u.username desc'), 
		    6 => array('m.member_achiev', 'm.member_achiev  desc')
		);
		
		$this->current_order = switch_order($sort_order);
		
		if($this->mode=='class')
		{
			$sql_array['ORDER_BY']  = "m.member_class_id, " . $this->current_order['sql'];
		}
		else 
		{
			$sql_array['ORDER_BY']  = $this->current_order['sql'];
		}
		
	    $sql = $db->sql_build_query('SELECT', $sql_array);
	   
	    $result = $db->sql_query($sql);
		
	    if ($this->mode=='listing' && $this->start > 0)
	    {
		    $this->member_count=0;
		    while ($row = $db->sql_fetchrow($result))
			{
				$this->member_count++;
			}
			
			//now get wanted window
			$result = $db->sql_query_limit ( $sql, $config ['bbdkp_user_llimit'], $this->start );	
	    	$this->dataset = $db->sql_fetchrowset($result);
	    }
	    else
	    {
	    	$this->dataset = $db->sql_fetchrowset($result);
	    	$this->member_count = count($this->dataset);
	    }
	    
	    $db->sql_freeresult($result);
	}
	
	/**
	 * gets class array
	 *
	 * @param unknown_type $game_id
	 * @return unknown
	 */
	protected function get_classes()
	{
		global $db, $config; 
		$sql_array = array(
	       'SELECT'    => 'c.class_id, c1.name as class_name, c.imagename, c.colorcode' , 
	       'FROM'      => array(
	           MEMBER_LIST_TABLE    =>  'm',
	           CLASS_TABLE          =>  'c',
	           BB_LANGUAGE			=>  'c1',
	           MEMBER_RANKS_TABLE   =>  'r',
	           ),
	       'WHERE'     => " c.class_id = m.member_class_id 
	       				 AND c.game_id = m.game_id
	       				 AND r.guild_id = m.member_guild_id 
	       				 AND r.rank_id = m.member_rank_id AND r.rank_hide = 0
	       				 AND c1.attribute_id =  c.class_id AND c1.language= '" . $config['bbdkp_lang'] . "' AND c1.attribute = 'class' 
	       				 AND (c.game_id = '" . $db->sql_escape($this->game_id) . "')  
	       				 AND c1.game_id=c.game_id
	       				 
	       				  ", 
	       'ORDER_BY'  =>  'c1.name asc'
	    );
	    $sql = $db->sql_build_query('SELECT', $sql_array);
	    $result = $db->sql_query($sql);
	    $this->classes = $db->sql_fetchrowset($result);
	    $db->sql_freeresult($result);
		
	}
		
	
}

?>
