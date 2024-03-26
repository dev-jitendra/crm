










class SearchManager {

    
    constructor(
        collection,
        type,
        storage,
        dateTime,
        defaultData,
        emptyOnReset
    ) {
        
        this.collection = collection;

        
        this.scope = collection.entityType;

        
        this.storage = storage;

        
        this.type = type || 'list';

        
        this.dateTime = dateTime;

        
        this.emptyOnReset = emptyOnReset;

        
        this.emptyData = {
            textFilter: '',
            bool: {},
            advanced: {},
            primary: null,
        };

        if (defaultData) {
            this.defaultData = defaultData;

            for (const p in this.emptyData) {
                if (!(p in defaultData)) {
                    defaultData[p] = Espo.Utils.clone(this.emptyData[p]);
                }
            }
        }

        this.data = Espo.Utils.clone(defaultData) || this.emptyData;

        this.sanitizeData();
    }

    
    sanitizeData() {
        if (!('advanced' in this.data)) {
            this.data.advanced = {};
        }

        if (!('bool' in this.data)) {
            this.data.bool = {};
        }

        if (!('textFilter' in this.data)) {
            this.data.textFilter = '';
        }
    }

    
    getWhere() {
        const where = [];

        if (this.data.textFilter && this.data.textFilter !== '') {
            where.push({
                type: 'textFilter',
                value: this.data.textFilter
            });
        }

        if (this.data.bool) {
            const o = {
                type: 'bool',
                value: [],
            };

            for (const name in this.data.bool) {
                if (this.data.bool[name]) {
                    o.value.push(name);
                }
            }

            if (o.value.length) {
                where.push(o);
            }
        }

        if (this.data.primary) {
            const o = {
                type: 'primary',
                value: this.data.primary,
            };

            if (o.value.length) {
                where.push(o);
            }
        }

        if (this.data.advanced) {
            for (const name in this.data.advanced) {
                const defs = this.data.advanced[name];

                if (!defs) {
                    continue;
                }

                const part = this.getWherePart(name, defs);

                where.push(part);
            }
        }

        return where;
    }

    
    getWherePart(name, defs) {
        let attribute = name;

        if (typeof defs !== 'object') {
            console.error('Bad where clause');

            return {};
        }

        if ('where' in defs) {
            return defs.where;
        }

        const type = defs.type;
        let value;

        if (type === 'or' || type === 'and') {
            const a = [];

            value = defs.value || {};

            for (const n in value) {
                a.push(this.getWherePart(n, value[n]));
            }

            return {
                type: type,
                value: a
            };
        }

        if ('field' in defs) { 
            attribute = defs.field;
        }

        if ('attribute' in defs) {
            attribute = defs.attribute;
        }

        if (defs.dateTime) {
            return {
                type: type,
                attribute: attribute,
                value: defs.value,
                dateTime: true,
                timeZone: this.dateTime.timeZone || 'UTC',
            };
        }

        value = defs.value;

        return {
            type: type,
            attribute: attribute,
            value: value
        };
    }

    
    loadStored() {
        this.data =
            this.storage.get(this.type + 'Search', this.scope) ||
            Espo.Utils.clone(this.defaultData) ||
            Espo.Utils.clone(this.emptyData);

        this.sanitizeData();

        return this;
    }

    
    get() {
        return this.data;
    }

    
    setAdvanced(advanced) {
        this.data = Espo.Utils.clone(this.data);

        this.data.advanced = advanced;
    }

    
    setBool(bool) {
        this.data = Espo.Utils.clone(this.data);

        this.data.bool = bool;
    }

    
    setPrimary(primary) {
        this.data = Espo.Utils.clone(this.data);

        this.data.primary = primary;
    }

    
    set(data) {
        this.data = data;

        if (this.storage) {
            data = Espo.Utils.clone(data);
            delete data['textFilter'];

            this.storage.set(this.type + 'Search', this.scope, data);
        }
    }

    
    empty() {
        this.data = Espo.Utils.clone(this.emptyData);

        if (this.storage) {
            this.storage.clear(this.type + 'Search', this.scope);
        }
    }

    
    reset() {
        if (this.emptyOnReset) {
            this.empty();

            return;
        }

        this.data = Espo.Utils.clone(this.defaultData) || Espo.Utils.clone(this.emptyData);

        if (this.storage) {
            this.storage.clear(this.type + 'Search', this.scope);
        }
    }
}

export default SearchManager;
