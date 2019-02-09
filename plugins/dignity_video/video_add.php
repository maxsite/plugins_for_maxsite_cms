<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_video (github)
 * License GNU GPL 2+
 */

// начало шаблона
require(getinfo('shared_dir') . 'main/main-start.php');
	  

// выводим навигацию видео
video_menu();

$options = mso_get_option('plugin_dignity_video', 'plugins', array());
if ( !isset($options['slug']) ) $options['slug'] = 'video';
if (!isset($options['ontop']))  $options['ontop'] = false;

$CI = & get_instance();

if (is_login_comuser())
{
	
	echo '<h1>' . t('Добавить видео', __FILE__) . '</h1>';

	if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_video')) )
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
			'dignity_video_title' => htmlspecialchars($post['f_dignity_video_title']),
			'dignity_video_text' => htmlspecialchars($post['f_dignity_video_text']),
			'dignity_video_category' => $post['f_dignity_video_category'],
			'dignity_video_datecreate' => date('Y-m-d H:i:s'),
			'dignity_video_approved' => isset($post['f_dignity_video_approved']) ? 1 : 0,
			'dignity_video_comments' => isset($post['f_dignity_video_comments']) ? 1 : 0,
			'dignity_video_rss' => 1,
			'dignity_video_ontop' => $ontop,
			'dignity_video_comuser_id' => getinfo('comusers_id'),
			);
		
		#pr($ins_data);

		$res = ($CI->db->insert('dignity_video', $ins_data)) ? '1' : '0';
			
		if ($res)
		{
			echo '<div class="update">' . t('Сохранено! После проверки, ваше видео будет опубликовано.', __FILE__) .
				'<p>' .
				'<a href="' . getinfo('site_url') . $options['slug'] . '/my/' . getinfo('comusers_id') . '">' . t('Показать мои видео', __FILE__) . '</a><br>' .
				'<a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Показать все видео', __FILE__) . '</a>'
				. '</p>'
				. '</div>';
		}
		else echo '<div class="error">' . t('Ошибка добавления в базу данных...', __FILE__) . '</div>';
		
		mso_flush_cache();
		
	}
	else
	{
		
		$form = "";
		$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
		
		dignity_video_editor();
		
		$form .= '<p><strong>' . t('Заголовок', __FILE__) . '</strong><br><input name="f_dignity_video_title" type="text" value=""
			maxlength="70" style="width:90%" required="required"></p>';
			
		$form .= '<p><strong>' . t('Текст и код видео', __FILE__) . '</strong><br><textarea name="f_dignity_video_text" class="markItUp"
			cols="90" rows="10" value=""></textarea></p>';
			
		// опубликовать?	
		$chckout = ''; 
		if (!isset($article['dignity_video_approved']))  $article['dignity_video_approved'] = true;
		if ( (bool)$article['dignity_video_approved'] )
		{
			$chckout = 'checked="true"';
		}    
		$form .= '<p>' . t('Опубликовать?', 'plugins') . ' <input name="f_dignity_video_approved" type="checkbox" ' . $chckout . '></p>';
		
		// разрешить комментарии?	
		$chckout = ''; 
		if (!isset($article['dignity_video_comments']))  $article['dignity_video_comments'] = true;
		if ( (bool)$article['dignity_video_comments'] )
		{
			$chckout = 'checked="true"';
		}    
		$form .= '<p>' . t('Разрешить комментирования?', 'plugins') . ' <input name="f_dignity_video_comments" type="checkbox" ' . $chckout . '></p>';
		
		$CI->load->helper('form');
		$CI->db->from('dignity_video_category');
		$q = $CI->db->get();
		$category_list = array();
		$category_list[] = 'Не задан.';
		foreach ($q->result_array() as $rw)
		{
			$category_list[$rw['dignity_video_category_id']] = $rw['dignity_video_category_name'];
		}
	
		if ( !isset($post['f_dignity_video_category'])) $post['f_dignity_video_category'] = 0;
		$form .= '<p><strong>' . 'Категория: ' . ' </strong>' .
		form_dropdown('f_dignity_video_category', $category_list, set_value($post['f_dignity_video_category'],
		(isset($post['f_dignity_video_category'])) ? $post['f_dignity_video_category'] : '')) . '</p>';
		
		$form .= '<p><input type="submit" class="submit" name="f_submit_dignity_video" value="' . t('Добавить', __FILE__) . '"></p>';
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
	else{
		echo t('Только зарегистрированные пользователи могут добавлять новые видео.', __FILE__);
	}
}

// конец шаблона
require(getinfo('shared_dir') . 'main/main-end.php');
	  

#end of file