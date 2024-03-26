



import Exceptions from 'exceptions';
import {Events, View as BullView} from 'bullbone';
import $ from 'jquery';







class Controller {

    
    constructor(params, injections) {
        this.params = params || {};

        
        this.baseController = injections.baseController;
        
        this.viewFactory = injections.viewFactory;
        
        this.modelFactory = injections.modelFactory;
        
        this.collectionFactory = injections.collectionFactory;

        this._settings = injections.settings || null;
        this._user = injections.user || null;
        this._preferences = injections.preferences || null;
        this._acl = injections.acl || null;
        this._cache = injections.cache || null;
        this._router = injections.router || null;
        this._storage = injections.storage || null;
        this._metadata = injections.metadata || null;
        this._dateTime = injections.dateTime || null;
        this._broadcastChannel = injections.broadcastChannel || null;

        if (!this.baseController) {
            this.on('logout', () => this.clearAllStoredMainViews());
        }

        this.set('masterRendered', false);
    }

    
    defaultAction = 'index'

    
    name = null

    
    params = null

    
    viewFactory = null

    
    modelFactory = null

    
    masterView = null

    
    setRouter(router) {
        this._router = router;

        this.trigger('router-set', router);
    }

    
    getConfig() {
        return this._settings;
    }

    
    getUser() {
        return this._user;
    }

    
    getPreferences() {
        return this._preferences;
    }

    
    getAcl() {
        return this._acl;
    }

    
    getCache() {
        return this._cache;
    }

    
    getRouter() {
        return this._router;
    }

    
    getStorage() {
        return this._storage;
    }

    
    getMetadata() {
        return this._metadata;
    }

    
    getDateTime() {
        return this._dateTime;
    }

    
    get(key) {
        if (key in this.params) {
            return this.params[key];
        }

        return null;
    }

    
    set(key, value) {
        this.params[key] = value;
    }

    
    unset(key) {
        delete this.params[key];
    }

    
    has(key) {
        return key in this.params;
    }

    
    getStoredMainView(key) {
        return this.get('storedMainView-' + key);
    }

    
    hasStoredMainView(key) {
        return this.has('storedMainView-' + key);
    }

    
    clearStoredMainView(key) {
        const view = this.getStoredMainView(key);

        if (view) {
            view.remove(true);
        }

        this.unset('storedMainView-' + key);
    }

    
    storeMainView(key, view) {
        this.set('storedMainView-' + key, view);

        this.listenTo(view, 'remove', (o) => {
            o = o || {};

            if (o.ignoreCleaning) {
                return;
            }

            this.stopListening(view, 'remove');

            this.clearStoredMainView(key);
        });
    }

    
    clearAllStoredMainViews() {
        for (const k in this.params) {
            if (k.indexOf('storedMainView-') !== 0) {
                continue;
            }

            const key = k.slice(15);

            this.clearStoredMainView(key);
        }
    }

    
    checkAccess(action) {
        return true;
    }

    
    handleAccessGlobal() {
        if (!this.checkAccessGlobal()) {
            throw new Exceptions.AccessDenied("Denied access to '" + this.name + "'");
        }
    }

    
    checkAccessGlobal() {
        return true;
    }

    
    handleCheckAccess(action) {
        if (this.checkAccess(action)) {
            return;
        }

        const msg = action ?
            "Denied access to action '" + this.name + "#" + action + "'" :
            "Denied access to scope '" + this.name + "'";

        throw new Exceptions.AccessDenied(msg);
    }

    
    doAction(action, options) {
        this.handleAccessGlobal();

        action = action || this.defaultAction;

        const method = 'action' + Espo.Utils.upperCaseFirst(action);

        if (!(method in this)) {
            throw new Exceptions.NotFound("Action '" + this.name + "#" + action + "' is not found");
        }

        const preMethod = 'before' + Espo.Utils.upperCaseFirst(action);
        const postMethod = 'after' + Espo.Utils.upperCaseFirst(action);

        if (preMethod in this) {
            this[preMethod].call(this, options || {});
        }

        this[method].call(this, options || {});

        if (postMethod in this) {
            this[postMethod].call(this, options || {});
        }
    }

    
    master(callback) {
        const entire = this.get('entire');

        if (entire) {
            entire.remove();

            this.set('entire', null);
        }

        const master = this.get('master');

        if (master) {
            callback.call(this, master);

            return;
        }

        const masterView = this.masterView || 'views/site/master';

        this.viewFactory.create(masterView, {fullSelector: 'body'}, master => {
            this.set('master', master);

            if (this.get('masterRendered')) {
                callback.call(this, master);

                return;
            }

            master.render()
                .then(() => {
                    this.set('masterRendered', true);

                    callback.call(this, master);
                })
        });
    }

    
    _unchainMainView(masterView) {
        
        if (
            !masterView.currentViewKey ||
            !this.hasStoredMainView(masterView.currentViewKey)
        ) {
            return;
        }

        const currentMainView = masterView.getView('main');

        if (!currentMainView) {
            return;
        }

        currentMainView.propagateEvent('remove', {ignoreCleaning: true});
        masterView.unchainView('main');
    }

    

    
    main(view, options, callback, params = {}) {
        const dto = {
            isCanceled: false,
            key: params.key,
            useStored: params.useStored,
            callback: callback,
        };

        const selector = '#main';

        const useStored = params.useStored || false;
        const key = params.key;

        this.listenToOnce(this.baseController, 'action', () => dto.isCanceled = true);

        const mainView = view && typeof view === 'object' ?
            view : undefined;

        const viewName = !mainView ?
            (view || 'views/base') : undefined;

        this.master(masterView => {
            if (dto.isCanceled) {
                return;
            }

            options = options || {};
            options.fullSelector = selector;

            if (useStored && this.hasStoredMainView(key)) {
                const mainView = this.getStoredMainView(key);

                let isActual = true;

                if (
                    mainView &&
                    ('isActualForReuse' in mainView) &&
                    typeof mainView.isActualForReuse === 'function'
                ) {
                    isActual = mainView.isActualForReuse();
                }

                const lastUrl = (mainView && 'lastUrl' in mainView) ? mainView.lastUrl : null;

                if (
                    isActual &&
                    (!lastUrl || lastUrl === this.getRouter().getCurrentUrl())
                ) {
                    this._processMain(mainView, masterView, dto);

                    if (
                        'setupReuse' in mainView &&
                        typeof mainView.setupReuse === 'function'
                    ) {
                        mainView.setupReuse(options.params || {});
                    }

                    return;
                }

                this.clearStoredMainView(key);
            }

            if (mainView) {
                this._unchainMainView(masterView);

                masterView.assignView('main', mainView, selector)
                    .then(() => {
                        dto.isSet = true;

                        this._processMain(view, masterView, dto);
                    });

                return;
            }

            this.viewFactory.create(viewName, options, view => {
                this._processMain(view, masterView, dto);
            });
        });
    }

    
    _processMain(mainView, masterView, dto) {
        if (dto.isCanceled) {
            return;
        }

        const key = dto.key;

        if (key) {
            this.storeMainView(key, mainView);
        }

        const onAction = () => {
            mainView.cancelRender();
            dto.isCanceled = true;
        };

        mainView.listenToOnce(this.baseController, 'action', onAction);

        if (masterView.currentViewKey) {
            this.set('storedScrollTop-' + masterView.currentViewKey, $(window).scrollTop());

            if (!dto.isSet) {
                this._unchainMainView(masterView);
            }
        }

        masterView.currentViewKey = key;

        if (!dto.isSet) {
            masterView.setView('main', mainView);
        }

        const afterRender = () => {
            setTimeout(() => mainView.stopListening(this.baseController, 'action', onAction), 500);

            mainView.updatePageTitle();

            if (dto.useStored && this.has('storedScrollTop-' + key)) {
                $(window).scrollTop(this.get('storedScrollTop-' + key));

                return;
            }

            $(window).scrollTop(0);
        };

        if (dto.callback) {
            this.listenToOnce(mainView, 'after:render', afterRender);

            dto.callback.call(this, mainView);

            return;
        }

        mainView.render()
            .then(afterRender);
    }

    
    showLoadingNotification() {
        const master = this.get('master');

        if (!master) {
            return;
        }

        master.showLoadingNotification();
    }

    
    hideLoadingNotification() {
        const master = this.get('master');

        if (!master) {
            return;
        }

        master.hideLoadingNotification();
    }

    
    entire(view, options, callback) {
        const masterView = this.get('master');

        if (masterView) {
            masterView.remove();
        }

        this.set('master', null);
        this.set('masterRendered', false);

        if (typeof view === 'object') {
            view.setElement('body');

            this.viewFactory.prepare(view, () => {
                if (!callback) {
                    view.render();

                    return;
                }

                callback(view);
            });

            return;
        }

        options = options || {};
        options.fullSelector = 'body';

        this.viewFactory.create(view, options, view => {
            this.set('entire', view);

            if (!callback) {
                view.render();

                return;
            }

            callback(view);
        });
    }
}

Object.assign(Controller.prototype, Events);


Controller.extend = BullView.extend;

export default Controller;
