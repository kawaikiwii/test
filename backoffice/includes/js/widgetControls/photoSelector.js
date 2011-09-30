
wcmPhotoSelector = Class.create(wcmWidgetControl, {

    initialize: function($super, argWidget, argId)
    {
	    var options = {
	        width: 300,
	        height: 200,
	        overlayPosition: true,
	        boxed: true,
	        parentId: argId,
	        id: argId,
	        appendNode: $(argId).up().up()
	    }
	    
	    $super(argWidget, options);
	    var wc = this;
        // Setup control panel
        
        var cp = new Element('div');
        cp.className = 'photoSelectorControl';
        
        closeButton = new Element('span');
        closeButton.className = 'buttonClose';
        closeButton.id = this.widgetBoxId + '_closeButton';
        closeButton.observe('click', function()
        {
            wc.hide();
        });
        cp.appendChild(closeButton);
        
        searchButton = new Element('span');
        searchButton.className = 'buttonSearch';
        searchButton.id = this.widgetBoxId + '_searchButton';
        searchButton.observe('click', function()
        {
            wc.loadSearchScreen();
        });
        cp.appendChild(searchButton); 
        
        loading = new Element('span');
        loading.className = 'loading';
        loading.id = this.widgetBoxId + '_loading';
        
        cp.appendChild(loading);
        loading.hide();
        
       
        
        // Setup box
        var cb = new Element('div');
        cb.className = 'photoSelectorContainer';
        cb.id = this.parentId + '_photoSelectorContainer';
        
        $(this.widgetBoxId).appendChild(cp);
        $(this.widgetBoxId).appendChild(cb);	    
	    
	    
	    
	    $(argId).observe('click', function(argEvent)
	    {
           wc.displayImageInfo();	       
	       wc.show();
	       argEvent.stop();
	    });
	},
	
	displayImageInfo: function(argId)
	{
	   if (argId)
	   {
	       photoId = argId;
	   } else {
	       photoId = this.parentId.split('-')[2];
	   }
	   
	   var wc = this;
	   
	   this.startLoading();
	   
	   new Ajax.Updater(this.parentId + '_photoSelectorContainer', wcmSiteURL + 'ajax/widgets/image-fetchSingle.php', {
	       parameters: {
	           photoId: photoId,
	           widgetId : wc.widget.contentId,
	           controlId: wc.parentId
	       },
	       onSuccess: function()
	       {
	           wc.endLoading();
	       },
	       evalScripts: true
	   });
    },
    
	
	searchImages: function()
	{
	    var results = 'imageResults_' + this.parentId;
	    var searchField = 'fulltext_' + this.parentId;
	    var fulltext = $(searchField).value;
	    this.startLoading();
	    
	    var wc = this;
	    
	    new Ajax.Updater(results, wcmSiteURL + 'ajax/widgets/image-searchResults.php', {
	        parameters: {
	            fulltext: fulltext,
	            controlId: wc.parentId,
	            widgetId: wc.widget.contentId
	        },
	        onSuccess: function()
	        {
	           wc.endLoading();
	        },
	        evalScripts: true
	        
	    });
	    return false;
	},  
	
	loadSearchScreen: function()
	{
	   var wc = this;
	   
	   this.startLoading();
	   
	   new Ajax.Updater(wc.parentId + '_photoSelectorContainer', wcmSiteURL + 'ajax/widgets/image-search.php', {
	       parameters: {
	           controlId: wc.parentId,
	           widgetId: wc.widget.contentId
	       },
	       onSuccess: function()
	       {
	           wc.endLoading();
	       },
	       evalScripts: true
	   });
	},
	
	useImage: function(argId, argSrc)
	{
	   if ($(this.parentId).hasClassName('big_pic'))
	   {
	   
	      rexp = /\-vignette/;
	      
	   	src = argSrc.replace(rexp, '');
	   } else {
	    src = argSrc;
	   }
	   $(this.parentId).src = src;
	   var articleId = this.parentId.split('-')[1];
	   this.widget.addChange(this.parentId, argId);
	   this.parentId = 'savePhoto-' + articleId + '-' + argId;
	   this.hide();
    },
	
	startLoading: function()
	{
	   // $(this.parentId + '_photoSelectorContainer').innerHTML = '';
	   $(this.widgetBoxId + '_loading').show();
	},
	
	endLoading: function()
	{
	   $(this.widgetBoxId + '_loading').hide();
	}
});

