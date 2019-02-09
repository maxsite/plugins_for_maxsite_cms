<?php
if (!defined('BASEPATH')) exit('No direct script access allowed'); 

function check_income( $params )
{
	if ( ! is_numeric( $params['service_id'] ) ) return false;
	if ( isset( $params['test']) and $params['test'] ) return true;
	$CI = &get_instance();
	$CI->db->select('id, service_id, cost, mincost');
	$CI->db->where('service_id', $params['service_id']);
	$query = $CI->db->get('a1pay_services');
	if ($query->num_rows() > 0)
	{
		$row = $query->row();
		if ( $row->mincost == -1 )
		{
			//фиксированная цена
			if ( $row->cost == $params['system_income'] )
				return true; 				// цена верна
			else 
				return false; 				// цена подделана
		}
	} else return false;
}

function alite_processor ($t,$secret,$test)
	// Функция обработки A1Lite "URL скрипта обработчика на Вашем сайте"
	// $t - Данные $_POST на входе
	// $secret - "Секретный ключ" совпадающий с указанным в настройках формы создания сервиса
{
	$params = array(	'tid' => $t['tid'],
	'name' => $t['name'], 
	'comment' => $t['comment'],
	'partner_id' => $t['partner_id'],
	'service_id' => $t['service_id'],
	'order_id' => $t['order_id'],
	'type' => $t['type'],
	'partner_income' => $t['partner_income'],
	'system_income' => $t['system_income'],
	);

	
	
	if ( $test ) $params['test'] = 1;
	$params['check'] = md5(join('', array_values($params)) .  $secret);
 
	if ($params['check'] === $t['check'])
	{
		// Действия по зачислению платежа. A1Lite - Ключи совпали.
		// сверим сумму
		if ( check_income( $params ) )
		{
			$get = http_build_query( $params, '', '&');
			header('Location: ' . getinfo('site_url') . 'a1success?' . $get);
		} else {
			header('Location: ' . getinfo('site_url') . 'a1fail?result=sum_error');
		}
	}
	else
	{
		// Действия по ошибке. A1Lite - Ключи не совпали.
		header('Location: ' . getinfo('site_url') . 'a1fail?result=sign_error');
	}
 
	return $ok;
}
 
 
	$options_key = 'a1pay';
	$options = mso_get_option($options_key, 'plugins', array());
	$secret = ( isset($options['secret_key']) and !empty($options['secret_key']) ) ? $options['secret_key'] : '';	
	$test =  ( isset($options['tests']) and !empty($options['tests']) ) ? true : false;	
	$res = alite_processor($_POST,$secret,$test);
?>
