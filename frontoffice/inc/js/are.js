/**
 * File : are.js (AFP/RELAX Engine)
 * @author jy
 * @version 1.0.0
 *
 * Describe the engine of AFP/RELAXNEWS Front-Office
 *
 */
Ext.namespace('Ext.ARe');

ARe = {
    dropZones: [],
    permissions: null,
	timelineRech_pos: 0,
    previewed: {
        classname: null,
        id: null
    },
    requeteSearchPrevision: null,
	 
	allowItems: function(elems){
        for (i = 0; i < elems.length; i++) {
            Ext.get(elems[i]).removeClass("ari-not-allowed");
        }
    },
    
    init: function(){
		ARe.permissions_news = new Ext.data.JsonStore({
            root: 'permissions',            
            fields: ['service', 'channels'],
            proxy: new Ext.data.HttpProxy({
                url: '/scripts/getPermissions.php'
            })
        });
        
		var a_trouver_news = /news/i;
        var a_trouver_notice = /notice/i;
        ARe.permissions_news.load({
            callback: function(r, o, s){
                ARe.permissions_news.each(function(r){
					if (r.data.service == "news") {
						var channels = r.data.channels + "";
						channels = channels.split(",");
						if (!Ext.isIE7) {
							for (var i = 0; i < channels.length; i++) {
								elems = Ext.query("*[classname*=" + r.data.service + "][channelId=" + channels[i] + "]");
								if (elems.length > 0) 
									ARe.allowItems(elems);
							}
						}
						else {
							for (var i = 0; i < channels.length; i++) {
								elems = Ext.query("*[channelId=" + channels[i] + "]");
								if (elems.length > 0) {
									var elems_news = new Array();
									for (var j = 0; j < elems.length; j++) {
										if(elems[j].id.match(a_trouver_news) || elems[j].id.match(a_trouver_notice))
											elems_news.push(elems[j]);
									}
									ARe.allowItems(elems_news);
								}
							}
						}
					}
					if (r.data.service == "notice") {
						var channels = r.data.channels + "";
						channels = channels.split(",");
						if (!Ext.isIE7) {
							for (var i = 0; i < channels.length; i++) {
								elems = Ext.query("*[classname*=" + r.data.service + "][channelId=" + channels[i] + "]");
								if (elems.length > 0) 
									ARe.allowItems(elems);
							}
						}
						else {
							for (var i = 0; i < channels.length; i++) {
								elems = Ext.query("*[channelId=" + channels[i] + "]");
								if (elems.length > 0) {
									var elems_news = new Array();
									for (var j = 0; j < elems.length; j++) {
										if(elems[j].id.match(a_trouver_notice))
											elems_news.push(elems[j]);
									}
									ARe.allowItems(elems_news);
								}
							}
						}
					}
                });
            }
        });
		
		/*ARe.permissions_forecast = new Ext.data.JsonStore({
            root: 'permissions',
            
            fields: ['service', 'channels'],
            proxy: new Ext.data.HttpProxy({
                url: '/scripts/getPermissions.php'
            })
        });
		
		ARe.permissions_forecast.load({
            callback: function(r, o, s){
                ARe.permissions_forecast.each(function(r){
					if (r.data.service == "forecast") {
						var channels = r.data.channels + "";
						channels = channels.split(",");
						for (var i = 0; i < channels.length; i++) {
							elems = Ext.query("*[classname*=" + r.data.service + "][channelId=" + channels[i] + "]");
							if (elems.length > 0) 
								ARe.allowItems(elems);
						}
					}
                });
            }
        });*/
       
       ARe.permissions_event = new Ext.data.JsonStore({
            root: 'permissions',            
            fields: ['service', 'channels'],
            proxy: new Ext.data.HttpProxy({
                url: '/scripts/getPermissions.php'
            })
        });
		
		var a_trouver_event = /event/i;
		ARe.permissions_event.load({
            callback: function(r, o, s){
                ARe.permissions_event.each(function(r){
					if (r.data.service == "event") {
						var channels = r.data.channels + "";
						channels = channels.split(",");
						if (!Ext.isIE7) {
							for (var i = 0; i < channels.length; i++) {
								elems = Ext.query("*[classname*=" + r.data.service + "][channelId=" + channels[i] + "]");
								if (elems.length > 0) 
									ARe.allowItems(elems);
							}
						}
						else {
							for (var i = 0; i < channels.length; i++) {
								elems = Ext.query("*[channelId=" + channels[i] + "]");
								if (elems.length > 0) {
									var elems_news = new Array();
									for (var j = 0; j < elems.length; j++) {
										if(elems[j].id.match(a_trouver_event))
											elems_news.push(elems[j]);
									}
									ARe.allowItems(elems_news);
								}
							}
						}
					}
                });
            }
        });
		
		ARe.permissions_slideshow = new Ext.data.JsonStore({
            root: 'permissions',            
            fields: ['service', 'channels'],
            proxy: new Ext.data.HttpProxy({
                url: '/scripts/getPermissions.php'
            })
        });
		
		var a_trouver_slideshow = /slideshow/i;
		ARe.permissions_slideshow.load({
            callback: function(r, o, s){
                ARe.permissions_slideshow.each(function(r){
					if (r.data.service == "slideshow") {
						var channels = r.data.channels + "";
						channels = channels.split(",");
						if (!Ext.isIE7) {
							for (var i = 0; i < channels.length; i++) {
								elems = Ext.query("*[classname*=" + r.data.service + "][channelId=" + channels[i] + "]");
								if (elems.length > 0) 
									ARe.allowItems(elems);
							}
						}
						else {
							for (var i = 0; i < channels.length; i++) {
								elems = Ext.query("*[channelId=" + channels[i] + "]");
								if (elems.length > 0) {
									var elems_news = new Array();
									for (var j = 0; j < elems.length; j++) {
										if(elems[j].id.match(a_trouver_slideshow))
											elems_news.push(elems[j]);
									}
									ARe.allowItems(elems_news);
								}
							}
						}
					}
                });
            }
        });
		
		ARe.permissions_video = new Ext.data.JsonStore({
            root: 'permissions',            
            fields: ['service', 'channels'],
            proxy: new Ext.data.HttpProxy({
                url: '/scripts/getPermissions.php'
            })
        });
		
		var a_trouver_video = /video/i;
		ARe.permissions_video.load({
            callback: function(r, o, s){
                ARe.permissions_video.each(function(r){
					if (r.data.service == "video") {
						var channels = r.data.channels + "";
						channels = channels.split(",");
						if (!Ext.isIE7) {
							for (var i = 0; i < channels.length; i++) {
								elems = Ext.query("*[classname*=" + r.data.service + "][channelId=" + channels[i] + "]");
								if (elems.length > 0) 
									ARe.allowItems(elems);
							}
						}
						else {
							for (var i = 0; i < channels.length; i++) {
								elems = Ext.query("*[channelId=" + channels[i] + "]");
								if (elems.length > 0) {
									var elems_news = new Array();
									for (var j = 0; j < elems.length; j++) {
										if(elems[j].id.match(a_trouver_video))
											elems_news.push(elems[j]);
									}
									ARe.allowItems(elems_news);
								}
							}
						}
					}
                });
            }
        });
		
		var homes = Ext.get("homes").query("*[class*=ari-home]");
        for (i = 0; i < homes.length; i++) {
            var home = Ext.get(homes[i]);
			if (home) {
				if (home.hasClass("ari-not-allowed")) {
					var tabPanelId = home.id.replace("home", "services__desk");
					var elem = Ext.get(tabPanelId);
					elem.addClass("x-item-disabled");
				}
				if (home.id == "home-event-rf") {
					var tabPanelId = home.id.replace("home", "services__desk");
					var elem = Ext.get(tabPanelId);
					if(tabPanelId == "services__desk-event-rf")
					    elem.addClass("event_tab_hidden");
				}
			}
        }
		
        /*
         return;
         
         Ext.Ajax.request({
         url: '/scripts/getPermissions.php',
         success: function(result, request){
         var jsonData = Ext.util.JSON.decode(result.responseText);
         for (i = 0; i < jsonData.length; i++) {
         alert(jsonData.permissions[i]);
         }
         
         
         }
         });*/
        //alert(store);
        //       this.register.dropZones();
        //this.register.dragElements();
    },
    
    profile: function(){
        Ext.form.Field.prototype.msgTarget = 'side';
        var fs = new Ext.FormPanel({
            labelWidth: 90,
            border: false,
            width: 380,
            waitMsgTarget: true,
            
            
            items: {
                xtype: 'tabpanel',
                activeTab: 0,
                defaults: {
                    autoHeight: true,
                    bodyStyle: 'padding:10px'
                },
                items: [{
                    title: ARl.PROFILE_TAB_INFOS,
                    layout: 'form',
                    defaults: {
                        width: 230
                    },
                    defaultType: 'textfield',
                    
                    items: [{
                        fieldLabel: ARl.PROFILE_FIRSTNAME,
                        name: 'first',
                        allowBlank: false,
                        value: ''
                    }, {
                        fieldLabel: ARl.PROFILE_LASTNAME,
                        name: 'last',
                        allowBlank: false,
                        value: ''
                    }, {
                        fieldLabel: ARl.PROFILE_EMAIL,
                        name: 'email',
                        allowBlank: false,
                        vtype: 'email'
                    }, {
                        fieldLabel: ARl.PROFILE_COMPANY,
                        name: 'company',
                        readOnly: true,
                        disabled: true
                    }, {
                        fieldLabel: ARl.PROFILE_EXPIRATIONDATE,
                        name: 'expirationdate',
                        readOnly: true,
                        disabled: true
                    }]
                }, {
                    title: ARl.PROFILE_TAB_PASSWORD,
                    layout: 'form',
                    defaults: {
                        width: 230
                    },
                    defaultType: 'textfield',
                    
                    items: [{
                        fieldLabel: ARl.PROFILE_PASSWORD_ACTUAL,
                        name: 'passwordOld',
                        value: "",
                        inputType: 'password'
                    }, {
                        fieldLabel: ARl.PROFILE_PASSWORD_NEW,
                        name: 'passwordNew',
                        value: "",
                        inputType: 'password'
                    }, {
                        fieldLabel: ARl.PROFILE_PASSWORD_RENEW,
                        name: 'passwordReNew',
                        value: "",
                        inputType: 'password'
                    }]
                }, {
                    title: ARl.PROFILE_TAB_CONTACT,
                    defaults: {
                        width: 200
                    },
                    contentEl: "contact"
                }, {
                    title: ARl.PROFILE_TAB_HELP,
                    defaults: {
                        width: 200
                    },
                    contentEl: "help"
                }]
            }
        });
        
        var win = new Ext.Window({
            width: 400,
            height: 300,
            autoScroll: true,
            modal: true,
            items: fs,
            resizable: false,
            buttons: [{
                text: 'Changer',
                handler: function(){
                    fs.form.submit({
                        url: '/scripts/changePass.php',
                        waitMsg: 'Saving Data...',
                        success: function(form, action){
                            Ext.MessageBox.alert('Message', 'Saved OK');
                        },
                        failure: function(form, action){
                            Ext.MessageBox.alert('Message', 'Save failed');
                        }
                        
                    });
                }
            }]
        });
        win.show();
        
        fs.form.load({
            url: '/scripts/getUser.php',
            method: 'GET',
            waitMsg: 'Loading'
        });
    },
    
    alerte: function(alerteId){
    	win_alerte = new Ext.Window({
			layout: 'fit',
			width: 1100,
			height: 640,
			id: 'alerte_customer',
			plain: true,
			modal: true,
			autoScroll: true,
			autoLoad: '/scripts/alerte.php?command=refresh&alerteId='+alerteId
		});
    	win_alerte.show();
    },
    
    manageAlerte: function(command,alerteId,textMessage,formDatas,perimeter){
    	if(command == "add"){
    		formDatas = "";
    		tabElem = document.forms.alerteForm.getElementsByTagName("*");
    		for (var i=0; i<tabElem.length; i++) {
    			if (tabElem[i].type == "text" || tabElem[i].type == "hidden") {
    				if (formDatas != "") 
    					formDatas += "__";
    				formDatas += tabElem[i].id + "=" + encodeURIComponent(tabElem[i].value);
    			}
    			if (tabElem[i].type == "select-one") {
    				if (formDatas != "") 
    					formDatas += "__";
    				formDatas += tabElem[i].id + "=" + encodeURIComponent(tabElem[i].options[tabElem[i].selectedIndex].value);
    			}
    			if (tabElem[i].type == "radio") {
    				if (tabElem[i].checked == true) {
    					if (formDatas != "") 
    						formDatas += "__";
    					formDatas += tabElem[i].id + "=" + encodeURIComponent(tabElem[i].value);
    				}
    			}
    		}
    	}
    	if(command == "add" || command == "preview"){
    		var tab_perimeter = perimeter.split(',');
    		var nb_univers = 0;
    		var tab_univers = new Array();
    		var car = '';
    		var trouve = false;
    		for (var i = 0; i < tab_perimeter.length; i++) {
    			trouve = false;
    			car = tab_perimeter[i].charAt(0);
    			if (i == 0) {
    				trouve = true;
    				tab_univers.push(car);
    				nb_univers++;
    			}
    			
    			for (var j = 0; j < tab_univers.length; j++) {
    				if (car == tab_univers[j]) 
    					trouve = true;
    			}
    			if (!trouve) {
    				nb_univers++;
    				tab_univers.push(car);
    			}
    		}
    		if (nb_univers > 1) {
    			alert(ARl.RESTRICT_NB_UNIVERS);
    			command = "refresh";
    			textMessage = "";
    		}
    	}
    	win_alerte.close();
    	win_alerte = new Ext.Window({
			layout: 'fit',
			width: 1100,
			height: 640,
			id: 'alerte_customer',
			plain: true,
			modal: true,
			autoScroll: true,
			autoLoad: {
				url: '/scripts/alerte.php?command='+command+'&alerteId='+alerteId+'&textMessage='+encodeURI(textMessage)+'&formDatas='+formDatas+'&perimeter='+perimeter,
				callback: function() {
					if(textMessage != '') {
						Ext.get("alertSaveMsg").highlight();
				    	Ext.get("alertSaveMsg").fadeOut();
					}
				}
			}
		});
    	win_alerte.show();
    	if(command == "add" || command == "delete")
    		var t=setTimeout("ARi.tree.alertes.getLoader().load(ARi.tree.alertes.getRootNode())",500);
    },
    
    searchbox: function(searchboxId){
    	win_searchbox = new Ext.Window({
			layout: 'fit',
			width: 1100,
			height: 640,
			id: 'searchbox_customer',
			plain: true,
			modal: true,
			autoScroll: true,
			autoLoad: '/scripts/searchbox.php?command=refresh&searchboxId='+searchboxId
		});
    	win_searchbox.show();
    },
    
    manageSearchbox: function(command,searchboxId,textMessage,formDatas,perimeter){
    	if(command == "add"){
    		formDatas = "";
    		tabElem = document.forms.searchboxForm.getElementsByTagName("*");
    		for (var i=0; i<tabElem.length; i++) {
    			if (tabElem[i].type == "text" || tabElem[i].type == "hidden") {
    				if (formDatas != "") 
    					formDatas += "__";
    				formDatas += tabElem[i].id + "=" + encodeURIComponent(tabElem[i].value);
    			}
    			if (tabElem[i].type == "select-one") {
    				if (formDatas != "") 
    					formDatas += "__";
    				formDatas += tabElem[i].id + "=" + encodeURIComponent(tabElem[i].options[tabElem[i].selectedIndex].value);
    			}
    			if (tabElem[i].type == "radio") {
    				if (tabElem[i].checked == true) {
    					if (formDatas != "") 
    						formDatas += "__";
    					formDatas += tabElem[i].id + "=" + encodeURIComponent(tabElem[i].value);
    				}
    			}
    		}
    	}
    	win_searchbox.close();
    	win_searchbox = new Ext.Window({
			layout: 'fit',
			width: 1100,
			height: 640,
			id: 'searchbox_customer',
			plain: true,
			modal: true,
			autoScroll: true,
			autoLoad: {
				url: '/scripts/searchbox.php?command='+command+'&searchboxId='+searchboxId+'&textMessage='+encodeURI(textMessage)+'&formDatas='+formDatas+'&perimeter='+perimeter,
				callback: function() {
					if(textMessage != '') {
						Ext.get("searchboxSaveMsg").highlight();
				    	Ext.get("searchboxSaveMsg").fadeOut();
					}
				}
			}
		});
    	win_searchbox.show();
    	if(command == "add" || command == "delete")
    		var t=setTimeout("ARi.tree.searchbox.getLoader().load(ARi.tree.searchbox.getRootNode())",500);
    },
    
    register: {
        dragElements: function(){
            return;
            var els = Ext.select(".ari-item", true);
            els.each(function(){
                this.dd = new Ext.dd.DDProxy(this.id, 'testGroup');
            });
        },
        
        dropZones: function(){
            var els = Ext.select("#myBins .x-tree-node-leaf", true);
            els.each(function(){
                new Ext.dd.DropTarget(this.id, {
                    ddGroup: 'testGroup',
                    copy: false,
                    notifyDrop: function(ddSource, e, data){
                        var el = Ext.get(this.id);
                        var binId = el.getAttributeNS("ext", "tree-node-id");
                        var selecteds = [];
                        Ext.each(ddSource.dragData.selections, function(r, ii, ai){
							selecteds.push(r.data.classname + "_" + r.data.id);
                        });
                        ARe.bin.request({
                            items: selecteds.join("/"),
                            binId: binId,
                            action: 'addTo'
                        }, false)
                        el.clearOpacity();
                        el.removeClass("ari-dropzone");
                        el.highlight();
                        return (true);
                    },
                    notifyEnter: function(ddSource, e, data){
                        var el = Ext.get(this.id);
                        el.setOpacity(.7);
                        el.addClass("ari-dropzone");
                        return ('x-dd-drop-ok');
                    },
                    notifyOut: function(ddSource, e, data){
                        var el = Ext.get(this.id);
                        el.clearOpacity();
                        el.removeClass("ari-dropzone");
                    }
                });
                ARe.dropZones.push(this.id);
            });
            
        }
    },
    
    /**
     * PREVIEW PART
     *
     * @param {Object} itemClass
     * @param {Object} itemId
     */
    pview: function(itemClass, itemId, query){
    
        var formatDate = ARc.DATEFORMAT_PUBLICATION_PREVIEW;
        if (itemClass == 'prevision') {
            formatDate = ARc.DATEFORMAT_EVENTDATE_SEARCH;
        }
        
        if (ARi.services.getActiveTab().id == "toHome") {
            ARh.home.hide();
            switch (itemClass) {
                case "news":
                    ARi.services.setActiveTab(1);
                    break;
                case "event":
                    ARi.services.setActiveTab(2);
                    break;
                case "slideshow":
                    ARi.services.setActiveTab(3);
                    break;
                case "video":
                    ARi.services.setActiveTab(4);
                    break;
            }
        }
        
        ARi.preview.load({
            url: "/scripts/pview.php",
            params: {
                classname: itemClass,
                id: itemId,
                query: query
            },
            nocache: false,
            text: ARl.LOADING,
            timeout: 30,
            scripts: false,
            callback: function(el, success, response, options){
                if (success) {
                    if (Ext.get("preview")) {
                        ARe.DateFormat(Ext.query("span[class*=ari-publishDate]", "preview"), formatDate);
                    }
                    ARe.previewed.classname = itemClass;
                    ARe.previewed.id = itemId;
                }
            }
        });
    },
    
    mediaview: function(itemClass, itemId){
        ARh.home.hide();
		if (itemClass != "video") {
			win = new Ext.Window({
				layout: 'fit',
				width: 800,
				height: 500,
				plain: true,
				modal: true,
				autoScroll: true,
				autoLoad: '/mediaview.php?lang=' + ARc.CURRENT_UNIVERSE_CODE + '&classname=' + itemClass + '&id=' + itemId,
				buttons: [{
					text: ARl.LABEL_BUTTON_DOWNDLOAD,
					handler: function(){
						document.getElementById("downloadZip").submit();
					}
				}]
			});
		}
		else {
			win = new Ext.Window({
				layout: 'fit',
				width: 800,
				height: 500,
				plain: true,
				modal: true,
				autoScroll: true,
				autoLoad: '/mediaview.php?lang=' + ARc.CURRENT_UNIVERSE_CODE + '&classname=' + itemClass + '&id=' + itemId
			});
		}
        win.show();
    },
    
    popview: function(itemClass, itemId){
        ARh.home.hide();
        window.open('/view.php?classname=' + itemClass + '&id=' + itemId);
    },
    
    popprint: function(itemClass, itemId){
        ARh.home.hide();
        window.open('/print.php?classname=' + itemClass + '&id=' + itemId);
    },
    
    
    
    /* ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** **
     * Functions related to selection display
     */
    folder: {
        open: function(elem, folderId){
        
            if (elem.innerHTML) {
                var tabTitle = elem.innerHTML;
            }
            else {
                var tabTitle = elem;
            }
            ARh.home.hide();
            
            var ctId = new Date().format("YmdHis").toString() + (Math.round(Math.random() * 1000000)).toString();
            var params = {
                folders: folderId,
                ctId: ctId
            };
            
            if (!Ext.getCmp('search-grid-' + folderId)) {
                var store = ARd._getDataStore('search', params);
                var grid = ARd._getGrid(store, 'folder', 'search-grid-' + folderId, tabTitle);
                var tab = ARi.services.insert(ARapp.tabNumber, grid);
                tab.show();
                store.load({
                    params: {
                        start: 0,
                        limit: 14
                    }
                });
				ARe.timelineRech_pos++;
                
            }
            else {
                var grid = Ext.getCmp('search-grid-' + folderId);
                grid.show();
                grid.getStore().load();
            }
        }
    },
	
	/* ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** **
     * Functions related to external selection display
     */
    externalFolder: {
        open: function(elem, externalFolderId){
        
            if (elem.innerHTML) {
                var tabTitle = elem.innerHTML;
            }
            else {
                var tabTitle = elem;
            }
            ARh.home.hide();
            
            var ctId = new Date().format("YmdHis").toString() + (Math.round(Math.random() * 1000000)).toString();
			var params = {
                externalFolders: externalFolderId,
                ctId: ctId
            };
			
			if(externalFolderId != "0-16" && externalFolderId != "0-17" && externalFolderId != "467" && externalFolderId != "468") {
				if (!Ext.getCmp('search-grid-' + externalFolderId)) {
					var store = ARd._getDataStore('search', params);
					var grid = ARd._getGrid(store, 'folder', 'search-grid-' + externalFolderId, tabTitle);
					var tab = ARi.services.insert(ARapp.tabNumber, grid);
					tab.show();
	                store.load({
	                    params: {
	                        start: 0,
	                        limit: 14
	                    }
	                });
					ARe.timelineRech_pos++;
				}
	            else {
	                var grid = Ext.getCmp('search-grid-' + externalFolderId);
	                grid.show();
	                grid.getStore().load();
	            }
			}
			else if(externalFolderId != "0-16" && externalFolderId != "0-17") {
				if (!Ext.getCmp('search-grid-' + externalFolderId)) {
					var grid = ARi.services.add({
						id: 'search-grid-' + externalFolderId,
					    title: tabTitle,
					    iconCls: 'ari-tab-type-folder',
					    html: '<iframe src="../../scripts/nowfashion.php?externalFolderId='+externalFolderId+'" width="99%" height="99%"></iframe>',
					    closable:true
					}).show();
					ARi.preview.load({
			            url: "/scripts/nowfashionGuide.php",
			            params: {
			                externalFolderId: externalFolderId
			            },
			            nocache: false,
			            text: ARl.LOADING,
			            timeout: 30,
			            scripts: false,
			            callback: function(el, success, response, options){
			                if (success) {
			                    ARe.previewed.classname = "";
			                    ARe.previewed.id = 0;
			                }
			            }
			        });
					ARe.timelineRech_pos++;
				}
				else {
					var grid = Ext.getCmp('search-grid-' + externalFolderId);
	                grid.show();
				}
			}
        }
    },
    
    /* ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** **
     * Functions related to search display
     */
    search: {
        q: function(query){
            ARh.home.hide();
            var ctId = new Date().format("YmdHis").toString() + (Math.round(Math.random() * 1000000)).toString();
            var params = {
                query: query,
                ctId: ctId
            };
            var store = ARd._getDataStore('search', params);
            var grid = ARd._getGrid(store, 'search', 'search-grid-' + ctId, query.substr(0, 15) + '...');
            
            var tab = ARi.services.insert(ARapp.tabNumber, grid);
            tab.show();
            store.load({
                params: {
                    start: 0,
                    limit: 14
                }
            });
            ARe.timelineRech_pos++;
        },
        
        rubric: function(elem, classname, rubricId){
            ARh.home.hide();
            if (elem.innerHTML) {
                var tabTitle = elem.innerHTML.substr(0, 15) + '...';
            }
            else {
                var tabTitle = elem.substr(0, 15) + '...';
            }
            
            var ctId = new Date().format("YmdHis").toString() + (Math.round(Math.random() * 1000000)).toString();
            var params = {
                channelIds: rubricId,
                classname: classname,
                ctId: ctId
            };
            var store = ARd._getDataStore('search', params);
            var grid = ARd._getGrid(store, 'search', 'search-grid-' + ctId, tabTitle);
            var tab = ARi.services.insert(ARapp.tabNumber, grid);
            tab.show();
            store.load({
                params: {
                    start: 0,
                    limit: 14
                }
            });
			ARe.timelineRech_pos++;
        },
        
        searchboxLaunch: function(elem, alertId){
        
            if (elem.innerHTML) {
                var tabTitle = elem.innerHTML;
            }
            else {
                var tabTitle = elem;
            }
            ARh.home.hide();
            
            var ctId = new Date().format("YmdHis").toString() + (Math.round(Math.random() * 1000000)).toString();
            var params = {
            	alertId: alertId,
                ctId: ctId
            };
            
            if (!Ext.getCmp('searchbox-grid-' + alertId)) {
                var store = ARd._getDataStore('search', params);
                var grid = ARd._getGrid(store, 'folder', 'searchbox-grid-' + alertId, tabTitle);
                var tab = ARi.services.insert(ARapp.tabNumber, grid);
                tab.show();
                store.load({
                    params: {
                        start: 0,
                        limit: 14
                    }
                });
				ARe.timelineRech_pos++;
                
            }
            else {
                var grid = Ext.getCmp('searchbox-grid-' + alertId);
                grid.show();
                grid.getStore().load();
            }
        },
        
        qPrevision: function(){
            ARh.home.hide();
            var ctId = new Date().format("YmdHis").toString() + (Math.round(Math.random() * 1000000)).toString();
            var query = Ext.get("prevision-keywords").getValue();
            if (query == previsionQuoi.emptyText)
                query = "";
            
            var quand = previsionQuand.getValue();
            if(quand == "")
            	quand = "start";
			
            var rubric = "";
            if(Ext.get("prevision-piliers").getValue() != previsionPiliers.emptyText)
            	rubric = previsionPiliers.getValue();
			
            var types = "";
			if(Ext.get("prevision-types").getValue() != "Tous les types")
            	types = previsionTypes.getValue();
                
			var note = "";
            if(Ext.get("prevision-notes").getValue() != "Toutes les notes")
            	note = previsionNotes.getValue();
            	
            performFiltering(tl, [0,1], query, quand, rubric, types, note, false);
            
            var params = {
        		query: query,
        		quand: quand,
                previsionStartDate: Ext.get("prevision-startDate").getValue(),
                previsionEndDate: Ext.get("prevision-endDate").getValue(),
                classname: 'prevision',
                channelIds: rubric,
                listIds: types,
                ratingValue: note,
                ctId: ctId
            };
            var store = ARd._getDataStore('search', params);
            var grid = ARd._getGrid(store, 'search', 'search-grid-prevision' + ctId, 'Prévisions');
			var prevision_tab_exist = Ext.query("*[id*=search-grid-prevision]");
			if(prevision_tab_exist.length > 0 && ARe.timelineRech_pos > 0)
				ARi.services.remove(ARe.timelineRech_pos);
            var tab = ARi.services.insert(ARapp.tabNumber, grid);
			ARe.timelineRech_pos = ARapp.tabNumber;
            //tab.show();
            store.load({
                params: {
                    start: 0,
                    limit: 14
                }
            });
            var requetePrevisionAll = "?previsionStartDate="+Ext.get("prevision-startDate").getValue()+"&previsionEndDate="+Ext.get("prevision-endDate").getValue()+"&quand="+quand;
            
            if(query != "")
            	requetePrevisionAll += "&query="+query;
            
    		if(rubric != "")
            	requetePrevisionAll += "&rubric="+rubric;
            
    		if(types != "")
            	requetePrevisionAll += "&types="+types;
            
    		if(note != "")
            	requetePrevisionAll += "&note="+note;
            
    		var ctId = new Date().format("YmdHis").toString() + (Math.round(Math.random() * 1000000)).toString();
            requetePrevisionAll += "&ctId="+ctId;
            
    		ARe.requeteSearchPrevision = requetePrevisionAll;
    		
    		var afficheListPrevision = Ext.query("*[id*=showListPrevision]");
    		Ext.get(afficheListPrevision).removeClass("icone-listtimeline");
        },
        
        qEvent: function(elem){
            ARh.home.hide();
            var ctId = new Date().format("YmdHis").toString() + (Math.round(Math.random() * 1000000)).toString();
            var query = Ext.get("event-keywords").getValue();
            if (query == eventQuoi.emptyText)
                query = "";
            
            var quand = eventQuand.getValue();
            if(quand == "")
            	quand = "start";
            
            var datesProgrammees = eventDatesProgrammees.getValue();
            if(datesProgrammees == "")
            	datesProgrammees = "1";
            if(datesProgrammees == 1){
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
            } else if(datesProgrammees == 2){
            	dFrom = new Date();
                if (dFrom.getDay() == 0)
                    dFrom.setDate(dFrom.getDate() + 1);
                else
                    dFrom.setDate(dFrom.getDate() - dFrom.getDay() + 8);
                dTo = dFrom.clone();
                dTo.setDate(dTo.getDate() + 6);                            
                dFromEvent.setValue(dFrom.format("Y-m-d"));
                dToEvent.setValue(dTo.format("Y-m-d"));
            } else if(datesProgrammees == 3){
            	dFrom = new Date();
                dFrom.setMonth(dFrom.getMonth() + 1);
                dFrom = dFrom.getFirstDateOfMonth();                            
                dTo = dFrom.getLastDateOfMonth();                            
                dFromEvent.setValue(dFrom.format("Y-m-d"));
                dToEvent.setValue(dTo.format("Y-m-d"));
            } else if(datesProgrammees == 4){
            	dFrom = new Date();
                dFrom.setMonth(dFrom.getMonth() + 1);
                dFrom = dFrom.getFirstDateOfMonth();
                dTo = new Date();
                dTo.setMonth(dTo.getMonth() + 3);
                dTo = dTo.getLastDateOfMonth();                            
                dFromEvent.setValue(dFrom.format("Y-m-d"));
                dToEvent.setValue(dTo.format("Y-m-d"));
            }
			
            var ville = "";
            if(Ext.get("event-villes").getValue() != eventVilles.emptyText)
            	ville = eventVilles.getValue();
            	
            performFiltering(tl, [0,1], query, quand, ville, false);
            
            var params = {
        		query: query,
        		quand: quand,
                eventStartDate: Ext.get("event-startDate").getValue(),
                eventEndDate: Ext.get("event-endDate").getValue(),
                classname: 'event',
                city: ville,
                ctId: ctId
            };
            var store = ARd._getDataStore('search', params);
            var grid = ARd._getGrid(store, 'search', 'search-grid-event' + ctId, elem);
			var event_tab_exist = Ext.query("*[id*=search-grid-event]");
			if(event_tab_exist.length > 0 && ARe.timelineRech_pos > 0)
				ARi.services.remove(ARe.timelineRech_pos);
            var tab = ARi.services.insert(ARapp.tabNumber, grid);
			ARe.timelineRech_pos = ARapp.tabNumber;
            //tab.show();
            store.load({
                params: {
                    start: 0,
                    limit: 14
                }
            });
            var requeteEventAll = "?eventStartDate="+Ext.get("event-startDate").getValue()+"&eventEndDate="+Ext.get("event-endDate").getValue()+"&quand="+quand;
            
            if(query != "")
            	requeteEventAll += "&query="+query;
            
    		if(ville != "")
            	requeteEventAll += "&city="+ville;
            
    		var ctId = new Date().format("YmdHis").toString() + (Math.round(Math.random() * 1000000)).toString();
            requeteEventAll += "&ctId="+ctId;
            
    		ARe.requeteSearchEvent = requeteEventAll;
    		
    		var afficheListEvent = Ext.query("*[id*=showListEvent]");
    		Ext.get(afficheListEvent).removeClass("icone-listtimeline");
        },
        
        qPrevisionRAZ: function(){
        	document.getElementById("prevision-keywords").value = "Mots clefs";
        	document.getElementById("prevision-keywords").className = "x-form-text x-form-field x-form-empty-field";
        	document.getElementById("prevision-quand").value = "Débutent";
        	document.getElementById("prevision-quand").className = "x-form-text x-form-field x-form-empty-field";
        	previsionQuand.clearValue();
        	document.getElementById("prevision-startDate").value = new Date().format("Y-m-d");
        	document.getElementById("prevision-startDate").className = "x-form-text x-form-field x-form-empty-field";
        	document.getElementById("prevision-endDate").value = new Date().add(Date.YEAR, +1).format("Y-m-d");
        	document.getElementById("prevision-endDate").className = "x-form-text x-form-field x-form-empty-field";
        	document.getElementById("prevision-piliers").value = "Tous les piliers";
        	document.getElementById("prevision-piliers").className = "x-form-text x-form-field x-form-empty-field";
        	previsionPiliers.clearValue();
        	document.getElementById("prevision-types").value = "Tous les types";
			document.getElementById("prevision-types").className = "x-form-text x-form-field x-form-empty-field";
			previsionTypes.clearValue();
        	document.getElementById("prevision-notes").value = "Toutes les notes";
			document.getElementById("prevision-notes").className = "x-form-text x-form-field x-form-empty-field";
			previsionNotes.clearValue();
        	var afficheToolbar = Ext.query("*[id*=secondToolBar]");
    		Ext.get(afficheToolbar[0]).addClass("toolbar-avancee");
    		var afficheListPrevision = Ext.query("*[id*=showListPrevision]");
    		Ext.get(afficheListPrevision).addClass("icone-listtimeline");
			//document.getElementById("nbPrevisionsAffichees").innerHTML = "Prévisions : "+tl.getBand(0).getEventSource().getCount();
        	performFiltering(tl, [0,1], "", "between", "", "", "", true);
			var prevision_tab_exist = Ext.query("*[id*=search-grid-prevision]");
			if(prevision_tab_exist.length > 0 && ARe.timelineRech_pos > 0)
				ARi.services.remove(ARe.timelineRech_pos);
        },
        
        qEventRAZ: function(){
        	document.getElementById("event-keywords").value = eventQuoi.emptyText;
        	document.getElementById("event-keywords").className = "x-form-text x-form-field x-form-empty-field";
        	document.getElementById("event-quand").value = eventQuand.emptyText;
        	document.getElementById("event-quand").className = "x-form-text x-form-field x-form-empty-field";
        	eventQuand.clearValue();
        	document.getElementById("event-datesProgrammees").value = eventDatesProgrammees.emptyText;
        	document.getElementById("event-datesProgrammees").className = "x-form-text x-form-field x-form-empty-field";
        	eventDatesProgrammees.clearValue();
        	document.getElementById("event-startDate").value = new Date().format("Y-m-d");
        	//document.getElementById("event-startDate").className = "x-form-text x-form-field x-form-empty-field";
        	document.getElementById("event-endDate").value = new Date().add(Date.YEAR, +1).format("Y-m-d");
        	//document.getElementById("event-endDate").className = "x-form-text x-form-field x-form-empty-field";
        	document.getElementById("event-villes").value = eventVilles.emptyText;
        	document.getElementById("event-villes").className = "x-form-text x-form-field x-form-empty-field";
        	eventVilles.clearValue();
        	var afficheListEvent = Ext.query("*[id*=showListEvent]");
    		Ext.get(afficheListEvent).addClass("icone-listtimeline");
        	performFiltering(tl, [0,1], "", "between", "", true);
        	var event_tab_exist = Ext.query("*[id*=search-grid-event]");
			if(event_tab_exist.length > 0 && ARe.timelineRech_pos > 0)
				ARi.services.remove(ARe.timelineRech_pos);
        },
        
        type: function(elem, classname){
            ARh.home.hide();
            var ctId = new Date().format("YmdHis").toString() + (Math.round(Math.random() * 1000000)).toString();
            var params = {
                classname: classname,
                accountPermission: false,
                ctId: ctId
            };
            if (classname == "prevision") {
                var today = new Date().format("Y-m-d");
                var tomorrow = new Date().add(Date.MONTH, 12).format("Y-m-d");
                
                var params = {
                    classname: classname,
                    previsionStartDate: today,
                    previsionEndDate: tomorrow,
                    quand: "between",
                    ctId: ctId
                };
            }
            if (classname == "event") {
                var today = new Date().format("Y-m-d");
                var tomorrow = new Date().add(Date.MONTH, 12).format("Y-m-d");
                
                var params = {
                    classname: classname,
                    eventStartDate: today,
                    eventEndDate: tomorrow,
                    quand: "between",
                    ctId: ctId
                };
            }
            
            var store = ARd._getDataStore('search', params);
            if (classname == "prevision")
                store.setDefaultSort('prevision_startdate', 'asc');
            if (classname == "event")
                store.setDefaultSort('event_startdate', 'asc');
            var grid = ARd._getGrid(store, 'search', 'search-grid-' + ctId, elem.substr(0, 15) + '...');
            var tab = ARi.services.insert(ARapp.tabNumber, grid);
            tab.show();
            store.load({
                params: {
                    start: 0,
                    limit: 14
                }
            });
			ARe.timelineRech_pos++;
        },
        
        f: function(title, params){
            ARh.home.hide();
            
            var ctId = new Date().format("YmdHis").toString() + (Math.round(Math.random() * 1000000)).toString();
            
            var store = ARd._getDataStore('search', params);
            var grid = ARd._getGrid(store, 'search', 'search-grid-' + ctId, title);
            var tab = ARi.services.insert(ARapp.tabNumber, grid);
            
            tab.show();
            store.load({
                params: {
                    start: 0,
                    limit: 14
                }
            });
			ARe.timelineRech_pos++;
        }
    },
    
    /* ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** **
     * Functions related to date formatting
     */
    DateFormat: function(els, format){
        var regEx = new RegExp("[-]", "g");
        
        for (i = 0; i < els.length; i++) {
            var aDate = els[i].innerHTML.split(regEx);
            var GMT0 = new Date(aDate[1] + "/" + aDate[0] + "/" + aDate[2]);
            var GMTdiff = 0;
            
            
            if (ARc.GMTIZE) {
                GMT0 = GMT0.add(Date.HOUR, GMTdiff)
            }
            
            els[i].innerHTML = GMT0.format(format);
        }
    },
    
    GMTize: function(els, format){
        var regEx = new RegExp("[-]", "g");
        
        for (i = 0; i < els.length; i++) {
            var tmlDate = els[i].innerHTML.split(regEx);
            els[i].innerHTML = new Date(tmlDate[1] + "/" + tmlDate[0] + "/" + tmlDate[2]).format(format);
        }
    },
    
    previewExport: function(exportRuleId){
        Ext.Ajax.request({
            url: '/scripts/exportPreview.php',
            /*
             failure: function(){
             //sb.setStatus("export : erreur");
             
             },*/
			success: function(){
                alert("export : ok");
            },
            params: {
                exportRuleId: exportRuleId,
                classname: ARe.previewed.classname,
                id: ARe.previewed.id
            }
        });
    },
    
    /* ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** **
     * Functions related to bin managment
     */
    bin: {
    
        launchExport: function(exportRuleId, binId){
        
            var sb = Ext.getCmp('bin-grid-' + binId + '-statusbar');
            sb.setStatus("export : en cours");
            
            Ext.Ajax.request({
                url: '/scripts/exportBin.php',
                success: function(){
                    sb.setStatus("export : ok");
                    alert("export : ok");
                },
                failure: function(){
                    sb.setStatus("export : erreur");
                    alert("export : erreur");
                },
                params: {
                    exportRuleId: exportRuleId,
                    binId: binId
                }
            });
        },
        
        print: function(itemId){
            ARh.home.hide();
            window.open('/print.php?classname=bin&id=' + itemId);
        },
        
        /**
         * Execute a request with params on the script and refresh the zone
         *
         * @param {Object} params
         */
        request: function(params, refresh){
            Ext.Ajax.request({
                url: '/scripts/bin.php',
                success: function(){
                    if (refresh) {
                        setTimeout("ARr.sidebar.bin()", "200");
                    }
                    if (params.gridId && params.action == 'addTo') {
                        var sb = Ext.getCmp(params.gridId + '-statusbar');
                        sb.clearStatus();
                    }
                },
                params: params
            });
        },
        
        _request: function(params, refresh){
            Ext.Ajax.request({
                url: '/scripts/bin.php',
                params: params
            });
        },
        
        create: function(name){
            ARe.bin._request({
                binName: name,
                action: 'create'
            }, true)
        },
        remove: function(id){
            ARe.bin.request({
                binId: id,
                action: 'remove'
            }, true);
			var grid = Ext.getCmp('bin-grid-' + id);
			ARi.services.remove(grid);
        },
        
        folderToBin: function(binId, gridId){
			var sb = Ext.getCmp(gridId + '-statusbar');
            sb.showBusy();
            sb.setStatus(ARl.LABEL_SB_ADDING_TO_BIN);
            items = ARd.getGridSelection(gridId);
            ARe.bin.request({
                items: items,
                binId: binId,
                gridId: gridId,
                action: 'addTo'
            }, false);
        },
        
        previewToBin: function(binId){
            if (ARe.previewed.classname && ARe.previewed.id) {
                ARe.bin.request({
                    items: ARe.previewed.classname + "_" + ARe.previewed.id,
                    binId: binId,
                    action: 'addTo'
                }, false);
            }
        },
        
        /*addTo: function(itemId, binId){
         var reg = new RegExp("[\\.\\-/\\\\]", "ig");
         res = itemId.split(reg);
         
         binId = binId.split("-");
         items = res[0] + "_" + res[6]
         
         ARe.bin.request({
         items: items,
         binId: binId[1],
         action: 'addTo'
         }, false)
         },*/
        addTo: function(items, binId){
            ARe.bin.request({
                items: items,
                binId: binId,
                action: 'addTo'
            }, false)
        },
        
        removeFrom: function(items, binId){
            ARe.bin.request({
                items: items,
                binId: binId,
                action: 'removeFrom'
            }, false);
        },
        
        clear: function(binId){
            ARe.bin.request({
                binId: binId,
                action: 'clear'
            }, false);
			var grid = Ext.getCmp('bin-grid-' + binId);
			ARi.services.remove(grid);
        },
        open: function(title, binId){
        
        
        
            var ctId = new Date().format("YmdHis").toString() + (Math.round(Math.random() * 1000000)).toString();
            var params = {
                binId: binId,
                action: "getData",
                ctId: ctId
            };
            
            if (!Ext.getCmp('bin-grid-' + binId)) {
                var store = ARd._getDataStore('bin', params);
                var grid = ARd._getGrid(store, 'bin', 'bin-grid-' + binId, title);
                var tab = ARi.services.insert(ARapp.tabNumber, grid);
                tab.show();
                store.load({
                    params: {
                        start: 0,
                        limit: 14
                    }
                });
                ARe.timelineRech_pos++;
            }
            else {
                var grid = Ext.getCmp('bin-grid-' + binId);
                grid.show();
                grid.getStore().load();
            }
        }
    },
    
    
    /* ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** **
     * Privates functions, only to be used by internal functions
     */
    tabCloseMenu: function(){
        var tabs, menu, ctxItem;
        this.init = function(tp){
            tabs = tp;
            tabs.on('contextmenu', onContextMenu);
        }
        
        function onContextMenu(ts, item, e){
            if (!menu) { // create context menu on first right click
                menu = new Ext.menu.Menu([{
                    id: tabs.id + '-close',
                    text: ARl.LABEL_CTXT_CLOSE_THIS_TAB,
                    handler: function(){
                        tabs.remove(ctxItem);
                    }
                }, {
                    id: tabs.id + '-close-others',
                    text: ARl.LABEL_CTXT_CLOSE_OTHERS_TABS,
                    handler: function(){
                        tabs.items.each(function(item){
                            if (item.closable && item != ctxItem) {
                                tabs.remove(item);
                            }
                        });
                    }
                }, {
                    id: tabs.id + '-close-all',
                    text: ARl.LABEL_CTXT_CLOSE_ALL_TABS,
                    handler: function(){
                        tabs.items.each(function(item){
                            if (item.closable) {
                                tabs.remove(item);
                            }
                        });
                    }
                }]);
            }
            ctxItem = item;
            var items = menu.items;
            items.get(tabs.id + '-close').setDisabled(!item.closable);
            var disableOthers = true;
            tabs.items.each(function(){
                if (this != item && this.closable) {
                    disableOthers = false;
                    return false;
                }
            });
            items.get(tabs.id + '-close-others').setDisabled(disableOthers);
            menu.showAt(e.getPoint());
        }
    },
    
    /**
     * Open a tab between the last service and the last temporary tab
     *
     * @param {Object} title 	The title of the tab
     * @param {Object} type		The type of the tab (search|folder|bin)
     * @param {Object} ctId		The container id inside the tab
     */
    _openTab: function(title, type, ctId){
        var tab = ARi.services.insert(ARapp.tabNumber, {
            title: title,
            iconCls: 'ari-tab-type-' + type,
            closable: true,
            autoScroll: true,
            html: String.format('<div id=\"{0}\"></div>', ctId)
        });
        return (tab);
    }
    
};
