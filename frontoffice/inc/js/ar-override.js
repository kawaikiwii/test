/**
 * File : ar-override.js (AFP/RELAX OverRide)
 * @author jy
 * @version 1.0.0
 *
 * Overwrite the default functions of Ext JS Library
 *
 */
Ext.BLANK_IMAGE_URL = '/rp/images/default/s.gif';

Ext.app.SearchField = Ext.extend(Ext.form.TwinTriggerField, {
    initComponent: function(){
        Ext.app.SearchField.superclass.initComponent.call(this);
        this.on('specialkey', function(f, e){
            if (e.getKey() == e.ENTER) {
                this.onTrigger2Click();
            }
        }, this);
    },
    
    validationEvent: false,
    validateOnBlur: false,
    trigger1Class: (Ext.isIE && !Ext.isIE7) ? 'x-form-clear-trigger ie-form-trigger' : 'x-form-clear-trigger',
	 trigger2Class: (Ext.isIE && !Ext.isIE7) ? 'x-form-search-trigger ie-form-trigger' : 'x-form-search-trigger',
	 hideTrigger1: true,
    width: 180,
    hasSearch: false,
    paramName: 'query',
    
    onTrigger1Click: function(){
        if (this.hasSearch) {
            this.el.dom.value = '';
            var o = {
                start: 0
            };
            this.triggers[0].hide();
            this.hasSearch = false;
        }
    },
    
    onTrigger2Click: function(){
        var v = this.getRawValue();
        if (v.length < 1) {
            this.onTrigger1Click();
            return;
        }
        var o = {
            start: 0
        };
        this.hasSearch = true;
        this.triggers[0].show();
        ARe.search.q(v);
    }
});

Ext.override(Ext.dd.DDProxy, {
    startDrag: function(x, y){
        var dragEl = Ext.get(this.getDragEl());
        var el = Ext.get(this.getEl());
        el.addClass("ari-draged");
        el.setOpacity(.8);
        Ext.select('.ari-bin').highlight();
    },
    
    onDragOver: function(e, targetId){
        if (ARe.dropZones.indexOf(targetId) > -1) {
            var target = Ext.get(targetId);
            this.lastTarget = target;
            target.setOpacity(.7);
            target.addClass("ari-dropzone");
        }
    },
    
    onDragOut: function(e, targetId){
        if (ARe.dropZones.indexOf(targetId) > -1) {
            var target = Ext.get(targetId);
            this.lastTarget = null;
            target.clearOpacity();
            target.removeClass("ari-dropzone");
        }
        
    },
    
    endDrag: function(){
        var dragEl = Ext.get(this.getDragEl());
        var el = Ext.get(this.getEl());
        el.removeClass("ari-draged");
        el.clearOpacity();
        if (this.lastTarget) {
            var elId = el.id.toString().replace("list-", "");
            var binId = this.lastTarget.id.split('-');
            ARe.bin.addTo(elId, binId[1]);
            this.lastTarget.clearOpacity();
            this.lastTarget.highlight();
            this.lastTarget.removeClass("ari-dropzone");
        }
    }
});
