<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// вспомогательные функции

 // определим общую ф-ю даты (чтобы везде одинаково)
function _dialog_date($format , $time)
{
  // $format = 'j F Y в H:i:s'
  return mso_page_date(date('Y-m-d H:i:s' , $time),	
									array(	'format' => $format, // 'j F Y в H:i:s'
											'days' => t('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
											'month' => t('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
									'', '' , false);	 

}

// функция вычисляет (склоняет) фразу - сколько времени назад
function _dialog_time_ago($timeold, $timenew , $format = array('год', 'года', 'лет' , 'месяц', 'месяца', 'месяцев' , 'день' , 'дня' , 'дней', 'час' , 'чаcа' , 'часов' , 'минута' , 'минуты' , 'минут' , 'секунда' , 'секунды' , 'секунд') )
{
   if ($timeold<$timenew)
   {
      $difftime = $timenew - $timeold;
      $mess = 'спустя';
   }
   else
   {
      $difftime = $timeold - $timenew;
      $mess = 'назад';
   }   

	$y = (int) ($difftime/31104000);
	$m = (int) ($difftime/2592000);
	$d = (int) ($difftime/86400);
	$h = (int) ($difftime/3600);
	$n = (int) ($difftime/60);
	$s = $difftime;
  $out = '';
  if ($y) {if ($y>4) $out .= $y . ' ' .$format[2] . ' '; elseif($y>1) $out .= $y . ' ' .$format[1] . ' '; else $out .= '1 ' .$format[0] . ' ';}
  if ($m) {if ($m>4) $out .= $m . ' ' .$format[5] . ' '; elseif($m>1) $out .= $m . ' ' .$format[4] . ' '; else $out .= '1 ' .$format[3] . ' ';}
  
  if (!$out)
  {
     if ($d) {if ($d>4) $out .= $d . ' ' .$format[8] . ' '; elseif($d>1) $out .= $d . ' ' .$format[7] . ' '; else $out .= '1 ' .$format[6] . ' ';}
     elseif ($h) {if ($h>4) $out .= $h . ' ' .$format[11] . ' '; elseif($h>1) $out .= $h . ' ' .$format[10] . ' '; else $out .= '1 ' .$format[9] . ' ';}
     
     if (!$out)
     {
       if ($n) {if ($n>4) $out .= $n . ' ' .$format[14] . ' '; elseif($n>1) $out .= $n . ' ' .$format[13] . ' '; else $out .= '1 ' .$format[12] . ' ';}
       elseif ($s) {if ($s>4) $out .= $s . ' ' .$format[17] . ' '; elseif($s>1) $out .= $s . ' ' .$format[16] . ' '; else $out .= '1 ' .$format[15] . ' ';}
     }  
  }
   
	 return $out . ' ' . $mess;
 
}


/*
 ф-я формирует заголовок цитаты, ответа, вопроса при перелинковке комментов по ответам и цитатам
 $comment1 - коммент где ссылка
 $comment2 - comment на который ссылка

 ##ARROW## - стрелка вверх, вниз или влево или вправо в зависимости местоположения коммента-назначения 
 ##DATE## - дата коммента-назначения
 ##TIMEAGO## - сколько прошло с момента комента назначения
 ##AUTOR## - имя автора
 ##COMMENT_URL## - адрес коммента
 пример: 
 
 $date_format 'j F Y'
*/ 
function dialog_perelink_title($comment_id1 , $comment_id2 , $date1, $date2, $autor_id , $autor_nik, $discussion_id, &$id_in_page , &$options , $arrows=array("↓","↑","←","→") , $mask='')
{
    $view_date = _dialog_date($options['perelink_date_format'] , $date2);
    $time_ago =  _dialog_time_ago($date1 , $date2);

    if (!$mask)
    {
      if ($options['perelink_title_mask']) $mask = $options['perelink_title_mask'];
      else $mask = '<p class="comment_perelink">##AUTOR## ответил(а) (<a href="##COMMENT_URL##" title ="Перейти к сообщению">##DATE## ##ARROW##</a>)</p>';
    }

// если коммент-потомок на этой странице, то ссылка на него - якорь
     if (in_array($comment_id2 , $id_in_page))
      {
         if ( $comment_id2 > $comment_id1) $arrow = 0; else $arrow = 1;
         $comment_url = '#key' . $comment_id2;
      }      
      else// иначе редирект на коммент, где вычислится его страница   
      {    
        if ( $comment_id2 < $comment_id1) $arrow = 2; else $arrow = 3;
        $comment_url = $options['siteurl'] . $options['goto_slug'] . '/disc/' . $discussion_id . '/comm/' . $comment_id2;
      }  
      
      $autor = dialog_profile_link($autor_id , $autor_nik , $options['profile_slug'], $options['siteurl'] , $options['profile']);
      
		  $out = str_replace( 
			    array('##AUTOR##',	'##COMMENT_URL##','##DATE##' , '##ARROW##' , '##TIMEAGO##'), 
			    array($autor, $comment_url	, $view_date , $arrows[$arrow] , $time_ago),
			    $mask);  
			    
			return $out;        

}
 

// для коммента выведем все комменты, где его цитировали 
function dialog_get_comment_perelinks($comment_id=0 , $comment_date , &$options , $comment_perelinks , $id_in_page = array(), $arrows=array("↓","↑","←","→"))
{

  if ($options['perelink_title_mask']) $mask = $options['perelink_title_mask'];
  else $mask = '<p class="comment_perelink">##AUTOR## ответил(а) (<a href="##COMMENT_URL##" title ="Перейти к сообщению">##DATE## ##ARROW##</a>)</p>';
    
  $out = ''; 
 if ($comment_perelinks) 
  foreach ($comment_perelinks as $cur)
     $out .=  dialog_perelink_title($comment_id , $cur['comment_id'] , $comment_date , $cur['comment_date_create'], $cur['comment_creator_id'] , $cur['profile_psevdonim'], $cur['comment_discussion_id'], $id_in_page , $options , $arrows , $mask);

  return $out;
} 
 

// в $content перед <blockquote id="comment_id"> подставим информацию о цитируемом пользователе и комменте
// $id_in_page - массив номеров комментов, которые на текущей странице: их ссылка - якорь
// $perelinks_info - массив инфы о перелинковках
function dialog_perelink_quotes($comment_id , $comment_date , &$content,  &$options , &$perelinks_info , $id_in_page=array()  )
{
  if ($perelinks_info)
    foreach ($perelinks_info as $perelink_id=>$perelink)
    {

         $dop_info = dialog_perelink_title($comment_id , $perelink_id , $comment_date , $perelink['comment_date_create'], $perelink['comment_creator_id'] , $perelink['profile_psevdonim'], $perelink['comment_discussion_id'] , $id_in_page , $options , $arrorws=array("↓","↑","←","→") , $options['quotes_title_mask']);         
			    
		   $content = str_replace("<blockquote id=\"" . $perelink_id . "\"" , $dop_info . "<blockquote id=\"" . $perelink_id . "\"",  $content);
			 
    }
  
}

// ссылка на профайл
function dialog_profile_link($user_id = 0 , $user_nik='' , $user_slug ='', $siteurl='' , $title = 'Profile')
{
  if (!$user_nik) $user_nik = $user_id;
  return '<a href = "' . $siteurl . $user_slug . '/' . $user_id . '" title = "' . $title . '">' . $user_nik . '</a>';
}
 

// возвращает ссылки на страницы категории вида 1 2 ... 10
function dialog_get_pages_links($siteurl , $discussion_slug = '' , $discussion_id = 0 , $pages_count = 1 , $max = 8)
{
  $out = '<a href ="' . $siteurl . $discussion_slug . '/' . $discussion_id . '">' . '1</a>'; 
  if ($pages_count > 1)
  {
    if ($pages_count<$max) $max = $pages_count;
  
    for ($i=2; $i <=$pages_count ; $i++)
    {
       if ($i>$max) break;
       $out .= ' <a href ="' . $siteurl . $discussion_slug . '/' . $discussion_id . '/next/' . $i . '">' . $i . '</a>'; 
    }
    
    if ( ($pages_count - $max) > 0)
    {
       if ( ($pages_count - $max) > 2)
       {
          $out .= ' ... ';
          $out .= ' <a href ="' . $siteurl . $discussion_slug . '/' . $discussion_id . '/next/' . ($pages_count-1) . '">' . ($pages_count-1) . '</a>';  
          $out .= ' <a href ="' . $siteurl . $discussion_slug . '/' . $discussion_id . '/next/' . $pages_count . '">' . $pages_count . '</a>';     
       }  
       elseif ( ($pages_count - $max) == 2)
       {
          $out .= ' <a href ="' . $siteurl . $discussion_slug . '/' . $discussion_id . '/next/' . ($pages_count-1) . '">' . ($pages_count-1) . '</a>';  
          $out .= ' <a href ="' . $siteurl . $discussion_slug . '/' . $discussion_id . '/next/' . $pages_count . '">' . $pages_count . '</a>';     
       }   
       else $out .= ' <a href ="' . $siteurl . $discussion_slug . '/' . $discussion_id . '/next/' . $pages_count . '">' . $pages_count . '</a>';  
   
    }
     
  }
  return $out;
}


// вычисляет кол-во страниц пагинации
function dialog_get_pages_count($comments_count=0 , $comments_on_page = 0)
{
   if (!$comments_on_page) return 1;
	 $pages_count = (int) ($comments_count / $comments_on_page);
   if  ($pages_count * $comments_on_page < ($comments_count)) $pages_count = $pages_count + 1;
   return $pages_count;
}


# вывод аватарки комментатора (перенесенная из mso_avatar)
function dialog_avatar($comment, $img_add = 'style="float: left; margin: 5px 10px 10px 0;" class="gravatar"', $echo = false)
{
	extract($comment);

	$avatar_url = '';
	if ($comusers_avatar_url) $avatar_url = $comusers_avatar_url;
	
	$avatar_size = (int) mso_get_option('gravatar_size', 'templates', 80);
	if ($avatar_size < 1 or $avatar_size > 512) $avatar_size = 80;
	
	if (!$avatar_url) 
	{ 
		// аватарки нет, попробуем получить из gravatara
		if ($comusers_email) $grav_email = $comusers_email;
		else $grav_email = '';
		
		if ($gravatar_type = mso_get_option('gravatar_type', 'templates', ''))
			$d = '&amp;d=' . urlencode($gravatar_type);
		else 
			$d = '';
		
		if (!empty($_SERVER['HTTPS'])) 
		{
		   $avatar_url = "https://secure.gravatar.com/avatar.php?gravatar_id="
				. md5($grav_email)
				. "&amp;size=" . $avatar_size
				. $d;
		} 
		else 
		{
		   $avatar_url = "http://www.gravatar.com/avatar.php?gravatar_id="
				. md5($grav_email)
				. "&amp;size=" . $avatar_size
				. $d;
		}
	}
	
	if ($avatar_url) 
		$avatar_url =  '<img src="' . $avatar_url . '" width="' . $avatar_size . '" height="'. $avatar_size . '" alt="" title="" '. $img_add . '>';
	
	if ($echo) echo $avatar_url;	
		else return $avatar_url;
}


function dialog_get_url($comment_discussion_id=0 , $comment_id=0)
{
  return '/disc/' . $comment_discussion_id . '/comm/' . $comment_id;
}
 
// делает необхоимые манипуляции с комментом перед выводом
// вызывается из разных мест
function dialog_comment_to_out(&$comments_content, &$options) 
{
		  // создадим если нужно глобальный флаг - в нем должно быть откуда вызывается функция
		  // это нужно для особых манипуляций с контентом по хукам
			if (isset($options['what_coment_out']))
			{
			  global $what_coment_out ; // флаг
			  $what_coment_out = false;
			  $what_coment_out = $options['what_coment_out']; // передаем глобальной переменной
			  /*
			     варианты:
           $what_coment_out = 'email';// информируем что контент для email (если что-то будет по хуку dialog_content_out)	
			     $what_coment_out = 'profile';// информируем что получаем коммент для плагина profile (если что-то будет по хуку dialog_content_out)
			  */
			
			  if ($options['what_coment_out'] == 'email') // если контент сообщения нужен для рассылки
			  {
			     $comments_content = strip_tags($comments_content, $options['email_tags']); // оставим только разрешенные для писем теги
			  }  		
			  /*elseif ($options['what_coment_out'] == 'profile') 
			  {
			  } */ 
	
			}
			
			global $comment_creator_id;  // пригодится
			if (isset($options['comment_creator_id'])) 
				 $comment_creator_id = $options['comment_creator_id']; else $comment_creator_id = 0; 



			$comments_content = mso_hook('comments_content', $comments_content);
            $comments_content = str_replace("\n", "<br>", $comments_content);		

			
			if (mso_hook_present('comments_content_custom'))
			{
				$comments_content = mso_hook('comments_content_custom', $comments_content);
			}
			elseif (function_exists('mso_auto_tag')) // для версий до 09x
		    { 
			  $comments_content = preg_replace_callback('!<pre>(.*?)</pre>!is', 'mso_clean_html_do', $comments_content);
			
			  $comments_content = str_replace('[html_base64]', '<pre>[html_base64]', $comments_content); // проставим pre
			  $comments_content = str_replace('[/html_base64]', '[/html_base64]</pre>', $comments_content);
			
			  // обратная замена
			  $comments_content = preg_replace_callback('!\[html_base64\](.*?)\[\/html_base64\]!is', 'mso_clean_html_posle', $comments_content);			
			
			  $comments_content = str_replace('<p>', '&lt;p&gt;', $comments_content);
			  $comments_content = str_replace('</p>', '&lt;/p&gt;', $comments_content);
			  $comments_content = str_replace('<P>', '&lt;P&gt;', $comments_content);
			  $comments_content = str_replace('</P>', '&lt;/P&gt;', $comments_content);
			  $comments_content = mso_auto_tag($comments_content, true);
			  $comments_content = mso_hook('content_balance_tags', $comments_content);
			}			
			else
			{
			   if (!function_exists('parser_default_content') )
					   require_once(getinfo('plugins_dir') . 'parser_default/index.php');
              
               $comments_content = parser_default_content($comments_content);
	        }
			
			$comments_content = mso_hook('comments_content_out', $comments_content);
			$comments_content = mso_hook('dialog_content_out', $comments_content);

}




 
?>