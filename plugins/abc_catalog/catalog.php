<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

//функция, передаваемая mso_get_page в качестве параметра для построения запроса
//сделана из _mso_sql_build_home с небольшими изменениями
function mso_sql_build_catalog($r, &$pag)
{
	$CI = & get_instance();

	$offset = 0;


	// если указан массив номеров рубрик, значит выводим только его
	if ($r['categories']) $categories = true;
	else $categories = false;

	// если указаны номера записей, котоыре следует исключить
	if ($r['exclude_page_id']) $exclude_page_id = true;
	else $exclude_page_id = false;

	// при получении учитываем часовой пояс
	$date_now = mso_date_convert('Y-m-d H:i:s', date('Y-m-d H:i:s'));
	if ($r['pagination'])
	{
		# пагинация
		# для неё нужно при том же запросе указываем общее кол-во записей и кол-во на страницу
		# сама пагинация выводится отдельным плагином
		# запрос один в один, кроме limit и юзеров
		$CI->db->select('page.page_id');
		$CI->db->from('page');

		if ($r['page_status']) $CI->db->where('page.page_status', $r['page_status']);

		if ($r['date_now']) $CI->db->where('page_date_publish <', $date_now);

		if ($r['type']) $CI->db->where('page_type.page_type_name', $r['type']);

		if ($r['page_id']) $CI->db->where('page.page_id', $r['page_id']);

		if ($r['page_id_autor']) $CI->db->where('page.page_id_autor', $r['page_id_autor']);
		
    $CI->db->like('page_title', $r['bukva'], 'after');
    
		$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');


		if ($categories)
			$CI->db->where_in('category.category_id', $r['categories']);

		if ($exclude_page_id)
			$CI->db->where_not_in('page.page_id', $r['exclude_page_id']);


		$CI->db->order_by('page_date_publish', 'desc');
		

		
		$query = $CI->db->get();


		$pag_row = $query->num_rows();
		if ($pag_row > 0)
		{
			$pag['maxcount'] = ceil($pag_row / $r['limit']); // всего станиц пагинации
			$pag['limit'] = $r['limit']; // записей на страницу

			$current_paged = mso_current_paged();
			if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];

			$offset = $current_paged * $pag['limit'] - $pag['limit'];
		}
		else
		{
			$pag = false;
		}
	}
	else
		$pag = false;

	// теперь сами страницы

	if (!$r['all_fields'])
	{
		if ($r['content'])
		{
			$CI->db->select('page.page_id, page_type_name, page_slug, page_title, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, users_avatar_url, COUNT(comments_id) AS page_count_comments, page.page_id_autor, users_description, users_login');
		}
		else
		{
			$CI->db->select('page.page_id, page_type_name, page_slug, page_title, "" AS page_content, page_date_publish, page_status, users_nik, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, users_avatar_url, COUNT(comments_id) AS page_count_comments, page.page_id_autor, users_description, users_login', false);
		}
	}
	else
	{
		$CI->db->select('page.*, page_type.*, users.*, COUNT(comments_id) AS page_count_comments');
	}

	$CI->db->from('page');

	if ($r['page_id']) $CI->db->where('page.page_id', $r['page_id']);

	if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);

	if ($r['type']) $CI->db->where('page_type_name', $r['type']);

	if ($r['date_now']) $CI->db->where('page_date_publish <', $date_now );

	if ($r['only_feed']) $CI->db->where('page_feed_allow', '1');
	

	if ($r['page_id_autor']) $CI->db->where('page.page_id_autor', $r['page_id_autor']);

	$CI->db->join('users', 'users.users_id = page.page_id_autor', 'left');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id', 'left');
	$CI->db->join('comments', 'comments.comments_page_id = page.page_id AND comments_approved = 1', 'left');

	if ($categories)
		$CI->db->where_in('category.category_id', $r['categories']);
    
    
    
  	if ($exclude_page_id)
			$CI->db->where_not_in('page.page_id', $r['exclude_page_id']);

  $CI->db->like('page_title', $r['bukva'], 'after');
  
	$CI->db->order_by($r['order'], $r['order_asc']);

	$CI->db->group_by('page.page_id');
	$CI->db->group_by('comments_page_id');

	if (!$r['no_limit'])
	{
		if ($pag and $offset) $CI->db->limit($r['limit'], $offset);
			else $CI->db->limit($r['limit']);
	}

}


// функция, формирующая и выводящая на экран абвгдейку
function abs_catalog_navigator($catalog_url = '')
{
 $fist_letter = 1040; 
 $last_letter = 1071;
 $i = $fist_letter;
 $navigator ='-';
 while ($i <= $last_letter) 
 {
   $bukva = '&#' . $i;
   $bukva_link = '<a href="' . $catalog_url . $bukva . '" title= Отобрщить записи на букву "' . $bukva . '">' . $bukva . '</a>';
   $navigator .= $bukva_link;
   $navigator .= " - ";
   $i++;

 }
 
 return $navigator;
 
}

# получение всех рубрик в одномерной структуре
# функция возвращает массив, 
#[10] => Array
#        (
#            [category_id] => 10
#            [category_id_parent] => 1
#            [category_menu_order] => 4
#            [category_name] => Новости
#            [parents] => 3 1
#            [childs] => 6 5
#            [level] => 2
#        )
# где ключ - номер рубрики
# массив можно использовать для быстрого доступа к параметрам рубрик
# автоматом вычисляются родители (parents) и дочерние элементы (childs)
# дополнительный параметр level указывает на левый отступ от края списка

$bukva = mso_segment(2);
$bukva = mso_strip($bukva);
global $MSO;
$catalog_url = $MSO->config['site_url'] . $options['catalog_slug'] . '/';

// Построим навигацию по подкатегориям двух уровней заданной категории
//одновременно попытаемя найти категорию с нашим слугом
$category_nav = '';
$category_this = false;
$category_id = intval($options['category_id']);

if ( $category_id >0 )
{
  $all_cats = mso_cat_array_single('page', 'category_name', 'ASC', $options['type'], true);
  if ( isset($all_cats[$category_id]) )
  {
    $level1 = $all_cats[$category_id]['level'];
    $category_childs  = $all_cats[$category_id]['childs'];
    $categorys  = mso_explode($category_childs , TRUE , TRUE);
    
    foreach ($categorys  as $child)
    if ( $all_cats[$child]['level'] == $level1+1 )
    {
 
       if ($all_cats[$child]['category_slug'] == $bukva) $category_this = $child;
       $category_nav .= '<a href="' . $catalog_url . $all_cats[$child]['category_slug'] . '" title= "Фильтр по категории"' . $all_cats[$child]['category_name'] . '">' . $all_cats[$child]['category_name'] . '</a>';
       $category_sub_childs = $all_cats[$child]['childs'];
       $categorys_sub = mso_explode($category_sub_childs , TRUE , TRUE);
       
       $category_sub_nav = '';
       foreach ($categorys_sub as $sub_child)
       if ( $all_cats[$sub_child]['level'] == $level1+2 )
       {
         $url = '<a href="' . $catalog_url . $all_cats[$sub_child]['category_slug'] . '" title= "Фильтр по категории"' . $all_cats[$sub_child]['category_name'] . '">' . $all_cats[$sub_child]['category_name'] . '</a>';
         if ($all_cats[$sub_child]['category_slug'] == $bukva) $category_this = $sub_child;
         if ($category_sub_nav) $category_sub_nav .= ', ' . $url;          
         else $category_sub_nav = ': ' . $url;     
       }
       $category_nav = $category_nav .  $category_sub_nav . '.<br>';
    }
  }
}

if ($bukva == 'next') $bukva = '';

// Все теперь зависит от того нашли ли мы урл категории или нет
$title = $options['catalog_name'];

if ($category_this) 
   $title_page = $options['catalog_name'] . ' - ' . $all_cats[$category_this]['category_name'];
else 
{
  if ($bukva)
      $title_page = $options['catalog_name'] . ' на букву ' . $bukva;
  else $title_page = $options['catalog_name'] . ' все.';
}
   
       

mso_head_meta('title', $title_page , $title); // meta title страницы
mso_head_meta('description', $title ); // meta title страницы

require(getinfo('template_dir') . 'main-start.php');

echo NR . '<div class="type type_home">' . NR;

echo '<H1>' . $title_page  . '</H1>';
$do = '<div>';
// $do = NR . '<div class="pagination">'; // начало блока абв...
$posle = '</div>' ; // конец блока	абв...	
$abs_catalog = abs_catalog_navigator($catalog_url);	  
echo $do;
echo '<table width="100%" border="1" cellpadding="5" cellspacing="5" align="center" class="box">
     <tr>
       <td> 
         <table width="100%" border="1" cellpadding="5" cellspacing="5" align="center" >
           <tr>
             <td width="20%" align="center"><a href="' . $catalog_url . '" title = "Каталог"><b>Все ' . $options['catalog_name']  . '</b></a></td> 
             <td align="center">' . $abs_catalog . '</td>
           </tr> 
         </table>
        </td>
      </tr> 
      <tr align="left"><td>'.  $category_nav . '</td></tr>
     </table>';
              
echo $posle;




// параметры для получения страниц
// строятся в зависиости от того - по букве мы будем делать выборку или по категории.
if ($category_this)
 // строится массив параметров для выборки страниц для найденой категории
$par = array( 
      // поскольку вызываем из страницы с неизвестным типом, то укажем тип принудительно
      'custom_type' => 'home',
      
      // нашу категорию в массив
      'cat_id' => $category_this,
      
			// колво записей на главной
			'limit' => mso_get_option('limit_post', 'templates', '15'),
			
			// полные ли записи (1) или только заголовки (0)
      'content'=> (bool)$options['full_text'],

			// текст для Далее
			'cut' => mso_get_option('more', 'templates', 'Читать полностью »'),
			
			// сортировка рубрик
			'cat_order' => 'category_id_parent',
			 
			// порядок сортировки
			'cat_order_asc' => 'asc',
			
			// тип страниц
			'type' => $options['type']
			
			);				
      
else // строится массив параметров для выборки страниц по первой букве
$par = array( 

			// колво записей на главной
			'limit' => mso_get_option('limit_post', 'templates', '15'),
			
			// буква алфавита для отображения
			'bukva' => $bukva,
	
			// полные ли записи (1) или только заголовки (0)
			'content'=> (bool)$options['full_text'], 
			
			// текст для Далее
			'cut' => mso_get_option('more', 'templates', 'Читать полностью »'),
			
			// категории
			'categories' => mso_explode($options['categories']),
			
			// исключаемые страницы
			'exclude_page_id' => mso_explode($options['exclude_page_id']),
			
			// сортировка рубрик
			'cat_order' => 'category_id_parent', 
			
			// порядок сортировки
			'cat_order_asc' => 'asc',
			
			// тип страниц
			'type' => $options['type'],
			
			//SQL функция, скнструированная нами в начале
			'custom_func' => 'mso_sql_build_catalog',
			
			//наверное, надо отдавать все записи
			'all_fields' => true,
	
			); 


			
$pages = mso_get_pages($par, $pagination); // получим все - второй параметр нужен для сформированной пагинации

if ($pages) // есть страницы
{ 	
	// выводим полнные тексты или списком
	if ( !$options['full_text'] ) echo '<ul class="category">';
		
	foreach ($pages as $page) : // выводим в цикле
  		
		extract($page);
		
		// выводим полные тексты или списком
		if ($options['full_text'] )
		{ 
			
			echo NR . '<div class="page_only">' . NR;
		
			mso_page_title($page_slug, $page_title, '<h1>', '</h1>', true);

			echo '<div class="info">';
				mso_page_date($page_date_publish, 
							array(	'format' => 'D, j F Y г.', // 'd/m/Y H:i:s'
									'days' => t('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
									'month' => t('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
							'<span>', '</span><br>');
				
				mso_page_cat_link($page_categories, ' -&gt; ', '<span>'.t('Рубрика').':</span> ', '<br>');
				mso_page_tag_link($page_tags, ' | ', '<span>'.t('Метки').':</span> ', '');
				mso_page_edit_link($page_id, 'Edit page', ' [', ']');
				# mso_page_feed($page_slug, 'комментарии по RSS', '<br><span>Подписаться</span> на ', '', true);
			echo '</div>';
			
			echo '<div class="page_content type_home">';
			
				mso_page_content($page_content);
				mso_page_content_end();
				echo '<div class="break"></div>';
				
				mso_page_comments_link( array( 
					'page_comment_allow' => $page_comment_allow,
					'page_slug' => $page_slug,
					'title' => t('Обсудить'). ' (' . $page_count_comments . ')',
					'title_no_link' => t('Читать комментарии').' (' . $page_count_comments . ')',
					'do' => '<div class="comments-link"><span>',
					'posle' => '</span></div>',
					'page_count_comments' => $page_count_comments
				 ));
				
				// mso_page_comments_link($page_comment_allow, $page_slug, 'Обсудить (' . $page_count_comments . ')', '<div class="comments-link">', '</div>');
				
			echo '</div>';
			echo NR . '</div><!--div class="page_only"-->' . NR;
		}
		else // списком
		{
			mso_page_title($page_slug, $page_title, '<li>', '', true);
			mso_page_date($page_date_publish, 'd/m/Y', ' - ', '');
			echo '</li>';
		}
		
	endforeach;
	
	if ( !$options['full_text'] ) echo '</ul><!--ul class="category"-->';
	
	mso_hook('pagination', $pagination);	
	
}			
echo NR . '</div><!-- class="type type_home" -->' . NR;

# конечная часть шаблона
require(getinfo('template_dir') . 'main-end.php');

?>