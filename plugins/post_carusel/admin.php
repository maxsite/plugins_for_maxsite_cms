<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	$CI = & get_instance();
	
	$options_key = 'post_carusel';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['count'] = (int)$post['f_count'];
		$options['speed'] = (int)$post['f_speed'];
		$options['pause'] = (int)$post['f_pause'];
		$options['autorotate'] = isset( $post['f_autorotate']) ? 1 : 0;
		$options['pagehooks'] = isset( $post['f_pagehooks']) ? 1 : 0;
		$options['width'] = (int)$post['f_width'];
		$options['height'] = (int)$post['f_height'];
		$options['randompage'] = isset( $post['f_randompage']) ? 1 : 0;
		$options['showtop'] = isset( $post['f_showtop']) ? 1 : 0;
	
		mso_add_option($options_key, $options, 'plugins');
		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}
	
?>
<h1><?= t('Карусель постов', __FILE__) ?></h1>
<p class="info"><?= t('Выводит записи в виде карусели', __FILE__) ?></p>

<?php
		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['count'])) $options['count'] = 5;
		$options['count'] = (int) $options['count'];
		if ($options['count'] < 1) $options['count'] = 5;

		if ( !isset($options['speed'])) $options['speed'] = 700;
		$options['speed'] = (int) $options['speed'];
		if ($options['speed'] < 1) $options['speed'] = 700;
		
		if ( !isset($options['pause'])) $options['pause'] = 3000;
		$options['pause'] = (int) $options['pause'];
		if ($options['pause'] < 1) $options['pause'] = 3000;

		if ( !isset($options['width'])) $options['width'] = 908;
		$options['width'] = (int) $options['width'];
		if ($options['width'] < 1) $options['width'] = 908;

		if ( !isset($options['height'])) $options['height'] = 226;
		$options['height'] = (int) $options['height'];
		if ($options['height'] < 1) $options['height'] = 226;
		
		if( !isset( $options['autorotate'])) $options['autorotate'] = 1;
		if( !isset( $options['pagehooks'])) $options['pagehooks'] = 0;
		if( !isset( $options['randompage'])) $options['randompage'] = 0;
		if( !isset( $options['showtop'])) $options['showtop'] = 0;
		
		$form = '';
		$form .= '<h2>' . t('Настройки', 'plugins') . '</h2>';

		$form .= '<p><div class="t150"><strong>' . t('Количество:', 'plugins') . '</strong></div> ' . ' <input name="f_count" type="text" value="' . $options['count'] . '"><span>' . NR .
		'В блоке карусели отобразится указанное количество записей' . NR . 
		'</span></p>';

		$form .= '<p><div class="t150"><strong>' . t('Скорость:', 'plugins') . '</strong></div> ' . ' <input name="f_speed" type="text" value="' . $options['speed'] . '"><span>' . NR .
		'Скорость прокрутки элементов в карусели в миллисекундах' . NR . 
		'</span></p>';

		$form .= '<p><div class="t150"><strong>' . t('Пауза:', 'plugins') . '</strong></div> ' . ' <input name="f_pause" type="text" value="' . $options['pause'] . '"><span>' . NR . 
		'Задержка между этапами прокрутки записей в миллисекундах' . NR . 
		'</span></p>';

		$checked = ( $options['autorotate'] ) ? ' checked' : '';
		$form .= '<p><div class="t150"><strong>' . t('Авто-прокрутка:', 'plugins') . '</strong></div> ' . ' <input name="f_autorotate" type="checkbox" value="' . $options['autorotate'] . '" ' . $checked . '><span>' . NR . 
		'Автоматическая прокрутка записей' . NR . 
		'</span></p><br>';
				
		$form .= '<p><div class="t150"><strong>' . t('Ширина:', 'plugins') . '</strong></div> ' . ' <input name="f_width" type="text" value="' . $options['width'] . '"><span>' . NR . 
		'Ширина блока карусели в px' . NR . 
		'</span></p>';

		$form .= '<p><div class="t150"><strong>' . t('Высота:', 'plugins') . '</strong></div> ' . ' <input name="f_height" type="text" value="' . $options['height'] . '"><span>' . NR . 
		'Высота блока карусели в px' . NR . 
		'</span></p>';
		
		$checked = ( $options['randompage'] ) ? ' checked' : '';
		$form .= '<p><div class="t150"><strong>' . t('Случайные записи:', 'plugins') . '</strong></div> ' . ' <input name="f_randompage" type="checkbox" value="' . $options['randompage'] . '" ' . $checked . '><span>' . NR . 
		'Выводить случайные записи в карусели. Если не отмечено, то выводятся последние записи.' . NR . 
		'</span></p>';

		$checked = ( $options['pagehooks'] ) ? ' checked' : '';
		$form .= '<p><div class="t150"><strong>' . t('Хуки на контент:', 'plugins') . '</strong></div> ' . ' <input name="f_pagehooks" type="checkbox" value="' . $options['pagehooks'] . '" ' . $checked . '><span>' . NR . 
		'Производить ли обработку содержимого поста плагинами' . NR . 
		'</span></p>';

		$checked = ( $options['showtop'] ) ? ' checked' : '';
		$form .= '<p><div class="t150"><strong>' . t('В самом верху:', 'plugins') . '</strong></div> ' . ' <input name="f_showtop" type="checkbox" value="' . $options['showtop'] . '" ' . $checked . '><span>' . NR . 
		'Карусель будет отображаться в самом верху страницы. В данном случае не требуется прописывать руками код вывода карусели (см. ниже описание)' . NR . 
		'</span></p><br>';
		
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo $form;
		echo '<input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;">';
		echo '</form>';
		
		$d = '<br><hr><br>';
		$d .= '<p><h2>Как использовать?</h2></p>';
		$d .= '<p>Для отображения карусели необходимо:</p>';
		$d .= '<ol><li>Определить место, где будет отображаться карусель</li>' . NR .
		      '<li>Вставить следующий код в место, определенное в п.1:<br>' . NR .
			  '<span><strong>&lt;?php if ( function_exists(\'post_carusel_show\') ) echo post_carusel_show();	?&gt;</strong></span>' . NR .
			  '</li>' . NR .
			  '</ol>';
			  
		$d .= '<br><p>Карусель можно размещать непосредственно в коде шаблона, либо с помощью ушки. Можно разместить в сайдбаре.</p>';	  
		$d .= '<p>В любом из случаев необходимо настроить ширину и высоту карусели либо в настройках, либо через файл стилей board.css</p>';
		$d .= '<p>Файл стилей board.css можно разместить в папке с шаблоном, но при этом необходимо скопировать папку <i>img/</i> из папки с плагином в папку с шаблоном для корректного отображения переключателей страниц под блоком карусели, либо в board.css самим прописать правильные пути.</p>';
		$d .= '<br>';
		$d .= '<p>Плагин создает на странице редактирования записи 2 дополнительных поля:<br>';
		$d .= '<i>"Картинка для карусели"</i> и <i>"Текст для карусели"</i></p>';
		$d .= '<p>При указании картинки в блоке карусели для записи будет отображена соответствующая картинка.<br>';
		$d .= 'При указании текста в блоке карусели вместо содержимого записи будет выведен указанный текст.</p>';
		$d .= '<br>';
		$d .= '<p><strong>Замечание: </strong><br>на странице допускается размещать только <u>ОДИН</u> блок карусели!</p>';
		echo $d;

?>