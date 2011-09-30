
wcmBasicTextEditorControl = Class.create(wcmWidgetControl, {
    initialize: function($super, argWidget, argId)
    {
        var options = {
            parentId: argId,
            id: argId,
            boxed: true,
            top: $(argId).up().offsetTop,
            left: $(argId).up().offsetLeft + 0,
            width: 322,
            height: $(argId).up().offsetHeight
        };
       
        $super(argWidget, options)
        
        var wc = this;
        $(argId).observe('click', function(argEvent)
        {
        
            var editor = new Element('div');
            editor.id = wc.widgetBoxId + '_container';
            editor.addClassName('basicTextEditor');
        
            // giggity
            var ta = new Element('textarea');
            ta.id = wc.widgetBoxId + '_textarea';
            ta.addClassName('textarea');
            ta.value = $(wc.parentId).innerHTML;
            
            
            var taControl = new Element('div');
            taControl.id = wc.widgetBoxId + '_textareaControl';
            taControl.addClassName('basicTextEditorControl');
            
            var taSave = new Element('div');
            taSave.id = wc.widgetBoxId + '_buttonSave';
            taSave.addClassName('buttonSave');
            
            var taClose = new Element('div');
            taClose.id = wc.widgetBoxId + '_buttonClose';
            taClose.addClassName('buttonClose');
            
            taSave.observe('click', function(argEvent)
            {
                wc.save(ta.value);
                $(wc.parentId).innerHTML = ta.value;
                ta.remove();
                taControl.remove();
                wc.hide();
            });
            
            taClose.observe('click', function(argEvent)
            {
                ta.remove();
                taControl.remove();
                wc.hide();
            });
            
            taControl.appendChild(taSave);
            taControl.appendChild(taClose);
            
            editor.appendChild(ta);
			editor.appendChild(taControl);
            $(wc.widgetBoxId).appendChild(editor);
            
            wc.setPos();       
            wc.show();
            argEvent.stop();           
        });
    }   
        

});
        
       
