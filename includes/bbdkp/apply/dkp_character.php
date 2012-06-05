<?php
/**
* bbdkp Apply core class
*
* @package bbDkp.includes
* @version $Id$
* @copyright (c) 2010 bbDkp <http://code.google.com/p/bbdkp/>
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @author Kapli, Malfate, Sajaki, Blazeflack, Twizted, Ethereal
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

/**
 * This class describes an applicant
 */
class dkp_character
{
	// character definition
	public $name ='';
	public $realm = '';
	public $ModelViewURL;
	public $url;
	public $feedurl; 
	public $level ='';
	public $class = '';
	public $classid = 0;
	public $talents ='';
	public $race ='';
	public $raceid = 0;
	public $game ='';
	
	public $talent1name ='';
	public $talent1 ='';
	public $talent2name ='';
	public $talent2 ='';
	public $professions ='';
	public $genderid = 0;
	public $faction = 0;
	public $guild = ''; 
	public $guildrank = 0;
	 
	public $spellpower = 0; 
	public $spellhit = 0; 
	public $firecrit = 0;
	public $frostcrit = 0; 
	public $arcanecrit = 0; 
	public $holycrit = 0; 
	public $shadowcrit = 0; 
	public $naturecrit = 0; 
	public $mrcast = 0; 
	public $spellhaste = 0; 
	
	public $hp = 0; 
	public $mana = 0; 
	public $rap = 0; 
	public $rcr = 0; 
	public $rhr = 0;  
	public $rdps = 0; 
	public $rspeed = 0; 
	public $map = 0; 
	public $mcr = 0;
	public $mhr = 0; 
	public $mhdps = 0; 
	public $ohdps = 0; 
	public $mspeed = 0; 
	
	public $expertise = 0; 
	public $armor = 0; 
	public $defense = 0; 
	public $dodge = 0; 
	public $parry = 0; 
	public $block = 0;  

	public $glyphminor;
	public $glyphmajor;
	public $item = array();
	public $achievements;
	public $gear = array();
	public $ilvl = array();
	public $gems1 = array();
	public $gems2 = array();
	public $gems3 = array();
	public $ench = array();
	public $gearNameLink = array();
	
	public $modeltemplate; 
	
	
}

?>