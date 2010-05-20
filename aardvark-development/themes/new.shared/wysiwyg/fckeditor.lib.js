/**
 * Copyright (C) 2005, 2006, 2007, 2008  Brice Burgess <bhb@iceburg.net>
 * 
 * The contents of this directory (themes/default/*) are part of poMMo (http://www.pommo.org)
 * 
 * poMMo is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published 
 * by the Free Software Foundation; either version 2, or any later version.
 * 
 * poMMo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See
 * the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with program; see the file docs/LICENSE. If not, write to the
 * Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 */

// Abstraction Libary for FCKEdior 2.5.1
var wysiwyg = {
	enabled: false, // state of WYSIWYG. False == Disabled, True == Enabled (visible). Default to disabled. Compose.tpl will enable it.
	language: 'en', // the WYSIWYG language
	baseURL: '/pommo/themes/wysiwyg/', // the URL path to poMMo's WYSIWYG directory [written via compose.tpl]
	fck: false, // instance of the FCKEditor
	textarea: false, // shortcut to the original textarea [jQuery Object]
	enable: function(){ // enable the WYSIWYG
		if(!this.fck) {
			
			// lock the interface
			poMMo.pause();
			
			// prepare FCKEditor
			var fck = new FCKeditor('body');
			fck.Height = '400';
			fck.Config['CustomConfigurationsPath'] = this.baseURL+'fckeditor.conf.js';
			this.baseURL = this.baseURL+'fckeditor/';
			fck.BasePath = this.baseURL;
			fck.ToolbarSet = 'Pommo' ;
			
			// set language, and LTR/RTL ("left to right, right to left")
			fck.Config['DefaultLanguage'] = this.language;
			fck.Config['ContentLangDirection'] = 'ltr';

			// start FCKEditor
			fck.ReplaceTextarea();
		}
		else {
			// hide the textarea, update the wysiwyg with its data
			this.fck.SetData(this.textarea.hide()[0].value);

			// show the WYSIWYG
			$('#'+this.fck.Name+'___Frame').show();
			
			// Hack for Gecko 1.0.x  (FCK stops editing when hidden)
			if(!document.all)
				if(this.fck.EditMode == FCK_EDITMODE_WYSIWYG)
					this.fck.MakeEditable();
		}
		return this.enabled = true;
	},
	disable: function(){
		if(!this.enabled || !this.fck)
			return false;
		
		// hide the WYSIWYG
		$('#'+this.fck.Name+'___Frame').hide();
		
		// show the textarea, update it with WYSIWYG contents
		this.textarea.show()[0].value = this.fck.GetXHTML();
		
		this.enabled = false;
		return true;
	},
	inject: function(text){ // called when adding a personalization
		
		// check if WYSIWYG is enabled
		if (this.enabled) {
			
			// check if text should be encased in an anchor tag.
			if(text == '[[!unsubscribe]]')
				text = '<a href="'+text+'">'+this.t_unsubscribe+'</a>';
			if(text == '[[!weblink]]')
				text = '<a href="'+text+'">'+this.t_weblink+'</a>';
				
			this.fck.InsertHtml(text);
		}
		else {
			this.textarea[0].value += text;
		}
	},
	getBody: function() {
		return (this.enabled) ?
			this.fck.GetXHTML(true) :
			this.textarea.val();
	},
	init: function(o) { 
		this.fck = false; 
		wysiwyg = $.extend(wysiwyg,o);
	},
	t_weblink: "View this Mailing on the Web", // translated via compose.tpl
	t_unsubscribe: "Unsubscribe or Update your Records" // translated via compose.tpl
}

function FCKeditor_OnComplete(instance) {
	wysiwyg.fck = instance;
	
	// unlock the interface
	poMMo.resume();
}
