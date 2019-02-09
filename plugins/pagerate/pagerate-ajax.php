<?php 
	if (!defined('BASEPATH')) exit('No direct script access allowed');
	
	global $_COOKIE;
	
	function change_value( $data, $x) 
	{
		switch ( $x ) {
			case 0:
					$data--;
					break;
			case 1: 
					$data++;
					break;		
			case 2: 	break;
		}
		return $data;
	}
	
	mso_checkreferer(); // защищаем реферер
	if ( $post = mso_check_post(array('send_mail_type', 'title', 'slug', 'change')) )
	{
		$send_mail_type = $post['send_mail_type'];
		$title = $post['title'];
		$slug = $post['slug'];
		$change = $post['change'];
		
		// function mso_mail($email = '', $subject = '', $message = '', $from = false, $preferences = array())
		$email = mso_get_option('admin_email', 'general', 'admin@site.com'); // куда приходят письма

	
		$options = mso_get_option('plugin_pagerate', 'plugins', array() ); // получаем опции
		$subject = $options['mail_subject'];
		if (!isset($subject)) $subject = 'Изменение рейтинга';
	
		if ( is_login() ) {
			// кто изменил?
			
		}
		$text = 'Рейтинг записи <a href="' . getinfo('site_url') .'page/' . $slug . '">' . $title . '</a>';
		$preferences['mailtype'] = 'html';
		switch ( $send_mail_type ) {
			case '1':
						$text .= ' был понижен';
						mso_mail( $email, $subject, $text, false, $preferences);
						break;
			case '2':
						$text .= ' был повышен';
						mso_mail( $email, $subject, $text, false, $preferences);
						break;						
			case '3':
						if ( $change == 0 ) { $text .= ' был понижен'; }
						else if ( $change == 1 ) { $text .= ' был повышен'; }
						else { $text .= ' был изменен'; }
						mso_mail( $email, $subject, $text, false, $preferences);
						break;						
		}
	}
	
	if ( $post = mso_check_post(array('page_id', 'value')) )
	{
		/* проверка на уникальность, что уже нажимали для этого поста */
		$curvalue = 0;
		$value = $post['value'];		
		$pageid = $post['page_id'];
		
		if ( $value == 2 ) {
		      $curvalue = 0;
		      $CI = & get_instance();
		      $CI->db->select('meta_value');
		      $CI->db->from('meta');
		      $CI->db->where('meta_key', 'pagerate_value');
		      $CI->db->where('meta_id_obj', $pageid);
		      $CI->db->limit(1);
		      $query = $CI->db->get();
		      if ($query->num_rows() > 0)
		      {
			      $row = $query->row();
			      $curvalue = $row->meta_value;
			      $curvalue = change_value( $curvalue, $value );
		      }

		      $classx = '';
		      if ( $curvalue > 0 ) {
			      $classx = 'pagerate_value_plus';
		      } else if ( $curvalue < 0 ){
			      $classx = 'pagerate_value_minus';
		      } else {
			      $classx = 'pagerate_value_null';
		      }
		      echo '<span class="' . $classx . '">' . $curvalue . '</span>';

		} else {
		      $name_cookies = 'maxsite_d51x_pagerate';
		      $expire = 60 * 60 * 24 * 30; // 30 дней = 2592000 секунд
		      
		      if (isset($_COOKIE[$name_cookies]))	$all_pages = $_COOKIE[$name_cookies]; // значения текущего кука
			      else $all_pages = ''; // нет такой куки вообще
		      $all_pages = explode(' ', $all_pages); // разделим в массив
		      if ( in_array($pageid, $all_pages) )
		      {
			      echo 'allready_vote';
			      return;
		      }
		      
		      $all_pages[] = $pageid;
		      $all_pages = array_unique($all_pages); // удалим дубли на всякий пожарный
		      $all_pages = implode(' ', $all_pages); // соединяем обратно в строку
		      $expire = time() + $expire;
		      @setcookie($name_cookies, $all_pages, $expire); // записали в куку
		      

		      
		      $CI = & get_instance();
		      $CI->db->select('meta_value');
		      $CI->db->from('meta');
		      $CI->db->where('meta_key', 'pagerate_value');
		      $CI->db->where('meta_id_obj', $pageid);
		      $CI->db->limit(1);
		      $query = $CI->db->get();
		      if ($query->num_rows() > 0)
		      {
			      $row = $query->row();
			      $curvalue = $row->meta_value;
			      $curvalue = change_value( $curvalue, $value );
			      /* update */
			      $sqldata = array( 'meta_value' => $curvalue );
			      $CI->db->where('meta_id_obj', $pageid);
			      $CI->db->where('meta_key', 'pagerate_value');
				  $CI->db->update('meta', $sqldata);
			      
		      } else {
			      /* insert */
			      $curvalue = 0;
			      $curvalue = change_value( $curvalue, $value );
			      $sqldata = array(
		    'meta_value' => $curvalue,
		    'meta_key' => 'pagerate_value' ,
		    'meta_id_obj' => $pageid,
				'meta_table' => 'page'
		  );
			      $CI->db->insert('meta', $sqldata);

		      }
		      $classx = '';
		      if ( $curvalue > 0 ) {
			      $classx = 'pagerate_value_plus';
		      } else if ( $curvalue < 0 ){
			      $classx = 'pagerate_value_minus';
		      } else {
			      $classx = 'pagerate_value_null';
		      }
		      echo '<span class="' . $classx . '">' . $curvalue . '</span>';
		      /*
			      meta_key - pagerate_value
			      meta_id_obj - page_id
			      neta_value - value
		      */
		}
	    
	}
	
?>