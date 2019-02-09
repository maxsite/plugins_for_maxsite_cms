<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */

// в зависимости от роли пользователя сформируем массив сортироваки
// массив имеет вид: slug=>(field,order,title)
// массив для всех:
  $array_fields = 
     array(
        'view' => array('sort_field'=>'discussion_view_count' , 'sort_order'=>'desc' , 'title'=>'Просмотры' , 'desc'=>'Самые просматриваемые'), 
        'date' => array('sort_field'=>'discussion_date_create' , 'sort_order'=>'asc' , 'title'=>'Свежие' , 'desc'=>'По дате создания обратно'), 
        'undate' => array('sort_field'=>'discussion_date_create' , 'sort_order'=>'desc' , 'title'=>'Старожилы' , 'desc'=>'по дате создания'), 
        'comments' => array('sort_field'=>'discussion_comments_count' , 'sort_order'=>'desc' , 'title'=>'Обсуждаемые' , 'desc'=>'По колличеству сообщений обратно'), 
        'noanswer' => array('sort_field'=>'discussion_comments_count' , 'sort_order'=>'asc' , 'title'=>'Нет ответов' , 'desc'=>'По колличеству сообщений'), 
        'activity' => array('sort_field'=>'discussion_date_last_active' , 'sort_order'=>'desc' , 'title'=>'Новые' , 'desc'=>'По дате последнего сообщения'), 
        'noactivity' => array('sort_field'=>'discussion_date_last_active' , 'sort_order'=>'asc' , 'title'=>'Старые' , 'desc'=>'По дате последнего сообщения обратно'), 
        'childs' => array('sort_field'=>'discussion_parent_comment_id' , 'sort_order'=>'desc' , 'title'=>'Порожденные' , 'desc'=>'Дискуссии, имеющие родительский коммент'),  
        );
        
  // дополнительные поля для 
  if ( ($comuser_role == 2) or ($comuser_role == 3))
  {
     $array_fields2 = array(
        'approved' => array('sort_field'=>'discussion_approved' , 'sort_order'=>'desc' , 'title'=>'Разрешенные' , 'desc'=>'Прошедшие модерацию'), 
        'unapproved' => array('sort_field'=>'discussion_approved' , 'sort_order'=>'asc' , 'title'=>'Запрещенные' , 'desc'=>'Требующие модерации'), 

        );     
     $array_fields = $array_fields + $array_fields2;
  }
  
  if ($comuser_id)
  {
     $array_fields2 = array(
        'member' => array('sort_field'=>'watch_comments_count' , 'sort_order'=>'desc' , 'title'=>'Учавствовал' , 'desc'=>'С Вашим участием'), 
        );     
     $array_fields = $array_fields + $array_fields2;
  }  
  
  
     
?>