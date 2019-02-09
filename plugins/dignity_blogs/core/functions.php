<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_blogs (github)
 * License GNU GPL 2+
 */

# Общие функции плагина
# require_once(getinfo('plugins_dir') . 'dignity_blogs/core/functions.php');
# $blogs = new Blogs;

class Blogs
{
	// меню
	function menu()
	{
		 // загружаем опции
        $options = mso_get_option('plugin_dignity_blogs', 'plugins', array());
        if ( !isset($options['slug']) ) $options['slug'] = 'blogs';
        
        echo '<div class="blogs_tabs">';
	        echo '<ul class="blogs_tabs-nav">';
	        
	        if (mso_segment(2))
	        {
	            echo '<li class="elem"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_blogs/img/fav.png' . '"></span><span><a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Избранные', __FILE__) . '</a></span></li>';
	        }
	        else
	        {
	            echo '<li class="elem blogs_tabs-current"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_blogs/img/fav.png' . '"></span><span><a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Избранные', __FILE__) . '</a></span></li>';
	        }
	        
	        if (mso_segment(2) == 'all')
	        {
	            echo '<li class="elem blogs_tabs-current"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_blogs/img/all.png' . '"></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/all/' . '">' . t('Блоги', __FILE__) . '</a></span></li>';
	        }
	        else
	        {
	            echo '<li class="elem"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_blogs/img/all.png' . '"></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/all/' . '">' . t('Блоги', __FILE__) . '</a></span></li>';
	        }
	        
	        if (is_login_comuser())
			{
	           	if (mso_segment(2) == 'my')
	            {
	                echo '<li class="elem blogs_tabs-current"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_blogs/img/my.png' . '"></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/my/' . getinfo('comusers_id') . '">' . t('Мои записи', __FILE__) . '</a></span></li>';
	            }
	            else
	            {
	                 echo '<li class="elem"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_blogs/img/my.png' . '"></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/my/' . getinfo('comusers_id') . '">' . t('Мои записи', __FILE__) . '</a></span></li>';
	            }
	        }
	        
			if (mso_segment(2) == 'add')
	        {
	            echo '<li class="elem blogs_tabs-current"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_blogs/img/edit.png' . '"></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/add/' . '">' . t('Написать в блог!', __FILE__) . '</a></span></li>';
	        }
	        else
	        {
	            echo '<li class="elem"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_blogs/img/edit.png' . '"></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/add/' . '">' . t('Написать в блог!', __FILE__) . '</a></span></li>';
	        }
	        
			if (mso_segment(2) == 'new')
		    {
		        echo '<li class="elem blogs_tabs-current"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_blogs/img/new.png' . '"></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/new/' . '">' . t('Новые', __FILE__) . '</a></span></li>';
		    }
		    else
		    {
		        echo '<li class="elem"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_blogs/img/new.png' . '"></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/new/' . '">' . t('Новые', __FILE__) . '</a></span></li>';
		    }
			
			if (mso_segment(2) == 'comments')
		    {
		        echo '<li class="elem blogs_tabs-current"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_blogs/img/comments.png' . '"></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/comments/' . '">' . t('Комментарии', __FILE__) . '</a></span></li>';
		    }
		    else
		    {
		        echo '<li class="elem"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_blogs/img/comments.png' . '"></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/comments/' . '">' . t('Комментарии', __FILE__) . '</a></span></li>';
		    }
		
			echo '<li class="elem"><a href="' . getinfo('site_url') . $options['slug'] . '/rss/' . '"><img src="' . getinfo('plugins_url') . 'dignity_blogs/img/rss.png' . '" title="' . t('RSS лента', __FILE__) . '"></a></li>';
		
	        echo '</ul>';
        echo '</div>';
        echo '<br />';
	}

	function yandex_share($out='')
	{
		$out .= '<div class="blogs_social">' . t('Понравилась статья? Расскажи о ней друзьям в социальных сетях, им тоже должно понравиться!', __FILE__) . '</div>';

		$out .= '<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
			<div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="icon" data-yashareQuickServices="vkontakte,facebook,twitter,gplus"></div>';
		
		return $out;
	}

	// подсчет количество введеных символов
	function char_count_js()
	{
		echo '<script type="text/javascript" src="' . getinfo('plugins_url') . 'dignity_blogs/js/charCount.js' . '"></script>';	
	}

	// bb редактор
	function editor()
	{
		// подключаем js от редактора markitup
		echo '<script src="'. getinfo('plugins_url') . 'dignity_blogs/js/jquery.markitup.js"></script>';

		// подключаем стили редактора
		echo '<link rel="stylesheet" href="'. getinfo('plugins_url') . 'dignity_blogs/css/editor.css">';
	 
		echo "<script type=\"text/javascript\" >
			var dignity_blogs_editor_settings = {
			
			nameSpace:'bbcode',
			
			markupSet:[
				{name:'Полужирный', openWith:'[b]', closeWith:'[/b]', className:'bold', key:'B'},
				{name:'Курсив', openWith:'[i]', closeWith:'[/i]', className:'italic', key:'I'},
				{name:'Подчеркнутый', openWith:'[u]', closeWith:'[/u]', className:'underline', key:'U'},
				{name:'Зачеркнутый', openWith:'[s]', closeWith:'[/s]', className:'stroke', key:'S'},
				{name:'Заголовок1', openWith:'[h1]', closeWith:'[/h1]', className:'h1'},
				{name:'Заголовок2', openWith:'[h2]', closeWith:'[/h2]', className:'h2'},
				{name:'Заголовок3', openWith:'[h3]', closeWith:'[/h3]', className:'h3'},
				{name:'По левому краю', openWith:'[left]', closeWith:'[/left]', className:'left'},
				{name:'По центру', openWith:'[center]', closeWith:'[/center]', className:'center'},
				{name:'По правому краю', openWith:'[right]', closeWith:'[/right]', className:'right'},
				{name:'По ширине', openWith:'[justify]', closeWith:'[/justify]', className:'justify'},
				{name:'Размер текста', openWith:'[size=]', closeWith:'[/size]', className:'text_smallcaps'},
				{name:'Цвет', openWith:'[color=]', closeWith:'[/color]', className:'colors'},
				{name:'Принудительный перенос', replaceWith:'[br]', className:'br'},
				{name:'Преформатированный текст', openWith:'[pre]', closeWith:'[/pre]', className:'pre'},
				{name:'Цитата', openWith:'[quote]', closeWith:'[/quote]', className:'quote'},
				{name:'Код', openBlockWith:'[code]', closeBlockWith:'[/code]', className:'code'}, 
				{name:'Изображение', openWith:'[img]', closeWith:'[/img]', className:'picture'},
				{name:'Ссылка', openBlockWith:'[url]', closeBlockWith:'[/url]', className:'link'},
				{name:'Видео', openBlockWith:'[video]', closeBlockWith:'[/video]', className:'video'},
			],
			
			}
		</script>";
	 
		echo '<script type="text/javascript" >
				$(document).ready(function() {
				$(".markItUp").markItUp(dignity_blogs_editor_settings);
				});
		</script>';
	}

	// bb редактор для комментарий
	function comments_editor()
	{
		// подключаем js от редактора markitup
		echo '<script src="'. getinfo('plugins_url') . 'dignity_blogs/js/jquery.markitup.js"></script>';

		// подключаем стили редактора
		echo '<link rel="stylesheet" href="'. getinfo('plugins_url') . 'dignity_blogs/css/editor.css">';
	 
		echo "<script type=\"text/javascript\" >
			var dignity_blogs_comments_editor_settings = {
			
			nameSpace:'bbcode',
			
			markupSet:[
				{name:'Полужирный', openWith:'[b]', closeWith:'[/b]', className:'bold', key:'B'},
				{name:'Курсив', openWith:'[i]', closeWith:'[/i]', className:'italic', key:'I'},
				{name:'Подчеркнутый', openWith:'[u]', closeWith:'[/u]', className:'underline', key:'U'},
				{name:'Зачеркнутый', openWith:'[s]', closeWith:'[/s]', className:'stroke', key:'S'},
				{name:'Цитата', openWith:'[quote]', closeWith:'[/quote]', className:'quote'},
				{name:'Код', openBlockWith:'[code]', closeBlockWith:'[/code]', className:'code'},
			],
			
			}
		</script>";
	 
		echo '<script type="text/javascript" >
				$(document).ready(function() {
				$(".markItUp").markItUp(dignity_blogs_comments_editor_settings);
				});
		</script>';
	}

	// парсер bb-code -> html
	function bb_parser($content='')
	{
		// trim|xss|strip_tags|htmlspecialchars
		$content = mso_clean_str($content, 'trim|xss|strip_tags');

		// переопределяем bb-тэги и добавляем новые
        $preg = array(
			// универсальный тэг для видео
			'~\[video\](.*?)\[\/video\]~si' => '<iframe width="640" height="360" src="$1" frameborder="0"></iframe>',

			// опасный тэг, позвоялет исполнять html и js так что блокируем его
			'~\[html\](.*?)\[\/html\]~si' => 'Заблокировано!',

			// тэг позволяет исполнять php, если вклечен плагин "run_php", поэтому блокируем его
			'~\[php\](.*?)\[\/php\]~si' => 'Заблокировано!',
		);

		$content = preg_replace(array_keys($preg), array_values($preg), $content);

		$content = str_replace(chr(10), "<br />", $content);
		$content = str_replace(chr(13), "", $content);
		$content = mso_hook('content', $content);

		$content = mso_hook('content_auto_tag', $content);
		$content = mso_hook('content_balance_tags', $content);	

		return $content;
	}
}

#end of file
