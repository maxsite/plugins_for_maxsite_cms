<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	global $MSO;
	$CI = & get_instance();
	
	$options_key = 'googlitics';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
            mso_checkreferer();
		
            $options = array();
            $options['uastring'] = $post['f_uastring'];
            $options['dlextensions'] = strtolower($post['f_dlextensions']);
            $options['dlprefix'] = strtolower($post['f_dlprefix']);
            $options['outprefix'] = strtolower($post['f_outprefix']);
            $options['domainorurl'] = $post['f_domainorurl'];
            $options['trackoutbound'] = isset($post['f_trackoutbound']) ? TRUE : FALSE;
            $options['extrase'] = isset($post['f_extrase']) ? TRUE : FALSE;
            $options['trackadmin'] = isset($post['f_trackadmin']) ? TRUE : FALSE;
            $options['async'] = isset($post['f_async']) ? TRUE : FALSE;
            $options['datefrom'] = strtolower($post['f_datefrom']);
            $options['numcountries'] = intval($post['f_numcountries']);
            $options['secretkey'] = str_replace(' ', '', strval($post['f_secretkey']));

            mso_add_option($options_key, $options, 'plugins');
            echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}

        //echo mso_load_jquery();
        
?>
<script type="text/javascript" src="<?php echo getinfo('plugins_url'); ?>googlitics/googlitics.js"></script>
<h1><?= t('Googlitics', 'plugins') ?></h1>
<a href="#" id="toggle_info" >Показать справку</a>
<div id="info" style="display: none;">
    <p class="info"><?= t('Плагин для подключения Google Analytics (подобно Google Analytics for Wordpress)', 'plugins') ?></p>
    <p class="info"><?= t('Плагин подключает скрипт Google Analytics в хидер (при асинхронной загрузке) или футер вашего сайта, а также позволяет отслеживать клики посетителей по исходящим ссылкам и скачивания, задавать варианты их отображения в вашем аккаунте. Имеется возможность не отслеживать действия администратора, чтобы не искажать статистику. <br />Имеется возможность просмотра отчетов о количестве посещений за последний месяц, помесячно, а также топ стран посетителей (за основу был взят код проекта <a href="http://code.google.com/p/statga/" target="_blank">Statga</a>)', 'plugins') ?></p>
    <p class="info"><?= t('В текущей версии отслеживаются клики по ссылкам, расположенным только в основном информационном содержании сайта. Не учитываются клики по ссылкам в виджетах, в футере и т.п.', 'plugins') ?></p>
	<p class="info"><?= t('ПОЯСНЕНИЕ К НЕКОТОРЫМ ПОЛЯМ ФОРМЫ<br /><b>Асинхронная загрузка скрипта</b> подключает вместо обычного скрипта статистики внизу страницы другой, находящийся в секции head, и за счет этого увеличивает точность отслеживания статистики. Однако в данном плагине не получилось гарантировать все условия безопасного для других плагинов размещения скрипта (в самом конце секции head), поэтому если после включения асинхронной статистики что-то перестанет работать, выключите ее<br /><b>Секретный ключ для сокрытия статистики
от постороннего просмотра</b> это строка из латинских букв и цифр без пробелов или каких-либо других символов, которая делает названия сохраненных файлов статистики уникальными для вашей системы и тем самым препятствует стороннему просмотру (если вам это не надо -- оставьте пустым). Со временем можно менять этот ключ, но тогда не забывайте удалять старые версии файлов из каталога uploads', 'plugins') ?></p>
    <p class="info"><?= t('ВНИМАНИЕ: если вы работаете со ссылочными биржами, размещая ссылки от них в основном содержимом сайта, то опцию "Отслеживать клики на исходящие ссылки и скачивания" следует ВЫКЛЮЧИТЬ', 'plugins') ?></p>
    <p class="info"><?= t('<a href="http://kupreev.com/page/maxsitecms-plugin-googlitics">Страница плагина</a> в блоге автора', 'plugins') ?></p>
</div>
<?php
	$options = mso_get_option($options_key, 'plugins', array());
	if ( !isset($options['uastring']) ) $options['uastring'] = ''; 
        if ( !isset($options['dlextensions']) ) $options['dlextensions'] = 'doc,exe,pdf,ppt,tgz,zip,xls,mp3'; 
        if ( !isset($options['dlprefix']) ) $options['dlprefix'] = '/downloads'; 
        if ( !isset($options['outprefix']) ) $options['outprefix'] = '/outbound'; 
        if ( !isset($options['domainorurl']) )
        {
            $seldomain = 'selected="true"';
            $selurl = '';
        } else {
            if ($options['domainorurl'] == 'domain')
            {
                $seldomain = 'selected="true"';
                $selurl = '';
            } else {
                $seldomain = '';
                $selurl = 'selected="true"';
            }
        }
        $chckout = '';
        if ( !isset($options['trackoutbound']) OR (bool)$options['trackoutbound'] )
        {
            $chckout = 'checked="true"';
        }
        $chckse = '';
        if ( !isset($options['extrase']) OR (bool)$options['extrase'] )
        {
            $chckse = 'checked="true"';
        }
        $chckadm = '';
        if ( isset($options['trackadmin']) AND (bool)$options['trackadmin'] )
        {
            $chckadm = 'checked="true"';
        }
        $chckasync = '';
        if ( isset($options['async']) AND (bool)$options['async'] )
        {
            $chckasync = 'checked="true"';
        }
        if ( !isset($options['datefrom']) ) $options['datefrom'] = '2008-05-01';
        if ( !isset($options['numcountries']) ) $options['numcountries'] = '3';
        if ( !isset($options['secretkey']) ) $options['secretkey'] = '';
        
	$form = '';
	$form .= '<tr align="center"><td colspan="2"><em>' . t('Настройки отслеживания статистики', 'plugins') . '</em></td></tr>';
	$form .= '<tr><td><strong>' . t('Аккаунт в Google Analytics:', 'plugins') . '</strong> </td>' . '<td> <input name="f_uastring" type="text" value="' . $options['uastring'] . '"></td></tr>';
	$form .= '<tr><td><strong>' . t('Отслеживать скачивание файлов с расширениями:<br /><small>если расширение состоит из двух символов, добавьте <br />перед ним точку (например, ".js")</small>', 'plugins') . '</strong></td> ' . '<td> <input name="f_dlextensions" type="text" value="' . $options['dlextensions'] . '"></td></tr>';                                                                      
        $form .= '<tr><td><strong>' . t('Префикс для отслеживаемых скачиваний:', 'plugins') . '</strong></td> ' . '<td> <input name="f_dlprefix" type="text" value="' . $options['dlprefix'] . '"></td></tr>';                                                                      
        $form .= '<tr><td><strong>' . t('Префикс для отслеживаемых кликов по ссылкам <br />на внешние ресурсы:', 'plugins') . '</strong></td> ' . '<td> <input name="f_outprefix" type="text" value="' . $options['outprefix'] . '"></td></tr>';                                                                      
        $form .= '<tr><td><strong>' . t('Отслеживать полный URL исходящих ссылок <br />или только домен?', 'plugins') . '</strong></td> ' . '<td> <select name="f_domainorurl"><option value="domain" '.$seldomain.'>Только домен</option><option value="url" '.$selurl.'>Полный URL</option></select></td></tr>';                                                                      
        $form .= '<tr><td><strong>' . t('Отслеживать клики на исходящие ссылки и скачивания', 'plugins') . '</strong></td> ' . '<td> <input name="f_trackoutbound" type="checkbox" '.$chckout.' /></td></tr>';                                                                      
        $form .= '<tr><td><strong>' . t('Отслеживать дополнительные поисковые машины', 'plugins') . '</strong></td> ' . '<td> <input name="f_extrase" type="checkbox" '.$chckse.' /></td></tr>';
        $form .= '<tr><td><strong>' . t('Отслеживать администратора', 'plugins') . '</strong></td> ' . '<td> <input name="f_trackadmin" type="checkbox" '.$chckadm.' /></td></tr>';
        $form .= '<tr><td><strong>' . t('Асинхронная загрузка скрипта', 'plugins') . '</strong></td> ' . '<td> <input name="f_async" type="checkbox" '.$chckasync.' /></td></tr>';
        $form .= '<tr align="center"><td colspan="2"><em>Настройки показа статистики</em></td></tr>';
        $form .= '<tr><td><strong>' . t('Дата начала показа статистики:<br /><small>Формат YYYY-MM-DD</small>', 'plugins') . '</strong></td><td> <input name="f_datefrom" type="text" value="' . $options['datefrom'] . '"></td></tr>';
        $form .= '<tr><td><strong>' . t('Количество учитываемых стран', 'plugins') . '</strong></td><td> <input name="f_numcountries" type="text" value="' . $options['numcountries'] . '"></td></tr>';
        $form .= '<tr><td><strong>' . t('Секретный ключ для сокрытия статистики<br />от постороннего просмотра <small>(латинские буквы и цифры)</small>', 'plugins') . '</strong></td><td> <input name="f_secretkey" type="text" value="' . $options['secretkey'] . '"></td></tr>';

	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo '<table>';
        echo $form;
        echo '</table>';
	echo '<input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;" />';
	echo '</form>';
?>
<hr />
<div id="srv_msg">
</div>
<br>
<div >
    <form id="manual_load" action="" method="post"><?php echo mso_form_session('f_session_id'); ?>
        Для ручного обновления статистики нажмите <input type="button" id="load_stats" value="Загрузить статистику" ref="<?php echo getinfo('ajax').base64_encode('plugins/googlitics/loadstats-ajax.php'); ?>" />
    </form>
</div>
<hr />
<a href="#" id="stats" >Показать статистику</a>
<div id="stat_graphs" style="display: none;">
    <script type="text/javascript" src="<?php echo getinfo('plugins_url'); ?>googlitics/swfobject.js"></script>

    <div id="visitors" align="center" style="padding-bottom:80px">
        <strong>Для просмотра содержимого установите последнюю версию Adobe Flash Player</strong>
    </div>

<script type="text/javascript">
    // <![CDATA[
    var so = new SWFObject("<?php echo getinfo('plugins_url');?>googlitics/amline.swf", "amline_chart", "630", "500", "8", "#FFFFFF");
    so.addVariable("path", "./amline/");
    so.addVariable("settings_file", escape("<?php echo getinfo('plugins_url'); ?>googlitics/visitors_settings.xml?<?php echo mktime();?>"));
    so.addVariable("data_file", escape("<?php echo getinfo('uploads_url'); ?>visitors<?php echo $options['secretkey'];?>.csv?<?php echo mktime();?>"));
    so.addVariable("preloader_color", "#BBBBBB");
    so.write("visitors");
    // ]]>
</script>

    <div id="visitors_3" align="center" style="padding-bottom:80px">
	<strong>Для просмотра содержимого установите последнюю версию Adobe Flash Player</strong>
    </div>

<script type="text/javascript">
    // <![CDATA[
    var so = new SWFObject("<?php echo getinfo('plugins_url'); ?>googlitics/amline.swf", "amline_chart", "600", "400", "8", "#FFFFFF");
    so.addVariable("path", "./amline/");
    so.addVariable("settings_file", escape("<?php echo getinfo('plugins_url'); ?>googlitics/visitors_3_settings.xml?<?php echo mktime();?>"));
    so.addVariable("data_file", escape("<?php echo getinfo('uploads_url'); ?>visitors_3<?php echo $options['secretkey'];?>.csv?<?php echo mktime();?>"));
    so.addVariable("preloader_color", "#BBBBBB");
    so.write("visitors_3");
    // ]]>
</script>

    <div id="country" align="center" style="padding-bottom:80px">
	<strong>Для просмотра содержимого установите последнюю версию Adobe Flash Player</strong>
    </div>

<script type="text/javascript">
    // <![CDATA[
    var so = new SWFObject("<?php echo getinfo('plugins_url'); ?>googlitics/ampie.swf", "ampie_chart", "550", "350", "8", "#FFFFFF");
    so.addVariable("path", "./ampie/");
    so.addVariable("settings_file", escape("<?php echo getinfo('plugins_url'); ?>googlitics/country_settings.xml?<?php echo mktime();?>"));
    so.addVariable("data_file", escape("<?php echo getinfo('uploads_url'); ?>country<?php echo $options['secretkey'];?>.csv?<?php echo mktime();?>"));
    so.addVariable("preloader_color", "#BBBBBB");
    so.write("country");
    // ]]>
</script>
</div>