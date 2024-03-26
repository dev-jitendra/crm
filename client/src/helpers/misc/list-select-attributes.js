



export default class {
    
    constructor(scope, storage, maxCount) {
        this.scope = scope;
        this.storage = storage;
        this.key = 'textSearches';
        this.maxCount = maxCount || 100;
        
        this.list = null;
    }

    
    match(text, limit) {
        text = text.toLowerCase().trim();

        const list = this.get();
        const matchedList = [];

        for (const item of list) {
            if (item.toLowerCase().startsWith(text)) {
                matchedList.push(item);
            }

            if (limit !== undefined && matchedList.length === limit) {
                break;
            }
        }

        return matchedList;
    }

    
    get() {
        if (this.list === null) {
            this.list = this.getFromStorage();
        }

        return this.list;
    }

    
    getFromStorage() {
        
        return this.storage.get(this.key, this.scope) || [];
    }

    
    store(text) {
        text = text.trim();

        let list = this.getFromStorage();

        const index = list.indexOf(text);

        if (index !== -1) {
            list.splice(index, 1);
        }

        list.unshift(text);

        if (list.length > this.maxCount) {
            list = list.slice(0, this.maxCount);
        }

        this.list = list;
        this.storage.set(this.key, this.scope, list);
    }

    
    remove(text) {
        text = text.trim();

        const list = this.getFromStorage();

        const index = list.indexOf(text);

        if (index === -1) {
            return;
        }

        list.splice(index, 1);

        this.list = list;
        this.storage.set(this.key, this.scope, list);
    }
}
