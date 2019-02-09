<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

  $out .= '<div class="info info-top">' . NR;

  $out .= '<span title="' . t('Дата') . '"><img src="' . $template_url . 'images/date.png" width="16" height="16" alt="" style="vertical-align: text-top;"> ';
 
	$date_format = array(	'format' => 'j F Y', // 'd/m/Y H:i:s'
									'days' => t('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
									'month' => t('января февраля марта апреля мая июня июля августа сентября октября ноября декабря'));  
  
  if (isset($picture[$options['date_field']])) $date = $picture[$options['date_field']];
  else $date = $picture['picture_date'];
  
  $out .= taggallery_date($date , $date_format, '', '' , false);
  
  $out .= '</span>' . NR;

  $out .= '<span class = "right"><img src="' .$template_url . 'images/view.png" width="16" height="16" alt="" title="' . $options['text_picture_view'] . '" >' . $picture['picture_view_count'] . '</span>' . NR;

// альтернативные метки картинки__________________________________________________________________________
// выводятся сылки на галереи в которых еще присутствует эта картинка,кроме текущей
  
  $posts_tags = '';
  if ($picture['gallerys']) foreach ($picture['gallerys'] as $gallery)
  {
    $gallery_link = '<a href ="' . $siteurl . $options['gallery_slug'] . '/' . $options['gallery_prefix'] . $gallery['gallery_slug'] . '">' . $gallery['gallery_title'] . '</a>';
    if ($posts_tags) $posts_tags .= $options['see_tags_razd'] . $gallery_link;
    else $posts_tags .= $gallery_link;
  }
  if ($posts_tags)
  {
     $out .=   '<br><span title="' . $options['title_gallerys_on_tag'] . '"><img src="' . $template_url . 'images/gall.png" width="16" height="16" alt="" style="vertical-align: text-top;">';
     $out .=  $posts_tags;
     $out .=  '</span>' . NR;
  }   
  

  // выводим ссылки на метки записей как у картинки __________________________________________________________
 	require_once( getinfo('common_dir') . 'meta.php' ); // функции мета
  $all_tags_page = mso_get_all_tags_page();
  
  $posts_tags = '';
  if ($picture['gallerys'])
    foreach ($picture['gallerys'] as $gallery)
    {
      if (!isset($all_tags_page[$gallery['gallery_name']])) continue;
      $gallery_link = '<a href ="' . $siteurl . 'tag' . '/' . $gallery['gallery_name'] . '">' . $gallery['gallery_name'] . '</a>';
      if ($posts_tags) $posts_tags .= $options['see_tags_razd'] . $gallery_link;
      else $posts_tags .= $gallery_link;
    }
  
  if ($posts_tags)  
  {
     $out .=   '<span style="margin-left: 15px;" title="' . $options['title_posts_on_tag'] . '"><img src="' . $template_url . 'images/tag.png" width="16" height="16" alt="" style="vertical-align: text-top;">';
     $out .=  $posts_tags;
     $out .=  '</span>' . NR;  
  }
  
  $out .= '</div>' . NR;

?>