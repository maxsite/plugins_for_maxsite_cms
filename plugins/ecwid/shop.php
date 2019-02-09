<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 *
 * Alexander Schilling
 * (c) http://alexanderschilling.net
 *
 */

if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);

ecwid();

if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);

#end of file
