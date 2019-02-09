<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

if (!isset($options['gallery_name'])) $options['gallery_name'] = 'Фотографии';
if (!isset($options['gallery_desc'])) $options['gallery_desc'] = 'Фотографии Садовника';

if (!isset($options['gallery_slug'])) $options['gallery_slug'] = 'pictures';
if (!isset($options['picture_slug'])) $options['picture_slug'] = 'pictures';
if (!isset($options['album_slug'])) $options['album_slug'] = 'pictures';

if (!isset($options['gallery_prefix'])) $options['gallery_prefix'] = 'gallery_';
if (!isset($options['picture_prefix'])) $options['picture_prefix'] = 'picture_';
if (!isset($options['album_prefix'])) $options['album_prefix'] = 'albom_';

if (!isset($options['main_slug'])) $options['main_slug'] = 'pictures';


if (!isset($options['comments_plugin'])) $options['comments_plugin'] = '';

if ( !isset($options['template']) ) $options['template'] = 'default'; 

if ( !isset($options['gallery_pag_count']) ) $options['gallery_pag_count'] = 15;

if ( !isset($options['default_album_thumb_url']) ) $options['default_album_thumb_url'] = '/application/maxsite/plugins/taggallery/templates/default/images/album.png';

if ( !isset($options['default_gallery_thumb_url']) ) $options['default_gallery_thumb_url'] = '/application/maxsite/plugins/taggallery/templates/default/images/album.png';

if ( !isset($options['end_text']) ) $options['end_text'] = '';

if ( !isset($options['cache_flag']) ) $options['cache_flag'] = false;
if ( !isset($options['date_field']) ) $options['date_field'] = 'picture_date';

if (!isset($options['all_gallerys_slug'])) $options['all_gallerys_slug'] = 'all-gallerys';
if ( !isset($options['all_gallerys_text']) ) $options['all_gallerys_text'] = 'Все галереи';
if ( !isset($options['all_gallerys_desc']) ) $options['all_gallerys_desc'] = 'Все имеющиеся галереи';

if ( !isset($options['gallerys_not_in_slug']) ) $options['gallerys_not_in_slug'] = 'nerazobrannoe';
if ( !isset($options['gallerys_not_in_text']) ) $options['gallerys_not_in_text'] = 'Остальные галереи';
if ( !isset($options['gallerys_not_in_desc']) ) $options['gallerys_not_in_desc'] = 'Галереи не вошедшие ни в один из альбомов';
?>