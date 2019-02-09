<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 *
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 *
 */

function dignity_soft_autoload()
{
	mso_hook_add('admin_init', 'dignity_soft_admin_init');
	mso_hook_add('custom_page_404', 'dignity_soft_custom_page_404');

	// для вывода количества статей и комментарий
	mso_hook_add('users_add_out', 'dignity_soft_users_add_out', '60');
	
	// подключаем плагин jquery
	mso_hook_add('head','soft_char_count_js_head');
	
	// регестируем виджет
	mso_register_widget('dignity_soft_category_widget', t('Категории приложений', __FILE__));
	mso_register_widget('dignity_soft_new_widget', t('Актуальные приложения', __FILE__));
}

function dignity_soft_activate($args = array())
{	
	mso_create_allow('dignity_soft_edit', t('Админ-доступ к', 'plugins') . ' ' . t('«Софт»', __FILE__));
	
	// доступ к CI
        $CI = & get_instance();	

	// создаём табилицу для записей
	if ( !$CI->db->table_exists('dignity_soft'))
	{
		$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
		$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
		$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate;
		
		$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "dignity_soft (
		dignity_soft_id bigint(20) NOT NULL auto_increment,
		dignity_soft_title varchar(100) NOT NULL default '',
		dignity_soft_keywords longtext NOT NULL default '',
		dignity_soft_description longtext NOT NULL default '',
		dignity_soft_cuttext longtext NOT NULL default '',
		dignity_soft_text longtext NOT NULL default '',
		dignity_soft_weblink longtext NOT NULL default '',
		dignity_soft_os bigint(20) NOT NULL default '0',
		dignity_soft_datecreate datetime NOT NULL default '0000-00-00 00:00:00',
		dignity_soft_dateupdate datetime NOT NULL default '0000-00-00 00:00:00',
		dignity_soft_approved varchar(1) NOT NULL default '',
		dignity_soft_comments varchar(1) NOT NULL default '',
		dignity_soft_rss varchar(1) NOT NULL default '',
		dignity_soft_ontop varchar(1) NOT NULL default '',
		dignity_soft_views bigint(20) NOT NULL default '0',
		dignity_soft_license bigint(20) NOT NULL default '0',
		dignity_soft_comuser_id bigint(20) NOT NULL default '0',
		dignity_soft_user_id bigint(20) NOT NULL default '0',
		dignity_soft_category bigint(20) NOT NULL default '0',
		dignity_soft_rating bigint(20) NOT NULL default '0',
		PRIMARY KEY (dignity_soft_id)
		)" . $charset_collate;
		
		$CI->db->query($sql);
	}
	
	// создаём табилицу для ответов
	if ( !$CI->db->table_exists('dignity_soft_comments'))
	{
		$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
		$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
		$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate;
		
		$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "dignity_soft_comments (
		dignity_soft_comments_id bigint(20) NOT NULL auto_increment,
		dignity_soft_comments_text longtext NOT NULL default '',
		dignity_soft_comments_thema_id bigint(20) NOT NULL default '0',
		dignity_soft_comments_datecreate datetime NOT NULL default '0000-00-00 00:00:00',
		dignity_soft_comments_dateupdate datetime NOT NULL default '0000-00-00 00:00:00',
		dignity_soft_comments_approved varchar(1) NOT NULL default '',
		dignity_soft_comments_comuser_id bigint(20) NOT NULL default '0',
		dignity_soft_comments_user_id bigint(20) NOT NULL default '0',
		dignity_soft_comments_rating bigint(20) NOT NULL default '0',
		PRIMARY KEY (dignity_soft_comments_id)
		)" . $charset_collate;
		
		$CI->db->query($sql);
	}
	
	// создаём табилицу для категорий
	if ( !$CI->db->table_exists('dignity_soft_category'))
	{
		$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
		$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
		$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate;
		
		$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "dignity_soft_category (
		dignity_soft_category_id bigint(20) NOT NULL auto_increment,
		dignity_soft_category_name longtext NOT NULL default '',
		dignity_soft_category_description longtext NOT NULL default '',
		dignity_soft_category_position bigint(20) NOT NULL default '0',
		PRIMARY KEY (dignity_soft_category_id)
		)" . $charset_collate;
		
		$CI->db->query($sql);
	}

	return $args;
}

function dignity_soft_uninstall($args = array())
{	
	mso_delete_option('plugin_dignity_soft', 'plugins');
	mso_remove_allow('dignity_soft_edit');
	
	// получааем доступ к CI
	$CI = &get_instance();
	
	$CI->load->dbforge();
	
	// удаляем таблицы
	$CI->dbforge->drop_table('dignity_soft');
	$CI->dbforge->drop_table('dignity_soft_comments');
	$CI->dbforge->drop_table('dignity_soft_category');
	
	// удаляем настройки виджета
	mso_delete_option_mask('dignity_soft_category_widget_', 'plugins');
	mso_delete_option_mask('dignity_soft_new_widget_', 'plugins');

	return $args;
}

# функция, которая берет настройки из опций виджетов
function dignity_soft_new_widget($num = 1) 
{
	$widget = 'dignity_soft_new_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	return dignity_soft_new_widget_custom($options, $num);
}

# функции плагина
function dignity_soft_new_widget_custom($options = array(), $num = 1)
{
	$out = '';
	
	// загружаем опции
	$options = mso_get_option('plugin_blog_plugins', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'soft';
	
	// добавляем заголовок «Категории»
        $out .= mso_get_val('widget_header_start', '<h2 class="box"><span>') . t('Актуальные приложения', __FILE__) . mso_get_val('widget_header_end', '</span></h2>');
	
	// получаем доступ к CI
	$CI = & get_instance();
	
	// берём данные из базы
	$CI->db->from('dignity_soft');
	$CI->db->limit(5);
	$CI->db->order_by('dignity_soft_datecreate', 'desc');
	$query = $CI->db->get();
	
	// если есть что выводить
	if ($query->num_rows() > 0)	
	{	
		$entrys = $query->result_array();
		
		// обьявлем переменую
		$catout = '';
		
		foreach ($entrys as $entry) 
		{
			// выводим названия категории и количество записей в ней
			$catout .= '<li><a href="' . getinfo('siteurl') . $options['slug'] . '/view/'
				 . $entry['dignity_soft_id'] . '">' . $entry['dignity_soft_title'] . '</a>' . '</li>';
		}
		
		// начиаем новый список
		$out .= '<ul>';
		
		// выводим назавания категорий и количетсов записей
		$out .= $catout;
	
		// заканчиваем список
		$out .= '</ul>';
		
		$out .= '<a href="' . getinfo('siteurl') . $options['slug'] . '">' . t('Все приложения»', __FILE__) . '</a>';
	}
	else
	{
		$out .= t('Нет новых приложений.', __FILE__);
	}
	
	return $out;	
}

# функция, которая берет настройки из опций виджетов
function dignity_soft_category_widget($num = 1) 
{
	$widget = 'dignity_soft_category_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	return dignity_soft_category_widget_custom($options, $num);
}

# функции плагина
function dignity_soft_category_widget_custom($options = array(), $num = 1)
{
	// получаем доступ к CI
	$CI = & get_instance();
	
	// обьявляем переменую
	$out = '';
	
	// загружаем опции
	$options = mso_get_option('plugin_blog_plugins', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'soft';
	
	// добавляем заголовок «Категории»
	$out .= mso_get_val('widget_header_start', '<h2 class="box"><span>') . t('Категории', __FILE__) . mso_get_val('widget_header_end', '</span></h2>');
	
	// берём данные из базы
	$CI->db->from('dignity_soft_category');
	$CI->db->order_by('dignity_soft_category_position', 'asc');
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
			$CI->db->where('dignity_soft_approved', 1);
			$CI->db->where('dignity_soft_category', $entry['dignity_soft_category_id']);
			$CI->db->from('dignity_soft');
			$entry_in_cat = $CI->db->count_all_results();
			
			if ($entry_in_cat > 0)
			{
				// выводим названия категории и количество записей в ней
				$catout .= '<li><a href="' . getinfo('siteurl') . $options['slug'] . '/category/'
				. $entry['dignity_soft_category_id'] . '">' . $entry['dignity_soft_category_name'] . '</a>' . ' (' . $entry_in_cat . ') ' . '</li>';
			}
		}
		
		// начиаем новый список
		$out .= '<ul>';
		
		// выводим назавания категорий и количетсов записей
		$out .= $catout;
		
		// количетсов записей всего
		$CI->db->where('dignity_soft_approved', 1);
		$CI->db->from('dignity_soft');
		$all_entry_in_cat = $CI->db->count_all_results();
		
		// добавляем ссылку «все записи»
		$out .= '<li><a href="' . getinfo('site_url') . $options['slug'] . '/' . '">' . t('Все приложения', __FILE__) . '</a>' . ' (' . $all_entry_in_cat . ') ' . '</li>';
		
		// заканчиваем список
		$out .= '</ul>';
	}
	
	return $out;	
}

function dignity_soft_admin_init($args = array()) 
{
	if ( !mso_check_allow('dignity_soft_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'dignity_soft';
	
	mso_admin_menu_add('plugins', $this_plugin_url, t('Софт', __FILE__));
	mso_admin_url_hook ($this_plugin_url, 'dignity_soft_admin_page');
	
	return $args;
}

function dignity_soft_admin_page($args = array()) 
{
	if ( !mso_check_allow('dignity_soft_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Софт', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Софт', __FILE__) . ' - " . $args; ' );

	if ( mso_segment(3) == 'edit') require(getinfo('plugins_dir') . 'dignity_soft/edit.php');
	elseif ( mso_segment(3) == 'editone') require(getinfo('plugins_dir') . 'dignity_soft/editone.php');
	
	else require(getinfo('plugins_dir') . 'dignity_soft/admin.php');
}

function dignity_soft_custom_page_404($args = false)
{
	$options = mso_get_option('plugin_dignity_soft', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'soft';
   
	if ( mso_segment(1)==$options['slug'] )
	{
		if(mso_segment(2) == 'add')
		{
			// открываем add
			require( getinfo('plugins_dir') . 'dignity_soft/soft_add.php' );
		}
		elseif(mso_segment(2) == 'edit')
		{
			// открываем edit
			require( getinfo('plugins_dir') . 'dignity_soft/soft_edit.php' );
		}
		elseif(mso_segment(2) == 'view')
		{
			// открываем view
			require( getinfo('plugins_dir') . 'dignity_soft/view.php' );
		}
		elseif(mso_segment(2) == 'category')
		{
			// открываем view
			require( getinfo('plugins_dir') . 'dignity_soft/category.php' );
		}
		elseif(mso_segment(2) == 'my')
		{
			// открываем view
			require( getinfo('plugins_dir') . 'dignity_soft/my.php' );
		}
		elseif(mso_segment(2) == 'rss')
		{
			// открываем view
			require( getinfo('plugins_dir') . 'dignity_soft/rss.php' );
		}
		elseif(mso_segment(2) == 'new')
		{
			// открываем new
			require( getinfo('plugins_dir') . 'dignity_soft/new.php' );
		}
		elseif(mso_segment(2) == 'comments')
		{
			// открываем new
			require( getinfo('plugins_dir') . 'dignity_soft/comments.php' );
		}
		else
		{
			// открываем
			require( getinfo('plugins_dir') . 'dignity_soft/soft.php' ) ;
		}
		
		return true;
		
	}

   return $args;
}

###############################################################################

function soft_char_count_js_head()
{
	if (is_login_comuser())
	{
		echo '<script type="text/javascript" src="' . getinfo('plugins_url') . 'dignity_soft/js/charCount.js' . '"></script>';	
	}
}

################################################################################

// парсер bb-code -> html

function soft_cleantext(&$content)
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
                
                // заголовок h1
                '~\[h1\](.*?)\[\/h1\]~si' => '<h1>$1</h1>',
                
                // заголовок h2
                '~\[h2\](.*?)\[\/h2\]~si' => '<h2>$1</h2>',
                
                // заголовок h3
                '~\[h3\](.*?)\[\/h3\]~si' => '<h3>$1</h3>',
                
                // цвет
                '~\[color=(.*?)\](.*?)\[\/color\]~si' => '<span style="color:$1">$2</span>',
                
                // p-абзац
                '~\[p\](.*?)\[\/p\]~si'	=> '<p>$1</p>',
                '~\[p=(.*?)\](.*?)\[\/p\]~si' => '<p style="$1">$2</p>',
                
                // пренудительный перенос
                '~\[br\]~si' => '<br>',
                
                // pre
                '~\[pre\](.*?)\[\/pre\]~si' => '<pre>$1</pre>',
                
                // цитата
                '~\[quote\](.*?)\[\/quote\]~si' => '<blockquote>$1</blockquote>',
                
                // код
                '~\[code\](.*?)\[\/code\]~si' => '<code>$1</code>',
                
                // изображение
                '~\[img\](.*?)\[\/img\]~si' => '<img src="$1" title="" alt="">',
                
                // ссылка
                '~\[url\](.*?)\[\/url\]~si' => '<a href="$1" rel="nofollow">$1</a>',
                '~\[url=(.[^ ]*?)\](.*?)\[\/url\]~si' => '<a href="$1" rel="nofollow">$2</a>',
                
                // youtube
                '~\[youtube\](.*?)\[\/youtube\]~si' => '<iframe width="640" height="360" src="http://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>',
                                                        
                // горизонтальная линия
                '~\[hr\]~si' => '<hr>',
                
                // переносы                                        
                '~\n~' => '<br/>',
                
            );
            
            $content = preg_replace(array_keys($preg), array_values($preg), $content);
            
    return $content;

}

##########################################

// подключаем редактор markitup и задаём настройки

function dignity_soft_editor()
{
 
	// подключаем js от редактора markitup
	echo '<script src="'. getinfo('plugins_url') . 'editor_markitup/jquery.markitup.js"></script>';

	// подключаем стили
	echo '<link rel="stylesheet" href="'. getinfo('plugins_url') . 'dignity_soft/style.css">';
 
	echo "<script type=\"text/javascript\" >
		var dignity_soft_editor_settings = {
		
		nameSpace:'bbcode',
		
		markupSet:[
			{name:'Полужирный', openWith:'[b]', closeWith:'[/b]', className:'bold', key:'B'},
			{name:'Курсив', openWith:'[i]', closeWith:'[/i]', className:'italic', key:'I'},
			{name:'Подчеркнутый', openWith:'[u]', closeWith:'[/u]', className:'underline', key:'U'},
			{name:'Зачеркнутый', openWith:'[s]', closeWith:'[/s]', className:'stroke', key:'S'},
			{name:'Цвет', openWith:'[color=]', closeWith:'[/color]', className:'colors'},
			{name:'Принудительный перенос', replaceWith:'[br]', className:'br'},
			{name:'Преформатированный текст', openWith:'[pre]', closeWith:'[/pre]', className:'pre'},
			{name:'Цитата', openWith:'[quote]', closeWith:'[/quote]', className:'quote'},
			{name:'Код', openBlockWith:'[code]', closeBlockWith:'[/code]', className:'code'}, 
			{name:'Изображение', openWith:'[img]', closeWith:'[/img]', className:'picture'},
			{name:'Ссылка', openBlockWith:'[url]', closeBlockWith:'[/url]', className:'link'},
			{name:'Youtube-Видео', openBlockWith:'[youtube]', closeBlockWith:'[/youtube]', className:'youtube'},
		],
		
		}
	</script>";
 
	echo '<script type="text/javascript" >
			$(document).ready(function() {
				$(".markItUp").markItUp(dignity_soft_editor_settings);
			});
	</script>';
	
}

function soft_menu()
{
        
        // загружаем опции
        $options = mso_get_option('plugin_dignity_soft', 'plugins', array());
        if ( !isset($options['slug']) ) $options['slug'] = 'soft';

	echo "<style>
        
        .tabs{
            border-bottom:solid 1px #dddddd;
            padding-bottom:1px;
            width: 100%;
        }
        
        ul.tabs-nav {
            margin: 0;
            padding: 0;
            height: 30px;
            width: 100%;
            list-style: none;
        }
        
        ul.tabs-nav li.elem {
            float: left;
            display: inline;
            position: relative;
            line-height: 30px;
            height: 30px;
            margin: 0 2px 0 0;
            padding: 0 5px;
            cursor: pointer;
            font-size: .9em;
            background: #fff;
            color: #888;
            -webkit-border-radius: 5px 5px 0 0;
            -moz-border-radius: 5px 5px 0 0;
            border-radius: 5px 5px 0 0;
            border:solid 1px #dddddd;
        }
        
        ul.tabs-nav li.elem:hover,
        ul.tabs-nav li.tabs-current {
            background: #DDD;
            color: black;
        }
        
        </style>";
        
        echo '<div class="tabs"><ul class="tabs-nav">';
        
        if (mso_segment(2))
        {
            echo '<li class="elem"><span><a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Все приложения', __FILE__) . '</a></span></li>';
        }
        else
        {
            echo '<li class="elem tabs-current"><span><a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Все приложения', __FILE__) . '</a></span></li>';
        }
	
	if (mso_segment(2) == 'new')
        {
            echo '<li class="elem tabs-current"><span><a href="' . getinfo('site_url') . $options['slug'] . '/new' . '">' . t('Новые', __FILE__) . '</a></span></li>';
        }
        else
        {
            echo '<li class="elem"><span><a href="' . getinfo('site_url') . $options['slug'] . '/new' . '">' . t('Новые', __FILE__) . '</a></span></li>';
        }
	
	if (mso_segment(2) == 'comments')
        {
            echo '<li class="elem tabs-current"><span><a href="' . getinfo('site_url') . $options['slug'] . '/comments' . '">' . t('Комментарии', __FILE__) . '</a></span></li>';
        }
        else
        {
            echo '<li class="elem"><span><a href="' . getinfo('site_url') . $options['slug'] . '/comments' . '">' . t('Комментарии', __FILE__) . '</a></span></li>';
        }
        
        if (is_login_comuser())
	{
            if (mso_segment(2) == 'my')
            {
                echo '<li class="elem tabs-current"><span><a href="' . getinfo('site_url') . $options['slug'] . '/my/' . getinfo('comusers_id') . '">' . t('Мои приложения', __FILE__) . '</a></span></li>';
            }
            else
            {
                 echo '<li class="elem"><span><a href="' . getinfo('site_url') . $options['slug'] . '/my/' . getinfo('comusers_id') . '">' . t('Мои приложения', __FILE__) . '</a></span></li>';
            }
            
            if (mso_segment(2) == 'add')
            {
                echo '<li class="elem tabs-current"><span><a href="' . getinfo('site_url') . $options['slug'] . '/add/' . '">' . t('Добавить', __FILE__) . '</a></span></li>';
            }
            else
            {
                 echo '<li class="elem"><span><a href="' . getinfo('site_url') . $options['slug'] . '/add/' . '">' . t('Добавить', __FILE__) . '</a></span></li>';
            }
        }
	
	echo '<li class="elem"><span><a href="' . getinfo('site_url') . $options['slug'] . '/rss/' . '">' . t('RSS', __FILE__) . '</a></span></li>';
        
        echo '</ul></div><br>';
}

// функция хука users_add_out
// выводит количество публикаций и комментарий на странице комюзера
function dignity_soft_users_add_out($comuser = array())
{
	// доступ к CodeIgniter
	$CI = & get_instance();

	echo '<h2 style="padding: 3px; border-bottom: 1px solid #DDD;">' . t('Активность в софт', __FILE__) . '</h2>';
		
	$CI->db->from('dignity_soft');
	$CI->db->where('dignity_soft_approved', '1');
	$CI->db->where('dignity_soft_comuser_id', mso_segment(2));
	$soft_entry = $CI->db->count_all_results();
		
	echo '<p style="padding-left:20px;">' . '<strong>' . t('Добавил:', __FILE__) . '</strong> ' . $soft_entry . '</p>';

	$CI->db->from('dignity_soft_comments');
	$CI->db->where('dignity_soft_comments_approved', '1');
	$CI->db->where('dignity_soft_comments_comuser_id', mso_segment(2));
	$soft_comments = $CI->db->count_all_results();
		
	echo '<p style="padding-left:20px;">' . '<strong>' . t('Комментарий:', __FILE__) . '</strong> ' . $soft_comments . '</p>';

	return $comuser;
}

#end of file