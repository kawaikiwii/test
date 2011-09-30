/**
 * File : are.js (AFP/RELAX Engine)
 * @author jy
 * @version 1.0.0
 *
 * Describe the data engine of AFP/RELAXNEWS Front-Office
 *
 */
Ext.namespace('Ext.ARd');

ARd = {
    getGridSelection: function(gridId){
        var grid = Ext.getCmp(gridId);
        var selectedRows = grid.getSelectionModel().getSelections();
        var selecteds = [];
        Ext.each(selectedRows, function(r, ii){
            selecteds.push(r.data.classname + "_" + r.data.id);
        });
        return (selecteds.join("/"));
    },
    
    _getDataStore: function(type, params){
        var store = new Ext.data.JsonStore({
            root: 'results',
            totalProperty: 'resultsCount',
            idProperty: 'id',
            remoteSort: true,
            baseParams: params,
            fields: ['classname', 'classnameTranslated', 'id', 'rootChannel', 'mainChannel', 'title', 'description', 'publicationDate', 'startDate', 'endDate', 'breaking', 'photo', 'allowed'],
            proxy: new Ext.data.HttpProxy({
                url: '/scripts/' + type + '.php'
            })
        });
        store.setDefaultSort('publicationDate', 'desc');
        return (store);
    },
    
    _getBinMenu: function(cmpId){
        var menu = new Ext.ux.menu.StoreMenu({
            url: '/scripts/bin.php',
            baseParams: {
                action: 'getbinsmenu',
                cmpId: cmpId
            },
            listeners: {
                'beforeshow': function(){
                    this.loaded = false;
                }
            }
        });
        
        return (menu);
    },
    
    _getBinExport: function(cmpId){
		var menu = new Ext.ux.menu.StoreMenu({
            url: '/scripts/getExportRules.php',
            baseParams: {
                cmpId: cmpId
            },
            listeners: {
                'beforeshow': function(){
                    this.loaded = false;
                }
            }
        });
        
        return (menu);
    },
    
    _getPagingbar: function(store, gridType, gridId){
        var grid = Ext.getCmp(gridId);
        var binMenu = this._getBinMenu(gridId);
        var exportMenu = this._getBinExport(gridId);
        
        switch (gridType) {
            case "bin":
                var tb = new Ext.PagingToolbar({
					pageSize: 15,
                    store: store,
                    displayInfo: true,
                    displayMsg: ARl.DISPLAYING_RESULTS + " {0} - {1}/{2}",
                    emptyMsg: ARl.NO_RESULTS,
                    
                    items: ['-', {
                        /*pressed: false,
                        enableToggle: true,
                        icon: '/rp/images/default/16x16/document.png',
                        tooltip: {
                            text: ARl.TOOLTIP_TEXT_TB_SHOW_DESCRIPTION
                        },
                        cls: 'x-btn-icon',
                        toggleHandler: function(btn, pressed){
                            var grid = Ext.getCmp(gridId);
                            var view = grid.getView();
                            view.showPreview = pressed;
                            view.refresh();
                        }
                    }, '-', {*/
                        text: ARl.LABEL_TB_ACTION_SELECTION,
                        icon: '/rp/images/default/16x16/elements_selection.png',
                        cls: 'x-btn-text-icon',
                        menu: {
                            items: [{
                                icon: '/rp/images/default/16x16/package_delete.png',
                                text: ARl.LABEL_TB_REMOVE_FROM_BIN,
                                handler: function(btn, pressed){
                                    Ext.Msg.show({
                                        title: ARl.MSG_BIN_REMOVEFROM_TITLE,
                                        msg: ARl.MSG_BIN_REMOVEFROM_TEXT,
                                        buttons: Ext.Msg.YESNO,
                                        icon: Ext.Msg.WARNING,
                                        fn: function(btn){
                                            if (btn == 'yes') {
                                                items = ARd.getGridSelection(gridId);
                                                ARe.bin.removeFrom(items, store.baseParams.binId);
                                                //Ext.getCmp(gridId).getStore().load();
                                                titleBar = Ext.getCmp(gridId).title;
                                                ARi.services.remove(Ext.getCmp(gridId));
												ARe.bin.open(titleBar, store.baseParams.binId);
                                            }
                                        }
                                    });
                                    
                                    
                                    
                                    
                                }
                            }, '-', {
                                text: ARl.LABEL_TB_ADD_TO_BIN,
                                icon: '/rp/images/default/16x16/box_into.png',
                                menu: binMenu
                            }]
                        }
                    }, {
                        text: ARl.LABEL_TB_ACTION_BIN,
                        icon: '/rp/images/default/16x16/package_preferences.png',
                        cls: 'x-btn-text-icon',
                        menu: {
                            items: [{
                                text: ARl.LABEL_TB_PRINT,
                                icon: '/rp/images/default/16x16/printer.png',
                                handler: function(btn, pressed){
                                    ARe.bin.print(store.baseParams.binId);
                                }
                                
                            }, '-', {
                                text: ARl.TOOLTIP_TEXT_TB_PURGE_BIN,
                                icon: '/rp/images/default/16x16/package_new.png',
                                handler: function(btn, pressed){
                                    Ext.Msg.show({
                                        title: ARl.MSG_BIN_PURGE_TITLE,
                                        msg: ARl.MSG_BIN_PURGE_TEXT,
                                        buttons: Ext.Msg.YESNO,
                                        icon: Ext.Msg.WARNING,
                                        fn: function(btn){
                                            if (btn == 'yes') {
                                                ARe.bin.clear(store.baseParams.binId);
                                                Ext.getCmp(gridId).getStore().load();
                                            }
                                        }
                                    });
                                }
                            }, '-', {
                                text: ARl.LABEL_TB_EXPORT,
                                icon: '/rp/images/default/16x16/export2.png',
                                menu: exportMenu
                            }]
                        }
                    }]
                });
                break;
            case "search":
            case "folder":
                var tb = new Ext.PagingToolbar({
                    pageSize: 15,
                    store: store,
                    displayInfo: true,
                    displayMsg: ARl.DISPLAYING_RESULTS + " {0} - {1}/{2}",
                    emptyMsg: ARl.NO_RESULTS,
                    
                    items: ['-', {
                        pressed: false,
                        enableToggle: true,
                        icon: '/rp/images/default/16x16/document.png',
                        tooltip: {
                            text: ARl.TOOLTIP_TEXT_TB_SHOW_DESCRIPTION
                        },
                        cls: 'x-btn-icon',
                        toggleHandler: function(btn, pressed){
                            var grid = Ext.getCmp(gridId);
                            var view = grid.getView();
                            view.showPreview = pressed;
                            view.refresh();
                        }
                    }, '-', {
                        text: ARl.LABEL_TB_ACTION_SELECTION,
                        icon: '/rp/images/default/16x16/elements_selection.png',
                        cls: 'x-btn-text-icon',
                        menu: {
                            items: ['-', {
                                text: ARl.LABEL_TB_ADD_TO_BIN,
                                icon: '/rp/images/default/16x16/box_into.png',
                                menu: binMenu
                            }]
                        }
                    }]
                });
                break;
        }
        return (tb);
    },
    
    _getGrid: function(store, gridType, gridId, title){
        var pgBar = this._getPagingbar(store, gridType, gridId);
        
        
        var sm = new Ext.grid.CheckboxSelectionModel({
            listeners: {
                'selectionchange': function(sm){
                    Ext.fly(itemCount.getEl()).update(ARl.LABEL_SB_ITEMS_COUNT + sm.getCount());
                }
            }
        });
        var itemCount = new Ext.Toolbar.TextItem(ARl.LABEL_SB_ITEMS_COUNT + ' 0');
        var grid = new Ext.grid.GridPanel({
            id: gridId,
            iconCls: 'ari-tab-type-' + gridType,
            closable: true,
            
            
            title: title,
            layout: 'fit',
            autoExpandColumn: 'title',
            
            autoScroll: true,
            tbar: pgBar,
            bbar: new Ext.StatusBar({
                id: gridId + '-statusbar',
                items: ['->', itemCount]
            }),
            
            enableDrag: true,
            ddGroup: 'testGroup',
            
            disableSelection: false,
            enableColumnHide: false,
            loadMask: true,
            stripeRows: true,
            trackMouseOver: true,
            store: store,
            sm: sm,
            columns: [sm, {
                id: 'classname',
                header: ARl.LABEL_GRID_COL_CLASSNAME,
                dataIndex: 'classname',
                renderer: this._renderClassname,
                width: 30,
                sortable: true
            }, {
                id: 'mainChannel',
                header: ARl.LABEL_GRID_COL_RUBRIC,
                dataIndex: 'mainChannel',
                width: 80,
                renderer: this._renderMainChannel,
                sortable: true
            }, {
                id: 'title',
                header: ARl.LABEL_GRID_COL_TITLE,
                dataIndex: 'title',
                renderer: this._renderTitle,
                width: 420,
                sortable: true
            }, {
                header: ARl.LABEL_GRID_COL_DATE,
                dataIndex: 'publicationDate',
                renderer: this._renderPublicationDate,
                width: 100,
                sortable: true
            }],
            viewConfig: {
                forceFit: true,
                enableRowBody: true,
                showPreview: false,
                getRowClass: function(r, rowIndex, p, store){
                
                    var breaking = (r.data.breaking == 'true') ? "ari-breaking-news" : "";
						  
					/*if(r.data.description == "")
						breaking += " x-grid3-row-not-allowed";*/
                    
                    if (this.showPreview) {
                        p.body = r.data.description;
                        return 'x-grid3-row-expanded' + ' ' + breaking;
                    }
                    
                    return 'x-grid3-row-collapsed' + ' ' + breaking;
                },
                deferEmptyText: false,
                emptyText: ARl.NO_RESULTS
            }
        });
        
        grid.on('rowclick', function(grid, rowIndex, e){
            var store = grid.getStore();
            var r = store.getAt(rowIndex);
			ARe.pview(r.data.classname, r.data.id, store.baseParams.query);
            
        });
        
        grid.on('rowdblclick', function(grid, rowIndex, e){
            var store = grid.getStore();
            var r = store.getAt(rowIndex);
            ARe.popview(r.data.classname, r.data.id, store.baseParams.query);
            
        });
        return (grid);
    },
    
    _renderPublicationDate: function(v, p, r){
        var regEx = new RegExp("[-]", "g");
        
        switch (r.data.classname.toString()) {
            case 'event':
                var startDate = r.data.startDate.toString().split(regEx);
                var endDate = r.data.endDate.toString().split(regEx);
                startDate = new Date(startDate[0] + "/" + startDate[1] + "/" + startDate[2]).format(ARc.DATEFORMAT_EVENTDATE_SEARCH);
                endDate = new Date(endDate[0] + "/" + endDate[1] + "/" + endDate[2]).format(ARc.DATEFORMAT_EVENTDATE_SEARCH);
                v = startDate + ' - ' + endDate;
                if (startDate.toString() == endDate.toString()) {
                    v = startDate;
                }
                break;
            case 'prevision':
                var startDate = r.data.startDate.toString().split(regEx);
                var endDate = r.data.endDate.toString().split(regEx);
                startDate = new Date(startDate[0] + "/" + startDate[1] + "/" + startDate[2]).format(ARc.DATEFORMAT_EVENTDATE_SEARCH);
                endDate = new Date(endDate[0] + "/" + endDate[1] + "/" + endDate[2]).format(ARc.DATEFORMAT_EVENTDATE_SEARCH);
                v = startDate + ' - ' + endDate;
                if (startDate.toString() == endDate.toString() || r.data.endDate.toString() == "") {
                    v = startDate;
                }
                break;
            default:
                var pubDate = v.toString().split(regEx);
                v = new Date(pubDate[0] + "/" + pubDate[1] + "/" + pubDate[2]).format(ARc.DATEFORMAT_PUBLICATION_SEARCH);
                break;
        }
        return (v);
    },
    
    _renderTitle: function(v, p, r){
	     //if(r.data.description != "")
           return String.format("<img src=\"{0}\" width=\"50\" height=\"50\" border=\"0\"/><h3>{1}</h3><p>{2}</p>",r.data.photo, v, r.data.description);
        //else
           //return String.format("<img src=\"{0}\" width=\"50\" height=\"50\" border=\"0\" style=\"visibility:hidden;\"/><h3>{1}</h3><p>{2}</p>",r.data.photo, v, r.data.description);
    },
    
    _renderMainChannel: function(v, p, r){
        return String.format("<h3 class=\"ari-{1}\">{0}</h3>", v, r.data.rootChannel);
    },
    
    _renderClassname: function(v, p, r){
        return String.format("<h3 class=\"ari-classname\">{0}</h3>", r.data.classnameTranslated);
    }
    
}
