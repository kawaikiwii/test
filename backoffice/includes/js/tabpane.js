/*----------------------------------------------------------------------------
 * The constructor for tab panes
 *
 *   el : HTMLElement     The html element used to represent the tab pane
 *   bUseCookie : Boolean Optional. TRUE to persist selected tab in cookie
 *   ajaxURLS: Array      Optional. Array of URLs to use for AJAX update
 *   selectedIndex: int   Optional. Default selected tab
 *----------------------------------------------------------------------------*/
function WebFXTabPane( el, bUseCookie, ajaxURLs, selectedIndex, overwriteCookie ) {
    this.element = el;
    this.element.tabPane = this;
    this.pages = [];
    this.selectedIndex = null;
    this.useCookie = bUseCookie != null ? bUseCookie : true;
    this.ajaxURLs = ajaxURLs;

    // add class name tag to class name
    this.element.className = this.classNameTag + " " + this.element.className;

    // add tab row
    this.tabRow = document.createElement( "div" );
    this.tabRow.className = "tab-row";
    el.insertBefore( this.tabRow, el.firstChild );

    var tabIndex = selectedIndex;
    if ( overwriteCookie ) {
        if(overwriteCookie !== true) tabIndex = overwriteCookie;
    } else if ( this.useCookie ) {
        tabIndex = Number( WebFXTabPane.getCookie( "webfxtab_" + this.element.id ) );
    }
    if ( isNaN( tabIndex ) ) tabIndex = 0;
    this.selectedIndex = tabIndex;
}

WebFXTabPane.prototype.classNameTag = "dynamic-tab-pane-control";

WebFXTabPane.prototype.setSelectedIndex = function ( n ) {
    if (this.selectedIndex != n) {
        if (this.selectedIndex != null && this.pages[ this.selectedIndex ] != null )
            this.pages[ this.selectedIndex ].hide();
        this.selectedIndex = n;
        this.pages[ this.selectedIndex ].show();

        if ( this.useCookie )
            WebFXTabPane.setCookie( "webfxtab_" + this.element.id, n ); // session cookie
    }
};

WebFXTabPane.prototype.getSelectedIndex = function () {
    return this.selectedIndex;
};

WebFXTabPane.prototype.addTabPage = function ( oElement ) {
    if ( oElement.tabPage == this ) // already added
        return oElement.tabPage;

    var n = this.pages.length;
    var tp = this.pages[n] = new WebFXTabPage( oElement, this, n);
    tp.tabPane = this;

    // move the tab out of the box
    this.tabRow.appendChild(tp.tab);

    if (n == this.selectedIndex)
        tp.show(true);
    else
        tp.hide();

    return tp;
};

WebFXTabPane.prototype.dispose = function () {
    this.element.tabPane = null;
    this.element = null;
    this.tabRow = null;

    for (var i = 0; i < this.pages.length; i++) {
        this.pages[i].dispose();
        this.pages[i] = null;
    }
    this.pages = null;
};



// Cookie handling
WebFXTabPane.setCookie = function ( sName, sValue, nDays ) {
    var expires = "";
    if ( nDays ) {
        var d = new Date();
        d.setTime( d.getTime() + nDays * 24 * 60 * 60 * 1000 );
        expires = "; expires=" + d.toGMTString();
    }

    document.cookie = sName + "=" + sValue + expires + "; path=/";
};

WebFXTabPane.getCookie = function (sName) {
    var re = new RegExp( "(\;|^)[^;]*(" + sName + ")\=([^;]*)(;|$)" );
    var res = re.exec( document.cookie );
    return res != null ? res[3] : null;
};

WebFXTabPane.removeCookie = function ( name ) {
    setCookie( name, "", -1 );
};








/*-----------------------------------------------------------------------------
 * The constructor for tab pages. This one should not be used.
 * Use WebFXTabPage.addTabPage instead
 *
 * el : HTMLElement         The html element used to represent the tab pane
 * tabPane : WebFXTabPane   The parent tab pane
 * nindex : Number          The index of the page in its siblings
 *---------------------------------------------------------------------------*/
function WebFXTabPage( el, tabPane, nIndex) {
    this.element = el;
    this.element.tabPage = this;
    this.index = nIndex;
    this.loaded = false;

    var cs = el.childNodes;
    for (var i = 0; i < cs.length; i++) {
        if (cs[i].nodeType == 1 && cs[i].className == "tab") {
            this.tab = cs[i];
            break;
        }
    }

    // insert a tag around content to support keyboard navigation
    var a = document.createElement( "A" );
    this.aElement = a;
    a.href = "#";
    a.onclick = function () { return false; };

    while ( this.tab.hasChildNodes() )
        a.appendChild( this.tab.firstChild );
    this.tab.appendChild( a );

    // hook up events, using DOM0
    var oThis = this;
    this.tab.onclick = function () { oThis.select(); };
    this.tab.onmouseover = function () { WebFXTabPage.tabOver( oThis ); };
    this.tab.onmouseout = function () { WebFXTabPage.tabOut( oThis ); };
}

WebFXTabPage.prototype.show = function () {
    var el = this.tab;
    var s = el.className + " selected";
    s = s.replace(/ +/g, " ");
    el.className = s;

    this.element.style.display = "block";
    if (!this.loaded)
    {
        if (this.tabPane.ajaxURLs)
        {
            var url = this.tabPane.ajaxURLs[this.index];
            if (url)
            {
                new Ajax.Updater(
                        $(this.element),
                        url,
                        {
                            evalScripts:true,
                            onComplete: function() { var fv = new WCM.FormValidator('mainForm'); }
                        });
            }
        }
        this.loaded = true;
    }
};

WebFXTabPage.prototype.hide = function () {
    var el = this.tab;
    var s = el.className;
    s = s.replace(/ selected/g, "");
    el.className = s;

    this.element.style.display = "none";
};

WebFXTabPage.prototype.select = function () {
    this.tabPane.setSelectedIndex( this.index );
};

WebFXTabPage.prototype.dispose = function () {
    this.aElement.onclick = null;
    this.aElement = null;
    this.element.tabPage = null;
    this.tab.onclick = null;
    this.tab.onmouseover = null;
    this.tab.onmouseout = null;
    this.tab = null;
    this.tabPane = null;
    this.element = null;
};

WebFXTabPage.tabOver = function ( tabpage ) {
    // Ignore hover of selected tab
    if (tabpage.tabPane.selectedIndex == tabpage.index) return;

    var el = tabpage.tab;
    var s = el.className + " hover";
    s = s.replace(/ +/g, " ");
    el.className = s;
};

WebFXTabPage.tabOut = function ( tabpage ) {
    var el = tabpage.tab;
    var s = el.className;
    s = s.replace(/ hover/g, "");
    el.className = s;
};

/* New functions added 2008/12/18 by jmeyer@relaxnews.com */

function switchPane(target)
{
	var initialClass = "search";
	var leftClass = "switchPane_large";
	var rightClass = "switchPane_small";

	if (target == 'left')
	{
		document.getElementById('switchPaneManager').className = initialClass+" "+leftClass;
	}
	else if (target == 'regular')
	{
		document.getElementById('switchPaneManager').className = initialClass;
	}
	else if (target == 'right')
	{
		document.getElementById('switchPaneManager').className = initialClass+" "+rightClass;
	}
}

function signsCount(f) {
	var txt=f.zone.value;
	var nb=txt.length;
	f.nbcar.value=nb;
}

function signsCountTimer() {
	compter(document.forms["form1"]);
	setTimeout("timer()",100);
}
