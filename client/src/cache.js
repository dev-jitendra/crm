




class Cache {

    
    constructor(cacheTimestamp) {
        this.basePrefix = this.prefix;

        if (cacheTimestamp) {
            this.prefix =  this.basePrefix + '-' + cacheTimestamp;
        }

        if (!this.get('app', 'timestamp')) {
            this.storeTimestamp();
        }
    }

    
    prefix = 'cache'

    
    handleActuality(cacheTimestamp) {
        const storedTimestamp = this.getCacheTimestamp();

        if (storedTimestamp) {
            if (storedTimestamp !== cacheTimestamp) {
                this.clear();
                this.set('app', 'cacheTimestamp', cacheTimestamp);
                this.storeTimestamp();
            }

            return;
        }

        this.clear();
        this.set('app', 'cacheTimestamp', cacheTimestamp);
        this.storeTimestamp();
    }

    
    getCacheTimestamp() {
        return parseInt(this.get('app', 'cacheTimestamp') || 0);
    }

    
    storeTimestamp() {
        const frontendCacheTimestamp = Date.now();

        this.set('app', 'timestamp', frontendCacheTimestamp);
    }

    
    composeFullPrefix(type) {
        return this.prefix + '-' + type;
    }

    
    composeKey(type, name) {
        return this.composeFullPrefix(type) + '-' + name;
    }

    
    checkType(type) {
        if (typeof type === 'undefined' && toString.call(type) !== '[object String]') {
            throw new TypeError("Bad type \"" + type + "\" passed to Cache().");
        }
    }

    
    get(type, name) {
        this.checkType(type);

        const key = this.composeKey(type, name);

        let stored;

        try {
            stored = localStorage.getItem(key);
        }
        catch (error) {
            console.error(error);

            return null;
        }

        if (stored) {
            let result = stored;

            if (stored.length > 9 && stored.substring(0, 9) === '__JSON__:') {
                const jsonString = stored.slice(9);

                try {
                    result = JSON.parse(jsonString);
                }
                catch (error) {
                    result = stored;
                }
            }

            return result;
        }

        return null;
    }

    
    set(type, name, value) {
        this.checkType(type);

        const key = this.composeKey(type, name);

        if (value instanceof Object || Array.isArray(value)) {
            value = '__JSON__:' + JSON.stringify(value);
        }

        try {
            localStorage.setItem(key, value);
        }
        catch (error) {
            console.log('Local storage limit exceeded.');
        }
    }

    
    clear(type, name) {
        let reText;

        if (typeof type !== 'undefined') {
            if (typeof name === 'undefined') {
                reText = '^' + this.composeFullPrefix(type);
            }
            else {
                reText = '^' + this.composeKey(type, name);
            }
        }
        else {
            reText = '^' + this.basePrefix + '-';
        }

        const re = new RegExp(reText);

        for (const i in localStorage) {
            if (re.test(i)) {
                delete localStorage[i];
            }
        }
    }
}

export default Cache;
