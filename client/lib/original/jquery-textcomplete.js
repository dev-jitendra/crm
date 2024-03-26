Espo.loader.setContextId('lib!jquery-textcomplete');
(function (factory) {
  if (typeof define === 'function' && define.amd) {
    
    define(['jquery'], factory);
  } else if (typeof module === "object" && module.exports) {
    var $ = require('jquery');
    module.exports = factory($);
  } else {
    
    factory(jQuery);
  }
}(function (jQuery) {



if (typeof jQuery === 'undefined') {
  throw new Error('jQuery.textcomplete requires jQuery');
}

+function ($) {
  'use strict';

  var warn = function (message) {
    if (console.warn) { console.warn(message); }
  };

  var id = 1;

  $.fn.textcomplete = function (strategies, option) {
    var args = Array.prototype.slice.call(arguments);
    return this.each(function () {
      var self = this;
      var $this = $(this);
      var completer = $this.data('textComplete');
      if (!completer) {
        option || (option = {});
        option._oid = id++;  
        completer = new $.fn.textcomplete.Completer(this, option);
        $this.data('textComplete', completer);
      }
      if (typeof strategies === 'string') {
        if (!completer) return;
        args.shift()
        completer[strategies].apply(completer, args);
        if (strategies === 'destroy') {
          $this.removeData('textComplete');
        }
      } else {
        
        
        $.each(strategies, function (obj) {
          $.each(['header', 'footer', 'placement', 'maxCount'], function (name) {
            if (obj[name]) {
              completer.option[name] = obj[name];
              warn(name + 'as a strategy param is deprecated. Use option.');
              delete obj[name];
            }
          });
        });
        completer.register($.fn.textcomplete.Strategy.parse(strategies, {
          el: self,
          $el: $this
        }));
      }
    });
  };

}(jQuery);

+function ($) {
  'use strict';

  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  var lock = function (func) {
    var locked, queuedArgsToReplay;

    return function () {
      
      var args = Array.prototype.slice.call(arguments);
      if (locked) {
        
        
        
        queuedArgsToReplay = args;
        return;
      }
      locked = true;
      var self = this;
      args.unshift(function replayOrFree() {
        if (queuedArgsToReplay) {
          
          
          
          
          
          var replayArgs = queuedArgsToReplay;
          queuedArgsToReplay = undefined;
          replayArgs.unshift(replayOrFree);
          func.apply(self, replayArgs);
        } else {
          locked = false;
        }
      });
      func.apply(this, args);
    };
  };

  var isString = function (obj) {
    return Object.prototype.toString.call(obj) === '[object String]';
  };

  var uniqueId = 0;
  var initializedEditors = [];

  function Completer(element, option) {
    this.$el        = $(element);
    this.id         = 'textcomplete' + uniqueId++;
    this.strategies = [];
    this.views      = [];
    this.option     = $.extend({}, Completer.defaults, option);

    if (!this.$el.is('input[type=text]') && !this.$el.is('input[type=search]') && !this.$el.is('textarea') && !element.isContentEditable && element.contentEditable != 'true') {
      throw new Error('textcomplete must be called on a Textarea or a ContentEditable.');
    }

    
    if (element === element.ownerDocument.activeElement) {
      
      this.initialize()
    } else {
      
      var self = this;
      this.$el.one('focus.' + this.id, function () { self.initialize(); });

      
      if ((!this.option.adapter || this.option.adapter == 'CKEditor') && typeof CKEDITOR != 'undefined' && (this.$el.is('textarea'))) {
        CKEDITOR.on("instanceReady", function(event) { 

          if($.inArray(event.editor.id, initializedEditors) == -1) { 
            initializedEditors.push(event.editor.id);
			
            event.editor.on("focus", function(event2) {
				
				self.$el = $(event.editor.editable().$);
				if (!self.option.adapter) {
					self.option.adapter = $.fn.textcomplete['CKEditor'];
				}
				self.option.ckeditor_instance = event.editor; 
              	self.initialize();
            });
          }
        });
      }
    }
  }

  Completer.defaults = {
    appendTo: 'body',
    className: '',  
    dropdownClassName: 'dropdown-menu textcomplete-dropdown',
    maxCount: 10,
    zIndex: '100',
    rightEdgeOffset: 30
  };

  $.extend(Completer.prototype, {
    
    

    id:         null,
    option:     null,
    strategies: null,
    adapter:    null,
    dropdown:   null,
    $el:        null,
    $iframe:    null,

    
    

    initialize: function () {
      var element = this.$el.get(0);
      
      
      
      if (this.$el.prop('ownerDocument') !== document && window.frames.length) {
        for (var iframeIndex = 0; iframeIndex < window.frames.length; iframeIndex++) {
          if (this.$el.prop('ownerDocument') === window.frames[iframeIndex].document) {
            this.$iframe = $(window.frames[iframeIndex].frameElement);
            break;
          }
        }
      }
      
      
      
      this.dropdown = new $.fn.textcomplete.Dropdown(element, this, this.option);
      var Adapter, viewName;
      if (this.option.adapter) {
        Adapter = this.option.adapter;
      } else {
        if (this.$el.is('textarea') || this.$el.is('input[type=text]') || this.$el.is('input[type=search]')) {
          viewName = typeof element.selectionEnd === 'number' ? 'Textarea' : 'IETextarea';
        } else {
          viewName = 'ContentEditable';
        }
        Adapter = $.fn.textcomplete[viewName];
      }
      this.adapter = new Adapter(element, this, this.option);
    },

    destroy: function () {
      this.$el.off('.' + this.id);
      if (this.adapter) {
        this.adapter.destroy();
      }
      if (this.dropdown) {
        this.dropdown.destroy();
      }
      this.$el = this.adapter = this.dropdown = null;
    },

    deactivate: function () {
      if (this.dropdown) {
        this.dropdown.deactivate();
      }
    },

    
    trigger: function (text, skipUnchangedTerm) {
      if (!this.dropdown) { this.initialize(); }
      text != null || (text = this.adapter.getTextFromHeadToCaret());
      var searchQuery = this._extractSearchQuery(text);
      if (searchQuery.length) {
        var term = searchQuery[1];
        
        if (skipUnchangedTerm && this._term === term && term !== "") { return; }
        this._term = term;
        this._search.apply(this, searchQuery);
      } else {
        this._term = null;
        this.dropdown.deactivate();
      }
    },

    fire: function (eventName) {
      var args = Array.prototype.slice.call(arguments, 1);
      this.$el.trigger(eventName, args);
      return this;
    },

    register: function (strategies) {
      Array.prototype.push.apply(this.strategies, strategies);
    },

    
    
    
    
    
    
    select: function (value, strategy, e) {
      this._term = null;
      this.adapter.select(value, strategy, e);
      this.fire('change').fire('textComplete:select', value, strategy);
      this.adapter.focus();
    },

    
    

    _clearAtNext: true,
    _term:        null,

    
    

    
    
    
    
    _extractSearchQuery: function (text) {
      for (var i = 0; i < this.strategies.length; i++) {
        var strategy = this.strategies[i];
        var context = strategy.context(text);
        if (context || context === '') {
          var matchRegexp = $.isFunction(strategy.match) ? strategy.match(text) : strategy.match;
          if (isString(context)) { text = context; }
          var match = text.match(matchRegexp);
          if (match) { return [strategy, match[strategy.index], match]; }
        }
      }
      return []
    },

    
    _search: lock(function (free, strategy, term, match) {
      var self = this;
      strategy.search(term, function (data, stillSearching) {
        if (!self.dropdown.shown) {
          self.dropdown.activate();
        }
        if (self._clearAtNext) {
          
          self.dropdown.clear();
          self._clearAtNext = false;
        }
        self.dropdown.setPosition(self.adapter.getCaretPosition());
        self.dropdown.render(self._zip(data, strategy, term));
        if (!stillSearching) {
          
          free();
          self._clearAtNext = true; 
        }
      }, match);
    }),

    
    
    
    
    
    
    _zip: function (data, strategy, term) {
      return $.map(data, function (value) {
        return { value: value, strategy: strategy, term: term };
      });
    }
  });

  $.fn.textcomplete.Completer = Completer;
}(jQuery);

+function ($) {
  'use strict';

  var $window = $(window);

  var include = function (zippedData, datum) {
    var i, elem;
    var idProperty = datum.strategy.idProperty
    for (i = 0; i < zippedData.length; i++) {
      elem = zippedData[i];
      if (elem.strategy !== datum.strategy) continue;
      if (idProperty) {
        if (elem.value[idProperty] === datum.value[idProperty]) return true;
      } else {
        if (elem.value === datum.value) return true;
      }
    }
    return false;
  };

  var dropdownViews = {};
  $(document).on('click', function (e) {
    var id = e.originalEvent && e.originalEvent.keepTextCompleteDropdown;
    $.each(dropdownViews, function (key, view) {
      if (key !== id) { view.deactivate(); }
    });
  });

  var commands = {
    SKIP_DEFAULT: 0,
    KEY_UP: 1,
    KEY_DOWN: 2,
    KEY_ENTER: 3,
    KEY_PAGEUP: 4,
    KEY_PAGEDOWN: 5,
    KEY_ESCAPE: 6
  };

  
  

  
  
  
  function Dropdown(element, completer, option) {
    this.$el       = Dropdown.createElement(option);
    this.completer = completer;
    this.id        = completer.id + 'dropdown';
    this._data     = []; 
    this.$inputEl  = $(element);
    this.option    = option;

    
    if (option.listPosition) { this.setPosition = option.listPosition; }
    if (option.height) { this.$el.height(option.height); }
    var self = this;
    $.each(['maxCount', 'placement', 'footer', 'header', 'noResultsMessage', 'className'], function (_i, name) {
      if (option[name] != null) { self[name] = option[name]; }
    });
    this._bindEvents(element);
    dropdownViews[this.id] = this;
  }

  $.extend(Dropdown, {
    
    

    createElement: function (option) {
      var $parent = option.appendTo;
      if (!($parent instanceof $)) { $parent = $($parent); }
      var $el = $('<ul></ul>')
        .addClass(option.dropdownClassName)
        .attr('id', 'textcomplete-dropdown-' + option._oid)
        .css({
          display: 'none',
          left: 0,
          position: 'absolute',
          zIndex: option.zIndex
        })
        .appendTo($parent);
      return $el;
    }
  });

  $.extend(Dropdown.prototype, {
    
    

    $el:       null,  
    $inputEl:  null,  
    completer: null,
    footer:    null,
    header:    null,
    id:        null,
    maxCount:  null,
    placement: '',
    shown:     false,
    data:      [],     
    className: '',

    
    

    destroy: function () {
      
      this.deactivate();

      this.$el.off('.' + this.id);
      this.$inputEl.off('.' + this.id);
      this.clear();
      this.$el.remove();
      this.$el = this.$inputEl = this.completer = null;
      delete dropdownViews[this.id]
    },

    render: function (zippedData) {
      var contentsHtml = this._buildContents(zippedData);
      var unzippedData = $.map(zippedData, function (d) { return d.value; });
      if (zippedData.length) {
        var strategy = zippedData[0].strategy;
        if (strategy.id) {
          this.$el.attr('data-strategy', strategy.id);
        } else {
          this.$el.removeAttr('data-strategy');
        }
        this._renderHeader(unzippedData);
        this._renderFooter(unzippedData);
        if (contentsHtml) {
          this._renderContents(contentsHtml);
          this._fitToBottom();
          this._fitToRight();
          this._activateIndexedItem();
        }
        this._setScroll();
      } else if (this.noResultsMessage) {
        this._renderNoResultsMessage(unzippedData);
      } else if (this.shown) {
        this.deactivate();
      }
    },

    setPosition: function (pos) {
      
      
      
      var position = 'absolute';
      
      this.$inputEl.add(this.$inputEl.parents()).each(function() {
        if($(this).css('position') === 'absolute') 
          return false;
        if($(this).css('position') === 'fixed') {
          pos.top -= $window.scrollTop();
          pos.left -= $window.scrollLeft();
          position = 'fixed';
          return false;
        }
      });
      this.$el.css(this._applyPlacement(pos));
      this.$el.css({ position: position }); 

      return this;
    },

    clear: function () {
      this.$el.html('');
      this.data = [];
      this._index = 0;
      this._$header = this._$footer = this._$noResultsMessage = null;
    },

    activate: function () {
      if (!this.shown) {
        this.clear();
        this.$el.show();
        if (this.className) { this.$el.addClass(this.className); }
        this.completer.fire('textComplete:show');
        this.shown = true;
      }
      return this;
    },

    deactivate: function () {
      if (this.shown) {
        this.$el.hide();
        if (this.className) { this.$el.removeClass(this.className); }
        this.completer.fire('textComplete:hide');
        this.shown = false;
      }
      return this;
    },

    isUp: function (e) {
      return e.keyCode === 38 || (e.ctrlKey && e.keyCode === 80);  
    },

    isDown: function (e) {
      return e.keyCode === 40 || (e.ctrlKey && e.keyCode === 78);  
    },

    isEnter: function (e) {
      var modifiers = e.ctrlKey || e.altKey || e.metaKey || e.shiftKey;
      return !modifiers && (e.keyCode === 13 || e.keyCode === 9 || (this.option.completeOnSpace === true && e.keyCode === 32))  
    },

    isPageup: function (e) {
      return e.keyCode === 33;  
    },

    isPagedown: function (e) {
      return e.keyCode === 34;  
    },

    isEscape: function (e) {
      return e.keyCode === 27;  
    },

    
    

    _data:    null,  
    _index:   null,
    _$header: null,
    _$noResultsMessage: null,
    _$footer: null,

    
    

    _bindEvents: function () {
      this.$el.on('mousedown.' + this.id, '.textcomplete-item', $.proxy(this._onClick, this));
      this.$el.on('touchstart.' + this.id, '.textcomplete-item', $.proxy(this._onClick, this));
      this.$el.on('mouseover.' + this.id, '.textcomplete-item', $.proxy(this._onMouseover, this));
      this.$inputEl.on('keydown.' + this.id, $.proxy(this._onKeydown, this));
    },

    _onClick: function (e) {
      var $el = $(e.target);
      e.preventDefault();
      e.originalEvent.keepTextCompleteDropdown = this.id;
      if (!$el.hasClass('textcomplete-item')) {
        $el = $el.closest('.textcomplete-item');
      }
      var datum = this.data[parseInt($el.data('index'), 10)];
      this.completer.select(datum.value, datum.strategy, e);
      var self = this;
      
      
      setTimeout(function () {
        self.deactivate();
        if (e.type === 'touchstart') {
          self.$inputEl.focus();
        }
      }, 0);
    },

    
    _onMouseover: function (e) {
      var $el = $(e.target);
      e.preventDefault();
      if (!$el.hasClass('textcomplete-item')) {
        $el = $el.closest('.textcomplete-item');
      }
      this._index = parseInt($el.data('index'), 10);
      this._activateIndexedItem();
    },

    _onKeydown: function (e) {
      if (!this.shown) { return; }

      var command;

      if ($.isFunction(this.option.onKeydown)) {
        command = this.option.onKeydown(e, commands);
      }

      if (command == null) {
        command = this._defaultKeydown(e);
      }

      switch (command) {
        case commands.KEY_UP:
          e.preventDefault();
          this._up();
          break;
        case commands.KEY_DOWN:
          e.preventDefault();
          this._down();
          break;
        case commands.KEY_ENTER:
          e.preventDefault();
          this._enter(e);
          break;
        case commands.KEY_PAGEUP:
          e.preventDefault();
          this._pageup();
          break;
        case commands.KEY_PAGEDOWN:
          e.preventDefault();
          this._pagedown();
          break;
        case commands.KEY_ESCAPE:
          e.preventDefault();
          this.deactivate();
          break;
      }
    },

    _defaultKeydown: function (e) {
      if (this.isUp(e)) {
        return commands.KEY_UP;
      } else if (this.isDown(e)) {
        return commands.KEY_DOWN;
      } else if (this.isEnter(e)) {
        return commands.KEY_ENTER;
      } else if (this.isPageup(e)) {
        return commands.KEY_PAGEUP;
      } else if (this.isPagedown(e)) {
        return commands.KEY_PAGEDOWN;
      } else if (this.isEscape(e)) {
        return commands.KEY_ESCAPE;
      }
    },

    _up: function () {
      if (this._index === 0) {
        this._index = this.data.length - 1;
      } else {
        this._index -= 1;
      }
      this._activateIndexedItem();
      this._setScroll();
    },

    _down: function () {
      if (this._index === this.data.length - 1) {
        this._index = 0;
      } else {
        this._index += 1;
      }
      this._activateIndexedItem();
      this._setScroll();
    },

    _enter: function (e) {
      var datum = this.data[parseInt(this._getActiveElement().data('index'), 10)];
      this.completer.select(datum.value, datum.strategy, e);
      this.deactivate();
    },

    _pageup: function () {
      var target = 0;
      var threshold = this._getActiveElement().position().top - this.$el.innerHeight();
      this.$el.children().each(function (i) {
        if ($(this).position().top + $(this).outerHeight() > threshold) {
          target = i;
          return false;
        }
      });
      this._index = target;
      this._activateIndexedItem();
      this._setScroll();
    },

    _pagedown: function () {
      var target = this.data.length - 1;
      var threshold = this._getActiveElement().position().top + this.$el.innerHeight();
      this.$el.children().each(function (i) {
        if ($(this).position().top > threshold) {
          target = i;
          return false
        }
      });
      this._index = target;
      this._activateIndexedItem();
      this._setScroll();
    },

    _activateIndexedItem: function () {
      this.$el.find('.textcomplete-item.active').removeClass('active');
      this._getActiveElement().addClass('active');
    },

    _getActiveElement: function () {
      return this.$el.children('.textcomplete-item:nth(' + this._index + ')');
    },

    _setScroll: function () {
      var $activeEl = this._getActiveElement();
      var itemTop = $activeEl.position().top;
      var itemHeight = $activeEl.outerHeight();
      var visibleHeight = this.$el.innerHeight();
      var visibleTop = this.$el.scrollTop();
      if (this._index === 0 || this._index == this.data.length - 1 || itemTop < 0) {
        this.$el.scrollTop(itemTop + visibleTop);
      } else if (itemTop + itemHeight > visibleHeight) {
        this.$el.scrollTop(itemTop + itemHeight + visibleTop - visibleHeight);
      }
    },

    _buildContents: function (zippedData) {
      var datum, i, index;
      var html = '';
      for (i = 0; i < zippedData.length; i++) {
        if (this.data.length === this.maxCount) break;
        datum = zippedData[i];
        if (include(this.data, datum)) { continue; }
        index = this.data.length;
        this.data.push(datum);
        html += '<li class="textcomplete-item" data-index="' + index + '"><a>';
        html +=   datum.strategy.template(datum.value, datum.term);
        html += '</a></li>';
      }
      return html;
    },

    _renderHeader: function (unzippedData) {
      if (this.header) {
        if (!this._$header) {
          this._$header = $('<li class="textcomplete-header"></li>').prependTo(this.$el);
        }
        var html = $.isFunction(this.header) ? this.header(unzippedData) : this.header;
        this._$header.html(html);
      }
    },

    _renderFooter: function (unzippedData) {
      if (this.footer) {
        if (!this._$footer) {
          this._$footer = $('<li class="textcomplete-footer"></li>').appendTo(this.$el);
        }
        var html = $.isFunction(this.footer) ? this.footer(unzippedData) : this.footer;
        this._$footer.html(html);
      }
    },

    _renderNoResultsMessage: function (unzippedData) {
      if (this.noResultsMessage) {
        if (!this._$noResultsMessage) {
          this._$noResultsMessage = $('<li class="textcomplete-no-results-message"></li>').appendTo(this.$el);
        }
        var html = $.isFunction(this.noResultsMessage) ? this.noResultsMessage(unzippedData) : this.noResultsMessage;
        this._$noResultsMessage.html(html);
      }
    },

    _renderContents: function (html) {
      if (this._$footer) {
        this._$footer.before(html);
      } else {
        this.$el.append(html);
      }
    },

    _fitToBottom: function() {
      var windowScrollBottom = $window.scrollTop() + $window.height();
      var height = this.$el.height();
      if ((this.$el.position().top + height) > windowScrollBottom) {
        
        if (!this.completer.$iframe) {
          this.$el.offset({top: windowScrollBottom - height});
        }
      }
    },

    _fitToRight: function() {
      
      
      
      
      var tolerance = this.option.rightEdgeOffset; 
      var lastOffset = this.$el.offset().left, offset;
      var width = this.$el.width();
      var maxLeft = $window.width() - tolerance;
      while (lastOffset + width > maxLeft) {
        this.$el.offset({left: lastOffset - tolerance});
        offset = this.$el.offset().left;
        if (offset >= lastOffset) { break; }
        lastOffset = offset;
      }
    },

    _applyPlacement: function (position) {
      
      if (this.placement.indexOf('top') !== -1) {
        
        position = {
          top: 'auto',
          bottom: this.$el.parent().height() - position.top + position.lineHeight,
          left: position.left
        };
      } else {
        position.bottom = 'auto';
        delete position.lineHeight;
      }
      if (this.placement.indexOf('absleft') !== -1) {
        position.left = 0;
      } else if (this.placement.indexOf('absright') !== -1) {
        position.right = 0;
        position.left = 'auto';
      }
      return position;
    }
  });

  $.fn.textcomplete.Dropdown = Dropdown;
  $.extend($.fn.textcomplete, commands);
}(jQuery);

+function ($) {
  'use strict';

  
  var memoize = function (func) {
    var memo = {};
    return function (term, callback) {
      if (memo[term]) {
        callback(memo[term]);
      } else {
        func.call(this, term, function (data) {
          memo[term] = (memo[term] || []).concat(data);
          callback.apply(null, arguments);
        });
      }
    };
  };

  function Strategy(options) {
    $.extend(this, options);
    if (this.cache) { this.search = memoize(this.search); }
  }

  Strategy.parse = function (strategiesArray, params) {
    return $.map(strategiesArray, function (strategy) {
      var strategyObj = new Strategy(strategy);
      strategyObj.el = params.el;
      strategyObj.$el = params.$el;
      return strategyObj;
    });
  };

  $.extend(Strategy.prototype, {
    
    

    
    match:      null,
    replace:    null,
    search:     null,

    
    id:         null,
    cache:      false,
    context:    function () { return true; },
    index:      2,
    template:   function (obj) { return obj; },
    idProperty: null
  });

  $.fn.textcomplete.Strategy = Strategy;

}(jQuery);

+function ($) {
  'use strict';

  var now = Date.now || function () { return new Date().getTime(); };

  
  
  
  
  
  var debounce = function (func, wait) {
    var timeout, args, context, timestamp, result;
    var later = function () {
      var last = now() - timestamp;
      if (last < wait) {
        timeout = setTimeout(later, wait - last);
      } else {
        timeout = null;
        result = func.apply(context, args);
        context = args = null;
      }
    };

    return function () {
      context = this;
      args = arguments;
      timestamp = now();
      if (!timeout) {
        timeout = setTimeout(later, wait);
      }
      return result;
    };
  };

  function Adapter () {}

  $.extend(Adapter.prototype, {
    
    

    id:        null, 
    completer: null, 
    el:        null, 
    $el:       null, 
    option:    null,

    
    

    initialize: function (element, completer, option) {
      this.el        = element;
      this.$el       = $(element);
      this.id        = completer.id + this.constructor.name;
      this.completer = completer;
      this.option    = option;

      if (this.option.debounce) {
        this._onKeyup = debounce(this._onKeyup, this.option.debounce);
      }

      this._bindEvents();
    },

    destroy: function () {
      this.$el.off('.' + this.id); 
      this.$el = this.el = this.completer = null;
    },

    
    
    
    
    
    select: function () {
      throw new Error('Not implemented');
    },

    
    getCaretPosition: function () {
      var position = this._getCaretRelativePosition();
      var offset = this.$el.offset();

      
      var $parent = this.option.appendTo;
      if ($parent) {
         if (!($parent instanceof $)) { $parent = $($parent); }
         var parentOffset = $parent.offsetParent().offset();
         offset.top -= parentOffset.top;
         offset.left -= parentOffset.left;
      }

      position.top += offset.top;
      position.left += offset.left;
      return position;
    },

    
    focus: function () {
      this.$el.focus();
    },

    
    

    _bindEvents: function () {
      this.$el.on('keyup.' + this.id, $.proxy(this._onKeyup, this));
    },

    _onKeyup: function (e) {
      if (this._skipSearch(e)) { return; }
      this.completer.trigger(this.getTextFromHeadToCaret(), true);
    },

    
    _skipSearch: function (clickEvent) {
      switch (clickEvent.keyCode) {
        case 9:  
        case 13: 
        case 16: 
        case 17: 
        case 18: 
        case 33: 
        case 34: 
        case 40: 
        case 38: 
        case 27: 
          return true;
      }
      if (clickEvent.ctrlKey) switch (clickEvent.keyCode) {
        case 78: 
        case 80: 
          return true;
      }
    }
  });

  $.fn.textcomplete.Adapter = Adapter;
}(jQuery);

+function ($) {
  'use strict';

  
  
  
  
  function Textarea(element, completer, option) {
    this.initialize(element, completer, option);
  }

  $.extend(Textarea.prototype, $.fn.textcomplete.Adapter.prototype, {
    
    

    
    select: function (value, strategy, e) {
      var pre = this.getTextFromHeadToCaret();
      var post = this.el.value.substring(this.el.selectionEnd);
      var newSubstr = strategy.replace(value, e);
      var regExp;
      if (typeof newSubstr !== 'undefined') {
        if ($.isArray(newSubstr)) {
          post = newSubstr[1] + post;
          newSubstr = newSubstr[0];
        }
        regExp = $.isFunction(strategy.match) ? strategy.match(pre) : strategy.match;
        pre = pre.replace(regExp, newSubstr);
        this.$el.val(pre + post);
        this.el.selectionStart = this.el.selectionEnd = pre.length;
      }
    },

    getTextFromHeadToCaret: function () {
      return this.el.value.substring(0, this.el.selectionEnd);
    },

    
    

    _getCaretRelativePosition: function () {
      var p = $.fn.textcomplete.getCaretCoordinates(this.el, this.el.selectionStart);
      return {
        top: p.top + this._calculateLineHeight() - this.$el.scrollTop(),
        left: p.left - this.$el.scrollLeft(),
        lineHeight: this._calculateLineHeight()
      };
    },

    _calculateLineHeight: function () {
      var lineHeight = parseInt(this.$el.css('line-height'), 10);
      if (isNaN(lineHeight)) {
        
        var parentNode = this.el.parentNode;
        var temp = document.createElement(this.el.nodeName);
        var style = this.el.style;
        temp.setAttribute(
          'style',
          'margin:0px;padding:0px;font-family:' + style.fontFamily + ';font-size:' + style.fontSize
        );
        temp.innerHTML = 'test';
        parentNode.appendChild(temp);
        lineHeight = temp.clientHeight;
        parentNode.removeChild(temp);
      }
      return lineHeight;
    }
  });

  $.fn.textcomplete.Textarea = Textarea;
}(jQuery);

+function ($) {
  'use strict';

  var sentinelChar = '吶';

  function IETextarea(element, completer, option) {
    this.initialize(element, completer, option);
    $('<span>' + sentinelChar + '</span>').css({
      position: 'absolute',
      top: -9999,
      left: -9999
    }).insertBefore(element);
  }

  $.extend(IETextarea.prototype, $.fn.textcomplete.Textarea.prototype, {
    
    

    select: function (value, strategy, e) {
      var pre = this.getTextFromHeadToCaret();
      var post = this.el.value.substring(pre.length);
      var newSubstr = strategy.replace(value, e);
      var regExp;
      if (typeof newSubstr !== 'undefined') {
        if ($.isArray(newSubstr)) {
          post = newSubstr[1] + post;
          newSubstr = newSubstr[0];
        }
        regExp = $.isFunction(strategy.match) ? strategy.match(pre) : strategy.match;
        pre = pre.replace(regExp, newSubstr);
        this.$el.val(pre + post);
        this.el.focus();
        var range = this.el.createTextRange();
        range.collapse(true);
        range.moveEnd('character', pre.length);
        range.moveStart('character', pre.length);
        range.select();
      }
    },

    getTextFromHeadToCaret: function () {
      this.el.focus();
      var range = document.selection.createRange();
      range.moveStart('character', -this.el.value.length);
      var arr = range.text.split(sentinelChar)
      return arr.length === 1 ? arr[0] : arr[1];
    }
  });

  $.fn.textcomplete.IETextarea = IETextarea;
}(jQuery);





+function ($) {
  'use strict';

  
  
  
  
  function ContentEditable (element, completer, option) {
    this.initialize(element, completer, option);
  }

  $.extend(ContentEditable.prototype, $.fn.textcomplete.Adapter.prototype, {
    
    

    
    
    select: function (value, strategy, e) {
      var pre = this.getTextFromHeadToCaret();
      
      var sel = this.el.ownerDocument.getSelection();
      
      var range = sel.getRangeAt(0);
      var selection = range.cloneRange();
      selection.selectNodeContents(range.startContainer);
      var content = selection.toString();
      var post = content.substring(range.startOffset);
      var newSubstr = strategy.replace(value, e);
      var regExp;
      if (typeof newSubstr !== 'undefined') {
        if ($.isArray(newSubstr)) {
          post = newSubstr[1] + post;
          newSubstr = newSubstr[0];
        }
        regExp = $.isFunction(strategy.match) ? strategy.match(pre) : strategy.match;
        pre = pre.replace(regExp, newSubstr)
            .replace(/ $/, "&nbsp"); 
        range.selectNodeContents(range.startContainer);
        range.deleteContents();
        
        
        var preWrapper = this.el.ownerDocument.createElement("div");
        preWrapper.innerHTML = pre;
        var postWrapper = this.el.ownerDocument.createElement("div");
        postWrapper.innerHTML = post;
        
        
        var fragment = this.el.ownerDocument.createDocumentFragment();
        var childNode;
        var lastOfPre;
        while (childNode = preWrapper.firstChild) {
        	lastOfPre = fragment.appendChild(childNode);
        }
        while (childNode = postWrapper.firstChild) {
        	fragment.appendChild(childNode);
        }
        
        
        range.insertNode(fragment);
        range.setStartAfter(lastOfPre);
        
        range.collapse(true);
        sel.removeAllRanges();
        sel.addRange(range);
      }
    },

    
    

    
    
    
    
    
    
    
    
    
    _getCaretRelativePosition: function () {
      var range = this.el.ownerDocument.getSelection().getRangeAt(0).cloneRange();
      var wrapperNode = range.endContainer.parentNode;
      var node = this.el.ownerDocument.createElement('span');
      range.insertNode(node);
      range.selectNodeContents(node);
      range.deleteContents();
      setTimeout(function() { wrapperNode.normalize(); }, 0);
      var $node = $(node);
      var position = $node.offset();
      position.left -= this.$el.offset().left;
      position.top += $node.height() - this.$el.offset().top;
      position.lineHeight = $node.height();
      
      
      
      if (this.completer.$iframe) {
        var iframePosition = this.completer.$iframe.offset();
        position.top += iframePosition.top;
        position.left += iframePosition.left;
        
        
        position.top -= $(this.completer.$iframe[0].contentWindow.document).scrollTop();
      }
      
      $node.remove();
      return position;
    },

    
    
    
    
    
    
    
    
    getTextFromHeadToCaret: function () {
      var range = this.el.ownerDocument.getSelection().getRangeAt(0);
      var selection = range.cloneRange();
      selection.selectNodeContents(range.startContainer);
      return selection.toString().substring(0, range.startOffset);
    }
  });

  $.fn.textcomplete.ContentEditable = ContentEditable;
}(jQuery);





+function ($) {
  'use strict';

  
  
  
  
  function CKEditor (element, completer, option) {
    this.initialize(element, completer, option);
  }

  $.extend(CKEditor.prototype, $.fn.textcomplete.ContentEditable.prototype, {
    _bindEvents: function () {
      var $this = this;
      this.option.ckeditor_instance.on('key', function(event) {
        var domEvent = event.data;
        $this._onKeyup(domEvent);
        if ($this.completer.dropdown.shown && $this._skipSearch(domEvent)) {
          return false;
        }
      }, null, null, 1); 
      
      this.$el.on('keyup.' + this.id, $.proxy(this._onKeyup, this));
    },
});

  $.fn.textcomplete.CKEditor = CKEditor;
}(jQuery);






















(function ($) {





var properties = [
  'direction',  
  'boxSizing',
  'width',  
  'height',
  'overflowX',
  'overflowY',  

  'borderTopWidth',
  'borderRightWidth',
  'borderBottomWidth',
  'borderLeftWidth',
  'borderStyle',

  'paddingTop',
  'paddingRight',
  'paddingBottom',
  'paddingLeft',

  
  'fontStyle',
  'fontVariant',
  'fontWeight',
  'fontStretch',
  'fontSize',
  'fontSizeAdjust',
  'lineHeight',
  'fontFamily',

  'textAlign',
  'textTransform',
  'textIndent',
  'textDecoration',  

  'letterSpacing',
  'wordSpacing',

  'tabSize',
  'MozTabSize'

];

var isBrowser = (typeof window !== 'undefined');
var isFirefox = (isBrowser && window.mozInnerScreenX != null);

function getCaretCoordinates(element, position, options) {
  if(!isBrowser) {
    throw new Error('textarea-caret-position#getCaretCoordinates should only be called in a browser');
  }

  var debug = options && options.debug || false;
  if (debug) {
    var el = document.querySelector('#input-textarea-caret-position-mirror-div');
    if ( el ) { el.parentNode.removeChild(el); }
  }

  
  var div = document.createElement('div');
  div.id = 'input-textarea-caret-position-mirror-div';
  document.body.appendChild(div);

  var style = div.style;
  var computed = window.getComputedStyle? getComputedStyle(element) : element.currentStyle;  

  
  style.whiteSpace = 'pre-wrap';
  if (element.nodeName !== 'INPUT')
    style.wordWrap = 'break-word';  

  
  style.position = 'absolute';  
  if (!debug)
    style.visibility = 'hidden';  

  
  properties.forEach(function (prop) {
    style[prop] = computed[prop];
  });

  if (isFirefox) {
    
    if (element.scrollHeight > parseInt(computed.height))
      style.overflowY = 'scroll';
  } else {
    style.overflow = 'hidden';  
  }

  div.textContent = element.value.substring(0, position);
  
  if (element.nodeName === 'INPUT')
    div.textContent = div.textContent.replace(/\s/g, '\u00a0');

  var span = document.createElement('span');
  
  
  
  
  
  span.textContent = element.value.substring(position) || '.';  
  div.appendChild(span);

  var coordinates = {
    top: span.offsetTop + parseInt(computed['borderTopWidth']),
    left: span.offsetLeft + parseInt(computed['borderLeftWidth'])
  };

  if (debug) {
    span.style.backgroundColor = '#aaa';
  } else {
    document.body.removeChild(div);
  }

  return coordinates;
}

$.fn.textcomplete.getCaretCoordinates = getCaretCoordinates;

}(jQuery));

return jQuery;
}));

Espo.loader.setContextId(null);
