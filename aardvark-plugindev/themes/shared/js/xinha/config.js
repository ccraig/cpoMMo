    xinha_editors = null;
    xinha_init    = null;
    xinha_config  = null;
    xinha_plugins = null;

    xinha_init = xinha_init ? xinha_init : function()
    {
      xinha_plugins = xinha_plugins ? xinha_plugins :
      [
       'CharacterMap',
       'EditTag',
       'FullPage',
       'FullScreen',
       'GetHtml',
       'HorizontalRule',
       'ListType',
       'PasteText',
       'QuickTag',
       'TableOperations'
      ];
             // THIS BIT OF JAVASCRIPT LOADS THE PLUGINS, NO TOUCHING  :)
             if(!HTMLArea.loadPlugins(xinha_plugins, xinha_init)) return;


      xinha_editors = xinha_editors ? xinha_editors :
      [
        'body'
      ];

       xinha_config = xinha_config ? xinha_config() : new HTMLArea.Config();

      xinha_editors   = HTMLArea.makeEditors(xinha_editors, xinha_config, xinha_plugins);

      HTMLArea.startEditors(xinha_editors);
    }

    // moved to jQ Document.Ready call in mailings_send2.tpl 
    //window.onload = xinha_init;
