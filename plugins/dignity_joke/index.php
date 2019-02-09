<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 *
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 *
 */

function dignity_joke_autoload()
{
	mso_hook_add('admin_init', 'dignity_joke_admin_init');
	mso_hook_add('custom_page_404', 'dignity_joke_custom_page_404');

	// для вывода количества анекдотов и комментарий
	mso_hook_add('users_add_out', 'dignity_joke_users_add_out', '70');
	
	// регестируем виджет
	mso_register_widget('dignity_joke_category_widget', t('Категории анекдотов', __FILE__));
}

function dignity_joke_activate($args = array())
{	
	mso_create_allow('dignity_joke_edit', t('Админ-доступ к', 'plugins') . ' ' . t('«Анекдоты»', __FILE__));
	
	// доступ к CI
        $CI = & get_instance();	

	// создаём табилицу для записей
	if ( !$CI->db->table_exists('dignity_joke'))
	{
		$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
		$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
		$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate;
		
		$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "dignity_joke (
		dignity_joke_id bigint(20) NOT NULL auto_increment,
		dignity_joke_cuttext longtext NOT NULL default '',
		dignity_joke_text longtext NOT NULL default '',
		dignity_joke_datecreate datetime NOT NULL default '0000-00-00 00:00:00',
		dignity_joke_dateupdate datetime NOT NULL default '0000-00-00 00:00:00',
		dignity_joke_approved varchar(1) NOT NULL default '',
		dignity_joke_comments varchar(1) NOT NULL default '',
		dignity_joke_rss varchar(1) NOT NULL default '',
		dignity_joke_ontop varchar(1) NOT NULL default '',
		dignity_joke_views bigint(20) NOT NULL default '0',
		dignity_joke_comuser_id bigint(20) NOT NULL default '0',
		dignity_joke_user_id bigint(20) NOT NULL default '0',
		dignity_joke_category bigint(20) NOT NULL default '0',
		dignity_joke_rating bigint(20) NOT NULL default '0',
		PRIMARY KEY (dignity_joke_id)
		)" . $charset_collate;
		
		$CI->db->query($sql);
	}
	
	// создаём табилицу для ответов
	if ( !$CI->db->table_exists('dignity_joke_comments'))
	{
		$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
		$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
		$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate;
		
		$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "dignity_joke_comments (
		dignity_joke_comments_id bigint(20) NOT NULL auto_increment,
		dignity_joke_comments_text longtext NOT NULL default '',
		dignity_joke_comments_thema_id bigint(20) NOT NULL default '0',
		dignity_joke_comments_datecreate datetime NOT NULL default '0000-00-00 00:00:00',
		dignity_joke_comments_dateupdate datetime NOT NULL default '0000-00-00 00:00:00',
		dignity_joke_comments_approved varchar(1) NOT NULL default '',
		dignity_joke_comments_comuser_id bigint(20) NOT NULL default '0',
		dignity_joke_comments_user_id bigint(20) NOT NULL default '0',
		dignity_joke_comments_rating bigint(20) NOT NULL default '0',
		PRIMARY KEY (dignity_joke_comments_id)
		)" . $charset_collate;
		
		$CI->db->query($sql);
	}
	
	// создаём табилицу для категорий
	if ( !$CI->db->table_exists('dignity_joke_category'))
	{
		$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
		$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
		$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate;
		
		$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "dignity_joke_category (
		dignity_joke_category_id bigint(20) NOT NULL auto_increment,
		dignity_joke_category_name longtext NOT NULL default '',
		dignity_joke_category_description longtext NOT NULL default '',
		dignity_joke_category_position bigint(20) NOT NULL default '0',
		PRIMARY KEY (dignity_joke_category_id)
		)" . $charset_collate;
		
		$CI->db->query($sql);
	}

	return $args;
}

function dignity_joke_uninstall($args = array())
{	
	mso_delete_option('plugin_dignity_joke', 'plugins');
	mso_remove_allow('dignity_joke_edit');
	
	// получааем доступ к CI
	$CI = &get_instance();
	
	$CI->load->dbforge();
	
	// удаляем таблицы
	$CI->dbforge->drop_table('dignity_joke');
	$CI->dbforge->drop_table('dignity_joke_comments');
	$CI->dbforge->drop_table('dignity_joke_category');
	
	// удаляем настройки виджета
	mso_delete_option_mask('dignity_joke_category_widget_', 'plugins');

	return $args;
}

# функция, которая берет настройки из опций виджетов
function dignity_joke_category_widget($num = 1) 
{
	$widget = 'dignity_joke_category_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	return dignity_joke_category_widget_custom($options, $num);
}

# функции плагина
function dignity_joke_category_widget_custom($options = array(), $num = 1)
{
	// получаем доступ к CI
	$CI = & get_instance();
	
	// обьявляем переменую
	$out = '';
	
	// загружаем опции
	$options = mso_get_option('plugin_blog_plugins', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'joke';
	
	// добавляем заголовок «Категории»
	$out .= mso_get_val('widget_header_start', '<h2 class="box"><span>') . t('Категории', __FILE__) . mso_get_val('widget_header_end', '</span></h2>');
	
	// берём данные из базы
	$CI->db->from('dignity_joke_category');
	$CI->db->order_by('dignity_joke_category_position', 'asc');
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
			$CI->db->where('dignity_joke_approved', 1);
			$CI->db->where('dignity_joke_category', $entry['dignity_joke_category_id']);
			$CI->db->from('dignity_joke');
			$entry_in_cat = $CI->db->count_all_results();
			
			if ($entry_in_cat > 0)
			{
				// выводим названия категории и количество записей в ней
				$catout .= '<li><a href="' . getinfo('siteurl') . $options['slug'] . '/category/'
					. $entry['dignity_joke_category_id'] . '">' . $entry['dignity_joke_category_name'] . '</a>' . ' (' . $entry_in_cat . ') ' . '</li>';
			}
		}
		
		// начиаем новый список
		$out .= '<ul>';
		
		// выводим назавания категорий и количетсов записей
		$out .= $catout;
		
		// количетсов записей всего
		$CI->db->where('dignity_joke_approved', 1);
		$CI->db->from('dignity_joke');
		$all_entry_in_cat = $CI->db->count_all_results();
		
		// добавляем ссылку «все записи»
		$out .= '<li><a href="' . getinfo('site_url') . $options['slug'] . '/' . '">' . t('Все анекдоты', __FILE__) . '</a>' . ' (' . $all_entry_in_cat . ') ' . '</li>';
		
		// заканчиваем список
		$out .= '</ul>';
	}
	
	return $out;	
}

function dignity_joke_admin_init($args = array()) 
{
	if ( !mso_check_allow('dignity_joke_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'dignity_joke';
	
	mso_admin_menu_add('plugins', $this_plugin_url, t('Анекдоты', __FILE__));
	mso_admin_url_hook ($this_plugin_url, 'dignity_joke_admin_page');
	
	return $args;
}

function dignity_joke_admin_page($args = array()) 
{
	if ( !mso_check_allow('dignity_joke_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Анекдоты', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Анекдоты', __FILE__) . ' - " . $args; ' );

	if ( mso_segment(3) == 'edit') require(getinfo('plugins_dir') . 'dignity_joke/edit.php');
	elseif ( mso_segment(3) == 'editone') require(getinfo('plugins_dir') . 'dignity_joke/editone.php');
	
	else require(getinfo('plugins_dir') . 'dignity_joke/admin.php');
}

function dignity_joke_custom_page_404($args = false)
{
	$options = mso_get_option('plugin_dignity_joke', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'joke';
   
	if ( mso_segment(1)==$options['slug'] )
	{
		if(mso_segment(2) == 'add')
		{
			// открываем add
			require( getinfo('plugins_dir') . 'dignity_joke/joke_add.php' );
		}
		elseif(mso_segment(2) == 'edit')
		{
			// открываем edit
			require( getinfo('plugins_dir') . 'dignity_joke/joke_edit.php' );
		}
		elseif(mso_segment(2) == 'view')
		{
			// открываем view
			require( getinfo('plugins_dir') . 'dignity_joke/view.php' );
		}
		elseif(mso_segment(2) == 'category')
		{
			// открываем view
			require( getinfo('plugins_dir') . 'dignity_joke/category.php' );
		}
		elseif(mso_segment(2) == 'my')
		{
			// открываем view
			require( getinfo('plugins_dir') . 'dignity_joke/my.php' );
		}
		elseif(mso_segment(2) == 'rss')
		{
			// открываем view
			require( getinfo('plugins_dir') . 'dignity_joke/rss.php' );
		}
		elseif(mso_segment(2) == 'new')
		{
			// открываем new
			require( getinfo('plugins_dir') . 'dignity_joke/new.php' );
		}
		elseif(mso_segment(2) == 'comments')
		{
			// открываем new
			require( getinfo('plugins_dir') . 'dignity_joke/comments.php' );
		}
		else
		{
			// открываем
			require( getinfo('plugins_dir') . 'dignity_joke/joke.php' ) ;
		}
		
		return true;
		
	}

   return $args;
}

################################################################################

// парсер bb-code -> html

function joke_cleantext(&$content)
{
        // защита от xss
        mso_xss_clean($content);
    
        // массив
        $preg = array(
                
                // переносы                                        
                '~\n~' => '<br/>',
                
            );
            
            $content = preg_replace(array_keys($preg), array_values($preg), $content);
            
    return $content;

}

#########################################################################

function joke_menu()
{
        
        // загружаем опции
        $options = mso_get_option('plugin_dignity_joke', 'plugins', array());
        if ( !isset($options['slug']) ) $options['slug'] = 'joke';

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
            echo '<li class="elem"><span><a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Все анекдоты', __FILE__) . '</a></span></li>';
        }
        else
        {
            echo '<li class="elem tabs-current"><span><a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Все анекдоты', __FILE__) . '</a></span></li>';
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
                echo '<li class="elem tabs-current"><span><a href="' . getinfo('site_url') . $options['slug'] . '/my/' . getinfo('comusers_id') . '">' . t('Мои анекдоты', __FILE__) . '</a></span></li>';
            }
            else
            {
                 echo '<li class="elem"><span><a href="' . getinfo('site_url') . $options['slug'] . '/my/' . getinfo('comusers_id') . '">' . t('Мои анекдоты', __FILE__) . '</a></span></li>';
            }
            
            if (mso_segment(2) == 'add')
            {
                echo '<li class="elem tabs-current"><span><a href="' . getinfo('site_url') . $options['slug'] . '/add/' . '">' . t('Добавить анекдот', __FILE__) . '</a></span></li>';
            }
            else
            {
                 echo '<li class="elem"><span><a href="' . getinfo('site_url') . $options['slug'] . '/add/' . '">' . t('Добавить анекдот', __FILE__) . '</a></span></li>';
            }
        }
	
	echo '<li class="elem"><span><a href="' . getinfo('site_url') . $options['slug'] . '/rss/' . '">' . t('RSS', __FILE__) . '</a></span></li>';
        
        echo '</ul></div><br>';
}

// функция хука users_add_out
// выводит количество анекдотов и комментарий на странице комюзера
function dignity_joke_users_add_out($comuser = array())
{
	// доступ к CodeIgniter
	$CI = & get_instance();

	echo '<h2 style="padding: 3px; border-bottom: 1px solid #DDD;">' . t('Активность в анекдотах', __FILE__) . '</h2>';
		
	$CI->db->from('dignity_joke');
	$CI->db->where('dignity_joke_approved', true);
	$CI->db->where('dignity_joke_comuser_id', mso_segment(2));
	$joke_entry = $CI->db->count_all_results();
		
	echo '<p style="padding-left:20px;">' . '<strong>' . t('Публикаций:', __FILE__) . '</strong> ' . $joke_entry . '</p>';

	$CI->db->from('dignity_joke_comments');
	$CI->db->where('dignity_joke_comments_approved', true);
	$CI->db->where('dignity_joke_comments_comuser_id', mso_segment(2));
	$joke_comments = $CI->db->count_all_results();
		
	echo '<p style="padding-left:20px;">' . '<strong>' . t('Комментарий:', __FILE__) . '</strong> ' . $joke_comments . '</p>';

	return $comuser;
}

#end of file