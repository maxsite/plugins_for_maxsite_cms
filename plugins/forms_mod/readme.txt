Пример формы:


[form]
    [email=mylo@sait.com]
    [redirect=http://site.com/]
    [subject=Моя форма]

    [field]
        require = 1   
        type = select
        description = Выберите специалиста
        values = Иванов # Петров # Сидоров
        default = Иванов
    [/field]
	
	[field]
        require = 1   
        type = text
        description = Ваша модель нашей техники
    [/field]

    [field]
        require = 0   
        type = select
        description = Обращались ли вы к нам раньше?
        values = Нет # Да
        default = Нет
    [/field]

    [field]
        require = 1
        type = textarea
        description = Ваш вопрос
    [/field]
[/form]


Для оформления можно использовать стили в шаблоном css, например:

/* forms plugin */
.forms div {margin-bottom:.8em; width: 40em; overflow:hidden}
.forms label {display:block; float:left; text-align:right; width:15em; margin-right:1em}
.forms .input_checkbox label {float:none; display:inline}
.forms .input_text input {width:15em}
.forms .antispam input {width:3em}
.forms div.input_checkbox, .forms .require-desc, .forms div.submit {padding-left:16em; width:25em}
.require {color: #F00; font-weight:900; vertical-align:top}