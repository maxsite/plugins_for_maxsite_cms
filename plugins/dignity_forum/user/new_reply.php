<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

// получаем доступ к CI
$CI = & get_instance();

require_once(getinfo('plugins_dir') . 'dignity_forum/core/functions.php');
$forum = new Forum;
        
// если комюзер или юзер
if (is_login_comuser() && $forum->comuser_activate() === true or is_login())
{

	$dignity_forum_reply_text = '';
            
    // если пост
    if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_forum_reply')) )
    {
        // id == 3 сегмент
        $id = mso_segment(3);
                        
        // проверяем реферала
       	mso_checkreferer();
                        
        // готовим массив
       	$ins_data = array (
                           	'dignity_forum_reply_text' => $post['f_dignity_forum_reply_text'],
                            'dignity_forum_reply_datecreate' => date('Y-m-d H:i:s'),
                            'dignity_forum_reply_dateupdate' => date('Y-m-d H:i:s'),
                            'dignity_forum_reply_topic_id' => $id,
                            'dignity_forum_reply_approved' => 1,
						);

       	// кто автор комментария (comuser/user)
		if (is_login_comuser())
		{
			$ins_data['dignity_forum_reply_comusers_id'] = getinfo('comusers_id');
		}
		else
		{
			$ins_data['dignity_forum_reply_users_id'] = getinfo('users_id');
		}

		mso_xss_clean($ins_data);
    
		require_once( getinfo('plugins_dir') . 'dignity_forum/core/functions-edit.php');

		$res = add_new_reply($ins_data);

		// если OK, то редиректим на список записей
		if ($res['result'] == '1')
		{
			echo '<div class="update">' . t('Ответ добавлен!', __FILE__) . '</div>';
		    #echo '<script>location.replace(window.location); </script>';
		    echo "<script>window.location.replace('" . $id . "'); </script>";
		}
		else
		{
			echo $res['message'];
			
			extract($ins_data);
				
		} // if $res
					
		// готовим массив для обновление времени темы
		$ins_data = array ('dignity_forum_topic_dateupdate' => date('Y-m-d H:i:s'));

		// обновляем время темы
		topic_time_update($ins_data, $id);
                        
    }
    else
    {
        require_once(getinfo('plugins_dir') . 'dignity_forum/user/form/form_reply.php');
    }
}

#end of file
