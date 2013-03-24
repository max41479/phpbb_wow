<?php
/**
* language file Application form English
* @author Sajaki
* @package bbDkp
* @copyright (c) 2009 bbDkp <http://code.google.com/p/bbdkp/>
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @version 1.4.1
* 
*/
 
/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* DO NOT CHANGE
*/
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

/*  here you change the fixed strings  for the recruitment page */

$lang = array_merge($lang, array(

/***** ACP template settings *****/
'ACP_DKP_APPLY_EXPLAIN'  	=> 'Here you can set all preferences of the Recruitment Mod',
'APPLY_ACP_FORUM_PREF_EXPLAIN'		=> 'decides in which forum the application will be published. Set up the forum acccess priveleges beforehand.',
'APPLY_WELCOME'				=> 'Welcome message',
'APPLY_WELCOME_EXPLAIN'		=> 'Message shown on top of this template. supports bbcodes. ',
'APPLY_ACP_FORUM_PUB'		=> 'Forum',
'APPLY_ACP_PUBLIC'			=> 'Public',
'APPLY_ACP_GUESTPOST' 		=> 'Can guests posts? :',
'APPLY_ACP_GUESTPOST_EXPLAIN' 	=> 'If you set Guest posting On, don’t forget to set "Enable visual confirmation for guest postings:" to "Yes".' ,  
'ACP_APPLY_MANDATORY'  		 => 'Mandatory',
'ACP_APPLY_HEADER'  		 => 'Title',
'ACP_APPLY_QUESTION'  		 => 'Question',
'ACP_APPLY_QUESTION_SHOW'  		 => '(check to show)',
'ACP_APPLY_CONTENTS'  		 => 'Select Options',
'ACP_APPLY_GSEL'  			 => 'Check to add to selected guild',
'ACP_APPLY_QORDER'			 => 'Order', 
'ACP_APPLY_WHATGUILD_EXPLAIN'  => 'Adds character to indicated guild', 
'ACP_APPLY_TEMPLATEEDIT_SUCCESS'  	=> 'Template %s edited successfully',
'APPLY_ACP_APPTEMPLATELIST_EXPLAIN'	=> 'The Apply form can either be called by simply making a new post in the forum or directly by calling url http://www.domain.tld/apply.php?template_id=7; make as many links on your forum as you have templates. ',
'TEMPLATE_ID'				=> 'Template ID', 
'APPLY_ACP_APPTEMPLATELIST' => 'Templates', 

/***** ACP templatelines *****/
'APPLY_ACP_TEMPLATELINES'  => 'Template lines', 
'APPLY_CHGCHAR_EXPLAIN' 	=> 'Choose between game, region, realm, name, class, race, gender',
'APPLY_ACP_CHARNAME' 		=> 'Character name',
'APPLY_ACP_REALM' 			=> 'Region, Realm',
'APPLY_ACP_REGION' 			=> 'Region',
'APPLY_GAME' => 'Game, Race, Class',
'APPLY_REGION' => 'Realm, Region',
'APPLY_NAME' => 'Name',
'APPLY_LEVEL'  => 'Level',
'APPLY_CLASS'  => 'Class',
'APPLY_RACE'  => 'Race',
'APPLY_TALENT'  => 'Talent',
'APPLY_PROFF'  =>  'Profession',
'APPLY_GENDER'  =>  'Gender',
'EU'  =>  'Europe',
'US'  =>  'America',
'KR'  =>  'Korea',
'TW'  =>  'Taiwan',
'CN'  =>  'China',
'SEA'  => 'Oceania',


/***** Questionnaire ******/
'APPLY_MENU' => 'Application Form',
'APPLY_TITLE' => 'Application Form',
'APPLY_INFO' => '[size=150]Welcome and thank you for choosing us as a possible home for your character. 
To help us with your application please answer the questions below. Enter your character name exactly as it appears in the armory. [/size] ',
'APPLY_PUBLICQUESTION' => 'Visible Application ?', 
'APPLY_REQUIRED'  => 'You need to fill in : %s', 
'MANDATORY'	=> '*',	
'APPLY_QUESTION'  => 'Question ',
'APPLY_ANSWER'  => 'Answer ',
'TEMPLATE'	=> 'Template', 
'CONFIRM_DELETE_TEMPLATE'	=> 'Please confirm deletion of template %s. ', 
'ALERT_TEMPLATENAME_EMPTY'	=> 'Template name can’t be empty',
'ALERT_NOTEMPLATE'	=> 'No Apply template found',
'ALERT_NOQUESTIONS'	=> 'No questions found for template %s',
'APPLY_ACP_APPTEMPLATELINES'  => 'Application Template lines',

/***** ACP Armory settings *****/
'APPLY_CHGMAND' 			=> 'Change Questionnaire here. ',
'APPLY_CHGMAND_EXPLAIN' 	=> 'Change the type mandatory check, question type (h3 Title, Inputbox, Textbox, Textbox with bbcode buttons, selectbox, radiobuttons or checkboxes), Title and question.  <br /> If Selectbox, radiobutton or checkbox, separate the options with a comma "," with no spaces.(ex. 1,2,3,4,5") ',
'APPLY_ACP_NEWQUESTION' 	=> 'Enter new questions here.',
'APPLY_ACP_NEWQUESTION_EXPLAIN' => 'Check if mandatory, select the type (Inputbox, Textbox, Textbox with bbcode buttons, Selectbox, Radiobuttons or Checkboxes), enter the title, question. <br /> Separate the checkbox, radiobutton, or selectbox options with a comma "," with no spaces.', 
'APPLY_ACP_INPUTBOX' 		=> 'Inputbox',	
'APPLY_ACP_TXTBOX' 			=> 'Textbox', 
'APPLY_ACP_TXTBOXBBCODE'	=> 'Textbox with bbcode',
'APPLY_ACP_SELECTBOX' 		=> 'Selectbox',
'APPLY_ACP_RADIOBOX' 		=> 'Radiobuttons',
'APPLY_ACP_CHECKBOX' 		=> 'Checkboxes',
'APPLY_ACP_TITLE'			=> 'Title', 

//warnings
'APPLY_ACP_RETURN' 			=> '<h3>Return to Application config.</h3>',
'APPLY_ACP_REALMBLANKWARN' 	=> 'Realm field cannot be blank.', 
'APPLY_ACP_SETTINGSAVED' 	=> 'Application form general settings saved',
'APPLY_NO_GUILD'			=> 'No Guild', 

//upd
'APPLY_ACP_TWOREALM' 		=> 'You can not have two of realms or character names.', 
'APPLY_ACP_QUESTUPD' 		=> 'Apply Questions Updated',
//addnew
'APPLY_ACP_ORDQUEST' 		=> 'You need to fill out order, question and options before adding.',
'APPLY_ACP_QUESTNOTADD' 	=> 'ERROR : New question not added !', 
'APPLY_ACP_QUESTNADD' 		=> 'New question added.',   
'APPLY_ACP_EXPLAINOPTIONS' 	=> 'Seperate Options with a comma "," with no spaces.',  
'APPLY_ACP_TEMPLATEADD' 	=> 'New template added.', 
'REQUIRED'					=> 'Required', 


/** ACP settings for posting template **/
'APPLY_COLORSETTINGS' 		=> 'Apply Color Settings',
'APPLY_POST_ANSWERCOLOR' 	=> 'Posting Answers color',
'APPLY_POST_QUESTIONCOLOR' 	=> 'Posting Questions color',
'APPLY_FORMCOLOR'			=> 'Form Questions Color',
'APPLY_POSTCOLOR'			=> 'Apply Posting and Application Form Colors',
'APPLY_POSTQUESTIONCOLOR_EXPLAIN' 	=> 'Color of texts used in the questions of the posted Apply Form.',
'APPLY_POSTANSWERCOLOR_EXPLAIN' 	=> 'Color of texts used in the answers of the posted Apply Form.',

/** posting template **/
'APPLY_CHAR_OVERVIEW' 		=> 'Character',
'APPLY_CHAR_MOTIVATION' 	=> 'Motivation',

'APPLY_CHAR_NAME' 	=> '[color=%s][b]Character name : [/b][/color]%s',
'APPLY_CHAR_LEVEL' 	=> '[color=%s]Character level : [/color]%s',  
'APPLY_CHAR_CLASS' 	=> '[color=%s]Character class : [/color]%s' ,
'APPLY_CHAR_PROFF' 	=> '[color=%s][u]Professions :[/u][/color]
%s',
'APPLY_CHAR_BUILD' 	=> '[color=%s][u]Talent build : [/u][/color]%s',
'APPLY_CHAR_URL' => '[color=%s][/color][url=%s]Armory Link[/url]', 
'APPLY_ERROR_NAME'  =>  'Error : Name must be Alphabetic. ',
'APPLY_REQUIRED_LEVEL'  => 'Level is required. ', 
'APPLY_REQUIRED_NAME'	=> 'Name is required. ', 
'RETURN_APPLY'  =>  'Return to Application',

/** installer **/
'APPLY_INSTALL_MOD' =>  'Application Mod version %s installed successfully. ',
'APPLY_UNINSTALL_MOD' =>  'Application Mod version %s uninstalled successfully. ',
'APPLY_UPD_MOD' =>  'Application Mod updated successfully to version %s',
'UMIL_CACHECLEARED' => 'Template, Theme, Imageset caches cleared', 
'APPLY'		=> 'Apply', 
'ERROR_MINIMUM133' => 'Minimum version required for upgrades is 1.3.3',

'DEFAULT_Q1' => 'What can you bring to us and what do you expect ?', 
'DEFAULT_Q2' => 'Comment on your build, Glyph set, gear.', 
'DEFAULT_Q3' => 'Describe your raid experience ', 
'DEFAULT_Q4' => 'Reason for leaving your current guild ?', 
'DEFAULT_Q5' => 'link to your raid logs.', 
'DEFAULT_Q6' => 'Please list your alts.', 
'DEFAULT_Q7' => 'Check the days you’re available', 
'DEFAULT_Q8' => 'Can you agree with our raid times 7:30pm to 11pm Server time (UTC+1) ?', 
'DEFAULT_Q9' => 'Is it good enough to maintain a high FPS? what’s the spec ?', 
'DEFAULT_Q10' => 'Can you tell us abit about yourself please ?', 
'DEFAULT_Q11' => 'Are you underage ? Check yes or no', 

'DEFAULT_H1' => 'Motivation',  
'DEFAULT_H2' => 'Build, and Gear',  
'DEFAULT_H3' => 'Raiding Experience',  
'DEFAULT_H4' => 'Guild history',  
'DEFAULT_H5' => 'Ranks & logs',  
'DEFAULT_H6' => 'Alts',  
'DEFAULT_H7' => 'Raiding Schedule',  
'DEFAULT_H8' => 'Raid times',  
'DEFAULT_H9' => 'Computer Specifications',  
'DEFAULT_H10' => 'Person information',  
'DEFAULT_H11' => 'Age',  

'DEFAULT_O8' => 'monday,tuesday,wednesday,thursday,friday,saturday,sunday',  
'DEFAULT_O11' => 'yes,no',  

'MEMBER_COMMENT' => 'Candidate'
));

?>
