<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * Plugin Name: Media Player
 * Plugin URI: http://6log.ru/mediaplayer
 * Author: (c)Tux, 2009.
 *
 * Flash: http://www.longtailvideo.com/
 * jQuery: http://malsup.com/jquery/media/
*/

# функция автоподключения плагина
function mediaplayer_autoload($args = array())
{
	mso_hook_add( 'head', 'mediaplayer_head');
}

# функции плагина
function mediaplayer_head($arg = array())
{
	$path = getinfo('plugins_url') . 'mediaplayer/';
    echo '
	<script src="' . $path . 'jquery.media.js" type="text/javascript"></script>
	<script src="' . $path . 'jquery.metadata.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(function() {
     	 $.fn.media.defaults.mp3Player = "' . $path . 'mediaplayer.swf";
		 $.fn.media.defaults.flvPlayer = "' . $path . 'mediaplayer.swf";
            $("a.media").media({ width: 300, height: 20, attrs: { allowfullscreen: "true" }});
        });
    </script>';	
	
	return $arg;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
function mediaplayer_mso_options() 
{
	mso_cur_dir_lang(__FILE__);
		
    # ключ, тип, ключи массива
    mso_admin_plugin_options('plugin_11', 'plugins', 
        array(
            '11' => array(
                            'type' => 'text', 
                            'name' => t('0'), 
                            'description' => t('0'), 
                            'default' => t('0')
                        ),

            ),
		t('Настройки плагина Media Player'), // титул
		t('<b>Плагин позволяет проигрывать видео, слушать музыку.</b><br />
		   <p>Примеры использования:<br />< a class="media" href="http://site.ru/sample.mp3">My Audio File< /a><br />
		   < a class="media {width: 400, height: 300, autoplay: true}" href="sample.flv">My Movie< /a></p>')  // инфа
    );
}

?>