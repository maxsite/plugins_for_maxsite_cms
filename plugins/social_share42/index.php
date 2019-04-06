<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
* MaxSite CMS
* (c) http://max-3000.com/
*
* Евгений Мирошниченко
* zhenya.webdev@gmail.com
* (c) https://modern-templates.com
*
*/

function social_share42_autoload($args = array())
{
	if( is_type('page') )
	{
	  mso_hook_add('head_css', 'social_share42_css');
      mso_hook_add('body_end', 'social_share42_js');

	  $options = mso_get_option('plugin_social_share42', 'plugins', array());
	  if (!isset($options['priory'])) $options['priory'] = 10;

	  mso_hook_add('content_end', 'social_share42_content_end', $options['priory']);

	}
}

function social_share42_css($args = array())
{
   echo'<link rel="stylesheet" href="' .getinfo('plugins_url') . 'social_share42/css/style.css">' . NR;
}

function social_share42_js($args = array())
{
  echo'<script src="' . getinfo('plugins_url') . 'social_share42/share42.js"></script>';
}

function social_share42_uninstall($args = array())
{
    mso_delete_option_mask('social_share42_widget_', 'plugins'); // удалим созданные опции
	mso_delete_option('plugin_social_share42', 'plugins');
	return $args;
}

function social_share42_mso_options()
{
	mso_admin_plugin_options('plugin_social_share42', 'plugins',

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
		    'default' => 'Данная публикация была вам полезна?'
		    ),

		    'text_share' => array(
		    'type' => 'text',
		    'name' => 'Зазывательная фраза',
		    'description' => 'Пример: Помоги автору сайта, расскажи друзьям...',
		    'default' => 'Помоги автору сайта, расскажи друзьям...'
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


function social_share42_content_end($args = array())
{
    global $page;

    $options = mso_get_option('plugin_social_share42', 'plugins', array());
    if (!isset($options['priory'])) $options['priory'] = '10';

    $def_options = array(
    'title' => '',
    'text_share' => '',
    'format' => '',
    );

	$options = array_merge($def_options, $options);
   // ' . $options['text-do'] . '
    $share_img ='<img src="' .getinfo('plugins_url') . 'social_share42/img/share.png" alt="share" class="share42__img">';

    if ($options['format']=='default')
	{

	 echo '<div class="share42">';
     echo $share_img;
     echo '<div class="share42__title">' . $options['title']. '</div>';
     echo '<div class="share42__text"><p>' . $options['text_share']. '</p></div>';
     echo '<div class="share42init"></div>';
     echo '</div>';

    }elseif($options['format']=='name_desc')
	{

	 echo '<div class="share42">';
     echo '<div class="share42__title">' . $options['title']. '</div>';
     echo '<div class="share42__text"><p>' . $options['text_share']. '</p></div>';
     echo '<div class="share42init"></div>';
     echo '</div>';

    }elseif($options['format']=='only_name')
	{

	 echo '<div class="share42">';
     echo '<div class="share42__title">' . $options['title']. '</div>';
     echo '<div class="share42init"></div>';
     echo '</div>';

    }elseif($options['format']=='only_bottom')
	{
	  echo '<div class="share42">';
      echo '<div class="share42init"></div>';
      echo '</div>';
    }

	return $args;
}

