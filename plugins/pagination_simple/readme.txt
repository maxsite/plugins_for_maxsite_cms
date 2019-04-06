Изначальный автор плагина :
http://nicothin.ru/page/plagin-paginacii-na-bootstrap-dlja-maxsite


Почитать о плагине пожно тут:
http://web-modern.net/blog/blog.php?id=48


Поскольку плагин был бажный(на сайте пагинация смотрелась шикарно, а вот в админке были косяки)
Вот собственно, чтоб не менять каждый раз при обновлениях cms стили админки ибо это жесть будет.
Пришлось создавать некий гибрид из стандартного плагина pagination и  pagination_bootstrap.



css.
все завязано на button.

/*кнопки button */
button, button:visited, .comment-form-button, div.pagination strong, div.pagination a, div.pagination span{background-color:#000;color:#f8f8f8;padding:6px 14px;font-size:.9em;font-weight:300;background-image: -moz-linear-gradient(top, #444444, #222222);background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#444444), to(#222222));background-image: -webkit-linear-gradient(top, #444444, #222222);background-image: -o-linear-gradient(top, #444444, #222222);background-image:linear-gradient(to bottom, #444444, #222222); background-repeat: repeat-x;-webkit-box-shadow:inset 0 1px 0 rgba(255, 255, 255, 0.196), 0px 1px 2px rgba(0, 0, 0, 0.047);-moz-box-shadow:inset 0 1px 0 rgba(255, 255, 255, 0.196), 0px 1px 2px rgba(0, 0, 0, 0.047);box-shadow:inset 0 1px 0 rgba(255, 255, 255, 0.196), 0px 1px 2px rgba(0, 0, 0, 0.047);}
button:hover, button:focus, button:active, .comment-form-button:hover, .comment-form-button:focus, .comment-form-button:active, div.pagination a:hover, div.pagination a:active, div.pagination strong{color:#fff;background-color: #000;background-image:none;}
button, button:visited, .comment-form-button{margin:2px;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px;border: 1px solid #444;}


/*Пагинация стандартная.*/
div.pagination{margin:0 auto;margin-top:10px; text-align:center;padding:8px;}
div.pagination strong, div.pagination a, div.pagination span{border-left:1px solid #404040;}
.pagination-first, .pagination-first a, .pagination-first span{border-left-width:1px;-webkit-border-bottom-left-radius:4px;border-bottom-left-radius:4px;-webkit-border-top-left-radius:4px;border-top-left-radius:4px;-moz-border-radius-bottomleft:4px;-moz-border-radius-topleft:4px;}
.next, .next a, .next span{-webkit-border-top-right-radius:4px;border-top-right-radius:4px;-webkit-border-bottom-right-radius:4px;border-bottom-right-radius:4px;-moz-border-radius-topright:4px;-moz-border-radius-bottomright:4px;}

