<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function yashare_autoload($args = array())
{
	if ( is_type('page') )
	{
		mso_hook_add( 'content_end', 'yashare_content_end');
	}
}


# функции плагина
function yashare_content_end($args = array())
{
	$options = mso_get_option('plugin_yashare', 'plugins', array() );

	?>
	<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
<script type="text/javascript">
new Ya.share({
	'element': 'ya_share1',
	'elementStyle': {
		'type': '<?php echo $options['type']  ?>',
		'text': '<?php echo $options['text']  ?>',
		'linkIcon': true,
		'border': false,
		'quickServices': [<?php echo $options['quickServices']  ?>]
	},
	'l10n': '<?php echo $options['language']  ?>',
	'popupStyle': {
		'copyPasteField': true
	}
 });
</script>
<span id="ya_share1"></span>
<?php
	return $args;
}

function yashare_uninstall($args = array())
{
	mso_delete_option('plugin_yashare', 'plugins'); // удалим созданные опции
	return $args;
}

function yashare_mso_options()
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_yashare', 'plugins',
		array(

			'language' => array(
							'type' => 'select',
							'name' => 'Язык плагина',
							'description' => 'Язык текста блока.',
							'values' => 'be||белорусский # en||английский # kk||казахский # ru||русский # tt||татарский # uk||украинский',
							'default' => 'ru'
						),
			'type' => array(
							'type' => 'select',
							'name' => 'Тип блока',
							'description' => '',
							'values' => 'button||кнопка # link||ссылка',
							'default' => 'button'
						),
			'text' => array(
							'type' => 'text',
							'name' => 'Текст для блока',
							'description' => '',
							'default' => 'Поделиться'
						),
			'quickServices' => array(
							'type' => 'text',
							'name' => 'Список сервисов, показываемых в блоке',
							'description' => 'yaru - Я.Ру; vkontakte - ВКонтакте; facebook - facebook; twitter - Twitter; lj - Живой Журнал; friendfeed - FriendFeed; moimir - Мой Мир; odnoklassniki - Одноклассники.ru.',
							'default' => '\'\', \'yaru\', \'vkontakte\', \'twitter\', \'facebook\''
						),


			),
		'Настройки плагина Yandex Share', // титул
		'Укажите необходимые опции.'   // инфо
	);
}



?>