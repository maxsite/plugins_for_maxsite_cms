<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

//---- устанавливаем значения переменных -------------
//---- имена параметров ---------
global $grsh;
global $nmfpdb;

$grsh['main_key_options'] = 'grshop'; 	//--- ключ для работы с опциями для всего плагина
$grsh['uploads_pict_dir'] = 'grshop';	//--- папка для загрузки картинок
//$grsh['shapka_admin_general'] = t('Общие настройки плагина GrShop', 'plugins/grshop');
//$grsh['']


//----- перевод названия полей из базы данных в человеческие названия -----
$nmfpdb['articul_prod'] = t('арт.', 'plugins/grshop');
$nmfpdb['cost_prod'] = t('цена', 'plugins/grshop');
$nmfpdb['description_prod'] = t('описание', 'plugins/grshop');
$nmfpdb['id_sklad_prod'] = t('скл. №', 'plugins/grshop');
$nmfpdb['name_prod'] = t('название', 'plugins/grshop');
$nmfpdb['id_prod'] = t('id', 'plugins/grshop');
$nmfpdb['cur_cost'] = t('цена вых.', 'plugins/grshop');
$nmfpdb['quantity_prod'] = t('осталось', 'plugins/grshop');
$nmfpdb['quantity_prodord'] = t('кол-во', 'plugins/grshop');
$nmfpdb['reserve_prod'] = t('резерв', 'plugins/grshop');


?>