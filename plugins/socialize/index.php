<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * Ruslan Gaysin
 * (c) http://www.rgblog.ru/
 */

if(!defined('IMG_URL')) define('IMG_URL', getinfo('plugins_url') . 'socialize/img/');

function socialize_autoload($args = array())
{
	mso_create_allow('socialize_edit', 'Админ-доступ к настройкам плагина Socialize');
	mso_hook_add( 'admin_init', 'socialize_admin_init');
	if ( is_type('page') )
	{
		mso_hook_add('head', 'socialize_head');
		mso_hook_add('content_end', 'socialize_content_end');
	}
}

function socialize_uninstall($args = array())
{	
	mso_delete_option('plugin_socialize', 'plugins');
	return $args;
}

function socialize_admin_init($args = array()) 
{
	if ( !mso_check_allow('socialize_edit') ) 
	{
		return $args;
	}
	$this_plugin_url = 'plugin_socialize';
	mso_admin_menu_add('plugins', $this_plugin_url, 'Socialize');
	mso_admin_url_hook($this_plugin_url, 'socialize_admin_page');
	return $args;
}

function socialize_admin_page($args = array()) 
{
	if ( !mso_check_allow('socialize_admin_page') ) 
	{
		echo 'Доступ запрещен';
		return $args;
	}
	
	mso_hook_add_dinamic('mso_admin_header', ' return $args."Socialize"; ' );
	mso_hook_add_dinamic('admin_title', ' return "Socialize - ".$args; ' );
	require(getinfo('plugins_dir') . 'socialize/admin.php');
}

function socialize_mso_options() 
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_socialize', 'plugins', 
		array(
			'size' => array(
							'type' => 'select', 
							'name' => 'Размеры иконок:', 
							'description' => 'Выберите размеры иконок сервисов',
							'values' => '16 # 24 # 32',
							'default' => '32'
						),
			'mypanel' => array(
							'type' => 'select', 
							'name' => 'Тип панели с иконками:', 
							'description' => 'вертикальная «плавающая» или горизонтальная (под текстом поста/страницы)', 
							'values'	=>	'floating||Плавающая # gorizontal||Горизонтальная',	
							'default' => 'floating'
						),
			'panelfloat' => array(
							'type' => 'select', 
							'name' => 'Выравнивание:', 
							'description' => 'Расположение панели с кнопками относительно шаблона страницы.', 
							'values'	=>	'left||Слева # right||Справа',
							'default' => 'left'
						),	
								
			'mtop' => array(
							'type' => 'text', 
							'name' => 'Отступ сверху:', 
							'description' => 'Отступ в пикселях от верхнего края видимой области экрана.', 
							'default' => '472'
						),					
			'mside' => array(
							'type' => 'text', 
							'name' => 'Боковой отступ:', 
							'description' => 'Отступ в пикселях от левого края шаблона с текстом поста (страницы). При выравнивании справа к установленному значению автоматически прибавляется 900 пикселей. Возможны отрицательные значения.', 
							'default' => '-80'
						),
			'hblogo' => array(
							'type' => 'select', 
							'name' => 'Добавить кнопку «Скачать плагин Socialize It!»: ', 
							'description' => 'Будет полезна посетителям вашего блога, которые пожелают установить себе такой же плагин. Добавляется после всех иконок.', 
							'values'	=>	'yes||Да # no||Нет',
							'default' => 'no'
						),
			),
		'Плавающие закдадки на соц.сервисы', // титул
		'Укажите необходимые опции.'   // инфо
	);
}

# хеад
function socialize_head($arg = array())
{
	global $page;
	$options = mso_get_option('plugin_socialize', 'plugins', array());
	if (!isset($options['mypanel']) or !$options['mypanel']) $options['mypanel'] = 'floating'; 
	if ($options['mypanel'] == 'floating') $css = 'css/hb.css';
	else $css = 'css/hb_gorizontal.css';
	echo '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'socialize/' . $css . '" type="text/css" media="screen">';
	return $arg;
}

function compile_buttons()
{
	require_once('config.php');
	$buttons = '';
	$options = mso_get_option('plugin_socialize', 'plugins', array());
	if (!isset($options['icons'])) $options['icons'] = '';
	if (!isset($options['hblogo'])) $options['hblogo'] = 'no';
	if (!isset($options['size'])) $options['size'] = '32';
	if (!isset($options['mypanel'])) $options['mypanel'] = 'floating';
	if (!isset($options['mside'])) $options['mside'] = '-80';
	if (!isset($options['mtop'])) $options['mtop'] = '472';
	if (!isset($options['panelfloat'])) $options['panelfloat'] = 'left';

	$myicons = $options['icons'];
	$hblogo = $options['hblogo'];
	$mysize = $options['size'];
	$panelfloat = $options['panelfloat'];
	
	$step = 8;

	$blocksqty = ceil(count($myicons)/$step);				
	if ($mysize == 32) {
		$hbw = $mysize*14;
		$hblogo_position = '-96px -224px';
	}
	if ($mysize == 24) {
		$hbw = $mysize*15;
		$hblogo_position = '-72px -168px';
	}
	if ($mysize == 16) {
		$hbw = $mysize*16;
		$hblogo_position = '-48px -112px';
	}
	$hblogo_url = 'http://www.rgblog.ru/page/socialize/';
	$mypanel = $options['mypanel'];
	if ($mypanel == 'floating')	{
			if ($options['mside']) $mymside = $options['mside'];
			else $mymside = 0;
			if ($options['mtop']) $mymtop = $options['mtop'];
			else $mymtop = 0;
			if ($panelfloat == 'right') $mymside += 900;
			$buttons .= "<script type=\"text/javascript\">HBSocializeIt=function(u,t){var mleft=".$mymside.";var m1=".$mymtop.";var m2=50;jQuery(document).ready(function(){var s=jQuery('#socializeit');s.css({top: m1,\"margin-left\": mleft});function margin(){var top=jQuery(window).scrollTop();if(top+m2<m1){s.css({top: m1-top});}else{s.css({top: m2});}}jQuery(window).scroll(function(){margin();});";
			if ($blocksqty > 1) :
			$buttons .= "s.append('<div id=\"hb_prev\" class=\"hbmore\" title=\"Назад\" style=\"background:url(".IMG_URL."up.png) no-repeat;width:12px;height:7px;margin:3px auto 6px;\"></div>');";
			endif;
			$i=0; while ($i < $blocksqty) : 
			$first = $step*$i;
			$myicons[$i] = array_slice($myicons, $first, $step);
			foreach ($myicons[$i] as $k) :
			foreach ($services as $key=>$val) :
			if ($k == $key) :
			$buttons .= "s.append('<div class=\"hbblock".$i."\"><a rel=\"nofollow\" href=\"".$val['url']."\"";
			if (isset($val['onclick'])) $buttons .= " onclick=\"".$val['onclick']."\"";
			$buttons .= " title=\"".$val['title']."\" style=\"width:".$mysize."px;height:".$mysize."px;\"><div id=\"".$key."\" style=\"background-image:url(".IMG_URL."icons-".$mysize.".png);background-repeat:no-repeat;background-position:".$val[$mysize].";width:".$mysize."px;height:".$mysize."px;\"></div></a></div>');";
			endif;
			endforeach;
			endforeach;
			if ($i>0) :
			$buttons .= "jQuery('.hbblock".$i."').hide();jQuery('.hbmore".$i."').hide();";
			endif;
			$i++; endwhile;
			if ($hblogo == 'yes') {
			$buttons .= "s.append('<div class=\"hbblock".$i."\"><a rel=\"nofollow\" href=\"".$hblogo_url."\"";
			$buttons .= " style=\"width:".$mysize."px;height:".$mysize."px;\"><div id=\"".$key."\" style=\"background-image:url(".IMG_URL."icons-".$mysize.".png);background-repeat:no-repeat;background-position:".$hblogo_position.";width:".$mysize."px;height:".$mysize."px;\"></div></a></div>');";
			}
			if ($blocksqty > 1) :
			$buttons .= "s.append('<div id=\"hb_next\" class=\"hbmore\" title=\"Больше\" style=\"background:url(".IMG_URL."down.png) no-repeat;width:12px;height:7px;margin:6px auto 8px;\"></div>');";
			endif;
			$buttons .= "s.find('a').attr({target: '_blank'}).css({opacity: 0.5}).hover(function(){jQuery(this).css({opacity: 1});},function(){jQuery(this).css({opacity: 0.7});});s.hover(function(){jQuery(this).find('a').css({opacity: 0.7});},function(){jQuery(this).find('a').css({opacity: 0.5});});jQuery('.hbmore').css({opacity: 0.5}).hover(function(){jQuery(this).css({opacity: 0.8});},function(){jQuery(this).css({opacity: 0.5});});jQuery('#hb_prev').hide();var hbcounter=0;var blocksqty=".$blocksqty.";";
			$buttons .= "jQuery('#hb_next').click(function(){jQuery('.hbblock'+hbcounter).animate({height: 'hide'},300);hbcounter++;jQuery('.hbblock'+hbcounter).animate({height: 'show'},300);if(hbcounter>0){jQuery('#hb_prev').show();}if (hbcounter==blocksqty-1){jQuery('#hb_next').hide();}});";
			$buttons .= "jQuery('#hb_prev').click(function(){jQuery('.hbblock'+hbcounter).animate({height: 'hide'},300);hbcounter--;jQuery('.hbblock'+hbcounter).animate({height: 'show'},300);if(hbcounter==0){jQuery('#hb_prev').hide();}if(hbcounter>=0){jQuery('#hb_next').show();}});})";
			$buttons .= "}</script>";
		}
		else {
			$buttons .= "<script type=\"text/javascript\">HBSocializeIt=function(u,t){jQuery(document).ready(function(){var s=jQuery('#socializeit');var hbnav=jQuery('#hbnav');";
			if ($blocksqty > 1) :
			$buttons .= "hbnav.append('<div id=\"hb_prev\" class=\"hbmore\" title=\"Назад\" style=\"float:left;\">... Назад</div>');";
			endif;
			$i=0; while ($i < $blocksqty) : 
			$first = $step*$i;
			$myicons[$i] = array_slice($myicons, $first, $step);
			foreach ($myicons[$i] as $k) :
			foreach ($services as $key=>$val) :
			if ($k == $key) :
			$buttons .= "s.append('<div class=\"hbblock".$i." hbholder\" style=\"display:inline-block;margin:0;padding:0;\"><a rel=\"nofollow\" href=\"".$val['url']."\"";
			if (isset($val['onclick'])) $buttons .= " onclick=\"".$val['onclick']."\"";
			$buttons .= " title=\"".$val['title']."\" style=\"width:".$mysize."px;height:".$mysize."px;\"><div id=\"".$key."\" style=\"background-image:url(".IMG_URL."icons-".$mysize.".png);background-repeat:no-repeat;background-position:".$val[$mysize].";width:".$mysize."px;height:".$mysize."px;margin:0 3px;\"></div></a></div>');";
			endif;
			endforeach;
			endforeach;
			if ($i>0) :
			$buttons .= "jQuery('.hbblock".$i."').hide();jQuery('.hbmore".$i."').hide();";
			endif;
			$i++; endwhile;
			if ($hblogo == 'yes') {
			$buttons .= "s.append('<div class=\"hbblock".$i." hbholder\" style=\"display:inline-block;margin:0;padding:0 0 0 3px;\"><a rel=\"nofollow\" href=\"".$hblogo_url."\"";
			$buttons .= " style=\"width:".$mysize."px;height:".$mysize."px;\"><div id=\"".$key."\" style=\"background-image:url(".IMG_URL."icons-".$mysize.".png);background-repeat:no-repeat;background-position:".$hblogo_position.";width:".$mysize."px;height:".$mysize."px;\"></div></a></div>');";
			}
			if ($blocksqty > 1) :
			$buttons .= "hbnav.append('<div id=\"hb_next\" class=\"hbmore\" title=\"Больше\" style=\"float:right;\">Больше ...</div>');";
			endif;
			$buttons .= "s.find('a').attr({target:'_blank'});jQuery('.hbholder').css({opacity:0.5}).hover(function(){jQuery(this).css({opacity:1});},function(){jQuery(this).css({opacity:0.7});});s.hover(function(){jQuery(this).find('a').css({opacity:0.7});},function(){jQuery(this).find('a').css({opacity:0.5});});jQuery('.hbmore').css({opacity:0.5}).hover(function(){jQuery(this).css({opacity: 0.8});},function(){jQuery(this).css({opacity:0.5});});jQuery('#hb_prev').hide();var hbcounter=0;var blocksqty=".$blocksqty.";jQuery('#hb_next').click(function(){jQuery('.hbblock'+hbcounter).animate({width:'hide'},300);hbcounter++;jQuery('.hbblock'+hbcounter).animate({width:'show'},300);if(hbcounter>0){jQuery('#hb_prev').show();}if(hbcounter==blocksqty-1){jQuery('#hb_next').hide();}});jQuery('#hb_prev').click(function(){jQuery('.hbblock'+hbcounter).animate({width:'hide'},300);hbcounter--;jQuery('.hbblock'+hbcounter).animate({width:'show'},300);if(hbcounter==0){jQuery('#hb_prev').hide();}if(hbcounter>=0){jQuery('#hb_next').show();}});});";
			$buttons .= "}</script>";
		}
		
		return $buttons;
		
}

# функции плагина
function socialize_content_end($args = array())
{
	global $page;

	echo NR . '<div id="socializeit"></div>' . NR;
	echo NR . compile_buttons() . NR;
	$post_title = urlencode ( stripslashes($page['page_title'] . ' - ' . mso_get_option('name_site', 'general') ) );
	$post_link = getinfo('siteurl') . mso_current_url();
	echo '<script type="text/javascript">HBSocializeIt(\''.$post_link.'\',\''.$post_title.'\');</script>';
	
	return $args;
}

# end file
