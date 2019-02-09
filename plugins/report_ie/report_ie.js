$(function() {
	var report = $('<div id="ie6report"><p>Ваш браузер сильно устарел! Корректная работа с сайтом не гарантируется. Обновитесь:</p> <a href="http://www.google.com/chrome/" title="Google Chrome"><img src="/application/maxsite/plugins/report_ie/images/chrome.jpg"/></a> <a href="http://www.mozilla-europe.org/" title="Mozilla Firefox"><img src="/application/maxsite/plugins/report_ie/images/firefox.jpg"/></a> <a href="http://ru.opera.com/" title="Opera"><img src="/application/maxsite/plugins/report_ie/images/opera.jpg"/></a> <a href="http://www.microsoft.com/rus/windows/internet-explorer/" title="Internet Explorer"><img src="/application/maxsite/plugins/report_ie/images/ie.jpg"/></a></div>');	
	report.prependTo('body').css({
        width:'100%', marginBottom:'2px', padding:'5px 0', borderBottom:'1px solid #696969',
		textAlign:'center', font:'bold 12px Verdana, sans-serif', background:'#fff'
    }).find('img').css({
	    verticalAlign:'middle'	
	}).end()
	.find('p').css({
	    display:'inline', marginRight:'10px', color:'#ff4500'
	});
});