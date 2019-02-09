<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 */

require(getinfo('template_dir') . 'main-start.php');

$options = mso_get_option('plugin_dignity_joke', 'plugins', array());
if ( !isset($options['slug']) ) $options['slug'] = 'joke';
if (!isset($options['ontop']))  $options['ontop'] = false;

$CI = & get_instance();

if (is_login_comuser())
{
	joke_menu();
	
	echo '<h1>' . t('Добавить анекдот', __FILE__) . '</h1>';

	if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_joke')) )
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
			'dignity_joke_cuttext' => htmlspecialchars($post['f_dignity_joke_cuttext']),
			'dignity_joke_text' => htmlspecialchars($post['f_dignity_joke_text']),
			'dignity_joke_category' => $post['f_dignity_joke_category'],
			'dignity_joke_datecreate' => date('Y-m-d H:i:s'),
			'dignity_joke_approved' => isset($post['f_dignity_joke_approved']) ? 1 : 0,
			'dignity_joke_comments' => isset($post['f_dignity_joke_comments']) ? 1 : 0,
			'dignity_joke_rss' => 1,
			'dignity_joke_ontop' => $ontop,
			'dignity_joke_comuser_id' => getinfo('comusers_id'),
			);
		
		#pr($ins_data);

		$res = ($CI->db->insert('dignity_joke', $ins_data)) ? '1' : '0';
			
		if ($res)
		{
			echo '<div class="update">' . t('Сохранено! После проверки, ваш анекдот будет опубликован.', __FILE__) .
				'<p>' .
				'<a href="' . getinfo('site_url') . $options['slug'] . '/my/' . getinfo('comusers_id') . '">' . t('Показать мои анекдоты', __FILE__) . '</a><br>' .
				'<a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Показать все анекдоты', __FILE__) . '</a>'
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

		$CI->load->helper('form');
		$CI->db->from('dignity_joke_category');
		$q = $CI->db->get();
		$category_list = array();
		$category_list[] = 'Все анекдоты';
		foreach ($q->result_array() as $rw)
		{
			$category_list[$rw['dignity_joke_category_id']] = $rw['dignity_joke_category_name'];
		}
	
		if ( !isset($post['f_dignity_joke_category'])) $post['f_dignity_joke_category'] = 0;
		$form .= '<p><strong>' . 'Категория: ' . '</strong><br>' .
		form_dropdown('f_dignity_joke_category', $category_list, set_value($post['f_dignity_joke_category'],
		(isset($post['f_dignity_joke_category'])) ? $post['f_dignity_joke_category'] : '')) . '</p>';
			
		$form .= '<p><strong>' . t('Текст', __FILE__) . '</strong><br><textarea name="f_dignity_joke_cuttext"
			cols="80" rows="5" value=""></textarea></p>';
		
		$form .= '<p><strong>' . t('Полный текст', __FILE__) . '</strong><br><textarea name="f_dignity_joke_text"
			cols="80" rows="10" value=""></textarea></p>';
			
		// опубликовать?	
		$chckout = ''; 
		if (!isset($article['dignity_joke_approved']))  $article['dignity_joke_approved'] = true;
		if ( (bool)$article['dignity_joke_approved'] )
		{
			$chckout = 'checked="true"';
		}    
		$form .= '<p>' . t('Опубликовать?', 'plugins') . ' <input name="f_dignity_joke_approved" type="checkbox" ' . $chckout . '></p>';
		
		// разрешить комментарии?	
		$chckout = ''; 
		if (!isset($article['dignity_joke_comments']))  $article['dignity_joke_comments'] = true;
		if ( (bool)$article['dignity_joke_comments'] )
		{
			$chckout = 'checked="true"';
		}    
		$form .= '<p>' . t('Разрешить комментирования?', 'plugins') . ' <input name="f_dignity_joke_comments" type="checkbox" ' . $chckout . '></p>';
		
		$form .= '<p><input type="submit" class="submit" name="f_submit_dignity_joke" value="' . t('Добавить', __FILE__) . '"></p>';
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
		echo t('Только зарегистрированные пользователи могут добавлять новые записи.', __FILE__);
	}
}

require(getinfo('template_dir') . 'main-end.php');

#end of file
