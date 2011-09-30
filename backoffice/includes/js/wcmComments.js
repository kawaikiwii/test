var wcmComment = {
    
    openedPreviews: new Hash(),
    
    sanitize: function(argText)
    {
        // Allowed tags: <p>, <b>, <u>, <i>, <blockquote>, <br />
        // Formats:
        // <p>tlsdkhsdg</p>
        // sldkghsdg<br />
        // sdlgkhsdgl<br>
        // TODO This looks like very broken regexp
        newText = argText.stripScripts().replace(/(<([^\b(p|lockquot|u|i|b)\b^>]+)>)/igm,"");
        newText = newText.replace(/\n/,'<br />');
        return newText;
    },
    
    quoteReply: function(argReplyId)
    {
    	quote = '<blockquote>' + $('comment-' + argReplyId + '-text').innerHTML + '</blockquote>';
    	quote += "\n";
    	$('comment-' + argReplyId + '-inputText').value = quote + $('comment-' + argReplyId + '-inputText').value;
    	$('comment-'+argReplyId+'-inputText').focus();
    },
    
    reply: function(argReplyId)
    {
    	Effect.SlideDown('comment-' + argReplyId + '-replyForm', {duration: 0.2});

		var subject = $('comment-' + argReplyId + '-titleEl').innerHTML;
		if (!subject.startsWith('Re:')) subject = 'Re: ' + subject;
		$('comment-'+argReplyId+'-inputTitle').value = subject;
		$('comment-'+argReplyId+'-inputTitle').focus();
    },
    
    cancelReply: function(argReplyId)
    {
    	Effect.SlideUp('comment-'+argReplyId+'-replyForm', {duration: 0.2});
    	wcmComment.resetReplyForm(argReplyId);
    },
    
    cancelPreviewReply: function(argReplyId)
    {
    	$('comment-'+argReplyId+'-childPreviewBox').fade({duration: 0.2});
    	wcmComment.resetReplyForm(argReplyId);
    },
    
    continueEdit: function(argReplyId)
    {
    	$('comment-'+argReplyId+'-childPreviewBox').hide();
    	$('comment-'+argReplyId+'-replyForm').show();
    },
    
    preview: function(argReplyId)
    {
    	var cTitle = "comment-"+argReplyId+"-previewTitle";
    	var cDate = "comment-"+argReplyId+"-previewDate";
    	var cText = "comment-"+argReplyId+"-previewText";
    	
    	var iTitle = "comment-"+argReplyId+"-inputTitle";
    	var iNick = "comment-"+argReplyId+"-inputNickname";
    	var iText = "comment-"+argReplyId+"-inputText";
    	
    	var d = new Date();
    	
    	var commentId = argReplyId;
    	
    	$(cTitle).innerHTML = $(iTitle).value;
    	$(cDate).innerHTML = "By <strong>" + $(iNick).value + "</strong> on " + d.toString();
    	$(cText).innerHTML = $(iText).value.replace("/\n/","<br />");
    	$(cText).innerHTML = $(cText).innerHTML.replace("/\r/","<br />");
    	
    	$('comment-' + argReplyId + '-childPreviewBox').appear({duration: 0.2});
    	$('comment-' + argReplyId + '-replyForm').hide();
    	
    	wcmBasicSemantics.sentiment($(iText).value.stripTags() + $(iTitle).value.stripTags(), function(argTone, argSubjectivity)
    	{
    	   wcmComment.updateSemantics(commentId, argTone, argSubjectivity);
    	});
    },
    
    makeSlideButton: function(argCommentId, initialState)
    {
        if ($('comment-' + argCommentId + '-slideButton').down() == undefined)
        {
	        btn = new Element('a');
	        btn.href = '#';
	        btn.id = 'comment-' + argCommentId + '-slideButtonTag';
	        var classname = 'button2Minimize';
	        if (initialState == 'min') classname = 'button2Maximize';
	        if (initialState == 'max') classname = 'button2Minimize';
	        btn.addClassName(classname);
	        btn.observe('click', function(argEvt)
	        {
	            wcmComment.slideComment(argCommentId);
	            argEvt.stop();
	        });
	        
	        $('comment-' + argCommentId + '-slideButton').appendChild(btn);
	    }
	},
        
        
    
    updateSemantics: function(argCommentId, argTone, argSubjectivity)
    {
        // How to set this up
        // Troll: Subjectivity is 75 or higher - Tone is -50 or lower
        // Insightful: Subjectivity is 74 to 50 - Tone is -25 to +25
        // Informative: Subjectivity is 49 to 25 - Tone is -25 to +25
        // Fact: Subjectivity is 25 or lower - Tone is -10 to +10
        // Positive: Subjectivity is 75 or higher - Tone is +25 or higher
        // Negative: Subjectivity is 75 or higher - Tone is 0 to -25
        
        var t = argTone;
        var s = argSubjectivity;
        var sentiment = 'Neutral';
        
        if (t < -30 && s >= 75) sentiment = 'Troll';
        if ((t < 25 && t > -25) && (s < 74 && s > 50)) sentiment = 'Insightful';
        if ((t < 25 && t > -25) && (s < 49 && s > 25)) sentiment = 'Informative';
        if ((t < 10 && t > -10) && (s < 25)) sentiment = 'Fact';
        if ((t > 25) && (s > 75)) sentiment = 'Positive';
        if ((t <= 0 && t > -25) && (s > 75)) sentiment = 'Negative';
        
        $('comment-' + argCommentId + '-previewSentiment').innerHTML = sentiment;
        
        // Troll: Subjectivity is opinion, tone is negative
        // Insightful: 
    },
    
    resetReplyForm: function(argReplyId)
    {
    	$('comment-'+argReplyId+'-inputNickname').value = '';
    	$('comment-'+argReplyId+'-inputText').value = '';
    	$('comment-'+argReplyId+'-inputTitle').value = '';
    	$('comment-'+argReplyId+'-inputEmail').value = '';
    },
    
    makeReplyControl: function(argReplyId)
    {
        html = '<span onclick="wcmComment.reply(\'' + argReplyId + '\', false)">Reply</span> <span onclick="wcmComment.reply(\'' + argReplyId + '\', true)">Reply & Quote</span>';
        return html;
    },
    
    flag: function(argCommentId)
    {
        var url = wcmSiteURL + 'ajax/controller.php';
        
        new Ajax.Request(url, {
            parameters: {
                id: argCommentId,
                command: 'flag',
                ajaxHandler: 'contribution/control'
            },
            onSuccess: function(argTransport)
            {
                if (argTransport.responseText > 0)
                {
                    wcmComment.makeSlideButton(argCommentId);
                    wcmComment.slideComment(argCommentId);
                    $('comment-'+argCommentId+'-block').addClassName('suspicious');
                } else {
                    alert('Error flagging');
                }
            }
        });
    },
    
    slideComment: function(argCommentId)
    {
        if ($('comment-'+argCommentId + '-slideButtonTag') != undefined)
        {
            bt = $('comment-'+argCommentId + '-slideButtonTag');
            if (bt.className == 'button2Maximize')
            {
                Effect.SlideDown('comment-' + argCommentId + '-body', {duration: 1.0});
                bt.className = 'button2Minimize';
            } else {
                Effect.SlideUp('comment-' + argCommentId + '-body', {duration: 1.0});
                bt.className = 'button2Maximize';
            }
        } else {
            alert('Trying to slide a comment that is not slidable');
        }
    },
    
    save: function(argParentId)
    {
    	var url = wcmSiteURL + 'ajax/controller.php';
        var params = $('comment-'+argParentId+'-replyFormEl').serialize(true);
        
        params.text = wcmComment.sanitize(params.text);
        
        new Ajax.Request(url, {
            parameters: params,
            onSuccess: function(argTransport)
            {
                if (argTransport.responseText == 0)
                {
                    saved = new Element('div');
                    saved.addClassName('savedMsg');
                    saved.innerHTML = '<p>Your comment has been saved. It will appear on the site shortly.</p>';
                    saved.hide();
                    
                    $('comment-' + argParentId + '-previewCommentButtons').update().appendChild(saved);
                    saved.appear();
                } else {
                    alert('Comment not saved: ' + argTransport.responseText);
                }
            }
        });
    }
}
