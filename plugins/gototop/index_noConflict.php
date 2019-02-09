<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

function gototop_autoload()
{
	mso_hook_add( 'head', 'gototop_head');
	mso_hook_add( 'body_start', 'gototop_body_top');
	mso_hook_add( 'body_end', 'gototop_body_bottom');
}

function gototop_uninstall($args = array())
{	
	mso_delete_option('plugin_gototop', 'plugins'); // удалим созданные опции
	return $args;
}

function gototop_head($args = array())
{
	$gtt_url = getinfo('plugins_url') . 'gototop/';
	$options = mso_get_option('plugin_gototop', 'plugins', array());
	if ( !isset($options['gtt_jq_top']) ) $options['gtt_jq_top'] = 160;
	if ( !isset($options['gtt_jq_speed']) ) $options['gtt_jq_speed'] = 800;
	if ( !isset($options['gtt_css_right']) ) $options['gtt_css_right'] = '10px';
	if ( !isset($options['gtt_css_bottom']) ) $options['gtt_css_bottom'] = '10px';
	if ( !isset($options['gtt_css_width']) ) $options['gtt_css_width'] = 40;
	if ( !isset($options['gtt_css_height']) ) $options['gtt_css_height'] = 40;
	
	$once = $options['gtt_css_height'];
	$twice = $options['gtt_css_height'] * 2;
	
	mso_load_jquery();
	echo NR . '
	<script type="text/javascript">
	/**
	* script by Lloyd http://agyuku.net/2009/05/back-to-top-link-using-jquery/
	* changed on 2011-03-12 by jen http://jenweb.info
	*
	* Opera fix: DynamicDrive script ::jQuery Scroll to Top Control v1.1
	* http://www.dynamicdrive.com/dynamicindex3/scrolltop.htm
	*/
	
	jQuery.noConflict();
	jQuery(function($) {
		$(\'#toTop\').hide();
		$(window).scroll(function() {
			if($(this).scrollTop() >= ' . $options['gtt_jq_top'] . ') {
				$(\'#toTop\').fadeIn().click(function() {
					$(this).addClass(\'toTop-click\');
				});
			} else {
				if($(window).scrollTop() == 0) {
					$(\'#toTop\').removeClass(\'toTop-click\');
				}
				$(\'#toTop\').fadeOut();
			}
		});
		var mode = (window.opera) ? ((document.compatMode == "CSS1Compat") ? $(\'html\') : $(\'body\')) : $(\'html,body\');
		$(\'#toTop\').click(function() {
			mode.animate({scrollTop:0},' . $options['gtt_jq_speed'] . ');
			return false;
		});
	});
	</script>
	<style type="text/css">
	#toTop {
		position: fixed;
		right: ' . $options['gtt_css_right'] . ';
		bottom: ' . $options['gtt_css_bottom'] . ';
		width: ' . $options['gtt_css_width'] . 'px;
		height: ' . $options['gtt_css_height'] . 'px;
		background: url(' . $gtt_url . 'toTop.png) no-repeat left top;
		outline: none;
	}
	a#toTop:hover, #toTop.toTop-click {background: url(' . $gtt_url . 'toTop.png) no-repeat left -' . $once . 'px;}
	a#toTop:active {background: url(' . $gtt_url . 'toTop.png) no-repeat left -' . $twice . 'px;}
	</style>
	' . NR;
}

function gototop_body_top($arg = array())
{
	echo '<div id="top"></div>';
	return $arg;
}

function gototop_body_bottom($arg = array())
{
	echo '<p><a id="toTop" href="' . getinfo('uri_get') . '#top" 
		title="' . t('Наверх', 'plugins') . '"><span style="display: none;">' . t('&#x21E7; Наверх', 'plugins') . '</span></a></p>';
	return $arg;
}

function gototop_mso_options() 
{
	mso_admin_plugin_options('plugin_gototop', 'plugins', 
		array(
			'gtt_jq_top' => array(
							'type' => 'text', 
							'name' => t('Верхняя граница появления кнопки', 'plugins'), 
							'description' => t('Расстояние в <b>px</b> до верха окна, когда кнопка появляется (от 0 и больше). <b>Указывать только число!</b>', 'plugins'), 
							'default' => '160'
						),
			'gtt_jq_speed' => array(
							'type' => 'text', 
							'name' => t('Скорость прокрутки', 'plugins'), 
							'description' => t('Время в <b>ms</b>, за которое страница прокручивается до верха. <b>Указывать только число!</b>', 'plugins'), 
							'default' => '800'
						),
			'gtt_css_right' => array(
							'type' => 'text', 
							'name' => t('Отступ кнопки от правой границы окна', 'plugins'), 
							'description' => t('Любые единицы, принятые в CSS. <b>Указывать число и единицу измерения!</b>', 'plugins'), 
							'default' => '10px'
						),
			'gtt_css_bottom' => array(
							'type' => 'text', 
							'name' => t('Отступ кнопки от низа окна', 'plugins'), 
							'description' => t('Любые единицы, принятые в CSS. <b>Указывать число и единицу измерения!</b>', 'plugins'), 
							'default' => '10px'
						),
			'gtt_explanation' => array(
							'type' => 'info', 
							'title' => t('Своя кнопка'), 
							'text' => t('<b>Если вы используете изображение кнопки по умолчанию, сохраните или вновь установите ширину и высоту 40(px).</b>
										<br>Для своей кнопки создайте спрайт и замените им оригинальный (<b>plugins/gototop/toTop.png</b>).
										<br>Ширина спрайта = ширине кнопки. Высота, соответственно, равна высоте кнопки * 3.
										<br>Введите ниже ширину и высоту изображения своей кнопки.
										<br>Для <b>IE 6</b> необходимо обеспечить прозрачность PNG.<br><br>', 'plugins')
						),
			'gtt_css_width' => array(
							'type' => 'text', 
							'name' => t('Ширина кнопки', 'plugins') . ' (px)', 
							'description' => t('<b>Указывать только число!</b>', 'plugins'), 
							'default' => '40'
						),
			'gtt_css_height' => array(
							'type' => 'text', 
							'name' => t('Высота кнопки', 'plugins') . ' (px)', 
							'description' => t('<b>Указывать только число!</b>', 'plugins'), 
							'default' => '40'
						)
			),
		'Настройки плагина gototop', // титул
		'Укажите необходимые опции.'   // инфо
	);
}

?>