<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function yahoo_media_player_autoload($args = array())
{
	mso_hook_add( 'head', 'yahoo_media_player_head');
	//mso_hook_add( 'content', 'yahoo_media_player_content');
}

# функции плагина
function yahoo_media_player_head($arg = array())
{
	static $yahoo_media_player_js = false;

	if (!$yahoo_media_player_js)
		echo '<script type="text/javascript">
                    var YWPParams =
                    {
                        theme: "silver",
                       autoplay: false,
                        volume: 0.5
                    };
                </script>
                ';
echo '	<script type="text/javascript" src="' . getinfo('plugins_url') . 'yahoo_media_player/player.js"></script>';
//echo '<script type="text/javascript" src="http://webplayer.yahooapis.com/player.js"></script>';

	$yahoo_media_player_js = true;

	return $arg;
}

?>