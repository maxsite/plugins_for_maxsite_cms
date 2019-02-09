<?php if (!defined('BASEPATH')) die('Любопытной Варваре на базаре нос оторвали.');

// !!Вызывается движком!!
function vg_orphus_autoload()
{
	mso_register_widget('orphus_widget', 'Виджет Orphus');
}

// !!Вызывается движком!!
function vg_orphus_uninstall()
{
	mso_delete_option('plugin_vg_orphus', 'plugins');
}

// !!Вызывается движком!!
function vg_orphus_mso_options()
{
	list($image_values, $image_default) = vgo_get_library_images_info();

	mso_admin_plugin_options('plugin_vg_orphus', 'plugins',
		array(
			'email' => array(
				'type' => 'text',
				'name' => t('Адрес электронной почты', 'vg_orphus'),
				'description' => t('На указанный адрес будут посылаться сообщения о найденных пользователями ошибках.', 'vg_orphus'),
				'default' => '',
			),
			'lang' => array(
				'type' => 'select',
				'name' => t('Язык интерфейса Orphus.', 'vg_orphus'),
				'description' => '',
				'values' => t('auto||Определять автоматически # ru||Русский # ua||Украинский # en||Английский', 'vg_orphus'),
				'default' => 'auto',
			),
			'widget_link' => array(
				'type' => 'select',
				'name' => t('Тип ссылки в виджете', 'vg_orphus'),
				'description' => '',
				'values' => 'normal||Обычная # nofollow||Неиндексируемая # none||Отсутствует',
				'default' => 'default',
			),
			'widget_image' => array(
				'type' => 'radio',
				'name' => t('Картинка для виджета', 'vg_orphus'),
				'description' => '',
				'default' => $image_default,
				'values' => $image_values,
			),
			'widget_own_image' => array(
				'type' => 'text',
				'name' => t('Адрес своей картинки', 'vg_orphus'),
				'description' => t('Введите полный адрес вашей собственной картинки для виджета (например, «<b>http://orphus.ru/ru/img/kuvalda.gif</b>»).', 'vg_orphus'),
				'default' => '',
			),
		),
		t('Настройки плагина Orphus', 'vg_orphus'),
		t('Для работы плагина обязательно укажите адрес электронной почты, а также добавьте в виджет <b>orphus_widget</b> в любой сайдбар. Вы также можете встроить Орфус в шаблон или ушки командой <b>&ltphp function_exists(\'orphus_bit\') ? orphus_bit() : \'\'; ?&gt</b>.', 'vg_orphus')
	);
}

// !!Вызывается движком!!
function orphus_widget_form($num = 1)
{
	$widget = 'orphus_widget_'.$num;

	$options = mso_get_option($widget, 'plugins', array());
	if (!isset($options['header'])) $options['header'] = '';

	get_instance()->load->helper('form');
	$form = mso_widget_create_form(t('Заголовок'), form_input(array('name'=>$widget.'header', 'value'=>$options['header'])));

	return $form;
}

// !!Вызывается движком!!
function orphus_widget_update($num = 1)
{
	$widget = 'orphus_widget_'.$num;

	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	$newoptions['header'] = mso_widget_get_post($widget . 'header');

	if ($options != $newoptions) {
		mso_add_option($widget, $newoptions, 'plugins');
	}
}

// !!Вызывается движком!!
function orphus_widget($num = 1)
{
	$widget = 'orphus_widget_'.$num;
	$options = mso_get_option($widget, 'plugins');

	if (isset($options['header']) and $options['header']) {
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	} else {
		$options['header'] = '';
	}

	return orphus_widget_custom($options, $num);
}

function orphus_widget_custom($options, $num)
{
	$cache_key = 'orphus_widget_custom'.serialize($options).$num;
	$out = mso_get_cache($cache_key);

	if (!$out) {
		$tpl = getinfo('templates_dir').'/custom/orphus_widget.php';

		$error = null; $orphus_bit = vgo_get_orphus_bit();

		ob_start();

		if (mso_fe($tpl)) {
			require $tpl;
		} else {
			if ($orphus_bit) {
				echo "<div style=\"text-align: center;\" class=\"vg-orphus\">{$orphus_bit}</div>";
			} else {
				$error = t('Виджет Orphus не настроен!', 'vg_orphus');
				echo "<div style=\"color: red; border: thick dashed red; text-align: center;\" class=\"vg-error\">{$error}</div>";
			}
		}
		$out = $options['header'].ob_get_clean();

		mso_add_cache($cache_key, $out);
	}

	return $out;
}

function orphus_bit()
{
	echo vgo_get_orphus_bit();
}

function vgo_get_orphus_bit()
{
	$options = vgo_get_options();

	if ($options) {
		$plugins_url = getinfo('plugins_url');

		$orphus_script = "<script type=\"text/javascript\" src=\"{$plugins_url}vg_orphus/vg_orphus.js\"></script>".
			             "<script>vg_orphus_init('{$options['email']}', '{$options['lang']}');</script>";
		$orphus_image  = "<img alt=\"Orphus system\" src=\"{$options['image_url']}\" border=\"0\" />";

		switch($options['widget_link']) {
			case 'normal':
				$orphus_image = '<a href="http://orphus.ru" id="orphus" target="_blank">'.$orphus_image.'</a>';
				break;
			case 'nofollow':
				$orphus_image = '<a href="http://orphus.ru" id="orphus" rel="nofollow" target="_blank">'.$orphus_image.'</a>';
				break;
			default:
				$orphus_image = '<a id="orphus">'.$orphus_image.'</a>';
		}

		return $orphus_script.$orphus_image;
	}

	return false;
}

function vgo_get_library_images_info()
{
	$images = glob(dirname(__FILE__) . '/images/*');
	foreach ($images as &$fn) {
		$fn = basename($fn);
		$url = getinfo('plugins_url') . 'vg_orphus/images/' . $fn;
		$image_values[] = $fn.'||<img src="'.$url.'" /> <br />#';
	}
	$image_values[] = "own||" . t('Своя картинка', 'vg_orphus');
	$image_values = implode($image_values);

	$image_default = array_shift($images);
	$image_default = !empty($image_default) ? $image_default : 'own';

	return array($image_values, $image_default);
}

function vgo_get_options()
{
	$raw = mso_get_option('plugin_vg_orphus', 'plugins', array());
	$raw += array('email' => '', 'lang' => '', 'widget_link' => '', 'widget_image' => '', 'widget_own_image' => '');

	$email = filter_var($raw['email'], FILTER_VALIDATE_EMAIL);
	$lang = in_array($raw['lang'], array('ru', 'en', 'ua', 'auto')) ? $raw['lang'] : 'auto';
	$widget_link = in_array($raw['widget_link'], array('normal', 'nofollow', 'none')) ? $raw['widget_link'] : 'none';
	$image_url = filter_var(
		($raw['widget_image'] <> 'own') ? getinfo('plugins_url').'vg_orphus/images/'.$raw['widget_image'] : $raw['widget_own_image'],
		FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED
	);

	if ($email && $image_url) {
		// Скрыть email от спамеров, закодировав его в принимаемый Orphus.js формат
		$email = '!' . implode('@', explode('@', preg_replace('~(.)(.)~', '\2\1', $email)));

		return array(
			'email' => $email,
			'lang' => $lang,
			'widget_link' => $widget_link,
			'image_url' => $image_url,
			'usable' => $email && $image_url
		);
	} else {
		return false;
	}
}
