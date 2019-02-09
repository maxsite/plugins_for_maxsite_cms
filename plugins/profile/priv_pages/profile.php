<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
//

// личный кабинет
// основные настройки




  # обработка отправленных данных
  $res_post = '';
  if ($post = mso_check_post(array('f_session_id', 'f_submit', 'f_comusers_email', 'f_comusers_password',
					'f_comusers_nik', 'f_comusers_url', 'f_comusers_icq', 'f_comusers_msn', 'f_comusers_jaber',
					'f_comusers_date_birth',  'f_comusers_description', 'f_comusers_avatar_url')))
  {
  require_once( getinfo('common_dir') . 'comments.php' );  
   
   $res_post = mso_comuser_edit();
    $comuser_info = mso_get_comuser(); 
	extract($comuser_info[0]);    
  }
  
  

	  
  if (isset($options['pages']['0'])) $title = $options['pages']['0'];
  else $title = '';
  mso_head_meta('title', $options['title'] . ' » ' . $title); // meta title страницы

  require(getinfo('shared_dir') . 'main/main-start.php');
  echo NR . '<div class="type type_users_form">' . NR;

  require (getinfo('plugins_dir') . 'profile/priv_pages/menu.php' );

  echo $res_post;


	      // выводим личный кабинет
	  	
				$CI = & get_instance();
				$CI->load->helper('form');
			    echo '<form action="" method="post" class="comusers-form">' . mso_form_session('f_session_id');
				echo '<input type="hidden" value="' . $comusers_email . '" name="f_comusers_email">';
				echo '<input type="hidden" value="' . $comusers_password . '" name="f_comusers_password">';

				echo '<input type="hidden" value="' . $comusers_avatar_url . '" name="f_comusers_avatar_url">'; //  это поле на другой странице

				echo '<div class="profile_block">';
				echo '<h3>'. t('Персональные данные'). '</h3>';
				echo '<p><strong>'. t('Отображаемый ник'). ':</strong> <input type="text" name="f_comusers_nik" value="' . $comusers_nik . '"></p>';
				echo '<p><strong>'. t('Дата рождения'). ':</strong> <input type="text" name="f_comusers_date_birth" value="' . $comusers_date_birth . '"></p>';
				echo '</div>';

				echo '<div class="profile_block">';
				echo '<h3>'. t('Контактная информация'). '</h3>';
				echo '<p><strong>'. t('Сайт (с http://)'). ':</strong> <input type="text" name="f_comusers_url" value="' . $comusers_url . '"></p>';
				echo '<p><strong>'. t('ICQ'). ':</strong> <input type="text" name="f_comusers_icq" value="' . $comusers_icq . '"></p>';
				echo '<p><strong>'. t('Twitter'). ':</strong> <input type="text" name="f_comusers_msn" value="' . $comusers_msn . '"></p>';
				echo '<p><strong>'. t('Jabber'). ':</strong> <input type="text" name="f_comusers_jaber" value="' . $comusers_jaber . '"></p>';
				echo '</div>';

				echo '<div class="profile_block">';
				echo '<h3>'. t('Подписка на уведомления'). '</h3>';
				// echo '<p><strong>'. t('Уведомления'). ':</strong>' . form_dropdown('f_comusers_notify', array('0'=>t('Без уведомлений'), '1'=>t('Подписаться')), $comusers_notify, '');
				
				// поскольку чекбоксы не передаются, если они не отмечены, 
				// то передаем скрытно их дефолтные значения
				echo '<input type="hidden" value="0" name="f_comusers_meta[subscribe_my_comments]">';
				
				$check = (isset($comusers_meta['subscribe_my_comments']) and $comusers_meta['subscribe_my_comments']=='1');
				echo '<br><strong>&nbsp;</strong><label>' 
					. form_checkbox('f_comusers_meta[subscribe_my_comments]', '1', $check) 
					. ' '. t('новые комментарии, где я участвую') . '</label>';
				
				
				echo '<input type="hidden" value="0" name="f_comusers_meta[subscribe_other_comments]">';
				$check = (isset($comusers_meta['subscribe_other_comments']) and $comusers_meta['subscribe_other_comments']=='1');
				echo '<br><strong>&nbsp;</strong><label>' 
					. form_checkbox('f_comusers_meta[subscribe_other_comments]', '1', $check) 
					. ' '. t('новые комментарии, где я не участвую') . '</label>';
				
				
				echo '<input type="hidden" value="0" name="f_comusers_meta[subscribe_new_pages]">';
				$check = (isset($comusers_meta['subscribe_new_pages']) and $comusers_meta['subscribe_new_pages']=='1');
				echo '<br><strong>&nbsp;</strong><label>' 
					. form_checkbox('f_comusers_meta[subscribe_new_pages]', '1', $check) 
					. ' '. t('новые записи сайта') . '</label>';
					
					
				echo '<input type="hidden" value="0" name="f_comusers_meta[subscribe_admin]">';
				$check = (isset($comusers_meta['subscribe_admin']) and $comusers_meta['subscribe_admin']=='1');
				echo '<br><strong>&nbsp;</strong><label>' 
					. form_checkbox('f_comusers_meta[subscribe_admin]', '1', $check) 
					. ' '. t('рассылка администратора') . '</label>';
				echo '</div>';

				echo '<div class="profile_block">';
				echo '<h3>'. t('О себе (HTML удаляются)'). '</h3>';
				echo '<p><textarea name="f_comusers_description">'. NR 
					. htmlspecialchars(strip_tags($comusers_description)) . '</textarea></p>';
				echo '</div>';

				echo '<p><input type="submit" name="f_submit[' . $comusers_id . ']" class="submit" value="' .  t('Отправить') . '"></p></form>';

  
   echo NR . '</div><!-- class="type type_users_form" -->' . NR;
	 require(getinfo('shared_dir') . 'main/main-end.php');

?>