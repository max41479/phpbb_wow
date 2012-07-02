<?php
/**
* language file Application form English
* @author Sajaki
* @package bbDkp
* @copyright (c) 2009 bbDkp <http://code.google.com/p/bbdkp/>
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @version $Id$
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

/****** installer ********/
'APPLY_INSTALL_MOD' =>  'Application Mod version %s installed successfully. ',
'APPLY_UNINSTALL_MOD' =>  'Application Mod version %s uninstalled successfully. ',
'APPLY_UPD_MOD' =>  'Application Mod updated successfully to version %s',
'UMIL_CACHECLEARED' => 'Template, Theme, Imageset caches cleared', 

/***** Questionnaire ******/
'APPLY_MENU' => 'Форма заявки',
'APPLY_TITLE' => 'Форма заявки',
'APPLY_INFO' => '[size=150]Welcome and thank you for choosing us as a possible home for your character. 
To help us with your application please answer the questions below. Enter your character name exactly as it appears in the armory. [/size] ',
'APPLY_PUBLICQUESTION' => 'Visible Application ?', 
'APPLY_REQUIRED'  => 'Вы должны заполнить все необходимые поля. ', 
'MANDATORY'	=> 'Необходимо',	
'APPLY_REALM' => 'Realm (blank for ',
'APPLY_NAME' => 'Игровой ник Вашего персонажа: ',
'APPLY_QUESTION'  => 'Question ',
'APPLY_ANSWER'  => 'Answer ',
'APPLY_REALM1' => 'Игровой мир Вашего персонажа: ',
'APPLY_LEVEL'  => 'Level: ',
'APPLY_SPEC'  => 'Специализация: ',
'APPLY_CLASS'  => 'Класс: ',
'APPLY_RACE'  => 'Race: ',
'APPLY_TALENT'  => 'Talent: ',
'APPLY_PROFF'  =>  'Profession: ',

/***** ACP Privacy settings *****/
'APPLY_ACP_PRISETTING'		=> 'Privacy Settings',
'APPLY_ACP_FORUM_PUB'		=> 'Recruitment forum (Public) ',
'APPLY_ACP_FORUM_PRI'		=> 'Recruitment forum (Private) ',
'APPLY_ACP_FORUM_PRI_EXPLAIN'	=> 'Set Group forum permission of Guests and Applicants to: <br />"Post"->"Can start new topics"->"Yes",<br/> "Can read forum" -> "No" ',
'APPLY_ACP_FORUM_PREF'		=> 'User preference (Private or Public) ',
'APPLY_ACP_FORUM_PREF_EXPLAIN'		=> 'decides in which forum the application will be published.',
'APPLY_ACP_FORUM_CHOICE' =>  'Allow User to choose Privacy setting ?',
'APPLY_ACP_FORUM_CHOICE_EXPLAIN' =>  'If your guild does not allow public applications, set this to "No"',
'APPLY_ACP_PUBLIC'			=> 'Public',
'APPLY_ACP_PRIVATE'			=> 'Private',
'APPLY_ACP_GUESTPOST' 		=> 'Can guests posts? :',
'APPLY_ACP_GUESTPOST_EXPLAIN' 	=> 'If you set Guest posting On, don’t forget to set "Enable visual confirmation for guest postings:" to "Yes".' ,  

/***** ACP Armory settings *****/
'APPLY_ACP_TEMPLATESETTING'	=> 'Template Settings',
'APPLY_WELCOME'				=> 'Welcome message',
'APPLY_WELCOME_EXPLAIN'		=> 'Message shown on top of Apply. supports bbcodes. ',
'APPLY_ACP_CHARNAME' 		=> 'Игровой ник Вашего персонажа',
'APPLY_ACP_REALM' 			=> 'Игровой мир Вашего персонажа',
'APPLY_ACP_REGION' 			=> 'Region',
'APPLY_ACP_APPTEMPLATEUPD'	=> 'Update Application template', 

/***** ACP template settings *****/
'ACP_APPLY_MANDATORY'  		 => 'Mandatory',
'ACP_APPLY_HEADER'  		 => 'Title',
'ACP_APPLY_QUESTION'  		 => 'Question',
'ACP_APPLY_CONTENTS'  		 => 'Select Options',
'ACP_APPLY_WHATGUILD'  		 => 'Select how candidate will be added to memberlist. ',
'ACP_APPLY_WHATGUILD_EXPLAIN' => 'candidate will be added to lowest rank if added to guild.',
'ACP_APPLY_GNONE'  			 => 'add to None',
'ACP_APPLY_GSEL'  			 => 'add to selected guild',

'ACP_DKP_APPLY_EXPLAIN'  	=> 'Here you can set all preferences of the Recruitment Mod',
'APPLY_ACP_APPTEMPLATENEW'  => 'Application template New item', 
'APPLY_CHGMAND' 			=> 'Change Questionnaire here. ',
'APPLY_CHGMAND_EXPLAIN' 	=> 'Change the type mandatory check, question type, Title and question. The two first questions are reserved. <br /> In case of a Selectbox, radiobutton or checkbox, separate the options with a comma "," with no spaces.(ex. 1,2,3,4,5") ',
'APPLY_ACP_NEWQUESTION' 	=> 'Enter new questions here.',
'APPLY_ACP_NEWQUESTION_EXPLAIN' => 'Check if mandatory, select the type, enter the title, question. <br /> Separate the checkbox, radiobutton, or selectbox options with a comma "," with no spaces.', 
'APPLY_ACP_INPUTBOX' 		=> 'Inputbox',	
'APPLY_ACP_TXTBOX' 			=> 'Textbox', 
'APPLY_ACP_SELECTBOX' 		=> 'Selectbox',
'APPLY_ACP_RADIOBOX' 		=> 'Radiobuttons',
'APPLY_ACP_CHECKBOX' 		=> 'Checkboxes',
'APPLY_ACP_CHECKBOX' 		=> 'Checkboxes',

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
'APPLY_ACP_QUESTNOTADD' 	=> 'ERROR : New question not saved !', 
'APPLY_ACP_QUESTNADD' 		=> 'New question Saved !',   
'APPLY_ACP_EXPLAINOPTIONS' 	=> 'Seperate Options with a comma "," with no spaces.',  

/** ACP settings for posting template **/
'APPLY_COLORSETTINGS' 		=> 'Apply Color Settings',
'APPLY_POST_ANSWERCOLOR' 	=> 'Posting Answers color',
'APPLY_POST_QUESTIONCOLOR' 	=> 'Posting Questions color',
'APPLY_FORMCOLOR'			=> 'Form Questions Color',
'APPLY_POSTCOLOR'			=> 'Apply Posting and Application Form Colors',
'APPLY_POSTCOLOR_EXPLAIN' 	=> 'Color of texts used in the Form and Recruitment post. If you use a dark Style, you can vary the text color to be used here.',

/** posting template **/
'APPLY_CHAR_OVERVIEW' 		=> 'Персонаж',
'APPLY_CHAR_MOTIVATION' 	=> 'Мотивация',
'APPLY_CHAR_PERSONAL' 		=> 'Личные данные',

'APPLY_CHAR_NAME' 	=> '[color=%s][b]Character name : [/b][/color]%s',
'APPLY_CHAR_LEVEL' 	=> '[color=%s]Character level : [/color]%s',  
'APPLY_CHAR_CLASS' 	=> '[color=%s]Character class : [/color]%s' ,
'APPLY_CHAR_PROFF' 	=> '[color=%s][u]Professions :[/u][/color]
%s',
'APPLY_CHAR_BUILD' 	=> '[color=%s][u]Talent build : [/u][/color]%s',
'APPLY_CHAR_URL' => '[color=%s][/color][url=%s]Armory Link[/url]', 
'APPLY_ERROR_NAME'  =>  'Error : Name must be Alphabetic (a-zA-ZàäåâÅÂçÇéèëêïÏîÎæŒæÆÅóòÓÒöÖôÔøØüÜ are allowed). ',
'APPLY_REQUIRED_LEVEL'  => 'Level is required. ', 
'APPLY_REQUIRED_NAME'	=> 'Пожалуйста заполните поле "Игровой ник Вашего персонажа". ', 
'APPLY_REQUIRED_REALM'	=> 'Пожалуйста выберите игровой мир Вашего персонажа. ', 
'APPLY_REQUIRED_SPEC'	=> 'Пожалуйста укажите специализацию Вашего персонажа". ',
'APPLY_REQUIRED_CLASS'	=> 'Пожалуйста выберите класс Вашего персонажа". ', 
'RETURN_APPLY'  =>  'Return to Application',

));

?>
