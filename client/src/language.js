



import {Events} from 'bullbone';


class Language {

    
    url = 'I18n'

    
    constructor(cache) {
        
        this.cache = cache || null;

        
        this.data = {};

        
        this.name = 'default';
    }

    
    has(name, category, scope) {
        if (scope in this.data) {
            if (category in this.data[scope]) {
                if (name in this.data[scope][category]) {
                    return true;
                }
            }
        }

        return false;
    }

    
    get(scope, category, name) {
        if (scope in this.data) {
            if (category in this.data[scope]) {
                if (name in this.data[scope][category]) {
                    return this.data[scope][category][name];
                }
            }
        }

        if (scope === 'Global') {
            return name;
        }

        return false;
    }

    
    translate(name, category, scope) {
        scope = scope || 'Global';
        category = category || 'labels';

        let res = this.get(scope, category, name);

        if (res === false && scope !== 'Global') {
            res = this.get('Global', category, name);
        }

        return res;
    }

    
    translateOption(value, field, scope) {
        let translation = this.translate(field, 'options', scope);

        if (typeof translation !== 'object') {
            translation = {};
        }

        return translation[value] || value;
    }

    
    loadFromCache(loadDefault) {
        let name = this.name;

        if (loadDefault) {
            name = 'default';
        }

        if (this.cache) {
            const cached = this.cache.get('app', 'language-' + name);

            if (cached) {
                this.data = cached;

                return true;
            }
        }

        return null;
    }

    
    clearCache() {
        if (this.cache) {
            this.cache.clear('app', 'language-' + this.name);
        }
    }

    
    storeToCache(loadDefault) {
        let name = this.name;

        if (loadDefault) {
            name = 'default';
        }

        if (this.cache) {
            this.cache.set('app', 'language-' + name, this.data);
        }
    }

    
    load(callback, disableCache, loadDefault) {
        if (callback) {
            this.once('sync', callback);
        }

        if (!disableCache) {
            if (this.loadFromCache(loadDefault)) {
                this.trigger('sync');

                return new Promise(resolve => resolve());
            }
        }

        return new Promise(resolve => {
            this.fetch(loadDefault)
                .then(() => resolve());
        });
    }

    
    loadDefault() {
        return this.load(null, false, true);
    }

    
    loadSkipCache() {
        return this.load(null, true);
    }

    
    
    loadDefaultSkipCache() {
        return this.load(null, true, true);
    }

    
    fetch(loadDefault) {
        return Espo.Ajax.getRequest(this.url, {default: loadDefault}).then(data => {
            this.data = data;

            this.storeToCache(loadDefault);
            this.trigger('sync');
        });
    }

    
    sortFieldList(scope, fieldList) {
        return fieldList.sort((v1, v2) => {
            return this.translate(v1, 'fields', scope)
                .localeCompare(this.translate(v2, 'fields', scope));
        });
    }

    
    sortEntityList(entityList, plural) {
        let category = 'scopeNames';

        if (plural) {
            category += 'Plural';
        }

        return entityList.sort((v1, v2) => {
            return this.translate(v1, category)
                .localeCompare(this.translate(v2, category));
        });
    }

    
    translatePath(path) {
        if (typeof path === 'string' || path instanceof String) {
            path = path.split('.');
        }

        let pointer = this.data;

        path.forEach(key => {
            if (key in pointer) {
                pointer = pointer[key];
            }
        });

        return pointer;
    }
}

Object.assign(Language.prototype, Events);

export default Language;
