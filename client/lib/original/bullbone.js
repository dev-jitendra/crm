Espo.loader.setContextId('lib!bullbone');
(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports, require('underscore'), require('jquery'), require('handlebars')) :
    typeof define === 'function' && define.amd ? define('bullbone', ['exports', 'underscore', 'jquery', 'handlebars'], factory) :
    (global = typeof globalThis !== 'undefined' ? globalThis : global || self, factory(global.bullbone = {}, global._, global.$, global.Handlebard));
})(this, (function (exports, _, $, Handlebars) { 'use strict';

    

    
    const Events = {};

    if ('Backbone' in window) {
        
        window.Backbone.Events = Events;
    }

    const eventSplitter = /\s+/;

    let _listening;

    const eventsApi = (iteratee, events, name, callback, opts) => {
        let i = 0, names;

        if (name && typeof name === 'object') {
            
            if (callback !== void 0 && 'context' in opts && opts.context === void 0) {
                opts.context = callback;
            }

            for (names = _.keys(name); i < names.length ; i++) {
                events = eventsApi(iteratee, events, names[i], name[names[i]], opts);
            }
        } else if (name && eventSplitter.test(name)) {
            
            for (names = name.split(eventSplitter); i < names.length; i++) {
                events = iteratee(events, names[i], callback, opts);
            }
        } else {
            
            events = iteratee(events, name, callback, opts);
        }

        return events;
    };

    
    Events.on = function (name, callback, context) {
        this._events = eventsApi(onApi, this._events || {}, name, callback, {
            context: context,
            ctx: this,
            listening: _listening,
        });

        if (_listening) {
            let listeners = this._listeners || (this._listeners = {});

            listeners[_listening.id] = _listening;
            
            
            _listening.interop = false;
        }

        return this;
    };

    
    Events.listenTo = function (other, name, callback) {
        if (!other) {
            return this;
        }

        let id = other._listenId || (other._listenId = _.uniqueId('l'));
        let listeningTo = this._listeningTo || (this._listeningTo = {});
        let listening = _listening = listeningTo[id];

        
        
        if (!listening) {
            this._listenId || (this._listenId = _.uniqueId('l'));

            listening = _listening = listeningTo[id] = new Listening(this, other);
        }

        
        let error = tryCatchOn(other, name, callback, this);
        _listening = void 0;

        if (error) {
            throw error;
        }

        
        if (listening.interop) {
            listening.on(name, callback);
        }

        return this;
    };

    const onApi = (events, name, callback, options) => {
        if (callback) {
            let handlers = events[name] || (events[name] = []);
            let context = options.context, ctx = options.ctx, listening = options.listening;

            if (listening) {
                listening.count++;
            }

            handlers.push({callback: callback, context: context, ctx: context || ctx, listening: listening});
        }

        return events;
    };

    const tryCatchOn = (obj, name, callback, context) => {
        try {
            obj.on(name, callback, context);
        } catch (e) {
            return e;
        }
    };

    
    Events.off = function(name, callback, context) {
        if (!this._events) {
            return this;
        }

        this._events = eventsApi(offApi, this._events, name, callback, {
            context: context,
            listeners: this._listeners
        });

        return this;
    };

    
    Events.stopListening = function (other, name, callback) {
        let listeningTo = this._listeningTo;

        if (!listeningTo) {
            return this;
        }

        let ids = other ? [other._listenId] : _.keys(listeningTo);

        for (let i = 0; i < ids.length; i++) {
            let listening = listeningTo[ids[i]];

            
            
            if (!listening) {
                break;
            }

            listening.obj.off(name, callback, this);

            if (listening.interop) {
                listening.off(name, callback);
            }
        }

        if (_.isEmpty(listeningTo)) {
            this._listeningTo = void 0;
        }

        return this;
    };

    const offApi = (events, name, callback, options) => {
        if (!events) {
            return;
        }

        let context = options.context, listeners = options.listeners;
        let i = 0, names;

        
        if (!name && !context && !callback) {
            for (names = _.keys(listeners); i < names.length; i++) {
                listeners[names[i]].cleanup();
            }

            return;
        }

        names = name ? [name] : _.keys(events);

        for (; i < names.length; i++) {
            name = names[i];
            let handlers = events[name];

            
            if (!handlers) {
                break;
            }

            
            let remaining = [];

            for (let j = 0; j < handlers.length; j++) {
                let handler = handlers[j];

                if (
                    callback && callback !== handler.callback &&
                    callback !== handler.callback._callback ||
                    context && context !== handler.context
                ) {
                    remaining.push(handler);
                } else {
                    let listening = handler.listening;

                    if (listening) {
                        listening.off(name, callback);
                    }
                }
            }

            
            if (remaining.length) {
                events[name] = remaining;
            } else {
                delete events[name];
            }
        }

        return events;
    };

    
    Events.once = function (name, callback, context) {
        
        let events = eventsApi(onceMap, {}, name, callback, this.off.bind(this));

        if (typeof name === 'string' && context == null) {
            callback = void 0;
        }

        return this.on(events, callback, context);
    };

    
    Events.listenToOnce = function (other, name, callback) {
        
        let events = eventsApi(onceMap, {}, name, callback, this.stopListening.bind(this, other));

        return this.listenTo(other, events);
    };

    let onceMap = function (map, name, callback, offer) {
        if (callback) {
            let once = map[name] = _.once(function() {
                offer(name, once);
                callback.apply(this, arguments);
            });

            once._callback = callback;
        }

        return map;
    };

    
    Events.trigger = function(name) {
        if (!this._events) {
            return this;
        }

        let length = Math.max(0, arguments.length - 1);
        let args = Array(length);

        for (let i = 0; i < length; i++) {
            args[i] = arguments[i + 1];
        }

        eventsApi(triggerApi, this._events, name, void 0, args);

        return this;
    };

    const triggerApi = (objEvents, name, callback, args) => {
        if (objEvents) {
            let events = objEvents[name];
            let allEvents = objEvents.all;

            if (events && allEvents) {
                allEvents = allEvents.slice();
            }

            if (events) {
                triggerEvents(events, args);
            }

            if (allEvents) {
                triggerEvents(allEvents, [name].concat(args));
            }
        }

        return objEvents;
    };

    const triggerEvents = (events, args) => {
        let ev,
            i = -1,
            l = events.length,
            a1 = args[0],
            a2 = args[1],
            a3 = args[2];

        switch (args.length) {
            case 0: while (++i < l) (ev = events[i]).callback.call(ev.ctx); return;
            case 1: while (++i < l) (ev = events[i]).callback.call(ev.ctx, a1); return;
            case 2: while (++i < l) (ev = events[i]).callback.call(ev.ctx, a1, a2); return;
            case 3: while (++i < l) (ev = events[i]).callback.call(ev.ctx, a1, a2, a3); return;
            default: while (++i < l) (ev = events[i]).callback.apply(ev.ctx, args); return;
        }
    };

    const Listening = function(listener, obj) {
        this.id = listener._listenId;
        this.listener = listener;
        this.obj = obj;
        this.interop = true;
        this.count = 0;
        this._events = void 0;
    };

    Listening.prototype.on = Events.on;

    Listening.prototype.off = function (name, callback) {
        let cleanup;

        if (this.interop) {
            this._events = eventsApi(offApi, this._events, name, callback, {
                context: void 0,
                listeners: void 0
            });

            cleanup = !this._events;
        }
        else {
            this.count--;

            cleanup = this.count === 0;
        }

        if (cleanup) {
            this.cleanup();
        }
    };

    Listening.prototype.cleanup = function () {
        delete this.listener._listeningTo[this.obj._listenId];

        if (!this.interop) {
            delete this.obj._listeners[this.id];
        }
    };

    


    

    

    

    

    

    

    

    

    

    

    

    

    

    

    

    

    

    

    

    

    

    

    

    
    class View {

        
        constructor(options = {}) {
            this.cid = _.uniqueId('view');

            if ('model' in options) {
                this.model = options.model;
            }

            if ('collection' in options) {
                this.collection = options.collection;
            }

            this.$el = $();
            this.options = options;

            let fullSelector = options.fullSelector || options.el;

            if (fullSelector) {
                this.setSelector(fullSelector);
            }
        }

        
        cid

        
        _elementSelector

        
        isComponent = false

        
        element

        
        template = null

        
        templateContent = null

        
        events = null

        
        notToRender = false

        
        _layoutDefs = null

        
        layoutData = null

        
        isReady = false

        
        views = null

        
        optionsToPass = null

        
        nestedViews = null

        
        _factory = null

        
        _helper = null

        
        _template = null
        
        _nestedViewDefs = null
        
        _templator = null
        
        _renderer = null
        
        _layouter = null
        
        _templateCompiled = null
        
        _parentView = null
        
        _path = ''
        
        _wait = false
        
        _waitViewList = null
        
        _nestedViewsFromLayoutLoaded = false
        
        _readyConditionList = null
        
        _isRendered = false
        
        _isFullyRendered = false
        
        _isBeingRendered = false
        
        _isRemoved = false
        
        _isRenderCanceled = false
        
        _preCompiledTemplates = null

        
        setElement(selector) {
            this.undelegateEvents();
            this._setElement(selector);
            this._delegateEvents();
        }

        
        undelegateEvents() {
            if (!this.$el) {
                return;
            }

            this.$el.off('.delegateEvents' + this.cid);
        }

        
        _delegateEvents() {
            let events = _.result(this, 'events');

            if (!events) {
                return;
            }

            this.undelegateEvents();

            for (let key in events) {
                let method = events[key];

                if (typeof method !== 'function') {
                    method = this[method];
                }

                if (!method) {
                    continue;
                }

                let match = key.match(delegateEventSplitter);

                this._delegate(match[1], match[2], method.bind(this));
            }
        }

        
        _delegate(eventName, selector, listener) {
            this.$el.on(eventName + '.delegateEvents' + this.cid, selector, listener);
        }

        
        addHandler(type, selector, handler) {
            let key = type + ' ' + selector;

            if (typeof handler === 'function') {
                this.events[key] = (e) => handler(e.originalEvent, e.currentTarget);

                return;
            }

            if (typeof this[handler] !== 'function') {
                console.warn(`Could not add event handler. No '${handler}' method.`);

                return;
            }

            this.events[key] = (e) => this[handler](e.originalEvent, e.currentTarget);
        }

        
        _initialize(data) {
            
            this._factory = data.factory;

            
            this._renderer = data.renderer;

            
            this._templator = data.templator;

            
            this._layouter = data.layouter;

            
            this._onReady = data.onReady || null;

            
            this._helper = data.helper || null;

            
            this._preCompiledTemplates = data.preCompiledTemplates || {};

            this.events = _.clone(this.events || {});
            this.notToRender = ('notToRender' in this.options) ? this.options.notToRender : this.notToRender;

            this.nestedViews = {};
            
            this._nestedViewDefs = {};

            if (this._waitViewList == null) {
                
                this._waitViewList = [];
            }

            
            this._waitPromiseCount = 0;

            if (this._readyConditionList == null) {
                
                this._readyConditionList = [];
            }

            this.optionsToPass = this.options.optionsToPass || this.optionsToPass || [];

            let merge = function (target, source) {
                for (let prop in source) {
                    if (typeof target === 'object') {
                        if (prop in target) {
                            merge(target[prop], source[prop]);
                        } else {
                            target[prop] = source[prop];
                        }
                    }
                }

                return target;
            };

            if (this.views || this.options.views) {
                this.views = merge(this.options.views || {}, this.views || {});
            }

            this.init();
            this.setup();
            this.setupFinal();

            this.template = this.options.template || this.template;

            
            this._layoutDefs = this.options.layoutDefs || this.options._layout;
            
            this.layoutData = this.options.layoutData || this.layoutData;
            
            this._template = this.templateContent || this.options.templateContent || this._template;

            if (this._template != null && this._templator.compilable) {
                
                this._templateCompiled = this._templator.compileTemplate(this._template);
            }

            if (this._elementSelector) {
                this.setElementInAdvance(this._elementSelector);
            }

            const loadNestedViews = () => {
                this._loadNestedViews(() => {
                    this._nestedViewsFromLayoutLoaded = true;

                    this._tryReady();
                });
            };

            if (this._layoutDefs !== null) {
                loadNestedViews();

                return;
            }

            if (this.views != null) {
                loadNestedViews();

                return;
            }

            this._nestedViewsFromLayoutLoaded = true;

            this._tryReady();
        }

        
        data() {
            return {};
        }

        
        init() {}

        
        setup() {}

        
        setupFinal() {}

        
        setElementInAdvance(fullSelector) {
            if (this._setElementInAdvancedInProcess) {
                return;
            }

            this._setElementInAdvancedInProcess = true;

            this.on('after:render-internal', () => {
                this.setElement(fullSelector);

                this._setElementInAdvancedInProcess = false;
            });
        }

        
        getSelector() {
            return this._elementSelector || null
        }

        
        setSelector(selector) {
            this._elementSelector = selector;

            
            this.options.el = selector;
        }

        
        isRendered() {
            return this._isRendered;
        }

        
        isFullyRendered() {
            return this._isFullyRendered;
        }

        
        isBeingRendered() {
            return this._isBeingRendered;
        }

        
        isRemoved() {
            return this._isRemoved;
        }

        
        getHtml(callback) {
            this._getHtml(callback);
        }

        
        cancelRender() {
            if (!this.isBeingRendered()) {
                return;
            }

            this._isRenderCanceled = true;
        }

        
        uncancelRender() {
            this._isRenderCanceled = false;
        }

        
        render(callback) {
            this._isRendered = false;
            this._isFullyRendered = false;

            return new Promise(resolve => {
                this._getHtml(html => {
                    if (this._isRenderCanceled) {
                        this._isRenderCanceled = false;
                        this._isBeingRendered = false;

                        return;
                    }

                    this.isComponent ?
                        this._renderComponentInDom(html) :
                        this._renderInDom(html);

                    if (!this.element) {
                        let msg = this._elementSelector ?
                            `Could not set element '${this._elementSelector}'.` :
                            `Could not set element. No selector.`;

                        console.warn(msg);
                    }

                    this._afterRender();

                    if (typeof callback === 'function') {
                        callback();
                    }

                    resolve(this);
                });
            });
        }

        
        _renderComponentInDom(html) {
            if (!this.element) {
                if (!this._elementSelector) {
                    console.warn(`Can't render component. No DOM selector.`);

                    return;
                }

                this._setElement(this._elementSelector);
            }

            if (!this.element) {
                console.warn(`Can't render component. No DOM element.`);

                return;
            }

            let div = document.createElement('div');
            div.innerHTML = html;
            let element = div.children[0];

            let parent = this.element.parentElement;

            parent.replaceChild(element, this.element);

            this.setElement(this._elementSelector);
        }

        
        _renderInDom(html) {
            if (!this.$el.length && this._elementSelector) {
                this.setElement(this._elementSelector);
            }

            this.$el.html(html);
        }

        
        reRender(force) {
            if (this.isRendered()) {
                return this.render();
            }

            if (this.isBeingRendered()) {
                return new Promise((resolve, reject) => {
                    this.once('after:render', () => {
                        this.render()
                            .then(() => resolve(this))
                            .catch(reject);
                    });
                });
            }

            if (force) {
                return this.render();
            }

            
            return new Promise(() => {});
        }

        
        _afterRender() {
            this._isBeingRendered = false;
            this._isRendered = true;

            this.trigger('after:render-internal', this);

            for (let key in this.nestedViews) {
                let nestedView = this.nestedViews[key];

                if (!nestedView.notToRender) {
                    nestedView._afterRender();
                }
            }

            this.afterRender();

            this.trigger('after:render', this);

            this._isFullyRendered = true;
        }

        
        afterRender() {}

        
        whenRendered() {
            if (this.isRendered()) {
                return Promise.resolve();
            }

            return new Promise(resolve => {
                this.once('after:render', () => resolve());
            });
        }

        
        _tryReady() {
            if (this.isReady) {
                return;
            }

            if (this._wait) {
                return;
            }

            if (!this._nestedViewsFromLayoutLoaded) {
                return;
            }

            for (let i = 0; i < this._waitViewList.length; i++) {
                if (!this.hasView(this._waitViewList[i])) {
                    return;
                }
            }

            if (this._waitPromiseCount) {
                return;
            }

            for (let i = 0; i < this._readyConditionList.length; i++) {
                if (typeof this._readyConditionList[i] === 'function') {
                    if (!this._readyConditionList[i]()) {
                        return;
                    }
                }
                else {
                    if (!this._readyConditionList) {
                        return;
                    }
                }
            }

            this._makeReady();
        }

        
        _makeReady() {
            this.isReady = true;
            this.trigger('ready');

            if (typeof this._onReady === 'function') {
                this._onReady(this);
            }
        }

        
        _addDefinedNestedViewDefs(list) {
            for (let name in this.views) {
                let o = _.clone(this.views[name]);

                o.name = name;

                list.push(o);

                this._nestedViewDefs[name] = o;
            }

            return list;
        }

        
        _getNestedViewDefsFromLayout() {
            let itemList = this._layouter.findNestedViews(this._layoutDefs);

            if (Object.prototype.toString.call(itemList) !== '[object Array]') {
                throw new Error(`Bad layout. It should be an array.`);
            }

            let nestedViewDefsFiltered = [];

            for (let item of itemList) {
                let key = item.name;

                this._nestedViewDefs[key] = item;

                if ('view' in item && item.view === true) {
                    if (!('template' in item)) {
                        continue;
                    }
                }

                nestedViewDefsFiltered.push(item);
            }

            return nestedViewDefsFiltered;
        }

        
        _loadNestedViews(callback) {
            let nestedViewDefs = this._layoutDefs != null ?
                this._getNestedViewDefsFromLayout() : [];

            this._addDefinedNestedViewDefs(nestedViewDefs);

            let count = nestedViewDefs.length;
            let loaded = 0;

            const tryReady = () => {
                if (loaded === count) {
                    callback();
                }
            };

            tryReady();

            nestedViewDefs.forEach(def => {
                let key = def.name;
                let viewName = this._factory.defaultViewName;
                let view;

                if ('view' in def) {
                    if (def.view != null && typeof def.view === 'object') {
                        view = def.view;
                    }
                    else {
                        viewName = def.view;
                    }
                }

                if (viewName === false) {
                    loaded++;
                    tryReady();

                    return;
                }

                if (typeof view === 'object') {
                    this.assignView(key, view, def.selector)
                        .then(() => {
                            loaded++;
                            tryReady();
                        });

                    return;
                }

                let options = {};

                if ('template' in def) {
                    options.template = def.template;
                }

                
                let fullSelector = def.fullSelector || def.el;

                if (fullSelector) {
                    options.fullSelector = fullSelector;
                }
                else if ('selector' in def) {
                    options.selector = def.selector;
                }

                if ('options' in def) {
                    options = {...options, ...def.options};
                }

                if (this.model) {
                    options.model = this.model;
                }

                if (this.collection) {
                    options.collection = this.collection;
                }

                for (let k in this.optionsToPass) {
                    let name = this.optionsToPass[k];

                    options[name] = this.options[name];
                }

                this._factory.create(viewName, options, view => {
                    if ('notToRender' in def) {
                        view.notToRender = def.notToRender;
                    }

                    this.setView(key, view);

                    loaded++;
                    tryReady();
                });
            });
        }

        
        _getData() {
            if (this.options.data) {
                if (typeof this.options.data === 'function') {
                    return this.options.data();
                }

                return this.options.data;
            }

            if (typeof this.data === 'function') {
                return this.data();
            }

            return this.data;
        }

        
        _getNestedViewsAsArray() {
            let nestedViewsArray = [];

            for (let key in this.nestedViews) {
                nestedViewsArray.push({
                    key: key,
                    view: this.nestedViews[key],
                });
            }

            return nestedViewsArray;
        }

        
        _getNestedViewsHtmlMap(callback) {
            let data = {};
            let items = this._getNestedViewsAsArray();

            let loaded = 0;
            let count = items.length;

            let tryReady = () => {
                if (loaded === count) {
                    callback(data);
                }
            };

            tryReady();

            items.forEach(item => {
                let key = item.key;
                let view = item.view;

                if (view.notToRender) {
                    if (view.isComponent) {
                        data[key] = this._createPlaceholderElement(view.cid).outerHTML;
                    }

                    loaded++;
                    tryReady();

                    return;
                }

                view.getHtml(html => {
                    data[key] = html;

                    loaded++;
                    tryReady();
                });
            });
        }

        
        handleDataBeforeRender(data) {}

        
        _getHtml(callback) {
            this._isBeingRendered = true;
            this.trigger('render', this);

            this._getNestedViewsHtmlMap(htmlMap => {
                let data = {...this._getData(), ...htmlMap};

                if (this.collection || null) {
                    data.collection = this.collection;
                }

                if (this.model || null) {
                    data.model = this.model;
                }

                data.viewObject = this;

                this.handleDataBeforeRender(data);

                this._getTemplate(template => {
                    let html = this._renderer.render(template, data);

                    if (!this.isComponent) {
                        callback(html);

                        return;
                    }

                    let root = (new DOMParser())
                        .parseFromString(html, 'text/html')
                        .body
                        .children[0];

                    if (!root) {
                        throw new Error(`Bad DOM. No root.`);
                    }

                    root.setAttribute('data-view-cid', this.cid);

                    callback(root.outerHTML);
                });
            });
        }

        
        _getTemplateName() {
            return this.template || null;
        }

        
        _getLayoutData() {
            return this.layoutData;
        }

        
        _getTemplate(callback) {
            if (
                this._templator &&
                this._templator.compilable &&
                this._templateCompiled !== null
            ) {
                callback(this._templateCompiled);

                return;
            }

            let _template = this._template || null;

            if (_template !== null) {
                callback(_template);

                return;
            }

            let templateName = this._getTemplateName();

            if (
                templateName &&
                templateName in (this._preCompiledTemplates || {})
            ) {
                callback(this._preCompiledTemplates[templateName]);

                return;
            }

            let layoutOptions = {};

            if (!templateName) {
                layoutOptions = {
                    data: this._getLayoutData(),
                    layout: this._layoutDefs,
                };
            }

            this._templator.getTemplate(templateName, layoutOptions, callback);
        }

        
        _updatePath(parentPath, viewKey) {
            this._path = parentPath + '/' + viewKey;

            for (let key in this.nestedViews) {
                this.nestedViews[key]._updatePath(this._path, key);
            }
        }

        
        _getSelectorForNestedView(key) {
            if (!(key in this._nestedViewDefs)) {
                return null;
            }

            if ('id' in this._nestedViewDefs[key]) {
                return '#' + this._nestedViewDefs[key].id;
            }

            let fullSelector = this._nestedViewDefs[key].fullSelector ||
                this._nestedViewDefs[key].el;

            if (fullSelector) {
                return fullSelector;
            }

            let currentEl = this.getSelector();

            if (!currentEl) {
                return null;
            }

            if ('selector' in this._nestedViewDefs[key]) {
                return currentEl + ' ' + this._nestedViewDefs[key].selector;
            }

            return currentEl + ' [data-view="' + key + '"]';
        }

        
        hasView(key) {
            return key in this.nestedViews;
        }

        
        getView(key) {
            if (key in this.nestedViews) {
                return this.nestedViews[key];
            }

            return null;
        }

        
        getViewKey(view) {
            for (let key in this.nestedViews) {
                if (view === this.nestedViews[key]) {
                    return key;
                }
            }

            return null;
        }

        
        assignView(key, view, selector) {
            this.clearView(key);

            this._viewPromiseHash = this._viewPromiseHash || {};
            let promise = null;

            promise = this._viewPromiseHash[key] = new Promise(resolve => {
                if (!this.isReady) {
                    this.waitForView(key);
                }

                if (!selector && view.isComponent) {
                    selector = `[data-view-cid="${view.cid}"]`;
                }

                if (selector) {
                    

                    view.setSelector(this.getSelector() + ' ' + selector);
                }

                view._initialize({
                    factory: this._factory,
                    layouter: this._layouter,
                    templator: this._templator,
                    renderer: this._renderer,
                    helper: this._helper,
                    onReady: () => this._assignViewCallback(key, view, resolve, promise),
                });
            });

            return promise;
        }

        
        createView(key, viewName, options, callback, wait) {
            this.clearView(key);

            this._viewPromiseHash = this._viewPromiseHash || {};

            let promise = null;

            promise = this._viewPromiseHash[key] = new Promise(resolve => {
                wait = (typeof wait === 'undefined') ? true : wait;

                if (wait) {
                    this.waitForView(key);
                }

                options = options || {};

                let fullSelector = options.fullSelector || options.el;

                if (!fullSelector && options.selector) {
                    options.fullSelector = this.getSelector() + ' ' + options.selector;
                }

                

                this._factory.create(viewName, options, view => {
                    if (view.isComponent && !options.fullSelector) {
                        options.fullSelector = this.getSelector() + ` [data-view-cid="${view.cid}"]`;
                    }

                    this._assignViewCallback(
                        key,
                        view,
                        resolve,
                        promise,
                        callback,
                        options.setViewBeforeCallback
                    );
                });
            });

            return promise;
        }

        
        _assignViewCallback(
            key,
            view,
            resolve,
            promise,
            callback,
            setViewBeforeCallback
        ) {
            let previousView = this.getView(key);

            if (previousView) {
                previousView.cancelRender();
            }

            delete this._viewPromiseHash[key];

            
            if (promise && promise._isToCancel) {
                if (!view.isRemoved()) {
                    view.remove();
                }

                return;
            }

            let isSet = false;

            if (this._isRendered || setViewBeforeCallback) {
                this.setView(key, view);

                isSet = true;
            }

            if (typeof callback === 'function') {
                callback.call(this, view);
            }

            resolve(view);

            if (!this._isRendered && !setViewBeforeCallback && !isSet) {
                this.setView(key, view);
            }
        }

        
        setView(key, view, fullSelector) {
            fullSelector = fullSelector || this._getSelectorForNestedView(key) || view.getSelector();

            if (fullSelector) {
                this.isRendered() ?
                    view.setElement(fullSelector) :
                    view.setElementInAdvance(fullSelector);
            }

            if (key in this.nestedViews) {
                this.clearView(key);
            }

            this.nestedViews[key] = view;

            view._parentView = this;
            view._updatePath(this._path, key);

            this._tryReady();
        }

        
        clearView(key) {
            if (key in this.nestedViews) {
                this.nestedViews[key].remove();

                delete this.nestedViews[key];
            }

            this._viewPromiseHash = this._viewPromiseHash || {};

            let previousPromise = this._viewPromiseHash[key];

            if (previousPromise) {
                previousPromise._isToCancel = true;
            }
        }

        
        unchainView(key) {
            if (key in this.nestedViews) {
                this.nestedViews[key]._parentView = null;
                this.nestedViews[key].undelegateEvents();

                delete this.nestedViews[key];
            }
        }

        
        getParentView() {
            return this._parentView;
        }

        
        hasParentView() {
            return !!this._parentView;
        }

        
        addReadyCondition(condition) {
            this._readyConditionList.push(condition);
        }

        
        waitForView(key) {
            this._waitViewList.push(key);
        }

        
        wait(wait) {
            if (typeof wait === 'object' && (wait instanceof Promise || typeof wait.then === 'function')) {
                this._waitPromiseCount++;

                wait.then(() => {
                    this._waitPromiseCount--;
                    this._tryReady();
                });

                return;
            }

            if (typeof wait === 'function') {
                this._waitPromiseCount++;

                let promise = new Promise(resolve => {
                    
                    resolve(wait.call(this));
                });

                promise.then(() => {
                    this._waitPromiseCount--;
                    this._tryReady();
                });

                return promise;
            }

            if (wait) {
                this._wait = true;

                return;
            }

            this._wait = false;
            this._tryReady();
        }

        
        _createPlaceholderElement(cid) {
            let span = document.createElement('span');

            span.setAttribute('data-view-cid', cid || this.cid);

            return span;
        }

        
        _replaceWithPlaceholderElement() {
            if (!this.element) {
                return;
            }

            let parent = this.element.parentElement;

            parent.replaceChild(this._createPlaceholderElement(), this.element);
        }

        
        remove(dontEmpty) {
            this.cancelRender();

            for (let key in this.nestedViews) {
                this.clearView(key);
            }

            this.trigger('remove');
            this.onRemove();
            this.off();

            if (!dontEmpty) {
                this.isComponent ?
                    this._replaceWithPlaceholderElement() :
                    this.$el.empty();
            }

            this.stopListening();
            this.undelegateEvents();

            if (this.model && typeof this.model.off === 'function') {
                this.model.off(null, null, this);
            }

            if (this.collection && typeof this.collection.off === 'function') {
                this.collection.off(null, null, this);
            }

            this._isRendered = false;
            this._isFullyRendered = false;
            this._isBeingRendered = false;
            this._isRemoved = true;

            return this;
        }

        
        onRemove() {}

        
        _setElement(fullSelector) {
            const setElement = () => {
                this.element = this.$el[0];

                
                this.el = this.element;
            };

            if (typeof fullSelector === 'string') {
                let parentView = this.getParentView();

                if (
                    parentView &&
                    parentView.isRendered() &&
                    parentView.$el &&
                    parentView.$el.length &&
                    parentView.getSelector() &&
                    fullSelector.indexOf(parentView.getSelector()) === 0
                ) {
                    let subSelector = fullSelector.slice(parentView.getSelector().length);

                    this.$el = $(subSelector, parentView.$el).eq(0);

                    setElement();

                    return;
                }
            }

            this.$el = $(fullSelector).eq(0);

            setElement();
        }

        
        propagateEvent() {
            this.trigger.apply(this, arguments);

            for (let key in this.nestedViews) {
                let view = this.nestedViews[key];

                view.propagateEvent.apply(view, arguments);
            }
        }

        
        setTemplate(template) {
            this.template = template;

            this._templateCompiled = null;
        }

        
        setTemplateContent(templateContent) {
            this._templateCompiled = this._templator.compileTemplate(templateContent);
        }
    }

    Object.assign(View.prototype, Events);

    const isEsClass = fn => {
        return typeof fn === 'function' &&
            Object.getOwnPropertyDescriptor(fn, 'prototype')?.writable === false;
    };

    View.extend = function (protoProps, staticProps) {
        let parent = this;

        let child;

        if (isEsClass(parent)) {
            let TemporaryHelperConstructor = function () {};

            child = function () {
                if (new.target) {
                    
                    let obj = Reflect.construct(parent, arguments, new.target);

                    for (let prop of Object.getOwnPropertyNames(obj)) {
                        if (typeof this[prop] !== 'undefined') {
                            obj[prop] = this[prop];
                        }
                    }

                    return obj;
                }

                
                return Reflect.construct(parent, arguments, TemporaryHelperConstructor);
            };

            _.extend(child, parent, staticProps);

            
            child.prototype = _.create(parent.prototype, protoProps);
            child.prototype.constructor = child;
            
            child.__super__ = parent.prototype;
            child.prototype.__isEs = true;

            TemporaryHelperConstructor.prototype = child.prototype;

            return child;
        }

        child = function () {
            
            if (parent.prototype.__isEs) {
                
                return Reflect.construct(parent, arguments, new.target);
            }

            
            return parent.apply(this, arguments);
        };

        _.extend(child, parent, staticProps);

        
        child.prototype = _.create(parent.prototype, protoProps);
        child.prototype.constructor = child;
        
        child.__super__ = parent.prototype;

        return child;
    };

    let delegateEventSplitter = /^(\S+)\s*(.*)$/;

    class Loader {

        
        constructor(options) {
            options = {...options};

            this._paths = _.extend(this._paths, options.paths || {});
            this._exts = _.extend(this._exts, options.exts || {});
            this._normalize = _.extend(this._normalize, options.normalize || {});
            this._isJson = _.extend(this._isJson, options.isJson || {});
            this._externalLoaders = _.extend(this._externalLoaders, options.loaders || {});
            this._externalPathFunction = options.path || null;
        }

        _exts = {
            layout: 'json',
            template: 'tpl',
            layoutTemplate: 'tpl',
        }

        _paths = {
            layout: 'layouts',
            template: 'templates',
            layoutTemplate: 'templates/layouts',
        }

        _isJson = {
            layout: true,
        }

        _externalLoaders = {
            layout: null,
            template: null,
            layoutTemplate: null,
        }

        _externalPathFunction = null

        _normalize = {
            layouts: function (name) {
                return name;
            },
            templates: function (name) {
                return name;
            },
            layoutTemplates: function (name) {
                return name;
            },
        }

        getFilePath(type, name) {
            if (!(type in this._paths) || !(type in this._exts)) {
                throw new TypeError("Unknown resource type \"" + type + "\" requested in Bull.Loader.");
            }

            let namePart = name;

            if (type in this._normalize) {
                namePart = this._normalize[type](name);
            }

            let pathPart = this._paths[type];

            if (pathPart.substr(-1) === '/') {
                pathPart = pathPart.substr(0, pathPart.length - 1);
            }

            return pathPart + '/' + namePart + '.' + this._exts[type];
        }

        _callExternalLoader(type, name, callback) {
            if (type in this._externalLoaders && this._externalLoaders[type] !== null) {
                if (typeof this._externalLoaders[type] === 'function') {
                    this._externalLoaders[type](name, callback);

                    return true;
                }

                throw new Error("Loader for \"" + type + "\" in not a Function.");
            }

            return null;
        }

        load(type, name, callback) {
            let customCalled = this._callExternalLoader(type, name, callback);

            if (customCalled) {
                return;
            }

            let response, filePath;

            if (this._externalPathFunction != null) {
                filePath = this._externalPathFunction.call(this, type, name);
            } else {
                filePath = this.getFilePath(type, name);
            }

            filePath += '?_=' + new Date().getTime();

            let xhr = new XMLHttpRequest();

            xhr.open('GET', filePath, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4) {
                    response = xhr.responseText;

                    if (type in this._isJson) {
                        if (this._isJson[type]) {
                            let obj;

                            if (xhr.status === 404 || xhr.status === 403) {
                                throw new Error("Could not load " + type + " \"" + name + "\".");
                            }

                            try {
                                obj = JSON.parse(String(response));
                            }
                            catch (e) {
                                throw new SyntaxError(
                                    "Error while parsing " + type + " \"" + name + "\": (" + e.message + ").");
                            }

                            callback(obj);

                            return;
                        }
                    }

                    callback(response);
                }
            };

            xhr.send(null);
        }
    }

    
    class Renderer {

        constructor(options) {
            options = options || {};

            this._render = options.method || this._render;
        }

        render(template, data) {
            return this._render.call(this, template, data);
        }

        _render(template, data) {
            return template(data, {allowProtoPropertiesByDefault: true});
        }
    }

    
    class Layouter {

        
        findNestedViews(layoutDefs) {
            if (!layoutDefs) {
                throw new Error("Can not find nested views. No layout data and name.");
            }

            let layout = layoutDefs.layout;
            let viewPathList = [];

            const uniqName = (name, count) => {
                let modName = name;

                if (typeof count !== 'undefined') {
                    modName = modName + '_' + count;
                } else {
                    count = 0;
                }

                for (let i in viewPathList) {
                    if (viewPathList[i].name === modName) {
                        return uniqName(name, count + 1);
                    }
                }

                return modName;
            };

            const getDefsForNestedView = (defsInLayout) => {
                let defs = {};

                let params = [
                    'view',
                    'layout',
                    'notToRender',
                    'options',
                    'template',
                    'id',
                    'selector',
                    'el',
                ];

                for (let i in params) {
                    let param = params[i];

                    if (param in defsInLayout) {
                        defs[param] = defsInLayout[param];
                    }
                }

                if ('name' in defsInLayout) {
                    defs.name = uniqName(defsInLayout.name);
                }

                return defs;
            };

            const seekForViews = (tree) => {
                for (let key in tree) {
                    if (tree[key] == null || typeof tree[key] !== 'object') {
                        continue;
                    }

                    if ('view' in tree[key] || 'layout' in tree[key] || 'template' in tree[key]) {
                        let def = getDefsForNestedView(tree[key]);

                        if ('name' in def) {
                            viewPathList.push(def);
                        }

                        continue;
                    }

                    seekForViews(tree[key]);
                }
            };

            seekForViews(layout);

            return viewPathList;
        }
    }

    
    class Templator {

        
        constructor(data) {
            data = data || {};

            this._templates = {};
            this._layoutTemplates = {};

            
            this._loader = data.loader || null;

            if ('compilable' in data) {
                this.compilable = data.compilable;
            }
        }

        compilable = true

        _templates = null
        _layoutTemplates = null

        addTemplate(name, template) {
            this._templates[name] = template;
        }

        
        getTemplate(name, layoutOptions,  callback) {
            layoutOptions = layoutOptions || {};

            if (!layoutOptions.layout && !name) {
                throw new Error(`Can not get template. Not enough data passed.`);
            }

            if (name) {
                let template = this._getCachedTemplate(name);

                if (template) {
                    callback(template);

                    return;
                }
            }

            let then = (template) => {
                if (this.compilable) {
                    template = this.compileTemplate(template);
                }

                this._templates[name] = template;

                callback(template);
            };

            if (layoutOptions.layout) {
                this._buildTemplate(layoutOptions.layout, layoutOptions.data, then);

                return;
            }

            this._loader.load('template', name, then);
        }

        compileTemplate(template) {
            if (typeof Handlebars !== 'undefined') {
                return Handlebars.compile(template);
            }

            return template;
        }

        _getCachedTemplate(templateName) {
            if (templateName in this._templates) {
                return this._templates[templateName];
            }

            return false;
        }

        _getCachedLayoutTemplate(layoutType) {
            if (layoutType in this._layoutTemplates) {
                return this._layoutTemplates[layoutType];
            }

            return false;
        }

        _cacheLayoutTemplate(layoutType, layoutTemplate) {
            this._layoutTemplates[layoutType] = layoutTemplate;
        }

        _buildTemplate(layoutDefs, data, callback) {
            let layoutType = layoutDefs.type || 'default';

            const proceed = layoutTemplate => {
                let injection = _.extend(layoutDefs, data || {});
                let template = _.template(layoutTemplate, injection);

                if (typeof template === 'function') {
                    template = template(injection);
                }

                callback(template);
            };

            let layoutTemplate = this._getCachedLayoutTemplate(layoutType);

            if (layoutTemplate) {
                proceed(layoutTemplate);

                return;
            }

            this._loader.load('layoutTemplate', layoutType, layoutTemplate => {
                this._cacheLayoutTemplate(layoutType, layoutTemplate);

                proceed(layoutTemplate);
            });
        }
    }

    let root = window;

    

    
    class Factory {

        
        constructor(options) {
            options = options || {};

            this.defaultViewName = options.defaultViewName || this.defaultViewName;

            this._loader = options.customLoader || new Loader(options.resources || {});
            this._renderer = options.customRenderer || new Renderer();
            this._layouter = options.customLayouter || new Layouter();
            this._templator = options.customTemplator || new Templator({loader: this._loader});

            this._helper = options.helper || null;

            this._viewClassHash = {};
            this._getViewClassFunction = options.viewLoader || this._getViewClassFunction;
            this._viewLoader = this._getViewClassFunction;
            this._preCompiledTemplates = options.preCompiledTemplates;
        }

        
        defaultViewName = 'View'
        
        _layouter = null
        
        _templator = null
        
        _renderer = null
        
        _loader = null
        
        _helper = null
        
        _viewClassHash = null
        
        _viewLoader = null

        
        create(viewName, options, callback) {
            this._getViewClass(viewName, viewClass => {
                if (typeof viewClass === 'undefined') {
                    throw new Error(`A view class '${viewName}' not found.`);
                }

                const view = new viewClass(options || {});

                this.prepare(view, callback);
            });
        }

        
        prepare(view, callback) {
            view._initialize({
                factory: this,
                layouter: this._layouter,
                templator: this._templator,
                renderer: this._renderer,
                helper: this._helper,
                preCompiledTemplates: this._preCompiledTemplates,
                onReady: callback,
            });
        }

        
        _getViewClassFunction(viewName, callback) {
            let viewClass = root[viewName];

            if (typeof viewClass !== "function") {
                throw new Error("function \"" + viewClass + "\" not found.");
            }

            callback(viewClass);
        }

        
        _getViewClass(viewName, callback) {
            if (viewName in this._viewClassHash) {
                callback(this._viewClassHash[viewName]);

                return;
            }

            this._getViewClassFunction(viewName, (viewClass) => {
                this._viewClassHash[viewName] = viewClass;

                callback(viewClass);
            });
        }
    }

    exports.Events = Events;
    exports.Factory = Factory;
    exports.View = View;

}));

Espo.loader.setContextId(null);
