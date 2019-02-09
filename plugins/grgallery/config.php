<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

//---- устанавливаем значения переменных -------------
//---- имена параметров ---------
global $grgll;
//global $nmfpdb;

$grgll['main_key_options'] = 'grgallery'; 	//--- ключ для работы с опциями и с кэшем для всего плагина
$grgll['uploads_pict_dir'] = 'grgll';		//--- папка для загрузки картинок
$grgll['prefix'] = 'gr'; 					//--- для формирования имени загрузочных папок для каждой страницы
$grgll['use_upload_id_dir'] = '1'; 	//--- по умолчанию при установке используем для каждой записи отдельную папку для загрузки
$grgll['q_col_list'] = '4'; 		//--- по умолчанию количество картинок-ссылок на главной и в категориях
$grgll['view_all_tags'] = '0';		//--- по умолчанию отображать на странице неактивные для страницы тэги (услуги)
$grgll['view_groups_page'] = '1';		//--- по умолчанию отображать на странице услуги с разбивкой по группам


//----- перевод названия полей из базы данных в человеческие названия -----
//$nmfpdb['articul_prod'] = t('арт.', 'plugins/grshop');
//$nmfpdb['cost_prod'] = t('цена', 'plugins/grshop');
//$nmfpdb['description_prod'] = t('описание', 'plugins/grshop');
//$nmfpdb['id_sklad_prod'] = t('скл. №', 'plugins/grshop');
//$nmfpdb['name_prod'] = t('название', 'plugins/grshop');
//$nmfpdb['id_prod'] = t('id', 'plugins/grshop');
//$nmfpdb['cur_cost'] = t('цена вых.', 'plugins/grshop');
//$nmfpdb['quantity_prod'] = t('осталось', 'plugins/grshop');
//$nmfpdb['quantity_prodord'] = t('кол-во', 'plugins/grshop');
//$nmfpdb['reserve_prod'] = t('резерв', 'plugins/grshop');


?>