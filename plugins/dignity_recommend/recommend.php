<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 *
 * Alexander Schilling
 * (c) http://alexanderschilling.net
 *
 */

require(getinfo('shared_dir') . 'main/main-start.php');
	  

$CI = & get_instance();

$options = mso_get_option('plugin_dignity_recommend', 'plugins', array());

// проверяем на существования
if ( !isset($options['slug']) ) $options['slug'] = 'recommend';
if ( !isset($options['header']) ) $options['header'] = t('Рекомендовать', __FILE__);
if ( !isset($options['textdo']) ) $options['textdo'] = '';
if ( !isset($options['textposle']) ) $options['textposle'] = '';

// выводим заголовк и текс-до
echo '<h1>' . $options['header'] . '</h1>';
echo '<p>' . $options['textdo'] . '</p>';

echo '<p>' . t('через социальные сети:', __FILE__) . '</p>';

$path = getinfo('plugins_url') . 'dignity_recommend/img/'; # путь к картинкам
$post_link = getinfo('site_url');
$post_title = getinfo('title');
$show = "";
$sep = ' ';

$link = '<a rel="nofollow" href="http://twitter.com/home/?status=' . urlencode (stripslashes(mb_substr($post_title, 0, 139 - mb_strlen($post_link, 'UTF8'), 'UTF8') . ' ' . $post_link)) . '">';
$show .= $link . '<img title="' . t('Опубликовать ссылку в Twitter', __FILE__) . '" alt="twitter.com" src="' . $path . '/twitter.png' . '" width="24" height="24"></a>';

$link = '<a rel="nofollow" href="http://www.facebook.com/sharer.php?u=' . $post_link . '">';
$show .= $sep . $link . '<img title="' . t('Опубликовать ссылку в Facebook', __FILE__) . '" alt="facebook.com" src="' . $path . '/facebook.png' . '" width="24" height="24"></a>';

$link = '<a rel="nofollow" href="http://vkontakte.ru/share.php?url=' . $post_link . '&amp;title=' . $post_title  . '">';
$show .= $sep . $link . '<img title="Опубликовать ссылку В Контакте" alt="vkontakte.ru" src="' . $path . '/vkontakte.png' . '" width="24" height="24"></a>';

$show .= $sep . '<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
		<div class="g-plusone" data-size="standard" data-count="true"></div>
		<script type="text/javascript"> gapi.plusone.render("g-plusone", {"size": "standard", "count": "true"}); </script>';

echo '<div class="addzakl">' . $show . '</div>';

echo '<p>' . t('или отправить рекомендацию на E-Mail:', __FILE__) . '</p>';

$session = getinfo('session'); 
// если был пост
if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_recommend', 'f_dignity_recommend_captha')) )
{
	// проверяем реферала
	mso_checkreferer();
	
	// проверяем на существования
	if ( !isset($post['f_dignity_recommend_friend_name']) ) $post['f_dignity_recommend_friend_name'] = '';
	if ( !isset($post['f_dignity_recommend_name']) ) $post['f_dignity_recommend_name'] = '';
	if ( !isset($post['f_dignity_recommend_email']) ) $post['f_dignity_recommend_email'] = '';
	if ( !isset($post['f_dignity_recommend_from']) ) $post['f_dignity_recommend_from'] = '';
	
	// капча
	$captcha = $post['f_dignity_recommend_captha'];
	$char = md5($session['session_id'] . mso_slug(mso_current_url()));
	$char = str_replace(array('a', 'b', 'c', 'd', 'e', 'f'), array('0', '5', '8', '3', '4', '7'), $char);
	$char = substr( $char, 1, 4);
	if ($captcha != $char)
	{
		// если ошибки...
		echo '<div class="error">' . t('Не правильно введена капча!', __FILE__) . '</div>';
	}
	else
	{
		// Если письмо отправлено, то выводим...
		echo '<div class="update">' . t('Письмо с вашей рекомендацией ресурса успешно отправлено.', __FILE__) . '</div>';
		
		// Если E-Mail введён и он правильный
		if ( $post['f_dignity_recommend_email'] and mso_valid_email($post['f_dignity_recommend_email']) ) 
		{
			// убираем всё лишнее
			$friend_name = htmlspecialchars(strip_tags(trim($post['f_dignity_recommend_friend_name'])));
			$name = htmlspecialchars(strip_tags(trim($post['f_dignity_recommend_name'])));
			$email = htmlspecialchars(strip_tags(trim($post['f_dignity_recommend_email'])));
			$from = htmlspecialchars(strip_tags(trim($post['f_dignity_recommend_from'])));
			
			// задаём заголовок для письма
			$subject = t('Рекомендовать - ', __FILE__) . getinfo('siteurl');
			
			// Готовим что будем отправлять
			$text_email = '';
			$text_email .= t('Здравствуйте,', __FILE__) . ' ' . $friend_name . "\n" . "\n";
			$text_email .= t('Ваш друг', __FILE__) . ' ' . $name . ' ' . t('посетив сайт', __FILE__) . ' ' . getinfo('siteurl') . ' ' . t('посчитал его интересным и решил оповестить Вас о нем.', __FILE__) . "\n" . "\n";
			$text_email .= t('Название сайта:', __FILE__) . ' ' . getinfo('title') . "\n";
			$text_email .= t('URL сайта:', __FILE__) . ' ' . getinfo('siteurl');
			
			// передаём всё функции отправки писем
			mso_mail($email, $subject, $text_email, $from);
		}
		
		// убиваем сессию
		$CI->session->sess_destroy();
	}
}
else
{
	// Выводим форму
	$form = "";
	$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
	
	$form .= '<p>' . t('Ваше имя', __FILE__) . ':<br>
		<input name="f_dignity_recommend_name" type="text" value="" maxlength="10" required="required"></p>';
		
	$form .= '<p>' . t('Ваш E-mail', __FILE__) . ':<br>
		<input name="f_dignity_recommend_from" type="email" value="" maxlength="70" required="required"></p>';
		
	$form .= '<p>' . t('Имя вашего друга', __FILE__) . ':<br>
		<input name="f_dignity_recommend_friend_name" type="text" value="" maxlength="10" required="required"></p>';
		
	$form .= '<p>' . t('E-mail Вашего друга', __FILE__) . ':<br>
		<input name="f_dignity_recommend_email" type="email" value="" maxlength="70" required="required"></p>';
 	
	// капча из плагина капчи
	
	$form .= '<p><tr><td style="vertical-align: top; text-align: right;" class="td1">' .  '</td>
			<td style="text-align: left;" class="td2">' . t('Введите нижние символы', __FILE__) . ':<br>
			<input type="text" name="f_dignity_recommend_captha" value="" maxlength="4" required="required">
			</p><p><img src="' 
			. getinfo('plugins_url') . 'captcha/img.php?image='
			. $session['session_id']
			. '&page='
			. mso_slug(mso_current_url())
			. '&code='
			. time()
			. '" title="' . t('Защита от спама: введите только нижние символы', __FILE__) . '" align="absmiddle"></td></tr></p>';

	
	$form .= '<p><input type="submit" class="submit" name="f_submit_dignity_recommend" value="' . t('Отправить', __FILE__) . '"></p>';
	
	$form .= '</form>';
	
	echo $form;
}

echo '<p>' . $options['textposle'] . '</p>';

require(getinfo('shared_dir') . 'main/main-end.php');
	  

#end of file