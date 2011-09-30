/**
 * Project:     WCM
 * File:        wcmActionController.js
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.0
 *
 * @required Libraries:
 *
 * - prototype.js
 * - wcmFormValidator.js
 *
 * WCM Action Controller
 */

/**
 * This class implement a javascript action controller for
 * the main form in WCM back-office.
 *
 * The action controller is able to invoke callbacks for specific
 * events such as 'save', 'delete', 'checkin', 'checkout', 'undocheckout',
 * 'lock', 'unlock' and 'reload'
 *
 * Use the method "registerCallback(event, callback)" to add your own callback.
 * A callback must return null on success or else an error message (a string)
 *
 * Also, the action controller will call a form validator to check fields validity
 * (@see wcmFormValidator.js for more info)
 */
if (!WCM) var WCM = {};
WCM.ActionController = Class.create({
    /**
     * Initializes the controller.
     */
    initialize: function() {
        this.callbacks = new Hash();
        this.lastError = null;
    },

    /**
     * Register a callback for a specifc event
     */    
    registerCallback: function(event, callback) {
        var cbList = this.callbacks.get(event);
        if (cbList == undefined) {
            cbList = new Array();
        }
        cbList.push(callback);
        this.callbacks.set(event, cbList);
    },

    /**
     * Trigger a specific event
     */    
    triggerEvent: function(event, parameters) {
        this.params = parameters;
        this.lastError = '';
        var actionCtl = this;
        var cbList = this.callbacks.get(event);
        if (cbList != undefined) {
            cbList.each(function(callback) {
                    var result = callback();
                    if (result != null) {
                        if(actionCtl.lastError.length > 0) actionCtl.lastError += '<br />';
                        actionCtl.lastError += result;
                    }
                });
        }
        if (this.lastError == '')
        {
            // force id to be explicitly set in the URL
            $('mainForm').action += '&id=' + $F('id');
            eval('actionCtl.on' + event.capitalize() + '()');
        }
        else
        {
            wcmMessage.error(this.lastError);
        }
    },

    /**
     * Validate the AJAX response returned by
     * ajax/bizlogic/executeAction.php
     */    
    validateResponse: function(response)
    {
        switch(response) {
            case 'ok':
                return true;
                
            case 'deleted':
                wcmMessage.error($I18N.OBJECT_IS_DELETED);
                break;

            case 'locked':
                wcmMessage.error($I18N.OBJECT_IS_LOCKED);
                break;

            case 'obsolete':
                wcmMessage.error($I18N.OBJECT_IS_OBSOLETE);
                this.onReload();
                break;

            default:
                wcmMessage.error($I18N.UNEXPECTED_ERROR + transport.responseText);
                break;                  
        }

        return false;
    },

    /**
     * On reload default callback
     */
    onReload: function()
    {
        // Explicit redirect (keep the query string)
        $('mainForm')._wcmTodo.value = 'view';
        $('mainForm').submit();
    },
    
    /**
     * On save default callback
     * - check form validity
     * - call optional callbacks
     * - check lock and obsolete status (ajax call)
     * - submit the form
     */
    onSave: function()
    {
        // check form validity
        var formValidator = new WCM.FormValidator('mainForm');
        if (!formValidator.checkFields()) {
            wcmMessage.error($I18N.INVALID_OR_MISSING_FIELDS);
            return;
        }
        
        var actionCtl = this;
        new Ajax.Request(wcmBaseURL + 'ajax/controller.php', {
                asynchronous: false,
                parameters: { ajaxHandler: 'bizlogic/executeAction', action: 'save' },
                onSuccess: function(transport) {
                    if (actionCtl.validateResponse(transport.responseText)) {
                        /// create version?
                        if (actionCtl.params.comment != undefined) {
                            $('mainForm').action += '&_comment=' + encodeURIComponent(actionCtl.params.comment);
                        }
                        // relaxnews update for object duplication 
                        if (actionCtl.params.clone != undefined) {
                            $('mainForm').action += '&clone=' + encodeURIComponent(actionCtl.params.clone);
                        }
						if (actionCtl.params.duplicateLanguage != undefined) {
							$('mainForm').action += '&duplicateLanguage=' + escape(actionCtl.params.duplicateLanguage);
						}
						$('mainForm')._wcmTodo.value = 'save';
                        $('mainForm').submit();
                    } else {
                        new Ajax.Updater($('objectMenu'), wcmBaseURL + 'ajax/controller.php',
                                { parameters: { ajaxHandler: 'bizlogic/renderObjectMenu' }});
                    }
                }
            });
    },

    /**
     * On save default callback
     * - check form validity
     * - call optional callbacks
     * - check lock and obsolete status (ajax call)
     * - submit the form
     */
    onCheckin: function()
    {
        // check form validity
        var formValidator = new WCM.FormValidator('mainForm');
        if (!formValidator.checkFields()) {
            formValidator.stopObserving();
            wcmMessage.error($I18N.INVALID_OR_MISSING_FIELDS);
            return;
        }
        formValidator.stopObserving();

        var actionCtl = this;
        new Ajax.Request(wcmBaseURL + 'ajax/controller.php', {
                asynchronous: false,
                parameters: { ajaxHandler: 'bizlogic/executeAction', action: 'checkin' },
                onSuccess: function(transport) {
                    if (actionCtl.validateResponse(transport.responseText)) {
                        // create version?
                        if (actionCtl.params.comment != undefined) {
                            $('mainForm').action += '&_comment=' + escape(this.params.comment);
                        }
                        $('mainForm')._wcmTodo.value = 'checkin';
                        $('mainForm').submit();
                    } else {
                        new Ajax.Updater($('objectMenu'), wcmBaseURL + 'ajax/controller.php',
                                { parameters: { ajaxHandler: 'bizlogic/renderObjectMenu' }});
                    }
                }
            });
    },
    
    /**
     * On checkout default callback
     * - check lock and obsolete status (ajax call)
     * - checkout the object
     */
    onCheckout: function()
    {
        var actionCtl = this;
        new Ajax.Request(wcmBaseURL + 'ajax/controller.php', {
                asynchronous: false,
                parameters: { ajaxHandler: 'bizlogic/executeAction', action: 'checkout' },
                onSuccess: function(transport) {
                    if (actionCtl.validateResponse(transport.responseText)) {
                        $('mainForm')._wcmTodo.value = 'checkout';
                        $('mainForm').submit();
                    } else {
                        new Ajax.Updater($('objectMenu'), wcmBaseURL + 'ajax/controller.php',
                                { parameters: { ajaxHandler: 'bizlogic/renderObjectMenu' }});
                    }
                }
            });
    },

    /**
     * On undocheckout default callback
     * - check lock and obsolete status (ajax call)
     * - unlock the object
     */
    onUndocheckout: function()
    {
        this.onUnlock();
        this.onReload();
    },
            
    /**
     * On lock default callback
     * - check lock and obsolete status (ajax call)
     * - lock the object
     */
    onLock: function()
    {
        var actionCtl = this;
        new Ajax.Request(wcmBaseURL + 'ajax/controller.php',
            {
                asynchronous: false,
                parameters: { ajaxHandler: 'bizlogic/executeAction', action: 'lock' },
                onSuccess: function(transport)
                {
                    // Validate response and refresh asset bar
                    actionCtl.validateResponse(transport.responseText);
                    new Ajax.Updater($('objectMenu'), wcmBaseURL + 'ajax/controller.php',
                            { parameters: { ajaxHandler: 'bizlogic/renderObjectMenu' }});
                }
            });
    },
    
    /**
     * On unlock default callback
     * - check lock and obsolete status (ajax call)
     * - unlock the object
     */
    onUnlock: function()
    {
        var actionCtl = this;
        new Ajax.Request(wcmBaseURL + 'ajax/controller.php',
            {
                asynchronous: false,
                parameters: { ajaxHandler: 'bizlogic/executeAction', action: 'unlock' },
                onSuccess: function(transport)
                {
                    // Validate response and refresh asset bar
                    actionCtl.validateResponse(transport.responseText);
                    new Ajax.Updater($('objectMenu'), wcmBaseURL + 'ajax/controller.php',
                            { parameters: { ajaxHandler: 'bizlogic/renderObjectMenu' }});
                }
            });
    },
    
    /**
     * On delete default callback
     * - check lock and obsolete status (ajax call)
     * - delete the object by submiting form
     */
    onDelete: function()
    {
        // confirm deletion
        wcmModal.confirm(
            $I18N.DELETE,
            $I18N.CONFIRM_DELETE_CURRENT_OBJECT,
            function(response) {
                if (response == 'YES') {
                    this.onDeleteConfirmed();
                }
            }.bind(this)
        );
    },

    /**
     * On delete default callback
     * - check lock and obsolete status (ajax call)
     * - delete the object by submiting form
     */
    onDeleteConfirmed: function()
    {
        var actionCtl = this;
        new Ajax.Request(wcmBaseURL + 'ajax/controller.php', {
                asynchronous: false,
                parameters: { ajaxHandler: 'bizlogic/executeAction', action: 'delete' },
                onSuccess: function(transport) {
                    if (actionCtl.validateResponse(transport.responseText)) {
                        $('mainForm')._wcmTodo.value = 'delete';
                        $('mainForm').submit();
                    } else {
                        new Ajax.Updater($('objectMenu'), wcmBaseURL + 'ajax/controller.php',
                                { parameters: { ajaxHandler: 'bizlogic/renderObjectMenu' }});
                    }
                }
            });
    },

    /**
     * On transition default callback
     * - check lock and obsolete status (ajax call)
     * - execute specific transition by submiting form
     */
    onTransition: function()
    {
        var actionCtl = this;
        new Ajax.Request(wcmBaseURL + 'ajax/controller.php', {
                asynchronous: false,
                parameters: { ajaxHandler: 'bizlogic/executeAction', action: 'transition' },
                onSuccess: function(transport) {
                    if (actionCtl.validateResponse(transport.responseText)) {
                        $('mainForm')._wcmTodo.value = 'transition';
                        $('mainForm').action +='&_wcmTransitionId=' + escape(actionCtl.params.transition);
                        $('mainForm').submit();
                    } else {
                        new Ajax.Updater($('objectMenu'), wcmBaseURL + 'ajax/controller.php',
                                { parameters: { ajaxHandler: 'bizlogic/renderObjectMenu' }});
                    }
                }
            });
    },

    /**
     * On restore default callback
     * - restore specific version (params contains version id)
     */
    onRestore: function()
    {
        $('mainForm')._wcmTodo.value = 'restoreVersion';
        $('mainForm').action += '&_versionId=' + escape(this.params.versionId);
        $('mainForm').submit();
    },


    /**
     * On rollback default callback
     * - rollback specific version (params contains version id)
     */
    onRollback: function()
    {
        $('mainForm')._wcmTodo.value = 'rollbackVersion';
        $('mainForm').action += '&_versionId=' + escape(this.params.versionId);
        $('mainForm').submit();
    },
	
	
	/**
     * On duplicate default callback
     * 
     */
    onDuplicate: function()
    {
        var actionCtl = this;
        new Ajax.Request(wcmBaseURL + 'ajax/controller.php', {
                asynchronous: false,
                parameters: { ajaxHandler: 'bizlogic/executeAction', action: 'duplicate' },
                onSuccess: function(transport) {
					$('mainForm')._wcmTodo.value = 'duplicate';
					$('mainForm').action += '&duplicateLanguage=' + escape(actionCtl.params.duplicateLanguage);
                    $('mainForm').submit();
                }
            });
		return;
    },
    /**
     * On createSlideshow default callback
     * 
     */
    onCreateslideshow: function()
    {
        var actionCtl = this;
        
        wcmModal.confirm(
            $I18N.CREATE,
            $I18N.CONFIRM_CREATE_SLIDESHOW,
            function(response) {
                if (response == 'YES') {
                	 new Ajax.Request(wcmBaseURL + 'ajax/controller.php', {
                         asynchronous: false,
                         parameters: { ajaxHandler: 'bizlogic/executeAction', action: 'createslideshow' },
                         onSuccess: function(transport) {
         					$('mainForm')._wcmTodo.value = 'createslideshow';
         					$('mainForm').submit();
                         }
                     });
         		return;
                }
            }.bind(this)
        );     
    }
});
