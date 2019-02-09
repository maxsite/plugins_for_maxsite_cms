<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */

// в этом файле вычислим, какой файл подключать функции шаблона

// функция подключения шаблонных файлов
// $template_dir - имя текущего шаблона
// $default_template_dir - имя дефолтного шаблона
// $fn - имя подключаемого файла
function template_require(&$template_dir , &$template_default_dir , $fn)
{
  if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
     require($template_dir . $fn);
  else 
  {
     echo $template_default_dir . $fn;
     require($template_default_dir . $fn); 
  } 
}

?>