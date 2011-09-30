

ARbin = {
    sidebar: {
        bbar: [{
            text: ARl.LABEL_BIN_ACTION_CREATE,
            icon: '/rp/images/default/16x16/package_add.png',
            cls: 'x-btn-text-icon',
            handler: function(){
                Ext.Msg.prompt(ARl.MSG_BIN_NAME_TITLE, ARl.MSG_BIN_NAME_TEXT, function(btn, name){
                    if (btn == 'ok' && name) {
                        ARe.bin.create(name);
                        ARi.tree.bins.getLoader().load(ARi.tree.bins.getRootNode());
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
                                ARi.tree.bins.getLoader().load(ARi.tree.bins.getRootNode());
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
                                ARi.tree.bins.getLoader().load(ARi.tree.bins.getRootNode());
                            }
                        }
                    });
                    
                    
                    
                }
            }
        }        /*,  {
         text: ARl.LABEL_BIN_ACTION_REFRESH,
         icon: '/inc/images/default/16x16/package_find.png',
         cls: 'x-btn-text-icon',
         handler: function(){
         ARr.sidebar.bin();
         }
         }*/
        ]
    }



};
