



import Backbone from 'backbone';















const Router = Backbone.Router.extend( {

    
    routeList: [
        {
            route: "clearCache",
            resolution: "clearCache"
        },
        {
            route: ":controller/view/:id/:options",
            resolution: "view"
        },
        {
            route: ":controller/view/:id",
            resolution: "view"
        },
        {
            route: ":controller/edit/:id/:options",
            resolution: "edit"
        },
        {
            route: ":controller/edit/:id",
            resolution: "edit"
        },
        {
            route: ":controller/create",
            resolution: "create"
        },
        {
            route: ":controller/related/:id/:link",
            resolution: "related"
        },
        {
            route: ":controller/:action/:options",
            resolution: "action",
            order: 100
        },
        {
            route: ":controller/:action",
            resolution: "action",
            order: 200
        },
        {
            route: ":controller",
            resolution: "defaultAction",
            order: 300
        },
        {
            route: "*actions",
            resolution: "home",
            order: 500
        },
    ],

    
    _bindRoutes: function() {},

    
    setupRoutes: function () {
        this.routeParams = {};

        if (this.options.routes) {
            const routeList = [];

            Object.keys(this.options.routes).forEach(route => {
                const item = this.options.routes[route];

                routeList.push({
                    route: route,
                    resolution: item.resolution || 'defaultRoute',
                    order: item.order || 0
                });

                this.routeParams[route] = item.params || {};
            });

            this.routeList = Espo.Utils.clone(this.routeList);

            routeList.forEach(item => {
                this.routeList.push(item);
            });

            this.routeList = this.routeList.sort((v1, v2) => {
                return (v1.order || 0) - (v2.order || 0);
            });
        }

        this.routeList.reverse().forEach(item => {
            this.route(item.route, item.resolution);
        });
    },

    
    _last: null,

    
    confirmLeaveOut: false,

    
    backProcessed: false,

    
    confirmLeaveOutMessage: 'Are you sure?',

    
    confirmLeaveOutConfirmText: 'Yes',

    
    confirmLeaveOutCancelText: 'No',

    
    initialize: function (options) {
        this.options = options || {};
        this.setupRoutes();

        this.history = [];

        let hashHistory = [window.location.hash];

        window.addEventListener('hashchange', () => {
            const hash = window.location.hash;

            if (
                hashHistory.length > 1 &&
                hashHistory[hashHistory.length - 2] === hash
            ) {
                hashHistory = hashHistory.slice(0, -1);

                this.backProcessed = true;
                setTimeout(() => this.backProcessed = false, 50);

                return;
            }

            hashHistory.push(hash);
        });

        this.on('route', () => {
            this.history.push(Backbone.history.fragment);
        });

        window.addEventListener('beforeunload', (e) => {
            e = e || window.event;

            if (this.confirmLeaveOut) {
                e.preventDefault();

                e.returnValue = this.confirmLeaveOutMessage;

                return this.confirmLeaveOutMessage;
            }
        });
    },

    
    getCurrentUrl: function () {
        return '#' + Backbone.history.fragment;
    },

    

    
    checkConfirmLeaveOut: function (callback, context, navigateBack) {
        if (this.confirmLeaveOutDisplayed) {
            this.navigateBack({trigger: false});

            this.confirmLeaveOutCanceled = true;

            return;
        }

        context = context || this;

        if (this.confirmLeaveOut) {
            this.confirmLeaveOutDisplayed = true;
            this.confirmLeaveOutCanceled = false;

            Espo.Ui.confirm(
                this.confirmLeaveOutMessage,
                {
                    confirmText: this.confirmLeaveOutConfirmText,
                    cancelText: this.confirmLeaveOutCancelText,
                    backdrop: true,
                    cancelCallback: () => {
                        this.confirmLeaveOutDisplayed = false;

                        if (navigateBack) {
                            this.navigateBack({trigger: false});
                        }
                    },
                },
                () => {
                    this.confirmLeaveOutDisplayed = false;
                    this.confirmLeaveOut = false;

                    if (!this.confirmLeaveOutCanceled) {
                        callback.call(context);
                    }
                }
            );

            return;
        }

        callback.call(context);
    },

    
    route: function (route, name) {
        const routeOriginal = route;

        if (!_.isRegExp(route)) {
            route = this._routeToRegExp(route);
        }

        let callback;

        
        

        
        callback = this['_' + name];

        const router = this;

        Backbone.history.route(route, function (fragment) {
            const args = router._extractParameters(route, fragment);

            const options = {};

            if (name === 'defaultRoute') {
                const keyList = [];

                routeOriginal.split('/').forEach(key => {
                    if (key && key.indexOf(':') === 0) {
                        keyList.push(key.substr(1));
                    }
                });

                keyList.forEach((key, i) => {
                    options[key] = args[i];
                });
            }

            
            router.execute(callback, args, name, routeOriginal, options);
            
                router.trigger.apply(router, ['route:' + name].concat(args));
                router.trigger('route', name, args);
                Backbone.history.trigger('route', router, name, args);
            
        });

        return this;
    },

    
    execute: function (callback, args, name, routeOriginal, options) {
        this.checkConfirmLeaveOut(() => {
            if (name === 'defaultRoute') {
                this._defaultRoute(this.routeParams[routeOriginal], options);

                return;
            }

            Backbone.Router.prototype.execute.call(this, callback, args, name);
        }, null, true);
    },

    
    navigate: function (fragment, options) {
        this.history.push(fragment);

        return Backbone.Router.prototype.navigate.call(this, fragment, options);
    },

    
    navigateBack: function (options) {
        let url;

        if (this.history.length > 1) {
            url = this.history[this.history.length - 2];
        }
        else {
            url = this.history[0];
        }

        this.navigate(url, options);
    },

    
    _parseOptionsParams: function (string) {
        if (!string) {
            return {};
        }

        if (string.indexOf('&') === -1 && string.indexOf('=') === -1) {
            return string;
        }

        const options = {};

        if (typeof string !== 'undefined') {
            string.split('&').forEach(item => {
                const p = item.split('=');

                options[p[0]] = true;

                if (p.length > 1) {
                    options[p[0]] = p[1];
                }
            });
        }

        return options;
    },

    
    _defaultRoute: function (params, options) {
        const controller = params.controller || options.controller;
        const action = params.action || options.action;

        this.dispatch(controller, action, options);
    },

    
    _record: function (controller, action, id, options) {
        options = this._parseOptionsParams(options);

        options.id = id;

        this.dispatch(controller, action, options);
    },

    
    _view: function (controller, id, options) {
        this._record(controller, 'view', id, options);
    },

    
    _edit: function (controller, id, options) {
        this._record(controller, 'edit', id, options);
    },

    
    _related: function (controller, id, link, options) {
        options = this._parseOptionsParams(options);

        options.id = id;
        options.link = link;

        this.dispatch(controller, 'related', options);
    },

    
    _create: function (controller, options) {
        this._record(controller, 'create', null, options);
    },

    
    _action: function (controller, action, options) {
        this.dispatch(controller, action, this._parseOptionsParams(options));
    },

    
    _defaultAction: function (controller) {
        this.dispatch(controller, null);
    },

    
    _home: function () {
        this.dispatch('Home', null);
    },

    
    _clearCache: function () {
        this.dispatch(null, 'clearCache');
    },

    
    logout: function () {
        this.dispatch(null, 'logout');

        this.navigate('', {trigger: false});
    },

    
    dispatch: function (controller, action, options) {
        const o = {
            controller: controller,
            action: action,
            options: options,
        };

        this._last = o;

        this.trigger('routed', o);
    },

    
    getLast: function () {
        return this._last;
    },
});

export default Router;

function isIOS9UIWebView() {
    const userAgent = window.navigator.userAgent;

    return /(iPhone|iPad|iPod).* OS 9_\d/.test(userAgent) && !/Version\/9\./.test(userAgent);
}



Backbone.history.getHash = function (window) {
    const match = (window || this).location.href.match(/#(.*)$/);

    return match ? this.decodeFragment(match[1]) : '';
};



if (isIOS9UIWebView()) {
    Backbone.history.loadUrl = function (fragment, oldHash) {
        fragment = this.fragment = this.getFragment(fragment);

        return _.any(this.handlers, function (handler) {
            if (handler.route.test(fragment)) {
                function runCallback() {
                    handler.callback(fragment);
                }

                function wait() {
                    if (oldHash === location.hash) {
                        window.setTimeout(wait, 50);
                    }
                    else {
                        runCallback();
                    }
                }

                wait();

                return true;
            }
        });
    };

    Backbone.history.navigate = function (fragment, options) {
        const pathStripper = /#.*$/;

        if (!Backbone.History.started) {
            return false;
        }

        if (!options || options === true) {
            options = {
                trigger: !!options
            };
        }

        let url = this.root + '#' + (fragment = this.getFragment(fragment || ''));

        fragment = fragment.replace(pathStripper, '');

        if (this.fragment === fragment) {
            return;
        }

        this.fragment = fragment;

        if (fragment === '' && url !== '/') {
            url = url.slice(0, -1);
        }

        const oldHash = location.hash;

        if (this._hasPushState) {
            this.history[options.replace ? 'replaceState' : 'pushState']({}, document.title, url);
        }
        else if (this._wantsHashChange) {
            this._updateHash(this.location, fragment, options.replace);

            if (
                this.iframe &&
                (fragment !== this.getFragment(this.getHash(this.iframe)))
            ) {
                if (!options.replace) {
                    this.iframe.document.open().close();
                }

                this._updateHash(this.iframe.location, fragment, options.replace);
            }
        }
        else {
            return this.location.assign(url);
        }

        if (options.trigger) {
            return this.loadUrl(fragment, oldHash);
        }
    };
}
