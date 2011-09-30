/**
 * File : arr.js (AFP/RELAX Homes)
 * @author jy
 * @version 1.0.0
 *
 * Describe the Refresh functions of services
 *
 */
Ext.namespace('ARr');

ARr = {

    tasks: {
        services: {
            run: function(){
                ARr.execute();
            },
            interval: 300000 //5 minutes
        },
        home: {
            run: function(){
                ARh.home.load('/home.php');
            },
            interval: 1800000 //30 minutes
        },
        clock: {
            run: function(){
                var t = new Date();
                Ext.fly('clockDate').update(t.format(ARc.DATEFORMAT_CLOCKDATE));
                Ext.fly('clockTime').update(t.format(ARc.DATEFORMAT_CLOCKTIME));
            },
            interval: 1000 //1 second
        }
    
    },
    
    refresh: 0,
    
    loaded: function(oElement, bSuccess){
        if (bSuccess) {
            ARe.DateFormat(Ext.query("*[class*=ari-publishDate]", oElement.id), ARc.DATEFORMAT_PUBLICATION_LIST);
            ARe.DateFormat(Ext.query("h1[class*=ari-desk-separator-date]", oElement.id), ARc.DATEFORMAT_PUBLICATION_LIST_SEPARATOR);
            ARe.register.dragElements();
        }
    },
    
    execute: function(){
    
    
    
        var store = new Ext.data.JsonStore({
            root: 'results',
            totalProperty: 'resultsCount',
            fields: ['classname', 'id', 'rootChannel', 'mainChannel', 'root', 'title', 'description', 'publicationDate', 'startDate', 'endDate', 'list', 'photo', 'allowed'],
            proxy: new Ext.data.HttpProxy({
                url: '/scripts/rfresh.php?from=' + ARr.refresh
            })
        });
        
        store.load({
            callback: function(r, o, s){
                if (this.reader.jsonData) {
                    ARr.refresh = this.reader.jsonData.executionTime;
                }
                
                store.each(function(r){
                
                    var classname = (r.data.classname == "notice") ? "news" : r.data.classname;
                    var rootEl = Ext.get("ari-separator-" + classname + "-" + r.data.rootChannel);
                    if (rootEl) {
                    
                        var elId = "list-" + r.data.classname + "_" + r.data.id;
                        var elem = Ext.get(elId);
                        if (!elem) {
                            var elem = rootEl.insertHtml("afterEnd", r.data.list, true);
                            if (elem) {
                                elem.highlight();
                                elem.dd = new Ext.dd.DDProxy(elem.id, 'testGroup');
                                ARe.DateFormat(Ext.query("*[class*=ari-publishDate]", elem.id), ARc.DATEFORMAT_PUBLICATION_LIST);
                            }
                        }
                        
                        if (r.data.allowed == "true")
                            elem.removeClass("ari-not-allowed");
                    }
                    
                })
            }
        });
        
    },
    
    sidebar: {
        bin: function(){
            try {
				ARi.tree.bins.getLoader().load(ARi.treeBins.getRootNode());
			} catch(err) {}
        }
    },
    
    news: {},
    event: {},
    slideshow: {},
    video: {}
}
