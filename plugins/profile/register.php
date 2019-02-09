<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


	mso_remove_hook( 'body_start', 'demo_body_start');
	mso_remove_hook( 'body_end', 'demo_body_end');


   function fn_head($a = array())
   {
      $fn_css = getinfo('plugins_url') . 'iphonepassword/css/style.css';
	    echo '<link rel="stylesheet" type="text/css" href="'. $fn_css . '">';
	    
	    echo '<script type="text/javascript" src="' . getinfo('plugins_url') . 'iphonepassword/js/jQuery.dPassword.js"></script>' . NR;
	    echo '<script type="text/javascript" src="' . getinfo('plugins_url') . 'iphonepassword/js/iPhonePassword.js"></script>' . NR;
    
	    return $a;
   } 

  $head_fn = 'fn_head';
 	mso_hook_add('head', $head_fn); 

	require(getinfo('template_dir') . 'main-start.php');
	
	echo NR . '<div class="type type_loginform">' . NR;
	
	echo '<div class="loginform">';
	
	
	if ( $post = mso_check_post(array('comusers_session', 'comusers_submit', 'comusers_nik', 'comusers_email', 'comusers_password' , 'redirect_url') ))
	{
			mso_checkreferer();
			echo '<p><strong style="color: red;" class="loginform">'. do_register($post, array()) . '</strong></p>';
	}
	$redirect_url = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : getinfo('siteurl');

	$comusers_nik = (isset($post['comusers_nik'])) ? $post['comusers_nik'] : '';
	$comusers_email = (isset($post['comusers_email'])) ? $post['comusers_email'] : '';
	
	if (isset($post['redirect_url'])) $redirect_url = $post['redirect_url'];
	
	
 $info_text = array(
    'hello' => 'Привет',
    'title' => 'Для регистрации введите: <strong>Ник, Ваш email, Пароль</strong>',
    'logout' => 'Выход',
    'login' => 'Вход', 
    'if_register' => 'Если вы уже зарегистрированны на сайте: ', 
    'return' => 'Вернуться',
    'nik' => 'Ник',
    'email' => 'email',
    'password' => 'Пароль',
     'edit' => 'Редактировать персональные данные');


	$login_comuser = is_login_comuser();
	if ( $login_comuser )
	{
    $profile_url = getinfo('siteurl'). 'users/' . $login_comuser['comusers_id'];
	  echo $info_text['hello'] . ' ' . $login_comuser['comusers_nik'];
	  echo '<p><a href="'. $profile_url . '">' . $info_text['edit'] . '</a></p>';
	  echo '<p>(<a href="'. getinfo('siteurl') . 'logout">' . $info_text['logout'] . '</a>)</p>';	
	  echo '<div class="form-end"><a href="'. $redirect_url . '">' . $info_text['return'] . '</a></div>';	
	}
  else
  {
	  echo '<p>' . $info_text['if_register'] . '<a href="'. getinfo('siteurl') . 'loginform">' . $info_text['login'] . '</a></p>';
?>

	<form action="" method="post" class="flogin">
		<input type="hidden" name="comments_page_id" value="register">
		<input type="hidden" name="redirect_url" value="<?=$redirect_url?>">
		
		<?= mso_form_session('comusers_session') ?>
			<p class="header"><?= $info_text['title'] ?></p>
			<p class="flogin_user">
			<label class="flogin_user"><span><?= t('Имя') ?></span><input type="text" name="comusers_nik" value="<?=$comusers_nik?>" class="flogin_user"></label>
			</p>
			<p class="flogin_user">
			<label class="flogin_user"><span><?= t('E-mail') ?></span><input type="text" name="comusers_email" value="<?=$comusers_email?>" class="flogin_user"></label>
			</p>			
			<p class="flogin_password">
			
			<label for="comusers_password" class="flogin_password"><span><?= t('Пароль') ?></span></label>
			<input type="password" id="comusers_password" name="comusers_password" value="" class="flogin_password" />
			</p>	
		<?php mso_hook('comments_content_end'); ?>
		<p class="flogin_submit"><input name="comusers_submit" type="submit" value="<?=t('Отправить')?>" class="flogin_submit"></p>
	</form>

<?php
		}
  echo '<div class="form-end"><a href="'. $redirect_url . '">' . $info_text['return'] . '</a></div>';	
	
	echo '</div>';
	
	echo NR . '</div><!-- class="type type_loginform" -->' . NR;
			
	require(getinfo('template_dir') . 'main-end.php');



function do_register($post, $args = array())
{
	// Это на будущее
	if ( !isset($args['page_title']) )	$args['page_title'] = '';
	if ( !isset($args['css_ok']) )		$args['css_ok'] = 'comment-ok';
	if ( !isset($args['css_error']) )	$args['css_error'] = 'comment-err';

	if (!mso_checksession($post['comusers_session']) )
		  return '<div class="' . $args['css_error']. '">'. t('Ошибка сессии! Обновите страницу'). '</div>';

	// если этот хук возвращает false, значит капча неверная
	if (!mso_hook('comments_new_captcha', true))
	{
		// если определен хук на неверную капчу, отдаем его
		if (mso_hook_present('comments_new_captcha_error'))
		{
			return mso_hook('comments_new_captcha_error');
		}
		else
		{
			return '<div class="' . $args['css_error']. '">'. t('Ошибка! Неверно введены нижние символы!'). '</div>';
		}
	}

	$email = mso_strip(trim($post['comusers_email']));
	$nik = trim(mso_strip($post['comusers_nik']));
	$password = mso_strip($post['comusers_password']);

	if ( !$email )
		return t('Нужно указать Email');

	if ( !$password )
		return t('Нужно указать пароль');

	if ( !$nik )
		return t('Нужно указать Ник - неужели трудноввести тройку букав?');
		
	if ( !mso_valid_email($email) )
		return t('Ошибочный Email');


	if (is_email_exists($email))
		return t('Такой E-mail уже зарегистрирован');

	if ($nik and is_nik_exist($nik))
		return t('Такое имя уже зарегистрировано');

	$ins_data = array (
		'comusers_email' => $email,
		'comusers_nik' => $nik,
		'comusers_password' => mso_md5($password) // Т.е. можно было не очищать пароль, но хрен с ним.
		);

	// генерируем случайный ключ активации
	$ins_data['comusers_activate_key'] = mso_md5(rand());
	$ins_data['comusers_date_registr'] = date('Y-m-d H:i:s');
	$ins_data['comusers_last_visit'] = date('Y-m-d H:i:s');
	$ins_data['comusers_ip_register'] = $_SERVER['REMOTE_ADDR'];
	
  if ( mso_get_option('comusers_activate_auto', 'general', '0') )
					$ins_data['comusers_activate_string'] = $ins_data['comusers_activate_key'];
							
	$CI = &get_instance();
	$res = ($CI->db->insert('comusers', $ins_data)) ? '1' : '0';

	if ($res)
	{
		$id = $CI->db->insert_id(); // номер добавленной записи
			mso_email_message_new_comuser($id, $ins_data); // отправляем ему уведомление с кодом активации
	}
	else
		return t('Ошибка регистрации');

	$CI->db->cache_delete_all();
							
	# если комюзер не залогинен, то сразу логиним его
							
	$CI->db->select('comusers_id, comusers_password, comusers_email, comusers_nik, comusers_url, comusers_avatar_url, comusers_last_visit');
	$CI->db->where('comusers_email', $email);
	$CI->db->where('comusers_password', mso_md5($password));
	$query = $CI->db->get('comusers');
							
	if ($query->num_rows()) // есть такой комюзер
	{
			$comuser_info = $query->row_array(1); // вся инфа о комюзере
								
			// сразу же обновим поле последнего входа
			$CI->db->where('comusers_id', $comuser_info['comusers_id']);
			$CI->db->update('comusers', array('comusers_last_visit'=>date('Y-m-d H:i:s')));
								
			$expire  = time() + 60 * 60 * 24 * 30; // 30 дней = 2592000 секунд
								
			$name_cookies = 'maxsite_comuser';
			$value = serialize($comuser_info); 
								
			# ставим куку и редиректимся автоматом
			mso_add_to_cookie($name_cookies, $value, $expire, 
											$post['redirect_url']);

  }
}



//Функция проверки на существование email
function is_email_exists($email)
{
	$ret = false;
	$CI  = &get_instance();
	$CI->db->from('comusers');
	$CI->db->select('comusers_email');
	$CI->db->limit(1);
	$CI->db->where( array( 'comusers_email' => $email));
	$query = $CI->db->get();
	if( $query->num_rows() )
	{
		$row = $query->result_array();
		if( $row[0]['comusers_email'] == $email ) $ret = true;
	}
	return $ret;
}


//Проверка на существование ника 
function is_nik_exist($nik)
{
	$ret = false;
	$CI = &get_instance();
	$CI->db->from('comusers');
	$CI->db->select( 'comusers_nik' );
	$CI->db->limit(1);
	$CI->db->where( array( 'comusers_nik' => $nik ) );
	$query = $CI->db->get();
	if ( $query->num_rows() )
	{
		$row = $query->result_array();
		if ( $row[0]['comusers_nik'] == $nik  )
		{
			$ret = true;
		}
		else
		{
			$ret = false;
		}
	}
	return $ret;
}



# функция отправляет новому комюзеру уведомление о новой регистрации
# первый парметр id, второй данные
function mso_email_message_new_comuser($comusers_id = 0, $ins_data = array())
{
	$email = $ins_data['comusers_email']; // email куда приходят уведомления
	if (!$email) return false;

	// comusers_password
	// comusers_activate_key

	$subject = 'Регистрация на ' . getinfo('title');

	$text = 'Вы или кто-то еще зарегистрировал ваш адрес на сайте "' . getinfo('name_site') . '" - ' . getinfo('siteurl') . NR ;
	$text .= 'Если это действительно сделали вы, то вам нужно подтвердить эту регистрацию. Для этого следует пройти по ссылке: ' . NR;
	$text .= getinfo('siteurl') . 'users/' . $comusers_id . NR . NR;
	$text .= 'И ввести следующий код для активации: '. NR;
	$text .= $ins_data['comusers_activate_key'] . NR. NR;
	$text .= '(Сохраните это письмо, поскольку код активации может понадобиться для смены пароля.)' . NR . NR;
	$text .= 'Если же эту регистрацию выполнили не вы, то просто удалите это письмо.' . NR;

	return mso_mail($email, $subject, $text, $email); // поскольку это регистрация, то отправитель - тот же email
}
