<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function last_comments_ex_autoload($args = array())
{
	global $MSO;
	
	mso_register_widget('last_comments_ex_widget', t('Последние комментарии (расширенные)', 'plugins')); # регистрируем виджет
	mso_hook_add('new_comment', 'last_comments_ex_new_comment'); # хук на новый коммент - нужно сбросить кэш комментариев
	
	// для того, чтобы обновлять только ключи этого виджета, а не всего кэша
	// в $MSO сохраним все созданные ключи кэша
	// при хуке new_comment просто их сбросим
	
	$MSO->data['cache_key']['last_comments_ex'] = array();
}

# функция выполняется при деинсталяции плагина
function last_comments_ex_uninstall($args = array())
{	
	mso_delete_option_mask('last_comments_ex_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# хук на сброс кэша при новом комментарии
function last_comments_ex_new_comment($args = array())
{
	// очистим кэш по нашей маске, то есть файлы начинающиеся с указанной строки
	mso_flush_cache_mask('last_comments_ex_widget_');
	
	return $args;
}


# функция, которая берет настройки из опций виджетов
function last_comments_ex_widget($num = 1) 
{
	$widget = 'last_comments_ex_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
				$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
		else $options['header'] = '';
	
	return last_comments_ex_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function last_comments_ex_widget_form($num = 1) 
{
	$widget = 'last_comments_ex_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	
	if ( !isset($options['simple']) ) $options['simple'] = 0;
	if ( empty($options['simple']) ) $options['simple'] = 0;
	
	if ( !isset($options['count']) ) $options['count'] = 5;
	if ( !isset($options['words']) ) $options['words'] = 20;
	if ( !isset($options['maxchars']) ) $options['maxchars'] = 20;
	
	if ( !isset($options['format']) ) $options['format'] = '<strong>[AUTHOR] » </strong>[URL][COMMENT][/URL]';	
	if ( !isset($options['format_date']) ) $options['format_date'] = 'd.m.Y H:i';	
	
	if ( !isset($options['humandate']) ) $options['humandate'] = 0;
	if ( empty($options['humandate']) ) $options['humandate'] = 0;
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) );
	
	$form .= '<br><br><p><div class="t150">' . t('Без разбивки по постам:', 'plugins') . '</div> '. form_checkbox( array( 'name'=>$widget . 'simple', 'checked'=>$options['simple'], 'value'=>'1'  ) ) . '<br><br>';
	
	$form .= '<p><div class="t150">' . t('Количество:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'count', 'value'=>$options['count'] ) );
	
	$form .= '<p><div class="t150">' . t('Количество слов:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'words', 'value'=>$options['words'] ) );
	
	$form .= '<p><div class="t150">' . t('Количество символов в одном слове:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'maxchars', 'value'=>$options['maxchars'] ) );
	
	$form .= '<br><br><p><div class="t150">' . t('Формат:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'format', 'value'=>$options['format'] ) ) ;
	$form .= '<br><div class="t150">&nbsp</div> [DATE] [AUTHOR] [COMMENT] [URL][/URL]';

	
	
	$form .= '<br><p><div class="t150">' . t('Формат даты:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'format_date', 'value'=>$options['format_date'] ) ) ;
	//$form .= '<br><div class="t150">&nbsp</div>[HDATE] - отобразит вчера, сегодня; [HTIME] - отобразит "X часов назад", "Y минут назад"' . NR;
	
	$help = getinfo('plugins_url') . 'last_comments_ex/dateformat.html';
	$form .= '<br><div class="t150">&nbsp</div>Подробнее о <a href="'.$help.'" target="blank">формате даты</a>' . NR;

	$form .= '<br><p><div class="t150">' . t('Вчера, сегодня:', 'plugins') . '</div> '. form_checkbox( array( 'name'=>$widget . 'humandate', 'checked'=>$options['humandate'], 'value'=>'1'  ) ) . 
	'&nbsp;дата в формате "вчера", "сегодня"<br><br>';

	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function last_comments_ex_widget_update($num = 1) 
{
	$widget = 'last_comments_ex_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['count'] = (int) mso_widget_get_post($widget . 'count');
	$newoptions['words'] = (int) mso_widget_get_post($widget . 'words');
	$newoptions['maxchars'] = (int) mso_widget_get_post($widget . 'maxchars');
	$newoptions['simple'] = mso_widget_get_post($widget . 'simple');
	$newoptions['format'] = mso_widget_get_post($widget . 'format');
	$newoptions['format_date'] = mso_widget_get_post($widget . 'format_date');
	$newoptions['humandate'] = mso_widget_get_post($widget . 'humandate');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function last_comments_ex_widget_custom($options = array(), $num = 1)
{	

	if (!isset($options['count'])) $options['count'] = 5;
	if (!isset($options['words'])) $options['words'] = 20;
	if (!isset($options['maxchars'])) $options['maxchars'] = 20;
	if (!isset($options['header'])) $options['header'] = '';
	if (!isset($options['humandate'])) $options['humandate'] = 0;
	if ( empty($options['humandate']) ) $options['humandate'] = 0;

	if (!isset($options['simple'])) $options['simple'] = 0;
	if ( empty($options['simple']) ) $options['simple'] = 0;
	
	if ( !isset($options['format']) ) 	$options['format'] = '<strong>[AUTHOR] » </strong>[URL][COMMENT][/URL]';
	if ( !isset($options['format_date']) ) $options['format_date'] = 'd.m.Y, H:i';	
	

	
	$options['count'] = (int) $options['count'];
	if ($options['count'] < 1) $options['count'] = 5;
	
	$options['words'] = (int) $options['words'];
	if ($options['words'] < 1) $options['words'] = 20;
	
	$options['maxchars'] = (int) $options['maxchars'];
	if ($options['maxchars'] < 1) $options['maxchars'] = 20;
	
	$cache_key = 'last_comments_ex_widget_' . $num . mso_md5(serialize($options));
	
	// в кэш добавляем только результаты выборки из БД, иначе динамическая дата не будет работать
	$k = mso_get_cache($cache_key, true);
	//pr( $k );
	//if ($k) return $k; // да есть в кэше


	require_once( getinfo('common_dir') . 'comments.php' ); // функции комментариев
	
	if ( !$k ) 
		$comments = mso_get_comments(false, array('limit' => $options['count'], 'order'=>'desc'));
	else
		$comments = $k;
	$out = '';
	
	if ($comments) // есть страницы
	{ 	
	
		if (  ! $options['simple'] ) 
		{
			// сгруппируем все комментарии по записям
			$arr_com_page = array();
			$arr_com_page_title = array();
			foreach ($comments as $comment)
			{
				$arr_com_page[ $comment['page_id'] ] [$comment['comments_id']] = $comment;
				$arr_com_page_title[ $comment['page_id'] ]  = $comment['page_title'];
			}
			
			// выводим по странично
			foreach ($arr_com_page as $key=>$comments)  // выводим в цикле
			{
				$out .= '<h2 class="box last_comment">' . $arr_com_page_title[$key] . '</h2>' . NR;
			
				$comments = array_reverse($comments); // чтобы комментарии были в привычном порядке сверху вниз
			
				$out .= '<ul class="is_link last_comment">' . NR;
			
				foreach ($comments as $comment)  // выводим в цикле
				{
					$out .= comment_item( $comment, $options );
				}
				$out .= '</ul>' . NR;
			}
		
			
		} else {
			// вывод простым списком
			$out .= '<ul class="is_link last_comment">' . NR;
			foreach ($comments as $comment)
			{
				$out .= comment_item( $comment, $options );
			}	
			$out .= '</ul>' . NR;			
		}
		if ($options['header']) $out = $options['header'] . $out;
	}
	//mso_add_cache($cache_key, $out, false, true); // сразу в кэш добавим
	// в кэш добавляем только результаты выборки из БД, иначе динамическая дата не будет работать
	mso_add_cache($cache_key, $comments, false, true); // сразу в кэш добавим
	
	return trim($out);
}

function comment_item( $comment, $options ) {
	$item = '';
	extract($comment);
	
	if ($comment['comments_users_id']) 
		$css_style_add = 'last_comment_users ' . ' last_comment_users_' . $comment['comments_users_id'];
	elseif ($comment['comments_comusers_id']) 
		$css_style_add = 'last_comment_comusers ' . ' last_comment_comusers_' . $comment['comments_comusers_id'];
	else 
		$css_style_add = 'last_comment_anonim';
				
	$item .= '<li class="' . $css_style_add . '">' . $options['format'] . '</li>';	
	
	if ($comments_users_id) // это автор
	{
		$author = $users_nik;
	}
	elseif ($comments_comusers_id) // это комюзер
	{
		if ($comusers_nik) $author = $comusers_nik;
		else $author = t('Комментатор', 'plugins') . ' ' . $comusers_id;
	}
	elseif ($comments_author_name) $author = $comments_author_name; // аноним . ' (анонимно)'
	else $author = ' ' . t('Аноним');
				
				
	$comments_content_1 = strip_tags($comments_content); // удалим тэги
	$comments_content = mso_str_word($comments_content_1, $options['words']); // ограничение на количество слов
				
	// если старый и новый текст после обрезки разные, значит добавим в конце ...
	if ($comments_content_1 != $comments_content) $comments_content .= '...';
				
	// каждое слово нужно проверить на длину и если оно больше maxchars, то добавить пробел в wordwrap
	$words = explode(' ', $comments_content);
	foreach($words as $key=>$word)
		$words[$key] = mso_wordwrap($word, $options['maxchars'], ' ');
	$comments_content = implode(' ', $words);
	$comments_content = strip_tags($comments_content);
	
	$href = getinfo('siteurl') . 'page/' . mso_slug($page_slug) . '#comment-' . $comments_id;
	$name = 'comment-' . $comments_id;
	$url_do = '<a href="'.$href.'" name="'.$name.'">';
	$url_posle = '</a>';
	
	$item = str_replace('[AUTHOR]',	$author, $item);
	$item = str_replace('[COMMENT]', $comments_content, $item);

	if ( strpos( $item, '[DATE]'  ) !== false )
	{	
		
		if ( $options['humandate'] )
		{
			$dt = preg_split("/[^\d]/", $comments_date);
			$now = time();
			$time = mktime($dt[3], $dt[4], $dt[5], $dt[1], $dt[2], $dt[0]);
			//$time = $time + getinfo('time_zone') * 60 * 60;
			$delta = $now - $time;
			$stime = date('Y m d H i s Z', $delta);	
			$stime = explode(' ', $stime);
			$delta = $stime[6] / 60 / 60;
			$h_ago = $stime[3] - $delta;
			$m_ago = $stime[4];
			$ago = '';

			
			if ( (int)$h_ago < 24 and (int)$h_ago > 0) $ago .= (int)$h_ago . ' ч';
			if ( !empty( $ago ) ) $ago .= ' ';
			
			if ( (int)$m_ago < 60 and (int)$m_ago > 0 ) $ago .= (int)$m_ago . ' мин';
			if ( !empty( $ago ) ) $ago .= ' назад';
			$today = date('Ymd');
			if( ($dt[0].$dt[1].$dt[2]) == $today) {
				$comments_date = ( !empty( $ago ) ) ? 'Сегодня, ' . $ago : 'Сегодня, ' . $dt[3] . ':' . $dt[4];;
				
			} else {
				$b = explode("-",date("Y-m-d"));
				$time2 = mktime(0,0,0,$b[1],$b[2]-1,$b[0]);
				$yesterday = date("Ymd", $time2);
				if ( ($dt[0].$dt[1].$dt[2]) == $yesterday) {
					$comments_date = 'Вчера, '.$dt[3].":".$dt[4];
				} else {
					$days = array('пн', 'вт', 'ср', 'чт', 'пт', 'сб', 'вс');
					$months = array('янв', 'фев', 'мар', 'апр', 'май', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек');
					$comments_date = mso_date_convert( $options['format_date'], $comments_date, true, $days, $months);
				}					
			}
		} else {
			$days = array('пн', 'вт', 'ср', 'чт', 'пт', 'сб', 'вс');
			$months = array('янв', 'фев', 'мар', 'апр', 'май', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек');
			$comments_date = mso_date_convert( $options['format_date'], $comments_date, true, $days, $months);
		}	
		$item = str_replace('[DATE]', $comments_date, $item);
	}
	
	$item = str_replace('[URL]', $url_do, $item);
	$item = str_replace('[/URL]', $url_posle, $item);
	
	return $item;
}

?>