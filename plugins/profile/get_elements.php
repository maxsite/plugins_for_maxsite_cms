<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


// получим массив всех элементов всех пагигов
// $profile_plugins - массив имен плагинов, которые должны формировать события
/* 
  под получением элемента понимается получение опций этого элемента
  по порядку пройдемся по всем плагинам, которые подключают элементы
  и получим опции по ключу: имя_плагина_profiles
*/
	$profiles_all = array();
  	if ($options['profile_plugins'])
  	 foreach ($options['profile_plugins'] as $profile_plugin)
	   {
	       // элементы к подключению должны быть заявленны в этой опции каждого плагина
	       $plugin_elements = mso_get_option($profile_plugin . '_profiles', 'plugins', array());

	      /*опция должна содержать массив элементов
	      
		     ['title'] = Сообщение в дискуссии
		     ['name'] = Сообщение
		     ['all'] = Все сообщения на форуме
		     ['title_go'] = Перейти к сообщению в дискуссии
		     ['all_link'] = forum/all-comments
		     ['img'] = getinfo('plugins_url') . dialog/img/message.png
		     ['filename'] = comments_profiles
	         
	         */      
	         
	       if ($plugin_elements)
	          foreach ($plugin_elements as $plugin_element)
	          {
	            if (file_exists(getinfo('plugins_dir') . $profile_plugin . '/profiles/' . $plugin_element['filename'] . '.php'))
	            {
	              $plugin_element['plugin'] = $profile_plugin;
	              $profiles_all[] = $plugin_element;
	            } 
	          } 
	          
     }

?>


