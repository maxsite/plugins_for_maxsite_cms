<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

function gotopbottom_autoload()
{
	mso_hook_add( 'head', 'gotopbottom_head');
	mso_hook_add( 'body_start', 'gotopbottom_body_top');
	mso_hook_add( 'body_end', 'gotopbottom_body_bottom');
}

function gotopbottom_uninstall($args = array())
{	
	mso_delete_option('plugin_gotopbottom', 'plugins'); // удалим созданные опции
	return $args;
}

function gotopbottom_head($args = array())
{
	$gtb_url = getinfo('plugins_url') . 'gotopbottom/';
	$options = mso_get_option('plugin_gotopbottom', 'plugins', array());
	if ( !isset($options['gtb_jq_speed']) ) $options['gtb_jq_speed'] = 800;
	if ( !isset($options['gtb_css_right']) ) $options['gtb_css_right'] = '6px';
	if ( !isset($options['gtb_css_top']) ) $options['gtb_css_top'] = '45%';
	if ( !isset($options['gtb_css_width']) ) $options['gtb_css_width'] = 32;
	if ( !isset($options['gtb_css_height']) ) $options['gtb_css_height'] = 32;
	if ( !isset($options['gtb_css_space']) ) $options['gtb_css_space'] = 20;
	
	$height = $options['gtb_css_height'] * 2 + $options['gtb_css_space'];
	$once = $options['gtb_css_height'];
	$twice = $options['gtb_css_height'] * 2;
	$thrice = $options['gtb_css_height'] * 3;
	
	mso_load_jquery();
	
	echo <<<GTB
	<script type="text/javascript">
	//<![CDATA[
	/**
	*‘Back to top’ link using jQuery 
	* Originally written by Lloyd http://agyuku.net/2009/05/back-to-top-link-using-jquery/
	* 
	* Scroll to top or bottom
	* Changed by jen on 2011-08-22 
	*
	* Opera fix: DynamicDrive script ::jQuery Scroll to Top Control v1.1
	* http://www.dynamicdrive.com/dynamicindex3/scrolltop.htm
	*/
	
	$(function() {
		if($.browser.opera && $('div#tooltip')) $('a#toTop, a#toBottom').removeAttr('title'); // temp. toggle for Opera
		var nav = $.browser.mozilla ? 1 : 0; // Fix for Gecko, where  document.height - (window.height + window.scrollTop) == 1px
		
		if($('body').height() > $(window).height()) {
		
			if($(this).scrollTop() == 0) $('#toTop').hide();
			$(window).scroll(function() {
				if($(this).scrollTop() > 0) 
					$('#toTop').fadeIn().click(function() {
						$(this).addClass('toTop-click');
					});
				if ($(this).scrollTop() == 0) 
					$('#toTop').fadeOut().removeClass('toTop-click').click(function() {
						$(this).removeClass('toTop-click');
					});
				if(($(this).scrollTop() + $(this).height() + nav) < $(document).height()) 
					$('#toBottom').fadeIn().click(function() {
						$(this).addClass('toBottom-click');
					});
				if (($(this).scrollTop() + $(this).height() + nav) >= $(document).height()) 
					$('#toBottom').fadeOut().removeClass('toBottom-click').click(function() {
						$(this).removeClass('toBottom-click');
					});
			});
			var mode = (window.opera) ? ((document.compatMode == "CSS1Compat") ? $('html') : $('body')) : $('html,body');
			
			$('#toTop').click(function() {
				mode.animate({scrollTop:0},{$options['gtb_jq_speed']});
				return false;
			});
			$('#toBottom').click(function() {
				mode.animate({scrollTop:$(document).height()},{$options['gtb_jq_speed']});
				return false;
			});
		}
		else $('#gtb_pos').css('display', 'none');
	});
	//]]>
	</script>
	<style type="text/css">
	/* Scroll to top or bottom */
	
	#gtb_pos {width: {$options['gtb_css_width']}px; height: {$height}px;}
	#gtb_pos {
		position: fixed;
		right: {$options['gtb_css_right']};
		top: {$options['gtb_css_top']};	
	}
	#gtb_top, #gtb_bottom, #toTop, #toBottom {
		position: absolute;
		left: 0;
		width: {$options['gtb_css_width']}px;
		height: {$options['gtb_css_height']}px;
	}
	#gtb_top {top: 0; background: url({$gtb_url}button.png) no-repeat left -{$thrice}px;}
	#gtb_bottom {bottom: 0; background: url({$gtb_url}button.png) no-repeat right -{$thrice}px;}
	#toTop {top: 0; background: url({$gtb_url}button.png) no-repeat left top;}
	#toBottom {bottom: 0; background: url({$gtb_url}button.png) no-repeat right top;}
	
	a#toTop, a#toBottom {outline: none;}
	a#toTop:hover, #toTop.toTop-click {background: url({$gtb_url}button.png) no-repeat left -{$once}px;}
	a#toTop:active {background: url({$gtb_url}button.png) no-repeat left -{$twice}px;}
	a#toBottom:hover, #toBottom.toBottom-click {background: url({$gtb_url}button.png) no-repeat right -{$once}px;}
	a#toBottom:active {background: url({$gtb_url}button.png) no-repeat right -{$twice}px;}
	</style>
	<!--[if lte IE 7]>
	<style type="text/css">
	a#toTop:active {background: url({$gtb_url}button.png) no-repeat left top;}
	a#toBottom:active {background: url({$gtb_url}button.png) no-repeat right top;}
	</style>
	<![endif]-->
	<!--[if IE 6]>
	<style type="text/css">
	html {
		background:url(about:blank);
		background-attachment: fixed;
	}
	#gtb_pos {
		position: absolute;
		top: expression(parseInt(document.documentElement.scrollTop + document.documentElement.clientHeight - this.offsetHeight, 10) -300 + "px");
	}
	</style>
	<![endif]-->
GTB;
}

function gotopbottom_body_top($arg = array())
{
	echo '<div id="top"></div>';
	return $arg;
}

function gotopbottom_body_bottom($arg = array())
{
	$out =
		'<div id="gtb_pos">' . NR .
		'<div id="gtb_top" class="png">' . NR .
		'<a id="toTop" class="png" href="' . getinfo('uri_get') . '#top" title="' . t('Наверх', 'plugins') . '"><span style="display: none;">' . t('&uArr; Наверх', 'plugins') . '</span></a>' . NR .
		'</div>' . NR .
		'<div id="gtb_bottom" class="png">' . NR .
		'<a id="toBottom" class="png" href="' . getinfo('uri_get') . '#bottom" title="' . t('Вниз', 'plugins') . '"><span style="display: none;">' . t('&dArr; Вниз', 'plugins') . '</span></a>' . NR .
		'</div>' . NR .
		'</div> <!-- /gtb_pos -->' . NR .
		'<div id="bottom"></div>' . NR;
	echo $out;
	return $arg;
}

function gotopbottom_mso_options() 
{
	mso_admin_plugin_options('plugin_gotopbottom', 'plugins', 
		array(
			'gtb_jq_speed' => array(
							'type' => 'text', 
							'name' => t('Скорость прокрутки', 'plugins'), 
							'description' => t('Время в <b>ms</b>, за которое страница прокручивается до верха. <b>Указывать только число!</b>', 'plugins'), 
							'default' => '800'
						),
			'gtb_css_right' => array(
							'type' => 'text', 
							'name' => t('Отступ блока кнопок от правой границы окна', 'plugins'), 
							'description' => t('Любые единицы, принятые в CSS. <b>Указывать число и единицу измерения!</b>', 'plugins'), 
							'default' => '6px'
						),
			'gtb_css_top' => array(
							'type' => 'text', 
							'name' => t('Вертиеальное положение блока кнопок', 'plugins'), 
							'description' => t('Любые единицы, принятые в CSS. <b>Указывать число и единицу измерения!</b>', 'plugins'), 
							'default' => '45%'
						),
			'gtb_explanation' => array(
							'type' => 'info', 
							'title' => t('Своя кнопка'), 
							'text' => t('<b>Если вы используете изображение кнопки по умолчанию, сохраните или вновь установите ширину и высоту 40(px).</b>
										<br>Для своей кнопки создайте спрайт и замените им оригинальный (<b>plugins/gotopbottom/toTop.png</b>).
										<br>Ширина спрайта = ширине кнопки. Высота, соответственно, равна высоте кнопки * 3.
										<br>Введите ниже ширину и высоту изображения своей кнопки.
										<br>Для <b>IE 6</b> необходимо обеспечить прозрачность PNG.<br><br>', 'plugins')
						),
			'gtb_css_width' => array(
							'type' => 'text', 
							'name' => t('Ширина кнопки', 'plugins') . ' (px)', 
							'description' => t('<b>Указывать только число!</b>', 'plugins'), 
							'default' => '32'
						),
			'gtb_css_height' => array(
							'type' => 'text', 
							'name' => t('Высота кнопки', 'plugins') . ' (px)', 
							'description' => t('<b>Указывать только число!</b>', 'plugins'), 
							'default' => '32'
						),
			'gtb_css_space' => array(
							'type' => 'text', 
							'name' => t('Вертикальное расстояние между кнопками', 'plugins'), 
							'description' => t('<b>Указывать только число!</b>', 'plugins'), 
							'default' => '20'
						)
			),
		'Настройки плагина gotopbottom', // титул
		'Укажите необходимые опции.'   // инфо
	);
}

?>