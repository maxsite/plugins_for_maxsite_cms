<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Dmitry Kuznetsov - meteorjs@outlook.com
 * 16/06/2015
 */


# функция автоподключения плагина
function moneycaptcha_autoload($args = array())
{	
	if ( !is_login() and !is_login_comuser() )
	{
		mso_hook_add('comments_content_end', 'moneycaptcha_show'); # хук на отображение картинки
		mso_hook_add('comments_new_captcha', 'moneycaptcha_validate'); # хук на обработку капчи
		mso_hook_add('comments_new_captcha_error', 'moneycaptcha_error'); # вывод ошибки каптчи
	}
	mso_hook_add('admin_init', 'moneycaptcha_admin_init'); # хук на админку
}

function moneycaptcha_validate($args = array()) 
{
	if(mcp_validate($_POST['moneycaptcha_code'])) 
	{ 
		return true; 
	} else { 
		return false; 
	}
}

# выводим картинку капчи
function moneycaptcha_show($args = array()) 
{
	$options = mso_get_option('plugin_moneycaptcha', 'plugins', array());
	echo mcp_show($options['siteid'], getinfo('plugins_url') . 'moneycaptcha/js');
}

function moneycaptcha_admin_init($args = array()) 
{
	$this_plugin_url = 'plugin_options/moneycaptcha'; // url и hook
	mso_admin_menu_add('plugins', $this_plugin_url, t('MoneyCaptcha'));
	mso_admin_url_hook ($this_plugin_url, 'plugin_moneycaptcha');
	
	return $args;
}

# Вывод ошибки неверно выбранной картинки.
function moneycaptcha_error()
{
	$options['errortext'] = 'Ошибка, неверно решена MoneyCaptcha!';
	echo('<div class="comment-error">' . $options['errortext'] . '</div>');
}

# Удаление плагина.
function moneycaptcha_uninstall($args = array())
{	
	mso_delete_option('plugin_moneycaptcha', 'plugins' );
	return $args;
}

function moneycaptcha_mso_options() 
{
	mso_admin_plugin_options('plugin_moneycaptcha', 'plugins', 
		array(
			'siteid' => array(
							'type' => 'text', 
							'name' => t('MoneyCaptcha SiteID:'), 
							'description' => t('Вам необходимо указать SiteID вашего сайта, который указан в личном кабинете на сайте <a href="https://moneycaptcha.ru/">MoneyCaptcha</a>.'),
							'default' => ''
							),
			),
			
		t('Настройки MoneyCaptcha')
	);
}

function mcp_show($siteid)
{
	$html = '';
	if(!empty($siteid))
	{
		$html = "		
					<script type='text/javascript'>
						<!--
						var MC_SITEID = $siteid;
						// -->
					</script>
					<div id='money_captcha_wrapper' class='money_captcha_wrapper'>
                    <script type='text/javascript' src='https://moneycaptcha.ru/captcha.php?siteid=$siteid&charset=utf-8&button=moneycaptchasubmit'></script>
                   </div>
                   <input name='moneycaptcha_code' id='moneycaptcha_code' type='hidden' value=''>
					";		
	}
	return $html;
}

function mcp_validate($code)
{
	if (!empty($code)) 
	{
		$handle = curl_init();
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($handle, CURLOPT_URL, "https://moneycaptcha.ru/valid.php?code=" . $code); 
			curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false); 
				
		$status = curl_exec($handle);
			
		if (($status === false))
		{
			return false;
		}			
		curl_close($handle);
			
		$xml = simplexml_load_string($status);			
		if($xml->code == "1")
		{ 
			return true;
		} else {
			return false;
		}
	}	
	return false;
}
# end file