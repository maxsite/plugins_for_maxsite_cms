<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function grshop_autoload($args = array())
{
	mso_create_allow('grshop_edit', t('Админ-доступ к редактированию GrShop', 'plugins/grshop')); # добавляем в раздачу прав
	mso_hook_add( 'admin_init', 'grshop_admin_init'); # хук на админку
	mso_hook_add( 'admin_head', 'grshop_add_head'); # хук на включение своего css-файла в админку	
	mso_hook_add( 'head', 'grshop_add_head'); # хук на включение своего css-файла
	mso_hook_add( 'content', 'grshop_content'); # хук на обработку текста [grshop]
	mso_hook_add('custom_page_404', 'grshop_catalog_page_404');
	mso_register_widget('grshop_basket_widget',  t('Вывод корзины GrShop', 'plugins/grshop')); # регистрируем виджет, он появляется в списке распределения по сайдбарам
	mso_register_widget('grshop_catalog_widget',  t('Вывод каталога GrShop', 'plugins/grshop')); # регистрируем виджет, он появляется в списке распределения по сайдбарам
}

# функция выполняется при активации (вкл) плагина
function grshop_activate($args = array())
{
	global $MSO;
	# создаем таблицы
	$CI = & get_instance(); // получаем доступ к CodeIgniter
	$CI->load->dbforge() ; // подгружаем библиотеку фордж, для создания таблиц в базе данных
	require_once ($MSO->config['plugins_dir'].'grshop/config.php');	// подгружаем переменные

	$dbprefix = $CI->db->dbprefix;
	$list_tables = $CI->db->list_tables();
	//pr($list_tables);

	# создаем таблицу категорий
	if (!in_array($dbprefix.'grsh_cat', $list_tables))
		{
		//echo 'Будем создавать таблицу категорий';
		$fields_cat = array(
			'id_cat' => array('type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ),
			'id_parent_cat' => array('type' => 'BIGINT', 'constraint' => 20,  'unsigned' => TRUE, 'default' => '0'),		
			'name_cat' => array('type' => 'VARCHAR', 'constraint' => '255'),
			'slug_cat' => array('type' => 'TEXT', 'null' => TRUE),		
			'descr_cat' => array('type' => 'LONGTEXT', 'null' => TRUE),
			//'public_status_cat' => array('type' => 'enum', 'constraint' => '"0","1"', 'default' => '1'),
			'public_status_cat' => array('type' => 'ENUM("0","1")', 'default' => '1'),
			'menu_order_cat' => array('type' => 'TEXT', 'null' => TRUE),
				);
		$CI->dbforge->add_field($fields_cat);
		$CI->dbforge->add_key('id_cat', TRUE);  //определяем ключевое поле	
		$CI->dbforge->create_table('grsh_cat', TRUE);   //создаем таблицу категорий
		Unset($fields_cat);
		# сразу создаем категорию для главной страницы каталога
		$mpcat = array	(
				'name_cat' => t('Главная страница каталога', 'plugins/grshop'),
 				'id_parent_cat' => '' ,
				'slug_cat' => 'cover',
				'descr_cat' => t('Главная страница каталога', 'plugins/grshop'),
				'public_status_cat' => '',
				'menu_order_cat' => '',
				);
		$CI->db->insert('grsh_cat', $mpcat);
		$id_fp_cat = $CI->db->insert_id();	//-- id категории главной стр. по умолчанию--
		Unset($mpcat);
		}
	//exit('Пререываем скрипт');
	# создаем таблицу товаров
	if (!in_array($dbprefix.'grsh_prod', $list_tables))
		{
		$fields_prod = array(
			'id_prod' => array('type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ),
			'id_sklad_prod' => array('type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'default' => '0' ),		
			'articul_prod' => array('type' => 'VARCHAR', 'constraint' => '255',),
			'name_prod' => array('type' => 'VARCHAR', 'constraint' => '255'),		
			'cost_prod' => array('type' =>'FLOAT', 'unsigned' => TRUE, 'length' => '10', 'decimals' => '2', 'default' => '00.00',),
			//'public_status_prod' => array('type' => 'enum', 'constraint' => '"0","1"', 'default' => "1"),
			'public_status_prod' => array('type' => 'ENUM("0","1")', 'default' => "1"),
			'description_prod' => array('type' => 'LONGTEXT', 'null' => TRUE,),
			'quantity_prod' => array('type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'default' => '1' ),
			'reserve_prod' => array('type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'default' => '0' ),
			'photo_prod' => array('type' => 'TEXT', 'null' => TRUE),			
				);
		$CI->dbforge->add_field($fields_prod);
		$CI->dbforge->add_key('id_prod', TRUE);  //определяем ключевое поле
		$CI->dbforge->create_table('grsh_prod', TRUE);   //создаем таблицу товаров
		Unset($fields_prod);
		}

	# создаем таблицу распределения товаров по категориям
	if (!in_array($dbprefix.'grsh_catprod', $list_tables))
		{
		$fields_catprod = array(
			'id_catprod' => array('type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ),
			'id_cat' => array('type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'default' => '0' ),	
			'id_prod' => array('type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'default' => '0' ),				
				);
		$CI->dbforge->add_field($fields_catprod);
		$CI->dbforge->add_key('id_catprod', TRUE);  //определяем ключевое поле
		$CI->dbforge->create_table('grsh_catprod', TRUE);   //создаем таблицу товаров в категориях
		Unset($fields_catprod);
		}

	# создаем таблицу названий добавочных свойств товаров
	if (!in_array($dbprefix.'grsh_add', $list_tables))
		{
		$fields_add = array(
			'id_add' => array('type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ),
			'name_add' => array('type' => 'VARCHAR', 'constraint' => '255'),				
				);
		$CI->dbforge->add_field($fields_add);
		$CI->dbforge->add_key('id_add', TRUE);  //определяем ключевое поле
		$CI->dbforge->create_table('grsh_add', TRUE);   //создаем таблицу товаров в категориях
		Unset($fields_add);	
		}

	# создаем таблицу соответствия свойств товаров товарам
	if (!in_array($dbprefix.'grsh_prodadd', $list_tables))
		{
		$fields_prodadd = array(
			'id_prodadd' => array('type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ),
			'id_add' => array('type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'default' => '0' ),	
			'id_prod' => array('type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'default' => '0' ),
			'val_prodadd' => array('type' => 'VARCHAR', 'constraint' => '255')			
				);
		$CI->dbforge->add_field($fields_prodadd);
		$CI->dbforge->add_key('id_prodadd', TRUE);  //определяем ключевое поле
		$CI->dbforge->create_table('grsh_prodadd', TRUE);   //создаем таблицу товаров в категориях
		Unset($fields_prodadd);
		}

	# создаем таблицу акций
	if (!in_array($dbprefix.'grsh_act', $list_tables))
		{
		$fields_act = array(
			'id_act' => array('type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ),
			'name_act' => array('type' => 'VARCHAR', 'constraint' => '255',),
			'description_act' => array('type' => 'LONGTEXT', 'null' => TRUE),
			'discount_act' => array('type' =>'FLOAT', 'unsigned' => TRUE, 'length' => '3', 'decimals' => '2', 'default' => '00.00',),	
			//'public_status_act' => array('type' => 'enum', 'constraint' => '"0","1"', 'default' => '0'),
			'public_status_act' => array('type' => 'ENUM("0","1")', 'default' => '0'),
			//'other_discount_act' => array('type' => 'enum', 'constraint' => '"0","1","2"', 'default' => '0'),
			'other_discount_act' => array('type' => 'ENUM("0","1","2")', 'default' => '0'),
			//'all_user_act' => array('type' => 'enum', 'constraint' => '"0","1"', 'default' => '0'),
			'all_user_act' => array('type' => 'ENUM("0","1")', 'default' => '0'),
			'start_data_act' => array('type' => 'DATETIME', 'default' => '2009-06-13 00:00:19'),
			'end_data_act' => array('type' => 'DATETIME', 'default' => '2036-12-31 23:59:59'),		
				);
		$CI->dbforge->add_field($fields_act);
		$CI->dbforge->add_key('id_act', TRUE);  //определяем ключевое поле
		$CI->dbforge->create_table('grsh_act', TRUE);   //создаем таблицу товаров
		Unset($fields_act);
		}

	# создаем таблицу действия акций на категории
	if (!in_array($dbprefix.'grsh_catact', $list_tables))
		{
		$fields_catact = array(
			'id_catact' => array('type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ),
			'id_cat' => array('type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'default' => '0' ),	
			'id_act' => array('type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'default' => '0' ),				
				);
		$CI->dbforge->add_field($fields_catact);
		$CI->dbforge->add_key('id_catact', TRUE);  //определяем ключевое поле
		$CI->dbforge->create_table('grsh_catact', TRUE);   //создаем таблицу товаров в категориях
		Unset($fields_catact);
		}

	# создаем таблицу заказов
	if (!in_array($dbprefix.'grsh_ord', $list_tables))
		{
		$fields_ord = array(
			'id_ord' => array('type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ),
			'start_data_order' => array('type' => 'DATETIME', 'default' => '2009-06-13 00:00:19'),
			//'status_order' => array('type' => 'enum', 'constraint' => '"0","1","2","3","4"', 'default' => '0'),
			'status_order' => array('type' => 'ENUM("0","1","2","3","4")', 'default' => '0'),
			//'status_pay_order' => array('type' => 'enum', 'constraint' => '"0","1","2"', 'default' => '0'),
			'status_pay_order' => array('type' => 'ENUM("0","1","2")', 'default' => '0'),
			'id_client_order' => array('type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'default' => '0' ),
			'email_order' => array('type' =>'TEXT'),
			'telephon_order' => array('type' => 'VARCHAR', 'constraint' => '255'),
			'adress_order' => array('type' => 'TEXT', 'null' => TRUE),
			'person_order' => array('type' => 'TEXT', 'null' => TRUE),
			'description_order' => array('type' => 'LONGTEXT', 'null' => TRUE,),
				);
		$CI->dbforge->add_field($fields_ord);
		$CI->dbforge->add_key('id_ord', TRUE);  //определяем ключевое поле
		$CI->dbforge->create_table('grsh_ord', TRUE);   //создаем таблицу заказов
		Unset($fields_ord);
		}

	# создаем таблицу товаров в заказах
	if (!in_array($dbprefix.'grsh_ordprod', $list_tables))
		{
		$fields_ordprod = array	(
			'id_ordprod' => array('type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ),
			'id_ord' => array('type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'default' => '0' ),
			'id_prod' => array('type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'default' => '0' ),
			'cur_cost' => array('type' =>'FLOAT', 'unsigned' => TRUE, 'length' => '10', 'decimals' => '2', 'default' => '00.00'),
			'quantity_prodord' => array('type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'default' => '1' ),
					);
		$CI->dbforge->add_field($fields_ordprod);
		$CI->dbforge->add_key('id_ordprod', TRUE);  //определяем ключевое поле
		$CI->dbforge->create_table('grsh_ordprod', TRUE);   //создаем таблицу товаров в категориях
		Unset($fields_ordprod);
		}

	# создаем папку для загрузки картинок
	$new_dir = getinfo('uploads_dir').$grsh['uploads_pict_dir'];
			if ( !is_dir($new_dir) ) // уже есть
			{
				@mkdir($new_dir, 0777); // нет каталога, пробуем создать
				@mkdir($new_dir . '/_mso_i', 0777); // нет каталога, пробуем создать
				@mkdir($new_dir . '/mini', 0777); // нет каталога, пробуем создать
				@mkdir($new_dir . '/arh', 0777); // нет каталога, пробуем создать
			}

	# устанавливаем опции по умолчанию
	$email_notice =t('
При оформлении заказа на сайте [urlname]

Вы указали следующие контактные данные

e-mail: [email]
Телефон: [tel]
Адрес: [adress]
Контактное лицо: [person]
Дополнительно: [description]

Ваша заявка принята к исполнению

Номер Вашей заявки: [num_ord]

[checklist]

ИТОГО: [price]

По всем вопросам можете обратиться к менеджеру
по телефону: +7 351 721-42-28

БЛАГОДАРИМ ЗА ЗАКАЗ
', 'plugins/grshop');


	if ( !isset($grsh_options['main_slug'])) $grsh_options['main_slug'] = 'catalog';
	if ( !isset($grsh_options['main_title'])) $grsh_options['main_title'] = '[product][category]GrShop';
	if ( !isset($grsh_options['money'])) $grsh_options['money'] = t('руб', 'plugins/grshop');
	if ( !isset($grsh_options['mode'])) $grsh_options['mode'] = 'shop';
	if ( !isset($grsh_options['id_fp_cat'])) $grsh_options['id_fp_cat'] = $id_fp_cat; 	//-- id клавн. стр. по умолчанию---
	if ( !isset($grsh_options['email'])) $grsh_options['email'] = mso_get_option('admin_email', 'general');
	if ( !isset($grsh_options['email_notice'])) $grsh_options['email_notice'] = $email_notice;

	if ( !isset($grsh_options['tip_out_prod'])) $grsh_options['tip_out_prod'] = 'table';
	if ( !isset($grsh_options['pag_limit_prod_list'])) $grsh_options['pag_limit_prod_list'] = 20;
	if ( !isset($grsh_options['pag_limit_prod_table'])) $grsh_options['pag_limit_prod_table'] = 25;

	if ( !isset($grsh_options['echo_articul_prod_list'])) $grsh_options['echo_articul_prod_list'] = 1;
	if ( !isset($grsh_options['echo_name_prod_list'])) $grsh_options['echo_name_prod_list'] = 1;
	if ( !isset($grsh_options['echo_cost_prod_list'])) $grsh_options['echo_cost_prod_list'] = 1;
	if ( !isset($grsh_options['echo_descr_prod_list'])) $grsh_options['echo_descr_prod_list'] = 1;
	if ( !isset($grsh_options['echo_id_sklad_prod_list'])) $grsh_options['echo_id_sklad_prod_list'] = 1;
	if ( !isset($grsh_options['echo_photo_prod_list'])) $grsh_options['echo_photo_prod_list'] = 0;

	if ( !isset($grsh_options['echo_articul_prod_table'])) $grsh_options['echo_articul_prod_table'] = 1;
	if ( !isset($grsh_options['echo_name_prod_table'])) $grsh_options['echo_name_prod_table'] = 1;
	if ( !isset($grsh_options['echo_cost_prod_table'])) $grsh_options['echo_cost_prod_table'] = 1;
	if ( !isset($grsh_options['echo_descr_prod_table'])) $grsh_options['echo_descr_prod_table'] = 1;
	if ( !isset($grsh_options['echo_id_sklad_prod_table'])) $grsh_options['echo_id_sklad_prod_table'] = 1;
	if ( !isset($grsh_options['echo_add_prod_table'])) $grsh_options['echo_add_prod_table'] = 1;	

	require_once ($MSO->config['plugins_dir'].'grshop/config.php');	// подгружаем переменные
	$grsh_options = mso_add_option($grsh['main_key_options'], $grsh_options, 'plugins'); // сохранение опций

	return $args;
}


# функция выполняется при деинсталяции плагина
function grshop_uninstall($args = array())
{	
	global $MSO;

	#удаляем запись в раздаче прав
	mso_remove_allow('grshop_edit');

	# удаляем таблицы каталога: таблицу категорий, и таблицу товаров
	$CI = & get_instance(); // вот здесь мы и получаем доступ к CodeIgniter
	$CI->load->dbforge() ; // подгружаем библиотеку фордж, для создания таблиц
	$CI->dbforge->drop_table('grsh_prod', TRUE); //удаляем таблицу товаров
	$CI->dbforge->drop_table('grsh_add', TRUE); //удаляем таблицу свойств товаров
	$CI->dbforge->drop_table('grsh_prodadd', TRUE); //удаляем таблицу соответствия свойств товаров товарам
	$CI->dbforge->drop_table('grsh_cat', TRUE); //удаляем таблицу категорий товаров
	$CI->dbforge->drop_table('grsh_catprod', TRUE); //удаляем таблицу распределения товаров по категориям
	$CI->dbforge->drop_table('grsh_act', TRUE); //удаляем таблицу акций
	$CI->dbforge->drop_table('grsh_catact', TRUE); //действия акций на категории
	$CI->dbforge->drop_table('grsh_ord', TRUE); //удаляем таблицу заказов
	$CI->dbforge->drop_table('grsh_ordprod', TRUE); //удаляем таблицу товаров в заказах
	mso_delete_option_mask('grshop_basket_widget_', 'plugins'); // удалим созданные опции
	mso_delete_option_mask('grshop_catalog_widget_', 'plugins'); // удалим созданные опции

	require_once ($MSO->config['plugins_dir'].'grshop/config.php');	// подгружаем переменные
	mso_delete_option_mask($grsh['main_key_options'], 'plugins'); // удаляем опции плагина

	return $args;
}

# функция выполняется при указаном хуке admin_init
function grshop_admin_init($args = array()) 
{
	if ( mso_check_allow('grshop_edit') ) 
	{
		$this_plugin_url = 'grshop'; // url и hook
		# добавляем свой пункт в меню админки
		# первый параметр - группа в меню
		# второй - это действие/адрес в url - http://сайт/admin/demo
		# можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
		# Третий - название ссылки	
		
		mso_admin_menu_add('plugins', $this_plugin_url, 'GrShop');

		# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
		# связанную функцию именно она будет вызываться, когда 
		# будет идти обращение по адресу http://сайт/admin/grshop
		mso_admin_url_hook ($this_plugin_url, 'grshop_admin_page');

	}
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
# ф-ция общих настроек плагина
function grshop_admin_page($args = array()) 
{
	global $MSO;
	if ( !mso_check_allow('grshop_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins/grshop');
		return $args;
	}
	# выносим админские функции отдельно в файл
	//mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('GrShop', 'plugins/grshop') . '"; ' );
	//mso_hook_add_dinamic( 'admin_title', ' return "' . t('GrShop', 'plugins/grshop') . ' - " . $args; ' );

	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('GrShop', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('GrShop', __FILE__) . ' - " . $args; ' );

	require($MSO->config['plugins_dir'] . 'grshop/admin/submenu.php');
}

# функция выполняется при указаном хуке head
# добавляет линк на css файл плагина
function grshop_add_head()
{
	$addhead = "\n".'<script type="text/javascript" src="'.getinfo('plugins_url') . 'grshop/tablesort.js'.'"></script>';
	$addhead .= '<link rel="stylesheet" href="'.getinfo('plugins_url').'grshop/grshop.css" type="text/css" media="screen">'.NR;
	echo $addhead;
}

#####______функции вывода каталога__________________######
# функция выполняется при указаном хуке content
# обработка текста на предмет в нем метки[grshop]
function grshop_content($text = '')
{
	if (strpos($text, '[grshop]') === false) // нет в тексте
	{
		return $text;
	}
	else 
	{
		return str_replace('[grshop]', grshop_catalog(), $text);
	}
}

# явный вызов функции - отдается товарный каталог
function grshop_catalog($arg = array())
{
	global $MSO;

	// кэш строим по url, потому что у он меняется от пагинации
	$cache_key = 'grshop' . serialize($MSO->data['uri_segment']);
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	$out = '';
	
	$out = 'тестовая заглушка, потом будет каталог тут';	
	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;
}


function grshop_catalog_page_404 ($args = false)
	{
	global $MSO;
	require($MSO->config['plugins_dir'].'grshop/config.php');	// подгружаем переменные
	$grsh_options = mso_get_option($grsh['main_key_options'], 'plugins', array()); // получение опций
	if ( !isset($grsh_options['main_slug']) ) $grsh_options['main_slug'] = 'catalog';

	if ( mso_segment(1)==$grsh_options['main_slug']) 
		{
		// подключили свой файл вывода
		require( getinfo('plugins_dir') . 'grshop/public/cat.php' ); 
		return true; // выходим с true
		}
		return $args;
	}



#####_______функции виджета (виджетов)_______________######

#_________________функции виджета корзины___________________#
# оболочечная функция, которая берет настройки из опций виджетов
# эта ф-ция будет выполняться в сайдбаре !!!.
function grshop_basket_widget($num = 1) 
{
	global $MSO;
	require_once ($MSO->config['plugins_dir'].'grshop/public/vj_basket.php');	// подгружаем библиотеку для админки
	return basket_widget($num);
}


# форма настройки виджета 
# имя функции = виджет_form
function grshop_basket_widget_form($num = 1) 
{
	global $MSO;
	require_once ($MSO->config['plugins_dir'].'grshop/public/vj_basket.php');	// подгружаем библиотеку для админки
	return basket_widget_form($num);
}


# сюда приходят POST из формы настройки виджета
# в этой ф-ции обновление опций
# имя функции = виджет_update
function grshop_basket_widget_update($num = 1) 
{
	global $MSO;
	require_once ($MSO->config['plugins_dir'].'grshop/public/vj_basket.php');	// подгружаем библиотеку для админки
	return basket_widget_update($num);
}


#----- ф-ции виджета каталога -----------------------#
function grshop_catalog_widget($num = 1) 
{
	global $MSO;
	require_once ($MSO->config['plugins_dir'].'grshop/public/vj_catalog.php');	// подгружаем библиотеку для админки
	return catalog_widget($num);
}

# форма настройки виджета 
# имя функции = виджет_form
function grshop_catalog_widget_form($num = 1) 
{
	global $MSO;
	require_once ($MSO->config['plugins_dir'].'grshop/public/vj_catalog.php');	// подгружаем библиотеку для админки
	return catalog_widget_form($num);
}


# сюда приходят POST из формы настройки виджета
# в этой ф-ции обновление опций
# имя функции = виджет_update
function grshop_catalog_widget_update($num = 1) 
{
	global $MSO;
	require_once ($MSO->config['plugins_dir'].'grshop/public/vj_catalog.php');	// подгружаем библиотеку для админки
	return catalog_widget_update($num);
}

?>