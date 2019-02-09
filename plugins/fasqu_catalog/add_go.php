<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
// Обработчик добавления ссылки

	mso_checkreferer();
    $postargs = array();
    $postargs['url'] = trim(strip_tags($post['f_url']));
	  $postargs['title'] = trim(strip_tags($post['f_title']));
    $postargs['description'] = trim(strip_tags($post['f_desc']));
    $postargs['queries'] = trim(strip_tags($post['f_queries']));	
	
	$captcha = $post['f_captha']; // это введенное значение капчи
	// которое должно быть вычисляем как и в img.php
	$char = md5($session['session_id'] . mso_slug(mso_current_url()));
	$char = str_replace(array('a', 'b', 'c', 'd', 'e', 'f'), array('0', '5', '8', '3', '4', '7'), $char);
	$char = substr( $char, 1, 4);
	if ($captcha != $char)
	{ // не равны
		echo '<div class="error">' . t('Привет роботам!', __FILE__) . '</div>';
		mso_flush_cache();
	}
	else
	{ // прошла капча, можно добавлять 
		$err = '';
    $postargs['api_key'] = $options["code"]; //  API Ключ
	  
	  if (!$postargs['url']) $err .= 'Не указан адрес сайта. ';
	  if (!$postargs['title']) $err .= 'Не указано название. ';
	  if (!$postargs['description']) $err .= 'Не указано описание. ';
	  if (!$postargs['queries']) $err .= 'Добавьте хотя бы одну метку. ';
	   
    if (!$err)
    {
      $postargs['queries'] .= ' , ' . $cur_tag;
      $postargs['share']= "no";
      $postargs['charset']= "UTF-8";
      $ch = curl_init("http://api.fasqu.com/draft.php");
      curl_setopt ($ch, CURLOPT_POST, true);
      curl_setopt ($ch, CURLOPT_POSTFIELDS, $postargs);
      curl_setopt($ch, CURLOPT_USERAGENT, "plugin");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    
      $response = curl_exec($ch);
      curl_close($ch);	
      if ($response != 'YES')
      {
        switch ($response ) 
        {
          case 'BAD_METHOD': $err = 'Неверный метод отправки данных, требуется POST запрос.';
          case 'NO_DATA': $err = 'Не все обязательные поля были отправлены.';
          case 'BAD_API_KEY': $err = 'Неверный ключ API.';
          case 'LIMIT_POSTS': $err = 'Превышен лимит обращений.';
          case 'ACCESS_DENIED_TO_DRAFTS': $err = 'Запрещен доступ к отправке в Черновики.';
          default:  $err = 'Неверный результат запроса на добавление.';
        }
			  $text_email = "Ошибка запроса на добавление: " . $err . "\n";
			  if ( $options['email'] and mso_valid_email($options['email']) ) 
		  	{
				  mso_mail($options['email'], t('Ошибка добавления', __FILE__), $text_email);
			  }        
      }  
      else
      {  	
 			  $text_email = t("Добавлена ссылка") . ": \n";
			  $text_email .= "\n" . $postargs['url'];
			  $text_email .= "\n" . $postargs['title'];
			  $text_email .= "\n" . $postargs['description'];
			  $text_email .= "\n" . $postargs['queries'];
			  if ( $options['email'] and mso_valid_email($options['email']) ) 
			  {
				  mso_mail($options['email'], t('Добавлена ссылка', __FILE__), $text_email);
			  }
	  	  echo '<div class="update">' . t('Ссылка добавлена! ', __FILE__);
		    echo t('Она будет опубликована после одобрения модератором.', __FILE__);
		    echo '</div>';
		  } 
		  $postargs = array();
		}
		
		if ($err) echo '<div class="error">' . t('Ошибка добавления. ' . $err , __FILE__) . '</div>';
		mso_flush_cache();
		// тут бы редирект, но мы просто убиваем сессию
		$CI->session->sess_destroy();
	}

?>