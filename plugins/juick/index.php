<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * Author: Jimmy
 * Stravaganza.ru
 */

# функция автоподключения плагина
function juick_autoload()
{
	mso_register_widget('juick_widget', t('Мой Juick') ); # регистрируем виджет
	mso_hook_add('head', 'juick_head');
}

# функция выполняется при деинсталяции плагина
function juick_uninstall($args = array())
{	
	mso_delete_option_mask('juick_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

function juick_head($args = array()) 
{
	echo mso_load_style(getinfo('plugins_url') .'juick/juick.css');
}

# функция, которая берет настройки из опций виджетов
function juick_widget($num = 1) 
{
	$widget = 'juick_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции

	// заменим заголовок, чтобы был в h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	return juick_widget_custom($options, $num);
}

# форма настройки виджета 
# имя функции = виджет_form
function juick_widget_form($num = 1) 
{
	$widget = 'juick_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());

	if ( !isset($options['header']) ) $options['header'] = t('Мой Juick');
	if ( !isset($options['url']) ) $options['url'] = 'juick';
	if ( !isset($options['count']) ) $options['count'] = '7';
	if ( !isset($options['footer']) ) $options['footer'] = '';
	if ( !isset($options['max_word_description']) ) $options['max_word_description'] = '0';
	
    if ( !isset($options['show_nick']) )  $options['show_nick'] = TRUE;
	if ( !isset($options['show_img']) )  $options['show_img'] = TRUE;
	if ( !isset($options['show_tag']) )  $options['show_tag'] = TRUE;
	if ( !isset($options['profile_visible']) )  $options['profile_visible'] = FALSE;
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), 
				form_input( array( 
					'name'=>$widget . '_header', 
					'value'=>$options['header']), 
					t('Заголовок виджета')));
					
	$form .= mso_widget_create_form(t('Имя'), 
				form_input( array( 
					'name'=>$widget . '_url', 
					'value'=>$options['url'] ) ), 
					'Необходимо указать имя juick-аккаунта');
					
	$form .= mso_widget_create_form(t('Количество записей'), 
				form_input( array( 
					'name'=>$widget . '_count', 
					'value'=>$options['count'] ) ), 
					'');
					
	$form .= mso_widget_create_form(t('Количество слов'), 
				form_input( array( 
					'name'=>$widget . '_max_word_description', 
					'value'=>$options['max_word_description'] ) ), '');
					
	$form .= mso_widget_create_form(t('Текст в конце блока'), 
				form_input( array( 
					'name'=>$widget . '_footer', 
					'value'=>$options['footer'] ) ), '');
	
	$form .= mso_widget_create_form(' ', 
				form_checkbox(array(
					'name'=> $widget . '_show_img', 
					'checked' =>  $options['show_img'],
					'value' => 'show_img')) . ' ' . t('Показывать аватар'), 
					'Размер изображения можно задать в juick.css (каталог плагина)');
	
	$form .= mso_widget_create_form(' ', 
				form_checkbox( array(
					'name'=> $widget . '_show_nick', 
					'value'=> 'show_nick', 
					'checked' =>  $options['show_nick'])
					) . ' ' . t('Отображать ник'));
					
	$form .= mso_widget_create_form(' ', 
				form_checkbox( array(
					'name'=> $widget . '_show_tag', 
					'value'=> 'show_tag', 
					'checked' =>  $options['show_tag'])
					) . ' ' . t('Показывать теги'));
	
	$form .= mso_widget_create_form(' ', 
				form_checkbox( array(
					'name'=> $widget . '_profile_visible', 
					'value'=> 'profile_visible', 
					'checked' =>  $options['profile_visible'])
					) . ' ' . t('Выводить только в профиле комюзера'), 
					'В поле "О себе" необходимо указать juick-аккаунт. Например, так: jimmyjonezz@jabber.ru');
	
	//$form .= mso_widget_create_form(t('Примечание: '), 'При выводе даты учитывается часовое смещение времени вашего сервера указанного в Служебных настройках.', t(''));
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function juick_widget_update($num = 1) 
{
	$widget = 'juick_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . '_header');
	$newoptions['url'] = mso_widget_get_post($widget . '_url');
	$newoptions['count'] = mso_widget_get_post($widget . '_count');
	if ($newoptions['count'] < 1) $newoptions['count'] = 5;
	
	$newoptions['footer'] = mso_widget_get_post($widget . '_footer');
	
	$newoptions['max_word_description'] = (int) mso_widget_get_post($widget . '_max_word_description');
	if ($newoptions['max_word_description'] < 1) $newoptions['max_word_description'] = 0;
	
	$newoptions['show_nick'] =  mso_widget_get_post($widget . '_show_nick');
	$newoptions['show_img'] =  mso_widget_get_post($widget . '_show_img');
	$newoptions['show_tag'] =  mso_widget_get_post($widget . '_show_tag');
	$newoptions['profile_visible'] =  mso_widget_get_post($widget . '_profile_visible');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функции плагина
function juick_widget_custom($arg, $num)
{
	if ( !isset($arg['url']) ) $arg['url'] = false;
	if ( !isset($arg['count']) ) $arg['count'] = 7;
	if ( !isset($arg['max_word_description']) ) $arg['max_word_description'] = false;
	
	if ( !isset($arg['header']) ) $arg['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . 'Мой Juick' . mso_get_val('widget_header_end', '</span></h2>');
	
	if ( !isset($arg['block_start']) ) $arg['block_start'] = '<div class="juick">';
	if ( !isset($arg['block_end']) ) $arg['block_end'] = '</div>';
	
	if ( !isset($arg['footer']) ) $arg['footer'] = '';
	if ( !isset($arg['show_nick']) ) $arg['show_nick'] = true;
	if ( !isset($arg['show_img']) ) $arg['show_img'] = true;
	if ( !isset($arg['show_tag']) ) $arg['show_tag'] = true;
	if ( !isset($arg['profile_visible']) ) $arg['profile_visible'] = false;

	if ($arg['profile_visible']==true && is_type_slug('users', '')) {
		$comuser_info = mso_get_comuser(mso_segment(2)); 
		if ($comuser_info)
		{
			extract($comuser_info[0]);
			
			preg_match('/(\S+)@([a-z0-9.-]+)/', $comusers_description, $comusers_jabber);
			
			$juick = @juick_go($arg['url'], $arg['count'], $arg['max_word_description'], $arg['show_nick'], $arg['show_img'], $arg['show_tag'], $comusers_jabber[1]);
			
			if ($juick) 
			{	
				return $arg['header'] . $arg['block_start'] . $juick . $arg['footer'] . $arg['block_end'];
			}
		}
	}

	if ($arg['profile_visible']==false) {
		$juick = @juick_go($arg['url'], $arg['count'], $arg['max_word_description'], $arg['show_nick'], $arg['show_img'], $arg['show_tag'], '');
		if ($juick) 
		{	
			return $arg['header'] . $arg['block_start'] . $juick . $arg['footer'] . $arg['block_end'];
		}
	}
}

function juick_go ($url = true, $count = 7, $max_word_description = false, $show_nick = true, $show_img = true, $show_tag = true, $comusers_jaber = true)
{
	if (!$url) return false;

	//Нахуй кэш. Пока не использую! 
	//$cache_key = 'rss/' . 'juick_go' . $url . $count . (int) $max_word_description;
	//$k = mso_get_cache($cache_key);

	//if ($k) return $k; // да есть в кэше
	
	if (!defined('MAGPIE_CACHE_AGE')) define('MAGPIE_CACHE_AGE', 600);
	
	require_once(getinfo('common_dir') . 'magpierss/rss_fetch.inc');
	if ($comusers_jaber) {
		$rss_juick = @fetch_rss('http://rss.juick.com/'. $comusers_jaber .'/blog');
	} else {
		$rss_juick = @fetch_rss('http://rss.juick.com/'. $url .'/blog');
	}
	//pr($rss_juick);
	
	$rss = array_slice($rss_juick->items, 0, $count);
	$image = array_slice($rss_juick->image, 0);

	$comments = '';
	
	foreach ($rss as $index) {
		if ($index == $count) break;
		
		if ($max_word_description) {
			$text_confirm = mso_str_word($index['description'], $max_word_description) . ' ..';
		} 
		else {
			$text_confirm = $index['description'];
		}

		//пробую чистить текст - поставил заглушку (разбираюсь)
		$text = trim($text_confirm, " \f\v\t\n\r\0\x0B");
		//$text = preg_replace('/^<br\/>|<br\/>$/', '', $text_confirm);
		//preg_replace("/^<br \/>|<br \/>$/","",$str);
				
		//$date_time = timespan_ru($index['date_timestamp'] $index['pubdate'], $now);
		$date_time = get_count_time($index['pubdate']);
		
		$comments = preg_replace('/.{0,}[\/]/', '', $index['comments']);
		$str = mso_explode($index['title'], false, true);
		
		$out .= '<div class="juick_message">';
		if ($show_img) {
			$out .= '<img src="' . $image[url] . '" class="juick_img">';
		}
		
		if ($show_nick) {
			$out .= '<strong>' . $str[0] . '</strong> ';
		}
		
		$out .= $text ;
		
		$i = 1;
		if ($show_tag) {
			$out .= '<br>';
			while ($i < count($str)) {
				$category = substr($str[$i], 1);
				$url = substr($str[0], 1, -1);
				$out .= ' ' . '<a rel="nofollow" href="http://juick.com/' . $url . '/?tag='. $category .'" target="_blank">' . $str[$i] . ' </a>';
				$i++;
			}	
		}
			
		$out .= '<br><small id="juick_small">
				<a rel="nofollow" href="'. $index['comments'] .'" target="_blank">#' . $comments . '</a> &bull; ' . $date_time;
		/*
		$out .= ' &bull; <a rel="nofollow" href="'. $index['comments'] .'" target="_blank" title="Количество ответов">' . $index['slash']['comments'] . '';
		*/
		
		$out .= '</a></small><br></div>';
	}
	
	//mso_add_cache($cache_key, $out); // сразу в кэш добавим

	return $out;
}

/* Please help me! Guys, girls... Help me, please! */
function get_count_time($date_str) {
        $month_name = 
            array( 1  => 'января',
                   2  => 'февраля',
                   3  => 'марта',
                   4  => 'апреля',
                   5  => 'мая',
                   6  => 'июня',
                   7  => 'июля',
                   8  => 'августа',
                   9  => 'сентября',
                   10 => 'октября',
                   11 => 'ноября',
                   12 => 'декабря'
       );
	   
		$date_str = trim(str_ireplace('UT','', $date_str));
		$date_utm = strtotime($date_str);

		$time_zone_popr = date("O") / 100 * 60 * 60; //поправка тайм зоны
		$dif = time() - $date_utm;

        $month = $month_name[ date( 'n', $date_utm) ];

        $day   = date( 'j', $date_utm);
        $year  = date( 'Y', $date_utm);
        $hour  = date( 'G', $date_utm) + getinfo('time_zone');
        $min   = date( 'i', $date_utm);
		/**/
		if ($year > date( 'Y', time())) {
			$date = $day . ' ' . $month . ' ' . $year . ' г. в ' . $hour . ':' . $min;
		} else {
			$date = $day . ' ' . $month . ' в ' . $hour . ':' . $min;
		}
		
		//время не вывожу - не решен вопрос по таймзоне
		//дата сообщений разнится на 7 часов
		//$date = $day . ' ' . $month . ' ' . $year . ' г.';

        if($dif<59){
            return $dif." сек. назад";
        }elseif($dif/60>1 and $dif/60<59){
            return round($dif/60)." мин. назад";
        }elseif($dif/3600>1 and $dif/3600<23){
            return round($dif/3600)." час. назад";
        }else{
            return $date;
        }
}

# end file