<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 */

require(getinfo('shared_dir') . 'main/main-start.php');
	  

$options = mso_get_option('plugin_dignity_wiki', 'plugins', array());
if ( !isset($options['slug']) ) $options['slug'] = 'wiki';

$CI = & get_instance();

if (is_login_comuser())
{
	wiki_menu();
	
	echo '<h1>' . t('Добавить статью в wiki', __FILE__) . '</h1>';

	if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_wiki')) )
	{
		mso_checkreferer();
		
		$ins_data = array (
			'dignity_wiki_title' => htmlspecialchars($post['f_dignity_wiki_title']),
			'dignity_wiki_cuttext' => htmlspecialchars($post['f_dignity_wiki_cuttext']),
			'dignity_wiki_text' => htmlspecialchars($post['f_dignity_wiki_text']),
			'dignity_wiki_category' => $post['f_dignity_wiki_category'],
			'dignity_wiki_datecreate' => date('Y-m-d H:i:s'),
			'dignity_wiki_approved' => 0,
			'dignity_wiki_comuser_id' => getinfo('comusers_id'),
			);
		
		#pr($ins_data);

		$res = ($CI->db->insert('dignity_wiki', $ins_data)) ? '1' : '0';
			
		if ($res)
		{
			echo '<div class="update">' . t('Сохранено! После проверки, ваша статья будет опубликована.', __FILE__) .
				'<p>' .
				 '<a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Показать все статьи', __FILE__) . '</a>'
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
		
		dignity_wiki_editor();
		
		$form .= '';
		
		$form .= '<p><strong>' . t('Заголовок', __FILE__) . '</strong><br><input name="f_dignity_wiki_title" type="text" value=""
			maxlength="70" style="width:90%" required="required"></p>';
			
		$form .= '<p><strong>' . t('Краткое описания', __FILE__) . '</strong><br><textarea name="f_dignity_wiki_cuttext" class="markItUp"
			cols="90" rows="5" value="" maxlength="1000"></textarea></p>';
		
		$form .= '<p><strong>' . t('Подробное описания', __FILE__) . '</strong><br><textarea name="f_dignity_wiki_text" class="markItUp"
			cols="90" rows="10" value="" maxlength="10000"></textarea></p>';
		
        $CI->load->helper('form');
		$CI->db->from('dignity_wiki_category');
		$q = $CI->db->get();
		$category_list = array();
		$category_list[] = 'Не задан.';
		foreach ($q->result_array() as $rw)
		{
			$category_list[$rw['dignity_wiki_category_id']] = $rw['dignity_wiki_category_name'];
		}
	
		if ( !isset($post['f_dignity_wiki_category'])) $post['f_dignity_wiki_category'] = 0;
		$form .= '<p>' . 'Категория: ' .
		form_dropdown('f_dignity_wiki_category', $category_list, set_value($post['f_dignity_wiki_category'],
		(isset($post['f_dignity_wiki_category'])) ? $post['f_dignity_wiki_category'] : '')) . '</p>';
		
		$form .= '<p><input type="submit" class="submit" name="f_submit_dignity_wiki" value="' . t('Добавить', __FILE__) . '"></p>';
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
		echo t('Только зарегистрированные пользователи могут добавлять новые статьи.', __FILE__);
	}
}

require(getinfo('shared_dir') . 'main/main-end.php');
	  

#end of file