<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

#################
# это файл опций
#################



$template = mso_segment(5);
mso_admin_plugin_options('taggallery_' . $template , 'taggallery', 
	array(
	
		'breadcumbs_razd' => array(
						'type' => 'text', 
						'name' => 'Разделитель хлебных крошек', 
						'description' => 'Чем разеляются ссылки в хлебных крошках.', 
						'default' => ' >> '
					),	
					
// опции вывода обложки альбома						
		'album_width' => array(
						'type' => 'text', 
						'name' => 'Выводимая ширина обложки альбома', 
						'description' => 'Будет добавлена как атрибут width при выводе', 
						'default' => '170'
					),	
	
// опции вывода обложки галереи						
		'gallery_width' => array(
						'type' => 'text', 
						'name' => 'Выводимая ширина обложки галереи', 
						'description' => 'Будет добавлена как атрибут width при выводе', 
						'default' => '180'
					),						
						
// опции вывода миниатюр картинок галереи						
		'gallery_img_class' => array(
						'type' => 'text', 
						'name' => 'Класс ссылки с миниатюры картинки в галерее', 
						'description' => 'Можно указать свой класс миниатюры', 
						'default' => ''
					),						
		'gallery_picture_width' => array(
						'type' => 'text', 
						'name' => 'Выводимая ширина миниатюры картинки галереи', 
						'description' => 'Будет добавлена как атрибут width при выводе миниатюры', 
						'default' => '180'
					),	

		'gallery_sort' => array(
						'type' => 'text', 
						'name' => 'ППоле сортировки картинок', 
						'description' => 'date , file , desc , rating , width , type , height', 
						'default' => ''
					),		
		'gallery_sort_order' => array(
						'type' => 'text', 
						'name' => 'Порядок сортировки', 
						'description' => 'asc , desc', 
						'default' => ''
					),			
																
// опции вывода страницы картинки
		'picture_do' => array(
						'type' => 'text', 
						'name' => 'Что до вывода одиночной картинки', 
						'description' => 'На странице одиночной картинки', 
						'default' => '<table width="100%"><tr align="center"><td>'
					),	
		'picture_posle' => array(
						'type' => 'text', 
						'name' => 'Что после вывода одиночной картинки', 
						'description' => 'На странице одиночной картинки', 
						'default' => '</td></tr></table>'
					),		
		'picture_img_class' => array(
						'type' => 'text', 
						'name' => 'Класс ссылки с одиночной картинкикартинки', 
						'description' => 'Можно указать свой класс одиночной картинки', 
						'default' => 'lightbox'
					),						
		'picture_picture_width' => array(
						'type' => 'text', 
						'name' => 'Выводимая ширина одиночной картинки', 
						'description' => 'Будет добавлена как атрибут width при выводе картинки на странице картинки<br/>Если пустое - width не добавляется.', 
						'default' => '600'
					),	

		'see_tags_razd' => array(
						'type' => 'text', 
						'name' => 'Разделитель меток.', 
						'description' => 'Чем разеляются ссылки на метки мтатей и на галереи картинки.', 
						'default' => ' | '
					),
		'title_posts_on_tag' => array(
						'type' => 'text', 
						'name' => 'Заголовок блока меток записей как у картинки.', 
						'description' => 'В блоке будут выведены ссылки на теги записей как у картинки.', 
						'default' => 'Статьи по теме картинки.'
					),
		'title_gallerys_on_tag' => array(
						'type' => 'text', 
						'name' => 'Заголовок блока других галерей картинки.', 
						'description' => 'В блоке будут выведены ссылки на галереи в которых картинка.', 
						'default' => 'Галереи в которых картинка.'
					),
															
		
// опции вывода блоков похожих статей 
		'similar_posts_count' => array(
						'type' => 'text', 
						'name' => 'Колличество похожих страниц.', 
						'description' => '', 
						'default' => '5'
					),	
		'similar_posts_width' => array(
						'type' => 'text', 
						'name' => 'Ширина миниатюры похожих страниц.', 
						'description' => '', 
						'default' => '150'
					),		
		'similar_posts_title' => array(
						'type' => 'text', 
						'name' => 'Заголовок блока похожих страниц.', 
						'description' => 'Например, <h3>Cтатьи по теме:</h3>', 
						'default' => '<h3>Cтатьи по теме:</h3>'
					),	
		'similar_posts_start' => array(
						'type' => 'text', 
						'name' => 'Начало блока похожих страниц.', 
						'description' => '', 
						'default' => '<div class="page_other_pages">'
					),	
		'similar_posts_do' => array(
						'type' => 'text', 
						'name' => 'Что до цикла вывода похожих страниц.', 
						'description' => '', 
						'default' => '<table width="100%" border="0"><tr>'
					),		
		'similar_posts_posle' => array(
						'type' => 'text', 
						'name' => 'Что после цикла вывода похожих страниц.', 
						'description' => '', 
						'default' => '</tr></table>'
					),	
		'similar_posts_end' => array(
						'type' => 'text', 
						'name' => 'Конец блока похожих страниц.', 
						'description' => '', 
						'default' => '</div>'
					),	
		'similar_posts_format' => array(
						'type' => 'text', 
						'name' => 'Формат вывода похожей страницы в цикле.', 
						'description' => '[link] - ссылка на странцу , [image] - миниатюра страницы', 
						'default' => '<td valign="bottom">[link]<br>[image]</td>'
					),	
		'similar_posts_full_text' => array(
						'type' => 'text', 
						'name' => 'Искать картинку во всем контенте (или до cut).', 
						'description' => 'false (true)', 
						'default' => 'false'
					),						
					

// опции вывода карусели изображений
		'carousel_picture_do' => array(
						'type' => 'text', 
						'name' => 'Что до вывода блока картинки карусели', 
						'description' => 'На странице одиночной картинки в карусели', 
						'default' => '<li><div style="width: 160px">'
					),	
		'carousel_picture_posle' => array(
						'type' => 'text', 
						'name' => 'ЧЧто после вывода блока картинки карусели', 
						'description' => 'На странице одиночной картинки в карусели', 
						'default' => '</div></li>'
					),		
/*
		'carousel_img_class' => array(
						'type' => 'text', 
						'name' => 'Класс картинки обложки альбома', 
						'description' => 'Можно указать свой класс миниатюры в карусели', 
						'default' => ''
					),						
*/
		'carousel_picture_width' => array(
						'type' => 'text', 
						'name' => 'Выводимая ширина миниатюры в карусели', 
						'description' => 'Будет добавлена как атрибут width при выводе миниатюры в карусели', 
						'default' => '150'
					),

// опции вывода блока последних добавленных картинок
		'last_pictures_do' => array(
						'type' => 'text', 
						'name' => 'Что до вывода блока последних картинок', 
						'description' => 'В блоке последних картинок', 
						'default' => '<div class="pictures">'
					),	
		'last_pictures_posle' => array(
						'type' => 'text', 
						'name' => 'Что после вывода блока последних картинок', 
						'description' => 'В блоке последних картинок', 
						'default' => '</div>'
					),			
		'last_picture_do' => array(
						'type' => 'text', 
						'name' => 'Что до картинки', 
						'description' => 'В блоке последних картинок', 
						'default' => '<div class="picture">'
					),	
		'last_picture_posle' => array(
						'type' => 'text', 
						'name' => 'Что после картинки', 
						'description' => 'В блоке последних картинок', 
						'default' => '</div>'
					),								
		'last_title' => array(
						'type' => 'text', 
						'name' => 'Заголовок блока последних картинок', 
						'description' => 'Текст заголовка блока последних картинок', 
						'default' => '<H2>Последние фото</H2>'
					),						
	  'last_count' => array(
						'type' => 'text', 
						'name' => 'Колличество последних картинок', 
						'description' => 'Колличество в блоке последних картинок, 0 - блок не выводится', 
						'default' => 3
					),		
	  'last_width' => array(
						'type' => 'text', 
						'name' => 'Ширина последних картинок', 
						'description' => 'Ширина в блоке последних картинок, если нужно изменить', 
						'default' => 100
					),		
	  'last_img_class' => array(
						'type' => 'text', 
						'name' => 'class последних картинок', 
						'description' => 'class в блоке последних картинок', 
						'default' => ''
					),	
		),
	'Шаблон: ' . $template, // титул
	'Укажите необходимые опции шаблона галерей.' // инфо
);

?>