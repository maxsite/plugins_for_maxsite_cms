$(document).ready(function() {
	var cnt = $('.' + contents_opts.class_contents), page = $('.' + contents_opts.сlass_page), widget = $('.mso-widget  .' + contents_opts.class_contents);
	var html = '', header = '<p>' + contents_opts.header + '</p>', arr = [];
	console.log(page);
	page.find('h1,h2,h3,h4,h5,h6').each( function(index)
	{
		$(this).prepend('<a name="part' + index + '"></a>');
		var text = $(this).text(), tag = this.tagName;
		var lvl = tag.substr(tag.length-1);
		if( arr.length == 0 )
		{
			arr.push(lvl);
		}

		var current_lvl = arr[arr.length-1]
			
		if( lvl > current_lvl )
		{
			html += '<ul>';
			arr.push(lvl);
		}
		else if( lvl < current_lvl )
		{
			while( lvl <= arr[arr.length-2] )
			{
				arr.pop();
				html += '</ul>';
			}
			if( lvl < arr[arr.length-1] ) arr[arr.length-1] = lvl;
		}
			
		html += ('<li><a href="#part' + index + '">' + text + '</a></li>');

	});

	cnt.each( function(){
		if( widget.length == 0 && $(this).parents('.mso-type-page').length > 0 )
		{
			$(this).html(header + '<ul>' + html + '</ul>');
		}
		else if( widget.length > 0 && $(this).parents('.mso-type-page').length > 0 )
		{
			$(this).remove();
		}
		else if( widget.length != 0 && $(this).parents('.mso-widget').length > 0 )
		{
			$(this).html('<ul>' + html + '</ul>');
		}
	});
});