<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Yuriy
 * (c) http://jet-web.org/
 */

# функция автоподключения плагина
function seotext_autoload($args = array())
{
    $installed = mso_get_option('seotext_exits', 'plugins'); 
     
    if ($installed != 'true') {
            $CI = & get_instance(); 
            $CI->db->select('*');      
            $CI->db->from('page_type');  
            $exist = false;
            $query = $CI->db->get();  
            if ($query->num_rows() > 0)
            {
                $pages = $query->result_array();        
                foreach ($pages as $key=>$page)
                {
                    if ($page['page_type_name']=='seotext') $exist = true;
                }
            }
            if (!$exist) {
                $CI->db->query("INSERT INTO mso_page_type (page_type_name, page_type_desc) VALUES ('seotext','Страницы для сеотекста')");     
                mso_add_option('seotext_exits', 'true', 'plugins');       
            }
            else mso_add_option('seotext_exits', 'true', 'plugins');     
    }         
    if ( is_type('home') || is_type('category'))
    {   
        
        $options = mso_get_option('seotext', 'plugins', array() ); // получаем опции
    
        // заменим заголовок, чтобы был в  h2 class="box"
        if (!isset($options['down'])) $options['down']=false;
        if ($options['down']) mso_hook_add( 'seotext_page_end', 'seotext_content');
        else mso_hook_add( 'seotext_page_start', 'seotext_content');

    }       
}


function seotext_content($arg = array())
{
    
    global $MSO;
    $CI = & get_instance();  
    $category = 0;
    if (count($MSO->data['uri_segment'])>0) { 
        $category = $MSO->data['uri_segment'][2];
        $CI->db->select('category_id'); 
        $CI->db->where('category_slug', $category); 
        $CI->db->from('category'); 
        $query = $CI->db->get(); 
        $cat = $query->result_array();
        if (count($cat)>0)
            $category = $cat[0]['category_id'];  
        
    }
    else $category = 0;
    if ($category>0) {
        $CI->db->select('*');
        $CI->db->where('page_type_name', 'seotext'); 
        $CI->db->where('cat2obj.category_id', intval($category)); 
        $CI->db->join('page_type', 'mso_page_type.page_type_id = page.page_type_id');   
        $CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id');  
        $CI->db->from('page');  
        $query = $CI->db->get(); 
        $seopage = $query->result_array(); 
    }   
    else {                          
        $query = $CI->db->query("SELECT * FROM `mso_page` JOIN `mso_page_type` ON `mso_page_type`.`page_type_id` = `mso_page`.`page_type_id` LEFT JOIN `mso_cat2obj` ON `mso_cat2obj`.`page_id` = `mso_page`.`page_id` WHERE `page_type_name` = 'seotext' GROUP BY `mso_page`.`page_id` HAVING count(`mso_cat2obj`.`page_id`) = 0");
        $seopage = $query->result_array();  
    }     
    if (count($seopage)>0)               
        echo "<div>".$seopage[0]['page_content']."</div>"; 
    return $arg;
}

# функция выполняется при деинсталяции плагина
function seotext_uninstall($args = array())
{    
    mso_delete_option_mask('seotext', 'plugins'); // удалим созданные опции
    return $args;
}
?>