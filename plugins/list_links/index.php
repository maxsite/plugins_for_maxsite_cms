<?php if (!defined('BASEPATH')) exit('No direct script access allowed');



# функция автоподключения плагина
function list_links_autoload($args = array())
{
	mso_hook_add('edit_category', 'list_links_custom');
	mso_hook_add('new_category', 'list_links_custom');
	mso_hook_add('delete_category', 'list_links_custom');
	mso_hook_add('new_page', 'list_links_custom');
	mso_hook_add('edit_page', 'list_links_custom');
}

# функция выполняется при активации (вкл) плагина
function list_links_activate($args = array())
{
	mso_create_allow('list_links_to_hook_edit', t('Админ-доступ к настройкам', 'plugins') . ' «' . t('Списка ссылок') . '»');
	list_links_custom();
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function list_links_to_hook_deactivate($args = array())
{
	mso_delete_option('plugin_list_links', 'plugins'); # удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function list_links_uninstall($args = array())
{
	mso_remove_allow('list_links_to_hook_edit'); # удалим созданные разрешения
	return $args;
}

# функция плагина - создание link.txt
function list_links_custom($args = array())
{
	// Настройки по-умолчанию
	$options = mso_get_option('plugin_list_links', 'plugins', array());

	if(!isset($options['page_hide'])) $options['page_hide'] = '';
	$options['page_hide'] = mso_explode($options['page_hide']);

	if(!isset($options['page_cats_hide'])) $options['page_cats_hide'] = '';
	$options['page_cats_hide'] = mso_explode($options['page_cats_hide']);

	if(!isset($options['categories_show'])) $options['categories_show'] = '';
	$options['categories_show'] = mso_explode($options['categories_show']);

    if(!isset($options['cats_show'])) $options['cats_show'] = true;
	if(!isset($options['tags_show'])) $options['tags_show'] = true;
	if(!isset($options['comusers_show'])) $options['comusers_show'] = true;
	if(!isset($options['users_show'])) $options['users_show'] = true;

	if(!isset($options['url_protocol'])) $options['url_protocol'] = '';

	if(!isset($options['custom_urls'])) $options['custom_urls'] = '';
	$options['custom_urls'] = array_map('trim', explode(NR, trim($options['custom_urls'])));

	$CI = & get_instance();
	$CI->load->helper('file'); // хелпер для работы с файлами

	$url = getinfo('siteurl');

	if ($options['url_protocol'])
	{
		$url = str_replace('http://', '', $url);
		$url = str_replace('https://', '', $url);
		$url = $options['url_protocol'] . $url;
	}

	// формирование links.txt
	$out = '' . $url . '' . NR;


	// страницы notblog
	$CI->db->select('page.page_id, page_slug, page_date_publish');
	$CI->db->from('page');

	if(count($options['page_cats_hide']) > 0)
	{
		$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id', 'left');
		$CI->db->where('( category_id NOT IN ('.implode(',', $options['page_cats_hide']).') or category_id IS NULL )');
	}

	if(count($options['page_hide']) > 0)
	{
		$CI->db->where_not_in('page.page_id', $options['page_hide']);
	}

	$CI->db->where('page_type_name !=', 'blog');
	$CI->db->where('page_status', 'publish');
	//$CI->db->where('page_date_publish < ', 'DATE_ADD(NOW(), INTERVAL "' . $tz . '" HOUR_MINUTE)', false);
	//$CI->db->where('page_date_publish <', mso_date_convert('Y-m-d H:i:s', date('Y-m-d H:i:s')));

	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id', 'left');
	//$CI->db->order_by('page_date_publish', 'desc');
	$CI->db->group_by('page.page_id');

	$query = $CI->db->get();
	if ($query->num_rows()>0)
	{
		foreach ($query->result_array() as $row)
		{

			$out .= '' . $url . 'page/' . $row['page_slug'] . '' . NR;

		}
	}

	// страницы blog
	$CI->db->select('page.page_id, page_slug, page_date_publish');
	$CI->db->from('page');

	if(count($options['page_cats_hide']) > 0)
	{
		$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id', 'left');
		$CI->db->where('( category_id NOT IN ('.implode(',', $options['page_cats_hide']).') or category_id IS NULL )');
	}

	if(count($options['page_hide']) > 0)
	{
		$CI->db->where_not_in('page.page_id', $options['page_hide']);
	}

	$CI->db->where('page_type_name', 'blog');
	$CI->db->where('page_status', 'publish');
	//$CI->db->where('page_date_publish < ', 'DATE_ADD(NOW(), INTERVAL "' . $tz . '" HOUR_MINUTE)', false);
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id', 'left');
	$CI->db->group_by('page.page_id');
	$CI->db->order_by('page_date_publish', 'desc');
	$query = $CI->db->get();

	if ($query->num_rows()>0)
	{
		foreach ($query->result_array() as $row)
		{

			$out .= $url . 'page/' . $row['page_slug'] . NR;
		}
	}

    // рубрики
    if($options['cats_show'])
	{
		if(count($options['categories_show']) > 0) $CI->db->or_where_in('category_id', $options['categories_show']);

		$CI->db->where('category_type', 'page');

		$query = $CI->db->get('category');

		if ($query->num_rows()>0)
		{
			foreach ($query->result_array() as $row)
			{
				$out .= $url . 'category/' . $row['category_slug'] . NR;

			}
		}
    }

	// все метки
	if($options['tags_show'])
	{
		require_once( getinfo('common_dir') . 'meta.php' );

		$alltags = mso_get_all_tags_page();

		foreach ($alltags as $tag => $count)
		{
			$out .= $url . 'tag/' . htmlentities(urlencode($tag)) . NR;

		}
	}

	// все комюзеры
	if($options['comusers_show'])
	{
		$CI->db->select('comusers_id');

		$query = $CI->db->get('comusers');

		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$out .= $url . 'users/' . $row['comusers_id'] . NR;
			}
		}
	}

	// все юзеры
	if($options['users_show'])
	{
		$CI->db->select('users_id');

		$query = $CI->db->get('users');

		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{

				$out .= $url . 'author/' . $row['users_id'] . NR;

			}
		}
	}

	$out .= mso_hook('list_links'); # хук, если нужно добавить свои данные


	$out = mso_hook('list_links_conv', $out); # хук, если нужно как-то обработать результирующий файл. Например, разбить на части

	$fn = getinfo('FCPATH') . 'links.txt';
	write_file($fn, $out);

	return $args; // для обеспечения цепочки хуков
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
function list_links_mso_options()
{
	if(!mso_check_allow('list_links_to_hook_edit'))
	{
		echo t('Доступ запрещен');
		return;
	}

    # ключ, тип, ключи массива
    mso_admin_plugin_options('plugin_list_links', 'plugins',
        array(

            'cats_show' => array(
                            'type' => 'checkbox',
                            'name' => t('Добавить страницы рубрик'),
                            'description' => '',
                            'default' => 0,
							'group_start' => '<hr>',
					  ),

			'tags_show' => array(
                            'type' => 'checkbox',
                            'name' => t('Добавить страницы меток'),
                            'description' => '',
                            'default' => 0,
						),

            'comusers_show' => array(
                            'type' => 'checkbox',
                            'name' => t('Добавить страницы комюзеров (комментаторов)'),
                            'description' => '',
                            'default' => 0
                        ),

            'users_show' => array(
                            'type' => 'checkbox',
                            'name' => t('Добавить страницы авторов'),
                            'description' => '',
                            'default' => 0,
							'group_end' => '<hr>',
                        ),

            'page_hide' => array(
                            'type' => 'text',
                            'name' => t('Исключить страницы (записи)'),
                            'description' => t('Перечислите через запятую ID записей, которые <b>не будут</b> добавлены в link.txt'),
                            'default' => ''
                        ),


			'url_protocol' => array(
                            'type' => 'select',
                            'name' => t('HTTP протокол сайта'),
							'values' => '||Не менять # http://||http # https://||https',
                            'description' => t('Можно явно задать протокол сайт.'),
                            'default' => ''
                        ),

            ),
		t('Настройки списка ссылок сайта'), # Заголовок страницы с настройками плагина

		t('C помощью настроек плагина можно формировать список нужных адресов страниц в файле <a href="' . getinfo('site_url') . '/links.txt"><b>links.txt</b></a>')  // инфа
    );

	if ($_POST) list_links_custom();
}

# end of file