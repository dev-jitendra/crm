




class SessionStorage {

    
    storageObject = sessionStorage

    
    get(name) {
        let stored;

        try {
            stored = this.storageObject.getItem(name);
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

    
    set(name, value) {
        if (value === null) {
            this.clear(name);

            return;
        }

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
            this.storageObject.setItem(name, value);
        }
        catch (error) {
            console.error(error);
        }
    }

    
    has(name) {
        return this.storageObject.getItem(name) !== null;
    }

    
    clear(name) {
        for (const i in this.storageObject) {
            if (i === name) {
                delete this.storageObject[i];
            }
        }
    }
}

export default SessionStorage;
