var debug = {
    container: null,
    cspan: null,
    msg: null,
    hideB: null,
    timer: null,

    init: function() {
        var _this = this;
        this.container = document.createElement('div');
        this.container.id = 'debug-container';

        this.msg = document.createElement('span');
        this.msg.id = 'debug-msg';

        this.hideB = document.createElement('a');
        this.hideB.id = 'debug-hide';
        this.hideB.innerHTML = 'X';
        this.hideB.onclick = function() {
            _this.hide();
        };

        this.container.appendChild(this.msg);
        this.container.appendChild(this.hideB);

        var body = document.getElementsByTagName('body')[0];
        body.insertBefore(this.container, body.firstChild);
    },
    
    show: function(txt) {
        if(this.container==null) this.init();
        else if(this.timer) clearTimeout(this.timer);
        var _this = this;
        this.msg.innerHTML = txt;
        Effect.Appear(this.container);
        this.timer = setTimeout(function() { _this.hide(); }, 4000);
    },

    hide: function() {
        if(this.timer) clearTimeout(this.timer) ;
        Effect.Fade(this.container);
    }
};


ZoneSet = Class.create();
ZoneSet.prototype = {


    initialize: function (bizClass, bizId, options)
    {
        this.widgets = [];
        this.bizClass = bizClass;
        this.bizId = bizId;
        this.setOptions(options);
        this.update();
    },

    setOptions: function (options) {
        this.options = {
            portal: 'portal',
            column: 'portal-column',
            block: 'block',
            content: 'content',
            handle: 'handle',
            hoverclass: 'block-hover',
            blocklist: 'module-list',
            blocklisthandle: 'module-list-handle',
            removeClass: 'block-remove',
            editSettingsClass: 'block-editsettings',
            numBlocks: 0,
            autoUpdate: false
        }

        if (options != undefined) Object.extend(this.options, options);
    },

    update: function () {
        this.updateSortables();
        this.updateObservers();
    },

    updateWidgetZone: function(zone) {
        $$('#' + zone.id + ' .' + this.options.block).each(
            function (block) {
                if(widget = (this.widgets[block['id']]))
                    widget.zone = zone['id'];
                else
                    this.widgets[block['id']] = new Widget(block['id'], this, zone['id']);
            }.bind(this));
    },

    updateSortables: function () {
        this.sortables = $$('#' + this.options.portal + ' .' + this.options.column);
        this.sortables.each(
            function (sortable) {
                this.makeSortable(sortable);
                this.updateWidgetZone(sortable);
           }.bind(this));
    },

    updateObservers: function () {
        var blockClass = this.options.block;
        var blocks = $$('#' + this.options.portal + ' .' + blockClass);
        blocks.each(
            function (block) {
                var removeList = $$('#' + block.id + ' .' + this.options.removeClass);
                if (removeList.length > 0) {
                    var removable = removeList[0];
                    if(!this.widgets[block.id].bindClose) {
                        this.widgets[block.id].bindClose = this.widgets[block.id].close.bind(this.widgets[block.id]);
                        Event.observe(removable, 'click', this.widgets[block.id].bindClose, false);
                    }
                }
            }.bind(this));
    },

    dbUpdate: function(container)
    {
        this.updateWidgetZone(container);

        var frm = document.createElement(frm);

        $$('#' + container.id + ' .' + this.options.block).each(
                    function (block) {
                        this.widgets[block.id].serializeForSubmit(frm);
                    }.bind(this));

        var request = {
                bizClass: this.bizClass,
                bizId: this.bizId,
                zoneName: container.id,
                zoneContent: Sortable.serialize(container),
                settings: Form.serialize(frm)
            };


        wcmSysAjaxController.call('wcm.ajaxZoneManager', request, null, {evalScripts:true});    
    },



    makeSortable: function(sortable)
    {
        options = {
            format: /^(.*)$/, /* NSTEIN: format override to match our custom naming format */
            containment: this.sortables,
            constraint: false,
            tag: 'div',
            only: this.options.block,
            dropOnEmpty: true,
            handle: this.options.handle,
            hoverclass: this.options.hoverclass,

            onUpdate: function (container) {
               if (container.id == this.options.blocklist)
                       return;
                if(this.options.autoUpdate)
                    this.dbUpdate(container);
            }.bind(this)
        };

        Sortable.create(sortable, options);
    },

    makeModule: function (title, body) {
        html = "";

        html += '<div class="' + this.options.handle + '">'
        html += '  <div class="' + this.options.removeClass + '"><span>x</span></div>';
        html +=    title;
        html += '</div><form id="'+title+'-settings"></form>';
        html += '<div class="content">';
        html += '  <div>' + body + '</div>';
        html += '</div>';
        return html;
    },

    addModule: function (index, zone) {
        if (index == null || index == '') return;
        if (zone == null || zone == '') return;

        var options = this.options;
        var numBlocks = options.numBlocks;
        var block = document.createElement('div');
        var module = modules[index].code;

        block.className = options.block;
    
        while(typeof(this.widgets[module + '-' + numBlocks]) != 'undefined') numBlocks++;

        block.id = module + '-' + numBlocks;
        block.title = modules[index].title;
        block.innerHTML = this.makeModule(block.id, modules[index].html);

        $(zone).appendChild(block);

        options.numBlocks = ++numBlocks;
        this.widgets[block.id] = new Widget(block.id, this, zone);
        this.widgets[block.id].ajaxCall('updateWidget', {divid: block.id, widgetClass: modules[index].code});
    },

    serializeForSubmit: function(frm, name) {
        var options = this.options;
        var zones = Element.getElementsBySelector(options.portal, '.' + options.column);
        var content = [];
        
        var input = document.createElement('input');
        input.type = 'hidden';
        input.value = '';
        input.name = name;

        zones.each(
            function (zone) {
                if (zone.id != options.blocklist) {
                    var s = Sortable.serialize(zone.id);
                    if(s.length > 0) 
                        input.value += s + '&';
                    else 
                        input.value += zone.id + '[]=&';
                }
            });

        input.value = input.value.substring(0, input.value.length - 1);
        frm.appendChild(input);

        this.sortables.each(
            function (sortable) {
                $$('#' + sortable.id + ' .' + this.options.block).each(
                    function (block) {
                        this.widgets[block.id].serializeForSubmit(frm);
                    }.bind(this));
            }.bind(this));
    
    },

    getWidget: function(name)
    {
        return this.widgets[name];
    }

};

Widget = Class.create();
Widget.prototype = {

        initialize: function(name, zoneset, zone)
        {
           this.name = name;  // Id de la div du Widget
           var tmp = name.split('-');
           this.classname = tmp[0]; // Class de la widget
           this.guid = tmp[1]; // GUID de la widget
           this.zoneset = zoneset; // ZoneSet de la page
           this.zone = zone; // Nom de la zone qui contient notre widget
           if(typeof(wcmWidget)!='undefined') {
               this.widgetCtl = new wcmWidget(this.name);
               this.initCtl();
           }
        },

        initCtl: function()
        {
            this.widgetCtl.init();
        },

        saveSettings: function()
        {
            this.ajaxCall('saveSettings', {settings: this.getSettings()});
            this.displaySettings();
            return false;
        },

        applySettings: function() 
        {
            this.ajaxCall('applySettings', {settings: this.getSettings()});
            return false;
        },

        getSettings: function() 
        {
            var form = $(this.name+'-settings');
            if(form)
                    return Form.serialize(this.name+'-settings');
            else
                    return '';
        },

        cancelSettings: function() {
            this.ajaxCall('cancelSettings');
            this.displaySettings();
            return false;
        },

        saveData: function()
        {
            if(this.widgetCtl)
            {
                var context = this.widgetCtl.changes.toQueryString();
                this.ajaxCall('saveData', {context: context});
            }
        },

        refreshData: function()
        {
            this.ajaxCall('refreshData');
        },

        ajaxCall: function(action, opt) {
            var request = {
                bizClass: this.zoneset.bizClass,
                bizId: this.zoneset.bizId,
                widgetClass: this.classname,
                guid: this.guid,
                zoneName: this.zone,
                action: action
            };
            Object.extend(request, opt);
            wcmSysAjaxController.call('wcm.ajaxWidgetManager', request, null, {evalScripts:true});
        },

        serializeForSubmit: function(frm, suffix)
        {
            suffix = suffix || 'settings';
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = this.name + '-' + suffix;
            input.value = this.getSettings();
            frm.appendChild(input);
        },

        displaySettings: function()
        {
            var div = $(this.name+'-settings').parentNode;
            div.style.display = (div.style.display=='block')?'none':'block';
        },

        close: function()
        {
            this.zoneset.widgets[this.name] = null;
            if(elem = $(this.name))
                elem.parentNode.removeChild(elem);
            if(this.zoneset.options.autoUpdate == true)
                    this.zoneset.dbUpdate($(this.zone));
        }
};


