/**
  *
  *  Copyright 2005 Sabre Airline Solutions
  *
  *  Licensed under the Apache License, Version 2.0 (the "License"); you may not use this
  *  file except in compliance with the License. You may obtain a copy of the License at
  *
  *         http://www.apache.org/licenses/LICENSE-2.0
  *
  *  Unless required by applicable law or agreed to in writing, software distributed under the
  *  License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND,
  *  either express or implied. See the License for the specific language governing permissions
  *  and limitations under the License.
  **/

var debugAjax;

//-------------------- rico.js
var Rico = {
  Version: '1.1.0',
  prototypeVersion: parseFloat(Prototype.Version.split(".")[0] + "." + Prototype.Version.split(".")[1])
}

if((typeof Prototype=='undefined') || Rico.prototypeVersion < 1.3)
      throw("Rico requires the Prototype JavaScript framework >= 1.3");

Rico.ArrayExtensions = new Array();

    /********* Component ART .NET Compatibility ***********/
    /********* ZH : 28/02/2007 ***************/
    /*
if (Object.prototype.extend) {
   Rico.ArrayExtensions[ Rico.ArrayExtensions.length ] = Object.prototype.extend;
}else{
  Object.prototype.extend = function(object) {
    return Object.extend.apply(this, [this, object]);
  }
  Rico.ArrayExtensions[ Rico.ArrayExtensions.length ] = Object.prototype.extend;
}
    */
    /******** End Component ART .NET Compatibility  *******/

if (Array.prototype.push) {
   Rico.ArrayExtensions[ Rico.ArrayExtensions.length ] = Array.prototype.push;
}

if (!Array.prototype.remove) {
   Array.prototype.remove = function(dx) {
      if( isNaN(dx) || dx > this.length )
         return false;
      for( var i=0,n=0; i<this.length; i++ )
         if( i != dx )
            this[n++]=this[i];
      this.length-=1;
   };
  Rico.ArrayExtensions[ Rico.ArrayExtensions.length ] = Array.prototype.remove;
}

if (!Array.prototype.removeItem) {
   Array.prototype.removeItem = function(item) {
      for ( var i = 0 ; i < this.length ; i++ )
         if ( this[i] == item ) {
            this.remove(i);
            break;
         }
   };
  Rico.ArrayExtensions[ Rico.ArrayExtensions.length ] = Array.prototype.removeItem;
}

if (!Array.prototype.indices) {
   Array.prototype.indices = function() {
      var indexArray = new Array();
      for ( index in this ) {
         var ignoreThis = false;
         for ( var i = 0 ; i < Rico.ArrayExtensions.length ; i++ ) {
            if ( this[index] == Rico.ArrayExtensions[i] ) {
               ignoreThis = true;
               break;
            }
         }
         if ( !ignoreThis )
            indexArray[ indexArray.length ] = index;
      }
      return indexArray;
   }
  Rico.ArrayExtensions[ Rico.ArrayExtensions.length ] = Array.prototype.indices;
}

// Create the loadXML method and xml getter for Mozilla
if ( window.DOMParser &&
      window.XMLSerializer &&
      window.Node && Node.prototype && Node.prototype.__defineGetter__ ) {

   if (!Document.prototype.loadXML) {
      Document.prototype.loadXML = function (s) {
         var doc2 = (new DOMParser()).parseFromString(s, "text/xml");
         while (this.hasChildNodes())
            this.removeChild(this.lastChild);

         for (var i = 0; i < doc2.childNodes.length; i++) {
            this.appendChild(this.importNode(doc2.childNodes[i], true));
         }
      };
    }

    Document.prototype.__defineGetter__( "xml",
       function () {
           return (new XMLSerializer()).serializeToString(this);
       }
     );
}

document.getElementsByTagAndClassName = function(tagName, className) {
  if ( tagName == null )
     tagName = '*';

  var children = document.getElementsByTagName(tagName) || document.all;
  var elements = new Array();

  if ( className == null )
    return children;

  for (var i = 0; i < children.length; i++) {
    var child = children[i];
    var classNames = child.className.split(' ');
    for (var j = 0; j < classNames.length; j++) {
      if (classNames[j] == className) {
        elements.push(child);
        break;
      }
    }
  }

  return elements;
}

//-------------------- ricoAjaxEngine.js
Rico.AjaxEngine = Class.create();

Rico.AjaxEngine.prototype = {

   initialize: function() {
      this.ajaxElements = new Array();
      this.ajaxObjects  = new Array();
      this.requestURLS  = new Array();
      this.options = {};
   },

   registerAjaxElement: function( anId, anElement ) {
      if ( !anElement )
         anElement = $(anId);
      this.ajaxElements[anId] = anElement;
   },

   registerAjaxObject: function( anId, anObject ) {
      this.ajaxObjects[anId] = anObject;
   },

   registerRequest: function (requestLogicalName, requestURL) {
      this.requestURLS[requestLogicalName] = requestURL;
   },

   sendRequest: function(requestName, options) {
      // Allow for backwards Compatibility
      if ( arguments.length >= 2 )
      if (typeof arguments[1] == 'string')
         options = {parameters: this._createQueryString(arguments, 1)};
      this.sendRequestWithData(requestName, null, options);
   },

   sendRequestWithData: function(requestName, xmlDocument, options) {
      var requestURL = this.requestURLS[requestName];
      if ( requestURL == null )
         return;

      // Allow for backwards Compatibility
      if ( arguments.length >= 3 )
        if (typeof arguments[2] == 'string')
          options.parameters = this._createQueryString(arguments, 2);

      new Ajax.Request(requestURL, this._requestOptions(options,xmlDocument));

      if (debugAjax)
        window.open(requestURL + '?' + options.parameters);
   },

   sendRequestAndUpdate: function(requestName,container,options) {
      // Allow for backwards Compatibility
      if ( arguments.length >= 3 )
        if (typeof arguments[2] == 'string')
          options.parameters = this._createQueryString(arguments, 2);

      this.sendRequestWithDataAndUpdate(requestName, null, container, options);
   },

   sendRequestWithDataAndUpdate: function(requestName,xmlDocument,container,options) {
      var requestURL = this.requestURLS[requestName];
      if ( requestURL == null )
         return;

      // Allow for backwards Compatibility
      if ( arguments.length >= 4 )
        if (typeof arguments[3] == 'string')
          options.parameters = this._createQueryString(arguments, 3);

      var updaterOptions = this._requestOptions(options,xmlDocument);

      new Ajax.Updater(container, requestURL, updaterOptions);
   },

   // Private -- not part of intended engine API --------------------------------------------------------------------

   _requestOptions: function(options,xmlDoc) {
      var requestHeaders = ['X-Rico-Version', Rico.Version ];
      var sendMethod = 'post';
      if ( xmlDoc == null )
        if (Rico.prototypeVersion < 1.4)
        requestHeaders.push( 'Content-type', 'text/xml' );
      else
          sendMethod = 'get';
      (!options) ? options = {} : '';

      if (!options._RicoOptionsProcessed){
      // Check and keep any user onComplete functions
        if (options.onComplete)
             options.onRicoComplete = options.onComplete;
        // Fix onComplete
        if (options.overrideOnComplete)
          options.onComplete = options.overrideOnComplete;
        else
          options.onComplete = this._onRequestComplete.bind(this);
        options._RicoOptionsProcessed = true;
      }

     // Set the default options and extend with any user options
     this.options = {
                     requestHeaders: requestHeaders,
                     parameters:     options.parameters,
                     postBody:       xmlDoc,
                     method:         sendMethod,
                     onComplete:     options.onComplete
                    };
     // Set any user options:
     Object.extend(this.options, options);
     return this.options;
   },

   _createQueryString: function( theArgs, offset ) {
      var queryString = ""
      for ( var i = offset ; i < theArgs.length ; i++ ) {
          if ( i != offset )
            queryString += "&";

          var anArg = theArgs[i];

          if ( anArg.name != undefined && anArg.value != undefined ) {
            queryString += anArg.name +  "=" + encodeURIComponent(anArg.value);
          }
          else {
             var ePos  = anArg.indexOf('=');
             var argName  = anArg.substring( 0, ePos );
             var argValue = anArg.substring( ePos + 1 );
             // FIXME This fix added by Alex Dowgailenko @ nStein. Not offical RICO code.
             // Original was:
             // queryString += argName + "=" + escape(argValue);
             // escape() is not UTF8 friendly.
             // see also line 257
             queryString += argName + "=" + encodeURIComponent(argValue);
          }
      }
      return queryString;
   },

   _onRequestComplete : function(request) {
      if(!request)
          return;
      // User can set an onFailure option - which will be called by prototype
      if (request.status != 200)
        return;

      var response = request.responseXML.getElementsByTagName("ajax-response");
      if (response == null || response.length != 1)
         return;
      this._processAjaxResponse( response[0].childNodes );
      
      // Check if user has set a onComplete function
      var onRicoComplete = this.options.onRicoComplete;
      if (onRicoComplete != null)
          onRicoComplete();
   },

   _processAjaxResponse: function( xmlResponseElements )
   {
        for ( var i = 0 ; i < xmlResponseElements.length ; i++ )
        {
            var responseElement = xmlResponseElements[i];

            // only process nodes of type element.....
            if ( responseElement.nodeType != 1 )
                continue;

            var responseType = responseElement.getAttribute("type");
            var responseId   = responseElement.getAttribute("id");

            /******************************************
            ** Eurocortex - Jean-Michel Texier
            **
            ** Changes  : Add "item" response type
            ** See Also : _processAjaxItemUpdate()
            **
            ******************************************/
            switch(responseType)
            {
                case "object":
                    this._processAjaxObjectUpdate( this.ajaxObjects[ responseId ], responseElement );
                    break;

                case "element":
                    this._processAjaxElementUpdate( this.ajaxElements[ responseId ], responseElement );
                    break;

                case "item":
                    this._processAjaxItemUpdate( responseId, responseElement );
                    break;

				case "javascript":
					this._processAjaxJavaScript(responseElement);
					break;

                default:
                    alert('unrecognized AjaxResponse type : ' + responseType );
            }
        }
    },

    /******************************************
    ** Eurocortex - Jean-Michel Texier
    **
    ** Added    : _processAjaxItemUpdate()
    **
    ******************************************/
   _processAjaxItemUpdate: function(ajaxId, responseElement)
   {
        // Update innerHTML (or value) of a specific item (identified by its 'id')
        var item = $(ajaxId);
        if (item)
        {
            if (item.value != undefined)
                item.value = RicoUtil.getContentAsString(responseElement);
            else if (item.innerHTML != undefined) {
                item.innerHTML = RicoUtil.getContentAsString(responseElement);
				
				var scripts = item.getElementsByTagName('script');
				for (var i=0; i<scripts.length; i++) {
					if(!scripts[i].getAttribute('src')) {
						eval(scripts[i].innerHTML);
					}
				}
	        }
		}	
   },

    /******************************************
    ** Nstein - Pierrick Charron
    **
    ** Added    : _processAjaxJavaScript()
    **
    ******************************************/
   _processAjaxJavaScript: function(responseElement)
   {
   		eval(RicoUtil.getContentAsString(responseElement));
   },


   _processAjaxObjectUpdate: function( ajaxObject, responseElement ) {
      ajaxObject.ajaxUpdate( responseElement );
   },

   _processAjaxElementUpdate: function( ajaxElement, responseElement ) {
      ajaxElement.innerHTML = RicoUtil.getContentAsString(responseElement);
   }

}

var ajaxEngine = new Rico.AjaxEngine();


//-------------------- ricoUtil.js
Rico.ArrayExtensions = new Array();

    /********* Component ART .NET Compatibility ***********/
    /********* ZH : 28/02/2007 ***************/
    /*
if (Object.prototype.extend) {
   // in prototype.js...
   Rico.ArrayExtensions[ Rico.ArrayExtensions.length ] = Object.prototype.extend;
}else{
  Object.prototype.extend = function(object) {
    return Object.extend.apply(this, [this, object]);
  }
  Rico.ArrayExtensions[ Rico.ArrayExtensions.length ] = Object.prototype.extend;
}
    */
    /** END Component ART .NET Compatibility ****/

if (Array.prototype.push) {
   // in prototype.js...
   Rico.ArrayExtensions[ Rico.ArrayExtensions.length ] = Array.prototype.push;
}

if (!Array.prototype.remove) {
   Array.prototype.remove = function(dx) {
      if( isNaN(dx) || dx > this.length )
         return false;
      for( var i=0,n=0; i<this.length; i++ )
         if( i != dx )
            this[n++]=this[i];
      this.length-=1;
   };
  Rico.ArrayExtensions[ Rico.ArrayExtensions.length ] = Array.prototype.remove;
}

if (!Array.prototype.removeItem) {
   Array.prototype.removeItem = function(item) {
      for ( var i = 0 ; i < this.length ; i++ )
         if ( this[i] == item ) {
            this.remove(i);
            break;
         }
   };
  Rico.ArrayExtensions[ Rico.ArrayExtensions.length ] = Array.prototype.removeItem;
}

if (!Array.prototype.indices) {
   Array.prototype.indices = function() {
      var indexArray = new Array();
      for ( index in this ) {
         var ignoreThis = false;
         for ( var i = 0 ; i < Rico.ArrayExtensions.length ; i++ ) {
            if ( this[index] == Rico.ArrayExtensions[i] ) {
               ignoreThis = true;
               break;
            }
         }
         if ( !ignoreThis )
            indexArray[ indexArray.length ] = index;
      }
      return indexArray;
   }
  Rico.ArrayExtensions[ Rico.ArrayExtensions.length ] = Array.prototype.indices;
}

  Rico.ArrayExtensions[ Rico.ArrayExtensions.length ] = Array.prototype.unique;
  Rico.ArrayExtensions[ Rico.ArrayExtensions.length ] = Array.prototype.inArray;





// Create the loadXML method and xml getter for Mozilla
if ( window.DOMParser &&
      window.XMLSerializer &&
      window.Node && Node.prototype && Node.prototype.__defineGetter__ ) {

   if (!Document.prototype.loadXML) {
      Document.prototype.loadXML = function (s) {
         var doc2 = (new DOMParser()).parseFromString(s, "text/xml");
         while (this.hasChildNodes())
            this.removeChild(this.lastChild);

         for (var i = 0; i < doc2.childNodes.length; i++) {
            this.appendChild(this.importNode(doc2.childNodes[i], true));
         }
      };
    }

    Document.prototype.__defineGetter__( "xml",
       function () {
           return (new XMLSerializer()).serializeToString(this);
       }
     );
}

document.getElementsByTagAndClassName = function(tagName, className) {
  if ( tagName == null )
     tagName = '*';

  var children = document.getElementsByTagName(tagName) || document.all;
  var elements = new Array();

  if ( className == null )
    return children;

  for (var i = 0; i < children.length; i++) {
    var child = children[i];
    var classNames = child.className.split(' ');
    for (var j = 0; j < classNames.length; j++) {
      if (classNames[j] == className) {
        elements.push(child);
        break;
      }
    }
  }

  return elements;
}

var RicoUtil = {

    /********* SAFARI Compatibility ***********/
    /********* BDP : 28/02/2007 ***************/
        innerXML: function(node){
           //   alert("innerXML");
                    if(node.innerXML){
                    return node.innerXML;
                    }else if(typeof XMLSerializer != undefined){
                    var ret = (new XMLSerializer()).serializeToString(node);
                        if (ret == undefined)
                            return RicoUtil._getElementAsStringGeneric(node);
                        else
                            return ret;
                        }
                },

            _innerXMLGeneric: function(node){
                //alert("_innerXMLGeneric");
            var _result = "";
            if (node == null) { return _result; }
            for (var i = 0; i < node.childNodes.length; i++) {
                var thisNode = node.childNodes[i];
                switch (thisNode.nodeType) {
                    case 1: // ELEMENT_NODE
                    case 5: // ENTITY_REFERENCE_NODE
                        _result += RicoUtil._getElementAsStringGeneric(thisNode);
                        break;
                    case 3: // TEXT_NODE
                    case 2: // ATTRIBUTE_NODE
                    case 4: // CDATA_SECTION_NODE
                        _result += thisNode.nodeValue;
                        break;
                    default:
                        break;
                }
            }
            return _result;
            },

            _getElementAsStringGeneric: function(thisNode){
            var _result = "";
            if (thisNode == null) { return _result; }
            // start tag
            _result += '<' + thisNode.nodeName;
            // add attributes
            if (thisNode.attributes && thisNode.attributes.length>0) {
                for (var i = 0; i < thisNode.attributes.length; i++) {
                    _result += " " + thisNode.attributes[i].name
                        + "=\"" + thisNode.attributes[i].value + "\"";
                }
            }
            // close start tag
            _result += '>';
            // content of tag
            _result += RicoUtil._innerXMLGeneric(thisNode);
            // end tag
            _result += '</' + thisNode.nodeName + '>';
            return _result;
        },
    /********* END SAFARI Compatibility **********/

    /********* SAFARI Compatibility ***********/
    /********* BDP : 28/02/2007 ***************/
    /*
   getElementsComputedStyle: function ( htmlElement, cssProperty, mozillaEquivalentCSS) {
      if ( arguments.length == 2 )
         mozillaEquivalentCSS = cssProperty;

      var el = $(htmlElement);
      if ( el.currentStyle )
         return el.currentStyle[cssProperty];
      else
         return document.defaultView.getComputedStyle(el, null).getPropertyValue(mozillaEquivalentCSS);
   },
   */
   /********* END SAFARI Compatibility **********/
   
getElementsComputedStyle: function ( htmlElement, cssProperty, mozillaEquivalentCSS) {
      if ( arguments.length == 2 )
         mozillaEquivalentCSS = cssProperty;

      var el = $(htmlElement);
      if ( el.currentStyle )
         return el.currentStyle[cssProperty];
      else {
          cs = document.defaultView.getComputedStyle(el, null);
          // this may come back null in safari
          if (cs) {
              return cs.getPropertyValue(mozillaEquivalentCSS);
          }
          else {
              return null;
          }
      }
   },
   

   createXmlDocument : function() {
      if (document.implementation && document.implementation.createDocument) {
         var doc = document.implementation.createDocument("", "", null);

         if (doc.readyState == null) {
            doc.readyState = 1;
            doc.addEventListener("load", function () {
               doc.readyState = 4;
               if (typeof doc.onreadystatechange == "function")
                  doc.onreadystatechange();
            }, false);
         }

         return doc;
      }

      if (window.ActiveXObject)
          return Try.these(
            function() { return new ActiveXObject('MSXML2.DomDocument')   },
            function() { return new ActiveXObject('Microsoft.DomDocument')},
            function() { return new ActiveXObject('MSXML.DomDocument')    },
            function() { return new ActiveXObject('MSXML3.DomDocument')   }
          ) || false;

      return null;
   },

   getContentAsString: function( parentNode )
   {
        if (parentNode.xml != undefined)
            return this._getContentAsStringIE(parentNode);
        else
            return this._getContentAsStringMozilla(parentNode);
   },

  _getContentAsStringIE: function(parentNode) {
     var contentStr = "";
     for ( var i = 0 ; i < parentNode.childNodes.length ; i++ ) {
         var n = parentNode.childNodes[i];
         if (n.nodeType == 4) {
             contentStr += n.nodeValue;
         }
         else {
           contentStr += n.xml;
       }
     }
     return contentStr;
  },

  _getContentAsStringMozilla: function(parentNode) {
     var xmlSerializer = new XMLSerializer();
     var contentStr = "";
     for ( var i = 0 ; i < parentNode.childNodes.length ; i++ ) {
          var n = parentNode.childNodes[i];
          if (n.nodeType == 4) { // CDATA node
              contentStr += n.nodeValue;
          }
          else {
            /********* SAFARI Compatibility ***********/
            /********* BDP : 28/02/2007 ***************/
            //contentStr += xmlSerializer.serializeToString(n);
            contentStr +=RicoUtil.innerXML(n); 
            /********* END SAFARI Compatibility **********/
        }
     }
     contentStr = contentStr.replace(/<\/?#(text|comment)>/g, '');
     return contentStr;
  },

   toViewportPosition: function(element) {
      return this._toAbsolute(element,true);
   },

   toDocumentPosition: function(element) {
      return this._toAbsolute(element,false);
   },

   /**
    *  Compute the elements position in terms of the window viewport
    *  so that it can be compared to the position of the mouse (dnd)
    *  This is additions of all the offsetTop,offsetLeft values up the
    *  offsetParent hierarchy, ...taking into account any scrollTop,
    *  scrollLeft values along the way...
    *
    * IE has a bug reporting a correct offsetLeft of elements within a
    * a relatively positioned parent!!!
    **/
   _toAbsolute: function(element,accountForDocScroll) {

      if ( navigator.userAgent.toLowerCase().indexOf("msie") == -1 )
         return this._toAbsoluteMozilla(element,accountForDocScroll);

      var x = 0;
      var y = 0;
      var parent = element;
      while ( parent ) {

         var borderXOffset = 0;
         var borderYOffset = 0;
         if ( parent != element ) {
            var borderXOffset = parseInt(this.getElementsComputedStyle(parent, "borderLeftWidth" ));
            var borderYOffset = parseInt(this.getElementsComputedStyle(parent, "borderTopWidth" ));
            borderXOffset = isNaN(borderXOffset) ? 0 : borderXOffset;
            borderYOffset = isNaN(borderYOffset) ? 0 : borderYOffset;
         }

         x += parent.offsetLeft - parent.scrollLeft + borderXOffset;
         y += parent.offsetTop - parent.scrollTop + borderYOffset;
         parent = parent.offsetParent;
      }

      if ( accountForDocScroll ) {
         x -= this.docScrollLeft();
         y -= this.docScrollTop();
      }

      return { x:x, y:y };
   },

   /**
    *  Mozilla did not report all of the parents up the hierarchy via the
    *  offsetParent property that IE did.  So for the calculation of the
    *  offsets we use the offsetParent property, but for the calculation of
    *  the scrollTop/scrollLeft adjustments we navigate up via the parentNode
    *  property instead so as to get the scroll offsets...
    *
    **/
   _toAbsoluteMozilla: function(element,accountForDocScroll) {
      var x = 0;
      var y = 0;
      var parent = element;
      while ( parent ) {
         x += parent.offsetLeft;
         y += parent.offsetTop;
         parent = parent.offsetParent;
      }

      parent = element;
      while ( parent &&
              parent != document.body &&
              parent != document.documentElement ) {
         if ( parent.scrollLeft  )
            x -= parent.scrollLeft;
         if ( parent.scrollTop )
            y -= parent.scrollTop;
         parent = parent.parentNode;
      }

      if ( accountForDocScroll ) {
         x -= this.docScrollLeft();
         y -= this.docScrollTop();
      }

      return { x:x, y:y };
   },

   docScrollLeft: function() {
      if ( window.pageXOffset )
         return window.pageXOffset;
      else if ( document.documentElement && document.documentElement.scrollLeft )
         return document.documentElement.scrollLeft;
      else if ( document.body )
         return document.body.scrollLeft;
      else
         return 0;
   },

   docScrollTop: function() {
      if ( window.pageYOffset )
         return window.pageYOffset;
      else if ( document.documentElement && document.documentElement.scrollTop )
         return document.documentElement.scrollTop;
      else if ( document.body )
         return document.body.scrollTop;
      else
         return 0;
   }

};
