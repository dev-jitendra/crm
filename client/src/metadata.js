



import {Events} from 'bullbone';


class Metadata {

    
    constructor(cache) {
        
        this.cache = cache || null;

        
        this.data = {};
    }

    
    url = 'Metadata'

    
    load(callback, disableCache) {
        this.off('sync');

        if (callback) {
            this.once('sync', callback);
        }

        if (!disableCache) {
            if (this.loadFromCache()) {
                this.trigger('sync');

                return new Promise(resolve => resolve());
            }
        }

        return new Promise(resolve => {
            this.fetch()
                .then(() => resolve());
        });
    }

    
    loadSkipCache() {
        return this.load(null, true);
    }

    
    fetch() {
        return Espo.Ajax.getRequest(this.url)
            .then(data => {
                this.data = data;
                this.storeToCache();
                this.trigger('sync');
            });
    }

    
    get(path, defaultValue) {
        defaultValue = defaultValue || null;

        let arr;

        if (Array && Array.isArray && Array.isArray(path)) {
            arr = path;
        }
        else {
            arr = path.split('.');
        }

        let pointer = this.data;
        let result = defaultValue;

        for (let i = 0; i < arr.length; i++) {
            const key = arr[i];

            if (!(key in pointer)) {
                result = defaultValue;

                break;
            }

            if (arr.length - 1 === i) {
                result = pointer[key];
            }

            pointer = pointer[key];
        }

        return result;
    }

    
    loadFromCache() {
        if (this.cache) {
            const cached = this.cache.get('app', 'metadata');

            if (cached) {
                this.data = cached;

                return true;
            }
        }

        return null;
    }

    
    storeToCache() {
        if (this.cache) {
            this.cache.set('app', 'metadata', this.data);
        }
    }

    
    clearCache() {
        if (!this.cache) {
            return;
        }

        this.cache.clear('app', 'metadata');
    }

    
    getScopeList () {
        const scopes = this.get('scopes') || {};
        const scopeList = [];

        for (const scope in scopes) {
            const d = scopes[scope];

            if (d.disabled) {
                continue;
            }

            scopeList.push(scope);
        }

        return scopeList;
    }

    
    getScopeObjectList () {
        const scopes = this.get('scopes') || {};
        const scopeList = [];

        for (const scope in scopes) {
            const d = scopes[scope];

            if (d.disabled) {
                continue;
            }

            if (!d.object) {
                continue;
            }

            scopeList.push(scope);
        }

        return scopeList;
    }

    
    getScopeEntityList () {
        const scopes = this.get('scopes') || {};
        const scopeList = [];

        for (const scope in scopes) {
            const d = scopes[scope];

            if (d.disabled) {
                continue;
            }

            if (!d.entity) {
                continue;
            }

            scopeList.push(scope);
        }

        return scopeList;
    }
}

Object.assign(Metadata.prototype, Events);

export default Metadata;
