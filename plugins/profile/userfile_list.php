<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

  // выводим список загруженных файлов пользователя
/*
	$CI = & get_instance();
	$CI->load->helper('file'); // хелпер для работы с файлами
	$CI->load->helper('directory');
	$CI->load->helper('form');
*/
 $uploads_url = getinfo('uploads_url');

 
 
 $pag = false;


  // $width = '" width = "' . $options_admin['admin_picture_width'];
  $width = '';
  

	
  echo '<div id="files_list" class="upload_pictures">';

  $id=0;
  if ($files)
	foreach ($files as $file)
	{
	    
		$file_arr = explode("." , $file['file']);

		// ключ файла для формы
    $file_form_key = $file_arr[0] . '_ext_' . $file_arr[1];
   
      $subpath = $subdir . '/' .  $file['comuser_id'] . '/';
	  $prev = '<img class="uploads_img" src="' . $uploads_url . $subpath . $file['prev'] . $file['file'] . '">';
	    	
	  $url = $uploads_url . $subdir . '/' . $file['comuser_id'] . '/' . $file['file'];
	  
	echo '<div class="uploads_picture">';
    echo '<a class="lightbox" href="' . $url . '">' . $prev . '</a>';
    
    if ($file['use']) 
    {
      $id++;
      echo '<p><a href="#" data-dropdown="#usepics-'.$id.'" class="dropdown" title="Показать">Использовано (' . count($file['use']) . ')...</a></p>';
      echo '<div id="usepics-'.$id.'" class="dropdown-menu has-tip " style="display: none;"><ul>';
      foreach ($file['use'] as $use)
      {
        $link = getinfo('siteurl') . 'goto/disc/' . $use['discussion_id'] . '/comm/' . $use['comment_id'];
        echo '<li><a href="' . $link . '" target="_blank" title="Перейти к сообщению, в котором использован файл">' . $use['discussion_title'] . '</a></li>';
      }
      echo '</ul></div>';
    }  
    else if ($file['file'] == 'avatar.jpg') echo '<span class="uploads_avatar">Аватар</span>';
    echo '</div>';
	}  
  else echo 'Ничего не загружено.';
  
  echo '</div>';

/*
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
  */

?>