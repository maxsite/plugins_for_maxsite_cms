<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

//Функция проверки на существование мыла в таблице пользователей\комментаторов.
function is_email_exists($email, $type = 'users')
{
	$ret = false;
	$CI  = &get_instance();
	$CI->db->from($type);
	$CI->db->select( $type . '_email' );
	$CI->db->limit(1);
	$CI->db->where( array( $type . '_email' => $email));
	$query = $CI->db->get();
	if( $query->num_rows() )
	{
		$row = $query->result_array();
		if( $row[0][$type.'_email'] == $email ) $ret = true;
	}
	return $ret;
}


//Проверка на существование ника пользователя\комментатора
function is_nik_exist($nik, $type = 'users')
{
	$ret = false;
	$CI = &get_instance();
	$CI->db->from($type);
	$CI->db->select( $type . '_nik' );
	$CI->db->limit(1);
	$CI->db->where( array( $type . '_nik' => $nik ) );
	$query = $CI->db->get();
	if ( $query->num_rows() )
	{
		$row = $query->result_array();
		if ( $row[0][$type.'_nik' == $nik ] )
		{
			$ret = true;
		}
		else
		{
			$ret = false;
		}
	}
	return $ret;
}


//Получение ИДа пользователя\комментатора по его нику.
function get_id_by_nik($name, $type = 'users')
{
	$ret = false;
	$id = 0;
	$CI = &get_instance();
	$CI->db->from($type);
	$CI->db->select( $type . '_id');
	$CI->db->limit(1);
	$CI->db->where( array( $type . '_nik' => $name ) );
	$query = $CI->db->get();
	if( $query->num_rows() )
	{
		$row = $query->result_array();
		$id = $row[0][$type.'_id'];
	}
	return (int) $id;
}


//Проверка на существование логина юзера (только одна таблица)
function is_users_login_exist($login)
{
	$ret = true;
	$CI = &get_instance();
	$CI->db->from('users');
	$CI->db->select('users_login');
	$CI->db->limit(1);
	$CI->db->where( array('users_login' => $login));
	$query = $CI->db->get();
	if( $query->num_rows() )
		return true;
	return false;
}


////Проверка на существование ника комъюзера //Что-то сомнительное.
//function is_comusers_nik($login, $userid)
//{
//	$ret = true;
//	$CI  = &get_instance();
//	$CI->db->from('comusers');
//	$CI->db->select('comusers_nik');
//	$CI->db->limit(1);
//	$CI->db->where( array('comusers_nik' => $login) );
//	$query = $CI->db->get();
//	if( $query->num_rows() )
//	{
//		$row = $query->row();
//		if ( ( $row->comusers_nik == $login ) and
//			( get_id_by_nik($row->comusers_nik) != $userid ) and ( $row->comusers_nik != '' ) )
//		{
//			$ret = false;
//		}
//		else
//		{
//			$ret = true;
//		}
//	}
//	return $ret;
//}


# функция отправляет новому комюзеру уведомление о новой регистрации
# первый парметр id, второй данные
function mso_email_message_new_comuser($comusers_id = 0, $ins_data = array() , $url = '' )
{
	$email = $ins_data['comusers_email']; // email куда приходят уведомления
	if (!$email) return false;

	// comusers_password
	// comusers_activate_key

	$subject = 'Регистрация на ' . getinfo('title');

	$text = 'Вы или кто-то еще зарегистрировал ваш адрес на сайте "' . getinfo('name_site') . '" - ' . getinfo('siteurl') . NR ;
	$text .= 'Если это действительно сделали вы, то вам нужно подтвердить эту регистрацию. Для этого следует пройти по ссылке: ' . NR;
	$text .= $url;
	$text .= 'И ввести следующий код для активации: '. NR;
	$text .= $ins_data['comusers_activate_key'] . NR. NR;
	$text .= '(Сохраните это письмо, поскольку код активации может понадобиться для смены пароля.)' . NR . NR;
	$text .= 'Если же эту регистрацию выполнили не вы, то просто удалите это письмо.' . NR;

	return mso_mail($email, $subject, $text, $email); // поскольку это регистрация, то отправитель - тот же email
}
