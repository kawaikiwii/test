/**
 * File : ar-onReady.js (AFP/RELAX OnReady)
 * @author jy
 * @version 1.0.0
 *
 * Overwrite the default function of Ext JS Library when the page has been fully loaded
 *
 */
Ext.QuickTips.init();

Ext.form.Field.prototype.msgTarget = 'side';

function createHome(){
    ARh.home = new Ext.Window({
        id: "mainHome",
        width: ARi.viewport.getSize().width,
        height: ARi.viewport.getSize().height - ARi.north.getSize().height - 25,
        y: ARi.north.getSize().height + 26,
        closeAction: 'hide',
        plain: true,
        frame: true,
        autoScroll: true,
        border: false,
        bodyBorder: false,
        hideBorders: true,
        draggable: false,
        resizable: false,
        closable: false
    });
}

function createTrees(){ 
    ARi.tree.bins = new Ext.tree.TreePanel({
        autoScroll: true,
        rootVisible: false,
        border: false,
        enableDD: false,
        loader: new Ext.tree.TreeLoader({
            url: '/scripts/getBins.php'
        }),
        root: {
            nodeType: 'async',
            draggable: false,
            id: 'bSrc'
        }
    });
    
    var tLoad = ARi.tree.bins.getLoader();
    tLoad.addListener('load', function(){
        ARe.register.dropZones();
    })
    
    ARi.tree.bins.render('myBins');
    ARi.tree.bins.getRootNode().expand();
    ARi.tree.bins.on('click', function(node, event){
        if (node.isLeaf()) {
            ARe.bin.open(node.text, node.id);
        }
    });
    
    ARi.tree.folders = new Ext.tree.TreePanel({
        autoScroll: true,
        rootVisible: false,
        border: false,
        enableDD: false,
        dataUrl: '/scripts/getFolders.php',
        root: {
            nodeType: 'async',
            draggable: false,
            id: 'fSrc'
        }
    });
    ARi.tree.folders.render('folders');
    ARi.tree.folders.expandAll();
    ARi.tree.folders.on('click', function(node, event){
        if (node.isLeaf()) {
            ARe.folder.open(node.text, node.id);
        }
    });
    
    ARi.tree.externalFolders = new Ext.tree.TreePanel({
        autoScroll: true,
        rootVisible: false,
        border: false,
        enableDD: false,
        dataUrl: '/scripts/getExternalFolders.php',
        root: {
            nodeType: 'async',
            draggable: false,
            id: 'eFSrc'
        }
    });
    ARi.tree.externalFolders.render('externalFolders');
    ARi.tree.externalFolders.expandAll();
    ARi.tree.externalFolders.on('click', function(node, event){
        ARe.externalFolder.open(node.text, node.id);
    });
}

function afficherFolders(tempsEcoule,permExtFolders) {
	var test = ARi.tree.externalFolders.getNodeById("eFSrc");
	tempsEcoule = parseInt(tempsEcoule)+parseInt(200);
	if (test.childNodes.length > 0) {
		for (var i = 0; i < test.childNodes.length; i++) {
			permissions = permExtFolders[0].toString();
			permissions = permissions.split(",");
			for (var cpt = 0; cpt < permissions.length; cpt++) {
				if (permissions[cpt] == test.childNodes[i].id) 
					ARe.externalFolder.open(test.childNodes[i].text, test.childNodes[i].id);
			}
		}
	}
	else if(tempsEcoule < 300000)
		var t=setTimeout("afficherFolders("+tempsEcoule+","+permExtFolders+")",200);
}

Ext.onReady(function(){    
    ARi.services.plugins = new ARe.tabCloseMenu();
    ARi.services = new Ext.TabPanel(ARi.services);
    ARi.preview = new Ext.Panel(ARi.preview);
    ARi.sidebar = new Ext.Panel(ARi.sidebar);
    ARi.qBar.render('qBar');
	Ext.Ajax.timeout = 120000;
    
    if (ARapp.menu) {
        ARi.qBar.add(ARapp.menu.universe);
    }
    ARi.north = new Ext.BoxComponent(ARi.header)
    
    ARi.viewport = new Ext.Viewport({
        layout: 'border',
        items: [ARi.services, ARi.sidebar, ARi.preview, ARi.north]
    });
    createHome();
    createTrees();
      
    ARi.services.addListener('resize', function(){
    	onResizeTimeLine();
    });
      
    ARi.viewport.addListener('resize', function(vp, adjWidth, adjHeight, rawWidth, rawHeight){
        ARh.home.setWidth(adjWidth);
        ARh.home.setHeight(adjHeight - ARi.north.getSize().height);
    });
    
    ARe.init();
	
	var param_ARe = Ext.query("*[id*=preview-news]");
	ARe.previewed.classname = "news";
        ARe.previewed.id = param_ARe[0].id.substr(parseInt(param_ARe[0].id.indexOf("_"))+1);
    
    ARi.services.doLayout();
    ARe.DateFormat(Ext.query("*[class*=ari-publishDate]"), ARc.DATEFORMAT_PUBLICATION_LIST);
    ARe.DateFormat(Ext.query("h1[class*=ari-desk-separator-date]"), ARc.DATEFORMAT_PUBLICATION_LIST_SEPARATOR);
    
    Ext.TaskMgr.start(ARr.tasks.clock);
    Ext.TaskMgr.start(ARr.tasks.home);
    Ext.TaskMgr.start(ARr.tasks.services);
    
    ARh.home.show();
    
    Ext.get('qBar-field').focus();
    Ext.get("loading-mask").hide();
    
    ARi.services.addListener('beforetabchange', function(tp, nt, ct){
        if (nt.id == "toHome" || nt.id == "desk-event-rf") {
            ARh.home.show();
        }
        else {
            ARh.home.hide();
        }
    });
    ARi.services.addListener('tabchange', function(tp, p){
    	var permPrevisions = Ext.query("*[id=desk-prevision][class*=ari-not-allowed]");
    	var permEvents = Ext.query("*[id=desk-event][class*=ari-not-allowed]");
    	if(((p.id == "desk-prevision" && permPrevisions.length == 0) || (p.id == "desk-event" && permEvents.length == 0)) && document.getElementById("tl").innerHTML == "") {
			onLoadTimeLine();
			if(p.id == "desk-prevision") {
				var tb2container = Ext.DomHelper.append(topToolBar.el,{tag:'div',id:'secondToolBar'},true);
				var secondToolBar = new Ext.Toolbar(tb2container);
				secondToolBar.add(previsionTypes, ' ', ' ', previsionNotes);
				var removeItem = Ext.query("*[id*=secondToolBar]");
				Ext.get(removeItem[0]).remove();
				Ext.get(removeItem[1]).addClass("toolbar-avancee");
			}
		}
        if (p.id == "toHome") {
            ARh.home.show();
        }
        if (p.id == "desk-event-rf") {
            var strOption = "left=0,top=0, height=" + screen.availHeight + ", width=" + screen.availWidth;
            window.open("http://old.relaxfil.com/relaxfil.asp", "Relaxfil", strOption + ", scrollbars=no,resizable=yes")
            ARh.home.show();
        }
        
        var panel = Ext.get(p.id);
		if (panel.hasClass("ari-not-allowed")) {
			panel.mask(Ext.get("restricted-service").dom.innerHTML, "ari-access-denied");
			var elems = panel.query("*[class*=ari-item]");
            ARe.allowItems(elems);
			var classTab = p.id.substr(parseInt(p.id.indexOf("-"))+1);
			ARe.pview(classTab, "denied");
        }
        
    });
    
    Ext.getCmp('relaxbarPartner').collapse();
	Ext.getCmp('relaxbarSelection').expand();
	
	var homes = Ext.get("homes").query("*[class*=ari-home]");
	var permissionGranted = homes.length;
	for (i = 0; i < homes.length; i++) {
		var home = Ext.get(homes[i]);
		if (home) {
			if (home.hasClass("ari-not-allowed"))
				permissionGranted--;
		}
	}
	if(permissionGranted == 0)
	{
		ARh.home.hide();
		Ext.getCmp('relaxbarSelection').collapse();
		Ext.getCmp('relaxbarPartner').expand();
		
		ARe.permissionsExternalFolders = new Ext.data.JsonStore({
            root: 'permissionsExternalFolders',            
            fields: ['id'],
            proxy: new Ext.data.HttpProxy({
                url: '/scripts/getPermissionsExternalFolders.php'
            })
        });
		
		ARe.permissionsExternalFolders.load({
            callback: function(r, o, s){
				var permExtFolders = new Array();
				ARe.permissionsExternalFolders.each(function(r){
					permExtFolders.push(r.data.id);
				});
				afficherFolders(0,permExtFolders);
			}
        });
	}
});

function print_r(obj){
    win_print_r = window.open('about:blank', 'win_print_r');
    win_print_r.document.write('<html><body>');
    r_print_r(obj, win_print_r);
    win_print_r.document.write('</body></html>');
}

function r_print_r(theObj, win_print_r){
    if (theObj.constructor == Array ||
    theObj.constructor == Object) {
        if (win_print_r == null) 
            win_print_r = window.open('about:blank', 'win_print_r');
    }
    for (var p in theObj) {
        if (theObj[p].constructor == Array ||
        theObj[p].constructor == Object) {
            win_print_r.document.write("<li>[" + p + "] =>" + typeof(theObj) + "</li>");
            win_print_r.document.write("<ul>")
            r_print_r(theObj[p], win_print_r);
            win_print_r.document.write("</ul>")
        }
        else {
            win_print_r.document.write("<li>[" + p + "] =>" + theObj[p] + "</li>");
        }
    }
    win_print_r.document.write("</ul>")
}

