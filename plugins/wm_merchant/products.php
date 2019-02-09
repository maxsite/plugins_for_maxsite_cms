<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * RGBlog
 * (c) http://rgblog.ru/
 */

$CI = & get_instance();
$path = getinfo('plugins_url') . 'wm_merchant/images/';
if (mso_segment(4)=='newstr')
{
	if ( $post = mso_check_post(array('id', 'name')) )
	{
		mso_checkreferer();
		$CI->db->where('id',$post['id']);
		$data = array();
		$data['name'] = $post['name'];
		$data['price'] = $post['price'];
		$data['attach'] = $post['attach'];		
		$data['text'] = $post['text'];		
		$CI->db->update('wm_products',$data);
	}
	exit;
}
elseif (mso_segment(4)=='delstr')
{
	if ( $post = mso_check_post(array('id')) )
	{
		mso_checkreferer();
		$CI->db->delete('wm_products',array('id' => $post['id']));
	}
	exit;
}

$options = mso_get_option('plugin_wm_merchant', 'plugins', array());

//Функция добавления матча
if ( $post = mso_check_post(array('f_session_id', 'a_submit')) )
{
	mso_checkreferer();
	if ($post['name']<>'')
		$CI->db->insert('wm_products',array(
		'name' => $post['name'],
		'price' => $post['price'],
		'attach' => $post['attach'],
		'text' => $post['text']
	));
	echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
}
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

<h1><?= t('Каталог товаров', __FILE__) ?></h1>
<p class="info"><?= t('Здесь вы можете редактировать список товаров. Добавлять товары и изменять старые.', __FILE__) ?></p>

<?php

//Построение таблицы
$CI = & get_instance();
$CI->load->library('table');
$tmpl = array (
				'table_open'		  => '<table cellspacing="0" cellpadding="0">',
				'row_alt_start'		  => '<tr>',
				'cell_alt_start'	  => '<td>',
				'cell_start'	 	  => '<td>',
	);
$CI->table->set_template($tmpl); // шаблон таблицы
$CI->table->set_heading('Номер', 'Наименование', 'Цена', 'Ссылка', 'Текст',''); // заголовки

// теперь получаем сами записи
$CI->db->from('wm_products');
//$CI->db->order_by('name', 'asc');
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
			'<input type="text" value="'.$book['name'].'" name="n'.$book['id'].'" style="width: 200px";">',
			'<input type="text" value="'.$book['price'].'" name="p'.$book['id'].'" style="width: 50px;">',
			'<input type="text" value="'.$book['attach'].'" name="a'.$book['id'].'" style="width: 200px;">',
			'<textarea name="t'.$book['id'].'" style="width: 200px;">'.$book['text'].'</textarea>',
			'<span id="ref'.$book['id'].'"  onclick="javascript:newstr('.$book['id'].')"><img src="'.$path.'refresh.png" style="cursor: pointer;" title="Обновить"></span> <span onclick="javascript:delstr('.$book['id'].')"><img src="'.$path.'delete.png" style="cursor: pointer;" title="Удалить"></span>'
		);
		$i++;
	}
}
$htm = $CI->table->generate();
echo $htm;

echo '<form name="three" method="post">'.mso_form_session('f_session_id').'<table style="width: 600px;"><tr><th colspan=2>Добавить товар</th></tr>';
echo '<tr><td style="width: 150px">Название товара:</td><td style="width: 450px;"></><input type="text" value="" name="name" style="width: 98%;"></td></tr>';
echo '<tr><td>Стоимость (руб):</td><td><input type="text" value="" name="price" style="width: 98%;"></td></tr>';
echo '<tr><td>Прикрепить файл:</td><td><input type="text" value="" name="attach" style="width: 98%;"></td></tr>';
echo '<tr><td>Текст письма:</td><td><textarea name="text" rows="8" style="width: 98%;"></textarea></td></tr>';
echo '<tr><td colspan=2><input type="submit" name="a_submit" value="' . t('Добавить товар', __FILE__) . '"></td></tr></table></form>';

echo '
<script language="JavaScript">
<!--
function newstr(newid) {
newname = $("input[name=n"+newid+"]").attr("value");
newprice = $("input[name=p"+newid+"]").attr("value");
newattach = $("input[name=a"+newid+"]").attr("value");
newtext = $("textarea[name=t"+newid+"]").val();
$.post("products/newstr", { id: newid, name: newname, price: newprice, attach: newattach, text: newtext },
function(){
		$("#ref"+newid).html(\'<img src="'.$path.'accept.png">\');
		setTimeout(function(){
			$("#ref"+newid).html(\'<img src="'.$path.'refresh.png">\');
		},5000);
	});
}
function delstr(newid) {
	$.post("products/delstr", { id: newid },
	function(){
		$("#ref"+newid).parent().parent("tr").fadeOut();
	});
}
-->
</script>';

?>