<?php
/**
* This acp manages Guild Applications 
* Application form created by Kapli (bbDKP developer)
*
* @package bbDkp.acp
* @author Kapli
* @version $Id$
* @copyright (c) 2009 bbdkp http://code.google.com/p/bbdkp/
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* 
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}
if (! defined('EMED_BBDKP')) 
{
    trigger_error($user->lang['BBDKPDISABLED'], E_USER_WARNING);
}

class acp_dkp_apply extends bbDkp_Admin
{
   public $u_action;
   
   function main($id, $mode)
   {
      global $db, $user, $template, $cache;
      global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx; 
      
      $user->add_lang(array('common'));
	  $user->add_lang(array('mods/dkp_admin'));
	  $user->add_lang(array('mods/dkp_common'));
	  $user->add_lang(array('mods/apply'));
	  
	  $form_key = 'dkp_apply';
	  add_form_key($form_key);
	  
	  switch($mode)
      {
       		/***
             * APPLICATIONCONFIG
             * 
             */
            case 'apply_settings' :
                $link = '<br /><a href="' . append_sid("index.$phpEx", "i=dkp_apply&amp;mode=apply_settings") . '">' . $user->lang['APPLY_ACP_RETURN'] . '</a>';
                $armsettings = (isset($_POST['savearm'])) ? true : false;
                $prisettings = (isset($_POST['savepri'])) ? true : false; 
                $update = (isset($_POST['update'])) ? true : false;
                $addnew = (isset($_POST['add'])) ? true : false;
				$colorsettings = (isset($_POST['updatecolor'])) ? true : false;
				$move_up = (isset($_GET['move_up'])) ? true : false;
				$move_down = (isset($_GET['move_down'])) ? true : false;
				$delete = (isset($_GET['delete'])) ? true : false;  
				
				
				/*
                 * privacy settings handler
                 */
               if($prisettings)
               {
               		if (!check_form_key($form_key))
					{
						trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
					}
                    set_config('bbdkp_apply_forum_id_public', request_var('app_id_pub', 0), true );	
                    set_config('bbdkp_apply_forum_id_private', request_var('app_id_pri', 0), true );	
                    set_config('bbdkp_apply_visibilitypref', request_var('priv', 0), true );
                    set_config('bbdkp_apply_forumchoice', request_var('forumchoice', 0), true );
                    set_config('bbdkp_apply_guests', request_var('guests', ''), true );	
                    $cache->destroy('config');
                    trigger_error($user->lang['APPLY_ACP_SETTINGSAVED'] . $link);
               }
                /*
                 * armory settings handler
                 */
               if($armsettings)
               {
                    if (!check_form_key($form_key))
					{
						trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
					}               	
               		$text = utf8_normalize_nfc(request_var('welcome_message', '', true));
					$uid = $bitfield = $options = ''; // will be modified by generate_text_for_storage
					$allow_bbcode = $allow_urls = $allow_smilies = true;
					generate_text_for_storage($text, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
					$sql = 'UPDATE ' . APPHEADER_TABLE . " SET 
							announcement_msg = '" . (string) $db->sql_escape($text) . "' , 
							announcement_timestamp = ".  (int) time() ." , 
							bbcode_bitfield = 	'".  (string) $bitfield ."' , 
							bbcode_uid = 		'".  (string) $uid ."'  
							WHERE announcement_id = 1";
					$db->sql_query($sql);
					
                    if (!isset($_POST['realm']) or request_var('realm', '') == '') 
                    {
                        trigger_error( $user->lang['APPLY_ACP_REALMBLANKWARN'] . adm_back_link($this->u_action), E_USER_WARNING);
                    }
                    set_config('bbdkp_apply_realm', utf8_normalize_nfc(str_replace(" ", "+", request_var('realm','', true)))  , true );	
                    set_config('bbdkp_apply_region', request_var('region', ''), true );	
					set_config('bbdkp_apply_gchoice', request_var('guild_choice', ''), true );	
                    $cache->destroy('config');
                    trigger_error($user->lang['APPLY_ACP_SETTINGSAVED'] . $link);
               }
               
               $qid = request_var('id', 0);
      		   //user pressed question order arrows
               if ($move_down or $move_up)
				{
					//find order of clicked line
					$sql = 'SELECT qorder FROM ' . APPTEMPLATE_TABLE . ' where id =  ' . $qid; 
					$result = $db->sql_query($sql);
					$current_order = (int) $db->sql_fetchfield('qorder', 0, $result);
					$db->sql_freeresult($result);
	
					if ($move_down)
					{
						$new_order = $current_order + 1; 
					}
					elseif($move_up) 
					{
						$new_order = $current_order - 1;
					}
	
					// find current id with new order and move that one notch, if any
					$sql = 'UPDATE  ' . APPTEMPLATE_TABLE . ' SET qorder = ' . $current_order . ' WHERE qorder = ' . $new_order;
					$db->sql_query($sql);
					
					// now increase old order
					$sql = 'UPDATE  ' . APPTEMPLATE_TABLE . ' set qorder = ' . $new_order . ' where id = ' . $qid; 
					$db->sql_query($sql);
					
					meta_refresh(1, $this->u_action);			
				}

                      
               if($delete)
               {
					//delete question handler
					$sql = "DELETE FROM " . APPTEMPLATE_TABLE . " WHERE id = '" . $qid . "'";
		            $db->sql_query($sql);
		            trigger_error("Question " . $qid . " deleted" . $link, E_USER_WARNING);
               }
               				
				//user pressed update questions
				if ($update) 
                {
					if (!check_form_key($form_key))
					{
						trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
					}        
                	
					$q_types = utf8_normalize_nfc(request_var('q_type', array( 0 => ''), true));   
					$q_headers = utf8_normalize_nfc(request_var('q_header', array( 0 => ''), true));
					$q_questions = utf8_normalize_nfc(request_var('q_question', array( 0 => ''), true));
					$q_options = utf8_normalize_nfc(request_var('q_options', array( 0 => ''), true));
					$q_defaultt = utf8_normalize_nfc(request_var('q_defaultt', array( 0 => ''), true));

					foreach ($q_questions as $key => $arrvalues) 
					{
						if (  $q_questions[$key] == $user->lang['APPLY_ACP_REALM'] || $q_questions[$key] == $user->lang['APPLY_ACP_CHARNAME']) 
                        {
                            trigger_error($user->lang['APPLY_ACP_TWOREALM'] . adm_back_link($this->u_action), E_USER_WARNING);
                        }
						
						$data = array(
							'mandatory' => isset ( $_POST ['q_mandatory'][$key] ) ? 'True' : 'False',		
						);
						$sql = 'UPDATE ' . APPTEMPLATE_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $data) . ' WHERE id = '. $key ;
						$db->sql_query($sql);
						
	                   /* updating questions */
						$data = array(
								'type' 		=> $q_types[$key],
								'header' 	=> $q_headers[$key],
								'question' 	=> $q_questions[$key],
								'options' 	=> $q_options[$key],
								'defaultt' 	=> $q_defaultt[$key],
						);
						
						$sql = 'UPDATE ' . APPTEMPLATE_TABLE . ' set ' . $db->sql_build_array('UPDATE', $data) . ' WHERE id = ' . $key;								
						$db->sql_query($sql);	
							
					}
					
                    trigger_error( $user->lang['APPLY_ACP_QUESTUPD']  . $link);    
                }
                

                if ($addnew) 
                {
					if (!check_form_key($form_key))
					{
						trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
					}        
                    /* You can not have two of realms or character names. */
                    if (request_var('app_add_question','') == $user->lang['APPLY_ACP_REALM'] || 
                    	request_var('app_add_question','') == $user->lang['APPLY_ACP_CHARNAME']) 
                    {
                        trigger_error($user->lang['APPLY_ACP_TWOREALM']  . adm_back_link($this->u_action), E_USER_WARNING);
                    }
                    
                    $sql = 'SELECT max(qorder) + 1 as maxorder  FROM ' . APPTEMPLATE_TABLE; 
					$result = $db->sql_query($sql);
					$max_order = (int) $db->sql_fetchfield('maxorder', 0, $result);
					$db->sql_freeresult($result);
                    
					
                    $sql_ary = array(
                        'qorder'     	=> $max_order, 
    				 	'header'   		=> utf8_normalize_nfc (request_var('app_add_title', ' ', true )),
                    	'question'   	=> utf8_normalize_nfc (request_var('app_add_question', ' ', true )),
                        'options'   	=> utf8_normalize_nfc (request_var('app_add_options', ' ', true )),
						'category'   	=> utf8_normalize_nfc (request_var('app_add_category', ' ', true )), 						
						'defaultt'   	=> utf8_normalize_nfc (request_var('app_add_defaultt', ' ', true )),
                        'type'       	=> utf8_normalize_nfc (request_var('app_add_type', ' ', true )),
                        'mandatory' 	=> (isset($_POST['app_add_mandatory']) ? 'True': 'False'),
                    );
                    
                    // insert new question
                    $sql = 'INSERT INTO ' . APPTEMPLATE_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
                    $result = $db->sql_query($sql);
                    
                    if (!$result)
                    {
                        trigger_error( $user->lang['APPLY_ACP_QUESTNOTADD'] . $link, E_USER_WARNING);
                    }
                    else 
                    {
                        trigger_error( $user->lang['APPLY_ACP_QUESTNADD']  .  $link, E_USER_WARNING);    
                    }
                        
                    
                }
                
         		/*
                 * color settings handler
                 */
               if($colorsettings)
               {
					if (!check_form_key($form_key))
					{
						trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
					}        
					$colorid = request_var('app_textcolors', ''); 
					$newcolor = request_var('applyquestioncolor', '');
					switch 	($colorid)
					{
						case 'postqcolor':
		               		set_config('bbdkp_apply_pqcolor', $newcolor, true );	
							break;
						case 'postacolor':
		               		set_config('bbdkp_apply_pacolor', $newcolor, true );	
		               		break;
						case 'formqcolor':
		               		set_config('bbdkp_apply_fqcolor', $newcolor, true );	
							break;
					}
                    $cache->destroy('config');
               }

                
                /*
				 * loading config
				 */
                
				// get welcome msg
				$sql = 'SELECT announcement_msg, bbcode_bitfield, bbcode_uid FROM ' . APPHEADER_TABLE;
				$db->sql_query($sql);
				$result = $db->sql_query($sql);
				while ( $row = $db->sql_fetchrow($result) )
				{
					$text = $row['announcement_msg'];
					$bitfield = $row['bbcode_bitfield'];
					$uid = $row['bbcode_uid'];
				}
				$textarr = generate_text_for_edit($text, $uid, $bitfield, 7);
				
				
                $template->assign_vars(array(
                	'WELCOME_MESSAGE' 		=> $textarr['text'],
                	'REALM'        			=> str_replace("+", " ", $config['bbdkp_apply_realm']), 
                	'PUBLIC_YES_CHECKED' 	=> ( $config['bbdkp_apply_visibilitypref'] == '1' ) ? ' checked="checked"' : '',
    				'PUBLIC_NO_CHECKED'  	=> ( $config['bbdkp_apply_visibilitypref'] == '0' ) ? ' checked="checked"' : '', 
                	'FORUM_CHOICE_YES_CHECKED' 	=> ( $config['bbdkp_apply_forumchoice'] == '1' ) ? ' checked="checked"' : '',
    				'FORUM_CHOICE_NO_CHECKED' 	=> ( $config['bbdkp_apply_forumchoice'] == '0' ) ? ' checked="checked"' : '',                 
                	'APPLY_VERS' 		 	=> $config['bbdkp_apply_version'], 
      				'POSTQCOLOR'			=> $config['bbdkp_apply_pqcolor'],
	                'POSTACOLOR'			=> $config['bbdkp_apply_pacolor'],
	                'FORMQCOLOR'			=> $config['bbdkp_apply_fqcolor'], 
					// loading forumlist
					'APPLY_FORUM_PUB_OPTIONS' => make_forum_select($config['bbdkp_apply_forum_id_public'],false, false, true),
					'APPLY_FORUM_PRIV_OPTIONS' => make_forum_select($config['bbdkp_apply_forum_id_private'],false, false, true),
                
                ));
                

                //region
                $template->assign_block_vars('region', array(
                	'VALUE' 	=> 'EU' , 
                	'SELECTED' 	=> ('EU' == $config['bbdkp_apply_region']) ? ' selected="selected"' : '' , 
                	'OPTION' 	=> 'EU'));
                
                $template->assign_block_vars('region', array(
                	'VALUE' 	=> 'US' , 
                	'SELECTED' 	=> ('US' == $config['bbdkp_apply_region']) ? ' selected="selected"' : '' , 
                	'OPTION' 	=> 'US'));
                
                //guests
				$template->assign_block_vars('guests', array(
                	'VALUE' 	=> 'True' , 
                	'SELECTED' 	=> ('True' == $config['bbdkp_apply_guests']) ? ' selected="selected"' : '' , 
                	'OPTION' 	=> 'True'));
                
                $template->assign_block_vars('guests', array(
                	'VALUE' 	=> 'False' , 
                	'SELECTED' 	=> ('False' == $config['bbdkp_apply_guests']) ? ' selected="selected"' : '' , 
                	'OPTION' 	=> 'False'));
                
                // where do we put candidate ? 
                 $template->assign_block_vars('guild', array(
                	'VALUE' 	=> '0' , 
                	'SELECTED' 	=> ('0' == $config['bbdkp_apply_gchoice']) ? ' selected="selected"' : '' , 
                	'OPTION' 	=> $user->lang['ACP_APPLY_GNONE']));
                
                $template->assign_block_vars('guild', array(
                	'VALUE' 	=> '1' , 
                	'SELECTED' 	=> ('1' == $config['bbdkp_apply_gchoice']) ? ' selected="selected"' : '' , 
                	'OPTION' 	=> $user->lang['ACP_APPLY_GSEL']));
                
               /*
                * loading questions
                */
                $sql = 'SELECT * FROM ' . APPTEMPLATE_TABLE . ' ORDER BY qorder';
                
                $result = $db->sql_query($sql);
                while ($row = $db->sql_fetchrow($result)) 
                {
                    $checked = '';
                    if ($row['mandatory'] == 'True') 
                    {
                        $checked = 'checked="checked"';
                    }
                    
                    $template->assign_block_vars('apptemplate', array(
                    	'QORDER'         => $row['qorder'] , 
                    	'HEADER'      	 => $row['header'] ,
                    	'QUESTION'       => $row['question'] , 
                    	'MANDATORY'      => $row['mandatory'] , 
                        'OPTIONS'        => $row['options'] ,
						'DEFAULTT'       => $row['defaultt'] ,
                    	'CHECKED'        => $checked,
                    	'ID'			 => $row['id'] ,
	                    'U_MOVE_UP'		 => append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_apply&amp;mode=apply_settings&amp;move_up=1&amp;id={$row['id']}"), 
						'U_MOVE_DOWN'	 => append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_apply&amp;mode=apply_settings&amp;move_down=1&amp;id={$row['id']}"),
                    	'U_DELETE'		 => append_sid("{$phpbb_admin_path}index.$phpEx", "i=dkp_apply&amp;mode=apply_settings&amp;delete=1&amp;id={$row['id']}"),
                      ));
                    
                    $type = array('Inputbox' , 'Textbox', 'Selectbox', 'Radiobuttons', 'Checkboxes');
                    foreach ($type as $t_name => $t_value) 
                    {
                        $template->assign_block_vars('apptemplate.template_type', array(
                        	'TYPE' => $t_value , 
                        	'SELECTED' => ($t_value == $row['type']) ? ' selected="selected"' : '' , 
                        	));
                    }
                }
                $db->sql_freeresult($result);
                
                
                $this->page_title = $user->lang['ACP_DKP_APPLY']; 
                $this->tpl_name = 'dkp/acp_' . $mode;
                break;
      }
   }
 
}

?>