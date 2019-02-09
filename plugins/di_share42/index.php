<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) http://max-3000.com
 *
 * Жека Dark-Inside
 * (c) http://di-modern.ru
 */

function di_share42_autoload($args = array())
{
	if( is_type('page') )
	{
	  mso_hook_add('head', 'di_share42_head');

	  $options = mso_get_option('plugin_di_share42', 'plugins', array());
	  if (!isset($options['priory'])) $options['priory'] = 10;

	  mso_hook_add('content_end', 'di_share42_content_end', $options['priory']);
	}
}

function di_share42_head($args = array())
{
   $path = getinfo('plugins_url') . 'di_share42/';
   echo'<link rel="stylesheet" href="' . $path . 'css/style.css">' . NR;
}

function di_share42_uninstall($args = array())
{
    mso_delete_option_mask('di_share42_widget_', 'plugins'); // удалим созданные опции
	mso_delete_option('plugin_di_share42', 'plugins');
	return $args;
}

function di_share42_mso_options()
{
	mso_admin_plugin_options('plugin_di_share42', 'plugins',

		array(

			'priory' => array(
			'type' => 'text',
			'name' => 'Приоритет блока',
			'description' => 'Располагает блок до или после аналогичных. Используйте значения от 10 до 90. Чем больше значение, тем выше блок. По умолчанию 10.',
			'default' => '10'
			),

			'title' => array(
		    'type' => 'text',
		    'name' => 'Заголовок блока',
		    'description' => 'Данная публикация была вам полезна?',
		    'default' => '<div class="head-share"><h3>Данная публикация была вам полезна?</h3></div>'
		    ),

		    'text_share' => array(
		    'type' => 'text',
		    'name' => 'Зазывательная фраза',
		    'description' => 'Пример: Помоги автору сайта, расскажи друзьям...',
		    'default' => '<p>Помоги автору сайта, расскажи друзьям...</p>'
		    ),

            'format' => array(
			'type' => 'select',
			'name' => t('Формат вывода'),
			'description' => t('<b>default</b> - Картинка, Заголовок, описание, кнопки.<br><b>name_desc</b> - Заголовок, описание, кнопки.<br><b>only_name</b> - только Заголовок и кнопки.<br> <b>only_bottom</b> - только кнопки'),
			'values' => 'default # name_desc # only_name #only_bottom',  // правила для select как в ini-файлах
			'default' => 'default'
			),

			),
		'Сервис закладок share42',
		'Укажите необходимые внешний вид и основные настройки.'
	);
}


function di_share42_content_end($args = array())
{
    global $page;

    $options = mso_get_option('plugin_di_share42', 'plugins', array());
    if (!isset($options['priory'])) $options['priory'] = '10';

    $def_options = array(
    'title' => '',
    'text_share' => '',
    'format' => '',
    );

	$options = array_merge($def_options, $options);
   // ' . $options['text-do'] . '
    $di_share_img ='<img src="' .getinfo('plugins_url') . 'di_share42/img/share.png" alt="help_share" class="di_share_img">';

    if ($options['format']=='default')
	{

	 echo '<div class="b-share">';
     echo ' ' . $di_share_img . $options['title']. $options['text_share']. ' ';
     echo '<div class="share42init"></div><script src="' . getinfo('plugins_url') . 'di_share42/share42.js"></script>';
     echo '</div>';

    }elseif($options['format']=='name_desc')
	{

	 echo '<div class="b-share">';
     echo ' ' . $options['title']. $options['text_share']. ' ';
     echo '<div class="share42init"></div><script src="' . getinfo('plugins_url') . 'di_share42/share42.js"></script>';
     echo '</div>';

    }elseif($options['format']=='only_name')
	{

	 echo '<div class="b-share">';
     echo $options['title'];
     echo '<div class="share42init"></div><script src="' . getinfo('plugins_url') . 'di_share42/share42.js"></script>';
     echo '</div>';

    }elseif($options['format']=='only_bottom')
	{
	  echo '<div class="b-share">';
      echo '<div class="share42init"></div><script src="' . getinfo('plugins_url') . 'di_share42/share42.js"></script>';
      echo '</div>';
    }

	return $args;
}

