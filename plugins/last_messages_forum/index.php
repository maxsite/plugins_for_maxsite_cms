<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */



# функция автоподключения плагина
function last_messages_forum_autoload()
{
	mso_register_widget('last_messages_forum_widget', t('Последние сообщения с форума', __FILE__)); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function last_messages_forum_uninstall($args = array())
{	
	mso_delete_option_mask('last_messages_forum_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function last_messages_forum_widget($num = 1) 
{
	$widget = 'last_messages_forum_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';
	
	return last_messages_forum_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function last_messages_forum_widget_form($num = 1) 
{
	$widget = 'last_messages_forum_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['message_count']) ) 	$options['message_count'] = 10; // кол-во последних сообщений
	if ( !isset($options['forum_url']) ) 	$options['forum_url'] = '';
	
		/*
	if ( !isset($options['same_base']) ) 	$options['same_base'] = 'true'; // checkbox - таже база, что и Maxsite
	if ( !isset($options['base_name']) ) 	$options['base_name'] = ''; // имя базы, если другая база
	if ( !isset($options['base_login']) ) 	$options['base_login'] = '';
	if ( !isset($options['base_password']) ) 	$options['base_password'] = '';
	if ( !isset($options['base_server']) ) 	$options['base_server'] = '';
	*/
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. 
			form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;

	// адрес форума
	$form .= '<p><div class="t150">' . t('Адрес форума:', 'plugins') . '</div> '. 
			form_input( array( 'name'=>$widget . 'forum_url', 'value'=>$options['forum_url'] ) );
	
	$form .= '<br><div class="t150">&nbsp;</div>Указывайте с http://, например, http://forum.max-3000.com';
	// количество сообщений
	$form .= '<p><div class="t150">' . t('Кол-во сообщений:', 'plugins') . '</div> '. 
			form_input( array( 'name'=>$widget . 'message_count', 'value'=>$options['message_count'] ) ) ;
	/*
	$form .= '<p><div class="t150">&nbsp;</div><strong>Данные для подключения к БД форума:</strong>';
	// checkbox
	$form .= '<p><div class="t150">' . t('База Maxsite:', 'plugins') . '</div> '. form_checkbox( array( 'name'=>$widget . 'same_base', 'value'=>$options['same_base'], 'checked'=>TRUE ) ) ;

	// имя сервера
	$form .= '<p><div class="t150">' . t('Сервер:', 'plugins') . '</div> '. 
			form_input( array( 'name'=>$widget . 'base_server', 'value'=>$options['base_server'] ) ) ;

	// имя базы
	$form .= '<p><div class="t150">' . t('База:', 'plugins') . '</div> '. 
			form_input( array( 'name'=>$widget . 'base_name', 'value'=>$options['base_name'] ) ) ;
	
	// логин
	$form .= '<p><div class="t150">' . t('Логин:', 'plugins') . '</div> '. 
			form_input( array( 'name'=>$widget . 'base_login', 'value'=>$options['base_login'] ) ) ;
	
	// пароль
	$form .= '<p><div class="t150">' . t('Пароль:', 'plugins') . '</div> '. 
			form_input( array( 'name'=>$widget . 'base_password', 'value'=>$options['base_password'] ) ) ;
	*/
	
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function last_messages_forum_widget_update($num = 1) 
{
	$widget = 'last_messages_forum_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['message_count'] = mso_widget_get_post($widget . 'message_count');
	$newoptions['forum_url'] = mso_widget_get_post($widget . 'forum_url');
/*	
	$newoptions['same_base'] = mso_widget_get_post($widget . 'same_base');
	$newoptions['base_server'] = mso_widget_get_post($widget . 'base_server');
	$newoptions['base_name'] = mso_widget_get_post($widget . 'base_name');
	$newoptions['base_login'] = mso_widget_get_post($widget . 'base_login');
	$newoptions['base_password'] = mso_widget_get_post($widget . 'base_password');
*/	
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function last_messages_forum_widget_custom($options = array(), $num = 1)
{
	// кэш 
	$cache_key = 'last_messages_forum_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	/*
	if ( !isset($options['same_base']) ) $options['same_base'] = 'true';
	if ( !isset($options['base_server']) ) $options['base_server'] = '';
	if ( !isset($options['base_name']) ) $options['base_name'] = '';
	if ( !isset($options['base_login']) ) $options['base_login'] = '';
	if ( !isset($options['base_password']) ) $options['base_password'] = '';
	*/
	if ( !isset($options['message_count']) ) $options['message_count'] = 10;
	if ( !isset($options['forum_url']) ) $options['forum_url'] = '';

	$CI = & get_instance();
	$new_db = $CI->db;
	$old_dbprefix = $CI->db->dbprefix;
	/*
	$old_hostname = $CI->db->hostname;
	$old_username = $CI->db->username;
	$old_password = $CI->db->password;
	$old_database = $CI->db->database;
	*/
	/*
	if (  $options['same_base'] != 'true' ) {
		$new_db->dbprefix = '';
		$new_db->hostname = $options['base_server'];
		$new_db->username = $options['base_login'];
		$new_db->password = $options['base_password'];
		$new_db->database = $options['base_name'];
	}
	*/
	/* ***** */ $new_db->dbprefix = '';
	
		$new_db->select('m.poster_time as time, m.subject as subject, m.poster_name as author, b.name as board, m.id_topic, m.id_board as id_board, m.id_msg');
		$new_db->from('smf_messages m');
		$new_db->join('smf_boards b', 'b.id_board = m.id_board');
		$new_db->order_by('m.poster_time', 'DESC');
		$new_db->limit( $options['message_count'] );
		$query = $new_db->get();
		
		if ($query->num_rows() > 0)	
		{	
			$messages = $query->result_array();
			$out = '<ul class="is_link last_comment">' . NR;
			foreach ($messages as $key=>$message)
			{
			
				# http://forum.mototrubka.ru/index.php/topic,285.msg5965/topicseen.html#msg5965
				$url_topic = $options['forum_url'] . '/index.php/topic,' . $message['id_topic'] . '.msg' . $message['id_msg'] . '/topicseen.html#msg' . $message['id_msg'];
				
				# http://forum.mototrubka.ru/index.php/board,28.0.html
				$url_board = $options['forum_url'] . '/index.php/board,' . $message['id_board'] . '.0.html';

				$data = date('d.m.Y H:i:s', $message['time']);
				$out .= '<li class="last_comment_anonim">' . NR;
				$out .= '<strong><i>' . $data . '</i></strong><br>' . '<strong>' . $message['author'] . '</strong>' . '&nbsp;»' . NR;
				$out .= '<a href="' . $url_topic . '">' . '<i>' . $message['subject'] . '</i>' . '</a>' . NR;
				$out .= '&nbsp;&rarr;&nbsp;' . NR;
				$out .= '<a href="' . $url_board . '">' . $message['board'] . '</a>' . NR;
				$out .= '</li>' . NR;
			}
			$out .= '</ul>' . NR;
		}
	
	
		$CI->db->dbprefix = $old_dbprefix;
	/*$CI->db->hostname = $old_hostname;
	$CI->db->username = $old_username;
	$CI->db->password = $old_password;
	$CI->db->database = $old_database;
	*/
	if ($options['header']) $out = $options['header'] . $out;
	
	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;	
}

?>