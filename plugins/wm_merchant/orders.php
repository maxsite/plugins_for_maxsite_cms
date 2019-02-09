<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * RGBlog
 * (c) http://rgblog.ru/
 */

//$path = getinfo('plugins_url') . 'wm_merchant/images/';

$options = mso_get_option('plugin_wm_merchant', 'plugins', array());

?>
<div class="admin-h-menu">
<?php
	# сделаем меню горизонтальное в текущей закладке
	// основной url этого плагина - жестко задается
	$plugin_url = getinfo('site_admin_url') . 'wm_merchant';
	$a  = mso_admin_link_segment_build($plugin_url, '', t('Настройка', __FILE__), 'select') . ' | ';
	$a .= mso_admin_link_segment_build($plugin_url, 'products', t('Товары', __FILE__), 'select');
	$a .= mso_admin_link_segment_build($plugin_url, 'orders', t('Покупки', __FILE__), 'select');
	echo $a;
?>
</div>

<h1><?= t('Каталог покупок', __FILE__) ?></h1>
<p class="info"><?= t('Здесь вы можете сможете просмотреть все совершенные покупки.', __FILE__) ?></p>

<?php

//Построение таблицы
$CI = & get_instance();
$CI->load->library('table');
$tmpl = array (
				'table_open'		  => '<table cellspacing="0" cellpadding="0" width="98%">',
				'row_alt_start'		  => '<tr>',
				'cell_alt_start'	  => '<td>',
				'cell_start'	 	  => '<td>',
	);
$CI->table->set_template($tmpl); // шаблон таблицы
$CI->table->set_heading('Номер', 'ID Товара', 'Дата', 'Кошелек', 'EMail', 'WM_ID'); // заголовки

// теперь получаем сами записи
$CI->db->from('wm_orders');
$CI->db->order_by('date', 'desc');
$query = $CI->db->get();

//Форма ввода данных

$i = 0;	
if ($query->num_rows() > 0)	
{	
	$books = $query->result_array();
	foreach ($books as $book) 
	{
		$CI->table->add_row(
			$book['id'],
			$book['item'],
			$book['date'],
			$book['purse'],
			$book['email'],
			$book['wm_id']
		);
		$i++;
	}
}
$htm = $CI->table->generate();
echo $htm;
echo '<p><a href="https://merchant.webmoney.ru/conf/traninfo.asp" target="_blank">Возврат средств</a> (по wm_id)</p>';
?>