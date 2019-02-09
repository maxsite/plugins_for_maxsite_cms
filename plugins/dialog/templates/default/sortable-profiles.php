<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */

// здесь задаются варианты сортировки пользователей

// в зависимости от роли пользователя сформируем массив сортироваки
// массив для всех:
  $array_fields = 
     array(
        'name' => array('sort_field'=>'profile_psevdonim' , 'sort_order'=>'asc' , 'title'=>'Имя' , 'desc'=>'По имени'), 
        'comments' => array('sort_field'=>'profile_comments_count' , 'sort_order'=>'desc' , 'title'=>'Комментарии' , 'desc'=>'По колличеству комментариев'),
        'register' => array('sort_field'=>'profile_date_first_visit' , 'sort_order'=>'desc' , 'title'=>'Регистрация' , 'desc'=>'По дате первого визита'), 
        'active' => array('sort_field'=>'profile_date_last_active' , 'sort_order'=>'desc' , 'title'=>'Активность' , 'desc'=>'По последней активности'), 
        'visit' => array('sort_field'=>'profile_date_last_visit' , 'sort_order'=>'desc' , 'title'=>'Визит' , 'desc'=>'По дате последнего визита'), 
        'rate' => array('sort_field'=>'profile_rate' , 'sort_order'=>'desc' , 'title'=>'Рейтинг' , 'desc'=>'По рейтингу'), 
 
 

	   );
        

  
  if ( ($comuser_role == 2) or ($comuser_role == 3))
  {
     $array_fields2 = array(
        'spam' => array('sort_field'=>'profile_spam_check' , 'sort_order'=>'asc' , 'title'=>'Спам' , 'desc'=>'Спам'), 
        'no-spam' => array('sort_field'=>'profile_spam_check' , 'sort_order'=>'desc' , 'title'=>'Не спам' , 'desc'=>'Не спам'), 
        'id' => array('sort_field'=>'profile_user_id' , 'sort_order'=>'asc' , 'title'=>'Id' , 'desc'=>'По номеру'), 
       
		);     
     
     $array_fields = $array_fields + $array_fields2;
  }
  
     
?>