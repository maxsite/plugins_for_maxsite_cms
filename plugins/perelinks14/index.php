<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function perelinks_autoload($args = array())
{
	mso_create_allow('perelinks_edit', t('Доступ к настройкам «perelinks»', __FILE__));
	mso_hook_add( 'content_content', 'perelinks_custom'); # хук на админку
	mso_hook_add( 'admin_init', 'perelinks_admin_init'); # хук на админку
}


function perelinks_uninstall($args = array())
{
	mso_delete_option('plugin_perelinks', 'plugins'); // удалим созданные опции
	return $args;
}


# функция выполняется при указаном хуке admin_init
function perelinks_admin_init($args = array())
{
	if ( !mso_check_allow('perelinks_edit') )
	{
		return $args;
	}

	$this_plugin_url = 'perelinks'; // url и hook
	mso_admin_menu_add('plugins', $this_plugin_url, t('Плагин perelinks', __FILE__));
	mso_admin_url_hook ($this_plugin_url, 'perelinks_admin_page');

	return $args;
}



# функция вызываемая при хуке, указанном в mso_admin_url_hook
function perelinks_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('perelinks_edit') )
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}

	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Плагин perelinks', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Плагин perelinks', __FILE__) . ' - " . $args; ' );

	require(getinfo('plugins_dir') . 'perelinks/admin.php');
}


# функции плагина
function perelinks_custom($content = '')
{
	// получаем список всех титлов - возможно из кэша
	// после этого выполняем замену всех этих вхождений в тексте на ссылки

	//Опции статические, получаем только единожды
	if ( !isset($perelinks_options) ) {static $perelinks_options; $perelinks_options = mso_get_option('perelinks', 'plugins', array() );}

	$perelinks_options['linkcount'] = isset($perelinks_options['linkcount']) ? (int)$perelinks_options['linkcount'] : 0;
	$perelinks_options['wordcount'] = isset($perelinks_options['wordcount']) ? (int)$perelinks_options['wordcount'] : 0;
	$perelinks_options['allowlate'] = isset($perelinks_options['allowlate']) ? (int)$perelinks_options['allowlate'] : 1;
	$perelinks_options['pagetypes'] = isset($perelinks_options['pagetypes']) ? (int)$perelinks_options['pagetypes'] : 1;
	$perelinks_options['only_page'] = isset($perelinks_options['only_page']) ? (int)$perelinks_options['only_page'] : 0;
	if ($perelinks_options['only_page'] and !is_type('page')) return  $content; //Если мы не на странице и соответствующая опция, выходим.
	//if (!is_type('page')) $perelinks_options['pagetypes'] = 2; //Кого куда совпадать? Не, и без этого работает нормально.
	$perelinks_options['stopwords'] = isset($perelinks_options['stopwords']) ? $perelinks_options['stopwords'] : 'будет нужно';
	if (isset($perelinks_options['stopwords'])) $stopwords = explode(' ', $perelinks_options['stopwords']);

	global $page; // текущая страница - это массив

	//Разные кэши для разных типов страниц. И один общий.
	switch ($perelinks_options['pagetypes'])
	{
		case 1: $cache_key = 'perelinks_custom'; break;
		case 2: $cache_key = 'perelinks_custom_blog'; break;
		case 3: $cache_key = 'perelinks_custom_static'; break;
		case 4: $cache_key = 'perelinks_custom_' . $page['page_type_name']; break;
	}

	//А кэш у нас вообще почему-то не работает.
	//if ( $k = mso_get_cache($cache_key) )
	if (false)
	{
		$all_title = $k;
	}
	else
	{
		$CI = & get_instance();

		//Восполняем пробел, заполняем $page['page_type_id']
		if ($page['page_type_name'] == 'blog') $page['page_type_id'] = 1;
		elseif ($page['page_type_name'] == 'static') $page['page_type_id'] = 2;

		//И только если у нас есть страницы нестандартного типа и на них нужно ссылаться, выполняем этот иф.
		if ( ($page['page_type_name'] != 'blog') and ($page['page_type_name'] != 'static') and ($perelinks_options['pagetypes'] > 3) )
		{
			if (!isset($perelinks_types)) //Статическая переменная, чтобы максимум один запрос в базу.
			{
				static $perelinks_types;
				$query = $CI->db->get_where('page_type');
				$perelinks_types = $query->result_array();
			}
			foreach ($perelinks_types as $page_type)
			{
				if ($page['page_type_name'] == $page_type['page_type_name'])
				{
					$page['page_type_id'] = $page_type['page_type_id'];
					break;
				}
			}
		}

		$CI->db->select('page_title, page_slug');

		//А тут большая засада с кэшом.
		if ($perelinks_options['allowlate'] > 0)
		{
			//$CI->db->where('page_date_publish <', date('Y-m-d H:i:s'));
			$CI->db->where('page_date_publish <', 'NOW()', false); //А что не так?
		}
		else
		{
			$CI->db->where('page_date_publish <', $page['page_date_publish']);
		}

		//В какой кэш мы пишем, то условие нам и нужно.
		switch ($perelinks_options['pagetypes'])
		{
			case 1: break;
			case 2: $CI->db-where('page_type_id', 1); break;
			case 3: $CI->db-where('page_type_id', 2); break;
			case 4: $CI->db-where('page_type_id', $page['page_type_id']); break; //А тут у нас вышезаполненные блог, статик, или нестандартный
		}

		$CI->db->where('page_status', 'publish');
		$CI->db->from('page');
		$query = $CI->db->get();

		$all_title = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$title = mb_strtolower($row['page_title'], 'UTF-8');
				$title = str_replace(array('\\', '|', '/', '?', '%', '*', '`', ',', '.', '$', '!', '\'', '"', '«', '»', '—') , '', $title);

				$a_words = explode(' ', $title);
				$a_words = array_unique($a_words);

				$title = array();
				foreach ($a_words as $word)
				{
					if ((mb_strlen($word, 'UTF-8') > 3) and (!in_array($word, $stopwords))) $title[] = $word;
				}

				foreach ($title as $word)
				{
					$all_title[$word][] = $row['page_slug'];
				}
			}
		}
		mso_add_cache($cache_key, $all_title);
	}

	$curr_page_slug = $page['page_slug']; // текущая страница - для ссылки
	$my_site = getinfo('siteurl') . 'page/';

	// ищем вхождения
	$linkcounter = 0;
	foreach ($all_title as $key => $word)
	{

		$r = '| (' . preg_quote($key) . ') |siu';

		if ( preg_match($r , $content) )
		{
			if (!in_array($curr_page_slug, $word))
			{
				if ($perelinks_options['wordcount'] > 0) $r = '| (' . preg_quote($key) . ') (.*$)|siu'; //Если только первое найденное слово-дубликат делать ссылкой
				$content = preg_replace($r, ' <a href="' . $my_site . $word[0] . '" class="perelink">\1</a> \2', $content);
				$linkcounter++;
			}
		}

		if (($linkcounter > 0) and ($linkcounter == $perelinks_options['linkcount'])) break;

	}

	return  $content;
}

?>