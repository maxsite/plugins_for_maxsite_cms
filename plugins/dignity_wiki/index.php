<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 *
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 *
 */

function dignity_wiki_autoload()
{
	mso_hook_add('admin_init', 'dignity_wiki_admin_init');
	mso_hook_add('custom_page_404', 'dignity_wiki_custom_page_404');
}

function dignity_wiki_activate($args = array())
{	
	mso_create_allow('dignity_wiki_edit', t('Админ-доступ к', 'plugins') . ' ' . t('«Wiki»', __FILE__));
	
	// доступ к CI
        $CI = & get_instance();	

	// создаём табилицу для записей
	if ( !$CI->db->table_exists('dignity_wiki'))
	{
		$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
		$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
		$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate;
		
		$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "dignity_wiki (
		dignity_wiki_id bigint(20) NOT NULL auto_increment,
		dignity_wiki_title varchar(100) NOT NULL default '',
		dignity_wiki_keywords longtext NOT NULL default '',
		dignity_wiki_description longtext NOT NULL default '',
		dignity_wiki_cuttext longtext NOT NULL default '',
		dignity_wiki_text longtext NOT NULL default '',
		dignity_wiki_datecreate datetime NOT NULL default '0000-00-00 00:00:00',
		dignity_wiki_dateupdate datetime NOT NULL default '0000-00-00 00:00:00',
		dignity_wiki_approved varchar(1) NOT NULL default '',
		dignity_wiki_views bigint(20) NOT NULL default '0',
		dignity_wiki_comuser_id bigint(20) NOT NULL default '0',
		dignity_wiki_user_id bigint(20) NOT NULL default '0',
		dignity_wiki_category bigint(20) NOT NULL default '0',
		dignity_wiki_rating bigint(20) NOT NULL default '0',
		PRIMARY KEY (dignity_wiki_id)
		)" . $charset_collate;
		
		$CI->db->query($sql);
	}
	
	// создаём табилицу для категорий
	if ( !$CI->db->table_exists('dignity_wiki_category'))
	{
		$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
		$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
		$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate;
		
		$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "dignity_wiki_category (
		dignity_wiki_category_id bigint(20) NOT NULL auto_increment,
		dignity_wiki_category_name longtext NOT NULL default '',
		dignity_wiki_category_description longtext NOT NULL default '',
		dignity_wiki_category_position bigint(20) NOT NULL default '0',
		PRIMARY KEY (dignity_wiki_category_id)
		)" . $charset_collate;
		
		$CI->db->query($sql);
	}

	return $args;
}

function dignity_wiki_uninstall($args = array())
{	
	mso_delete_option('plugin_dignity_wiki', 'plugins');
	mso_remove_allow('dignity_wiki_edit');
	
	// получааем доступ к CI
	$CI = &get_instance();
	
	$CI->load->dbforge();
	
	// удаляем таблицы
	$CI->dbforge->drop_table('dignity_wiki');
	$CI->dbforge->drop_table('dignity_wiki_category');

	return $args;
}

function dignity_wiki_admin_init($args = array()) 
{
	if ( !mso_check_allow('dignity_wiki_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'dignity_wiki';
	
	mso_admin_menu_add('plugins', $this_plugin_url, t('Wiki', __FILE__));
	mso_admin_url_hook ($this_plugin_url, 'dignity_wiki_admin_page');
	
	return $args;
}

function dignity_wiki_admin_page($args = array()) 
{
	if ( !mso_check_allow('dignity_wiki_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Wiki', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Wiki', __FILE__) . ' - " . $args; ' );

	require(getinfo('plugins_dir') . 'dignity_wiki/admin.php');
}

function dignity_wiki_custom_page_404($args = false)
{
	$options = mso_get_option('plugin_dignity_wiki', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'wiki';
   
	if ( mso_segment(1)==$options['slug'] )
	{
		if(mso_segment(2) == 'add')
		{
			// открываем add
			require( getinfo('plugins_dir') . 'dignity_wiki/add.php' );
		}
		elseif(mso_segment(2) == 'edit')
		{
			// открываем edit
			require( getinfo('plugins_dir') . 'dignity_wiki/edit.php' );
		}
		elseif(mso_segment(2) == 'view')
		{
			// открываем view
			require( getinfo('plugins_dir') . 'dignity_wiki/view.php' );
		}
		else
		{
			// открываем
			require( getinfo('plugins_dir') . 'dignity_wiki/wiki.php' ) ;
		}
		
		return true;
		
	}

   return $args;
}

################################################################################

// парсер bb-code -> html

function wiki_cleantext(&$content)
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

function dignity_wiki_editor()
{
 
	// подключаем js от редактора markitup
	echo '<script src="'. getinfo('plugins_url') . 'editor_markitup/jquery.markitup.js"></script>';

	// подключаем стили
	echo '<link rel="stylesheet" href="'. getinfo('plugins_url') . 'dignity_wiki/style.css">';
 
	echo "<script type=\"text/javascript\" >
		var dignity_wiki_editor_settings = {
		
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
				$(".markItUp").markItUp(dignity_wiki_editor_settings);
			});
	</script>';
	
}

function wiki_menu()
{
        
        // загружаем опции
        $options = mso_get_option('plugin_dignity_wiki', 'plugins', array());
        if ( !isset($options['slug']) ) $options['slug'] = 'wiki';

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
            echo '<li class="elem"><span><a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Все', __FILE__) . '</a></span></li>';
        }
        else
        {
            echo '<li class="elem tabs-current"><span><a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Все', __FILE__) . '</a></span></li>';
        }
        
        if (is_login_comuser())
	   {
            if (mso_segment(2) == 'add')
            {
                echo '<li class="elem tabs-current"><span><a href="' . getinfo('site_url') . $options['slug'] . '/add/' . '">' . t('Добавить', __FILE__) . '</a></span></li>';
            }
            else
            {
                 echo '<li class="elem"><span><a href="' . getinfo('site_url') . $options['slug'] . '/add/' . '">' . t('Добавить', __FILE__) . '</a></span></li>';
            }
        }
        
        echo '</ul></div><br>';
}

#end of file