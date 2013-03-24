<?php
/**
* This acp manages Guild Applications
* Application form created by Kapli (bbDKP developer)
*
* @package bbDkp.acp
* @author Kapli
* @copyright (c) 2009 bbdkp http://code.google.com/p/bbdkp/
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @version 1.4
*
*/

/**
 *
 * @ignore
 *
 *
 */
if (! defined ( 'IN_PHPBB' ))
{
	exit ();
}

if (! defined ( 'EMED_BBDKP' ))
{
	trigger_error ( $user->lang ['BBDKPDISABLED'], E_USER_WARNING );
}

class acp_dkp_apply extends bbDkp_Admin 
{
	public $u_action;
	private $link;
	private $form_key;
	private $apptype = array();
	private $chartype = array();
	private $regions = array();
	
	function main($id, $mode)
	{
		global $db, $user, $template, $cache;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;

		$user->add_lang ( array ('common'));
		$user->add_lang ( array ('mods/dkp_admin' ));
		$user->add_lang ( array ('mods/dkp_common'));
		$user->add_lang ( array ('mods/apply'));
		$this->link = '<br /><a href="' . append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_apply&amp;mode=apply_settings" ) . '"><h3>' . $user->lang ['APPLY_ACP_RETURN'] . '</h3></a>';

		$this->apptype = array (
				'title' => $user->lang ['APPLY_ACP_TITLE'],
				'charname' => $user->lang ['APPLY_ACP_CHARNAME'],
				'gameraceclass' => $user->lang ['APPLY_GAME'],
				'regionrealm' => $user->lang ['APPLY_REGION'],
				'level' => $user->lang ['APPLY_LEVEL'],
				'gender' => $user->lang ['APPLY_GENDER'],
				'Inputbox' => $user->lang ['APPLY_ACP_INPUTBOX'],
				'Textbox' => $user->lang ['APPLY_ACP_TXTBOX'],
				'Textboxbbcode' => $user->lang ['APPLY_ACP_TXTBOXBBCODE'],
				'Selectbox' => $user->lang ['APPLY_ACP_SELECTBOX'],
				'Radiobuttons' => $user->lang ['APPLY_ACP_RADIOBOX'],
				'Checkboxes' => $user->lang ['APPLY_ACP_CHECKBOX'],
		);

		$this->regions = array(
				'us' => $user->lang ['US'],
				'en' => $user->lang ['EU'],
				'kr' => $user->lang ['KR'],
				'tw' => $user->lang ['TW'],
				'sea' => $user->lang ['SEA']);
		
		// getting guilds
		$sql_array = array (
				'SELECT' => 'a.id, a.name, a.realm, a.region ',
				'FROM' => array (
						GUILD_TABLE => 'a',
						MEMBER_LIST_TABLE => 'b'
				),
				'WHERE' => 'a.id = b.member_guild_id and id != 0',
				'GROUP_BY' => 'a.id, a.name, a.realm, a.region',
				'ORDER_BY' => 'a.id ASC'
		);
		
		$sql = $db->sql_build_query ( 'SELECT', $sql_array );
		$result = $db->sql_query ( $sql );
		while ( $row = $db->sql_fetchrow ( $result ) )
		{
			$guilds [] = array (
					'id' => $row ['id'],
					'name' => $row ['name']
			);

			$template->assign_block_vars ( 'guild_row', array (
					'VALUE' => $row ['id'],
					'SELECTED' => '',
					'OPTION' => $row ['name']
			) );
		}
		$db->sql_freeresult ( $result );


		switch ($mode)
		{

			case 'apply_edittemplate' :

				$this->form_key = '554k6Qmm5clM3dUhq67jX0M';
				add_form_key ( $this->form_key );

				$appformsupdate = (isset ( $_POST ['update'] )) ? true : false;

				
				if($appformsupdate)
				{
					//do update and return
					if (! check_form_key ( $this->form_key ))
					{
						trigger_error ( $user->lang ['FORM_INVALID'] . adm_back_link ( $this->u_action ), E_USER_WARNING );
					}

					$applytemplate_id = request_var ( 'template_id', 0);

					$sql_ary = array (
							'template_name' => utf8_normalize_nfc ( request_var ( 'apptemplate_name', ' ', true ) ),
							'guild_id' => request_var ( 'candidate_guild_id', 0),
							'forum_id' => request_var ( 'applyforum_id', 0),
							'question_color'	=> request_var ( 'postqcolor', '#1961a9'),
							'answer_color'	=> request_var ( 'postacolor', '#4880b1'),
							'gchoice'	=> request_var ( 'gchoice', 0),
					);

					$sql = 'UPDATE ' . APPTEMPLATELIST_TABLE . ' SET ' . $db->sql_build_array ( 'UPDATE', $sql_ary ) . ' WHERE template_id = ' . $applytemplate_id;
					$db->sql_query ( $sql );

					
					// welcome text
					$welcometext = utf8_normalize_nfc ( request_var ( 'welcome_message', '', true ) );
					$uid = $bitfield = $options = ''; // will be modified by
					$allow_bbcode = $allow_urls = $allow_smilies = true;
					generate_text_for_storage ( $welcometext, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies );
					$sql = 'UPDATE ' . APPHEADER_TABLE . " SET
							announcement_msg = '" . ( string ) $db->sql_escape ( $welcometext ) . "' ,
							announcement_timestamp = " . ( int ) time () . " ,
							bbcode_bitfield = 	'" . ( string ) $bitfield . "' ,
							bbcode_uid = 		'" . ( string ) $uid . "'
							WHERE template_id = " . $applytemplate_id;
					$db->sql_query ( $sql );
					
					//colors
					
					meta_refresh ( 1, append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_apply&amp;mode=apply_settings") );
					trigger_error ( sprintf ( $user->lang ['ACP_APPLY_TEMPLATEEDIT_SUCCESS'], $applytemplate_id ) . $this->link, E_USER_NOTICE );

				}
				else
				{
					//display form
					$applytemplate_id = request_var ( 'template_id', 0 );
					
					//general template parameters
					$result = $db->sql_query ( 'SELECT * FROM ' . APPTEMPLATELIST_TABLE . ' WHERE template_id = ' . $applytemplate_id);
					$template_info = $db->sql_fetchrowset ( $result);
					$template_info=$template_info[0];
					foreach ( $guilds as $key => $guild )
					{
						if ($template_info ['guild_id'] == $guild ['id'])
						{
							$guildname = $guild ['name'];
						}
					}

					$foruminfo = $this->apply_get_forum_info($template_info['forum_id']);
					
					// get welcome msg
					$sql = 'SELECT announcement_msg, bbcode_bitfield, bbcode_uid FROM ' . APPHEADER_TABLE . ' WHERE template_id = ' . $applytemplate_id;
					$result = $db->sql_query ( $sql );
					$text = "";
					$bitfield = "";
					$uid = "";
					while ( $row = $db->sql_fetchrow ( $result ) ) 
					{
						$text = $row ['announcement_msg'];
						$bitfield = $row ['bbcode_bitfield'];
						$uid = $row ['bbcode_uid'];
					}
					$db->sql_freeresult ( $result );
					
					$textarr = generate_text_for_edit ( $text, $uid, $bitfield, 7 );
					
					$template->assign_vars ( array (
						'WELCOME_MESSAGE'	=> $textarr ['text'], 
						'TEMPLATE_ID' => $applytemplate_id,
						'FORUMNAME' => $foruminfo ['forum_name'],
						'GUILDNAME' => $guildname,
						'TEMPLATEFORUM_OPTIONS' => make_forum_select ( $template_info ['forum_id'], false, false, true ),
						'TEMPLATE_NAME' => $template_info ['template_name'],
						'POSTQCOLOR' => $template_info['question_color'], 
						'POSTACOLOR' => $template_info['answer_color'] ,
						'F_GCHOICE'	=> $template_info['gchoice'] ,
					));
					
						
				}

				$this->page_title = $user->lang ['ACP_DKP_APPLY_TEMPLATE_EDIT'];
				$this->tpl_name = 'dkp/acp_' . $mode;


				break;
						
			case 'apply_settings' :

				$this->form_key = '2uE88d0k5Jy0ZWLQV53WKO2';
				add_form_key ( $this->form_key );

				// getting template definitions
				$applytemplate_id = request_var ( 'applytemplate_id', request_var ( 'apptemplate_id_hidden', 0 ));
				
				if ($applytemplate_id == 0)
				{
					$i=0;
					// get first row
					$result = $db->sql_query ( 'SELECT * FROM ' . APPTEMPLATELIST_TABLE );
					while ( $row = $db->sql_fetchrow ( $result ) )
					{
						if ($i == 0)
						{
							$applytemplate_id = $row ['template_id'];
						}
						$i += 1;
					}
					$db->sql_freeresult ( $result );
				}

				
				/**
				 * handlers
				 */

				/*
				 * general appform settings
				 */
				if (isset ( $_POST ['appformsettings'] ))
				{
					$this->appformsettings();
				}

				/**
				 * deleting an entire template
				 */
				if (isset ( $_GET ['apptemplatedelete'] ))
				{
					$this->apptemplatedelete($applytemplate_id);
				}

				/**
				 * adding an new template
				 */
				if (isset ( $_POST ['apptemplateadd'] ))
				{
					$this->apptemplate_add();
				}

				/**
				 * adds a template question
				 */
				if (isset ( $_POST ['appformquestionadd'] ))
				{
					$this->appformquestion_add($applytemplate_id);
				}

				/**
				 * deletes a template question
				 */
				if (isset ( $_GET ['appquestiondelete'] ))
				{
					$this->question_delete();
				}

				/**
				 * updates template question
				 */
				if (isset ( $_POST ['appformquestionupdate'] ))
				{
					$this->appformquestionupdate($applytemplate_id);
				}
				
				// user pressed question order arrows
				if(isset($_GET ['appquestionmove_up'] ))
				{
					$this->movequestion(-1, $applytemplate_id);
				}
				
				if(isset($_GET ['appquestionmove_down'] ))
				{
					$this->movequestion(1, $applytemplate_id);
				}

				/**
				 * loading template types
				*/

				$result = $db->sql_query ( 'SELECT * FROM ' . APPTEMPLATELIST_TABLE );
				while ( $row = $db->sql_fetchrow ( $result ) )
				{
					foreach ( $guilds as $key => $guild )
					{
						if ($row ['guild_id'] == $guild ['id'])
						{
							$guildname = $guild ['name'];
						}
					}

					$foruminfo = $this->apply_get_forum_info ( $row ['forum_id'] );

					$template->assign_block_vars ( 'apptemplatelist', array (
							'ID' => $row ['template_id'],
							'STATUS' => $row ['status'],
							'TEMPLATE_NAME' => $row ['template_name'],
							'GUILDNAME' => $guildname,
							'FORUMID' => $foruminfo ['forum_name'],
							'SELECTED' => ($applytemplate_id == $row ['template_id']) ? ' selected = "selected"' : '',
							'FORUM_OPTIONS' => make_forum_select ( $row ['forum_id'], false, false, true ),
							'U_DELETE_TEMPLATE' => append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_apply&amp;mode=apply_settings&amp;apptemplatedelete=1&amp;template_id={$row['template_id']}" ),
							'U_EDIT_TEMPLATE' => append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_apply&amp;mode=apply_edittemplate&amp;template_id={$row['template_id']}" )
					) );
				}
				$db->sql_freeresult ( $result );

				
				/*
				 * loading app questions
				 * 12 question types supported
				*/

				foreach ( $this->apptype as $key => $value ) 
				{
					$template->assign_block_vars ( 'template_type', array (
							'TYPE' => $key,
							'VALUE' => $value,
							'SELECTED' => ($key == $applytemplate_id) ? ' selected="selected"' : ''
					) );
				}

				$sql = 'SELECT * FROM ' . APPTEMPLATE_TABLE . ' a
                		INNER JOIN ' . APPTEMPLATELIST_TABLE . ' b
		                ON b.template_id = a.template_id
		                WHERE a.template_id = ' . $applytemplate_id . '
		                ORDER BY a.qorder ';
				$result = $db->sql_query ( $sql );
				while ( $row = $db->sql_fetchrow ( $result ) )
				{
					$checked = '';
					if ($row ['mandatory'] == 'True') 
					{
						$checked = ' checked="checked"';
					}
					$questionshow = ''; 
					if ((int) $row ['showquestion'] == 1)
					{
						$questionshow = ' checked="checked"';
					}
					$titleinvisible= '';
					$optioninvisible = '';
					$questioninvisible = ''; 
					$optionenabled = '';
					
					switch ($row ['type'])
					{
						case 'title' :
							$questioninvisible = ' visibility:hidden;';
							$optionenabled = ' disabled="disabled"';
							$optioninvisible = ' visibility:hidden;';
							break;
						case 'charname' :
						case 'gameraceclass':
						case 'regionrealm' :
						case 'level' :
						case 'gender' :
							$titleinvisible = ' visibility:hidden;';
							$questioninvisible = ' visibility:hidden;';
							$optionenabled = ' disabled="disabled"';
							$optioninvisible = ' visibility:hidden;';
							break;
						case 'Inputbox':
						case 'Textbox':
						case 'Textboxbbcode':
							$optionenabled = ' disabled="disabled"';
							$optioninvisible = ' visibility:hidden;';
							break;
						case 'Selectbox':
						case 'Radiobuttons':
						case 'Checkboxes':
							break;
						
					}
					
					$template->assign_block_vars ( 'apptemplate', array (
							'QORDER' => $row ['qorder'],
							'TEMPLATE' => $row ['template_name'],
							'HEADER' => $row ['header'],
							'QUESTION' => $row ['question'],
							'TITLEINVISIBLE' => $titleinvisible, 
							'QUESTIONINVISIBLE' => $questioninvisible,
							'MANDATORY' => $row ['mandatory'],
							'OPTIONS' => $row ['options'],
							'QMANDATORY_CHECKED' => $questionshow, 
							'OPTIONDISABLED' => $optionenabled,
							'OPTIONINVISIBLE' => $optioninvisible,
							'CHECKED' => $checked,
							'ID' => $row ['id'],
							'U_APPQUESTIONMOVE_UP' => append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_apply&amp;mode=apply_settings&amp;appquestionmove_up=1&amp;id={$row['id']}&amp;applytemplate_id=" . $applytemplate_id ),
							'U_APPQUESTIONMOVE_DOWN' => append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_apply&amp;mode=apply_settings&amp;appquestionmove_down=1&amp;id={$row['id']}&amp;applytemplate_id=" . $applytemplate_id ),
							'U_APPQUESTIONDELETE' => append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_apply&amp;mode=apply_settings&amp;appquestiondelete=1&amp;id={$row['id']}&amp;applytemplate_id=" . $applytemplate_id )
					) );

					foreach ( $this->apptype as $key => $value )
					{
						$template->assign_block_vars ( 'apptemplate.template_type', array (
								'TYPE' => $key,
								'VALUE' => $value,
								'SELECTED' => ($key == $row ['type']) ? ' selected="selected"' : ''
						) );
					}
				}
				$db->sql_freeresult ( $result );
				
				$template->assign_vars ( array (
						'TEMPLATE_ID' => $applytemplate_id,
						'ADDTEMPLATEFORUM_OPTIONS' => make_forum_select ( 0, false, false, true ),
						'REALM' => str_replace ( "+", " ", $config ['bbdkp_apply_realm'] ),
						'APPLY_VERS' => $config ['bbdkp_apply_version'],
				));


				$this->page_title = $user->lang ['ACP_DKP_APPLY'];
				$this->tpl_name = 'dkp/acp_' . $mode;
				break;
		}
	}
	
	
	/**
	 * adds a new app template
	 *
	 */
	public function apptemplate_add()
	{
		global  $user, $db;
	
		$sql_ary = array (
				'status' => 1,
				'template_name' => utf8_normalize_nfc ( request_var ( 'template_name', ' ', true ) ),
				'forum_id' => request_var ( 'new_applyforum_id', 0 ),
				'question_color'	=> '#1961a9',
				'answer_color'	=> '#4880b1',
				'gchoice'	=> 1,
		);
	
		// insert new question
		$sql = 'INSERT INTO ' . APPTEMPLATELIST_TABLE . ' ' . $db->sql_build_array ( 'INSERT', $sql_ary );
		$db->sql_query ( $sql );
	
		$template_id = $db->sql_nextid();
	
		// insert standard welcome text
		$welcometext =$user->lang('APPLY_INFO');
		$uid = $bitfield = $options = ''; // will be modified by
		$allow_bbcode = $allow_urls = $allow_smilies = true;
		generate_text_for_storage ( $welcometext, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies );
	
		$data = array(
				'announcement_msg'     => $welcometext,
				'announcement_timestamp'  => (int) time (),
				'bbcode_bitfield'	=> (string) $bitfield,
				'bbcode_uid'	=> (string) $uid,
				'template_id'	=> $template_id,
	
		);
	
		$sql = 'INSERT INTO ' . APPHEADER_TABLE . ' ' . $db->sql_build_array('INSERT', $data);
		$db->sql_query($sql);
	
		meta_refresh ( 1, $this->u_action );
		trigger_error ( $user->lang ['APPLY_ACP_TEMPLATEADD'] . $this->link, E_USER_NOTICE );
	}
	

	/**
	 * deletes an entire template
	 *
	 * @param int $applytemplate_id
	 */
	public function apptemplatedelete($applytemplate_id)
	{
		global $template,$user,$db;
	
		if (confirm_box ( true ))
		{
			$hiddentemplateid = request_var ( 'hidden_template_id', 0 );
	
			// delete template
			$db->sql_query ( "DELETE FROM " . APPTEMPLATELIST_TABLE . " WHERE template_id = '" . $hiddentemplateid . "'" );
			$db->sql_query ( "DELETE FROM " . APPTEMPLATE_TABLE . " WHERE template_id = '" . $hiddentemplateid . "'" );
			$db->sql_query ( "DELETE FROM " . APPHEADER_TABLE . " WHERE template_id = '" . $hiddentemplateid . "'" );
				
			meta_refresh ( 1, $this->u_action );
			trigger_error ( "Template " . $hiddentemplateid . " deleted", E_USER_WARNING );
		}
		else
	
		{
			$s_hidden_fields = build_hidden_fields ( array (
					'apptemplatedelete' => true,
					'hidden_template_id' => $applytemplate_id
			) );
	
			$template->assign_vars ( array (
					'S_HIDDEN_FIELDS' => $s_hidden_fields
			) );
			confirm_box ( false, sprintf ( $user->lang ['CONFIRM_DELETE_TEMPLATE'], $applytemplate_id ), $s_hidden_fields );
		}
	}
	
	/**
	 * adds a new question
	 *
	 * @param int $applytemplate_id
	 */
	public function appformquestion_add($applytemplate_id)
	{
	
		global $db, $phpbb_admin_path, $phpEx, $user;
		if (! check_form_key ( $this->form_key ))
		{
			trigger_error ( $user->lang ['FORM_INVALID'] . adm_back_link ( $this->u_action ), E_USER_WARNING );
		}
	
		$sql = 'SELECT max(qorder) + 1 as maxorder, max(lineid) + 1 as maxline_id
                     	FROM ' . APPTEMPLATE_TABLE . ' WHERE template_id= ' . $applytemplate_id;
		$result = $db->sql_query ( $sql );
		$max_order = ( int ) $db->sql_fetchfield ( 'maxorder', 0, $result );
		$maxline_id = ( int ) $db->sql_fetchfield ( 'maxline_id', 0, $result );
	
		$db->sql_freeresult ( $result );
	
		$sql_ary = array (
				'template_id' => $applytemplate_id,
				'qorder' => $max_order,
				'mandatory' => (isset ( $_POST ['app_add_mandatory'] ) ? 'True' : 'False'),
				'type' => utf8_normalize_nfc ( request_var ( 'app_add_type', ' ', true ) ),
				'header' => utf8_normalize_nfc ( request_var ( 'app_add_title', ' ', true ) ),
				'question' => utf8_normalize_nfc ( request_var ( 'app_add_question', ' ', true ) ),
				'showquestion' => (isset ( $_POST ['app_add_question_mandatory'] ) ? 1 : 0),
				'options' => utf8_normalize_nfc ( request_var ( 'app_add_options', ' ', true ) ),
				'lineid' => $maxline_id
		);
	
		// insert new question
		$sql = 'INSERT INTO ' . APPTEMPLATE_TABLE . ' ' . $db->sql_build_array ( 'INSERT', $sql_ary );
		$db->sql_query ( $sql );
		$link = append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_apply&amp;mode=apply_settings&amp;applytemplate_id=".$applytemplate_id );
		$this->link = '<br /><a href="' . $link . '"><h3>' . $user->lang ['APPLY_ACP_RETURN'] . '</h3></a>';
	
		meta_refresh ( 1, $link );
	
		trigger_error ( $user->lang ['APPLY_ACP_QUESTNADD'] . $this->link, E_USER_NOTICE );
	
	}
	
	/**
	 * updates current question
	 * 
	 * @param unknown_type $applytemplate_id
	 */
	public function appformquestionupdate($applytemplate_id)
	{
		global $user, $db, $phpbb_admin_path, $phpEx;

		if (! check_form_key ( $this->form_key ))
		{
			trigger_error ( $user->lang ['FORM_INVALID'] . adm_back_link ( $this->u_action ), E_USER_WARNING );
		}

		$q_types = utf8_normalize_nfc ( request_var ( 'q_type', array (0 => '' ), true ) );
		$q_headers = utf8_normalize_nfc ( request_var ( 'q_header', array (0 => '' ), true ) );
		$q_questions = utf8_normalize_nfc ( request_var ( 'q_question', array (0 => '' ), true ) );
		$q_options = (isset ( $_POST ['q_options'] ) ? 
					utf8_normalize_nfc ( request_var ( 'q_options', array (0 => '' ), true ) ) : '')  ;

		foreach ( $q_questions as $key => $arrvalues )
		{

			/* updating questions */
			$data = array (
					'mandatory' => isset ( $_POST ['q_mandatory'] [$key] ) ? 'True' : 'False', 
					'type' => $q_types [$key],
					'header' => $q_headers [$key],
					'question' => $q_questions [$key] ,
					'showquestion' => (isset ( $_POST ['q_question_mandatory'][$key] ) ? 1 : 0),
					'options' =>  (isset ( $_POST ['q_options'][$key] ) ? $q_options [$key] : ''), 
			);

			$sql = 'UPDATE ' . APPTEMPLATE_TABLE . ' set ' . $db->sql_build_array ( 'UPDATE', $data ) . ' WHERE id = ' . $key;
			$db->sql_query ( $sql );
		}
		
		$link = append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_apply&amp;mode=apply_settings&amp;applytemplate_id=".$applytemplate_id );
		$this->link = '<br /><a href="' . $link . '"><h3>' . $user->lang ['APPLY_ACP_RETURN'] . '</h3></a>';
		meta_refresh ( 1, $link );
		
		trigger_error ( $user->lang ['APPLY_ACP_QUESTUPD'] . $this->link );
	}
	
	/**
	 * delete app template question
	 *
	 */
	public function question_delete()
	{
		global $db;
		$qid = request_var ( 'id', 0 );
		$sql = "DELETE FROM " . APPTEMPLATE_TABLE . " WHERE id = '" . $qid . "'";
		$db->sql_query ( $sql );
		meta_refresh ( 1, $this->u_action );
		trigger_error ( "Question " . $qid . " deleted" . $this->link, E_USER_WARNING );
	}

	/**
	 * movequestion: moves question up or down
	 *
	 * @param int $direction +1 or -1
	 */
	public function movequestion($direction, $applytemplate_id )
	{
		global $phpbb_admin_path, $phpEx, $db;
		$qid = request_var ( 'id', 0 );
		
		// find order of clicked line
		$sql = 'SELECT qorder FROM ' . APPTEMPLATE_TABLE . ' WHERE id =  ' . $qid ;
		$result = $db->sql_query ( $sql );
		$current_order = ( int ) $db->sql_fetchfield ( 'qorder', 0, $result );
		$db->sql_freeresult ( $result );

		$new_order = $current_order + (int) $direction;

		// find current id with new order and move that one notch, if any
		$sql = 'UPDATE  ' . APPTEMPLATE_TABLE . ' SET qorder = ' . $current_order . ' WHERE qorder = ' . $new_order . ' AND template_id = ' . $applytemplate_id;
		$db->sql_query ( $sql );

		// now increase old order
		$sql = 'UPDATE  ' . APPTEMPLATE_TABLE . ' set qorder = ' . $new_order . ' where id = ' . $qid . ' AND template_id = ' . $applytemplate_id;
		$db->sql_query ( $sql );

		$link = append_sid ( "{$phpbb_admin_path}index.$phpEx", "i=dkp_apply&amp;mode=apply_settings&amp;applytemplate_id=".$applytemplate_id );

		
	}

	/**
	 * fetches array with forum info
	 *
	 * @param int $forum_id
	 * @return array
	 */
	public function apply_get_forum_info($forum_id)
	{
		global $db;
		// get some forum info
		$sql = 'SELECT * FROM ' . FORUMS_TABLE . " WHERE forum_id = $forum_id";
		$result = $db->sql_query ( $sql );
		$row = $db->sql_fetchrow ( $result );
		$db->sql_freeresult ( $result );
		return $row;
	}
}


?>