FCKConfig.AutoDetectLanguage = false ;
FCKConfig.DefaultLanguage = "ru" ;

FCKConfig.StartupShowBlocks = true ;

FCKConfig.ProtectedSource.Add( /<\?[\s\S]*?\?>/g ) ;	// PHP style server side code

FCKConfig.FormatSource = false;
FCKConfig.FormatOutput = false;

FCKConfig.Plugins.Add( 'typograf' ) ;

FCKConfig.EMailProtection = 'encode' ; // none | encode | function

FCKConfig.ForcePasteAsPlainText = false ; 
FCKConfig.TemplateReplaceAll = false ;

FCKConfig.LinkBrowser = true;

FCKConfig.SmileyPath	= '/uploads/smiles/' ;
FCKConfig.SmileyImages	= ['angry.gif','bigsurprise.gif','blank.gif','cheese.gif','confused.gif','downer.gif','embarrassed.gif','exclaim.gif','grin.gif','grrr.gif','gulp.gif','hmm.gif','kiss.gif','lol.gif','longface.gif','mad.gif','ohh.gif','ohoh.gif','question.gif','rasberry.gif','rolleyes.gif','shade_cheese.gif','shade_grin.gif','shade_hmm.gif','shade_mad.gif','shade_smile.gif','shade_smirk.gif','shock.gif','shuteye.gif','sick.gif','smile.gif','smirk.gif','snake.gif','surprise.gif','tongue_laugh.gif','tongue_rolleye.gif','tongue_wink.gif','vampire.gif','wink.gif','zip.gif'] ;
FCKConfig.SmileyColumns = 8 ;
FCKConfig.SmileyWindowWidth		= 320 ;
FCKConfig.SmileyWindowHeight	= 210 ;

FCKConfig.SkinPath = FCKConfig.BasePath + 'skins/silver/' ;

FCKConfig.ToolbarSets["MaxSiteDefault"] = [
	['Source','FitWindow','Preview','-','Templates'],
	['Cut','Copy','Paste','PasteText','PasteWord'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['Image','Table','Rule','Smiley','SpecialChar','-','About'],	
	'/',
	['Style','FontFormat','FontName','FontSize'],
	'/',
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	['Link','Unlink','Anchor'],
	['OrderedList','UnorderedList','-','Outdent','Indent','Blockquote','CreateDiv'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
	['TextColor','BGColor'],
	['typograf']
] ;
