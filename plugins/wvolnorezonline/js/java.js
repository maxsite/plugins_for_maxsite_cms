// DEFAULT FALUES
var nNowVolume = 1;
var cPluginURL,nCountStations,cLanguage,bGenreList,nIDLastGenre,nDefaultGenreID,bAutoplay,proxy_url = '';
var ajax_url = 'http://volnorez.com/plugins/wsbonline/page_export/';

jQuery(document).ready(function(){
	// GET DEFAULT VALUES
	cPluginURL = jQuery('#VolnorezOnlineConfig_PluginURL').val();
	nCountStations = jQuery('#VolnorezOnlineConfig_Count').val();
	cLanguage = jQuery('#VolnorezOnlineConfig_Language').val();
	bGenreList = (jQuery('#VolnorezOnlineConfig_GenreList').val()!='')?'true':'false';
	nIDLastGenre = (jQuery('#VolnorezOnlineConfig_LastGenre').val()!='')?jQuery('#VolnorezOnlineConfig_LastGenre').val():'false';
	nDefaultGenreID = (jQuery('#VolnorezOnlineConfig_GenreID').val()!='')?jQuery('#VolnorezOnlineConfig_GenreID').val():'false';
	bAutoplay = (jQuery('#VolnorezOnlineConfig_Autoplay').val()!='')?'true':'false';
	if( nDefaultGenreID != 'All' && nDefaultGenreID != 'false' )
	{
		nIDLastGenre = nDefaultGenreID;
		bGenreList = 'false';
	}
	proxy_url = cPluginURL + 'proxy.php';

	if( jQuery('#VolnorezOnline_Container').length )
	{
		VolnorezOnline_LoadWidget();
		VolnorezOnline_GetCurrentGenre();
		VolnorezOnline_SlideRight();
		VolnorezOnline_SlideLeft();
		VolnorezOnline_ChangeLiveStation();
		VolnorezOnline_MainPlay();
		VolnorezOnline_HoverPlayButton();
	}
	
	// UPDATE LIST ONLINE CHANNELS
	setInterval( function(){VolnorezOnline_ChannelsListUpdate();},120000 );
});

// LOAD WIDGET
function	VolnorezOnline_LoadWidget()
{
	// DATA TO BE SENT TO THE SERVER
	var data = new Object();
	data.get_online_list = 'true';
	data.count_stations = nCountStations;
	data.language = cLanguage;
	data.genre_list = bGenreList;
	data.id_last_genre = nIDLastGenre;
	data.ajax_url = ajax_url;
	
	// ASYNCHRONOUS AJAX REQUEST
	jQuery.ajax({
	type: 'POST', url: proxy_url, dataType: 'html', data: data,
	success: function(data) {
		// CHECK EXISTENCE DATA
		if( data != '' )
		{
			// INSERT CONTENT TO THE END OF ELEMENT
			jQuery('#VolnorezOnline_Container').append(data);
			// SHOW ELEMENT
			jQuery('#VolnorezOnline').show();
			if(!jQuery('#VolnorezOnline_ChannelsContainer').length)return false;
			if( jQuery('.VolnorezOnline_GenreList').length )
			{
				// WIDTH ELEMENT WITH GERNE TITLE
				var nStep = jQuery('.VolnorezOnline_Genre').css('width').replace('px','');
				// ORDINAL NUMBER OF THE SELECTED GENRE
				var nSelectedIndex = jQuery('.VolnorezOnline_GenreSelected').index();
				// NUMBER UNSHOWN GENRES
				var nInvisibleCountGenres = jQuery('.VolnorezOnline_GenreContainer').children().length - 3;
				// MAX AND MIN POSITION GENRE LIST
				var nMaxLeftSlideMargin = 0;
				var nMaxRightSlideMargin = 0 - ( nInvisibleCountGenres * nStep );
				if( nSelectedIndex > 2 )
				{
					// CORRECT GENRE LIST POSITION
					var nMargin = 0 - (( nSelectedIndex - 2 ) * parseInt(nStep,10));
					jQuery('.VolnorezOnline_GenreContainer').css('margin-left', nMargin );
					// CHECK STATUS ARROWS
					if( nMargin != nMaxLeftSlideMargin )
					{
						if( jQuery('#VolnorezOnline_SlideLeft img').attr('class') == 'VolnorezOnline_ArrowInactive' )
						{
							jQuery('#VolnorezOnline_SlideLeft img').attr('class','VolnorezOnline_ArrowActive');
							jQuery('#VolnorezOnline_SlideLeft img').attr('src',jQuery('#VolnorezOnline_SlideLeft img').attr('src').replace('back_inactive.png','back.png'));
						}
					}
					if( nMargin == nMaxRightSlideMargin )
					{
						if( jQuery('#VolnorezOnline_SlideRight img').attr('class') == 'VolnorezOnline_ArrowActive' )
						{
							jQuery('#VolnorezOnline_SlideRight img').attr('class','VolnorezOnline_ArrowInactive');
							jQuery('#VolnorezOnline_SlideRight img').attr('src',jQuery('#VolnorezOnline_SlideRight img').attr('src').replace('forward.png','forward_inactive.png'));
						}
					}
				}
			}
			VolnorezOnline_Volume();
			// GET FLASH CONFIG
			var jscode_player_config = '';
			var domFirstChannel = jQuery('#VolnorezOnline_ChannelsContainer .VolnorezOnline_Channel:first .VolnorezOnline_LiveConfig');
			var nJSCodeID = domFirstChannel.find('.VolnorezOnline_JSCodeID').val();
			var nFMS_URL = domFirstChannel.find('.VolnorezOnline_URL').val();
			var cBColor = domFirstChannel.find('.VolnorezOnline_BColor').val();
			var cTitle = domFirstChannel.find('.VolnorezOnline_cTitle').val();
			var cStatURL = 'staturl=http://volnorez.com/flash-listener';
			var cNoAutoplay = 'noautoplay=true&amp;';
			if( bAutoplay == 'true' )
			{
				cNoAutoplay = '';
				var domPlayerInterface = jQuery('#VolnorezOnline').find('.VolnorezOnline_Play:first');
				if( domPlayerInterface.length )
				{
					// CHANGE STATUS PLAY BUTTON FOR THE LIVE CHANNEL
					domPlayerInterface.attr('class','VolnorezOnline_Pause');
					domPlayerInterface.attr('src',domPlayerInterface.attr('src').replace('play.png','pause.png'));
					var cLiveTitle = domPlayerInterface.closest('.VolnorezOnline_PlayerContainer').find('.VolnorezOnline_cTitle').val();
					$('#VolnorezOnline_ChannelTitleContainer').text(cLiveTitle);
					jQuery('#VolnorezOnline_PlayMin').attr('id','VolnorezOnline_PauseMin');
					jQuery('#VolnorezOnline_PauseMin').attr('src',jQuery('#VolnorezOnline_PauseMin').attr('src').replace('play_min.png','pause_min.png'));
				}
			}
			jscode_player_config = nJSCodeID + '&amp;url=' + nFMS_URL + '&amp;bcolor=' + cBColor + '&amp;'+cNoAutoplay+'staturl=' + cStatURL + '&amp;title=' + cTitle;
			// CREATE FLASH CONTAINER
			jQuery('#VolnorezOnline_Container').append('<div id="export_live_player"></div>');
			var domPlayer = document.getElementById('export_live_player');
			// LOAD FLASH PLAYER
			domPlayer.innerHTML = AC_FL_RunContent_Player( 
				"allowScriptAccess","always",
				"width",'0', 
				"height",'0',  
				"data","http://volnorez.com/application/maxsite/plugins/media/players/liveplayer.swf?showlike=0&amp;jscode_id="+jscode_player_config, 
				"quality","high",
				"wmode","transparent",
				"flashvars", "", "id", "export_live_player",
				"pluginspage","http://www.macromedia.com/go/getflashplayer", 
				"movie","http://volnorez.com/application/maxsite/plugins/media/players/liveplayer?showlike=0&amp;jscode_id="+jscode_player_config);
		}
	}});
}

// GET ONLINE LIST CURRENT GENRE
function	VolnorezOnline_GetCurrentGenre()
{
	jQuery('#VolnorezOnline').delegate('.VolnorezOnline_Genre','click',function(){
		// GET DATA FOR THE AJAX REQUEST
		var nIDGenre = jQuery(this).attr('id').replace('VolnorezOnline_Genre_','');
		var nIDNowPlaying = ( jQuery('#VolnorezOnline_ChannelsContainer .VolnorezOnline_Pause').length )?jQuery('#VolnorezOnline_ChannelsContainer .VolnorezOnline_Pause').closest('.VolnorezOnline_PlayerContainer').find('.VolnorezOnline_ChannelID').val():false;
		var nCountStations = jQuery('#VolnorezOnlineConfig_Count').val();
		var cLanguage = jQuery('#VolnorezOnlineConfig_Language').val();
		var bGenreList = false;
		// CHANGE SELECTED GENRE
		jQuery('.VolnorezOnline_GenreContainer').children().each(function(){
			jQuery(this).attr('class','VolnorezOnline_Genre');
		});
		jQuery('#VolnorezOnline_Genre_'+nIDGenre).attr('class','VolnorezOnline_GenreSelected');
		// SET HEIGHT TEMPORARY ELEMENT
		var nHeight = jQuery('#VolnorezOnline_ChannelsContainer').height();
		jQuery('#VolnorezOnline_ChannelsContainer').empty().append('<div id="VolnorezOnline_Wait">'+jQuery('#VolnorezOnline_Wait').html()+'</div>');
		jQuery('#VolnorezOnline_ChannelsContainer #VolnorezOnline_Wait').height( nHeight ).css('display','block');
		
		// DATA TO BE SENT TO THE SERVER
		var data = new Object();
		data.get_online_list_current_genre = 'true';
		data.count_stations = nCountStations;
		data.language = cLanguage;
		data.genre_list = bGenreList;
		data.genre_id = nIDGenre;
		data.ajax_url = ajax_url;

		// ASYNCHRONOUS AJAX REQUEST
		jQuery.ajax({
		type: 'POST', url: proxy_url, dataType: 'html', data: data,
		success: function(data) {
			if( data != '' )
			{
				// REMEMBER LAST SELECTED GENRE
				VolnorezOnline_SetInterfaceCookie('VolnorezOnline_genre',nIDGenre);
				// INSERT CONTENT TO THE END OF ELEMENT
				jQuery('#VolnorezOnline_ChannelsContainer').empty().append(data);
				// CHECK EXISTENCE LIVE CHANNEL
				if( nIDNowPlaying!==false )
				{
					var domPlayerInterface = jQuery('#VolnorezOnline').find('#VolnorezOnline_LiveChannel_'+nIDNowPlaying);
					if( domPlayerInterface.length )
					{
						// CHANGE STATUS PLAY BUTTON FOR THE LIVE CHANNEL
						domPlayerInterface.attr('class','VolnorezOnline_Pause');
						domPlayerInterface.attr('src',domPlayerInterface.attr('src').replace('play.png','pause.png'));
					}
				}
			}
		}});
	});
}

// SLIDE TO RIGHT GENRE LIST
function	VolnorezOnline_SlideRight()
{
	// ON CLICK EVENT TO THE RIGHT ARROW
	jQuery('#VolnorezOnline').delegate('#VolnorezOnline_SlideRight','click',function(){
		// GET VALUES
		var nInvisibleCountGenres = jQuery('.VolnorezOnline_GenreContainer').children().length - 3;
		var nStep = jQuery('.VolnorezOnline_Genre').css('width').replace('px','');
		var nMaxMargin = 0 - ( nInvisibleCountGenres * nStep );
		var nMargin = jQuery('.VolnorezOnline_GenreContainer').css('margin-left').replace('px','');
		var nNewMargin = nMargin-nStep;
		// CHECK SLIDER POSITION
		if( nNewMargin < nMaxMargin ) return false;
		// SET SLIDER POSITION
		jQuery('.VolnorezOnline_GenreContainer').css('margin-left', nNewMargin );
		// CHECK STATUS ARROWS
		if( (nNewMargin-nStep) < nMaxMargin )
		{
			if( jQuery('#VolnorezOnline_SlideRight img').attr('class') == 'VolnorezOnline_ArrowActive' )
			{
				jQuery('#VolnorezOnline_SlideRight img').attr('class','VolnorezOnline_ArrowInactive');
				jQuery('#VolnorezOnline_SlideRight img').attr('src',jQuery('#VolnorezOnline_SlideRight img').attr('src').replace('forward.png','forward_inactive.png'));
			}
		}
		if( jQuery('#VolnorezOnline_SlideLeft img').attr('class') == 'VolnorezOnline_ArrowInactive' )
		{
			jQuery('#VolnorezOnline_SlideLeft img').attr('class','VolnorezOnline_ArrowActive');
			jQuery('#VolnorezOnline_SlideLeft img').attr('src',jQuery('#VolnorezOnline_SlideLeft img').attr('src').replace('back_inactive.png','back.png'));
		}
	});
}
// SLIDE TO LEFT GENRE LIST
function	VolnorezOnline_SlideLeft()
{
	// ON CLICK EVENT TO THE LEFT ARROW
	jQuery('#VolnorezOnline').delegate('#VolnorezOnline_SlideLeft','click',function(){
		// GET VALUES
		var nStep = jQuery('.VolnorezOnline_Genre').css('width').replace('px','');
		var nMaxMargin = 0;
		var nMargin = jQuery('.VolnorezOnline_GenreContainer').css('margin-left').replace('px','');
		var nNewMargin = parseInt(nMargin,10) + parseInt(nStep,10);
		// CHECK SLIDER POSITION
		if( nNewMargin > nMaxMargin ) return false;
		// SET SLIDER POSITION
		jQuery('.VolnorezOnline_GenreContainer').css('margin-left', nNewMargin );
		// CHECK STATUS ARROWS
		if( (parseInt(nNewMargin,10) + parseInt(nStep,10)) > nMaxMargin )
		{
			if( jQuery('#VolnorezOnline_SlideLeft img').attr('class') == 'VolnorezOnline_ArrowActive' )
			{
				jQuery('#VolnorezOnline_SlideLeft img').attr('class','VolnorezOnline_ArrowInactive');
				jQuery('#VolnorezOnline_SlideLeft img').attr('src',jQuery('#VolnorezOnline_SlideLeft img').attr('src').replace('back.png','back_inactive.png'));
			}
		}
		if( jQuery('#VolnorezOnline_SlideRight img').attr('class') == 'VolnorezOnline_ArrowInactive' )
		{
			jQuery('#VolnorezOnline_SlideRight img').attr('class','VolnorezOnline_ArrowActive');
			jQuery('#VolnorezOnline_SlideRight img').attr('src',jQuery('#VolnorezOnline_SlideRight img').attr('src').replace('forward_inactive.png','forward.png'));
		}
	});
}

// UPDATE CHANNELS LIST
function	VolnorezOnline_ChannelsListUpdate()
{
	// CHECK EXISTENCE WIDGET CONTENT
	if( !jQuery('#VolnorezOnline').length ) return false;
	// GET VALUES
	var nIDNowPlaying = ( jQuery('#VolnorezOnline_ChannelsContainer .VolnorezOnline_Pause').length )?jQuery('#VolnorezOnline_ChannelsContainer .VolnorezOnline_Pause').closest('.VolnorezOnline_PlayerContainer').find('.VolnorezOnline_ChannelID').val():false;
	var nIDGenre = '';
	if( nDefaultGenreID != 'All' && nDefaultGenreID != 'false' ) nIDGenre = nDefaultGenreID;
	else nIDGenre = jQuery('.VolnorezOnline_GenreSelected').attr('id').replace('VolnorezOnline_Genre_','');
	var bGenreList = false;
	var cPluginURL = jQuery('#VolnorezOnlineConfig_PluginURL').val();
	var proxy_url = cPluginURL + 'proxy.php';
	var ajax_url = 'http://volnorez.com/plugins/wsbonline/page_export/';
	
	// DATA TO BE SENT TO THE SERVER
	var data = new Object();
	data.get_online_list_current_genre = 'true';
	data.count_stations = nCountStations;
	data.language = cLanguage;
	data.genre_list = bGenreList;
	data.genre_id = nIDGenre;
	data.ajax_url = ajax_url;
	
	// ASYNCHRONOUS AJAX REQUEST
	jQuery.ajax({
		type: 'POST', url: proxy_url, dataType: 'html', data: data,
		success: function(data) {			if( data != '' )
			{
				// INSERT CONTENT TO THE END OF ELEMENT
				jQuery('#VolnorezOnline_ChannelsContainer').empty().append(data);
				// CHECK EXISTENCE LIVE CHANNEL
				if( nIDNowPlaying!==false )
				{
					var domPlayerInterface = jQuery('#VolnorezOnline').find('#VolnorezOnline_LiveChannel_'+nIDNowPlaying);
					if( domPlayerInterface.length )
					{
						// CHANGE STATUS PLAY BUTTON FOR THE LIVE CHANNEL
						domPlayerInterface.attr('class','VolnorezOnline_Pause');
						domPlayerInterface.attr('src',domPlayerInterface.attr('src').replace('play.png','pause.png'));
					}
				}
				// SHOW LOADED CONTENT
				jQuery('#VolnorezOnline').show();
			}
		}});
}

// SET COOKIE
function	VolnorezOnline_SetCookie (name, value, expires, path, domain, secure) {
	if( expires )
	{
		var date = new Date();
		date.setTime(date.getTime()+expires*1000);
		expires = date.toGMTString();
	}
	document.cookie = name + "=" + escape(value) +
		((expires) ? "; expires=" + expires : "") +
		((path) ? "; path=" + path : "") +
		((domain) ? "; domain=" + domain : "") +
		((secure) ? "; secure" : "");
}

// GET COOKIE
function	VolnorezOnline_GetCookie(name) {
	var cookie = " " + document.cookie;
	var search = " " + name + "=";
	var setStr = null;
	var offset = 0;
	var end = 0;
	if (cookie.length > 0) {
		offset = cookie.indexOf(search);
		if (offset != -1) {
			offset += search.length;
			end = cookie.indexOf(";", offset)
			if (end == -1) {
				end = cookie.length;
			}
			setStr = unescape(cookie.substring(offset, end));
		}
	}
	return(setStr);
}

// SET COOKIE INTERFACE
function	VolnorezOnline_SetInterfaceCookie( cName, cValue )
{
	var cCookie = VolnorezOnline_GetCookie( 'interface' );
	var dateCurrent = new Date();
	var cToday = '';
	var nNextYear = parseInt(dateCurrent.getFullYear(),10) + 1;
	cToday = dateCurrent.getFullYear() + ('0'+(dateCurrent.getMonth()+1)).slice(-2) + ('0'+(dateCurrent.getDate())).slice(-2);
	cCookieTime = nNextYear + ('0'+(dateCurrent.getMonth()+1)).slice(-2) + ('0'+(dateCurrent.getDate())).slice(-2);
	if( cCookie==null || cCookie.substr(16,8) <= cToday ) cCookie = '{"' + 'cookie_date' + '":"' + cCookieTime + '"}';
	var nExpires = dateCurrent.getTime();
	dateCurrent.setFullYear( dateCurrent.getFullYear() + 1 );
	nExpires = ( dateCurrent.getTime() - nExpires )/1000;
	var nPtr = cCookie.indexOf(cName);
	if( nPtr==-1 && cValue=='' )return;
	else if( nPtr!=-1 && cValue=='' )
	{
		var aCookie =$.parseJSON(cCookie);
		delete aCookie[cName];
		cCookie = JSON.stringify(aCookie);
	}
	else if( nPtr==-1 )
	{
		cCookie = cCookie.slice(0,-1);
		cCookie += ',"' + cName + '":"' + cValue + '"}';
	}
	else if( nPtr!=-1 )
	{
		var aCookie =jQuery.parseJSON(cCookie);
		aCookie[cName] = cValue;
		cCookie = JSON.stringify(aCookie);
	}
	VolnorezOnline_SetCookie('interface', cCookie, nExpires, '/');
}
 
function AC_AddExtension_Player(src, ext)
{
  if (src.indexOf('?') != -1)
    return src.replace(/\?/, ext+'?'); 
  else
    return src + ext;
}

function AC_Generateobj_Player(objAttrs, params, embedAttrs) 
{ 
  var str = '<object ';
  for (var i in objAttrs)
    str += i + '="' + objAttrs[i] + '" ';
  str += '>';
  for (var i in params)
    str += '<param name="' + i + '" value="' + params[i] + '" /> ';
  str += '<embed ';
  for (var i in embedAttrs)
    str += i + '="' + embedAttrs[i] + '" ';
  str += ' ></embed></object>';

  return str;
}

function AC_FL_RunContent_Player(){
  var ret = 
    AC_GetArgs
    (  arguments, ".swf", "movie", "clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
     , "application/x-shockwave-flash"
    );
  return AC_Generateobj_Player(ret.objAttrs, ret.params, ret.embedAttrs);
}

function AC_GetArgs(args, ext, srcParamName, classid, mimeType){
  var ret = new Object();
  ret.embedAttrs = new Object();
  ret.params = new Object();
  ret.objAttrs = new Object();
  for (var i=0; i < args.length; i=i+2){
    var currArg = args[i].toLowerCase();    

    switch (currArg){	
      case "classid":
        break;
      case "pluginspage":
        ret.embedAttrs[args[i]] = args[i+1];
        break;
      case "src":
      case "movie":	
        args[i+1] = AC_AddExtension_Player(args[i+1], ext);
        ret.embedAttrs["src"] = args[i+1];
        ret.params[srcParamName] = args[i+1];
        break;
      case "onafterupdate":
      case "onbeforeupdate":
      case "onblur":
      case "oncellchange":
      case "onclick":
      case "ondblClick":
      case "ondrag":
      case "ondragend":
      case "ondragenter":
      case "ondragleave":
      case "ondragover":
      case "ondrop":
      case "onfinish":
      case "onfocus":
      case "onhelp":
      case "onmousedown":
      case "onmouseup":
      case "onmouseover":
      case "onmousemove":
      case "onmouseout":
      case "onkeypress":
      case "onkeydown":
      case "onkeyup":
      case "onload":
      case "onlosecapture":
      case "onpropertychange":
      case "onreadystatechange":
      case "onrowsdelete":
      case "onrowenter":
      case "onrowexit":
      case "onrowsinserted":
      case "onstart":
      case "onscroll":
      case "onbeforeeditfocus":
      case "onactivate":
      case "onbeforedeactivate":
      case "ondeactivate":
      case "type":
      case "codebase":
        ret.objAttrs[args[i]] = args[i+1];
        break;
      case "width":
      case "height":
      case "align":
      case "vspace": 
      case "hspace":
      case "class":
      case "title":
      case "accesskey":
      case "name":
      case "id":
      case "tabindex":
        ret.embedAttrs[args[i]] = ret.objAttrs[args[i]] = args[i+1];
        break;
      default:
        ret.embedAttrs[args[i]] = ret.params[args[i]] = args[i+1];
    }
  }
  ret.objAttrs["classid"] = classid;
  if (mimeType) ret.embedAttrs["type"] = mimeType;
  return ret;
}

(function($)  
{  
    $.fn.externalInterface = function(args)  
    {  
        this.each(function()  
        {  
            if(typeof(args.method) != 'undefined')  
            {  
                try  
                {  
                    if(typeof(args.args) != 'undefined')  
                    {  
                        var data = this[args.method](args.args);  
                    }  
                    else  
                    {  
                        var data = this[args.method]();  
                    }  
                      
                    if(typeof(args.success) != 'undefined')  
                    {  
                        args.success(data);  
                    }  
                }  
                catch(error)  
                {  
                    if(typeof(args.error) != 'undefined')  
                    {  
                        args.error(error); 
                    }  
                }  
            }  
        });  
      
        return this;  
    };  
})(jQuery);  

// FLASH EXTERNAL INTERFACE
function	VolnorezOnline_ExternalInterface( cID, cFunction, aParams )
{
	var dom = jQuery('#'+cID);
	var bOK = false;
	dom.each(function(){
			if( this[cFunction]!=undefined )
				bOK = true;
		});
	if( !bOK )dom = jQuery('object #' + cID);

	dom.externalInterface( {method: cFunction, args: aParams} );
}

// CHANGE LIVE STATION
function	VolnorezOnline_ChangeLiveStation()
{
	// ON CLICK PLAY BUTTON
	jQuery('body').delegate('.VolnorezOnline_Play','click',function(){
		// GET CONFIG LIVE CHANNEL
		var domConfig = jQuery(this).closest('.VolnorezOnline_PlayerContainer').find('.VolnorezOnline_LiveConfig');
		// CHECK CHANNEL STATUS
		if( !domConfig.length )
		{
			alert('Эта станция сейчас не транслируется!');
			return;
		}
		// RESUME BROADCAST
		if( domConfig.find('.VolnorezOnline_LiveStatus').text() == 'pause' )
		{
			// CHANGE BUTTON ON CHANNEL LOGO
			jQuery(this).attr('class','VolnorezOnline_Pause');
			jQuery(this).attr('src',jQuery(this).attr('src').replace('play.png','pause.png'));
			// SEND COMMAND TO FLASH
			VolnorezOnline_ExternalInterface( 'export_live_player', 'sendToFlash', new Array('resume') );
			// CHANGE BUTTON ON MAIN PLAYER INTERFACE
			jQuery('#VolnorezOnline_PlayMin').attr('id','VolnorezOnline_PauseMin');
			jQuery('#VolnorezOnline_PauseMin').attr('src',jQuery('#VolnorezOnline_PauseMin').attr('src').replace('play_min.png','pause_min.png'));
		}
		// BEGIN BROADCASTING FROM ANOTHER CHANNEL
		else if( domConfig.find('.VolnorezOnline_LiveStatus').text() == '' )
		{
			// GET VALUES
			var nJSCodeID = jQuery(domConfig).find('.VolnorezOnline_JSCodeID').val();
			var cURL = jQuery(domConfig).find('.VolnorezOnline_URL').val();
			var cBColor = jQuery(domConfig).find('.VolnorezOnline_BColor').val();
			var cChannelTitle = jQuery(domConfig).find('.VolnorezOnline_cTitle').val();
			var nIDChannel = jQuery(domConfig).find('.VolnorezOnline_ChannelID').val();
			jQuery('#VolnorezOnline_ChannelsContainer .VolnorezOnline_Pause').each(function(){
				jQuery(this).attr('class','VolnorezOnline_Play');
				jQuery(this).attr('src',jQuery(this).attr('src').replace('pause.png','play.png'));
			});
			// CLEAR LIVE STATUS ALL CHANNELS
			jQuery('#VolnorezOnline_ChannelsContainer .VolnorezOnline_LiveStatus').each(function(){
				jQuery(this).text('');
			});
			// CHANGE BUTTON ON CHANNEL LOGO
			jQuery(this).attr('class','VolnorezOnline_Pause');
			jQuery(this).attr('src',jQuery(this).attr('src').replace('play.png','pause.png'));
			// SEND COMMAND TO FLASH
			VolnorezOnline_ExternalInterface( 'export_live_player', 'sendToFlash', new Array( 'play_new', nJSCodeID, cURL, cBColor) );
			console.log('play_new'+'|'+ nJSCodeID+'|'+ cURL+'|'+ cBColor);
			// CHANGE BUTTON ON MAIN PLAYER INTERFACE
			jQuery('#VolnorezOnline_PlayMin').attr('id','VolnorezOnline_PauseMin');
			jQuery('#VolnorezOnline_PauseMin').attr('src',jQuery('#VolnorezOnline_PauseMin').attr('src').replace('play_min.png','pause_min.png'));
			// CHANGE OR SET TITLE LIVE CHANNEL ON MAIN PLAYER INTERFACE
			jQuery('#VolnorezOnline_ChannelTitleContainer').text(cChannelTitle);
			jQuery('#VolnorezOnline_StoppedStation').val(nIDChannel);
		}
	});
	// ON CLICK PAUSE BUTTON
	jQuery('body').delegate('.VolnorezOnline_Pause','click',function(){
		// GET CONFIG LIVE CHANNEL
		var domConfig = jQuery(this).closest('.VolnorezOnline_PlayerContainer').find('.VolnorezOnline_LiveConfig');
		// SET LIVE STATUS
		domConfig.find('.VolnorezOnline_LiveStatus').text('pause');
		// CHANGE BUTTON ON CHANNEL LOGO
		jQuery(this).attr('class','VolnorezOnline_Play');
		jQuery(this).attr('src',jQuery(this).attr('src').replace('pause.png','play.png'));
		// SEND COMMAND TO FLASH
		VolnorezOnline_ExternalInterface( 'export_live_player', 'sendToFlash', new Array('stop') );
		// CHANGE BUTTON ON MAIN PLAYER INTERFACE
		jQuery('#VolnorezOnline_PauseMin').attr('id','VolnorezOnline_PlayMin');
		jQuery('#VolnorezOnline_PlayMin').attr('src',jQuery('#VolnorezOnline_PlayMin').attr('src').replace('pause_min.png','play_min.png'));
	});
}

// CHANGE VOLUME
function	VolnorezOnline_Volume()
{
	jQuery(document).delegate('#VolnorezOnline_Vol','mousedown',function(){
		// GET VALUES
		var nStartX = Math.ceil(jQuery('#VolnorezOnline_Vol').offset().left);
		var nWidth = jQuery('#VolnorezOnline_Vol').width();
		// "CLICK" ON VOLUME
		jQuery(document).bind('mouseup.VolnorezVolume',function(e){
			jQuery(document).unbind('mouseup.VolnorezVolume');
			jQuery(document).unbind('mousemove.VolnorezVolume');
			var nVol = (e.clientX - nStartX)/nWidth;
			if( nVol > 1 ) nVol = 1;
			if( nVol < 0 ) nVol = 0;
			if( nVol != nNowVolume )
			{
				jQuery('#VolnorezOnline_Volume').width(100-(nVol*100)+'%');
				VolnorezOnline_SetVolume( nVol );
			}
			nNowVolume = nVol;
			return;
		});
		// ON MOUSE MOVE
		jQuery(document).bind('mousemove.VolnorezVolume',function(e){
			var nVol = (e.clientX - nStartX)/nWidth;
			if( nVol > 1 ) nVol = 1;
			if( nVol < 0 ) nVol = 0;
			if( nVol != nNowVolume )
			{
				jQuery('#VolnorezOnline_Volume').width(100-(nVol*100)+'%');
				VolnorezOnline_SetVolume( nVol );
			}
			nNowVolume = nVol;
			jQuery(document).bind('mouseup.VolnorezVolume',function(){
				jQuery(document).unbind('mouseup.VolnorezVolume');
				jQuery(document).unbind('mousemove.VolnorezVolume');
			});
		});
	});
}

// SET VOLUME
function	VolnorezOnline_SetVolume( nVol )
{
	VolnorezOnline_ExternalInterface( 'export_live_player', 'sendToFlash', new Array('set_volume','','',nVol) );
}

// PLAY/PAUSE MAIN INTERFACE
function	VolnorezOnline_MainPlay()
{
	// PLAY BUTTON
	jQuery(document).delegate('#VolnorezOnline_PlayMin','click',function(){
		// GET VALUES
		var nChannelID = jQuery('#VolnorezOnline_StoppedStation').val();
		var domPlayButton = jQuery('#VolnorezOnline_LiveChannel_'+nChannelID);
		// CHECK EXISTENCE LIVE CHANNEL
		if( nChannelID == '' )
		{
			alert('Выберите станцию из списка!');
			return;
		}
		// SEND COMMAND TO FLASH
		VolnorezOnline_ExternalInterface( 'export_live_player', 'sendToFlash', new Array('resume') );
		// CHANGE BUTTON ON MAIN PLAYER INTERFACE
		jQuery(this).attr('id','VolnorezOnline_PauseMin');
		jQuery(this).attr('src',jQuery(this).attr('src').replace('play_min.png','pause_min.png'));
		// CHANGE BUTTON ON CHANNEL LOGO
		domPlayButton.attr('class','VolnorezOnline_Pause');
		domPlayButton.attr('src',domPlayButton.attr('src').replace('play.png','pause.png'));

	});
	// PAUSE BUTTON
	jQuery(document).delegate('#VolnorezOnline_PauseMin','click',function(){
		jQuery('#VolnorezOnline_ChannelsContainer .VolnorezOnline_Pause').each(function(){
			jQuery(this).attr('class','VolnorezOnline_Play');
			jQuery(this).attr('src',jQuery(this).attr('src').replace('pause.png','play.png'));
		});
		// SEND COMMAND TO FLASH
		VolnorezOnline_ExternalInterface( 'export_live_player', 'sendToFlash', new Array('stop') );
		// CHANGE BUTTON ON MAIN PLAYER INTERFACE
		jQuery(this).attr('id','VolnorezOnline_PlayMin');
		jQuery(this).attr('src',jQuery(this).attr('src').replace('pause_min.png','play_min.png'));
	});
}

// ON HOVER CHANNEL LOGO
function	VolnorezOnline_HoverPlayButton()
{
	jQuery(document).delegate('.VolnorezOnline_PlayerContainer','mouseenter',function(){
		jQuery(this).find('.VolnorezOnline_PlayerInterface').show();
	});
	jQuery(document).delegate('.VolnorezOnline_PlayerContainer','mouseleave',function(){
		jQuery(this).find('.VolnorezOnline_PlayerInterface').hide();
	});
}