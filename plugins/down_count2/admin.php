<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * Plugin Name: down cownt 2
 * Plugin URI: http://6log.ru/down-count2
 * Author: Tux
 */

	global $MSO;
	
	$CI = & get_instance();

	$options_key = 'plugin_down_count2';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_file', 'f_prefix', 'f_format')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['file'] = $post['f_file'];
		$options['prefix'] = $post['f_prefix'];
		$options['format'] = $post['f_format'];
		$options['referer'] = isset( $post['f_referer']) ? 1 : 0;
		$options['images'] = isset( $post['f_images']) ? 1 : 0;
	
		mso_add_option($options_key, $options, 'plugins');
		
		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}
	
?>
<h1><?= t('Счетчик переходов 2', 'plugins') ?></h1>
<p class="info"><?= t('С помощью этого плагина вы можете подсчитывать количество скачиваний или переходв по ссылке.<br /> Для использования плагина обрамите нужную ссылку в код [dc]ваша ссылка[/dc]', 'plugins') ?></p>

<?php
		
		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['file']) ) $options['file'] = 'dc2.dat'; // путь к файлу данных
		if ( !isset($options['prefix']) ) $options['prefix'] = 'dc'; // префикса
		if ( !isset($options['format']) ) $options['format'] = 
			'%IMG% %URL%<sup title="' . t('Количество переходов', 'plugins') . '">%COUNT%</sup>( %SIZE% )'; // формат 
		if ( !isset($options['referer']) ) $options['referer'] = 1; // запретить скачку с чужого сайта
		if ( !isset($options['images']) ) $options['images'] = 1; 

		$form = '';

		$form .= '<h2>' . t('Настройки', 'plugins') . '</h2>';
		
		$form .= '<p><strong>' . t('Файл для хранения количества скачиваний:', 'plugins') . '</strong><br />' . 
			$MSO->config['uploads_dir'] . ' <input name="f_file" type="text" value="' . $options['file'] . '"></p>';
			
		$form .= '<p><strong>' . t('Префикс URL:', 'plugins') . '</strong> ' . getinfo('siteurl') . ' <input name="f_prefix" type="text" value="' . $options['prefix'] . '">/' . t('ссылка', 'plugins') . '</p>';
		
		$form .= '<p><strong>Формат:</strong> <input name="f_format" style="width: 500px;" type="text" value="' . htmlspecialchars($options['format']) . '"></p>';
		
	
		$chk = $options['referer'] ? ' checked="checked"  ' : '';
		$form .= '<p><label><input name="f_referer" type="checkbox" ' . $chk . '> <strong>' . t('Запретить переходы с чужих сайтов', 'plugins') . '</strong></label></p>';
		$chk2 = $options['images'] ? ' checked="checked"  ' : '';
		$form .= '<p><label><input name="f_images" type="checkbox" ' . $chk2 . '> <strong>' . t('Отображать картинки', 'plugins') . '</strong></label></p>';
		
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo $form;
		echo '<input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;" />';
		echo '</form>';

echo '<script type="text/javascript" src="'.getinfo('plugins_url').'down_count2/common.js"></script>';		

		// выведем ниже формы всю статистику
		$fn = $MSO->config['uploads_dir'] . $options['file'];
		
		$CI = & get_instance();
		$CI->load->helper('file'); // хелпер для работы с файлами
		
		if (file_exists( $fn )) // файла нет, нужно его создать
		{
			// массив данных: url => array ( count=>77 )
			$data = unserialize( read_file($fn) ); // поулчим из файла
			$j = 0;
			if ($data)
			{//'.getinfo('plugins_url').'down_count2/admin.php
				echo '<br /><h2>' . t('Статистика переходов', 'plugins') . '</h2>';
				echo '<form id="Form" action="" method="post">
					<ul id="urls">';
					
				foreach($data as $url => $aaa)
				{
					echo '<li id="urlli_'.$j.'">
						<input type="hidden" name="get_'.$j.'" value="'.$url.'"  />
						<input name="url_'.$j.'" type="checkbox"  /><strong>' . urldecode($url) . '</strong> - ' .
						t('переходов', 'plugins') . ': ' . $data[$url]['count'] . '</li>' . NR;
					$j++;
				}
				echo '</ul>';
			}
			// pr($data);
			
			echo '<br /><input name="save" type="submit" value="Удалить отмеченные"  />
			<input name="remove" type="submit" value="Очистить всё"  />
			</form>';
			
		}
//////////////
if (isset($_REQUEST['remove']))
{
global $MSO;
$CI = & get_instance();

	if ( !isset($options['file']) ) $options['file'] = 'dc2.dat';
	
	$fn = $MSO->config['uploads_dir'] . $options['file']; // имя файла
	
	$CI->load->helper('file'); // хелпер для работы с файлами
	
	write_file($fn, serialize(array())); // записываем в него пустой массив

	echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	mso_redirect(getinfo('site_url').'admin/plugin_down_count2');	
}
////////
if (isset($_REQUEST['save']))
{
global $MSO;
$CI = & get_instance();
		if ( !isset($options['file']) ) $options['file'] = 'dc2.dat';
		
		$fn = $MSO->config['uploads_dir'] . $options['file']; // имя файла
		
		
		$CI->load->helper('file'); // хелпер для работы с файлами
		
		if (!file_exists( $fn )) // файла нет, нужно его создать
			write_file($fn, serialize(array())); // записываем в него пустой массив
		
		// массив данных: url => array ( count=>77 )
		$data = unserialize( read_file($fn) ); // получим из файла


	// Добавляем варианты ответов
			foreach( $_POST as $key => $value )
			{
				if( preg_match('/get_(\d+)/',$key,$out) )
				{				
					$answer = addslashes(trim($_POST[$key]));
					if( !empty($answer) )
					{
						if (isset($_POST['url_'.$out[1]]))
						{
							echo $out[1] . $_POST['get_'.$out[1]] . '<br />';
							unset($data[$_POST['get_'.$out[1]]]);
						}
					//	else echo $_POST['url_'.$out[1]];
					}
				}
			}
	
		write_file($fn, serialize($data) ); // созраняем в файл
		
//		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
		mso_redirect(getinfo('site_url').'admin/plugin_down_count2');		
}
?>