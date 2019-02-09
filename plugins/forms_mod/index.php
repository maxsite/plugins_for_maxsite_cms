<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * Модифицировано Н. Громовым
 */

# функция автоподключения плагина
function forms_autoload($args = array())
{
	mso_hook_add( 'content', 'forms_content'); # хук на вывод контента
}

# 
function forms_content_callback($matches) 
{
	$text = $matches[1];
	
	$text = str_replace('&nbsp;', ' ', $text);
	$text = str_replace("\t", ' ', $text);
	$text = str_replace('<br />', "\n", $text);
	$text = str_replace('<br>', "\n", $text);
	$text = str_replace("\n\n", "\n", $text);
	$text = str_replace('     ', ' ', $text);
	$text = str_replace('    ', ' ', $text);
	$text = str_replace('   ', ' ', $text);
	$text = str_replace('  ', ' ', $text);
	$text = str_replace("\n ", "\n", $text);
	$text = trim($text);
	
	$out = ''; // убиваем исходный текст формы
	
	// занесем в массив все поля
	$r = preg_match_all('!\[email=(.*?)\]|\[redirect=(.*?)\]|\[subject=(.*?)\]|\[field\](.*?)\[\/field\]!is', $text, $all);
	// pr($all);
	$f = array(); // массив для полей
	if ($r)
	{
		$email = trim(implode(' ', $all[1]));
		$redirect = trim(implode(' ', $all[2]));
		$subject = trim(implode(' ', $all[3]));
		
		$fields = $all[4];
		
		$i = 0;
		
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
		
		if (!$f) return ''; // нет полей - выходим
		
		// теперь по-идее у нас есть вся необходимая информация по полям и по форме
		// смотрим есть ли POST. Если есть, то проверяем введенные поля и если они корректные, 
		// то выполняем отправку почты, выводим сообщение и редиректимся
		
		// если POST нет, то выводим обычную форму
		// pr($f);
		
		if ( $post = mso_check_post(array('forms_session', 'forms_antispam1', 'forms_antispam2', 'forms_antispam',
					'forms_name', 'forms_email',  'forms_submit' )) )
		{
			mso_checkreferer();
			
			$out .= '<div class="forms-post">';
			// вырный email?
			if (!$ok = mso_valid_email($post['forms_email']))
			{
				$out .= '<h2>' . t('Неверный email!', 'plugins') . '</h2>';
			}
			
			// антиспам 
			if ($ok)
			{
				$antispam1s = (int) $post['forms_antispam1'];
				$antispam2s = (int) $post['forms_antispam2'];
				$antispam3s = (int) $post['forms_antispam'];
				
				if ( ($antispam1s/984 + $antispam2s/765) != $antispam3s )
				{ // неверный код
					$ok = false;
					$out .= '<h2>' . t('Некорректно заполнено поле «Защита от спама»!', 'plugins') . '</h2>';
				}
			}
			
			if ($ok) // проверим обязательные поля
			{
				foreach ($f as $key=>$val)
				{
					if ( $ok and isset($val['require']) and $val['require'] == 1 ) // поле отмечено как обязательное
					{
						if (!isset($post['forms_fields'][$key]) or !$post['forms_fields'][$key]) 
						{
							$ok = false;
							$out .= '<h2>' . t('Заполните все необходимые поля!', 'plugins') . '</h2>';
						}
					}
					if (!$ok) break;
				}
			}
			
			// всё ок
			if ($ok)
			{
				// pr($post);
				// pr($f);
				// pr($redirect);
				// pr($email);
				// pr($subject);
				
				// формируем письмо и отправляем его
				
				if (!mso_valid_email($email)) mso_get_option('admin_email', 'general', 'admin@site.com'); // куда приходят письма
				
				$message = 'Имя: ' . $post['forms_name'] . "\n";
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
				
				// pr($message);
				
				$form_hide = mso_mail($email, $subject, $message, $post['forms_email']);
				
				if ( isset($post['forms_subscribe']) ) 
					mso_mail($post['forms_email'], t('Вами отправлено сообщение:', 'plugins') . ' ' . $subject, $message);
				
				
				$out .= '<h2>' . t('Ваше сообщение отправлено!', 'plugins') . '</h2><p>' 
						. str_replace("\n", '<br />', htmlspecialchars($subject. "\n" . $message)) 
						. '</p>';
				
				if ($redirect) mso_redirect($redirect, true);

			}
			else // какая-то ошибка, опять отображаем форму
			{
				$out .= forms_show_form($f);
			}
			
			
			$out .= '</div>';

		}
		else // нет post
		{
			$out .= forms_show_form($f);
		}
	}

	return $out;
}

function forms_show_form($f = array())
{
	$out = '';
	srand((double) microtime() * 1000000);
	$antispam1 = rand(1, 10);
	$antispam2 = rand(1, 10);
	$require_default = ' <big class="require">*</big>';
	
	$out .= NR . '<div class="forms">' . NR . '<form action="" method="post">'. NR . mso_form_session('forms_session');
	
	$out .= NR . '<input type="hidden" name="forms_antispam1" value="' . $antispam1 * 984 . '" />';
	$out .= NR . '<input type="hidden" name="forms_antispam2" value="' . $antispam2 * 765 . '" />';
	
	// обязательные поля
	$out .= NR . '<div class="name input_text">' . NR . '<label for="forms_name">' . t('Ваше имя', 'plugins') . '</label> <input name="forms_name" id="forms_name" type="text" value="" />' . $require_default . NR . '</div>';
	
	$out .= NR . '<div class="email input_text">' . NR . '<label for="forms_email">' . t('Ваш e–mail', 'plugins') . '</label> <input name="forms_email" id="forms_email" type="text" value="" />' . $require_default . NR . '</div>';
	
	
	// тут указанные поля в $f
	
	foreach ($f as $key=>$val)
	{
		if (!isset($val['description'])) continue;
		if (!isset($val['type'])) continue;
		
		
		if (isset($val['require']) and  $val['require'] == 1) $require = $require_default;
			else $require = false;
		
		$description = trim($val['description']);
		
		if ($val['type'] == 'text')
		{
			$out .= NR . '<div class="fields_' . $key . ' input_text">' . NR . '<label for="forms_fields_' . $key . '">' . $description . '</label> <input name="forms_fields[' . $key . ']" id="forms_fields_' . $key . '" type="text" value="" />';
			if ($require) $out .= $require;
			$out .= NR . '</div>';
		}
		elseif ($val['type'] == 'select')
		{
			if (!isset($val['default'])) continue;
			if (!isset($val['values'])) continue;
			
			$out .= NR . '<div class="fields_' . $key . '">' . NR . '<label for="forms_fields_' . $key . '">' . $description . '</label> <select name="forms_fields[' . $key . ']" id="forms_fields_' . $key . '" >';
			
			$default = trim($val['default']);
			$values = explode('#', $val['values']);
			foreach ($values as $value)
			{
				$value = trim($value);
				if ($value == $default) $checked = ' selected="selected"';
					else $checked = '';
				
				$out .= NR . '<option' . $checked . '>' . $value . '</option>';
			}
			
			$out .= NR . '</select>';
			if ($require) $out .= $require;
			$out .= NR . '</div>';
	
		}
		elseif ($val['type'] == 'textarea')
		{
			$out .= NR . '<div class="fields_' . $key . '">' . NR . '<label for="forms_fields_' . $key . '">' . $description . '</label> <textarea cols="40" rows="8" name="forms_fields[' . $key . ']" id="forms_fields_' . $key . '"></textarea>';
			if ($require) $out .= $require;
			$out .= NR . '</div>';
		
		}
	}
	
	// обязательные поля антиспама и отправка и ресет
	$out .= NR . '<div class="antispam input_text">' . NR . '<label for="forms_antispam">' . t('Защита от спама:', 'plugins') . ' ' . $antispam1 . '&nbsp;+&nbsp;' . $antispam2 . '&nbsp;=</label> <input name="forms_antispam" id="forms_antispam" type="text" value="" />' . $require_default . NR . '</div>';

	$out .= NR . '<div class="subscribe input_checkbox">' . NR . '<input name="forms_subscribe" id="forms_subscribe" value="" type="checkbox" />&nbsp;<label for="forms_subscribe">' . t('Отправить копию письма на ваш e-mail', 'plugins') . '</label>' . NR . '</div>';

	$out .= NR . '<p class="require-desc">' . t('Поля, отмеченные', 'plugins') . ' ' . $require_default . ' ' . t('обязательны для заполнения!', 'plugins') . '</p>';

	$out .= NR . '<div class="submit">' . NR . '<input name="forms_submit" type="submit" value="' . t('Отправить', 'plugins') . '" /> <input name="forms_clear" type="reset" value="' . t('Очистить форму', 'plugins') . '" />' . NR . '</div>';
	
	$out .= NR . '</form>' . NR . '</div>' . NR;
	
	return $out;
}

# функции плагина
function forms_content($text = '')
{
	if (strpos($text, '[form]') !== false) $text = preg_replace_callback('!\[form\](.*?)\[/form\]!is', 'forms_content_callback', $text );
	return $text;
}

?>