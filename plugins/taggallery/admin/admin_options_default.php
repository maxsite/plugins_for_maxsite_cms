<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

if( !isset($options_admin['date_field']) ) $options_admin['date_field'] = 'picture_date';
if( !isset($options_admin['sort_field']) ) $options_admin['sort_field'] = 'file';

if( !isset($options_admin['admin_picture_width'])) $options_admin['admin_picture_width'] = '200';  


if( !isset($options_admin['collage_picture_count'])) $options_admin['collage_picture_count'] = '5';  
if( !isset($options_admin['collage_picture_width'])) $options_admin['collage_picture_width'] = '200';  
if( !isset($options_admin['collage_picture_height'])) $options_admin['collage_picture_height'] = '150';  
if( !isset($options_admin['collage_angles'])) $options_admin['collage_angles'] = '20,-15,25,10,20,-10,15,25,-15';  
if( !isset($options_admin['collage_dir'])) $options_admin['collage_dir'] = 'header_collage';  


if( !isset($options_admin['collage_pointx'])) $options_admin['collage_pointx'] = '20';  
if( !isset($options_admin['collage_pointy'])) $options_admin['collage_pointy'] = '20';  
if( !isset($options_admin['collage_borderx'])) $options_admin['collage_borderx'] = '8';  
if( !isset($options_admin['collage_bordery'])) $options_admin['collage_bordery'] = '8';  
if( !isset($options_admin['collage_stepx'])) $options_admin['collage_stepx'] = '150';  


if( !isset($options_admin['collage_gallerys'])) $options_admin['collage_gallerys'] = '';  
if( !isset($options_admin['collage_sort_field'])) $options_admin['collage_sort_field'] = 'picture_view_count';  
if( !isset($options_admin['collage_sort_order'])) $options_admin['collage_sort_order'] = 'desc';  
if( !isset($options_admin['collage_limit'])) $options_admin['collage_limit'] = '20';  
	    

?>