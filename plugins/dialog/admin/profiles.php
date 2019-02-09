<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	$CI = & get_instance();

	$options_key = 'dialog_profiles';

	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();

		$element1 = array();
		$element1['title'] = isset($post['f_title1']) ? $post['f_title1'] : 'сообщение в дискуссии';
		$element1['name'] = isset($post['f_name1']) ? $post['f_name1'] : 'Сообщения';
		$element1['all'] = isset($post['f_all1']) ? $post['f_all1'] : 'Все сообщения на форуме';
		$element1['title_go'] = isset($post['f_title_go1']) ? $post['f_title_go1'] : 'Перейти к сообщению в дискуссии';
		$element1['all_link'] = isset($post['f_all_link1']) ? $post['f_all_link1'] : '';
		$element1['img'] = isset($post['f_img1']) ? $post['f_img1'] : getinfo('plugins_url') . 'dialog/img/message.png';
		$element1['filename'] = isset($post['f_filename1']) ? $post['f_filename1'] : 'comments';
		$element1['slug'] = isset($post['f_slug1']) ? $post['f_slug1'] : 'comments-forum';

		$element2 = array();
		$element2['title'] = isset($post['f_title2']) ? $post['f_title2'] : 'сказал спасибо за сообщение пользователю';
		$element2['name'] = isset($post['f_name2']) ? $post['f_name2'] : 'Благодарил';
		$element2['all'] = isset($post['f_all2']) ? $post['f_all2'] : 'Все благодарности';
		$element2['title_go'] = isset($post['f_title_go2']) ? $post['f_title_go2'] : 'Перейти к сообщению в дискуссии';
		$element2['all_link'] = isset($post['f_all_link2']) ? $post['f_all_link2'] : '';
		$element2['img'] = isset($post['f_img2']) ? $post['f_img2'] : getinfo('plugins_url') . 'dialog/img/danke2.png';
		$element2['filename'] = isset($post['f_filename2']) ? $post['f_filename2'] : 'dankes2';
		$element2['slug'] = isset($post['f_slug2']) ? $post['f_slug2'] : 'dankes2';
		
		$element3 = array();
		$element3['title'] = isset($post['f_title3']) ? $post['f_title3'] : 'получил благодарность от пользователя';
		$element3['name'] = isset($post['f_name3']) ? $post['f_name3'] : 'Получал благодарности';
		$element3['all'] = isset($post['f_all3']) ? $post['f_all3'] : 'Все полученные благодарности';
		$element3['title_go'] = isset($post['f_title_go3']) ? $post['f_title_go3'] : 'Перейти к сообщению в дискуссии';
		$element3['all_link'] = isset($post['f_all_link3']) ? $post['f_all_link3'] : '';
		$element3['img'] = isset($post['f_img3']) ? $post['f_img3'] : getinfo('plugins_url') . 'dialog/img/danke1.png';
		$element3['filename'] = isset($post['f_filename3']) ? $post['f_filename3'] : 'dankes1';		
		$element3['slug'] = isset($post['f_slug3']) ? $post['f_slug3'] : 'dankes1';
				
		$element4 = array();
		$element4['title'] = isset($post['f_title4']) ? $post['f_title4'] : 'получил положительную оценку сообщения от';
		$element4['name'] = isset($post['f_name4']) ? $post['f_name4'] : 'Положительно оцененные комментарии';
		$element4['all'] = isset($post['f_all4']) ? $post['f_all4'] : 'Все полученные положительные оценки';
		$element4['title_go'] = isset($post['f_title_go4']) ? $post['f_title_go4'] : 'Перейти к сообщению в дискуссии';
		$element4['all_link'] = isset($post['f_all_link4']) ? $post['f_all_link4'] : '';
		$element4['img'] = isset($post['f_img4']) ? $post['f_img4'] : getinfo('plugins_url') . 'dialog/img/vote_plus1.png';
		$element4['filename'] = isset($post['f_filename4']) ? $post['f_filename4'] : 'vote_plus1';					
		$element4['slug'] = isset($post['f_slug4']) ? $post['f_slug4'] : 'vote_plus1';

		$element5 = array();
		$element5['title'] = isset($post['f_title5']) ? $post['f_title5'] : 'положительно оценил сообщение пользователя';
		$element5['name'] = isset($post['f_name5']) ? $post['f_name5'] : 'Оценивал положительно';
		$element5['all'] = isset($post['f_all5']) ? $post['f_all5'] : 'Все положительные оценки';
		$element5['title_go'] = isset($post['f_title_go5']) ? $post['f_title_go5'] : 'Перейти к сообщению в дискуссии';
		$element5['all_link'] = isset($post['f_all_link5']) ? $post['f_all_link5'] : '';
		$element5['img'] = isset($post['f_img5']) ? $post['f_img5'] : getinfo('plugins_url') . 'dialog/img/vote_plus2.png';
		$element5['filename'] = isset($post['f_filename5']) ? $post['f_filename5'] : 'vote_plus2';	
		$element5['slug'] = isset($post['f_slug5']) ? $post['f_slug5'] : 'vote_plus2';

		$element6 = array();
		$element6['title'] = isset($post['f_title6']) ? $post['f_title6'] : 'получил отрицательную оценку сообщения от';
		$element6['name'] = isset($post['f_name6']) ? $post['f_name6'] : 'Отрицательно оцененные комментарии';
		$element6['all'] = isset($post['f_all6']) ? $post['f_all6'] : 'Все полученные отрицательные оценки';
		$element6['title_go'] = isset($post['f_title_go6']) ? $post['f_title_go6'] : 'Перейти к сообщению в дискуссии';
		$element6['all_link'] = isset($post['f_all_link6']) ? $post['f_all_link6'] : '';
		$element6['img'] = isset($post['f_img6']) ? $post['f_img6'] : getinfo('plugins_url') . 'dialog/img/vote_minus1.png';
		$element6['filename'] = isset($post['f_filename6']) ? $post['f_filename6'] : 'vote_minus1';	
		$element6['slug'] = isset($post['f_slug6']) ? $post['f_slug6'] : 'vote_minus1';

		$element7 = array();
		$element7['title'] = isset($post['f_title7']) ? $post['f_title7'] : 'отрицательно оценил сообщение пользователя';
		$element7['name'] = isset($post['f_name7']) ? $post['f_name7'] : 'Оценивал отрицательно';
		$element7['all'] = isset($post['f_all7']) ? $post['f_all7'] : 'Все отрицательные оценки';
		$element7['title_go'] = isset($post['f_title_go7']) ? $post['f_title_go7'] : 'Перейти к сообщению в дискуссии';
		$element7['all_link'] = isset($post['f_all_link7']) ? $post['f_all_link7'] : '';
		$element7['img'] = isset($post['f_img7']) ? $post['f_img7'] : getinfo('plugins_url') . 'dialog/img/vote_minus2.png';
		$element7['filename'] = isset($post['f_filename7']) ? $post['f_filename7'] : 'vote_minus2';	
		$element7['slug'] = isset($post['f_slug7']) ? $post['f_slug7'] : 'vote_minus2';

		$element8 = array();
		$element8['title'] = isset($post['f_title8']) ? $post['f_title8'] : 'перенесенные сообщения пользователя';
		$element8['name'] = isset($post['f_name8']) ? $post['f_name8'] : 'Перенос';
		$element8['all'] = isset($post['f_all8']) ? $post['f_all8'] : 'Все перенесенные сообщения';
		$element8['title_go'] = isset($post['f_title_go8']) ? $post['f_title_go8'] : 'Перейти к сообщению в дискуссии';
		$element8['all_link'] = isset($post['f_all_link8']) ? $post['f_all_link8'] : '';
		$element8['img'] = isset($post['f_img8']) ? $post['f_img8'] : getinfo('plugins_url') . 'dialog/img/moved.png';
		$element8['filename'] = isset($post['f_filename8']) ? $post['f_filename8'] : 'moved';	
		$element8['slug'] = isset($post['f_slug8']) ? $post['f_slug8'] : 'moved';

		$element9 = array();
		$element9['title'] = isset($post['f_title9']) ? $post['f_title9'] : 'ответ на сообщение в дискуссии';
		$element9['name'] = isset($post['f_name9']) ? $post['f_name9'] : 'Ответы';
		$element9['all'] = isset($post['f_all9']) ? $post['f_all9'] : 'все ответы';
		$element9['title_go'] = isset($post['f_title_go9']) ? $post['f_title_go9'] : 'Перейти к сообщению в дискуссии';
		$element9['all_link'] = isset($post['f_all_link9']) ? $post['f_all_link9'] : '';
		$element9['img'] = isset($post['f_img9']) ? $post['f_img9'] : getinfo('plugins_url') . 'dialog/img/answer.png';
		$element9['filename'] = isset($post['f_filename9']) ? $post['f_filename9'] : 'answers';	
		$element9['slug'] = isset($post['f_slug9']) ? $post['f_slug9'] : 'answers';
														
		$elements =array($element1 , $element2 , $element3, $element4, $element5, $element6, $element7, $element8, $element9);
		mso_add_option($options_key, $elements, 'plugins');

    mso_flush_cache();
    		
		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
		
	}
	

	$form =  '<h3>'. t('Настройки модулей вывода событий'). '</h3><p class="info">'. t('Для работы с плагином profile.'). '</p>';

	$options = mso_get_option($options_key, 'plugins', array());
	
	$options[0] = isset($options[0]) ? $options[0] : array();
		$options[0]['title'] = isset($options[0]['title']) ? $options[0]['title'] : 'Сообщение в дискуссии';
		$options[0]['name'] = isset($options[0]['name']) ? $options[0]['name'] : 'Сообщение';
		$options[0]['all'] = isset($options[0]['all']) ? $options[0]['all'] : 'Все сообщения на форуме';
		$options[0]['title_go'] = isset($options[0]['title_go']) ? $options[0]['title_go'] : 'Перейти к сообщению в дискуссии';
		$options[0]['all_link'] = isset($options[0]['all_link']) ? $options[0]['all_link'] : 'forum/all-comments';
		$options[0]['img'] = isset($options[0]['img']) ? $options[0]['img'] : getinfo('plugins_url') . 'dialog/img/message.png';
		$options[0]['filename'] = isset($options[0]['filename']) ? $options[0]['filename'] : 'comments';			
		$options[0]['slug'] = isset($options[0]['slug']) ? $options[0]['slug'] : 'comments-forum';			
	
	$options[1] = isset($options[1]) ? $options[1] : array();
		$options[1]['title'] = isset($options[1]['title']) ? $options[1]['title'] : 'сказал спасибо за сообщение пользователю';
		$options[1]['name'] = isset($options[1]['name']) ? $options[1]['name'] : 'Благодарил';
		$options[1]['all'] = isset($options[1]['all']) ? $options[1]['all'] : 'Все благодарности';
		$options[1]['title_go'] = isset($options[1]['title_go']) ? $options[1]['title_go'] : 'Перейти к сообщению в дискуссии';
		$options[1]['all_link'] = isset($options[1]['all_link']) ? $options[1]['all_link'] : '';
		$options[1]['img'] = isset($options[1]['img']) ? $options[1]['img'] : getinfo('plugins_url') . 'dialog/img/danke[1].png';
		$options[1]['filename'] = isset($options[1]['filename']) ? $options[1]['filename'] : 'dankes';
		$options[1]['slug'] = isset($options[1]['slug']) ? $options[1]['slug'] : 'dankes';
		
	$options[2] = isset($options[2]) ? $options[2] : array();
		$options[2]['title'] = isset($options[2]['title']) ? $options[2]['title'] : 'получил благодарность от пользователя';
		$options[2]['name'] = isset($options[2]['name']) ? $options[2]['name'] : 'Получал благодарности';
		$options[2]['all'] = isset($options[2]['all']) ? $options[2]['all'] : 'Все полученные благодарности';
		$options[2]['title_go'] = isset($options[2]['title_go']) ? $options[2]['title_go'] : 'Перейти к сообщению в дискуссии';
		$options[2]['all_link'] = isset($options[2]['all_link']) ? $options[2]['all_link'] : '';
		$options[2]['img'] = isset($options[2]['img']) ? $options[2]['img'] : getinfo('plugins_url') . 'dialog/img/danke1.png';
		$options[2]['filename'] = isset($options[2]['filename']) ? $options[2]['filename'] : 'dankes1';
		$options[2]['slug'] = isset($options[2]['slug']) ? $options[2]['slug'] : 'dankes1';

	$options[3] = isset($options[3]) ? $options[3] : array();
		$options[3]['title'] = isset($options[3]['title']) ? $options[3]['title'] : 'получил положительную оценку сообщения от';
		$options[3]['name'] = isset($options[3]['name']) ? $options[3]['name'] : 'Положительно оцененные комментарии';
		$options[3]['all'] = isset($options[3]['all']) ? $options[3]['all'] : 'Все полученные положительные оценки';
		$options[3]['title_go'] = isset($options[3]['title_go']) ? $options[3]['title_go'] : 'Перейти к сообщению в дискуссии';
		$options[3]['all_link'] = isset($options[3]['all_link']) ? $options[3]['all_link'] : '';
		$options[3]['img'] = isset($options[3]['img']) ? $options[3]['img'] : getinfo('plugins_url') . 'dialog/img/vote_plus1.png';
		$options[3]['filename'] = isset($options[3]['filename']) ? $options[3]['filename'] : 'vote_plus1';
		$options[3]['slug'] = isset($options[3]['slug']) ? $options[3]['slug'] : 'vote_plus1';
	
	$options[4] = isset($options[4]) ? $options[4] : array();
		$options[4]['title'] = isset($options[4]['title']) ? $options[4]['title'] : 'положительно оценил сообщение пользователя';
		$options[4]['name'] = isset($options[4]['name']) ? $options[4]['name'] : 'Оценивал положительно';
		$options[4]['all'] = isset($options[4]['all']) ? $options[4]['all'] : 'Все положительные оценки';
		$options[4]['title_go'] = isset($options[4]['title_go']) ? $options[4]['title_go'] : 'Перейти к сообщению в дискуссии';
		$options[4]['all_link'] = isset($options[4]['all_link']) ? $options[4]['all_link'] : '';
		$options[4]['img'] = isset($options[4]['img']) ? $options[4]['img'] : getinfo('plugins_url') . 'dialog/img/vote_plus2.png';
		$options[4]['filename'] = isset($options[4]['filename']) ? $options[4]['filename'] : 'vote_plus2';	
		$options[4]['slug'] = isset($options[4]['slug']) ? $options[4]['slug'] : 'vote_plus2';	

	$options[5] = isset($options[5]) ? $options[5] : array();
		$options[5]['title'] = isset($options[5]['title']) ? $options[5]['title'] : 'получил отрицательную оценку сообщения от';
		$options[5]['name'] = isset($options[5]['name']) ? $options[5]['name'] : 'Отрицательно оцененные комментарии';
		$options[5]['all'] = isset($options[5]['all']) ? $options[5]['all'] : 'Все полученные отрицательные оценки';
		$options[5]['title_go'] = isset($options[5]['title_go']) ? $options[5]['title_go'] : 'Перейти к сообщению в дискуссии';
		$options[5]['all_link'] = isset($options[5]['all_link']) ? $options[5]['all_link'] : '';
		$options[5]['img'] = isset($options[5]['img']) ? $options[5]['img'] : getinfo('plugins_url') . 'dialog/img/vote_minus1.png';
		$options[5]['filename'] = isset($options[5]['filename']) ? $options[5]['filename'] : 'vote_minus1';	
		$options[5]['slug'] = isset($options[5]['slug']) ? $options[5]['slug'] : 'vote_minus1';	

	$options[6] = isset($options[6]) ? $options[6] : array();
		$options[6]['title'] = isset($options[6]['title']) ? $options[6]['title'] : 'отрицательно оценил сообщение пользователя';
		$options[6]['name'] = isset($options[6]['name']) ? $options[6]['name'] : 'Оценивал отрицательно';
		$options[6]['all'] = isset($options[6]['all']) ? $options[6]['all'] : 'Все отрицательные оценки';
		$options[6]['title_go'] = isset($options[6]['title_go']) ? $options[6]['title_go'] : 'Перейти к сообщению в дискуссии';
		$options[6]['all_link'] = isset($options[6]['all_link']) ? $options[6]['all_link'] : '';
		$options[6]['img'] = isset($options[6]['img']) ? $options[6]['img'] : getinfo('plugins_url') . 'dialog/img/vote_minus2.png';
		$options[6]['filename'] = isset($options[6]['filename']) ? $options[6]['filename'] : 'vote_minus2';	
		$options[6]['slug'] = isset($options[6]['slug']) ? $options[6]['slug'] : 'vote_minus2';	
		
	$options[7] = isset($options[7]) ? $options[7] : array();
		$options[7]['title'] = isset($options[7]['title']) ? $options[7]['title'] : 'перенесенные сообщения';
		$options[7]['name'] = isset($options[7]['name']) ? $options[7]['name'] : 'Перенесенные сообщения';
		$options[7]['all'] = isset($options[7]['all']) ? $options[7]['all'] : 'Все перенесенные сообщения';
		$options[7]['title_go'] = isset($options[7]['title_go']) ? $options[7]['title_go'] : 'Перейти к сообщению в дискуссии';
		$options[7]['all_link'] = isset($options[7]['all_link']) ? $options[7]['all_link'] : '';
		$options[7]['img'] = isset($options[7]['img']) ? $options[7]['img'] : getinfo('plugins_url') . 'dialog/img/moved.png';
		$options[7]['filename'] = isset($options[7]['filename']) ? $options[7]['filename'] : 'moved';	
		$options[7]['slug'] = isset($options[7]['slug']) ? $options[7]['slug'] : 'moved';	

	$options[8] = isset($options[8]) ? $options[8] : array();
		$options[8]['title'] = isset($options[8]['title']) ? $options[8]['title'] : 'ответ на сообщение в дискуссии';
		$options[8]['name'] = isset($options[8]['name']) ? $options[8]['name'] : 'Ответы';
		$options[8]['all'] = isset($options[8]['all']) ? $options[8]['all'] : 'Все ответы';
		$options[8]['title_go'] = isset($options[8]['title_go']) ? $options[8]['title_go'] : 'Перейти к сообщению в дискуссии';
		$options[8]['all_link'] = isset($options[8]['all_link']) ? $options[8]['all_link'] : '';
		$options[8]['img'] = isset($options[8]['img']) ? $options[8]['img'] : getinfo('plugins_url') . 'dialog/img/answer.png';
		$options[8]['filename'] = isset($options[8]['filename']) ? $options[8]['filename'] : 'answers';	
		$options[8]['slug'] = isset($options[8]['slug']) ? $options[8]['slug'] : 'answers';	
									
	$title_array = array(
	   t('Сообщения', 'plugins'),
	   t('Благодарил', 'plugins'),
	   t('Получал блогадарности', 'plugins'),	
	   t('Положительно оцененные комментарии', 'plugins'),	
	   t('Положительно оценивал комментарии', 'plugins'),	
	   t('Отрицательно оцененные комментарии', 'plugins'),	
	   t('Отрицательно оценивал комментарии', 'plugins'),	
	   t('Перенесенные сообщения', 'plugins'),	
	   t('Ответы', 'plugins'),	
	   
	);
	
		$form .= '<div class="error">' . t('Обязательно нужно сохранить эти настройки перед использованием.<br>По дефолту настройки подключаемых событий не инициализируются.', 'plugins') . '</div>';
		
	foreach ($options as $key=>$option)
	{
	  $form .= '<div class="admin_plugin_options">';
	  $key2 = $key+1;
	  $form .= '<h2>' . $key2 . ' ' . $title_array[$key] . '</h2>';
	  $form .= '<p><label><strong>' . t('title') . ' </strong><input name="f_title' . $key2 . '" type="text" value="' . $option['title'] . '" /></label></p>';
	  $form .= '<p><label><strong>' . t('name') . ' </strong><input name="f_name' . $key2 . '" type="text" value="' . $option['name'] . '" /></label></p>';
	  $form .= '<p><label><strong>' . t('all') . ' </strong><input name="f_all' . $key2 . '" type="text" value="' . $option['all'] . '" /></label></p>';
	  $form .= '<p><label><strong>' . t('title_go') . ' </strong><input name="f_title_go' . $key2 . '" type="text" value="' . $option['title_go'] . '" /></label></p>';
	  $form .= '<p><label><strong>' . t('all_link') . ' </strong><input name="f_all_link' . $key2 . '" type="text" value="' . $option['all_link'] . '" /></label></p>';
	  $form .= '<p><label><strong>' . t('img') . ' </strong><input name="f_img' . $key2 . '" type="text" value="' . $option['img'] . '" /></label></p>';
	  $form .= '<p><label><strong>' . t('filename') . ' </strong><input name="f_filename' . $key2 . '" type="text" value="' . $option['filename'] . '" /></label></p>';	 
	  $form .= '<p><label><strong>' . t('slug') . ' </strong><input name="f_slug' . $key2 . '" type="text" value="' . $option['slug'] . '" /></label></p>';	 
	  $form .= '</div>';
	}
		

	
	echo '<form class = "fform" action="" method="post">' . mso_form_session('f_session_id');
	echo $form;
	echo '<br><input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;"></form>';

