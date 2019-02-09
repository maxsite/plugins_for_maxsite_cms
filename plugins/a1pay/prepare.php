<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	// внесем данные платежа в базу
	$options = mso_get_option('a1pay', 'plugins', array());
	if ( !isset($options['duration_default']) ) $options['duration_default'] = 86400; // сутки, 24 часа
	$duration = $options['duration_default'];

	if ( !isset($options['check_ip']) ) $options['check_ip'] = 0; 
	$check_ip = $options['check_ip'];
			
	if ( !isset($options['check_duration'] )) $options['check_duration'] = 0;
	$check_duration = $options['check_duration'];		
	
	$CI = & get_instance();
    // проверим на повторность. А надо? tid всегда должен быть уникальным
	$CI->db->select('tid');
	$CI->db->where('tid', $get_arr['tid']);
	$query = $CI->db->get('a1pay_purchases');
	if ($query->num_rows() <= 0)
	{
		// добавим  транзакцию в базу
		$data = array(
						'tid' => $get_arr['tid'],
						'service_id' => $get_arr['service_id'],
						'order_id' => $get_arr['order_id'],
						'type' => $get_arr['type'],
						'cost_partner' => $get_arr['partner_income'],
						'cost_system' => $get_arr['system_income'],
						'date' => date('Y-m-d H:i:s'), // вычислить
						'check' => $get_arr['check'],
						'duration' => $duration, 
						'ip' => $_SERVER['REMOTE_ADDR'],
						'email' => (isset($get_arr['email'])) ? $get_arr['email'] : '',
						'phone' => (isset($get_arr['phone_number'])) ? $get_arr['phone_number'] : '',
					 );
		$CI->db->insert('a1pay_purchases', $data);
					
	}
	// отобразим ссылку на скачивание
	$url = getinfo('site_url') . 'a1download?tid=' . $get_arr['tid'] . '&check=' . $get_arr['check'];
	$url_text = '<a href="' . $url . '" target="blank">' . $url . '</a>';
	// получить title, ip, duration
	$CI->db->select('title, '. 
	                'ip, ' . 
					'duration' );
	$CI->db->from( 'a1pay_purchases p');
	$CI->db->join( 'a1pay_services s', 'p.service_id = s.service_id');
	$CI->db->where('p.tid', $get_arr['tid']);
	$query = $CI->db->get();
	if ($query->num_rows() > 0)
	{
			$row = $query->row();	
			$ip = $row->ip;
			$title = $row->title;
			$duration = $row->duration;
	}

	$text = '<div style="margin: 0 auto; width: 800px; margin-top: 40px;">';
	$text .=  '<h4>Оплата' .
	          ( ( isset($title) && !empty( $title ) ) ? ' "' . $title . '" '  : ' ' ) . 
	          'была произведена.</h4>';
	$text .= '<p>Ваша ссылка на скачивание:</p>';
	$text .= '<p>' . $url_text . '</p>';

	if ( $check_ip && isset($ip) && !empty($ip) ) {
		$text .= '<p>Вы сможете скачать ссылку только с вашего текущего IP-адреса: ' . $_SERVER['REMOTE_ADDR'] . ' </p>';
	}
	
	if ( $check_duration  && isset($duration) && !empty($duration) ) {
		$dt = date('Y m d H i s Z', $duration);
		$stime = explode(' ', $dt);
		$delta = $stime[6] / 60 / 60;
		$dt = getdate( mktime( $stime[3] - $delta, $stime[4], $stime[5], $stime[1], $stime[2], $stime[0]));
		$datestr = '';
		$datestr .= ( $dt['yday'] > 0 ) ?  $dt['yday'] . ' дн ' : '';
		$datestr .= ( $dt['hours'] > 0 ) ?  $dt['hours'] . ' ч ' : '';
		$datestr .= ( $dt['minutes'] > 0 ) ?  $dt['minutes'] . ' мин ' : '';
		$datestr .= ( $dt['seconds'] > 0 ) ?  $dt['seconds'] . ' сек ' : '';
		$text .= '<p>Ссылка будет действительна в течении ' . $datestr . '</p>';
	}
	
	//$text .= '<p>Скачайте <a href="'.$url.'" target="blank">' . ( ( isset($title) && !empty( $title ) ) ? $title : $url ) . '</a></p>';
	
	echo $text;
	
	

?>