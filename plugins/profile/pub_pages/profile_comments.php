<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_cur_dir_lang('templates');

// вывод комментариев комюзера

require_once( getinfo('common_dir') . 'comments.php' ); 

// mso_get_comuser(0, array( 'limit'=> 20, 'tags'=>'<img><strong><em><i><b><u><s><font><pre><code><blockquote>' ) );



  if (isset($options['pages_profiles'][$segment3])) $title = $options['pages_profiles'][$segment3];
  else $title = '';
  mso_head_meta('title', $comusers_nik . ' » ' . $title); // meta title страницы

// теперь сам вывод
# начальная часть шаблона
require(getinfo('shared_dir') . 'main/main-start.php');
echo NR . '<div class="type type_users">' . NR;

// меню страниц публичного профиля
require (getinfo('plugins_dir') . 'profile/pub_pages/menu-profiles.php' );

$comuser_info = mso_get_comuser(mso_segment(2)); // получим всю информацию о комюзере - номер в сегменте url

if ($comuser_info)
{
		if ($comuser_info[0]['comments']) // есть комментарии
		{
			echo '<div class="events">';
			foreach ($comuser_info[0]['comments'] as $comment)
			{
				//if ($comment['comments_approved']) // только отмодерированные
				//{
					echo '<div class="event"><div class="event_title">' . $comment['comments_date'] . ' >> <a href="' . getinfo('siteurl') . 'page/' . mso_slug($comment['page_slug']) . '#comment-' . $comment['comments_id'] . '" name="comment-' . $comment['comments_id'] . '">' . $comment['page_title'] . '</a></div>';
					// echo ' | ' . $comments_url;
					echo '<span class="event_content">' . $comment['comments_content'] . '</span>';
					echo '</div>';
				//}
			}
			echo '</div>';
		}
}
else
{
	if ($f = mso_page_foreach('pages-not-found')) 
	{
		require($f); // подключаем кастомный вывод
	}
	else // стандартный вывод
	{
		echo '<h1>' . t('404. Ничего не найдено...') . '</h1>';
		echo '<p>' . t('Извините, пользователь с указанным номером не найден.') . '</p>';
		echo mso_hook('page_404');
	}
}

echo NR . '</div><!-- class="type type_users" -->' . NR;

# конечная часть шаблона
require(getinfo('shared_dir') . 'main/main-end.php');


?>