﻿<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); require_once( getinfo('common_dir') . 'category.php' ); require_once( getinfo('common_dir') . 'page.php' ); $CI->load->helper('form');	// подгружаем хелпер форм$slug = mso_segment('4');$form = '<input name="chcat[]" type="checkbox" value="[ID]">';if ( !isset($options['include']) ) $options['include'] = array();if ( !isset($options['exclude']) ) $options['exclude'] = array();if ( !isset($options['format']) ) $options['format'] = $form.'  [LINK][TITLE]<sup>[COUNT]</sup>[/LINK]  № [ID]';if ( !isset($options['format_current']) ) $options['format_current'] = $form.' <span>[TITLE]<sup>[COUNT]</sup>  № [ID]</span>';if ( !isset($options['header']) ) $options['header'] = '';if ( !isset($options['hide_empty']) ) $options['hide_empty'] = 0;if ( !isset($options['order']) ) $options['order'] = 'category_menu_order';if ( !isset($options['order_asc']) ) $options['order_asc'] = 'ASC';if ( !isset($options['include_child']) ) $options['include_child'] = 0;if ( !isset($options['nofollow']) ) $options['nofollow'] = false;	$all = mso_cat_array('page', 0, $options['order'], $options['order_asc'], $options['order'], $options['order_asc'], $options['include'], $options['exclude'], $options['include_child'], $options['hide_empty'], false, false);	$id_cat = mso_get_cat_from_slug($slug, $full = false);$headcat = 'Новая категория';$nmcat = '';$descrcat = '';$slugcat = '';$nameparent = '';$ordercat = '';$linknewcat = '';if ($id_cat != false)	{	$linknewcat = '<a href="'.$plugin_url .'/category">'.t('СОЗДАТЬ КАТЕГОРИЮ','plugins/grgallery').'</a><hr class = "br"/>';	$headcat = t('Редактирование категории №'.$id_cat.' : ', 'plugins/grgallery');	$cat = mso_cat_array_single();	$nmcat = $cat[$id_cat]['category_name'];	$descrcat = $cat[$id_cat]['category_desc'];	$slugcat = $cat[$id_cat]['category_slug'];	$nameparent = $cat[$id_cat]['parents'];	$ordercat = $cat[$id_cat]['category_menu_order'];	}$outstart = '<h1 class="content">'.$headcat.$nmcat.'</h1>'.	'<form action="" method="post">'.mso_form_session('f_session_id').form_hidden('id_cat', $id_cat).	'<table style="width: 99%; border: none; line-height: 1.4em;">		<tr>			<td style="vertical-align: top; padding: 0 10px 0 0;">';	$out1 = '	<div class="item new_cat">		<p class="input"><strong>' . t('Название', 'admin') . ' </strong><input type="text" name="f_new_name" value="'.$nmcat.'"></p><br/>		<p class="textarea"><strong>' . t('Описание', 'admin') . ' </strong><textarea name="f_new_desc">'.$descrcat.'</textarea></p><br/>		<p class="input"><strong>' . t('Ссылка', 'admin') . ' </strong><input type="text" name="f_new_slug" value="'.$slugcat.'"></p><br/>		<p class="input short"><strong>' . t('Родитель', 'admin') . ' </strong><input type="text" name="f_new_parent" value="'.$nameparent.'"></p><br/>		<p class="input short"><strong>' . t('Порядок', 'admin') . ' </strong><input type="text" name="f_new_order" value="'.$ordercat.'"></p><br/>		<p class="input_submit"> <input type="submit" name="f_new_submit" value="' . t('Сохранить', 'admin') . '"></p>	</div>';		$out1 .= '</td><td style="vertical-align: top; width: 250px;">';		$out = '';	$out .= $linknewcat;	$out .= mso_create_list($all, array('childs'=>'childs', 'format'=>$options['format'], 'format_current'=>$options['format_current'], 'class_ul'=>'is_link', 'title'=>'category_name', 'link'=>'category_slug', 'current_id'=>false, 'prefix'=>'admin/grgallery/category/', 'count'=>'pages_count', 'slug'=>'category_slug', 'id'=>'category_id', 'menu_order'=>'category_menu_order', 'id_parent'=>'category_id_parent', 'nofollow'=>$options['nofollow'] ) );	$out .= form_submit('delcat', t('удалить отмеченные', 'plugins/grgallery') );		if ($out and $options['header']) $out = $options['header'] . $out;		$outend = '</td></tr>	</table>	</form>';	$out = $outstart.$out1.$out.$outend;?>