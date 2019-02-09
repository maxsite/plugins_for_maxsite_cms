<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин Dialog для MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// в этом файле в $out выводятся дискуссии в массиве $forums

foreach ($forums as $forum)
{
  if (!$forum['categorys']) continue;
  
  $out .= '<table class="table_forum">';
  
  $out .= '<tr><th>' . $forum['forum_title'] .'</th><th></th><th></th><th></th></tr><tbody>';
  $out .= '<tr class="title"><td>' . $options['title_category'] .'</td><td>' . $options['count_discussions'] .'</td><td>' . $options['new_discussions'] .'</td><td>' . $options['comments_count'] .'</td></tr>';

  foreach ($forum['categorys'] as $category)
  {
     extract ($category);
     
     $category_link = '<a href="' . $siteurl . $options['main_slug'] . '/' . $category_slug . '" title="' . $category_desc . '">' . $category_title . '</a>'; 
     
     $out .= '<tr class="cur-cat">';
  
     $out .= '<td class="cat-link"><span class="category_title">' . $category_link . '</span>' . /*'<span class="category_desc">' . $category_desc . '</span>' . */'</td>';
     $out .= '<td>' . $count . '</td>';
     $out .= '<td>' . $news . '</td>';
     $out .= '<td>' . $comments_count . '</td>';
     $out .= '</tr>';
   }
$out .= '</tbody></table>';

}
     
?>