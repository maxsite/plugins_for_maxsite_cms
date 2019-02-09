<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Plugin «SapeTextTool» for maxSite CMS
 * 
 * Author: (c) Илья Земсков (ака Профессор)
 * Plugin URL: http://vizr.ru/page/plugin-sapetexttool
 */

	$CI = & get_instance();
		
?>
<h1>SapeTextTool</h1>
<p class="info">На данной странице вы можете сформировать список текстов или урлов для пакетной загрузки в SAPE или любой другой сервис. Введите шаблон для формирования строк и нажмите кнопку «Генерировать». Подсказка по доступным псевдокодам приведена после формы.</p>
<hr>
<form class="fform" method="post">
<p class="">
<strong>Автор:</strong>
<select id="page_author" name="page_author">
<option selected="selected" value="0">Любой</option>
<?=get_authors();?>
</select>
<strong>Тип страницы:</strong>
<select id="page_type" name="page_type">
<option selected="selected" value="0">Любой</option>
<?=get_types();?>
</select>
<strong>Статус страницы:</strong>
<select id="page_status" name="page_status">
<option selected="selected" value="">Любой</option>
<option value="publish">publish</option>
<option value="draft">draft</option>
<option value="private">private</option>
</select>
<strong>Рубрика:</strong>
<select id="page_category" name="page_category">
<option selected="selected" value="0">Любая</option>
<?=get_categories();?>
</select>
<strong>Тэг:</strong> <input id="page_tag" class="" type="text" value="" name="page_tag">
</p>
<p class="get_range">
<input id="get_range" type="checkbox" name="get_range" value="0"><strong>Выборка по page_id:</strong>
<input id="page_id_begin" type="text" value="начать с id (>=)" name="page_id_begin">
<input id="page_id_end" type="text" value="закончить на id (<=)" name="page_id_end">
</p>
<p class="get_range">
<input id="get_list" type="checkbox" name="get_list" value="0"><strong>По списку page_id:</strong>
<input id="pages_id" type="text" value="id через запятую" name="pages_id">
</p>
<p>
<strong>Шаблон:</strong>
<textarea id="templ" rows="3" name="result">[RUBRIC] - <a href="[URL]">[TITLE] [KEYWORD]</a> [TAG]<?=NR;?></textarea>
<button id="go" class="i go" name="go" type="submit">Генерировать</button><span id="loader"><img src="/application/maxsite/plugins/sapetexttool/loader.gif" width=16 height=11></span><!--button id="save" class="i options-save save" name="save" type="submit">Сохранить шаблон</button-->
</p>
<textarea id="result" rows="25" name="result"></textarea>
<p class="nop"><span class="fhint">При составлении шаблона можно использовать псевдокоды для подстановки:
<pre style="font-size:85%;">
<b>[TITLE]</b> - SEO-заголовок статьи
<b>[NAME]</b> - название статьи
<b>[TOPIC]</b> - SEO-заголовок статьи, но если он пустой, то название статьи
<b>[URL]</b> - полный адрес статьи
<b>[SLUG]</b> - slug статьи
<b>[DOMAIN]</b> - домен сайта
<b>[KEYWORDS]</b> - ключевые слова статьи
<b>[KEYWORD]</b> - одно из ключевых слов статьи
<b>[DESC]</b> - описание статьи
<b>[RUBRICS]</b> - рубрики статьи
<b>[RUBRIC]</b> - одна из рубрик статьи
<b>[TAGS]</b> - метки статьи
<b>[TAG]</b> - одна из меток статьи
<b>[ID]</b> - id статьи
<b>[NUM]</b> - порядковый номер в сгенерированном списке
</pre>
</span></p>
<p class="nop"><span class="fhint">Если данные для замены псевдокода недоступны, то замена не происходит (это позволяет отследить ошибки).</span></p>
<p class="nop"><span class="fhint">В <a href="http://www.sape.ru/r.22a187a5d4.php" rel="nofollow" title="Мои Sape-рефералы получают бесплатные консультации по настройке и оптимизации закупки ссылок." alt="Мои Sape-рефералы получают бесплатные консультации по настройке и оптимизации закупки ссылок.">SAPE.ru</a> поддерживаются тэги:
<pre style="font-size:85%;">
<<b>name</b>><<b>/name</b>> - текстовое название урла
<<b>keyword</b>><<b>/keyword</b>> - продвигаемое ключевое слово для урла
<<b>a href="урл"</b>><b>анкор</b><<b>/a</b>> - для урла и анкора
</pre>
</span></p>
</form>
<?
	require( getinfo('plugins_dir').basename(dirname(__FILE__)).'/author-info.php' ); # подключаем файл информации об авторе плагина
	
###
function get_categories() # Формирование списка рубрик для поля выбора рубрики
{
	$CI = & get_instance();
	$res = array();
		
	# нужно выбрать все рубрики
	$CI->db->select('category_id, category_name');
	$CI->db->from('category');
	$CI->db->where('category_type', 'page');
	$CI->db->order_by('category_menu_order', 'ASC');
	if($qry = $CI->db->get())
	{ 
		$cats = $qry->result_array();
		foreach( $cats as $c )
		{
			$res[] = '<option value="'.$c['category_id'].'">'.$c['category_name'].'</option>'.NR;
		}
	}
		
	return implode('', $res);
}
	
function get_types() # Формирование списка типов страницы для поля выбора типа
{
	$CI = & get_instance();
	$res = array();
		
	# нужно выбрать все рубрики
	$CI->db->select('page_type_id, page_type_name');
	$CI->db->from('page_type');
	$CI->db->order_by('page_type_id', 'ASC');
	if($qry = $CI->db->get())
	{ 
		$types = $qry->result_array();
		foreach( $types as $t )
		{
			$res[] = '<option value="'.$t['page_type_id'].'">'.$t['page_type_name'].'</option>'.NR;
		}
	}
		
	return implode('', $res);
}
	
function get_authors() # Формирование списка авторов страницы для поля выбора автора
{
	$CI = & get_instance();
	$res = array();
		
	# нужно выбрать все рубрики
	$CI->db->select('users_id, users_nik');
	$CI->db->from('users');
	$CI->db->order_by('users_id', 'ASC');
	if($qry = $CI->db->get())
	{ 
		$users = $qry->result_array();
		foreach( $users as $u )
		{
			$res[] = '<option value="'.$u['users_id'].'">'.$u['users_nik'].'</option>'.NR;
		}
	}
		
	return implode('', $res);
}
?>