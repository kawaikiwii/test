/*
 * Project:     NCM
 * File:        wcmAjaxController.js
 *
 * @copyright   (c)2007 Nstein Technologies
 * @version     4.x
 *
 */

/*
 * Required Libraries:
 *
 * - prototype.js
 * - rico.js
 *
 */

/**
 * Ajax Controller
 *
 * Thin wrapper around Prototype and Rico that provides:
 *
 * - a single JavaScript entry point for all PHP Ajax request
 *   handlers; and
 *
 * - reasonable default values for Ajax request options.
 *
 */
var NcmAjaxController = Class.create();
NcmAjaxController.prototype = {
    /**
     * Initializes the controller.
     *
     * @param string name the name of the Ajax request handler to register
     *
     * @param string baseUrl the base URL of the PHP controller and
     *                       associated PHP handlers
     *
     * @param string controller the path of the PHP controller
     *                          relative to the base URL without the
     *                          '.php' extension
     *
     * @param object options default Ajax request options,
     *                       eg. { foo: bar, hey: there } (optional)
     *
     */
    initialize: function(name, baseUrl, controller, options) {
        this.name       = name;
        this.baseUrl    = baseUrl;
        this.controller = baseUrl + '/' + controller + '.php';

        this.defaultOptions = {
            asynchronous: false,
            method:       'post',
            parameters:   {}
        };        

        if (options != undefined && options != null) {
            Object.extend(this.defaultOptions, options);
        }

        ajaxEngine.registerRequest(this.name, this.controller);
    },

    /**
     * Performs a redirection to a given PHP handler.
     *
     * @param string handler the path of the PHP handler relative to
     *                       the base URL without the '.php' extension
     *
     * @param object parameters request query parameters,
     *                          eg. { foo: bar, hey: there } (optional)
     *
     * @param string callback the name of a validating JavaScript
     *                        function to call back with the query parameters (optional)
     */
    redirect: function(handler, parameters, callback) {
        if (this._invokeCallback(callback, parameters)) {
            var queryParameters = this._queryParameters(handler, parameters);
            window.location.href = this.controller + '?' + $H(queryParameters).toQueryString();
        }
    },

    /**
     * Dispatches an Ajax request to a given PHP handler.
     *
     * @param string handler the path of the PHP handler relative to
     *                       the back URL without the '.php' extension
     *
     * @param object parameters request query parameters,
     *                          eg. { foo: bar, hey: there } (optional)
     *
     * @param string callback the name of a validating JavaScript
     *                        function to call back with the query parameters (optional)
     *
     * @param object options Ajax request options,
     *                       eg. { foo: bar, hey: there } (optional)
     *
     */
    call: function(handler, parameters, callback, options) {
        if (this._invokeCallback(callback, parameters)) {
            ajaxEngine.sendRequest(this.name, this._ajaxOptions(handler, parameters, options));
        }
    },

    /**
     * Dispatches an Ajax request to a given PHP handler without any item
     * update - ie. bypasses Rico. Hence, the 'onComplete' Ajax option
     * should be specified in order to do something with the Ajax response.
     *
     * @param string handler the path of the PHP handler relative to
     *                       the back URL without the '.php' extension
     *
     * @param object parameters request query parameters,
     *                          eg. { foo: bar, hey: there } (optional)
     *
     * @param string callback the name of a validating JavaScript
     *                        function to call back with the query parameters (optional)
     *
     * @param object options Ajax request options,
     *                       eg. { foo: bar, hey: there } (optional)
     *
     */
    callWithoutUpdate: function(handler, parameters, callback, options) {
        if (this._invokeCallback(callback, parameters)) {
            new Ajax.Request(this.controller, this._ajaxOptions(handler, parameters, options));        
        }
    },

    /**
     * Dispatches an Ajax request to a given PHP handler with an
     * element update, bypassing Rico.
     *
     * @param string handler the path of the PHP handler relative to
     *                       the back URL without the '.php' extension
     *
     * @param string element the ID of the element to update
     *
     * @param object parameters request query parameters,
     *                          eg. { foo: bar, hey: there } (optional)
     *
     * @param string callback the name of a validating JavaScript
     *                        function to call back with the query parameters (optional)
     *
     * @param object options Ajax request options,
     *                       eg. { foo: bar, hey: there } (optional)
     *
     */
    update: function(handler, element, parameters, callback, options) {
        if (this._invokeCallback(callback, parameters)) {
        var ajaxOptions = this._ajaxOptions(handler, parameters, options);
            new Ajax.Updater(element, this.controller, ajaxOptions);        
        }
    },

    /**
     * Dispatches an Ajax request to a given PHP handler with a
     * periodical element update, bypassing Rico.
     *
     * @param string handler the path of the PHP handler relative to
     *                       the back URL without the '.php' extension
     *
     * @param string element the ID of the element to update
     *
     * @param object parameters request query parameters,
     *                          eg. { foo: bar, hey: there } (optional)
     *
     * @param string callback the name of a validating JavaScript
     *                        function to call back with the query parameters (optional)
     *
     * @param object options Ajax request options,
     *                       eg. { foo: bar, hey: there } (optional)
     *
     */
    updatePeridocially: function(handler, element, parameters, callback, options) {
        if (this._invokeCallback(callback, parameters)) {
        var ajaxOptions = this._ajaxOptions(handler, parameters, options);
           return new Ajax.PeriodicalUpdater(element, this.controller, ajaxOptions);        
        }
    },

    /**
     * Submits a given form to a PHP handler.
     *
     * Essentially, serializes the form parameters, extends them with
     * the given parameters (if any), and invokes 'this.call()'.
     *
     * @param string handler the path of the PHP handler relative to
     *                       the base URL without the '.php' extension
     *
     * @param string form the name of the form to submit
     *
     * @param object parameters request query parameters
     *                          eg. { foo: bar, hey: there } (optional)
     *
     * @param string callback the name of a validating JavaScript
     *                        function to call back with the query parameters (optional)
     *
     * @param object options Ajax request options,
     *                       eg. { foo: bar, hey: there } (optional)
     *
     */
    submit: function(handler, form, parameters, callback, options) {
        var formParameters = Form.serialize(form, true);

        if (parameters != undefined && parameters != null) {
            Object.extend(formParameters, parameters);
        }

        this.call(handler, formParameters, callback, options);
    },

    _queryParameters: function (handler, parameters) {
        var queryParameters = {};

        Object.extend(queryParameters, { ajaxHandler: handler });

        if (parameters != undefined && parameters != null) {
            Object.extend(queryParameters, parameters);
        }

        return queryParameters;
    },

    _ajaxOptions: function(handler, parameters, options) {
        var defaultOptions = this.defaultOptions;

        var ajaxOptions = {
            asynchronous: defaultOptions.asynchronous,
            method:       defaultOptions.method,
            parameters:   Object.extend({}, defaultOptions.parameters)
        };

        Object.extend(ajaxOptions.parameters, this._queryParameters(handler, parameters));
        ajaxOptions.parameters = $H(ajaxOptions.parameters).toQueryString();
        
        if (options != undefined && options != null) {
            Object.extend(ajaxOptions, options);
        }
        return ajaxOptions;
    },

    _invokeCallback: function(callback, parameters) {
        if (callback != undefined && callback != null) {
            var callbackFunc = window[callback];

            if (callbackFunc != undefined && callbackFunc != null) {
                return callbackFunc.apply(parameters);
            }
        }

        return true;
    }
};
