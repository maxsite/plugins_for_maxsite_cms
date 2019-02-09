<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// Это расширение можно скачать на страничке http://askname.ru/page/bookmarks_jquery
// Ссылки для соц.сервисов и закладок на фреймворке jquery.
// Создатель плагина - Иван Александрович Чалый ( UmFal ) 
// Оригинальный jquery - Дмитрий Лялин ( dimox.name ), 
// Адаптирован к MaxSite на основе кода от Алексея Баринова ( driverlab.ru )

	global $MSO;
	$CI = & get_instance();
	$optionb_key = 'plugin_bookmarks_jquery';
	
	if ( $post = mso_check_post(array('f_session_id','f_submit')) )
	{
		mso_checkreferer();

		$options = array();
		$options['100zakladok.ru'] = isset($post['b_100zakladok_ru']) ? 1 : 0;
		$options['delicious.com'] = isset($post['b_delicious_com']) ? 1 : 0;
		$options['google.com'] = isset($post['b_google_com']) ? 1 : 0;
		$options['bobrdobr.ru'] = isset($post['b_bobrdobr_ru']) ? 1 : 0;
		$options['links.i.ua'] = isset($post['b_links_i_ua']) ? 1 : 0;
		$options['memori.ru'] = isset($post['b_memori_ru']) ? 1 : 0;
		$options['moemesto.ru'] = isset($post['b_moemesto_ru']) ? 1 : 0;
		$options['mister-wong.ru'] = isset($post['b_mister_wong_ru']) ? 1 : 0;
		$options['linkstore.ru'] = isset($post['b_linkstore_ru']) ? 1 : 0;
		$options['lopas.ru'] = isset($post['b_lopas_ru']) ? 1 : 0;
		$options['myscoop.ru'] = isset($post['b_myscoop_ru']) ? 1 : 0;
		$options['ruspace.ru'] = isset($post['b_ruspace_ru']) ? 1 : 0;
		$options['vaau.ru'] = isset($post['b_vaau_ru']) ? 1 : 0;

		$options['badabadu.com'] = isset($post['b_badabadu_com']) ? 1 : 0;
		$options['chipp.ru'] = isset($post['b_chipp_ru']) ? 1 : 0;
		$options['feedblog.ru'] = isset($post['b_feedblog_ru']) ? 1 : 0;
		$options['korica.info'] = isset($post['b_korica_info']) ? 1 : 0;
		$options['monrate.ru'] = isset($post['b_monrate_ru']) ? 1 : 0;
		$options['news2.ru'] = isset($post['b_news2_ru']) ? 1 : 0;
		$options['newsland.ru'] = isset($post['b_newsland_ru']) ? 1 : 0;
		$options['sloger.net'] = isset($post['b_sloger_net']) ? 1 : 0;

		mso_add_option($optionb_key, $options, 'plugins');
		echo '<div class="update">Обновлено!</div>';
	}
?>
<h1 style="margin-bottom:10px">Настройка Bookmarks</h1>
<script type="text/javascript">
$(document).ready( function() {
	$("a[href='#select_all']").click( function() {
		$("#" + $(this).attr('rel') + " input[type='checkbox']").attr('checked', true);
		return false;
	});

	$("a[href='#select_none']").click( function() {
		$("#" + $(this).attr('rel') + " input[type='checkbox']").attr('checked', false);
		return false;
	});

	$("a[href='#invert_selection']").click( function() {
		$("#" + $(this).attr('rel') + " input[type='checkbox']").each( function() {
		$(this).attr('checked', !$(this).attr('checked'));
	});
		return false;
		}); 
});
</script>
<?php
	$options = mso_get_option($optionb_key, 'plugins', array());
	if ( !isset($options['100zakladok.ru']) ) $options['100zakladok.ru'] = true;
	if ( !isset($options['delicious.com']) ) $options['delicious.com'] = true;
	if ( !isset($options['google.com']) ) $options['google.com'] = true;
	if ( !isset($options['bobrdobr.ru']) ) $options['bobrdobr.ru'] = true;
	if ( !isset($options['links.i.ua']) ) $options['links.i.ua'] = true;
	if ( !isset($options['memori.ru']) ) $options['memori.ru'] = true;
	if ( !isset($options['moemesto.ru']) ) $options['moemesto.ru'] = true;
	if ( !isset($options['mister-wong.ru']) ) $options['mister-wong.ru'] = true;
	if ( !isset($options['linkstore.ru']) ) $options['linkstore.ru'] = true;
	if ( !isset($options['lopas.ru']) ) $options['lopas.ru'] = true;
	if ( !isset($options['myscoop.ru']) ) $options['myscoop.ru'] = true;
	if ( !isset($options['ruspace.ru']) ) $options['ruspace.ru'] = true;
	if ( !isset($options['vaau.ru']) ) $options['vaau.ru'] = true;

	$checked_100zakladok_ru = $options['100zakladok.ru'] ? ' checked="checked" ' : '';
	$checked_delicious_com = $options['delicious.com'] ? ' checked="checked" ' : '';
	$checked_google_com = $options['google.com'] ? ' checked="checked" ' : '';
	$checked_bobrdobr_ru = $options['bobrdobr.ru'] ? ' checked="checked" ' : '';
	$checked_links_i_ua = $options['links.i.ua'] ? ' checked="checked" ' : '';
	$checked_memori_ru = $options['memori.ru'] ? ' checked="checked" ' : '';
	$checked_moemesto_ru = $options['moemesto.ru'] ? ' checked="checked" ' : '';
	$checked_mister_wong_ru = $options['mister-wong.ru'] ? ' checked="checked" ' : '';
	$checked_linkstore_ru = $options['linkstore.ru'] ? ' checked="checked" ' : '';
	$checked_lopas_ru = $options['lopas.ru'] ? ' checked="checked" ' : '';
	$checked_myscoop_ru = $options['myscoop.ru'] ? ' checked="checked" ' : '';
	$checked_ruspace_ru = $options['ruspace.ru'] ? ' checked="checked" ' : '';
	$checked_vaau_ru = $options['vaau.ru'] ? ' checked="checked" ' : '';

	if ( !isset($options['badabadu.com']) ) $options['badabadu.com'] = true;
	if ( !isset($options['chipp.ru']) ) $options['chipp.ru'] = true;
	if ( !isset($options['feedblog.ru']) ) $options['feedblog.ru'] = true;
	if ( !isset($options['korica.info']) ) $options['korica.info'] = true;
	if ( !isset($options['monrate.ru']) ) $options['monrate.ru'] = true;
	if ( !isset($options['news2.ru']) ) $options['news2.ru'] = true;
	if ( !isset($options['newsland.ru']) ) $options['newsland.ru'] = true;
	if ( !isset($options['sloger.net']) ) $options['sloger.net'] = true;

	$checked_badabadu_com = $options['badabadu.com'] ? ' checked="checked" ' : '';
	$checked_chipp_ru = $options['chipp.ru'] ? ' checked="checked" ' : '';
	$checked_feedblog_ru = $options['feedblog.ru'] ? ' checked="checked" ' : '';
	$checked_korica_info = $options['korica.info'] ? ' checked="checked" ' : '';
	$checked_monrate_ru = $options['monrate.ru'] ? ' checked="checked" ' : '';
	$checked_news2_ru = $options['news2.ru'] ? ' checked="checked" ' : '';
	$checked_newsland_ru = $options['newsland.ru'] ? ' checked="checked" ' : '';
	$checked_sloger_net = $options['sloger.net'] ? ' checked="checked" ' : '';


	$path = getinfo('plugins_url').'bookmarks_jquery/s/';
	$form = '<a rel="b_checkbox" href="#select_all">Выделить все</a> | 
<a rel="b_checkbox" href="#select_none">Отменить выделение</a> | 
<a rel="b_checkbox" href="#invert_selection">Инвертировать выделение</a>';

	$form .= '<h3>Добавить в закладки</h3>';
	$form .= '<p title="100zakladok.ru" style="margin-top:10px"><label><img src="'.$path.'100zakladok.ru.ico" /> <input name="b_100zakladok_ru" type="checkbox"'.$checked_100zakladok_ru.'> 100zakladok.ru</label></p>';
	$form .= '<p title="delicious.com"><label><img src="'.$path.'delicious.com.ico" /> <input name="b_delicious_com" type="checkbox"'.$checked_delicious_com.'> delicious.com</label></p>';
	$form .= '<p title="google.com"><label><img src="'.$path.'google.com.ico" /> <input name="b_google_com" type="checkbox"'.$checked_google_com.'> google.com</label></p>';
	$form .= '<p title="bobrdobr.ru"><label><img src="'.$path.'bobrdobr.ru.ico" /> <input name="b_bobrdobr_ru" type="checkbox"'.$checked_bobrdobr_ru.'> bobrdobr.ru</label></p>';
	$form .= '<p title="links.i.ua"><label><img src="'.$path.'links.i.ua.ico" /> <input name="b_links_i_ua" type="checkbox"'.$checked_links_i_ua.'> links.i.ua</label></p>';
	$form .= '<p title="memori.ru"><label><img src="'.$path.'memori.ru.ico" /> <input name="b_memori_ru" type="checkbox"'.$checked_memori_ru.'> memori.ru</label></p>';
	$form .= '<p title="moemesto.ru"><label><img src="'.$path.'moemesto.ru.ico" /> <input name="b_moemesto_ru" type="checkbox"'.$checked_moemesto_ru.'> moemesto.ru</label></p>';
	$form .= '<p title="mister-wong.ru"><label><img src="'.$path.'mister-wong.ru.ico" /> <input name="b_mister_wong_ru" type="checkbox"'.$checked_mister_wong_ru.'> mister-wong.ru</label></p>';
	$form .= '<p title="linkstore.ru"><label><img src="'.$path.'linkstore.ru.ico" /> <input name="b_linkstore_ru" type="checkbox"'.$checked_linkstore_ru.'> linkstore.ru</label></p>';
	$form .= '<p title="lopas.ru"><label><img src="'.$path.'lopas.ru.ico" /> <input name="b_lopas_ru" type="checkbox"'.$checked_lopas_ru.'> lopas.ru</label></p>';
	$form .= '<p title="myscoop.ru"><label><img src="'.$path.'myscoop.ru.ico" /> <input name="b_myscoop_ru" type="checkbox"'.$checked_myscoop_ru.'> myscoop.ru</label></p>';
	$form .= '<p title="ruspace.ru"><label><img src="'.$path.'ruspace.ru.ico" /> <input name="b_ruspace_ru" type="checkbox"'.$checked_ruspace_ru.'> ruspace.ru</label></p>';
	$form .= '<p title="vaau.ru"><label><img src="'.$path.'vaau.ru.ico" /> <input name="b_vaau_ru" type="checkbox"'.$checked_vaau_ru.'> vaau.ru</label></p>';

	$form .= '<h3>Добавить в социальные сервисы</h3>';
	$form .= '<p title="badabadu.com"><label><img src="'.$path.'badabadu.com.ico" /> <input name="b_badabadu_com" type="checkbox"'.$checked_badabadu_com.'> badabadu.com</label></p>';
	$form .= '<p title="chipp.ru"><label><img src="'.$path.'chipp.ru.ico" /> <input name="b_chipp_ru" type="checkbox"'.$checked_chipp_ru.'> chipp.ru</label></p>';
	$form .= '<p title="feedblog.ru"><label><img src="'.$path.'feedblog.ru.ico" /> <input name="b_feedblog_ru" type="checkbox"'.$checked_feedblog_ru.'> feedblog.ru</label></p>';
	$form .= '<p title="korica.info"><label><img src="'.$path.'korica.info.ico" /> <input name="b_korica_info" type="checkbox"'.$checked_korica_info.'> korica.info</label></p>';
	$form .= '<p title="monrate.ru"><label><img src="'.$path.'monrate.ru.ico" /> <input name="b_monrate_ru" type="checkbox"'.$checked_monrate_ru.'> monrate.ru</label></p>';
	$form .= '<p title="news2.ru"><label><img src="'.$path.'news2.ru.ico" /> <input name="b_news2_ru" type="checkbox"'.$checked_news2_ru.'> news2.ru</label></p>';
	$form .= '<p title="newsland.ru"><label><img src="'.$path.'newsland.ru.ico" /> <input name="b_newsland_ru" type="checkbox"'.$checked_newsland_ru.'> newsland.ru</label></p>';
	$form .= '<p title="sloger.net"><label><img src="'.$path.'sloger.net.ico" /> <input name="b_sloger_net" type="checkbox"'.$checked_sloger_net.'> sloger.net</label></p>';

	echo '<form action="" method="post" id="b_checkbox">'.mso_form_session('f_session_id');
	echo $form;
	echo '<input type="submit" name="f_submit" value=" Сохранить изменения " style="margin:15px 0 5px" />';
	echo '</form>';
echo <<<END
<h3>Немного об авторах</h3>
<p>Создатель плагина - Иван Александрович Чалый ( <a href="http://AskName.Ru">AskName.Ru</a> ) </p>
<p>Оригинальный jquery - Дмитрий Лялин ( <a href="http://dimox.name">dimox.name</a> ), </p>
<p>Адаптирован к MaxSite на основе кода от Алексея Баринова ( <a href="http://driverlab.ru">DRiVERlab.ru</a> )</p>
<p>Это расширение можно скачать на страничке <a href="http://askname.ru/page/bookmarks_jquery/">askname.ru/page/bookmarks_jquery</a></p>
END;
?>