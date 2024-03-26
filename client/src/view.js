



import {View as BullView} from 'bullbone';


class View extends BullView {

    

    

    

    

    
    
    whenReady() {
        if (this.isReady) {
            return Promise.resolve();
        }

        return new Promise(resolve => {
            this.once('ready', () => resolve());
        });
    }

    
    addActionHandler(action, handler) {
        const fullAction = `click [data-action="${action}"]`;

        this.events[fullAction] = e => {
            
            handler.call(this, e.originalEvent, e.currentTarget);
        };
    }

    
    escapeString(string) {
        return Handlebars.Utils.escapeExpression(string);
    }

    
    notify(label, type, timeout, scope) {
        if (!label) {
            Espo.Ui.notify(false);

            return;
        }

        scope = scope || null;
        timeout = timeout || 2000;

        if (!type) {
            timeout = void 0;
        }

        const text = this.getLanguage().translate(label, 'labels', scope);

        Espo.Ui.notify(text, type, timeout);
    }

    
    getHelper() {
        return this._helper;
    }

    
    getUser() {
        return this._helper.user;
    }

    
    getPreferences() {
        return this._helper.preferences;
    }

    
    getConfig() {
        return this._helper.settings;
    }

    
    getAcl() {
        return this._helper.acl;
    }

    
    getModelFactory() {
        return this._helper.modelFactory;
    }

    
    getCollectionFactory() {
        return this._helper.collectionFactory;
    }

    
    getRouter() {
        return this._helper.router;
    }

    
    getStorage() {
        return this._helper.storage;
    }

    
    getSessionStorage() {
        return this._helper.sessionStorage;
    }

    
    getLanguage() {
        return this._helper.language;
    }

    
    getMetadata() {
        return this._helper.metadata;
    }

    
    getCache() {
        return this._helper.cache;
    }

    
    getDateTime() {
        return this._helper.dateTime;
    }

    
    getNumberUtil() {
        return this._helper.numberUtil;
    }

    
    getFieldManager() {
        return this._helper.fieldManager;
    }

    
    getBaseController() {
        return this._helper.baseController;
    }

    
    getThemeManager() {
        return this._helper.themeManager;
    }

    
    updatePageTitle() {
        const title = this.getConfig().get('applicationName') || 'EspoCRM';

        this.setPageTitle(title);
    }

    
    setPageTitle(title) {
        this.getHelper().pageTitle.setTitle(title);
    }

    
    translate(label, category, scope) {
        return this.getLanguage().translate(label, category, scope);
    }

    
    getBasePath() {
        return this._helper.basePath || '';
    }

    
    
    ajaxRequest(url, type, data, options) {
        return  Espo.Ajax.request(url, type, data, options);
    }

    
    
    ajaxPostRequest(url, data, options) {
        return Espo.Ajax.postRequest(url, data, options);
    }

    
    
    ajaxGetRequest(url, data, options) {
        return Espo.Ajax.getRequest(url, data, options);
    }

    

    
    confirm(o, callback, context) {
        let message;

        if (typeof o === 'string' || o instanceof String) {
            message = o;

            o = {};
        }
        else {
            o = o || {};

            message = o.message;
        }

        if (message) {
            message = this.getHelper()
                .transformMarkdownText(message, {linksInNewTab: true})
                .toString();
        }

        const confirmText = o.confirmText || this.translate('Yes');
        const confirmStyle = o.confirmStyle || null;
        const cancelText = o.cancelText || this.translate('Cancel');

        return Espo.Ui.confirm(message, {
            confirmText: confirmText,
            cancelText: cancelText,
            confirmStyle: confirmStyle,
            backdrop: ('backdrop' in o) ? o.backdrop : true,
            isHtml: true,
        }, callback, context);
    }
}

export default View;
