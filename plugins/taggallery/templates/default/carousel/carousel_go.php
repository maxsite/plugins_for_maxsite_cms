<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */

  
  if ($options['carousel_picture_width']) $html_adds = ' width="' . $options['carousel_picture_width'] . '" '; 
  else $html_adds = '';
  
  $pagination = false; // без пагинации
  
  $pictures_car = taggallery_get_pictures(array('tags' => $picture['tags'] , 'sort_field' => 'picture_height') , $pagination);
  
if ( count($pictures_car)<3 ) 
{
   $options['carousel_picture_do'] ='<td align="center">';
   $options['carousel_picture_posle'] ='</td>';
}
  $pictures_out = '';
  
 if ($pictures_car)
  foreach ($pictures_car as $picture_car)
  {
    if ($picture_car['picture_mini_url']) $mini_url = $picture_car['picture_mini_url'];
    else $mini_url = $siteurl . 'uploads/' . $picture_car['picture_dir'] . 'mini/' . $picture_car['picture_file'];
   
		$mini_link = '<img src="' . $mini_url . '" alt="' . $picture_car['picture_title'] .'" ' . $html_adds . ' >';
		$picture_page_url = $siteurl . $options['picture_slug'] . '/' . $options['picture_prefix'] . $picture_car['picture_slug'];
		$link = '<a href="' . $picture_page_url . '" title="' . $picture_car['picture_title'] . '">' . $mini_link . '</a>';    
    
    $pictures_out .= $options['carousel_picture_do'] . $link . $options['carousel_picture_posle'];  
  }


  
if ($pictures_out) 
if (count($pictures_car)>2)
{  
$out .= '
<div id="jCarouselLiteDemo">
<div class="carousel mouseWheel">
<table border="0"><tr><td align="center">
<a href="#" class="prev">&nbsp</a>
</td><td align="center">
<div class="jCarouselLite">
        <ul>
';

$out .= $pictures_out;


$out .= '
      </ul>
    </div>
</td><td align="center">
<a href="#" class="next">&nbsp</a>
</td></tr>
</table>
</div>
</div>
';
}  
else
{
$out .= '
<table><tr>
';

$out .= $pictures_out;

$out .= '
</tr>
</table>
';
}
?>