<?php
/**
*
* @package ucp
* @version $Id$
* @copyright (c) 2007 phpBB Group
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

/**
* ucp_streams
* @package ucp
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

class ucp_streams
{
	function main($id, $mode)
	{
		global $db, $user, $auth, $template;
		global $phpbb_root_path, $phpEx;
		switch($mode)
		{
			case 'my_streams':
				$show_buttons = false;
				$show = true;
				$s_guildmembers = ' '; 
				
				//if there are no streams at all, do not show
				$sql = 'SELECT count(*) AS my_streams_count FROM ' . STREAMS_TABLE .' WHERE phpbb_user_id = ' . $user->data['user_id'];
				$result = $db->sql_query($sql, 0);
				$my_streams_count = (int) $db->sql_fetchfield('my_streams_count');
				if ($my_streams_count == 0)
				{
					$show = false;
					$no_streams = true;
					$template->assign_vars(array(
						'NO_STREAMS'		=> $no_streams,
						'STREAMS_NOT_FOUND'	=> $user->lang['STREAMS_NOT_FOUND'],
					));
				}
				else 
				{
					// list all characters bound to me
					$this->list_my_streams();
				}
				$db->sql_freeresult($result);
									
				// These template variables are used on all the pages
				$template->assign_vars(array(
					'S_SHOW'				=> $show,
					'U_ADD'			=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=streams&amp;mode=manage_streams"),
				));
				
				$this->page_title = 'UCP_STREAMS';
				$this->tpl_name = 'ucp_my_streams';
				break;
			case 'manage_streams':
				//SHOW ADD STREAM FORM
				//get stream_id if selected from pulldown
				$stream_id =  request_var('hidden_stream_id',  request_var('stream_id', 0)); 
				$submit = (isset($_POST['add'])) ? true : false;
				$update = (isset($_POST['update'])) ? true : false;
				$delete = (isset($_POST['delete'])) ? true : false;
				if ($submit || $update || $delete)
				{
					if($delete)
					{
						// check if user can delete stream
						if(!$auth->acl_get('u_delete_stream') )
						{
							trigger_error($user->lang['NOUCPDELSTREAMS']);
						}
						$this->delete_stream($stream_id);
					}
					
					if($submit)
					{
						if (!check_form_key('streamadd'))
						{
							trigger_error('FORM_INVALID');
						}
						$this->add_stream();
					}
					
					if($update)
					{
						if (!check_form_key('streamadd'))
						{
							trigger_error('FORM_INVALID');
						}
						// check if user can update stream
						if(!$auth->acl_get('u_manage_streams') )
						{
							trigger_error($user->lang['NOUCPUPDSTREAMS']);
						}
						$this->update_stream($stream_id);
					}
				}
				$this->fill_addstream($stream_id);
				$this->page_title = 'UCP_STREAMS';
				$this->tpl_name = 'ucp_manage_streams';
				break;
		}
	}

	/**
	 * shows add/edit stream form
	 *
	 * @param unknown_type $stream_id
	 */
	private function fill_addstream($stream_id)
	{
		global $db, $auth, $user, $template, $config;
		$show = true;
		if($stream_id == 0)
		{
			// check if user can add stream
			if(!$auth->acl_get('u_add_stream') )
			{
				trigger_error($user->lang['NOUCPADDCHARS']);
			}
			// check if user exceeded allowed streams count
			$sql = 'SELECT count(*) as streams_count
					FROM ' . STREAMS_TABLE . '
					WHERE phpbb_user_id = ' . (int) $user->data['user_id'];
			$result = $db->sql_query($sql);
			$streams_count = $db->sql_fetchfield('streams_count');
			$db->sql_freeresult($result);
			if ($streams_count >= $config['streams_max_count'])
			{
				$show = false;
				$template->assign_vars(array(
				'MAX_CHARS_EXCEEDED' => sprintf($user->lang['MAX_CHARS_EXCEEDED'],$config['streams_max_count']),
				));
			}
			// set add mode
			$S_ADD = true;
		}
		else
		{
			//update mode
			$S_ADD = false;
			$sql = 'SELECT *
					FROM ' . STREAMS_TABLE . '	
					WHERE stream_id = ' . (int) $stream_id;
			$result = $db->sql_query($sql);
			$stream = array();
			$stream = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
		}

		// Stream platforms drop-down -> for initial load
		if ($stream_id > 0)
		{
			$sql = 'SELECT *
					FROM ' . STREAM_PLATFORMS_TABLE;
			$result = $db->sql_query($sql);
			
			while ($row = $db->sql_fetchrow($result) )
			{
				$template->assign_block_vars('stream_platform_row', array(
							'VALUE'	   => $row['stream_platform_id'],
							'SELECTED' => ( $stream['stream_platform_id'] == $row['stream_platform_id'] ) ? ' selected="selected"' : '',
							'OPTION'   => ( !empty($row['stream_platform_name']) ) ? $row['stream_platform_name'] : '(None)')
					);
			}
		}
		else 
		{
			$sql = 'SELECT *
					FROM ' . STREAM_PLATFORMS_TABLE;
			$result = $db->sql_query($sql);
			
			while ($row = $db->sql_fetchrow($result) )
			{
				$template->assign_block_vars('stream_platform_row', array(
							'VALUE'	   => $row['stream_platform_id'],
							'SELECTED' => '',
							'OPTION'   => ( !empty($row['stream_platform_name']) ) ? $row['stream_platform_name'] : '(None)')
					);
			}
		}
		
		$S_UPDATE = true;
		if(!$auth->acl_get('u_edit_stream') )
		{
			$S_UPDATE = false;
		}
		
		$S_DELETE = true;
		if(!$auth->acl_get('u_delete_stream') )
		{
			$S_DELETE = false;
		}

		$form_key = 'streamadd';
		add_form_key($form_key);

		$template->assign_vars(array(
			'S_SHOW'				=> $show,
			'S_ADD'					=> $S_ADD,
			'S_CANDELETE'			=> $S_DELETE,
			'S_CANUPDATE'			=> $S_UPDATE,
			'STREAM_DESCRIPTION'	=> $stream_id > 0 ? $stream['stream_description'] : '',
			'STREAM_CHANNEL_NAME'	=> $stream_id > 0 ? $stream['stream_channel_name'] : '',
			'ASSOCIATED_THREAD'		=> $stream_id > 0 ? $stream['associated_thread'] : '',
		));
	}

	/**
	* gets stream input, adds stream
	*
	*/
	function add_stream()
	{
		global $db, $config, $user, $phpbb_root_path, $phpEx;
		
		// check again if user exceeded allowed character count
		$sql = 'SELECT count(*) as streams_count
				FROM ' . STREAMS_TABLE . '
				WHERE phpbb_user_id = ' . (int) $user->data['user_id'];
		$result = $db->sql_query($sql);
		$streams_count = $db->sql_fetchfield('streams_count');
		$db->sql_freeresult($result);
		if ($streams_count >= $config['streams_max_count'])
		{
			 trigger_error(sprintf($user->lang['MAX_CHARS_EXCEEDED'],$config['streams_max_count']) , E_USER_WARNING);
		}
		
		$stream_platform_id = request_var('stream_platform_id', '', true);
		$stream_channel_name = strtolower(request_var('stream_channel_name', ''));
		$associated_thread = request_var('associated_thread', '', true);
		$pattern = array(
			'#https:#i',
			'#http:#i',
			'#//#',
		);
		$associated_thread = preg_replace($pattern ,'' , strtolower($associated_thread));
		$associated_thread = '//' . $associated_thread;
		$stream_description = request_var('stream_description', '', true);
		$phpbb_user_id = $user->data['user_id']; 
		
		$query = $db->sql_build_array('INSERT', array(
			'phpbb_user_id'			=> (int) $phpbb_user_id ,
			'stream_platform_id'	=> (string) $stream_platform_id ,
			'stream_channel_name'	=> (string) $stream_channel_name ,
			'associated_thread'		=> (string) $associated_thread ,
			'stream_description'	=> (string) $stream_description));
		$db->sql_query('INSERT INTO ' . STREAMS_TABLE . $query);
		$stream_id = $db->sql_nextid();
		
		if ($stream_id > 0) 
		{
			// record added. 
			meta_refresh(3, $this->u_action . '&amp;stream_id=' . $stream_id . '&amp;stream_id=' . $stream_id);
			$success_message = sprintf($user->lang['ADMIN_ADD_STREAM_SUCCESS'], $stream_channel_name) . '<br /><br />' . sprintf($user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '&amp;stream_id=' . $stream_id . '">', '</a>');
			trigger_error($success_message, E_USER_NOTICE);
		}
		else 
		{
			meta_refresh(3, $this->u_action . '&amp;stream_id=' . $stream_id . '&amp;stream_id=' . $stream_id);
			$failure_message = sprintf($user->lang['ADMIN_ADD_STREAM_FAIL'], $stream_channel_name, $stream_id) . '<br /><br />' . sprintf($user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '&amp;stream_id=' . $stream_id . '">', '</a>');
			 trigger_error($failure_message, E_USER_WARNING);
		}
	}
	
	/**
	 * deletes stream
	 *
	 */
	private function delete_stream($stream_id)
	{
		global $user, $db, $config, $phpbb_root_path;
		
		if (confirm_box(true))
		{
			$stream_channel_link = utf8_normalize_nfc(request_var('stream_channel_link','',true));
			$sql = 'DELETE FROM ' . STREAMS_TABLE . ' where stream_id= ' . (int) $stream_id;
			$db->sql_query($sql);
		
			meta_refresh(3, $this->u_action);
			$success_message = sprintf($user->lang['ADMIN_DELETE_STREAM_SUCCESS'], $stream_channel_link) . '<br /><br />' . sprintf($user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '">', '</a>');
			trigger_error($success_message);
		}
		else
		{
			$stream_platform_id = 0;
			$stream_channel_name = '';
			$phpbb_user_id = 0;
			$sql = "SELECT stream_platform_id, stream_channel_name, phpbb_user_id FROM " . STREAMS_TABLE . ' where stream_id = ' . $stream_id ;
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				$stream_platform_id		= $row['stream_platform_id'];
				$stream_channel_name	= $row['stream_channel_name'];
				$phpbb_user_id			= $row['phpbb_user_id'];
			}
			$db->sql_freeresult($result);
			//check if stream exists 
			if($stream_channel_name == '')
			{
				meta_refresh(3, $this->u_action);
				$error_message = sprintf($user->lang['ADMIN_DELETE_STREAM_FAIL'], $stream_id) . '<br /><br />' . sprintf($user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '">', '</a>');
				trigger_error($error_message);
			}
			//check if user is owner of this stream
			elseif ($phpbb_user_id != $user->data['user_id'])
			{
				meta_refresh(3, $this->u_action);
				$error_message = sprintf($user->lang['DELETE_STREAM_FAIL_NO_OWNWER'], $stream_id) . '<br /><br />' . sprintf($user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '">', '</a>');
				trigger_error($error_message);
			}
			else
			{
				$s_hidden_fields = build_hidden_fields(array(
					'delete'				=> true,
					'stream_channel_link'	=> $stream_channel_name,
					)
				);
				confirm_box(false, sprintf($user->lang['CONFIRM_DELETE_STREAM'], $stream_channel_name) , $s_hidden_fields);
			}
		}
	}
	
	/**
	 * gets user input, updates member
	 *
	 */
	private function update_stream($stream_id)
	{
		global $db, $user, $phpbb_root_path, $phpEx;
		
		// check if stream exists
		//$stream_channel_name = '';
		$sql = "SELECT stream_platform_id, phpbb_user_id FROM " . STREAMS_TABLE . ' where stream_id = ' . $stream_id ;
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
			{
				$phpbb_user_id			= $row['phpbb_user_id'];
			}
		$db->sql_freeresult($result);
		
		$stream_platform_id = request_var('stream_platform_id', '', true);
		$stream_channel_name = strtolower(request_var('stream_channel_name', '', true));
		$associated_thread = request_var('associated_thread', '', true);
		$pattern = array(
			'#https:#i',
			'#http:#i',
			'#//#',
		);
		$associated_thread = preg_replace($pattern ,'' , strtolower($associated_thread));
		$associated_thread = '//' . $associated_thread;
		$stream_description = request_var('stream_description', '', true);

		if ($stream_id == 0)
		{
			$stream_update_status = false;
		}
		//check if user is owner of this stream
		elseif ($phpbb_user_id != $user->data['user_id'])
		{
			meta_refresh(3, $this->u_action . '&amp;stream_id=' . $stream_id);
			$error_message = sprintf($user->lang['UPDATE_STREAM_FAIL_NO_OWNWER'], $stream_id) . '<br /><br />' . sprintf($user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '&amp;stream_id=' . $stream_id . '">', '</a>');
			trigger_error($error_message);
		}
		else
		{
			// get existing data
			$sql = 'SELECT * FROM ' . STREAMS_TABLE . ' WHERE stream_id = ' . (int) $stream_id;
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				$this->old_stream = array(
					'stream_id' => $row['stream_id'],
					'stream_platform_id' => (int) $row['stream_platform_id'],
					'stream_channel_name' => (string) $row['stream_channel_name'],
					'associated_thread' => $row['associated_thread'],
					'stream_description' => (string) $row['stream_description']);
			}
			$db->sql_freeresult($result);

			$this->new_stream = array(
					'stream_id' => $stream_id,
					'stream_platform_id' => (int) $stream_platform_id,
					'stream_channel_name' => (string) $stream_channel_name,
					'associated_thread' => trim($associated_thread),
					'stream_description' => (string) $stream_description);

			if ($this->new_stream != $this->old_stream)
			{
				// we have changes, so update 
				$sql = 'UPDATE ' . STREAMS_TABLE . '
				SET ' . $db->sql_build_array('UPDATE', $this->new_stream) . '
				WHERE stream_id = ' . (int) $stream_id;
				$db->sql_query($sql);

				$stream_update_status = true;
			}
			else
			{
				// no change
				$stream_update_status = false;
			}
		}
	
		if ($stream_update_status)
		{
			// record updated. 
			meta_refresh(3, $this->u_action . '&amp;stream_id=' . $stream_id . '&amp;stream_id=' . $stream_id);
			$success_message = sprintf($user->lang['ADMIN_UPDATE_STREAM_SUCCESS'], ucwords($stream_id)) . '<br /><br />' . sprintf($user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '&amp;stream_id=' . $stream_id . '&amp;stream_id=' . $stream_id . '">', '</a>');
			trigger_error($success_message, E_USER_NOTICE);
		}
		else 
		{
			// update fail.
			meta_refresh(3, $this->u_action . '&amp;stream_id=' . $stream_id . '&amp;stream_id=' . $stream_id);
			$failure_message = sprintf($user->lang['ADMIN_UPDATE_STREAM_FAIL'], ucwords($stream_id)) . '<br /><br />' . sprintf($user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '&amp;stream_id=' . $stream_id . '&amp;stream_id=' . $stream_id . '">', '</a>');
			trigger_error($failure_message, E_USER_WARNING);
		}
		
	}
	
	/**
	 * lists all my streams
	 *
	 */
	private function list_my_streams()
	{
		
		global $db, $user, $auth, $template, $config, $phpbb_root_path, $phpEx;
		
		// make a listing of my own streams with dkp for each pool
		$sql_array = array(
			'SELECT'    => 's.*, p.stream_platform as stream_platform, p.stream_platform_name as stream_platform_name',
			'FROM'      => array(
				STREAMS_TABLE			=> 's',
				STREAM_PLATFORMS_TABLE	=> 'p',
			),
			'WHERE'     =>  "p.stream_platform_id = s.stream_platform_id AND s.phpbb_user_id = " . $user->data['user_id'],
			'ORDER_BY'	=> "s.stream_channel_name",
		);
		
		$sql = $db->sql_build_query('SELECT', $sql_array);
		if (!($streams_result = $db->sql_query($sql)) )
		{
			trigger_error($user->lang['ERROR_STREAMNOTFOUND'], E_USER_WARNING);
		}
		
		while ( $row = $db->sql_fetchrow($streams_result) )
		{
			$template->assign_block_vars('streams_row', array(
				'LINK'			=> $row['stream_platform'] . $row['stream_channel_name'],
				'NAME'			=> $row['stream_platform_name'] . '(' . $row['stream_channel_name'] . ')',
				'U_EDIT'		=> append_sid("{$phpbb_root_path}ucp.$phpEx", "i=streams&amp;mode=manage_streams&amp;stream_id=" . $row['stream_id']),
				)
			);
		}
		$db->sql_freeresult ($streams_result);
	}
}
?>