<?php
/**
* language file Application form English
* @author Sajaki
* @package bbDkp
* @copyright (c) 2009 bbDkp <http://code.google.com/p/bbdkp/>
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @version 1.3.7
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

/***** Questionnaire ******/
'APPLY_MENU' => 'Форма заявки',
'APPLY_TITLE' => 'Форма заявки',
'APPLY_INFO' => '[size=150]Welcome and thank you for choosing us as a possible home for your character. 
To help us with your application please answer the questions below. Enter your character name exactly as it appears in the armory. [/size] ',
'APPLY_PUBLICQUESTION' => 'Заявка видима ?', 
'APPLY_REQUIRED'  => 'Вы должны заполнить все необходимые поля. ', 
'MANDATORY'	=> 'Необходимо',	
'APPLY_REALM' => 'Реалм (blank for ',
'APPLY_NAME' => 'Игровой ник Вашего персонажа: ',
'APPLY_QUESTION'  => 'Вопрос ',
'APPLY_ANSWER'  => 'Ответ ',
'APPLY_REALM1' => 'Игровой мир Вашего персонажа: ',
'APPLY_LEVEL'  => 'Уровень: ',
'APPLY_ARMORY_LINK'  => 'Армори: ',
'APPLY_SPEC'  => 'Специализация: ',
'APPLY_CLASS'  => 'Класс: ',
'APPLY_RACE'  => 'Раса: ',
'APPLY_TALENT'  => 'Талант: ',
'APPLY_PROFF'  =>  'Профессия: ',
'TEMPLATE'	=> 'Template', 
'CONFIRM_DELETE_TEMPLATE'	=> 'Please confirm deletion of template %s. ', 
'ALERT_TEMPLATENAME_EMPTY'	=> 'Template name can’t be empty',

/***** ACP Armory settings *****/
'APPLY_ACP_TEMPLATESETTING'	=> 'Настройки шаблона',
'APPLY_WELCOME'				=> 'Сообщение приветствия',
'APPLY_WELCOME_EXPLAIN'		=> 'Message shown on top of Apply. supports bbcodes. ',
'APPLY_ACP_CHARNAME' 		=> 'Игровой ник Вашего персонажа',
'APPLY_ACP_REALM' 			=> 'Игровой мир Вашего персонажа',
'APPLY_ACP_REGION' 			=> 'Регион',
'APPLY_ACP_APPTEMPLATELIST'	=> 'Application templates', 
'APPLY_ACP_APPTEMPLATELIST_EXPLAIN'	=> 'the template ID is needed as a parameter for apply.php : for example template id 7 is called by url http://www.myguild.org/apply.php?template_id=7; make as many links on your forum as you have templates. ',
'TEMPLATE_ID'				=> 'Template ID', 

/***** ACP template settings *****/
'APPLY_ACP_FORUM_PREF_EXPLAIN'		=> 'decides in which forum the application will be published. Set up the forum acccess priveleges beforehand.',
'APPLY_ACP_FORUM_PUB'		=> 'Forum',
'APPLY_ACP_PUBLIC'			=> 'Public',
'APPLY_ACP_GUESTPOST' 		=> 'Can guests posts? :',
'APPLY_ACP_GUESTPOST_EXPLAIN' 	=> 'If you set Guest posting On, don’t forget to set "Enable visual confirmation for guest postings:" to "Yes".' ,  
'ACP_APPLY_MANDATORY'  		 => 'Mandatory',
'ACP_APPLY_HEADER'  		 => 'Заголовок',
'ACP_APPLY_QUESTION'  		 => 'Вопрос',
'ACP_APPLY_CONTENTS'  		 => 'Варианты выбора',
'ACP_APPLY_DEFAULTT'  		 => 'Текст по умолчанию',
'ACP_APPLY_WHATGUILD_EXPLAIN' 	 => 'Select the Guild to which a candidate will be added.',
'ACP_APPLY_GNONE'  			 => 'add to None',
'ACP_APPLY_GSEL'  			 => 'add to selected guild',


'ACP_DKP_APPLY_EXPLAIN'  	=> 'Here you can set all preferences of the Recruitment Mod',
'APPLY_ACP_APPTEMPLATELINES'  => 'Template lines', 
'APPLY_CHGMAND' 			=> 'Change Questionnaire here. ',
'APPLY_CHGMAND_EXPLAIN' 	=> 'Change the type mandatory check, question type (Inputbox, Textbox, Textbox with bbcode buttons, selectbox, radiobuttons or checkboxes), Title and question.  <br /> In case of a Selectbox, radiobutton or checkbox, separate the options with a comma "," with no spaces.(ex. 1,2,3,4,5") ',
'APPLY_ACP_NEWQUESTION' 	=> 'Enter new questions here.',
'APPLY_ACP_NEWQUESTION_EXPLAIN' => 'Check if mandatory, select the type (Inputbox, Textbox, Textbox with bbcode buttons, Selectbox, Radiobuttons or Checkboxes), enter the title, question. <br /> Separate the checkbox, radiobutton, or selectbox options with a comma "," with no spaces.', 
'APPLY_ACP_INPUTBOX' 		=> 'Inputbox',	
'APPLY_ACP_TXTBOX' 			=> 'Textbox', 
'APPLY_ACP_TXTBOXBBCODE'	=> 'Textbox with bbcode',
'APPLY_ACP_SELECTBOX' 		=> 'Selectbox',
'APPLY_ACP_RADIOBOX' 		=> 'Radiobuttons',
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
'APPLY_ACP_QUESTNOTADD' 	=> 'ERROR : New question not added !', 
'APPLY_ACP_QUESTNADD' 		=> 'New question added.',   
'APPLY_ACP_EXPLAINOPTIONS' 	=> 'Seperate Options with a comma "," with no spaces.',  
'APPLY_ACP_TEMPLATEADD' 	=> 'New template added.', 
'REQUIRED'					=> 'Required', 


/** ACP settings for posting template **/
'APPLY_COLORSETTINGS' 		=> 'Настройки цвета заявки',
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
'APPLY_CHAR_PROFF' 	=> '[color=%s][u]Professions :[/u][/color]%s',
'APPLY_CHAR_BUILD' 	=> '[color=%s][u]Talent build : [/u][/color]%s',
'APPLY_CHAR_URL' => '[color=%s][/color][url=%s]Armory Link[/url]', 
'APPLY_ERROR_NAME'  =>  'Ошибка : Имя должно содержать только буквы (a-zA-Zа-яА-ЯàäåâÅÂçÇéèëêïÏîÎæŒæÆÅóòÓÒöÖôÔøØüÜ разрешены). ',
'APPLY_REQUIRED_LEVEL'  => 'Level is required. ', 
'APPLY_REQUIRED_NAME'	=> 'Пожалуйста заполните поле "Игровой ник Вашего персонажа". ', 
'APPLY_REQUIRED_REALM'	=> 'Пожалуйста выберите игровой мир Вашего персонажа. ', 
'APPLY_REQUIRED_SPEC'	=> 'Пожалуйста укажите специализацию Вашего персонажа". ',
'APPLY_REQUIRED_CLASS'	=> 'Пожалуйста выберите класс Вашего персонажа". ', 
'RETURN_APPLY'  =>  'Return to Application',

/** installer **/
'APPLY_INSTALL_MOD' =>  'Application Mod version %s installed successfully. ',
'APPLY_UNINSTALL_MOD' =>  'Application Mod version %s uninstalled successfully. ',
'APPLY_UPD_MOD' =>  'Application Mod updated successfully to version %s',
'UMIL_CACHECLEARED' => 'Template, Theme, Imageset caches cleared', 
'APPLY'		=> 'Apply', 
'ERROR_MINIMUM133' => 'Minimum version required for upgrades is 1.3.3',
'DEFAULT_Q1' => 'Can you tell us abit about yourself please ?', 
'DEFAULT_Q2' => 'Please list your alts.', 
'DEFAULT_Q3' => 'Reason for leaving your current guild ?', 
'DEFAULT_Q4' => 'What can you bring to us and what do you expect ?', 
'DEFAULT_Q5' => 'Comment on your build, Glyph set, gear.', 
'DEFAULT_Q6' => 'Describe your raid experience ', 
'DEFAULT_Q7' => 'link to your raid logs.', 
'DEFAULT_Q8' => 'Check the days you’re available', 
'DEFAULT_Q9' => 'Can you agree with our raid times 7:30pm to 11pm Server time (UTC+1) ?', 
'DEFAULT_Q10' => 'Is it good enough to maintain a high FPS? what’s the spec ?', 
'DEFAULT_Q11' => 'Are you underage ? Check yes or no', 
'DEFAULT_H1' => 'Personal Info',  
'DEFAULT_H2' => 'Alts',  
'DEFAULT_H3' => 'Guild history',  
'DEFAULT_H4' => 'Motivation',  
'DEFAULT_H5' => 'Build, Glyphs, Gear',  
'DEFAULT_H6' => 'Raid experience ',  
'DEFAULT_H7' => 'Ranks and WOL logs',  
'DEFAULT_H8' => 'Raid Days',  
'DEFAULT_H9' => 'Raid times',  
'DEFAULT_H10' => 'Computer/Connection info',  
'DEFAULT_H11' => 'Age',  
'DEFAULT_O8' => 'monday,tuesday,wednesday,thursday,friday,saturday,sunday',  
'DEFAULT_O11' => 'yes,no',  


));

?>
