var $j = jQuery.noConflict();

function jqsocial(u,t) {
	document.write('<link href="'+folder+'jqsocial.css" type="text/css" rel="stylesheet" /><div class="jsb-wrap"><div class="jsb-title"><span title="Кликните для переключения">Добавить в закладки</span></div><ul class="jsb-list1">');
	folder = folder+'s/';
	function so(u,t) {
		for (i=0; i< s.length; i=i+2) document.write(
			'<li><a rel="nofollow" style="background: url('+folder+s[i]+'.ico) no-repeat; background-position: 0 50%" href="http://'+s[i]+'/'+s[i+1].replace('{u}',u).replace('{t}',t)+'">'+s[i]+'</a></li>'
		);
	}
	document.write('<noindex>');
	so (u,t);
	document.write('</ul></noindex><ul class="jsb-list2">');
	function so2(u,t) {
		for (i=0; i< s2.length; i=i+2) document.write(
			'<li><a rel="nofollow" style="background: url('+folder+s2[i]+'.ico) no-repeat; background-position: 0 50%" href="http://'+s2[i]+'/'+s2[i+1].replace('{u}',u).replace('{t}',t)+'">'+s2[i]+'</a></li>'
		);
	}
	document.write('<noindex>');
	so2 (u,t);
	document.write('</ul></noindex></div>');
}

$j(document).ready(function() {
	var is_toogled = false;
	$j('.jsb-wrap a').attr({target: '_blank'});
	$j('.jsb-wrap').hover(
		function() {
			$j(this).addClass('jsb-current');
			$j('.jsb-current ul').css({opacity: 0}).hide();
			$j('.jsb-current .jsb-list1').animate({opacity: 1}, 300).show();
			$j('.jsb-current .jsb-title').addClass('jsb-s1').removeClass('jsb-s2');
		},
		function() {
			if( is_toogled == true ) { $j('.jsb-title').click(); }
			$j('.jsb-current ul').hide();
			$j('.jsb-current .jsb-title').removeClass('jsb-s1').removeClass('jsb-s2');
			$j('.jsb-current .jsb-title span').text('Добавить в закладки');
			$j('.jsb-wrap').removeClass('jsb-current');
		}
	);
	$j('.jsb-title').toggle(
		function() {
			$j('.jsb-current .jsb-list1').css({opacity: 0}).hide();
			$j('.jsb-current .jsb-list2').animate({opacity: 1}, 300).show();
			$j('.jsb-current .jsb-title').removeClass('jsb-s1').addClass('jsb-s2');
			$j('.jsb-current .jsb-title span').text('Добавить в соц. сервисы');
			is_toogled = true;
		},
		function() {
			$j('.jsb-current .jsb-list2').css({opacity: 0}).hide();
			$j('.jsb-current .jsb-list1').animate({opacity: 1}, 300).show();
			$j('.jsb-current .jsb-title').removeClass('jsb-s2').addClass('jsb-s1');
			$j('.jsb-current .jsb-title span').text('Добавить в закладки');
			is_toogled = false;
		}
	);
})