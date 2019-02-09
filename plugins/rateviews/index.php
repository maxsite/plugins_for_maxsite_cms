<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function rateviews_autoload($args = array())
{
return $args;
}

# функция выполняется при деактивации (выкл) плагина
function rateviews_deactivate($args = array())
{	
	//mso_delete_option('plugin_pageviews', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при деинстяляции плагина
function rateviews_uninstall($args = array())
{	
	//mso_delete_option('plugin_pageviews', 'plugins'); // удалим созданные опции
	//mso_remove_allow('pageviews_edit'); // удалим созданные разрешения
	return $args;
}

#Функции настройки видеоплеера в админке сайта
function rateviews_mso_options() 
{
    mso_admin_plugin_options('plugin_rateviews', 'plugins',
        array(
            'minvoices' => array(
                            'type' => 'text',
                            'name' => t('Минимальное количество голосов для выборки.', __FILE__),
                            'description' => t('Если у Вас на сайте много страниц, то Вам может понадобиться сократить выбор для сортировки. Здесь Вы можете указать минимальное количество голосов (страницы с меньшим значением в сортировку не попадут. По умолчанию 1.)', __FILE__),
                            'default' => '1'
                        ),
           'asc' => array(
                            'type' => 'select',
                            'name' => t('Прямой или обратный порядок', __FILE__),
                            'description' => t('Выберите, каким образом будет сортироваться список - от минимального с максимальному рейтингу или от максимального к минимальному. По умолчанию второй вариант.', __FILE__),
                            'default' => 'desc',
			    'values' =>  'asc # desc'
                        ),
            'limit' => array(
                            'type' => 'text',
                            'name' => t('Количество ссылок в списке', __FILE__),
                            'description' => t('Укажите здесь, сколько Вы бы хотели ссылок в списке самых рейтинговых страниц отображалось на странице rateviews. По умолчанию отображается 30 ссылок.', __FILE__),
                            'default' => '30'
                        ),
            ),
        t('Настройки плагина «pageviews»', __FILE__),
        t('Укажите необходимые опции. Учтите, что страница rateviews кэшируется, поэтому для того, чтобы увидеть сохраненнные изменения на самом сайте, Вам необходимо будет очистить кэш системы.Подробная информация об установке и работе с плагином - <a href="">на странице плагина</a>.', __FILE__)
    );
}

# явный вызов функции - отдается карта сайта
function rateviews($arg = array())
{
        $options = mso_get_option('plugin_rateviews', 'plugins', array() );
        if ( !isset($options['minvoices']) ) $options['minvoices'] = '1';
        if ( !isset($options['asc']) ) $options['asc'] = 'desc';
        if ( !isset($options['limit']) ) $options['limit'] = '30';

	global $MSO;

	// кэш строим по url, потому что он меняется от пагинации
	$cache_key = 'rateviews' . serialize($MSO->data['uri_segment']);
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	# получаем все записи как есть
	
	$curdate = time();

	$CI = & get_instance();
	$CI->db->select('page_slug, page_title, page_id, page_rating/page_rating_count AS page_ball, page_rating, page_rating_count', false);
	$CI->db->where('page_status', 'publish');
	$CI->db->where('page_rating_count > ', '' . $options['minvoices'] .'');
        $CI->db->order_by('page_ball', '' . $options['asc'] .'');
	$CI->db->order_by('page_rating', '' . $options['asc'] .'');
        $CI->db->limit('' . $options['limit'] .'');
	
	$query = $CI->db->get('page');
	
	if ($query->num_rows() > 0)
	{   
        $out = '';
        foreach ($query->result_array() as $page) // обходим в цикле
        {
         // формируем вывод - просто пример
         $slug = mso_slug($page['page_slug']);
         $out .= '<li><a href="' . getinfo('siteurl') . 'page/' . $slug . '">' . $page['page_title'] . '</a>. Рейтинг/голосов: ' . $page['page_ball'] . '/ ' . $page['page_rating_count'] . '.</li>';
         }
	
        $pagination['type'] = '';
	ob_start();
	mso_hook('pagination', $pagination);
	$out .=  ob_get_contents();
	ob_end_clean();
       echo $out;
}

	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;
}
?>