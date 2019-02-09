<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 */

// начало шаблона
require(getinfo('shared_dir') . 'main/main-start.php');
	  

// доступ к CI
$CI = & get_instance();

// выводим навигацию новостей
wiki_menu();

// загружаем опции
$options = mso_get_option('plugin_dignity_wiki', 'plugins', array());
if ( !isset($options['limit']) ) $options['limit'] = 10;
if ( !isset($options['slug']) ) $options['slug'] = 'wiki';
if ( !isset($options['header']) ) $options['header'] = t('Wiki', __FILE__);
if ( !isset($options['meta_description']) ) $options['meta_description'] = '';
if ( !isset($options['meta_keywords']) ) $options['meta_keywords'] = '';
if ( !isset($options['textdo']) ) $options['textdo'] = '';
if ( !isset($options['textposle']) ) $options['textposle'] = '';

echo '<h1>' . $options['header'] . '</h1>';
echo '<p>' . $options['textdo'] . '</p>';

// meta-тэги
mso_head_meta('title', $options['header']);
mso_head_meta('description', $options['meta_description']);
mso_head_meta('keywords', $options['meta_keywords']);

$CI->db->from('dignity_wiki_category');
$CI->db->order_by('dignity_wiki_category_position', 'asc');
$query = $CI->db->get();

// если есть что выводить...
if ($query->num_rows() > 0)	
{
	$all_categorys = $query->result_array();
	
	$out_category = '';
    
    foreach ($all_categorys as $one_category) 
	{
	   
            // узнаем количество записей в категории
            $CI->db->where('dignity_wiki_approved', true);
            $CI->db->where('dignity_wiki_category', $one_category['dignity_wiki_category_id']);
            $CI->db->from('dignity_wiki');
            $entry_in_cat = $CI->db->count_all_results();
            
            if ($entry_in_cat > 0)
			{
	   
                    $out_category .= '<h2>' . $one_category['dignity_wiki_category_name'] . '</h2>';
            
            }
            
            // берём статьи из базы
            $CI->db->from('dignity_wiki');
            if (!is_login())
            {
                $CI->db->where('dignity_wiki_approved', true);
            }
            $CI->db->where('dignity_wiki_category', $one_category['dignity_wiki_category_id']);
            $query = $CI->db->get();
            
            // если есть что выводить...
            if ($query->num_rows() > 0)	
            {
                $all_articles = $query->result_array();
                
                $out_category .= '<ul>';
                
                foreach ($all_articles as $one_article) 
                {
                    
                    $status = '';
                    if ($one_article['dignity_wiki_approved'])
                    {
                        $status .= '';
                    }
                    else
                    {
                        $status .= ' (' . t('Черновик', __FILE__) . ') ';
                    }
                    
                    $out_category .= '<li><a href="' . getinfo('siteurl') . $options['slug'] . '/view/' . $one_article['dignity_wiki_id'] . '">' . $one_article['dignity_wiki_title'] . '</a>' . $status . '</li>';
                
                    if (is_login())
                    {	
                            # удаление
                            if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_wiki_delete')) )
                            {
				                    mso_checkreferer();
				
				                    if ( !isset($post['f_id'])) $post['f_id'] = $one_article['dignity_wiki_id'];
				
				                    $CI->db->where('dignity_wiki_id', $post['f_wiki_id']);
				                    $CI->db->delete('dignity_wiki');
					
				                    mso_flush_cache();
				
				                    $out_category .= '<div class="update">' . t('Удалено!', __FILE__) . '<script>location.replace(window.location); </script></div>';
			                 }
			
			                 # опубликовать
			                 if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_wiki_approved')) )
			                 {
				                    mso_checkreferer();
				
				                    if ( !isset($post['f_id'])) $post['f_id'] = $one_article['dignity_wiki_id'];
				
				                    $CI->db->where('dignity_wiki_id', $post['f_wiki_id']);
				
				                    $data = array (
					                           'dignity_wiki_approved' => 1,
                                    );
				
				                    $CI->db->where('dignity_wiki_id', $post['f_wiki_id']);
				                    $CI->db->update('dignity_wiki', $data);
                                    echo '<script>location.replace(window.location); </script>';
					
				                    mso_flush_cache();
			                 }
		
			                 $form = '';
			                 $form .= '<form action="" method="post">' . mso_form_session('f_session_id');
			                 $form .= '<input type="hidden" name="f_wiki_id" value="' . $one_article['dignity_wiki_id'] . '" />';
			
			                 if (!$one_article['dignity_wiki_approved'])
			                 {
				                    $form .= ' <input type="submit" name="f_submit_dignity_wiki_approved" onClick="if(confirm(\'' . t('Опубликовать?', __FILE__) . ' ' . $one_article['dignity_wiki_title'] . '\')) {return true;} else {return false;}" value="' . t('Опубликовать', __FILE__) . '">';	
			                 }
			
                             $form .= ' <input type="submit" name="f_submit_dignity_wiki_delete" onClick="if(confirm(\'' . t('Удалить?', __FILE__) . ' ' . $one_article['dignity_wiki_title'] . '\')) {return true;} else {return false;}" value="' . t('Удалить', __FILE__) . '">';
                             $form .= '</form>';
			
			                 $out_category .= $form;	
                    }
                }
                
                $out_category .= '</ul>';
            }
       
    }
    
    echo $out_category;
}

echo '<p>' . $options['textposle'] . '</p>';

require(getinfo('shared_dir') . 'main/main-end.php');
	  

# end of file