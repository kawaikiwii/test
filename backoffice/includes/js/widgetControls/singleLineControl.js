wcmSingleLineControl = Class.create(wcmWidgetControl, {

    initialize: function($super, argWidget, argId)
    {
        var options = {
            overlayWidth: false,
            overlayPosition: true,
            parentId: argId,
            id: argId + '_singleLineControl',
            height: 24,
			width: 321,
            boxed: true
        }    
        $super(argWidget, options);
        
        var wc = this;
        
        
        singleLineContainer = new Element('div');
        singleLineContainer.addClassName('singleLineContainer');
        singleLineContainer.id = wc.parentId + '_container';
        
        saveButton = new Element('div');
        saveButton.addClassName('buttonSave');
        saveButton.id = wc.parentId + '_buttonSave';
        saveButton.observe('click', function()
        {
        	$(wc.parentId).innerHTML = $(wc.parentId + '_formEl').value;
        	wc.save($(wc.parentId + '_formEl').value);
        	wc.setPos();
        	wc.hide();
        	});        
        
        cancelButton = new Element('div');
        cancelButton.addClassName('buttonClose');
        cancelButton.id = wc.parentId + '_buttonClose';
        cancelButton.observe('click', function()
				{
					wc.hide();
				
				});
        
        inputEl = new Element('input');
        inputEl.className = 'singleLineControl';
        inputEl.type = 'text';
        inputEl.id = wc.parentId + '_formEl';
        inputEl.observe('keypress', function(argEvent)
        {
            key = argEvent.which || argEvent.keycode;
            if (key == Event.KEY_RETURN)
            {
                $(wc.parentId).innerHTML = argEvent.element().value;
                wc.save(argEvent.element().value);
                wc.setPos();
                wc.hide();
            }
            return false;
        });
        
        singleLineContainer.appendChild(inputEl);
        singleLineContainer.appendChild(saveButton);
        singleLineContainer.appendChild(cancelButton);

				$(this.widgetBoxId).appendChild(singleLineContainer);
        
        $(argId).observe('click', function(argEvent)
        {
            
            wc.setPos();
            wc.show();
            
            $(wc.parentId + '_formEl').focus();
            
            // Update the form element
            $(wc.parentId + '_formEl').value = $(wc.parentId).innerHTML;
            argEvent.stop();
        });
    },
    
    rem: function()
    {
        $(this.parentId + '_formEl').remove();
    }
});