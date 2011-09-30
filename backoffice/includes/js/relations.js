/**
 * This class manages searches made through the relationship builder
 */
relationSearch = {
    
    params: new Hash(),
    linkMgr: new Hash(),
    
    prepareSearch: function(argOptions)
    {
        options = {
            ajaxHandler: 'search/quickSearch',
            style: 'grid',
            uid: 'res',
            filter: '',
            offset: 0,
            pageSize: 10,
            engine: '',
            idPrefix: '',
            resultSet: 'resultset',
            kind: '',
            orderBy: '',
            pk: '',
            relclassname: '',
            relclassid: '',
            useLinkManager: true
        };
        
        if (argOptions != undefined) Object.extend(options, argOptions);
        
        if (relationSearch.linkMgr.get(options.uid) == undefined)
        {
            lmgr = new jsRelationManager({
                                pk: options.pk,
                                sourceElem: options.idPrefix + 'resultset',
                                destBox: options.idPrefix + 'relations',
                                relationList: options.idPrefix + 'list'
                                });
            relationSearch.linkMgr.set(options.uid, lmgr);
        }
        
        relationSearch.params.set(options.uid, options);
        
        if (!options.relclassname.empty() && !options.relclassid.empty()) 
        {
        	relmng = relationSearch.linkMgr.get(options.uid);
        	relmng.addRelationManual(options.relclassid, options.relclassname);
        }
    },
    
    orderBy: function(argUid, argOrderBy)
    {
        var options = relationSearch.params.get(argUid);
        options.orderBy = argOrderBy;
        options.currentPage = 1;
        relationSearch.doTheSearch(options.uid, options);
    },
    
    selectPage: function(argUid, argPage)
    {
        var options = relationSearch.params.get(argUid);
        options.currentPage = argPage;
        relationSearch.doTheSearch(options.uid, options);
    },
            
    doTheSearch: function(argUid, argOptions)
    {
        var options = argOptions;
        new Ajax.Updater($(argOptions.resultSet), wcmBaseURL + 'ajax/controller.php', {
            parameters: argOptions,
            onComplete: function() {
            	
                $(options.idPrefix+'sablier').hide();
                
                if (options.useLinkManager)
                {
                    lmgr = relationSearch.linkMgr.get(argOptions.uid);
                    lmgr.initDraggables().bind(lmgr);
                }
            },
            evalScripts: true
        });
    },    
    
    search: function(argUid, argQuery, argOptions)
    {
        var options = relationSearch.params.get(argUid);
        
        if (argOptions != undefined)
        {
            if (options != undefined)
            {
                Object.extend(options, argOptions);
            } else {
                options = argOptions;
            }
        } else {
            if (options == undefined) options = { };
        }
        
        if (options.query != argQuery) options.currentPage = 1;
        options.query = argQuery;
        
        relationSearch.params.set(options.uid, options);
        relationSearch.doTheSearch(options.uid, options);
    }
} 

/**
 * The relation manager class manages the draggable and sortable lists of the relation page
 */
jsRelationManager = Class.create();
jsRelationManager.prototype = {
    
    initialize: function (options)
    {
        this.initConfig(options);
        this.initSortableRelations();
        this.initDraggables();
        this.initDropDestination();
    },

    initConfig: function (options)
    {
        this.options = {
            objectsClass: 'bizobject',
            handle: 'toolbar',
            hoverClass: 'allowDrag',
            bizrelation: 'bizrelation',
            sourceElem: 'relation_resultset',
            destBox: 'relations',
            relationList: 'sortable_relations',
            fieldname: '_bizrel',
            pk: '_br_2'
        };
        if (options != undefined) Object.extend(this.options, options);
    },

    initSortableRelations: function()
    {
        Sortable.destroy(this.options.relationList);
        Position.includeScrollOffsets = true;
        Sortable.create(this.options.relationList, {
            format: /^(.*)$/, /* NSTEIN: format override to match our custom naming format */
            handle: 'toolbar',
            scroll: this.options.destBox});

        var _this = this;

        $$('#'+this.options.relationList+' .remove').each(function(removebutton) {
            removebutton.onclick = function(a) { _this.removeRelation(this); };
        }.bind(this));
    },

    initDraggables: function() 
    {
        $$('#'+this.options.sourceElem+' .'+this.options.objectsClass).each(function(bizobject) {
            
            new Draggable(bizobject,  {
                revert: true,
                reverteffect: function(element, top_offset, left_offset) { element.style.top = element.style.left = ''; }, // Rement a sa place
                ghosting: true,
                handle: this.options.searchHandle
            });         
    
            var _ref = bizobject;   
            var _this = this;

            bizobject.getElementsBySelector('.add')[0].onclick = function() { return  _this.addRelation(_ref); };
        }.bind(this));
    },

    initDropDestination: function()
    {
        Droppables.add(this.options.destBox, {
            accept: this.options.objectsClass,
            hoverclass: this.options.hoverClass,
            onDrop: this.addRelation.bind(this)
        });
        /**
         * TODO : Trouver une solution qui evite de caller cette ligne pour ne pas avoir le probleme de z-index
         * dans firefox et peut etre d'autres navigateurs. Il est possible que ce patch face bugger IE
         */
        $(this.options.destBox).style.position='';
    },


    addRelationManual: function(relatedId, relatedClass)
    {
    	var newId = 'rel-' + relatedClass + '-' + relatedId + '-' + this.options.pk;
        if ($(newId)) {
            alert($I18N.RELATION_ALREADY_EXISTS);
        } else {
            var li = document.createElement('li');
            li.id = newId;
            li.className = this.options.bizrelation;
            li.innerHTML = '<span class="wait">' + $I18N.LOADING + '</span>';
            
            $(this.options.relationList).appendChild(li);
            
            new Ajax.Updater(li, wcmBaseURL + 'ajax/wcm.ajaxRelationManager.php', {
                parameters: {
                    action: 'renderObject',
                    relatedClass: relatedClass,
                    relatedId: relatedId,
                    pk: this.options.pk
                },
                onComplete: function() { this.initSortableRelations(); }.bind(this)
            });
        }
        return false;
    },
    
    addRelation: function(elem)
    {
        var part = elem.id.split('-');
        
        return this.addRelationManual(part[1], part[0]);
    },

    removeRelation: function(elem)
    {
        var current = elem;
        while(current && !current.hasClassName(this.options.bizrelation) && (current = current.parentNode));
        current.parentNode.removeChild(current);
    },
    
    removeRelations: function()
    {
        var current = $(this.options.relationList).innerHTML = "";
    }
};

