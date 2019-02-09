<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MoonBack
 * (c) http://moonback.ru/
 */


# функция автоподключения плагина
function adinsertion_autoload()
{
	mso_hook_add( 'content', 'adinsertion_content');
	mso_hook_add( 'admin_init', 'adinsertion_admin_init'); # хук на админку
}

# функция выполняется при активации (вкл) плагина
function adinsertion_activate($args = array())
{	
//	mso_create_allow('adinsertion_edit', t('Админ-доступ к настройкам') . ' ' . t('adinsertion'));
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function adinsertion_deactivate($args = array())
{	
	// mso_delete_option('plugin_adinsertion', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function adinsertion_uninstall($args = array())
{	
	 mso_delete_option('plugin_adinsertion', 'plugins' ); // удалим созданные опции
	 mso_remove_allow('adinsertion_edit'); // удалим созданные разрешения
	return $args;
}

# функция выполняется при указаном хуке admin_init
function adinsertion_admin_init($args = array()) 
{
	if ( mso_check_allow('plugin_adinsertion') ) 
	{
		$this_plugin_url = 'plugin_adinsertion'; // url и hook
		
		# добавляем свой пункт в меню админки
		# первый параметр - группа в меню
		# второй - это действие/адрес в url - http://сайт/admin/demo
		# можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
		# Третий - название ссылки	
		
		mso_admin_menu_add('plugins', 'plugin_options/adinsertion', t('AdInsertion'));

		# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
		# связанную функцию именно она будет вызываться, когда 
		# будет идти обращение по адресу http://сайт/admin/_null
		
	}
	
	return $args;
}


# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function adinsertion_mso_options() 
{

	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_adinsertion', 'plugins', 
		array(
			'instarttext' => array(
							'type' => 'textarea', 
							'name' => 'Текст для вставки в начале каждой статьи', 
							'description' => 'Добавьте необходимый текст для вставки в начале каждой статьи', 
							'default' => ''
						),
			'incuttext' => array(
							'type' => 'textarea', 
							'name' => 'Текст для вставки после CUT', 
							'description' => 'Добавьте необходимый текст для вставки после анонса', 
							'default' => ''
						),						
			'inmiddletext' => array(
							'type' => 'textarea', 
							'name' => 'Текст для вставки в середине каждой статьи', 
							'description' => 'Добавьте необходимый текст для вставки в середине каждой статьи', 
							'default' => ''
						),
			'inendtext' => array(
							'type' => 'textarea', 
							'name' => 'Текст для вставки в конце каждой статьи', 
							'description' => 'Добавьте необходимый текст для вставки в конце каждой статьи', 
							'default' => ''
						),					
			'inhome' => array(
							'type' => 'checkbox', 
							'name' => 'Разрешить вставку на главной', 
							'description' => 'Отметьте если необходимо чтобы плагин работал на главной странице сайта', 
							'default' => '0' 
						),					
			'adddiv' => array(
							'type' => 'checkbox', 
							'name' => '"Заворачивать" в DIV', 
							'description' => 'Отметьте если необходимо чтобы вставляемый текст был "завернут" в конструкцию &lt;div class="adinsertion"&gt; текст рекламы &lt;/div&gt;<br>В этом случае Вы можете указать свои css-стили для блока рекламы', 
							'default' => '1'
						),	
			'mintext' => array(
							'type' => 'text',
							'name' => 'Минимальная длина текста для вставки рекламы в середине статьи', 
							'description' => 'Укажите количество символов. Если статья больше (длиннее) этого колличества символов, то в середину статьи будет вставляться реклама.', 
							'default' => '6000'
						),
			'excludepage' => array(
							'type' => 'textarea', 
							'name' => 'Страницы без рекламы', 
							'description' => 'Добавьте адреса страниц (через пробел или с новой строки) на которых не должно быть рекламы, к примеру: <b>links about</b>. <br>Сравнение производится по имени страницы - "Короткая ссылка", то есть все что после "http://мой сайт/page/")', 
							'default' => ''
						),					

			),
		'Настройки плагина adinsertion', // титул
		'Укажите необходимые опции.'   // инфо
	);
	
}

# функции плагина
function adinsertion_content($text = '')
{
$toadd = '1';
$divstart = '<div class="adinsertion">';
$divend = '</div>';

$options = mso_get_option('plugin_adinsertion', 'plugins', array());
	if ( !isset($options['adddiv']) ) $options['adddiv'] = '1';
	if ( !isset($options['inhome']) ) $options['inhome'] = '0';
	if ( !isset($options['instarttext']) ) $options['instarttext'] = '';
	if ( !isset($options['incuttext']) ) $options['incuttext'] = '';
	if ( !isset($options['inmiddletext']) ) $options['inmiddletext'] = '';
	if ( !isset($options['inendtext']) ) $options['inendtext'] = '';
	if ( !isset($options['excludepage']) ) $options['excludepage'] = '';
	if ( !isset($options['mintext']) ) $options['mintext'] = '6000';

if (mso_segment(2)!='' and $options['excludepage']!='') {
 if (substr_count($options['excludepage'], mso_segment(2))>0) $toadd = '0';
 }

if ($options['adddiv']=='0') {$divstart =''; $divend = '';}
if ($options['inhome']=='0' and !(is_type('page'))) $toadd='0';
if ($options['instarttext']!='' and $toadd == '1') $text = $divstart.$options['instarttext'].$divend.$text;
if ($options['inendtext']!='' and $toadd == '1') $text = $text.$divstart.$options['inendtext'].$divend;
if ($options['incuttext']!='' and $toadd == '1') $text = preg_replace('/(<a id="cut"><\/a>(.*?)?)/',$divstart.$options['incuttext'].$divend,$text);

$mintext = $options['mintext'];
$mintext = $mintext/2;

$substrlen = round(mb_strlen($text, 'UTF-8')/2);
 
if ($substrlen>$mintext) {
  $text1 = mb_substr($text, 0, $substrlen,'UTF-8');
  $text2 = mb_substr($text, $substrlen,$substrlen,'UTF-8');
  $text3 = preg_replace ('/<br>/','<br>'.$divstart.$options['inmiddletext'].$divend.'<br>', $text2, 1);
  $text = $text1.$text3;}

return $text;
}

# end file