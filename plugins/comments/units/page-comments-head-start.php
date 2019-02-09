<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

# здесь можно переопределить значения мета-поля title и description у страниц с пагинацией комментариев
# доступна переменная $comments_pagination_current с номером страницы
# Внимание!!! Данный юнит сработает только если в шаблоне вашего сайта есть вызов хука head_start

global $MSO;

$MSO->title .= ' - Страница № '.$comments_pagination_current;
$MSO->description .= ' - Страница № '.$comments_pagination_current;

?>