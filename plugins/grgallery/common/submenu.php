<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_cur_dir_lang('admin');

?>
<div class="admin-h-menu">
<?php
	//-- формируем массив с данными подключаемых модулей--
	
	$plugins_dir = $MSO->config['plugins_dir'];
	$plugins_dir = $plugins_dir.'grgallery/plugin/';
	$dirs = directory_map($plugins_dir, true);
	foreach ($dirs as $dir)
	{
		$info_f = $plugins_dir . $dir . '/info.php';
		if (file_exists($info_f))
		{
		require($info_f);
			if (isset( $info )) 
			{
			$titles[$info['order']] = isset($info['title']) ? mso_strip($info['title']) : '';
			$links[$info['order']] = isset($info['link']) ? $info['link'] : $dir;
			}
		}
	};
	ksort($titles);
	ksort($links);
	//--- конец блока формирования массива модулей---------

	# сделаем меню горизонтальное в текущей закладке
	
	// основной url этого плагина - жестко задается
	$plugin_url = $MSO->config['site_admin_url'] . 'grgallery';
	
	// Определим текущую страницу (на основе сегмента url)
	$seg = mso_segment(3);
	if ($seg == '' || $seg == 'pages') $segord = 'pages'; // это страница выводимая по умолчанию
	
	// само меню
	// вкладка на первом месте
	//$a = mso_admin_link_segment_build($plugin_url, 'pages', t('Настройки', 'plugins/grgallery'), 'select'). ' | ';
	$a = '';
	
	// дальше из grgllпланигов
	// ---- динамическое добавление в меню пунктов дополн. блоков----
	if (isset( $titles )) 
		{
		foreach ($titles as $i=>$title)
			{
			$a .= mso_admin_link_segment_build($plugin_url, $links[$i], $title, 'select'). ' | ';
			}
		};
	// ---- конец блока добавления в меню пунктов дополн. блоков----	
	$a = mso_hook('plugin_admin_options_menu', $a);  // не понятно зачем эта строка
	echo $a;
?>
</div>

<?php
// Определим текущую страницу (на основе сегмента url)
$seg = mso_segment(3);

// подключаем соответственно нужный файл
if ($seg == '' || $seg == 'pages') require_once($MSO->config['plugins_dir'] . 'grgallery/plugin/pages/admin/index.php');
//	elseif ($seg == 'general') require($MSO->config['plugins_dir'] . 'grgallery/admin/admin_general.php');
//----- динамическое подключение дополнительных модулей -------------
	if (isset( $links )) 
		{
		$strcod = '';
		foreach ($links as $i=>$link)
			{
			$strcod .= "if (\$seg == '$link') require_once (\$MSO->config['plugins_dir'].'grgallery/plugin/'.'$link/admin/index.php');";	
			};
		eval ($strcod);
		}
//------ конец динамического подключение дополнительных модулей -----

?>