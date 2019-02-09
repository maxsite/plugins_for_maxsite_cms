<?php
/**
 * Plugin Name: uLogin - виджет авторизации через социальные сети
 * Plugin URI:  https://ulogin.ru/
 * Description: uLogin — это инструмент, который позволяет пользователям получить единый доступ к различным
 * Интернет-сервисам без необходимости повторной регистрации, а владельцам сайтов — получить дополнительный
 * приток клиентов из социальных сетей и популярных порталов (Google, Яндекс, Mail.ru, ВКонтакте, Facebook и др.)
 * Version:     2.0.0
 * Author:      uLoginTeam
 * Author URI:  https://ulogin.ru/
 * License:     GPL2
 */

class ulogin {

	private $first_name;
	private $last_name;
	private $nickname;
	private $birthday;
	private $bdate;

	private $email;
	private $identity;
	private $network;

	private $photo;
	private $mergeaccount;
	private $verify;
	private $CI;

	public $soclink;
	public $notify;

	private $redirectCurPage;
	private $ComUsId;

	function __construct($u_user) {
		$options = mso_get_option('plugin_ulogin', 'plugins', array());
		$this->soclink = isset($options['soclink']) ? $options['soclink'] : 0;
//		$this->notify = isset($options['notify']) ? ($options['notify'] == '1' ? 'true' : 'false') : 0;
		$this->notify = true;
		$this->first_name = isset($u_user['first_name']) ? $u_user['first_name'] : '';
		$this->last_name = isset($u_user['last_name']) ? $u_user['last_name'] : '';
		$this->nickname = isset($u_user['nickname']) ? mso_clean_str($u_user['nickname'], 'base|not_url') : '';
		$this->bdate = isset($u_user['bdate']) ? $u_user['bdate'] : '';
		if(isset($u_user['photo_big'])) {
			$this->photo = $u_user['photo_big'] === "https://ulogin.ru/img/photo_big.png" ? '' : $u_user['photo_big'];
		} else {
			if(isset($u_user['photo'])) {
				$this->photo = ($u_user['photo'] === "https://ulogin.ru/img/photo.png") ? '' : $u_user['photo'];
			} else
				$this->photo = '';
		}
		$this->email = isset($u_user['email']) ? mso_clean_str($u_user['email'], 'email') : 0;
		$this->url = $this->soclink > 0 ? $u_user['profile'] : '';
		$this->identity = isset($u_user['identity']) ? $u_user['identity'] : '';
		$this->network = isset($u_user['network']) ? $u_user['network'] : '';
		$this->verify = isset($u_user['verified_email']) ? $u_user['verified_email'] : '';
		$this->mergeaccount = isset($u_user['merge_account']) ? $u_user['merge_account'] : 0;
		$this->CI =& get_instance();
		$this->ComUsId = 0;
		$this->redirectCurPage = mso_url_get();
		if($this->redirectCurPage == getinfo('site_url'))
			$this->redirectCurPage = false;
	}

	public function initAutorith() {
		$user_id = $this->getUserIdByIdentity($this->identity);
		if(isset($user_id) && !empty($user_id)) {
			$d = $this->getUserIdById($user_id);
			if($user_id > 0 && ($d['comusers_id'] > 0)) {
				$this->uloginCheckUserId($user_id);
			} else {
				$user_id = $this->uloginRegistrationUser(1);
			}
		} else $user_id = $this->uloginRegistrationUser();
		if($user_id > 0)
			$this->comuser_auth($user_id); else return false;

		return true;
	}

	private function comuser_auth($user_id) {
		$this->CI->db->select('comusers_id, comusers_password, comusers_email, comusers_nik, comusers_url, comusers_date_birth ,comusers_avatar_url, comusers_last_visit');
		$this->CI->db->where('comusers_id', $user_id);
		$query = $this->CI->db->get('comusers');
		if($query->num_rows() > 0) {
			$comuser_info = $query->row_array();
			if(!empty($this->photo) && empty($comuser_info['comusers_avatar_url'])) {
				$comuser_info['comusers_avatar_url'] = $this->_uploadAvatar($this->photo, $user_id);
			}
			$bdate = explode('-', $comuser_info['comusers_date_birth']);
			if(($bdate[0] == '0000') && ($bdate[1] == '00') && (!empty($this->bdate))) {
				$bdate = explode('.', $this->bdate);
				$comuser_info['comusers_date_birth'] = (isset($bdate[2]) ? $bdate[2] : '00') . '-' . (isset($bdate[1]) ? $bdate[1] : '00') . '-' . (isset($bdate[0]) ? $bdate[0] : '0000') . ' 00:00:00';
			}
			$this->CI->db->where('comusers_id', $comuser_info['comusers_id']);
			$this->CI->db->update('comusers', array('comusers_avatar_url' => $comuser_info['comusers_avatar_url'], 'comusers_last_visit' => date('Y-m-d H:i:s'), 'comusers_date_birth' => $comuser_info['comusers_date_birth']));
			$expire = time() + 60 * 60 * 24 * 365;
			$name_cookies = 'maxsite_comuser';
			$comuser_info = serialize($comuser_info);
			mso_add_to_cookie($name_cookies, $comuser_info, $expire, $this->redirectCurPage); // в куку для всего сайта
		}
	}

	private function uloginRegistrationUser($in_db = 0) {
		if(!isset($this->email)) {
			die(t('Через данную форму выполнить регистрацию невозможно. Сообщите администратору сайта о следующей ошибке:
            Необходимо указать "email" в возвращаемых полях uLogin') . $this->_get_back_url());
		} else {
			if(!mso_valid_email($this->email)) {
				die(t('Ошибка авторизации. Некорректный Email пользователя.') . $this->_get_back_url());
			}
		}
		// данные о пользователе есть в ulogintabel, но отсутствуют в comusers
		if($in_db == 1) {
			$this->CI->db->select('id');
			$this->CI->db->where('comusers_id', $this->ComUsId);
			$query = $this->CI->db->get('ulogintable');
			if($query->num_rows()) {
				$comuser_info = $query->row_array(1);
				$this->CI->db->where('id', $comuser_info['id']);
				$this->CI->db->delete('ulogintable');
			}
		}
		$user_id = $this->getUserByEmail($this->email);
		// $check_m_user == true -> есть пользователь с таким email
		$check_m_user = $user_id > 0 ? true : false;
		require_once(getinfo('common_dir') . 'comments.php');
		require_once(getinfo('common_dir') . 'common.php');
		$user = mso_get_comuser();
		$current_user = isset($user[0]['comusers_id']) ? $user[0]['comusers_id'] : 0;
		// $is_logged_in == true -> ползователь онлайн
		$is_logged_in = (!empty($current_user)) ? true : false;
		if(($check_m_user == false) && !$is_logged_in) {
			//регистрация нового пользователя
			if(!mso_get_option('allow_comment_comusers', 'general', '1')) {
				die(t('На сайте запрещена регистрация.') . $this->_get_back_url());
			}
			$pass = substr(mso_md5(mt_rand()), 0, 8);
			$ins_data['comusers_email'] = $this->email;
			$ins_data['comusers_password'] = mso_md5($pass);
			$ins_data['comusers_activate_key'] = mso_md5(rand());
			$ins_data['comusers_date_registr'] = date('Y-m-d H:i:s');
			$ins_data['comusers_last_visit'] = date('Y-m-d H:i:s');
			$ins_data['comusers_ip_register'] = $_SERVER['REMOTE_ADDR'];
			$ins_data['comusers_notify'] = '1';
			$ins_data['comusers_url'] = $this->url;
			if(isset($this->bdate)) {
				$this->birthday = $this->bdate;
				$this->birthday = explode('.', $this->bdate);
				$this->birthday = (isset($this->bdate[2]) ? $this->bdate[2] : '00') . '-' . (isset($this->bdate[1]) ? $this->bdate[1] : '00') . '-' . (isset($this->bdate[0]) ? $this->bdate[0] : '0000') . ' 00:00:00';
			}
			$ins_data['comusers_date_birth'] = isset($this->birthday) ? $this->birthday : '';
			if(empty($this->nickname)) {
				$this->nickname = $this->ulogin_generateNickname($this->first_name, $this->last_name, $this->nickname, $this->bdate);
			}
			$ins_data['comusers_nik'] = $this->nickname;
			$ins_data['comusers_activate_string'] = $ins_data['comusers_activate_key'];
			$res = ($this->CI->db->insert('comusers', $ins_data)) ? '1' : '0';
			if($res) {
				$this->ComUsId = $this->CI->db->insert_id();
				$this->insertUloginIfNotExists($this->ComUsId);
				//Отправка почты
				if($this->notify)
					mso_email_message_new_comuser($this->ComUsId, $ins_data, $this->notify);

				return $this->ComUsId;
			}
		} else {// существует пользователь с таким email или это текущий пользователь
			if(!isset($this->verify) || intval($this->verify) != 1) {
				$token = $_POST['token'];
				$uLogin_message = t("Электронный адрес данного аккаунта совпадает с электронным адресом существующего пользователя");
				$uLogin_message .= '<br/>' . t("Требуется подтверждение на владение указанным email.");
				$uLogin_message .= "<script src='//ulogin.ru/js/ulogin.js'  type='text/javascript'></script><script type='text/javascript'>uLogin.mergeAccounts('$token')</script>";
				die($uLogin_message . $this->_get_back_url());
			}
			if(intval($this->verify) == 1) {
				$user_id = $is_logged_in ? $current_user : $user_id;
				$other_u = $this->CI->db->query("SELECT identity FROM " . $this->CI->db->dbprefix . "ulogintable WHERE comusers_id='" . $user_id . "'");
				if($other_u->num_rows() > 0) {
					$other_u = $other_u->row_array();
					if($other_u['identity']) {
						if(!$is_logged_in && empty($this->mergeaccount)) {
							$token = urlencode($_POST['token']);
							$uLogin_message = t("С данным аккаунтом уже связаны данные из другой социальной сети.");
							$uLogin_message .= '<br/>' . t("Требуется привязка новой учётной записи социальной сети к этому аккаунту.");
							$uLogin_message .= "<script src='//ulogin.ru/js/ulogin.js'  type='text/javascript'></script><script type='text/javascript'>uLogin.mergeAccounts('$token','" . $other_u['identity'] . "')</script>";
							die($uLogin_message . $this->_get_back_url());
						}
					}
				}
				$this->ComUsId = $this->CI->db->insert_id();
				$this->insertUloginIfNotExists($user_id);

				return $user_id;
			}
		}

		return false;
	}

	private function insertUloginIfNotExists($comuser_id) {
		$this->CI->db->select('*');
		$this->CI->db->where('comusers_id', $comuser_id);
		$query = $this->CI->db->get('ulogintable');
		$result = $query->num_rows();
		if(isset($result)) {
			$this->CI->db->insert($this->CI->db->dbprefix . "ulogintable", array('comusers_id' => $comuser_id, 'identity' => $this->identity, 'network' => $this->network));
		}
	}

	/**
	 * Обменивает токен на пользовательские данные
	 * @param bool $token
	 * @return bool|mixed|string
	 */
	public static function uloginGetUserFromToken($token = false) {
		global $MSO;
		$response = false;
		if($token) {
			$data = array('cms' => 'maxsite', 'version' => $MSO->version);
			$request = 'http://ulogin.ru/token.php?token=' . $token . '&host=' . $_SERVER['HTTP_HOST'] . '&data=' . base64_encode(json_encode($data));
			if(in_array('curl', get_loaded_extensions())) {
				$c = curl_init($request);
				curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
				$response = curl_exec($c);
				curl_close($c);
			} elseif(function_exists('file_get_contents') && ini_get('allow_url_fopen'))
				$response = file_get_contents($request);
		}

		return $response;
	}

	/**
	 * Гнерация логина пользователя
	 * в случае успешного выполнения возвращает уникальный логин пользователя
	 * @param $first_name
	 * @param string $last_name
	 * @param string $nickname
	 * @param string $bdate
	 * @param array $delimiters
	 * @return string
	 */
	private function ulogin_generateNickname($first_name, $last_name = "", $nickname = "", $bdate = "", $delimiters = array('.', '_')) {
		$delim = array_shift($delimiters);
		$first_name = ulogin::ulogin_translitIt($first_name);
		$first_name_s = substr($first_name, 0, 1);
		$variants = array();
		if(!empty($nickname))
			$variants[] = $nickname;
		$variants[] = $first_name;
		if(!empty($last_name)) {
			$last_name = ulogin::ulogin_translitIt($last_name);
			$variants[] = $first_name . $delim . $last_name;
			$variants[] = $last_name . $delim . $first_name;
			$variants[] = $first_name_s . $delim . $last_name;
			$variants[] = $first_name_s . $last_name;
			$variants[] = $last_name . $delim . $first_name_s;
			$variants[] = $last_name . $first_name_s;
		}
		if(!empty($bdate)) {
			$date = explode('.', $bdate);
			for($i = 0, $l = 3; $i < $l; $i++) {
				$date[$i] = isset($date[$i]) ? $date[$i] : '00';
			}
			$variants[] = $first_name . $date[2];
			$variants[] = $first_name . $delim . $date[2];
			$variants[] = $first_name . $date[0] . $date[1];
			$variants[] = $first_name . $delim . $date[0] . $date[1];
			$variants[] = $first_name . $delim . $last_name . $date[2];
			$variants[] = $first_name . $delim . $last_name . $delim . $date[2];
			$variants[] = $first_name . $delim . $last_name . $date[0] . $date[1];
			$variants[] = $first_name . $delim . $last_name . $delim . $date[0] . $date[1];
			$variants[] = $last_name . $delim . $first_name . $date[2];
			$variants[] = $last_name . $delim . $first_name . $delim . $date[2];
			$variants[] = $last_name . $delim . $first_name . $date[0] . $date[1];
			$variants[] = $last_name . $delim . $first_name . $delim . $date[0] . $date[1];
			$variants[] = $first_name_s . $delim . $last_name . $date[2];
			$variants[] = $first_name_s . $delim . $last_name . $delim . $date[2];
			$variants[] = $first_name_s . $delim . $last_name . $date[0] . $date[1];
			$variants[] = $first_name_s . $delim . $last_name . $delim . $date[0] . $date[1];
			$variants[] = $last_name . $delim . $first_name_s . $date[2];
			$variants[] = $last_name . $delim . $first_name_s . $delim . $date[2];
			$variants[] = $last_name . $delim . $first_name_s . $date[0] . $date[1];
			$variants[] = $last_name . $delim . $first_name_s . $delim . $date[0] . $date[1];
			$variants[] = $first_name_s . $last_name . $date[2];
			$variants[] = $first_name_s . $last_name . $delim . $date[2];
			$variants[] = $first_name_s . $last_name . $date[0] . $date[1];
			$variants[] = $first_name_s . $last_name . $delim . $date[0] . $date[1];
			$variants[] = $last_name . $first_name_s . $date[2];
			$variants[] = $last_name . $first_name_s . $delim . $date[2];
			$variants[] = $last_name . $first_name_s . $date[0] . $date[1];
			$variants[] = $last_name . $first_name_s . $delim . $date[0] . $date[1];
		}
		$i = 0;
		$exist = true;
		while(true) {
			if($exist = ulogin::ulogin_userExist($variants[$i])) {
				foreach($delimiters as $del) {
					$replaced = str_replace($delim, $del, $variants[$i]);
					if($replaced !== $variants[$i]) {
						$variants[$i] = $replaced;
						if(!$exist = ulogin::ulogin_userExist($variants[$i]))
							break;
					}
				}
			}
			if($i >= count($variants) - 1 || !$exist)
				break;
			$i++;
		}
		if($exist) {
			while($exist) {
				$nickname = $first_name . mt_rand(1, 100000);
				$exist = ulogin::ulogin_userExist($nickname);
			}

			return $nickname;
		} else
			return $variants[$i];
	}

	/**
	 * Транслит
	 */
	private function ulogin_translitIt($str) {
		$tr = array("А" => "a", "Б" => "b", "В" => "v", "Г" => "g", "Д" => "d", "Е" => "e", "Ж" => "j", "З" => "z", "И" => "i", "Й" => "y", "К" => "k", "Л" => "l", "М" => "m", "Н" => "n", "О" => "o", "П" => "p", "Р" => "r", "С" => "s", "Т" => "t", "У" => "u", "Ф" => "f", "Х" => "h", "Ц" => "ts", "Ч" => "ch", "Ш" => "sh", "Щ" => "sch", "Ъ" => "", "Ы" => "yi", "Ь" => "", "Э" => "e", "Ю" => "yu", "Я" => "ya", "а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "j", "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l", "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r", "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h", "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "y", "ы" => "y", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya");
		if(preg_match('/[^A-Za-z0-9\_\-]/', $str)) {
			$str = strtr($str, $tr);
			$str = preg_replace('/[^A-Za-z0-9\_\-\.]/', '', $str);
		}

		return $str;
	}

	/**
	 * Проверка существует ли пользователь с заданным логином
	 */
	private function ulogin_userExist($login) {
		$this->CI->db->select('comusers_id');
		$this->CI->db->where('comusers_nik', $login);
		$query = $this->CI->db->get('comusers');
		if($query->num_rows() == 0) {
			return false;
		}

		return true;
	}

	/**
	 * Проверка пользовательских данных, полученных по токену
	 * @param $u_user - пользовательские данные
	 * @return bool
	 */
	public static function uloginCheckTokenError($u_user) {
		if(!is_array($u_user)) {
			die(t('Ошибка работы uLogin. Данные о пользователе содержат неверный формат') . ulogin::_get_back_url());
		}
		if(isset($u_user['error'])) {
			$strpos = strpos($u_user['error'], 'host is not');
			if($strpos) {
				die(t('Ошибка работы uLogin. Адрес хоста не совпадает с оригиналом') . ulogin::_get_back_url());
			}
			switch($u_user['error']) {
				case 'token expired':
					die(t('Ошибка работы uLogin. Время жизни токена истекло') . ulogin::_get_back_url());
				case 'invalid token':
					die(t('Ошибка работы uLogin. Неверный токен') . ulogin::_get_back_url());
				default:
					die(t('Ошибка работы uLogin. ') . $u_user['error'] . '.' . ulogin::_get_back_url());
			}
		}
		if(!isset($u_user['identity'])) {
			die(t('Ошибка работы uLogin. В возвращаемых данных отсутствует переменная
			 "identity"') . ulogin::_get_back_url());
		}

		return true;
	}

	private function getUserIdByIdentity($identity) {
		$this->CI->db->select('comusers_id');
		$this->CI->db->where('identity', $identity);
		$query = $this->CI->db->get('ulogintable');
		$result = $query->num_rows();
		if($result > 0) {
			$this->ComUsId = $query->row_array();
			$this->ComUsId = $this->ComUsId['comusers_id'];

			return $this->ComUsId;
		} else
			return false;
	}

	private function getUserIdById($comusers_id) {
		$this->CI->db->select('*');
		$this->CI->db->where('comusers_id', $comusers_id);
		$query = $this->CI->db->get('comusers');
		$result = $query->num_rows();
		if($result > 0)
			return $query->row_array(1); else
			return false;
	}

	/**
	 * @param $user_id
	 * @return bool
	 */
	private function uloginCheckUserId($user_id) {
		require_once(getinfo('common_dir') . 'comments.php');
		$current_user = mso_get_comuser();
		$current_user = isset($current_user[0]) ? $current_user[0]['comusers_id'] : 0;
		if(($current_user > 0) && ($user_id > 0) && ($current_user != $user_id)) {
			die(t('Данный аккаунт привязан к другому пользователю. Вы не можете использовать этот аккаунт.') . $this->_get_back_url());
		}

		return true;
	}

	private function getUserByEmail($email) {
		$this->CI->db->select('comusers_id');
		$this->CI->db->where('comusers_email', $email);
		$query = $this->CI->db->get('comusers');
		$result = $query->num_rows();
		if($result > 0) {
			$result = $query->row_array(1);

			return $result['comusers_id'];
		} else return false;
	}

	/**
	 * @param int $place - указывает, какую форму виджета необходимо выводить (0 - форма входа, 1 - форма
	 *     синхронизации). Значение по умолчанию = 0
	 * @return string(html)
	 */
	static public function getPanelCode($place = 0) {
		/*
		 * Выводит в форму html для генерации виджета
		 */
		$options = mso_get_option('plugin_ulogin', 'plugins', array());
		$ulogin_id1 = isset($options['ulogin_id1']) ? $options['ulogin_id1'] : '';
		$ulogin_id2 = isset($options['ulogin_id2']) ? $options['ulogin_id2'] : '';
		$page = mso_current_url();
		$current_url = getinfo('siteurl') . 'maxsite-ulogin-auth?' . getinfo('siteurl') . $page;
		$redirect_uri = urlencode($current_url);
		$ulogin_default_options = array();
		$ulogin_default_options['display'] = 'small';
		$ulogin_default_options['providers'] = 'vkontakte,odnoklassniki,mailru,facebook';
		$ulogin_default_options['fields'] = 'first_name,last_name,email,photo,photo_big';
		$ulogin_default_options['optional'] = 'sex,bdate,country,city';
		$ulogin_default_options['hidden'] = 'other';
		$ulogin_options = array();
		$ulogin_options['ulogin_id1'] = $ulogin_id1;
		$ulogin_options['ulogin_id2'] = $ulogin_id2;
		$default_panel = false;
		switch($place) {
			case 0:
				$ulogin_id = $ulogin_options['ulogin_id1'];
				break;
			case 1:
				$ulogin_id = $ulogin_options['ulogin_id2'];
				break;
			default:
				$ulogin_id = $ulogin_options['ulogin_id1'];
		}
		if(empty($ulogin_id)) {
			$ul_options = $ulogin_default_options;
			$default_panel = true;
		}
		$panel = '';
		$panel .= '<div class="ulogin_panel clearfix"';
		if($default_panel) {
			$ul_options['redirect_uri'] = $redirect_uri;
			$x_ulogin_params = '';
			foreach($ul_options as $key => $value)
				$x_ulogin_params .= $key . '=' . $value . ';';
			if($ul_options['display'] != 'window')
				$panel .= ' data-ulogin="' . $x_ulogin_params . '"></div>'; else
				$panel .= ' data-ulogin="' . $x_ulogin_params . '" href="#"><img src="https://ulogin.ru/img/button.png" width=187 height=30 alt="МультиВход"/></div>';
		} else
			$panel .= ' data-uloginid="' . $ulogin_id . '" data-ulogin="redirect_uri=' . $redirect_uri . '"></div>';
		$panel = '<div class="ulogin_block ulogin_place' . $place . '">' . $panel . '</div>';

		return $panel;
	}

	/**
	 * Вывод списка аккаунтов пользователя
	 * @param int $user_id - ID пользователя (значение по умолчанию = текущий пользователь)
	 * @return string
	 */
	static function getuloginUserAccountsPanel($user_id = 0) {
		require_once(getinfo('common_dir') . 'common.php');
		$current_user = mso_get_comuser(mso_segment(2));
		$current_user = isset($current_user[0]) ? $current_user[0]['comusers_id'] : 0;
		$user_id = empty($user_id) ? $current_user : $user_id;
		if(empty($user_id))
			return '';
		$DB =& get_instance();
		$networks = $DB->db->query("SELECT * FROM " . $DB->db->dbprefix . "ulogintable WHERE comusers_id='" . $user_id . "'");
		$networks = $networks->result_array();
		$output = '
			<style>
			    .big_provider {
			        display: inline-block;
			        margin-right: 10px;
			    }
			</style>';
		if($networks) {
			$output .= '<div id="ulogin_accounts">';
			foreach($networks as $network) {
				if($network['comusers_id'] = $user_id)
					$output .= "<div data-ulogin-network='{$network['network']}'  data-ulogin-identity='{$network['identity']}' class='ulogin_network big_provider {$network['network']}_big'></div>";
			}
			$output .= '</div>';

			return $output;
		}

		return '';
	}

	public static function _uploadAvatar($url, $uid) {
		//загрузка фотографии на сайт
		if(!empty($url)) {
			$uid = abs(intval($uid));
			$dir = getinfo('uploads_dir') . 'ulogin_avatar/';
			if(!file_exists($dir)) {
				mkdir($dir, 0777);
			}
			$sc = stream_context_create();
			$imagedump = file_get_contents($url, FILE_BINARY, $sc);
			$tmpfname = $dir . $uid . '_avatar.jpg';
			$fh = fopen($tmpfname, "w");
			fwrite($fh, $imagedump);
			if(file_exists($tmpfname))
				copy($url, $tmpfname);
			fclose($fh);
			//уменьшение фотографии
			list($w_real, $h_real, $type) = getimagesize($tmpfname);
			$types = array("", "gif", "jpeg", "png");
			$ext = $types[$type];
			if($ext) {
				$func = 'imagecreatefrom' . $ext;
				$img_i = $func($tmpfname); // для работы с исходным изображением
			} else {
				//некорректное изображение
				return '';
			}
			//получаем отношение реальных сторон к сторонам 100*100px
			$x_r = 100 / $w_real;
			$y_r = 100 / $h_real;
			//определяем наибольшее отношение
			$ratio = max($x_r, $y_r);
			$use_x_ratio = ($x_r == $ratio);
			$new_width = !$use_x_ratio ? 100 : floor($w_real * $ratio);
			$new_height = $use_x_ratio ? 100 : floor($h_real * $ratio);
			$idest = imagecreatetruecolor($new_width, $new_height);
			imagecopyresampled($idest, $img_i, 0, 0, 0, 0, $new_width, $new_height, $w_real, $w_real);
			imagejpeg($idest, $tmpfname, 100);
			imagedestroy($img_i);
			imagedestroy($idest);
			//обрезание фотографии из 100*100 в 80*80px чтобы было минимум потерь для больших фотографий
			$img_i = $func($tmpfname);
			$img_o = imagecreatetruecolor(80, 80); // для выходного изображения
			imagecopy($img_o, $img_i, 0, 0, 10, 10, 80, 80); // копируем изображение 80*80px
			$func = 'image' . $ext; // сохранение результата
			$func($img_o, $tmpfname); // сохраняем изображение в исходный файл
			return $dir = getinfo('uploads_url') . 'ulogin_avatar/' . $uid . '_avatar.jpg';
		}

		return '';
	}

	/**
	 * Возвращает Back url в html формате
	 */
	function _get_back_url() {
		return '<br/><a href="' . (isset($this->redirectCurPage) ? $this->redirectCurPage : '') . '">' . 'Назад' . '</a>';
	}
}