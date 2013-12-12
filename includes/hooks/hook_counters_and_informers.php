<?php
/**
*
* @package phpBB Antibot100500
* @version $Id$
* @copyright (c) 2013 c61 http://www.phpbbguru.net & http://c61.no-ip.org
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

function counters_and_informers()
{
	global $template, $user, $auth;
	
	if (!defined('ADMIN_START') && !defined('IN_INSTALL') && defined('HEADER_INC') && isset($template->_rootref['DEBUG_OUTPUT']))
	{
		if(!defined('MOBILE_STYLE')
		// Эту строку можно закомментировать для добавки информеров независимо от пользователя
		&& ($auth->acl_get('a_') && !empty($user->data['is_registered']))
		  )
		{
			// Добавляем информеры
			$template->_rootref['DEBUG_OUTPUT'] .= '
<!-- Информеры -->

<!-- /Информеры -->
			';
		}

		// Добавляем коды счетчиков
		$template->_rootref['DEBUG_OUTPUT'] .= "
<!-- Счетчики -->
<script type=\"text/javascript\">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-32002413-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<!-- /Счетчики -->
		";
	}
}

// Регистрируем хук
$phpbb_hook->register(array('template', 'display'), 'counters_and_informers');

?>