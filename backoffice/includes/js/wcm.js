/**
 * AjaxController is used to invoke a server-side php method and update a client html element
 * Requires "prototype.js"
 *
 * Usage: new AjaxController(divId, container, options)
 *
 * => container: an id string or a array with 3 ids (success:id, failure:id, progress:id)
 *               => the failure id is a div populated on failure or error.
 *                  if not specified, the failure id is equals to the success id.
 *               => the progress id is a div populated while asynchronously invoking the method
 *                  with a basic '<img src="img/wait.gif"/>' html content.
 *                  if not specified, the progress id is equals to the success id.
 *
 * => options: same as Ajax.Updater from prototype plus "progressMessage" to fill the container.progress.innerHTML
 *
 * Example:
 *          new AjaxController(
 *                   'TheMethod',
 *                   { success: 'myDiv', progress: 'waitingDiv' },
 *                   { parameters: {p1:1, p2:"text"}, progressMessage: 'executing <i>your</i> query...' });
 *
 */
AjaxController = Class.create();
Object.extend(Object.extend(AjaxController.prototype, Ajax.Updater.prototype), {
  initialize: function(method, container, options) {
    this.container = {
      success: (container.success || container),
      failure: (container.failure || (container.success ? null : container)),
      progress: (container.progress || container)
    }

    // Display progress message?
    if (this.container.progress)
    {
        if (progressContainer = $(this.container.progress))
        {
            this.oldProgressMessage = progressContainer.innerHTML;
            progressContainer.update(options.progressMessage || '<img src="img/wait.gif" alt=""/>');
        }
    }

    this.transport = Ajax.getTransport();

    // By default, this controller will eval scripts
    if (options.evalScripts == undefined)
        options.evalScripts = true;
        
    this.setOptions(options);

    var onComplete = this.options.onComplete || Prototype.emptyFunction;
    this.options.onComplete = (function(transport, param) {
      this.restoreProgress();
      this.updateContent();
      onComplete(transport, param);
    }).bind(this);

    var onFailure = this.options.onFailure || Prototype.emptyFunction;
    this.options.onFailure = (function(transport, param) {
      this.finalizeRequest();
      onFailure(transport, param);
    }).bind(this);

    // Invoke server side
    this.request('ajax/'+method+'.php');
  },

  restoreProgress: function() {
    // Restore progress message?
    if (!this.container.progress) return;

    if (progressContainer = $(this.container.progress))
    {
        if (this.success())
        {
            if (this.container.process != this.container.success)
                progressContainer.update(this.oldProgressMessage);
        }
        else
        {
            if (this.container.progress != this.container.failure)
                progressContainer.update(this.oldProgressMessage);
        }
    }
  }
});

// Refresh opener (and optionally close current window)
function refreshOpener(close)
{
    if (window.opener)
    {
        window.opener.location.reload();
        if (close)
        {
            window.opener.focus();
            window.close();
        }
    }
}

// Get window height
function windowHeight()
{
    if (window.innerHeight)
        return window.innerHeight;

    if (document.compatMode && document.compatMode.indexOf("CSS1") >= 0)
        return document.body.parentElement.clientHeight;

    if (document.body && document.body.clientHeight)
        return document.body.clientHeight;

    return 0;
}

// Get window width
function windowWidth()
{
    if (window.innerWidth)
        return window.innerWidth;

    if (document.compatMode && document.compatMode.indexOf("CSS1") >= 0)
        return document.body.parentElement.clientWidth;

    if (document.body && document.body.clientWidth)
        return document.body.clientWidth;

    return 0;
}

// Resize element
function resizeElement(id, w, h)
{
    var d = document.getElementById(id);
    if (d)
    {
        if (w != null)
        {
            if (w < 0) w = 0;
            d.style.width  = w + 'px';
        }
        
        if (h != null)
        {
            if (h < 0) h = 0;
            d.style.height = h + 'px';
        }
    }
}

// Resize element to fil all the window space
function fitToWindow(id)
{
    resizeElement(id, windowWidth(), windowHeight());
}

// Global variables
var isCSS, isW3C, isIE4, isNN4, isIE6CSS;

// Initialize upon load to let all browsers establish content objects
function initDHTMLAPI( )
{
    if (document.images)
    {
        isCSS = (document.body && document.body.style) ? true : false;
        isW3C = (isCSS && document.getElementById) ? true : false;
        isIE4 = (isCSS && document.all) ? true : false;
        isNN4 = (document.layers) ? true : false;
        isIE6CSS = (document.compatMode && document.compatMode.indexOf("CSS1") >= 0) ?  true : false;
    }
}

// Set event handler to initialize API
window.onload = initDHTMLAPI;
   
// Seek nested NN4 layer from string name
function seekLayer(doc, name)
{
    var theObj;
    for (var i = 0; i < doc.layers.length; i++)
    {
        if (doc.layers[i].name == name)
        {
            theObj = doc.layers[i];
            break;
        }
        // dive into nested layers if necessary
        if (doc.layers[i].document.layers.length > 0)
        {
            theObj = seekLayer(document.layers[i].document, name);
        }
    }
    return theObj;
}
   
// Convert object name string or object reference into a valid element object reference
function getRawObject(obj)
{
    var theObj;
    if (typeof obj == "string")
    {
        if (isW3C)
        {
            theObj = document.getElementById(obj);
        }
        else if (isIE4)
        {
            theObj = document.all(obj);
        }
        else if (isNN4)
        {
            theObj = seekLayer(document, obj);
        }
    }
    else
    {
        // pass through object reference
        theObj = obj;
    }
    return theObj;
}
   
// Convert object name string or object reference
// into a valid style (or NN4 layer) reference
function getObject(obj)
{
    var theObj = getRawObject(obj);
    if (theObj && isCSS)
    {
        theObj = theObj.style;
    }
    return theObj;
}
   
// Position an object at a specific pixel coordinate
function shiftTo(obj, x, y)
{
    var theObj = getObject(obj);
    if (theObj)
    {
        if (isCSS)
        {
            // equalize incorrect numeric value type
            var units = (typeof theObj.left == "string") ? "px" : 0;
            theObj.left = x + units;
            theObj.top = y + units;
        }
        else if (isNN4)
        {
            theObj.moveTo(x,y);
        }
    }
}
   
// Move an object by x and/or y pixels
function shiftBy(obj, deltaX, deltaY)
{
    var theObj = getObject(obj);
    if (theObj)
    {
        if (isCSS)
        {
            // equalize incorrect numeric value type
            var units = (typeof theObj.left == "string") ? "px" : 0;
            theObj.left = getObjectLeft(obj) + deltaX + units;
            theObj.top = getObjectTop(obj) + deltaY + units;
        }
        else if (isNN4)
        {
            theObj.moveBy(deltaX, deltaY);
        }
    }
}
   
// Set the z-order of an object
function setZIndex(obj, zOrder)
{
    var theObj = getObject(obj);
    if (theObj)
    {
        theObj.zIndex = zOrder;
    }
}
   
// Set the background color of an object
function setBGColor(obj, color)
{
    var theObj = getObject(obj);
    if (theObj)
    {
        if (isNN4)
        {
            theObj.bgColor = color;
        }
        else if (isCSS)
        {
            theObj.backgroundColor = color;
        }
    }
}
   
// Set the visibility of an object to visible
function show(obj)
{
    var theObj = getObject(obj);
    if (theObj)
    {
        if (theObj.visibility) theObj.visibility = "visible";
        if (theObj.style) theObj.style.display = '';
    }
}
   
// Set the visibility of an object to hidden
function hide(obj)
{
    var theObj = getObject(obj);
    if (theObj)
    {
        if (theObj.visibility) theObj.visibility = "hidden";
        if (theObj.style) theObj.style.display = 'none';
    }
}

// Swap object visibility
// => If a picture is given the picture source will swap from "show" to "hide"
// => By default, showSrc = "img/show.gif", hideSrc="img/hide.gif"
function swap(theObj, thePic, showSrc, hideSrc)
{
    theObj = $(theObj);
    thePic = $(thePic);

    if (!showSrc) showSrc = 'img/expand.gif';
    if (!hideSrc) hideSrc = 'img/collapse.gif';
    
    if (theObj)
    {
        if ((theObj.visibility && theObj.visibility == "hidden") || (theObj.style && theObj.style.display == 'none'))
        {
            if (theObj.visibility) theObj.visibility = "visible";
            if (theObj.style) theObj.style.display = '';
            if (thePic) thePic.src = hideSrc;
        }
        else
        {
            if (theObj.visibility) theObj.visibility = "hidden";
            if (theObj.style) theObj.style.display = 'none';
            if (thePic) thePic.src = showSrc;
        }
    }
}
   
// Retrieve the x coordinate of a positionable object
function getObjectLeft(obj)
{
    var elem = getRawObject(obj);
    var result = 0;
    if (document.defaultView)
    {
        var style = document.defaultView;
        var cssDecl = style.getComputedStyle(elem, "");
        result = cssDecl.getPropertyValue("left");
    }
    else if (elem.currentStyle)
    {
        result = elem.currentStyle.left;
    }
    else if (elem.style)
    {
        result = elem.style.left;
    }
    else if (isNN4)
    {
        result = elem.left;
    }
    return parseInt(result);
}
   
// Retrieve the y coordinate of a positionable object
function getObjectTop(obj)
{
    var elem = getRawObject(obj);
    var result = 0;
    if (document.defaultView)
    {
        var style = document.defaultView;
        var cssDecl = style.getComputedStyle(elem, "");
        result = cssDecl.getPropertyValue("top");
    }
    else if (elem.currentStyle)
    {
        result = elem.currentStyle.top;
    }
    else if (elem.style)
    {
        result = elem.style.top;
    }
    else if (isNN4)
    {
        result = elem.top;
    }
    return parseInt(result);
}
   
// Retrieve the rendered width of an element
function getObjectWidth(obj)
{
    var elem = getRawObject(obj);
    var result = 0;
    if (elem.offsetWidth)
    {
        result = elem.offsetWidth;
    }
    else if (elem.clip && elem.clip.width)
    {
        result = elem.clip.width;
    }
    else if (elem.style && elem.style.pixelWidth)
    {
        result = elem.style.pixelWidth;
    }
    return parseInt(result);
}
   
// Retrieve the rendered height of an element
function getObjectHeight(obj)
{
    var elem = getRawObject(obj);
    var result = 0;
    if (elem.offsetHeight)
    {
        result = elem.offsetHeight;
    }
    else if (elem.clip && elem.clip.height)
    {
        result = elem.clip.height;
    }
    else if (elem.style && elem.style.pixelHeight)
    {
        result = elem.style.pixelHeight;
    }
    return parseInt(result);
}
   
// Return the available content width space in browser window
function getInsideWindowWidth()
{
    if (window.innerWidth)
    {
        return window.innerWidth;
    }
    else if (isIE6CSS)
    {
        // measure the html element's clientWidth
        return document.body.parentElement.clientWidth;
    }
    else if (document.body && document.body.clientWidth)
    {
        return document.body.clientWidth;
    }
    return 0;
}
   
// Return the available content height space in browser window
function getInsideWindowHeight()
{
    if (window.innerHeight)
    {
        return window.innerHeight;
    }
    else if (isIE6CSS)
    {
        // measure the html element's clientHeight
        return document.body.parentElement.clientHeight;
    }
    else if (document.body && document.body.clientHeight)
    {
        return document.body.clientHeight;
    }
    return 0;
}

// Global holds reference to selected element
var selectedObj;
   
// Globals hold location of click relative to element
var offsetX, offsetY;
   
// Set global reference to element being engaged and dragged
function setSelectedElem(evt)
{
    var target = (evt.target) ? evt.target : evt.srcElement;
    var divID = (target.id && target.className == "draggable") ? target.id + "Div" : "";
    //var divID = (target.name && target.src) ? target.name + "Wrap" : "";
    if (divID) {
        if (document.layers) {
            selectedObj = document.layers[divID];
        } else if (document.all) {
            selectedObj = document.all(divID);
        } else if (document.getElementById) {
            selectedObj = document.getElementById(divID);
        }
        setZIndex(selectedObj, 100);
        return;
    }
    selectedObj = null;
    return;
}
   
// Turn selected element on
function engage(evt)
{
    evt = (evt) ? evt : event;
    setSelectedElem(evt);
    if (selectedObj)
    {
        if (document.body && document.body.setCapture) 
        {
            // engage event capture in IE/Win
            document.body.setCapture();
        }
        if (evt.pageX)
        {
            offsetX = evt.pageX - ((selectedObj.offsetLeft) ? 
                      selectedObj.offsetLeft : selectedObj.left);
            offsetY = evt.pageY - ((selectedObj.offsetTop) ? 
                      selectedObj.offsetTop : selectedObj.top);
        }
        else if (typeof evt.offsetX != "undefined")
        {
            offsetX = evt.offsetX - ((evt.offsetX < -2) ? 
                      0 : document.body.scrollLeft);
            offsetX -= (document.body.parentElement && 
                     document.body.parentElement.scrollLeft) ? 
                     document.body.parentElement.scrollLeft : 0
            offsetY = evt.offsetY - ((evt.offsetY < -2) ? 
                      0 : document.body.scrollTop);
            offsetY -= (document.body.parentElement && 
                     document.body.parentElement.scrollTop) ? 
                     document.body.parentElement.scrollTop : 0
        }
        else if (typeof evt.clientX != "undefined")
        {
            offsetX = evt.clientX - ((selectedObj.offsetLeft) ? 
                      selectedObj.offsetLeft : 0);
            offsetY = evt.clientY - ((selectedObj.offsetTop) ? 
                      selectedObj.offsetTop : 0);
        }
        return false;
    }
}
   
// Drag an element
function dragIt(evt) {
    evt = (evt) ? evt : event;
    if (selectedObj) {
        if (evt.pageX) {
            shiftTo(selectedObj, (evt.pageX - offsetX), (evt.pageY - offsetY));
        } else if (evt.clientX || evt.clientY) {
            shiftTo(selectedObj, (evt.clientX - offsetX), (evt.clientY - offsetY));
        }
        evt.cancelBubble = true;
        return false;
    }
}
   
// Turn selected element off
function release(evt) {
    if (selectedObj) {
        setZIndex(selectedObj, 0);
        if (document.body && document.body.releaseCapture) {
            // stop event capture in IE/Win
            document.body.releaseCapture();
        }
        selectedObj = null;
    }
}
   
// Assign event handlers used by both Navigator and IE
function initDrag( )
{
    if (document.layers)
    {
        // turn on event capture for these events in NN4 event model
        document.captureEvents(Event.MOUSEDOWN | Event.MOUSEMOVE | Event.MOUSEUP);
        return;
    }
    else if (document.body & document.body.addEventListener)
    {
        // turn on event capture for these events in W3C DOM event model
        document.addEventListener("mousedown", engage, true);
        document.addEventListener("mousemove", dragIt, true);
        document.addEventListener("mouseup", release, true);
        return;
    }
    document.onmousedown = engage;
    document.onmousemove = dragIt;
    document.onmouseup = release;
    return;
}

//Open a dialog window
var _wcm_lastDialog = null;
function openDialog(url, params, w, h, l, t, winid)
{
    if (!winid) winid = 'dialog';
    
    var width   = (w) ? w : 240;
    var height  = (h) ? h : 140;
    var left    = (l) ? l : parseInt((screen.availWidth/2) - (width/2));
    var top     = (t) ? t : parseInt((screen.availHeight/2) - (height/2));
    var options = "width=" + width + ",height=" + height +
                  ",directories=no,status=no,scrollbars=yes,menubar=no,toolbar=no,resizable=yes" +
                  ",left=" + left + ",top=" + top + ",screenX=" + left + ",screenY=" + top;

    if (params) url += '?' + params;
    
    //replace all special characters to fix bug under internet explorer
    //add other characters if needed 
    winid = winid.replace(/[:-]/g,'aa');
            
    _wcm_lastDialog = window.open(url, winid, options);
    _wcm_lastDialog.focus();
    return;
}

function openDialogWithScrollbar(url, params, w, h, l, t, winid)
{
    if (!winid) winid = 'dialog';
    
    var width   = (w) ? w : 240;
    var height  = (h) ? h : 140;
    var left    = (l) ? l : parseInt((screen.availWidth/2) - (width/2));
    var top     = (t) ? t : parseInt((screen.availHeight/2) - (height/2));
    var options = "width=" + width + ",height=" + height +
                  ",directories=no,status=no,scrollbars=yes,menubar=no,toolbar=no,resizable=yes" +
                  ",left=" + left + ",top=" + top + ",screenX=" + left + ",screenY=" + top;

    if (params) url += '?' + params;
        
    _wcm_lastDialog = window.open(url, winid, options);
    _wcm_lastDialog.focus();
    return;
}

/*
 * Show a calendar
 * => Works if (and only if) calendar.js + calendar-[lang].js has been loaded
 *
 * @param   obj         The object where to display calendar (id or object itself)
 * @param   date        The date used to initialize calendar (or null to use obj.value)
 *
 */
var _calendar = null;
var _calendarDateTimeId = null;
var _calendarCallback = null;
function onCalendar_Close()
{
    _calendar.hide();
    if (_calendarCallback)
        calendarCallback(null);
}

function onCalendar_Selection(calendar, date)
{
    calendar.sel.value = date;
    // DateTime mode ?
    if (_calendarDateTimeId)
        updateCalendarDateTime(_calendarDateTimeId);
    _calendar.hide();
    
    if (_calendarCallback)
        calendarCallback(date);
}

function showCalendar(idOrObject, date, callback, calendarDateTimeId)
{
    // Get selection objet (usually an input box)
    var obj = null;
    if (typeof idOrObject == "string")
        obj = document.getElementById(idOrObject);
    else
        obj = idOrObject;

    if (_calendar != null)
    {
        // we already have one created, so just update it
        _calendar.hide();
    }
    else
    {
        // Remember datetime id (for "datetime" mode only)
        _calendarDateTimeId = calendarDateTimeId;
        
        // Remember callback
        if (callback)
            _calendarCallback = callback;

        // first-time call, create the calendar
        _calendar = new Calendar(true, null, onCalendar_Selection, onCalendar_Close);
        _calendar.setRange(1970, 2070);
        _calendar.create();
    }
    if (date)
        _calendar.parseDate(date);
    else
        _calendar.parseDate(obj.value);

    _calendar.sel = obj;
    _calendar.showAtElement(obj);

    return false;
}

/*
 * Update a calendar picked-up value when it's in "datetime" mode
 * (i.e. with hour,minute,seconds fields)
 */
function updateCalendarDateTime(id)
{
    var date = $(id + '_date');
    var time = $(id + '_time');
    var datetime = $(id);
    if (datetime)
    {
        if (date)
        {
            datetime.value = date.value + ' ';
            if (time)
                datetime.value += time.value;
            else
                datetime.value += '00:00:00';
        }
        else
        {
            if (time)
                datetime.value = time.value;
        }
    }
}
 
/*
 * Test date validity as yyyy-mm-dd
 *
 */
function isDate(strValue) 
{
    var part = strValue.split('-');
    if (part.length != 3) return false;
    var y = parseInt(part[0]);
    var m = parseInt(part[1]);
    var d = parseInt(part[2]);
    var validDate = m + '/' + d + '/' + y;
    
    return (Date.parse(validDate) > 0); 
} 


/**
 * Add slashes into the string after an apostroph
 */
function addslashes(ch) {
    ch = ch.replace(/\\/g,"\\\\")
    ch = ch.replace(/\'/g,"\\'")
    ch = ch.replace(/\"/g,"\\\"")
    return ch
}

        //Author:       Ben Nadel
        //Copyright Ben Nadel @ KinkySolutions.com 2006
        function GetParentNodeWithTagName( objNode, strTagName ){
            // Lowercase the tag name for comparison.
            strTagName = strTagName.toLowerCase();
            
            // Crawl up the parent node chain. Keep crawling until we find the 
            // node with the proper tag name, we hit a null node, or we hit 
            // a non-text node that has not tag name (the document object).
            for ( 
                objNode = objNode.parentNode ; 
                (
                    objNode && (            
                        (objNode.tagName && (objNode.tagName.toLowerCase() != strTagName)) ||
                        (!objNode.tagName && (objNode.nodeType != 3)) 
                    )
                );
                objNode = objNode.parentNode
                ){
                // Nothing has to be done within in the FOR loop. We are purely
                // using the FOR loop to crawl up the DOM structure.
            }
        
            // Return the node. At this point, it might contains a valide
            // parent node, or it might be null.
            return( objNode );
        }
        
        //Author:       Ben Nadel
        //Copyright Ben Nadel @ KinkySolutions.com 2006
        // This is our testing method.
        function FindParent( objNode, strTagName ){
            var objParent = GetParentNodeWithTagName( objNode, strTagName );
    
            // Check to see if we found the parent.
            if (objParent != null){
                return objParent.getAttribute( "id" );
            } else {
                return;
            }
        }

// This function changes the booleans values (from true to false or false to true)        
function toggleBooleans(formName, id)
{
    // loop through form to get correct ids
    var theForm = document.forms[formName];
    var frmLength = theForm.length;
    for (i = 0; i < frmLength; i++)
    {
        var theElement = theForm.elements[i];
        var pieces     = theElement.name.split('_');
        var theName    = pieces[3] + '_' + pieces[4] + '_' + pieces[5];
        var boxName    = pieces[1] + '_' + pieces[2] + '_' + theName;

        if (theName == id || id == 'all')
        {
            //changeHtmlBoolean(formName, theElement.name);
            if ((theElement.type == 'checkbox') && (theElement.checked == true))
            {
                theElement.checked = 0;
                theForm.elements[boxName].value = '0';
            }
            else if (theElement.type == 'checkbox')
            {
                theElement.checked = 1;
                theForm.elements[boxName].value = '1';
            }
            
        }
    }
}

var wcmMessage =
{
	timer : null,
	effect : null,
	displayMsg: function(msg, cssclass, time)
	{
		if(this.timer) clearTimeout(this.timer);
		if(this.effect) {
			this.effect.cancel();
			if (this.effect.finish) this.effect.finish();
	        this.effect.event('afterFinish');
		}
		var div = $('sysmessage');
		div.style.display = 'block';
		div.innerHTML = msg;
		div.className = cssclass;
		if(time)
		{
			this.timer = setTimeout(this.hideMsg.bind(this),time);
		}
	},

	hideMsg: function()
	{
		this.effect = Effect.Fade('sysmessage');
	},

	warning: function(msg, time)
	{
		this.displayMsg(msg, 'warning', time);
	},

	info: function(msg, time)
	{
		this.displayMsg(msg, 'info', time);
	},

	error: function(msg, time)
	{
		this.displayMsg(msg, 'error', time);
	}
};
