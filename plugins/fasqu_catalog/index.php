<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
 
 
# функция автоподключения плагина
function fasqu_catalog_autoload($args = array())
{
    mso_hook_add('custom_page_404', 'fasqu_catalog_custom_page_404');

}

function fasqu_catalog_custom_page_404($args=false)
{
 $options = mso_get_option('fasqu_catalog', 'plugins', array());
 if ( !isset($options['catalog_slug']) ) $options['catalog_slug'] = 'links'; 

 $catalog_slug = $options['catalog_slug'];
 $segment = mso_segment(1);
 if ($segment == $catalog_slug)
 {
   require(getinfo('plugins_dir').'fasqu_catalog/catalog.php');
   return true;
 }
 return $args;
}

# функция выполняется при активации (вкл) плагина
function fasqu_catalog_activate($args = array())
{    
    return $args;
}
  
# функция выполняется при деактивации (выкл) плагина
function fasqu_catalog_deactivate($args = array())
{  
   return $args;
}
  
# функция выполняется при деинсталляции плагина
function fasqu_catalog_uninstall($args = array())
{
    mso_delete_option('fasqu_catalog', 'plugins'); // удалим созданные опции
    return $args; 
}

function fasqu_catalog_custom($response = '')
{    
     return $args;
}
  
function fasqu_catalog_mso_options()
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('fasqu_catalog', 'plugins',
		array(

			'catalog_slug' => array(
							'type' => 'text',
							'name' => 'Slug для каталога',
							'description' => 'Определяет адрес каталога вида http://mysite/slug',
							'default' => 'links'
						),
			'catalog_title' => array(
							'type' => 'text',
							'name' => 'Загаловок каталога',
							'description' => 'Выводимый title каталога',
							'default' => 'Ссылки'
						),						
			'links' => array(
							'type' => 'textarea',
							'name' => 'Опредление страниц каталога ссылок',
							'description' => 'Задайте метки ссылок, выводимые в каталоге ссылок по одному в каждой строчке в виде: <b>метка | slug страницы | Заголовок<b>',
							'default' => 'Max+Site+cms | maxsite | Max Site CMS'
						),
			'menu_do' => array(
							'type' => 'text',
							'name' => 'До списка страниц каталога',
							'description' => 'Выводится до блока списка старниц каталога',
							'default' => '<ul>'
						),						
			'menu_posle' => array(
							'type' => 'text',
							'name' => 'После списка страниц каталога',
							'description' => 'Выводится после блока списка старниц каталога',
							'default' => '</ul>'
						),
			'menu_item_do' => array(
							'type' => 'text',
							'name' => 'До элемента списка станиц каталога',
							'description' => 'Можно обрамить ссылку на каждую страницу каталога в теги',
							'default' => '<li>'
						),
			'menu_item_posle' => array(
							'type' => 'text',
							'name' => 'После  элемента списка станиц каталога',
							'description' => 'Можно обрамить ссылку на каждую страницу каталога в теги',
							'default' => '</li>'
						),
			'links_do' => array(
							'type' => 'text',
							'name' => 'До блока списка ссылок',
							'description' => 'Можно задать свой способ вывода ссылок',
							'default' => '<ul>'
						),
			'links_posle' => array(
							'type' => 'text',
							'name' => 'После блока списка ссылок',
							'description' => 'Можно задать свой способ вывода ссылок',
							'default' => '</ul>'
						),		
			'link_do' => array(
							'type' => 'text',
							'name' => 'Что до каждой ссылки в списке',
							'description' => 'Можно задать свой способ вывода ссылок',
							'default' => '<li>'
						),							
			'link_posle' => array(
							'type' => 'text',
							'name' => 'Что после каждой ссылки в списке',
							'description' => 'Можно задать свой способ вывода ссылок',
							'default' => '</li>'
						),			
			'do' => array(
							'type' => 'text',
							'name' => 'Что до вывода каталога ссылок',
							'description' => 'Например, блок баннеров',
							'default' => ''
						),				
			'posle' => array(
							'type' => 'text',
							'name' => 'Что после вывода каталога ссылок',
							'description' => 'Например, блок добавления ссылки',
							'default' => ''
						),											
			'code' => array(
							'type' => 'text',
							'name' => 'Код пользователя FasQu',
							'description' => 'Если поле не пустое - будет выводиться форма добавления',
							'default' => ''
						),					
			'email' => array(
							'type' => 'text',
							'name' => 'email для отправки уведомлений о добавлении новой ссылки',
							'description' => 'если нужно отправлять уведомления о необходимости модерации',
							'default' => ''
						),							
												
			),
		'Настройки плагина fasqu_catalog', // титул
		'Укажите необходимые опции.'   // инфо
	);
}
  
  
  
?>