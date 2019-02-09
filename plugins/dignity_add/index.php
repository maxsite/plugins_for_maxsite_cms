<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * Alexander Schilling
 * (c) http://maxsite.thedignity.biz
 *
 * Icons
 * (c) http://icondock.com
 */

# функция автоподключения плагина
function dignity_add_autoload($args = array())
{
	if ( is_type('page') )
	{
		mso_hook_add( 'content_end', 'dignity_add_content_end');
	}
}


# функции плагина
function dignity_add_content_end($args = array())
{
	global $page;
	
	$sep = ' ';  # разделитель мужду кнопками - можно указать свой
	
	# ширина и высота картинок
	$width_height = ' width="24" height="24"';  

	$path = getinfo('plugins_url') . 'dignity_add/images/'; # путь к картинкам
	
	$post_title = urlencode ( stripslashes($page['page_title'] . ' - ' . mso_get_option('name_site', 'general') ) );
	$post_link = getinfo('siteurl') . mso_current_url();
	$out = '';
	
	$img_src = 'twitter.png';
	$link = '<a rel="nofollow" href="http://twitter.com/home/?status=' . urlencode (stripslashes(mb_substr($page['page_title'], 0, 139 - mb_strlen($post_link, 'UTF8'), 'UTF8') . ' ' . $post_link)) . '">';
	$out .= $link . '<img border="0" title="Добавить в Twitter.com" alt="twitter.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';	
	
	$img_src = 'facebook.png';
	$link = '<a rel="nofollow" href="http://www.facebook.com/sharer.php?u=' . $post_link . '">';
	$out .= $sep . $link . '<img border="0" title="Поделиться в Facebook" alt="facebook.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';		
	
	$img_src = 'vkontakte.png';
	$link = '<a rel="nofollow" href="http://vkontakte.ru/share.php?url=' . $post_link . '&title=' . $post_title  . '">';
	$out .= $sep . $link . '<img border="0" title="Поделиться В Контакте" alt="vkontakte.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'odnoklassniki.png';
	$link = '<a rel="nofollow" href="http://www.odnoklassniki.ru/dk?st.cmd=addShare&st._surl=' . $post_link . '&title=' . $post_title  . '">';
	$out .= $sep . $link . '<img border="0" title="Добавить в Одноклассники" alt="odnoklassniki.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'google-buzz.png';
	$link = '<a rel="nofollow" href="http://www.google.com/buzz/post?message=' . $post_link . '&url=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Добавить в Google Buzz" alt="google.com/buzz" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'friendfeed.png';
	$link = '<a rel="nofollow" href="http://www.friendfeed.com/share?title=' . $post_link .  '">';
	$out .= $sep . $link . '<img border="0" title="Добавить в FriendFeed" alt="friendfeed.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'yaru.png';
	$link = '<a rel="nofollow" href="http://my.ya.ru/posts_add_link.xml?URL=' . $post_link . '&title=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Поделиться в Я.ру" alt="ya.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'mail-ru.png';
	$link = '<a rel="nofollow" href="http://connect.mail.ru/share?url=' . $post_link . '&title=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Поделиться в Моем Мире@Mail.Ru" alt="mail.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'myspace.png';
	$link = '<a rel="nofollow" href="http://www.myspace.com/Modules/PostTo/Pages/?u=' . $post_link . '&t' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Добавить в MySpace" alt="myspace.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'rutvit.png';
	$link = '<a rel="nofollow" href="http://rutvit.ru/tools/widgets/share/popup?url=' . $post_link . '&title=' . $post_title  . '">';
	$out .= $sep . $link . '<img border="0" title="Добавить в РуТви" alt="rutvit.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'pikabu.png';
	$link = '<a rel="nofollow" href="http://pikabu.ru/add_story.php?story_url=' . $post_link  . '">';
	$out .= $sep . $link . '<img border="0" title="Добавить в Pikabu" alt="pikabu.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'liveinternet.png';
	$link = '<a rel="nofollow" href="http://www.liveinternet.ru/journal_post.php?action=n_add&cnurl=' . $post_link . '&cntitle=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Опубликовать в LiveInternet" alt="liveinternet.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'livejournal.png';
	$link = '<a rel="nofollow" href="http://www.livejournal.com/update.bml?event=' . $post_link . '&subject' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Опубликовать в LiveJournal" alt="livejournal.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'blogger.png';
	$link = '<a rel="nofollow" href="http://www.blogger.com/blog_this.pyra?t&u=' . $post_link . '&n=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Опубликовать в Blogger.com" alt="blogger.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	
	$img_src = 'google-bookmarks.png';
	$link = '<a rel="nofollow" href="http://www.google.com/bookmarks/mark?op=edit&amp;bkmk=' . $post_link . '&amp;title=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Сохранить закладку в Google" alt="google.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'yandex.png';
	$link = '<a rel="nofollow" href="http://zakladki.yandex.ru/newlink.xml?url=' . $post_link . '&name=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Добавить в Яндекс.Закладки" alt="yandex.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'memori.png';
	$link = '<a rel="nofollow" href="http://memori.ru/link/?sm=1&amp;u_data[url]=' . $post_link . '&amp;u_data[name]=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Сохранить закладку в Memori.ru" alt="memori.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	echo "\n<div class=\"dignity_add\"><span style=\"display: none\"><![CDATA[<noindex>]]></span>" . $out . "<span style=\"display: none\"><![CDATA[</noindex>]]></span></div>\n";
	
	return $args;
}

# end file
