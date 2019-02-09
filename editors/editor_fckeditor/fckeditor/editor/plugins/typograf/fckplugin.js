(function($) {
	$.fn.extend({
		typograf : function(options) 
		{
			var request_url = options.url;
			var request_text = options.text;
			var response = $.ajax({
				type	:	'POST',
				url		:	request_url,
				async	:	false,
				data	:	{ text : request_text },
				dataType:	'html'
			});
			
			return response.responseText;
		}
	});
})(window.parent.jQuery);  //closure for emulating 'private'

var TypografCommand = function() {
	//create our own command, we dont want to use the FCKDialogCommand because it uses the default fck layout and not our own
};

TypografCommand.GetState = function() {
	return FCK_TRISTATE_OFF; //we dont want the button to be toggled
}

TypografCommand.Execute = function() {
	var $ = window.parent.jQuery;	
	var FCK = window.FCK;
	var str = '';
	for (var key in FCK)
	{
		str += key + '     ';
	}
	var text = FCK.GetXHTML(true);
	
	var typografOptions = {
		url : FCKConfig.PluginsPath + 'typograf/php/handler.php',
		text : text
	};
	
	text = $().typograf(typografOptions);
	FCK.SetData(text);
}

FCKCommands.RegisterCommand('typograf', TypografCommand);

// Create the "Typograf" toolbar button.
var oTypoItem = new FCKToolbarButton('typograf', 'Типографить');
oTypoItem.IconPath = FCKConfig.PluginsPath + 'typograf/typo.gif';
FCKToolbarItems.RegisterItem('typograf', oTypoItem ); // 'typograf' is the name used in the Toolbar config.