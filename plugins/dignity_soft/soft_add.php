<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 */

require(getinfo('shared_dir') . 'main/main-start.php');
	  

$options = mso_get_option('plugin_dignity_soft', 'plugins', array());
if ( !isset($options['slug']) ) $options['slug'] = 'soft';
if (!isset($options['ontop']))  $options['ontop'] = false;

$CI = & get_instance();

if (is_login_comuser())
{
	soft_menu();
	
	echo '<h1>' . t('Добавить приложения', __FILE__) . '</h1>';

	if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_soft')) )
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
			'dignity_soft_title' => htmlspecialchars($post['f_dignity_soft_title']),
			'dignity_soft_cuttext' => htmlspecialchars($post['f_dignity_soft_cuttext']),
			'dignity_soft_text' => htmlspecialchars($post['f_dignity_soft_text']),
			'dignity_soft_weblink' => htmlspecialchars($post['f_dignity_soft_weblink']),
			'dignity_soft_category' => $post['f_dignity_soft_category'],
			'dignity_soft_datecreate' => date('Y-m-d H:i:s'),
			'dignity_soft_approved' => isset($post['f_dignity_soft_approved']) ? 1 : 0,
			'dignity_soft_comments' => isset($post['f_dignity_soft_comments']) ? 1 : 0,
			'dignity_soft_rss' => 1,
			'dignity_soft_ontop' => $ontop,
			'dignity_soft_comuser_id' => getinfo('comusers_id'),
			'dignity_soft_os' => htmlspecialchars($post['f_dignity_soft_os']),
			'dignity_soft_license' => $post['f_dignity_soft_license'],
			);
		
		#pr($ins_data);

		$res = ($CI->db->insert('dignity_soft', $ins_data)) ? '1' : '0';
			
		if ($res)
		{
			echo '<div class="update">' . t('Сохранено! После проверки, ваше приложения будет опубликовано.', __FILE__) .
				'<p>' .
				'<a href="' . getinfo('site_url') . $options['slug'] . '/my/' . getinfo('comusers_id') . '">' . t('Показать мои приложения', __FILE__) . '</a><br>' .
				'<a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Показать все приложения', __FILE__) . '</a>'
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
		
		dignity_soft_editor();
		
		$form .= '
		<script>
		$(document).ready(function(){	
			$("#soft_title").charCount({
			allowed: 70,		
			warning: 20,
			counterText: "<br>" + "Осталось: "	
			});
			
			$("#soft_anonce").charCount({
			allowed: 1000,		
			warning: 20,
			counterText: "Осталось: "	
			});
			
			$("#soft_text").charCount({
			allowed: 10000,		
			warning: 20,
			counterText: "Осталось: "	
			});
			
			$("#soft_weblink").charCount({
			allowed: 70,		
			warning: 20,
			counterText: "<br>" + "Осталось: "	
			});
		});
		</script>';
		
		$form .= '<p><strong>' . t('Заголовок', __FILE__) . '</strong><br><input name="f_dignity_soft_title" type="text" value=""
			maxlength="70" style="width:90%" required="required" id="soft_title"></p>';
			
		$form .= '<p><strong>' . t('Краткое описания', __FILE__) . '</strong><br><textarea name="f_dignity_soft_cuttext" class="markItUp"
			cols="90" rows="5" value="" maxlength="1000" id="soft_anonce"></textarea></p>';
		
		$form .= '<p><strong>' . t('Подробное описания', __FILE__) . '</strong><br><textarea name="f_dignity_soft_text" class="markItUp"
			cols="90" rows="10" value="" maxlength="10000" id="soft_text"></textarea></p>';
			
		$form .= '<p><strong>' . t('Ссылка на приложения', __FILE__) . '</strong><br><input name="f_dignity_soft_weblink" type="text" value=""
			maxlength="70" style="width:90%" required="required" id="soft_weblink"></p>';
		
		// Лицензия
		$CI->load->helper('form');
		$license_list = array();
		$license_list[0] = 'Freeware';
		$license_list[1] = 'Shareware';
		$license_list[2] = 'Open Source (GNU GPL, MIT, BSD...)';
		$license_list[3] = 'Non-Free';
		$license_list[4] = 'Другая лицензия';
	
		if ( !isset($post['f_dignity_soft_license'])) $post['f_dignity_soft_license'] = 0;
		$form .= '<p>' . 'Лицензия: ' .
		form_dropdown('f_dignity_soft_license', $license_list, set_value($post['f_dignity_soft_license'],
		(isset($post['f_dignity_soft_license'])) ? $post['f_dignity_soft_license'] : '')) . '</p>';
		
		// OS
		$os_list = array();
		$os_list[0] = 'Windows';
		$os_list[1] = 'Linux';
		$os_list[2] = 'Windows, Linux';
	
		if ( !isset($post['f_dignity_soft_os'])) $post['f_dignity_soft_os'] = 0;
		$form .= '<p>' . 'ОС: ' .
		form_dropdown('f_dignity_soft_os', $os_list, set_value($post['f_dignity_soft_os'],
		(isset($post['f_dignity_soft_os'])) ? $post['f_dignity_soft_os'] : '')) . '</p>';
		
		// опубликовать?	
		$chckout = ''; 
		if (!isset($article['dignity_soft_approved']))  $article['dignity_soft_approved'] = true;
		if ( (bool)$article['dignity_soft_approved'] )
		{
			$chckout = 'checked="true"';
		}    
		$form .= '<p>' . t('Опубликовать?', 'plugins') . ' <input name="f_dignity_soft_approved" type="checkbox" ' . $chckout . '></p>';
		
		// разрешить комментарии?	
		$chckout = ''; 
		if (!isset($article['dignity_soft_comments']))  $article['dignity_soft_comments'] = true;
		if ( (bool)$article['dignity_soft_comments'] )
		{
			$chckout = 'checked="true"';
		}    
		$form .= '<p>' . t('Разрешить комментирования?', 'plugins') . ' <input name="f_dignity_soft_comments" type="checkbox" ' . $chckout . '></p>';
		
		$CI->db->from('dignity_soft_category');
		$q = $CI->db->get();
		$category_list = array();
		$category_list[] = 'Не задан.';
		foreach ($q->result_array() as $rw)
		{
			$category_list[$rw['dignity_soft_category_id']] = $rw['dignity_soft_category_name'];
		}
	
		if ( !isset($post['f_dignity_soft_category'])) $post['f_dignity_soft_category'] = 0;
		$form .= '<p>' . 'Категория: ' .
		form_dropdown('f_dignity_soft_category', $category_list, set_value($post['f_dignity_soft_category'],
		(isset($post['f_dignity_soft_category'])) ? $post['f_dignity_soft_category'] : '')) . '</p>';
		
		$form .= '<p><input type="submit" class="submit" name="f_submit_dignity_soft" value="' . t('Добавить', __FILE__) . '"></p>';
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
		echo t('Только зарегистрированные пользователи могут добавлять новые приложения.', __FILE__);
	}
}

require(getinfo('shared_dir') . 'main/main-end.php');
	  

#end of file