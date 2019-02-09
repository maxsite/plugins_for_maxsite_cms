<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */

// здесь задаются варианты сортировки комментариев

// в зависимости от роли пользователя сформируем массив сортироваки
// массив для всех:
  $array_fields = 
     array(
        'date' => array('sort_field'=>'comment_date_create' , 'sort_order'=>'asc' , 'title'=>'Старые' , 'desc'=>'От старых к новым'), 
        'undate' => array('sort_field'=>'comment_date_create' , 'sort_order'=>'desc' , 'title'=>'Новые' , 'desc'=>'От новых к старым'), 
        'rate' => array('sort_field'=>'comment_rate' , 'sort_order'=>'desc' , 'title'=>'Рейтинговые' , 'desc'=>'По рейтингу') 
        );
        

  
  if ( ($comuser_role == 2) or ($comuser_role == 3))
  {
     $array_fields2 = array(
        'approved' => array('sort_field'=>'comment_approved' , 'sort_order'=>'desc' , 'title'=>'Разрешенные' , 'desc'=>'Разрешенные'), 
        'unapproved' => array('sort_field'=>'comment_approved' , 'sort_order'=>'asc' , 'title'=>'Запрещенные' , 'desc'=>'Запрещенные'), 
        'spam_check' => array('sort_field'=>'comment_check' , 'sort_order'=>'desc' , 'title'=>'Не проверенные' , 'desc'=>'Не прошедшие проверку'), 
        'deleted' => array('sort_field'=>'comment_deleted' , 'sort_order'=>'asc' , 'title'=>'Удаленные' , 'desc'=>'Со статусом: удаленные'), 
        'spam' => array('sort_field'=>'comment_spam' , 'sort_order'=>'asc' , 'title'=>'Спам' , 'desc'=>'Определенные как спам'), 
        'edit' => array('sort_field'=>'comment_date_edit' , 'sort_order'=>'desc' , 'title'=>'Отредактированные' , 'desc'=>'Редактированные недавно'), 
        );     
     
     $array_fields = $array_fields + $array_fields2;
  }
  
     
?>