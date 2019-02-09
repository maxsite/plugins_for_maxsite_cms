<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
/*
в файле Функции для работы с масивами данных (для плагина галереи картинок taggallery)
*/


// возвращает массив картинок
function taggallery_get_pictures($par = array() , &$pagination )
{
	 $CI = & get_instance();
 

  if (!isset($par['tags'])) $par['tags'] = array();
  if (!isset($par['tag'])) $par['tag'] = '';
  if (!isset($par['count'])) $par['count'] = 0;
  if (!isset($par['limit'])) $par['limit'] = false;
  if (!isset($par['file'])) $par['file'] = ''; 
  if (!isset($par['result_key'])) $par['result_key'] = false; // 
  if (!isset($par['return_tags'])) $par['return_tags'] = true; // 
  if (!isset($par['pag'])) $par['pag'] = false; // 
  
  if ($par['pag'] and $par['count']) 
    	$CI->db->select('SQL_CALC_FOUND_ROWS *', false);
  else $CI->db->select('*');
  
	 if (isset($par['nerazobrannoe']) and $par['nerazobrannoe']) 
	 {
	    $pictures_in_gallery = taggallery_get_pictures_in_gallery();
	    if ($pictures_in_gallery)
	    { 
	       $CI->db->from('pictures');
   			 $CI->db->where_not_in('pictures.picture_id', $pictures_in_gallery);
   		}	 
	 }
	 else $pictures_in_gallery = false;
	 
	 if (isset($par['pictures_id']) )
			$CI->db->where_in('picture_id', $par['pictures_id']);	
		
	 if (isset($par['picture_id']) )
			$CI->db->where('picture_id', $par['picture_id']);	
	
	 if (isset($par['gallery_id']))
		{
			$CI->db->where('picgal_gallery_id', $par['gallery_id']);
			$CI->db->join('picgal', 'picgal.picgal_picture_id = pictures.picture_id');
		}	 

	 if ($par['tags']) 
		{
			$CI->db->join('picgal', 'picgal.picgal_picture_id = pictures.picture_id', 'left');
			$CI->db->join('gallerys', 'picgal.picgal_gallery_id = gallerys.gallery_id');
			$CI->db->where_in('gallerys.gallery_name', $par['tags']);
			$CI->db->group_by('picture_id');
		}
	 
	 if ($par['tag']) 
		{
			$CI->db->join('picgal', 'picgal.picgal_picture_id = pictures.picture_id', 'left');
			$CI->db->join('gallerys', 'picgal.picgal_gallery_id = gallerys.gallery_id');
			$CI->db->where('gallerys.gallery_name', $par['tag']);
		}	 
	 
	 if (isset($par['picture_slug']) )
			$CI->db->where('picture_slug', $par['picture_slug']);
			 
	 if (isset($par['dir']) )
			$CI->db->where('picture_dir', $par['dir']);	 

	 if (isset($par['source_id']) )
			$CI->db->where('picture_source_id', $par['source_id']);	 
	 
	 if ($par['file']) 
			$CI->db->where('pictures.picture_file', $par['file']);
			
	if (isset($par['sort_field']))
	{
	  if ($par['sort_field'] == 'random')
	  {
	     $CI->db->order_by('page_id', 'random');
	  }
	  else
	  {
	     $array_sort_fields = array('picture_id' , 'picture_file', 'picture_date', 'picture_date_file', 'picture_date_photo' , 'picture_position' , 'picture_width' , 'picture_height' , 'picture_view_count' , 'picture_source_id');	
	     $array_sort_order = array('asc' , 'desc');	
	 	
	     if (!isset($par['sort_order']) or !in_array($par['sort_order'] , $array_sort_order) ) $par['sort_order'] = 'desc';
	     if (in_array($par['sort_field'] , $array_sort_fields))
	         $CI->db->order_by($par['sort_field'], $par['sort_order']);
	  }
	}
	
	if ($par['limit']) $CI->db->limit($par['limit']); // не более $limit
	if ($par['pag'] and $par['count']) $CI->db->limit($par['count'], mso_current_paged() * $par['count'] - $par['count'] );
	 
	if ($pictures_in_gallery)
     $query = $CI->db->get();
  else   
     $query = $CI->db->get('pictures');
     
     
	 
//	if ($query)  
	  if ($query->num_rows()) // если есть картинки
	  {
	   if ($par['pag'] and $par['count']) $pagination = mso_sql_found_rows($par['count']); // определим общее кол-во записей для пагинации
 
	   $result_pictures = $query->result_array();
	   $pictures = array();	 
	   
	   // получим кол-во комментариев
	   if (isset($par['comments_count']) and $par['comments_count']) $comments_count = taggallery_get_comments_count();
	   else $comments_count = false;
	   
	   foreach ($result_pictures as $picture)
	   {
	     
	  //   echo $picture['picture_file'];
	     
	     $picture['gallerys'] = array();
 	     $picture['tags'] = array();
 	     if ( $comments_count and isset($comments_count[$picture['picture_id']]) ) $picture['comments_count'] = $comments_count[$picture['picture_id']];
 	     else $picture['comments_count'] = 0;
 	     //$picture['picture_date'] = date ("y-m-d" , $picture['picture_date']);
 	     
 	     
	     // если нужно возвращать метки картинки
	     if ($par['return_tags']) 
	     { 
	     
	         $CI->db->select('*');
	         $CI->db->from('gallerys');
			     $CI->db->where('picgal_picture_id', $picture['picture_id']);
			     $CI->db->join('picgal', 'picgal.picgal_gallery_id = gallerys.gallery_id');
			     
			     if ( $query = $CI->db->get() )
	           if ($query->num_rows()) // если есть галереи
	           {
	             $picture['gallerys'] = $query->result_array();
	             foreach ($picture['gallerys'] as $gal) 
	             {
	                 $picture['tags'][] = $gal['gallery_name'];
	             }    
	           }
	     }	
	     // если нужно сделать какое-то поле ключом массива
	     if ( ($par['result_key']) and isset($picture[$par['result_key']]) ) 
	         $pictures[$par['result_key']] = $picture;
	     else $pictures[] = $picture;   
	   }
	   
	   return $pictures;
	  }
	$pagination = false;
	return false;
  
}

// получить галереи по метке, слугу или номеру
function taggallery_get_gallerys($par = array())
{
	 $CI = & get_instance();
  $CI->db->select('*');

	 if (!isset($par['hash_tags'])) $par['hash_tags'] = false;
	 
	 if (isset($par['nerazobrannoe']) and $par['nerazobrannoe']) 
	 {
	    $gallery_in = taggallery_get_gallerys_in_album();
	    if ($gallery_in)
	    {
	      $CI->db->from('gallerys');
			  $CI->db->where_not_in('gallery_id', $gallery_in);
			} 
	 }	
	 else $gallery_in = false;
	 
	 if (isset($par['gallery_name']) and $par['gallery_name']) 
	 {
			$CI->db->where('gallery_name', $par['gallery_name']);
	 }	 
	 elseif (isset($par['gallery_slug']) and $par['gallery_slug'])
	 {
			$CI->db->where('gallery_slug', $par['gallery_slug']);
	 }
	 elseif (isset($par['gallery_id']) and $par['gallery_id'])
	 {
			$CI->db->where('gallery_id', $par['gallery_id']);
	 }	 
	 elseif (isset($par['picture_id']) and $par['picture_id'])
	 {
		  $CI->db->join('picgal', 'picgal.picgal_gallery_id = gallerys.gallery_id');	 
			$CI->db->where('picgal.picgal_picture_id', $par['picture_id']);
	 }		 
	 elseif (isset($par['album_id']) and $par['album_id'])
	 {
		  $CI->db->join('galalb', 'galalb.galalb_gallery_id = gallerys.gallery_id');	 
			$CI->db->where('galalb.galalb_album_id', $par['album_id']);
	 }	 

	 $gallerys = array();
	 
	 $uploads_dir = getinfo('uploads_dir');
	 $uploads_url = getinfo('uploads_url');
	 
	 if ($gallery_in) 
	   $query = $CI->db->get();
	 else
	   $query = $CI->db->get('gallerys');
	 
   if ($query) 
	   if ($query->num_rows()) // если есть галереи 
	   {
	     
	     $result_gallerys = $query->result_array();
	   
	     if ($result_gallerys)
	       foreach ($result_gallerys as $gallery)
	       {
	          if (!$par['hash_tags'] and (substr($gallery['gallery_name'],0,1) == '_')) continue; //не выводим служебные галереи
	          
	          $gallery['thumb_url'] = false;
	       	  $gallery['albums'] = array();

	          if ( !isset($par['simple']) or !$par['simple'])
	          {
	           // получим обложку
	           $CI->db->select('picture_dir , picture_file , picture_mini_url');
	           $CI->db->where('picture_id', $gallery['gallery_thumb_id']);
             if ($query = $CI->db->get('pictures'))   
	             if ($query->num_rows()) // если есть обложка
	             {
	    	         $pictures = $query->result_array();
	    	         if ($pictures)
	    	         {
	    	            foreach ($pictures as $picture); 
	      	      	    
	                  if (isset($picture['picture_mini_url'])  and $picture['picture_mini_url'])
	                       $gallery['thumb_url'] = $picture['picture_mini_url'];
	                  elseif ( isset ($picture['picture_dir']) and isset($picture['picture_file']) )
	                  {
                      $mini_dir = $uploads_dir . $picture['picture_dir'] . 'mini/' . $picture['picture_file'];
                      if (file_exists($mini_dir))
                      $gallery['thumb_url'] = $uploads_url . $picture['picture_dir'] . 'mini/' . $picture['picture_file']; 
                      else $gallery['thumb_url'] = $uploads_url . $picture['picture_dir'] . $picture['picture_file']; 	   
	                  }
	               }   
	             }      

	         // получим альбомы галереи

	         $CI->db->select('album_title , album_slug');
		       $CI->db->join('galalb', 'albums.album_id = galalb.galalb_album_id');	 
	         $CI->db->where('galalb.galalb_gallery_id', $gallery['gallery_id']);
	         if ($query = $CI->db->get('albums'))
	           if ($query->num_rows() > 0 ) // если есть альбомы
	           {
	    	       $gallery['albums'] = $query->result_array();
	           }  
	          
	        }  
          $gallerys[] = $gallery;
	      } // foreach ($result_gallerys as $gallery);

	   }
	   
	   return $gallerys;
}	 


/*
// получить галереи
function taggallery_get_gallerys($par = array())
{
	 $CI = & get_instance();

$par['vid'] == 'easy';
	 if (isset($par['vid']) and $par['vid'] == 'easy') 
	 {
	    $CI->db->select('*');
	    $CI->db->from('galerys');	
	 }	
	 else
	 {
	 
	    $CI->db->select('*galerys , pictures.picture_mini_url , pictures.picture_dir , pictures.picture_file');
	    $CI->db->from('galerys');	 
			$CI->db->join('pictures', 'pictures.picture_id = gallerys.gallery_thumb_id');	 
	 }


	 if (isset($par['album_id']) and $par['album_id']) 
	 {
			$CI->db->where('gallery_album_id', $par['gallery_album_id']);
	 }	


	    $CI->db->select('*');
	    $CI->db->from('galerys');	
	 $query = $CI->db->get();   
	 
	 
	 if ($query->num_rows() > 0) // если есть галереи
	 {
	   $gallerys = $query->result_array();
	   return $gallerys;
	 }
	 else return false;
}	 
*/

 
// получим один альбом по id или slug
function taggallery_get_album($par = array())
{
	$CI = & get_instance();
  $album = array();
  
 
	$CI->db->select('*');
	if (isset($par['album_slug']) and $par['album_slug'])
	        $CI->db->where('album_slug', $par['album_slug']);
	elseif (isset($par['album_id']) and $par['album_id'])
	        $CI->db->where('album_id', $par['album_id']);	
  else return $album;
  
	if ($query = $CI->db->get('albums'))   
	  if ($query->num_rows()) // если есть альбом
	     foreach ($query->result_array() as $album);
	 
  return $album; 
}

// получим альбомы
// если $par['vid'] == 'simple' то получаем без гаерей
// иначе получаем и галереи каждого альбома
function taggallery_get_albums($par = array())
{
  $CI = & get_instance();

  $albums = array();

	$CI->db->select('*');
	
	if (isset($par['album_slug']) and $par['album_slug'])
	        $CI->db->where('album_slug', $par['album_slug']);
	elseif (isset($par['album_id']) and $par['album_id'])
	        $CI->db->where('album_id', $par['album_id']);	
	elseif (isset($par['gallery_id']) and $par['gallery_id'])
	{
          $CI->db->join('galalb', 'galalb.galalb_album_id = albums.album_id');
	        $CI->db->where('galalb.galalb_gallery_id', $par['gallery_id']);	  
  }
  
  
	if ($query = $CI->db->get('albums'))   
	  if ($query->num_rows() >0) // если есть альбомs
    {
	     $res_albums = $query->result_array();
	  
       if (!isset($par['simple']) or !$par['simple']) 
       {
	       foreach ($res_albums as $album)
	       { 
	          $album['gallerys'] = array();
	    
			      $CI->db->select('gallerys.gallery_name , gallerys.gallery_slug');
			      $CI->db->join('galalb', 'galalb.galalb_gallery_id = gallerys.gallery_id');
			      $CI->db->where('galalb.galalb_album_id', $album['album_id']);

			      if ($query = $CI->db->get('gallerys'))
			         if ($query->num_rows() >0)
			         {
			            $gallerys  = $query->result_array();	    
    	            foreach ($gallerys as $gallery) $album['gallerys'][] = $gallery;
               }   

            $albums[] = $album;
         } 
         return $albums;    
       }
       else return $res_albums;  
	}
	
  return $albums;
}


//функция возвращает массив picture_id => comments_count
function taggallery_get_comments_count()
{
	    $CI = & get_instance();

      $comments_count = array();
   
      global $MSO;
      if ( !in_array('other_comments', $MSO->active_plugins) ) return $comments_count = array();
   
			$CI->db->select('SQL_BUFFER_RESULT `element_id_in_table`, COUNT(`element_id_in_table`) AS `comments_count`', false);
			$CI->db->where('comments_approved', '1');
			$CI->db->join('elements', 'elements.element_id = comments_element_id');
			$CI->db->where('elements.element_table_name', 'pictures');
			$CI->db->group_by('element_id_in_table');
			$CI->db->from('other_comments');

			$query = $CI->db->get();
			$result_array = $query->result_array();
			
			// переместим все в массив 
			$page_count_comments = array();
			foreach ($result_array as $key=>$val)
			{
				$comments_count[$val['element_id_in_table']] = $val['comments_count'];
			}
	 return $comments_count;  
}
       

//функция возвращает массив gallery_id => pictures_count
function taggallery_get_pictures_count()
{
	    $CI = & get_instance();

			$CI->db->select('SQL_BUFFER_RESULT `picgal_gallery_id`, COUNT(`picgal_gallery_id`) AS `pictures_count`', false);
			$CI->db->group_by('picgal_gallery_id');
			$CI->db->from('picgal');

			$query = $CI->db->get();
			$result_array = $query->result_array();
			
			// переместим все в массив 
			$pictures_count = array();
			foreach ($result_array as $key=>$val)
			{
				$pictures_count[$val['picgal_gallery_id']] = $val['pictures_count'];
			}
			
	 return $pictures_count;  
}       


// получим массив номеров галерей, состоящих в каком-нибудь альбоме
function taggallery_get_gallerys_in_album()
{
   // получим массив картинок в галереях
	    $CI = & get_instance();

      $CI->db->select('`galalb_gallery_id`, COUNT(`galalb_gallery_id`) AS `gal_count`', false);
			$CI->db->group_by('galalb_gallery_id');
			$CI->db->from('galalb');

			$query = $CI->db->get();
			$result_array = $query->result_array();
			
			$gallery_in = array();
			foreach ($result_array as $key=>$val)
			{
				$gallery_in[] = $val['galalb_gallery_id'];
			}
			return $gallery_in;
}

// получим массив номеров галерей, не состоящих ни в одном альбоме
function taggallery_get_galerys_not_in_album()
{

   // получим массив картинок в галереях
	    $CI = & get_instance();

			$gallerys_in = taggallery_get_gallerys_in_album();
		
		//	получим массив картинок не в галереях
			$CI->db->select('gallery_id');
			$CI->db->where_not_in('gallery_id', $gallerys_in);
			$query = $CI->db->get('gallerys');
			$result_array = $query->result_array();		
		
			$gallery_not_in = array();
			foreach ($result_array as $key=>$val)
			{
				$gallery_not_in[] = $val['gallery_id'];
			}		
		
	 return $gallery_not_in; 
}


function taggallery_get_pictures_in_gallery()
{
   // получим массив картинок в галереях
	    $CI = & get_instance();

      $CI->db->select('`picgal_picture_id`, COUNT(`picgal_picture_id`) AS `gal_count`', false);
			$CI->db->group_by('picgal_picture_id');
			$CI->db->from('picgal');

			$query = $CI->db->get();
			$result_array = $query->result_array();
			
			$pictures_id_in_gallery = array();
			foreach ($result_array as $key=>$val)
			{
				$pictures_id_in_gallery[] = $val['picgal_picture_id'];
			}
			return $pictures_id_in_gallery;
}


function taggallery_get_pictures_not_in_gallery()
{

   // получим массив картинок в галереях
	    $CI = & get_instance();

			$pictures_id_in_gallery = taggallery_get_pictures_in_gallery();
		
		//	получим массив картинок не в галереях
			$CI->db->select('picture_id');
			$CI->db->where_not_in('picture_id', $pictures_id_in_gallery);
			$query = $CI->db->get('pictures');
			$result_array = $query->result_array();		
		
			$pictures_id_not_in_gallery = array();
			foreach ($result_array as $key=>$val)
			{
				$pictures_id_not_in_gallery[] = $val['picture_id'];
			}		
		
	 return $pictures_id_not_in_gallery; 
}


?>