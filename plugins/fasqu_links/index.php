<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function fasqu_links_autoload($args = array())
{
	mso_hook_add( 'content', 'fasqu_links_content');
}

# функции плагина


# callback функция 
function fasqu_links_content_callback($matches)
{	
$fasqu_tag = $matches[1];

 if( $response = file_get_contents("http://api.fasqu.com/api/urls.json?word=".$fasqu_tag) )
 { 
 
  //Перекодируем в массив
  $data = json_decode($response); 
  if( isset($data) ) 
   {
	   //создание списка
	   $out =  '<ul>';
  	 foreach( $data as $i => $url ) 
     {
	    	$cur_link = '<li><a href="'.$url->url.'">'.mso_strip($url->title).'</a> - '.        mso_strip($url->description).'</li>';
	  	  $out .= $cur_link;
	   }	
	   $out = $out .= '</ul>';
   }
 }
 else
 {
	//Какая-то ошибка
	$out = $http_response_header[0];	//нужно ли это выводить?
 }
 return $out;
}

# функции плагина
function fasqu_links_content($text = '')
{
  if (strpos($text, 'fasqu_tag') !== false) // есть вхождения
  {
	  $text = preg_replace_callback('~\[fasqu_tag=(.*?)\]~si', 'fasqu_links_content_callback', $text);
  } 
  return $text;
}

?>