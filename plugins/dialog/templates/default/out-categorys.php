<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин Dialog для MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// в этом файле в $out выводятся дискуссии в массиве $forums

foreach ($forums as $forum)
{
  if (!$forum['categorys']) continue;
  
  $out .= '<div class="tab_forum">';
  $out .= '<table class="table_forum">';  
  $out .= '<tr><th>' . $forum['forum_title'] .'</th><th>' . $options['count_discussions'] .'</th><th>' . $options['new_discussions'] .'</th><th>' . $options['comments_count'] .'</th></tr>';
  $out .= '<tbody>';

  foreach ($forum['categorys'] as $category)
  {
     extract ($category);
     $last_disc = '';
     if ($category['disc'])
     {
       foreach ($category['disc'] as $disc)
       {
          $last_disc .= '<span class="cat_disc"><a href="' . $siteurl . $options['discussion_slug'] . '/' . $disc['discussion_id'] . '" title="' . $disc['discussion_desc'] . '">' . $disc['discussion_title'] . '</a></span>';
       }
     }
     
     $category_link = '<a href="' . $siteurl . $options['main_slug'] . '/' . $category_slug . '" title="' . $category_desc . '">' . $category_title . '</a>'; 
     
     $out .= '<tr class="cur-cat">';
  
     $out .= '<td class="cat-link"><span class="category_title">' . $category_link . '</span><span class="category_desc">' . $category_desc . '</span>' . 
               
     '<br>' . $options['title_last_desc'] . $last_disc . '</td>';
     
     $out .= '<td>' . $count . '</td>';
     $out .= '<td>' . $news . '</td>';
     $out .= '<td>' . $comments_count . '</td>';
     $out .= '</tr>';
   }
$out .= '</tbody></table></div>';
}     
?>