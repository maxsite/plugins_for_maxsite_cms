<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 *
 * Alexander Schilling
 * http://alexanderschilling.net
 *
 */

function cackle_comments_autoload()
{
   mso_hook_add('type-foreach-file', 'cackle_comments_f1');
   
   // регестируем виджет
   mso_register_widget('cackle_comments_widget', t('Последние комментарии', __FILE__));
}

function cackle_comments_uninstall($args = array())
{	
	mso_delete_option('cackle_comments', 'plugins');
    
    // удаляем настройки виджета
	mso_delete_option_mask('cackle_comments_widget_', 'plugins');
    
	return $args;
}

# функция, которая берет настройки из опций виджетов
function cackle_comments_widget($num = 1) 
{
	$widget = 'cackle_comments_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	return cackle_comments_widget_custom($options, $num);
}

# функции плагина
function cackle_comments_widget_custom($options = array(), $num = 1)
{
    
    $options = mso_get_option('cackle_comments', 'plugins', array() );
    
    $out = '';
    
    $out .= mso_get_val('widget_header_start', '<h2 class="box"><span>') . t('Последние комментарии', __FILE__) . mso_get_val('widget_header_end', '</span></h2>');
    
    $out .= "<div id=\"mc-last\"></div>
<script type=\"text/javascript\">
var mcSite = '" . $options['cackle_shortname'] . "';
var mcSize = '5';
var mcAvatarSize = '32';
var mcTextSize = '150';
(function() {
    var mc = document.createElement('script');
    mc.type = 'text/javascript';
    mc.async = true;
    mc.src = 'http://cackle.me/mc.last-min.js';
    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(mc);
})();
</script>";
    
	return $out;	
}

function cackle_comments_mso_options() 
{
	mso_admin_plugin_options('cackle_comments', 'plugins', 
		array(
			'cackle_shortname' => array(
						'type' => 'text', 
						'name' => 'mcSite ID:', 
						'description' => t('Пройдите регистрацию на <a href="http://ru.cackle.me/plans" target="_blank">Cackle</a> и добавьте новый сайт. Скопируйте ваш ID "mcsite", например: 112. Удалять, изменять комментарии нужно на сайте <a href="http://cackle.ru/">Cackle</a>.', __FILE__),
						'default' => ''
					),										
			),
		t('Настройки плагина Cackle для MaxSite', __FILE__),
		t('Укажите необходимые опции.', __FILE__)
	);
}

function cackle_comments_f1($tff = false) 
{   
   if ($tff == 'page-comment-form-do') return getinfo('plugins_dir') . 'cackle_comments/type_foreach/page-comment-form-do.php';
   elseif ($tff == 'page-comment-form') return getinfo('plugins_dir') . 'cackle_comments/type_foreach/page-comment-form.php';
   
   return false;
}

#end of file
