<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_cur_dir_lang('admin');

?>
<div class="admin-h-menu">
<?php
	# сделаем меню горизонтальное в текущей закладке
	
	// основной url этого плагина - жестко задается
	$plugin_url = $MSO->config['site_admin_url'] . 'grshop';
	
	// само меню
	$a = mso_admin_link_segment_build($plugin_url, 'ord', t('Заказы', 'admin'), 'select'). ' | ';
	$a .= mso_admin_link_segment_build($plugin_url, 'export', t('Экспорт', 'admin'), 'select'). ' | ';
	$a .= mso_admin_link_segment_build($plugin_url, 'category', t('Категории товаров', 'admin'), 'select'). ' | ';
	$a .= mso_admin_link_segment_build($plugin_url, 'product', t('Товары', 'admin'), 'select'). ' | ';
	$a .= mso_admin_link_segment_build($plugin_url, 'actions', t('Акции', 'admin'), 'select'). ' | ';
	$a .= mso_admin_link_segment_build($plugin_url, 'general', t('Общие настройки', 'admin'), 'select'). ' | ';
	$a = mso_hook('plugin_admin_options_menu', $a);
	
	echo $a;
?>
</div>

<?php
// Определим текущую страницу (на основе сегмента url)
$seg = mso_segment(3);

// подключаем соответственно нужный файл
if ($seg == '' || $seg == 'ord') require($MSO->config['plugins_dir'] . 'grshop/admin/admin_orders.php');
	elseif ($seg == 'general') require($MSO->config['plugins_dir'] . 'grshop/admin/admin_general.php');
	elseif ($seg == 'category') require($MSO->config['plugins_dir'] . 'grshop/admin/admin_category.php');
	elseif ($seg == 'product') require($MSO->config['plugins_dir'] . 'grshop/admin/admin_product.php');
	elseif ($seg == 'actions') require($MSO->config['plugins_dir'] . 'grshop/admin/admin_actions.php');
//	elseif ($seg == 'ord') require($MSO->config['plugins_dir'] . 'grshop/admin/admin_orders.php');
	elseif ($seg == 'export') require($MSO->config['plugins_dir'] . 'grshop/admin/admin_export.php');
//	elseif ($seg == 'page_type') require($MSO->config['admin_plugins_dir'] . 'admin_options/page-type.php');

?>