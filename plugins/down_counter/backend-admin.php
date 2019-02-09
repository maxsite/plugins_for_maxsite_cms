<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Plugin «Down Counter» for MaxSite CMS
 * 
 * Author: (c) Илья Земсков (ака Профессор)
 * Plugin URL: http://maxhub.ru/page/plugin-down-counter
 */

require( getinfo('plugins_dir').basename(dirname(__FILE__)).'/backend-menu.php' ); # подключаем файл вывода меню

if( mso_segment(3) == 'edit' )  # редактируем базу статистики
{
	if ( $post = mso_check_post(array('f_session_id', 'f_data')) )
	{
		mso_checkreferer();

		$data = down_counter_get_data( explode(NR, mso_clean_str( $post['f_data'] )) ); # делаем разбор в массив, чтобы проверить формат
			
		down_counter_save_data($data); # сохраняем данные

		echo '<div class="update">' . t('Данные сохранены!') . '</div>';
	}
		
	$data = down_counter_get_data(); $out = ''; #pr($data);
		
	foreach( $data as $url => $aaa )
	{
		$url = down_counter_get_url(mso_xss_clean($url));
		if( $url )
		{
			$out .= $url.' || '.$aaa['count'].( isset($aaa['desc']) ? ' || '.$aaa['desc'] : '' ).NR;
		}
	}

	echo '<h1>' . t('Счетчик переходов') . ' - Правка данных</h1>';
	echo '<p class="info">'.t("На этой странице можно отредактировать текущую статистику обращения к ссылкам.").'</p>';
		
	$form = '';
	$form .= '<form class="fform" method="post">' . mso_form_session('f_session_id');
		$form .= '<p class="hr"><span class="fheader">Содержимое файла '.getinfo('uploads_dir').$options['file'].': </span></p><p><span><textarea name="f_data" rows="15">' . $out . '</textarea></span></p>';
		$form .= '<p class="nop"><span class="fhint">Каждая ссылка хранится в отдельной строке. Формат строки: <code>исходный url || количество обращений || описание</code>. Описания не должны содержать переносов строки, но могут содержать html.</span></p>';
		$form .= '<button type="submit" name="f_submit" class="i save">' . t('Сохранить изменения') . '</button>';
	$form .= '</form>';
		
	echo $form;
}
else # просто выводим статистику
{
?>
<h1><?= t('Счетчик переходов') ?> - Статистика</h1>
<p class="info"><?= t('На этой странице можно увидеть текущую статистику обращения к ссылкам.') ?></p>
<?
	echo down_counter_stat_tbl_generate();

	echo mso_load_jquery('jquery.tablesorter.js') . '
	<script>
	$(function() {
		$("table.tablesorter").tablesorter();
	});
	</script>';				
}