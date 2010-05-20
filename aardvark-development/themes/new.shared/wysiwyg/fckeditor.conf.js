/**
 * poMMo's FCKEditor Configuration.
 * 
 * More options available in the default configuration file;
 *   fckeditor/fckconfig.js
 *   
 * Changes to this file will override the default settings.
 */

FCKConfig.FullPage = false ;
FCKConfig.DocType = '' ;
FCKConfig.BaseHref = '' ;

FCKConfig.Plugins.Add( 'dragresizetable' );

FCKConfig.AutoDetectLanguage = false;

FCKConfig.SkinPath = FCKConfig.BasePath + 'skins/silver/' ;

FCKConfig.ToolbarSets["Pommo"] = [
	['Cut','Copy','Paste','PasteText','PasteWord'],
	['Undo','Redo','-','Find','-','SelectAll','RemoveFormat'],
	['Form','Checkbox','Radio','TextField','Textarea','Select','Button','HiddenField'],
	['SpecialChar','Rule'],
	['ShowBlocks'],
	'/',
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	['OrderedList','UnorderedList','-','Outdent','Indent','Blockquote'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
	['Link','Unlink','Anchor'],
	['Image','Flash','Table'],
	'/',
	['Style','FontFormat','FontName','FontSize'],
	['TextColor','BGColor','-','FitWindow']
] ;

FCKConfig.EnterMode = 'p' ;			// p | div | br
FCKConfig.ShiftEnterMode = 'br' ;	// p | div | br
FCKConfig.FontNames		= 'Arial;Comic Sans MS;Courier New;Eurostile;Gill Sans;Tahoma;Times New Roman;Verdana' ;
FCKConfig.FirefoxSpellChecker	= true ;
FCKConfig.MaxUndoLevels = 16 ;

FCKConfig.LinkBrowser = false ;
FCKConfig.ImageBrowser = false ;
FCKConfig.FlashBrowser = false ;
FCKConfig.LinkUpload = false ;
FCKConfig.ImageUpload = false ;
FCKConfig.FlashUpload = false ;


