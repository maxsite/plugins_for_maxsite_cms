<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

global $plug_url;

# коммментарии
   require(getinfo('plugins_dir') . 'other_comments/functions.php');


if (!isset($element)) $element = array();

if (!isset($element['slug'])) $element['slug'] = mso_segment(2);
if (!isset($element['kind'])) $element['kind'] = mso_segment(1);

// проверим в таблице сущностей наличие комментируемой сущности
// если нет то создадим
// в результате будет добавлен эл-т массива - ID
// $element['id'] - номер записи о комментируемой сущности в таблице OC_Elements
$element = other_comments_get_element($element);

$page_comment_allow = $element['comment_allow'];

if ($element['id'])
{
   echo '<span><a name="comments"></a></span>';

   require_once( getinfo('common_dir') . 'comments.php' ); // функции комментариев

   // если был отправлен новый коммент, то обрабатываем его и выводим сообщение в случае ошибки
   echo other_comments_get_new_comment($element); 

   // получаем все разрешенные комментарии
   $comments = other_comments_get_comments($element);

  if (isset($MSO->data['session']['other_comments']) and $MSO->data['other_comments'] )
  {
	  $anon_comm = $MSO->data['session']['other_comments']; // массив: id-коммент
	  // получаем комментарии для этого юзера
	  $an_comments = other_comments_get_comments($element, array('anonim_comments'=>$anon_comm));
	
	  // добавляем в вывод
	  if ($an_comments) $comments = array_merge($comments, $an_comments);
  }

  if (is_login()) $edit_link = ''; //getinfo('siteurl') . 'admin/comments/edit/';
  else $edit_link = '';

  if ($comments or $page_comment_allow) echo NR . '<div class="type type_page_comments">' . NR;

if ($page_comment_allow)
{
	// если запрещены комментарии и от анонимов и от комюзеров, то выходим
	if ( mso_get_option('allow_comment_anonim', 'general', '1') 
		or mso_get_option('allow_comment_comusers', 'general', '1') ) 
	{
		if ($f = mso_page_foreach('other_comments-form-do')) require($f); // подключаем кастомный вывод
		else echo '<div class="break"></div><h3 class="comments">'. t('Оставьте комментарий!'). '</h3>';
		
		if ($f = mso_page_foreach('other_comments-form')) 
		{
			require($f); // подключаем кастомный вывод
		}
		else 
		{
			// форма комментариев
			// page-comment-form.php может быть в type своего шаблона
			$fn1 = getinfo('template_dir') . 'type/other_comments-form.php'; 		 // путь в шаблоне
			$fn2 = getinfo('plugins_dir') . 'other_comments/other_comments-form.php'; // путь в default
			if ( file_exists($fn1) ) require($fn1); // если есть, подключаем шаблонный
			elseif (file_exists($fn2)) require($fn2); // нет, значит дефолтный
		}
	}
}

if ($comments) // есть страницы
{ 	

	if ($f = mso_page_foreach('other_comments-comments-do')) require($f); // подключаем кастомный вывод
	else 
	{
		echo '<div class="comments">';
		echo '<h3 class="comments">' . t('Комментариев') . ': ' . count($comments) . '</h3>';
	}
	
	echo '<ol>';
	
	foreach ($comments as $comment)  // выводим в цикле
	{
		if ($f = mso_page_foreach('page-comments')) 
		{
			require($f); // подключаем кастомный вывод
			continue; // следующая итерация
		}
		
		extract($comment);
		
		if ($users_id) $class = ' class="users"';
		elseif ($comusers_id) $class = ' class="comusers"';
		else $class = ' class="anonim"';
		
		$comments_date = mso_date_convert('Y-m-d в H:i:s', $comments_date);
		
		echo NR . '<li style="clear: both"' . $class . '><div class="comment-info"><span class="date"><a href="#comment-' . $comments_id . '" id="comment-' . $comments_id . '">' . $comments_date . '</a></span>';
		echo ' | <span class="url">' . $comments_url . '</span>';
		
		if ($edit_link) echo ' | <a href="' . $edit_link . $comments_id . '">edit</a>';
		
		if (!$comments_approved) echo ' | '. t('Ожидает модерации');
		
		echo '</div>';
		
		echo '<div class="comments_content">' 
			. mso_avatar($comment) 
			. mso_comments_content($comments_content) 
			. '</div>';
		
		echo '</li>'; 
		
	//	pr($comment);
	}
	
	echo '</ol>';
	echo '</div>' . NR;
}


if ($comments or $page_comment_allow) echo NR . '</div><!-- class="type type_page_comments" -->' . NR;
}
?>