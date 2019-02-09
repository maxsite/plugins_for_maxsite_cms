<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// обработчик аякс запросов смены дискуссии коммента

	$return = array(
		'error_code' => 1,
		'error_description' => 'Ошибка. Неверные данные.',
		'resp' => '0',
	);
	
	if ( $post = mso_check_post(array('c_id' , 'e_id' , 'e_t_id' , 'act' , 'b_id')) )
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

               $err = bookmaker_edit($comuser_id , $post['e_id'] , $post['e_t_id'] , $post['act']);
               if (!$err) // если не возвращает ошибку
               {
				          $return['error_code'] = 0;
				          $return['error_description'] = '';               
                  if ($post['act']) 
                     $return['resp'] = 'Добавлена';
                  else    
                     $return['resp'] = 'Удалена';
                  
               }   
               else $return['error_description'] = $err;
            }    
	          else
	             $return['error_description'] = 'Ошибка сущности.';                  
	       }
	       else 
				    $return['error_description'] = 'Ошибка пользователя. Перегрузите страницу';	       
	       
	       // то выводим кнопку в зависимости от того: есть ли сущность в закладках у комюзера
	       
	   }
	   else $return['error_description'] = 'Ошибка сессии. Перегрузите страницу';	
	}
	
	


	
	echo json_encode($return);	


	
?>