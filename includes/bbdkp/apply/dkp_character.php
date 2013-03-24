<?php
/**
* bbdkp Apply core class
*
* @package bbDkp.includes
* @copyright (c) 2010 bbDkp <http://code.google.com/p/bbdkp/>
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @author Kapli, Malfate, Sajaki, Blazeflack, Twizted, Ethereal
* @version 1.4
*
*
**/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

class apply_post
{
	public $questioncolor = '';
	public $answercolor = '';
	public $gchoice = '';
	public $candidate_guild_id=0;
	public $message= ''; 
}



/**
 * This class describes an applicant
 */
class dkp_character
{
	
	//image
	public $modeltemplate; 
	public $portraitimg;
	
	// character definition
	//http://blizzard.github.com/api-wow-docs/#character-profile-api
	
	public $name ='';
	public $region = '';
	public $realm = '';
	public $achievementPoints;
	public $ModelViewURL;
	public $url;
	public $feedurl; 
	public $level ='';
	public $class = '';
	public $class_color = '';
	public $class_color_exists = '';
	public $classid = 0;
	public $class_image = '';
	public $class_image_exists = '';
	public $race ='';
	public $raceid = 0;
	public $race_image ='';
	public $race_image_exists = '';
	public $game ='';
	public $genderid = 0;
	public $faction = 0;
	
	//http://blizzard.github.com/api-wow-docs/#character-profile-api/guild
	public $guild = '';
	public $guild_id = 0;
	public $guildrank = 0;

	//http://blizzard.github.com/api-wow-docs/#character-profile-api/items	
	public $averageItemLevel;
	public $averageItemLevelEquipped;
	public $item_back = array();
	public $item_chest = array();
	public $item_feet = array();
	public $item_finger1 = array();
	public $item_finger2 = array();
	public $item_hands = array();
	public $item_legs = array();
	public $item_mainHand = array();
	public $item_neck = array();
	public $item_shoulder = array();
	public $item_trinket1 = array();
	public $item_trinket2 = array();
	public $item_waist = array();
	public $item_wrist = array();
	
	//http://blizzard.github.com/api-wow-docs/#character-profile-api/professions
	public $profession1 = array();
	public $profession2 = array();
	
	//http://blizzard.github.com/api-wow-docs/#character-profile-api/talents
	public $talent1 = array(
		'spec'	=> '', 
		'role'	=> '', 
		'icon'	=> '');
	
	public $talent2 = array(
			'spec'	=> '',
			'role'	=> '',
			'icon'	=> '');
		
	//stats in MOP
	//http://blizzard.github.com/api-wow-docs/#character-profile-api/stats
	public $health= 0;
	public $powerType= '';
	public $power=0;
	public $str=0;
	public $agi=0;
	public $sta	=0;
	public $int	=0;
	public $spr	=0;
	public $attackPower	=0;
	public $rangedAttackPower=0;
	public $mastery	=0.0;
	public $masteryRating	=0;
	public $crit=0.0;
	public $critRating	=0;
	public $hitPercent	=0.0;
	public $hitRating	=0;
	public $hasteRating	=0;
	public $expertiseRating	=0;
	public $spellPower	=0;
	public $spellPen	=0;
	public $spellCrit	=0.0;
	public $spellCritRating	=0;
	public $spellHitPercent	=0.0;
	public $spellHitRating	=0;
	public $mana5=0.0;
	public $mana5Combat=0.0;
	public $armor	=0;
	public $dodge	=0.0;
	public $dodgeRating	=0.0;
	public $parry=0.0;
	public $parryRating	=0;
	public $block	=0.0;
	public $blockRating	=0;
	public $pvpResilience	=0.0;
	public $pvpResilienceRating	=0;
	public $mainHandDmgMin	=0.0;
	public $mainHandDmgMax	=0.0;
	public $mainHandSpeed	=0.0;
	public $mainHandDps	=0.0;
	public $mainHandExpertise	=0.0;
	public $offHandDmgMin	=0.0;
	public $offHandDmgMax	=0.0;
	public $offHandSpeed	=0.0;
	public $offHandDps	=0.0;
	public $offHandExpertise	=0.0;
	public $rangedDmgMin=0.0;
	public $rangedDmgMax	=0.0;
	public $rangedSpeed	=0.0;
	public $rangedDps	=0.0;
	public $rangedExpertise	=0.0;
	public $rangedCrit	=0.0;
	public $rangedCritRating =0;
	public $rangedHitPercent=0.0;
	public $rangedHitRating	=0;
	public $pvpPower	=0.0;
	public $pvpPowerRating	=0;
	
	
}

?>