wcmSortableControl = Class.create(wcmWidgetControl, {
    initialize: function($super, argWidget, argId)
    {
        var options = {
            parentId: argId,
            boxed: true,
            overlayPosition: false,
            overlayDimensions: false,
            top: $(argId).offsetTop,
            left: $(argId).offsetLeft - 20,
            width: 20,
            height: $(argId).offsetHeight
        };
        $super(argWidget, options);
        
        var wc = this;
        
        Sortable.create(this.parentId, {
            onUpdate: function()
            {
                wc.save(Sortable.sequence(wc.parentId));
            }
        });
        
        $$('#' + wc.parentId + ' li').each(function(s)
        {
            if (s.getStyle('listStyle') == 'none')
            {
                s.innerHTML = '<span class="sortControl"></span>' + s.innerHTML;
            }
        });
        
        // Setup add list item
        /**
        var addItem = new Element('div');
        addItem.addClassName('sortControlButton');
        addItem.addClassName('add');
        addItem.observe('click', function(argEvent)
        {
            var listQuery = new wcmListQueryControl(wc);
        });
        
        $(this.widgetBoxId).appendChild(addItem);
        
        $(this.widgetBoxId).appear({ to: 0.5 });
        $(this.widgetBackgroundId).appear({ to: 0.8 });
        
        $(this.widgetBoxId).observe('mouseover', function(argEvent) { this.setStyle({opacity: 1.0})});
        
        $(this.widgetBoxId).observe('mouseout', function(argEvent) { this.setStyle({opacity: 0.5})});
        **/
    },
    
    rem: function()
    {
        $(this.widgetBoxId).remove();
        $(this.widgetBackgroundId).remove();
        $$('#' + this.parentId + ' .sortControl').each(function(s) { s.remove(); });
        Sortable.destroy(this.parentId);
    }
});

wcmListQueryControl = Class.create({
    
    initialize: function(argParentControl)
    {
        
        var searchBox = new Element('div');
        var parent = argParentControl;
        searchBox.setStyle({
            top: $(parent.parentId).offsetTop + 'px',
            left: $(parent.parentId).offsetLeft + 'px',
            width: '300px',
            height: '200px',
            backgroundColor: '#ccc',
            opacity: 0.9,
            position: 'absolute',
            display: 'none'
        });
                
        
        searchBox.id = argParentControl.widgetBoxId + '_searchControl';
        $(parent.parentId).appendChild(searchBox);
        
        new Ajax.Updater(searchBox.id, 'searchBox.php?id=' + parent.parentId + '&widget=' + parent.widget.contentId, {
            evalScripts: true
        });
        
        $(searchBox.id).grow({direction: 'top-left', duration: 0.5});
    },
    
    rem: function()
    {
        $(searchBox.id).remove();
    }
});
