<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * For MaxSite CMS
 * ProtectBlog Plugin
 * Author: (c) RGaysin
 * Plugin URL: http://rgblog.ru/
 */

# функция автоподключения плагина
function protectblog_autoload($args = array())
{
	mso_hook_add( 'head', 'protectblog_head');
	mso_hook_add( 'body_end', 'protectblog_end');
	//mso_hook_add( 'comments_content_out', 'protectblog_end');
}

# функция выполняется при деинсталяции плагина
function protectblog_uninstall($args = array())
{
	// константа
	$options_key = 'plugin_protectblog';
	mso_delete_option($options_key,'plugins');
	return $args;
}

# добавляем в head
function protectblog_head($args = array())
{
	$options_key = 'plugin_protectblog';
	$options = mso_get_option($options_key, 'plugins', array());
	
	if (!isset($options['pb_txt'])) $options['pb_txt'] = true;
	if (!isset($options['pb_cl_txt'])) $options['pb_cl_txt'] = '';
	if (!isset($options['pb_cl'])) $options['pb_cl'] = true;
	if (!isset($options['pb_auth'])) $options['pb_auth'] = true;
	
	//$pos = strpos(strtoupper(getenv("REQUEST_URI")), '?preview=true');
	$pos = false;
	
	if ($options['pb_auth'])
	{
		if (!is_login() && !is_login_comuser())
		{
			if ($pos === false) {
				if($options['pb_txt']) { protectblog_rclick($options['pb_cl_txt']); }
			}		
		}
	}
	else
	{
		if ($pos === false) {
			if($options['pb_txt']) { protectblog_rclick($options['pb_cl_txt']); }
		}
	}
}

function protectblog_end($args = array())
{
	$options_key = 'plugin_protectblog';
	$options = mso_get_option($options_key, 'plugins', array());
	
	if (!isset($options['pb_cl'])) $options['pb_cl'] = true;
	
	if ($options['pb_cl'])
	{
		echo '<style>*{-ms-user-select:none;-moz-user-select:-moz-none;-khtml-user-select:none;-webkit-user-select:none;user-select:none}.selectable{-ms-user-select:auto;-moz-user-select:auto;-khtml-user-select:auto;-webkit-user-select:auto;user-select:auto}</style><a href="http://rgblog.ru/" style="display: none;">Блог RGBlog</a>';
	}	
}

function protectblog_rclick($mes)
{
	if (trim($mes)=='') { $mes = 'Спасибо за то что зашли на наш сайт!'; }
	echo '<meta http-equiv="imagetoolbar" content="no">
<script type="text/javascript" language="JavaScript">
function disableText(e){ return false }
function reEnable(){ return true }
//For browser IE4+
document.onselectstart = new Function ("return false")
//For browser NS6
if (window.sidebar){
  document.onmousdown = disableText
  document.onclick = reEnable
}
</script>
<script language="JavaScript1.2">
var msgpopup="'.$mes.'";
function pmb(){
	  if(alertVis == "1") alert(message);
          if(closeWin == "1") self.close();
          return false;
}
function IE() {
     if (event.button == "2" || event.button == "3"){pmb();}
}
function NS(e) {
     if (document.layers || (document.getElementById && !document.all)){
          if (e.which == "2" || e.which == "3"){ pmb();}
     }
}
document.onmousedown=IE;document.onmouseup=NS;document.oncontextmenu=new Function("alert(msgpopup);return false")
</script>';
}

# функция отрабатывающая миниопции плагина
function protectblog_mso_options() 
{
    # ключ, тип, ключи массива
    mso_admin_plugin_options('plugin_protectblog', 'plugins', 
        array(
            'pb_txt' => array(
                            'type' => 'checkbox', 
                            'name' => t('Отключить нажатие правой кнопки мыши'), 
                            'description' => t(' '), 
                            'default' => 1
                        ), 
            'pb_cl_txt' => array(
                            'type' => 'text', 
                            'name' => t('Введите предупреждающее сообщение:'), 
                            'description' => t('Этот текст будет отображаться когда пользователь нажимет ПКМ.'), 
                            'default' => ''
                        ), 
            'pb_cl' => array(
                            'type' => 'checkbox', 
                            'name' => t('Отключить выделение текста и перемещение изображений'), 
                            'description' => t(' '), 
                            'default' => 1
                        ),
			'pb_auth'=> array(
							'type' => 'checkbox',
							'name' => t('Отключить если пользователь залогинен'),
							'description' => t(' '),
							'default' => 1
						),
            ),
		t('Настройки плагина ProtectBlog'), // титул
		t('Плагин блокирует нажатие правой кнопки мыши на контенте, выделение текста, а также перемещение изображений')  // инфа
    );
}
?>