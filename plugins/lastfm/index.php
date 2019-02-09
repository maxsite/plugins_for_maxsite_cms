<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

# функция автоподключения плагина
function lastfm_autoload($args = array())
{
	mso_register_widget('lastfm_widget', 'Last.fm'); 
}

# функция выполняется при деинстяляции плагина
function lastfm_uninstall($args = array())
{	
	mso_delete_option_mask('lastfm_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

function lastfm_widget($num = 1)
{
	$widget = 'lastfm_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';

	return lastfm_widget_custom($options, $num);
}

# форма настройки виджета
function lastfm_widget_form($num = 1) 
{
	$widget = 'lastfm_widget_' . $num;
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = t('Недавно слушал на Last.fm', 'plugins');
	if ( !isset($options['url']) ) $options['url'] = 'логин';
	if ( !isset($options['count']) ) $options['count'] = 10;
		else $options['count'] = (int) $options['count'];
	if ( !isset($options['max_word']) ) $options['max_word'] = 0;
		else $options['max_word'] = (int) $options['max_word'];
	if ( !isset($options['show_time']) ) $options['show_time'] = 1;
	if ( !isset($options['cache_time']) ) $options['cache_time'] = 300;
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<div class="t150">' . t('Заголовок:', 'plugins') . '</div><p>'. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	$form .= '<div class="t150">' . t('Логин Last.fm:', 'plugins') . '</div><p>'. form_input( array( 'name'=>$widget . 'url', 'value'=>$options['url'] ) ) ;
	$form .= '<div class="t150">&nbsp;</div><p>http://www.lastfm.ru/user/<b>логин</b></p>';
	
	$form .= '<div class="t150">' . t('Макс. треков:', 'plugins') . '</div><p>'. form_input( array( 'name'=>$widget . 'count', 'value'=>$options['count'] ) ) ;
	$form .= '<div class="t150">&nbsp;</div><p>Максимальное количество последних прослушанных треков (максимум 10)</p>';
	
	$form .= '<div class="t150">' . t('Макс. символов:', 'plugins') . '</div><p>'. form_input( array( 'name'=>$widget . 'max_word', 'value'=>$options['max_word'] ) ) ;
	$form .= '<div class="t150">&nbsp;</div><p>Максимальное количество символов в названии трека (укажите 0 если хотите показывать название целиком)</p>';
	
	$form .= '<div class="t150">' . t('Показывать время:', 'plugins') . '</div><p>'. form_dropdown($widget . 'show_time', 				array( '1'=>t('Да (показывать)', 'plugins'), 
						'0'=>t('Нет (скрыть)', 'plugins')), 
						$options['show_time'] ) ;
	$form .= '<div class="t150">&nbsp;</div><p>Требуется ли показывать время когда был прослушан трек</p>';
	
	$form .= '<div class="t150">' . t('Время кеширования:', 'plugins') . '</div><p>'. form_dropdown($widget . 'cache_time', 				array( '5'=>t('Не кешировать', 'plugins'), 
						'60'=>t('1 минута', 'plugins'),																																   						'180'=>t('3 минуты', 'plugins'),
						'300'=>t('5 минут', 'plugins'),
						'600'=>t('10 минут', 'plugins'),
						'1800'=>t('30 минут', 'plugins')), 
						$options['cache_time'] ) ;
	
	return $form;
}

# получаем/обновляем опции
function lastfm_widget_update($num = 1) 
{
	$widget = 'lastfm_widget_' . $num;
	
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['url'] = mso_widget_get_post($widget . 'url');
	
	$newoptions['count'] = (int) mso_widget_get_post($widget . 'count');
	if ($newoptions['count'] > 10) (int) $newoptions['count'] = 10;
	if ($newoptions['count'] < 1) (int) $newoptions['count'] = 1;
	
	$newoptions['max_word'] = (int) mso_widget_get_post($widget . 'max_word');
	if ($newoptions['max_word'] < 0) (int) $newoptions['max_word'] = 0;
	
	$newoptions['show_time'] = mso_widget_get_post($widget . 'show_time');
	$newoptions['cache_time'] = mso_widget_get_post($widget . 'cache_time');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

function lastfm_widget_custom($arg, $num)
{
	# параметры ленты
	if ( !isset($arg['url']) ) $arg['url'] = false;
	if ( !isset($arg['count']) ) (int) $arg['count'] = 10;
	if ( !isset($arg['max_word']) ) (int) $arg['max_word'] = 0;
	if ( !isset($arg['show_time']) ) $arg['show_time'] = 1;
	if ( !isset($arg['cache_time']) ) $arg['cache_time'] = 300;

	# оформление виджета
	if ( !isset($arg['header']) ) $arg['header'] = '<h2 class="box"><span>Недавно слушал на Last.fm</span></h2>';
	if ( !isset($arg['block_start']) ) $arg['block_start'] = '<div class="lastfm"><ul class="tracks">';
	if ( !isset($arg['block_end']) ) $arg['block_end'] = '</ul></div>';

	$rss = @lastfm_go($arg['url'], $arg['count'], $arg['max_word'], $arg['show_time'], $arg['cache_time']);
	if ($rss) 
	{	
		return $arg['header'] . $arg['block_start'] . $rss . $arg['block_end'];
	}
}

function plural( $n ) {
	if ($n%10==1&&$n%100!=11) {return 0;}
	if ($n%10>=2&&$n%10<=4&&($n%100<10||$n%100>=20)) {return 1;}
	return 2;
}
function human_time_diff( $time ) {
	$diff = (int) abs(time() - $time);
	if ($diff <= 3600) {
		$n = round($diff / 60);
		$plural = plural($n);
		switch ($plural) {
			case 0:
            	$out = 'минуту';
            	break;
        	case 1:
            	$out = 'минуты';
            	break;
        	default: $out = 'минут';
    	}
	} else if (($diff <= 86400) && ($diff > 3600)) {
		$n = round($diff / 3600);
		$plural = plural($n);
		switch ($plural) {
			case 0:
            	$out = 'час';
            	break;
        	case 1:
            	$out = 'часа';
            	break;
        	default: $out = 'часов';
    	}
	} elseif ($diff >= 86400) {
		$n = round($diff / 86400);
		$plural = plural($n);
		switch ($plural) {
			case 0:
            	$out = 'день';
            	break;
        	case 1:
            	$out = 'дня';
            	break;
        	default: $out = 'дней';
    	}
	}
	$since = $n.' '.$out;
	return $since;
}

function lastfm_go($url = false, $count = 10, $max_word = 0, $show_time = 1, $cache_time = 300)
{	
	global $MSO;

	if (!$url) return false;
	
	# проверим кеш, может уже есть в нем все данные
	$cache_key = 'rss/' . 'lastfm_' . $url . (int) $count . (int) $max_word . $show_time . $cache_time;
	$k = mso_get_cache($cache_key, true);
	if ($k) return $k; // да есть в кэше
	
	if (!defined('MAGPIE_CACHE_AGE'))	define('MAGPIE_CACHE_AGE', $cache_time); // время кэширования MAGPIE
	require_once($MSO->config['common_dir'] . 'magpierss/rss_fetch.inc');

	$rss = fetch_rss("http://ws.audioscrobbler.com/1.0/user/".$url."/recenttracks.rss");
	$rss = array_slice($rss->items, 0, $count);
	
	$result = '';
	foreach ( $rss as $item ) 
	{ 	
		if ($max_word > 0)
		{
			$title = mb_substr($item['title'], 0, $max_word, 'UTF-8').'...';
		} else $title = $item['title'];
		$title = str_replace("'","",$title);
		$title = str_replace('"',"",$title);
		
		$full_title = $item['title'];
		$full_title = str_replace("'","",$full_title);
		$full_title = str_replace('"',"",$full_title);
		
		if ($show_time == 1) {
		$pubdate = strtotime($item['pubdate']);
		if ( (abs(time() - $pubdate)) < 604800 ) //7 дней
              $h_time = human_time_diff($pubdate).' назад';
            else
              $h_time = date(('H:i'), $pubdate);
		$time = "<span class='lastfm-time' title='".date(('d.m.Y H:i'), $pubdate)."'>".$h_time."</span>";
		} else $time='';
		
		$result .= "<li><a href='".$item['link']."' title='".$full_title."' target='_blank'>".$title."</a>".$time."</li>";	
	}
	mso_add_cache($cache_key, $result, $cache_time, true);
	return $result;
}
?>