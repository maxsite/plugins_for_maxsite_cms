<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * For MaxSite CMS
 * Masha in MaxSite
 * Author: (c) Bugo
 * Plugin URL: http://dragomano.ru/page/maxsite-cms-plugins
 */

function masha_autoload()
{
	if (is_type('page')) mso_hook_add('head', 'masha_head', 50);
	if (is_type('page')) mso_hook_add('content_end', 'masha_content_end');
}

function masha_head($args = array())
{
	$options = mso_get_option('plugin_masha', 'plugins', array());
	$options['masha_selectable'] = isset($options['masha_selectable']) ? $options['masha_selectable'] : 'content';
	$options['masha_ignored'] = isset($options['masha_ignored']) ? $options['masha_ignored'] : null;

	echo '
	<link rel="stylesheet" type="text/css" href="' . getinfo('plugins_url') . 'masha/css/masha.css" media="screen" />
	<!--[IF IE]><script type="text/javascript" src="' . getinfo('plugins_url') . 'masha/js/ierange.js"></script><![ENDIF]-->
	<script type="text/javascript" src="' . getinfo('plugins_url') . 'masha/js/masha.min.js"></script>
	<script type="text/javascript">
		function init_masha(){
			MaSha.instance = new MaSha({"selectable": "' . $options['masha_selectable'] . '", "ignored": "' . $options['masha_ignored'] . '", "select_message": "upmsg-selectable"});
		}
		if (window.addEventListener){
			window.addEventListener("load", init_masha);
		} else {
			window.attachEvent("onload", init_masha);
		}
	</script>';
}

function masha_content_end($arg = array())
{
	$options = mso_get_option('plugin_masha', 'plugins', array());
	$options['masha_message'] = isset($options['masha_message']) ? $options['masha_message'] : null;
	
	echo '
		<a href="#" id="txtselect_marker"></a>';
		
	if (!empty($options['masha_message']))
		echo '
		<div id="upmsg-selectable">
			<div class="upmsg-selectable-inner">
				<img src="' . getinfo('plugins_url') . 'masha/img/upmsg_arrow.png" alt="" />
				<p>' . $options['masha_message'] . '</p>
				<a href="#" class="upmsg_closebtn"></a>
			</div>
		</div>';

	return $arg;
}

function masha_mso_options() 
{
	mso_admin_plugin_options('plugin_masha', 'plugins', 
		array(
			'masha_selectable' => array(
				'type' => 'text',
				'name' => t('Область выделения', __FILE__),
				'description' => t('html-элемент или id элемента - контейнер текста, который можно выделять.', __FILE__),
				'default' => 'content'
			),
			'masha_ignored' => array(
				'type' => 'text',
				'name' => t('Игнорируемая область', __FILE__),
				'description' => t('html-элементы, выделение которых не требуется. Например: .links (запрет выделения текста внутри элемента с классом "links").', __FILE__),
				'default' => 'pre, span'
			),
			'masha_message' => array(
				'type' => 'text',
				'name' => t('Всплывающее уведомление', __FILE__),
				'description' => t('Сообщение, появляющееся в верхней части экрана после пометки выбранного фрагмента текста.', __FILE__),
				'default' => t('Отметьте интересные вам фрагменты текста и они станут доступны по уникальной ссылке в адресной строке браузера.', __FILE__)
			),
		),
		t('Настройки плагина Masha', __FILE__),
		t('Укажите необходимые опции.', __FILE__)
	);
}

function masha_uninstall($args = array())
{	
	mso_delete_option('plugin_masha', 'plugins');
	return $args;
}

# end file