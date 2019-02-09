<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

# функция автоподключения плагина
function linkexchange_autoload()
{
	mso_hook_add( 'content_content', 'linkexchange_custom');
}

# функции плагина
function linkexchange_custom($content = '')
{
	if (!isset($all_links)) static $all_links = array();
	if (!isset($st)) static $st = false;

	if ($st and !$all_links) return $content;
	if ( strpos($content, '[linkexchange=' !== true) ) return $content;

	if (!$st)
	{
		$st = true;
		$links = array();

		$CI = & get_instance();
		$CI->load->helper('file');
		$path = getinfo('uploads_dir') . '/linkexchange/';

		if ( ! is_dir($path) ) // нет каталога
		{
			$path = getinfo('uploads_dir');
		}

		$dirs = directory_map($path, true);
		if (!$dirs) $dirs = array();
		sort($dirs);

		$files = array();
		foreach ($dirs as $file)
		{
			if (@is_dir($path . $file)) continue;
			if ( strrpos($file, '.txt') !== (strlen($file) - 4) ) continue;
			$files[] = $file;
		}
		if (empty($files)) return $content;

		$links = array();
		foreach ($files as $file)
		{
			$data = file($path . $file);
			if (!$data) continue;
			$links = array_merge($links, $data);
		}
		if (!$links) return $content;
		$links = array_unique($links);
		foreach ($links as $link)
		{
			$link = trim($link);
			if (!$link) continue;
			$link = explode('|', $link);
			if ( !isset($link[1]) or !isset($link[2]) ) continue;
			$all_links[ '|(\[linkexchange=' . preg_quote(trim($link[1])) .'\])|siu' ] = trim($link[2]);
		}
	}
	if (!$all_links) return $content;

	$content = preg_replace(array_keys($all_links), array_values($all_links), $content);

	$r = '|(\[linkexchange=.*?\])|siu';
	$content = preg_replace($r, '', $content);

	return $content;
}

