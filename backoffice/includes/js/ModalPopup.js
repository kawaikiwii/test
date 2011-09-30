Position.getWindowSize = function(w) {
    var width, height;
    w = w ? w : window;
    width = w.innerWidth || (w.document.documentElement.clientWidth || w.document.body.clientWidth);
    height = w.innerHeight || (w.document.documentElement.clientHeight || w.document.body.clientHeight);

    return [width, height];
}

Position.center = function(element, parent) {
        var w, h, pw, ph;
        var d = Element.getDimensions(element);
        w = d.width;
        h = d.height;
        Position.prepare();
        if (!parent) {
                var ws = Position.getWindowSize();
                pw = ws[0];
                ph = ws[1];
        } else {
                pw = parent.offsetWidth;
                ph = parent.offsetHeight;
        }

        offset = element.cumulativeScrollOffset();
        
        element.style.top = offset.top + (ph/2) - (h/2) -  Position.deltaY + "px";
        element.style.left = offset.left + (pw/2) - (w/2) -  Position.deltaX + "px";
}

/**
 * A static class used to display modal dialogs in WCM
 */
var wcmModal = {
    /**
     * Last response selected by the user for
     * button-based dialogs (YesNoCancel, OkCancel, ...)
     */
    response: null,

    /**
     * Show the modal dialog
     *
     * @parameter string title Dialog title
     * @parameter string html Inner html content of the dialog
     */
    show: function(title, html) {
        this.response = null;

        $('modalTitle').innerHTML = title;
        $('modalDialog').innerHTML = html;
        $('modalWindow').style.display = $('modalBackground').style.display = 'block';

        Position.center($('modalBackground'));
        Position.center($('modalWindow'));
    },

    /**
     * Hide the previously shown modal dialog
     */
    hide: function() {
        $('modalWindow').style.display = $('modalBackground').style.display = 'none';
    },

    /**
     * Show a modal dialog popupater by an AJAX call
     *
     * @param string title Dialog title
     * @param string url URL used for the AJAX call (will return the inner html content)
     * @param array parameters (parameters to post with the AJAX call)
     */
    showAjax: function(title, url, parameters) {
        this.show(title, '<div class="wait"></div>');
        new Ajax.Updater(   'modalDialog', url,
                            {   asynchronous: false,
                                evalScript:true,
                                parameters: parameters }
                        );
    },
    
     /**
     * Show a modal dialog popupater by an AJAX call
     *
     * @param string title Dialog title
     * @param string url URL used for the AJAX call (will return the inner html content)
     * @param array parameters (parameters to post with the AJAX call)
     * @param function callback Callback to invoked once user has clicked on a button
     * @param buttons, array of buttons
     *
     * The callback will receive 'YES' or 'NO' as a response
     */
    showAjaxButtons: function(title, url, parameters, callback, buttons) {
     
     	//buttons = new Array("ok","save");
     	this.show(title, '<div class="wait"></div>');
       	this.showDialog(title, 	
        	new Ajax.Updater('modalDialog', url,
           		{asynchronous: false,
             	evalScript:true,
             	parameters: parameters }), 
    			callback, buttons); 
    },
   
   	/**
     * Get the button code
     *
     * @param string name the button constant
     */
    getButtonByName : function(name) {
     	var codeButton;
   		switch(name){
            case "OK":
                codeButton = {css: 'ok', caption: $I18N.OK, response: 'OK'};
                break;
            
            case "YES":
                codeButton = {css: 'yes', caption: $I18N.YES, response: 'YES'};
                break;
            
            case "NO":
                codeButton = {css: 'no', caption: $I18N.NO, response: 'NO'};
                break;
            
			case "SAVE":
                codeButton = {css: 'save', caption: $I18N.SAVE, response: 'SAVE'};
                break;
            
            case "REPLACE":
            	 codeButton = {css: 'replace', caption: $I18N.REPLACE, response:'REPLACE'};
            	 break;
          		
          	case "ADD":
          		codeButton = {css: 'add', caption: $I18N.ADD, response: 'ADD'};
          		break;

            default:
            case "CANCEL":
                codeButton =  {css: 'cancel', caption: $I18N.CANCEL, response: 'CANCEL'};
          		break;
		}
		return codeButton;
    },
   
    
    /**
     * Display a basic Yes/No dialog
     *
     * @param string title Title of the dialog
     * @param mixed message Inner html of the dialog or on object with {url, parameters} used for ajax callback
     * @param function callback Callback to invoked once user has clicked on a button
     *
     * The callback will receive 'YES' or 'NO' as a response
     */
    showYesNo : function(title, message, callback) {   
        this.showDialog(title, message, callback, [
            { css: 'yes', caption: $I18N.YES, response: 'YES' },
            { css: 'no', caption: $I18N.NO, response: 'NO' }
        ]);
    },
    
    /**
     * Display a basic Yes/No/Cancel dialog
     *
     * @param string title Title of the dialog
     * @param mixed message Inner html of the dialog or on object with {url, parameters} used for ajax callback
     * @param function callback Callback to invoked once user has clicked on a button
     *
     * The callback will receive 'YES', 'NO' or 'CANCEL' as a response
     */
    showYesNoCancel : function(title, message, callback) {
        this.showDialog(title, message, callback, [
            { css: 'yes', caption: $I18N.YES, response: 'YES' },
            { css: 'no', caption: $I18N.NO, response:'NO' },
            { css: 'cancel', caption: $I18N.CANCEL, response: 'CANCEL' }
        ]);
    },
    
    /**
     * Display a basic Add/Replace/Cancel dialog
     *
     * @param string title Title of the dialog
     * @param mixed message Inner html of the dialog or on object with {url, parameters} used for ajax callback
     * @param function callback Callback to invoked once user has clicked on a button
     *
     * The callback will receive 'ADD', 'REPLACE' or 'CANCEL' as a response
     */
    showAddReplaceCancel : function(title, message, callback) {
        this.showDialog(title, message, callback, [
            { css: 'add', caption: $I18N.ADD, response: 'ADD' },
            { css: 'replace', caption: $I18N.REPLACE, response:'REPLACE' },
            { css: 'cancel', caption: $I18N.CANCEL, response: 'CANCEL' }
        ]);
    },

    /**
     * Display a basic Ok dialog
     *
     * @param string title Title of the dialog
     * @param mixed message Inner html of the dialog or on object with {url, parameters} used for ajax callback
     * @param function callback Callback to invoked once user has clicked on a button
     *
     * The callback will receive 'OK' as a response
     */
    showOk : function(title, message, callback) {
        this.showDialog(title, message, callback, [
            { css: 'ok', caption: $I18N.OK, response: 'OK' }
        ]);
    },

    /**
     * Display a basic Ok/Cancel dialog
     *
     * @param string title Title of the dialog
     * @param mixed message Inner html of the dialog or on object with {url, parameters} used for ajax callback
     * @param function callback Callback to invoked once user has clicked on a button
     *
     * The callback will receive 'OK' or 'CANCEL' as a response
     */
    showOkCancel : function(title, message, callback) {
        this.showDialog(title, message, callback, [
            { css: 'ok', caption: $I18N.OK, response: 'OK' },
            { css: 'cancel', caption: $I18N.CANCEL, response: 'CANCEL' }
        ]);
    },

    /**
     * Display a basic confirmation dialog (message with Yes/No buttons)
     *
     * @param string title Title of the dialog
     * @param mixed message Inner html of the dialog or on object with {url, parameters} used for ajax callback
     * @param function callback Callback to invoked once user has clicked on 'Ok' or 'Cancel'
     *
     * The callback will receive 'YES' or 'NO' as a response
     */
    confirm: function(title, message, callback) {
        var html = '<fieldset><ul><li>' + message + '</li></ul></fieldset>';
        this.showYesNo(title, html, callback);
    },
    
    /**
     * Display a basic prompt dialog to retrieve simple info from the user
     *
     * @param string title Title of the dialog
     * @param string label Label of the textarea (can be null)
     * @param string value Initial value of the textarea (can bell null)
     * @param function callback Callback to invoked once user has clicked on 'Ok' or 'Cancel'
     *
     * The callback receive either null (if cancel) or the answer typed by the user
     */
    prompt : function(title, label, value, callback) {
        var html = '<form id="modalForm">' +
                   '<fieldset>' +
                   '<ul><li><label>' + label + '</label>' +
                   '<textarea id="_modalFormValue" row="3">' + value + '</textarea></li></ul>' +
                   '</fieldset>' +
                   '</form>';
        this.showOkCancel(  title,
                            html,
                            function(response) {
                                if (Object.isFunction(callback)) {
                                    callback((response == 'OK') ? $F('_modalFormValue') : null); 
                                }
                            }.bind(this)
                         );
    },

    /**
     * Show a generic modal dialog with a button bar
     *
     * @param string title Title of the dialog
     * @param mixed message Inner html of the dialog or on object with {url, parameters} used for ajax callback
     * @param function callback Callback to invoke when a button is clicked
     * @param array buttons Array of button objects (css, caption and reponse are the 3 properties)
     *
     * The callback will receive the clicked button's 'response' property as parameter
     *
     * Example: showDialog('Answer if you dare',
     *                     'Do you like this stuff?', 
     *                     function(response) { alert(response); }, 
     *                     [
     *                         { caption:'Yeap!', response:'YES', css:'cssYesButton' },
     *                         { caption:'Not really', response:'NO', css:'cssNoButton' }
     *                     ]);
     *          => the callback will receive either 'YES' or 'NO' as parameter
     */
    showDialog : function(title, message, callback, buttons) {
        this.callback = callback;
		this.title = title;
		//keep the parameters in the object so it can be sent to the callback if needed
		this.message = message;
		
        // If there is no button an 'Ok' button will be added
        if (buttons == undefined || buttons == null) {
            buttons = [ { css: 'ok', caption: $I18N.OK, response: 'OK' } ];
        }

        // Build button bar
        var html =  '<ul class="toolbar">';
        buttons.each(function (button) {
                html += '<li><a class="' + button.css + '" href="#"';
                html += ' onclick="wcmModal.onDialogButton(\'' + button.response + '\');return false;"';
                html += '>' + button.caption + '</a></li>';
            });
            
        html += '</ul>';

        // is message a simple string or an object?
        if (Object.isString(message)) {
            // display message and toolbar
            this.show(title, message + html);
        } else {
            // waiting message
            var wait = '<div id="modalDialogAjax"><div class="wait">' + $I18N.LOADING + '</div></div>'
                     + '<ul class="toolbar"><li><a class="cancel" href="#"'
                     + ' onclick="wcmModal.onDialogButton(\'CANCEL\'); return false;"'
                     + '>' + $I18N.CANCEL + '</a></li></ul>';

            this.show(title, wait);

            // invoke the ajax updater
            new Ajax.Updater(   'modalDialog', message.url,
                                {   asynchronous: true,
                                    evalScripts:true,
                                    parameters: message.parameters,
                                    onComplete: function() {
                                        $('modalDialog').insert(html);
                                    }
                                }
                            );
        }
    },

    /**
     * Internal event handler called when a dialog button is clicked
     *
     * @param string response Value of the button's response
     */
    onDialogButton : function(response) {
        // save response and hide dialog
        this.response = response;
        this.hide();

        // invoke callback with response value
        if (Object.isFunction(this.callback))
            this.callback(response, this.message);
    }
}

// http://www.howtocreate.co.uk/tutorials/javascript/browserwindow
function GetSize() {
    var myWidth = 0, myHeight = 0;
    if( typeof( window.innerWidth ) == 'number' ) {
        //Non-IE
        myWidth = window.innerWidth;
        myHeight = window.innerHeight;
    } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
        //IE 6+ in 'standards compliant mode'
        myWidth = document.documentElement.clientWidth;
        myHeight = document.documentElement.clientHeight;
    } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
        //IE 4 compatible
        myWidth = document.body.clientWidth;
        myHeight = document.body.clientHeight;
    }
    return { 'Width':Number(myWidth), 'Height':Number(myHeight) };
}

var modalb;
function openmodal(title, width, height, html)
{
    // default value
    if (html == undefined) html = '';
    if (width == undefined) width = '400';
    if (height == undefined) height = '400';
    
    modalb = new ModalPopup(html, width, height);
    modalb.title(title);
    modalb.Show();

}
function closemodal()
{
    modalb.Hide();
}

/*****************************************************************
/* Link : http://luke.breuer.com/tutorial/javascript-modal-dialog.aspx
/* Description :
/* This script contains the base methods to manage a modal popup.
*****************************************************************/

/* NSTEIN TN - Modified For RelaxNews - 2009.03.13 - BEGIN */
/*
ModalPopup = function() {
    this.InnerHTML = '';
    this.WindowWidth = '400';
    this.WindowHeight = '400';
};
*/
ModalPopup = function(innerHTML, windowWidth, windowHeight) {
    this.InnerHTML = innerHTML;
    this.WindowWidth = windowWidth;
    this.WindowHeight = windowHeight;
};

/* NSTEIN TN - Modified For RelaxNews - 2009.03.13 - END */

ModalPopup.prototype.title = function(title)
{
    document.getElementById('modalTitle').innerHTML = title + '<span style="float:right; cursor:pointer" onClick="closemodal(); return false;">X</span>' ;
};

ModalPopup.prototype.Initialize = function(innerHTML, windowWidth, windowHeight) {
    this.InnerHTML = innerHTML;
    this.WindowWidth = windowWidth;
    this.WindowHeight = windowHeight;
};

ModalPopup.prototype.CreateModalPopup = function() {
    var body = document.getElementsByTagName('body')[0];
    
    var backgroundDiv = document.createElement('div');
    backgroundDiv.setAttribute('id', 'modalBackground');
    
    var modalDiv = document.createElement('div');
    modalDiv.setAttribute('id', 'modalWindow');
    this.SetWindowDimension(modalDiv, this.WindowWidth, this.WindowHeight);
    
    body.appendChild(backgroundDiv);
    body.appendChild(modalDiv);
       
    modalDiv.innerHTML = this.InnerHTML;
    
    this.SetupHandlers();
};

ModalPopup.prototype.RemoveSelectSpans = function()
{
    var selects = document.getElementsByTagName('select');
    
    for (var i = 0; i < selects.length; i++)
    {
        var select = selects[i];
        
        if (select.clientWidth == 0 || select.clientHeight == 0 || 
            select.nextSibling == null || select.nextSibling.className != 'selectReplacement')
        {
            continue;
        }
            
        select.parentNode.removeChild(select.nextSibling);
        select.style.display = select.cachedDisplay;
    }
};

ModalPopup.prototype.ReplaceSelectsWithSpans = function()
{
    var selects = document.getElementsByTagName('select');
    
    for (var i = 0; i < selects.length; i++)
    {
        var select = selects[i];
        
        if (select.clientWidth == 0 || select.clientHeight == 0 || 
            select.nextSibling == null || select.nextSibling.className == 'selectReplacement')
        {
            continue;
        }
            
        var span = document.createElement('span');
        
        // this would be "- 3", but for that appears to shift the block that contains the span 
        //   one pixel down; instead we tolerate the span being 1px shorter than the select
        span.style.height = (select.clientHeight - 4) + 'px';
        span.style.width = (select.clientWidth - 6) + 'px';
        span.style.display = 'inline-block';
        span.style.border = '1px solid rgb(200, 210, 230)';
        span.style.padding = '1px 0 0 4px';
        span.style.fontFamily = 'Arial';
        span.style.fontSize = 'smaller';
        span.style.position = 'relative';
        span.style.top = '1px';
        span.className = 'selectReplacement';
        
        span.innerHTML = select.options[select.selectedIndex].innerHTML + 
            '<img src="custom_drop.gif" alt="drop down" style="position: absolute; right: 1px; top: 1px;" />';
        
        select.cachedDisplay = select.style.display;
        select.style.display = 'none';
        select.parentNode.insertBefore(span, select.nextSibling);
    }
};

ModalPopup.prototype.OnWindowResize = function()
{
    // we only need to move the dialog based on scroll position if
    // we're using a browser that doesn't support position: fixed, like < IE 7
    //var scroll = GetScrollXY();
    var size = GetSize();
    var div = document.getElementById('modalWindow');
    
    div.style.left = Math.max((/*scroll.ScrollOffsetX + */(size.Width - div.offsetWidth) / 2), 0) + 'px';
/* NSTEIN TN - Modified For RelaxNews - 2009.12.18 - BEGIN */
    //div.style.top = Math.max((/*scroll.ScrollOffsetY + */(size.Height - div.offsetHeight) / 2), 0) + 'px';
    div.style.top = '150px';
/* NSTEIN TN - Modified For RelaxNews - 2009.12.18 - END */
};

ModalPopup.prototype.Show = function()
{
    var modalWindow = document.getElementById('modalWindow');
    var modalBackground = document.getElementById('modalBackground');
    
    modalWindow.style.display = modalBackground.style.display = 'block';

    // special < IE7 -only processing for windowed elements, like select    
    if (window.XMLHttpRequest == null)
    { this.ReplaceSelectsWithSpans(); }

/* NSTEIN TN - Modified For RelaxNews - 2009.03.13 - BEGIN */
    modalWindow.style.width = this.WindowWidth + 'px';
/* NSTEIN TN - Modified For RelaxNews - 2009.03.13 - END */

    // call once to center everything
    this.OnWindowResize();
/*  
    if (window.attachEvent)
        window.attachEvent('onresize', this.OnWindowResize);
    else if (window.addEventListener)
        window.addEventListener('resize', this.OnWindowResize, false);
    else
        window.onresize = this.OnWindowResize;
    
    // we won't bother with using javascript in CSS to take care
    //   keeping the window centered
    if (document.all)
        document.documentElement.onscroll = this.OnWindowResize;
        */
};

ModalPopup.prototype.Hide = function()
{   

    $('modalWindow').style.display = $('modalBackground').style.display = 'none';
    
    //modalWindow.style.display = modalBackground.style.display = 'none';
    // special IE-only processing for windowed elements, like select    
    
    /*
    if (document.all)
    { this.RemoveSelectSpans(); }
    
    if (window.detachEvent)
        window.detachEvent('onresize', this.OnWindowResize);
    else if (window.removeEventListener)
        window.removeEventListener('resize', this.OnWindowResize, false);
    else
        window.onresize = null;
        
    if (document.all)
        document.documentElement.onscroll = null;

    var body = document.getElementsByTagName('body')[0];
    var modalWindow = document.getElementById('modalWindow');
    var modalBackground = document.getElementById('modalBackground');
    
    body.removeChild(modalWindow);
    body.removeChild(modalBackground);*/
};

ModalPopup.prototype.SetWindowDimension = function(item, itemWidth, itemHeight) {
    var noPx = document.childNodes ? 'px' : 0;
    if( item.style ) { item = item.style; }
    item.height = itemHeight + noPx;
    item.width = itemWidth + noPx;
}