<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
 
 
# функция автоподключения плагина
function googlitics_autoload($args = array())
{
    // we need to get options
    $options = mso_get_option('googlitics', 'plugins', array());

    mso_create_allow('googlitics_edit', t('Админ-доступ к Googlitics', __FILE__));
    
    mso_hook_add('content_complete', 'googlitics_custom_hrefs');    

    if (isset($options["async"]) AND $options["async"] == TRUE)
    {
        mso_hook_add('head', 'googlitics_custom_head');
    } else {
        mso_hook_add('body_end', 'googlitics_custom_footer');
    }
    
    mso_hook_add('admin_init', 'googlitics_admin_init'); # хук на админку

}

# функция выполняется при активации (вкл) плагина
function googlitics_activate($args = array())
{    
    return $args;
}
  
# функция выполняется при деактивации (выкл) плагина
function googlitics_deactivate($args = array())
{    
    // mso_delete_option('plugin_%%%', 'plugins'); // удалим созданные опции
    return $args;
}
  
# функция выполняется при деинсталляции плагина
function googlitics_uninstall($args = array())
{
    mso_delete_option('googlitics', 'plugins'); // удалим созданные опции
    return $args; 
}

function googlitics_custom_footer($content = '')
{    
    global $MSO;
    //global $page;
        
    // получаем опции 
    $options = mso_get_option('googlitics', 'plugins', array());
        
    if (isset($options["uastring"]) AND ($options["uastring"] != "") AND ( ! isset($MSO->data['session']['users_groups_id']) OR ($MSO->data['session']['users_groups_id'] != '1' ) OR ((bool)$options['trackadmin']) )) 
    { ?>
    <!-- Googlitics for MaxSite CMS | http://kupreev.com  -->
    <script type="text/javascript">
        var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
        document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <script type="text/javascript">
        var pageTracker = _gat._getTracker("<?php echo $options["uastring"]; ?>");
    </script>
    <?php 
    if ((bool)$options["extrase"] == TRUE) 
    {
        $plugin_path = APPPATH.'maxsite/plugins/googlitics/';
        
        echo("\t<script src=\"".$plugin_path."custom_se.js\" type=\"text/javascript\"></script>\n"); 
    } ?>
    <script type="text/javascript">
        pageTracker._trackPageview();
    </script>
    <!-- End of Google Analytics code -->
    <?php
    }
      
    return $content;
}

function googlitics_custom_head($content = '')
{
    global $MSO;
    //global $page;

    // получаем опции
    $options = mso_get_option('googlitics', 'plugins', array());

    if (isset($options["uastring"]) AND ($options["uastring"] != "") AND ( ! isset($MSO->data['session']['users_groups_id']) OR ($MSO->data['session']['users_groups_id'] != '1' ) OR ((bool)$options['trackadmin']) ))
    { ?>
    <!-- Googlitics for MaxSite CMS | http://kupreev.com -->
    <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(function() {
        var pageTracker = _gaq._createAsyncTracker('<?php echo $options["uastring"]; ?>');
    <?php 
    if ((bool)$options["extrase"] == TRUE)
    {
        $plugin_path = APPPATH.'maxsite/plugins/googlitics/';

        include $plugin_path.'custom_se.js';
        //echo("\t<script src=\"".$plugin_path."custom_se.js\" type=\"text/javascript\"></script>\n");
    } ?>
            pageTracker._trackPageview();
        });

        (function() {
         var ga = document.createElement('script');
         ga.src = ('https:' == document.location.protocol ? 'https://ssl' :
           'http://www') + '.google-analytics.com/ga.js';
         ga.setAttribute('async', 'true');
         document.documentElement.firstChild.appendChild(ga);
        })();
    </script>
    <!-- End of Google Analytics code -->
    <?php
    }

    return $content;
}

function googlitics_custom_hrefs($content = '')
{    
    global $MSO;
    global $options;
    global $origin;
        
    // получаем опции 
    $options = mso_get_option('googlitics', 'plugins', array());
    $origin = googlitics_get_domain($_SERVER["HTTP_HOST"]);
    
    if (isset($options['trackoutbound']) AND (bool)$options['trackoutbound']) 
    {
        if ($options["uastring"] != "" AND ( ! isset($MSO->data['session']['users_groups_id']) OR ($MSO->data['session']['users_groups_id'] != '1' ) OR ((bool)$options['trackadmin']) )) 
        { 
            $anchorPattern = '/<a (.*?)href=[\'\"](.*?)\/\/([^\'\"]+?)[\'\"](.*?)>(.*?)<\/a>/i';
            $content = preg_replace_callback($anchorPattern,'googlitics_parse_link',$content);
             
        }
    }
      
    return $content;
}

# при входе в админку
function googlitics_admin_init($args = array()) 
 {
     if ( !mso_check_allow('googlitics_edit') ) return $args;
  
     $this_plugin_url = 'googlitics'; // url и hook 
     
     # добавляем свой пункт в меню админки
     # первый параметр - группа в меню
     # второй - это действие/адрес в url - http://сайт/admin/demo
     # Третий - название ссылки    
     mso_admin_menu_add('plugins', $this_plugin_url, 'Googlitics');
  
     # прописываем для указаного url
     # связанную функцию именно она будет вызываться, когда 
     # будет идти обращение по адресу http://сайт/admin/demo
     mso_admin_url_hook ($this_plugin_url, 'googlitics_admin_page');
     
     return $args;
 }
  
 # функция вызываемая при хуке, указанном в mso_admin_url_hook
 function googlitics_admin_page($args = array()) 
 {
     global $MSO;
     if ( !mso_check_allow('googlitics_edit') ) 
     {
         echo 'Доступ запрещен';
         return $args;
     }
     
     mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "Googlitics"; ' );
     mso_hook_add_dinamic( 'admin_title', ' return "Googlitics - " . $args; ' );
  
     # выносим админские функции отдельно в файл    
     require($MSO->config['plugins_dir'] . 'googlitics/admin.php');
 }
 
 function googlitics_get_domain($uri)
 {
    $hostPattern = "/^(http:\/\/)?([^\/]+)/i";
    $domainPattern = "/[^\.\/]+\.[^\.\/]+$/";

    preg_match($hostPattern, $uri, $matches);
    $host = $matches[2];
    preg_match($domainPattern, $host, $matches);
    if (isset($matches[0]))
    {
        return array("domain"=>$matches[0],"host"=>$host);                                                                                                                                                            
    } else {
        return array("domain"=>"","host"=>""); 
    }
                
 }
 
 function googlitics_parse_link($matches)
 {
    global $origin;
    global $options ;
    
    $target = googlitics_get_domain($matches[3]);
    $coolBit = "";
    $extension = substr($matches[3],-3);
    $dlextensions = split(",",$options['dlextensions']);
    if ( $target['domain'] != $origin['domain'] )
    {
        if ($options['domainorurl'] == "domain") 
        {
            $coolBit .= "onclick=\"javascript:pageTracker._trackPageview('".$options['outprefix']."/".$target['host']."');\"";
        } elseif ($options['domainorurl'] == "url") {
            $coolBit .= "onclick=\"javascript:pageTracker._trackPageview('".$options['outprefix']."/".$matches[2]."//".$matches[3]."');\"";
        }
    } elseif ( in_array($extension, $dlextensions) AND $target['domain'] == $origin['domain'] ) {
        $file = str_replace($origin['domain'],"",$matches[3]);
        $file = str_replace('www.',"",$file);
        $coolBit .= "onclick=\"javascript:pageTracker._trackPageview('".$options['dlprefix'].$file."');\"";
    }
    return '<a ' . $matches[1] . 'href="' . $matches[2] . '//' . $matches[3] . '"' . ' ' .$coolBit . $matches[4] . '>' . $matches[5] . '</a>';    
 
 }
 
?>