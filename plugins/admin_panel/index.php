<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

function admin_panel_autoload()
{
	mso_hook_add( 'head', 'admin_panel_head');
	mso_hook_add( 'body_start', 'admin_panel_body');
}

function admin_panel_uninstall($args = array())
{	
	mso_delete_option('plugin_admin_panel', 'plugins'); // удалим созданные опции
	return $args;
}

function admin_panel_head($args = array())
{
	if (is_login())
	{
		$options = mso_get_option('plugin_admin_panel', 'plugins', array()); // все опции
		if ( !isset($options['padding_block']) ) $options['padding_block'] = 'body'; // включен ли антиспам
		if ( !isset($options['padding_size']) ) $options['padding_size'] = '28'; // включен ли антиспам

		$plug_url = getinfo('plugins_url') . 'admin_panel/';
		echo '<link rel="stylesheet" id="admin-bar-css" href="'.$plug_url.'admin-bar.css" type="text/css" media="all">';
		echo '<style type="text/css"> '.$options['padding_block'].' { padding-top: '.$options['padding_size'].'px; } </style>';
	}
}

function admin_panel_body($arg = array())
{
	if (is_login())
	{
	$plug_url = getinfo('plugins_url') . 'admin_panel/';
?>
<div id = "adminbar">
    <div class = "bars">
        <ul>
            <li id = "icon" class = "popup">
            <a href = "<?= getinfo('siteurl') ?>admin/users_my_profile"><span><img alt = ""
                src = "<?= $plug_url ?>maxsitelogo.png"
                class = "avatar avatar-16 photo" height = "16" width = "16"><?= getinfo('users_nik') ?></span></a>

            <ul>
                <li class = "">
                <a href = "<?= getinfo('siteurl') ?>admin/users_my_profile">Изменить профиль</a> </li>

	        <li class = "">
		<a href = "<?= getinfo('siteurl') ?>admin">Консоль</a> </li>

                <li class = "">
                <a href = "<?= getinfo('siteurl') ?>logout">Выйти</a> </li>
            </ul>

            </li>
<?
	if ( mso_check_allow('admin_page_new') ) 
	{
?>
            <li class = "popup">
            <a href = "<?= getinfo('siteurl') ?>admin/page_new"><span>Добавить</span></a>

            <ul>
                <li class = "">
                <a href = "<?= getinfo('siteurl') ?>admin/page_new">Запись</a> </li>
<?
	if ( mso_check_allow('admin_files') ) 
	{
?>
                <li class = "">
                <a href = "<?= getinfo('siteurl') ?>admin/files">Медиафайл</a> </li>
<?
	}
?>
            </ul>
<?
	}
	if ( mso_check_allow('admin_comments') ) 
	{
?>
            <li class = "">
            <a href = "<?= getinfo('siteurl') ?>admin/comments">Комментарии </a> </li>
<?
	}
	$viewurl = '';
	if ( mso_check_allow('template_options_admin') ) $viewurl = getinfo('siteurl') .'admin/template_options';
	if ( mso_check_allow('admin_plugins') )  $viewurl = getinfo('siteurl') .'admin/page_new';
	if ( mso_check_allow('admin_sidebars') ) $viewurl = getinfo('siteurl') .'admin/sidebars';
	if ($viewurl<>'')
	{
?>
            <li class = "popup">
            <a href = "<?= $viewurl ?>"><span>Внешний вид</span></a>

            <ul>
<?
	if ( mso_check_allow('admin_plugins') ) 
	{
?>
                <li class = "">
                <a href = "<?= getinfo('siteurl') ?>admin/plugins">Плагины</a> </li>
<?
	}
	if ( mso_check_allow('admin_sidebars') ) 
	{
?>
                <li class = "">
                <a href = "<?= getinfo('siteurl') ?>admin/sidebars">Виджеты</a> </li>
<?
	}
	$fn = getinfo('template_dir') . 'options.php';
	if ((mso_check_allow('template_options_admin') )&&(file_exists($fn)))
	{
?>
                <li class = "">
                <a href = "<?= getinfo('siteurl') ?>admin/template_options">Настройки шаблона</a> </li>
<?
	}
?>
            </ul>
<?
	}
?>
            </li>
        </ul>
    </div>

    <div id = "adminbarsearch-wrap">
        <form name="f_search action = ""
            method = "get" id = "adminbarsearch"
		onsubmit="location.href='<?= getinfo('siteurl') ?>search/' + encodeURIComponent(this.s.value).replace(/%20/g, '+'); return false;">
            <input class = "adminbar-input" name = "s" id = "adminbar-search" type = "text" value = ""
                maxlength = "150">
            <input type = "submit" class = "adminbar-button" value = "Поиск">
        </form>
    </div>
</div>
<?php
	}
	return $arg;
}

function admin_panel_mso_options() 
{
	mso_admin_plugin_options('plugin_admin_panel', 'plugins', 
		array(
			'padding_block' => array(
							'type' => 'text', 
							'name' => t('Блок для смешения', 'plugins'), 
							'description' => t('Название блока для смещения, если панель наезжает на дизайн. Например <b>.content</b>', 'plugins'), 
							'default' => 'body'
						),
			'padding_size' => array(
							'type' => 'text', 
							'name' => t('Интервал смешения', 'plugins'), 
							'description' => t('Размер смещения для предыдущей опции в px', 'plugins'), 
							'default' => '28'
						),
			),
		'Настройки плагина admin_panel', // титул
		'Укажите необходимые опции.'   // инфо
	);
}

?>