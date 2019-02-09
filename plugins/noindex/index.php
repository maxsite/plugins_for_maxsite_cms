<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Page attachments plugin for MaxSite CMS
 * Первоначальная версия http://www.moneymaker.ru/
 * Доработка загрузки файлов с сервера - quantum (http://maxsitecms.ru)
 */
 
function noindex_autoload($args = array())
{
    
    mso_create_allow('noindex_page_form_add_all_meta', t('Админ-доступ', 'plugins'));
    mso_create_allow('noindex_custom_new', t('Админ-доступ', 'plugins'));
    mso_create_allow('noindex_custom_edit', t('Админ-доступ', 'plugins'));          
    mso_create_allow('noindex_do_it', t('Админ-доступ', 'plugins'));   
  
    mso_hook_add('admin_page_form_add_all_meta', 'noindex_page_form_add_all_meta');
    mso_hook_add('new_page', 'noindex_custom_new');
    mso_hook_add('edit_page', 'noindex_custom_edit');
    mso_hook_add('head', 'noindex_head');       

}

function noindex_activate($args = array())
{  
    return $args;
}

# функция выполняется при деактивации (выкл) плагина
function noindex_deactivate($args = array())
{
    //mso_delete_option('plugin_noindex', 'plugins'); // удалим созданные опции
    return $args;
}

# функция выполняется при деинстяляции плагина
function noindex_uninstall($args = array())
{

    mso_delete_option('plugin_noindex', 'plugins'); // удалим созданные опции
    mso_remove_allow('noindex_edit'); // удалим созданные разрешения
    return $args;
}

function noindex_head($args = array())
{
    global $MSO;
    
    if (is_type('page')) {
        $page_slug = mso_segment(2);
        $CI = & get_instance();       
        $CI->db->select('page_noindex');
        $CI->db->from('page');
        $CI->db->like('page_slug',$page_slug);
        $query = $CI->db->get();   
        $result = $query->result_array();  
        if (isset($result[0])) if (intval($result[0]['page_noindex'])>0) {                        
            echo '          <meta name="robots" content="noindex, nofollow" />';
        }       
    }
   

}

function noindex_page_form_add_all_meta($args = array())
{      
    $id = mso_segment(3);
    if (!is_numeric($id) || empty($id)) $id = false; // не число
    else $id = (int) $id;   
    $result = array(0=>array('page_noindex'=>0));      
    if ($id) {
        $CI = & get_instance();       
        $CI->db->select('page_noindex');
        $CI->db->from('page');
        $CI->db->where(array('page_id'=>$id));
        $query = $CI->db->get();   
        $result = $query->result_array();  
    }  

    $out = '';
    $out .= '<div>';
    $out .= '<h3>Запрет индексации в поисковиках</h3>';
    $out .= '<p><input type="checkbox" name="noindex_plugin_hide" value="true" '.((intval($result[0]['page_noindex'])>0)?'checked':'').' id="noindex_plugin_hide" /> <label for="noindex_plugin_hide">Скрыть страницу от поисковых систем</label></p>';     
    $out .= '</div>';
    $out .= $args;    
    return $out;
}

function noindex_custom_new($args = array()){
    $id = $args[0]; 
    noindex_do_it($id);                           
    return $args;
}

function noindex_custom_edit($args = array())
{
    $id = mso_segment(3);
    if (!is_numeric($id)) $id = false; // не число
    else $id = (int) $id;    
    noindex_do_it($id);      
    return $args;
}
                


function noindex_do_it($id)
{                    
    $CI = & get_instance();
    $record = 0; 
    if ( $post = mso_check_post(array('noindex_plugin_hide')) ) {  
        if ($post['noindex_plugin_hide']=='true') $record = 1;  
    }

    $data = array(
        'page_noindex' => $record,
    );                      
    $CI->db->where('page_id', $id);
    $CI->db->update('page', $data); 
    
}




?>