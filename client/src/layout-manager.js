



import {Events} from 'bullbone';


class LayoutManager {

    
    constructor(cache, applicationId, userId) {

        
        this.cache = cache || null;

        
        this.applicationId = applicationId || 'default-id';

        
        this.userId = userId || null;

        
        this.data = {};

        
        this.ajax = Espo.Ajax;
    }

    
    setUserId(userId) {
        this.userId = userId
    }

    
    getKey(scope, type) {
        if (this.userId) {
            return this.applicationId + '-' + this.userId + '-' + scope + '-' + type;
        }

        return this.applicationId + '-' + scope + '-' + type;
    }

    
    getUrl(scope, type, setId) {
        let url = scope + '/layout/' + type;

        if (setId) {
            url += '/' + setId;
        }

        return url;
    }

    

    
    get(scope, type, callback, cache) {
        if (typeof cache === 'undefined') {
            cache = true;
        }

        const key = this.getKey(scope, type);

        if (cache) {
            if (key in this.data) {
                if (typeof callback === 'function') {
                    callback(this.data[key]);
                }

                return;
            }
        }

        if (this.cache && cache) {
            const cached = this.cache.get('app-layout', key);

            if (cached) {
                if (typeof callback === 'function') {
                    callback(cached);
                }

                this.data[key] = cached;

                return;
            }
        }

        this.ajax
            .getRequest(this.getUrl(scope, type))
            .then(
                layout => {
                    if (typeof callback === 'function') {
                        callback(layout);
                    }

                    this.data[key] = layout;

                    if (this.cache) {
                        this.cache.set('app-layout', key, layout);
                    }
                }
            );
    }

    
    getOriginal(scope, type, setId, callback) {
        let url = 'Layout/action/getOriginal?scope='+scope+'&name='+type;

        if (setId) {
            url += '&setId=' + setId;
        }

        Espo.Ajax
            .getRequest(url)
            .then(
                layout => {
                    if (typeof callback === 'function') {
                        callback(layout);
                    }
                }
            );
    }

    
    set(scope, type, layout, callback, setId) {
        return Espo.Ajax
            .putRequest(this.getUrl(scope, type, setId), layout)
            .then(
                () => {
                    const key = this.getKey(scope, type);

                    if (this.cache && key) {
                        this.cache.clear('app-layout', key);
                    }

                    delete this.data[key];

                    this.trigger('sync');

                    if (typeof callback === 'function') {
                        callback();
                    }
                }
            );
    }

    
    resetToDefault(scope, type, callback, setId) {
        Espo.Ajax
            .postRequest('Layout/action/resetToDefault', {
                scope: scope,
                name: type,
                setId: setId,
            })
            .then(
                () => {
                    const key = this.getKey(scope, type);

                    if (this.cache) {
                        this.cache.clear('app-layout', key);
                    }

                    delete this.data[key];

                    this.trigger('sync');

                    if (typeof callback === 'function') {
                        callback();
                    }
                }
            );
    }

    
    clearLoadedData() {
        this.data = {};
    }
}

Object.assign(LayoutManager.prototype, Events);

export default LayoutManager;
