<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	$CI = & get_instance();
	$options_key = 'tree_comments';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['tc_block1']			= $post['f_tc_block1'];
		$options['tc_block2']			= $post['f_tc_block2'];
		$options['tc_block3']			= $post['f_tc_block3'];
		$options['tc_tabs']				= isset( $post['f_tc_tabs'])		? 1 : 0;
		$options['tc_comment_link']		= $post['f_tc_comment_link'];
		$options['tc_comment_date']		= $post['f_tc_comment_date'];
		$options['tc_comment_ip']		= isset( $post['f_tc_comment_ip'])	? 1 : 0;
		$options['tc_vk_apiid']			= $post['f_tc_vk_apiid'];
		$options['tc_vk_limit']			= $post['f_tc_vk_limit'];
		$options['tc_vk_width']			= $post['f_tc_vk_width'];
		$options['tc_vk_init']			= $post['f_tc_vk_init'];
		$options['tc_fb_limit']			= $post['f_tc_fb_limit'];
		$options['tc_fb_width']			= $post['f_tc_fb_width'];
		$options['tc_dq_id']			= $post['f_tc_dq_id'];
		$options['tc_dq_mobile']		= isset( $post['tc_dq_mobile'])		? 1 : 0;
		$options['tc_form']				= isset( $post['f_tc_form'])		? 1 : 0;
		$options['tc_form_reg']			= isset( $post['f_tc_form_reg'])	? 1 : 0;
		$options['tc_form_nick']		= isset( $post['f_tc_form_nick'])	? 1 : 0;
		$options['tc_form_url']			= isset( $post['f_tc_form_url'])	? 1 : 0;
		$options['tc_form_text1']		= $post['f_tc_form_text1'];
		$options['tc_form_text2']		= $post['f_tc_form_text2'];
		$options['tc_form_text3']		= $post['f_tc_form_text3'];
		
		mso_add_option($options_key, $options, 'plugins');
		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}
	
?>
<h1>Настройка Tree Comments</h1>
<p class="info">Плагин содержит модификацию стандартных комментариев, системы комментирования Вконтакте и Facebook.
<br>Благодаря многочисленным настройкам можно получить комментарии своей мечты :)
<br><br>Тема обсуждения <a href="http://forum.max-3000.com/viewtopic.php?f=6&t=3821">на оффициальном форуме</a> и страница <a href="http://shvind.ru/page/maxsite-tree-style-comments-plugin">на блоге разработчика</a>. Оставляя пожелания и отзывы об ошибках, есть шанс их реализации :)
<br><br>Настройки касающиеся комментирования из <a href="/admin/options#a-kommentirovanie"><b>основых настроек</b></a> и <a href="/admin/template_options#a-prochee"><b>настроек шаблона</b></a>.
</p>

<?php
	$CI = & get_instance();
	$CI->load->helper('form');
	
		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['tc_block1']) )		$options['tc_block1']		= 'comments-tree.php';
		if ( !isset($options['tc_block2']) )		$options['tc_block2']		= 'comments-vk.php';
		if ( !isset($options['tc_block3']) )		$options['tc_block3']		= 'comments-fb.php';
		if ( !isset($options['tc_block4']) )		$options['tc_block4']		= 'comments-dq.php';
		if ( !isset($options['tc_tabs']) )			$options['tc_tabs']			= '1';
		if ( !isset($options['tc_comment_link']) )	$options['tc_comment_link']	= 'none';
		if ( !isset($options['tc_comment_date']) )	$options['tc_comment_date']	= 'j F Y в H:i:s';
		if ( !isset($options['tc_comment_ip']) )	$options['tc_comment_ip']	= '1';
		if ( !isset($options['tc_vk_apiid']) )		$options['tc_vk_apiid']		= '';
		if ( !isset($options['tc_vk_limit']) )		$options['tc_vk_limit']		= '20';
		if ( !isset($options['tc_vk_width']) )		$options['tc_vk_width']		= '660';
		if ( !isset($options['tc_vk_init']) )		$options['tc_vk_init']		= '1';
		if ( !isset($options['tc_fb_limit']) )		$options['tc_fb_limit']		= '20';
		if ( !isset($options['tc_fb_width']) )		$options['tc_fb_width']		= '660';
		if ( !isset($options['tc_dq_id']) )			$options['tc_dq_id']		= '';
		if ( !isset($options['tc_dq_mobile']) )		$options['tc_dq_mobile']	= '1';
		if ( !isset($options['tc_form']) )			$options['tc_form']			= '0';
		if ( !isset($options['tc_form_reg']) )		$options['tc_form_reg']		= '0';
		if ( !isset($options['tc_form_nick']) )		$options['tc_form_nick']	= '0';
		if ( !isset($options['tc_form_url']) )		$options['tc_form_url']		= '0';
		if ( !isset($options['tc_form_text1']) )	$options['tc_form_text1']	= 'Анонимно';
		if ( !isset($options['tc_form_text2']) )	$options['tc_form_text2']	= 'Вход или регистрация';
		if ( !isset($options['tc_form_text3']) )	$options['tc_form_text3']	= 'Авторизация: ';		
	
		$form = '<br><h2>Общие настройки</h2>';

		$form .= '<br><p>Очерёдность систем комментирования</p>';		
		$form .= '<p style="padding-bottom:5px"><strong>#1: </strong>'. 
				form_dropdown('f_tc_block1', 
					array(  'comments-tree.php' => 'Древовидные',
							'comments-vk.php' => 'Вконтакте',
							'comments-fb.php' => 'Facebook',
							'comments-dq.php' => 'Disqus',
							'0'	=> 'Не использовать'),
					$options['tc_block1']).'</p>';
		$form .= '<p style="padding-bottom:5px"><strong>#2: </strong>'. 
				form_dropdown('f_tc_block2', 
					array(  'comments-tree.php' => 'Древовидные',
							'comments-vk.php' => 'Вконтакте',
							'comments-fb.php' => 'Facebook',
							'comments-dq.php' => 'Disqus',
							'0'	=> 'Не использовать'),
					$options['tc_block2']).'</p>';
		$form .= '<p style="padding-bottom:5px"><strong>#3: </strong>'. 
				form_dropdown('f_tc_block3', 
					array(  'comments-tree.php' => 'Древовидные',
							'comments-vk.php' => 'Вконтакте',
							'comments-fb.php' => 'Facebook',
							'comments-dq.php' => 'Disqus',
							'0'	=> 'Не использовать'),
					$options['tc_block3']).'</p>';
		$form .= '<p style="padding-bottom:5px"><strong>#4: </strong>'. 
				form_dropdown('f_tc_block4', 
					array(  'comments-tree.php' => 'Древовидные',
							'comments-vk.php' => 'Вконтакте',
							'comments-fb.php' => 'Facebook',
							'comments-dq.php' => 'Disqus',
							'0'	=> 'Не использовать'),
					$options['tc_block4']).'</p>';
		$form .= '<br>';
		
		$chk = $options['tc_tabs'] ? ' checked="checked"  ' : '';
		$form .= '<p><label><input name="f_tc_tabs" type="checkbox" ' . $chk . '> ' . t('Включить вкладки для нескольких систем комментирования') . '</label>';
		$form .= '<br><span style="font-size: .9em; font-style: italic; color: #606060;">Необходим плагин tabs. Очерёдность выставить в 1древовидные 2вконтакте 3facebook.</span></p>';
		
		$form .= '<br><h2>Опции древовидных комментариев</h2>';
		
		$form .= '<br>Вывод ссылки на комментарий post#comment-nn';
		$form .= '<p style="padding-bottom:5px"><strong>Cсылка: </strong>'. 
				form_dropdown('f_tc_comment_link', 
					array(  'text' => 'Добавить отдельным текстом', 
							'date' => 'Дата комментария станет ссылкой',
							'none' => 'Не отображать'),
					$options['tc_comment_link']).'</p>';
		
		$form .= '<br>' . t('Можно задать произвольный формат даты комментария. Примеры: (H:i d/m/Y) и (j F Y в H:i:s)');
		$form .= '<p><strong>' . t('Формат:') . '</strong> <input name="f_tc_comment_date" type="text" value="' . $options['tc_comment_date'] . '"></p>';
		
		$chk = $options['tc_comment_ip'] ? ' checked="checked"  ' : '';
		$form .= '<br><p><label><input name="f_tc_comment_ip" type="checkbox" ' . $chk . '> ' . t('Отображать админу IP автора каждого комментария в строке "ник, дата и тд"') . '</label>';
		$form .= '<br>';
		
		$form .= '<br><h2>Опции комментариев Вконтакте</h2>';
		
		$form .= '<br>' . t('Получить API-ID можно <a href="http://vkontakte.ru/developers.php?oid=-1&p=Comments" target="_blank">здесь</a>.');
		$form .= '<p><strong>' . t('API-ID:') . '</strong> <input name="f_tc_vk_apiid" type="text" value="' . $options['tc_vk_apiid'] . '"></p>';
		
		$form .= '<br>' . t('Пагинация комментариев (вконтакт предлагает 5, 10, 15, 20).');
		$form .= '<p><strong>' . t('Кол-во:') . '</strong> <input name="f_tc_vk_limit" type="text" value="' . $options['tc_vk_limit'] . '"></p>';

		$form .= '<br>' . t('Размер блока комментариев (если не указывать будет 100%)');
		$form .= '<p><strong>' . t('Ширина:') . '</strong> <input name="f_tc_vk_width" type="text" value="' . $options['tc_vk_width'] . '"></p>';
		
		$chk = $options['tc_vk_init'] ? ' checked="checked"  ' : '';
		$form .= '<br><p><label><input name="f_tc_vk_init" type="checkbox" ' . $chk . '> ' . t('Выводить скрипт инициализации вконтакте. Отключить при конфликте с другими плагинами.') . '</label>';
		$form .= '<br>';
		
		$form .= '<br><h2>Опции комментариев Facebook</h2>';

		$form .= '<br>' . t('Пагинация комментариев (любое значение)');
		$form .= '<p><strong>' . t('Кол-во:') . '</strong> <input name="f_tc_fb_limit" type="text" value="' . $options['tc_fb_limit'] . '"></p>';

		$form .= '<br>' . t('Размер блока комментариев (обязательно. стандартно default 660px, d2 650px).');
		$form .= '<p><strong>' . t('Ширина:') . '</strong> <input name="f_tc_fb_width" type="text" value="' . $options['tc_fb_width'] . '"></p>';
		
		$form .= '<br><h2>Опции комментариев Disqus</h2>';

		$form .= '<br>' . t('Идентификатор вашего сайта');
		$form .= '<p><strong>' . t('Shortcode:') . '</strong> <input name="f_tc_dq_id" type="text" value="' . $options['tc_dq_id'] . '"></p>';

		//$chk = $options['tc_dq_mobile'] ? ' checked="checked"  ' : '';
		//$form .= '<br><p><label><input name="f_tc_dq_mobile" type="checkbox" ' . $chk . '> ' . t('Не использовать версию оптимизированную для мобильных устройств') . '</label><br>';
		
		$form .= '<br><h2>Опции формы комментирования</h2>';
		
		$chk = $options['tc_form'] ? ' checked="checked"  ' : '';
		$form .= '<br><p><label><input name="f_tc_form" type="checkbox" ' . $chk . '> ' . t('Использовать старую форму комментирования <0.62.') . '</label>';
		$form .= '<br><span style="font-size: .9em; font-style: italic; color: #606060;">Заменяет стандратный блок. Необходимо для опций касающихся формы.</span><br>';
		
		$form .= '<br>' . t('Cтрока выводимая для анонимного комментирования:');
		$form .= '<p><strong>' . t('Текст:') . '</strong> <input name="f_tc_form_text1" type="text" value="' . $options['tc_form_text1'] . '"></p>';

		$form .= '<br>' . t('Cтрока выводимая для регистрации:');
		$form .= '<p><strong>' . t('Текст:') . '</strong> <input name="f_tc_form_text2" type="text" value="' . $options['tc_form_text2'] . '"></p>';		

		$form .= '<br>' . t('Строка выводимая перед системами авторизации:');
		$form .= '<p><strong>' . t('Текст:') . '</strong> <input name="f_tc_form_text3" type="text" value="' . $options['tc_form_text3'] . '"></p>';	
		
		$chk = $options['tc_form_reg'] ? ' checked="checked"  ' : '';
		$form .= '<br><p><label><input name="f_tc_form_reg" type="checkbox" ' . $chk . '> ' . t('Переключить способ комментирования по умолчанию.') . '</label>';
		$form .= '<br><span style="font-size: .9em; font-style: italic; color: #606060;">Актуально, если включено анонимное комментирование.</span> <br>';
		
		$chk = $options['tc_form_nick'] ? ' checked="checked"  ' : '';
		$form .= '<br><p><label><input name="f_tc_form_nick" type="checkbox" ' . $chk . '> ' . t('Отображать поле "Ник" при регистрации.') . '</label>';
		$form .= '<br><span style="font-size: .9em; font-style: italic; color: #606060;">работает на версии >0.60</span><br>';

		$chk = $options['tc_form_url'] ? ' checked="checked"  ' : '';
		$form .= '<br><p><label><input name="f_tc_form_url" type="checkbox" ' . $chk . '> ' . t('Отображать поле "Сайт" при регистрации.') . '</label>';
		$form .= '<br><span style="font-size: .9em; font-style: italic; color: #606060;">работает на версии >0.60</span><br>';	
		
		$form .= '<br>';
		echo '<form action="" method="post">'.mso_form_session('f_session_id');
		echo $form;
		echo '<input type="submit" name="f_submit" value=" Сохранить изменения " style="margin:15px 0 5px" />';
		echo '</form><br><br>';
?>