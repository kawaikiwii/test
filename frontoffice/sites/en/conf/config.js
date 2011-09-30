/**
 * File : arh.js (AFP/RELAX Homes)
 * @author jy
 * @version 1.0.0
 *
 * Describe the Homes interface of AFP/RELAXNEWS Front-Office
 *
 */
Ext.namespace('ARh');

var eventQuoi = new Ext.form.TextField({
    width: 150,
    name: 'event-keywords',
    id: 'event-keywords',
    emptyText: "Keywords"
});

var datesProgrammees = [['1', 'This week'], ['2', 'Next week'], ['3', 'Next month'], ['4', 'Next 3 months']];

var storeDatesProgrammees = new Ext.data.SimpleStore({
    fields: ['listIdDatesProgrammees', 'listDatesProgrammees'],
    data: datesProgrammees
});

var eventDatesProgrammees = new Ext.form.ComboBox({
	width: 140,
    name: 'event-datesProgrammees',
    id: 'event-datesProgrammees',
    store: storeDatesProgrammees,
    displayField: 'listDatesProgrammees',
    valueField: 'listIdDatesProgrammees',
    typeAhead: true,
    mode: 'local',
    triggerAction: 'all',
    emptyText: 'This week',
    selectOnFocus: true,
    readOnly: true
});

var quand = [['start', 'Start'], ['between', 'Between'], ['end', 'End']];

var storeQuand = new Ext.data.SimpleStore({
    fields: ['listIdQuand', 'listQuand'],
    data: quand
});

var eventQuand = new Ext.form.ComboBox({
	width: 90,
    name: 'event-quand',
    id: 'event-quand',
    store: storeQuand,
    displayField: 'listQuand',
    valueField: 'listIdQuand',
    typeAhead: true,
    mode: 'local',
    triggerAction: 'all',
    emptyText: 'Start',
    selectOnFocus: true,
    readOnly: true
});

var storeVilles =  new Ext.data.JsonStore({
	root: 'events',
	fields: ['ville'],
	proxy: new Ext.data.HttpProxy({
		url: '/scripts/getVilles.php?file=event_en.json'
	})
});
storeVilles.load();
var eventVilles = new Ext.form.ComboBox({
    name: 'event-villes',
    id: 'event-villes',
    store: storeVilles,
    displayField: 'ville',
    valueField: 'ville',
    typeAhead: true,
    mode: 'local',
    triggerAction: 'all',
    emptyText: 'Every towns',
    selectOnFocus: true,
    readOnly: true
});

/*var piliers = [['', 'All channels'], ['49,53,56,57,60', 'Well-being'], ['50,61,64,67,68,69,70', 'House & Home'], ['51,80,84,85,86,90,91,94', 'Entertainment'], ['52,73,76,77,78,79', 'Tourism']];

var storePiliers = new Ext.data.SimpleStore({
    fields: ['channelIds', 'channelTitle'],
    data: piliers
});

var eventPiliers = new Ext.form.ComboBox({
	width: 110,
    name: 'event-piliers',
    id: 'event-piliers',
    store: storePiliers,
    displayField: 'channelTitle',
    valueField: 'channelIds',
    typeAhead: true,
    mode: 'local',
    triggerAction: 'all',
    emptyText: 'All channels',
    selectOnFocus: true
});*/

/*var dFromEvent = new Ext.form.DateField({
    width: 100,
    name: 'event-startDate',
    id: 'event-startDate',
    format: "Y-m-d",
    readOnly: true,
    emptyText: new Date().format("Y-m-d")
});
var dToEvent = new Ext.form.DateField({
    width: 100,
    name: 'event-endDate',
    id: 'event-endDate',
    format: "Y-m-d",
    readOnly: true,
    emptyText: new Date().add(Date.YEAR, 1).format("Y-m-d")
});*/

var dFromEvent = new Ext.form.TextField({
    name: 'event-startDate',
    id: 'event-startDate',
    readOnly: true,
    emptyText: new Date().format("Y-m-d"),
    cls: 'icone-listtimeline'
});
var dToEvent = new Ext.form.TextField({
    name: 'event-endDate',
    id: 'event-endDate',
    readOnly: true,
    emptyText: new Date().add(Date.YEAR, 1).format("Y-m-d"),
    cls: 'icone-listtimeline'
});

ARh = {
    services: {
        /*news: new Ext.Panel({
            xtype: 'portal',
            layout: 'column',
            autoScroll: true,
            border: false,
            items: [{
                columnWidth: .24,
                border: false,
                style: 'padding: 0.3em',
                items: [{
                    baseCls: 'ari-desk',
                    border: false,
                    contentEl: 'news-channel-wellbeing'
                }]
            }, {
                columnWidth: .24,
                border: false,
                style: 'padding: 0.3em',
                items: [{
                    baseCls: 'ari-desk',
                    border: false,
                    contentEl: 'news-channel-househome'
                }]
            }, {
                columnWidth: .24,
                border: false,
                style: 'padding: 0.3em',
                items: [{
                    baseCls: 'ari-desk',
                    border: false,
                    contentEl: 'news-channel-entertainment'
                }]
            }, {
                columnWidth: .24,
                border: false,
                style: 'padding: 0.3em',
                items: [{
                    baseCls: 'ari-desk',
                    border: false,
                    contentEl: 'news-channel-tourism'
                }]
            }]
        }),*/
    	
    	news: new Ext.Panel({
	        //Ext.get("home-news").hasClass("ari-not-allowed"),
	        xtype: 'portal',
	        layout: 'table',
	        autoScroll: true,
	        border: false,
	        layoutConfig: {
	            // The total column count must be specified here
	            columns: 4
	        },
	        items: [{
	            columnWidth: .24,
	            border: false,
	            style: 'padding: 0.3em',
	            cellCls: 'temp_table',
	            items: [{
	                baseCls: 'ari-desk',
	                border: false,
	                contentEl: 'news-channel-wellbeing'
	            }]
	        }, {
	            columnWidth: .24,
	            border: false,
	            style: 'padding: 0.3em',
	            cellCls: 'temp_table',
	            items: [{
	                baseCls: 'ari-desk',
	                border: false,
	                contentEl: 'news-channel-househome'
	            }]
	        }, {
	            columnWidth: .24,
	            border: false,
	            style: 'padding: 0.3em',
	            cellCls: 'temp_table',
	            items: [{
	                baseCls: 'ari-desk',
	                border: false,
	                contentEl: 'news-channel-entertainment'
	            }]
	        }, {
	            columnWidth: .24,
	            border: false,
	            style: 'padding: 0.3em',
	            cellCls: 'temp_table',
	            items: [{
	                baseCls: 'ari-desk',
	                border: false,
	                contentEl: 'news-channel-tourism'
	            }]
	        }]
	    }),
        
        /*slideshows: new Ext.Panel({
            xtype: 'portal',
            layout: 'column',
            autoScroll: true,
            border: false,
            items: [{
                columnWidth: .24,
                border: false,
                style: 'padding: 0.3em',
                items: [{
                    baseCls: 'ari-desk',
                    border: false,
                    contentEl: 'slideshow-channel-wellbeing'
                }]
            }, {
                columnWidth: .24,
                border: false,
                style: 'padding: 0.3em',
                items: [{
                    baseCls: 'ari-desk',
                    border: false,
                    contentEl: 'slideshow-channel-househome'
                }]
            }, {
                columnWidth: .24,
                border: false,
                style: 'padding: 0.3em',
                items: [{
                    baseCls: 'ari-desk',
                    border: false,
                    contentEl: 'slideshow-channel-entertainment'
                }]
            }, {
                columnWidth: .24,
                border: false,
                style: 'padding: 0.3em',
                items: [{
                    baseCls: 'ari-desk',
                    border: false,
                    contentEl: 'slideshow-channel-tourism'
                }]
            }]
        }),*/
    	
    	slideshows: new Ext.Panel({
	        //Ext.get("home-news").hasClass("ari-not-allowed"),
	        xtype: 'portal',
	        layout: 'table',
	        autoScroll: true,
	        border: false,
	        layoutConfig: {
	            // The total column count must be specified here
	            columns: 4
	        },
	        items: [{
	            columnWidth: .24,
	            border: false,
	            style: 'padding: 0.3em',
	            cellCls: 'temp_table',
	            items: [{
	                baseCls: 'ari-desk',
	                border: false,
	                contentEl: 'slideshow-channel-wellbeing'
	            }]
	        }, {
	            columnWidth: .24,
	            border: false,
	            style: 'padding: 0.3em',
	            cellCls: 'temp_table',
	            items: [{
	                baseCls: 'ari-desk',
	                border: false,
	                contentEl: 'slideshow-channel-househome'
	            }]
	        }, {
	            columnWidth: .24,
	            border: false,
	            style: 'padding: 0.3em',
	            cellCls: 'temp_table',
	            items: [{
	                baseCls: 'ari-desk',
	                border: false,
	                contentEl: 'slideshow-channel-entertainment'
	            }]
	        }, {
	            columnWidth: .24,
	            border: false,
	            style: 'padding: 0.3em',
	            cellCls: 'temp_table',
	            items: [{
	                baseCls: 'ari-desk',
	                border: false,
	                contentEl: 'slideshow-channel-tourism'
	            }]
	        }]
	    }),
        
        /*videos: new Ext.Panel({
            xtype: 'portal',
            layout: 'column',
            autoScroll: true,
            border: false,
            items: [{
                columnWidth: .24,
                border: false,
                style: 'padding: 0.3em',
                items: [{
                    baseCls: 'ari-desk',
                    border: false,
                    contentEl: 'video-channel-wellbeing'
                }]
            }, {
                columnWidth: .24,
                border: false,
                style: 'padding: 0.3em',
                items: [{
                    baseCls: 'ari-desk',
                    border: false,
                    contentEl: 'video-channel-househome'
                }]
            }, {
                columnWidth: .24,
                border: false,
                
                style: 'padding: 0.3em',
                items: [{
                    baseCls: 'ari-desk',
                    border: false,
                    contentEl: 'video-channel-entertainment'
                }]
            }, {
                columnWidth: .24,
                border: false,
                style: 'padding: 0.3em',
                items: [{
                    baseCls: 'ari-desk',
                    border: false,
                    contentEl: 'video-channel-tourism'
                }]
            }]
        }),*/
	    
	    videos: new Ext.Panel({
	        //Ext.get("home-news").hasClass("ari-not-allowed"),
	        xtype: 'portal',
	        layout: 'table',
	        autoScroll: true,
	        border: false,
	        layoutConfig: {
	            // The total column count must be specified here
	            columns: 4
	        },
	        items: [{
	            columnWidth: .24,
	            border: false,
	            style: 'padding: 0.3em',
	            cellCls: 'temp_table',
	            items: [{
	                baseCls: 'ari-desk',
	                border: false,
	                contentEl: 'video-channel-wellbeing'
	            }]
	        }, {
	            columnWidth: .24,
	            border: false,
	            style: 'padding: 0.3em',
	            cellCls: 'temp_table',
	            items: [{
	                baseCls: 'ari-desk',
	                border: false,
	                contentEl: 'video-channel-househome'
	            }]
	        }, {
	            columnWidth: .24,
	            border: false,
	            style: 'padding: 0.3em',
	            cellCls: 'temp_table',
	            items: [{
	                baseCls: 'ari-desk',
	                border: false,
	                contentEl: 'video-channel-entertainment'
	            }]
	        }, {
	            columnWidth: .24,
	            border: false,
	            style: 'padding: 0.3em',
	            cellCls: 'temp_table',
	            items: [{
	                baseCls: 'ari-desk',
	                border: false,
	                contentEl: 'video-channel-tourism'
	            }]
	        }]
	    }),
        
        /*events: new Ext.Panel({
            xtype: 'portal',
            layout: 'column',
            autoScroll: true,
            border: false,
            
            items: [{
                columnWidth: .49,
                border: false,
                style: 'padding:0.3em',
                items: [{
                    style: 'background-image:none',
                    baseCls: 'ari-desk',
                    border: false,
                    contentEl: 'event-channel-ourselection'
                }]
            }, {
                columnWidth: .49,
                border: false,
                style: 'padding: 0.3em',
                items: [{
                    style: 'background-image:none',
                    baseCls: 'ari-desk',
                    border: false,
                    contentEl: 'event-channel-mustsee'
                }]
            }]
        })*/
       
       	events: new Ext.Panel({
            xtype: 'portal',
            layout: 'column',
            autoScroll: true,
            border: false,            
            items: [{
                columnWidth: .99,
                border: false,
                style: 'padding:0.3em',
                items: [{
                    baseCls: 'ari-desk',
                    border: false,
                    contentEl: 'event-channel-ourselection'
                }]
            }]
        })
    }
};

var topToolBar = new Ext.Toolbar({
	id: "mainToolBar",
    items: ['<span style="font-weight:bold; line-height: 30px;">Keywords</span>', ' ', ' ', eventQuoi, ' ', ' ', eventQuand, ' ', ' ', eventDatesProgrammees/*, ' ', ' ', {
        text: '<span style="font-weight:bold;">Dates prédéfinies</span>',
        menu: new Ext.menu.Menu({
            items: [{
                text: 'Cette semaine',
                handler: function(){
                    dFrom = new Date();
                    if (dFrom.getDay() == 0) {
                        dFrom.setDate(dFrom.getDate() - 6)
                    }
                    else {
                        dFrom.setDate(dFrom.getDate() - dFrom.getDay() + 1);
                    }
                    dTo = dFrom.clone();
                    dTo.setDate(dTo.getDate() + 6);
                    
                    dFromEvent.setValue(dFrom.format("Y-m-d"));
                    dToEvent.setValue(dTo.format("Y-m-d"));
                }
            }, {
                text: 'La semaine prochaine',
                handler: function(){
                    dFrom = new Date();
                    if (dFrom.getDay() == 0)
                        dFrom.setDate(dFrom.getDate() + 1);
                    else
                        dFrom.setDate(dFrom.getDate() - dFrom.getDay() + 8);
                    dTo = dFrom.clone();
                    dTo.setDate(dTo.getDate() + 6);                            
                    dFromEvent.setValue(dFrom.format("Y-m-d"));
                    dToEvent.setValue(dTo.format("Y-m-d"));
                }
            }, {
                text: 'Le mois prochain',
                handler: function(){
                    dFrom = new Date();
                    dFrom.setMonth(dFrom.getMonth() + 1);
                    dFrom = dFrom.getFirstDateOfMonth();                            
                    dTo = dFrom.getLastDateOfMonth();                            
                    dFromEvent.setValue(dFrom.format("Y-m-d"));
                    dToEvent.setValue(dTo.format("Y-m-d"));
                }
            }, {
                text: 'Les 3 prochains mois',
                handler: function(){
                    dFrom = new Date();
                    dFrom.setMonth(dFrom.getMonth() + 1);
                    dFrom = dFrom.getFirstDateOfMonth();
                    dTo = new Date();
                    dTo.setMonth(dTo.getMonth() + 3);
                    dTo = dTo.getLastDateOfMonth();                            
                    dFromEvent.setValue(dFrom.format("Y-m-d"));
                    dToEvent.setValue(dTo.format("Y-m-d"));
                }
            }]
        })
    }, ' ', ' '*/, dFromEvent, dToEvent, ' ', ' ', eventVilles, ' ', ' ', {
    	icon: '/rp/images/default/16x16/view.png',
        cls: 'x-btn-icon',
        tooltip: 'Search',
        handler: function(){
            ARe.search.qEvent("Events");
        }
    }, ' ', ' ', ' ', ' ', ' ', ' ', {
        icon: '/rp/images/default/grid/refresh.gif',
        cls: 'x-btn-icon',
        tooltip: 'Reset',
        handler: function(){
            onResizeTimeLine();
        }
    }, {
        xtype: 'tbfill'
    }, {
    	id: 'showListEvent',
        icon: '/rp/images/default/16x16/windows.png',
        cls: 'x-btn-icon icone-listtimeline',
        tooltip: ARl.TOOLTIP_TEXT_TB_DISPLAY_LIST_EVENT,
        handler: function(){
            win = new Ext.Window({
				layout: 'fit',
				width: 800,
				height: 500,
				plain: true,
				modal: true,
				autoScroll: true,
				autoLoad: '/scripts/timelinelist.php'+ARe.requeteSearchEvent
			});
			win.show();
        }
    }]
});

ARapp = {
    tabNumber: 5,
    
    menu: {
        universe: [{
            xtype: 'tbspacer',
            handleMouseEvents: false,
            style: 'cursor:default;'
        }, {
            text: "Language",
            cls: 'ari-btn-menu-active',
            handleMouseEvents: false,
            menu: {
                items: [{
					id: "afprelax-lang-en",
                    text: "English",
                    onClick: function(){
                        window.location = "/en/";
                    }
                }, {
					id: "afprelax-lang-fr",
                    text: "Français",
                    onClick: function(){
                        window.location = "/fr/";
                    }
                }]
            }
        }]
    },
    
    services: [{
        id: 'toHome',
        iconCls: 'toHome',
        title: '&nbsp;'
    
    }, {
        id: 'desk-news',
        cls: Ext.get("home-news").hasClass("ari-not-allowed") ? "ari-not-allowed" : "",
        
        title: ARl.SERVICES_NEWS_TAB_TITLE,
        autoScroll: true,
        bbar: ['->', {
            text: '<b>' + ARl.LABEL_SEE_ALL_NEWS + '</b>',
            cls: 'ari-btn-over',
            handler: function(){
                ARe.search.type(ARl.SERVICES_NEWS_TAB_TITLE, 'news')
            }
        }],
        items: [new Ext.Panel(ARh.services.news)]
    }, {
        id: 'desk-event',
        title: ARl.SERVICES_EVENTS_TAB_TITLE,
        cls: Ext.get("home-event").hasClass("ari-not-allowed") ? "ari-not-allowed" : "",
        autoScroll: true,
        tbar: topToolBar,
        bbar: ['->', {
            text: '<b>' + ARl.LABEL_SEE_ALL_EVENTS + '</b>',
            cls: 'ari-btn-over',
            handler: function(){
                ARe.search.type(ARl.SERVICES_EVENTS_TAB_TITLE, 'event')
            }
        }],
        items: [new Ext.Panel(ARh.services.events)]
    }, {
        id: 'desk-slideshow',
        cls: Ext.get("home-slideshow").hasClass("ari-not-allowed") ? "ari-not-allowed" : "",
        
        title: ARl.SERVICES_SLIDESHOWS_TAB_TITLE,
        autoScroll: true,
        bbar: ['->', {
            text: '<b>' + ARl.LABEL_SEE_ALL_SLIDESHOWS + '</b>',
            cls: 'ari-btn-over',
            handler: function(){
                ARe.search.type(ARl.SERVICES_SLIDESHOWS_TAB_TITLE, 'slideshow')
            }
        }],
        items: [new Ext.Panel(ARh.services.slideshows)]
    }, {
        id: 'desk-video',
        cls: Ext.get("home-video").hasClass("ari-not-allowed") ? "ari-not-allowed" : "",
        
        title: ARl.SERVICES_VIDEOS_TAB_TITLE,
        autoScroll: true,
        bbar: ['->', {
            text: '<b>' + ARl.LABEL_SEE_ALL_VIDEOS + '</b>',
            cls: 'ari-btn-over',
            handler: function(){
                ARe.search.type(ARl.SERVICES_VIDEOS_TAB_TITLE, 'video')
            }
        }],
        items: [new Ext.Panel(ARh.services.videos)]
    }]


}

