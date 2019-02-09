<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 *
 * Форк плагина Addzakl
 *
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * Alexander Schilling
 * (c) http://alexanderschilling.net/
 *
 * Icons
 * (c) http://icondock.com/
 */

# функция автоподключения плагина
function dignity_share_autoload($args = array())
{
	if ( is_type('page') )
	{
		$options = mso_get_option('plugin_dignity_share', 'plugins', array());
	
		if (!isset($options['priory'])) $options['priory'] = 10;
		mso_hook_add('content_end', 'dignity_share_content_end', $options['priory']);
	}
}

# функция выполняется при деинсталяции плагина
function dignity_share_uninstall($args = array())
{	
	mso_delete_option('plugin_dignity_share', 'plugins'); // удалим созданные опции
	return $args;
}

function dignity_share_mso_options() 
{
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_dignity_share', 'plugins', 
		array(
			'size' => array(
							'type' => 'select', 
							'name' => 'Размеры иконок', 
							'description' => 'Выберите размеры иконок',
							'values' => '16 # 24',  // правила для select как в ini-файлах
							'default' => '16'
						),
			'text-do' => array(
							'type' => 'text', 
							'name' => 'Текст перед иконками', 
							'description' => 'Укажите произвольный текст перед иконками. Можно использовать HTML', 
							'default' => ''
						),
			'text-posle' => array(
							'type' => 'text', 
							'name' => 'Текст после иконок', 
							'description' => 'Укажите произвольный текст после иконок', 
							'default' => ''
						),	
								
			'priory' => array(
							'type' => 'text', 
							'name' => 'Приоритет блока', 
							'description' => 'Позволяет расположить блок до или после аналогичных. Используйте значения от 1 до 90. Чем больше значение, тем выше блок. По умолчанию значение равно 10.', 
							'default' => '10'
						),
			'temp' => array(
							'type' => 'info',
							'title' => t('Выберите какие кнопки следует отображать', 'plugins'),
							'text' => t('', 'plugins'), 
						),
							
						
			'twitter' => array(
							'type' => 'checkbox', 
							'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'dignity_share/images24/twitter.png"> twitter', 
							'description' => '', 
							'default' => '1'
						),
						
			'facebook' => array(
							'type' => 'checkbox', 
							'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'dignity_share/images24/facebook.png"> facebook', 
							'description' => '', 
							'default' => '1'
						),
			
			'vkontakte' => array(
							'type' => 'checkbox', 
							'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'dignity_share/images24/vkontakte.png"> vkontakte', 
							'description' => '', 
							'default' => '1'
						),
			
			'odnoklassniki' => array(
							'type' => 'checkbox', 
							'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'dignity_share/images24/odnoklassniki.png"> odnoklassniki', 
							'description' => '', 
							'default' => '1'
						),
			'liveinternet' => array(
							'type' => 'checkbox', 
							'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'dignity_share/images24/liveinternet.png"> liveinternet', 
							'description' => '', 
							'default' => '1'
						),
			
			'livejournal' => array(
							'type' => 'checkbox', 
							'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'dignity_share/images24/livejournal.png"> livejournal', 
							'description' => '', 
							'default' => '1'
						),
			
			'google-bookmarks' => array(
							'type' => 'checkbox', 
							'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'dignity_share/images24/google-bookmarks.png"> google-bookmarks', 
							'description' => '', 
							'default' => '1'
						),
			
			'yandex' => array(
							'type' => 'checkbox', 
							'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'dignity_share/images24/yandex.png"> yandex', 
							'description' => '', 
							'default' => '1'
						),
			
			'print' => array(
							'type' => 'checkbox', 
							'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'dignity_share/images24/print.png"> Распечатать', 
							'description' => '', 
							'default' => '1'
						),
			
			'sendto' => array(
							'type' => 'checkbox', 
							'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'dignity_share/images24/sendto.png"> Отправить на E-Mail', 
							'description' => '', 
							'default' => '1'
						),
			
			'reddit' => array(
							'type' => 'checkbox', 
							'name' => ' <img width="24" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'dignity_share/images24/reddit.png"> Добавить в Reddit', 
							'description' => '', 
							'default' => '1'
						),
			
			'gplusone' => array(
							'type' => 'checkbox', 
							'name' => ' <img width="38" height="24" align="absmiddle" src="' . getinfo('plugins_url') . 'dignity_share/images24/gplusone.png"> Google +1', 
							'description' => '', 
							'default' => '1'
						),
										
			),
		'Закладки на социальные сервисы', // титул
		'Укажите необходимые опции.'   // инфо
	);
}

# функции плагина
function dignity_share_content_end($args = array())
{
	global $page;
	
	$options = mso_get_option('plugin_dignity_share', 'plugins', array());
	
	$def_options = array(
		'size' => 16, 
		'text-do' => '', 
		'text-posle' => '', 
		'twitter' => 1, 
		'facebook' => 1, 
		'vkontakte' => 1, 
		'odnoklassniki' => 1, 
		'blogger' => 1, 
		'liveinternet' => 1, 
		'livejournal' => 1, 
		'google-bookmarks' => 1,
		'yandex' => 1,
		'print' => 1,
		'sendto' => 1,
		'reddit' => 1,
		'gplusone' => 1, 
		);
	
	$options = array_merge($def_options, $options);

	$size = (int) $options['size']; // размер икнонок
	
	$sep = ' ';  # разделитель мужду кнопками - можно указать свой
	
	# ширина и высота картинок
	$width_height = ' width="' . $size . '" height="' . $size . '"';  
	
	if ($size == 16) // если размер 16, то каталог /images/
		$path = getinfo('plugins_url') . 'dignity_share/images/'; # путь к картинкам
	else // каталог /imagesXX/
		$path = getinfo('plugins_url') . 'dignity_share/images' . $size . '/'; # путь к картинкам
		
	$post_title = urlencode ( stripslashes($page['page_title'] . ' - ' . mso_get_option('name_site', 'general') ) );
	$post_link = getinfo('siteurl') . mso_current_url();
	$out = '';
	
	if ($options['twitter'])
	{
		$img_src = 'twitter.png';
		$link = '<a rel="nofollow" href="http://twitter.com/home/?status=' . urlencode (stripslashes(mb_substr($page['page_title'], 0, 139 - mb_strlen($post_link, 'UTF8'), 'UTF8') . ' ' . $post_link)) . '">';
		$out .= $link . '<img title="Добавить в Twitter" alt="twitter.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';	
	}
	
	if ($options['facebook'])
	{
		$img_src = 'facebook.png';
		$link = '<a rel="nofollow" href="http://www.facebook.com/sharer.php?u=' . $post_link . '">';
		$out .= $sep . $link . '<img title="Поделиться в Facebook" alt="facebook.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';		
	}
	
	if ($options['vkontakte'])
	{	
		$img_src = 'vkontakte.png';
		$link = '<a rel="nofollow" href="http://vkontakte.ru/share.php?url=' . $post_link . '&amp;title=' . $post_title  . '">';
		$out .= $sep . $link . '<img title="Поделиться В Контакте" alt="vkontakte.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options['odnoklassniki'])
	{
		$img_src = 'odnoklassniki.png';
		$link = '<a rel="nofollow" href="http://www.odnoklassniki.ru/dk?st.cmd=addShare&amp;st._surl=' . $post_link . '&amp;title=' . $post_title .  '">';
		$out .= $sep . $link . '<img title="Добавить в Одноклассники" alt="odnoklassniki.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	
	}
	
	if ($options['blogger'])
	{
		$img_src = 'blogger.png';
		$link = '<a rel="nofollow" href="http://www.blogger.com/blog_this.pyra?t&amp;u=' . $post_link . '&amp;n=' . $post_title .  '">';
		$out .= $sep . $link . '<img title="Опубликовать в Blogger.com" alt="blogger.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options['liveinternet'])
	{
		$img_src = 'liveinternet.png';
		$link = '<a rel="nofollow" href="http://www.liveinternet.ru/journal_post.php?action=n_add&amp;cnurl=' . $post_link . '&amp;cntitle=' . $post_title .  '">';
		$out .= $sep . $link . '<img title="Опубликовать в LiveInternet" alt="liveinternet.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options['livejournal'])
	{
		$img_src = 'livejournal.png';
		$link = '<a rel="nofollow" href="http://www.livejournal.com/update.bml?event=' . $post_link . '&amp;subject=' . $post_title .  '">';
		$out .= $sep . $link . '<img title="Опубликовать в LiveJournal" alt="livejournal.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options['google-bookmarks'])
	{
		$img_src = 'google-bookmarks.png';
		$link = '<a rel="nofollow" href="http://www.google.com/bookmarks/mark?op=edit&amp;bkmk=' . $post_link . '&amp;title=' . $post_title .  '">';
		$out .= $sep . $link . '<img title="Сохранить закладку в Google" alt="google.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options['yandex'])
	{
		$img_src = 'yandex.png';
		$link = '<a rel="nofollow" href="http://zakladki.yandex.ru/newlink.xml?url=' . $post_link . '&amp;name=' . $post_title .  '">';
		$out .= $sep . $link . '<img title="Добавить в Яндекс.Закладки" alt="yandex.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options['reddit'])
	{
		$img_src = 'reddit.png';
		$link = '<a rel="nofollow" href="http://reddit.com/submit?url=' . $post_link . '&title=' . $post_title .  '">';
		$out .= $sep . $link . '<img title="Добавить в Reddit" alt="send to friend" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}

	if ($options['print'])
	{
		$img_src = 'print.png';
		$link = '<a rel="nofollow" href="#" onclick="print();return false">';
		$out .= $sep . $link . '<img title="Распечатать" alt="Распечатать" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options['sendto'])
	{
		$img_src = 'sendto.png';
		$link = '<a rel="nofollow" href="http://www.feedburner.com/fb/a/emailFlare?loc=ru_RU&itemTitle=' . $post_title . '&uri=' . $post_link .  '">';
		$out .= $sep . $link . '<img title="Отправить на E-Mail другу" alt="send to friend" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	}
	
	if ($options['gplusone'])
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
			
		$out .= $sep . '
		<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
		<div class="g-plusone" data-size="' . $sg . '" data-count="true"></div>
		<script type="text/javascript"> gapi.plusone.render("g-plusone", {"size": "' . $sg . '", "count": "true"}); </script>
		';
	}

	if ($out)
		echo NR . '<div class="addzakl">' . $options['text-do'] . $out . $options['text-posle'] . '</div>' . NR;
	
	return $args;
}

# end file