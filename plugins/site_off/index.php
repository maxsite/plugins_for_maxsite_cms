<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция выполняется при деинстяляции плагина
function site_off_uninstall($args = array())
{
	return $args;
}

# функция автоподключения плагина
function site_off_autoload($args = array())
{
	mso_hook_add('init', 'site_off_init'); # хук на init
}

# функции плагина
function site_off_init($text = '')
{
  if (mso_segment(1) == 'login') 
  {
    return $text;
  }
	if (getinfo('users_id') == 0) // если гость ( users_id = 0 ), то показываем надпись и завершаемся
	{
	  $reason = '<html><head><title>Сайт на ремонте</title></head><body bgcolor="#ffffff"><table height="100%" width="100%"><tr><td align="center"><font face="monospace"><b>Сайт на ремонте. Зайдите попозже или <a href="'.getinfo('siteurl').'login'.'">войдите</a></b></font></td></tr></table></body></html>';
		die($reason);
	}
	return $text;
}

?>