<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

   echo '<div class="forms-post">';
   $ok = false;
   $redirect_url = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : getinfo('siteurl');
 
 // посылать могут только зарегистрированные
 // и те, у которых рейтинг больше заданного
 // и тем, кто разрешил посылать сообщение
 if ($comuser_id  and ($comuser['profile_rate'] >= $options['rate_alow_send']) and ($edit_profile['profile_allow_msg'] == '1') )
 {
   // подключаем опции писем
   require($plugin_dir . 'options_send_email.php');

 	// здесь должны быть известны переменные
 	
	$text = $options['email_fields'];
    $email = $edit_profile['comusers_email'];
    $subject = $options['name'] . ' - Сообщение от пользователя: ' . $comuser['profile_psevdonim']; 
    $forms_email = $comuser['comusers_email'];
	$user_profile_link = $comuser['profile_psevdonim'];
		
		
  // подготовим поля
		
	$text = str_replace("\r", "", $text);
	
	$text = str_replace('&nbsp;', ' ', $text);
	$text = str_replace("\t", ' ', $text);
	$text = str_replace('<br />', "<br>", $text);
	$text = str_replace('<br>', "\n", $text);
	$text = str_replace("\n\n", "\n", $text);
	$text = str_replace('     ', ' ', $text);
	$text = str_replace('    ', ' ', $text);
	$text = str_replace('   ', ' ', $text);
	$text = str_replace('  ', ' ', $text);
	$text = str_replace("\n ", "\n", $text);
	$text = str_replace("\n\n", "\n", $text);
	$text = trim($text);
		
		$r = preg_match_all('!\[email=(.*?)\]|\[redirect=(.*?)\]|\[subject=(.*?)\]|\[field\](.*?)\[\/field\]!is', $text, $all);

		$fields = $all[4];
	
	  $f = array(); // массив для полей
		$i = 0;
		// подготовим поля для формы
		foreach ($fields as $val)
		{
			$val = trim($val);
			
			if (!$val) continue;
			
			$val = str_replace(' = ', '=', $val);
			$val = str_replace('= ', '=', $val);
			$val = str_replace(' =', '=', $val);
			$val = explode("\n", $val); // разделим на строки
			
			$ar_val = array();
			foreach ($val as $pole)
			{
				$ar_val = explode('=', $pole); // строки разделены = type = select
				if ( isset($ar_val[0]) and isset($ar_val[1]))
					$f[$i][$ar_val[0]] = $ar_val[1];
			}
			
			$i++;
		}    
 
 
 
    // если post отправлен
		if ( $post = mso_check_post(array('forms_session', 'forms_antispam1', 'forms_antispam2', 'forms_antispam',
					'forms_email',  'forms_submit' )) )
		{

       $ok = true; // я-пессимист		
		
			  // проверим обязательные поля
				foreach ($f as $key=>$val)
				{
					if (isset($val['require']) and $val['require'] == 1 ) // поле отмечено как обязательное
					{
						if (!isset($post['forms_fields'][$key]) or !$post['forms_fields'][$key]) 
						{
							$ok = false;
							if ($ok) $out .= '<h2>' . t('Заполните все необходимые поля!', 'plugins') . '</h2>';
						}
					}
			    // заодно заполним поля значениями если будет ошибка и снова нужна форма
				  if (isset($post['forms_fields'][$key])) $f[$key]['default'] = $post['forms_fields'][$key];			
				}


			mso_checkreferer();

			
			$forms_email = $post['forms_email'];
			
			// вырный email?
			if (!$ok = mso_valid_email($forms_email))
			{
				$out .= '<h2>' . t('Неверный email!', 'plugins') . '</h2>';
			}
			
			$redirect_url = $post['redirect_url'];
      if ($redirect_url == ($siteurl . mso_current_url())) $redirect_url = false;
			
			// антиспам 
			if ($ok)
			{
				$antispam1s = (int) $post['forms_antispam1'];
				$antispam2s = (int) $post['forms_antispam2'];
				$antispam3s = (int) $post['forms_antispam'];
				
				if ( ($antispam1s/984 + $antispam2s/765) != $antispam3s )
				{ // неверный код
					$ok = false;
					$out .= '<h2>' . t('Вы что - прогуляли 2-й класс?', 'plugins') . '</h2>';
				}
			}
			

			
			$redirect_url = $post['redirect_url'];
			
			
			// всё ок
			if ($ok)
			{
				// формируем письмо и отправляем его
				
				if (!mso_valid_email($email)) mso_get_option('admin_email', 'general', 'admin@site.com'); // куда приходят письма
				
				$message = 'Профиль отправителя с ником ' . $comuser['profile_psevdonim'] . ' : ' . "\n" . $user_profile_link . "\n";
				$message .= 'Email: ' . $post['forms_email'] . "\n";
				
				foreach ($post['forms_fields'] as $key=>$val)
				{
					$message .= $f[$key]['description'] . ': ' . $val . "\n";
				}
				
				if ($_SERVER['REMOTE_ADDR'] and $_SERVER['HTTP_REFERER'] and $_SERVER['HTTP_USER_AGENT']) 
				{
					$message .= "\n" . 'IP-адрес: ' . $_SERVER['REMOTE_ADDR'] . "\n";
					$message .= 'Отправлено со страницы: ' . $_SERVER['HTTP_REFERER'] . "\n";
					$message .= 'Браузер: ' . $_SERVER['HTTP_USER_AGENT'] . "\n";
				}
				
				$form_hide = mso_mail($email, $subject, $message, $post['forms_email']);
				
				if ( isset($post['forms_subscribe']) ) 
					mso_mail($post['forms_email'], t('Вами отправлено сообщение:', 'plugins') . ' ' . $subject, $message);
				
				
				$out .= '<h2>' . t('Ваше сообщение отправлено!', 'plugins') . '</h2><p>' 
						. str_replace("\n", '<br>', htmlspecialchars($subject. "\n" . $message)) 
						. '</p>';
				
				if ($redirect_url and ($redirect_url != ($siteurl . mso_current_url())) ) mso_redirect($redirect_url, true);

			}
		}  //if post





// отобразим форму
if (!$ok)
{ 
	$antispam1 = rand(1, 10);
	$antispam2 = rand(1, 10);
	
	$out .= NR . '<div class="forms"><form method="post">' . mso_form_session('forms_session');
	
	$out .= '<input type="hidden" name="forms_antispam1" value="' . $antispam1 * 984 . '">';
	$out .= '<input type="hidden" name="forms_antispam2" value="' . $antispam2 * 765 . '">';
	$out .= '<input type="hidden" name="redirect_url" value="' . $redirect_url . '">';
	
	// обязательные поля
	$out .= '<div><label><span>' . t('Ваш email*', 'plugins') . '</span><input name="forms_email" type="text" value="' . $forms_email . '"></label></div><div class="break"></div>';
	
	
	// тут указанные поля в $f
	// pr($f);
	foreach ($f as $key=>$val)
	{
		if (!isset($val['description'])) continue;
		if (!isset($val['type'])) continue;
		
		$val['type'] = trim($val['type']);
		$val['description'] = trim($val['description']);
		
		if (isset($val['require']) and  trim($val['require']) == 1) $require = '*';
			else $require = '';
		
		$description = trim($val['description']);
		
		if (isset($val['tip']) and trim($val['tip']) ) $tip = '<div class="tip">'. trim($val['tip']) . '</div>';
			else $tip = '';
			
		if ($val['type'] == 'text')
		{
		  if (isset($val['default'])) $value = $val['default']; else $value = '';
			$out .= '<div><label><span>' . $description . $require . '</span><input name="forms_fields[' . $key . ']" type="text" value="' . $value . '"></label>' . $tip . '</div><div class="break"></div>';
		}
		elseif ($val['type'] == 'select')
		{
			if (!isset($val['default'])) continue;
			if (!isset($val['values'])) continue;
			
			$out .= '<div><label><span>' . $description . $require . '</span><select name="forms_fields[' . $key . ']">';
			
			$default = trim($val['default']);
			$values = explode('#', $val['values']);
			foreach ($values as $value)
			{
				$value = trim($value);
				if ($value == $default) $checked = ' selected="selected"';
					else $checked = '';
				
				$out .= '<option' . $checked . '>' . $value . '</option>';
			}
			
			$out .= '</select></label>' . $tip . '</div><div class="break"></div>';
	
		}
		elseif ($val['type'] == 'textarea')
		{
		  if (isset($val['default'])) $value = $val['default']; else $value = '';
			$out .= '<div><label><span>' . $description . $require . '</span><textarea name="forms_fields[' . $key . ']">' . $value . '</textarea></label>' . $tip . '</div><div class="break"></div>';
		
		}
	}
	
	// обязательные поля антиспама и отправка и ресет
	$out .= '<div><label><span>' . t('Защита от спама:', 'plugins') . ' ' . $antispam1 . ' + ' . $antispam2 . '=</span>';
	$out .= '<input name="forms_antispam" type="text" value=""></label></div><div class="break"></div>';

	$out .= '<div><span>&nbsp;</span><label><input name="forms_subscribe" value="" type="checkbox"  class="forms_checkbox">&nbsp;' . t('Отправить копию письма на ваш e-mail', 'plugins') . '</label></div><div class="break"></div>';
	
	$out .= '<div><span>&nbsp;</span><input name="forms_submit" type="submit" class="forms_submit" value="' . t('Отправить', 'plugins') . '">';
	$out .= '<input name="forms_clear" type="reset" class="forms_reset" value="' . t('Очистить форму', 'plugins') . '"></div>';
	
	$out .= '</form></div>' . NR;
	
}

  echo $out;

}
elseif (!$comuser_id) // если нет залогиненного
{
   echo '<a href="' . $redirect_url . '" title="Вернутся на исходную страницу">Вернуться</a>';
   echo '<div class="error">' . $options['send_not_allow'] . '</div>';
   // подключаем форму входа

      $fn = 'form_login_register.php';
      if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
      else 
        require($template_default_dir . $fn);    
}
elseif ($comuser['profile_rate'] < $options['rate_alow_send']) // если нет залогиненного
{
   echo '<a href="' . $redirect_url . '" title="Вернутся на исходную страницу">Вернуться</a>';
   echo '<div class="error">' . $options['send_not_allow_rate'] . '</div>';
}
elseif ($edit_profile['profile_allow_msg'] != '1') // если нет залогиненного
{
   echo '<a href="' . $redirect_url . '" title="Вернутся на исходную страницу">Вернуться</a>';
   echo '<div class="error">' . $options['send_not_allow_user'] . '</div>';
}

echo '</div>';

	
?>


