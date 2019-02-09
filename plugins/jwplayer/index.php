<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function jwplayer_autoload()
{
	mso_hook_add( 'head', 'jwplayer_head');
	mso_hook_add( 'content', 'jwplayer_content');
	mso_create_allow('jwplayer_edit', t('Админ-доступ к настройкам', 'plugins') . ' ' . t('jwplayer', __FILE__));
	mso_hook_add( 'admin_init', 'jwplayer_admin_init');
}

# функция выполняется при активации (вкл) плагина
function jwplayer_activate($args = array())
{	
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function jwplayer_deactivate($args = array())
{	
	mso_delete_option('plugin_jwplayer', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при деинстяляции плагина
function jwplayer_uninstall($args = array())
{	
	mso_delete_option('plugin_jwplayer', 'plugins'); // удалим созданные опции
	mso_remove_allow('jwplayer_edit'); // удалим созданные разрешения
	return $args;
}

# функции плагина
function jwplayer_head($arg = array())
{
	static $jwplayer_js = false;
	
	if (!$jwplayer_js)
		echo '	<script language="JavaScript" src="' . getinfo('plugins_url') . 'jwplayer/swfobject.js"></script>';
	
	$jwplayer_js = true;
	
	return $arg;
}

# функция выполняется при указаном хуке admin_init
function jwplayer_admin_init($args = array()) 
{
	if ( mso_check_allow('jwplayer_edit') ) 
	{
		$this_plugin_url = 'jwplayer';
		mso_admin_menu_add('plugins', $this_plugin_url, t('jwplayer', __FILE__));
		mso_admin_url_hook ($this_plugin_url, 'jwplayer_admin_page');
	}
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function jwplayer_admin_page($args = array()) 
{
	global $MSO;
	
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('jwplayer_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	# выносим админские функции отдельно в файл
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('jwplayer', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('jwplayer', __FILE__) . ' - " . $args; ' );
	require($MSO->config['plugins_dir'] . 'jwplayer/admin.php');
}

# callback функция 
function jwplayer_content_callback($matches)
{	
        $url = $matches[1];
	$id = md5($url);

	$options = mso_get_option('jwplayer', 'plugins', array());
	$options['width']         = isset($options['width'])         ?      $options['width']         : '400';
	$options['height'] = isset($options['height']) ? $options['height'] : '300';
	$options['plugins']    = isset($options['plugins'])    ? $options['plugins']    : 'viral-2';
	$options['scin']    = isset($options['scin'])    ? $options['scin']    : '' . getinfo('plugins_url') . 'jwplayer/jwplayer.swf';
	
	$code = '<object width="' . $options['width'] .
  '" height="' . $options['height'] .
  '"><param name="movie" value="' . $url . 
  '"></param><param name="allowFullScreen" value="true"></param><param name="allowScriptAccess" value="always"></param><embed src="' . $options['scin'] .
  '" type="application/x-shockwave-flash" width="' . $options['width'] .
  '" height="' . $options['height'] .
  '" bgcolor="#FFFFFF" allowscriptaccess="always" allowfullscreen="true" flashvars="file=' . $url . 
  '&plugins=' . $options['plugins'] .
  '" /></embed></object>';
	$out = $code;

return $out;
}

# функции плагина
function jwplayer_content($text = '')
{	
	$text = preg_replace_callback('~\[jwvideo=(.*?)\]~si', 'jwplayer_content_callback', $text);
	
	return $text;
}

?>