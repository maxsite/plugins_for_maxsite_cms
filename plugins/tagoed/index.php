<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function tagoed_autoload()
{
	mso_create_allow('tagoed_edit', t('Админ-доступ к ТегоРедактору', __FILE__));
	mso_hook_add( 'admin_init', 'tagoed_admin_init'); # хук на админку
}

# функция выполняется при активации (вкл) плагина
function tagoed_activate($args = array())
{
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function tagoed_deactivate($args = array())
{
	// mso_delete_option('plugin_tagoed', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function tagoed_uninstall($args = array())
{
	// mso_delete_option('plugin_tagoed', 'plugins'); // удалим созданные опции
	mso_remove_allow('tagoed_edit'); // удалим созданные разрешения
	return $args;
}

# функция выполняется при указаном хуке admin_init
function tagoed_admin_init($args = array())
{
	if ( !mso_check_allow('tagoed_edit') )
	{
		return $args;
	}

	$this_plugin_url = 'plugin_tagoed'; // url и hook

	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки

	mso_admin_menu_add('plugins', $this_plugin_url, t('ТегоРедактор', __FILE__));

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url)
	# связанную функцию именно она будет вызываться, когда
	# будет идти обращение по адресу http://сайт/admin/tagoed
	mso_admin_url_hook ($this_plugin_url, 'tagoed_admin_page');

	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function tagoed_admin_page($args = array())
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('tagoed_edit') )
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}

	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('ТегоРедактор', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('ТегоРедактор', __FILE__) . ' - " . $args; ' );

	require(getinfo('plugins_dir') . 'tagoed/admin.php');
}


# функции плагина

/**
 * Выводит список всех тегов в системе
 * @return array() $tagList
 */
function tagoed_tag_list_all()
{
	$CI = & get_instance();

	$cache_key = 'tagoed_tags_full';
	$time = 60*60*24;
	//Выбираем все теги из таблицы meta
	//Смотрим в кэш и достаём данные
	$result = mso_get_cache($cache_key, true);
	if ($result)
	{
		return $result;
	}
	else
	{
		//Если данные протухли, сформируем их заново
		$CI->db->select('meta_id, meta_value');
		$CI->db->from('meta');
		$CI->db->where('meta_key', 'tags');
		$result_query = $CI->db->get();
		if ($result_query->num_rows() > 0)
		{
			$tagList = array();
			foreach ($result_query->result_array() as $tag)
			{
				$id = $tag['meta_id'];
				$value = $tag['meta_value'];
				if (array_key_exists($value, $tagList))
				{
					$tagList[$value] .= ',' . $id;
				}
				else
				{
					$tagList[$value] = $id;
				}
			}
			//Добавляем данные в кэш
			mso_add_cache($cache_key, $tagList, $time, true);
			return $tagList;
		}
		else
		{
			return array();
		}
	}
}

/**
 * Выводит список статей, содержащих этот тег
 * @param integer $tag
 * @return array() $articleList
 */
function tagoed_tag_list_articles($tag = '')
{
	$CI = & get_instance();

	$cache_key = 'tagoed_articles_' . mso_md5($tag);
	$time = 60*60*24;
	//Выбираем все статьи из таблицы page
	//Смотрим в кэш и достаём данные
	$result = mso_get_cache($cache_key, true);
	if ($result)
	{
		return $result;
	}
	else
	{
		//Если данные протухли, сформируем их заново
		//Работает в божественном присутствии
/*
		$CI->db->select('meta_id_obj');
		$CI->db->from('meta');
		$CI->db->where('meta_value', $tag);
		$CI->db->where('meta_key', 'tags');
		$subquery = $CI->db->_compile_select();
		$CI->db->_reset_select();
		$subquery = str_replace("\n"," ",$subquery);

		$CI->db->select('page_id, page_title, meta_id, meta_value');
		$CI->db->from('page');
		$CI->db->join('meta', 'page_id = meta_id_obj', 'inner');
		$CI->db->where_in('meta_id_obj', $subquery, false);

		$result_query = $CI->db->get();
*/

		//Работает в случае смертных
		$sql = 'SELECT page_id, page_title, meta_id, meta_value
						FROM mso_page
						INNER JOIN mso_meta
						ON page_id = meta_id_obj
						WHERE meta_id_obj
						IN (
								SELECT meta_id_obj
								FROM mso_meta
								WHERE meta_value = "' . $tag . '"
								AND meta_key = "tags"
						)
						AND meta_key = "tags"';
		$result_query = $CI->db->query($sql);

		if ($result_query->num_rows() > 0)
		{
			$articleList = array();
			foreach ($result_query->result_array() as $article)
			{
				$id = $article['page_id'];
				$title = $article['page_title'];
				$tag_id = $article['meta_id'];
				$tag_name = $article['meta_value'];
				if (array_key_exists($id, $articleList))
				{
					$tags_array = $articleList[$id]['1'];
					$tags_array[$tag_id] = $tag_name;
					$articleList[$id] = array($title, $tags_array);
				}
				else
				{
					$articleList[$id] = array($title, array($tag_id => $tag_name));
				}
			}
			//Добавляем данные в кэш
			mso_add_cache($cache_key, $articleList, $time, true);
			return $articleList;
		}
		else
		{
			return array();
		}
	}
}

/**
 * Удаляет тег
 * @param integer $tag
 * @return boolean $result
 */
function tagoed_tag_delete($tag = '')
{
	$CI = & get_instance();

	//Протухаем кэш
	mso_flush_cache_mask('tagoed_tags_full');
	mso_flush_cache_mask('tagoed_articles_full');
	mso_flush_cache_mask('tagoed_articles_' . mso_md5($tag));

	//Удаляем тег
	$CI->db->where('meta_value', $tag);
	$CI->db->where('meta_key', 'tags');
	$CI->db->delete('meta');
	return true;
}

/**
 * Переименовывает тег
 * @param integer $tag
 * @return boolean $result
 */
function tagoed_tag_rename($old_tag = '', $new_tag = '')
{
	$CI = & get_instance();

	//Протухаем кэш
	mso_flush_cache_mask('tagoed_tags_full');
	mso_flush_cache_mask('tagoed_articles_full');
	mso_flush_cache_mask('tagoed_articles_' . mso_md5($old_tag));

	//Переименовываем тег
	$CI->db->set('meta_value', $new_tag);
	$CI->db->where('meta_value', $old_tag);
	$CI->db->where('meta_key', 'tags');
	$CI->db->update('meta');
	return true;
}

/**
 * Выводит список всех статей в системе
 * @param array() $par
 * @return array() $articleList
 */
function tagoed_article_list_all()
{
	$CI = & get_instance();

	$cache_key = 'tagoed_articles_full';
	$time = 60*60*24;
	//Выбираем все статьи из таблицы page
	//Смотрим в кэш и достаём данные
	$result = mso_get_cache($cache_key, true);
	if ($result)
	{
		return $result;
	}
	else
	{

		//Если данные протухли, сформируем их заново
		$CI->db->select('page_id, page_title, meta_id, meta_value, meta_key');
		$CI->db->from('page');
		$CI->db->join('meta', 'page_id = meta_id_obj', 'left');
		$result_query = $CI->db->get();

		if ($result_query->num_rows() > 0)
		{
			$articleList = array();
			foreach ($result_query->result_array() as $article)
			{
				$id = $article['page_id'];
				$title = $article['page_title'];
				//Если есть теги, отобразим их
				if ($article['meta_key'] == 'tags')
				{
					$tag_id = $article['meta_id'];
					$tag_name = $article['meta_value'];
					//Теги уже есть, записываем в массив
					if (array_key_exists($id, $articleList))
					{
						$tags_array = $articleList[$id]['1'];
						$tags_array[$tag_id] = $tag_name;
						$articleList[$id] = array($title, $tags_array);
					}
					else
					{
						$articleList[$id] = array($title, array($tag_id => $tag_name));
					}
				}
				//Иначе просто покажем заголовок статьи
				else
				{
					$articleList[$id] = array($title, array());
				}
			}
			//Добавляем данные в кэш
			mso_add_cache($cache_key, $articleList, $time, true);
			return $articleList;
		}
		else
		{
			return array();
		}
	}
}

/**
 * Добавляет тег к статье
 * @param integer $article
 * @param integer $tag
 * @return boolean $result
 */
function tagoed_article_tag_add($id_article = '', $new_tag = '')
{
	$CI = & get_instance();

	//Протухаем кэш
	mso_flush_cache_mask('tagoed_tags_full');
	mso_flush_cache_mask('tagoed_articles_full');

	//Добавляем тег
	$data = array(
								'meta_key' => 'tags',
								'meta_id_obj' => $id_article,
								'meta_table' => 'page',
								'meta_value' => $new_tag
							);
	$CI->db->insert('meta', $data);

	return true;
}

/**
 * Удаляет тег у статьи
 * @param integer $article
 * @param integer $tag
 * @return boolean $result
 */
function tagoed_article_tag_delete($id_article = '', $tag_name = '')
{
	$CI = & get_instance();

	//Протухаем кэш
	mso_flush_cache_mask('tagoed_tags_full');
	mso_flush_cache_mask('tagoed_articles_full');

	//Удаляем тег
	$CI->db->where('meta_value', $tag_name);
	$CI->db->where('meta_id_obj', $id_article);
	$CI->db->where('meta_key', 'tags');

	$CI->db->delete('meta');

	return true;
}

/**
 * Возвращает заголовок статьи по id
 * @param integer $id_article
 * @return string $article_title
 */
function tagoed_article_get_title($id_article = '')
{
	$CI = & get_instance();

	$CI->db->select('page_title');
	$CI->db->from('page');
	$CI->db->where('page_id', $id_article);

	$result_query = $CI->db->get();
	if ($result_query->num_rows() > 0)
	{
		$article = $result_query->row();
		$article_title = $article->page_title;
		return $article_title;
	}
}

/**
 * Возвращает ссылку для вставки в таблицу
 * @param string $title
 * @param string $relative_url
 * @param string $id
 * @param string $name
 * @return string $link
 */
function tagoed_link_create($title, $relative_url, $id, $name)
{
	$absolute_url = getinfo('site_admin_url') . 'plugin_tagoed/' . $relative_url;
	$link = '<span style="text-decoration: underline;" title="' . $title . '"><strong><a href="' . $absolute_url . $id . '">' . $name . '</a></strong></span>';
	return $link;
}

?>