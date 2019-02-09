<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function wvolnorezonline_autoload()
{
	mso_register_widget('wvolnorezonline_widget', 'VolnorezOnline'); 
	mso_hook_add('head', 'wvolnorezonline_head', 0);
	mso_hook_add('admin_head', 'wvolnorezonline_admin_head', 0);
}


# функция выполняется при деактивации (выкл) плагина
function wvolnorezonline_deactivate($args = array())
{	
	// mso_delete_option('plugin_channels', 'plugins'); // удалим созданные опции
	return $args;
}

function	wvolnorezonline_widget()
{
	$options = mso_get_option('plugin_wvolnorezonline', 'plugins', array() );
	
	$cWidgetTitle = (isset($options['VolnorezOnline_WidgetTitle']))?$options['VolnorezOnline_WidgetTitle']:false;
	$nAmountStations = (isset($options['VolnorezOnline_Count'])&&$options['VolnorezOnline_Count']!=0)?$options['VolnorezOnline_Count']:false;
	$nGenreID = (isset($options['VolnorezOnline_GenreID'])&&$options['VolnorezOnline_GenreID']!=0)?$options['VolnorezOnline_GenreID']:false;
	$cLanguage = (isset($options['VolnorezOnline_Language']))?$options['VolnorezOnline_Language']:false;
	$bGenreList = (isset($options['VolnorezOnline_GenreList'])&&$options['VolnorezOnline_GenreList']!=0)?$options['VolnorezOnline_GenreList']:false;
	$bAutoplay = (isset($options['VolnorezOnline_Autoplay'])&&$options['VolnorezOnline_Autoplay']!=0)?$options['VolnorezOnline_Autoplay']:false;
	
	echo '<span id="VolnorezOnline" style="display:none;">';
		$nIDLastGenre = false;
		if( isset($_COOKIE['interface']) )
		{
			$cName = 'VolnorezOnline_genre';
			if( strpos($_COOKIE['interface'], $cName)!==false )
			{
				$_COOKIE['interface'] = str_replace('\\','',$_COOKIE['interface']);
				$aCookie = json_decode($_COOKIE['interface']);
				if( isset( $aCookie->$cName ) ) $nIDLastGenre = $aCookie->$cName;
			}
		}
		echo '<input type="hidden" id="VolnorezOnlineConfig_PluginURL" value="' . getinfo('plugins_url') . 'wvolnorezonline/"/>';
		echo '<input type="hidden" id="VolnorezOnlineConfig_Count" value="' . $nAmountStations . '"/>';
		echo '<input type="hidden" id="VolnorezOnlineConfig_LastGenre" value="' . $nIDLastGenre . '"/>';
		echo '<input type="hidden" id="VolnorezOnlineConfig_GenreID" value="' . $nGenreID . '"/>';
		echo '<input type="hidden" id="VolnorezOnlineConfig_Language" value="' . $cLanguage . '"/>';
		echo '<input type="hidden" id="VolnorezOnlineConfig_GenreList" value="' . $bGenreList . '"/>';
		echo '<input type="hidden" id="VolnorezOnlineConfig_Autoplay" value="' . $bAutoplay . '"/>';
		echo '<span id="VolnorezOnline_WidgetTitle">' . $cWidgetTitle . '</span>';
		echo '<div id="VolnorezOnline_Container"></div>';
	echo '</span>';
}

# функция выполняется при деинсталяции плагина
function wvolnorezonline_uninstall($args = array())
{	
	// mso_delete_option('plugin_channels', 'plugins'); // удалим созданные опции
	// mso_remove_allow('wvolnorezonline_edit'); // удалим созданные разрешения
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function wvolnorezonline_mso_options() 
{
	$options = mso_get_option('plugin_wvolnorezonline', 'plugins', array() );

	if( isset($options['VolnorezOnline_Language']) )
	{
		$cWidgetTitle_Title = "Заголовок виджета";
		$cCount_Title = "Количество станций";
		$cCount_Description = "Количество станций одновременно находящихся на странице";
		$cLanguage_Title = "Язык";
		$cGenreID_Title = "Жанр";
		$cGenreList_Title = "Показывать список жанров?";
		$cAutostart_Title = "Автостарт";
		$cAutostart_Description = "Запускать трансляцию первой станции автоматически?";
		$cSettingsTitle = 'Настройки плагина "VolnorezOnline" (wvolnorezonline)';
		$cSettingsDescription = "Укажите необходимые опции";
		switch( $options['VolnorezOnline_Language'] )
		{
			case 'ENGLISH':		{
									$cWidgetTitle_Title = "Widget title";
									$cCount_Title = "Amount stations";
									$cCount_Description = "The number of stations simultaneously located on page";
									$cLanguage_Title = "Language";
									$cGenreID_Title = "Genre";
									$cGenreList_Title = "Show genre list?";
									$cAutostart_Title = "Autoplay";
									$cAutostart_Description = "Run broadcast first station automatically?";
									$cSettingsTitle = 'Plugin settings "VolnorezOnline" (wvolnorezonline)';
									$cSettingsDescription = "Enter the required options";
									break;
								}
		}
	}
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_wvolnorezonline', 'plugins', 
		array(
			'VolnorezOnline_WidgetTitle' => array(
					'type' => 'text', 
					'name' => $cWidgetTitle_Title, 
					'description' => '', 
					'default' => ''
				),
			'VolnorezOnline_Count' => array(
					'type' => 'text', 
					'name' => $cCount_Title, 
					'description' => $cCount_Description, 
					'default' => ''
				),
			'VolnorezOnline_Language' => array(
					'type' => 'select', 
					'name' => $cLanguage_Title, 
					'description' => '',
					'values' => 'RUSSIAN||Русский#
								 ENGLISH||English',
					'default' => 'RUSSIAN'
				),	
			'VolnorezOnline_GenreID' => array(
					'type' => 'select', 
					'name' => $cGenreID_Title, 
					'description' => '',
					'values' => 'All||All#
								 1||Pop#
								 2||Electronic music#
								 3||Rap (Hip Hop)#
								 4||Games#
								 5||Communication, Books, Sports, Humor#
								 6||Rock#
								 7||Ambient, Lounge, Dream#
								 8||Chanson, Romance, Bards#
								 9||Religious music#
								 10||Blues#
								 11||Jazz#
								 12||Classical#
								 13||Country#
								 14||Ska, Rokstedi, Reggae#
								 15||Latin American Music#
								 16||Folk Music',
					'default' => 'All'
				),	
			'VolnorezOnline_GenreList' => array(
					'type' => 'checkbox', 
					'name' => $cGenreList_Title, 
					'description' => '', 
					'default' => '1'
				),
			'VolnorezOnline_Autoplay' => array(
					'type' => 'checkbox',
					'name' => $cAutostart_Title, 
					'description' => $cAutostart_Description, 
					'default' => '0'
				),
			),
		$cSettingsTitle, // титул
		$cSettingsDescription   // инфо
	);
}

function	wvolnorezonline_head($args = array())
{
	mso_load_jquery();
	echo '<script src="' . getinfo('plugins_url') . 'wvolnorezonline/js/java.js" type="text/javascript"></script>' . NR;
	echo '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'wvolnorezonline/css/style.css" type="text/css" />' . NR;

	return $args;
}

function	wvolnorezonline_admin_head($args = array())
{
	echo '<script src="' . getinfo('plugins_url') . 'wvolnorezonline/js/admin_java.js" type="text/javascript"></script>' . NR;
}
?>
