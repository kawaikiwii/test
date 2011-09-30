var wcmWidgetContainer = {
    widgets: new Hash(),
    addWcmWidget: function(argWidget)
    {
        wcmWidgetContainer.widgets.set(argWidget.contentId, argWidget);
    }
}
    

wcmWidget = function(argId, argOptions)
{
    this.contentId = argId;
    this.widgetControls = new Hash();
    this.changes = new Hash();
    
    this.addChange = function(argId, argValue)
    {
        this.changes.set(argId, argValue);
    }
    
    this.addWidgetControl = function(argControlName, argId)
    {
        eval("wobj = new " + argControlName + "(this, '" + argId + "');");
        this.widgetControls.set(wobj.parentId, wobj);
        return wobj;
    }
    
    this.getWidgetControl = function(argId)
    {
        return this.widgetControls.get(argId);
    }
    
    this.rem = function()
    {
        this.widgetControls.each(function(s)
        {
            s.value.rem();
        });
    }
    
    this.refreshWidgets = function()
    {
        this.rem();
        this.widgetControls = new Hash();
        
        var masterWidget = this;
        $$("#" + this.contentId + " .editable").each(function(s) {
            masterWidget.addWidgetControl($w(s.className)[0], s.id);
        });
    }

    this.init = function()
    {
    
    
        /**
         * Setup the toolbar
         *
         * The toolbar needs a save, revert, close button
         */
        this.refreshWidgets();
        wcmWidgetContainer.addWcmWidget(this);
    }
}


var wcmWidgetControl = Class.create({
    initialize: function(argWidget, argOptions)
    {
        this.initialized = false;
        this.widget = argWidget;
        this.parentId = argOptions.parentId;

        if (argOptions.boxed)
        {        
            this.options = {
                width: false,
                height: false,
                id: '',
                widgetUrl: '',
                loadCallback: false,
                saveCallback: false,
                destroyCallback: false,
                cancelCallback: false,
                hideCallback: false,
                left: false,
                top: false,
                widgetClassname: 'wcmWidget',
                widgetBgClassname: 'wcmWidgetBg',
                overlayPosition: false,
                overlayDimensions: false,
                overlayHeight: false,
                overlayWidth: false,
                overlayTop: false,
                overlayLeft: false,
                appendNode: false
            };
        
            if (argOptions)
            {
                Object.extend(this.options, argOptions);
            }
            
            this.widgetBackgroundId = '';
            this.widgetBoxId = '';
            
            var styles = {
                width: this.options.width + 'px',
                height: this.options.height + 'px',
                position: 'absolute',
                display: 'none',
                overflow: 'auto',
                top: this.options.top + 'px',
                left: this.options.left + 'px',
                opacity: 0.0
            };
            

            
            var widgetBox = new Element('div');
            var widgetBackground = new Element('div');
            
            widgetBox.id = this.options.id + '_widgetBox';
            widgetBackground.id = this.options.id + '_widgetBackground';
            
            widgetBackground.addClassName(this.options.widgetBgClassname);
            widgetBox.addClassName(this.options.widgetClassname);;
            
            if (this.options.appendNode)
            {
                this.options.appendNode.appendChild(widgetBackground);
                this.options.appendNode.appendChild(widgetBox);
            } else {
	            $(this.parentId).up().appendChild(widgetBackground);
	            $(this.parentId).up().appendChild(widgetBox);
	        }
        
            $(widgetBox.id).setStyle(styles);
            $(widgetBackground.id).setStyle(styles);    
            
            // Store IDs
            this.widgetBoxId = widgetBox.id;
            this.widgetBackgroundId = widgetBackground.id;
            
            this.setPos();
        }
           
        this.initialized = true;
    },
    
    setPos: function()
    {
        var styles = { };
        if (this.options.overlayPosition)
        {
            styles.top = $(this.parentId).offsetTop + 'px';
            styles.left = $(this.parentId).offsetLeft + 'px';
        }
        
        if (this.options.overlayDimensions)
        {
            styles.width = $(this.parentId).offsetWidth + 'px';
            styles.height = $(this.parentId).offsetHeight + 'px';
        }
        
        if (this.options.overlayWidth)
        {
            styles.width = $(this.parentId).offsetWidth + 'px';
        }
        
        if (this.options.overlayHeight)
        {
            styles.height = $(this.parentId).offsetHeight + 'px';
        }
        
        if (this.options.overlayTop)
        {
            styles.top = $(this.parentId).offsetTop + 'px';
        }
        
        if (this.options.overlayLeft)
        {
            styles.left = $(this.parentId).offsetLeft + 'px';
        }
        
        if (this.options.width) styles.width = this.options.width + 'px';
        if (this.options.height) styles.height = this.options.height + 'px';
        if (this.options.top) styles.top = this.options.top + 'px';
        if (this.options.left) styles.left = this.options.left + 'px';
        
        
        $(this.widgetBoxId).setStyle(styles);
        $(this.widgetBackgroundId).setStyle(styles);
    },  
    
    save: function(argValue)
    {
       this.widget.addChange(this.parentId, argValue);
    },
    
    show: function()
    {
       if (!this.initialized)
       {
           alert('Widget Control {id: ' + this.parentId + '} isnt initialized, so nothing to show!');
           return false;
       }
       
       if (this.options.boxed)
       {
        $(this.widgetBoxId).appear();
        $(this.widgetBackgroundId).appear();
       }
       
    },
    
    hide: function()
    {
       $(this.widgetBoxId).hide();
       $(this.widgetBackgroundId).hide();
       
       if (this.options.hideCallback != false)
       {
            this.options.hideCallback();
       }
       
    },
    
    rem: function()
    {
        $(this.widgetBoxId).remove();
        $(this.widgetBackgroundId).remove();
    }
});





