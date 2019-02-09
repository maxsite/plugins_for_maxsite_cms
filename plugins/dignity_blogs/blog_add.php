<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_blogs (github)
 * License GNU GPL 2+
 */

// начало шаблона
require(getinfo('shared_dir') . 'main/main-start.php');
	  

// получаем доступ к CI
$CI = & get_instance();

// выводим меню
blogs_menu();

$options = mso_get_option('plugin_dignity_blogs', 'plugins', array());
if ( !isset($options['slug']) ) $options['slug'] = 'blogs';
if (!isset($options['ontop']))  $options['ontop'] = false;

if (is_login_comuser())
{

	if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_blogs')) )
	{
		mso_checkreferer();
		
		if ($options['ontop'])
		{
			$ontop = 1;
		}
		else
		{
			$ontop = 0;
		}
		
		$ins_data = array (
			'dignity_blogs_title' => htmlspecialchars(mso_xss_clean($post['f_dignity_blogs_title'])),
			'dignity_blogs_cuttext' => htmlspecialchars(mso_xss_clean($post['f_dignity_blogs_cuttext'])),
			'dignity_blogs_text' => htmlspecialchars(mso_xss_clean($post['f_dignity_blogs_text'])),
			'dignity_blogs_datecreate' => date('Y-m-d H:i:s'),
			'dignity_blogs_approved' => isset($post['f_dignity_blogs_approved']) ? 1 : 0,
			'dignity_blogs_comments' => isset($post['f_dignity_blogs_comments']) ? 1 : 0,
			'dignity_blogs_rss' => 1,
			'dignity_blogs_ontop' => $ontop,
			'dignity_blogs_comuser_id' => getinfo('comusers_id'),
			'dignity_blogs_category' => $post['f_dignity_blogs_category'],
			);

		$res = ($CI->db->insert('dignity_blogs', $ins_data)) ? '1' : '0';
			
		if ($res)
		{
			echo '<div class="update">' . t('Ваша запись добавлена!', __FILE__);
			echo '<p><a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Назад к ленте записей', __FILE__) . '</a>' . '</p>';
			echo '</div>';
		}
		else echo '<div class="error">' . t('Ошибка добавления в базу данных...', __FILE__) . '</div>';
		
		mso_flush_cache();
		
	}
	else
	{
		// начало формы
		$form = "";
		$form .= '<h1>' . t('Новая запись', __FILE__) . '</h1>';
		$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
		
		dignity_blogs_editor();
		
		$form .= '
		<script>
		$(document).ready(function(){	
			$("#blogs_title").charCount({
			allowed: 70,		
			warning: 20,
			counterText: "<br>" + "Осталось: "	
			});
			
			$("#blogs_anonce").charCount({
			allowed: 1000,		
			warning: 20,
			counterText: "Осталось: "	
			});
			
			$("#blogs_text").charCount({
			allowed: 30000,		
			warning: 20,
			counterText: "Осталось: "	
			});
		});
		</script>';
		
		$form .= '<p><strong>' . t('Заголовок:', __FILE__) . '</strong><br><input name="f_dignity_blogs_title" type="text" value=""
			maxlength="70" style="width:90%" required="required" id="blogs_title"></p>';
			
		$form .= '<p style="color:#b2b2b3;">' . t('Заголовок должен быть наполнен смыслом, чтобы можно было понять, о чем будет запись.', __FILE__) . '</p>';
			
		$form .= '<p><strong>' . t('Анонс:', __FILE__) . '</strong><br><textarea name="f_dignity_blogs_cuttext" class="markItUp"
			cols="90" rows="5" value="" maxlength="1000" required="required" id="blogs_anonce"></textarea></p>';
		
		$form .= '<p><strong>' . t('Текст:', __FILE__) . '</strong><br><textarea name="f_dignity_blogs_text" class="markItUp"
			cols="90" rows="10" value="" maxlength="30000" id="blogs_text"></textarea></p>';
						
		// опубликовать?	
		$chckout = ''; 
		if (!isset($article['dignity_blogs_approved']))  $article['dignity_blogs_approved'] = true;
		if ( (bool)$article['dignity_blogs_approved'] )
		{
			$chckout = 'checked="true"';
		}    
		$form .= '<p>' . t('Опубликовать статью?', 'plugins') . ' <input name="f_dignity_blogs_approved" type="checkbox" ' . $chckout . '></p>';
		$form .= '<p style="color:#b2b2b3;">' . t('Если убрать эту галочку, то запись будет сохранена как черновик.', __FILE__) . '</p>';
		
		// разрешить комментарии?	
		$chckout = ''; 
		if (!isset($article['dignity_blogs_comments']))  $article['dignity_blogs_comments'] = true;
		if ( (bool)$article['dignity_blogs_comments'] )
		{
			$chckout = 'checked="true"';
		}    
		$form .= '<p>' . t('Разрешить комментирование?', 'plugins') . ' <input name="f_dignity_blogs_comments" type="checkbox" ' . $chckout . '></p>';
		$form .= '<p style="color:#b2b2b3;">' . t('Если убрать эту галочку, то нельзя будет оставлять комментарии к записи.', __FILE__) . '</p>';
		
		// выбрать категорию
		$CI->load->helper('form');
		$CI->db->from('dignity_blogs_category');
		$q = $CI->db->get();
		$category_list = array();
		$category_list[] = 'Не задан.';
		foreach ($q->result_array() as $rw)
		{
			$category_list[$rw['dignity_blogs_category_id']] = $rw['dignity_blogs_category_name'];
		}
	
		if ( !isset($post['f_dignity_blogs_category'])) $post['f_dignity_blogs_category'] = 0;
		$form .= '<p>' . 'Категория: ' .
		form_dropdown('f_dignity_blogs_category', $category_list, set_value($post['f_dignity_blogs_category'],
		(isset($post['f_dignity_blogs_category'])) ? $post['f_dignity_blogs_category'] : '')) . '</p>';
		$form .= '<p style="color:#b2b2b3;">' . t('Выберите более подходяшую категорию для вашей записи.', __FILE__) . '</p>';
		
		// конец формы
		$form .= '<p><input type="submit" class="submit" name="f_submit_dignity_blogs" value="' . t('Опубликовать', __FILE__) . '"></p>';
		$form .= '</form>';
			
		echo $form;
	}
}
else
{
	if (is_login())
	{
		echo t('Вы должны войти как комюзер.', __FILE__);
	}
	else
	{
		echo t('Только зарегистрированные пользователи могут добавлять новые записи.', __FILE__);
	}
}

// конец шаблона
require(getinfo('shared_dir') . 'main/main-end.php');
	  

#end of file
