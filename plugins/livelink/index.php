<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function livelink_autoload()
{
mso_hook_add( 'content', 'livelink_custom'); 
}

# функция выполняется при активации (вкл) плагина
function livelink_activate($args = array())
{	
	//mso_create_allow('%%%_edit', t('Админ-доступ к настройкам', 'plugins') . ' ' . t('%%%', __FILE__));
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function livelink_deactivate($args = array())
{	
	// mso_delete_option('plugin_%%%', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function livelink_uninstall($args = array())
{	
	// mso_delete_option('plugin_%%%', 'plugins'); // удалим созданные опции
	// mso_remove_allow('%%%_edit'); // удалим созданные разрешения
	return $args;
}

# функции плагина
function livelink_custom($str='')
{
//$text=preg_replace("#(https?|ftp)://\S+[^\s.,>)\];'\"!?]#",'<a href="\\0">\\0</a>',$text);
//Текст функции отсюда: http://www.snippy.ru/snippet/1774-preobrazovanie-v-ssylki-adresa-url-i-email-iz-teksta/
if (preg_match_all("#(^|\s|\()((http(s?)://)|(www\.))(\w+[^\s\)\<]+)#i", $str, $matches))  
    {  
    $pop = " target=\"_blank\" ";  
        for ($i = 0; $i < count($matches['0']); $i++)  
            {  
            $period = '';  
            if (preg_match("|\.$|", $matches['6'][$i]))  
                {  
                 $period = '.';  
                 $matches['6'][$i] = substr($matches['6'][$i], 0, -1);  
                }   
            $str = str_replace($matches['0'][$i],  
                                        $matches['1'][$i].'<a href="http'.  
                                        $matches['4'][$i].'://'.  
                                        $matches['5'][$i].  
                                        $matches['6'][$i].'"'.$pop.'>http'.  
                                        $matches['4'][$i].'://'.  
                                        $matches['5'][$i].  
                                        $matches['6'][$i].'</a>'.  
                                        $period, $str);  
                }  
            }  
  
return $str;
}



# end file