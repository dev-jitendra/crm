




class Storage {

    constructor() {}

    
    prefix = 'espo'

    
    storageObject = localStorage

    
    composeFullPrefix(type) {
        return this.prefix + '-' + type;
    }

    
    composeKey(type, name) {
        return this.composeFullPrefix(type) + '-' + name;
    }

    
    checkType(type) {
        if (
            typeof type === 'undefined' &&
            toString.call(type) !== '[object String]' || type === 'cache'
        ) {
            throw new TypeError("Bad type \"" + type + "\" passed to Espo.Storage.");
        }
    }

    
    has(type, name) {
        this.checkType(type);

        const key = this.composeKey(type, name);

        return this.storageObject.getItem(key) !== null;
    }

    
    get(type, name) {
        this.checkType(type);

        const key = this.composeKey(type, name);

        let stored;

        try {
            stored = this.storageObject.getItem(key);
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
            else if (stored[0] === "{" || stored[0] === "[") { 
                try {
                    result = JSON.parse(stored);
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

        if (value === null) {
            this.clear(type, name);

            return;
        }

        const key = this.composeKey(type, name);

        if (
            value instanceof Object ||
            Array.isArray(value) ||
            value === true ||
            value === false ||
            typeof value === 'number'
        ) {
            value = '__JSON__:' + JSON.stringify(value);
        }

        try {
            this.storageObject.setItem(key, value);
        }
        catch (error) {
            console.error(error);

            return null;
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
            reText = '^' + this.prefix + '-';
        }

        const re = new RegExp(reText);

        for (const i in this.storageObject) {
            if (re.test(i)) {
                delete this.storageObject[i];
            }
        }
    }
}

export default Storage;
