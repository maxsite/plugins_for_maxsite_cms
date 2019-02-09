<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

# коммментарии плагина Comments
# файл для размещения в папке шаблона /type/page/units/ если нет хука custom_ts_file

if( $code = mso_hook('page_comments', $page) ) echo $code;

# end file