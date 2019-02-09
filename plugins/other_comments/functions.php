<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// проверим в таблице сущностей наличие комментируемой сущности
// если нет то создадим
// в результате будет добавлен эл-т массива $element['id']
//  - номер записи о комментируемой сущности в таблице Elements
function other_comments_get_element($element)
{
  $CI = get_instance();

  $element_id = false;
  $element_title = '';

  if (!isset($element['slug'])) $element['slug'] = '';
  if (!isset($element['kind'])) $element['kind'] = '';
  if (!isset($element['element_id_in_table'])) $element['element_id_in_table'] = '';
  if (!isset($element['table_name'])) $element['table_name'] = '';
  if (!isset($element['title'])) $element['title'] = '';
  if (!isset($element['comment_allow'])) $element['comment_allow'] = 1;

  if ( $element['element_id_in_table'] and $element['table_name'] )
  {
	   $CI->db->select('element_id , element_title');
	   $CI->db->where(array (
				'element_id_in_table' => $element['element_id_in_table'],
				'element_table_name' => $element['table_name'],
	      ));

	   $query = $CI->db->get('elements');
	   if ($query->num_rows()) // есть такая сущность
	   {
         $row = $query->row_array(1);
         $element_id = $row['element_id'];
         $element_title = $row['element_title'];
	   }
	}
  elseif ( $element['slug'] and $element['kind'] )
  {
	   $CI->db->select('element_id , element_title');
	   $CI->db->where(array (
				'element_slug' => $element['slug'],
				'kinds.kind_slug' => $element['kind'],
	      ));
	   $CI->db->join('kinds', 'kinds.kind_id = elements.element_kind_id');

	   $query = $CI->db->get('elements');
	   if ($query->num_rows()) // есть такая сущность
	   {
         $row = $query->row_array(1);
         $element_id = $row['element_id'];
         $element_title = $row['element_title'];
	   }
	}
	
	
	if (!$element_id) // если нет сущности, создадим запись о сущности		
	{
	    $kind_id = 0;
	    if ($element['kind'])   
	    {
	       // сперва получим номер вида сущности для слуга вида сущности
	      $CI->db->select('kind_id');
	      $CI->db->where('kind_slug' , $element['kind']);
	      if ($query = $CI->db->get('kinds'))
	        if ($query->num_rows()) // есть такой вид сущностей
	        {
             $row = $query->row_array(1);
             $kind_id = $row['kind_id'];
	        }	   
		  }
		  
		    $ins_data = array (
					'element_slug' => $element['slug'],
					'element_kind_id' => $kind_id,
					'element_title' => $element['title'],
					'element_id_in_table' => $element['element_id_in_table'],
					'element_table_name' => $element['table_name'],
					'element_comment_allow' => $element['comment_allow']
					  );		  
		  
		    
	   // если приыязываем по id 
	   if ($element['element_id_in_table'] and $element['table_name'])
	   {
				$res = ($CI->db->insert('elements', $ins_data)) ? '1' : '0';
				if ($res) $element_id = $CI->db->insert_id(); // номер добавленной сущности
				else $element_id = false; // возвращаем без ID 
		 }
		 //если привязываемся по slug
	   elseif ($element['kind'] and $element['slug'] and $kind_id)
	   {
				$res = ($CI->db->insert('elements', $ins_data)) ? '1' : '0';
				if ($res) $element_id = $CI->db->insert_id(); // номер добавленной сущности
				else $element_id = false; // возвращаем без ID 	   
		 }		 
  }
   //если сущность есть - проверим ее title и обновим если нужно
  else
  {
     if ($element_title != $element['title'])
     // обновим в БД
     {
		    $upd_data = array (
					'element_title' => $element['title'],
					  );	  
	      $CI->db->where('element_id' , $element_id);
				$res = ($CI->db->update('elements', $upd_data)) ? '1' : '0';	    
     }
  }
  
   $element['id'] = $element_id;
   return $element;
}


# функция получения комментариев
function other_comments_get_comments($element , $args=array())
{

		global $MSO;

		if ( !isset($args['limit']) )	$args['limit'] = false;
		if ( !isset($args['order']) )	$args['order'] = 'asc';
		if ( !isset($args['tags']) )	$args['tags'] = '<p><img><strong><em><i><b><u><s><font><pre><code><blockquote>';
		if ( !isset($args['tags_users']) )	$args['tags_users'] = '<a><p><img><strong><em><i><b><u><s><font><pre><code><blockquote>';
		if ( !isset($args['tags_comusers']) )	$args['tags_comusers'] = '<a><p><img><strong><em><i><b><u><s><font><pre><code><blockquote>';
		if ( !isset($args['anonim_comments']) )	$args['anonim_comments'] = array();
		if ( !isset($args['anonim_title']) )	$args['anonim_title'] = '';// ' ('. t('анонимно'). ')'; // дописка к имени для анонимов
		if ( !isset($args['anonim_no_name']) )	$args['anonim_no_name'] = t('Аноним', 'template');// Если не указано имя анонима
		
		// если аноним указывает имя с @, то это страница в твиттере - делаем ссылку
		if ( !isset($args['anonim_twitter']) )	$args['anonim_twitter'] = true; 

		// дописка к имени для комментаторов без ника
		if ( !isset($args['add_author_name']) )	$args['add_author_name'] = t('Комментатор');


		$CI = get_instance();
		
		// получим список всех комюзеров, где посдчитается количество их комментариев
		$all_comusers = mso_comuser_update_count_comment();

		$CI->db->select('elements.element_id, elements.element_slug, elements.element_title, 
		kinds.kind_slug, kinds.kind_title,
		other_comments.*,
		
		users.users_id, 
		users.users_nik,
		users.users_count_comments,
		users.users_url,
		users.users_email,
		users.users_avatar_url,
		
		comusers.comusers_id, 
		comusers.comusers_nik,
		comusers.comusers_url,
		comusers.comusers_count_comments,
		comusers.comusers_allow_publish,
		comusers.comusers_email,
		comusers.comusers_avatar_url
		');

		if ($element['id']) $CI->db->where('elements.element_id', $element['id']);
		
		// если нет анономого коммента, то вводим условие на comments_approved=1 - только разрешенные
		if (!$args['anonim_comments'])
		{
			$CI->db->where('other_comments.comments_approved', '1');
		}
		else // есть массив с указанными комментариям - они выводятся отдельно
		{
			$CI->db->where('other_comments.comments_approved', '0');
			$CI->db->where_in('other_comments.comments_id', $args['anonim_comments']);
		}

		// вот эти два join жутко валят мускуль...
		// пока решение не найдено, все запросы к комментам следует кэшировать на уровне плагина
		$CI->db->join('users', 'users.users_id = other_comments.comments_users_id', 'left');
		$CI->db->join('comusers', 'comusers.comusers_id = other_comments.comments_comusers_id', 'left');
		$CI->db->join('kinds', 'kinds.kind_id = elements.element_kind_id', 'left');
		
		// вручную делаем этот where, потому что придурочный CodeIgniter его неверно экранирует
		$CI->db->where($CI->db->dbprefix . 'elements.element_id', $CI->db->dbprefix . 'other_comments.comments_element_id', false);
		
		//$CI->db->where('page.page_status', 'publish');
		
		$CI->db->order_by('other_comments.comments_date', $args['order']);
		
		if ($args['limit']) $CI->db->limit($args['limit']);
		
		$CI->db->from('other_comments, elements');
		
		//pr(_sql());

		$query = $CI->db->get();

		//return array();


		if ($query->num_rows() > 0)
		{
			$comments = $query->result_array();
			//pr($comments);
			foreach ($comments as $key=>$comment)
			{
				//pr($comment);

				$commentator = 3; // комментатор: 1-комюзер 2-автор 3-аноним
				

				if ($comment['comusers_id']) // это комюзер
				{
					if ($comment['comusers_nik']) $comment['comments_author_name'] = $comment['comusers_nik'];
					else $comment['comments_author_name'] = $args['add_author_name'] . ' ' . $comment['comusers_id'];
					$comment['comments_url'] = '<a href="' . getinfo('siteurl') . 'users/' . $comment['comusers_id'] . '">'
							. $comment['comments_author_name'] . '</a>';
					$commentator = 1;

					if (isset($all_comusers[$comment['comusers_id']]))
						$comments[$key]['comusers_count_comments'] = $all_comusers[$comment['comusers_id']];

				}
				elseif ($comment['users_id']) // это автор
				{
					if ($comment['users_url'])
							$comment['comments_url'] = '<a href="' . $comment['users_url'] . '">' . $comment['users_nik'] . '</a>';
						else $comment['comments_url'] = $comment['users_nik'];
					$commentator = 2;
				}
				else // просто аноним
				{
					if (!$comment['comments_author_name']) $comment['comments_author_name'] = $args['anonim_no_name'];
					if ($args['anonim_twitter']) // разрешено проверять это твиттер-логин?
					{
						
						if (strpos($comment['comments_author_name'], '@') === 0) // первый символ @
						{	
							$lt = substr($comment['comments_author_name'], 1); // вычленим @
							
							// проверим корректность логина
							if ($lt == mso_slug($lt))
								$comment['comments_url'] = '<a href="http://twitter.com/' . $lt . '" rel="nofollow">@' . $lt . '</a>';
							else
								$comment['comments_url'] = $comment['comments_author_name'] . $args['anonim_title']; 
						}
						else $comment['comments_url'] = $comment['comments_author_name'] . $args['anonim_title']; 
					}
					else
					{
						$comment['comments_url'] = $comment['comments_author_name'] . $args['anonim_title']; 
					}
				}


				$comments_content = $comment['comments_content'];
				
				// защитим pre
				$t = $comments_content;
				$t = str_replace('&lt;/pre>', '</pre>', $t); // проставим pre - исправление ошибки CodeIgniter
				
				$t = preg_replace_callback('!<pre>(.*?)</pre>!is', 'mso_clean_html_do', $t);

				if ($commentator==1) $t = strip_tags($t, $args['tags_comusers']);
				elseif ($commentator==2) $t = strip_tags($t, $args['tags_users']);
				else $t = strip_tags($t, $args['tags']);
				
				$t = mso_xss_clean($t);

				$t = str_replace('[html_base64]', '<pre>[html_base64]', $t); // проставим pre
				$t = str_replace('[/html_base64]', '[/html_base64]</pre>', $t);
				
				// обратная замена
				$t = preg_replace_callback('!\[html_base64\](.*?)\[\/html_base64\]!is', 'mso_clean_html_posle', $t);
				
				$comments_content = $t; // сохраним как текст комментария
				
				$comments_content = mso_hook('comments_content', $comments_content);
				
				$comments_content = str_replace("\n", "<br>", $comments_content);
		
				$comments_content = str_replace('<p>', '&lt;p&gt;', $comments_content);
				$comments_content = str_replace('</p>', '&lt;/p&gt;', $comments_content);
				$comments_content = str_replace('<P>', '&lt;P&gt;', $comments_content);
				$comments_content = str_replace('</P>', '&lt;/P&gt;', $comments_content);
				
				
				if (mso_hook_present('comments_content_custom'))
				{
					$comments_content = mso_hook('comments_content_custom', $comments_content);
				}
				else
				{
					$comments_content = mso_auto_tag($comments_content, true);
					$comments_content = mso_hook('content_balance_tags', $comments_content);
				}
				
				$comments_content = mso_hook('comments_content_out', $comments_content);

				$comments[$key]['comments_content'] = $comments_content;
				$comments[$key]['comments_url'] = $comment['comments_url'];

			}
		}
		else
			$comments = array();

		return $comments;
	
	} 
	
	# функция добавляет новый коммент и выводит сообщение о результате
 	function other_comments_get_new_comment($element , $args=array())
	{
		global $MSO;

		if ( $post = mso_check_post(array('other_comments_session', 'other_comments_submit', 'other_comments_element_id', 'comments_content')) )
		{
			// mso_checkreferer(); // если нужно проверять на реферер
			$CI = get_instance();
			
			// заголовок страницы
			if ( !isset($element['title']) )		$element['title'] = '';
			
			// стили
			if ( !isset($args['css_ok']) )		$args['css_ok'] = 'comment-ok';
			if ( !isset($args['css_error']) )	$args['css_error'] = 'comment-error';
			
			// разрешенные тэги
			if ( !isset($args['tags']) )		$args['tags'] = '<p><blockquote><br><span><strong><strong><em><i><b><u><s><pre><code>';
			
			// обрабатывать текст на xss-атаку
			if ( !isset($args['xss_clean']) )		$args['xss_clean'] = true;
			
			// если найдена xss-атака, то не публиковать комментарий
			if ( !isset($args['xss_clean_die']) )		$args['xss_clean_die'] = false;
			
			if ( !isset($args['noword']) )		$args['noword'] = array('.com', '.ru', '.net', '.org', '.info', '.ua', 
																		'.su', '.name', '/', 'www.', 'http', ':', '-', '"',
																		'«', '»', '%', '<', '>', '&', '*', '+', '\'' );
			
			mso_hook('add_new_comment');


			if (!mso_checksession($post['other_comments_session']) )
				return '<div class="' . $args['css_error']. '">'. t('Ошибка сессии! Обновите страницу'). '</div>';

			if (!$post['other_comments_element_id']) return '<div class="' . $args['css_error']. '">'. t('Ошибка!'). '</div>';


			$comments_element_id = $post['other_comments_element_id'];
			$id = (int) $comments_element_id;
			if ( (string) $comments_element_id != (string) $id ) $id = false; // $comments_element_id не число
			if (!$id) return '<div class="' . $args['css_error']. '">'. t('Ошибка!'). '</div>';
			if ($element['id'] != $id) return '<div class="' . $args['css_error']. '">'. t('Ошибка!'). '</div>';

			// капчу проверим
			// если этот хук возвращает false, значит капча неверная
			if (!mso_hook('comments_new_captcha', true))
			{	
				// если определен хук на неверную капчу, отдаем его
				if (mso_hook_present('comments_new_captcha_error'))
				{
					return mso_hook('comments_new_captcha_error');
				}
				else
				{
					return '<div class="' . $args['css_error']. '">'. t('Ошибка! Неверно введены нижние символы!'). '</div>';
				}
			}
			
			// вычищаем от запрещенных тэгов
			if ($args['tags']) 
			{
				// перед этим нужно все pre защитить
				$t = $post['other_comments_content'];
				
				$t = preg_replace_callback('!<pre>(.*?)</pre>!is', 'mso_clean_html_do', $t);
				
				$t = strip_tags($t, $args['tags']); // теперь оставим только разрешенные тэги
				
				$t = str_replace('[html_base64]', '<pre>[html_base64]', $t); // проставим pre
				$t = str_replace('[/html_base64]', '[/html_base64]</pre>', $t);
				
				// обратная замена
				$t = preg_replace_callback('!\[html_base64\](.*?)\[\/html_base64\]!is', 'mso_clean_html_posle', $t);
				
				$post['other_comments_content'] = $t; // сохраним как текст комментария
			}
			
			// если указано рубить коммент при обнаруженной xss-атаке 
			if ($args['xss_clean_die'] and $mso_xss_clean($post['other_comments_content'], true, false) === true)
			{
				return '<div class="' . $args['css_error']. '">'. t('Обнаружена XSS-атака!'). '</div>';
			}
				
			if (!trim($post['comments_content'])) 
				return '<div class="' . $args['css_error']. '">'. t('Ошибка, нет текста!'). '</div>';

			// возможно есть текст, но только из одних html - не пускаем
			if ( !trim(strip_tags(trim($post['comments_content']))) )
				return '<div class="' . $args['css_error']. '">'. t('Ошибка, нет полезного текста!'). '</div>';
			
			// вычищаем текст от xss
			if ($args['xss_clean'])
			{
				$post['other_comments_content'] =  mso_xss_clean($post['comments_content']);
				// проставим pre исправление ошибки CodeIgniter
				$post['other_comments_content'] = str_replace('&lt;/pre>', '</pre>', $post['comments_content']); 
			}	
			
			$comments_author_ip = $_SERVER['REMOTE_ADDR'];
			$comments_date = date('Y-m-d H:i:s');

			$comments_content = mso_hook('new_comments_content', $post['comments_content']);

			// есть дли родитель у комментария
			$comments_parent_id = isset($post['comments_parent_id']) ? $post['comments_parent_id'] : '0'; 
			
			// провека на спам - проверим через хук new_comments_check_spam
			$comments_check_spam = mso_hook('new_comments_check_spam',
											array(
												'comments_content' => $comments_content,
												'comments_date' => $comments_date,
												'comments_author_ip' => $comments_author_ip,
												'comments_page_id' => $comments_element_id,
												'comments_server' => $_SERVER,
												'comments_parent_id' => $comments_parent_id,
											), false);

			// если есть спам, то возвращается что-то отличное от comments_content
			// если спама нет, то должно вернуться false
			// если есть подозрения, то возвращается массив с moderation (comments_approved)
			// если есть параметр check_spam=true, значит определен спам и он вообще не пускается
			// сообщение для вывода в парметре 'message'

			// разрешение антиспама moderation
			// -1 - не определено, 0 - можно разрешить, 1 - отдать на модерацию
			$moderation = -1;

			if ($comments_check_spam)
			{
				if (isset($comments_check_spam['check_spam']) and $comments_check_spam['check_spam']==true)
				{
					if ( isset($comments_check_spam['message']) and $comments_check_spam['message'] )
						return '<div class="' . $args['css_error']. '">' . $comments_check_spam['message'] . '</div>';
					else
						return '<div class="' . $args['css_error']. '">'. t('Ваш комментарий определен как спам и удален.'). '</div>';
				}
				else
				{
					// спам не определен, но возможно стоит moderation - принудительная модерация
					if (isset($comments_check_spam['moderation'])) $moderation = $comments_check_spam['moderation'];
				}
			}

					

			// проверим есть ли уже такой комментарий
			// проверка по ip и тексту
			$CI->db->select('comments_id');
			$CI->db->where(array (
				'comments_element_id' => $comments_element_id,
				'comments_author_ip' => $comments_author_ip,
				'comments_content' => $comments_content,
				));

			$query = $CI->db->get('other_comments');
			if ($query->num_rows()) // есть такой коммент
			{
				return '<div class="' . $args['css_error']. '">'. t('Похоже, вы уже отправили этот комментарий...'). '</div>';
			}
		
			
			if (is_login()) // коммент от автора
			{
				$comments_users_id = $MSO->data['session']['users_id'];

				$ins_data = array (
					'comments_users_id' => $comments_users_id,
					'comments_element_id' => $comments_element_id,
					'comments_author_ip' => $comments_author_ip,
					'comments_date' => $comments_date,
					'comments_content' => $comments_content,
					'comments_parent_id' => $comments_parent_id,
					'comments_approved' => 1 // авторы могут сразу публиковать комменты без модерации
					);

				$res = ($CI->db->insert('other_comments', $ins_data)) ? '1' : '0';

				if ($res)
				{
					mso_email_message_new_comment($CI->db->insert_id(), $ins_data, $element['title']);
					mso_flush_cache();
					$CI->db->cache_delete_all();
					mso_hook('new_comment');
					mso_redirect(mso_current_url() . '#comment-' . $CI->db->insert_id());
				}
				else
					return '<div class="' . $args['css_error']. '">'. t('Ошибка добавления комментария'). '</div>';
			}
			else
			{
				if ( isset($post['comments_reg']) ) // комюзер или аноном
				{
					if ($post['comments_reg'] == 'reg') // нужно зарегистрировать или уже есть регистрация
					{
						
						// проверим есть ли разршение на комментарии от комюзеров
						// для случаев подделки post-запроса
						if ( !mso_get_option('allow_comment_comusers', 'general', '1') )
							return '<div class="' . $args['css_error']. '">'. t('Error allow_comment_comusers'). '</div>';
							

						if ( !isset($post['comments_email']) or !$post['comments_email'] )
							return '<div class="' . $args['css_error']. '">'. t('Нужно указать Email'). '</div>';

						if ( !isset($post['comments_password']) or !$post['comments_password'] )
							return '<div class="' . $args['css_error']. '">'. t('Нужно указать пароль'). '</div>';

						$comments_email = mso_strip($post['comments_email']);
						$comments_password = mso_strip($post['comments_password']);

						if ( !mso_valid_email($comments_email) )
							return '<div class="' . $args['css_error']. '">'. t('Ошибочный Email'). '</div>';

						// вначале нужно зарегистрировать comюзера - получить его id и только после этого добавить сам коммент
						// но вначале есть смысл проверить есть ли такой ком-пользователь

						$comusers_id = false;

						$CI->db->select('comusers_id, comusers_password');
						$CI->db->where('comusers_email', $comments_email);
						$query = $CI->db->get('comusers');
						if ($query->num_rows()) // есть такой комюзер
						{
							$row = $query->row_array(1);

							// пароль не нужно шифровать mso_md5
							if (isset($post['comments_password_md']) and $post['comments_password_md'])
							{
								if ($row['comusers_password'] != $comments_password) // пароль неверный
									return '<div class="' . $args['css_error']. '">'. t('Неверный пароль'). '</div>';
							}
							else
							{
								if ($row['comusers_password'] != mso_md5($comments_password)) // пароль неверный
									return '<div class="' . $args['css_error']. '">'. t('Неверный пароль'). '</div>';
							}

							$comusers_id = $row['comusers_id']; // получаем номер комюзера
						}
						else
						{
							// такого комюзера нет
						
						  $comusers_url =  mso_xss_clean($post['comusers_url']);
		          if ($comusers_url and strpos($comusers_url, 'http://') === false) 
			        $comusers_url = 'http://' . $comusers_url;
						  $comusers_nik =  mso_xss_clean($post['comusers_nik']);
							$ins_data = array (
								'comusers_email' => $comments_email,
								'comusers_password' => mso_md5($comments_password),
								'comusers_url' => $comusers_url,
								'comusers_nik' => $comusers_nik,
								);

							// генерируем случайный ключ активации
							$ins_data['comusers_activate_key'] = mso_md5(rand());
							$ins_data['comusers_date_registr'] = date('Y-m-d H:i:s');
							$ins_data['comusers_last_visit'] = date('Y-m-d H:i:s');
							$ins_data['comusers_ip_register'] = $_SERVER['REMOTE_ADDR'];
							$ins_data['comusers_notify'] = '1'; // сразу включаем подписку на уведомления
							
							// Автоматическая активация новых комюзеров
							// если активация стоит автоматом, то сразу её и прописываем
							if ( mso_get_option('comusers_activate_auto', 'general', '0') )
								$ins_data['comusers_activate_string'] = $ins_data['comusers_activate_key'];

							$res = ($CI->db->insert('comusers', $ins_data)) ? '1' : '0';

							if ($res)
							{
								$comusers_id = $CI->db->insert_id(); // номер добавленной записи

								// нужно добавить опцию в мета «новые комментарии, где я участвую» subscribe_my_comments
								// вначале грохаем если есть такой ключ
								$CI->db->where('meta_table', 'comusers');
								$CI->db->where('meta_id_obj', $comusers_id);
								$CI->db->where('meta_key', 'subscribe_my_comments');
								$CI->db->delete('meta');
								
								// теперь добавляем как новый
								$ins_data2 = array(
										'meta_table' => 'comusers',
										'meta_id_obj' => $comusers_id,
										'meta_key' => 'subscribe_my_comments',
										'meta_value' => '1'
										);
								
								$CI->db->insert('meta', $ins_data2);
						
								// почему CodeIgniter не может так?
								// INSERT INTO table SET column = 1, id=1 ON DUPLICATE KEY UPDATE column = 2
								
								
								// отправляем ему уведомление с кодом активации
								mso_email_message_new_comuser($comusers_id, $ins_data, mso_get_option('comusers_activate_auto', 'general', '0')); 
							}
							else
								return '<div class="' . $args['css_error']. '">'. t('Ошибка регистрации'). '</div>';
						}

						if ($comusers_id)
						{
							// Модерация комюзеров 1 - модерировать
							$comments_com_approved = mso_get_option('new_comment_comuser_moderate', 'general', 1);

							// если включена модерация комюзеров
							// и включена опция только первого комментария
							// то получаем кол-во комментариев комюзера
							if ($comments_com_approved and mso_get_option('new_comment_comuser_moderate_first_comment', 'general', 0)) 
							{
								$all_comusers = mso_comuser_update_count_comment(); // список комюзер => колво комментов
								
								// есть такой комюзер и у него более 1 комментария
								if (isset($all_comusers[$comusers_id]) and $all_comusers[$comusers_id] > 0)
									$comments_com_approved = 0; // разрешаем публикацию
							}
							
							// но у нас в базе хранится значение наоборот - 1 разрешить 0 - запретить
							$comments_com_approved = !$comments_com_approved;
							
							if ($moderation == 1) $comments_com_approved = 0; // антиспам определил, что нужно премодерировать

							if ($comments_com_approved == 1) // если разрешено
							{
								$comments_com_approved = mso_hook('new_comments_check_spam_comusers',
												array(
													'comments_element_id' => $comments_element_id,
													'comments_comusers_id' => $comusers_id,
													'comments_com_approved' => $comments_com_approved,
												), 1);
							}


							// комюзер добавлен или есть
							// теперь сам коммент
							$ins_data = array (
								'comments_element_id' => $comments_element_id,
								'comments_comusers_id' => $comusers_id,
								'comments_author_ip' => $comments_author_ip,
								'comments_date' => $comments_date,
								'comments_content' => $comments_content,
								'comments_approved' => $comments_com_approved,
								'comments_parent_id' => $comments_parent_id,
								);

							$res = ($CI->db->insert('other_comments', $ins_data)) ? '1' : '0';
							if ($res)
							{
								
								$id_comment_new = $CI->db->insert_id();
								
								// посколько у нас идет редирект, то данные об отправленном комменте
								// сохраняем в сессии номер комментария
								if ( isset($MSO->data['session']) )
								{
									$CI->session->set_userdata(array( 'other_comments' =>
														array(
														// $CI->db->insert_id()=>$comments_page_id
														$id_comment_new
														)));
								}
								mso_email_message_new_comment($id_comment_new, $ins_data, $element['title']);
								// mso_flush_cache();
								$CI->db->cache_delete_all();
								mso_hook('new_comment');
								
								
								
								
								# если комюзер не залогинен, то сразу логиним его
								
								$CI->db->select('comusers_id, comusers_password, comusers_email, 
										comusers_nik, comusers_url, comusers_avatar_url, comusers_last_visit');
								$CI->db->where('comusers_email', $comments_email);
								$CI->db->where('comusers_password', mso_md5($comments_password));
								$query = $CI->db->get('comusers');
								
								if ($query->num_rows()) // есть такой комюзер
								{
									$comuser_info = $query->row_array(1); // вся инфа о комюзере
									
									// сразу же обновим поле последнего входа
									$CI->db->where('comusers_id', $comuser_info['comusers_id']);
									$CI->db->update('comusers', array('comusers_last_visit'=>date('Y-m-d H:i:s')));
									
									$expire  = time() + 60 * 60 * 24 * 30; // 30 дней = 2592000 секунд
									
									$name_cookies = 'maxsite_comuser';
									$value = serialize($comuser_info); 
									
									# ставим куку и редиректимся автоматом
									mso_add_to_cookie($name_cookies, $value, $expire, 
												mso_current_url(true) . '#comment-' . $id_comment_new);
									exit;
								}
								
								
								
								
								mso_redirect(mso_current_url() . '#comment-' . $id_comment_new);
							}
							else
								return '<div class="' . $args['css_error']. '">'. t('Ошибка добавления комментария'). '</div>';
						}
					}
					elseif  ($post['comments_reg'] == 'noreg')
					{
						// комментарий от анонима
						
						// проверим есть ли разрешение на комментарии от анонимов
						// для случаев подделки post-запроса
						if ( !mso_get_option('allow_comment_anonim', 'general', '1') )
							return '<div class="' . $args['css_error']. '">'. t('Error allow_comment_anonim'). '</div>';

						if ( isset($post['comments_author']) )
						{
							$comments_author_name = mso_strip($post['comments_author']);
							$comments_author_name = str_replace($args['noword'], '', $comments_author_name);
							$comments_author_name = trim($comments_author_name);
							if (!$comments_author_name) $comments_author_name = t('Аноним');
						}
						else $comments_author_name = 'Аноним';

						// можно ли публиковать без модерации?
						$comments_approved = mso_get_option('new_comment_anonim_moderate', 'general', 1);

						// но у нас в базе хранится значение наоборот - 1 разрешить 0 - запретить
						$comments_approved = !$comments_approved;

						if ($moderation==1) $comments_approved = 0; // антиспам определил, что нужно премодерировать

						$ins_data = array (
							'comments_element_id' => $comments_element_id,
							'comments_author_name' => $comments_author_name,
							'comments_author_ip' => $comments_author_ip,
							'comments_date' => $comments_date,
							'comments_content' => $comments_content,
							'comments_approved' => $comments_approved,
							'comments_parent_id' => $comments_parent_id,
							);

						$res = ($CI->db->insert('other_comments', $ins_data)) ? '1' : '0';
						if ($res)
						{
              $id_comment_new = $CI->db->insert_id();

							// посколько у нас идет редирект, то данные об отправленном комменте
							// сохраняем в сессии номер комментария
							if ( isset($MSO->data['session']) )
							{
								$CI->session->set_userdata(array( 'other_comments' =>
													array(
													  $id_comment_new
													)));
							}
							mso_email_message_new_comment($CI->db->insert_id(), $ins_data, $element['title']);
							// mso_flush_cache();
							$CI->db->cache_delete_all();
							mso_hook('new_comment');
							mso_redirect(mso_current_url() . '#comment-' . $CI->db->insert_id());
						}
						else
							return '<div class="' . $args['css_error']. '">'. t('Ошибка добавления комментария'). '</div>';
					}
				}
			}
		}
		// else return '<div class="comment-new">Комментарий добавлен и возможно ожидает модерации.</div>';
	}




// синхронизация количества комментариев у комюзеров
// mso_comuser_update_count_comment();
// mso_email_message_new_comment_subscribe
				
?>