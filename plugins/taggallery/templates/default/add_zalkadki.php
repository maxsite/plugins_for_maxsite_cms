<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
if (function_exists('addzakl_content_end'))
{
	$options2 = mso_get_option('plugin_addzakl', 'plugins', array());
	
	$def_options = array(
		'size' => 16, 
		'text-do' => '', 
		'text-posle' => '', 
		
		'twitter' => 1, 
		'facebook' => 1, 
		'vkontakte' => 1, 
		'odnoklassniki' => 1, 
		'mail-ru' => 1, 
		'yaru' => 1, 
		'rutvit' => 1, 
		'myspace' => 1, 
		'buzz' => 1, 
		'technorati' => 1, 
		'digg' => 1, 
		'friendfeed' => 1, 
		'pikabu' => 1, 
		'blogger' => 1, 
		'liveinternet' => 1, 
		'livejournal' => 1, 
		'memori' => 1, 
		'google-bookmarks' => 1, 
		'bobrdobr' => 1, 
		'mister-wong' => 1, 
		'yahoo-bookmarks' => 1, 
		'yandex' => 1, 
		'delicious' => 1, 
		'gplusone' => 1, 
		);
	
	$options2 = array_merge($def_options, $options2);

	$size = (int) $options2['size']; // размер икнонок
	
	$sep = ' ';  # разделитель мужду кнопками - можно указать свой
	
	# ширина и высота картинок
	$width_height = ' width="' . $size . '" height="' . $size . '"';  
	
	if ($size == 16) // если размер 16, то каталог /images/
		$path = getinfo('plugins_url') . 'addzakl/images/'; # путь к картинкам
	else // каталог /imagesXX/
		$path = getinfo('plugins_url') . 'addzakl/images' . $size . '/'; # путь к картинкам
		
	$post_title = urlencode ( stripslashes($title . ' - ' . mso_get_option('name_site', 'general') ) );
	$post_link = getinfo('siteurl') . mso_current_url();
	
	$out_add_zakl = '';
	
	if ($options2['twitter'])
	{
		$img_src = 'twitter.png';
		$link = '<a rel="nofollow" href="http://twitter.com/home/?status=' . urlencode (stripslashes(mb_substr($title, 0, 139 - mb_strlen($post_link, 'UTF8'), 'UTF8') . ' ' . $post_link)) . '">';
		$out_add_zakl .= $link . '<img title="Добавить в Twitter" alt="twitter.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';	
	}
	
	if ($options2['facebook'])
	{
		$img_src = 'facebook.png';
		$link = '<a rel="nofollow" href="http://www.facebook.com/sharer.php?u=' . $post_link . '">';
		$out_add_zakl .= $sep . $link . '<img title="Поделиться в Facebook" alt="facebook.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';		
	}
	
	if ($options2['vkontakte'])
	{	
		$img_src = 'vkontakte.png';
		$link = '<a rel="nofollow" href="http://vkontakte.ru/share.php?url=' . $post_link . '&amp;title=' . $post_title  . '">';
		$out_add_zakl .= $sep . $link . '<img title="Поделиться В Контакте" alt="vkontakte.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options2['odnoklassniki'])
	{
		$img_src = 'odnoklassniki.png';
		$link = '<a rel="nofollow" href="http://www.odnoklassniki.ru/dk?st.cmd=addShare&amp;st._surl=' . $post_link . '&amp;title=' . $post_title .  '">';
		$out_add_zakl .= $sep . $link . '<img title="Добавить в Одноклассники" alt="odnoklassniki.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	
	}
	
	if ($options2['mail-ru'])
	{
		$img_src = 'mail-ru.png';
		$link = '<a rel="nofollow" href="http://connect.mail.ru/share?url=' . $post_link . '&amp;title=' . $post_title .  '">';
		$out_add_zakl .= $sep . $link . '<img title="Поделиться в Моем Мире@Mail.Ru" alt="mail.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	}
	
	if ($options2['yaru'])
	{
		$img_src = 'yaru.png';
		$link = '<a rel="nofollow" href="http://my.ya.ru/posts_add_link.xml?URL=' . $post_link . '&amp;title=' . $post_title .  '">';
		$out_add_zakl .= $sep . $link . '<img title="Поделиться в Я.ру" alt="ya.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options2['rutvit'])
	{
		$img_src = 'rutvit.png';
		$link = '<a rel="nofollow" href="http://rutvit.ru/tools/widgets/share/popup?url=' . $post_link . '&amp;title=' . $post_title .  '">';
		$out_add_zakl .= $sep . $link . '<img title="Добавить в РуТвит" alt="rutvit.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options2['myspace'])
	{
		$img_src = 'myspace.png';
		$link = '<a rel="nofollow" href="http://www.myspace.com/Modules/PostTo/Pages/?u=' . $post_link . '&amp;t=' . $post_title .  '">';
		$out_add_zakl .= $sep . $link . '<img title="Добавить в MySpace" alt="myspace.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options2['buzz'])
	{
		$img_src = 'buzz.png';
		$link = '<a rel="nofollow" href="http://www.google.com/buzz/post?message=' . $post_link . '&amp;url=' . $post_title . '&amp;srcURL=' . getinfo('siteurl') . '">';
		$out_add_zakl .= $sep . $link . '<img title="Добавить в Google Buzz" alt="Google Buzz" src="' . $path . $img_src  . '"' . $width_height . '></a>';		
	}
	
	if ($options2['technorati'])
	{
		$img_src = 'technorati.png';
		$link = '<a rel="nofollow" href="http://www.technorati.com/faves?add=' . $post_link . '">';
		$out_add_zakl .= $sep . $link . '<img title="Добавить в Technorati" alt="technorati.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options2['digg'])
	{
		$img_src = 'digg.png';
		$link = '<a rel="nofollow" href="http://digg.com/submit?url=' . $post_link .  '">';
		$out_add_zakl .= $sep . $link . '<img title="Добавить в Digg" alt="digg.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options2['friendfeed'])
	{
		$img_src = 'friendfeed.png';
		$link = '<a rel="nofollow" href="http://www.friendfeed.com/share?title=' . $post_link .  '">';
		$out_add_zakl .= $sep . $link . '<img title="Добавить в FriendFeed" alt="friendfeed.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options2['pikabu'])
	{
		$img_src = 'pikabu.png';
		$link = '<a rel="nofollow" href="http://pikabu.ru/add_story.php?story_url=' . $post_link . '&amp;title=' . $post_title .  '">';
		$out_add_zakl .= $sep . $link . '<img title="Добавить в Pikabu" alt="pikabu.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options2['blogger'])
	{
		$img_src = 'blogger.png';
		$link = '<a rel="nofollow" href="http://www.blogger.com/blog_this.pyra?t&amp;u=' . $post_link . '&amp;n=' . $post_title .  '">';
		$out_add_zakl .= $sep . $link . '<img title="Опубликовать в Blogger.com" alt="blogger.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options2['liveinternet'])
	{
		$img_src = 'liveinternet.png';
		$link = '<a rel="nofollow" href="http://www.liveinternet.ru/journal_post.php?action=n_add&amp;cnurl=' . $post_link . '&amp;cntitle=' . $post_title .  '">';
		$out_add_zakl .= $sep . $link . '<img title="Опубликовать в LiveInternet" alt="liveinternet.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options2['livejournal'])
	{
		$img_src = 'livejournal.png';
		$link = '<a rel="nofollow" href="http://www.livejournal.com/update.bml?event=' . $post_link . '&amp;subject=' . $post_title .  '">';
		$out_add_zakl .= $sep . $link . '<img title="Опубликовать в LiveJournal" alt="livejournal.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options2['memori'])
	{
		$img_src = 'memori.png';
		$link = '<a rel="nofollow" href="http://memori.ru/link/">';
		$out_add_zakl .= $sep . $link . '<img title="Сохранить закладку в Memori.ru" alt="memori.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	
	if ($options2['google-bookmarks'])
	{
		$img_src = 'google-bookmarks.png';
		$link = '<a rel="nofollow" href="http://www.google.com/bookmarks/mark?op=edit&amp;bkmk=' . $post_link . '&amp;title=' . $post_title .  '">';
		$out_add_zakl .= $sep . $link . '<img title="Сохранить закладку в Google" alt="google.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options2['bobrdobr'])
	{	
		$img_src = 'bobrdobr.png';
		$link = '<a rel="nofollow" href="http://bobrdobr.ru/addext.html?url=' . $post_link . '&amp;title=' . $post_title .  '">';
		$out_add_zakl .= $sep . $link . '<img title="Забобрить" alt="bobrdobr.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options2['mister-wong'])
	{
		$img_src = 'mister-wong.png';
		$link = '<a rel="nofollow" href="http://www.mister-wong.ru/index.php?action=addurl&amp;bm_url=' . $post_link . '&amp;bm_description=' . $post_title .  '">';
		$out_add_zakl .= $sep . $link . '<img title="Сохранить закладку в Мистер Вонг" alt="mister-wong.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options2['yahoo-bookmarks'])
	{
		$img_src = 'yahoo-bookmarks.png';
		$link = '<a rel="nofollow" href="http://bookmarks.yahoo.com/toolbar/savebm?u=' . $post_link . '&amp;t=' . $post_title .  '">';
		$out_add_zakl .= $sep . $link . '<img title="Добавить в Yahoo! Закладки" alt="yahoo.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options2['yandex'])
	{
		$img_src = 'yandex.png';
		$link = '<a rel="nofollow" href="http://zakladki.yandex.ru/newlink.xml?url=' . $post_link . '&amp;name=' . $post_title .  '">';
		$out_add_zakl .= $sep . $link . '<img title="Добавить в Яндекс.Закладки" alt="yandex.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}

	if ($options2['delicious'])
	{
		$img_src = 'delicious.png';
		$link = '<a rel="nofollow" href="http://del.icio.us/post?url=' . $post_link . '&amp;title=' . $post_title .  '">';
		$out_add_zakl .= $sep . $link . '<img title="Сохранить закладку в Delicious" alt="del.icio.us" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options2['gplusone'])
	{
		// гугл +1 gplusone
		if ($size == 16) 
		{
			$sg = 'small';
		}
		else 
		{
			$sg = 'standard';
		}
			
		$out_add_zakl .= $sep . '
		<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
		<div class="g-plusone" data-size="' . $sg . '" data-count="true"></div>
		<script type="text/javascript"> gapi.plusone.render("g-plusone", {"size": "' . $sg . '", "count": "true"}); </script>
		';
	}

		if ($out_add_zakl) $out .= NR . '<div class="addzakl">' . $options2['text-do'] . $out_add_zakl . $options2['text-posle'] . '</div>' . NR;
}	
	
?>