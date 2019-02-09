<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * RGBlog
 * (c) http://rgblog.ru/
 */

# функция автоподключения плагина
function wm_merchant_autoload()
{
	mso_hook_add( 'init', 'wm_merchant_init'); # хук на обработку входящего url
	mso_hook_add('admin_init', 'wm_merchant_admin_init'); # хук на админку
	mso_hook_add( 'content', 'wm_merchant_content'); # хук на обработку текста	
}

# функция выполняется при активации (вкл) плагина
function wm_merchant_activate($args = array())
{	
	mso_create_allow('wm_merchant_edit', t('Админ-доступ к настройкам') . ' ' . t('WM_Merchant'));
	$CI = get_instance();
	$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
	$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
	$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate;

	if ( !$CI->db->table_exists('wm_products'))
	{
  	$sql = "
	CREATE TABLE  `" . $CI->db->dbprefix . "wm_products` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`price` double(10,1) NOT NULL,
	`attach` VARCHAR(255) NOT NULL,
	`text` TEXT NOT NULL,
	PRIMARY KEY (`id`)
	)" . $charset_collate;
		$CI->db->query($sql);
	}
	
	if ( !$CI->db->table_exists('wm_orders'))
	{
  	$sql = "
	CREATE TABLE  `" . $CI->db->dbprefix . "wm_orders` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`item` INT(10) NOT NULL,
	`date` VARCHAR(17) NOT NULL,
	`purse` VARCHAR(20) NOT NULL,
	`email` VARCHAR(100) NOT NULL,
	`wm_id` VARCHAR(20) NOT NULL,
	PRIMARY KEY (`id`)
	)" . $charset_collate;
		$CI->db->query($sql);
	}	
	return $args;
}

# функция выполняется при деинсталяции плагина
function wm_merchant_uninstall($args = array())
{	
	mso_delete_option('plugin_wm_merchant', 'plugins' ); // удалим созданные опции
	mso_remove_allow('wm_merchant_edit'); // удалим созданные разрешения

	$CI = &get_instance();
	$CI->load->dbforge();
	$CI->dbforge->drop_table('wm_products');
	$CI->dbforge->drop_table('wm_orders');
	return $args;
}

# функция выполняется при указаном хуке admin_init
function wm_merchant_admin_init($args = array()) 
{
	if ( !mso_check_allow('wm_merchant_edit') ) 
	{
		return $args;
	}	
	$this_plugin_url = 'wm_merchant'; // url и hook
	mso_admin_menu_add('plugins', $this_plugin_url, t('WebMoney Merchant', __FILE__));
	mso_admin_url_hook ($this_plugin_url, 'wm_merchant_admin_page');	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function wm_merchant_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('wm_merchant_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Таблицы спидвея', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Таблицы спидвея', __FILE__) . ' - " . $args; ' );
	
	if ( mso_segment(3) == 'products') require(getinfo('plugins_dir') . 'wm_merchant/products.php');
	elseif ( mso_segment(3) == 'orders') require(getinfo('plugins_dir') . 'wm_merchant/orders.php');
	else require(getinfo('plugins_dir') . 'wm_merchant/admin.php');
}

# функции плагина
function wm_merchant_init($args = array())
{	
	# опции плагина
	$options = mso_get_option('plugin_wm_merchant', 'plugins', array());
	if ( !isset($options['prefix']) ) $options['prefix'] = 'pay';
	if (mso_segment(1) == $options['prefix'] and mso_segment(2)) 
	{
		$url = mso_segment(2);
		if (isset($_SERVER['HTTP_REFERER'])) 
		{
			$p = parse_url($_SERVER['HTTP_REFERER']);
			if (isset($p['host'])) $p = $p['host'];
				else $p = '';
			if ( $p = $_SERVER['HTTP_HOST'] )
			{
				wm_merchant_a($url);
			}	
		}
		if ($url == $options['result']) wm_merchant_b();
		elseif ($url == 'yes') wm_merchant_c();
		elseif ($url == 'no') wm_merchant_d();
	}
	return $args;
}

function wm_merchant_a($product)
{
	$options = mso_get_option('plugin_wm_merchant', 'plugins', array());
	if (($product==$options['result'])or($product=='yes')or($product=='no')) return;
	if (!isset($options['purse'])) return;
	if (!isset($options['secret'])) return;
	$CI = & get_instance();
	//$paynum = $CI->db->count_all('wm_orders');
	$query = $CI->db->query('SHOW TABLE STATUS LIKE  \''.$CI->db->dbprefix.'wm_orders\'');
	if ($query->num_rows() > 0)	
	{	
		$row = $query->row_array();
		$paynum = $row['Auto_increment'];
	}
	else return;
	$CI->db->from('wm_products');
	$CI->db->where('id',$product);
	$CI->db->limit(1);
	$query = $CI->db->get();
	$i = 0;	
	if ($query->num_rows() > 0)	
	{	
		$row = $query->row_array();
		mso_head_meta('title', t('Покупка', __FILE__) ); // meta title страницы
		require_once(getinfo('template_dir') . 'main-start.php');
		if (function_exists('rgblog_start')) rgblog_start('Покупка товара');
		else echo '<h1>Покупка товара</h1>';
		echo '<h2>Вы пытаетесь купить следующий товар:</h2>
		<p><strong>Номер товара:</strong> '.$row['id'].'</p>
		<p><strong>Наименование:</strong> '.$row['name'].'</p>
		<p><strong>Цена:</strong> '.$row['price'].' руб.</p>
		<form method="POST" action="https://merchant.webmoney.ru/lmi/payment.asp">
		<input type="hidden" name="LMI_PAYMENT_NO" value="'.($paynum).'">
		<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="'.$row['price'].'">
		<input type="hidden" name="LMI_PAYMENT_DESC_BASE64" value="'.base64_encode($row['name']).'">
		<input type="hidden" name="LMI_PAYEE_PURSE" value="'.$options['purse'].'">
		<input type="hidden" name="id" value="'.$row['id'].'">
		<p>'.t('Укажите email для отправки товара', __FILE__).': </p>
		<p><input type="text" name="email" size="20"></p>
		<p><em>Внимательно вводите адрес электронной почты!</em></p>
		<p><input type="submit" value="'.t('Перейти к оплате', __FILE__).'" style="width: 185px;"></p>
		</form>';
		echo '<p><a href="'.$_SERVER['HTTP_REFERER'].'">Вернуться назад</a></p>';
		require_once(getinfo('template_dir') . 'main-end.php');			
	}
	return;
}

function wm_merchant_b()
{
	$options = mso_get_option('plugin_wm_merchant', 'plugins', array());
	if (!isset($options['secret'])) return;
	if (!isset($options['purse'])) return;
	$options['mail_title'] = isset($options['mail_title']) ? $options['mail_title'] : 'Покупка товара';
	
	if ($post = mso_check_post(array('LMI_PAYMENT_NO')))
	{
		$CI = & get_instance();
		if ($post['LMI_PREREQUEST']==1)
		{
			$CI->db->from('wm_products');
			$CI->db->where('id',$post['id']);
			$CI->db->limit(1);
			$query = $CI->db->get();
			// 1) Проверяем, есть ли товар с таким id в базе данных.
			if ($query->num_rows() == 0)
			{
				echo "Ошибка: Нет такого товара";
				exit;
			}
			$row = $query->row_array();
			// 2) Проверяем, не произошла ли подмена суммы.
			if ($row['price']!=$post['LMI_PAYMENT_AMOUNT'])
			{
				 echo "Ошибка: Неверная сумма ".$post['LMI_PAYMENT_AMOUNT'];
				 exit;
			}
			// 3) Проверяем, не произошла ли подмена кошелька.
			if ($options['purse']!=$post['LMI_PAYEE_PURSE'])
			{
				 echo "Ошибка: Неверный кошелек получателя ".$post['LMI_PAYEE_PURSE'];
				 exit;
			}
			// 4) Проверяем, указал ли пользователь свой email.
			if (!isset($post['email']))
			{				
				 echo "Ошибка: Не указан email";
				 exit;
			}
			// 5) Проверка правильного адреса
			if ($post['email'] and !mso_valid_email($post['email']))
			{
				 echo "Ошибка: Указан неверный email отправителя";
				 exit;
			}
			// Если ошибок не возникло и мы дошли до этого места, то выводим YES
			echo "YES";	
			exit;
		}
		else
		{
			// Склеиваем строку параметров
			$common_string = $post['LMI_PAYEE_PURSE'].$post['LMI_PAYMENT_AMOUNT'].$post['LMI_PAYMENT_NO'].
			 $post['LMI_MODE'].$post['LMI_SYS_INVS_NO'].$post['LMI_SYS_TRANS_NO'].
			 $post['LMI_SYS_TRANS_DATE'].$options['secret'].$post['LMI_PAYER_PURSE'].$post['LMI_PAYER_WM'];
			// Шифруем полученную строку в MD5 и переводим ее в верхний регистр
			$hash = strtoupper(md5($common_string));
			// Прерываем работу скрипта, если контрольные суммы не совпадают
			if ($hash != $post['LMI_HASH']) exit;
			// Выбираем из базы данных нужный товар, записываем его в переменную $tovar;
			// .........................................................................
			// Вносим покупку в таблицу orders
			$CI->db->from('wm_products');
			$CI->db->where('id',$post['id']);
			$CI->db->limit(1);
			$query = $CI->db->get();
			$row = $query->row_array();
			$CI->db->insert('wm_orders',array(
				'id' => $post['LMI_PAYMENT_NO'],
				'item' => $post['id'],
				'date' => $post['LMI_SYS_TRANS_DATE'],
				'purse' => $post['LMI_PAYER_PURSE'],
				'email' => $post['email'],
				'wm_id' => $post['LMI_SYS_TRANS_NO']
			));
			// Отправляем товар на email покупателя		
			if (mso_valid_email($post['email'])) 
			{
				$text = '';
				if (isset($options['mail_do'])) $text .= $options['mail_do'] . NR;
				$text .= $row['text'];
				if (isset($options['mail_posle'])) $text .= NR . $options['mail_posle'];
				$preferences = array();
				$fn = FCPATH . $row['attach'];
				if (file_exists($fn)) $preferences['attach'] = $fn;
				$res = mso_mail($post['email'], $options['mail_title'], $text, false, $preferences);                         
				//if ($res) echo 'Отправлено';
				//else echo 'Ошибка отправки почты на сервере.';			
			}				
		}	
	}
}

function wm_merchant_c()
{
	if ($post = mso_check_post(array('id','email')))
	{
		mso_head_meta('title', t('Покупка', __FILE__) ); // meta title страницы
		require_once(getinfo('template_dir') . 'main-start.php');
		if (function_exists('rgblog_start')) rgblog_start('Покупка товара');
		else echo '<h1>Покупка товара</h1>';
		echo '<h2>Покупка товара успешно произведена.</h2>
		<p>Товар номер '.$post['id'].' выслан на ваш email '.$post['email'].'</p>';
		require_once(getinfo('template_dir') . 'main-end.php');			
	}		
}

function wm_merchant_d()
{
	if ($post = mso_check_post(array('id','email')))
	{
		mso_head_meta('title', t('Покупка', __FILE__) ); // meta title страницы
		require_once(getinfo('template_dir') . 'main-start.php');
		if (function_exists('rgblog_start')) rgblog_start('Покупка товара');
		else echo '<h1>Покупка товара</h1>';
		echo '<h2>Оплата не прошла.</h2>
		<p>Возможно, вы отказались от платежа или возникла другая ошибка</p>';
		require_once(getinfo('template_dir') . 'main-end.php');			
	}		
}

function wm_merchant_content_callback($matches)
{
	static $prefix;
	if (!isset($prefix) or !isset($style)) 
	{
		$options = mso_get_option('plugin_wm_merchant', 'plugins', array());
		if ( !isset($options['prefix']) ) $options['prefix'] = 'pay';
		$prefix = $options['prefix'];
		if ( !isset($options['style']) ) $options['style'] = '';
		$style = $options['style'];
	}
	if (isset($options['style'])) $style = ' style="'.$style.'"';
	$url = $matches[1]; // Номер заказа	
	if (isset($options['image']))
	{
		$out = '<span class="buy"'.$style.'><a href="'.getinfo('siteurl').$prefix.'/'.$url.'"><img src="'.$options['image'].'" title="Купить" alt="Купить"></a></span>';
	}
	else $out = '<span class="buy"'.$style.'><a href="'.getinfo('siteurl').$prefix.'/'.$url.'">Купить</a></span>';
	return $out;
}

# замена ссылок в тексте
function wm_merchant_content($text = '')
{
	// [webmoney]Номер товара[/webmoney]
	$pattern = '|\[webmoney\](\d+)\[/webmoney\]|ui';
	$text = preg_replace_callback($pattern, 'wm_merchant_content_callback', $text);
	return $text;
}

# end file