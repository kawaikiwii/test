    <!-- BEGIN COMMENT: {$comment.id} -->
    <a name="comment{$comment.id}"></a>
        <div id="comment-{$comment.id}">
    {if $nested && $nested == 1}
        <div class="block indent {if $comment.workflowState eq 'suspicious'}suspicious{/if}" id="comment-{$comment.id}-block">
    {else}
        <div class="block {if $comment.workflowSate eq 'suspicious'}suspicious{/if}" id="comment-{$comment.id}-block">
        {/if}
            <div class="handle">
                <div class="floatright" id="comment-{$comment.id}-slideButton">
                    {if $comment.workflowState eq 'suspicious'}
                    <a href="#" onclick="wcmComment.slideComment('{$comment.id}'); return false" class="button2Maximize" id="comment-{$comment.id}-slideButtonTag"></a>
                    {/if}
                </div>
                <div class="commentTitle" id="comment-{$comment.id}-titleEl">{$comment.title}</div>
                <div class="commentTitleNext" id="comment-{$comment.id}-sentiment">{$comment|@wcm:sentiment}</div>
                <div class="clear"></div>
            </div>
            {if $comment.workflowState eq 'suspicious'}
            <div id="comment-{$comment.id}-body" style="display: none">
            {else}
            <div id="comment-{$comment.id}-body">
            {/if}
                <div class="date">
                    By <strong>{$comment.nickname}</strong> on {$comment.createdAt}
                </div>
                <div class="content">
                    
                    <div id="comment-{$comment.id}-text">{$comment.text}</div>
                    
                    <div class="line5"></div>
                    <a href="#" onclick="wcmComment.reply({$comment.id}); return false;" class="buttonReply"></a>
                    <div class="space5">&nbsp;</div>
                    <a href="#" onclick="wcmComment.flag({$comment.id}); return false;" class="buttonFlag"></a>
                    <div class="clear"></div>
                    
                    <!-- BEGIN CHILD REPLY BOX TO COMMENT ID {$comment.id} -->
                    <div id="comment-{$comment.id}-childPreviewBox" style="display: none">
                    
                        <div class="block indent">
                            <div class="handle">

                                <div class="commentTitle" id="comment-{$comment.id}-previewTitle"></div>
                                <div class="commentTitleNext" id="comment-{$comment.id}-previewSentiment"></div>
                                <div class="clear"></div>
                            </div>
                            <div id="comment-{$comment.id}-body">
                                <div class="date" id="comment-{$comment.id}-previewDate">
                                    
                                </div>
                                <div class="content">
                                    
                                    <div id="comment-{$comment.id}-previewText">{$comment.text}</div>
                                    
                                </div>
                                
                                <div class="line10"></div>
                                <div id="comment-{$comment.id}-previewCommentButtons">
                                <a href="#" onclick="wcmComment.continueEdit({$comment.id}); return false;" class="buttonContinue"></a>
                                <div class="space5">&nbsp;</div>
                                <a href="#" onclick="wcmComment.save({$comment.id}); return false;" class="buttonSubmit"></a>
                                <div class="space5">&nbsp;</div>
                                <a href="#" onclick="wcmComment.cancelPreviewReply({$comment.id}); return false;" class="buttonCancel"></a>
                                </div>
                                <div class="clear"></div>                               
                                
                            </div>
                        </div>
                    </div>
                
                    <!-- BEGIN REPLY TO COMMENT ID {$comment.id} -->
                    <div id="comment-{$comment.id}-replyForm" style="display: none;">
                    <form method="post" action="?" id="comment-{$comment.id}-replyFormEl" name="submitContribution">
                        <div class="block indent">
                            <div class="handle">
                                <input type="hidden" value="{$article.id}" name="articleId"/>
                                <input type="hidden" value="save" name="command" />
                                <input type="hidden" value="contribution/control" name="ajaxHandler" />
                                <input type="hidden" value="{$comment.id}" name="parentId" />
                                <label for="nickname">Subject</label>
                                <input type="text" class="comment_input" value="" id="comment-{$comment.id}-inputTitle" name="title" size="35"/><br/>
                            </div>
                            <div class="content">
                                <label for="text">Comment</label>
                                <textarea class="comment_input" id="comment-{$comment.id}-inputText" cols="35" rows="5" id="text" name="text"></textarea><br/>
                                <label>&nbsp;</label><small>Comments can contain the &lt;b&gt;, &lt;u&gt;, &lt;i&gt;, &lt;p&gt;, &lt;br&gt; and &lt;blockquote&gt; HTML elements.</small>
                                <div class="line5"></div>
                                <label for="nickname">Alias</label>
                                <input type="text" class="comment_input" id="comment-{$comment.id}-inputNickname" name="nickname" size="35"/><br/>
                                <label for="email">Email</label>
                                <input type="text" class="comment_input" id="comment-{$comment.id}-inputEmail" name="email" size="35"/><br/>
                                <label>&nbsp;</label><small>Email address won't be used for spam.</small>
                                <div class="clear"></div>
                                <label>&nbsp;</label>
                                <p class="footnote">
                                    * Nstein reserves the right to edit comments submitted on this site.<br/>
                                    * Comments submitted on this site can aslo be published on other Nstein sites.
                                </p>
                                <label>&nbsp;</label>
                                <a href="#" onclick="wcmComment.preview({$comment.id}); return false" class="buttonPreview"></a>
                                <div class="space5">&nbsp;</div>
                                <a href="#" class="buttonQuote" onclick="wcmComment.quoteReply({$comment.id}); return false;"></a>
                                <div class="space5">&nbsp;</div>
                                <a href="#" onclick="wcmComment.cancelReply({$comment.id}); return false" class="buttonCancel"></a>
                            </div>
                            <div class="line10"></div>
                        </div>
                    </form>
                    <div class="clear"></div>
                    </div>
                    {foreach item=child from=$comment.comments}
                        {include file="demo/blocks/ugc/comment.tpl" comment=$child nested=1}
                    {/foreach}
                </div>
            </div>
        </div>
        </div>
        <!--  END COMMENT: {$comment.id} -->