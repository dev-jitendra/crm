



import {Events, View as BullView} from 'bullbone';
import _ from 'underscore';










class Model {

    
    urlRoot = null

    
    url = null

    
    name = null

    
    entityType = null

    
    lastSyncPromise = null

    
    _pending
    
    _changing

    
    constructor(attributes, options) {
        options = options || {};

        
        this.idAttribute = 'id';

        
        this.id = null;

        
        this.cid = _.uniqueId('c');

        
        this.attributes = {};

        if (options.collection) {
            this.collection = options.collection;
        }

        this.set(attributes || {});

        
        this.defs = options.defs || {};

        if (!this.defs.fields) {
            this.defs.fields = {};
        }

        if (options.entityType) {
            this.entityType = options.entityType;
            this.name = options.entityType;
            this.urlRoot = options.entityType;
        }

        this.urlRoot = options.urlRoot || this.urlRoot;
        this.url = options.url || this.url;

        
        this.dateTime = options.dateTime || null;

        
        this.changed = {};
        
        this._previousAttributes = null;
    }

    
    sync(method, model, options) {
        const methodMap = {
            'create': 'POST',
            'update': 'PUT',
            'patch': 'PUT',
            'delete': 'DELETE',
            'read': 'GET',
        };

        const httpMethod = methodMap[method];

        if (!httpMethod) {
            throw new Error(`Bad request method '${method}'.`);
        }

        options = options || {};

        const url = this.composeSyncUrl();

        if (!url) {
            throw new Error(`No 'url'.`);
        }

        const data = model && ['create', 'update', 'patch'].includes(method) ?
            (options.attributes || model.getClonedAttributes()) : null;

        const error = options.error;

        options.error = (xhr, textStatus, errorThrown) => {
            options.textStatus = textStatus;
            options.errorThrown = errorThrown;

            if (error) {
                error.call(options.context, xhr, textStatus, errorThrown);
            }
        };

        const stringData = data ? JSON.stringify(data) : null;

        const ajaxPromise = !options.bypassRequest ?
            Espo.Ajax.request(url, httpMethod, stringData, options) :
            Promise.resolve();

        options.xhr = ajaxPromise.xhr;

        model.trigger('request', url, httpMethod, data, ajaxPromise, options);

        return ajaxPromise;
    }

    
    set(attribute, value, options) {
        if (attribute == null) {
            return this;
        }

        let attributes;

        if (typeof attribute === 'object') {
            return this.setMultiple(attribute, value);
        }

        attributes = {};
        attributes[attribute] = value;

        return this.setMultiple(attributes, options);
    }

    
    setMultiple(attributes, options) {
        if (this.idAttribute in attributes) {
            this.id = attributes[this.idAttribute];
        }

        options = options || {};

        const changes = [];
        const changing = this._changing;

        this._changing = true;

        if (!changing) {
            this._previousAttributes = _.clone(this.attributes);
            this.changed = {};
        }

        const current = this.attributes;
        const changed = this.changed;
        const previous = this._previousAttributes;

        for (const attribute in attributes) {
            const value = attributes[attribute];

            if (!_.isEqual(current[attribute], value)) {
                changes.push(attribute);
            }

            if (!_.isEqual(previous[attribute], value)) {
                changed[attribute] = value;
            } else {
                delete changed[attribute];
            }

            options.unset ?
                delete current[attribute] :
                current[attribute] = value;
        }

        if (!options.silent) {
            if (changes.length) {
                this._pending = options;
            }

            for (let i = 0; i < changes.length; i++) {
                this.trigger('change:' + changes[i], this, current[changes[i]], options);
            }
        }

        if (changing) {
            return this;
        }

        if (!options.silent) {
            
            while (this._pending) {
                options = this._pending;
                this._pending = false;

                this.trigger('change', this, options);
            }
        }

        this._pending = false;
        this._changing = false;

        return this;
    }

    
    unset(attribute, options) {
        options = {...options, unset: true};

        const attributes = {};
        attributes[attribute] = null;

        return this.setMultiple(attributes, options);
    }

    
    get(attribute) {
        if (attribute === this.idAttribute && this.id) {
            return this.id;
        }

        return this.attributes[attribute];
    }

    
    has(attribute) {
        const value = this.get(attribute);

        return typeof value !== 'undefined';
    }

    
    clear(options) {
        const attributes = {};

        for (const key in this.attributes) {
            attributes[key] = void 0;
        }

        options = {...options, unset: true};

        return this.set(attributes, options);
    }

    
    isNew() {
        return !this.id;
    }

    
    hasChanged(attribute) {
        if (!attribute) {
            return !_.isEmpty(this.changed);
        }

        return _.has(this.changed, attribute);
    }

    
    changedAttributes() {
        return this.hasChanged() ? _.clone(this.changed) : {};
    }

    
    previousAttributes() {
        return _.clone(this._previousAttributes);
    }

    
    previous(attribute) {
        if (!this._previousAttributes) {
            return null;
        }

        return this._previousAttributes[attribute];
    }

    
    fetch(options) {
        options = {...options};

        const success = options.success;

        options.success = response => {
            const serverAttributes = this.prepareAttributes(response, options);

            this.set(serverAttributes, options);

            if (success) {
                success.call(options.context, this, response, options);
            }

            this.trigger('sync', this, response, options);
        };

        this.lastSyncPromise = this.sync('read', this, options);

        return this.lastSyncPromise;
    }

    
    save(attributes, options) {
        options = {...options};

        if (attributes && !options.wait) {
            this.setMultiple(attributes, options);
        }

        const success = options.success;

        const setAttributes = this.attributes;

        options.success = response => {
            this.attributes = setAttributes;

            let responseAttributes = this.prepareAttributes(response, options);

            if (options.wait) {
                responseAttributes = {...setAttributes, ...responseAttributes};
            }

            if (responseAttributes) {
                this.setMultiple(responseAttributes, options);
            }

            if (success) {
                success.call(options.context, this, response, options);
            }

            this.trigger('sync', this, response, options);
        };

        const error = options.error;

        options.error = response => {
            if (error) {
                error.call(options.context, this, response, options);
            }

            this.trigger('error', this, response, options);
        };

        if (attributes && options.wait) {
            
            this.attributes =  {...setAttributes, ...attributes};
        }

        const method = this.isNew() ?
            'create' :
            (options.patch ? 'patch' : 'update');

        if (method === 'patch') {
            options.attributes = attributes;
        }

        const result = this.sync(method, this, options);

        this.attributes = setAttributes;

        return result;
    }

    
    destroy(options) {
        options = _.clone(options || {});

        const success = options.success;

        const destroy = () => {
            this.stopListening();
            this.trigger('destroy', this, this.collection, options);
        };

        options.success = response => {
            if (options.wait) {
                destroy();
            }

            if (success) {
                success.call(options.context, this, response, options);
            }

            if (!this.isNew()) {
                this.trigger('sync', this, response, options);
            }
        };

        if (this.isNew()) {
            _.defer(options.success);

            if (!options.wait) {
                destroy();
            }

            return Promise.resolve();
        }

        const error = options.error;

        options.error = response => {
            if (error) {
                error.call(options.context, this, response, options);
            }

            this.trigger('error', this, response, options);
        };

        const result = this.sync('delete', this, options);

        if (!options.wait) {
            destroy();
        }

        return result;
    }

    
    composeSyncUrl() {
        if (this.url) {
            return this.url;
        }

        let urlRoot = this.urlRoot;

        if (!urlRoot && this.collection) {
            urlRoot = this.collection.urlRoot
        }

        if (!urlRoot) {
            throw new Error("No urlRoot.");
        }

        if (this.isNew()) {
            return urlRoot;
        }

        const id = this.get(this.idAttribute);

        return urlRoot.replace(/[^\/]$/, '$&/') + encodeURIComponent(id);
    }

    
    
    prepareAttributes(response, options) {
        return response;
    }

    
    clone() {
        return new this.constructor(
            Espo.Utils.cloneDeep(this.attributes),
            {
                entityType: this.entityType,
                urlRoot: this.urlRoot,
                url: this.url,
                defs: this.defs,
                dateTime: this.dateTime,
            }
        );
    }

    
    setDefs(defs) {
        this.defs = defs || {};

        if (!this.defs.fields) {
            this.defs.fields = {};
        }
    }

    
    getClonedAttributes() {
        return Espo.Utils.cloneDeep(this.attributes);
    }

    
    populateDefaults() {
        let defaultHash = {};

        const fieldDefs = this.defs.fields;

        for (const field in fieldDefs) {
            if (this.hasFieldParam(field, 'default')) {
                try {
                    defaultHash[field] = this.parseDefaultValue(this.getFieldParam(field, 'default'));
                }
                catch (e) {
                    console.error(e);
                }
            }

            const defaultAttributes = this.getFieldParam(field, 'defaultAttributes');

            if (defaultAttributes) {
                for (const attribute in defaultAttributes) {
                    defaultHash[attribute] = defaultAttributes[attribute];
                }
            }
        }

        defaultHash = Espo.Utils.cloneDeep(defaultHash);

        for (const attr in defaultHash) {
            if (this.has(attr)) {
                delete defaultHash[attr];
            }
        }

        this.set(defaultHash, {silent: true});
    }

    
    parseDefaultValue(defaultValue) {
        if (
            typeof defaultValue === 'string' &&
            defaultValue.indexOf('javascript:') === 0
        ) {
            const code = defaultValue.substring(11);

            defaultValue = (new Function( "with(this) { " + code + "}")).call(this);
        }

        return defaultValue;
    }

    
    getLinkMultipleColumn(field, column, id) {
        return ((this.get(field + 'Columns') || {})[id] || {})[column];
    }

    
    setRelate(data) {
        const setRelate = options => {
            const link = options.link;
            const model = options.model;

            if (!link || !model) {
                throw new Error('Bad related options');
            }

            const type = this.defs.links[link].type;

            switch (type) {
                case 'belongsToParent':
                    this.set(link + 'Id', model.id);
                    this.set(link + 'Type', model.entityType);
                    this.set(link + 'Name', model.get('name'));

                    break;

                case 'belongsTo':
                    this.set(link + 'Id', model.id);
                    this.set(link + 'Name', model.get('name'));

                    break;

                case 'hasMany':
                    const ids = [];
                    ids.push(model.id);

                    const names = {};

                    names[model.id] = model.get('name');

                    this.set(link + 'Ids', ids);
                    this.set(link + 'Names', names);

                    break;
            }
        };

        if (Object.prototype.toString.call(data) === '[object Array]') {
            data.forEach(options => {
                setRelate(options);
            });

            return;
        }

        setRelate(data);
    }

    
    getFieldList() {
        if (!this.defs || !this.defs.fields) {
            return [];
        }

        return Object.keys(this.defs.fields);
    }

    
    getFieldType(field) {
        if (!this.defs || !this.defs.fields) {
            return null;
        }

        if (field in this.defs.fields) {
            return this.defs.fields[field].type || null;
        }

        return null;
    }

    
    getFieldParam(field, param) {
        if (!this.defs || !this.defs.fields) {
            return null;
        }

        if (field in this.defs.fields) {
            if (param in this.defs.fields[field]) {
                return this.defs.fields[field][param];
            }
        }

        return null;
    }

    hasFieldParam(field, param) {
        if (!this.defs || !this.defs.fields) {
            return false;
        }

        if (field in this.defs.fields) {
            if (param in this.defs.fields[field]) {
                return true;
            }
        }

        return false;
    }

    
    getLinkType(link) {
        if (!this.defs || !this.defs.links) {
            return null;
        }

        if (link in this.defs.links) {
            return this.defs.links[link].type || null;
        }

        return null;
    }

    
    getLinkParam(link, param) {
        if (!this.defs || !this.defs.links) {
            return null;
        }

        if (link in this.defs.links) {
            if (param in this.defs.links[link]) {
                return this.defs.links[link][param];
            }
        }

        return null;
    }

    
    isFieldReadOnly(field) {
        return this.getFieldParam(field, 'readOnly') || false;
    }

    
    isRequired(field) {
        return this.getFieldParam(field, 'required') || false;
    }

    
    getLinkMultipleIdList(field) {
        return this.get(field + 'Ids') || [];
    }

    
    getTeamIdList() {
        return this.get('teamsIds') || [];
    }

    
    hasField(field) {
        return ('defs' in this) && ('fields' in this.defs) && (field in this.defs.fields);
    }

    
    hasLink(link) {
        return ('defs' in this) && ('links' in this.defs) && (link in this.defs.links);
    }

    
    isEditable() {
        return true;
    }

    
    isRemovable() {
        return true;
    }

    
    getEntityType() {
        return this.name;
    }

    
    abortLastFetch() {
        if (this.lastSyncPromise && this.lastSyncPromise.getReadyState() < 4) {
            this.lastSyncPromise.abort();
        }
    }

    
    
    toJSON() {
        console.warn(`model.toJSON is deprecated. Use 'getClonedAttributes' instead.`);

        return this.getClonedAttributes();
    }
}

Object.assign(Model.prototype, Events);

Model.extend = BullView.extend;

export default Model;
