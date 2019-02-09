<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

  // страницы админки плагина profile


	echo '<h1>'. t('Плагин profile'). '</h1><p class="info">'. t('Плагин управления страницами пользователя.'). '</p>';
	
  $options_key = 'profile';

	$curl = (!function_exists('curl_init')) ? '<span style="color:red">' . t('Для работы плагина требуется наличие включенной PHP-библиотеки CURL!') . '</span><br><br>' : '';
	
	mso_admin_plugin_options($options_key, 'plugins', 
		array(
				'widget_type' => array(
						'type' => 'select', 
						'name' => t('Ссылка авторизации для формы комментариев в виде:'), 
						'description' => t('Отображение ссылки авторизации для формы комментариев в виде'),
						'values' => t('small||Кнопки # panel||Большие кнопки # window||Одна кнопка'),
						'default' => '1'
					),	
				'auth_title' => array(
						'type' => 'text', 
						'name' => t('Текст ссылки авторизации для формы комментариев:'), 
						'description' => t('Укажите текст ссылки авторизации для формы комментариев. Работает html'),
						'default' => 'ulogin'
					),					
				'widget_fcomments_priority' => array(
						'type' => 'text', 
						'name' => t('Приоритет ссылки авторизации для формы комментариев:'), 
						'description' => t('Укажите приоритет ссылки авторизации для формы логина. Чем меньше приоритет, тем дальше от начала будет ссылка. Чем больше - тем первее. Например, 10 - самый конец, 99 - самое начало'),
						'default' => '10'
					),	
					
				'widget_type_flogin' => array(
						'type' => 'select', 
						'name' => t('Ссылка авторизации для формы логина в виде:'), 
						'description' => t('Отображение ссылки авторизации для формы логина в виде'),
						'values' => t('small||Кнопки # panel||Большие кнопки # window||Одна кнопка'),
						'default' => '1'
					),	
					
				'auth_title_flogin' => array(
						'type' => 'text', 
						'name' => t('Текст ссылки авторизации для формы логина:'), 
						'description' => t('Укажите текст ссылки авторизации для формы логина. Работает html'),
						'default' => 'ulogin'
					),		
					
				'widget_flogin_priority' => array(
						'type' => 'text', 
						'name' => t('Приоритет ссылки авторизации для формы логина:'), 
						'description' => t('Укажите приоритет ссылки авторизации для формы логина. Чем меньше приоритет, тем дальше от начала будет ссылка. Чем больше - тем первее. Например, 10 - самый конец, 99 - самое начало'),
						'default' => '10'
					),	
					
				'providers_set' => array(
						'type' => 'text', 
						'name' => t('Отображаемые провайдеры:'), 
						'description' => t('Укажите через запятую доступных провайдеров:<br>') .
						'vkontakte,odnoklassniki,mailru,facebook,twitter,google,yandex,livejournal, youtube', 
						'default' => 'vkontakte,odnoklassniki,mailru,facebook,twitter'
					),
					
				'providers_hidden' => array(
						'type' => 'text', 
						'name' => t('Скрытые провайдеры:'), 
						'description' => t('Укажите через запятую провайдеров, которые появятся при наведении:<br>'),
						'default' => 'google,yandex,livejournal,youtube'
					),		
								
				'fields' => array(
						'type' => 'text', 
						'name' => t('Обязательные поля:'), 
						'description' => t('Укажите через запятую поля, которые будут запрошены у автора в случае отсутствия:<br>(email)<br>'),
						'default' => ''
					),	
							
				'edit_email' => array(
						'type' => 'checkbox', 
						'name' => t('Разрешить редактировать email:'), 
						'description' => t('Будет выведено поле ввода нового email комюзером.<br>Иначе изменение email будет доступно только если email пуст.'),
						'default' => false
					),													

			),
		t('Настройки авторизации при помощи сервиса ulogin'), // титул
		t('Авторизация на сайте через сервис <a href="http://ulogin.ru">ulogin</a>')
		. $curl
		. t('<br><b>Авторизация может работать без e-mail адреса!</b>')   // инфо
	);	
	

