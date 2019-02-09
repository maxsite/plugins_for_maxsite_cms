// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
myHtmlSettings = {
    nameSpace:       "html", // Useful to prevent multi-instances CSS conflict
    onShiftEnter:    {keepDefault:false, replaceWith:'<br />\n'},
    onCtrlEnter:     {keepDefault:false, openWith:'\n<p>', closeWith:'</p>\n'},
    onTab:           {keepDefault:false, openWith:'     '},
    markupSet:  [
        {name:'Заголовок 1', key:'1', openWith:'<h1(!( class="[![Class]!]")!)>', closeWith:'</h1>', placeHolder:'Название сюда...' },
        {name:'Заголовок 2', key:'2', openWith:'<h2(!( class="[![Class]!]")!)>', closeWith:'</h2>', placeHolder:'Название сюда...' },
        {name:'Заголовок 3', key:'3', openWith:'<h3(!( class="[![Class]!]")!)>', closeWith:'</h3>', placeHolder:'Название сюда...' },
        {name:'Заголовок 4', key:'4', openWith:'<h4(!( class="[![Class]!]")!)>', closeWith:'</h4>', placeHolder:'Название сюда...' },
        {name:'Заголовок 5', key:'5', openWith:'<h5(!( class="[![Class]!]")!)>', closeWith:'</h5>', placeHolder:'Название сюда...' },
        {name:'Заголовок 6', key:'6', openWith:'<h6(!( class="[![Class]!]")!)>', closeWith:'</h6>', placeHolder:'Название сюда...' },
        {name:'Абзац', openWith:'<p(!( class="[![Class]!]")!)>', closeWith:'</p>'  },
        {separator:'---------------' },
        {name:'Жирный', key:'B', openWith:'<strong>', closeWith:'</strong>' },
        {name:'Курсив', key:'I', openWith:'<em>', closeWith:'</em>'  },
        {name:'Зачеркнутый', key:'S', openWith:'<del>', closeWith:'</del>' },
        {separator:'---------------' },
        {name:'Маркированный список', openWith:'<ul>\n', closeWith:'</ul>\n' },
        {name:'Числовой список', openWith:'<ol>\n', closeWith:'</ol>\n' },
        {name:'Элемент списка', openWith:'<li>', closeWith:'</li>' },
        {separator:'---------------' },
        {name:'Рисунок', key:'P', replaceWith:'<img src="[![Source:!:http://]!]" alt="[![Alternative text]!]" />' },
        {name:'Ссылка', key:'L', openWith:'<a href="[![Link:!:http://]!]"(!( title="[![Title]!]")!)>', closeWith:'</a>', placeHolder:'Your text to link...' },
        {separator:'---------------' },
        {name:'чистить выделенное от HTML кодов', replaceWith:function(h) { return h.selection.replace(/<(.*?)>/g, "") } },
        /*{name:'Preview', call:'preview', className:'preview' }*/
    ]
}