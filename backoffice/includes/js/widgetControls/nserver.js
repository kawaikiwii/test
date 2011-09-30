wcmConceptsControl = Class.create(wcmWidgetControl, {
    
    initialize: function($super, argWidget, argId)
    {
        var options = {
            width: 20,
            height: 20,
            overlayPosition: true,
            parentId: argId,
            id: argId + '_conceptsControl',
            boxed: true
        }
        
        $super(argWidget, options);
        
        $(this.widgetBoxId).innerHTML = '<div id="' + this.widgetBoxId + '_refresh" class="nServer_refresh"></div>';
        var wc = this;
        
        var rButton = this.widgetBoxId + '_refresh';
        $(rButton).observe('click', function(argEvent)
        {
            this.setStyle({
                backgroundImage: 'url("img/smallSpinner.gif")'
            });
            
            new Ajax.Request('nserver.php?n=concepts', {
                parameters: {
                    text: $(wc.parentId).innerHTML
                },
                onSuccess: function(argTransport)
                {
                    var results = new Hash(argTransport.responseText.evalJSON());
                    
                    tags = new Element('div');
                    tags.setStyle({
                        font: '16px arial, sans-serif'
                    });
                    
                    results.each(function(s)
                    {
                        scores = new Hash(s.value);
                        tags.innerHTML += '<span style="font-size: ' + scores.get('relevancy') + '%">' + s.key + '</span>';
                    });
                    
                    alert(tags.innerHTML);
                    
                    $('whatever').appendChild(tags);
                }
            });
        });
                
                        
        this.show();
     },
     
     rem: function()
     {
        alert('concepts control not removable yet');
     }
});
            

wcmSentimentControl = Class.create(wcmWidgetControl, {
    
    initialize: function($super, argWidget, argId)
    {
        var options = {
        
            width: 20,
            height: 60,
            top: $(argId).offsetTop,
            left: $(argId).offsetLeft - 20,
            parentId: argId,
            id: argId + '_sentimentControl',
            boxed: true
        }    
        $super(argWidget, options);
        
        $(this.widgetBoxId).innerHTML = '<div id="' + this.widgetBoxId + '_thumbs" class="nServer_thumbs"></div>';
        $(this.widgetBoxId).innerHTML += '<div id="' + this.widgetBoxId + '_refresh" class="nServer_refresh"></div>';
        
        this.show();
        
        var wc = this;
        
        $(this.widgetBoxId + '_refresh').observe('click', function(argEvent)
        {
            this.setStyle({
                backgroundImage: 'url("img/smallSpinner.gif")'
            });
            
            new Ajax.Request('nserver.php?n=sentiment', {
                parameters: {
                    text: $(wc.parentId).innerHTML
                },
                onSuccess: function(argTransport)
                {
                    var score = argTransport.responseText;
                    
                    if (score <= -10)
                    {
                        $(wc.widgetBoxId + '_thumbs').setStyle({
                            backgroundImage: 'url("img/thumb_down.png")'
                        });
                    } else if (score >= 10) {
                        $(wc.widgetBoxId + '_thumbs').setStyle({
                            backgroundImage: 'url("img/thumb_up.png")'
                        });
                    } else {
                        $(wc.widgetBoxId + '_thumbs').setStyle({
                            backgroundImage: 'url("img/question.png")'
                        });
                    }
                    
                    $(wc.widgetBoxId + '_refresh').setStyle({
                        backgroundImage: 'url("img/refresh.png")'
                    });
                }
            }); 
        });
        
    },
    
    rem: function()
    {
        alert('sentimentControl not removable!');
    }
});    