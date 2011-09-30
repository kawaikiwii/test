/**
 * File : arh.js (AFP/RELAX Homes)
 * @author jy
 * @version 1.0.0
 *
 * Describe the Homes interface of AFP/RELAXNEWS Front-Office
 *
 */
Ext.namespace('ARh');

/*var channels = [['195,196,197,198,199,200,201,202,203,204,205,206,207,210', 'Divertissements'], ['196', 'Musique'], ['197', 'Cinéma'], ['198', 'Jeux vidéo'], ['199', 'Arts-Spectacles'], ['200', 'Arts'], ['201', 'Spectacles'], ['202', 'Livres-BD'], ['203', 'Livres'], ['204', 'BD'], ['205', 'Télévision-Radio'], ['206', 'Télévision'], ['207', 'Radio'], ['210', 'DVD'], ['211,212,213,214,215,216,217', 'Bien-être'], ['212', 'Beauté'], ['213', 'Sport'], ['214', 'Nutrition'], ['215', 'Santé-Forme'], ['216', 'Santé'], ['217', 'Forme'], ['220,221,22,223,224,225,226,227,228,229,230,231,232,235,236,237', 'Maison'], ['221', 'Cuisine'], ['222', 'Mode'], ['223', 'Brico-Jardin'], ['224', 'Jardinage'], ['225', 'Bricolage'], ['226', 'Décoration'], ['227', 'Environnement'], ['228', 'High-Tech'], ['229', 'Loisirs Créatifs'], ['230', 'Shopping-Cadeaux'], ['231', 'Shopping'], ['232', 'Cadeaux'], ['235', 'Vie Pratique-Conso'], ['236', 'Vie pratique'], ['237', 'Conso'], ['238,239,240,241,242,243,244,245', 'Tourisme'], ['239', 'Auto-Deux roues'], ['240', 'Auto'], ['241', 'Deux roues'], ['242', 'Destinations'], ['243', 'Hôtels'], ['244', 'Transports'], ['245', 'Gastronomie']];

var storeChannels = new Ext.data.SimpleStore({
    fields: ['channelIds', 'channelTitle'],
    data: channels
});

var ComboPrevision = new Ext.form.ComboBox({
    name: 'prevision-channelIds',
    id: 'prevision-channelIds',
    store: storeChannels,
    displayField: 'channelTitle',
    valueField: 'channelIds',
    typeAhead: true,
    mode: 'local',
    triggerAction: 'all',
    emptyText: 'Toutes les rubriques',
    selectOnFocus: true
});*/

var previsionQuoi = new Ext.form.TextField({
    width: 150,
    name: 'prevision-keywords',
    id: 'prevision-keywords',
    emptyText: "Mots clefs"
});

var quand = [['start', 'Débutent'], ['between', 'Ont lieu'], ['end', 'Se terminent']];

var storeQuand = new Ext.data.SimpleStore({
    fields: ['listIdQuand', 'listQuand'],
    data: quand
});

var previsionQuand = new Ext.form.ComboBox({
	width: 90,
    name: 'prevision-quand',
    id: 'prevision-quand',
    store: storeQuand,
    displayField: 'listQuand',
    valueField: 'listIdQuand',
    typeAhead: true,
    mode: 'local',
    triggerAction: 'all',
    emptyText: 'Débutent',
    selectOnFocus: true,
    readOnly: true
});

var piliers = [['', 'Tous les piliers'], ['211,212,213,214,215,216,217', 'Bien-être'], ['220,221,22,223,224,225,226,227,228,229,230,231,232,235,236,237', 'Maison'], ['195,196,197,198,199,200,201,202,203,204,205,206,207,210', 'Divertissements'], ['238,239,240,241,242,243,244,245', 'Tourisme']];

var storePiliers = new Ext.data.SimpleStore({
    fields: ['channelIds', 'channelTitle'],
    data: piliers
});

var previsionPiliers = new Ext.form.ComboBox({
	width: 110,
    name: 'prevision-piliers',
    id: 'prevision-piliers',
    store: storePiliers,
    displayField: 'channelTitle',
    valueField: 'channelIds',
    typeAhead: true,
    mode: 'local',
    triggerAction: 'all',
    emptyText: 'Tous les piliers',
    selectOnFocus: true,
    readOnly: true
});

var types = [['', 'Tous les types'], ['1608', 'Prévistar'], ['1609', 'Echéancier'], ['1610', 'Anniversaire']];

var storeTypes = new Ext.data.SimpleStore({
    fields: ['listIds', 'listTitle'],
    data: types
});

var previsionTypes = new Ext.form.ComboBox({
	width: 110,
    hideLabel: true,
    name: 'prevision-types',
    id: 'prevision-types',
    store: storeTypes,
    displayField: 'listTitle',
    valueField: 'listIds',
    typeAhead: true,
    mode: 'local',
    triggerAction: 'all',
    emptyText: 'Tous les types',
    selectOnFocus: true,
    readOnly: true
});

var notes = [['', 'Toutes les notes'], ['1','*'], ['2','**'], ['3','***']];

var storeNotes = new Ext.data.SimpleStore({
    fields: ['noteIds', 'noteTitle'],
    data: notes
});

var previsionNotes = new Ext.form.ComboBox({
	width: 110,
    hideLabel: true,
    name: 'prevision-notes',
    id: 'prevision-notes',
    store: storeNotes,
    displayField: 'noteTitle',
    valueField: 'noteIds',
    typeAhead: true,
    mode: 'local',
    triggerAction: 'all',
    emptyText: 'Toutes les notes',
    selectOnFocus: true,
    readOnly: true
});

var dFromPrevision = new Ext.form.DateField({
    width: 100,
    name: 'prevision-startDate',
    id: 'prevision-startDate',
    format: "Y-m-d",
    readOnly: true,
    emptyText: new Date().format("Y-m-d")
});
var dToPrevision = new Ext.form.DateField({
    width: 100,
    name: 'prevision-endDate',
    id: 'prevision-endDate',
    format: "Y-m-d",
    readOnly: true,
    emptyText: new Date().add(Date.YEAR, 1).format("Y-m-d")
});

ARh = {
    services: {
        /*news: new Ext.Panel({
            //Ext.get("home-news").hasClass("ari-not-allowed"),
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
        
        /*forecast: new Ext.Panel({
            xtype: 'portal',
            layout: 'column',
            autoScroll: true,
            border: false,
            items: [{
                columnWidth: .49,
                border: false,
                style: 'padding: 0.3em',
                items: [{
                    baseCls: 'ari-desk',
                    border: false,
                    contentEl: 'forecast-left'
                }]
            }, {
                columnWidth: .32,
                border: false,
                style: 'padding: 0.3em',
                items: [{
                    baseCls: 'ari-desk',
                    border: false,
                    contentEl: 'forecast-center'
                }]
            }, {
                columnWidth: .49,
                border: false,
                style: 'padding: 0.3em',
                items: [{
                    baseCls: 'ari-desk',
                    border: false,
                    contentEl: 'forecast-right'
                }]
            }]
        }),*/
		
        prevision: new Ext.Panel({
            xtype: 'portal',
            layout: 'column',
            autoScroll: true,
            border: false,
            items: [{
                columnWidth: .99,
                border: false,
                style: 'padding: 0.3em',
                items: [{
                    baseCls: 'ari-desk',
                    border: false,
                    contentEl: 'prevision-left'
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
        })*/
	    
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
	    })
    }
};

var topToolBar = new Ext.Toolbar({
	id: "mainToolBar",
    items: ['<span style="font-weight:bold; line-height: 30px;">Quoi</span>', ' ', ' ', previsionQuoi, ' ', ' ', {
        text: '<span style="font-weight:bold;">Dates prédéfinies</span>',
        menu: new Ext.menu.Menu({
            items: [/*{
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
                    
                    dFromForecast.setValue(dFrom.format("Y-m-d"));
                    dToForecast.setValue(dTo.format("Y-m-d"));
                }
            }, {
                text: 'Ce week-end',
                handler: function(){
                    dFrom = new Date();
                    if (dFrom.getDay() == 0) {
                        dFrom.setDate(dFrom.getDate() - 1)
                    }
                    else {
                        dFrom.setDate(dFrom.getDate() - dFrom.getDay() + 6);
                    }
                    dTo = dFrom.clone();
                    dTo.setDate(dTo.getDate() + 1);
                    
                    dFromForecast.setValue(dFrom.format("Y-m-d"));
                    dToForecast.setValue(dTo.format("Y-m-d"));
                }
            }, */{
                text: 'La semaine prochaine',
                handler: function(){
                    dFrom = new Date();
                    if (dFrom.getDay() == 0)
                        dFrom.setDate(dFrom.getDate() + 1);
                    else
                        dFrom.setDate(dFrom.getDate() - dFrom.getDay() + 8);
                    dTo = dFrom.clone();
                    dTo.setDate(dTo.getDate() + 6);                            
                    dFromPrevision.setValue(dFrom.format("Y-m-d"));
                    dToPrevision.setValue(dTo.format("Y-m-d"));
                }
            }, {
                text: 'Les 30 prochains jours',
                handler: function(){
                    dFrom = new Date();
                    dFromPrevision.setValue(dFrom.format("Y-m-d"));
                    dTo = new Date().add(Date.DAY, 30);
                    dToPrevision.setValue(dTo.format("Y-m-d"));
                }
            }, {
                text: 'Le mois prochain',
                handler: function(){
                    dFrom = new Date();
                    dFrom.setMonth(dFrom.getMonth() + 1);
                    dFrom = dFrom.getFirstDateOfMonth();                            
                    dTo = dFrom.getLastDateOfMonth();                            
                    dFromPrevision.setValue(dFrom.format("Y-m-d"));
                    dToPrevision.setValue(dTo.format("Y-m-d"));
                }
            }/*, {
                text: 'Les 3 prochains mois',
                handler: function(){
                    dFrom = new Date();
                    
                    dTo = new Date().add(Date.MONTH, 2);
                    dTo = dTo.getLastDateOfMonth();
                    
                    dFromForecast.setValue(dFrom.format("Y-m-d"));
                    dToForecast.setValue(dTo.format("Y-m-d"));
                }
            }, {
                text: 'D\'ici la fin de l\'année',
                handler: function(){
                    dFrom = new Date();
                    
                    dTo = new Date();
                    dTo.setMonth(11);
                    dTo = dTo.getLastDateOfMonth();
                    
                    dFromForecast.setValue(dFrom.format("Y-m-d"));
                    dToForecast.setValue(dTo.format("Y-m-d"));
                }
            }*/]
        })
    }, ' ', ' ', previsionQuand, ' ', ' ', dFromPrevision, dToPrevision, ' ', ' ', previsionPiliers, ' ', ' ', {
    	icon: '/rp/images/default/16x16/view.png',
        cls: 'x-btn-icon',
        tooltip: 'Rechercher',
        handler: function(){
            ARe.search.qPrevision();
        }
    },/*{
		text: '<span style="font-weight:bold;">Recherche avancée</span>',
		menu: {
			defaults: {
				hideOnClick: false
			},
			items: [
			new Ext.menu.Adapter(
				new Ext.Panel({
					border: false,
					layout: 'form',
					hideOnClick: false,
					items: [
						previsionTypes
					]
				})
			), '-',
			new Ext.menu.Adapter(
				new Ext.Panel({
					border: false,
					layout: 'form',
					hideOnClick: false,
					items: [
						previsionVilles
					]
				})
			), '-',
			new Ext.menu.Adapter(
				new Ext.Panel({
					border: false,
					layout: 'form',
					hideOnClick: false,
					items: [
						previsionNotes
					]
				})
			)
		]}
	},*/ ' ', ' ', ' ', ' ', ' ', ' ', {
		icon: '/rp/images/default/16x16/zoom_in.png',
        cls: 'x-btn-icon',
        tooltip: 'Recherche avancée',
    	handler: function(){
    		var afficheToolbar = Ext.query("*[id*=secondToolBar]");
			var toolbarVisible = Ext.query("*[class*=toolbar-avancee]");
			if(toolbarVisible.length > 0)
    			Ext.get(afficheToolbar[0]).removeClass("toolbar-avancee");
			else
    			Ext.get(afficheToolbar[0]).addClass("toolbar-avancee");
        }
    }, ' ', ' ', {
        icon: '/rp/images/default/grid/refresh.gif',
        cls: 'x-btn-icon',
        tooltip: 'Réinitialiser',
        handler: function(){
            onResizeTimeLine();
        }
    }, {
        xtype: 'tbfill'
    }, {
    	id: 'showListPrevision',
        icon: '/rp/images/default/16x16/windows.png',
        cls: 'x-btn-icon icone-listtimeline',
        tooltip: ARl.TOOLTIP_TEXT_TB_DISPLAY_LIST_PREVISION,
        handler: function(){
            win = new Ext.Window({
				layout: 'fit',
				width: 800,
				height: 500,
				plain: true,
				modal: true,
				autoScroll: true,
				autoLoad: '/scripts/timelinelist.php'+ARe.requeteSearchPrevision
			});
			win.show();
        }
    }, '<span id="nbPrevisionsAffichees" style="font-weight:bold; line-height: 30px;"></span>']
});

ARapp = {

    tabNumber: 6,
    
    menu: null,
    
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
        id: 'desk-prevision',
        title: "Prévisions",
        cls: Ext.get("home-prevision").hasClass("ari-not-allowed") ? "ari-not-allowed" : "",
        autoScroll: true,
		tbar: topToolBar,
        bbar: ['->', {
            text: '<b>voir toutes les prévisions</b>',
            cls: 'ari-btn-over',
            handler: function(){
                ARe.search.type("Prévisions", 'prevision')
            }
        }],
        items: [new Ext.Panel(ARh.services.prevision)]
    }, {
        id: 'desk-slideshow',
        title: ARl.SERVICES_SLIDESHOWS_TAB_TITLE,
        autoScroll: true,
        cls: Ext.get("home-slideshow").hasClass("ari-not-allowed") ? "ari-not-allowed" : "",
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
    }, {
        id: 'desk-event-rf',
        cls: Ext.get("home-event-rf").hasClass("ari-not-allowed") ? "ari-not-allowed" : "",
        title: 'Événements'
    }]
}
