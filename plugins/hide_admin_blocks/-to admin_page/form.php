<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
	
	mso_cur_dir_lang('admin');
	
	# Форма - работает совместно с edit и new
	
	
	# загрузки
	/*
	
	загрузки пока убираю. фигня получилась.
	
	ob_start();	
	require($MSO->config['admin_plugins_dir'] . 'admin_page/files.php');
	$page_files = ob_get_contents();
	ob_end_clean();
		
	$page_admin_files = '<p>' . t('Скопируйте код в редактор.', 'admin') . ' (<a href="'. $MSO->config['site_admin_url'] . 'files" target="_blank">' . t('Страница «Загрузки»', 'admin') . '</a>)</p>';
	*/
	$page_files = '';
	$page_admin_files = '';
	
	# до 
	$do = '
	<table class="new_or_edit">
	<tr>
		<td class="editor_and_meta">
		<input type="text" value="' . $f_header . '" name="f_header" class="f_header">' . $fses;
	
	# после
	$posle = '

			<div class="page_status">
				
				<a style="display: block; float: right;" href="'. $MSO->config['site_admin_url'] 
						. 'files" target="_blank" class="page_files">' . t('Страница «Загрузки»', 'admin') . '</a>
						
				<p class="page_status">
					<label><input name="f_status[]" type="radio" ' . $f_status_publish . ' value="publish" id="f_status_publish"> ' . t('Опубликовать', 'admin') . '</label> 
					<label><input name="f_status[]" type="radio" ' . $f_status_draft . ' value="draft" id="f_status_draft"> ' . t('Черновик', 'admin') . '</label> 
					<label><input name="f_status[]" type="radio" ' . $f_status_private . ' value="private" id="f_status_private"> ' . t('Личное', 'admin') . '</label>
				</p>
									
				' . $f_return . '
				<input type="submit" name="' . $name_submit . '" value="' . t('Готово', 'admin') . '" class="wymupdate"> <span class="autosave-editor"></span>
			</div>
			
			<div>
				<div class="block_page page_meta">
					<h3>' . t('Дополнительные поля', 'admin') . '</h3>
					' . $all_meta . '
					' . mso_hook('admin_page_form_add_all_meta') . '
				</div>
				<!--
				<div class="block_page page_files">
					<h3>' . t('Файлы', 'admin') . '</h3>
					' . $page_admin_files . '
					<div class="frame">
					' . $page_files . '
					</div>
				</div>
				-->
			</div>
		</td>
		
		<td class="page_info">
			' . mso_hook('admin_page_form_add_block_1') . '
			<div class="block_page page_all_cat">
				<h3>' . t('Рубрика', 'admin') . '</h3>
				<div class="cat_page">' . $all_cat . '</div>
			</div>
			
			<div class="block_page page_tags">
				<h3>' . t('Метки (через запятую)', 'admin') . '</h3>
				<p><input type="text" value="' . $f_tags . '" name="f_tags" id="f_tags" style="width: 99%;"></p>
				' . $f_all_tags . '
			</div>
			
			<div class="block_page page_slug">
				<h3>' . t('Короткая ссылка', 'admin') . '</h3>
				<p><input type="text" value="' . $f_slug . '" name="f_slug" style="width: 99%;"></p>
			</div>

			<div class="block_page page_discus">
				<h3>' . t('Обсуждение', 'admin') . '</h3>
				<p><label><input name="f_comment_allow" type="checkbox" ' . $f_comment_allow . '> ' . t('Разрешить комментирование', 'admin') . '</label></p>
				<p><label><input name="f_feed_allow" type="checkbox" ' . $f_feed_allow . '> ' . t('Публикация в RSS', 'admin') . '</label></p>
				<!--p><input name="f_ping_allow" type="checkbox" ' . $f_ping_allow . '> ' . t('Разрешить пинг', 'admin') . '</p-->
			</div>

			<div class="block_page page_date">
				<h3>' . t('Дата публикации', 'admin') . '</h3>
				<p><label><input name="f_date_change" type="checkbox" ' . $f_date_change . '> Изменить дату публикации</label></p>
				<p>' . $date_y . ' - ' . $date_m . ' - ' . $date_d . '</p>
				<p>' . $time_h . ' : ' . $time_m . ' : ' . $time_s . '</p>
				<p><em>' . $date_time . '</em></p>
			</div>
			
			<div class="block_page page_post_type">
				<h3>' . t('Тип страницы', 'admin') . '</h3>
				' . $all_post_types . '
			</div>			
			
			<div class="block_page page_password">
				<h3>' . t('Пароль для чтения', 'admin') . '</h3>
				<p><input type="text" value="' . $f_password . '" name="f_password" style="width: 99%;"></p>
			</div>
			
			<div class="block_page page_menu_order">
				<h3>' . t('Порядок', 'admin') . '</h3>
				<p><input type="text" value="' . $page_menu_order . '" name="f_menu_order" style="width: 99%;"></p>
			</div>
				
			<div class="block_page page_all_parent">
				<h3>' . t('Родительская страница', 'admin') . '</h3>
				<p>' . $all_pages . '</p>
			</div>
			
			<div class="block_page page_all_users">
				<h3>' . t('Автор', 'admin') . '</h3>
				<p>' . $all_users . '</p>
			</div>
		</td>
	</tr>
	</table>
	';

?>