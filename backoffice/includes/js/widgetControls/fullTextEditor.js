wcmFullTextEditorControl = Class.create(wcmWidgetControl, {
    initialize: function($super, argWidget, argId)
    {
        var options = {
            parentId: argId,
            id: argId,
            boxed: true,
            overlayPosition: true,
            width: $(argId).offsetWidth + 50,
            height: $(argId).offsetHeight + 50
        };
        
        $super(argWidget, options);
        
        var wc = this;
        
        $(argId).observe('click', function(argEvent)
        {
        
            // create textarea
            ta = new Element('textarea');
            ta.style.width = '100%';
            ta.style.height = '100%';
            ta.id = wc.widget.contentId + '_' + wc.widgetBoxId + '_tinyMCE';
            ta.value = $(wc.parentId).innerHTML;
            
            $(wc.widgetBoxId).appendChild(ta);
        
			tinyMCE.init({
			    // General options
			    mode : "exact",
			    elements: ta.id,
			    theme : "advanced",
			    plugins : "nssave,safari,style,save,advhr,emotions,inlinepopups,insertdatetime,media,searchreplace,print,contextmenu,paste,fullscreen,visualchars,nonbreaking,xhtmlxtras",
			    height: "100%",
			    // Theme options
			    theme_advanced_buttons1 : "nsSave,save,cancel,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
			    theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,cleanup,code,|,insertdate,inserttime",
			    theme_advanced_buttons3 : "hr,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,fullscreen,styleprops,visualchars,nonbreaking",
			    theme_advanced_toolbar_location : "top",
			    theme_advanced_toolbar_align : "left",
			    theme_advanced_statusbar_location : "bottom",
			    theme_advanced_resizing : false,
			    
			    save_onsavecallback: function()
			    {
			        $(wc.parentId).innerHTML = ta.value;
			        // ar instance = tinymce.EditorManager.get(wc.widgetBoxId + '_tinyMCE');
			        // tinymce.EditorManager.remove(instance);
			        ta.remove();
			        wc.hide();
			        return false;
			    }
				
				
				
				
			});
			wc.show();
	   });
    }          
});