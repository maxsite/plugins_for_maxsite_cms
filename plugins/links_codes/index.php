<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function links_codes_autoload($args = array())
{
	mso_create_allow('links_codes_edit', t('Админ-доступ к настройкам', 'plugins') . ' ' . t('«Коды ссылок»', __FILE__));
	mso_hook_add( 'content_content', 'links_codes_custom');
	mso_hook_add( 'head', 'links_codes_head');
}


# функция выполняется при деинсталяции плагина
function links_codes_uninstall($args = array())
{
	mso_delete_option('plugin_links_codes', 'plugins'); // удалим созданные опции
	mso_remove_allow('links_codes_edit'); // удалим созданные разрешения
	return $args;
}


function links_codes_mso_options() 
{
	if ( !mso_check_allow('links_codes_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return;
	}

	mso_admin_plugin_options('plugin_links_codes', 'plugins', 
		array(
			'location' => array(
							'type' => 'select', 
							'name' => t('Расположение', __FILE__), 
							'description' => t('Где будут находиться коды ссылок, сверху или снизу', __FILE__), 
							'values' => '0||Сверху#1||Снизу',
						),
			'only_page' => array(
							'type' => 'checkbox', 
							'name' => t('Только страницы', __FILE__), 
							'description' => t('Выводить ли только на страницах, или в категориях и пр.', __FILE__), 
							'default' => '1',
						),
			),
		'Настройки плагина links_codes',
		'Укажите необходимые опции.'
	);
}


function links_codes_head($args = array())
{
	if (!function_exists('spoiler_head'))
		echo '
			<script type="text/javascript" src="' . getinfo('plugins_url') . 'links_codes/spoiler.js"></script>
			';
	echo '
		<script type="text/javascript">
			function jFocus(elm) {
				if(typeof(elm) == "string") elm = xGetElementById(elm);
					if (elm) {     
						elm.focus();
						elm.select();
					}
			}
		</script>';

	return $args;
}


# функции плагина
function links_codes_custom($content = '')
{
	$options_key = 'plugin_links_codes';
	$options = mso_get_option( $options_key, 'plugins', array() );
	
	if ( !isset($options['only_page']) ) $options['only_page'] = 1;
	if ( !isset($options['location'])  ) $options['location'] = 0;
	
	if (is_type('page') and mso_check_allow('links_codes_edit'))
	{
		global $page_slug, $page_title;
		$codes = '
					<p>[<a class="spoiler_link_show" href="javascript:void(0)" onclick="SpoilerToggle(\'bufer\', this, \'Ссылки\', \'Скрыть\')">Ссылки</a>]
					<div class="spoiler_div" id="bufer" style="display: none">
						<input type="text" style="width: 100%;" value="'.getinfo('siteurl').'page/'.$page_slug.'" onclick="jFocus(this)"><br>
						<input type="text" style="width: 100%;" value="[url='.getinfo('siteurl').'page/'.$page_slug.']'.$page_title.'[/url]" onclick="jFocus(this)"><br>
						<input type="text" style="width: 100%;" value="<a href=&quot;'.getinfo('siteurl').'page/'.$page_slug.'&quot; target=&quot;_blank&quot;>'.$page_title.'</a>" onclick="jFocus(this)">
					</div>
					';
		if (!$options['location'])  $content =  $codes . $content;
		else  $content =  $content . $codes;
	}
	if ( !is_type('page') /*and !$options['only_page']*/ and mso_check_allow('links_codes_edit') )
	{
		if ( !isset($links_codes_counter) ) static $links_codes_counter = false;
		if ( !$links_codes_counter )
		{
			$uri = mso_segment_array();
			$link = getinfo('siteurl');
			foreach ($uri as $u)
			{
				$link .= '/'.$u;
			}
			$codes = '
						<p>[<a class="spoiler_link_show" href="javascript:void(0)" onclick="SpoilerToggle(\'bufer\', this, \'Ссылки\', \'Скрыть\')">Ссылки</a>]
						<div class="spoiler_div" id="bufer" style="display: none">
							<input type="text" style="width: 100%;" value="'.$link.'" onclick="jFocus(this)"><br>
							<input type="text" style="width: 100%;" value="[url='.$link.']'.getinfo('name_site').'[/url]" onclick="jFocus(this)"><br>
							<input type="text" style="width: 100%;" value="<a href=&quot;'.$link.'&quot; target=&quot;_blank&quot;>'.getinfo('name_site').'</a>" onclick="jFocus(this)">
						</div>
						';
			$content =  $codes . $content;
			$links_codes_counter = true;
		}
	}
	
	return  $content;
}

?>