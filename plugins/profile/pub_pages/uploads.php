<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


  //загрузки комюзера

  require(getinfo('plugins_dir') . 'profile/functions_userfile.php');


  if (isset($options['pages_profiles'][$segment3])) $title = $options['pages_profiles'][$segment3];
  else $title = '';
  
  $err = ''; 
  
 $sort_type = 1; 
 if (in_array(mso_segment(4) , array(1,2,3,4,5,6))) $sort_type = mso_segment(4);
 elseif ( mso_segment(4) != 'next' )
 { 
    if ( mso_get_option('page_404_http_not_found', 'templates', 1) ) header('HTTP/1.0 404 Not Found');
    $err = 'Ошибка параметра сортировки';
 }
   
  mso_head_meta('title', $comusers_nik . ' » ' . $title); // meta title страницы


// теперь сам вывод
# начальная часть шаблона
require(getinfo('shared_dir') . 'main/main-start.php');
echo NR . '<div class="type type_users">' . NR;

if ($err) echo '<div class="error">' . $err . '</div>';

// меню страниц публичного профиля
require (getinfo('plugins_dir') . 'profile/pub_pages/menu-profiles.php' );

 


  // ____________________________________________________________________________
  
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
  
   echo'	
		<div class="sort_list"><p>' . t('Сортировка', 'admin') . ': <select id="f_sort_type" class="sort_type">
		<option value="' . $site_url . $options['profiles_slug'] . '/' . $comusers_id . '/files/1"' . (($sort_type == 1)?(' selected="selected"'):('')).'>' . t('По дате загрузки', '_FILE_') . '</option>
		<option value="' . $site_url . $options['profiles_slug'] . '/' . $comusers_id  . '/files/2"'.(($sort_type == 2)?(' selected="selected"'):('')).'>' . t('По дате загрузки обратно', '_FILE_') . '</option>
		<option value="' . $site_url . $options['profiles_slug'] . '/' . $comusers_id  . '/files/3"'.(($sort_type == 3)?(' selected="selected"'):('')).'>' . t('По использованию', '_FILE_') . '</option>
		<option value="' . $site_url . $options['profiles_slug'] . '/' . $comusers_id  . '/files/4"'.(($sort_type == 4)?(' selected="selected"'):('')).'>' . t('По использованию обратно', '_FILE_') . '</option>
		<option value="' . $site_url . $options['profiles_slug'] . '/' . $comusers_id  . '/files/5"'.(($sort_type == 5)?(' selected="selected"'):('')).'>' . t('По алфавиту', '_FILE_') . '</option>
		<option value="' . $site_url . $options['profiles_slug'] . '/' . $comusers_id  . '/files/6"'.(($sort_type == 6)?(' selected="selected"'):('')).'>' . t('По алфавиту обратно', '_FILE_') . '</option>
		</select>	
 ';
	echo '<script>
	$("select.sort_type").change(function(){
		window.location = $(this).val();
	});
	</script>';
  
  echo '<span class="my_files">';	
  if (is_login_comuser())
      echo '<a href="' . getinfo('siteurl') . $options['profile_slug'] . '/files">Мои загрузки >></a>     ';  
  echo '<a href="' . getinfo('siteurl') . $options['profiles_slug'] . '/files">Все загрузки >></a>';
      
  echo '</span></p></div>'; 



  // все файлы в массиве $files
  $files = get_userfiles($comusers_id, $subdir, $sort_type); 
  require(getinfo('plugins_dir') . 'profile/userfile_list.php'); // тут вывод файлов в $files 

  
  echo NR . '</div><!-- class="type type_users_form" -->' . NR;
	require(getinfo('shared_dir') . 'main/main-end.php');






?>