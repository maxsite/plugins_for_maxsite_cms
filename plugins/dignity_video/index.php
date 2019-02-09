<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_video (github)
 * License GNU GPL 2+
 */

function dignity_video_autoload()
{
	mso_hook_add('admin_init', 'dignity_video_admin_init');
	mso_hook_add('custom_page_404', 'dignity_video_custom_page_404');

	// для вывода количеств видео записей и комментарий
	mso_hook_add('users_add_out', 'dignity_video_users_add_out', '80');
	
	// регестируем виджет
	mso_register_widget('dignity_video_category_widget', t('Видео категории', __FILE__));
	mso_register_widget('dignity_video_new_widget', t('Новые видео', __FILE__));
}

function dignity_video_activate($args = array())
{	
	mso_create_allow('dignity_video_edit', t('Админ-доступ к', 'plugins') . ' ' . t('«Видео»', __FILE__));
	
	// доступ к CI
        $CI = & get_instance();	

	// создаём табилицу для записей
	if ( !$CI->db->table_exists('dignity_video'))
	{
		$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
		$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
		$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate;
		
		$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "dignity_video (
		dignity_video_id bigint(20) NOT NULL auto_increment,
		dignity_video_title varchar(100) NOT NULL default '',
		dignity_video_keywords longtext NOT NULL default '',
		dignity_video_description longtext NOT NULL default '',
		dignity_video_text longtext NOT NULL default '',
		dignity_video_datecreate datetime NOT NULL default '0000-00-00 00:00:00',
		dignity_video_dateupdate datetime NOT NULL default '0000-00-00 00:00:00',
		dignity_video_approved varchar(1) NOT NULL default '',
		dignity_video_comments varchar(1) NOT NULL default '',
		dignity_video_rss varchar(1) NOT NULL default '',
		dignity_video_ontop varchar(1) NOT NULL default '',
		dignity_video_views bigint(20) NOT NULL default '0',
		dignity_video_comuser_id bigint(20) NOT NULL default '0',
		dignity_video_user_id bigint(20) NOT NULL default '0',
		dignity_video_category bigint(20) NOT NULL default '0',
		dignity_video_rating bigint(20) NOT NULL default '0',
		PRIMARY KEY (dignity_video_id)
		)" . $charset_collate;
		
		$CI->db->query($sql);
	}
	
	// создаём табилицу для ответов
	if ( !$CI->db->table_exists('dignity_video_comments'))
	{
		$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
		$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
		$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate;
		
		$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "dignity_video_comments (
		dignity_video_comments_id bigint(20) NOT NULL auto_increment,
		dignity_video_comments_text longtext NOT NULL default '',
		dignity_video_comments_thema_id bigint(20) NOT NULL default '0',
		dignity_video_comments_datecreate datetime NOT NULL default '0000-00-00 00:00:00',
		dignity_video_comments_dateupdate datetime NOT NULL default '0000-00-00 00:00:00',
		dignity_video_comments_approved varchar(1) NOT NULL default '',
		dignity_video_comments_comuser_id bigint(20) NOT NULL default '0',
		dignity_video_comments_user_id bigint(20) NOT NULL default '0',
		dignity_video_comments_rating bigint(20) NOT NULL default '0',
		PRIMARY KEY (dignity_video_comments_id)
		)" . $charset_collate;
		
		$CI->db->query($sql);
	}
	
	// создаём табилицу для категорий
	if ( !$CI->db->table_exists('dignity_video_category'))
	{
		$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
		$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
		$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate;
		
		$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "dignity_video_category (
		dignity_video_category_id bigint(20) NOT NULL auto_increment,
		dignity_video_category_name longtext NOT NULL default '',
		dignity_video_category_description longtext NOT NULL default '',
		dignity_video_category_position bigint(20) NOT NULL default '0',
		dignity_video_category_parentid bigint(20) NOT NULL default '0',
		PRIMARY KEY (dignity_video_category_id)
		)" . $charset_collate;
		
		$CI->db->query($sql);
	}

	return $args;
}

function dignity_video_uninstall($args = array())
{	
	mso_delete_option('plugin_dignity_video', 'plugins');
	mso_remove_allow('dignity_video_edit');
	
	// получааем доступ к CI
	$CI = &get_instance();
	
	$CI->load->dbforge();
	
	// удаляем таблицы
	$CI->dbforge->drop_table('dignity_video');
	$CI->dbforge->drop_table('dignity_video_comments');
	$CI->dbforge->drop_table('dignity_video_category');
	
	// удаляем настройки виджета
	mso_delete_option_mask('dignity_video_category_widget_', 'plugins');
	mso_delete_option_mask('dignity_video_new_widget_', 'plugins');

	return $args;
}

# функция, которая берет настройки из опций виджетов
function dignity_video_new_widget($num = 1) 
{
	$widget = 'dignity_video_new_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	return dignity_video_new_widget_custom($options, $num);
}

# функции плагина
function dignity_video_new_widget_custom($options = array(), $num = 1)
{
	$out = '';
	
	// загружаем опции
	$options = mso_get_option('plugin_blog_plugins', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'video';
	
	// добавляем заголовок «Категории»
	$out .= mso_get_val('widget_header_start', '<h2 class="box"><span>') . t('Новые видео', __FILE__) . mso_get_val('widget_header_end', '</span></h2>');
        
	// получаем доступ к CI
	$CI = & get_instance();
	
	// берём данные из базы
	$CI->db->from('dignity_video');
	$CI->db->limit(5);
	$CI->db->order_by('dignity_video_datecreate', 'desc');
	$query = $CI->db->get();
	
	// если есть что выводить
	if ($query->num_rows() > 0)	
	{	
		$entrys = $query->result_array();
		$catout = '';
		
		foreach ($entrys as $entry) 
		{
			// выводим названия категории и количество записей в ней
			$catout .= '<li><a href="' . getinfo('siteurl') . $options['slug'] . '/view/'
				. $entry['dignity_video_id'] . '">' . $entry['dignity_video_title'] . '</a>' . '</li>';
		}
		
		$out .= '<ul>';
		$out .= $catout;
		$out .= '</ul>';
		
		$out .= '<a href="' . getinfo('siteurl') . $options['slug'] . '">' . t('Все видео»', __FILE__) . '</a>';
	}
	else
	{
		$out .= t('Нет новых видео.', __FILE__);
	}
	
	return $out;	
}

# функция, которая берет настройки из опций виджетов
function dignity_video_category_widget($num = 1) 
{
	$widget = 'dignity_video_category_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	return dignity_video_category_widget_custom($options, $num);
}

# функции плагина
function dignity_video_category_widget_custom($options = array(), $num = 1)
{
	// получаем доступ к CI
	$CI = & get_instance();
	
	// обьявляем переменую
	$out = '';
	
	// загружаем опции
	$options = mso_get_option('plugin_blog_plugins', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'video';
	
	// добавляем заголовок «Категории»
	$out .= mso_get_val('widget_header_start', '<h2 class="box"><span>') . t('Категории', __FILE__) . mso_get_val('widget_header_end', '</span></h2>');
	
	// берём данные из базы
	$CI->db->from('dignity_video_category');
	$CI->db->order_by('dignity_video_category_position', 'asc');
	$query = $CI->db->get();
	
	// если есть что выводить
	if ($query->num_rows() > 0)	
	{	
		$entrys = $query->result_array();
		
		// обьявлем переменую
                $catout = '';
		
		// цикл
		foreach ($entrys as $entry) 
		{
			// узнаем количество записей в категории
			$CI->db->where('dignity_video_approved', 1);
			$CI->db->where('dignity_video_category', $entry['dignity_video_category_id']);
			$CI->db->from('dignity_video');
			$entry_in_cat = $CI->db->count_all_results();
			
			if ($entry_in_cat > 0)
			{
				// выводим названия категории и количество записей в ней
				$catout .= '<li><a href="' . getinfo('siteurl') . $options['slug'] . '/category/'
				    . $entry['dignity_video_category_id'] . '">' . $entry['dignity_video_category_name'] . '</a>' . ' (' . $entry_in_cat . ') ' . '</li>';
			}
		}
		
		// начиаем новый список
		$out .= '<ul>';
		
		// выводим назавания категорий и количетсов записей
		$out .= $catout;
		
		// количетсов записей всего
		$CI->db->where('dignity_video_approved', true);
		$CI->db->from('dignity_video');
		$all_entry_in_cat = $CI->db->count_all_results();
		
		// добавляем ссылку «все видео»
		$out .= '<li><a href="' . getinfo('site_url') . $options['slug'] . '/' . '">' . t('Все видео', __FILE__) . '</a>' . ' (' . $all_entry_in_cat . ') ' . '</li>';
		
		// заканчиваем список
		$out .= '</ul>';
	}
	
	return $out;	
}

function dignity_video_admin_init($args = array()) 
{
	if ( !mso_check_allow('dignity_video_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'dignity_video';
	
	mso_admin_menu_add('plugins', $this_plugin_url, t('Видео', __FILE__));
	mso_admin_url_hook ($this_plugin_url, 'dignity_video_admin_page');
	
	return $args;
}

function dignity_video_admin_page($args = array()) 
{
	if ( !mso_check_allow('dignity_video_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Видео', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Видео', __FILE__) . ' - " . $args; ' );

	// редактировать (админ)
	if ( mso_segment(3) == 'edit_comments') require(getinfo('plugins_dir') . 'dignity_video/admin/edit_comments.php');
	elseif ( mso_segment(3) == 'editone_comment') require(getinfo('plugins_dir') . 'dignity_video/admin/editone_comment.php');
	elseif ( mso_segment(3) == 'edit_video') require(getinfo('plugins_dir') . 'dignity_video/admin/edit_video.php');
	elseif ( mso_segment(3) == 'editone_video') require(getinfo('plugins_dir') . 'dignity_video/admin/editone_video.php');
	
	else require(getinfo('plugins_dir') . 'dignity_video/admin.php');
}

function dignity_video_custom_page_404($args = false)
{
	$options = mso_get_option('plugin_dignity_video', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'video';
   
	if ( mso_segment(1)==$options['slug'] )
	{
		if(mso_segment(2) == 'add')
		{
			// открываем add
			require( getinfo('plugins_dir') . 'dignity_video/video_add.php' );
		}
		elseif(mso_segment(2) == 'edit')
		{
			// открываем edit
			require( getinfo('plugins_dir') . 'dignity_video/video_edit.php' );
		}
		elseif(mso_segment(2) == 'view')
		{
			// открываем view
			require( getinfo('plugins_dir') . 'dignity_video/view.php' );
		}
		elseif(mso_segment(2) == 'category')
		{
			// открываем view
			require( getinfo('plugins_dir') . 'dignity_video/category.php' );
		}
		elseif(mso_segment(2) == 'my')
		{
			// открываем view
			require( getinfo('plugins_dir') . 'dignity_video/my.php' );
		}
		elseif(mso_segment(2) == 'rss')
		{
			// открываем view
			require( getinfo('plugins_dir') . 'dignity_video/rss.php' );
		}
		elseif(mso_segment(2) == 'new')
		{
			// открываем view
			require( getinfo('plugins_dir') . 'dignity_video/new.php' );
		}
		elseif(mso_segment(2) == 'comments')
		{
			// открываем view
			require( getinfo('plugins_dir') . 'dignity_video/comments.php' );
		}
		elseif(mso_segment(2) == 'all_author')
		{
			// открываем view
			require( getinfo('plugins_dir') . 'dignity_video/all_author.php' );
		}
		elseif(mso_segment(2) == 'all_one_author')
		{
			// открываем view
			require( getinfo('plugins_dir') . 'dignity_video/all_one_author.php' );
		}
		else
		{
			// открываем
			require( getinfo('plugins_dir') . 'dignity_video/video.php' ) ;
		}
		
		return true;
		
	}

   return $args;
}

// парсер bb-code -> html

function video_cleantext(&$content)
{
        // защита от xss
        mso_xss_clean($content);
    
        // массив
        $preg = array(
                    
                // жирный
                '~\[b\](.*?)\[\/b\]~si' => '<strong>$1</strong>',
                    
                // курсив
                '~\[i\](.*?)\[\/i\]~si' => '<i>$1</i>',
                    
                // подчёркнутый
                '~\[u\](.*?)\[\/u\]~si' => '<u>$1</u>',
                
                // зачёркнутый
                '~\[s\](.*?)\[\/s\]~si' => '<s>$1</s>',
                
                // пренудительный перенос
                '~\[br\]~si' => '<br>',
                
                // ссылка
                '~\[url\](.*?)\[\/url\]~si' => '<a href="$1" rel="nofollow">$1</a>',
                '~\[url=(.[^ ]*?)\](.*?)\[\/url\]~si' => '<a href="$1" rel="nofollow">$2</a>',
                
                // youtube (оставил для совместимости)
                '~\[youtube\](.*?)\[\/youtube\]~si' => '<iframe width="640" height="360" src="http://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>',
                
                // вконтакте (оставил для совместимости)
                '~\[vk\](.*?)\[\/vk\]~si' => '<iframe src="$1" width="640" height="360" frameborder="0"></iframe>',
				
				// vimeo (оставил для совместимости)
				'~\[vimeo\](.*?)\[\/vimeo\]~si' => '<iframe src="http://player.vimeo.com/video/$1" width="640" height="360" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>',

				// yandex-video (оставил для совместимости)
				'~\[yavideo\](.*?)\[\/yavideo\]~si' => '<iframe width="640" height="360" frameborder="0" src="$1"></iframe>',

				// новый тэг для видео
				'~\[video\](.*?)\[\/video\]~si' => '<iframe width="640" height="360" src="$1"></iframe>',
                                            
                // переносы                                        
                '~\n~' => '<br/>',
                
            );
            
            $content = preg_replace(array_keys($preg), array_values($preg), $content);
            
    return $content;

}

// подключаем редактор markitup и задаём настройки
function dignity_video_editor()
{
 
	// подключаем js от редактора markitup
	echo '<script src="'. getinfo('plugins_url') . 'dignity_video/js/jquery.markitup.js"></script>';

	// подключаем стили
	echo '<link rel="stylesheet" href="'. getinfo('plugins_url') . 'dignity_video/css/editor.css">';
 
	echo "<script type=\"text/javascript\" >
		var dignity_video_editor_settings = {
		
		nameSpace:'bbcode',
		
		markupSet:[
			{name:'Полужирный', openWith:'[b]', closeWith:'[/b]', className:'bold', key:'B'},
			{name:'Курсив', openWith:'[i]', closeWith:'[/i]', className:'italic', key:'I'},
			{name:'Подчеркнутый', openWith:'[u]', closeWith:'[/u]', className:'underline', key:'U'},
			{name:'Зачеркнутый', openWith:'[s]', closeWith:'[/s]', className:'stroke', key:'S'},
			{name:'Ссылка', openBlockWith:'[url]', closeBlockWith:'[/url]', className:'link'},
			{name:'Видео', openBlockWith:'[video]', closeBlockWith:'[/video]', className:'video'},
		],
		
		}
	</script>";
 
	echo '<script type="text/javascript" >
			$(document).ready(function() {
			$(".markItUp").markItUp(dignity_video_editor_settings);
			});
	</script>';
	
}

function video_menu()
{
        
        // загружаем опции
        $options = mso_get_option('plugin_dignity_video', 'plugins', array());
        if ( !isset($options['slug']) ) $options['slug'] = 'video';
        
    	echo '<div class="video_tabs">';
		    echo '<ul class="video_tabs-nav">';
		        
		    if (mso_segment(2))
		    {
		        echo '<li class="elem"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_video/img/all.png' . '" alt=""></span><span><a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Все видео', __FILE__) . '</a></span></li>';
		    }
		    else
		    {
		        echo '<li class="elem video_tabs-current"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_video/img/all.png' . '" alt=""></span><span><span><a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Все видео', __FILE__) . '</a></span></li>';
		    }

		    // все авторы
			if (mso_segment(2) == 'all_author')
		        {
		            echo '<li class="elem video_tabs-current"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_video/img/user.png' . '" alt=""></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/all_author' . '">' . t('Авторы', __FILE__) . '</a></span></li>';
		        }
		        else
		        {
		            echo '<li class="elem"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_video/img/user.png' . '" alt=""></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/all_author' . '">' . t('Авторы', __FILE__) . '</a></span></li>';
		        }
			
			// новые видео записи
			if (mso_segment(2) == 'new')
		        {
		            echo '<li class="elem video_tabs-current"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_video/img/new.png' . '" alt=""></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/new' . '">' . t('Новые', __FILE__) . '</a></span></li>';
		        }
		        else
		        {
		            echo '<li class="elem"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_video/img/new.png' . '" alt=""></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/new' . '">' . t('Новые', __FILE__) . '</a></span></li>';
		        }
			
			// если комментарии
			if (mso_segment(2) == 'comments')
		        {
		            echo '<li class="elem video_tabs-current"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_video/img/comments.png' . '" alt=""></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/comments' . '">' . t('Комментарии', __FILE__) . '</a></span></li>';
		        }
		        else
		        {
		            echo '<li class="elem"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_video/img/comments.png' . '" alt=""></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/comments' . '">' . t('Комментарии', __FILE__) . '</a></span></li>';
		        }
		        
		    if (is_login_comuser())
			{

				// если добавить
				if (mso_segment(2) == 'add')
				{
					echo '<li class="elem video_tabs-current"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_video/img/edit.png' . '" alt=""></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/add/' . '">' . t('Добавить видео', __FILE__) . '</a></span></li>';
				}
				else
				{
					echo '<li class="elem"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_video/img/edit.png' . '" alt=""></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/add/' . '">' . t('Добавить видео', __FILE__) . '</a></span></li>';
				}

				// если мои записи
				if (mso_segment(2) == 'my')
				{
					echo '<li class="elem video_tabs-current"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_video/img/my.png' . '" alt=""></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/my/' . getinfo('comusers_id') . '">' . t('Мои видео', __FILE__) . '</a></span></li>';
				}
				else
				{
					echo '<li class="elem"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_video/img/my.png' . '" alt=""></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/my/' . getinfo('comusers_id') . '">' . t('Мои видео', __FILE__) . '</a></span></li>';
				}
		    }
			
			echo '<li class="elem"><span style="padding-right:0px;"><img src="' . getinfo('plugins_url') . 'dignity_video/img/rss.png' . '" alt="" title="' . t('RSS', __FILE__) . '"></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/rss/' . '"></a></span></li>';
		        
		    echo '</ul>';
    echo '</div>';
    echo '<br>';
}

function video_not_found()
{
	// если страницы не существует, выводим ошибку
	echo '<h1>' . tf('404 - несуществующая страница') . '</h1>';
	echo '<p>' . tf('Извините по вашему запросу ничего не найдено!') . '</p>';
	echo mso_hook('page_404');
}

// подключаем css стили
mso_hook_add('head', 'video_style_css');

function video_style_css($a = array())
{
	if (file_exists(getinfo('plugins_url') . 'dignity_video/css/custom.css'))
	{
		$css = getinfo('plugins_url') . 'dignity_video/css/custom.css';
	} 
	else $css = getinfo('plugins_url') . 'dignity_video/css/style.css';
		
	echo '<link rel="stylesheet" href="' . $css . '">' . NR;
	
	return $a;
}

// функция хука users_add_out
// выводит количество видео записей и комментарий на странице комюзера
function dignity_video_users_add_out($comuser = array())
{
	// доступ к CodeIgniter
	$CI = & get_instance();

	// загружаем опции
	$options = mso_get_option('plugin_dignity_video', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'video';

	echo '<h2 style="padding: 3px; border-bottom: 1px solid #DDD;">' . t('Активность в видео', __FILE__) . '</h2>';
		
	$CI->db->from('dignity_video');
	$CI->db->where('dignity_video_approved', '1');
	$CI->db->where('dignity_video_comuser_id', mso_segment(2));
	$video_entry = $CI->db->count_all_results();

	// если больше одной, то выводим ссылку на все видео записи комюзера
    if ($video_entry >= 1)
    {
    	$entry_url = '<a href="' . getinfo('site_url') . $options['slug'] . '/all_one_author/' . mso_segment(2) . '">' . $video_entry . '</a>';
    }
    else
    {
    	$entry_url = $video_entry;
    }
		
	echo '<p style="padding-left:20px;">' . '<strong>' . t('Публикаций:', __FILE__) . '</strong> ' . $entry_url . '</p>';
		
	$CI->db->from('dignity_video_comments');
	$CI->db->where('dignity_video_comments_approved', '1');
	$CI->db->where('dignity_video_comments_comuser_id', mso_segment(2));
	$video_comments = $CI->db->count_all_results();
		
	echo '<p style="padding-left:20px;">' . '<strong>' . t('Комментарий:', __FILE__) . '</strong> ' . $video_comments . '</p>';

	return $comuser;

}

#end of file
