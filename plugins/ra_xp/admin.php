<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	$CI = & get_instance();
require_once( getinfo('common_dir') . 'category.php' );
	
	$options_key = 'ra_xp';
		$default=array(
		'ra_xp_profile'=>'',
		'ra_xp_host'=>'www.livejournal.com/interface/xmlrpc',
		'ra_xp_name'=>'',
		'ra_xp_pass'=>'',
		'ra_xp_community'=>'',
		'ra_xp_comments'=>'0',
		'ra_xp_comments_text'=>'',
		'ra_xp_status'=>'0',
		'ra_xp_header'=>'',
		'ra_xp_footer'=>'',
		'ra_xp_status_edit'=>'0',
		'ra_xp_status_del'=>'0',
		'ra_xp_comments_link'=>'0',
		'ra_xp_more'=>'cuta',
		'ra_xp_cut'=>'lj-cut',
		'ra_xp_cute'=>'Далее',
		'ra_xp_cats'=>array(),
		'ra_xp_metki_cat'=>'0',
		'ra_xp_metki_tag'=>'0',
		'ra_xp_privacy'=>'public');
	
		
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) ){
		$new=array_merge($default,$post);
		//pr($new);
		mso_checkreferer();
		//if($post['ra_xp_pass']=='') $post['ra_xp_pass']==$post['pass_old'];
		$options_all = mso_get_option($options_key, 'plugins', array());
		$options_all[$post['ra_xp_num']]=$new; // меняем часть опций
		mso_add_option($options_key, $options_all, 'plugins');
		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}elseif($post = mso_check_post(array('f_session_id', 'delete_profile'))){
		mso_checkreferer();
		$options_all = mso_get_option($options_key, 'plugins', array());
		unset($options_all[$post['ra_xp_num']]);
		if (isset($post['delete_profile_all_meta']) ) ra_xp_delete_profile_all_meta($options_all[$post['ra_xp_num']]); // передаем код профиля
		mso_add_option($options_key, $options_all, 'plugins');
		echo '<div class="update">' . t('Удалено!', 'plugins') . '</div>';
	}
	//pr($post);
?>
<h1><?= t('Плагин', 'plugins') ?></h1>
<p class="info"><a href=http://fo.com.ua><?= t('Отблагодарить автора', 'plugins') ?></a></p>

<?php

		echo '
		<script  type="text/javascript">
		$(document).ready(function(){ 
		$("table.page tr:even").addClass(\'alt\'); 
		});
		</script>';
		$num=0;
		

		$options_all = mso_get_option($options_key, 'plugins', array());
		
		//pr($options_all);
		if(!$options_all) $options_all=(array(0=>$default)); 
			//мульти-кросспостинг 
			else $options_all[]=$default; 
				
		foreach ($options_all as $options){
		if ( !isset($options['ra_xp_profile']) ) $options['ra_xp_profile'] = '';
		if ( !isset($options['ra_xp_num']) ) $options['ra_xp_num'] = $num+1;
		if ( !isset($options['ra_xp_host']) ) $options['ra_xp_host'] = 'www.livejournal.com/interface/xmlrpc'; 
		if ( !isset($options['ra_xp_name']) ) $options['ra_xp_name'] = '';
		if ( !isset($options['ra_xp_pass']) ) $options['ra_xp_pass'] = '';
		//$options['ra_xp_pass'] = '';
		if ( !isset($options['ra_xp_community']) ) $options['ra_xp_community'] = '';
		if ( !isset($options['ra_xp_comments']) ) $options['ra_xp_comments'] = '0';
		if ( !isset($options['ra_xp_comments_link']) ) $options['ra_xp_comments_link'] = '0';
		if ( !isset($options['ra_xp_comments_text']) ) $options['ra_xp_comments_text'] = 'Добавить комментарий';
		
		if ( !isset($options['ra_xp_status']) ) $options['ra_xp_status'] = '0';
		if ( !isset($options['ra_xp_header']) ) $options['ra_xp_header'] = '';
		if ( !isset($options['ra_xp_footer']) ) $options['ra_xp_footer'] = '';
		if ( !isset($options['ra_xp_status_edit']) ) $options['ra_xp_status_edit'] = '0';
		if ( !isset($options['ra_xp_status_del']) ) $options['ra_xp_status_del'] = '0';
		if ( !isset($options['ra_xp_more']) ) $options['ra_xp_more'] = 'cuta';
		if ( !isset($options['ra_xp_cats']) )$options['ra_xp_cats']=array();
		if ( !isset($options['ra_xp_cut']) ) $options['ra_xp_cut'] = 'lj-cut';
		if ( !isset($options['ra_xp_cute']) ) $options['ra_xp_cute'] = 'Далее';
		if ( !isset($options['ra_xp_privacy']) ) $options['ra_xp_privacy'] = 'public';
		if ( !isset($options['ra_xp_metki_cat']) ) $options['ra_xp_metki_cat'] = '0';
		if ( !isset($options['ra_xp_metki_tag']) ) $options['ra_xp_metki_tag'] = '0';
		
//		pr($options);
		$form='';
		$form .= '<h2>' . t('Настройки', 'plugins') . ' '.$options['ra_xp_num'].'</h2>';
		$form .= '<table class="page" width="99%" border="0">';
		$form .= '<tr><td><strong>Имя профиля</strong></td> ' . 
		' <td><input name="ra_xp_profile" type="text" value="' . $options['ra_xp_profile'] . '"></td></tr>';
		$form .= '<tr><td><strong>Статус по-умолчанию</strong><br> </td> ' . 
		' <td> 
				<input name="ra_xp_status" type="checkbox" value="1" '.ra_xp_checked($options['ra_xp_status'], 1).'/>
				Включить этот профиль<br>
				<input name="ra_xp_status_edit" type="checkbox" value="1" '.ra_xp_checked($options['ra_xp_status_edit'], 1).'/>
				Редактировать кросс-посты<br>
				<!-- input name="ra_xp_status_del" type="checkbox" value="1" '.ra_xp_checked($options['ra_xp_status_del'], 1).'/>
				<s>Удалять кросс-посты</s> Для удаления пользуйтесь ссылкой на странице редактирования.<br-->

				</td></tr>';
		$form .= '<tr><td><strong>Адрес interface/xmlrpc</strong></td> ' . 
		' <td><input name="ra_xp_host" type="text" value="' . $options['ra_xp_host'] . '"></td></tr>';
		$form .= '<tr><td><strong>Имя пользователя</strong> </td> ' . 
		' <td><input name="ra_xp_name" type="text" value="' . $options['ra_xp_name'] . '"></td></tr>';
		$form .= '<tr><td><strong>Пароль</strong> </td> ' . 
		' <td> <input name="ra_xp_pass" type="text" value="' . $options['ra_xp_pass'] . '">
		 <input name="pass_old" type="hidden" value="' . $options['ra_xp_pass'] . '">
		</td></tr>';
		$form .= '<tr><td><strong>Сообщество</strong> </td> ' . 
		' <td> <input name="ra_xp_community" type="text" value="' . $options['ra_xp_community'] . '"></td></tr>';
		$form .= '<tr><td><strong>Заголовок</strong> </td> ' . 
		' <td> <input name="ra_xp_header" type="text" value="' . htmlspecialchars($options['ra_xp_header']) . '"></td></tr>';
		$form .= '<tr><td><strong>Подвал</strong> </td> ' . 
		' <td> <input name="ra_xp_footer" type="text" value="' . htmlspecialchars($options['ra_xp_footer']) . '"></td></tr>';
		$form .= '<tr><td><strong>Оставлять комментарии?</strong><br> </td> ' . 
		' <td> 
				<input name="ra_xp_comments_link" type="checkbox" value="1" '.ra_xp_checked($options['ra_xp_comments_link'], 1).'/>
				Добавлять ссылку для комментирования на этом сайте <br>
				Текст ссылки 
				<input name="ra_xp_comments_text" type="text" value="'.$options['ra_xp_comments_text'].'" style="width:200px;"/> <br>
				<input name="ra_xp_comments" type="checkbox" value="1" '.ra_xp_checked($options['ra_xp_comments'], 1).'/>
				 Разрешить оставлять комментарии 
				</td></tr>';
		$form .= '<tr><td><strong>Уровень доступа к записям</strong><br> </td> ' . 
		' <td> 
				<input name="ra_xp_privacy" type="radio" value="public" '.ra_xp_checked($options['ra_xp_privacy'], 'public').'/>
				Публичный<br>
				<input name="ra_xp_privacy" type="radio" value="private" '.ra_xp_checked($options['ra_xp_privacy'], 'private').'/>
				Приватный<br>
				<input name="ra_xp_privacy" type="radio" value="friends" '.ra_xp_checked($options['ra_xp_privacy'], 'friends').'/>
				Для друзей
				</td></tr>';
		$form .= '<tr><td><strong>Как обрабатывать cut/xcut?</strong><br> </td> ' . 
		' <td> 
				<input name="ra_xp_more" type="radio" value="link" '.ra_xp_checked($options['ra_xp_more'], 'link').'/>
				 Ссылка на этот блог. Текст ссылки: <input name="ra_xp_cute" type="text" value="'.$options['ra_xp_cute'].'" style="width:100px;" /> <br>
				<input name="ra_xp_more" type="radio" value="cuta" '.ra_xp_checked($options['ra_xp_more'], 'cuta').'/>
				  Использовать lj-cut <input name="ra_xp_cut" type="text" value="'.$options['ra_xp_cut'].'" style="width:200px;" /><br>  
				  <input name="ra_xp_more" type="radio" value="copy" '.ra_xp_checked($options['ra_xp_more'], 'copy').'/>
				 Копировать всю запись <br>
				</td></tr>';
		$form .= '<tr><td><strong>Метки к записи</strong><br> </td> ' . 
		' <td> 
				<input name="ra_xp_metki_cat" type="checkbox" value="1" '.ra_xp_checked($options['ra_xp_metki_cat'], 1).'/>
				Использовать рубрики в метках<br>
				<input name="ra_xp_metki_tag" type="checkbox" value="1" '.ra_xp_checked($options['ra_xp_metki_tag'], 1).'/>
				Использовать метки в метках 
				</td></tr>';
		$all_cat = mso_cat_ul('<label><input name="ra_xp_cats[]" 
		type="checkbox" %CHECKED% value="%ID%" title="id = %ID%"> %NAME%</label>', true, $options['ra_xp_cats'], array());
		$form .= '<tr><td><strong>Рубрики для кросспостинга</strong> </td> ' . 
		' <td> '.$all_cat.'</td></tr>';
		$form .= '</table>';
		
		echo '<form action="" method="post"> <input name="ra_xp_num" type="hidden" value="' . $options['ra_xp_num'] . '">' . mso_form_session('f_session_id');
		echo $form;
		echo '<input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;">';
		echo '<input type="submit" name="delete_profile" value="' . t('Удалить профиль', 'plugins') . '" style="margin: 25px 0 5px 0;">';
		echo '</form>';
			//$i++;
			$num=$options['ra_xp_num'];
		}
//pr($options_all);
?>