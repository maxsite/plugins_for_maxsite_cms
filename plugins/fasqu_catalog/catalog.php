<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

function fasqu_catalog_get($tag='', $do='' , $posle='')
{
	$cache_key = 'fasqu_catalog_get' . serialize($tag . $do . $posle);
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше

 $noindex1 = '<noindex>'; 
 $noindex2 = '</noindex>';
 $nofollow = 'rel="nofollow"';
 $blank = 'target="_blank"'; 
      if( $response = file_get_contents("http://api.fasqu.com/api/urls.json?word=".$tag) )
      { 
 
        //Перекодируем в массив
       $data = json_decode($response); 
       if( isset($data) and $data) 
       {
	       //создание списка
	      $out =  '';
  	    foreach( $data as $i => $url ) 
        {
          if ($url and isset($url->url) and isset($url->title) and isset($url->description))
          {
	    	    $cur_link = $do . $noindex1 . '<a href="'.$url->url.'"' . $nofollow . $blank . '>'.mso_strip($url->title).'</a><br>'. mso_strip($url->description) . $noindex2 . $posle;
	  	      $out .= $cur_link;
	  	    }  
	      }	
       }
     }
     else
     {
	     //Какая-то ошибка
	    $out = $http_response_header[0];	//нужно ли это выводить?
     }  
	mso_add_cache($cache_key, $out); // сразу в кэш добавим
  return $out;
}

// ______________________________________________________________________________
$cur_slug = mso_segment(2);
$cur_slug = mso_strip($cur_slug);
$cur_tag = '';
$out = '';
$menu_links = '';

 if ( !isset($options['catalog_title']) ) $options['catalog_title'] = 'Ссылки';
 if ( !isset($options['links']) ) $options['links'] = 'Max+Site+cms | maxsite | Max Site CMS';   

 if ( !isset($options['menu_do']) ) $options['menu_do'] = '<ul>';
 if ( !isset($options['menu_posle']) ) $options['menu_posle'] = '</ul>';
  if ( !isset($options['menu_item_do']) ) $options['menu_item_do'] = '<li>';
 if ( !isset($options['menu_item_posle']) ) $options['menu_item_posle'] = '</li>';
 
 if ( !isset($options['links_do']) ) $options['links_do'] = '<ul>';
 if ( !isset($options['links_posle']) ) $options['links_posle'] = '</ul>';
 if ( !isset($options['link_do']) ) $options['link_do'] = '<li>';
 if ( !isset($options['link_posle']) ) $options['link_posle'] = '</li>';
 
 if ( !isset($options['do']) ) $options['do'] = '';
 if ( !isset($options['posle']) ) $options['posle'] = '';
 
 if ( !isset($options['code']) ) $options['code'] = '';
 if ( !isset($options['email']) ) $options['email'] = '';
 
$title_page = '';
$title = $options['catalog_title'];

$catalog_url = getinfo('site_url') . $options['catalog_slug'] . '/';
$CI = & get_instance();


	$links = explode("\n", trim($options['links']));
	foreach ($links as $link)
	{
		$link = trim($link);
		if (!$link) continue;
		
		$link = explode('|', $link);

		if (count($link)<3) continue; // неверные данные
		
		$tag = trim($link[0]); //
		$slug = trim($link[1]); //
		$title = trim($link[2]); //
		
		if ($cur_slug == $slug)
		{
		  $menu_links .= $options['menu_item_do'] . $title . $options['menu_item_posle'];
		  $cur_tag = $tag;
		  $cur_title = $title;
      $title_page = $title;
      $out = fasqu_catalog_get($cur_tag , $options['link_do'] , $options['link_posle']);
   }  
   else $menu_links .= $options['menu_item_do'] . '<a href="' . $catalog_url . $slug . '">' . $title . '</a>' . $options['menu_item_posle'];
	}

mso_head_meta('title', $title_page . ' - ' . $options['catalog_title'], $options['catalog_title']); // meta title страницы
mso_head_meta('description', $title ); // meta title страницы

mso_hook_add('head', 'links_css');
function guestbook_css($a = array())
{
	if (file_exists(getinfo('template_dir') . 'links.css')) $css = getinfo('stylesheet_url') . 'links.css';
		else $css = getinfo('plugins_url') . 'fasqu_catalog/links.css';
	echo '<link rel="stylesheet" href="' . $css . '" type="text/css" media="screen">' . NR;
	return $a;
}


require(getinfo('template_dir') . 'main-start.php');
echo NR . '<div class="links">' . NR;

$session = getinfo('session'); // текущая сессия 
if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_url', 'f_captha'  , 'f_desc', 'f_queries')) )
      require(getinfo('plugins_dir').'fasqu_catalog/add_go.php'); // подключаем обработчик добавления


		


echo '<H1>' . $options['catalog_title']  . '</H1>';
echo $options['do'];
echo $options['menu_do'] . $menu_links . $options['menu_posle'];
echo '<H1>' . $title_page  . '</H1>';
echo $options['links_do'] . $out . $options['links_posle'];
echo $options['posle'];

if ($options['code'] and $cur_tag) require(getinfo('plugins_dir').'fasqu_catalog/add_form.php'); // подключаем обработчик добавления

# конечная часть шаблона
echo NR . '</div><!--div class="links"-->' . NR;

require(getinfo('template_dir') . 'main-end.php');

?>