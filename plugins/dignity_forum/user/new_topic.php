<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

// загружаем опции и присваиваем значения по умолчанию
$options = mso_get_option('plugin_dignity_forum', 'plugins', array());
if ( !isset($options['slug']) ) $options['slug'] = 'forum';
        
// получаем доступ к CI
$CI = & get_instance();

require_once(getinfo('plugins_dir') . 'dignity_forum/core/functions.php');
$forum = new Forum;
        
// если комюзер и аккаунт активированный или юзер
if (is_login_comuser() && $forum->comuser_activate() === true or is_login())
{

	$dignity_forum_topic_text = '';
	$dignity_forum_topic_subject = '';
                
	if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_forum_topic')) )
	{
		// id == 3 сегмент
		$id = mso_segment(3);
                       
		// проверяем реферала
		mso_checkreferer();
                        
		// готовим массив
		$ins_data = array (
			'dignity_forum_topic_subject' => $post['f_dignity_forum_topic_subject'],
			'dignity_forum_topic_text' => $post['f_dignity_forum_topic_text'],
			'dignity_forum_topic_category' => $id,
			'dignity_forum_topic_datecreate' => date('Y-m-d H:i:s'),
			'dignity_forum_topic_dateupdate' => date('Y-m-d H:i:s'),
			'dignity_forum_topic_approved' => 1, // сделать проверку
		);
			
		if (is_login_comuser())
		{
			$ins_data['dignity_forum_topic_сomusers_id'] = getinfo('comusers_id');
		}
		elseif (is_login())
		{
			$ins_data['dignity_forum_topic_users_id'] = getinfo('users_id');
		}

		mso_xss_clean($ins_data);
                        
		require_once( getinfo('plugins_dir') . 'dignity_forum/core/functions-edit.php');

		$res = add_new_topic($ins_data);

		// если OK, то редиректим на список записей
		if ($res['result'] == '1')
		{
			echo '<script>location.replace(window.location); </script>';
		}
		else
		{
			echo $res['message'];
			
			extract($ins_data);
				
		} // if $res
                        
	}
	else
	{	

		echo '<div class="new_forum_post">';
	
		echo '<h1>' . t('Новая тема', __FILE__) . '</h1>';

		require_once(getinfo('plugins_dir') . 'dignity_forum/user/form/form_topic.php');

		echo '</div>';
	}
}

#end of file