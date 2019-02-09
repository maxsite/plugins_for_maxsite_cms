<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// публичная информация о профиле пользователя

     // просто выводим инфу для всех
     echo '<h3>'. t('Персональные данные'). '</h3>';
		 echo '<p><strong>'. t('Отображаемый ник'). ': </strong>' . $edit_profile['profile_psevdonim'] . '</p>';
		 echo '<p><strong>'. t('Подпись'). ': </strong>' . $edit_profile['profile_podpis'] . '</p>';     
		 echo '<p><strong>'. t('Сайт'). ': </strong>' . $edit_profile['comusers_url'] . '</p>';     

?>