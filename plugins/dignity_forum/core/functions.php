<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

# Общие функции плагина
# require_once(getinfo('plugins_dir') . 'dignity_forum/core/functions.php');
# $forum = new Forum;

class Forum
{
	// меню
	function menu() 
	{
		// загружаем опции
	    $options = mso_get_option('plugin_dignity_forum', 'plugins', array());
	    if ( !isset($options['slug']) ) $options['slug'] = 'forum';
	        
	    echo '<div class="forum_tabs"><ul class="forum_tabs_nav">';
	        
	    if (mso_segment(2))
	    {
			echo '<li class="elem"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_forum/img/forum.png' . '" alt=""></span><span><a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Форум', __FILE__) . '</a></span></li>';
		}
	    else
	    {
		   echo '<li class="elem forum_tabs-current"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_forum/img/forum.png' . '" alt=""></span><span><a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Форум', __FILE__) . '</a></span></li>';
	    }
		
		if (mso_segment(2) == 'topics' && !mso_segment(3))
	    {
	        echo '<li class="elem forum_tabs-current"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_forum/img/topics.png' . '" alt=""></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/topics/' . '">' . t('Темы', __FILE__) . '</a></span></li>';
	    }
	    else
	    {
	        echo '<li class="elem"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_forum/img/topics.png' . '" alt=""></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/topics/' . '">' . t('Темы', __FILE__) . '</a></span></li>';
	    }
		
		if (mso_segment(2) == 'replys' && !mso_segment(3))
	    {
	        echo '<li class="elem forum_tabs-current"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_forum/img/comments.png' . '" alt=""></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/replys/' . '">' . t('Ответы', __FILE__) . '</a></span></li>';
	    }
	    else
	    {
	    	echo '<li class="elem"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_forum/img/comments.png' . '" alt=""></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/replys/' . '">' . t('Ответы', __FILE__) . '</a></span></li>';
	    }

	    $user = '';
	    if (is_login_comuser())
	    {
	    	$user = getinfo('comusers_id');
	    }
	    elseif (is_login())
	    {
	    	$user = getinfo('comusers_id');
	    }

	    if (is_login_comuser() || is_login())
	    {
		    if (mso_segment(2) == 'topics' && mso_segment(3) == $user)
		    {
		        echo '<li class="elem forum_tabs-current"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_forum/img/topics.png' . '" alt=""></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/topics/' . $user . '">' . t('Ваши темы', __FILE__) . '</a></span></li>';
		    }
		    else
		    {
		    	echo '<li class="elem"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_forum/img/topics.png' . '" alt=""></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/topics/' . $user . '">' . t('Ваши темы', __FILE__) . '</a></span></li>';
		    }
	    }

	    if (is_login_comuser() || is_login())
	    {
		    if (mso_segment(2) == 'replys' && mso_segment(3) == $user)
		    {
		        echo '<li class="elem forum_tabs-current"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_forum/img/comments.png' . '" alt=""></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/replys/' . $user . '">' . t('Ваши ответы', __FILE__) . '</a></span></li>';
		    }
		    else
		    {
		    	echo '<li class="elem"><span style="padding-right:5px;"><img src="' . getinfo('plugins_url') . 'dignity_forum/img/comments.png' . '" alt=""></span><span><a href="' . getinfo('site_url') . $options['slug'] . '/replys/' . $user . '">' . t('Ваши ответы', __FILE__) . '</a></span></li>';
		    }
	    }
	    
	    if (mso_segment(2) == 'search')
	    {
	        echo '<li class="elem forum_tabs-current"><span style="padding-right:0px;"><a href="' . getinfo('site_url') . $options['slug'] . '/search/' . '"><img src="' . getinfo('plugins_url') . 'dignity_forum/img/search.png' . '" alt="" title="' . t('Поиск', __FILE__) . '"></span></a></li>';
	    }
	    else
	    {
	        echo '<li class="elem"><span style="padding-right:0px;"><a href="' . getinfo('site_url') . $options['slug'] . '/search/' . '"><img src="' . getinfo('plugins_url') . 'dignity_forum/img/search.png' . '" alt="" title="' . t('Поиск', __FILE__) . '"></span></a></li>';
	    }
	    
	    if (mso_segment(2) == 'rules')
	    {
	        echo '<li class="elem forum_tabs-current"><span style="padding-right:0px;"><a href="' . getinfo('site_url') . $options['slug'] . '/rules/' . '"><img src="' . getinfo('plugins_url') . 'dignity_forum/img/rules.png' . '" alt="" title="' . t('Правила', __FILE__) . '"></span></a></li>';
	    }
	    else
	    {
	        echo '<li class="elem"><span style="padding-right:0px;"><a href="' . getinfo('site_url') . $options['slug'] . '/rules/' . '"><img src="' . getinfo('plugins_url') . 'dignity_forum/img/rules.png' . '" alt="" title="' . t('Правила', __FILE__) . '"></span></a></li>';
	    }

	    if (mso_segment(2) == 'profile')
	    {
	        echo '<li class="elem forum_tabs-current"><span style="padding-right:0px;"><a href="' . getinfo('site_url') . $options['slug'] . '/profile/' . '"><img src="' . getinfo('plugins_url') . 'dignity_forum/img/user.png' . '" alt="" title="' . t('Пользователи', __FILE__) . '"></a></span></li>';
	    }
	    else
	    {
	        echo '<li class="elem"><span style="padding-right:0px;"><a href="' . getinfo('site_url') . $options['slug'] . '/profile/' . '"><img src="' . getinfo('plugins_url') . 'dignity_forum/img/user.png' . '" alt="" title="' . t('Пользователи', __FILE__) . '"></a></span></li>';
	    }
		
		echo '<li class="elem"><span style="padding-right:0px;"><a href="' . getinfo('site_url') . $options['slug'] . '/rss/' . '"><img src="' . getinfo('plugins_url') . 'dignity_forum/img/rss.png' . '" alt="" title="' . t('RSS', __FILE__) . '"></span></a></li>';
		
		if (is_login())
		{
			echo '<li class="elem"><span><a href="' . getinfo('site_admin_url') . 'dignity_forum/' . '"><img src="' . getinfo('plugins_url') . 'dignity_forum/img/settings.png' . '" alt="" title="' . t('Управление', __FILE__) . '"></a></span></li>';
		}
	        
	        echo '</ul></div><br>';
	}

	// меню анминки
	function admin_menu()
	{
		$plugin_url = getinfo('site_admin_url') . 'dignity_forum';
        
        $a  = mso_admin_link_segment_build($plugin_url, '', t('Настройки', __FILE__), 'select') . ' | ';
        $a .= mso_admin_link_segment_build($plugin_url, 'edit_category', t('Категории', __FILE__), 'select') . ' | ';
        $a .= mso_admin_link_segment_build($plugin_url, 'edit_topic', t('Темы', __FILE__), 'select') . ' | ';
        $a .= mso_admin_link_segment_build($plugin_url, 'edit_reply', t('Ответы', __FILE__), 'select') . ' | ';
        $a .= mso_admin_link_segment_build($plugin_url, 'meta', t('Meta-данные', __FILE__), 'select') . ' | ';
        $a .= mso_admin_link_segment_build($plugin_url, 'maintenance', t('Режим обслуживания', __FILE__), 'select') . ' | ';
        $a .= mso_admin_link_segment_build($plugin_url, 'rules', t('Правила форума', __FILE__), 'select');

        echo $a;
        
    	return $a;
	}

	// скрывать сайтбар
	function hide_sidebar()
	{
		$options = mso_get_option('plugin_dignity_forum', 'plugins', array());
		if ( !isset($options['hide_sidebar']) ) $options['hide_sidebar'] = false;

		if($options['hide_sidebar'])
		{
			echo '<style type="text/css">
				div.content{width: 100%;}
				div.sidebar{display: none;}
			</style>';
		}
	}

	// покасывает постоянную ссылку на тему
	function current_url()
	{
		// загружаем опции и присваиваем значения по умолчанию
	    $options = mso_get_option('plugin_dignity_forum', 'plugins', array());
	    if ( !isset($options['slug']) ) $options['slug'] = 'forum';
		
		$url = getinfo('siteurl') . $options['slug'] . '/topic/' . mso_segment(3);
		
	    $out = '<a href="' . $url . '">' . $url . '</a>';

	    return $out;
	}

	// новый комюзер
	function new_comuser($out='')
	{
		// кэш 
        $cache_key = 'new_comusers';
        $k = mso_get_cache($cache_key);
        if ($k) return $k;
        
        // загружаем опции и присваиваем значения по умолчанию
        $options = mso_get_option('plugin_dignity_forum', 'plugins', array());
        if ( !isset($options['slug']) ) $options['slug'] = 'forum';
        
        $CI = &get_instance();
        $CI->db->from('comusers');
        $CI->db->order_by('comusers_id', 'desc');
        $CI->db->limit(1);
        $q = $CI->db->get();
      
        foreach ($q->result_array() as $rw)
        {
                $out = '<a href="' . getinfo('siteurl') . 'users/' . $rw['comusers_id'] . '">' . $rw['comusers_nik'] . '</a>';
        }
        
        mso_add_cache($cache_key, $out);
        
    	return $out;
	}

	// все комюзеры
	function all_comusers()
	{

        $CI = &get_instance();
        $CI->db->from('comusers');
        $out = $CI->db->count_all_results();
        
    	return $out;
	}

	// все юзеры
	function all_users()
	{

        $CI = &get_instance();
        $CI->db->from('users');
        $out = $CI->db->count_all_results();
        
    	return $out;
	}

	// всего тем
	function all_topics()
	{
		// кэш 
        $cache_key = 'all_topics';
        $k = mso_get_cache($cache_key);
        if ($k) return $k;
        
        $CI = &get_instance();
        $CI->db->where('dignity_forum_topic_approved', '1');
        $CI->db->from('dignity_forum_topic');
        $out = $CI->db->count_all_results();
        
        mso_add_cache($cache_key, $out);
        
    	return $out;
	}

	// всего ответов
	function all_replys()
	{
		 // кэш 
        $cache_key = 'all_reply';
        $k = mso_get_cache($cache_key);
        if ($k) return $k;
        
        $CI = &get_instance();
        $CI->db->where('dignity_forum_reply_approved', '1');
        $CI->db->from('dignity_forum_reply');
        $out = $CI->db->count_all_results();
        
        mso_add_cache($cache_key, $out);
        
    	return $out;
	}

	// всего тем и сообщений
	function all_topics_and_replys()
	{
		// кэш 
        $cache_key = 'all_topics_and_replys';
        $k = mso_get_cache($cache_key);
        if ($k) return $k;

        $CI = &get_instance();
        $CI->db->where('dignity_forum_topic_approved', '1');
        $CI->db->from('dignity_forum_topic');
        $topics = $CI->db->count_all_results();

        $CI = &get_instance();
        $CI->db->where('dignity_forum_reply_approved', '1');
        $CI->db->from('dignity_forum_reply');
        $replys = $CI->db->count_all_results();
        
        $out = $topics + $replys;
        
        mso_add_cache($cache_key, $out);
        
    	return $out;
	}

	// данные для вывода на /forum
	function get_forums()
	{
		// доступ к CI
		$CI = &get_instance();

		// пустой массив
		$out_forums = array();

		// получаем данные из базы
		$CI->db->from('dignity_forum_category');
		$CI->db->where('dignity_forum_category_parent_id', false);
		$CI->db->order_by('dignity_forum_category_order', 'asc');
		$query = $CI->db->get();

		// если есть что выводить
		if ($query->num_rows() > 0)	
		{
			// помешаем результат в массив
			$out_forums['forum_content'] = $query->result_array();
		}
		else
		{
			// если ещё нет разделов
			$out_forums['forum_content'] = t('Не создано ни одной категории.', __FILE__);
		}

		return $out_forums;
	}

	// последний ответ
	function get_last_reply($topic_id='', $last_reply='')
	{

		// загружаем опции и присваиваем значения по умолчанию
		$options = mso_get_option('plugin_dignity_forum', 'plugins', array());
		if ( !isset($options['slug']) ) $options['slug'] = 'forum';

		// выводим последний ответ
		$CI = &get_instance();
		$CI->db->from('dignity_forum_reply');
		$CI->db->select('dignity_forum_reply_id, dignity_forum_reply_datecreate, dignity_forum_reply_topic_id, dignity_forum_reply_comusers_id, comusers_nik, dignity_forum_reply_users_id, users_nik, dignity_forum_reply_approved');
		$CI->db->where('dignity_forum_reply_topic_id', $topic_id);
		$CI->db->join('comusers', 'comusers.comusers_id = dignity_forum_reply.dignity_forum_reply_comusers_id', 'left');
		$CI->db->join('users', 'users.users_id = dignity_forum_reply.dignity_forum_reply_users_id', 'left');
		$CI->db->order_by('dignity_forum_reply_dateupdate', 'desc');
		$CI->db->limit(1);
		$q = $CI->db->get();
						
		foreach ($q->result_array() as $row)
		{
			// определяем кто автор темы
			$reply_nik = '';
			if ($row['dignity_forum_reply_comusers_id'])
			{
				$reply_nik = $row['comusers_nik'];
				$reply_site = getinfo('siteurl') . $options['slug'] . '/profile/#' . $row['dignity_forum_reply_comusers_id'];
			}
			else
			{
				$reply_nik = $row['users_nik'];
				$reply_site = getinfo('siteurl') . 'author/' . $row['dignity_forum_reply_users_id'];
			}

			$last_reply .= ' | <img src="' . getinfo('plugins_url') . 'dignity_forum/img/comments.png' . '" alt=""> <a href="' . getinfo('siteurl') . $options['slug'] . '/topic/'. $row['dignity_forum_reply_topic_id'] .'#answer-'. $row['dignity_forum_reply_id'] .'">' . $reply_nik . '</a>';
		}

		return $last_reply;
	}

	// Количество тем в категории
	function topics_in_category($category_id='')
	{
		$CI = &get_instance();
		$CI->db->where('dignity_forum_topic_approved', true);
		$CI->db->where('dignity_forum_topic_category', $category_id);
		$CI->db->from('dignity_forum_topic');
		$topics_in_cat = $CI->db->count_all_results();

		return $topics_in_cat;
	}

	// Количество ответов в теме
	function reply_in_topic($category_id='')
	{
		$CI = &get_instance();
		$CI->db->join('dignity_forum_topic', 'dignity_forum_topic.dignity_forum_topic_id = dignity_forum_reply.dignity_forum_reply_topic_id');
		$CI->db->where('dignity_forum_topic_category', $category_id);
		$CI->db->where('dignity_forum_reply_approved', true);
		$CI->db->from('dignity_forum_reply');
		$reply_in_cat = $CI->db->count_all_results();

		return $reply_in_cat;
	}

	// Подкатегории
	function sub_topic($category_id='', $sub_topics='')
	{

		// загружаем опции и присваиваем значения по умолчанию
		$options = mso_get_option('plugin_dignity_forum', 'plugins', array());
		if ( !isset($options['slug']) ) $options['slug'] = 'forum';

		// получаем под категории из базы
		$CI = &get_instance();
		$CI->db->from('dignity_forum_category');
		$CI->db->where('dignity_forum_category_parent_id', $category_id);
		$query = $CI->db->get();
			
		if ($query->num_rows() > 0)	
		{		
			$sub_topics .= '<ul>';
				
			foreach ($query->result_array() as $row) 
			{
					$sub_topics .= '<p><span class="forum_category_description">' . t('Подфорумы: ', __FILE__) . '<a href="' . getinfo('siteurl') . $options['slug'] . '/view/' . $row['dignity_forum_category_id'] . '">' . $row['dignity_forum_category_name'] . '</a><span></p>';
			}
				
			$sub_topics .= '</ul>';
		}

		return $sub_topics;
	}

	// получить имя категории
	function get_category_name($out_category_name = '')
	{

		// получаем доступ к CI
		$CI = & get_instance();

		$CI->db->from('dignity_forum_category');
		$q = $CI->db->get();
		foreach ($q->result_array() as $rw)
		{
			if (mso_segment(3) === $rw['dignity_forum_category_id'])
			{
				$out_category_name = $rw['dignity_forum_category_name'];
			}
		}

		return $out_category_name;
	}

	// получить прикрепленные темы
	function get_topic_ontop($out_get_topic_ontop = array())
	{
		$CI = &get_instance();

		$id = mso_segment(3);
		if (!is_numeric($id)) $id = false;
		else $id = (int) $id;

		// берем темы из базы
		$CI->db->from('dignity_forum_topic');
		$CI->db->where('dignity_forum_topic_category', $id);
		$CI->db->where('dignity_forum_topic_ontop', true);
		$CI->db->join('comusers', 'comusers.comusers_id = dignity_forum_topic.dignity_forum_topic_сomusers_id', 'left');
		$CI->db->join('users', 'users.users_id = dignity_forum_topic.dignity_forum_topic_users_id', 'left');
		$CI->db->order_by('dignity_forum_topic_dateupdate', 'desc');
		$query = $CI->db->get();

		$out_get_topic_ontop['all_topics_in_category'] = $query->result_array();

		$out_get_topic_ontop['num_rows'] = $query->num_rows();

		return $out_get_topic_ontop;
	}

	// получить темы
	function get_topic($out_get_topic = array())
	{
		$CI = &get_instance();

		$id = mso_segment(3);
		if (!is_numeric($id)) $id = false;
		else $id = (int) $id;

		// готовим пингацию для тем
		$pag = array();
		$pag['limit'] = 10;
		$CI->db->select('dignity_forum_topic_id');
		$CI->db->where('dignity_forum_topic_category', $id);
		$CI->db->where('dignity_forum_topic_approved', true);
		$CI->db->where('dignity_forum_topic_ontop', false);
		$CI->db->from('dignity_forum_topic');
		$query = $CI->db->get();
		$pag_row = $query->num_rows();
		if ($pag_row > 0)
		{
			$pag['maxcount'] = ceil($pag_row / $pag['limit']);
			$current_paged = mso_current_paged();
			if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];
			$offset = $current_paged * $pag['limit'] - $pag['limit'];
		}
		else
		{
			$pag = false;
		}

		// берём темы из базы
		$CI->db->from('dignity_forum_topic');
		$CI->db->where('dignity_forum_topic_category', $id);
		$CI->db->where('dignity_forum_topic_ontop', false);
		$CI->db->join('comusers', 'comusers.comusers_id = dignity_forum_topic.dignity_forum_topic_сomusers_id', 'left');
		$CI->db->join('users', 'users.users_id = dignity_forum_topic.dignity_forum_topic_users_id', 'left');
		$CI->db->order_by('dignity_forum_topic_dateupdate', 'desc');
		if ($pag and $offset) $CI->db->limit($pag['limit'], $offset);
		else $CI->db->limit($pag['limit']);
		$query = $CI->db->get();

		$out_get_topic['all_topics_in_category'] = $query->result_array();

		$out_get_topic['num_rows'] = $query->num_rows();

		$out_get_topic['pag'] = $pag;

		return $out_get_topic;
	}

	// получаем тему согласно id
	function get_topic_show($out_get_topic_show = array())
	{
		$CI = &get_instance();

		$id = mso_segment(3);
		if (!is_numeric($id)) $id = false;
		else $id = (int) $id;

		// берём тему из базы, согласно $id
		$CI->db->from('dignity_forum_topic');
		$CI->db->where('dignity_forum_topic_id', $id);
		$CI->db->join('comusers', 'comusers.comusers_id = dignity_forum_topic.dignity_forum_topic_сomusers_id', 'left');
		$CI->db->join('users', 'users.users_id = dignity_forum_topic.dignity_forum_topic_users_id', 'left');
		$CI->db->join('dignity_forum_category', 'dignity_forum_category.dignity_forum_category_id = dignity_forum_topic.dignity_forum_topic_category', 'left');
		$query = $CI->db->get();

		$out_get_topic_show['topic'] = $query->result_array();

		$out_get_topic_show['num_rows'] = $query->num_rows();

		return $out_get_topic_show;
	}

	// получить все ответы к топику
	function get_replys_in_topics($out_get_replys_in_topic_show = array())
	{
		$CI = &get_instance();

		$id = mso_segment(3);
		if (!is_numeric($id)) $id = false;
		else $id = (int) $id;

		// готовим пагинацию для ответов
		$pag = array();
		$pag['limit'] = 10;
		$CI->db->select('dignity_forum_reply_id');
		$CI->db->where('dignity_forum_reply_approved', true);
		$CI->db->where('dignity_forum_reply_topic_id', $id);
		$CI->db->from('dignity_forum_reply');
		$query = $CI->db->get();
		$pag_row = $query->num_rows();
		if ($pag_row > 0)
		{
			$pag['maxcount'] = ceil($pag_row / $pag['limit']);

			$current_paged = mso_current_paged();
			if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];
			$offset = $current_paged * $pag['limit'] - $pag['limit'];
		}
		else
		{
			$pag = false;
		}

		// берём ответы из базы, согласно $id
		$CI->db->from('dignity_forum_reply');
		$CI->db->where('dignity_forum_reply_topic_id', $id);
		$CI->db->where('dignity_forum_reply_approved', true);
		$CI->db->join('comusers', 'comusers.comusers_id = dignity_forum_reply.dignity_forum_reply_comusers_id', 'left');
		$CI->db->join('users', 'users.users_id = dignity_forum_reply.dignity_forum_reply_users_id', 'left');
		if ($pag and $offset) $CI->db->limit($pag['limit'], $offset);
		else $CI->db->limit($pag['limit']);	
		$query = $CI->db->get();

		$out_get_replys_in_topic_show['replys'] = $query->result_array();

		$out_get_replys_in_topic_show['num_rows'] = $query->num_rows();

		$out_get_replys_in_topic_show['pag'] = $pag;

		return $out_get_replys_in_topic_show;
	}

	// фуркнция проверки статуса акцивации
	function comuser_activate($activate=false, $comuser_approved = '')
	{
		$CI = & get_instance();
        
	    $CI->db->where('comusers_id', getinfo('comusers_id'));
	    $CI->db->limit(1);
	    $query = $CI->db->get('comusers');
	    $comuser_approved = $query->result_array();
	        
	    if ($comuser_approved)
	    {
	        extract($comuser_approved[0]);
	            
	        if($comusers_activate_string != $comusers_activate_key)
	        {
	            $activate = false;
	        }
	        else
	        {
	            $activate = true;
	        }
	    }
	        
	    return $activate;
	}

	// подключаем редактор markitup
	function editor()
	{
		// подключаем js от редактора markitup
        echo '<script src="'. getinfo('plugins_url') . 'dignity_forum/js/jquery.markitup.js"></script>';

        // подключаем стили
        echo '<link rel="stylesheet" href="'. getinfo('plugins_url') . 'dignity_forum/css/editor.css">';
 
        // настройки редактора
        echo "<script type=\"text/javascript\" >
            var forum_topic_editor_settings =
            {
            
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
                {name:'Видео', openBlockWith:'[video]', closeBlockWith:'[/video]', className:'video'},
                {name:'Ссылка', openBlockWith:'[url]', closeBlockWith:'[/url]', className:'link'}, 
            ],
            
            }
        </script>";
 
        // ципляем редактор к текстареа
        echo '<script type="text/javascript" >
                $(document).ready(function()
                {
                    $(".markItUp").markItUp(forum_topic_editor_settings);
                }
                );
            </script>';
	}

	// парсер bb-code->html
	function bb_parser($content = '')
	{

		// trim|xss|strip_tags|htmlspecialchars
		$content = mso_clean_str($content, 'xss');

		// переопределяем bb-тэги и добавляем новые
        $preg = array(
			// универсальный тэг для видео
			'~\[video\](.*?)\[\/video\]~si' => '<iframe width="640" height="360" src="$1" frameborder="0"></iframe>',

			// опасный тэг, позвоялет исполнять html и js так что блокируем его
			'~\[html\](.*?)\[\/html\]~si' => t('Заблокировано!', __FILE__),

			// тэг позволяет исполнять php, если вклечен плагин "run_php", поэтому блокируем его
			'~\[php\](.*?)\[\/php\]~si' => t('Заблокировано!', __FILE__),
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
