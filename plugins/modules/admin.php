<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	global $MODULES;

	$CI = & get_instance();
	
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'fmoduless')) )
	{
		mso_checkreferer();
		
		$modules = array();
		foreach($MODULES as $mod)
		{
			$modules[$mod['id']]['file_name'] = $post['fmoduless'][$mod['id']]['file_name'];
			$modules[$mod['id']]['php_code'] = $post['fmoduless'][$mod['id']]['php_code'];
		}
		
		mso_add_float_option('modules', $modules, 'modules'); // и в опции
		
		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}
	
?>
<h1><?= t('Модули', __FILE__) ?></h1>
<p class="info"><?= t('Для настройки модуля, выберите файл или введите PHP-код.', __FILE__) ?></p>

<?php
	
	if (!$MODULES) 
	{
		echo 'Модули не определены.';
		return;
	}
	
	$modules = mso_get_float_option('modules', 'modules', array());
	
	# получим подключаемые файлы
	$CI->load->helper('directory');
	
	$files_template = directory_map(getinfo('template_dir') . 'modules/', true); // только в modules шаблона
	
	$list_files_template = array();
	if ($files_template) foreach($files_template as $val)
	{
		if (!is_dir(getinfo('plugins_dir') . 'modules/modules/' . $val)) $list_files_template['TEMPLATE:' . $val] = 'TEMPLATE:' . $val;
	}
	
	$files_plugin = directory_map(getinfo('plugins_dir') . 'modules/modules/', true); // только в modules плагина
	$list_files_plugin = array();
	if ($files_plugin) foreach($files_plugin as $val)
	{
		if (!is_dir(getinfo('plugins_dir') . 'modules/modules/' . $val)) $list_files_plugin['PLUGIN:' . $val] = 'PLUGIN:' . $val;
	}
	
	global $module_in_plugin;  // теперь в modules других плагинов
	$list_files_other = array();
	if ($module_in_plugin) 
	foreach ($module_in_plugin as $file_key => $module)
	{
	  $plugin_name = $module['plugin_name'];
	  $file_name = $module['file_name'];
	  $module_name = $module['module_name'];
	  if ( file_exists(getinfo('plugins_dir') . $plugin_name . '/modules/' . $file_name) )
	  { 
	     $list_files_other['OTHER:' . $file_key] = 'OTHER:' . $file_key;
	  }   
	}
	
	
	
	$list_files = array_merge(array('none'=>'none'), $list_files_template, $list_files_plugin, $list_files_other);
	
	unset($files_template);
	unset($list_files_template);
	unset($files_plugin);
	unset($list_files_plugin);
	
	$CI->load->helper('form');


	$form = '';
	
	$url_opt = getinfo('siteurl') . 'admin/plugin_options/modules/';
	
	echo '
	<script>
		function newLink(id, val)
		{
			var u = document.getElementById("mod_url_" + id);
			if (val == "none")
			{
				u.innerHTML = "Нет опций";
			}
			else
			{
				u.innerHTML = "<a href=\"' . $url_opt . '" + id + "/" + val + "\" target=\"_blank\">Опции</a>";
			}
		}
	</script>
	';
	
	

	# проходимся по $MODULE, потому что он определен modules_set
	foreach ($MODULES as $mod)
	{
		$form .= '<h2>' . $mod['name'] . ' ('. $mod['id'] . ')</h2>';

		//	'file_name'=>'', // подключаемый require файл
		//	'php_code'=>'', // можно произвольный php указать
		
		$v = isset($modules[$mod['id']]['file_name']) ? $modules[$mod['id']]['file_name'] : '';
		
		if (!$v or $v == 'none')
			$form .= '<p id="mod_url_' . $mod['id'] . '">Нет опций</p>';
		else
			$form .= '<p id="mod_url_' . $mod['id'] . '"><a href="' . $url_opt . $mod['id'] . '/' . $v .'" target="_blank">Опции</a></p>';
			
		$form .= NR . '<p><label><strong>' . t('Файл', __FILE__) . '</strong> '
				. form_dropdown('fmoduless[' . $mod['id'] . '][file_name]', 
				$list_files, 
				$v,
				' style="width: 650px;" onChange="newLink(\'' . $mod['id'] . '\', this.value)"') . '</label></p>';
		
		$v = isset($modules[$mod['id']]['php_code']) ? $modules[$mod['id']]['php_code'] : '';
		$form .= NR . '<p><strong>' . t('PHP', __FILE__) . '</strong><br/>' . '<textarea name="fmoduless[' . $mod['id'] . '][php_code]" style="width: 700px;">' . htmlspecialchars($v) . '</textarea></p>';				
		
		
		
		$form .= '<br>' . NR;
	}
	
	
	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo $form;
	echo '<input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;">';
	echo '</form>';

?>