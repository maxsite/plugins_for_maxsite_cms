<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// обработчик аякс запросов смены дискуссии коммента

	$return = array(
		'error_code' => 1,
		'error_description' => 'Ошибка. Неверные данные.',
		'resp' => '0',
	);
	
	if ( $post = mso_check_post(array('c_id' , 'e_id' , 'e_t_id' , 'b_id')) )
	{	
	    global $MSO;	
	   // если передан комюзер залогинен
	   if (isset($MSO->data['session']['comuser']) and ($comuser = $MSO->data['session']['comuser']))
	   {
	       $comuser_id = $comuser['comusers_id'];
	   
	       if ($comuser_id == $post['c_id'])
	       {
	          // проверим id сущности на целочисленность
	          
	          
	          if ($post['e_id'])
	          {
               require(getinfo('plugins_dir') . 'bookmaker/functions.php');	  
				       $return['error_code'] = 0;
				       $return['error_description'] = '';
				       
               // если пользователь добавил закладку к сущности e_id, типа e_t_id
               if (bookmaker_added($comuser_id , $post['e_id'] , $post['e_t_id']))   
               {
                 // выводим кнопку отдобавления
                 $button_value = 'Удалить из закладок'; 
                 $button_title = 'Удалить материал из закладок';
                 $button_act = 0;                 
               }
               else
               {
                 // выводим кнопку добавления
                 $button_value = 'В закладки'; 
                 $button_title = 'Добавить материал в закладки';
                 $button_act = 1;                    
               }   
               
               $return['resp'] = '<input id="bookmaker_edit' . $post['b_id'] . '" type="button" value="'.$button_value.'" title="'.$button_title.'" onClick="editBM(' . $button_act . ' , ' . $post['b_id'] . ') ">';                 
                   
	          }
	          else
	             $return['error_description'] = 'Ошибка сущности.';
	       
	       }
	       else 
				    $return['error_description'] = 'Ошибка сессии. Перегрузите страницу';	       
	       
	       // то выводим кнопку в зависимости от того: есть ли сущность в закладках у комюзера
	       
	   }
	   else $return['resp'] = '';
	}
	
	


	
	echo json_encode($return);	


	
?>