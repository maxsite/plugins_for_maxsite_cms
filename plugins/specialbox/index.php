<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * MaxSite CMS
 * Plugin Name: Special Text Box
 * Authors: Tux(http://6log.ru), minimus(http://blogovod.co.cc/)
 * Plugin URL: http://6log.ru/special-text-boxes
 */

/*  Copyright 2009, Tux

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

# функция автоподключения плагина
function specialbox_autoload($args = array())
{
	mso_hook_add( 'head', 'specialbox_head');
	mso_hook_add( 'content_out', 'specialbox_custom'); # хук на вывод контента
	mso_hook_add( 'admin_init', 'specialbox_admin_init'); # хук на админку
	mso_create_allow('specialbox_edit', t('Админ-доступ к редактированию Special Box', __FILE__));

}

include_once('lib.php');

# функция выполняется при указаном хуке admin_init
function specialbox_admin_init($args = array()) 
{
	if ( mso_check_allow('specialbox_edit') ) 
	{
		$this_plugin_url = 'specialbox'; // url и hook

		mso_admin_menu_add('plugins', $this_plugin_url, t('Special Box', __FILE__));
		mso_admin_url_hook ($this_plugin_url, 'specialbox_admin_page');
	}
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function specialbox_admin_page($args = array()) 
{
	global $MSO;
	
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('specialbox_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	# выносим админские функции отдельно в файл
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('настройки плагина specialbox', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Плагин special text box', __FILE__) . ' - " . $args; ' );
	require($MSO->config['plugins_dir'] . 'specialbox/admin.php');
}

# функция выполняется при деинстяляции плагина
function specialbox_uninstall($args = array())
{	
	mso_delete_option_mask('plugin_specialbox', 'plugins'); // удалим созданные опции
	mso_remove_allow('specialbox_edit'); // удалим созданные разрешения
	return $args;
}

# Добавляем в head
function specialbox_head($args = array())
{
$options_key = 'plugin_specialbox';
$options = mso_get_option($options_key, 'plugins', array());
if ( !isset($options['corner']) )  {$options['corner'] = false; }
if ($options['corner'] == 'true')
{
	echo '<script type="text/javascript" src="' .  getinfo('plugins_url') . 'specialbox/js/cornerz.min.js" ></script>
	<script>
	$(document).ready(function() {				   
		$(".stb_box").cornerz({radius:5});
		$(".stb_box_c").cornerz({radius:5,corners: "tl tr"});
		$(".stb_box_b").cornerz({radius:5,corners: "bl br"});
	 });</script>';
}
	echo '<link rel="stylesheet" href="' . getinfo('require-maxsite') 
        . base64_encode('plugins/specialbox/css/special-text-boxes.css-require-maxsite.php') . '" type="text/css" media="screen" charset="utf-8" />
		';
	echo '<link rel="stylesheet" href="' . getinfo('require-maxsite') 
        . base64_encode('plugins/specialbox/css/boxes.css-require-maxsite.php') . '" type="text/css" media="screen" charset="utf-8" />';	
	
}

# функции плагина
function specialbox_custom($text)
{
	// dont edit!
	$pattern = "@\[sbox(.*?)\](.*?)\[\/sbox\]@is";
	
	// замена тегов
	if (preg_match_all($pattern, $text, $matches))
	{
		for ($i = 0; $i < count($matches[0]); $i++)
		{
			$html = '';
			$matches[1][$i] = trim($matches[1][$i]);
			if ( !empty($matches[1][$i]) )
			{
				$options = get_opt( $matches[1][$i] );
				//echo '111='.$options['id'].$options['color'].$options['caption'];
				$html = get_SpecialBox($options, $matches[2][$i]);
				//$text = preg_replace($pattern, $html, $text, 1);
				$text = str_replace($matches[0][$i], $html, $text);
			}
			else
			{
				$options = array();
				//echo ' not ';
				$html = get_SpecialBox($options, $matches[2][$i]);
				//$text = preg_replace($pattern, $html, $text, 1);
				$text = str_replace($matches[0][$i], $html, $text);
			}
		}
	}
	return $text;
}
?>