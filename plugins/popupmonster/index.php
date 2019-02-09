<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * Ruslan Gaysin
 * (c) http://rgblog.ru/
 *
 */

# функция автоподключения плагина
function popupmonster_autoload($args = array())
{
	mso_hook_add( 'head', 'popupmonster_head');
}

# функция выполняется при деинсталяции плагина
function popupmonster_uninstall($args = array())
{	
	mso_delete_option('plugin_popupmonster', 'plugins'); // удалим созданные опции
	return $args;
}

function popupmonster_mso_options() 
{	
	$CI = & get_instance();
	$CI->load->helper('directory');
	$all_dirs = directory_map(getinfo('plugins_dir'). 'popupmonster/themes', true);
	
	if ($all_dirs) $all_dirs = implode(' # ', $all_dirs);

	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_popupmonster', 'plugins', 
		array(
			'info1' => array(
							'type' => 'info', 
							'title' => t('Основные настройки'), 
							'text' => t('Основные настройки плагина<br>', 'plugins')
						),
			'page_type' => array(
							'type' => 'select', 
							'name' => 'Показывать на страницах', 
							'description' => 'Когда выводить всплывающую форму',
							'values' => 'home||Только на главной # page||Только на страницах # all||На всех',
							'default' => 'all'
						),	
			'theme' => array(
						'type' => 'select', 
						'name' => 'Шаблон', 
						'description' => 'Выберите шаблон всплывающего окна.',
						'values' => $all_dirs,
						'default' => 'One'
					),
			'open' => array(
							'type' => 'select', 
							'name' => 'Режим появления', 
							'description' => 'Выберите тип появления формы',
							'values' => 'open||При загрузке страницы # mouseleave||При уходе мышки с сайта # unload||При выходе',
							'default' => 'open'
						),	
			'exit' => array(
						'type' => 'text', 
						'name' => 'Сообщение выхода', 
						'description' => 'Сообщение которое появляется при выходе с сайта, только при выборе предыдущей опции "При выходе"',
						'default' => 'Хотели бы Вы, подписаться на рассылку новостей, прежде чем выйти?'
					),
			'cookie' => array(
						'type' => 'text', 
						'name' => 'Не показывать повторно форму в течении Х дней', 
						'description' => 'Количество дней, по истичению которых, форма показывается повторно. 0 - показывать всегда',
						'default' => '0'
					),
			'impress' => array(
						'type' => 'text', 
						'name' => 'Показывать после просмотра X страниц', 
						'description' => 'Показывать форму только после показа выбранного количества страниц. 0 - показывать сразу',
						'default' => '0'
					),
			'delay' => array(
						'type' => 'text', 
						'name' => 'Задержка показа формы', 
						'description' => 'Задержка показа формы в секундах',
						'default' => '2'
					),
			'info2' => array(
							'type' => 'info', 
							'title' => t('Заполнение'), 
							'text' => t('Текстовые блоки<br>', 'plugins')
						),
			'title' => array(
						'type' => 'text', 
						'name' => 'Заголовок окна', 
						'description' => 'Основной заголовок окна',
						'default' => 'Бесплатная подписка на онлайн журнал "Тямтя-Лямтя"!'
					),
			'formheader' => array(
						'type' => 'text', 
						'name' => 'Заголовок формы', 
						'description' => 'Заголовок формы подписки',
						'default' => 'ПОДПИСКА!'
					),
			'paragraph' => array(
						'type' => 'text', 
						'name' => 'Текст под заголовком', 
						'description' => 'Текст под заголовком, перед началом списка',
						'default' => 'Каждый подписчик получит уникальные мега возможности!!!'
					),
			'security' => array(
						'type' => 'text', 
						'name' => 'Текст под формой', 
						'description' => 'Текст под формой',
						'default' => 'Мы гарантируем 100% конфиденциальность ваших данных! В любой момент вы можете отказаться от получения писем!'
					),
			'text1' => array(
						'type' => 'text', 
						'name' => 'Заголовок поля Name', 
						'description' => 'Заголовок поля Name',
						'default' => 'Ваше имя на русском языке:'
					),
			'value1' => array(
						'type' => 'text', 
						'name' => 'Значение по умолчанию Name', 
						'description' => 'Значение по умолчанию Name',
						'default' => ''
					),
			'text2' => array(
						'type' => 'text', 
						'name' => 'Заголовок поля EMail', 
						'description' => 'Заголовок поля EMail',
						'default' => 'E-mail, на который будут высылаться уроки:'
					),
			'value2' => array(
						'type' => 'text', 
						'name' => 'Значение по умолчанию EMail', 
						'description' => 'Значение по умолчанию EMail',
						'default' => ''
					),
			'button' => array(
						'type' => 'text', 
						'name' => 'Заголовок кнопки', 
						'description' => 'Заголовок кнопки',
						'default' => 'Хочу подписаться!'
					),
			'spisok' => array(
						'type' => 'textarea', 
						'name' => 'Список преимуществ', 
						'rows' => '5',
						'description' => 'Список преимуществ',
						'default' => 'Две кнопки "БАБЛО" - бесплатно!
		374 видеоурока по кнопке "БАБЛО"!
		Кнопку "Мега БАБЛО" всего за 999 у.е.!
		Пособие по "Тямтя-Лямтию"!
		Супер сборник из 97 ЛОХ-пакетов!
		Почетную грамоту "Тямти-Лямти"!'
					),
			'info3' => array(
							'type' => 'info', 
							'title' => t('Настройки формы'), 
							'text' => t('Элементы формы подписки<br>', 'plugins')
						),
			'fieldname' => array(
						'type' => 'text', 
						'name' => 'Имя поля ввода имени', 
						'description' => 'name = ""',
						'default' => 'field_name'
					),
			'fieldmail' => array(
						'type' => 'text', 
						'name' => 'Имя поля ввода эл.почты', 
						'description' => 'name = ""',
						'default' => 'field_email'
					),
			'fieldsubmit' => array(
						'type' => 'text', 
						'name' => 'Имя поля кнопки', 
						'description' => 'input type="submit" name="ТУТ"',
						'default' => 'SR_submitButton'
					),
			'formaction' => array(
						'type' => 'text', 
						'name' => 'Ссылка перехода для формы', 
						'description' => 'Ссылка в теге form в свойстве action',
						'default' => 'http://smartresponder.ru/subscribe.html'
					),
			'target' => array(
						'type' => 'select', 
						'name' => 'В новом окне:', 
						'description' => 'Открывать страницу подписки в новом окне',
						'values' => 'old||В том же окне # new||В новом окне',
						'default' => 'new'
					),
			'hidden' => array(
						'type' => 'textarea', 
						'name' => 'Скрытые поля формы', 
						'rows' => '5',
						'description' => 'Скопируйте скрытые поля вида <input type=hidden name=version value="1">',
						'default' => '<input type = hidden name = version value = "1">
                    <input type = hidden name = tid value = "0">
                    <input type = hidden name = uid value = "53644">
                    <input type = hidden name = lang value = "ru">
                    <input type = hidden name = "did[]" value = "51511">'
					),
		),
		'Всплывающее окно подписки', // титул
		'Укажите необходимые опции.'   // инфо
	);
}

# функции плагина
function popupmonster_head($arg = array())
{
	$options = mso_get_option('plugin_popupmonster', 'plugins', array() ); // получаем опции
	
	$def_options = array(
		'page_type' => 'all', 
		'theme' 	=> 'One',
		'cookie' 	=> '0',
		'impress' 	=> '0',
		'delay' 	=> '2',
		'open'		=> 'open',
		'exit'		=> 'Хотели бы Вы, подписаться на рассылку новостей, прежде чем выйти?',
		'title' 	=> 'Бесплатная подписка на онлайн журнал "Тямтя-Лямтя"!',
		'formheader'=> 'ПОДПИСКА!',
		'paragraph' => 'Каждый подписчик получит уникальные мега возможности!!!',
		'security'	=> 'Мы гарантируем 100% конфиденциальность ваших данных! В любой момент вы можете отказаться от получения писем!',
		'text1'		=> 'Ваше имя на русском языке:',
		'value1'	=> '',
		'text2'		=> 'E-mail, на который будут высылаться уроки:',
		'value2'	=> '',
		'button'	=>	'Хочу подписаться!',
		'fieldname' => 'field_name',
		'fieldmail'	=> 'field_email',
		'fieldsubmit'=> 'SR_submitButton',
		'formaction'=> 'http://smartresponder.ru/subscribe.html',
		'target'	=> 'new',
		'hidden'	=> '<input type = hidden name = version value = "1">
                    <input type = hidden name = tid value = "0">
                    <input type = hidden name = uid value = "53644">
                    <input type = hidden name = lang value = "ru">
                    <input type = hidden name = "did[]" value = "51511">',
		'spisok'	=> 'Две кнопки "БАБЛО" - бесплатно!
		374 видеоурока по кнопке "БАБЛО"!
		Кнопку "Мега БАБЛО" всего за 999 у.е.!
		Пособие по "Тямтя-Лямтию"!
		Супер сборник из 97 ЛОХ-пакетов!
		Почетную грамоту "Тямти-Лямти"!'
		);
	$options = array_merge($def_options, $options);
	
	if ($options['page_type']!='all') {
		if ($options['page_type']=='home' and !is_type('home')) return $arg;
		if ($options['page_type']=='page' and !is_type('page')) return $arg;
	}
	if (!is_feed()) 
	{
		echo '<link href="'.getinfo('plugins_url').'popupmonster/themes/'.$options['theme'].'/theme.css" type="text/css" rel="stylesheet" media="all" />';
		echo '<script type = "text/javascript" src = "'.getinfo('plugins_url').'popupmonster/js/lightbox.js"></script>';
		echo '<script type = "text/javascript">var masterpopup = {';
		echo '"delay": '.$options['delay'].',';
		echo '"cookie_time": '.$options['cookie'].',';
		echo '"cookie_path": "\/",';
		echo '"show_opt": "'.$options['open'].'",';
		echo '"unload_msg": "'.$options['exit'].'",';
		echo '"impression_count": '.$options['impress'].',';
		$url_template = getinfo('plugins_url').'popupmonster/themes/'.$options['theme'].'/template.php';
		$theme = '';
		$theme = file_get_contents($url_template);
		$spisok_elem = explode("\r\n",$options['spisok']);
		$spisok = '';
		foreach ($spisok_elem as $item)
		{
			$spisok .= '<li><div class = "spisok">'.$item.'</div></li>';		
		}
		$vars = array (
			'{TITLE}'	=>	$options['title'],
			'{FORMHEADER}'	=>	$options['formheader'],
			'{PARAGRAPH}'	=>	$options['paragraph'],
			'{SECURITY}'	=>	$options['security'],
			'{TEXT1}'	=>	$options['text1'],
			'{VALUE1}'	=>	$options['value1'],
			'{TEXT2}'	=>	$options['text2'],
			'{VALUE2}'	=>	$options['value2'],
			'{BUTTON}'	=>	$options['button'],
			'{SPISOK}'	=>	$spisok,
			'{FIELDNAME}'=> $options['fieldname'],
			'{FIELDMAIL}'=>	$options['fieldmail'],
			'{FIELDSUBMIT}'=>$options['fieldsubmit'],
			'{FORMACTION}'=> $options['formaction'],
			'{TARGET}'	=> ($options['target']=='new')?' target="_blank"':'',
			'{PATH}'	=> getinfo('plugins_url').'popupmonster/themes/'.$options['theme'].'/',
			'{HIDDEN}'	=> $options['hidden']
		);
		$theme = str_replace(array_keys($vars), array_values($vars), $theme);
		$theme = preg_replace('/[\r\n\s]+/',' ',$theme);
		$theme = preg_replace('/"/','\"',$theme);
		echo '"output": "'.$theme.'",}; </script>';
	}
}

# end file
