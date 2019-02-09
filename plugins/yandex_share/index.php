<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * For MaxSite CMS
 * Yandex Share
 * Author: (c) Bugo
 * Plugin URL: http://dragomano.ru/page/maxsite-cms-plugins
 */

function yandex_share_autoload()
{
	if (is_type('page')) mso_hook_add('content_end', 'yandex_share_content_end', 10);
}

function yandex_share_mso_options() 
{
	mso_admin_plugin_options('plugin_yandex_share', 'plugins', 
		array(
			'type' => array(
				'type' => 'select', 
				'name' => t('Внешний вид блока', __FILE__), 
				'description' => t('Укажите внешний вид блока ссылок', __FILE__),
				'values' => 'button||' . t('Кнопка', __FILE__) . '#link||' . t('Ссылка', __FILE__) . '#icon||' . t('Иконки и меню', __FILE__) . '#none||' . t('Только иконки', __FILE__),
				'default' => 'button'
			),
			'align' => array(
				'type' => 'select',
				'name' => 'Выравнивание блока',
				'description' => '',
				'values' => 'left||' . t('По левой стороне', __FILE__) . '#right||' . t('По правой стороне', __FILE__) . '',
				'default' => 'left'
			),
			'message' => array(
				'type' => 'text',
				'name' => t('Текстовое сообщение внутри всплывающего окна', __FILE__),
				'description' => '',
				'default' => t('Поделитесь с друзьями', __FILE__)
			),
			'border' => array(
				'type' => 'checkbox',
				'name' => t('Есть ли у блока рамка', __FILE__),
				'description' => '',
				'default' => '0'
			),
			'quickServices' => array(
				'type' => 'info',
				'title' => t('Список сервисов, показываемых в блоке', __FILE__),
				'text' => t('', 'plugins'), 
			),
			'qs_blogger' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://www.blogger.com/" target="_blank">Blogger</a>', 
				'description' => '', 
				'default' => '0'
			),
			'qs_digg' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://www.digg.com/" target="_blank">Digg</a>', 
				'description' => '', 
				'default' => '0'
			),
			'qs_evernote' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://www.evernote.com/" target="_blank">Evernote</a>', 
				'description' => '', 
				'default' => '0'
			),
			'qs_delicious' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://www.delicious.com/" target="_blank">Delicious</a>', 
				'description' => '', 
				'default' => '0'
			),
			'qs_facebook' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://facebook.com" target="_blank">Facebook</a>', 
				'description' => '', 
				'default' => '0'
			),
			'qs_friendfeed' => array(
				'type' => 'checkbox', 
				'name' => '<a href="https://friendfeed.com/" target="_blank">FriendFeed</a>', 
				'description' => '', 
				'default' => '0'
			),
			'qs_gbuzz' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://www.google.com/buzz" target="_blank">Google Buzz</a>', 
				'description' => '', 
				'default' => '0'
			),
			'qs_greader' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://www.google.com/reader" target="_blank">Google Reader</a>', 
				'description' => '', 
				'default' => '0'
			),
			'qs_juick' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://juick.com/" target="_blank">Juick</a>', 
				'description' => '', 
				'default' => '0'
			),
			'qs_liveinternet' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://www.liveinternet.ru/" target="_blank">LiveInternet</a>', 
				'description' => '', 
				'default' => '0'
			),
			'qs_linkedin' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://www.linkedin.com/" target="_blank">LinkedIn</a>', 
				'description' => '', 
				'default' => '0'
			),
			'qs_lj' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://www.livejournal.com/" target="_blank">Живой Журнал</a>', 
				'description' => '', 
				'default' => '0'
			),
			'qs_moikrug' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://moikrug.ru/" target="_blank">Мой Круг</a>', 
				'description' => '', 
				'default' => '0'
			),
			'qs_moimir' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://my.mail.ru" target="_blank">Мой Мир</a>', 
				'description' => '', 
				'default' => '0'
			),
			'qs_myspace' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://www.myspace.com/" target="_blank">MySpace</a>', 
				'description' => '', 
				'default' => '0'
			),
			'qs_odnoklassniki' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://odnoklassniki.ru" target="_blank">Одноклассники</a>', 
				'description' => '', 
				'default' => '0'
			),
			'qs_twitter' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://twitter.com" target="_blank">Twitter</a>', 
				'description' => '', 
				'default' => '0'
			),
			'qs_vkontakte' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://vkontakte.ru" target="_blank">ВКонтакте</a>', 
				'description' => '', 
				'default' => '0'
			),
			'qs_yaru' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://my.ya.ru/" target="_blank">Я.ру</a>', 
				'description' => '', 
				'default' => '0'
			),
			'qs_yazakladki' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://zakladki.yandex.ru/" target="_blank">Яндекс.Закладки</a>', 
				'description' => '', 
				'default' => '0'
			),
			'blocks' => array(
				'type' => 'info',
				'title' => t('Список сервисов во всплывающем окне', __FILE__),
				'text' => t('', 'plugins'), 
			),
			'ps_blogger' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://www.blogger.com/" target="_blank">Blogger</a>', 
				'description' => '', 
				'default' => '0'
			),
			'ps_digg' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://www.digg.com/" target="_blank">Digg</a>', 
				'description' => '', 
				'default' => '0'
			),
			'ps_evernote' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://www.evernote.com/" target="_blank">Evernote</a>', 
				'description' => '', 
				'default' => '0'
			),
			'ps_delicious' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://www.delicious.com/" target="_blank">Delicious</a>', 
				'description' => '', 
				'default' => '0'
			),
			'ps_facebook' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://facebook.com" target="_blank">Facebook</a>', 
				'description' => '', 
				'default' => '1'
			),
			'ps_friendfeed' => array(
				'type' => 'checkbox', 
				'name' => '<a href="https://friendfeed.com/" target="_blank">FriendFeed</a>', 
				'description' => '', 
				'default' => '0'
			),
			'ps_gbuzz' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://www.google.com/buzz" target="_blank">Google Buzz</a>', 
				'description' => '', 
				'default' => '0'
			),
			'ps_greader' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://www.google.com/reader" target="_blank">Google Reader</a>', 
				'description' => '', 
				'default' => '1'
			),
			'ps_juick' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://juick.com/" target="_blank">Juick</a>', 
				'description' => '', 
				'default' => '0'
			),
			'ps_liveinternet' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://www.liveinternet.ru/" target="_blank">LiveInternet</a>', 
				'description' => '', 
				'default' => '1'
			),
			'ps_linkedin' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://www.linkedin.com/" target="_blank">LinkedIn</a>', 
				'description' => '', 
				'default' => '0'
			),
			'ps_lj' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://www.livejournal.com/" target="_blank">Живой Журнал</a>', 
				'description' => '', 
				'default' => '1'
			),
			'ps_moikrug' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://moikrug.ru/" target="_blank">Мой Круг</a>', 
				'description' => '', 
				'default' => '1'
			),
			'ps_moimir' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://my.mail.ru" target="_blank">Мой Мир</a>', 
				'description' => '', 
				'default' => '1'
			),
			'ps_myspace' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://www.myspace.com/" target="_blank">MySpace</a>', 
				'description' => '', 
				'default' => '0'
			),
			'ps_odnoklassniki' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://odnoklassniki.ru" target="_blank">Одноклассники</a>', 
				'description' => '', 
				'default' => '1'
			),
			'ps_twitter' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://twitter.com" target="_blank">Twitter</a>', 
				'description' => '', 
				'default' => '1'
			),
			'ps_vkontakte' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://vkontakte.ru" target="_blank">ВКонтакте</a>', 
				'description' => '', 
				'default' => '1'
			),
			'ps_yaru' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://my.ya.ru/" target="_blank">Я.ру</a>', 
				'description' => '', 
				'default' => '1'
			),
			'ps_yazakladki' => array(
				'type' => 'checkbox', 
				'name' => '<a href="http://zakladki.yandex.ru/" target="_blank">Яндекс.Закладки</a>', 
				'description' => '', 
				'default' => '1'
			),
		),
		t('Настройки плагина Yandex Share', __FILE__),
		t('Укажите необходимые опции.', 'plugins')
	);
}

function yandex_share_content_end($args = array())
{
	global $MSO;
	
	$options = mso_get_option('plugin_yandex_share', 'plugins', array());
	
	if (!isset($options['type'])) $options['type'] = 'button';
	if (!isset($options['align'])) $options['align'] = 'left';
	if (!isset($options['message'])) $options['message'] = '';
	if (!isset($options['border'])) $options['border'] = '0';
	
	$set = array('blogger', 'digg', 'evernote', 'delicious', 'facebook', 'friendfeed', 'gbuzz', 'greader', 'juick', 'liveinternet', 'linkedin', 'lj', 'moikrug', 'moimir', 'myspace', 'odnoklassniki', 'twitter', 'vkontakte', 'yaru', 'yazakladki');
	$options['def_set'] = $options['fly_set'] = array();
	
	foreach ($set as $name)
	{
		if ($options['qs_' . $name]) $options['def_set'][] = $name;
		if ($options['ps_' . $name]) $options['fly_set'][] = $name;
	}
	
	$def_set = implode('", "', $options['def_set']);
	if (!empty($def_set)) $def_set = '"' . $def_set . '"';
	$fly_set = implode('", "', $options['fly_set']);
	if (!empty($fly_set)) $fly_set = '"' . $fly_set . '"';
	
	echo '
	<script src="//yandex.st/share/share.js" charset="utf-8"></script>
	<span id="yandex_share" style="float: ' . $options['align'] . '"></span>
	<script type="text/javascript">
		new Ya.share({
			element: "yandex_share",
			elementStyle: {
				"type": "' . $options['type'] . '",
				"border": ' . ($options['border'] ? 'true' : 'false') . ',
				"quickServices": [' . $def_set . ']
			},
			popupStyle: {
				blocks: {
					"' . $options['message'] . '": [' . $fly_set . ']
				}
			}
		})
	</script>' . NR;
}

function yandex_share_uninstall($args = array())
{	
	mso_delete_option('plugin_yandex_share', 'plugins');
	return $args;
}

# end file