<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

// доступ к CI
$CI = &get_instance();

require_once(getinfo('plugins_dir') . 'dignity_forum/core/functions.php');
$forum = new Forum;

// скрывать сайтбар
$forum->hide_sidebar();

// меню
$forum->menu();

// загружаем опции и проверяем
$options = mso_get_option('plugin_dignity_forum', 'plugins', array());
if ( !isset($options['limit']) ) $options['limit'] = '10';
if ( !isset($options['slug']) ) $options['slug'] = 'forum';
if ( !isset($options['header']) ) $options['header'] = t('Форум', __FILE__);
if ( !isset($options['textdo']) ) $options['textdo'] = '';
if ( !isset($options['textposle']) ) $options['textposle'] = '';
if ( !isset($options['meta_title']) ) $options['meta_title'] = '';
if ( !isset($options['meta_description']) ) $options['meta_description'] = '';
if ( !isset($options['meta_keywords']) ) $options['meta_keywords'] = '';
if ( !isset($options['stats']) ) $options['stats'] = true;

// добавляем мета-данные
mso_head_meta('title', $options['meta_title']);
mso_head_meta('description', $options['meta_description']);
mso_head_meta('keywords', $options['meta_keywords']);

// начальный div-forum
echo '<div class="forum">';

// выводим заголовок
echo '<div class="forum_header_forums">';
echo '<h1>' . $options['header'] . '</h1>';
if (is_login())
{
	echo '<p><span style="color:red;">' . t('Внимание:', __FILE__) . '</span> ' . t('Вы вошли как администратор (user)!', __FILE__) . '</p>';
}
echo '</div>';

// выводим текст до
echo '<p>' . $options['textdo'] . '</p>';

// получаем массив из функции
$out_forums = $forum->get_forums();

// dignity_forum_category_id
// dignity_forum_category_name
// dignity_forum_category_description
// dignity_forum_category_order
// dignity_forum_category_onlycomusers
// dignity_forum_category_onlyusers
// dignity_forum_category_parent_id
// dignity_forum_category_img

$out = '';

if ($out_forums['forum_content'] > 0)	
{

	$out .= '<div class="forum_info">';

	$out .= '<span class="forum_info_replys">' . t('Сообщения', __FILE__) . '</span>
		<span class="forum_info_topics">' . t('Темы', __FILE__) . '</span>
		<span class="forum_info_forums">' . t('Форумы', __FILE__) . '</span>';

	$out .= '</div>';

	foreach ($out_forums['forum_content'] as $rw)
	{

		// подгатавливаем к выводу названия раздела, количество тем
		$out .= '<div class="forum_topic" id="topic-'. $rw['dignity_forum_category_id'] .'">';

		$out .= '<span class="forum_topic_replys">'. $forum->reply_in_topic($rw['dignity_forum_category_id']) .'</span>';
		$out .= '<span class="forum_topic_topics_in_cat">' . $forum->topics_in_category($rw['dignity_forum_category_id']) . '</span>';

		$out .= '<h2><img src="' . getinfo('plugins_url') . 'dignity_forum/img/forum_read.png' . '" alt="" title=""> <a href="' . getinfo('siteurl') . $options['slug'] . '/view/' . $rw['dignity_forum_category_id'] . '">' . $rw['dignity_forum_category_name'] . '</a></h2>';

		// подгатавливаем к выводу описания раздела
		$out .= '<span class="forum_category_description">';
		$out .= '<p>' . $rw['dignity_forum_category_description'] . '</p>';
		$out .= '</span>';

		// выводим подкатегории
		$out .= $forum->sub_topic($rw['dignity_forum_category_id']);

		$out .= '</div>';
	}

	echo $out;

}
else
{
	echo '<p>' . t('Нет категорий для отображения.') . '</p>';

	if (is_login())
	{
		echo '<p><a href="' . getinfo('site_admin_url') . 'dignity_forum/edit_category/' . '">' . t('Создать →', __FILE__) . '</a></p>';
	}
}

// если нужно выводить статистику
if ($options['stats'])
{	

	// начало forum-footer
	echo '<div class="forum_footer">';

	echo '<div class="forum_footer_statistic">';

	echo '<span>' . t('Статистика форума', __FILE__) . ' </span>';

	echo '</div>';

	echo '<img src="' . getinfo('plugins_url') . 'dignity_forum/img/statistics.png' . '" alt="" title="">';
			
	echo '<br /><p>' . t('Написано сообщений:', __FILE__) . ' <strong>' . $forum->all_topics_and_replys() . '</strong></p>';
	echo '<p>' . t('Создано тем:', __FILE__) . ' <strong>' . $forum->all_topics() . '</strong></p>';
	echo '<p>' . t('Зарегистрировано пользователей:', __FILE__) . ' <strong>' . $forum->all_comusers() . '</strong></p>';
		
	// выводим последного зарег. пользователя
	if ($forum->new_comuser())
	{
		echo '<p>' . t('Последний зарегистрировавшийся пользователь:', __FILE__) . ' <strong>' . $forum->new_comuser() . '</strong></p>';
	}
		
	// конец forum-footer
	echo '</div>';	
}

echo '<div class="clearfix"></div>';

// выводим текст после
echo '<p>' . $options['textposle'] . '</p>';

echo '</div>'; // end of class forum
