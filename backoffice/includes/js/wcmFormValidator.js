/**
 * Project:     WCM
 * File:        wcmFormValidator.js
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.0
 *
 * @required Libraries:
 *
 * - prototype.js
 *
 * WCM Form Validator
 */

/**
 * This class helps validating a form by checking rules for each form's field
 * A rule is either an regular expression applied of the field value or a javascript function.
 * When a field value is invalid, the CSS class name of the upper 'li' tag will be changed
 */
if (!WCM) var WCM = {};
WCM.FormValidator = Class.create({
    /**
     * Initialize form validator
     *
     * @param form Form (or form ID) to observe
     * @param options Validator options
     *
     * Available options are:
     *      classRegExp:    Regexp used to determine type of field (default is '/type\-([a-z]+)/')
     *      requiredSuffix: suffix of class for required field (default is '-req')
     *      errorClass:     CSS class name applied when field is invalid (default is 'error')
     *      validClass:     CSS class name applied when field is valid (default is '')
     *      rules:          Type's rule hash table (key is 'type', value is a bool function or a regexp)
     *      autoObserve:    (bool) Automatically observe 'onBlur' event on form fields (default is true)
     *      debug:          (bool) Display (alert) on error or missing rules (default is false|0)
     */
    initialize: function(form, options) {
        this.options = {
            classRegExp: /type\-([a-z]+)/,
            requiredSuffix: '-req',
            errorClass: 'error',
            validClass: '',
            rules: {
                    int: /^[0-9]+$/,
                    float: /^[0-9]+\.[0-9]+$/,
                    time: /^([0-1][0-9]|2[0-3])\:[0-5][0-9](|\:[0-5][0-9])$/,
                    code: /^[a-z0-9_\-]+$/i,
                    filename: /^[a-z0-9_ \.]+$/i,
                    path: /^[a-z0-9_ \.\\\/]+$/i,
                    email: /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i,
                    url: /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/,
                    date: function(value) {
                            var re = /^\d{4}\-\d{2}\-\d{2}$/;
                            if (!re.test(value)) return false;
                            var dArr = value.split("-");
                            var d = new Date(dArr[1] + '/' + dArr[2] + '/' + dArr[0]);
                            return (d.getMonth() + 1 == dArr[1]
                                    && d.getDate() == dArr[2]
                                    && d.getFullYear() == dArr[0]);
                    },
                    datetime: function(value) {
                            var re = /^\d{4}\-\d{2}\-\d{2} ([0-1][0-9]|2[0-3])\:[0-5][0-9](|\:[0-5][0-9])$/;
                            if (!re.test(value)) return false;
                            var parts = value.split(' ');
                            var dArr = parts[0].split("-");
                            var d = new Date(dArr[1] + '/' + dArr[2] + '/' + dArr[0]);
                            return (d.getMonth() + 1 == dArr[1]
                                    && d.getDate() == dArr[2]
                                    && d.getFullYear() == dArr[0]);
                    }
            },
            autoObserve: 1,
            debug: 0
        };
        Object.extend(this.options, options || { });
        
        this.form = $(form);
        if (!this.form) {
            if (this.options.debug) {
                alert('Invalid form used for validation: ' + form);
            }
            return;
        }

        // start observing 'blur' event?
        if (this.options.autoObserve) {
            this.observeFields();
        }
    },

    /**
     * Observe 'onblur' event for each form field with valid class name
     * (see clasPrefix and requiredSuffix option entries)
     */
    observeFields: function() {
        this.stopObserving();
        var fv = this;
        this.form.getInputs().each(
            function(item) {
                if (fv.options.classRegExp.match(item.className)) {         
                    Event.observe(item, 'blur', function() {
                        fv.checkField(item);
                        });
                }
            });        
    },

    /**
     * Stop observing 'onblur' event for each form field with valid class name
     * (automatically called by observeFields() to avoid memory leak)
     */
    stopObserving: function() {
        var fv = this;
        this.form.getInputs().each(
            function(item) {
                if (fv.options.classRegExp.match(item.className)) {         
                    Event.stopObserving(item, 'blur', function() {
                        fv.checkField(item);
                        });
                }
            });        
    },
    
    /**
     * Check a field validity
     *
     * @param item The field item to check (an input field)
     *
     * @return bool TRUE if field content is valid
     */
    checkField: function(item) {
        // check required value
        var ok = (!item.className.endsWith(this.options.requiredSuffix) || 
                  (item.value != '' && item.value != null));
        if (ok)
        {
            if (item.value != '' && item.value != null)
            {
                // check reg-exp rule
                var result = this.options.classRegExp.exec(item.className);
                if (result)
                {
                    var rule = this.options.rules[result[1]];
                    if (rule != undefined)
                    {
                        if (Object.isFunction(rule))
                        {
                            // function rule found
                            ok = rule(item.value);
                        }
                        else if(rule)
                        {
                            // reg-exp rule found
                            ok = rule.match(item.value);
                        }
                    }
                }
            }
        }
        
        // change CSS class of upper 'li' (or of item if no 'li' was found)
        var elem = $(item).up('li');
        if (!elem) {
            elem = $(item);
        }
        elem.className = (ok) ? this.options.validClass : this.options.errorClass;
        
        return ok;
    },

    /**
     * Check all form's fields validity
     *
     * @return bool TRUE if all fields'content are valid
     */
    checkFields: function() {
        var fv = this;
        var ok = true;
        this.form.getInputs().each(
            function(item) {
                if (fv.options.classRegExp.match(item.className)) {
                    if (!fv.checkField(item)) {
                        ok = false;
                    }
                }
            });

        return ok;
    }
});