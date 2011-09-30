wcmCommentControl = Class.create(wcmWidgetControl, {

    initialize: function($super, argWidget, argId)
    {
        var options = {
            width: 20,
            overlayHeight: true,
            overlayTop: true,
            left: $(argId).offsetLeft - 25,
            boxed: true,
            parentId: argId,
            id: argId
        };
        
        $super(argWidget, options);
        
        var wc = this;
        
        // Create sentiment tag
        span = new Element('div');
        span.id = this.parentId + '-sentiment';
        span.addClassName('sentiment-neutral');
        span.innerHTML = 'nN';
        $(this.widgetBoxId).appendChild(span);
        
        // Create approve tag
        span = new Element('div');
        span.id = this.parentId + '-approve';
        span.addClassName('buttonApprove');
        span.innerHTML = 'a';
        span.observe('click', function()
        {
            wc.approve();
        });
        $(this.widgetBoxId).appendChild(span);
        
        // Create reject tag
        span = new Element('div');
        span.id = this.parentId + '-reject';
        span.addClassName('buttonReject');
        span.innerHTML = 'r';
        span.observe('click', function()
        {
            wc.reject();
        });
        $(this.widgetBoxId).appendChild(span);
        
        // create flag tag
        span = new Element('div');
        span.id = this.parentId + '-flag';
        span.addClassName('buttonFlag');
        span.observe('click', function()
        {
            wc.flag();
        });
        $(this.widgetBoxId).appendChild(span);
        
        this.fetchSentiment();
        
        this.show();
    },

    fetchSentiment: function()
    {
        if (Math.round(Math.random()) == 1)
        {
            $(this.parentId + '-sentiment').classname = 'sentiment-pos';
            $(this.parentId + '-sentiment').innerHTML = 'nPos';
        } else {
            $(this.parentId + '-sentiment').classname = 'sentiment-neg';
            $(this.parentId + '-sentiment').innerHTML = 'nNeg';
        }
    },
    
    flag: function()
    {
        alert('flagged');
    },
    
    approve: function()
    {
        alert('approve');
    },
    
    reject: function()
    {
        alert('reject');
    }
});       