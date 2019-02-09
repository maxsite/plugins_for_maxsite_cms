<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * Ruslan Gaysin
 * (c) http://www.rgblog.ru/
 */
if(!defined('IMG_URL')) define('IMG_URL', getinfo('plugins_url') . 'socialize/img/');
require_once('config.php');
$CI = & get_instance();
$options_key = 'plugin_socialize';
	
if ( $post = mso_check_post(array('s_size','s_mypanel','s_panelfloat','s_mtop','s_mside','s_logo','s_icons')) )
{
	mso_checkreferer();
		
	$options = array();
	$options['size'] = $post['s_size'];
	$options['mypanel'] = $post['s_mypanel'];
	$options['panelfloat'] = $post['s_panelfloat'];
	$options['mtop'] = $post['s_mtop'];
	$options['mside'] = $post['s_mside'];
	$options['hblogo'] = $post['s_logo'];
	$hbicons = urldecode($post['s_icons']);
	$m1 = explode('&',$hbicons);
	$myicons = array();
	foreach($m1 as $key=>$val) {
		$myicons[] = str_replace('s_icons=','',$val);
	}
	$options['icons'] = $myicons;
	mso_add_option($options_key, $options, 'plugins');
	echo '1';
	exit;
}
$CI = & get_instance();
$CI->load->helper('form');
	$options = mso_get_option($options_key, 'plugins', array());
	if ( !isset($options['size']) ) $options['size'] = '32';
	if ( !isset($options['mypanel']) ) $options['mypanel'] = 'floating';
	if ( !isset($options['panelfloat']) ) $options['panelfloat'] = 'left';
	if ( !isset($options['mtop']) ) $options['mtop'] = '472';
	if ( !isset($options['mside']) ) $options['mside'] = '-80';
	if ( !isset($options['hblogo']) ) $options['hblogo'] = '0';
	if ( !isset($options['icons']) ) $options['icons'] = '';
	$mysize = (int)$options['size'];
	$myicons = $options['icons'];
	$mypanel = $options['mypanel'];
	$panelfloat = $options['panelfloat'];
	$mtop = $options['mtop'];
	$mside = $options['mside'];
	$hblogo = $options['hblogo'];
?>
<script type="text/javascript" src="<?=getinfo('plugins_url')?>socialize/js/jquery-ui-1.8.13.custom.min.js"></script>
<link rel="stylesheet" href="<?=getinfo('plugins_url')?>socialize/css/adminhundredbuttons.css" type="text/css" media="screen">
<link rel="stylesheet" href="<?=getinfo('plugins_url')?>socialize/css/admin.css" type="text/css" media="screen">
<script type='text/javascript'>
jQuery(document).ready(function() {
	
	var imgpath = '<?php echo IMG_URL; ?>';
	var size = <?php if (!$mysize) echo '32'; else echo $mysize; ?>;
	var input = jQuery('#icons ol li input');
		
	jQuery('#all').click(function() {
		if (jQuery('#icons li input:checked').length > 0) {
			input.each(function() {
				jQuery(this).attr({checked: false}).parents('li').removeClass('selected');
			})
		} else {
			input.each(function() {
				jQuery(this).attr({checked: true}).parents('li').addClass('selected');
			})	
		}
	})

	jQuery('#invert').click(function() {
		input.each(function() {
			if (jQuery(this).is(':checked')) {
				jQuery(this).attr({checked: false}).parents('li').removeClass('selected');
			} else {
				jQuery(this).attr({checked: true}).parents('li').addClass('selected');
			}
		})	
	})

	jQuery('#icons ol').sortable({
		placeholder: 'ui-state-highlight'
	});
	
	jQuery('#icons ol').disableSelection();

	input.change(function() {
		jQuery(this).parents('li').toggleClass('selected');
	});
	
	jQuery('#size li').click(function() {
		size = jQuery(this).attr('id').replace('s', '');
		jQuery('#icons, #size li').removeClass();
		jQuery(this).addClass('current');
		
		jQuery('#icons li div').each(function() {
			
			var bgpos = '0 0';
			
			<?php foreach ($services as $key=>$val) : ?>
			if (jQuery(this).attr('id') == '<?php echo $key; ?>') {
				if (size == 32) bgpos = '<?php echo $val['32']; ?>';
				if (size == 24) bgpos = '<?php echo $val['24']; ?>';
				if (size == 16) bgpos = '<?php echo $val['16']; ?>';
			}
			<?php endforeach; ?>
			
			jQuery(this).css ( { 
				width:size, 
				height:size,
				"background-image":'url(' + imgpath + 'icons-' + size + '.png)',
				"background-position":bgpos,
			});

		})

	})
	function error() {
		jQuery('#error').remove();
		jQuery('#hbsocializesubmit').after('<div id="error">Надо выбрать хотя бы одну иконку!</div>');
		jQuery('#error').hide().fadeIn().delay(5000).fadeOut(750, function() { jQuery(this).remove(); });
	}
	
	function msg(settingsmsg) {
		jQuery('#respond').remove();
		jQuery('#hbsocializesubmit').after('<div id="respond">' + settingsmsg + '</div>');
		jQuery('#respond').hide().fadeIn().delay(2000).fadeOut(750, function() { jQuery(this).remove(); });
	}
	
	jQuery('#hbsocializesubmit').click(function() {
		if (jQuery('#icons input:checked').length < 1) {
			error();
			return false;
		} else {
			jQuery('input[name="size"]').val(size);
			var settingsmsg = '';
			var hblogo = 'yes';
			if (jQuery('input[name="s_logo"]:checked').val()) hblogo = 'yes';
			else hblogo = 'no';

			var data = {
				action: 'submithb',
				s_size: size,
				s_icons: jQuery('input[name="s_icons"]:checked').serialize(),
				s_mypanel: jQuery('input[name="s_mypanel"]:checked').val(),
				s_panelfloat: jQuery('select[name="s_panelfloat"] option:selected').val(),
				s_mtop: jQuery('input[name="s_mtop"]').val(),
				s_mside: jQuery('input[name="s_mside"]').val(),
				s_logo: hblogo,
				hb_admin_wpnonce: jQuery('input[name="hb_admin_wpnonce"]').val(),
			};

			jQuery.post('<?=getinfo('siteurl')?>admin/plugin_socialize', data, function(response) {
				if (response == 1) {
					settingsmsg = "Ваши настройки успешно сохранены!";
				}
				else {
					settingsmsg = "Неудалось сохранить настройки - попробуйте сделать это позже :(";
				}
				msg(settingsmsg);
			});

			return false;
		}
	})
});
</script>
<h1>Настройка Socialize</h1>
<h2>Выберите размер и отметьте иконки сервисов, которые хотите использовать на своем блоге:</h2>
<div id="icons">
	<p class="info">Всего сервисов: <?php echo count($services); ?></p>
	<ul id="size">
		<li id="s32"<?php if ($mysize == 32 || !$mysize) echo ' class="current"'; ?>>32x32</li>
		<li id="s24"<?php if ($mysize == 24) echo ' class="current"'; ?>>24x24</li>
		<li id="s16"<?php if ($mysize == 16) echo ' class="current"'; ?>>16x16</li>
	</ul>
	<span id="all">Выбрать/снять все</span> <span id="invert">Инвертировать выделение</span>
	<span class="tooltip" style="font-weight:bold;" title="Порядок иконок можно менять, перетаскивая их мышью.">!</span>
	<ol id="dynamic">
		<?php foreach ($services as $key=>$val) : ?>
		<li<?php if (is_array($myicons)) { foreach ($myicons as $icn) : if ($icn == $key) echo ' class="selected"'; endforeach; } ?>><label for="<?php echo $key;?>"><input type="checkbox" name="s_icons" value="<?php echo $key;?>" id="<?php echo $key;?>" <?php if (is_array($myicons)) { foreach ($myicons as $icn) : if ($icn == $key) echo 'checked="checked"'; endforeach; } ?> /><div id="<?php echo $key;?>" style="background-image:url(<?php echo IMG_URL; ?>icons-<?php echo $mysize; ?>.png);background-repeat:no-repeat;background-position:<?php echo $val[$mysize];?>;width:<?php echo $mysize; ?>px;height:<?php echo $mysize; ?>px;"></div><?php echo $val['name'];?></label></li>
		<?php endforeach; ?>
	</ol>
	<div id="hiddens"></div>
</div>
<h2>Выберите нужные опции</h2>
<ul id="options">
			<li><strong>Тип панели с иконками:</strong>
				<label><input id="option-floating" type="radio" name="s_mypanel" value="floating"<?php if ($mypanel == 'floating' || !$mypanel) echo ' checked="checked"'; ?> /> вертикальная «плавающая»</label>
				<label><input id="option-gorizontal" type="radio" name="s_mypanel" value="gorizontal"<?php if ($mypanel == 'gorizontal') echo ' checked="checked"'; ?> /> горизонтальная (под текстом поста/страницы)</label> 
			</li>
			<li id="panel-mtop"<?php if ($mypanel == 'gorizontal') echo ' style="display:none;visibility:hidden;"'; ?>><strong>Отступ сверху: <span class="tooltip" title="Отступ в пикселях от верхнего края видимой области экрана.">?</span></strong>
				<label><input type="text" name="s_mtop" style="margin-left:8px;width:75px;" value="<?php if (!$mtop) echo 0; else echo $mtop; ?>" /> px</label>
			</li>
			<li id="panel-float-li"<?php if ($mypanel == 'gorizontal') echo ' style="display:none;visibility:hidden;"'; ?>><strong>Выравнивание: <span class="tooltip" title="Расположение панели с кнопками относительно шаблона страницы.">?</span></strong>
				<label><select id="panelfloat" name="s_panelfloat" style="margin-left:8px;width:85px;"><option value="left"<?php if (!$panelfloat || $panelfloat == 'left') echo ' selected="selected"'; ?>>слева</option><option value="right"<?php if ($panelfloat == 'right') echo ' selected="selected"'; ?>>справа</option></select></label>
			</li>
			<li id="panel-mside"<?php if ($mypanel == 'gorizontal') echo ' style="display:none;visibility:hidden;"'; ?>><strong>Боковой отступ: <span class="tooltip" title="Отступ в пикселях от левого края шаблона с текстом поста (страницы). При выравнивании справа к установленному значению автоматически прибавляется 900 пикселей. Возможны отрицательные значения.">?</span></strong>
				<label><input type="text" name="s_mside" style="margin-left:8px;width:75px;" value="<?php if (!$mside) echo 0; else echo $mside; ?>" /> px</label>
			</li>
			<li><label><strong><?php echo 'Добавить кнопку «Скачать плагин Socialize!»:'; ?>: <span class="tooltip" title="Будет полезна посетителям вашего блога, которые пожелают установить себе такой же плагин. Добавляется после всех иконок.">?</span></strong><input type="checkbox" id="hblogo" name="s_logo"<?php if ($hblogo == 'yes' || !$hblogo) echo ' checked="checked"'; ?> value="yes" style="margin-left:13px;" /></label></li>
</ul>
<form action="" method="post"><?=mso_form_session('f_session_id');?>
<a href="#" id="hbsocializesubmit">Сохранить настройки</a>
</form>
<br />
<div id="hbcopyright">Дизайн плагина основан на скрипте кнопок социальных закладок и сетей <a href="http://share42.com/" target="_blank"><img src="<?php echo IMG_URL; ?>share42_logo.png" alt="share42" style="border:none;padding:7px 0 0 0" /></a> (автор скрипта <a href="http://dimox.name/" target="_blank">Dimox</a>).<br />Портирование на MaxCMS (<a href="http://www.rgblog.ru/" target="_blank">RGaysin</a>).</div>
</div>