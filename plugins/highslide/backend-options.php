<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Plugin «Highslide» for maxSite CMS
 * 
 * Author: (c) Илья Земсков (ака Профессор)
 * Plugin URL: http://vizr.ru/page/plugin-highslide
 */
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_highslide', 'plugins', 
		array(
			'group_1' => array(
				'type' => 'info',
				'title' => 'Общие параметры',
				'text' => '', 
			),
			'adminka' => array(
				'type' => 'checkbox',
				'name' => 'Подключать скрипт и стили на страницах админ-панели',
				'description' => 'Если галочка стоит, то стили и скрипты плагином будут подключаться ещё на страницах панели управления сайтом.',
				'default' => 1
			),
			'js_file' => array(
				'type' => 'select',
				'name' => 'Какой вариант js-файла подключать?',
				'description' => 'Выберите js-файл библиотеки Highslide JS для подключения. В скобках указана комплектация файла.',
				'values' => '0||highslide.js (<b>default</b>)#1||highslide-with-html.js (default + inline + ajax + iframe + flash)#2||highslide-with-gallery.js (default + slideshow + positioning + transitions + viewport + thumbstrip)#3||highslide-full.js (default + events + unobtrusive + imagemap + slideshow + positioning + transitions + viewport + thumbstrip + inline + ajax + iframe + flash)',
				'default' => '0'
			),
			'css' => array(
				'type' => 'textarea', 
				'name' => 'CSS стили',
				'description' => 'Вы можете указать произвольные css-стили, которые могут пригодится при оформлении.',
				'default' => ''
			),
			'group_2' => array(
				'type' => 'info',
				'title' => 'Настройки Highslide JS',
				'text' => '', 
			),
			'variables' => array(
				'type' => 'textarea', 
				'name' => 'Переменные',
				'description' => 'Настройки параметров переменной hs «по-умолчанию». Подробности см. в подразделе <b>VARIABLES</b> раздела <b>Object: hs</b> на странице «<a href="http://highslide.com/ref/" target=_blank>Highslide JS API Reference</a>». Поддерживаются все параметры, кроме <b>lang</b> - её нужно настраивать с помощью опций ниже. Каждый параметр размещать на отдельной строке. Значение параметра указывать через символ «|». Строковые значения должны указываться с кавычками! Например:<br><pre><b>showCredits | false</b><br><b>outlineType | \'rounded-white\'</b><br><b>fadeInOut | true</b></pre>', 
				'default' => 'showCredits | false'
			),			
			'custom_js' => array(
				'type' => 'textarea', 
				'name' => 'Дополнительный js-скрипт настройки',
				'description' => 'Вы можете указать произвольный js-скрипт, который поможет реализовывать особенную логику настройки и поведения библиотеки. Пример такой настройки можно увидеть на странице «<a href="http://highslide.com/ref/hs.skin" target=_blank>hs.skin</a>».',
				'default' => ''
			),
			'gal_def' => array(
				'type' => 'textarea', 
				'name' => 'Настройки галереи по-умолчанию для hs.addSlideshow',
				'description' => 'Вы можете указать произвольные настройки отображения изображений галереи. Подробности см. на странице «<a href="http://highslide.com/ref/hs.addSlideshow" target=_blank>hs.addSlideshow</a>».',
				'default' => 'interval: 5000,'.NR.'repeat: false,'.NR.'useControls: true,'.NR.'fixedControls: \'fit\','.NR.'overlayOptions: {'.NR.TAB.'opacity: .75,'.NR.TAB.'position: \'bottom center\','.NR.TAB.'hideOnMouseOut: true'.NR.'}'.NR
			),
			'gal_img_hide' => array(
				'type' => 'checkbox',
				'name' => 'Скрывать превью-изображения в галереях при разворачивании',
				'description' => 'Если галочка стоит, то при обработке <b>[gallery=имягалереи]</b> галерея будет вписана в div-блок с классом <b>.highslide-gallery-имягалереи</b> вместо div-блока с классом <b>.highslide-gallery</b> (это приведёт к тому, что превью-изображения будут скрываться при их разворачивании).',
				'default' => 0
			),
			'lang_file' => array(
				'type' => 'select',
				'name' => 'Какой язык сообщений использовать?',
				'description' => 'Выберите язык служебных сообщений. Вы можете создать свой языковой файл (см. папку <b>lang</b>) для сообщений и потом выбрать его.',
				'values' => get_langs(), #'0||english#1||русский',
				'default' => '0'
			),
			'custom_lang' => array(
				'type' => 'textarea', 
				'name' => 'Текстовые сообщения - hs.lang',
				'description' => 'Ручная настройка строк сообщений Highslide JS (нужна в случаях, когда вам нужно немного подправить стандартные строки). Подробности см. на странице «<a href="http://highslide.com/ref/hs.lang">hs.lang</a>». Поддерживаются: <b>cssDirection</b>, <b>loadingText</b>, <b>loadingTitle</b>, <b>focusTitle</b>, <b>fullExpandTitle</b>, <b>creditsText</b>, <b>creditsTitle</b>, <b>previousText</b>, <b>nextText</b>, <b>moveText</b>, <b>closeText</b>, <b>closeTitle</b>, <b>resizeTitle</b>, <b>playText</b>, <b>playTitle</b>, <b>pauseText</b>, <b>pauseTitle</b>, <b>previousTitle</b>, <b>nextTitle</b>, <b>moveTitle</b>, <b>fullExpandText</b>, <b>number</b>, <b>restoreTitle</b>. Каждый параметр размещать на отдельной строке. Значение параметра указывать через символ «|». Оставьте поле пустым, если достаточно стандартных текстов на выбраном языке. Пример:<br><pre><b>closeText | "Klose"</b><br><b>previousText | "Peredetim"</b><br><b>pauseText | "Pauze"</b></pre>', 
				'default' => ''
			),			
		),
		'Настройки плагина «Highslide»', # титул
		'Задайте необходимые значения указанным опциям. Справку по использованию плагина при верстке материалов можно <a href="http://vizr.ru/page/help-highslide-js">посмотреть здесь</a>. Cкачать последнюю версию плагина «<b>Highslide</b>» или сообщить о багах можно на <a href="http://vizr.ru/page/plugin-highslide">официальной странице плагина</a>. Узнать подробнее про js-библиотеку «<b>Highslide</b>» можно <a href="http://alexgorbatchev.com/SyntaxHighlighter/">здесь</a>.'   # инфо
	);
	
	echo '<br>Разработка плагина - <a href="http://vizr.ru/">Илья Земсков</a>';

function get_langs()
{
	$folder = getinfo('plugins_dir').'highslide/lang';
	$dir = scandir( $folder ); # сканируем папку
	if( $dir )
	{
		$out = 'default||оставить язык «по-умолчанию»';
		foreach( $dir as $name)
		{
			if( preg_match("/\.js/", $name) ) # если расширения файла js, то надо обработать
			{
				$out .= '#';
				$lines = file( $folder.'/'.$name );
				preg_match("/\[(.*?)\]/", $lines[0], $match);
				$out .= $name.'||'.$match[1];
			}
		}
			
		if( $out == '' )
		{
			$out = 'Языковые файлы не найдены. Проверьте комплектацию плагина!';
		}
			
		return $out;
	}
	else
	{
		return 'Ошибка считывания директории языковых файлов';
	}
}
?>