<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

  //загрузки комюзера
 
   $err = ''; 

 $sort_type = 1; 
 if (in_array(mso_segment(3) , array(1,2,3,4,5,6))) $sort_type = mso_segment(3);
 elseif ( mso_segment(3) != 'next' )
 { 
    if ( mso_get_option('page_404_http_not_found', 'templates', 1) ) header('HTTP/1.0 404 Not Found');
    $err = 'Ошибка параметра сортировки';
 } 
 
  require(getinfo('plugins_dir') . 'profile/functions_userfile.php');

  if (isset($options['pages_profiles'][$segment3])) $title = $options['pages_profiles'][$segment3];
  else $title = '';
  mso_head_meta('title', $title); // meta title страницы


// теперь сам вывод
# начальная часть шаблона
require(getinfo('shared_dir') . 'main/main-start.php');
echo NR . '<div class="type type_users">' . NR;
if ($err) echo '<div class="error">' . $err . '</div>';

// меню 
   require (getinfo('plugins_dir') . 'profile/menu-main.php' ); // выводим главное меню

  
 $all_count = get_userfile_count();
 if ($all_count)
 {
    echo '<div class="uploads_users">';
    echo '<H4>Загрузки пользователей:</H4>';
    foreach ($all_count as $cur)
    {
		  if (!$cur['comusers_nik']) $cur['comusers_nik'] = t('Пользователь'). ' ' . $cur['comusers_id'];
		  echo '<a href="' . getinfo('siteurl') . $options['profiles_slug'] . '/' . $cur['comusers_id'] . '/files">' . $cur['comusers_nik'] . '(' . $cur['filecount'] . ')</a> ';       
   
    }
    echo '</div>';
 }
 
 echo '<H4>Все загруженные:</H4>';

 echo'	
		<div class="sort_list"><p>' . t('Сортировка', 'admin') . ': <select id="f_sort_type" class="sort_type">
		<option value="' . $site_url . $options['profiles_slug'] . '/files/1"' . (($sort_type == 1)?(' selected="selected"'):('')).'>' . t('По дате загрузки', '_FILE_') . '</option>
		<option value="' . $site_url . $options['profiles_slug'] . '/files/2"'.(($sort_type == 2)?(' selected="selected"'):('')).'>' . t('По дате загрузки обратно', '_FILE_') . '</option>
		<option value="' . $site_url . $options['profiles_slug'] . '/files/3"'.(($sort_type == 3)?(' selected="selected"'):('')).'>' . t('По использованию', '_FILE_') . '</option>
		<option value="' . $site_url . $options['profiles_slug'] . '/files/4"'.(($sort_type == 4)?(' selected="selected"'):('')).'>' . t('По использованию обратно', '_FILE_') . '</option>
		<option value="' . $site_url . $options['profiles_slug'] . '/files/5"'.(($sort_type == 5)?(' selected="selected"'):('')).'>' . t('По алфавиту', '_FILE_') . '</option>
		<option value="' . $site_url . $options['profiles_slug'] . '/files/6"'.(($sort_type == 6)?(' selected="selected"'):('')).'>' . t('По алфавиту обратно', '_FILE_') . '</option>
		</select>	
 ';
	echo '<script>
	$("select.sort_type").change(function(){
		window.location = $(this).val();
	});
	</script>';
	
if (is_login_comuser())
  echo '<span class="my_files"><a href="' . getinfo('siteurl') . $options['profile_slug'] . '/files">Мои загрузки >></a></span>';

echo '</p></div>'; 
 

  // ____________________________________________________________________________
  	$CI = & get_instance();
	$CI->load->helper('file'); // хелпер для работы с файлами
	$CI->load->helper('directory');
	$CI->load->helper('form');

 	$path = getinfo('uploads_dir') . $subdir . '/';

	$dirs = directory_map($path, true); // только в текущем каталоге
	if (!$dirs) $dirs = array();

    $files = array();
	foreach ($dirs as $dir)
	{
		if (!is_dir($path . $dir)) continue; // это не каталог
		if (!is_numeric($dir)) continue;
		$cur_files = get_userfiles($dir , $subdir , $sort_type);
		if ($cur_files) $files = array_merge($files , $cur_files);
     }

  uasort($files , 'uploads_sort_' . $sort_type);
 
  require(getinfo('plugins_dir') . 'profile/userfile_list.php');

  
  echo NR . '</div><!-- class="type type_users_form" -->' . NR;
require(getinfo('shared_dir') . 'main/main-end.php');


?>