

{if $article.contributionState != 'none'}

<a name="comments"></a>

<div id="comments">
        <h4>Discussion</h4>
        <div id="comments_list"></div>
        <p class="footnote">
            * Nstein reserves the right to edit comments submitted on this site.<br />
            * Comments submitted on this site can aslo be published on other Nstein sites.
        </p>
        <div class="linedotted"></div>
        	<a href="#rootReply" onclick="Effect.SlideDown('comment-root-replyForm',{ldelim}duration:0.2{rdelim});" class="buttonComment"></a>
        <div class="clear"></div>
        <br />

{foreach item=comment from=$article.comments}
{include file="demo/blocks/ugc/comment.tpl" comment=$comment article=$article nested=0}
{/foreach}
        

        
        <!-- ROOT COMMENT BUTTON -->
        <div class="linedotted"></div>
        <a href="#" onclick="Effect.SlideDown('comment-root-replyForm',{ldelim}duration:0.2{rdelim}); return false;" class="buttonComment"></a>
        <div class="clear"></div>
        <br />
        
        <!-- ROOT COMMENT FORM  -->
        <div id="comment-root-replyForm" style="display: none;">
        <form method="post" action="?" id="comment-root-replyFormEl" name="submitContribution">
            <div class="block">
                <div class="handle">
                    <input type="hidden" value="{$article.id}" name="articleId"/>
                    <input type="hidden" value="save" name="command" />
                    <input type="hidden" value="contribution/control" name="ajaxHandler" />
                    <input type="hidden" value="root" name="parentId" />
                    <label for="nickname">Subject</label>
                    <input type="text" class="comment_input" id="comment-root-inputTitle" name="title" size="35"/><br/>
                </div>
                <div class="content">
                    <label for="text">Comment</label>
                        <textarea class="comment_input" cols="35" rows="5" id="comment-root-inputText" name="text"></textarea><br/>
                        
                    <label>&nbsp;</label> <small>Comments can contain the &lt;b&gt;, &lt;u&gt;, &lt;i&gt;, &lt;p&gt;, &lt;br&gt; and &lt;blockquote&gt; HTML elements.</small>
                    
                    <div class="line5"></div>
                    
                    <label for="nickname">Alias</label>
                        <input type="text" class="comment_input" id="comment-root-inputNickname" name="nickname" size="35"/><br/>
                        
                    <label for="email">Email</label>
                        <input type="text" class="comment_input" id="comment-root-inputEmail" name="email" size="35"/><br/>
                        
                    <label>&nbsp;</label><small>Email address won't be used for spam.</small>
                    
                    <div class="clear"></div>
                    <label>&nbsp;</label>
                    <div class="footnote">
                        * Nstein reserves the right to edit comments submitted on this site.<br/>
                        * Comments submitted on this site can aslo be published on other Nstein sites.
                    </div>
                    
                    <a href="#" onclick="wcmComment.preview('root'); return false;" class="buttonPreview"></a>
                    <div class="space5">&nbsp;</div>
                    <a href="#" onclick="wcmComment.cancelReply('root'); return false;" class="buttonCancel"></a>
                </div>
                <div class="line10"></div>
            </div>
        </form>
        </div>
        <a name="rootReply"></a>
        
        <!-- ROOT COMMENT PREVIEW -->
        <div id="comment-root-childPreviewBox" style="display: none;">
        <div class="block">
            <div class="handle">
                <div class="commentTitle" id="comment-root-previewTitle">This is sad...</div>
                <div class="commentTitleNext" id="comment-root-previewSentiment">Insightful</div>
                <div class="clear"></div>
            </div>
            <div class="date" id="comment-root-previewDate">
                By <strong>Gary who</strong> on Tuesday July 08, @02:20PM
            </div>
            <div class="content">
                <div id="comment-root-previewText"><p>This event is so sad. I will pray for Indonesian people...</p></div>
                <div class="line10"></div>
                <div id="comment-root-previewCommentButtons">
                <a href="#" onclick="wcmComment.continueEdit('root'); return false;" class="buttonContinue"></a>
                <div class="space5">&nbsp;</div>
                <a href="#" onclick="wcmComment.save('root'); return false" class="buttonSubmit"></a>
                <div class="space5">&nbsp;</div>
                <a href="#" onclick="wcmComment.cancelPreviewReply('root'); return false;" class="buttonCancel"></a>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        </div>
</div>
{/if}