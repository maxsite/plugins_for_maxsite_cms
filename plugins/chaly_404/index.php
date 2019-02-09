<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/* 

Файл является неотъемлемой частью плагина chaly_404
Создано для использования в Maxsite CMS http://max-3000.com
Разработано авторским коллективом разработчиков студии "Чалый со товарищи" 
http://ЧалыйСоТоварищи.РФ http://ChalyComrades.com

*/

# функция автоподключения плагина
function chaly_404_autoload($args = array())
{
	mso_create_allow('chaly_404_edit', t('Админ-доступ к плагину редиректов', 'plugins'));
	mso_hook_add( 'admin_init', 'chaly_404_admin_init'); # хук на админку
	if ( mso_segment(1)!='admin' ) // так можно исключить некоторые сегменты из обработки 
		mso_hook_add( 'init', 'chaly_404_init'); # хук на init
}


# функция выполняется при деинстяляции плагина
function chaly_404_uninstall($args = array())
{
	mso_delete_option('chaly_404', 'plugins'); // удалим созданные опции
	return $args;
}

# цепляемся к хуку init
function chaly_404_init($args = array())
{
	$num_seg = count(explode('/',$_SERVER['REQUEST_URI']))-1;
	//pr($num_seg);	

	$options = mso_get_option('chaly_404', 'plugins', array());
	if ( !isset($options['all']) ) return $args; // нет опций

	$all = explode("\n", $options['all']); // разобъем по строкам

	if (!$all) return $args; // пустой массив

	$corr=false;
	
	foreach ($all as $row) // перебираем каждую строчку
	{
		$urls = explode('/', $row); //  сегменты
		$urls = array_map('trim', $urls);

		$ii=0; // 
		while ($ii<$num_seg) // в $urls[$ii] лежит сегмент разрешённого адреса
		{	
			if ( isset($urls[$ii]) ) 
			{
				if ( ( mso_segment($ii+1)==$urls[$ii] ) or ($urls[$ii]=='*') ) // если совпадает с текущим сегментом или *
				{ 
					// pr( $urls[$ii]); 
					if ($ii==$num_seg-1) // признак правильности - если дошли до конца массива
						$corr = true;
				}
				else break;
			}
			else break;
			$ii++;
		}
	}
	if (!$corr) header('HTTP/1.0 404 Not Found'); 
	//pr($corr);
	return $args;
}

# функция выполняется при хуке admin_init
function chaly_404_admin_init($args = array())
{
	if ( !mso_check_allow('chaly_404_edit') )
	{
		return $args;
	}

	$this_plugin_url = 'chaly_404'; // url и hook

	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки

	mso_admin_menu_add('plugins', $this_plugin_url, t('Заголовки HTTP', 'plugins'));

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url)
	# связанную функцию именно она будет вызываться, когда
	# будет идти обращение по адресу http://сайт/admin/chaly_404
	mso_admin_url_hook ($this_plugin_url, 'chaly_404_admin_page');

	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function chaly_404_admin_page($args = array())
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('chaly_404_edit') )
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}

	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Заголовки HTTP', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Заголовки HTTP', __FILE__) . ' - " . $args; ' );

	require(getinfo('plugins_dir') . 'chaly_404/admin.php');
}

# end file
