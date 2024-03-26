



import $ from 'jquery';
import Backbone from 'backbone';
import {Events, View as BullView, Factory as BullFactory} from 'bullbone';
import Base64 from 'js-base64';
import Ui from 'ui';
import Utils from 'utils';
import AclManager from 'acl-manager';
import Cache from 'cache';
import Storage from 'storage';
import Settings from 'models/settings';
import Language from 'language';
import Metadata from 'metadata';
import FieldManager from 'field-manager';
import User from 'models/user';
import Preferences from 'models/preferences';
import ModelFactory from 'model-factory';
import CollectionFactory from 'collection-factory';
import BaseController from 'controllers/base';
import Router from 'router';
import DateTime from 'date-time';
import LayoutManager from 'layout-manager';
import ThemeManager from 'theme-manager';
import SessionStorage from 'session-storage';
import ViewHelper from 'view-helper';
import WebSocketManager from 'web-socket-manager';
import Ajax from 'ajax';
import NumberUtil from 'number-util';
import PageTitle from 'page-title';
import BroadcastChannel from 'broadcast-channel';


class App {

    
    constructor(options, callback) {
        options = options || {};

        
        this.id = options.id || 'espocrm-application-id';

        
        this.useCache = options.useCache || this.useCache;

        this.apiUrl = options.apiUrl || this.apiUrl;

        
        this.basePath = options.basePath || '';

        
        this.ajaxTimeout = options.ajaxTimeout || 0;

        
        this.internalModuleList = options.internalModuleList || [];

        
        this.bundledModuleList = options.bundledModuleList || [];

        this.appTimestamp = options.appTimestamp;

        this.initCache(options)
            .then(() => this.init(options, callback));

        this.initDomEventListeners();
    }

    
    useCache = false

    
    user = null

    
    preferences = null

    
    settings = null

    
    metadata = null

    
    language = null

    
    fieldManager = null

    
    cache = null

    
    storage = null

    
    loader = null

    
    apiUrl = 'api/v1'

    
    auth = null

    
    anotherUser = null

    
    baseController = null

    
    controllers = null

    
    router = null

    
    modelFactory = null

    
    collectionFactory = null

    
    viewFactory = null

    
    viewLoader = null

    
    viewHelper = null

    
    masterView = 'views/site/master'

    
    responseCache = null

    
    broadcastChannel = null

    
    dateTime = null

    
    numberUtil = null

    
    webSocketManager = null

    
    appTimestamp = null

    
    started = false

    
    aclName = 'acl'

    
    initCache(options) {
        if (!this.useCache) {
            return Promise.resolve();
        }

        const cacheTimestamp = options.cacheTimestamp || null;

        this.cache = new Cache(cacheTimestamp);

        const storedCacheTimestamp = this.cache.getCacheTimestamp();

        cacheTimestamp ?
            this.cache.handleActuality(cacheTimestamp) :
            this.cache.storeTimestamp();

        if (!window.caches) {
            return Promise.resolve();
        }

        return new Promise(resolve => {
            const deleteCache = !cacheTimestamp ||
                !storedCacheTimestamp ||
                cacheTimestamp !== storedCacheTimestamp;

            (
                deleteCache ?
                    caches.delete('espo') :
                    Promise.resolve()
            )
                .then(() => caches.open('espo'))
                .then(cache => {
                    this.responseCache = cache;

                    resolve();
                })
                .catch(() => {
                    console.error(`Could not open 'espo' cache.`);
                    resolve();
                });
        });
    }

    
    init(options, callback) {
        
        this.appParams = {};
        this.controllers = {};

        
        this.loader = Espo.loader;

        this.loader.setResponseCache(this.responseCache);

        if (this.useCache && !this.loader.getCacheTimestamp() && options.cacheTimestamp) {
            this.loader.setCacheTimestamp(options.cacheTimestamp);
        }

        this.storage = new Storage();
        this.sessionStorage = new SessionStorage();

        this.setupAjax();

        this.settings = new Settings(null);
        this.language = new Language(this.cache);
        this.metadata = new Metadata(this.cache);
        this.fieldManager = new FieldManager();

        this.initBroadcastChannel();

        Promise
            .all([
                this.settings.load(),
                this.language.loadDefault(),
                this.initTemplateBundles(),
            ])
            .then(() => {
                this.loader.setIsDeveloperMode(this.settings.get('isDeveloperMode'));

                this.user = new User();
                this.preferences = new Preferences();

                this.preferences.settings = this.settings;

                
                this.acl = this.createAclManager();

                this.fieldManager.acl = this.acl;

                this.themeManager = new ThemeManager(this.settings, this.preferences, this.metadata);
                this.modelFactory = new ModelFactory(this.metadata);
                this.collectionFactory = new CollectionFactory(this.modelFactory, this.settings, this.metadata);

                if (this.settings.get('useWebSocket')) {
                    this.webSocketManager = new WebSocketManager(this.settings);
                }

                this.initUtils();
                this.initView();
                this.initBaseController();

                callback.call(this, this);
            });
    }

    
    start() {
        this.initAuth();

        this.started = true;

        if (!this.auth) {
            this.baseController.login();

            return;
        }

        this.initUserData(null, () => this.onAuth.call(this));
    }

    
    onAuth(afterLogin) {
        this.metadata.load().then(() => {
            this.fieldManager.defs = this.metadata.get('fields');
            this.fieldManager.metadata = this.metadata;

            this.settings.defs = this.metadata.get('entityDefs.Settings') || {};
            this.user.defs = this.metadata.get('entityDefs.User');
            this.preferences.defs = this.metadata.get('entityDefs.Preferences');
            this.viewHelper.layoutManager.userId = this.user.id;

            if (this.themeManager.isUserTheme()) {
                this.loadStylesheet();
            }

            if (this.anotherUser) {
                this.viewHelper.webSocketManager = null;
                this.webSocketManager = null;
            }

            if (this.webSocketManager) {
                this.webSocketManager.connect(this.auth, this.user.id);
            }

            const promiseList = [];
            const aclImplementationClassMap = {};

            const clientDefs = this.metadata.get('clientDefs') || {};

            Object.keys(clientDefs).forEach(scope => {
                const o = clientDefs[scope];

                const implClassName = (o || {})[this.aclName];

                if (!implClassName) {
                    return;
                }

                promiseList.push(
                    new Promise(resolve => {
                        this.loader.require(implClassName, implClass => {
                            aclImplementationClassMap[scope] = implClass;

                            resolve();
                        });
                    })
                );
            });

            if (!this.themeManager.isApplied() && this.themeManager.isUserTheme()) {
                promiseList.push(
                    new Promise(resolve => {
                        const check = i => {
                            if (this.themeManager.isApplied() || i === 50) {
                                resolve();

                                return;
                            }

                            i = i || 0;

                            setTimeout(() => check(i + 1), 10);
                        }

                        check();
                    })
                );
            }

            Promise.all(promiseList)
                .then(() => {
                    this.acl.implementationClassMap = aclImplementationClassMap;

                    this.initRouter();
                });

            if (afterLogin) {
                this.broadcastChannel.postMessage('logged-in');
            }
        });
    }

    
    initRouter() {
        const routes = this.metadata.get(['app', 'clientRoutes']) || {};

        this.router = new Router({routes: routes});

        this.viewHelper.router = this.router;

        this.baseController.setRouter(this.router);

        this.router.confirmLeaveOutMessage = this.language.translate('confirmLeaveOutMessage', 'messages');
        this.router.confirmLeaveOutConfirmText = this.language.translate('Yes');
        this.router.confirmLeaveOutCancelText = this.language.translate('Cancel');

        this.router.on('routed', params => this.doAction(params));

        try {
            Backbone.history.start({root: window.location.pathname});
        }
        catch (e) {
            Backbone.history.loadUrl();
        }
    }

    
    doAction(params) {
        this.trigger('action', params);

        this.baseController.trigger('action');

        const callback = controller => {
            try {
                controller.doAction(params.action, params.options);

                this.trigger('action:done');
            } catch (e) {
                console.error(e);

                switch (e.name) {
                    case 'AccessDenied':
                        this.baseController.error403();

                        break;

                    case 'NotFound':
                        this.baseController.error404();

                        break;

                    default:
                        throw e;
                }
            }
        };

        if (params.controllerClassName) {
            this.createController(params.controllerClassName, null, callback);

            return;
        }

        this.getController(params.controller, callback);
    }

    
    initBaseController() {
        this.baseController = new BaseController({}, this.getControllerInjection());

        this.viewHelper.baseController = this.baseController;
    }

    
    getControllerInjection() {
        return {
            viewFactory: this.viewFactory,
            modelFactory: this.modelFactory,
            collectionFactory: this.collectionFactory,
            settings: this.settings,
            user: this.user,
            preferences: this.preferences,
            acl: this.acl,
            cache: this.cache,
            router: this.router,
            storage: this.storage,
            metadata: this.metadata,
            dateTime: this.dateTime,
            broadcastChannel: this.broadcastChannel,
            baseController: this.baseController,
        };
    }

    
    getController(name, callback) {
        if (!name) {
            callback(this.baseController);

            return;
        }

        if (name in this.controllers) {
            callback(this.controllers[name]);

            return;
        }

        try {
            let className = this.metadata.get(['clientDefs', name, 'controller']);

            if (!className) {
                const module = this.metadata.get(['scopes', name, 'module']);

                className = Utils.composeClassName(module, name, 'controllers');
            }

            this.createController(className, name, callback);
        }
        catch (e) {
            this.baseController.error404();
        }
    }

    
    createController(className, name, callback) {
        Espo.loader.require(
            className,
            controllerClass => {
                const injections = this.getControllerInjection();

                const controller = new controllerClass(this.baseController.params, injections);

                controller.name = name;
                controller.masterView = this.masterView;

                this.controllers[name] = controller

                callback(controller);
            },
            () => this.baseController.error404()
        );
    }

    
    initUtils() {
        this.dateTime = new DateTime();
        this.modelFactory.dateTime = this.dateTime;
        this.dateTime.setSettingsAndPreferences(this.settings, this.preferences);
        this.numberUtil = new NumberUtil(this.settings, this.preferences);
    }

    
    createAclManager() {
        return new AclManager(this.user, null, this.settings.get('aclAllowDeleteCreated'));
    }

    
    initView() {
        const helper = this.viewHelper = new ViewHelper();

        helper.layoutManager = new LayoutManager(this.cache, this.id);
        helper.settings = this.settings;
        helper.config = this.settings;
        helper.user = this.user;
        helper.preferences = this.preferences;
        helper.acl = this.acl;
        helper.modelFactory = this.modelFactory;
        helper.collectionFactory = this.collectionFactory;
        helper.storage = this.storage;
        helper.sessionStorage = this.sessionStorage;
        helper.dateTime = this.dateTime;
        helper.language = this.language;
        helper.metadata = this.metadata;
        helper.fieldManager = this.fieldManager;
        helper.cache = this.cache;
        helper.themeManager = this.themeManager;
        helper.webSocketManager = this.webSocketManager;
        helper.numberUtil = this.numberUtil;
        helper.pageTitle = new PageTitle(this.settings);
        helper.basePath = this.basePath;
        helper.appParams = this.appParams;
        helper.broadcastChannel = this.broadcastChannel;

        this.viewLoader = (viewName, callback) => {
            this.loader.require(Utils.composeViewClassName(viewName), callback);
        };

        const internalModuleMap = {};

        const isModuleInternal = (module) => {
            if (!(module in internalModuleMap)) {
                internalModuleMap[module] = this.internalModuleList.indexOf(module) !== -1;
            }

            return internalModuleMap[module];
        };

        const getResourceInnerPath = (type, name) => {
            let path = null;

            switch (type) {
                case 'template':
                    if (~name.indexOf('.')) {
                        console.warn(name + ': template name should use slashes for a directory separator.');
                    }

                    path = 'res/templates/' + name.split('.').join('/') + '.tpl';

                    break;

                case 'layoutTemplate':
                    path = 'res/layout-types/' + name + '.tpl';

                    break;
            }

            return path;
        };

        const getResourcePath = (type, name) => {
            if (!name.includes(':')) {
                return 'client/' + getResourceInnerPath(type, name);
            }

            const [mod, path] = name.split(':');

            if (mod === 'custom') {
                return 'client/custom/' + getResourceInnerPath(type, path);
            }

            if (isModuleInternal(mod)) {
                return 'client/modules/' + mod + '/' + getResourceInnerPath(type, path);
            }

            return 'client/custom/modules/' + mod + '/' + getResourceInnerPath(type, path);
        };

        this.viewFactory = new BullFactory({
            defaultViewName: 'views/base',
            helper: helper,
            viewLoader: this.viewLoader,
            resources: {
                loaders: {
                    template: (name, callback) => {
                        const path = getResourcePath('template', name);

                        this.loader.require('res!' + path, callback);
                    },
                    layoutTemplate: (name, callback) => {
                        if (Espo.layoutTemplates && name in Espo.layoutTemplates) {
                            callback(Espo.layoutTemplates[name]);

                            return;
                        }

                        const path = getResourcePath('layoutTemplate', name);

                        this.loader.require('res!' + path, callback);
                    },
                },
            },
            preCompiledTemplates: Espo.preCompiledTemplates || {},
        });
    }

    
    initAuth() {
        this.auth = this.storage.get('user', 'auth') || null;
        this.anotherUser = this.storage.get('user', 'anotherUser') || null;

        this.baseController.on('login', data => {
            const userId = data.user.id;
            const userName = data.auth.userName;
            const token = data.auth.token;
            const anotherUser = data.auth.anotherUser || null;

            this.auth = Base64.encode(userName  + ':' + token);
            this.anotherUser = anotherUser;

            const lastUserId = this.storage.get('user', 'lastUserId');

            if (lastUserId !== userId) {
                this.metadata.clearCache();
                this.language.clearCache();
            }

            this.storage.set('user', 'auth', this.auth);
            this.storage.set('user', 'lastUserId', userId);
            this.storage.set('user', 'anotherUser', this.anotherUser);

            this.setCookieAuth(userName, token);

            this.initUserData(data, () => this.onAuth(true));
        });

        this.baseController.on('logout', () => this.logout());
    }

    
    logout(afterFail, silent) {
        let logoutWait = false;

        if (this.auth && !afterFail) {
            const arr = Base64.decode(this.auth).split(':');

            if (arr.length > 1) {
                logoutWait = this.appParams.logoutWait || false;

                Ajax.postRequest('App/destroyAuthToken', {token: arr[1]}, {resolveWithXhr: true})
                    .then(xhr => {
                        const redirectUrl = xhr.getResponseHeader('X-Logout-Redirect-Url');

                        if (redirectUrl) {
                            setTimeout(() => window.location.href = redirectUrl, 50);

                            return;
                        }

                        if (logoutWait) {
                            this.doAction({action: 'login'});
                        }
                    });
            }
        }

        if (this.webSocketManager) {
            this.webSocketManager.close();
        }

        silent = silent || afterFail &&
            this.auth &&
            this.auth !== this.storage.get('user', 'auth');

        this.auth = null;
        this.anotherUser = null;

        this.user.clear();
        this.preferences.clear();
        this.acl.clear();

        if (!silent) {
            this.storage.clear('user', 'auth');
            this.storage.clear('user', 'anotherUser');
        }

        const action = logoutWait ? 'logoutWait' : 'login';

        this.doAction({action: action});

        if (!silent) {
            this.unsetCookieAuth();
        }

        if (this.broadcastChannel.object) {
            if (!silent) {
                this.broadcastChannel.postMessage('logged-out');
            }
        }

        if (!silent) {
            this.sendLogoutRequest();
        }

        this.loadStylesheet();
    }

    
    sendLogoutRequest() {
        const xhr = new XMLHttpRequest;

        xhr.open('GET', this.basePath + this.apiUrl + '/');
        xhr.setRequestHeader('Authorization', 'Basic ' + Base64.encode('**logout:logout'));
        xhr.send('');
        xhr.abort();
    }

    
    loadStylesheet() {
        if (!this.metadata.get(['themes'])) {
            return;
        }

        const stylesheetPath = this.basePath + this.themeManager.getStylesheet();

        $('#main-stylesheet').attr('href', stylesheetPath);
    }

    
    setCookieAuth(username, token) {
        const date = new Date();

        date.setTime(date.getTime() + (1000 * 24 * 60 * 60 * 1000));

        document.cookie = 'auth-token=' + token + '; SameSite=Lax; expires=' + date.toUTCString() + '; path=/';
    }

    
    unsetCookieAuth() {
        document.cookie = 'auth-token' + '=; SameSite=Lax; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/';
    }

    
    initUserData(options, callback) {
        options = options || {};

        if (this.auth === null) {
            return;
        }

        new Promise(resolve => {
            if (options.user) {
                resolve(options);

                return;
            }

            this.requestUserData(data => {
                options = data;

                resolve(options);
            });
        })
            .then(options => {
                this.language.name = options.language;

                return this.language.load();
            })
            .then(() => {
                this.dateTime.setLanguage(this.language);

                const userData = options.user || null;
                const preferencesData = options.preferences || null;
                const aclData = options.acl || null;

                const settingData = options.settings || {};

                this.user.set(userData);
                this.preferences.set(preferencesData);

                this.settings.set(settingData);
                this.acl.set(aclData);

                for (const param in options.appParams) {
                    this.appParams[param] = options.appParams[param];
                }

                if (!this.auth) {
                    return;
                }

                const xhr = new XMLHttpRequest();

                xhr.open('GET', this.basePath + this.apiUrl + '/');
                xhr.setRequestHeader('Authorization', 'Basic ' + this.auth);

                xhr.onreadystatechange = () => {
                    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                        const arr = Base64.decode(this.auth).split(':');

                        this.setCookieAuth(arr[0], arr[1]);

                        callback();
                    }

                    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 401) {
                        Ui.error('Auth error');
                    }
                };

                xhr.send('');
            });
    }

    
    requestUserData(callback) {
        Ajax.getRequest('App/user', {}, {appStart: true})
            .then(callback);
    }

    
    setupAjax() {
        
        const beforeSend = (xhr, options) => {
            if (this.auth !== null && !options.login) {
                xhr.setRequestHeader('Authorization', 'Basic ' + this.auth);
                xhr.setRequestHeader('Espo-Authorization', this.auth);
                xhr.setRequestHeader('Espo-Authorization-By-Token', 'true');
            }

            if (this.anotherUser !== null && !options.login) {
                xhr.setRequestHeader('X-Another-User', this.anotherUser);
            }
        };

        let appTimestampChangeProcessed = false;

        
        const onSuccess = (xhr, options) => {
            const appTimestampHeader = xhr.getResponseHeader('X-App-Timestamp');

            if (!appTimestampHeader || appTimestampChangeProcessed) {
                return;
            }

            const appTimestamp = parseInt(appTimestampHeader);

            
            const bypassAppReload = options.bypassAppReload;

            if (
                this.appTimestamp &&
                
                appTimestamp > this.appTimestamp &&
                !bypassAppReload
            ) {
                appTimestampChangeProcessed = true;

                Ui
                    .confirm(
                        this.language.translate('confirmAppRefresh', 'messages'),
                        {
                            confirmText: this.language.translate('Refresh'),
                            cancelText: this.language.translate('Cancel'),
                            backdrop: 'static',
                            confirmStyle: 'success',
                        }
                    )
                    .then(() => {
                        window.location.reload();

                        if (this.broadcastChannel) {
                            this.broadcastChannel.postMessage('reload');
                        }
                    });
            }
        };

        
        const onError = (xhr, options) => {
            setTimeout(() => {
                if (xhr.errorIsHandled) {
                    return;
                }

                switch (xhr.status) {
                    case 200:
                        Ui.error(this.language.translate('Bad server response'));

                        console.error('Bad server response: ' + xhr.responseText);

                        break;

                    case 401:
                        
                        if (options.login) {
                            break;
                        }

                        if (this.auth && this.router && !this.router.confirmLeaveOut) {
                            this.logout(true);

                            break;
                        }

                        if (this.auth && this.router && this.router.confirmLeaveOut) {
                            Ui.error(this.language.translate('loggedOutLeaveOut', 'messages'), true);

                            this.router.trigger('logout');

                            break;
                        }

                        if (this.auth) {
                            
                            const silent = !options.appStart;

                            this.logout(true, silent);
                        }

                        console.error('Error 401: Unauthorized.');

                        break;

                    case 403:
                        
                        if (options.main) {
                            this.baseController.error403();

                            break;
                        }

                        this._processErrorAlert(xhr, 'Access denied');

                        break;

                    case 400:
                        this._processErrorAlert(xhr, 'Bad request');

                        break;

                    case 404:
                        
                        if (options.main) {
                            this.baseController.error404();

                            break
                        }

                        this._processErrorAlert(xhr, 'Not found', true);

                        break;

                    default:
                        this._processErrorAlert(xhr, null);
                }

                const statusReason = xhr.getResponseHeader('X-Status-Reason');

                if (statusReason) {
                    console.error('Server side error ' + xhr.status + ': ' + statusReason);
                }
            }, 0);
        };

        const onTimeout = () => {
            Ui.error(this.language.translate('Timeout'), true);
        };

        Ajax.configure({
            apiUrl: this.basePath + this.apiUrl,
            timeout: this.ajaxTimeout,
            beforeSend: beforeSend,
            onSuccess: onSuccess,
            onError: onError,
            onTimeout: onTimeout,
        });

        
        
        $.ajaxSetup({
            beforeSend: (xhr, options) => {
                if (!options.url || !options.url.includes('q=')) {
                    console.warn(`$.ajax is deprecated, support will be removed in v9.0. Use Espo.Ajax instead.`);
                }

                
                if (!options.local && this.apiUrl) {
                    options.url = Utils.trimSlash(this.apiUrl) + '/' + options.url;
                }

                
                if (!options.local && this.basePath !== '') {
                    options.url = this.basePath + options.url;
                }

                if (this.auth !== null) {
                    xhr.setRequestHeader('Authorization', 'Basic ' + this.auth);
                    xhr.setRequestHeader('Espo-Authorization', this.auth);
                    xhr.setRequestHeader('Espo-Authorization-By-Token', 'true');
                }

                if (this.anotherUser !== null) {
                    xhr.setRequestHeader('X-Another-User', this.anotherUser);
                }
            },
            dataType: 'json',
            timeout: this.ajaxTimeout,
            contentType: 'application/json',
        });
    }

    
    _processErrorAlert(xhr, label, noDetail) {
        let msg = this.language.translate('Error') + ' ' + xhr.status;

        if (label) {
            msg += ': ' + this.language.translate(label);
        }

        const obj = {
            msg: msg,
            closeButton: false,
        };

        let isMessageDone = false;

        if (noDetail) {
            isMessageDone = true;
        }

        if (!isMessageDone && xhr.responseText && xhr.responseText[0] === '{') {
            
            let data = null;

            try {
                data = JSON.parse(xhr.responseText);
            }
            catch (e) {}

            if (data && data.messageTranslation && data.messageTranslation.label) {
                let msgDetail = this.language.translate(
                    data.messageTranslation.label,
                    'messages',
                    data.messageTranslation.scope
                );

                const msgData = data.messageTranslation.data || {};

                for (const key in msgData) {
                    msgDetail = msgDetail.replace('{' + key + '}', msgData[key]);
                }

                obj.msg += '\n' + msgDetail;
                obj.closeButton = true;

                isMessageDone = true;
            }

            if (
                !isMessageDone &&
                data &&
                'message'in data &&
                data.message
            ) {
                obj.msg += '\n' + data.message;
                obj.closeButton = true;

                isMessageDone = true;
            }
        }

        if (!isMessageDone) {
            const statusReason = xhr.getResponseHeader('X-Status-Reason');

            if (statusReason) {
                obj.msg += '\n' + statusReason;
                obj.closeButton = true;
            }
        }

        Ui.error(obj.msg, obj.closeButton);
    }

    
    initBroadcastChannel() {
        this.broadcastChannel = new BroadcastChannel();

        this.broadcastChannel.subscribe(event => {
            if (!this.auth && this.started) {
                if (event.data === 'logged-in') {
                    
                    
                    
                    window.location.reload();
                }

                return;
            }

            if (event.data === 'update:all') {
                this.metadata.loadSkipCache();
                this.settings.load();
                this.language.loadSkipCache();
                this.viewHelper.layoutManager.clearLoadedData();

                return;
            }

            if (event.data === 'update:metadata') {
                this.metadata.loadSkipCache();

                return;
            }

            if (event.data === 'update:config') {
                this.settings.load();

                return;
            }

            if (event.data === 'update:language') {
                this.language.loadSkipCache();

                return;
            }

            if (event.data === 'update:layout') {
                this.viewHelper.layoutManager.clearLoadedData();

                return;
            }

            if (event.data === 'reload') {
                window.location.reload();

                return;
            }

            if (event.data === 'logged-out' && this.started) {
                if (this.auth && this.router.confirmLeaveOut) {
                    Ui.error(this.language.translate('loggedOutLeaveOut', 'messages'), true);

                    this.router.trigger('logout');

                    return;
                }

                this.logout(true);
            }
        });
    }

    
    initDomEventListeners() {
        $(document).on('keydown.espo.button', e => {
            if (
                e.code !== 'Enter' ||
                e.target.tagName !== 'A' ||
                e.target.getAttribute('role') !== 'button' ||
                e.target.getAttribute('href') ||
                e.ctrlKey ||
                e.altKey ||
                e.metaKey
            ) {
                return;
            }

            $(e.target).click();

            e.preventDefault();
        });
    }

    
    initTemplateBundles() {
        if (!this.responseCache) {
            return Promise.resolve();
        }

        const key = 'templateBundlesCached';

        if (this.cache.get('app', key)) {
            return Promise.resolve();
        }

        const files = ['client/lib/templates.tpl'];

        this.bundledModuleList.forEach(mod => {
            const file = this.internalModuleList.includes(mod) ?
                `client/modules/${mod}/lib/templates.tpl` :
                `client/custom/modules/${mod}/lib/templates.tpl`;

            files.push(file);
        });

        const baseUrl = Utils.obtainBaseUrl();
        const timestamp = this.loader.getCacheTimestamp();

        const promiseList = files.map(file => {
            const url = new URL(baseUrl + this.basePath + file);
            url.searchParams.append('t', this.appTimestamp);

            return new Promise(resolve => {
                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            console.error(`Could not fetch ${url}.`);
                            resolve();

                            return;
                        }

                        const promiseList = [];

                        response.text().then(text => {
                            const index = text.indexOf('\n');

                            if (index <= 0) {
                                resolve();

                                return;
                            }

                            const delimiter = text.slice(0, index + 1);
                            text = text.slice(index + 1);

                            text.split(delimiter).forEach(item => {
                                const index = item.indexOf('\n');

                                const file = item.slice(0, index);
                                const content = item.slice(index + 1);

                                const url = baseUrl + this.basePath + 'client/' + file;

                                const urlObj = new URL(url);
                                urlObj.searchParams.append('r', timestamp);

                                promiseList.push(
                                    this.responseCache.put(urlObj, new Response(content))
                                );
                            });
                        });

                        Promise.all(promiseList).then(() => resolve());
                    });
            });
        });

        return Promise.all(promiseList)
            .then(() => {
                this.cache.set('app', key, true);
            });
    }
}





Object.assign(App.prototype, Events);

App.extend = BullView.extend;

export default App;
