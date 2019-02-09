<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	$CI = & get_instance();
	$CI->load->helper('form');
	$CI->load->helper('directory');
	
    //опции 
    require($plugin_dir . 'options_default.php');

	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
	  
		if ( isset($post['f_prev_field']) ) $options['prev_field'] = $post['f_prev_field'];
		if ( isset($post['f_limit']) and  (is_numeric($post['f_limit'])) )  $options['limit'] = $post['f_limit'];
        $options['out_tags']  = isset($post['f_out_tags']) ? TRUE : FALSE;

		mso_add_option('page_img_edit', $options , 'plugins');

  	    if(!is_numeric($post['f_limit'])) $message = 'Лимит не число!' ; $message = 'Обновлено!' ;
  	     
		echo '<div class="update">' . t($message, 'plugins') . '</div>';
	}


		$form = '';
		$form .= '<H3>' . t('Настройки управления превьюшками страниц.', 'plugins') . '</H3>';
		$form .= '<table>';

		$form .= '<tr><td>' . t('Поле превьюшки', 'plugins') . ' </td>' . '<td><input name="f_prev_field" type="text" value="' . $options['prev_field'] . '"></td></tr>';
		$form .= '<tr><td>' . t('Кол-во страниц', 'plugins') . ' </td>' . '<td><input name="f_limit" type="text" value="' . $options['limit'] . '"></td></tr>';
        $chckout = ''; 
        if ( (bool)$options['out_tags'] )
        {
            $chckout = 'checked="true"';
        } 	
	    $form .= '<tr><td>' . t('Выводить метки и категории', 'plugins') . '</td>' . '<td> <input name="f_out_tags" type="checkbox" ' . $chckout . '></td></tr>';
		$form .= '</table>';

		$form .=  '<button type="submit" name="f_submit">' . t('Сохранить') . '</button>';

		echo '<form action="" method="post">' . mso_form_session('f_session_id');
        echo $form;
		echo '</form>';

?>