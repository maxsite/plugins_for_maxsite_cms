<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	global $MSO;
	mso_cur_dir_lang(__FILE__);
	$CI = & get_instance();

	$options_key = 'jwplayer';
	

	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		$options = array();
		$options['width']         = isset( $post['f_width'])         ? $post['f_width']         : '400';
		$options['height'] = isset( $post['f_height'])         ? $post['f_height']         : '300';
		$options['plugins'] = isset( $post['f_plugins'])         ? $post['f_plugins']         : 'viral-2';
		$options['scin'] = isset( $post['f_scin'])         ? $post['f_scin']         : '' . getinfo('plugins_url') . 'jwplayer/jwplayer.swf';

		mso_add_option($options_key, $options, 'plugins');

		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}


	echo '<h1>'. t('Плагин jwplayer. Версия 4.0'). '</h1><p class="info">'. t('Плагин jwplayer от <a href="http://max-cms.com/">max-cms.com</a>. Этот плагин является усовершенствоавнной версией старого плагина JW Videoplayer. <br>Плагин разработан для <a href="http://max-3000.com/">MaxSite CMS</a>.<br>Плагин использует JW player от <a href="http://www.longtailvideo.com/">Long Tail</a>, все права на который принадлежат разработчикам. Чтобы использовать в плагине самые последние версии этого плеера, Вам необходимо скачать его на официальном сайте разработчика. В скачанном архиве находим файл player, копируем в папку плагина jwplayer и переименовываем этот файл в jwplayer.<br>Если у Вас что-то не получается, возникли какие-то вопросы - обращайтесь на сайт разработчика плагина <a href="http://max-cms.com/">max-cms.com</a>.'). '</p>';

	$options = mso_get_option($options_key, 'plugins', array());
	$options['width']         = isset($options['width'])         ?      $options['width']         : '400';
	$options['height'] = isset($options['height']) ? $options['height'] : '300';
	$options['plugins']    = isset($options['plugins'])    ? $options['plugins']    : 'viral-2';
	$options['scin']    = isset($options['scin'])    ? $options['scin']    : '' . getinfo('plugins_url') . 'jwplayer/jwplayer.swf';
	
	$form = '';

	$form .= '<h2>' . t('Настройки', 'plugins') . '</h2>';

	$form .= '<p><strong>' . t('Ширина.') . '</strong><br><label><input name="f_width" type="text" value="' . $options['width'] . '"></label><br />';
	$form .= t('Ширина окна видеоплеера в пикселях.'). '</p>';

	$form .= '<p><strong>' . t('Высота.') . '</strong><br><label><input name="f_height" type="text" value ="' . $options['height'] . '"></label><br />';
	$form .= t('Высота окна видеоплеера в пикселях.'). '</p>';
	
	$form .= '<p><strong>' . t('Плагины JW.') . '</strong><br><label><input name="f_plugins" type="text" value ="' . $options['plugins'] . '"></label><br />';
	$form .= t('Вы можете задействовать различные существующие для JW плеера плагины. Их много, полный список доступен <noindex><a href="http://www.longtailvideo.com/addons/plugins" rel="nofollow">здесь</a><noindex>.<br> Для того, чтобы использовать несколько плагинов, введите их значения через запятую. Имейте в виду, что некоторые плагины могут морально устареть или даже конфликтовать с другими. Также отдельные плагины можно настраивать, добавляя коды настроек.<br> Вот несколько наиболее популярных и удобных значений:<br><strong>viral-2,embed-1,rateit-2.</strong>.'). '</p>';

	$form .= '<p><strong>' . t('URL обложки.') . '</strong><br><label><input name="f_scin" type="text" value ="' . $options['scin'] . '"></label><br />';
	$form .= t('Для JW плеера разработано несколько десятков разнообразных скинов (шкурок, тем). Вы можете скачать любой из них на офсайте плеера, залить куда пожелаете и использовать. Для этого нужно будет указать в настройках полный url к файлу скина.'). '</p>';

	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo $form;
	echo '<br /><input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;" />';
	echo '</form>';
	
	echo '<a href="http://max-cms.com/"><img src="' . getinfo('plugins_url') . 'jwplayer/logo.png"></a>';
	echo '<h2>' . t('Благодарности разработчику, авторские права', 'plugins') . '</h2>';
	echo '<p>Разработкой этого плагина занимается Парфенов М. С.. Другие мои плагины для MaxSite CMS, а также модификации уже существующих плагинов и авторские шаблоны для MaxSite CMS Вы найдете на моем сайте <a href="http://max-cms.com/">max-cms.com</a>.</p>';
    echo '<p>Плагин совершенно бесплатен. Вы можете распространять этот плагин, изменять и модифицировать любые файлы этого плагина при условии сохранения указания авторства и ссылки на сайт разработчкика. Коммерческое использование плагина, а именно - продажа, модификация с целью продажи, возможны по предварительной договоренности с разработчиком.</p>';
    echo '<p>Для того, чтобы я и далее мог заниматься разработкой этого и других полезных плагинов, мне поможет не столько Ваше "спасибо", сколько прямая индексируемая ссылка на сайт max-cms.com в Вашем блоге или небольшая финансовая помощь.<br>Мои веб-мани кошельки для Вашей денежной благодарности:</p>';
    echo '<p><ul><li>Z242955976890</li><li>R312863831559</li><li>E122471955757</li></ul></p>';

?>