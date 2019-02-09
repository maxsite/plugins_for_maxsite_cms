<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

# коммментарии плагина Comments
# файл реакции на хук custom_ts_file

if( isset($page) ) echo comments_show( $page );

# end file