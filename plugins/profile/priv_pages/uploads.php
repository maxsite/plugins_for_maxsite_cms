<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

  // управление загрузками комюзера
   require(getinfo('plugins_dir') . 'profile/functions_userfile.php');
 
  // выведем общее меню ________________________________________________________
  if (isset($options['pages'][mso_segment(2)])) $title = $options['pages'][mso_segment(2)];
  else $title = '';
  mso_head_meta('title', $options['title'] . ' » ' . $title); // meta title страницы

  require(getinfo('shared_dir') . 'main/main-start.php');
  echo NR . '<div class="type type_users_form">' . NR;

  require (getinfo('plugins_dir') . 'profile/priv_pages/menu.php' );
  
  echo '<span class="my_files"><a href="' . getinfo('siteurl') . $options['profiles_slug'] . '/' . $comusers_id . '/files" title="Как страницу ваших файлов другие пользователи">Публичная страница загрузок>></a></span>';  
  // ____________________________________________________________________________

  // вставим загрузчик файлов в спойлер
  // загрузчик дожен быть в плагине
  $fm_namef = getinfo('plugins_dir') . 'file_manager/manager-profile.php';
  if (file_exists($fm_namef)) 
  {
	require($fm_namef);
    echo '<div class="uploader"><div class="uploads_spoiler">';
    echo '<div class="uploads_name"><span><a href="javascript:toggle();">Развернуть/свернуть загрузчик...</a></span></div>';
    echo '<div class="uploads_text">';
    echo file_manager_profile($comusers_id);  
    echo '</div></div></div>';  
  }
  
  
  
  
  // обработчик получения картинок комюзера	
 $ajax_path = getinfo('ajax') . base64_encode('plugins/profile/userfile-ajax.php');
 $subdir = 'userfile'; 
 $sort_type = 1;

  echo'
  	<link href="' . getinfo('plugins_url') . 'profile/userfile.css" rel="stylesheet" type="text/css">
    <script src="' . getinfo('plugins_url') . 'profile/userfile.js"></script>
   ';
   
  echo '<input type="hidden" id="f_ajax_path" value="' . $ajax_path . '">';
  echo '<input type="hidden" id="f_subdir" value="' . $subdir . '">';
  
   echo'	
		<div class="sort_list"><p>' . t('Сортировка', 'admin') . ': <select id="f_sort_type">
		<option value="1"'.(($sort_type == 1)?(' selected="selected"'):('')).'>' . t('По дате загрузки', '_FILE_') . '</option>
		<option value="2"'.(($sort_type == 2)?(' selected="selected"'):('')).'>' . t('По дате загрузки обратно', '_FILE_') . '</option>
		<option value="3"'.(($sort_type == 3)?(' selected="selected"'):('')).'>' . t('По использованию', '_FILE_') . '</option>
		<option value="4"'.(($sort_type == 4)?(' selected="selected"'):('')).'>' . t('По использованию обратно', '_FILE_') . '</option>
		<option value="5"'.(($sort_type == 5)?(' selected="selected"'):('')).'>' . t('По алфавиту', '_FILE_') . '</option>
		<option value="6"'.(($sort_type == 6)?(' selected="selected"'):('')).'>' . t('По алфавиту обратно', '_FILE_') . '</option>
		</select></p></div>	
	';
	   	
	echo '<div id="files_list" class="upload_pictures">';
    echo '</div>';



  
  // выведем список файлов
  // require(getinfo('plugins_dir') . 'profile/userfile_list.php');

    echo '
<script type="text/javascript">
$(document).ready(function(){
$("div.uploads_spoiler div.uploads_name span").toggle(function(){
$(this).parent("div.uploads_name").parent("div.uploads_spoiler").children("div.uploads_text").show();
$(this).parent("div.uploads_name").parent("div.uploads_spoiler").css("backgroundPosition", " -15px 0");
},function(){
$(this).parent("div.uploads_name").parent("div.uploads_spoiler").children("div.uploads_text").hide();
$(this).parent("div.uploads_name").parent("div.uploads_spoiler").css("backgroundPosition", " 0 -15px");
});
});

</script>
    ';
  
  
  echo NR . '</div><!-- class="type type_users_form" -->' . NR;
  require(getinfo('shared_dir') . 'main/main-end.php');


?>