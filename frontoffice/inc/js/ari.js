/**
 * File : ari.js (AFP/RELAX Interface)
 * @author jy
 * @version 1.0.0
 *
 * Describe the interface of AFP/RELAXNEWS Front-Office
 *
 */
Ext.namespace('ARi');

ARi = {
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
    
    gmt: new Date().format("P"),
    tree: {},
    
    qBar: new Ext.Toolbar({
        contentEl: 'qBar',
        items: [new Ext.app.SearchField({
            id: 'qBar-field',
            emptyText: ARl.DEFAULT_LABEL_SEARCH,
            width: 450
        })        /*, {
         id: 'qBar-field-more',
         text: 'more',
         handler: function(){
         var qField = Ext.get('qBar-field');
         var XY = qField.getXY();
         var HEIGHT = qField.getHeight();
         // ARe.adSearch.setPageY(XY[1] + HEIGHT);
         ARe.adSearch.show();
         }
         }*/
        ]
    }),
    
    header: {
        region: 'north',
        el: 'logo',
        height: 50
    },
    
    services: {
        id: 'services',
        region: 'center',
        enableTabScroll: true,
        activeTab: 0,
        layoutOnTabChange: true,
        border: false,     
        items: ARapp.services
    },
    
    sidebar: {
        id: 'sidebar',
        title: 'Relaxbar',
        split: true,
        region: 'east',
        width: 250,
        minSize: 175,
        maxSize: 400,
        collapsible: true,
        titleCollapse: true,
        hideCollapseTool: true,
        margins: '0 0 0 5',
        layout: 'accordion',
        layoutConfig: {
            animate: true
        },
        items: [{
            title: ARl.SIDEBAR_EDITORFOLDER_TAB_TITLE,
            border: false,
            iconCls: 'ari-sidebar-header-folder',
            autoScroll: true,
			contentEl: 'externalFolders',
			id: 'relaxbarPartner'
        },{
            title: ARl.SIDEBAR_EDITORSELECTION_TAB_TITLE,
            border: false,
            iconCls: 'ari-sidebar-header-folder',
            autoScroll: true,
            contentEl: 'folders',
			id: 'relaxbarSelection'
        }, {
            title: ARl.SIDEBAR_MYSELECTION_TAB_TITLE,
            border: false,
            iconCls: 'ari-sidebar-header-bin',
            autoscroll: true,
            ddGroup: 'testGroup',
            contentEl: 'myBins',
			id: 'relaxbarMyBins',
            bbar: [{
                text: ARl.LABEL_BIN_ACTION_CREATE,
                icon: '/rp/images/default/16x16/package_add.png',
                cls: 'x-btn-text-icon',
                handler: function(){
                    Ext.Msg.prompt(ARl.MSG_BIN_NAME_TITLE, ARl.MSG_BIN_NAME_TEXT, function(btn, name){
                        if (btn == 'ok' && name) {
                            ARe.bin.create(name);
							var t=setTimeout("ARi.tree.bins.getLoader().load(ARi.tree.bins.getRootNode())",500);
                        }
                    });
                }
            }, {
                xtype: 'tbfill'
            }, {
            
                icon: '/rp/images/default/16x16/package_new.png',
                cls: 'x-btn-icon',
                tooltip: ARl.TOOLTIP_TEXT_TB_PURGE_BIN,
                handler: function(){
                    var ids = '', msg = '', selNodes = ARi.tree.bins.getChecked();
                    Ext.each(selNodes, function(node){
                        if (ids.length > 0) {
                            ids += ',';
                            msg += ', ';
                        }
                        ids += node.id;
                        msg += node.text;
                    });
                    
                    if (ids != "") {
                    
                        Ext.Msg.show({
                            title: ARl.MSG_BIN_PURGE_TITLE,
                            msg: ARl.MSG_BIN_PURGE_TEXT + " : " + msg,
                            buttons: Ext.Msg.YESNO,
                            icon: Ext.Msg.WARNING,
                            fn: function(btn){
                                if (btn == 'yes') {
                                    ARe.bin.clear(ids);
                                    var t=setTimeout("ARi.tree.bins.getLoader().load(ARi.tree.bins.getRootNode())",500);
                                }
                            }
                        });
                        
                        
                        
                    }
                }
            }, {
            
                icon: '/rp/images/default/16x16/package_delete.png',
                cls: 'x-btn-icon',
                tooltip: ARl.MSG_BIN_REMOVE_TITLE,
                handler: function(){
                    var ids = '', msg = '', selNodes = ARi.tree.bins.getChecked();
                    Ext.each(selNodes, function(node){
                        if (ids.length > 0) {
                            ids += ',';
                            msg += ', ';
                        }
                        ids += node.id;
                        msg += node.text;
                    });
                    
                    if (ids != "") {                    
                        Ext.Msg.show({
                            title: ARl.MSG_BIN_REMOVE_TITLE,
                            msg: ARl.MSG_BIN_REMOVE_TEXT + " : " + msg,
                            buttons: Ext.Msg.YESNO,
                            icon: Ext.Msg.WARNING,
                            fn: function(btn){
                                if (btn == 'yes') {
                                    ARe.bin.remove(ids);
                                    var t=setTimeout("ARi.tree.bins.getLoader().load(ARi.tree.bins.getRootNode())",500);
                                }
                            }
                        });                        
                    }
                }
            }            /*,  {
             text: ARl.LABEL_BIN_ACTION_REFRESH,
             icon: '/inc/images/default/16x16/package_find.png',
             cls: 'x-btn-text-icon',
             handler: function(){
             ARr.sidebar.bin();
             }
             }*/
            ]
        }]
    },
    
    preview: {
        id: 'preview',
        contentEl: 'previewzone',
        cls: 'ari-preview',
        region: 'south',
        autoScroll: true,
        split: true,
        height: document.body.clientHeight / 3,
        minSize: 100,
        maxSize: 600,
        collapsible: true,
        margins: '0 0 0 0',
        tbar: [{
            icon: '/rp/images/default/16x16/windows.png',
            cls: 'x-btn-icon',
            tooltip: ARl.TOOLTIP_TEXT_TB_OPEN_IN_WINDOW,
            handler: function(){
                if (ARe.previewed.classname && ARe.previewed.id) {
                    ARe.popview(ARe.previewed.classname, ARe.previewed.id);
                }
            }
        }, {
            icon: '/rp/images/default/16x16/printer.png',
            cls: 'x-btn-icon',
            tooltip: ARl.TOOLTIP_TEXT_TB_PRINT,
            handler: function(){
                if (ARe.previewed.classname && ARe.previewed.id) {
                    ARe.popprint(ARe.previewed.classname, ARe.previewed.id);
                }
            }
        }, '-', {
            icon: '/rp/images/default/16x16/disk_blue.png',
            cls: 'x-btn-icon',
            tooltip: ARl.TOOLTIP_TEXT_TB_MEDIA,
            handler: function(){
                if (ARe.previewed.classname && ARe.previewed.id) {
                    ARe.mediaview(ARe.previewed.classname, ARe.previewed.id);
                }
                
            }
        }, '-', {
            icon: '/rp/images/default/16x16/document_gear.png',
            cls: 'x-btn-icon',
            tooltip: ARl.TOOLTIP_TEXT_TB_ACTIONS,
            menu: {
                items: [{
                    text: ARl.LABEL_TB_ADD_TO_BIN,
                    icon: '/rp/images/default/16x16/box_into.png',
                    menu: new Ext.ux.menu.StoreMenu({
                        url: '/scripts/bin.php',
                        baseParams: {
                            action: 'getbinsmenu',
                            cmpId: "preview"
                        },
                        listeners: {
                            'beforeshow': function(){
                                this.loaded = false;
                            }
                        }
                    })
                }, '-', {
                    text: ARl.LABEL_TB_EXPORT,
                    icon: '/rp/images/default/16x16/export1.png',
                    menu: new Ext.ux.menu.StoreMenu({
                        url: '/scripts/getExportRules.php',
                        baseParams: {
                            cmpId: "preview-preview-preview"
                        },
                        listeners: {
                            'beforeshow': function(){
                                this.loaded = false;
                            }
                        }
                    })
                }]
            }
        }, {
            xtype: 'tbfill'
        }, {
            icon: '/rp/images/default/16x16/window_split_ver.png',
            cls: 'x-btn-icon',
            tooltip: ARl.TOOLTIP_TEXT_TB_REDUCE,
            handler: function(){
                Ext.getCmp('preview').toggleCollapse();
            }
            
        }]
    }



};
