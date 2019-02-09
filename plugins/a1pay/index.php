<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

static $a1pay = 'a1pay'; // имя папки плагина
static $a1process = 'a1process'; // имя страницы обработчика
static $a1success = 'a1success'; // имя страницы успешной оплаты
static $a1fail = 'a1fail'; // имя страницы неуспешной оплаты

# функция автоподключения плагина
function a1pay_autoload()
{
	mso_create_allow('a1pay_edit', t('Админ-доступ к настройкам', 'plugins') . ' ' . t('a1pay', __FILE__));
	mso_hook_add( 'admin_init', 'a1pay_admin_init'); # хук на админку
	mso_hook_add( 'custom_page_404', 'a1pay_custom_page_404');
	mso_hook_add( 'content_content', 'a1pay_content');
}

# функция выполняется при активации (вкл) плагина
function a1pay_activate($args = array())
{	
	mso_create_allow('a1pay_edit', t('Админ-доступ к настройкам', 'plugins') . ' ' . t('a1pay', __FILE__));
	
	// создать таблицу, если нет такой
	$CI = & get_instance();

	$query = $CI->db->query('CREATE TABLE IF NOT EXISTS `' . $CI->db->dbprefix . 'a1pay_services` (
								`id` int(11) NOT NULL AUTO_INCREMENT,
								`service_id` int(11) NOT NULL,
								`key` text CHARACTER SET utf8 NOT NULL,
								`filename` text CHARACTER SET utf8 NOT NULL,
								`subfolder` text CHARACTER SET utf8 NOT NULL,
								`title` text CHARACTER SET utf8 NOT NULL,
								`downcount` int(11),
								`lastdownload` datetime DEFAULT \'0000-00-00 00:00:00\',
								`cost` float(11),
								`mincost` float(11),
								PRIMARY KEY (`id`)
							) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ');

	$query = $CI->db->query('CREATE TABLE IF NOT EXISTS `' . $CI->db->dbprefix . 'a1pay_purchases` (
							`tid` int(11) NOT NULL,
							`order_id` int(11) NOT NULL,
							`service_id` int(11) NOT NULL,
							`type` text CHARACTER SET utf8 NOT NULL,
							`cost_partner` float(11) NOT NULL,
							`cost_system` float(11) NOT NULL,
							`date` datetime DEFAULT \'0000-00-00 00:00:00\' NOT NULL,
							`duration` int(11) NOT NULL,
							`check` text CHARACTER SET utf8 NOT NULL,
							`ip` text CHARACTER SET utf8 NOT NULL,
							`email` text CHARACTER SET utf8 NULL,
							`phone` text CHARACTER SET utf8 NULL,
							PRIMARY KEY (`tid`)
							) ENGINE=MyISAM  DEFAULT CHARSET=utf8');
	return $args;

}

# функция выполняется при деактивации (выкл) плагина
function a1pay_deactivate($args = array())
{	
	// mso_delete_option('plugin_a1pay', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function a1pay_uninstall($args = array())
{	
	mso_delete_option('plugin_a1pay', 'plugins'); // удалим созданные опции
	mso_remove_allow('a1pay_edit'); // удалим созданные разрешения
	
	// удалить таблицу
	$CI = & get_instance();
	$query = $CI->db->query('DROP TABLE IF EXISTS `' . $CI->db->dbprefix .'a1pay_services`');
	$query = $CI->db->query('DROP TABLE IF EXISTS `' . $CI->db->dbprefix .'a1pay_purchases`');
	
	return $args;
}

# функция выполняется при указаном хуке admin_init
function a1pay_admin_init($args = array()) 
{
	if ( mso_check_allow('a1pay_edit') ) 
	{
		$this_plugin_url = 'a1pay'; // url и hook
		
		# добавляем свой пункт в меню админки
		# первый параметр - группа в меню
		# второй - это действие/адрес в url - http://сайт/admin/demo
		#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
		# Третий - название ссылки	
		
		mso_admin_menu_add('plugins', $this_plugin_url, t('Система оплаты A1Pay', __FILE__));

		# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
		# связанную функцию именно она будет вызываться, когда 
		# будет идти обращение по адресу http://сайт/admin/androidfan_models
		mso_admin_url_hook ($this_plugin_url, 'a1pay_admin_page');
	}
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function a1pay_admin_page($args = array()) 
{
	if ( !mso_check_allow('a1pay_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}

	# выносим админские функции отдельно в файл
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('a1pay', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('a1pay', __FILE__) . ' - " . $args; ' );
	require(getinfo('plugins_dir') . 'a1pay/admin.php');
}

function a1pay_custom_page_404($args=false)
{
	$segment = mso_segment(1);
	if ($segment == 'a1process')
	{
		// обработчик, вызвается системой a1pay. Этому скрипту передается информация о принятых платежах
		require(getinfo('plugins_dir'). 'a1pay' . '/process.php');
		return true;
	} 
	else if ($segment == 'a1success') {
		// если оплата успешна, идет перенаправление сюда
		$get = mso_url_get();
		$get_arr = mso_parse_url_get( $get );
		require(getinfo('plugins_dir'). 'a1pay' . '/prepare.php');
		return true;
	}
	else if ($segment == 'a1fail') {
		// неуспешный платеж
		$get = mso_url_get();
		$get = mso_parse_url_get( $get );
		if ( isset( $get['result']) and !empty( $get['result'] )) {
			if ( $get['result'] == 'sign_error' ) {
				echo 'Платеж не принят. Ошибка проверки подписи.';
			} else if ( ( $get['result'] == 'sum_error'  )) {
				echo 'Платеж не принят. Оплаченная сумма не соответствует ожидаемой.';
			}
		} else {
			echo 'Платеж не принят. Неизвестная ошибка.';
		}
		
		return true;
	} else if ( $segment == 'a1download' ) {
		// будем скачивать
		$get = mso_url_get();
		$get_arr = mso_parse_url_get( $get );
		require(getinfo('plugins_dir'). 'a1pay' . '/download.php');
		return true;
	}
	return $args;
}


function get_params_by_id ( $id ) {
	$res = array();
	if ( ! is_numeric( $id ) ) return false;
	$CI = &get_instance();
	//$CI->db->select('id, service_id, filename, subfolder, title, downcount, lastdownload, cost, mincost');
	$CI->db->select('`id`, `service_id`, `key`, `title`, `cost`, `mincost`');
	
	$CI->db->where('id', $id);
	$query = $CI->db->get('a1pay_services');
	if ($query->num_rows() > 0)
	{	
		$res = $query->result_array();
		$res = isset($res[0]) ? $res[0] : false;
		return $res;
	} else return false;
}

function a1pay_content_callback($matches)
{


		
	$id = $matches[1];
	// получить инфу по id
	$res = get_params_by_id( $id );
	$out = '';
	if ( $res !== false ) {
	
		$options = mso_get_option('a1pay', 'plugins', array());
		if ( !isset($options['mybutton']) or empty($options['mybutton'])) 
		{
			$options['mybutton'] = 'https://partner.a1pay.ru/gui/images/a1lite_buttons/button_small.png'; 
		} else {
			$options['mybutton'] = getinfo('template_url') . $options['mybutton'];
		}	
		$mybutton = $options['mybutton'];


		
		// вывести форму
		if ( $res['mincost'] > 0 ) {
			echo '<script language="javascript">
			  var mincost = ' . $res['mincost'] . ';' . '
			  function checkCost(f) {
				 if ((f.cost.value < mincost) || (isNaN(f.cost.value))){
					   alert("Стоимость не может быть меньше чем " + f.cost.value + " руб.");
					   return false;
					}
			   }
			</script>';
			$form = '<form method="POST"  class="application"  accept-charset="UTF-8" action="https://partner.a1pay.ru/a1lite/input" onSubmit="return checkCost(this)">';
		} else 
			$form = '<form method="POST"  class="application"  accept-charset="UTF-8" action="https://partner.a1pay.ru/a1lite/input">';
			
		$form .= '<input type="hidden" name="key" value="' . $res['key'] . '" />';
		$form .= '<input type="hidden" name="name" value="'.$res['title'].'" />';
		$form .= ' <input type="hidden" name="default_email" value="" />';
		$form .= '<input type="hidden" name="order_id" value="0" />';
		
		
		if ( $res['mincost'] == -1 ) {
			// фиксированная цена
			if ( !isset($options['desc_fixprice']) ) $options['desc_fixprice'] = ''; 
			$desc_fixprice = $options['desc_fixprice'];
		
			if ( !empty($desc_fixprice) ) $form .= '<p>' . $desc_fixprice . '</p>';
			$form .= '<div class="cost" style="float: left; line-height: 28px; height: 28px; margin-right: 10px;">';
			$form .= ' <input type="hidden" name="cost" value="' . $res['cost'] . '" />';		
			$form .= '<label>' . $res['cost'] . '</label><span>руб.</span>';
			
		} else if ( $res['mincost'] == 0 ) {
			// свободная цена
			$form .= '<p>Этот продукт продается по свободной цене. Вы можете указать любую сумму, которую Вам не жалко.</p>';
			$form .= '<div class="cost" style="float: left; line-height: 28px; height: 28px; margin-right: 10px;">';
			$form .= '<input type="input" name="cost" value="' . $res['cost'] . '" style="width:45px;padding:2px;text-align:right;" /> <span>руб.</span>';
		} else {
			// цена с интервалом
			if ( !isset($options['desc_freeprice']) ) $options['desc_freeprice'] = ''; 
			$desc_freeprice = $options['desc_freeprice'];	
		
			$repl = '<span class="freeprice">' . $res['mincost'] . ' руб.' . '</span>';
			$desc_freeprice = str_replace( '%mincost%',  $repl, $desc_freeprice);
			if ( !empty($desc_freeprice) ) $form .= '<p>' . $desc_freeprice . '</p>';
			//$form .= '<p>Этот продукт можно приобрести по минимальной цене ' . $res['mincost'] . ' руб., но не все функции при этом будут доступны. Оплачивая полную стоимость, Вы получаете полноценный продукт, без ограничений..</p>';
			$form .= '<div class="cost" style="float: left; line-height: 28px; height: 28px; margin-right: 10px;">';
			$form .= '<input type="input" class="a1pay_cost" name="cost" value="' . $res['cost'] . '" style="width:45px;padding:2px;text-align:right;" /> <span>руб.</span>';
			
		}
		$form .= '</div>';
		$form .= '  <input type="image" style="border:0;" src="' . $mybutton . '" value="Оплатить" />';
		$form .= '</form>';
		$out = '<div class="a1pay">' . $form . '</div>';
	}
	
	return $out;
}
function a1pay_content($content = '') 
{
	/*
	$pattern = '|\[a1pay\]<a(.*?)href="(.*?)"(.*?)>(.*?)</a>\[/a1pay\]|ui';
	$content = preg_replace_callback($pattern, 'a1pay_content_callback', $content);

	$pattern = '|\[a1pay\]\[url=(.*?)\](.*?)\[/url\]\[/a1pay\]|ui';
	$content = preg_replace_callback($pattern, 'a1pay_content_callback_url', $content);
	*/

	$pattern = '|\[a1pay id=(\d+)\]|ui';
	$content = preg_replace_callback($pattern, 'a1pay_content_callback', $content);
	
	// найти в коде тег [a1pay] и сделать замену
	
	return $content;	
}


?>