<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Alexander Schilling
 * (c) http://alexanderschilling.net
 * License GNU GPL 2+
 */


# функция автоподключения плагина
function statistic_autoload()
{
	mso_register_widget('statistic_widget', t('Статистика') ); # регистрируем виджет

	mso_hook_add( 'admin_init', 'statistic_admin_init'); # хук на админку
}

// функция выполняется при активации (вкл) плагина
function statistic_activate($args = array())
{	
	mso_create_allow('statistic_edit', t('Админ-доступ к настройкам') . ' ' . t('Статистика'));

	return $args;
}

# функция выполняется при деинсталяции плагина
function statistic_uninstall($args = array())
{	
	mso_delete_option_mask('statistic_widget_', 'plugins' ); // удалим созданные опции

	mso_remove_allow('statistic_edit'); // удалим созданные разрешения

	return $args;
}

# функция выполняется при указаном хуке admin_init
function statistic_admin_init($args = array()) 
{
	if ( mso_check_allow('statistic_edit') ) 
	{
		$this_plugin_url = 'statistic'; // url и hook
		
		# добавляем свой пункт в меню админки
		# первый параметр - группа в меню
		# второй - это действие/адрес в url - http://сайт/admin/demo
		#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
		# Третий - название ссылки	
		
		mso_admin_menu_add('plugins', $this_plugin_url, t('Статистика'));

		# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
		# связанную функцию именно она будет вызываться, когда 
		# будет идти обращение по адресу http://сайт/admin/%%%
		mso_admin_url_hook ($this_plugin_url, 'statistic_admin_page');
	}
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function statistic_admin_page($args = array()) 
{
	if ( !mso_check_allow('statistic_edit') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}

	# выносим админские функции отдельно в файл
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Статистика') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Статистика') . ' - " . $args; ' );
	require(getinfo('plugins_dir') . 'statistic/admin.php');
}

# функция, которая берет настройки из опций виджетов
function statistic_widget($num = 1) 
{
	$widget = 'statistic_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';

	if (isset($options['textdo']) ) $options['textdo'] = '<p>' . $options['textdo'] . '</p>';
	else $options['textdo'] = '';

	if (isset($options['textposle']) ) $options['textposle'] = '<p>' . $options['textposle'] . '</p>';
	else $options['textposle'] = '';

	if (isset($options['usuarios_online_code']) ) $options['usuarios_online_code'] = $options['usuarios_online_code'];
	else $options['usuarios_online_code'] = '';

	if (isset($options['online_from']) ) $options['online_from'] = $options['online_from'];
	else $options['online_from'] = '';
	
	return statistic_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function statistic_widget_form($num = 1) 
{
	$widget = 'statistic_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = t('Статистика', __FILE__);
	if ( !isset($options['textdo']) ) $options['textdo'] = '';
	if ( !isset($options['textposle']) ) $options['textposle'] = '';
	if ( !isset($options['usuarios_online_code']) ) $options['usuarios_online_code'] = '';
	if ( !isset($options['online_from']) ) $options['online_from'] = 'Jul 26, 2012';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header']), t('Подсказка')));

	$form .= '<p><div class="t150">' . t('Текст вначале:', __FILE__) . '</div> '. form_textarea( array( 'name'=>$widget . 'textdo', 'value'=>$options['textdo'] ) ) ;

	$form .= '<p><div class="t150">' . t('Текст в конце:', __FILE__) . '</div> '. form_textarea( array( 'name'=>$widget . 'textposle', 'value'=>$options['textposle'] ) ) ;

	$form .= '<p><div class="t150">' . t('Код получиный на сайте: ', __FILE__) . '<a href="http://usuarios-online.com/" target="_blank">http://usuarios-online.com/</a>' . '</div> '. form_textarea( array( 'name'=>$widget . 'usuarios_online_code', 'value'=>$options['usuarios_online_code'] ) ) ;

	$form .= '<p><div class="t150">' . t('Начальная дата: ', __FILE__) . '</div> '. form_input( array( 'name'=>$widget . 'online_from', 'value'=>$options['online_from'] ) ) ;

	// $form .= mso_widget_create_form(t(''), , t(''));

	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function statistic_widget_update($num = 1) 
{
	$widget = 'statistic_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['textdo'] = mso_widget_get_post($widget . 'textdo');
	$newoptions['textposle'] = mso_widget_get_post($widget . 'textposle');
	$newoptions['usuarios_online_code'] = mso_widget_get_post($widget . 'usuarios_online_code');
	$newoptions['online_from'] = mso_widget_get_post($widget . 'online_from');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функции плагина
function statistic_widget_custom($options = array(), $num = 1)
{
	/*
	// кэш 
	$cache_key = 'statistic_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	*/

	$out = '';

	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['textdo']) ) $options['textdo'] = '';
	if ( !isset($options['textposle']) ) $options['textposle'] = '';
	if ( !isset($options['usuarios_online_code']) ) $options['usuarios_online_code'] = '';
	if ( !isset($options['online_from']) ) $options['online_from'] = 'Jul 26, 2012';

	// опции из админки
	$options_admin = mso_get_option('plugin_statistic', 'plugins', array());
	if (!isset($options_admin['hide_users']))  $options_admin['hide_users'] = true;
	if (!isset($options_admin['hide_comusers']))  $options_admin['hide_comusers'] = false;
	if (!isset($options_admin['hide_active_comusers']))  $options_admin['hide_active_comusers'] = false;
	if (!isset($options_admin['hide_no_active_comusers']))  $options_admin['hide_no_active_comusers'] = false;
	if (!isset($options_admin['hide_article_dignity_blogs']))  $options_admin['hide_article_dignity_blogs'] = false;
	if (!isset($options_admin['hide_comments_dignity_blogs']))  $options_admin['hide_comments_dignity_blogs'] = false;
	if (!isset($options_admin['hide_topic_dignity_forum']))  $options_admin['hide_topic_dignity_forum'] = false;
	if (!isset($options_admin['hide_reply_dignity_forum']))  $options_admin['hide_reply_dignity_forum'] = false;
	if (!isset($options_admin['hide_video_dignity_video']))  $options_admin['hide_video_dignity_video'] = false;
	if (!isset($options_admin['hide_comments_dignity_video']))  $options_admin['hide_comments_dignity_video'] = false;
	if (!isset($options_admin['hide_joke_dignity_joke']))  $options_admin['hide_joke_dignity_joke'] = false;
	if (!isset($options_admin['hide_comments_dignity_joke']))  $options_admin['hide_comments_dignity_joke'] = false;
	if (!isset($options_admin['hide_soft_dignity_soft']))  $options_admin['hide_soft_dignity_soft'] = false;
	if (!isset($options_admin['hide_comments_dignity_soft']))  $options_admin['hide_comments_dignity_soft'] = false;

	// заголовок
	$out .= $options['header'];

	// текст до
	$out .= $options['textdo'];

	// получаем доступ к CI
	$CI = &get_instance();

	// если выбрана опция скрыть админов
	if (!$options_admin['hide_users'])
	{
		// всего администраторов
	    $CI->db->from('users');
	    $out .= '<p>' . t('Всего администраторов: ', __FILE__) . $CI->db->count_all_results() . '</p>';
	}

	// если выбрана опция скрыть комюзеро
	if (!$options_admin['hide_comusers'])
	{
		// всего пользователей (комюзеров)
    	$CI->db->from('comusers');
    	$out .= '<p>' . t('Всего пользователей: ', __FILE__) . $CI->db->count_all_results() . '</p>';
	}

	// если выбрана опция скрыть активных комюзеров
	if (!$options_admin['hide_active_comusers'])
	{
	    // активных пользователей (комюзеров)
	    $CI->db->from('comusers');
	    $CI->db->where('comusers_activate_string !=', '');
	    $out .= '<p>' . t('Активных: ', __FILE__) . $CI->db->count_all_results() . '</p>';
    }

    if (!$options_admin['hide_no_active_comusers'])
    {
    	// не активных пользователей (комюзеров)
	    $CI->db->from('comusers');
	    $CI->db->where('comusers_activate_string', '');
	    $out .= '<p>' . t('Заблудившихся: ', __FILE__) . $CI->db->count_all_results() . '</p>';
    }

    global $MSO;

    // если плагин включен, выводим статистику
    if (in_array('dignity_blogs', $MSO->active_plugins))
	{
		if (!$options_admin['hide_article_dignity_blogs'])
		{
			// количество статей в блогах
		  	$CI->db->from('dignity_blogs');
		    $CI->db->where('dignity_blogs_approved', 1);
		    $out .= '<p>' . t('Статей в блогах: ', __FILE__) . $CI->db->count_all_results() . '</p>';
		}

		if (!$options_admin['hide_comments_dignity_blogs'])
		{
			// количество комментариев в блогах
		    $CI->db->from('dignity_blogs_comments');
		    $CI->db->where('dignity_blogs_comments_approved', 1);
		    $out .= '<p>' . t('Комментариев в блогах: ', __FILE__) . $CI->db->count_all_results() . '</p>';
		}
	}

	// если влючен плагин, выводим статистику
	if (in_array('dignity_forum', $MSO->active_plugins))
	{
		if (!$options_admin['hide_topic_dignity_forum'])
		{
			// количество тем на форуме
		  	$CI->db->from('dignity_forum_topic');
		    $CI->db->where('dignity_forum_topic_approved', 1);
		    $out .= '<p>' . t('Тем на форуме: ', __FILE__) . $CI->db->count_all_results() . '</p>';
		}

		if (!$options_admin['hide_reply_dignity_forum'])
		{
			// количество ответов на форуме
		    $CI->db->from('dignity_forum_reply');
		    $CI->db->where('dignity_forum_reply_approved', 1);
		    $out .= '<p>' . t('Ответов на форуме: ', __FILE__) . $CI->db->count_all_results() . '</p>';
		}
	}

	// если включен плагин, выводим статистику
	if (in_array('dignity_video', $MSO->active_plugins))
	{
		if (!$options_admin['hide_video_dignity_video'])
		{
			// количество видео записей
		  	$CI->db->from('dignity_video');
		    $CI->db->where('dignity_video_approved', 1);
		    $out .= '<p>' . t('Опубликовано видео: ', __FILE__) . $CI->db->count_all_results() . '</p>';
	    }

	    if (!$options_admin['hide_comments_dignity_video'])
	    {
		    // количество комментариев к видео записям
		    $CI->db->from('dignity_video_comments');
		    $CI->db->where('dignity_video_comments_approved', 1);
		    $out .= '<p>' . t('Комментариев к видео: ', __FILE__) . $CI->db->count_all_results() . '</p>';
	    }
	}

	// если включен плагин, выводим статистику
	if (in_array('dignity_joke', $MSO->active_plugins))
	{
		if (!$options_admin['hide_joke_dignity_joke'])
		{
			// количество анекдотов
		  	$CI->db->from('dignity_joke');
		    $CI->db->where('dignity_joke_approved', 1);
		    $out .= '<p>' . t('Опубликовано анекдотов: ', __FILE__) . $CI->db->count_all_results() . '</p>';
		}

		if (!$options_admin['hide_comments_dignity_joke'])
		{
		    // количество комментариев к анекдотам
		    $CI->db->from('dignity_joke_comments');
		    $CI->db->where('dignity_joke_comments_approved', 1);
		    $out .= '<p>' . t('Комментариев к анекдотам: ', __FILE__) . $CI->db->count_all_results() . '</p>';
	    }
	}

	// если включен плагин, выводим статистику
	if (in_array('dignity_soft', $MSO->active_plugins))
	{
		if (!$options_admin['hide_soft_dignity_soft'])
		{
			// количество приложений
		  	$CI->db->from('dignity_soft');
		    $CI->db->where('dignity_soft_approved', 1);
		    $out .= '<p>' . t('Опубликовано приложений: ', __FILE__) . $CI->db->count_all_results() . '</p>';
	    }

	    if (!$options_admin['hide_comments_dignity_soft'])
	    {
		    // количество комментариев к приложениям
		    $CI->db->from('dignity_soft_comments');
		    $CI->db->where('dignity_soft_comments_approved', 1);
		    $out .= '<p>' . t('Комментариев к приложениям: ', __FILE__) . $CI->db->count_all_results() . '</p>';
	    }
	}

    /*
    // Новых
	$CI->db->from('comusers');
	$CI->db->where("UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(`comusers_last_visit`) < 900");
	$out .= '<p>' . t('Новых: ', __FILE__) . $CI->db->count_all_results() . '</p>';
	*/

    /*

	НЕ РАБОТАЕТ!

    // сколько комюзеров в онлайне
    // http://forum.max-3000.com/viewtopic.php?f=4&t=27
    // интервал 15 минут (900 секунд)
    function comusers_online_count($interval = 900)
    {
	   
	   $key = 'comusers_online_count_' . $interval;
	   $ret = mso_get_cache($key);
	   if( !$ret ){
	   	  $CI = &get_instance();
	      $CI->db->where("UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(`comusers_last_visit`) <  $interval");
	      $CI->db->from('comusers');
	      $ret = $CI->db->count_all_results();
	      
	      mso_add_cache($key,$ret,300); // в кэше на 5 минут
	   }
	   
	   return $ret;
	}

	$out .= '<p>' . t('Сейчас на сайте: ', __FILE__) . comusers_online_count() . '<p>';

	// кто именно в онлайне
	// http://forum.max-3000.com/viewtopic.php?f=4&t=27
	// 15 минут, отображать 10 пользователей
	function who_online($interval = 900,$limit = 10)
	{
   
	   $key = 'who_online';
	   $ret = mso_get_cache($key);

	   if( !$ret )
	   {
	      $CI = &get_instance();
	      $CI->db->select('*');
	      $CI->db->limit($limit);
	      $CI->db->where("UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(`comusers_last_visit`) <  $interval");
	      $query = $CI->db->get('comusers');
	      
	      if( $query->num_rows() )
	      {
	        $ret = $query->result_array();
	        mso_add_cache($key,$ret,300); // в кэше на 5 минут
	      }
	   }
	   
	   return $ret;
	}

	if (comusers_online_count() > 0)
	{
		$out .= '<p>' . t('Кто онлайн? ', __FILE__) . who_online() . '</p>';
	}
	*/

    // код с сайта usuarios-online
	if($options['usuarios_online_code'])
	{
		$out .= '<p>' . t('Сейчас на сайте: ', __FILE__) . $options['usuarios_online_code'] . '</p>';
	}

	// сайт в сети
	if ($options['online_from'])
	{
		$out .= "<p><script>
		start = new Date('" . $options['online_from'] . "');
		now = new Date();
		dt = (now.getTime() - start.getTime()) / (1000*60*60*24);
		document.write('" . t('Cайт в сети', __FILE__) . " ' + Math.round(dt) + '" . t('-й день', __FILE__) . "');
		</script></p>";
	}

	// текст после
	$out .= $options['textposle'];

	#mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;	
}

# end file