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


if (isset($_GET[URI_ITEM]) )
{
    $sort_order = array(
        0 => array('i.item_date desc', 	'i.item_date'),
        1 => array('m.item_buyer', 		'm.item_buyer desc'),
        2 => array('i.item_value desc', 'i.item_value')
    );

    $current_order = switch_order($sort_order);
    $item_id = request_var(URI_ITEM, 0);
    $sql = 'SELECT item_name, item_gameid FROM ' . RAID_ITEMS_TABLE . " WHERE item_id = " . $item_id ;
    $result = $db->sql_query($sql);
	if (! $result)
	{
		$user->add_lang ( array ('mods/dkp_admin' ) );
		trigger_error ( $user->lang ['ERROR_INVALID_ITEM_PROVIDED'], E_USER_WARNING );
	}
	
	$total_items = 0;
	while ( $row = $db->sql_fetchrow($result) )
    {
	    $item_name = $row['item_name'];
	    $item_gameid = (int) $row['item_gameid'];
	    $total_items++;
    } 
    $db->sql_freeresult ( $result );
    
    if ( empty($item_name) )
    {
		$user->add_lang ( array ('mods/dkp_admin' ) );
		trigger_error ( $user->lang ['ERROR_INVALID_ITEM_PROVIDED'], E_USER_WARNING );
    }
	 
	//get info on all buyers of this item
     $sql_array = array(
	    'SELECT'    => 	' e.event_name, e.event_dkpid, 
	      i.item_id, i.item_gameid, i.item_name, i.item_value, i.item_date, i.raid_id, i.member_id, 
	      c.colorcode, c.imagename, c.class_id, l.member_name', 
	    'FROM'      => array(
				EVENTS_TABLE 		=> 'e', 
		        RAIDS_TABLE 		=> 'r', 
		        CLASS_TABLE			=> 'c', 
		        MEMBER_LIST_TABLE 	=> 'l', 
     			RAID_ITEMS_TABLE 		=> 'i', 
	    	),
	 
	    'WHERE'     =>  " e.event_id = r.event_id
    					AND r.raid_id = i.raid_id 
	    				AND l.member_class_id = c.class_id 
	    				AND l.game_id = c.game_id 
	    				AND i.member_id = l.member_id
				        AND i.item_name='". $db->sql_escape($item_name) . "'",
	    'ORDER_BY'	=> $current_order['sql'], 
	    );

	$sql = $db->sql_build_query('SELECT', $sql_array);
	
    if ( !($result = $db->sql_query($sql)) )
    {
        trigger_error ( $user->lang ['ERROR_INVALID_ITEM_PROVIDED'], E_USER_WARNING );
    }
	$title = $user->lang['ITEM'] . ' : '. $item_name;
	
    if ($bbDKP_Admin->bbtips == true)
	{
		if ($item_gameid > 0 )
		{
			$item_name = '<strong>' . $bbtips->parse('[itemdkp]' . $item_gameid  . '[/itemdkp]') . '</strong>' ; 
		}
		else 
		{
			$item_name = '<strong>' . $bbtips->parse ( '[itemdkp]' . $item_name . '[/itemdkp]' . '</strong>'  );
		}
		
	}
	else
	{
		$item_name = '<strong>' . $item_name . '</strong>';
	}    
    
	while ( $item = $db->sql_fetchrow($result) )
    {
        $template->assign_block_vars('items_row', array(
        
            'DATE' => ( !empty($item['item_date']) ) ? date('d.m.y', $item['item_date']) : '&nbsp;',
            'CLASSCOLOR' => ( !empty($item['member_name']) ) ? $item['colorcode'] : '',
            'CLASSIMAGE' => ( !empty($item['member_name']) ) ? $item['imagename'] : '',            
            'BUYER' => ( !empty($item['member_name']) ) ? $item['member_name'] : '&nbsp;',
            'U_VIEW_BUYER' => append_sid("{$phpbb_root_path}dkp.$phpEx" , "page=viewmember&amp;" . URI_NAMEID . '='.$item['member_id']. '&amp;' . URI_DKPSYS . '=' . $item['event_dkpid']) ,
            'U_VIEW_RAID' => append_sid("{$phpbb_root_path}dkp.$phpEx", 'page=viewraid&amp;' . URI_RAID . '='.$item['raid_id']) ,
            'RAID' => ( !empty($item['event_name']) ) ? $item['event_name'] : '&lt;<i>Not Found</i>&gt;',
            'VALUE' => $item['item_value'])
        );
    }
    $db->sql_freeresult ( $result );
    
    // breadcrumbs ////
    $navlinks_array = array(
    array(
     'DKPPAGE' => $user->lang['MENU_ITEMVAL'],
     'U_DKPPAGE' => append_sid("{$phpbb_root_path}dkp.$phpEx", "page=listitems"),
    ),

    array(
     'DKPPAGE' => $user->lang['MENU_VIEWITEM'],
     'U_DKPPAGE' => append_sid("{$phpbb_root_path}dkp.$phpEx" , "page=viewitem&amp;" . URI_ITEM . '='. $item_id),
    ),
    );

    foreach( $navlinks_array as $name )
    {
	    $template->assign_block_vars('dkpnavlinks', array(
	    'DKPPAGE' => $name['DKPPAGE'],
	    'U_DKPPAGE' => $name['U_DKPPAGE'],
	    ));
    }
    
    $template->assign_vars(array(
		'L_PURCHASE_HISTORY_FOR' => sprintf($user->lang['PURCHASE_HISTORY_FOR'], '<strong>' . $item_name. '</strong>'),
        'O_DATE' 				 => $current_order['uri'][0],
        'O_BUYER'				 => $current_order['uri'][1],
        'O_VALUE'			 	 => $current_order['uri'][2],
        'U_VIEW_ITEM' 			 => append_sid("{$phpbb_root_path}dkp.$phpEx" , 'page=viewitem&amp;' . URI_ITEM . '='. $item_id) ,
        'VIEWITEM_FOOTCOUNT' 	 => sprintf($user->lang['VIEWITEM_FOOTCOUNT'], $total_items), 
		'S_DISPLAY_VIEWITEM' 	 => true,
        )
    );

	// Output page
	page_header($title);

}
else
{
	trigger_error ( $user->lang ['ERROR_INVALID_ITEM_PROVIDED'], E_USER_WARNING );
}
?>