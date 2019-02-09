<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	$CI = & get_instance();
	
	$options_key = 'a1pay';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['secret_key'] = $post['f_secret_key'];
		$options['duration_default'] = $post['f_duration_default'];
		
		if ( !isset($post['f_duration_default']) or empty($post['f_duration_default'])) $post['f_duration_default'] = 86400;
		$options['duration_default'] = $post['f_duration_default'];
		
		if ( !isset($post['f_folder']) or empty($post['f_folder'])) $post['f_folder'] = 'downloads';
		$options['folder'] = $post['f_folder'];
		
		
		if ( !isset($post['f_tests'])) $post['f_tests'] = 0;
		$options['tests'] = $post['f_tests'];

		if ( !isset($post['f_check_ip'])) $post['f_check_ip'] = 0;
		$options['check_ip'] = $post['f_check_ip'];

		if ( !isset($post['f_check_duration'])) $post['f_check_duration'] = 0;
		$options['check_duration'] = $post['f_check_duration'];
		
		if ( !isset($post['f_desc_fixprice']) or empty($post['f_desc_fixprice'])) $post['f_desc_fixprice'] = '';
		$options['desc_fixprice'] = $post['f_desc_fixprice'];

		if ( !isset($post['f_mybutton']) or empty($post['f_mybutton'])) $post['f_mybutton'] = '';
		$options['mybutton'] = $post['f_mybutton'];
		
		if ( !isset($post['f_desc_freeprice']) or empty($post['f_desc_freeprice'])) $post['f_desc_freeprice'] = 'Этот продукт можно приобрести по минимальной цене %mincost%. Рекомендованная стоимость %price%';
		$options['desc_freeprice'] = $post['f_desc_freeprice'];
		
		mso_add_option($options_key, $options, 'plugins');
		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}
	
	// добавляем новую запись
	if ( $post = mso_check_post(array('f_session_id', 'f_submit_new')) )
	{
		mso_checkreferer();
		$service_id = $post['f_service_id'];
		$service_key = $post['f_service_key'];
		
		$title = $post['f_title'];
		$filename = $post['f_filename'];
		$subfolder = ( !isset( $post['f_subfolder']) or empty( $post['f_subfolder']) ) ? '' : $post['f_subfolder'];
		$cost = $post['f_cost'];
		$mincost = ( !isset( $post['f_mincost']) or empty($post['f_mincost']) ) ? -1 : $post['f_mincost'];
		if ($mincost == 'free' ) $mincost = 0;
		
		if ( !isset($service_id) or empty($service_id))
		{
			echo '<div class="error">' . t('Не указан service_id!', 'plugins') . '</div>';
		} else if ( !isset($service_key) or empty($service_key) ) {
			echo '<div class="error">' . t('Не указан key формы!', 'plugins') . '</div>';
		} else if ( !isset($title) or empty($title) ) {
			echo '<div class="error">' . t('Не указано название!', 'plugins') . '</div>';
		} else if ( !isset($filename) or empty($filename) ) {
			echo '<div class="error">' . t('Не указано имя файла!', 'plugins') . '</div>';
		} else if ( !isset($cost) or empty($cost) ) {
			echo '<div class="error">' . t('Не указана цена!', 'plugins') . '</div>';
		} else {
			if ( ! is_numeric($service_id) or ( $service_id < 0 )) {
				echo '<div class="error">' . t('Не корректно указан service_id!', 'plugins') . '</div>';
			} else	
			if ( ! is_numeric($cost) or ( $cost < 0 )) {
				echo '<div class="error">' . t('Не корректно указана стоимость!', 'plugins') . '</div>';
				return;
			} else {				
				$CI = & get_instance();
				// провеяем наличие
				$CI->db->select('service_id');
				$CI->db->where('service_id', $service_id);
				$query = $CI->db->get('a1pay_services');
				if ($query->num_rows() > 0)
				{
					echo '<div class="error">' . t('Указанные данные уже существуют в базе!', 'plugins') . '</div>';
				} else {
					$data = array(
									'service_id' => $service_id,
									'key' => $service_key,
									'filename' => $filename,
									'subfolder' => $subfolder,
									'title' => $title ,
									'cost' => $cost,
									'mincost' => $mincost
								 );
					$CI->db->insert('a1pay_services', $data);
					echo '<div class="update">' . t('Данные успешно добавлены в базу!', 'plugins') . '</div>';
				}
			}
		}
	}

	
    // удаление записи
	if ( $post = mso_check_post(array('f_session_id', 'f_delete_submit')) )
	{
		mso_checkreferer();
		$f_id = mso_array_get_key($post['f_delete_submit']); 
		$CI = & get_instance();
		$CI->db->where('id', $f_id);
		$CI->db->delete('a1pay_services');
		
		echo '<div class="update">' . t('Запись удалена!', 'plugins') . '</div>';	
	}
	
	$seg3 = mso_segment(3);
?>

<div class="admin-h-menu">
<?php
	if ( $seg3 == '' ) {
		echo '<a class="select" href="' . getinfo('site_url') . 'admin/a1pay' . '">Общие настройки</a>';
		echo '&nbsp;&nbsp;|&nbsp;&nbsp;';
		echo '<a href="' . getinfo('site_url') . 'admin/a1pay/services' . '">Управление сервисами</a>';
		//echo '&nbsp;&nbsp;|&nbsp;&nbsp;';
		//echo '<a href="' . getinfo('site_url') . 'admin/a1pay/appearance' . '">Внешний вид</a>';
		echo '&nbsp;&nbsp;|&nbsp;&nbsp;';
		echo '<a href="' . getinfo('site_url') . 'admin/a1pay/orders' . '">Заказы</a>';		
	} else if ( $seg3 == 'services' ) {
		echo '<a href="' . getinfo('site_url') . 'admin/a1pay' . '">Общие настройки</a>';
		echo '&nbsp;&nbsp;|&nbsp;&nbsp;';
		echo '<a class="select" href="' . getinfo('site_url') . 'admin/a1pay/services' . '">Управление сервисами</a>';
		//echo '&nbsp;&nbsp;|&nbsp;&nbsp;';
		//echo '<a href="' . getinfo('site_url') . 'admin/a1pay/appearance' . '">Внешний вид</a>';
		echo '&nbsp;&nbsp;|&nbsp;&nbsp;';
		echo '<a href="' . getinfo('site_url') . 'admin/a1pay/orders' . '">Заказы</a>';		
	} else if ( $seg3 == 'appearance') {
		echo '<a href="' . getinfo('site_url') . 'admin/a1pay' . '">Общие настройки</a>';
		echo '&nbsp;&nbsp;|&nbsp;&nbsp;';
		echo '<a href="' . getinfo('site_url') . 'admin/a1pay/services' . '">Управление сервисами</a>';
		//echo '&nbsp;&nbsp;|&nbsp;&nbsp;';
		//echo '<a class="select" href="' . getinfo('site_url') . 'admin/a1pay/appearance' . '">Внешний вид</a>';
		echo '&nbsp;&nbsp;|&nbsp;&nbsp;';
		echo '<a href="' . getinfo('site_url') . 'admin/a1pay/orders' . '">Заказы</a>';		
	} else if ( $seg3 == 'orders' ) {
		echo '<a href="' . getinfo('site_url') . 'admin/a1pay' . '">Общие настройки</a>';
		echo '&nbsp;&nbsp;|&nbsp;&nbsp;';
		echo '<a href="' . getinfo('site_url') . 'admin/a1pay/services' . '">Управление сервисами</a>';
		//echo '&nbsp;&nbsp;|&nbsp;&nbsp;';
		//echo '<a href="' . getinfo('site_url') . 'admin/a1pay/appearance' . '">Внешний вид</a>';
		echo '&nbsp;&nbsp;|&nbsp;&nbsp;';
		echo '<a class="select"  href="' . getinfo('site_url') . 'admin/a1pay/orders' . '">Заказы</a>';		
	}
?>
</div>
<h1><?= t('Агрегатор платежей A1Pay', __FILE__) ?></h1>
<p class="info"><?= t('Плагин автоматической оплаты за услуги посредством агрегатора платежей A1Pay.', __FILE__) ?>
<br>
Позволяет скачивать пользователям файлы после оплаты.
</p>

<?php
	
	if (  $seg3 == '' ) {	
		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['secret_key']) ) $options['secret_key'] = ''; 
		$secret_key = $options['secret_key'];
		
		if ( !isset($options['duration_default']) ) $options['duration_default'] = 86400; // сутки, 24 часа
		$duration_default = $options['duration_default'];
		
		if ( !isset($options['folder']) ) $options['folder'] = 'downloads'; 
		$folder = $options['folder'];
		
		if ( !isset($options['desc_fixprice']) ) $options['desc_fixprice'] = ''; 
		$desc_fixprice = $options['desc_fixprice'];

		if ( !isset($options['desc_freeprice']) ) $options['desc_freeprice'] = 'Этот продукт можно приобрести по минимальной цене %mincost%. Рекомендованная стоимость %price%'; 
		$desc_freeprice = $options['desc_freeprice'];
		
		if ( !isset($options['tests'] )) $options['tests'] = 0;
		$test = $options['tests'];

		if ( !isset($options['check_ip'] )) $options['check_ip'] = 0;
		$check_ip = $options['check_ip'];

		if ( !isset($options['check_duration'] )) $options['check_duration'] = 0;
		$check_duration = $options['check_duration'];

		if ( !isset($options['mybutton']) ) $options['mybutton'] = ''; 
		$mybutton = $options['mybutton'];
		
		
		
		$xpath = '/' . $folder . '/';
		$fpath = $_SERVER['DOCUMENT_ROOT'] . $xpath;
		if	( !file_exists( $fpath ) ) {
			echo '<div class="error">' . t('Папка <strong>'.$folder.'</strong> не существует!<br>Создайте ее!', 'plugins') . '</div>';		
		}
		echo '<h1>Общие настройки</h1>';
		echo '<hr><br>';
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo '<table>';
		echo '<colgroup style="width: 180px">';
		echo '<colgroup style="width: 150px">';
		// Секретный ключ
		echo '<tr>';
		echo '<td><strong>Секретный ключ:</strong></td>';
		echo '<td><input name="f_secret_key" type="text" value="' . $secret_key . '"></td>';
		echo '<td style="padding-left: 4px;">Ключ, по которому проверяется достоверность передаваемых данных. Этот ключ указывается при создании сервиса в A1Pay.</td>';
		echo '</tr>';
		
		// время жизни ссылок по умолчанию
		echo '<tr>';
		echo '<td><strong>Время жизни ссылок:</strong></td>';
		echo '<td><input name="f_duration_default" type="text" value="' . $duration_default . '"></td>';
		echo '<td style="padding-left: 4px;">Время жизни ссылок по умолчанию. Указывается в секундах.</td></tr>';
		echo '<tr><td></td><td></td><td style="padding-left: 4px;">' . NR . 
		     '<p>Сейчас время жизни ссылок ' . $duration_default . ' сек, или ' . 
				$duration_default/60 . ' мин';
				if ( ($duration_default/60/60 ) >= 1 ) echo ', или ' . round($duration_default/60/60) . ' часа(ов)';
				$tmpday = 	round($duration_default/60/60/24);
				if ( ($tmpday ) >= 1 ) echo ', или ' . $tmpday . ' дн';
			 '</p>' . NR .	
		     '</td>';
		echo '</tr>';
		
		// папка по дефолту
		echo '<tr>';
		echo '<td><strong>Папка хранения файлов:</strong></td>'; 
		echo '<td><input name="f_folder" value="' . $folder . '" maxlength="200" type="text" style="width: 200px"></td>';
		echo '<td style="padding-left: 4px;">Папка хранения файлов относительно корневой папки сайта,<br> т.е. <strong>' . getinfo('FCPATH') . $folder . '/<ваши файлы></strong></i>';
		echo '</td>';
		echo '</tr>';
		
		echo '<tr></tr>';
		// тестовый режим
		echo '<tr>';
			echo '<td><strong>Тестовый режим:</strong></td>'; 
				
				$checked = ( $test ) ? ' checked ' : '';
				echo '<td><input name="f_tests" value="accept" maxlength="200" type="checkbox" ' . $checked . 'style="width: 20px">&nbsp;';
				echo '</td>';
			echo '</tr>';

		echo '<tr></tr>';
		// проверка по времени жизни ссылок
		echo '<tr>';
			echo '<td><strong>Время жизни ссылок:</strong></td>'; 
				
				$checked = ( $check_duration ) ? ' checked ' : '';
				echo '<td><input name="f_check_duration" value="accept" maxlength="200" type="checkbox" ' . $checked . 'style="width: 20px">&nbsp;';
				echo '</td>';
				echo '<td style="padding-left: 4px;">Производить проверку времени жизни ссылки</td>';				
			echo '</tr>';		
		// проверка по IP
		echo '<tr>';
			echo '<td><strong>Проверка IP:</strong></td>'; 
				
				$checked = ( $check_ip ) ? ' checked ' : '';
				echo '<td><input name="f_check_ip" value="accept" maxlength="200" type="checkbox" ' . $checked . 'style="width: 20px">&nbsp;';
				echo '</td>';
				echo '<td style="padding-left: 4px;">Производить проверку по IP при скачивании файла</td>';				
			echo '</tr>';
			

			echo '<tr><td><br></td><td></td></tr>';
			echo '<tr>';
				echo '<td><strong>Описание для блока с фиксированной ценой:</strong></td>';
				echo '<td colspan="2"><input name="f_desc_fixprice" value="' . $desc_fixprice . '" type="text" style="width: 700px"></td>';
			echo '</tr>';
			echo '<tr><td><br></td><td></td></tr>';
			echo '<tr>';
				echo '<td><strong>Описание для блока со свободной ценой:</strong></td>';
				echo '<td colspan="2"><input name="f_desc_freeprice" value="' . $desc_freeprice . '" type="text" style="width: 700px"></td>';
			echo '</tr>';
			echo '<tr><td></td><td colspan="2"><i>Используйте в тексте:<br><b>%mincost%</b> для отображения минимальной цены товара</i>,<br> <b>%price%</b> для указания рекомендованной цены</td></tr>';	

			echo '<tr><td><br></td><td></td></tr>';			
			echo '<tr>';
				echo '<td><strong>Изображение для кнопки:</strong></td>';
				echo '<td><input name="f_mybutton" value="' . $mybutton . '" type="text" style="width: 200px"></td>';
				echo '<td><i>Если вы желаете изменить вид стандартной кнопки, то разместите картинку в папке с шаблоном, в поле укажите путь к картинке относительно папки шаблона, например, <b>pay_button.jpg</b> или <b>images/pay_button.png</b></i><br>Оставьте поле пустым, если желаете отображать стандартную кнопку</td>';
			echo '</tr>';
			
			
		echo '</table>';
		
		echo '<input type="submit" name="f_submit" value="' . t('Изменить', 'plugins') . '" style="margin: 25px 0 5px 0;">';
		echo '</form>';
		
		echo '<br><hr><br>';
	} else if ( $seg3 == 'services') {	
		// добавление в базу
		echo '<h1>Управление сервисами A1Pay</h1>';
				echo '<hr><br>';
		echo '<h3>Добавление нового сервиса А1Pay в базу:</h3>';
		
		$form = '<table>';
		$form .= '<colgroup style="width: 150px">';
		$form .= '<colgroup style="width: 300px">';
		$form .= '<tr><td><strong>Service id:</strong></td>' . ' <td><input name="f_service_id" type="text" value=""></td><td style="padding-left: 4px;">Укажите service_id. Его можно взять из таблицы (столбец id) на странице с созданными <a href="https://home.a1pay.ru/a1lite/index/" target="blank">сервисами</a></td></tr>';
		$form .= '<tr style="height: 5px;"></tr>';
		$form .= '<tr><td><strong>Service key:</strong></td>' . ' <td><input name="f_service_key" type="text" value=""></td><td style="padding-left: 4px;">Укажите service key. Его нужно взять из формы, сгенеренной после создания сервиса в a1pay</td></tr>';
		$form .= '<tr style="height: 5px;"></tr>';
		$form .= '<tr><td><strong>Название:</strong></td>' . ' <td><input name="f_title" type="text" value=""></td><td style="padding-left: 4px;">Укажите здесь название сервиса (см. страницу с <a href="https://home.a1pay.ru/a1lite/index/" target="blank">сервисами</a>)</td></tr>';			
		$form .= '<tr style="height: 5px;"></tr>';
		$form .= '<tr><td><strong>Имя файла:</strong></td>' . ' <td><input name="f_filename" type="text" value=""></td><td style="padding-left: 4px;">Укажите имя физического файла. Под этим именем браузер будет отдавать файл клиенту.</td></tr>';	
		$form .= '<tr style="height: 5px;"></tr>';		
		$form .= '<tr><td><strong>Подкаталог:</strong></td>' . ' <td><input name="f_subfolder" type="text" value=""></td><td style="padding-left: 4px;">Можно указать подкаталог для более удобной категоризации файлов.</td></tr>';			
		$form .= '<tr style="height: 5px;"></tr>';
		$form .= '<tr><td><strong>Цена:</strong></td>' . ' <td><input name="f_cost" type="text" value=""></td><td style="padding-left: 4px;">Укажите цену товара в рублях</td></tr>';			
		$form .= '<tr style="height: 5px;"></tr>';
		$form .= '<tr><td><strong>Мин. цена:</strong></td>' . ' <td><input name="f_mincost" type="text" value=""></td><td style="padding-left: 4px;">Укажите минимальную цену товара в рублях.</td></tr>';			
		$form .= '<tr><td></td><td></td><td style="padding-left: 4px;">Если используется фиксированная цена, то ничего не указывайте в этом поле.' . /*<br>Если файл можно скачивать бесплатно (свободная цена), то укажите слово \'free\'.*/ '</td></tr>';
		$form .= '</table>';
		

		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo $form;
		echo '<input type="submit" name="f_submit_new" value="' . t('Добавить в базу', 'plugins') . '" style="margin: 5px 0 25px 0;">';
		echo '</form>';	

		echo '<hr><br>';
		
		echo '<h3>Все сервисы в базе:</h3>';		
		// строим таблицу
		$CI = & get_instance();
		$CI->db->select('id, service_id, filename, subfolder, title, downcount, lastdownload, cost, mincost');
		$CI->db->order_by('id');
		$query = $CI->db->get('a1pay_services');
		if ($query->num_rows() > 0)
		{
		    echo '<form action="" method="post">' . mso_form_session('f_session_id');
			echo '<table class="page cats" style="text-align: left;">' . 
				'<colgroup style="width: 30px">' .
				'<colgroup style="width: 80px">' .
				//'<colgroup style="width: 200px">' .
				//'<colgroup style="width: 150px;">' .
			  '<tr>' .
			    '<th><strong>id</strong></th>' .
				'<th><strong>service id</strong></th>' .
				'<th><strong>Название</strong></th>' .
				'<th><strong>Имя файла</strong></th>' .
				'<th><strong>Скачано</strong></th>' .
				'<th><strong>Дата</strong></th>' .
				'<th><strong>Цена</strong></th>' .
				'<th></th>' .
			  '</tr>';				
			// выводим по строкам
			foreach ($query->result() as $row)
			{			
			
				  // скопировать ссылку без счетчика не bb-code
				  $cod01 = '[a1pay id='.$row->id.']';
				  $f = ( empty($row->subfolder) ? '' : $row->subfolder . '/') . $row->filename ;
				  $link01 = '<a href="#" onClick = "jAlert(\'' .
							'<strong>HTML-код формы оплаты</strong>' .
							'<textarea cols=10 rows=1>'.$cod01.'</textarea>\', \'HTML-код формы\'); return false;">' . $f . '</a>';
				$line = '<tr>';
				$line .= '<td>'.$row->id.'</td>';
				$line .= '<td>'.$row->service_id.'</td>';
				$line .= '<td>'.$row->title.'</td>';
				$line .= '<td>'.$link01.'</td>';
				$line .= '<td>'. ((!empty($row->downcount)) ? $row->downcount : 0) .'</td>';
				$line .= '<td>'. (( $row->lastdownload == '0000-00-00 00:00:00' ) ? '' : date('d.m.Y H:i', strtotime($row->lastdownload))).'</td>';
				$line .= '<td>'.$row->cost. ( ($row->mincost  > 0) ? '(мин. '.$row->mincost.')' : ( ($row->mincost == 0) ? '(свободная)' : '' )) . '</td>';
				$line .= '<td><input type="submit" name="f_delete_submit[' . $row->id . ']" value="' . t('Удалить', 'admin') . '" onClick="if(confirm(\'' . t('Удалить файл?', 'admin') . '\')) {return true;} else {return false;}" ></td>';				
				$line .= '</tr>';
				
				echo $line;
			}
			echo '</table><br><br>';
		}
	} else if ( $seg3 == 'appearance' ) {
		echo '<h1>Внешний вид</h1>';
		echo '<hr><br>';	
		// 1. общий текст перед всем
		// 2. Текст для фиксированной цены
		// 3. Текст для свободной цены
		// 4. ссылка на статистику скачиваний под кнопкой оплаты
	} else if ( $seg3 == 'orders' ) {
		echo '<h1>Заказы</h1>';
		echo '<hr><br>';	
		$CI = & get_instance();
		$CI->db->select('tid, order_id, service_id, type, cost_partner, cost_system, date, ip, email, phone');
		$CI->db->order_by('tid');
		$query = $CI->db->get('a1pay_purchases');
		if ($query->num_rows() > 0)
		{
			echo '<table class="page cats" style="text-align: left;">' . 
			'<colgroup style="width: 30px">' .
			'<colgroup style="width: 80px">' .
			'<tr>' .
				'<th><strong>Транзакция</strong></th>' .
				'<th><strong>№ заказа</strong></th>' .
				'<th><strong>id сервиса</strong></th>' .
				'<th><strong>Тип</strong></th>' .
				'<th><strong>Стоимость<br>(партнер)</strong></th>' .
				'<th><strong>Стоимость<br>(системная)</strong></th>' .
				'<th><strong>Дата</strong></th>' .
				'<th>IP</th>' .
				'<th>E-mail</th>' .
				'<th>Телефон</th>' .
			'</tr>';	
			// выводим по строкам
			foreach ($query->result() as $row)
			{
				$line = '<tr>';
				$line .= '<td>'.$row->tid.'</td>';
				$line .= '<td>'.$row->order_id.'</td>';
				$line .= '<td>'.$row->service_id.'</td>';
				$line .= '<td>'.$row->type.'</td>';
				$line .= '<td>'.$row->cost_partner.'</td>';
				$line .= '<td>'.$row->cost_system.'</td>';
				$line .= '<td>'.$row->date.'</td>';
				$line .= '<td>'.$row->ip.'</td>';
				$line .= '<td>'.$row->email.'</td>';
				$line .= '<td>'.$row->phone.'</td>';
				$line .= '</tr>';
				echo $line;
			}
			echo '</table><br><br>';
		}
	}
?>