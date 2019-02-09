<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

<div style="border: 2px solid yellow; margin: 20px 0; text-align: center; color: red; padding: 10px; font-size: 18pt;">
<?php 

# получаем опции помодульно
# сами опции заданы в options/test.php

# ключ_ID тип
$module_options = mso_get_option('test_' . $module_id, 'modules', array());

# выводим
if (isset($module_options['title'])) echo $module_options['title']; 

?>

</div>