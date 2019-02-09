<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Plugin «Uppod-плеер» for MaxSite CMS
 * 
 * Author: (c) Илья Земсков (ака Профессор)
 * Plugin URL: http://vizr.ru/page/plugin-uppod-player
 */

	# ключ, тип, ключи массива
	$uppod_plugin_options = array(
			
		# Общие
		'group_1' => array(
			'type' => 'info',
			'title' => 'Общие параметры',
			'text' => '',
		),
		'pleerversion' => array(
			'type' => 'select',
			'name' => 'Версия плеера',
			'description' => 'Выберите версию плеера, которая будет использоваться для отображения плеера на сайте. Подробнее о доступных версиях смотрите <a href="http://uppod.ru/help/specification/">здесь</a>. Учтите, что версия HTML5β находится на стадии тестирования и в ней возможны ошибки.',
			'values' => 'swf||Flash (SWF)#html5||HTML5β',
			'default' => 'swf'
		),
		'warning_message' => array(
			'type' => 'text',
			'name' => 'Текст сообщения при невозможности отобразить плеер',
			'description' => 'Укажите текст сообщения, которое будет отображено вместо плеера.',
			'default' => 'Требуется включить JavaScript или обновить плеер!'
		),
		'center' => array(
			'type' => 'checkbox', 
			'name' => 'Размещать плеер по центру', 
			'description' => 'Если поставить галочку, то код будет обрамляться в блок &lt;center&gt;&lt;/center&gt;. По-умолчанию плеер размещается «как есть».',
			'default' => '0'
		),
		'bgcolor' => array(
			'type' => 'text',
			'name' => 'Фоновый цвет',
			'description' => 'Укажите RGB код цвета. Знак &quot;#&quot; подставится автоматически.',
			'default' => 'FFFFFF'
		),
		'wmode' => array(
			'type' => 'select',
			'name' => 'Режим отображения SWF-плеера на сайте',
			'description' => 'Можно настроить режим отображения. Подробнее <a href="http://uppod.ru/player/faq2/wmode/">здесь</a>. Если вы не знаете какой режим использовать — используйте режим &quot;авто&quot;.',
			'values' => '||авто#window||window#direct||direct#transparent||transparent#opaque||opaque',
			'default' => ''
		),
		'html_before' => array(
			'type' => 'textarea', 
			'name' => 'Текст перед выводом плеера',
			'description' => 'Можно использовать html.',
			'default' => ''
		),
		'html_after' => array(
			'type' => 'textarea', 
			'name' => 'Текст после вывода плеера',
			'description' => 'Можно использовать html.',
			'default' => ''
		),
		'comments' => array(
			'type' => 'checkbox',
			'name' => 'Обрабатывать комментарии',
			'description' => 'Заданные BB-коды можно будет использовать в комментариях.',
			'default' => 0
		),
		'tag_uppod' => array(
			'type' => 'checkbox',
			'name' => 'Обрабатывать универсальный тэг [uppod]',
			'description' => 'Если поставить галочку, то кроме тэгов для видео/аудио-плееров будет доступен к использованию тэг [uppod] (тип плеера будет передаваться через параметр type). Не ставьте галочку, если таким тэгом не пользуетесь - это сэкономит вычислительные ресурсы вебсервера.',
			'default' => 0
		),
		'tag_youtube' => array(
			'type' => 'checkbox',
			'name' => 'Обрабатывать тэг [youtube]',
			'description' => 'Если поставить галочку, то кроме тэгов для видео/аудио-плееров будет доступен к использованию тэг [youtube], что позволит выводить ролики с сервиса YouTube.com (нужно указать адрес ролика между парным тэгом [youtube][/youtube]). Не ставьте галочку, если таким тэгом не пользуетесь - это сэкономит вычислительные ресурсы вебсервера.',
			'default' => 0
		),
		'editor_buttons' => array(
			'type' => 'checkbox',
			'name' => 'Добавлять кнопки в редактор',
			'description' => 'Если поставить галочку, то в админ-панели в редакторах editor_nic и markItUp будут добавлены кнопки вставки тэгов плеера.',
			'default' => 1
		),
			
		# Видео-плеер
		'group_2' => array(
			'type' => 'info',
			'title' => 'Параметры видео-плеера',
			'text' => '',
		),
		'code_video' => array(
			'type' => 'text',
			'name' => 'BB-код для видео',
			'description' => 'Укажите основу кода. Например, для &quot;video&quot; надо будет использовать соответственно bb-коды [video] и [/video].',
			'default' => 'video'
		),
		'style_video' => array(
			'type' => 'select',
			'name' => 'Файл стилей для видео-плеера',
			'description' => 'Скачайте ваш файл стиля оформления видео-плеера с соответствующего раздела сайта <a href="http://uppod.ru/player/my/media=video">uppod.ru</a> и разместите его в папку: &quot;.../plugins/uppod/style/&quot;. Расширение файла должно быть &quot;<b>.TXT</b>&quot;',
			'values' => uppod_styles(),
			'default' => ''
		),
		'width_video' => array(
			'type' => 'text',
			'name' => 'Ширина видео-плеера',
			'description' => 'В пикселах (px)',
			'default' => '600'
		),
		'height_video' => array(
			'type' => 'text',
			'name' => 'Высота видео-плеера',
			'description' => 'В пикселах (px)',
			'default' => '375'
		),
		'fullscreen' => array(
			'type' => 'checkbox',
			'name' => 'Разрешить раскрытие видео-плеера на весь экран',
			'description' => 'На панели видео-плеера будет работать специальная кнопка &quot;Раскрытие на весь экран&quot;. Вывод кнопки контролируется через настройку стиля плеера.',
			'default' => 0
		),
			
		# Аудио-плеер
		'group_3' => array(
			'type' => 'info',
			'title' => 'Параметры аудио-плеера',
			'text' => '',
		),
		'code_audio' => array(
			'type' => 'text',
			'name' => 'BB-код для аудио',
			'description' => 'Укажите основу кода. Например, для &quot;audio&quot; надо будет использовать соответственно bb-коды [audio] и [/audio].',
			'default' => 'audio'
		),
		'style_audio' => array(
			'type' => 'select', 
			'name' => 'Файл стилей для аудио-плеера',
			'description' => 'Скачайте ваш файл стиля оформления аудио-плеера с соответствующего раздела сайта <a href="http://uppod.ru/player/my/media=audio">uppod.ru</a> и разместите его в папку: &quot;.../plugins/uppod/style/&quot;. Расширение файла должно быть &quot;<b>.TXT</b>&quot;',
			'values' => uppod_styles(),
			'default' => ''
		),
		'width_audio' => array(
			'type' => 'text', 
			'name' => 'Ширина аудио-плеера',
			'description' => 'В пикселах (px)',
			'default' => '400'
		),
		'height_audio' => array(
			'type' => 'text',
			'name' => 'Высота аудио-плеера',
			'description' => 'В пикселах (px)',
			'default' => '90'
		),
	);
?>