<?php

if (!defined('IN_PHPBB'))
{
	exit;
}
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

$lang = array_merge($lang, array(
	'ANNOUNCEMENT_TOPIC'	=> 'Release Announcement',
	'CURRENT_VERSION'		=> 'Current version',
	'DOWNLOAD_LATEST'		=> 'Download Latest Version',
	'LATEST_VERSION'		=> 'Latest version',
	'NO_INFO'				=> 'Version server could not be contacted',
	'NOT_UP_TO_DATE'		=> '%s is not up to date',
	'RELEASE_ANNOUNCEMENT'	=> 'Annoucement Topic',
	'UP_TO_DATE'			=> '%s is up to date',
	'VERSION_CHECK'			=> 'MOD Version Check',

	'ACP_PBWOW_INDEX_TITLE'				=> 'PBWoW Module Index',
	'ACP_PBWOW_INDEX_TITLE_EXPLAIN'		=> 'Thank you for choosing PBWoW, hope you like it.',
	

	// OVERVIEW //
	'ACP_PBWOW2_OVERVIEW_TITLE'			=> 'PBWoW 2 Module Overview',
	'ACP_PBWOW2_OVERVIEW_TITLE_EXPLAIN'	=> 'Thank you for choosing PBWoW, hope you like it.',
	'ACP_PBWOW_INDEX_SETTINGS'			=> 'General information',

	'ACP_PBWOW_DB_CHECK'				=> 'PBWoW Database Check',
	'PBWOW_DB_GOOD'						=> 'PBWoW configuration table found (%s)',
	'PBWOW_DB_BAD'						=> 'No PBWoW configuration table found. Make sure that the table (%s) exists in your phpBB database.',
	'PBWOW_DB_BAD_EXPLAIN'				=> 'Run the PBWoW 2 installation script included in the MOD package. This will create and populate the appropriate database table.',
	'PBWOW_DB_CREATE'					=> 'Create PBWoW configuration table',
	'PBWOW_DB_CREATE_EXPLAIN'			=> 'You haven&acute;t created a configuration table yet for your PBWoW installation. Either do so manually, or hit the &quot;Install&quot; button.',
	'PBWOW_CONSTANTS_BAD'				=> 'Constants not set! This means that the PBWoW 2 MOD was not applied correctly!',
	'PBWOW_CONSTANTS_BAD_EXPLAIN'		=> 'Try installing the MOD again, or manually add the following line to your includes/constants.php file:<br /><br />define(\'PBWOW2_CONFIG_TABLE\', $table_prefix . \'pbwow2_config\');',

	'ACP_PBWOW_VERSION_CHECK'			=> 'PBWoW Version Check',
	'PBWOW_DATABASE_VERSION'			=> 'Database version',
	'PBWOW_ACP_MODULE_VERSION'			=> 'ACP module version',
	'PBWOW_ACP_TEMPLATE_VERSION'		=> 'ACP template version',
	'PBWOW_STYLE_VERSION'				=> 'Style version',
	'PBWOW_IMAGESET_VERSION'			=> 'Imageset version',
	'PBWOW_TEMPLATE_VERSION'			=> 'Template version',
	'PBWOW_THEME_VERSION'				=> 'Theme version',
	'PBWOW_VERSION_ERROR'				=> 'Unable to determine version!',
	'PBWOW_CHECK_UPDATE'				=> 'Check <a href="http://pbwow.com/forum/index.php">PBWoW.com</a> to see if there are updates available.',

	'ACP_PBWOW_CPF_CHECK'				=> 'Custom Profile Fields Check',
	'PBWOW_ACTIVE'						=> 'Active',
	'PBWOW_INACTIVE'					=> 'Inactive',
	'PBWOW_DETECTED'					=> 'Detected',
	'PBWOW_NOT_DETECTED'				=> 'Not detected',
	'PBWOW_OBSOLETE'					=> 'no longer used',
	'PBWOW_CPF_MEMBERLIST'				=> 'Allow styles to display custom profile fields in memberlist',
	'PBWOW_CPF_VIEWPROFILE'				=> 'Display custom profile fields in user profiles',
	'PBWOW_CPF_VIEWTOPIC'				=> 'Display custom profile fields on topic pages',
	'PBWOW_CPF_CREATE_LOCATION'			=> 'Create or enable this field via ACP > Users and Groups > Custom profile fields',
	'PBWOW_CPF_LOAD_LOCATION'			=> 'Enable this via ACP > General > Board Configuration > Board Features',

	'ACP_PBWOW_DONATE'					=> 'Donate to PBWoW',
	'PBWOW_DONATE'						=> 'Make a donation to PBWoW',
	'PBWOW_DONATE_EXPLAIN'				=> 'PBWoW is 100% free. It is a hobby project that I am spending my time and money on, just for the fun of it. If you enjoy using PBWoW, please consider making a donation. I would really appreciate it. No strings attached.',

	// legacy functions
	'ACP_PBWOW_RANKS'					=> 'Special Rank Images on Viewforum Pages',
	'PBWOW_RANKS_REFRESH'				=> 'Refresh special rank images',
	'PBWOW_RANKS_REFRESH_EXPLAIN'		=> 'By clicking this button, special rank images of the topic starter will be refreshed for all topics in the database. These are used to display the rank image behind the user&acute;s name on viewforum pages.',
	'PBWOW_RANKS_RESET'					=> 'Reset special rank images mod',
	'PBWOW_RANKS_RESET_EXPLAIN'			=> 'By clicking this button, the columns in the topics table of your database that store the rank title and image will be dropped. This is advisable if you have experienced &quot;no default value set&quot; SQL errors in the past (when creating new topics). After resetting, you will be prompted to recreate the columns using an improved script.',
	'PBWOW_RANKS_CREATE'				=> 'Modify the database',
	'PBWOW_RANKS_CREATE_EXPLAIN'		=> 'You haven&acute;t modified your forum viewtopics table (%s) yet to support this feature. Luckily, this is automated (but only tested on MySQL). This button automatically ads the 2 needed columns (fields) to %s.',
	'PBWOW_NOTE_ONLYONCE'				=> 'Only press this button once!',
	'PBWOW_NOTE_LONGTIME'				=> 'This process might take a while on big forums, so be patient.',
	'PBWOW_NOTE_DBCHANGE'				=> 'Warning! This will modify your database!',

	'ACP_PBWOW_REFRESH_THEMES'			=> 'Refresh All Themes',
	'PBWOW_REFRESH_THEMES'				=> 'Refresh all style themes in database',
	'PBWOW_REFRESH_THEMES_EXPLAIN'		=> 'This button refreshes all the theme data for every style in the database with a single click. This is particularly handy when editing CSS of styles that use template inheritance and shared stylesheets (like PBWoW).',
	'PBWOW_REFRESH_NOTE'				=> 'This isn\'t the same as &quot;purging the board cache&quot;!',
	'PBWOW_PURGE_CACHE_INFO'			=> 'Please note that many changes will not take effect untill you purge your forum cache.',
	'PBWOW_PURGE_CACHE_AUTO'			=> 'After submitting, your forum cache will automatically be purged.',

	// legacy checks
	'ACP_PBWOW_LEGACY_CHECK'			=> 'PBWoW Legacy Check',
	'PBWOW_LEGACY_CONSTANTS'			=> 'PBWoW v1 Constants',
	'PBWOW_LEGACY_CONSTANTS_EXPLAIN'	=> 'If detected, this means that there are still MODs of PBWoW v1 active! This could lead to PHP errors since PBWoW 1 and PBWoW 2 share some functions, but they are declared differently. So we <u>strongly</u> advise you to uninstall all PBWoW v1 MODs.',
	'PBWOW_LEGACY_DB_ACTIVE'			=> 'PBWoW v1 Config Database',
	'PBWOW_LEGACY_DB_ACTIVE_EXPLAIN'	=> 'The config table of PBWoW v1 is still active. This is no problem, since PBWoW 2 does not interact with it. But you can drop/delete the table if you want (and are no longer using it).',
	'PBWOW_LEGACY_TOPICS_MOD'			=> 'PBWoW v1 Topic Table MOD',
	'PBWOW_LEGACY_TOPICS_MOD_EXPLAIN'	=> 'If you use this old PBWoW v1 MOD in combination with PBWoW 2, you might get SQL errors when trying to post new topics (and vice versa). We advise you to remove this MOD <u>if you are no longer using PBWoW v1</u>.',
	'PBWOW_LEGACY_NONE'					=> 'No obvious traces of the old PBWoW v1 were found',


	// CONFIG //
	'ACP_PBWOW_CONFIG_TITLE'			=> 'PBWoW Configuration',
	'ACP_PBWOW_CONFIG_TITLE_EXPLAIN'	=> 'Here you can choose some options for your PBWoW installation.',
	'ACP_PBWOW_CONFIG_SETTINGS'			=> 'Configuration Options',

	'ACP_PBWOW_LOGO'					=> 'Custom Logo',
	'PBWOW_LOGO_ENABLE'					=> 'Enable your own custom logo image',
	'PBWOW_LOGO_ENABLE_EXPLAIN'			=> 'Using this will enable your own custom logo for all installed PBWoW 2 styles, except the PBWoW 2 master style (Pandaria).',
	'PBWOW_LOGO_SRC'					=> 'Image source path',
	'PBWOW_LOGO_SRC_EXPLAIN'			=> 'Image path under your phpBB root directory, e.g. <samp>images/logo.png</samp>.<br />I strongly advise you to use a PNG image with a transparent background.',
	'PBWOW_LOGO_SIZE'					=> 'Logo dimensions',
	'PBWOW_LOGO_SIZE_EXPLAIN'			=> 'Exact dimensions of your logo image (Width x Height in pixels)<br />Images of more than 350 x 200 are not advised.',
	'PBWOW_LOGO_MARGINS'				=> 'Logo margins',
	'PBWOW_LOGO_MARGINS_EXPLAIN'		=> 'Set the CSS margins of your logo. This will give more control over the positioning of your image. Use valid CSS markup, e.g. <samp>10px 5px 25px 0</samp>.',

	'ACP_PBWOW_TOPBAR'					=> 'Top Header-Bar',
	'PBWOW_TOPBAR_ENABLE'				=> 'Enable the top header-bar',
	'PBWOW_TOPBAR_ENABLE_EXPLAIN'		=> 'By enabling this option, a 40px high bar will be displayed at the top of each page.',
	'PBWOW_TOPBAR_CODE'					=> 'Top header-bar code',
	'PBWOW_TOPBAR_CODE_EXPLAIN'			=> 'Enter your code here, use &lt;span&gt; or &lt;a class="cell"&gt; elements to seperate blocks with borders. To use icons, either use &lt;img&gt; blocks or define special CSS classes inside your custom.css stylesheet (better).',
	'PBWOW_TOPBAR_FIXED'				=> 'Fixed to top',
	'PBWOW_TOPBAR_FIXED_EXPLAIN'		=> 'Fixing the top header-bar to the top of the screen will keep it visible and locked in place, even when scrolling. This only applies to devices with a minimum width of 670px (smaller devices give bad results).',

	'ACP_PBWOW_HEADERLINKS'				=> 'Header Box Custom Links',
	'PBWOW_HEADERLINKS_ENABLE'			=> 'Enable custom links in the header box',
	'PBWOW_HEADERLINKS_ENABLE_EXPLAIN'	=> 'By enabling this option, the HTML code entered below will be displayed inside the box at the top-right of the screen (in-line before the FAQ link). This is useful for portal and DKP links (some of which will be detected automatically).',
	'PBWOW_HEADERLINKS_CODE'			=> 'Custom header links code',
	'PBWOW_HEADERLINKS_CODE_EXPLAIN'	=> 'Enter your custom links here. These should be wrapped in &lt;li&gt; elements. To use icons, please define CSS classes inside your custom.css stylesheet.',

	'ACP_PBWOW_NAVMENU'					=> 'Breadcrumb Navigation Menus (experimental)',
	'PBWOW_NAVMENU_ENABLE'				=> 'Enable the drop-down navigation menus for breadcrumbs',
	'PBWOW_NAVMENU_ENABLE_EXPLAIN'		=> 'This feature will generate drop-down navigation menus for each breadcrumb (nav-links) item in the header (currently disabled for mobile devices due lack of touch-support).',

	'ACP_PBWOW_IE6MESSAGE'				=> 'Unsupported Browser (IE6 & IE7) Warning Message',
	'PBWOW_IE6MESSAGE_ENABLE'			=> 'Enable warning message for old browsers (IE6 & IE7)',
	'PBWOW_IE6MESSAGE_ENABLE_EXPLAIN'	=> 'By enabling this option, a banner will be shown to visitors of your forum that are still using Internet Explorer 7 or older, advising them to upgrade.',
	'PBWOW_IE6MESSAGE_CODE'				=> 'Unsupported browser warning message code',
	'PBWOW_IE6MESSAGE_CODE_EXPLAIN'		=> 'Customize your warning message code. Visit this website for <a href="http://www.ie6nomore.com/code-samples.html" target="_blank">CODE SAMPLES</a>.',
	
	'ACP_PBWOW_VIDEOBG'					=> '(Video) Background Settings',
	'PBWOW_VIDEOBG_ENABLE'				=> 'Enable animated video backgrounds',
	'PBWOW_VIDEOBG_ENABLE_EXPLAIN'		=> 'Some PBWoW 2 styles support special animated video backgrounds (not all). You can enable these for cool effect, or disable them to save bandwidth (or if you are having problems).',
	'PBWOW_VIDEOBG_ALLPAGES'			=> 'Display video backgrounds on all pages?',
	'PBWOW_VIDEOBG_ALLPAGES_EXPLAIN'	=> 'By default, PBWoW 2 only loads the video backgrounds (if available) on <u>index.php</u> pages. You can enable them for all pages, but this may affect the browsing speed of your visitors (but in general not your server bandwith, because they are cached locally). [only applies if video is enabled]',
	'PBWOW_BG_FIXED'					=> 'Fixed background position',
	'PBWOW_BG_FIXED_EXPLAIN'			=> 'Fixing the background position will prevent it from scrolling along with the rest of the content. Keep in mind that some lower resolution devices will have no option to see the entire background image.',

	'ACP_PBWOW_TOOLTIPS'				=> 'Tooltips',
	'PBWOW_WOWTIPS_SCRIPT'				=> 'World of Warcraft Tooltips script',
	'PBWOW_WOWTIPS_SCRIPT_EXPLAIN'		=> 'Choose the tooltips script you wish to use. If enabled, all links found on your site will feature a tooltip. For more information, visit <a href="http://www.wowhead.com/tooltips" target="_blank">Wowhead</a>, <a href="http://www.openwow.com/misc=integrate#phpbb" target="_blank">OpenWoW</a>, <a href="http://db.hellground.net/" target="_blank">DB.HellGround.net</a> or <a href="http://db.vanillagaming.org/?tooltips" target="_blank">VanillaGaming</a>.',

	'PBWOW_D3TIPS_SCRIPT'				=> 'Diablo 3 Tooltips script',
	'PBWOW_D3TIPS_SCRIPT_EXPLAIN'		=> 'Choose the tooltips script you wish to use. If enabled, all links found on your site will feature a tooltip. For more information, visit <a href="http://us.battle.net/d3/en/tooltip/" target="_blank">Battle.net</a> or <a href="http://d3db.com/tooltip" target="_blank">D3DB.com</a>.',

	'PBWOW_ZAMTIPS_ENABLE'				=> 'Enable ZAM Tooltips script',
	'PBWOW_ZAMTIPS_ENABLE_EXPLAIN'		=> 'If enabled, ZAM links found on your site will feature a tooltip and an icon. This supports tooltips for: Everquest, Everquest II, Final Fantasy XI, Final Fantasy XIV, Lord of the Rings Online and Warhammer Online. For more information, visit <a href="http://www.zam.com/wiki/Tooltips" target="_blank">ZAM Tooltips Wiki</a>.',

	'PBWOW_TOOLTIPS_REGION'				=> 'Region Settings',
	'PBWOW_TOOLTIPS_REGION_EXPLAIN'		=> 'Some (not all) tooltip scripts have regional distribution. Depending on your user\'s demographics, it might be advisable to choose the one with the lowest latency.',
	
	'PBWOW_TOOLTIPS_FOOTER'				=> 'Load scripts in the <u>footer</u> instead of header?',
	'PBWOW_TOOLTIPS_FOOTER_EXPLAIN'		=> 'Some users experience time-out issues when tooltip scripts are loaded in the header. Enabling this option will load all tooltip scripts in the footer instead. This is not recommended unless you are experiencing problems.',
	
	'PBWOW_TOOLTIPS_LOCAL'				=> 'Load scripts from local webserver?',
	'PBWOW_TOOLTIPS_LOCAL_EXPLAIN'		=> 'Loads a local copy (11-11-2013) of the tooltip scripts instead of the live DB websites. Do not enable unless you know what you are doing.',
	'PBWOW_MOD_COLORS'					=> 'Allow users to post with mod colors?',
	'ACP_PBWOW_MOD_COLORS'				=> 'Moderator colors censoring',
	'PBWOW_RANGE_RED'					=> 'Range of red color component (max 255)',
	'PBWOW_RANGE_RED_EXPLAIN'			=> 'Example: rgb( <b>r</b>, g, b )',
	'PBWOW_RANGE_GREEN'					=> 'Range of green color component (max 255)',
	'PBWOW_RANGE_GREEN_EXPLAIN'			=> 'Example: rgb( r, <b>g</b>, b )',
	'PBWOW_RANGE_BLUE'					=> 'Range of blue color component (max 255)',
	'PBWOW_RANGE_BLUE_EXPLAIN'			=> 'Example: rgb( r, g, <b>b</b> )',


	// POSTSTYLING //
	'ACP_PBWOW_POSTSTYLING_TITLE'		=> 'PBWoW Post Styling Settings',
	'ACP_PBWOW_POSTSTYLING_TITLE_EXPLAIN'	=> 'This page controls the PBWoW features relating to special post styling. You can enable these features for specific user groups.',

	'ACP_PBWOW_BLIZZ'					=> 'Blizzard Post Styling',
	'PBWOW_BLIZZ_ENABLE'				=> 'Enable Blizzard post styling',
	'PBWOW_BLIZZ_ENABLE_EXPLAIN'		=> 'Enable this feature to let the rank(s) selected below display as "Blizzard" posters, usually reserved for admins and moderators.',
	'PBWOW_BLIZZ_RANKS'					=> 'Blizzard post styling ranks',
	'PBWOW_BLIZZ_RANKS_EXPLAIN'			=> 'Choose the user rank(s) that you want to display as "Blizzard" posters (hold down the CTRL key to select multiple).',
	'PBWOW_BLIZZ_COLOR'					=> 'Blizzard post color',
	'PBWOW_BLIZZ_COLOR_EXPLAIN'			=> 'This will affect the text color of the posts made by the users with the ranks selected above. Leave empty to disable. Default = #00C0FF',

	'ACP_PBWOW_PROPASS'					=> 'Propass Post Styling',
	'PBWOW_PROPASS_ENABLE'				=> 'Enable Propass post styling',
	'PBWOW_PROPASS_ENABLE_EXPLAIN'		=> 'Enable this feature to let the rank(s) selected below display as "Propass" or "Dragon" posters, usually reserved for special users.',
	'PBWOW_PROPASS_RANKS'				=> 'Propass post styling ranks',
	'PBWOW_PROPASS_RANKS_EXPLAIN'		=> 'Choose the user rank(s) that you want to display as "Propass" posters (hold down the CTRL key to select multiple).',
	'PBWOW_PROPASS_COLOR'				=> 'Propass post color',
	'PBWOW_PROPASS_COLOR_EXPLAIN'		=> 'This will affect the text color of the posts made by the users with the ranks selected above. Leave empty to disable. Default = #9988FF',
	
	'ACP_PBWOW_RED'						=> 'Red Post Styling',
	'PBWOW_RED_ENABLE'					=> 'Enable Red post styling',
	'PBWOW_RED_ENABLE_EXPLAIN'			=> 'Enable this feature to let the rank(s) selected below display as "Red" posters. I don&rsquo;t really know what it&rsquo;s for.',
	'PBWOW_RED_RANKS'					=> 'Red post styling ranks',
	'PBWOW_RED_RANKS_EXPLAIN'			=> 'Choose the user rank(s) that you want to display as "Red" posters (hold down the CTRL key to select multiple).',	
	'PBWOW_RED_COLOR'					=> 'Red post color',
	'PBWOW_RED_COLOR_EXPLAIN'			=> 'This will affect the text color of the posts made by the users with the ranks selected above. Leave empty to disable.',

	'ACP_PBWOW_GREEN'					=> 'Green Post Styling',
	'PBWOW_GREEN_ENABLE'				=> 'Enable Green post styling',
	'PBWOW_GREEN_ENABLE_EXPLAIN'		=> 'Enable this feature to let the rank(s) selected below display as "Green" or "MVP" posters, usually reserved for community leaders.',
	'PBWOW_GREEN_RANKS'					=> 'Green post styling ranks',
	'PBWOW_GREEN_RANKS_EXPLAIN'			=> 'Choose the user rank(s) that you want to display as "Green" posters (hold down the CTRL key to select multiple).',	
	'PBWOW_GREEN_COLOR'					=> 'Green post color',
	'PBWOW_GREEN_COLOR_EXPLAIN'			=> 'This will affect the text color of the posts made by the users with the ranks selected above. Leave empty to disable. Default = #5DF644',


	// ADVERTISEMENTS //
	'ACP_PBWOW_ADS_TITLE'				=> 'PBWoW Advertisement Settings',
	'ACP_PBWOW_ADS_TITLE_EXPLAIN'		=> 'This page controls the way PBWoW displays advertisements. These blocks can of course also be used to put your own content, images, banners or whatever. Just keep in mind the dimension limitations.',

	'ACP_PBWOW_ADS_INDEX'				=> 'Index Advertisement Block',
	'PBWOW_ADS_INDEX_ENABLE'			=> 'Enable index advertisements',
	'PBWOW_ADS_INDEX_ENABLE_EXPLAIN'	=> 'Enabling this ad will generate a square ads block on the forum index page (requires NV Recent Topics MOD).',
	'PBWOW_ADS_INDEX_CODE'				=> 'Index advertisment code',
	'PBWOW_ADS_INDEX_CODE_EXPLAIN'		=> 'This block is suitable for advertisements with a <u>width</u> of: <b>300px</b>.',

	'ACP_PBWOW_ADS_TOP'					=> 'Horizontal (Top) Advertisement Block',
	'PBWOW_ADS_TOP_ENABLE'				=> 'Enable horizontal (top) forum advertisements',
	'PBWOW_ADS_TOP_ENABLE_EXPLAIN'		=> 'Enabling this ad will generate a horizontal bar advertisment at the top of every page except the index page.',
	'PBWOW_ADS_TOP_CODE'				=> 'Horizontal (top) advertisment code',
	'PBWOW_ADS_TOP_CODE_EXPLAIN'		=> 'Technically, this block has a maximum width of 930px, but this is not advisable (due to mobile devices, etc.). This block is generally suitable for advertisements with dimensions around: <b>728 x 90</b>.',
	
	'ACP_PBWOW_ADS_BOTTOM'				=> 'Horizontal (Bottom) Advertisement Block',
	'PBWOW_ADS_BOTTOM_ENABLE'			=> 'Enable horizontal (bottom) forum advertisements',
	'PBWOW_ADS_BOTTOM_ENABLE_EXPLAIN'	=> 'Enabling this ad will generate a horizontal bar advertisment at the bottom of every page except the index page.',
	'PBWOW_ADS_BOTTOM_CODE'				=> 'Horizontal (bottom) advertisment code',
	'PBWOW_ADS_BOTTOM_CODE_EXPLAIN'		=> 'Technically, this block has a maximum width of 930px, but this is not advisable (due to mobile devices, etc.). This block is generally suitable for advertisements with dimensions around: <b>728 x 90</b>.',

	'ACP_PBWOW_ADS_SIDE'				=> '[UNAVAILABLE] Vertical (Side) Advertisement Block',
	'PBWOW_ADS_SIDE_ENABLE'				=> 'Enable vertical (side) forum advertisements',
	'PBWOW_ADS_SIDE_ENABLE_EXPLAIN'		=> 'Enabling this ad will generate a vertical bar advertisment along the right side of viewforum and viewtopic pages.',
	'PBWOW_ADS_SIDE_CODE'				=> 'Vertical (side) advertisment code',
	'PBWOW_ADS_SIDE_CODE_EXPLAIN'		=> 'This block is suitable for advertisements with dimensions around: <b>160 x 600</b>.',
	
	// tracking
	'ACP_PBWOW_TRACKING'				=> 'Tracking Script',
	'PBWOW_TRACKING_ENABLE'				=> 'Enable tracking script for visitors',
	'PBWOW_TRACKING_ENABLE_EXPLAIN'		=> 'Enabling this will insert the code you enter at the bottom of the footer. This can be Google analytics or whatever scripts you want.',
	'PBWOW_TRACKING_CODE'				=> 'Tracking script code',
	'PBWOW_TRACKING_CODE_EXPLAIN'		=> 'Insert your tracking script code here, or whatever other script you want to use.',
));
?>