<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

global $plug_url;
$page_comment_allow = 1;

# коммментарии
echo '<span><a name="comments"></a></span>';

// получаем список комментариев текущей страницы
require_once( getinfo('common_dir') . 'comments.php' ); // функции комментариев

// если был отправлен новый коммент, то обрабатываем его и выводим сообщение в случае ошибки
echo foto_get_new_comment( array('foto_title'=>$foto_title) ); 


// получаем все разрешенные комментарии
$comments = fotki_get_comments($foto_id);

// в сессии проверяем может быть только что отправленный комментарий
if (isset($MSO->data['session']['foto_comments']) and $MSO->data['session']['foto_comments'] )
{
	$anon_comm = $MSO->data['session']['foto_comments']; // массив: id-коммент
	
	// получаем комментарии для этого юзера
	$an_comments = fotki_get_comments($page_id, array('anonim_comments'=>$anon_comm));
	
	// добавляем в вывод
	if ($an_comments) $comments = array_merge($comments, $an_comments);
}

if (is_login()) 
	$edit_link = ''; //getinfo('siteurl') . 'admin/comments/edit/';
else 
	$edit_link = '';

if ($comments or $page_comment_allow) echo NR . '<div class="type type_foto_comments">' . NR;

if ($comments) // есть страницы
{ 	

	if ($f = mso_page_foreach('foto-comments-do')) require($f); // подключаем кастомный вывод
	else 
	{
		echo '<div class="comments">';
		echo '<h3 class="comments">' . t('Комментариев') . ': ' . count($comments) . '</h3>';
	}
	
	echo '<ol>';
	
	foreach ($comments as $comment)  // выводим в цикле
	{
		if ($f = mso_page_foreach('foto-comments')) 
		{
			require($f); // подключаем кастомный вывод
			continue; // следующая итерация
		}
		
		extract($comment);
		
		if ($users_id) $class = ' class="users"';
		elseif ($comusers_id) $class = ' class="comusers"';
		else $class = ' class="anonim"';
		
		$foto_comments_date = mso_date_convert('Y-m-d в H:i:s', $foto_comments_date);
		
		echo NR . '<li style="clear: both"' . $class . '><span class="date"><a href="#comment-' . $foto_comments_id . '" name="comment-' . $foto_comments_id . '">' . $foto_comments_date . '</a></span>';
		echo ' | <span class="url">' . $comments_url . '</span>';
		
		if ($edit_link) echo ' | <a href="' . $edit_link . $foto_comments_id . '">edit</a>';
		
		if (!$foto_comments_approved) echo ' | '. t('Ожидает модерации');

		
		$avatar_url = '';
		if ($comusers_avatar_url) $avatar_url = $comusers_avatar_url;
		elseif ($users_avatar_url) $avatar_url = $users_avatar_url;
		
		$avatar_size = (int) mso_get_option('gravatar_size', 'templates', 80);
		if ($avatar_size < 1 or $avatar_size > 512) $avatar_size = 80;
		
		if (!$avatar_url) 
		{ // аватарки нет, попробуем получить из gravatara
			
			if ($users_email) $grav_email = $users_email;
			elseif ($comusers_email) $grav_email = $comusers_email;
			else $grav_email = '';
			
			if ($gravatar_type = mso_get_option('gravatar_type', 'templates', ''))
				$d = '&amp;d=' . urlencode($gravatar_type);
			else 
				$d = '';
			
			$avatar_url = "http://www.gravatar.com/avatar.php?gravatar_id=" 
					. md5($grav_email)
					. "&amp;size=" . $avatar_size
					. $d;
		}
		
		if ($avatar_url) 
			$avatar_url = '<span style="display: none"><![CDATA[<noindex>]]></span><img src="' . $avatar_url . '" width="' . $avatar_size . '" height="'. $avatar_size . '" alt="" title="" style="float: left; margin: 5px 10px 10px 0;" class="gravatar"><span style="display: none"><![CDATA[</noindex>]]></span>';
		
		echo '<div class="comments_content">' . $avatar_url;
		echo mso_comments_content($comments_content);
		echo '</div>';
		
		echo '</li>'; 
		
	//	pr($comment);
	}
	
	echo '</ol>';
	echo '</div>' . NR;
}

if ($page_comment_allow)
{
	// если запрещены комментарии и от анонимов и от комюзеров, то выходим
	if ( mso_get_option('allow_comment_anonim', 'general', '1') 
		or mso_get_option('allow_comment_comusers', 'general', '1') ) 
	{
		if ($f = mso_page_foreach('foto-comment-form-do')) require($f); // подключаем кастомный вывод
		else echo '<div class="break"></div><h3 class="comments">'. t('Оставьте комментарий!'). '</h3>';
		
		if ($f = mso_page_foreach('foto-comment-form')) 
		{
			require($f); // подключаем кастомный вывод
		}
		else 
		{
			// форма комментариев
			// page-comment-form.php может быть в type своего шаблона
			$fn1 = getinfo('template_dir') . 'type/foto-comment-form.php'; 		 // путь в шаблоне
			$fn2 = getinfo('plugins_dir') . $plug_url . '/foto-comment-form.php'; // путь в default
			if ( file_exists($fn1) ) require($fn1); // если есть, подключаем шаблонный
			elseif (file_exists($fn2)) require($fn2); // нет, значит дефолтный
		}
	}
}

if ($comments or $page_comment_allow) echo NR . '</div><!-- class="type type_foto_comments" -->' . NR;

?>