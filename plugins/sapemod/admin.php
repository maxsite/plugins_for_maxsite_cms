<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	$CI = & get_instance();
	
	$options_key = 'sapemod';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_kod')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['kod'] = $post['f_kod'];
		
		$options['go'] = 0; // признак, что код установлен верно - каталог есть и доступен для записи
		
		// проверим введенный код
		$fn = $_SERVER['DOCUMENT_ROOT'] . '/' . $options['kod'] . '/sape.php';
		
		if (!file_exists($fn)) // нет файла, просто выведем предупреждение
		{
			echo '<div class="error">Введенный вам код, возможно неправильный, или вы не распаковали архив на сервере!</div>';
		}
		else // есть файл, проверим что каталог доступен на запись
		{
			if (!is_writable($_SERVER['DOCUMENT_ROOT'] . '/' . $options['kod'])) 
				echo '<div class="error">Указанный вами каталог недоступен для записи. Установите для него права 777 (разрешающие запись).</div>';
			else
				$options['go'] = 1; // нет ошибок
		}
		
		$options['start'] = isset($post['f_start']) ? 1 : 0;
		$options['test'] = isset($post['f_test']) ? 1 : 0;
		$options['anticheck'] = isset($post['f_anticheck']) ? 1 : 0;
		$options['context'] = isset($post['f_context']) ? 1 : 0;
		$options['context_comment'] = isset($post['f_context_comment']) ? 1 : 0;
		
		mso_add_option($options_key, $options, 'plugins');
		echo '<div class="update">Настройки обновлены!</div>';
	}
	
?>
<h1>Настройка Sape Mod</h1>
<div align="center">
<?php
	$promo_array = array('<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="468" height="60"><param name="movie" value="http://img.sape.ru/bn/sape_011.swf"/><param name="bgcolor" value="#FFFFFF"/><param name="quality" value="high"/><param name="flashvars" value="ref_id=lIQENJVrZj"/><param name="allowscriptaccess" value="samedomain/"><embed type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" width="468" height="60" src="http://img.sape.ru/bn/sape_011.swf" bgcolor="#FFFFFF" quality="high" flashvars="ref_id=lIQENJVrZj"/></object>',
	'<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="468" height="60"><param name="movie" value="http://img.sape.ru/bn/sape_20_468_60_1.swf"/><param name="bgcolor" value="#FFFFFF"/><param name="quality" value="high"><param name="flashvars" value="ref_id=lIQENJVrZj"><param name="allowscriptaccess" value="samedomain"><embed type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" width="468" height="60" src="http://img.sape.ru/bn/sape_20_468_60_1.swf" bgcolor="#FFFFFF" quality="high" flashvars="ref_id=lIQENJVrZj"/></object>', 
	'<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="468" height="60" align="middle"><param name="movie" value="http://img.sape.ru/bn/sape_004.swf?myID=lIQENJVrZj" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" /><embed src="http://img.sape.ru/bn/sape_004.swf?myID=lIQENJVrZj" quality="high" bgcolor="#ffffff" width="468" height="60" align="middle" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" /></object>', 
	'<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="468" height="60" align="middle"><param name="movie" value="http://img.sape.ru/bn/sape_002.swf?myID=lIQENJVrZj" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" /><embed src="http://img.sape.ru/bn/sape_002.swf?myID=lIQENJVrZj" quality="high" bgcolor="#ffffff" width="468" height="60" align="middle" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" /></object>',
	'<a target="_blank" href="http://www.sape.ru/r.lIQENJVrZj.php"><img src="http://img.sape.ru/bn/468_2.gif" border="0" /></a>', 
	'<a target="_blank" href="http://www.sape.ru/r.lIQENJVrZj.php"><img src="http://img.sape.ru/bn/3.gif" border="0" /></a>', 
	'<a target="_blank" href="http://www.sape.ru/r.lIQENJVrZj.php"><img src="http://img.sape.ru/bn/2.gif" border="0" /></a>', 
	'<a target="_blank" href="http://www.sape.ru/r.lIQENJVrZj.php"><img src="http://img.sape.ru/bn/1.gif" border="0" /></a>');
	$rnd = array_rand($promo_array);
	$str = $promo_array[ $rnd ];
	pr( $str );
?>	
</div>
<h2>Небольшое описание модификации</h2>
<p>Данная модификация выводит все ссылки в <b>одном!!!</b> блоке аналогично блокам контекстной реклами (Яндекс.Директ, Google Adsense, Begun и др.)</p>
<br />
<p>Для настройки плагина выполните следующие шаги:</p>
<ol>
<li>Скачайте с сайта <a href="http://www.sape.ru/r.lIQENJVrZj.php" target="_blank">sape.ru</a> архив с вашим кодом.
<li>Распакуйте архив. Внутри архива будет лежать папка. Имя папки будет вроде такого: «8df7s4sd2if89as5v34vbez3e2».
<li>Скопируйте эту папку к себе на сайт. Положите ее в <b>корень</b> вашего сайта.
<li><b>Выставите права доступа 777</b> на эту папку (но не на файл <a href="http://www.sape.ru/r.lIQENJVrZj.php" target="_blank">sape.ru</a>), чтобы php-клиент <a href="http://www.sape.ru/r.lIQENJVrZj.php" target="_blank">sape.ru</a> мог управлять файлом с базой ссылок.
</ol>
<br>
<p><strong>Блок вывода ссылок можно размещать несколькими способами:</strong></p>
<ol>
<li>размещение с помощью виджета</li>
<li>размещение вручную</li>
</ol>
<br />
<p><strong>Размещение с помощью виджета:</strong></p>
<p>в этом случае для виджета можно указать тип вывода блока ссылок - вертикальный или горизонтальный</p>
<p>Виджетов может быть несколько, для каждого виджета можно указать сколько ссылок выводить.</p>
<br />
<p><strong>Размещение вручную:</strong></p>
<p>В этом случае используется функция <b>sapemod_out( $type, $snap )</b></p>
<pre>
	$count = 0; // указывается количество ссылок в блоке, если в переменной пусто или 0, то выводятся все ссылки
	$type = 'vertical'; // вертикальный блок
	// $type = 'horizontal'; // горизонтальный блок
	// $snap - это массив
	$snap['snap'] = 'true'; // если true - отображаем скриншоты сайтов
	$snap['width'] и $snap['height'] - ширина и высота скриншота сайта
	if (function_exists('sapemod_out')) sapemod_out($count, $type, $snap);
	
</pre>
<p>Скришоты сайтов делаются через сервис http://open.thumbshots.org и выдают скриншот размером 120x90.<br />
Для уменьшения размера как раз можно задать ширину и высоту, которые пропишутся в атрибуты тэга &lt;img&gt;<br />
Скриншоты некотрых сайтов могут не отображаться. На том сервисе есть какой-то таймаут по времени или на количество полученных скриншотов.</p>
<?php
		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['kod']) ) $options['kod'] = ''; 
		if ( !isset($options['test']) ) $options['test'] = false; 
		if ( !isset($options['start']) ) $options['start'] = true; 
		if ( !isset($options['anticheck']) ) $options['anticheck'] = false; 
		if ( !isset($options['context']) ) $options['context'] = true; 
		if ( !isset($options['context_comment']) ) $options['context_comment'] = true; 
		
		$checked_test = $options['test'] ? ' checked="checked" ' : '';
		$checked_start = $options['start'] ? ' checked="checked" ' : '';
		$checked_anticheck = $options['anticheck'] ? ' checked="checked" ' : '';
		$checked_context = $options['context'] ? ' checked="checked" ' : '';
		$checked_context_comment = $options['context_comment'] ? ' checked="checked" ' : '';
		
		$form = '';
		$form .= '<p><strong>Sape user ID:</strong> ' . ' <input name="f_kod" type="text" style="width: 300px;" value="' . $options['kod'] . '"></p>';
		
		$form .= '<p><label><input name="f_start" type="checkbox"' . $checked_start . '> Включить плагин</label></p>';
		$form .= '<p><label><input name="f_context" type="checkbox"' . $checked_context . '> Контекстные ссылки</label></p>';
		$form .= '<p><label><input name="f_context_comment" type="checkbox"' . $checked_context_comment . '> Контекстные ссылки в комментариях</label></p>';
		$form .= '<p><label><input name="f_test" type="checkbox"' . $checked_test . '> Проверка установленного кода</label></p>';
		$form .= '<p><label><input name="f_anticheck" type="checkbox"' . $checked_anticheck . '> Антиобнаружение продажных ссылок</label></p>';
		
		
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo $form;
		echo '<input type="submit" name="f_submit" value=" Сохранить изменения " style="margin: 25px 0 5px 0;">';
		echo '</form>';

?>