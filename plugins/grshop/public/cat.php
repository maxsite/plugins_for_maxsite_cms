<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_cur_dir_lang('templates');
global $MSO;
require_once ($MSO->config['plugins_dir'].'grshop/common/common.php');	//подгружаем библиотеку c ф-циями вывода
require_once ($MSO->config['plugins_dir'].'grshop/config.php');	// подгружаем переменные
$grsh_options = mso_get_option($grsh['main_key_options'], 'plugins', array()); // получение опций

$out = '';
$pagination = false;

if ($post = mso_check_post(array('f_session_id', 'addbasket')))
	{
	mso_checkreferer();
	$res = addbasket($post);	//-ф-ция добляет в корзину из коммона	
	};
if (mso_segment(2) == '')		require_once ($MSO->config['plugins_dir'].'grshop/public/frontpage.php');//главная страница каталога
elseif (mso_segment(2) == 'cat') 	require_once ($MSO->config['plugins_dir'].'grshop/public/catalog.php');	// каталог
elseif (mso_segment(2) == 'bas')	require_once ($MSO->config['plugins_dir'].'grshop/public/basket.php');	// корзина
elseif (mso_segment(2) == 'prod')	require_once ($MSO->config['plugins_dir'].'grshop/public/product.php');	// один товар
else 				require_once ($MSO->config['plugins_dir'].'grshop/public/frontpage.php');//главная страница каталога

//---- собственно вывод---------
//require(getinfo('template_dir') . 'main-start.php');	// начальная часть шаблона
if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);
echo $out;
mso_hook('pagination', $pagination);
echo '<br>';	
//require(getinfo('template_dir') . 'main-end.php');		//# конечная часть шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);
	
?>