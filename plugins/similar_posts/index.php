<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
*/

// вспомогательная функция сортировки
function cmp ($a, $b)
{
    if ($a['similarity'] == $b['similarity']) return 0;
    return ($a['similarity'] > $b['similarity']) ? -1 : 1;
}

// функция находит в контенте первую попавшуюся картинку
function similar_posts_get_pictures ($content = '') 
{
  $picture = '';
	if ($content)
	{	    
      $pic = stristr($content, "img");
	    if (!$pic) $pic = stristr($content, "IMG");
	    
	    if ($pic)
      {
	      $pic2 = stristr($pic, "src");
	      if ($pic2) 
	      {	      
	        $pic3 = stristr($pic2, "http");
	        if ($pic3) 
	        {
	          $num = explode('"', $pic3);
            if ($num[0])
            {
              $picture = $num[0];
              $flag = true;
            }
          }
       }
     }   
   }
 
 return $picture;
}

// функция делает из контента краткое описание
function similar_posts_get_desc ($content = '' , $count =0, $tags ='', $end='') 
{

  $count = (int)$count;

	$content = str_replace('[xcut', '[cut', $content);
					
	if ( preg_match('/\[cut(.*?)?\]/', $content, $matches) )
	{
			$content = explode($matches[0], $content, 2);
			$cut = $matches[1];
	}
	else
	{
			$content = array($content);
			$cut = '';
	}
	$content = $content[0];


  $content = strip_tags($content, $tags);   
  
  if ($count and(strlen($content) > $count))
  {
     $text_arr = explode(" ",$content); 
     $content=""; 
     foreach ($text_arr as $word)
     { 
        $word = trim($word);
        if (!$word) continue;
        if ( strlen($content . $word . ' ') <= $count )
        { 
           $content .= $word . ' ';
        } 
        else break;
     } 
     $content .= $end;
  }
  return $content;
}


# функция автоподключения плагина
function similar_posts_autoload($args = array())
{
	if ( is_type('page') )
	{
		$options = mso_get_option('plugin_similar_posts', 'plugins', array());
		if (!isset($options['priory'])) $options['priory'] = 10;
		mso_hook_add('content_end', 'similar_posts_content_end', $options['priory']);
	}
}

# функция выполняется при деинсталяции плагина
function similar_posts_uninstall($args = array())
{	
	mso_delete_option('plugin_similar_posts', 'plugins'); // удалим созданные опции
	return $args;
}

function similar_posts_mso_options() 
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_similar_posts', 'plugins', 
		array(
			'count' => array(
							'type' => 'text', 
							'name' => 'Колличество похожих страниц.', 
							'description' => 'Максимальное кол-во похожих страниц для вывода.', 
							'default' => '5'
						),
			'title' => array(
							'type' => 'text', 
							'name' => 'Заголовок блока', 
							'description' => 'Введите заголовок блока последних страниц в (!)соответствующих тегах.', 
							'default' => '<div class="page_other_pages_header">Похожие страницы</div>'
						),								
			'priory' => array(
							'type' => 'text', 
							'name' => 'Приоритет блока', 
							'description' => 'Позволяет расположить блок до (или после) аналогичных. Используйте значения от 1 до 90. Чем больше значение, тем выше блок. По умолчанию значение равно 10.', 
							'default' => '10'
						),
			'type' => array(
							'type' => 'text', 
							'name' => 'Типы страниц на которых выводить блок похожих страниц.', 
							'description' => 'Укажите, разделяя запятыми или пробелами, типы страниц, на которых выводить блок. Если выводить на всех - введите all.<br>Например <strong>blog , static</strong>', 
							'default' => 'all'
						),
			'tag_prior' => array(
							'type' => 'text', 
							'name' => 'Коэффициент веса меток.', 
							'description' => 'При суммировании кол-ва общих меток с категориями, кол-во меток будет умножено на этот коэффициент.', 
							'default' => '1'	
												
         ),
			'start' => array(
							'type' => 'text', 
							'name' => 'Что перед блоком последних страниц.', 
							'description' => 'Начальный тег.', 
							'default' => '<div class="page_other_pages">'	
         ), 
			'end' => array(
							'type' => 'text', 
							'name' => 'Что после блока.', 
							'description' => 'Завершающий тег', 
							'default' => '</div>'	
         ),                  
			'do' => array(
							'type' => 'text', 
							'name' => 'Что перед циклом вывода последних страниц.', 
							'description' => 'Что перед выводом последних страниц.', 
							'default' => '<table width="100%" border="0"><tr>'	
												
         ),         
			'posle' => array(
							'type' => 'text', 
							'name' => 'Что после цикла вывода.', 
							'description' => 'Что после вывода.', 
							'default' => '</tr></table></div>'	
												
         ),
			'format' => array(
							'type' => 'text', 
							'name' => 'Формат тела цикла вывода.', 
							'description' => '[link], [image], [title], [desc] , [url]', 
							'default' => '<td>[link]<br>[image]<br>[desc]</td>'	
         ),
			'width' => array(
							'type' => 'text', 
							'name' => 'Ширина картинки', 
							'description' => 'Ширина картинки.', 
							'default' => '120'
						),   
 
			'field_of_numbers' => array(
							'type' => 'text', 
							'name' => 'Метаполе номеров похожих страниц.', 
							'description' => 'Если указать и оно окажется не пустое - выведутся страницы по номерам.<br>Номера указываются через запятые или пробелы.', 
							'default' => 'false'
						), 	
			'field_of_desc' => array(
							'type' => 'text', 
							'name' => 'Метаполе описания.', 
							'description' => 'Если указать и оно будет не пустое, то описание будет взято оттуда', 
							'default' => ''
						), 		
			'field_of_prev' => array(
							'type' => 'text', 
							'name' => 'Метаполе превьюшки.', 
							'description' => 'Сперва превьюшка будет искаться там, затем в контенте.<br>Превьюшка ищется во всем контенте, игнорируя [cut]', 
							'default' => 'prev'
						), 	
			'tags' => array(
							'type' => 'text', 
							'name' => 'Теги, которые не убираются из контента.', 
							'description' => 'В том случае, если нужно оставить форматирование.', 
							'default' => ''
						), 			
			'symbol_count' => array(
							'type' => 'text', 
							'name' => 'Кол-во символов, которые оставляются в контенте.', 
							'description' => 'Если пустое значение, то обрезки не будет.<br>В любом случае, берется до [cut]', 
							'default' => '140'
						), 	
			'end_of_desc' => array(
							'type' => 'text', 
							'name' => 'Что в конце описания, если контент обрезается.', 
							'description' => 'max длина результата считается без учета этих символов.', 
							'default' => '...'
						), 
			'dir' => array(
							'type' => 'text', 
							'name' => 'Директорий относительно uploads, из которого превьюшка, в случае отсутствия, будет выбрана рандомно.', 
							'description' => 'Например "post_prev/mini". Если пусто - не будет выбираться.<br>Необходима функция get_path_files, которая есть в functions-template.php дефолтного шаблона.', 
							'default' => ''
						), 						
			'cache_key' => array(
							'type' => 'text', 
							'name' => 'Ключ кеширования, если нужно (а нужно!!!) кешировать.', 
							'description' => 'Установить, например "similar_post", после отладки внешнего вида.', 
							'default' => ''
						), 																																		
									),
		'Похожие страницы', // титул
		'Укажите необходимые опции.'   // инфо
	);
}

# функции плагина
function similar_posts_content_end($args = array())
{
 global $page;
	
 $options = mso_get_option('plugin_similar_posts', 'plugins', array());
	
	$def_options = array(
		'count' => 5, 
		'type' => 'all',
		'tag_prior' => 1,
		'title' => '<div class="page_other_pages_header">Похожие страницы</div>',
		'start' => '<div class="page_other_pages">',
		'end' => '</div">',
		'do' => '<table width="100%" border="0"><tr>',
		'posle' => '</tr></table></div>',
		'format' => '<td>[link]<br>[image]<br>[desc]</td>',
		'width' => 120, 
		'field_of_numbers' => '', 
		'field_of_desc' => '', 
		'field_of_prev' => 'prev', 
		'tags' => '',
		'symbol_count' => 140, 
		'end_of_desc' => '...',
		'cache_key' => '',
		'dir' => '',
		);
	
 $options = array_merge($def_options, $options);

 if ($options['type'] !== 'all')
 {
   $options_types = mso_explode($options['type'], false, true);
   if (!in_array($page['page_type_name'] , $options_types)) 	return $args;
 }

 if ($options['cache_key']) $cache_key = $options['cache_key'] . $page['page_id'];
 else ($cache_key = false);
 
 if ($cache_key) $out = mso_get_cache($cache_key);
 else $out = '';
 if ($out) echo $out;
 else  // если нет в кеше
 {  
   $bl_pages = array();
   // если есть что-то в указанном метаполе номеров
   if ($options['field_of_numbers'])
   {
      global $page;
      if ( isset($page['page_meta'][$options['field_of_numbers']][0]) and $page['page_meta'][$options['field_of_numbers']][0])  
      {
        $numbers = $page['page_meta'][$options['field_of_numbers']][0];

        if ($numbers) // если список страниц существует, то получаем их
        {
           $par = array( 
               'type'=> false,
               'limit'=>$options['count'],
               'work_cut'=>false,
               'page_id' => $numbers,
               'pagination'=>false,
               'custom_type'=> 'home',
               'order'=> mso_get_option('page_other_pages_order', 'templates', 'page_date_publish'),
               'order_asc'=> mso_get_option('page_other_pages_order_asc', 'templates', 'random')         );
           if ($options['full_text'] == 'false')	$par['cut'] = false; // нужно ли все содержания для поиска картинки   
           $bl_pages = mso_get_pages($par , $_temp);
        }
      }
   }
   
   // если страницы не найдены
   // то определим их
   if (!$bl_pages)
   {
      // получим все страницы
	    $par = array( 
			   'no_limit' => true, // нам нужны все записи
			   'type' => false, // любой тип страниц
			   'custom_type' => 'home', // запрос как в home
			   'work_cut'=>false,
			   'order' => 'page_date_publish', // запрос как в home
			   'order_asc' => 'desc', // в обратном порядке
		  	); 

	     $all_pages = mso_get_pages($par, $pagination); // получим все - второй параметр нужен для сформированной пагинации
	     $bl_pages = array();
	
	    if ($all_pages) // есть страницы
	    { 	
		     foreach ($all_pages as $cur_page) // сравниваем все страницы с текущей
		     {
		       if ($cur_page['page_id'] == $page['page_id']) continue;
		       // похожесть страниц это сколько у них одинаковых меток и категорий
			     $tag_similarity = count(array_intersect($cur_page['page_tags'] , $page['page_tags'])); 
			     $cat_similarity = count(array_intersect($cur_page['page_categories'] , $page['page_categories'])); 
			     if (is_numeric($options['tag_prior'])) $k = $options['tag_prior']; 	else $k = 1;
			     $similarity = $tag_similarity*$k + $cat_similarity;
			     if ($similarity) // если похожесть больше 0, тоесть есть общие метки и категории
			     {
   			     $cur_page['similarity'] = $similarity;
	           $bl_pages[] = $cur_page; 
			     } // if ($similarity)
	       }	// foreach	
      } // if ($all_pages)
  
      // массив похожих страниц будет отортирован по убыванию похожести
      usort($bl_pages, "cmp"); 
   } // if (!$similar_pages)

   if ($bl_pages)
   {
			$i = 0;
			$out_pages = '';
			
			if ($options['dir'] and function_exists('get_path_files') )
		      $imgs = get_path_files(getinfo('uploads_dir') . $options['dir'] . '/', getinfo('uploads_url') . $options['dir'] . '/');
		  else $imgs = false;
		        		
			foreach ($bl_pages as $similar_page)
			{
			  if ($i >= $options['count']) break; // выводим только заданное кол-во наиболее похожих
			  
			  if (isset($similar_page['page_meta'][$options['field_of_prev']][0]) and $similar_page['page_meta'][$options['field_of_prev']][0])
			     $image = $similar_page['page_meta'][$options['field_of_prev']][0];
			  else  
			     $image = similar_posts_get_pictures($similar_page['page_content']);
			     
			  if (!$image and $imgs)
			  {
			      $rand_id = array_rand($imgs, 1);
	          $image = $imgs[$rand_id]; 
	          if (count($imgs)>1) unset ($imgs[$rand_id]);
			  }
			  
			  if ($image)   
			  {
			     if ($options['width']) $width = ' width="' . $options['width'] . '"'; else $width = '';
			     $image = '<img src="' . $image . '"' . $width  . ' alt="' . $similar_page['page_title'] . '">';
			  }
			  
			  
			  
			  // теперь desс
			  if (isset($similar_page['page_meta'][$options['field_of_desc']][0]) and $similar_page['page_meta'][$options['field_of_desc']][0])
			     $desc = $similar_page['page_meta'][$options['field_of_desc']][0];
			  else   
			     $desc = $similar_page['page_content'];
			  $desc = similar_posts_get_desc($desc , $options['symbol_count'] , $options['tags'], $options['end_of_desc']);			  

			  $url = getinfo('site_url') . 'page/' . $similar_page['page_slug'];
			  $title = mso_strip($similar_page['page_title']);
			  
			  if (isset($similar_page['page_meta']['description'][0]) and $similar_page['page_meta']['description'][0])
			      $seo_desc = $similar_page['page_meta']['description'][0];
			  else $seo_desc = $title; 			  
			  
			  $link = '<a href="' . $url . '" title="' . $seo_desc . '">' . $similar_page['page_title'] . '</a>';
			  
		    $out_pages .= str_replace( 
			    array('[link]',	'[image]','[title]' , '[desc]' , '[url]'), 
			    array($link, $image	, $title , $desc , $url),
			    $options['format']);			  
				$i++;
			}
			
			if ($out_pages)
			{
			  $out .= $options['start'] . $options['title'];
			  $out .=  $options['do'] . $out_pages . $options['posle'];
			  $out .= $options['end'];
			  echo $out;
			} 
 
   }
   mso_add_cache($cache_key, $out);
	}//if cache
	
	return $args;
}

# end file
